<?php

namespace View\Movimentacoes;

use Funcoes\Lib\GlobalHelper;
use App\MOVIMENTACOES\DAO\Visitas;

class Alterar extends GlobalHelper
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
            'vis_relatorio' => $this->request->post('vis_relatorio')
        ];
    }

    private function alterarRegistro()
    {
        $vis_id = $this->request->post('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->visitasDAO->get($vis_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $this->campos = array_merge($this->campos, [
            'vis_situacao'   => ($this->request->post('acao') == 'cancelar') ? 'C' : 'R',
            'vis_data_alt' => date('Y-m-d H:i:s'),
            'vis_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->visitasDAO->update($vis_id, $this->campos);

        if ($atualizado) {
            $this->session->flash('success', 'Visita atualizada com sucesso');
        } else {
            $this->voltarErro('Visita não foi atualizada');
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
