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

            $temperatura = $Objeto_json['temperatura'];
            $status = $Objeto_json['status'];
            $ano = $Objeto_json['ano'];
            $nome_do_mes = $Objeto_json['nome_do_mes'];
            $dia = $Objeto_json['dia'];
            $hora = $Objeto_json['hora'];
            $minuto = $Objeto_json['minuto'];
            $segundo = $Objeto_json['segundo'];
            $inputs = $Objeto_json['inputs'];
            
            if ($status == 'true') {
                $status = "Ligado";
            } else {
                $status = "Desligado";
            }

            $dados = [                         // Cria um novo array associativo para o novo objeto
                        "temperatura" => $temperatura,
                        "status" => $status,
                        "ano" => $ano,
                        "nome_do_mes" => $nome_do_mes,
                        "dia" => $dia,
                        "hora" => $hora,
                        "minuto" => $minuto,
                        "segundo" => $segundo,
                        "inputs" => $inputs,
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
