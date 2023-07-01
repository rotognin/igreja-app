<?php

namespace View\Movimentacoes;

use Funcoes\Lib\ViewHelper;
use App\MOVIMENTACOES\DAO\Visitas;
use Funcoes\Helpers\Format;

class Alterar extends ViewHelper
{
    private Visitas $visitasDAO;
    private array $campos = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->preencherCampos();
        $this->alterarRegistro();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->visitasDAO = new Visitas();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'vis_observacao' => $this->request->post('vis_observacao')
        ];
    }

    private function alterarRegistro()
    {
        $vis_id = $this->request->post('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $registro = $this->visitasDAO->get($vis_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'vis_status'   => ($this->request->post('acao') == 'cancelar') ? 'Cancelada' : 'Realizada',
            'vis_data_alt' => date('Y-m-d H:i:s'),
            'vis_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->visitasDAO->update($vis_id, $this->campos);

        if ($atualizado) {
            $this->session->flash('success', _('Visita atualizada com sucesso'));
        } else {
            $this->voltarErro(_('Visita não foi atualizada'));
        }
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->set('previous', $this->request->postArray());
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function saidaPagina()
    {
        $this->response->redirect("visitas.php");
    }
}
