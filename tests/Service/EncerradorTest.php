<?php

namespace Service;

use Caio\Leilao\Dao\Leilao as LeilaoDao;
use Caio\Leilao\Model\Leilao;
use Caio\Leilao\Service\Encerrador;
use Caio\Leilao\Service\EnviarEmail;
use PHPUnit\Framework\TestCase;


class EncerradorTest extends TestCase
{
    private Encerrador $encerrador;
    private EnviarEmail $enviarEmail;
    private Leilao $fiat147;
    private Leilao $variant;

    protected function setUp(): void
    {
        $this->fiat147 = new Leilao('Fiat 147 0KM',new \DateTimeImmutable('8 days ago'));
        $this->variant = new Leilao('Variant 1982 0KM',new \DateTimeImmutable('10 days ago'));

        $leilaodao = self::createMock(LeilaoDao::class);
        /*$leilaodao = self::getMockBuilder(LeilaoDao::class)
            ->setConstructorArgs([new \PDO('sqlite::memory:')])
            ->getMock();*/
        $leilaodao->method('recuperarNaoFinalizados')
            ->willReturn(
                [$this->fiat147,$this->variant]
            );

        $leilaodao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$this->fiat147],
                [$this->variant]
            );
        $this->enviarEmail = self::createMock(EnviarEmail::class);
        $this->encerrador = new Encerrador(dao: $leilaodao,enviarEmail: $this->enviarEmail);

    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {

        $this->encerrador->encerra();

        $leiloes =[$this->fiat147,$this->variant];


        self::assertCount(2,$leiloes);
        self::assertEquals('Fiat 147 0KM',$leiloes[0]->recuperarDescricao());
        self::assertEquals('Variant 1982 0KM',$leiloes[1]->recuperarDescricao());
        self::assertTrue($leiloes[0]->leilaoEstaFinalizado());
        self::assertTrue($leiloes[1]->leilaoEstaFinalizado());
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('Erro ao enviar Email');

        $this->enviarEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }
}