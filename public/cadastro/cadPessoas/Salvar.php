<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Pessoas;

class Salvar extends ViewHelper
{
    private Pessoas $pessoasDAO;
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
        $this->pessoasDAO = new Pessoas();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'pes_nome'        => mb_strtoupper($this->request->post('pes_nome')),
            'pes_telefone'    => $this->request->post('pes_telefone'),
            'pes_email'       => $this->request->post('pes_email'),
            'pes_endereco'    => mb_strtoupper($this->request->post('pes_endereco')),
            'pes_numero'      => $this->request->post('pes_numero'),
            'pes_bairro'      => mb_strtoupper($this->request->post('pes_bairro')),
            'pes_complemento' => mb_strtoupper($this->request->post('pes_complemento')),
            'pes_cidade'      => mb_strtoupper($this->request->post('pes_cidade')),
            'pes_estado'      => $this->request->post('pes_estado'),
            'pes_cep'         => preg_replace("/[^0-9]/", '', $this->request->post('pes_cep')),
            'pes_familia_id'  => $this->request->post('pes_familia_id')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'pes_data_inc' => date('Y-m-d H:i:s'),
            'pes_usu_inc' => $this->session->get('credentials.default')
        ]);

        $pes_id = $this->pessoasDAO->insert($this->campos);

        if ($pes_id) {
            $this->session->flash('success', _('Cadastro efetuado com sucesso'));
        } else {
            $this->voltarErro(_('Cadastro não foi gravado'));
        }
    }

    private function atualizarRegistro()
    {
        $pes_id = $this->request->post('pes_id', '0');

        if ($pes_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $registro = $this->pessoasDAO->get($pes_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'pes_data_alt' => date('Y-m-d H:i:s'),
            'pes_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->pessoasDAO->update($pes_id, $this->campos);

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
        $this->response->redirect("pessoas.php");
    }
}
