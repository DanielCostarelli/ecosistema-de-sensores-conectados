<?php
// Caminho do arquivo onde o conteúdo está armazenado
$arquivo = "Registro.json";

// Verifica se o arquivo existe
if (file_exists($arquivo)) {
    // Lê o conteúdo do arquivo
    $conteudo = file_get_contents($arquivo);

    // Decodifica o JSON
    $Objeto_json = json_decode($conteudo, true);

    // Verifica se a decodificação foi bem-sucedida
    if (json_last_error() === JSON_ERROR_NONE) {
        // Criando um objeto de resposta para retornar apenas os campos necessários

        $tensao_FN = $Objeto_json['tensao_FN'];
        $tensao_FF = $Objeto_json['tensao_FF'];
        $corrente_FN = $Objeto_json['corrente_FN'];
        $corrente_FF = $Objeto_json['corrente_FF'];
        $potencia_FN = $Objeto_json['potencia_FN'];
        $potencia_FF = $Objeto_json['potencia_FF'];
        $energia = $Objeto_json['energia'];
        $frequencia_FN = $Objeto_json['frequencia_FN'];
        $frequencia_FF = $Objeto_json['frequencia_FF'];
        $fp_FN = $Objeto_json['fp_FN'];
        $fp_FF = $Objeto_json['fp_FF'];
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
                    "tensao_FN" => $tensao_FN,
                    "tensao_FF" => $tensao_FF,
                    "corrente_FN" => $corrente_FN,
                    "corrente_FF" => $corrente_FF,
                    "potencia_FN" => $potencia_FN,
                    "potencia_FF" => $potencia_FF,
                    "energia" => $energia,
                    "frequencia_FN" => $frequencia_FN,
                    "frequencia_FF" => $frequencia_FF,
                    "fp_FN" => $fp_FN,
                    "fp_FF" => $fp_FF,
                    "data_e_hora" => [
                        "ano" => $tempo_ano,
                        "mes" => $tempo_mes,
                        "dia" => $tempo_dia,
                        "hora" => $tempo_hora,
                        "minuto" => $tempo_minuto,
                        "segundo" => $tempo_segundo,
                        "dia_da_semana" => $tempo_dia_da_semana,
                        "nome_do_mes" => $tempo_nome_do_mes
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
                    "kwh_fechamento_mes_anterior" => $kwh_fechamento_mes_anterior
                ];



        

        // Retorna o objeto como JSON
        header('Content-Type: application/json');
        echo json_encode($dados);
    } else {
        // Caso ocorra erro na decodificação do JSON
        echo json_encode(["error" => "Erro ao decodificar o JSON."]);
    }
} else {
    // Caso o arquivo não exista
    echo json_encode(["error" => "Arquivo não encontrado."]);
}
?>
