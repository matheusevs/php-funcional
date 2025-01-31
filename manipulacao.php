<?php

use Alura\Fp\Maybe;

require_once 'vendor/autoload.php';

$dados = require 'dados.php';

$contador = count($dados->getOrElse([]));

echo $contador;

$somarMedalhas = fn ($medalhasAcumuladas, $medalha) => $medalhasAcumuladas + $medalha;

function converterPaisParaLetrasMaiusculas ($pais) {
    $pais['pais'] = mb_convert_case(($pais['pais']), MB_CASE_UPPER);
    return $pais;
}

$verificarSePaisTemEspacoNoNome = fn ($pais) => strpos($pais['pais'], ' ') !== false;

$compararMedalhas = fn ($medalhasPais1, $medalhasPais2) => fn ($modalidade) => $medalhasPais2[$modalidade] <=> $medalhasPais1[$modalidade];

$nomeDePaisesEmMaisculo = fn (Maybe $dados) => Maybe::of(array_map('converterPaisParaLetrasMaiusculas', $dados->getOrElse([])));
$filtrarPaisesSemEspacoNome = fn (Maybe $dados) => Maybe::of(array_filter($dados->getOrElse([]), $verificarSePaisTemEspacoNoNome));

$funcoes = \igorw\pipeline(
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
