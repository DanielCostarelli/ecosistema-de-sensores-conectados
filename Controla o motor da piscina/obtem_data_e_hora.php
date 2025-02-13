<?php

date_default_timezone_set('America/Cuiaba'); // Define o fuso horário para Cuiabá

// Obtendo a data e hora atuais
// $dataCompleta = date('Y-m-d H:i:s'); // Formato: Ano-Mês-Dia Hora:Minuto:Segundo

// Armazenando componentes da data em variáveis
$ano = date('Y');          // Ano
$mes = date('m');         // Mês (com dois dígitos)
$diaDoMes = date('d');    // Dia do mês (com dois dígitos)
$diaDaSemana = date('w');  // Dia da semana (0=domingo, 1=segunda, ..., 6=sábado)
$hora = date('H');        // Hora (24 horas)
$minuto = date('i');      // Minuto
$segundo = date('s');     // Segundo

// Strings para os dias da semana em português
$diasDaSemana = ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sabado"];

echo json_encode([$diaDoMes, $mes, $ano, $diasDaSemana[$diaDaSemana], $hora, $minuto, $segundo]);

?>
