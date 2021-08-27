<?php

namespace Caio\Leilao\Model;

use mysql_xdevapi\Exception;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;

    private bool $finalizado;

    private \DateTimeInterface $dataInicio;

    private $id;

    public function __construct(string $descricao, \DateTimeImmutable $dataInicio = null, int $id = null)
    {
        $this->descricao = $descricao;
        $this->finalizado = false;
        $this->lances = [];
        $this->dataInicio = $dataInicio ?? new \DateTimeImmutable();
        $this->id = $id;
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new \DomainException('Usuário não pode propor dois lances consecutivos');
        }

        $totalLancesUsuario = $this->qtdLancesUsuario($lance->getUsuario());

        if ($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuário não pode efetuar mais de 5 lances no mesmo item');
        }

        $this->lances[] = $lance;
    }

    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)];
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    /**
     * @param Usuario $usuario
     * @return int
     */
    private function qtdLancesUsuario(Usuario $usuario): int
    {
        return array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() === $usuario) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function leilaoEstaFinalizado():bool
    {
        return $this->finalizado;
    }

    public function recuperarDescricao(): string
    {
        return $this->descricao;
    }

    public function recuperarDataInicio(): \DateTimeInterface
    {
        return $this->dataInicio;
    }

    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTime();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    public function recuperarId(): int
    {
        return $this->id;
    }
}
