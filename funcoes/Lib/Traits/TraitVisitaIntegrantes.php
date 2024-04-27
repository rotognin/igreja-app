<?php

namespace Funcoes\Lib\Traits;

use App\MOVIMENTACOES\DAO\VisitaIntegrantes;

trait TraitVisitaIntegrantes
{
    private VisitaIntegrantes $visitaIntegrantesDAO;

    private function getDAOVisitaIntegrantes()
    {
        $this->visitaIntegrantesDAO = new VisitaIntegrantes();
    }

    public function excluirVisitantes(int $vis_id)
    {
        if (!$vis_id) {
            return false;
        }

        $this->getDAOVisitaIntegrantes();
        $this->visitaIntegrantesDAO->delete($vis_id);
    }

    public function adicionarVisitantes(int $vis_id, string $tipo, array $visitantes)
    {
        if (!$vis_id) {
            return false;
        }

        if (empty($visitantes)) {
            return false;
        }

        if (!in_array($tipo, ['Membro', 'Pessoa'])) {
            return false;
        }

        $campoTipo = ($tipo == 'Membro') ? 'vin_membro_id' : 'vin_pessoa_id';

        $this->getDAOVisitaIntegrantes();

        foreach ($visitantes as $visitante) {
            $this->visitaIntegrantesDAO->insert([
                'vin_visita_id' => $vis_id,
                $campoTipo => $visitante,
                'vin_tipo' => $tipo
            ]);
        }
    }

    public function obterVisitantes(int $vis_id): array
    {
        $this->getDAOVisitaIntegrantes();

        $where = array('');
        $where[0] = ' AND vin_visita_id = ?';
        $where[1][] = $vis_id;

        return $this->visitaIntegrantesDAO->getArray($where);
    }
}
