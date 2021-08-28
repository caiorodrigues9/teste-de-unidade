<?php

namespace Caio\Leilao\Tests\Integration\Dao;

use Caio\Leilao\Infra\ConnectionCreator;
use Caio\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    public function testInsercaoEBuscaDevemFuncionar()
    {
        $leilao = new Leilao('Variant 0KM',new \DateTimeImmutable('8 days ago'));
        $leilaoDao = new \Caio\Leilao\Dao\Leilao(ConnectionCreator::getConnection());
        $leilaoDao->salva($leilao);
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1,$leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class,$leiloes);
        self::assertSame('Variant 0KM',$leiloes[0]->recuperarDescricao());
    }
}