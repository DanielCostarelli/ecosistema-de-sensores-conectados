<?php


    if (isset($_POST['inputs'])) {
        $inputs = $_POST['inputs'];
        $inputs_array = explode(',', $inputs);
        $inputs_array = array_map('intval', $inputs_array);
        

        $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
        $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo
        $Objeto_json = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

        if (json_last_error() === JSON_ERROR_NONE) {    // Verifica se a decodificação foi bem-sucedida
                                                        // Atribui os valores a variáveis
            $temperatura = $Objeto_json['temperatura'];
            $status = $Objeto_json['status'];
            $ano = $Objeto_json['ano'];
            $mes = $Objeto_json['mes'];
            $dia = $Objeto_json['dia'];
            $hora = $Objeto_json['hora'];
            $minuto = $Objeto_json['minuto'];
            $segundo = $Objeto_json['segundo'];
            $dia_da_semana = $Objeto_json['dia_da_semana'];
            $nome_do_mes = $Objeto_json['nome_do_mes'];
        

            
            $NEW_Objeto_json = [                         // Cria um novo array associativo para o novo objeto
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
                "inputs" => $inputs_array
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


        } else {
            echo "Erro ao decodificar JSON: " . json_last_error_msg();
        }





    } else {
        echo "Nenhum valor recebido.";
    }
?>
