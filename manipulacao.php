<?php

$dados = require 'dados.php';

$contador = count($dados);

echo $contador;

function converterPaisParaLetrasMaiusculas ($pais) {
    $pais['pais'] = mb_convert_case(($pais['pais']), MB_CASE_UPPER);
    return $pais;
}

$dados = array_map('converterPaisParaLetrasMaiusculas', $dados);
var_dump($dados);