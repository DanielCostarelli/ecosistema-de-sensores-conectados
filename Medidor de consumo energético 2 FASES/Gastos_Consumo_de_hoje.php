        


<?php
/////////////////////////MUDAR O CODIGO PARA RECONHECER DESDE AS 00 ATÉ O MOMENTO (ULTIMO ELEMENTO), já que está sempre ligado






///////// RESUMO:

// acessa a data e hora atual, 
// puxa o objeto do json do registro
// e na data e hora do json ele filtra para encontrar os indices que correspondem a data de desde 00h até a hora atual.
// e então usar esse indice (o indice das 00h e o indice da ultima leitura do dia de hoje) para pegar o consumo de energia do ultimo indice menos o consumo do primeiro indice. 
// E também ele vê quantas horas dá, e retorna um array tipo  [0.92, "04:11"]. 
// E então o JS recebe e exibe na tela no html.



/////////////////////////////////////////////////////////////////////  RECEBE A DATA E HORA


        $ano = null;                                        // inicia as variaveis de tempo
        $mes = null;
        $dia = null;
        $hora = null;
        $minuto = null;
        $segundo = null;

        ob_start();                                         // Incluir o arquivo que retorna as datas e horas   // Inicia o buffer de saída
        include 'obtem_data_e_hora.php';                    // Executa o código do arquivo
        $data_e_hora_json = ob_get_clean();                 // Obtém o conteúdo gerado pelo echo e limpa o buffer
        if ($data_e_hora_json) {
                $hora = json_decode($data_e_hora_json)[4];
                $minuto = json_decode($data_e_hora_json)[5];
        }

/////////////////////////////////////////////////////////////////////  RECEBE LISTAS DE CORRENTE, POTENCIA, ENERGIA, LISTAS DE HORA E MINUTOS DE CADA LEITURA



        $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
        $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo
        $Objeto_jon = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

        if (json_last_error() === JSON_ERROR_NONE) {
                $corrente = $Objeto_jon['corrente'];
                $potencia = $Objeto_jon['potencia'];
                $fp = $Objeto_jon['fp'];
                $energia = $Objeto_jon['energia'];

                $tempo_hora = $Objeto_jon['data_e_hora']['hora'];
                $tempo_minuto = $Objeto_jon['data_e_hora']['minuto'];






/////////////////////////////////////////////////////////////////////  DETERMINA O INDICE DE SEPARAÇÃO DE AGORA ATÉ AS 00h DE HOJE CEDO

                
                $horas = $tempo_hora;                                           // Sua lista de horas

                $indiceUltimo00 = -1;
                for ($i = count($horas) - 1; $i >= 0; $i--) {                   // Encontrar o índice do último "00"
                        if ($horas[$i] === "00") {
                                $indiceUltimo00 = $i;
                                break;
                        }
                }

                if ($indiceUltimo00 == -1) {                                    // Verificar se encontrou um "00"
                        // echo "Nenhum valor '00' encontrado na lista.";
                        exit;
                }

                
                $indice23MaisProximo = -1;
                for ($i = $indiceUltimo00; $i >= 0; $i--) {                     // Encontrar o índice do valor "23" mais próximo antes do grupo de "00"
                        if ($horas[$i] === "23") {
                                $indice23MaisProximo = $i;
                                break;
                        }
                }

                if ($indice23MaisProximo == -1) {                               // Verificar se encontrou o "23"
                        // echo "Nenhum valor '23' encontrado antes do '00' na lista.";
                        exit;
                }

                $indicePrimeiro00 = -1;
                for ($i = $indice23MaisProximo + 1; $i < count($horas); $i++) { // Encontrar o índice do primeiro "00" após o "23"
                        if ($horas[$i] === "00") {
                                $indicePrimeiro00 = $i;
                                break;
                        }
                }


/////////////////////////////////////////////////////////////////////  DETERMINA O CONSUMO DE HOJE,  E MANIPULA ALGUMAS LISTAS


                if ($indicePrimeiro00 != -1) {                                  // Exibindo o resultado

                        $consumo_hoje = (float)$energia[count($energia) - 1] - (float)$energia[$indicePrimeiro00];      // recebe o consumo da hora atual menos o consumo das 00h
                        

                        $corrente_hoje = array_slice($corrente, $indicePrimeiro00);                                     // separa a lista de corrente de apenas hoje.
                        $potencia_hoje = array_slice($potencia, $indicePrimeiro00);
                        $fp_hoje = array_slice($fp, $indicePrimeiro00);
                        $status_hoje = [];

                        for ($i = 0; $i < count($corrente_hoje); $i++) {
                                if (((float)$corrente_hoje[$i] >= 0.01 || (float)$potencia_hoje[$i] >= 0.1) && (float)$fp_hoje[$i] >= 0.2) {            // cria uma lista de status para a condição de corrente e potencia no medidor
                                        $status_hoje[] = 1;
                                } else {
                                        $status_hoje[] = 0;
                                }
                        }

/////////////////////////////////////////////////////////////////////  DETERMINA O TOTAL DE MINUTOS QUE LIGOU O AR CONDICIONADO HOJE

                        $soma_minutos_empreitada = 0;           // Inicializa a soma dos minutos
                        $primeiro_index_1 = -1;                 // Índice do primeiro 1
                        $primeiro_index_0 = -1;                 // Índice do primeiro 0
                        $contagem_segundos = 0;
                        
                        for ($i = 0; $i < count($status_hoje); $i++) {                                  // Percorre a lista de status
                                if ($status_hoje[$i] == 1) {
                                        $contagem_segundos += 10.64;             // 10.64 é uma média, que significa que cada medição dura aproximadamente 10.64 segundos, mas pode variar de 10 a 11 segundos. O correto seria analizar cada trecho de 1 no meio dos 0, e identificar os horarios, e subtrair. mas assim funciona provisoriamente bem.
                                }
                        }
                        $soma_minutos_empreitada = $contagem_segundos / 60;



/////////////////////////////////////////////////////////////////////  FORMATA O NUMERO DE MINUTOS PARA hh:mm


                        $ho = floor($soma_minutos_empreitada / 60);                     // Obtém o número total de horas
                        $mi = $soma_minutos_empreitada % 60;               // Obtém o número de minutos restantes
                        $horas_ligado = sprintf('%02d:%02d', $ho, $mi);                 // Formata a saída com zeros à esquerda
                        // echo $soma_minutos_empreitada;                                      // Exibe o resultado


/////////////////////////////////////////////////////////////////////  RETORNA AO JAVASCRIPT            (essa parte e todos os echo ta meme pq nem foi testado)
// antes de isso funcionar, deve-se atualizar todo o conteudo modificado antes disso e deixar rodar por um tempo, com o ventilador desligado e ligado algum tempo.
// depois deixar apenas 1 echo, com esse array ai abaixo:


                        
                        $resultado = [
                                'consumo_hoje' => $consumo_hoje,
                                'horas_ligado' => $horas_ligado
                        ];
                        echo json_encode($resultado);
                        
                        


/////////////////////////////////////////////////////////////////////



                } else {
                        // echo "Não foi possível encontrar o primeiro '00' após o '23'.";
                }









        } else {
                // echo "Erro ao decodificar JSON: " . json_last_error_msg();
        }












?>


        