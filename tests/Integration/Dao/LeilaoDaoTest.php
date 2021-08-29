<?php

namespace Caio\Leilao\Tests\Integration\Dao;

use Caio\Leilao\Dao\Leilao as LeilaoDao;
use Caio\Leilao\Infra\ConnectionCreator;
use Caio\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes(
                                        id INTEGER primary key,
                                        descricao TEXT,
                                        finalizado BOOL,
                                        dataInicio TEXT
                                    );'
        );
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }
    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1,$leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class,$leiloes);
        self::assertSame('Variant 0KM',$leiloes[0]->recuperarDescricao());
        self::assertFalse($leiloes[0]->leilaoEstaFinalizado());
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloes = $leilaoDao->recuperarFinalizados();

        self::assertCount(1,$leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class,$leiloes);
        self::assertSame('Fiat 147 0KM',$leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->leilaoEstaFinalizado());

    }

    public function testAoAtualizarleilaoStatusDeveSerAlterado()
    {
        $leilao = new Leilao('Brasília Amarela');
        $leilaoDao = new LeilaoDao(self::$pdo);
        $leilao = $leilaoDao->salva($leilao);

        $leiloes = $leilaoDao->recuperarNaoFinalizados();
        self::assertCount(1,$leiloes);
        self::assertSame('Brasília Amarela',$leiloes[0]->recuperarDescricao());
        self::assertFalse($leiloes[0]->leilaoEstaFinalizado());

        $leilao->finaliza();
        $leilaoDao->atualiza($leilao);

        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(1,$leiloes);
        self::assertSame('Brasília Amarela',$leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->leilaoEstaFinalizado());
    }
    
    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    public function leiloes()
    {
        $finalizado = new Leilao('Fiat 147 0KM',new \DateTimeImmutable('8 days ago'));
        $finalizado->finaliza();
        $naoFinalizado = new Leilao('Variant 0KM',new \DateTimeImmutable());
        return [
            'leiloes'=> [
                [$naoFinalizado,$finalizado]
            ]
        ];
    }
}