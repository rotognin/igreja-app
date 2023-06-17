<?php

namespace Funcoes\Lib\Traits;

use App\CADASTRO\DAO\Pessoas;

trait TraitPessoas
{
    private Pessoas $pessoasDAO;

    private function getDAOPessoas()
    {
        $this->pessoasDAO = new Pessoas();
    }

    public function buscarPessoas()
    {
        $this->getDAOPessoas();

        $array = $this->pessoasDAO->getArray();
        $pessoas = array();

        if ($array) {
            foreach ($array as $pessoa) {
                $pessoas[$pessoa['pes_id']] = $pessoa['pes_nome'];
            }
        }

        return array_merge(['0' => 'Todas'] + $pessoas);
    }
}
