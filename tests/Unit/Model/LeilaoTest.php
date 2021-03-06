<?php

namespace Caio\Leilao\Tests\Unit\Model;

use Caio\Leilao\Model\Lance;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode efetuar mais de 5 lances no mesmo item');
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('joao');
        $ana = new Usuario('ana');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 2500));
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($ana, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($ana, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($ana, 5500));

        $leilao->recebeLance(new Lance($joao, 6000));

    }
    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor dois lances consecutivos');
        $leilao = new Leilao('Fiat 147 0KM');
        $ana = new Usuario('ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));
    }
    /**
     * @dataProvider geraLances
     */
    public function testLeilaoRecebeLance(int $qtdLances,Leilao $leilao,array $valores)
    {
        self::assertCount($qtdLances,$leilao->getLances());

        foreach ($valores as $indice=>$valor){
            self::assertEquals($valor,$leilao->getLances()[$indice]->getValor());
        }
    }

    public function geraLances()
    {
        $joao = new Usuario('joao');
        $maria = new Usuario('maria');

        $leilaoCom2Lances = new Leilao('Fiat 147 0KM');
        $leilaoCom1Lance = new Leilao('Fusca  0KM');

        $leilaoCom1Lance->recebeLance(new Lance($joao,5000));

        $leilaoCom2Lances->recebeLance(new Lance($joao,1000));
        $leilaoCom2Lances->recebeLance(new Lance($maria,2000));

        return [
            'Leilão dois lances'=>[2,$leilaoCom2Lances,[1000,2000]],
            'Leilão um lance'=>[1,$leilaoCom1Lance,[5000]]
        ];
    }
}