<?php

namespace View\Cadastro;

use Funcoes\Lib\GlobalHelper;
use App\CADASTRO\DAO\Ministerios;

class Salvar extends GlobalHelper
{
    private Ministerios $ministeriosDAO;
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
        $this->ministeriosDAO = new Ministerios();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'min_nome'  => mb_strtoupper($this->request->post('min_nome')),
            'min_sigla' => mb_strtoupper($this->request->post('min_sigla')),
            'min_ativo' => $this->request->post('min_ativo')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'min_data_inc' => date('Y-m-d H:i:s'),
            'min_usu_inc' => $this->session->get('credentials.default')
        ]);

        $min_id = $this->ministeriosDAO->insert($this->campos);

        if ($min_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $min_id = $this->request->post('min_id', '0');

        if ($min_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->ministeriosDAO->get($min_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'min_data_alt' => date('Y-m-d H:i:s'),
            'min_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->ministeriosDAO->update($min_id, $this->campos);

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
        $this->response->redirect("ministerios.php");
    }
}
