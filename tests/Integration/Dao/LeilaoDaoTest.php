<?php

namespace Caio\Leilao\Tests\Integration\Dao;

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

    public function testInsercaoEBuscaDevemFuncionar()
    {
        $leilao = new Leilao('Variant 0KM',new \DateTimeImmutable('8 days ago'));
        $leilaoDao = new \Caio\Leilao\Dao\Leilao(self::$pdo);
        $leilaoDao->salva($leilao);
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1,$leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class,$leiloes);
        self::assertSame('Variant 0KM',$leiloes[0]->recuperarDescricao());

    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}