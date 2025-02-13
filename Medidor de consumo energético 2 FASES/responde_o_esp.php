<?php





if (isset($_GET['key'])) {                                  // Verifica se a variável 'key' foi enviada via GET
    $mensagem = $_GET['key'];                               // Mensagem = valor recebido via GET
    $partes = explode("//", $mensagem);                     // Dividindo a mensagem em duas partes usando '//' como delimitador
    $chave = $partes[1];
    $chave = trim($chave, "'");                             // Removendo aspas da chave, se necessário

    if ($chave == 'admin1248') {                             // Se a senha estiver correta:

        $ano = null;                                        // inicia as variaveis de tempo
        $mes = null;
        $dia = null;
        $hora = null;
        $minuto = null;
        $segundo = null;
        $dia_da_semana = null;
        $nome_do_mes = null;
        ob_start();                                         // Incluir o arquivo que retorna as datas e horas   // Inicia o buffer de saída
        include 'obtem_data_e_hora.php';                    // Executa o código do arquivo
        $data_e_hora_json = ob_get_clean();                 // Obtém o conteúdo gerado pelo echo e limpa o buffer
        if ($data_e_hora_json) {
            echo $data_e_hora_json;       // retorna nesse formato:     ["26","09","2024","Quinta-feira","22","46","58"]
            
            $ano = json_decode($data_e_hora_json)[2];
            $mes = json_decode($data_e_hora_json)[1];
            $dia = json_decode($data_e_hora_json)[0];
            $hora = json_decode($data_e_hora_json)[4];
            $minuto = json_decode($data_e_hora_json)[5];
            $segundo = json_decode($data_e_hora_json)[6];
            $dia_da_semana = json_decode($data_e_hora_json)[3];

            $meses = [ 1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" ];
            $nome_do_mes = $meses[(int)$mes] ?? "Mês inválido";

        }








        $valores = $partes[0];
        $valores = json_decode($valores);

        if (is_array($valores)) {
            $valores = array_map('strval', $valores);       // Converte cada elemento para string
                                                            // Cada variavel recebe seu valor, em string, da ultima leitura do ESP
            $tensao_FN_ = $valores[0];
            $corrente_FN_ = $valores[1];
            $potencia_FN_ = $valores[2];
            $energia_FN_ = $valores[3];
            $frequencia_FN_ = $valores[4];
            $fp_FN_ = $valores[5];

            $tensao_FF_ = $valores[6];
            $corrente_FF_ = $valores[7];
            $potencia_FF_ = $valores[8];
            $energia_FF_ = $valores[9];
            $frequencia_FF_ = $valores[10];
            $fp_FF_ = $valores[11];



            
            $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
            $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo


            // Garante que há apenas 1 '}' no final
            $conteudo = rtrim($conteudo); // Remove espaços em branco e novas linhas do final
            while (substr($conteudo, -1) === '}') {
                $conteudo = substr($conteudo, 0, -1); // Remove o último caractere '}'
            }
            $conteudo .= '}'; // Adiciona um único '}' no final




            $Objeto_json = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

            if (json_last_error() === JSON_ERROR_NONE) {    // Verifica se a decodificação foi bem-sucedida
                                                            // Atribui os valores a variáveis
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





                                                            // Aqui append o valor recem medido ao arquivo de registro


                $tensao_FN[] = (float)$tensao_FN_;
                $tensao_FF[] = (float)$tensao_FF_;
                $corrente_FN[] = (float)$corrente_FN_;
                $corrente_FF[] = (float)$corrente_FF_;
                $potencia_FN[] = (float)$potencia_FN_;
                $potencia_FF[] = (float)$potencia_FF_;
                $energia[] = round(((float)$energia_FN_ + (float)$energia_FF_) - $kwh_fechamento_mes_anterior, 3);
                $frequencia_FN[] = (float)$frequencia_FN_;
                $frequencia_FF[] = (float)$frequencia_FF_;
                $fp_FN[] = (float)$fp_FN_;
                $fp_FF[] = (float)$fp_FF_;
                $tempo_ano[] = $ano;
                $tempo_mes[] = $mes;
                $tempo_dia[] = $dia;
                $tempo_hora[] = $hora;
                $tempo_minuto[] = $minuto;
                $tempo_segundo[] = $segundo;
                $tempo_dia_da_semana[] = $dia_da_semana;
                $tempo_nome_do_mes[] = $nome_do_mes;
                $consumo = round(((float)$energia_FN_ + (float)$energia_FF_) - $kwh_fechamento_mes_anterior, 3);
                // $dia_semana[] = [];
                // $dia_mes[] = [];
                // $consumo_dia_mes[] = [];
                // $horas_ligado_semanal[] = [];
                // $meses[] = [];
                // $consumo_meses[] = [];
                // $dias_leitura[] = [];


                

                
                $NEW_Objeto_json = [                         // Cria um novo array associativo para o novo objeto
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


                $novo_conteudo_json = json_encode($NEW_Objeto_json, JSON_PRETTY_PRINT); // Codifica o novo objeto como JSON      // JSON_PRETTY_PRINT para uma melhor legibilidade


                // Garante que há apenas 1 '}' no final:
                $novo_conteudo_json = rtrim($novo_conteudo_json); // Remove espaços em branco e novas linhas do final
                while (substr($novo_conteudo_json, -1) === '}') {
                    $novo_conteudo_json = substr($novo_conteudo_json, 0, -1); // Remove o último caractere
                }
                $novo_conteudo_json .= '}'; // Adiciona um único '}' no final





                // Abre o arquivo em modo de escrita para sobrescrever o conteúdo
                $handle = fopen($arquivo, "w");

                if ($handle) {
                    fwrite($handle, $novo_conteudo_json);                      // Modifica (acrescenta conteudo) no arquivo
                    fclose($handle);
                    echo 'Conteudo adicionado';
                } else {
                    echo "Erro ao abrir o arquivo.";
                }


            } else {
                echo "Erro ao decodificar JSON: " . json_last_error_msg();
            }








        }


    }
    else {
        echo "Senha incorreta.";
    }
    
} else {
    echo "Entrada errada.";
}
?>

