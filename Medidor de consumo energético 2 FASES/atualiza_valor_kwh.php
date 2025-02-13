// <?php


// //////////ATUALIZA O VALOR DO OBJETO DO CONSUMO DE FECHAMENTO DO MES ANTERIOR, PARA ZERAR O CONSUMO:

// if (isset($_POST['Consumo_ate_o_momento'])) {
//     $Consumo_ate_o_momento = (float) $_POST['Consumo_ate_o_momento'];

//     $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
//     $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo
//     $Objeto_jon = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

//     if (json_last_error() === JSON_ERROR_NONE) {    // Verifica se a decodificação foi bem-sucedida
//                                                     // Atribui os valores a variáveis
//         $tensao_FN = $Objeto_json['tensao_FN'];
//         $tensao_FF = $Objeto_json['tensao_FF'];
//         $corrente_FN = $Objeto_json['corrente_FN'];
//         $corrente_FF = $Objeto_json['corrente_FF'];
//         $potencia_FN = $Objeto_json['potencia_FN'];
//         $potencia_FF = $Objeto_json['potencia_FF'];
//         $energia = $Objeto_json['energia'];
//         $frequencia_FN = $Objeto_json['frequencia_FN'];
//         $frequencia_FF = $Objeto_json['frequencia_FF'];
//         $fp_FN = $Objeto_json['fp_FN'];
//         $fp_FF = $Objeto_json['fp_FF'];
//         $tempo_ano = $Objeto_json['data_e_hora']['ano'];
//         $tempo_mes = $Objeto_json['data_e_hora']['mes'];
//         $tempo_dia = $Objeto_json['data_e_hora']['dia'];
//         $tempo_hora = $Objeto_json['data_e_hora']['hora'];
//         $tempo_minuto = $Objeto_json['data_e_hora']['minuto'];
//         $tempo_segundo = $Objeto_json['data_e_hora']['segundo'];
//         $tempo_dia_da_semana = $Objeto_json['data_e_hora']['dia_da_semana'];
//         $tempo_nome_do_mes = $Objeto_json['data_e_hora']['nome_do_mes'];
//         $consumo = $Objeto_json['Consumo_ate_o_momento']['kwh'];
//         $dia_semana = $Objeto_json['Gastos_da_semana']['dia_semana'];
//         $dia_mes = $Objeto_json['Gastos_da_semana']['dia_mes'];
//         $consumo_dia_mes = $Objeto_json['Gastos_da_semana']['kwh'];
//         $horas_ligado_semanal = $Objeto_json['Gastos_da_semana']['horas'];
//         $meses = $Objeto_json['Consumo_meses']['mes'];
//         $consumo_meses = $Objeto_json['Consumo_meses']['kwh'];
//         $dias_leitura = $Objeto_json['Consumo_meses']['dias'];
//         $resetar_leitura = $Objeto_json['resetar_leitura'];
//         $preco_unitario_kwh = $Objeto_json['preco_unitario_kwh']; 
//         $kwh_fechamento_mes_anterior = $Objeto_json['kwh_fechamento_mes_anterior'];





        
//         $NEW_Objeto_jon = [                         // Cria um novo array associativo para o novo objeto
//             "tensao_FN" => $tensao_FN,
//             "tensao_FF" => $tensao_FF,
//             "corrente_FN" => $corrente_FN,
//             "corrente_FF" => $corrente_FF,
//             "potencia_FN" => $potencia_FN,
//             "potencia_FF" => $potencia_FF,
//             "energia" => $energia,
//             "frequencia_FN" => $frequencia_FN,
//             "frequencia_FF" => $frequencia_FF,
//             "fp_FN" => $fp_FN,
//             "fp_FF" => $fp_FF,
//             "data_e_hora" => [
//                 "ano" => $tempo_ano,
//                 "mes" => $tempo_mes,
//                 "dia" => $tempo_dia,
//                 "hora" => $tempo_hora,
//                 "minuto" => $tempo_minuto,
//                 "segundo" => $tempo_segundo,
//                 "dia_da_semana" => $tempo_dia_da_semana,
//                 "nome_do_mes" => $tempo_nome_do_mes
//             ],
//             "Consumo_ate_o_momento" => [
//                 "kwh" => $consumo
//             ],
//             "Gastos_da_semana" => [
//                 "dia_semana" => $dia_semana,
//                 "dia_mes" => $dia_mes,
//                 "kwh" => $consumo_dia_mes,
//                 "horas" => $horas_ligado_semanal
//             ],
//             "Consumo_meses" => [
//                 "mes" => $meses,
//                 "kwh" => $consumo_meses,
//                 "dias" => $dias_leitura
//             ],
//             "resetar_leitura" => $resetar_leitura,
//             "preco_unitario_kwh" => $preco_unitario_kwh,
//             "kwh_fechamento_mes_anterior" => $kwh_fechamento_mes_anterior,

//         ];


//         $novo_conteudo_json = json_encode($NEW_Objeto_jon, JSON_PRETTY_PRINT); // Codifica o novo objeto como JSON      // JSON_PRETTY_PRINT para uma melhor legibilidade

//         // Abre o arquivo em modo de escrita para sobrescrever o conteúdo
//         $handle = fopen($arquivo, "w");

//         if ($handle) {
//             fwrite($handle, $novo_conteudo_json);                      // Modifica (acrescenta conteudo) no arquivo
//             fclose($handle);
//             // echo 'Conteudo adicionado';
//         } else {
//             echo "Erro ao abrir o arquivo.";
//         }


