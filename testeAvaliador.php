<?php

use Caio\Leilao\Model\Lance;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Model\Usuario;
use Caio\Leilao\Service\Avaliador;

require 'vendor/autoload.php';

// Arrange - Given
$leilao = new Leilao('Fiat 147 0KM');

$maria = new Usuario('Maria');
$joao = new Usuario('João');

$leilao->recebeLance(new Lance($joao,2000));
$leilao->recebeLance(new Lance($maria,2500));

$leiloeiro = new Avaliador();

// Act - When

$leiloeiro->avalia($leilao);

$maiorValor = $leiloeiro->getMaiorValor();

//Assert - Then
$valorEsperado = 2500;

if($maiorValor == $valorEsperado){
    echo 'TESTE OK';
}else{
    echo 'TESTE FALHOU';
}


