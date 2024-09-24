<?php

$dados = require 'dados.php';

$contador = count($dados);

echo $contador;

function somarMedalhas($medalhasAcumuladas, $medalha) {
    return $medalhasAcumuladas + $medalha;
}

function medalhasAcumuladas($medalhasAcumuladas, $pais) {
    return $medalhasAcumuladas + array_reduce($pais['medalhas'], 'somarMedalhas', 0);
}

function converterPaisParaLetrasMaiusculas ($pais) {
    $pais['pais'] = mb_convert_case(($pais['pais']), MB_CASE_UPPER);
    return $pais;
}

function verificarSePaisTemEspacoNoNome($pais)
{
    return strpos($pais['pais'], ' ') !== false;
}

$dados = array_map('converterPaisParaLetrasMaiusculas', $dados);
$dados = array_filter($dados, 'verificarSePaisTemEspacoNoNome');

var_dump($dados);

echo array_reduce($dados, 'medalhasAcumuladas', 0);