//     } else {
//         echo "Erro ao decodificar JSON: " . json_last_error_msg();
//     }



// }  else {
//     echo "Nenhum valor recebido.";
// }













// /////////////////////////////////

// ///////// ATUALIZA O PREÇO DO kWh NO OBJETO DO JSON:




// if (isset($_POST['valor_preco_kwh_unitario'])) {
//     $valor_preco_kwh_unitario = (float) $_POST['valor_preco_kwh_unitario'];
    


//     $arquivo = "Registro.json";                     // Caminho do arquivo onde o conteúdo esta armazenado
//     $conteudo = file_get_contents($arquivo);        // Lê o conteúdo do arquivo
//     $Objeto_jon = json_decode($conteudo, true);     // Decodifica o JSON    // 'true' para retornar um array associativo

//     if (json_last_error() === JSON_ERROR_NONE) {    // Verifica se a decodificação foi bem-sucedida
//                                                     // Atribui os valores a variáveis
//         $tensao = $Objeto_jon['tensao'];
//         $corrente = $Objeto_jon['corrente'];
//         $potencia = $Objeto_jon['potencia'];
//         $energia = $Objeto_jon['energia'];
//         $frequencia = $Objeto_jon['frequencia'];
//         $fp = $Objeto_jon['fp'];
//         $status = $Objeto_jon['status'];
//         $tempo_ano = $Objeto_jon['data_e_hora']['ano'];
//         $tempo_mes = $Objeto_jon['data_e_hora']['mes'];
//         $tempo_dia = $Objeto_jon['data_e_hora']['dia'];
//         $tempo_hora = $Objeto_jon['data_e_hora']['hora'];
//         $tempo_minuto = $Objeto_jon['data_e_hora']['minuto'];
//         $tempo_segundo = $Objeto_jon['data_e_hora']['segundo'];
//         $tempo_dia_da_semana = $Objeto_jon['data_e_hora']['dia_da_semana'];
//         $tempo_nome_do_mes = $Objeto_jon['data_e_hora']['nome_do_mes'];
//         $consumo = $Objeto_jon['Consumo_ate_o_momento']['kwh'];
//         $dia_semana = $Objeto_jon['Gastos_da_semana']['dia_semana'];
//         $dia_mes = $Objeto_jon['Gastos_da_semana']['dia_mes'];
//         $consumo_dia_mes = $Objeto_jon['Gastos_da_semana']['kwh'];
//         $horas_ligado_semanal = $Objeto_jon['Gastos_da_semana']['horas'];
//         $meses = $Objeto_jon['Consumo_meses']['mes'];
//         $consumo_meses = $Objeto_jon['Consumo_meses']['kwh'];
//         $dias_leitura = $Objeto_jon['Consumo_meses']['dias'];
//         $resetar_leitura = $Objeto_jon['resetar_leitura'];
//         $preco_unitario_kwh = $Objeto_jon['preco_unitario_kwh']; 
//         $kwh_fechamento_mes_anterior = $Objeto_jon['kwh_fechamento_mes_anterior'];





        
//         $NEW_Objeto_jon = [                         // Cria um novo array associativo para o novo objeto
//             "tensao" => $tensao,
//             "corrente" => $corrente,
//             "potencia" => $potencia,
//             "energia" => $energia,
//             "frequencia" => $frequencia,
//             "fp" => $fp,
//             "status" => $status,
//             "data_e_hora" => [
//                 "ano" => $tempo_ano,
//                 "mes" => $tempo_mes,
//                 "dia" => $tempo_dia,
//                 "hora" => $tempo_hora,
//                 "minuto" => $tempo_minuto,
//                 "segundo" => $tempo_segundo,
//                 "dia_da_semana" => $tempo_dia_da_semana,
//                 "nome_do_mes" => $tempo_nome_do_mes
//             ],
//             "Consumo_ate_o_momento" => [
//                 "kwh" => $consumo
//             ],
//             "Gastos_da_semana" => [
//                 "dia_semana" => $dia_semana,
//                 "dia_mes" => $dia_mes,
//                 "kwh" => $consumo_dia_mes,
//                 "horas" => $horas_ligado_semanal
//             ],
//             "Consumo_meses" => [
//                 "mes" => $meses,
//                 "kwh" => $consumo_meses,
//                 "dias" => $dias_leitura
//             ],
//             "resetar_leitura" => $resetar_leitura,
//             "preco_unitario_kwh" => $valor_preco_kwh_unitario,
//             "kwh_fechamento_mes_anterior" => $kwh_fechamento_mes_anterior,

//         ];


//         $novo_conteudo_json = json_encode($NEW_Objeto_jon, JSON_PRETTY_PRINT); // Codifica o novo objeto como JSON      // JSON_PRETTY_PRINT para uma melhor legibilidade

//         // Abre o arquivo em modo de escrita para sobrescrever o conteúdo
//         $handle = fopen($arquivo, "w");

//         if ($handle) {
//             fwrite($handle, $novo_conteudo_json);                      // Modifica (acrescenta conteudo) no arquivo
//             fclose($handle);
//             // echo 'Conteudo adicionado';
//         } else {
//             echo "Erro ao abrir o arquivo.";
//         }


//     } else {
//         echo "Erro ao decodificar JSON: " . json_last_error_msg();
//     }





// } else {
//     echo "Nenhum valor recebido.";
// }
// ?>
