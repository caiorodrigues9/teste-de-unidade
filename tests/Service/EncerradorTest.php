<?php

namespace Service;

use Caio\Leilao\Dao\Leilao as LeilaoDao;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;


class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao('Fiat 147 0KM',new \DateTimeImmutable('8 days ago'));
        $variant = new Leilao('Variant 1982 0KM',new \DateTimeImmutable('10 days ago'));

        $leilaodao = self::createMock(LeilaoDao::class);
        /*$leilaodao = self::getMockBuilder(LeilaoDao::class)
            ->setConstructorArgs([new \PDO('sqlite::memory:')])
            ->getMock();*/
        $leilaodao->method('recuperarNaoFinalizados')
            ->willReturn(
                [$fiat147,$variant]
            );

        $leilaodao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$fiat147],
                [$variant]
            );

        $encerrador = new Encerrador($leilaodao);

        $encerrador->encerra();

        $leiloes =[$fiat147,$variant];


        self::assertCount(2,$leiloes);
        self::assertEquals('Fiat 147 0KM',$leiloes[0]->recuperarDescricao());
        self::assertEquals('Variant 1982 0KM',$leiloes[1]->recuperarDescricao());
        self::assertTrue($leiloes[0]->leilaoEstaFinalizado());
        self::assertTrue($leiloes[1]->leilaoEstaFinalizado());
    }
}