<?php

namespace Caio\Leilao\Service;

use Caio\Leilao\Model\Lance;
use Caio\Leilao\Model\Leilao;

class Avaliador
{
    private float $maiorValor = -INF;
    private float $menorValor = INF;
    private array $maioresLances;

    public function avalia(Leilao $leilao): void
    {
        foreach ($leilao->getLances() as $lance) {

            if($lance->getValor() > $this->maiorValor){
                $this->maiorValor = $lance->getValor();
            }

            if ($lance->getValor()<$this->menorValor) {
                $this->menorValor = $lance->getValor();
            }
        }

        $lances = $leilao->getLances();
        uasort($lances,function (Lance $lance1, Lance $lance2) {
            return $lance2->getValor() - $lance1->getValor();
        });
        $this->maioresLances = array_slice($lances,0,3);
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }
    public function getMenorValor(): float
    {
        return $this->menorValor;
    }


    public function getMaioresLances(): array
    {
        return $this->maioresLances;
    }
}