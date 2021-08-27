<?php

namespace Caio\Leilao\Tests\Service;

use Caio\Leilao\Model\Lance;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Model\Usuario;
use Caio\Leilao\Service\Avaliador;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;

    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     */
    public function testAvaliadorDeveEncontrarMaiorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        //Assert - Then

        self::assertEquals(2500,$maiorValor);
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     */
    public function testAvaliadorDeveEncontrarMenorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        //Assert - Then

        self::assertEquals(1700,$menorValor);
    }

    /**
     * @dataProvider leilaoEmOrdemAleatoria
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     */
    public function testAvaliadorDeveBuscar3MaioresLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiores = $this->leiloeiro->getMaioresLances();
        self::assertCount(3,$maiores);
        self::assertEquals(2500,$maiores[0]->getValor());
        self::assertEquals(2000,$maiores[1]->getValor());
        self::assertEquals(1700,$maiores[2]->getValor());


    }

    public function testLeilaoSemLanceNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar leilão sem lance');

        $leilao = new Leilao('Fiat 147 0KM');
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');
        $leilao = new Leilao('Fiat 147 0KM');

        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana,2000));
        $leilao->finaliza();
        $this->leiloeiro->avalia($leilao);
    }


    /**************    DADOS  ***********************/
    public function leilaoEmOrdemCrescente() : array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana,1700));
        $leilao->recebeLance(new Lance($joao,2000));
        $leilao->recebeLance(new Lance($maria,2500));

        return [
            'ordem-crescente'=>[$leilao]
        ];
    }

    public function leilaoEmOrdemDecrescente() : array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria,2500));
        $leilao->recebeLance(new Lance($joao,2000));
        $leilao->recebeLance(new Lance($ana,1700));

        return [
            'ordem-decrescente'=>[$leilao]
        ];
    }

    public function leilaoEmOrdemAleatoria() : array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana,1700));
        $leilao->recebeLance(new Lance($joao,2000));
        $leilao->recebeLance(new Lance($maria,2500));

        return [
            'ordem-aleatoria'=>[$leilao]
        ];
    }

}