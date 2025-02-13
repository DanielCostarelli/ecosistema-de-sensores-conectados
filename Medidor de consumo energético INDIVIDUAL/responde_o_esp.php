<?php
// é esse q ta sendo usado!!!!!!!!!!!!!!!


///////// RESUMO:
// recebe do esp o conteudo dos sensores e uma senha
// se a senha for correta:
// recebe o conteudo e decompoe em variaveis para cada informação...
// 
// abre o Registro.json e decompoe cada objeto e array em uma variavel
// acrescenta/append o valor recem recebido do esp ao fim do array da variavel de registro
// forma um novo objeto formatado identico ao do Registro.json, mas com novas medições adicionadas ao fim dos arrays, 
// sobreescreve o Registro.json
// 
// acessa um php que obtem e informa a data e hora
// retorna a data e hora ao esp
//






///////////////  MUDANÇAS:
// removi a variavel $status_ e mais algumas.
// adicionei a verificação de status aqui no php, e se houver potencia ou corrente é ligado.
// o $status do JSON recebe essa variavel, que agora é criada aqui, e nao trazida do esp.
// trouxe/movi o codigo que importa a data e hora para cima, logo após verificar a senha.
// adicionei no json de registro:                  (onde cada indice é um momento) (o item tensão[123] pertence a medição junto com corrente[123], no tempo hora[123] minuto[123] e segundo[123])   
    // "data_e_hora": {
    //     "ano": [],
    //     "mes": [],
    //     "dia": [],
    //     "hora": [],
    //     "minuto": [],
    //     "segundo": [],
    //     "dia_da_semana": [],
    //     "nome_do_mes": []
    // },
// agora junto com a recepção do sensor do esp, é recebido a data e hora, não sendo necessário receber data e hora do esp.
// os valores de tempo a serem armazenados no json partirão desses aqui.
// cada vez que é executado, ele add o tempo no json.
// MUDAR EM TODOS OS PHP O FORMATO De recepção do JSON
// apaguei o objeto 'gastos_de_hoje' do JSON, pois não é algo que é armazenado, é apenas atualizado, e pode ser verificado entre JS e PHP.
// para determinar gastos_de_hoje, o JS do site assim que onload.window, e cada vez que atualiza, faz uma função que deve acessar um novo php, chamado Gastos_Consumo_de_hoje.php.  Nesse novo php ele acessa a data e hora atual, e puxa o objeto do json, e na data e hora do json ele filtra para encontrar os indices que correspondem a data de desde 00h até a hora atual.  e então usar esse indice (o indice das 00h e o indice da ultima leitura do dia de hoje) para pegar o consumo de energia do ultimo indice menos o consumo do primeiro indice. E também ele vê quantas horas dá, e retorna um array tipo  [0.92, "04:11"]. E então o JS recebe e exibe na tela no html.




///////////// MUDAR NO ESP:
// remover/tirar o codigo de teste de status para determinar se ta ligado ou desligado.
// remover/tirar o status da lista de envio ao url, pois nao recebe mais e daria confusão.
// remover/tirar a parte que envia a data ao php. tirando totalmente da lista de envio da url.  (receber pode, e ainda vai, mas é opcional, pois só serve para imprimir)
// no array de envio da URL deve estar assim:   [tensao, corrente, potencia, energia, frequencia, fp],   onde ele envia apenas os dados dos sensores, e o backend quem se resolve a idenificar e separar as coisas.






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

            if (((float)$potencia_ >= 2.0  ||  ((float)$corrente_ >= 0.1) && (float)$fp_ >= 0.9)) { $status_ = "Ligado"; }
            else { $status_ = "Desligado"; }



            



            
            $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
            $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo

            // Garante que há apenas 1 '}' no final
            $conteudo = rtrim($conteudo); // Remove espaços em branco e novas linhas do final
            while (substr($conteudo, -1) === '}') {
                $conteudo = substr($conteudo, 0, -1); // Remove o último caractere '}'
            }
            $conteudo .= '}'; // Adiciona um único '}' no final



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

