<?php

namespace Service;

use Caio\Leilao\Dao\Leilao as LeilaoDao;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class LeilaoDaomock extends LeilaoDao
{
    private $leiloes = [];
    public function salva(Leilao $leilao) : void
    {
        $this->leiloes[] = $leilao;
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return !$leilao->leilaoEstaFinalizado();
        });
    }

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return $leilao->leilaoEstaFinalizado();
        });
    }

    public function atualiza(Leilao $leilao)
    {
        
    }
}

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao('Fiat 147 0KM',new \DateTimeImmutable('8 days ago'));
        $variant = new Leilao('Variant 1982 0KM',new \DateTimeImmutable('10 days ago'));

        $leiladao = new LeilaoDaomock();
        $leiladao->salva($fiat147);
        $leiladao->salva($variant);

        $encerrador = new Encerrador($leiladao);
        $encerrador->encerra();

        $leiloes = $leiladao->recuperarFinalizados($leiladao);
        self::assertCount(2,$leiloes);
        self::assertEquals('Fiat 147 0KM',$leiloes[0]->recuperarDescricao());
        self::assertEquals('Variant 1982 0KM',$leiloes[1]->recuperarDescricao());
    }
}