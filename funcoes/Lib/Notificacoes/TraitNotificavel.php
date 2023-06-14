<?php

namespace Funcoes\Lib\Notificacoes;

trait TraitNotificavel
{
    public function notificar(string $tipo, string $icone, string $url, string $mensagem)
    {
        $notificacao = new Notificacao($tipo, $icone, $url, $mensagem);
        $notificacao->enviar($this);
    }

    public abstract function destinosNotificacao(): array;

    public abstract function getTipoNotificavel(): string;

    public abstract function getID(): string;
}
