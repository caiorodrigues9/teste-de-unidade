<?php

namespace Caio\Leilao\Service;

use Caio\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    public function __construct(
        private LeilaoDao $dao,
        private EnviarEmail $enviarEmail
    )
    {

    }

    public function encerra()
    {
        $leiloes = $this->dao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            try{
                if ($leilao->temMaisDeUmaSemana()) {
                    $leilao->finaliza();
                    $this->dao->atualiza($leilao);
                    $this->enviarEmail->notificarTerminoLeilao($leilao);
                }
            }catch (\DomainException $e) {
                error_log($e->getMessage());
            }
        }
    }
}
