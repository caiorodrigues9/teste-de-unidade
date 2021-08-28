<?php

namespace Caio\Leilao\Service;

use Caio\Leilao\Model\Leilao;

class EnviarEmail
{
    public function notificarTerminoLeilao(Leilao $leilao)
    {
        $sucesso = mail(
            'teste@teste.com',
            'Leilão Finalizado',
            'O leilão para '.$leilao->recuperarDescricao(). ' foi finalizado'
        );
        if(!$sucesso){
            throw new \DomainException('Erro ao enviar Email');
        }
    }
}