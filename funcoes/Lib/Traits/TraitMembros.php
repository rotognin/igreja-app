<?php

namespace Funcoes\Lib\Traits;

use App\CADASTRO\DAO\Membros;

trait TraitMembros
{
    private Membros $membrosDAO;

    private function getDAOMembros()
    {
        $this->membrosDAO = new Membros();
    }

    public function buscarMembros()
    {
        $this->getDAOMembros();

        $array = $this->membrosDAO->getArray();
        $membros = array();

        if ($array) {
            foreach ($array as $membro) {
                $membros[$membro['mem_id']] = $membro['mem_nome'];
            }
        }

        return array_merge(['0' => 'Todas'] + $membros);
    }
}
