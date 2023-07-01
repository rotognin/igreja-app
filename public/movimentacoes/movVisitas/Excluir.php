<?php

namespace View\Movimentacoes;

use Funcoes\Lib\ViewHelper;
use App\MOVIMENTACOES\DAO\Visitas;

class Excluir extends ViewHelper
{
    private Visitas $visitasDAO;
    private array $aVisita;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->carregarRegistro();
        $this->excluirRegistro();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->visitasDAO = new Visitas();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $vis_id = $this->request->get('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro(_('Registro não encontrado'));
        }

        $this->aVisita = $this->visitasDAO->get($vis_id);

        if (empty($this->aVisita)) {
            $this->voltarErro(_('Registro não carregado'));
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->visitasDAO->update($this->aVisita['vis_id'], [
            'vis_status' => 'Excluída',
            'vis_data_exc' => date('Y-m-d H:i:s'),
            'vis_usu_exc' => $this->session->get('credentials.default')
        ]);

        if ($excluido) {
            $this->session->flash('success', _('Visita excluída'));
        } else {
            $this->voltarErro(_('Cadastro não foi excluído'));
        }
    }

    private function saidaPagina()
    {
        $this->response->redirect("visitas.php");
    }
}
