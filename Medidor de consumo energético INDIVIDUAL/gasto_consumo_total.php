<?php
$arquivo = "Registro.json";
$conteudo = file_get_contents($arquivo);
$Objeto_json = json_decode($conteudo, true);

if (json_last_error() === JSON_ERROR_NONE) {
    $horas = $Objeto_json['data_e_hora']['hora'];
    $minutos = $Objeto_json['data_e_hora']['minuto'];
    $segundos = $Objeto_json['data_e_hora']['segundo'];
    $dias = $Objeto_json['data_e_hora']['dia'];
    $meses = $Objeto_json['data_e_hora']['mes'];
    $semana = $Objeto_json['data_e_hora']['dia_da_semana'];
    $energia = $Objeto_json['energia'];
    $corrente = $Objeto_json['corrente'];
    $potencia = $Objeto_json['potencia'];
    $fp = $Objeto_json['fp'];

    $indices_mudanca_dia = [];       // Inicializar array para armazenar índices de mudança de dia

    // Detectar mudanças de dia
    $ultimo_dia = null;
    for ($i = 0; $i < count($dias); $i++) {
        $dia_atual = $dias[$i];

        if ($i === 0 || $dia_atual !== $ultimo_dia || $i === count($dias)-1) {
            // É o primeiro elemento ou mudança de dia detectada
            $indices_mudanca_dia[] = $i;
            $ultimo_dia = $dia_atual;
        }
    }
    // echo "<pre>";
    // print_r($indices_mudanca_dia);
    // echo "</pre>";




    // Calcular consumo diário de energia
    for ($j = 1; $j < count($indices_mudanca_dia); $j++) {
        $inicio = $indices_mudanca_dia[$j - 1]; // Índice do começo do dia
        $fim = $indices_mudanca_dia[$j] - 1;    // Índice do fim do dia

        // Garantir que os índices existem no array de energia
        if (isset($energia[$fim]) && isset($energia[$inicio])) {
            $consumo_diario[] = $energia[$fim] - $energia[$inicio];
        }
    }

    // Exibir o consumo diário calculado
    // echo "<pre>";
    // print_r($consumo_diario);
    // echo "</pre>";





    $datas_formatadas = [];

    foreach ($indices_mudanca_dia as $indice) {
        // Obtém o dia e o mês correspondente ao índice de troca
        $semana_formatado = substr($semana[$indice], 0, 3) . '.';
        $dia_formatado = str_pad($dias[$indice], 2, "0", STR_PAD_LEFT);
        $mes_formatado = str_pad($meses[$indice], 2, "0", STR_PAD_LEFT);
        
        // Formata a data como "DD/MM"
        $datas_formatadas[] = "$semana_formatado $dia_formatado/$mes_formatado";
    }
    array_pop($datas_formatadas);

    // Exibe a lista de datas formatadas
    // echo "<pre>";
    // print_r($datas_formatadas);
    // echo "</pre>";



    $status = [];   

    for ($i = 0; $i < count($corrente); $i++) {
        if (((float)$corrente[$i] >= 0.1 || (float)$potencia[$i] >= 0.1) && (float)$fp[$i] <= 0.8) {
            $status[] = 1; // Ligada
        } else {
            $status[] = 0; // Desligada
        }
    }

    // Exibe a lista de status
    // echo "<pre>";
    // print_r($status);
    // echo "</pre>";





    

    // Calcula o tempo ligado para cada dia    USANDO O METODO DE CADA MEDIÇÃO LIGADA X 12 SEGUNDOS
    $tempo_ligado_por_dia = [];
    for ($j = 0; $j < count($indices_mudanca_dia) - 1; $j++) {
        $inicio = $indices_mudanca_dia[$j];
        $fim = $indices_mudanca_dia[$j + 1] - 1;
        $tempo_ligado = 0;

        for ($i = $inicio; $i < $fim; $i++) {
            if ($status[$i] === 1) {
                $tempo_ligado += 12; // Cada registro é feito a cada 12 segundos
            }
        }

        // $tempo_ligado_por_dia[] = $tempo_ligado;
        $tempo_ligado_por_dia[] = sprintf("%02d:%02d", floor($tempo_ligado / 3600), floor(($tempo_ligado % 3600) / 60));
    }

    // Exibir o tempo ligado por dia
    // echo "<pre>";
    // print_r($tempo_ligado_por_dia);
    // echo "</pre>";




    // Calcula o tempo ligado para cada dia    USANDO O METODO DE SUBTRAIR CADA SEGUNDO DA LISTA, ENQUANTO LIGADO
    $tempo_ligado_por_dia_2 = [];

    // Percorre cada dia
    for ($j = 0; $j < count($indices_mudanca_dia) - 1; $j++) {
        $inicio = $indices_mudanca_dia[$j];
        $fim = $indices_mudanca_dia[$j + 1] - 1;
        $tempo_ligado = 0;

        for ($i = $inicio + 1; $i <= $fim; $i++) {
            if ($status[$i] === 1) {
                // Calcula a diferença de tempo entre os registros
                $diferenca_segundos = ($horas[$i] * 3600 + $minutos[$i] * 60 + $segundos[$i]) - 
                                      ($horas[$i - 1] * 3600 + $minutos[$i - 1] * 60 + $segundos[$i - 1]);

                // Evita contagens negativas, e caso haja erro no registro
                if ($diferenca_segundos > 0 && $diferenca_segundos < 60) { // Limite de 1 min para evitar contar o tempo qunado há quedas de energia.
                    $tempo_ligado += $diferenca_segundos;
                }
            }
        }

        $tempo_ligado_por_dia_2[] = sprintf("%02d:%02d", floor($tempo_ligado / 3600), floor(($tempo_ligado % 3600) / 60));
    }

    // Exibir o tempo ligado por dia
    // echo "<pre>";
    // print_r($tempo_ligado_por_dia_2);
    // echo "</pre>";



    header('Content-Type: application/json');
    $resultado = [
        'datas' => $datas_formatadas,
        'energia_gasta' => $consumo_diario,
        'horas_ligadas' => $tempo_ligado_por_dia_2,
        'consumo_hoje' => $consumo_diario[count($consumo_diario) - 1],
        'horas_ligado_hoje' => $tempo_ligado_por_dia_2[count($tempo_ligado_por_dia_2) - 1]
    ];
    echo json_encode($resultado);




    
} else {
    echo "Erro ao decodificar o JSON.";
}
?>
