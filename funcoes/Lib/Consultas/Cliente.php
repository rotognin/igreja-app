<?php

namespace Funcoes\Lib\Consultas;

class Cliente
{
    private string $url = '/testes/dados.php';

    public function getUrl()
    {
        return $this->url;
    }
}
