<?php

$dados = require 'dados.php';

$contador = count($dados);

echo $contador;

$somarMedalhas = fn ($medalhasAcumuladas, $medalha) => $medalhasAcumuladas + $medalha;

function converterPaisParaLetrasMaiusculas ($pais) {
    $pais['pais'] = mb_convert_case(($pais['pais']), MB_CASE_UPPER);
    return $pais;
}

$verificarSePaisTemEspacoNoNome = fn ($pais) => strpos($pais['pais'], ' ') !== false;

$compararMedalhas = fn ($medalhasPais1, $medalhasPais2) => fn ($modalidade) => $medalhasPais2[$modalidade] <=> $medalhasPais1[$modalidade];

$nomeDePaisesEmMaisculo = fn ($dados) => array_map('converterPaisParaLetrasMaiusculas', $dados);
$filtrarPaisesSemEspacoNome = fn ($dados) => array_filter($dados, $verificarSePaisTemEspacoNoNome);

function pipe(...$funcoes)
{
    return fn ($valor) => array_reduce(
        $funcoes, 
        fn ($valorAcumulado, $funcaoAtual) => $funcaoAtual($valorAcumulado), 
        $valor
    );
}

$funcoes = pipe(
    $nomeDePaisesEmMaisculo, 
    $filtrarPaisesSemEspacoNome
);

$dados = $funcoes($dados);

$medalhas = array_reduce(
    array_map(fn ($medalhas) => array_reduce($medalhas, $somarMedalhas, 0),
    array_column($dados, 'medalhas')),
    $somarMedalhas,
    0
);

usort($dados, function ($pais1, $pais2) use ($compararMedalhas) {
    $medalhasPais1 = $pais1['medalhas'];
    $medalhasPais2 = $pais2['medalhas'];
    $comparador = $compararMedalhas($medalhasPais1, $medalhasPais2);

    return $comparador('ouro') !== 0 ? $comparador('ouro') 
        : ($comparador('prata') !== 0 ? $comparador('prata') 
        : $comparador('bronze'));
});

var_dump($dados);

echo $medalhas;
