<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Familias;

class Salvar extends ViewHelper
{
    private Familias $familiasDAO;
    private array $campos = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->preencherCampos();

        ($this->novoCadastro()) ? $this->inserirRegistro() : $this->atualizarRegistro();

        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->familiasDAO = new Familias();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'fam_nome'        => mb_strtoupper($this->request->post('fam_nome')),
            'fam_observacao'  => mb_strtoupper($this->request->post('fam_observacao'))
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'fam_data_inc' => date('Y-m-d H:i:s'),
            'fam_usu_inc' => $this->session->get('credentials.default')
        ]);

        $fam_id = $this->familiasDAO->insert($this->campos);

        if ($fam_id) {
            $this->session->flash('success', _('Cadastro efetuado com sucesso'));
        } else {
            $this->voltarErro(_('Cadastro não foi gravado'));
        }
    }

    private function atualizarRegistro()
    {
        $fam_id = $this->request->post('fam_id', '0');

        if ($fam_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $registro = $this->familiasDAO->get($fam_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'fam_data_alt' => date('Y-m-d H:i:s'),
            'fam_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->familiasDAO->update($fam_id, $this->campos);

        if ($atualizado) {
            $this->session->flash('success', _('Cadastro atualizado com sucesso'));
        } else {
            $this->voltarErro(_('Cadastro não foi atualizado'));
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
        $this->response->redirect("familias.php");
    }
}
