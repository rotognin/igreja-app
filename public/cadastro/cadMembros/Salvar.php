<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Membros;

class Salvar extends ViewHelper
{
    private Membros $membrosDAO;
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
        $this->membrosDAO = new Membros();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'mem_nome'        => mb_strtoupper($this->request->post('mem_nome')),
            'mem_telefone'    => $this->request->post('mem_telefone'),
            'mem_email'       => $this->request->post('mem_email'),
            'mem_endereco'    => mb_strtoupper($this->request->post('mem_endereco')),
            'mem_numero'      => $this->request->post('mem_numero'),
            'mem_bairro'      => mb_strtoupper($this->request->post('mem_bairro')),
            'mem_complemento' => mb_strtoupper($this->request->post('mem_complemento')),
            'mem_cidade'      => mb_strtoupper($this->request->post('mem_cidade')),
            'mem_estado'      => $this->request->post('mem_estado'),
            'mem_cep'         => preg_replace("/[^0-9]/", '', $this->request->post('mem_cep')),
            'mem_familia_id'  => $this->request->post('mem_familia_id')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'mem_data_inc' => date('Y-m-d H:i:s'),
            'mem_usu_inc' => $this->session->get('credentials.default')
        ]);

        $mem_id = $this->membrosDAO->insert($this->campos);

        if ($mem_id) {
            $this->session->flash('success', _('Cadastro efetuado com sucesso'));
        } else {
            $this->voltarErro(_('Cadastro não foi gravado'));
        }
    }

    private function atualizarRegistro()
    {
        $mem_id = $this->request->post('mem_id', '0');

        if ($mem_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $registro = $this->membrosDAO->get($mem_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'mem_data_alt' => date('Y-m-d H:i:s'),
            'mem_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->membrosDAO->update($mem_id, $this->campos);

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
        $this->response->redirect("membros.php");
    }
}
