<?php

if (isset($_GET['key'])) {                                  // Verifica se a variável 'key' foi enviada via GET
    $url = $_GET['key'];                                    // url = valor recebido via GET
    $valores = explode("//", $url);                         // Dividindo a url em duas partes usando '//' como delimitador
    $chave = $valores[1];
    $chave = trim($chave, "'");                             // Removendo aspas da chave, se necessário

    if ($chave == '1478621p') {                             // Se a senha estiver correta:


        $variavel = $valores[0];
        $variavel = json_decode($variavel);                 // Transforma a string valores em um array
        if (is_array($variavel)) {                          // Se for um array:
            $variavel = array_map('strval', $variavel);     // Converte cada elemento para string
            $temperatura = $variavel[0];
            $status = ($variavel[1] ? "true" : "false");




            $ano = null;                                        // inicia as variaveis de tempo, que indicarão o ultimo acesso desse php com o esp 
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
                                                                // retorna nesse formato:     ["26","09","2024","Quinta-feira","22","46","58"]
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
            




            
            $arquivo = "Registro.json";                         // Caminho do arquivo onde o conteúdo esta armazenado
            $conteudo = file_get_contents($arquivo);            // Lê o conteúdo do arquivo
            $Objeto_json = json_decode($conteudo, true);        // Decodifica o JSON    // 'true' para retornar um array associativo

            if (json_last_error() === JSON_ERROR_NONE) {        // Verifica se a decodificação foi bem-sucedida
                                                                // Atribui os valores a variáveis
                $inputs = $Objeto_json['inputs'];               // Lê o inputs e grava a uma variavel

                $NEW_Objeto_json = [                            // Cria um novo array associativo para o novo objeto
                    "temperatura" => $temperatura,
                    "status" => $status,
                    "ano" => $ano,
                    "mes" => $mes,
                    "dia" => $dia,
                    "hora" => $hora,
                    "minuto" => $minuto,
                    "segundo" => $segundo,
                    "dia_da_semana" => $dia_da_semana,
                    "nome_do_mes" => $nome_do_mes,
                    "inputs" => $inputs,
                ];


                $novo_conteudo_json = json_encode($NEW_Objeto_json, JSON_PRETTY_PRINT); // Codifica o novo objeto como JSON      // JSON_PRETTY_PRINT para uma melhor legibilidade

                // Abre o arquivo em modo de escrita para sobrescrever o conteúdo
                $handle = fopen($arquivo, "w");

                if ($handle) {
                    fwrite($handle, $novo_conteudo_json);                      // Modifica (acrescenta conteudo) no arquivo
                    fclose($handle);
                    echo 'Conteudo adicionado';
                } else {
                    echo "Erro ao abrir o arquivo.";
                }


                $Objeto_de_resposta_ao_esp = [                            // Cria um novo array associativo para o novo objeto
                    "hora" => (int)$hora,
                    "minuto" => (int)$minuto,
                    "segundo" => (int)$segundo,
                    "inputs" => $inputs
                ];

                $Objeto_de_resposta_ao_esp = json_encode($Objeto_de_resposta_ao_esp, JSON_PRETTY_PRINT);
                echo "//";
                echo $Objeto_de_resposta_ao_esp;


            } else {
                echo "Erro ao decodificar JSON: " . json_last_error_msg();
            }




        }






        




    } else {
        echo "Senha incorreta.";
    }
    
} else {
    echo "Entrada errada.";
}
?>

