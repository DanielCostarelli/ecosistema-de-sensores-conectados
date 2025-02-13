<?php
// Caminho do arquivo onde o conteúdo está armazenado
$arquivo = "Registro.json";

// Verifica se o arquivo existe
if (file_exists($arquivo)) {
    // Lê o conteúdo do arquivo
    $conteudo = file_get_contents($arquivo);
    $ultimos_caracteres = substr($conteudo, -40);

    // Decodifica o JSON
    $Objeto_json = json_decode($conteudo, true);

    // Verifica se a decodificação foi bem-sucedida
    if (json_last_error() === JSON_ERROR_NONE) {
        // Criando um objeto de resposta para retornar apenas os campos necessários
        $tensao = $Objeto_json['tensao'];
        $corrente = $Objeto_json['corrente'];
        $potencia = $Objeto_json['potencia'];
        $energia = $Objeto_json['energia'];
        $frequencia = $Objeto_json['frequencia'];
        $fp = $Objeto_json['fp'];
        $status = $Objeto_json['status'];
        $tempo_ano = $Objeto_json['data_e_hora']['ano'];
        $tempo_mes = $Objeto_json['data_e_hora']['mes'];
        $tempo_dia = $Objeto_json['data_e_hora']['dia'];
        $tempo_hora = $Objeto_json['data_e_hora']['hora'];
        $tempo_minuto = $Objeto_json['data_e_hora']['minuto'];
        $tempo_segundo = $Objeto_json['data_e_hora']['segundo'];
        $tempo_dia_da_semana = $Objeto_json['data_e_hora']['dia_da_semana'];
        $tempo_nome_do_mes = $Objeto_json['data_e_hora']['nome_do_mes'];
        $consumo = $Objeto_json['Consumo_ate_o_momento']['kwh'];
        $dia_semana = $Objeto_json['Gastos_da_semana']['dia_semana'];
        $dia_mes = $Objeto_json['Gastos_da_semana']['dia_mes'];
        $consumo_dia_mes = $Objeto_json['Gastos_da_semana']['kwh'];
        $horas_ligado_semanal = $Objeto_json['Gastos_da_semana']['horas'];
        $meses = $Objeto_json['Consumo_meses']['mes'];
        $consumo_meses = $Objeto_json['Consumo_meses']['kwh'];
        $dias_leitura = $Objeto_json['Consumo_meses']['dias'];
        $resetar_leitura = $Objeto_json['resetar_leitura'];
        $preco_unitario_kwh = $Objeto_json['preco_unitario_kwh']; 
        $kwh_fechamento_mes_anterior = $Objeto_json['kwh_fechamento_mes_anterior'];

        $dados = [                         // Cria um novo array associativo para o novo objeto
                    "tensao" => array_slice($tensao, -1),
                    "corrente" => array_slice($corrente, -1),
                    "potencia" => array_slice($potencia, -3000),
                    "energia" => array_slice($energia, -1),
                    "frequencia" => array_slice($frequencia, -1),
                    "fp" => array_slice($fp, -1),
                    "status" => $status,
                    "data_e_hora" => [
                        "ano" => array_slice($tempo_ano, -1),
                        "mes" => array_slice($tempo_mes, -1),
                        "dia" => array_slice($tempo_dia, -3000),
                        "hora" => array_slice($tempo_hora, -3000),
                        "minuto" => array_slice($tempo_minuto, -3000),
                        "segundo" => array_slice($tempo_segundo, -3000),
                        "dia_da_semana" => array_slice($tempo_dia_da_semana, -1),
                        "nome_do_mes" => array_slice($tempo_nome_do_mes, -3000)
                    ],
                    "Consumo_ate_o_momento" => [
                        "kwh" => $consumo
                    ],
                    "Gastos_da_semana" => [
                        "dia_semana" => $dia_semana,
                        "dia_mes" => $dia_mes,
                        "kwh" => $consumo_dia_mes,
                        "horas" => $horas_ligado_semanal
                    ],
                    "Consumo_meses" => [
                        "mes" => $meses,
                        "kwh" => $consumo_meses,
                        "dias" => $dias_leitura
                    ],
                    "resetar_leitura" => $resetar_leitura,
                    "preco_unitario_kwh" => $preco_unitario_kwh,
                    "kwh_fechamento_mes_anterior" => $kwh_fechamento_mes_anterior,
                    "json_recebido" => "sim",
                ];



        

        // Retorna o objeto como JSON
        header('Content-Type: application/json');
        echo json_encode($dados);
    } else {
        // Caso ocorra erro na decodificação do JSON
        echo json_encode([
                "error" => "Erro ao decodificar o JSON.",
                "json_recebido" => "erro no json",
                "ultimos_caracteres" => $ultimos_caracteres,
            ]);
    }
} else {
    // Caso o arquivo não exista
    echo json_encode([
            "error" => "Arquivo não encontrado.",
            "json_recebido" => "não encontrado",
        ]);
}
?>
