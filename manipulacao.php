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

function compararMedalhas($medalhasPais1, $medalhasPais2)
{
    return function($modalidade) use ($medalhasPais1, $medalhasPais2) {
        return $medalhasPais2[$modalidade] <=> $medalhasPais1[$modalidade];
    };
}

$dados = array_map('converterPaisParaLetrasMaiusculas', $dados);
$dados = array_filter($dados, 'verificarSePaisTemEspacoNoNome');

$medalhas = array_reduce(
    array_map(function ($medalhas) {
        return array_reduce($medalhas, 'somarMedalhas', 0);
    }, array_column($dados, 'medalhas')),
    'somarMedalhas',
    0
);

usort($dados, function ($pais1, $pais2) {
    $medalhasPais1 = $pais1['medalhas'];
    $medalhasPais2 = $pais2['medalhas'];
    $comparador = compararMedalhas($medalhasPais1, $medalhasPais2);

    return $comparador('ouro') !== 0 ? $comparador('ouro') 
        : ($comparador('prata') !== 0 ? $comparador('prata') 
        : $comparador('bronze'));
});

var_dump($dados);

echo $medalhas;