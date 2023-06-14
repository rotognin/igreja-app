<?php

namespace Funcoes\Interfaces;

interface Notificavel
{
    public function notificar(string $tipo, string $icone, string $url, string $mensagem);
    public function destinosNotificacao(): array;
    public function getTipoNotificavel(): string;
    public function getID(): string;
}
