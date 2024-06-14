<?php

namespace View\Patrimonio;

use Funcoes\Lib\GlobalHelper;
use Funcoes\Helpers\Format;
use Funcoes\Helpers\Values;
use App\PATRIMONIO\DAO\Patrimonio;

class Salvar extends GlobalHelper
{
    private Patrimonio $patrimonioDAO;
    private array $campos = [];
    private string $usuario;

    public function __construct()
    {
        parent::__construct();
        $this->usuario = $this->session->get('credentials.default');
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
        $this->patrimonioDAO = new Patrimonio();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'pat_descricao' => mb_strtoupper($this->request->post('pat_descricao')),
            'pat_categoria_id' => $this->request->post('pat_categoria_id'),
            'pat_marca' => mb_strtoupper($this->request->post('pat_marca')),
            'pat_especificacao' => mb_strtoupper($this->request->post('pat_especificacao')),
            'pat_tipo_entrada' => $this->request->post('pat_tipo_entrada'),
            'pat_nf_serie' => mb_strtoupper($this->request->post('pat_nf_serie')),
            'pat_nf_numero' => $this->request->post('pat_nf_numero'),
            'pat_nf_chave' => $this->request->post('pat_nf_chave'),
            'pat_usu_entrada' => Values::seEntao($this->request->post('pat_usu_entrada'), '0', ''),
            'pat_observacoes' => mb_strtoupper($this->request->post('pat_observacoes')),
            'pat_conservacao' => $this->request->post('pat_conservacao'),
            'pat_usu_responsavel' => Values::seEntao($this->request->post('pat_usu_responsavel'), '0', ''),
            'pat_ativo' => 'S',
            'pat_data_hora_cadastro' => date('Y-m-d H:i:s'),
            'pat_usu_cadastro' => $this->usuario
        ];

        // Campos de Data
        if ($this->request->post('pat_nf_data') != '') {
            $this->campos['pat_nf_data'] = Format::sqlDatetime($this->request->post('pat_nf_data'), 'd/m/Y', 'Y-m-d');
        }

        if ($this->request->post('pat_data_entrada') != '') {
            $this->campos['pat_data_entrada'] = Format::sqlDatetime($this->request->post('pat_data_entrada'),  'd/m/Y', 'Y-m-d');
        }

        // Campos de Valor
        if ($this->request->post('pat_nf_valor') != '') {
            $this->campos['pat_data_valor'] = Format::ajustarValor($this->request->post('pat_nf_valor'));
        }

        if ($this->request->post('pat_valor_estimado') != '') {
            $this->campos['pat_valor_estimado'] = Format::ajustarValor($this->request->post('pat_valor_estimado'));
        }

        // Campos Gerais
        if ($this->request->post('pat_quantidade') != '') {
            $this->campos['pat_quantidade'] = str_replace(',', '.', $this->request->post('pat_quantidade'));
        }
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $pat_id = $this->patrimonioDAO->insert($this->campos);

        if ($pat_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $pat_id = $this->request->post('pat_id', '0');

        if ($pat_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->patrimonioDAO->get($pat_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $atualizado = $this->patrimonioDAO->update($pat_id, $this->campos);

        if ($atualizado) {
            $this->session->flash('success', 'Cadastro atualizado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi atualizado');
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
        $this->response->redirect("patrimonios.php");
    }
}
