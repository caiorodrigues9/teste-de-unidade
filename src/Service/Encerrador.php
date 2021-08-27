<?php

namespace Caio\Leilao\Service;

use Caio\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    public function __construct(private LeilaoDao $dao)
    {

    }

    public function encerra()
    {
        $leiloes = $this->dao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                $leilao->finaliza();
                $this->dao->atualiza($leilao);
            }
        }
    }
}
