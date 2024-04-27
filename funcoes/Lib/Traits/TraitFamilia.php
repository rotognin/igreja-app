<?php

namespace Funcoes\Lib\Traits;

use App\CADASTRO\DAO\Familias;

trait TraitFamilia
{
    private Familias $familiasDAO;

    private function getDAOFamilias()
    {
        $this->familiasDAO = new Familias();
    }

    public function buscarFamilias(string $placeholder = 'Todas')
    {
        $this->getDAOFamilias();

        $array = $this->familiasDAO->getArray();
        $familias = array();

        if ($array) {
            foreach ($array as $familia) {
                $familias[$familia['fam_id']] = $familia['fam_nome'];
            }
        }

        return array_merge(['0' => $placeholder] + $familias);
    }
}
