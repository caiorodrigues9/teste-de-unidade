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

        $leiladao = new LeilaoDao();
        $leiladao->salva($fiat147);
        $leiladao->salva($variant);

        $encerrador = new Encerrador();
        $encerrador->encerra();

        $leiloes = $leiladao->recuperarFinalizados();
        self::assertCount(2,$leiloes);
        self::assertEquals('Fiat 147 0KM',$leiloes[0]->recuperarDescricao());
        self::assertEquals('Variant 1982 0KM',$leiloes[1]->recuperarDescricao());
    }
}