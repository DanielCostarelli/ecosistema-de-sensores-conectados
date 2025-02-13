<?php
// é esse q ta sendo usado!!!!!!!!!!!!!!!


///////// RESUMO:
// recebe do esp o conteudo dos sensores e uma senha
// se a senha for correta:
// recebe o conteudo e decompoe em variaveis para cada informação...
// 
// abre o Registro.json e decompoe cada chave e array em uma variavel
// acrescenta/append o valor recem recebido do esp ao fim do array da variavel de registro
// forma um novo objeto formatado identico ao do Registro.json, mas com novas medições adicionadas ao fim dos arrays, 
// sobreescreve o Registro.json
// 
// acessa um php que obtem e informa a data e hora
// retorna a data e hora ao esp
//
// ele verifica se o registro.json esta corrompido (ja aconteceu diversas vezes dele inserir um '}' a mais ou remover a '}' final do objeto, deixando incapaz de ser lido).
// se houver 2 chaves no fim, ele remove uma e salva.
// se houver nenhuma chave no fim, ele acrescenta uma e salva.
//










if (isset($_GET['key'])) {                                  // Verifica se a variável 'key' foi enviada via GET
    $mensagem = $_GET['key'];                               // Mensagem = valor recebido via GET
    $partes = explode("//", $mensagem);                     // Dividindo a mensagem em duas partes usando '//' como delimitador
    $chave = $partes[1];
    $chave = trim($chave, "'");                             // Removendo aspas da chave, se necessário

    if ($chave == 'admin124') {                             // Se a senha estiver correta:

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
            $tensao_ = $valores[0];
            $corrente_ = $valores[1];
            $potencia_ = $valores[2];
            $energia_ = $valores[3];
            $frequencia_ = $valores[4];
            $fp_ = $valores[5];

            if (((float)$corrente_ >= 0.1  ||  ((float)$potencia_ >= 0.1)) && (float)$fp_ <= 0.8) { $status_ = "Ligado"; }
            else { $status_ = "Desligado"; }


            



            
            $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
            $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo




            $Objeto_jon = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

            if (json_last_error() === JSON_ERROR_NONE) {    // Verifica se a decodificação foi bem-sucedida
                                                            // Atribui os valores a variáveis
                $tensao = $Objeto_jon['tensao'];
                $corrente = $Objeto_jon['corrente'];
                $potencia = $Objeto_jon['potencia'];
                $energia = $Objeto_jon['energia'];
                $frequencia = $Objeto_jon['frequencia'];
                $fp = $Objeto_jon['fp'];
                $status = $Objeto_jon['status'];
                $tempo_ano = $Objeto_jon['data_e_hora']['ano'];
                $tempo_mes = $Objeto_jon['data_e_hora']['mes'];
                $tempo_dia = $Objeto_jon['data_e_hora']['dia'];
                $tempo_hora = $Objeto_jon['data_e_hora']['hora'];
                $tempo_minuto = $Objeto_jon['data_e_hora']['minuto'];
                $tempo_segundo = $Objeto_jon['data_e_hora']['segundo'];
                $tempo_dia_da_semana = $Objeto_jon['data_e_hora']['dia_da_semana'];
                $tempo_nome_do_mes = $Objeto_jon['data_e_hora']['nome_do_mes'];
                $consumo = $Objeto_jon['Consumo_ate_o_momento']['kwh'];
                $dia_semana = $Objeto_jon['Gastos_da_semana']['dia_semana'];
                $dia_mes = $Objeto_jon['Gastos_da_semana']['dia_mes'];
                $consumo_dia_mes = $Objeto_jon['Gastos_da_semana']['kwh'];
                $horas_ligado_semanal = $Objeto_jon['Gastos_da_semana']['horas'];
                $meses = $Objeto_jon['Consumo_meses']['mes'];
                $consumo_meses = $Objeto_jon['Consumo_meses']['kwh'];
                $dias_leitura = $Objeto_jon['Consumo_meses']['dias'];
                $resetar_leitura = $Objeto_jon['resetar_leitura'];
                $preco_unitario_kwh = $Objeto_jon['preco_unitario_kwh']; 
                $kwh_fechamento_mes_anterior = $Objeto_jon['kwh_fechamento_mes_anterior'];





                                                            // Aqui append o valor recem medido ao arquivo de registro


                $tensao[] = (float)$tensao_;
                $corrente[] = (float)$corrente_;
                $potencia[] = (float)$potencia_;
                $energia[] = round((float)$energia_ - $kwh_fechamento_mes_anterior, 2);
                $frequencia[] = (float)$frequencia_;
                $fp[] = (float)$fp_;
                $status = $status_;
                $tempo_ano[] = $ano;
                $tempo_mes[] = $mes;
                $tempo_dia[] = $dia;
                $tempo_hora[] = $hora;
                $tempo_minuto[] = $minuto;
                $tempo_segundo[] = $segundo;
                $tempo_dia_da_semana[] = $dia_da_semana;
                $tempo_nome_do_mes[] = $nome_do_mes;
                $consumo = round((float)$energia_ - $kwh_fechamento_mes_anterior, 2);
                // $dia_semana[] = [];
                // $dia_mes[] = [];
                // $consumo_dia_mes[] = [];
                // $horas_ligado_semanal[] = [];
                // $meses[] = [];
                // $consumo_meses[] = [];
                // $dias_leitura[] = [];


                

                
                $NEW_Objeto_jon = [                         // Cria um novo array associativo para o novo objeto
                    "tensao" => $tensao,
                    "corrente" => $corrente,
                    "potencia" => $potencia,
                    "energia" => $energia,
                    "frequencia" => $frequencia,
                    "fp" => $fp,
                    "status" => $status,
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
                    "kwh_fechamento_mes_anterior" => $kwh_fechamento_mes_anterior,

                ];


                $novo_conteudo_json = json_encode($NEW_Objeto_jon, JSON_PRETTY_PRINT); // Codifica o novo objeto como JSON      // JSON_PRETTY_PRINT para uma melhor legibilidade






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
                // se o arquivo lido não é um Objeto válido (JSON): tratar ele como string e ver a quantidade de chaves no final.
                
                $conteudo_limpo = preg_replace('/\s+/', '', $conteudo);                 // Transforma o conteudo de Registro.json em string, e remove os espaços e quebra de linha.
                $ultimos_caracteres = substr($conteudo_limpo, -40);                     // Pega os últimos 40 caracteres para análise

                if (substr($ultimos_caracteres, -2) === '}}') {                         // Se termina com '}}':
                    $conteudo_limpo = substr($conteudo_limpo, 0, -1);                   // Remove o último '}'
                } else {                                                                // Se não:
                    $conteudo_limpo .= '}';                                             // Adiciona um '}'
                }

                $objeto = json_decode($conteudo_limpo, true);                           // Tenta decodificar novamente
                $objeto_final = json_encode($objeto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                if (file_put_contents($arquivo, $objeto_final) !== false) {             // Salva o JSON formatado no arquivo
                    echo 'Conteúdo salvo com sucesso';
                } else {
                    echo "Erro ao salvar o arquivo.";
                }

                
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

