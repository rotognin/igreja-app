<?php

namespace View\Patrimonio;

use Funcoes\Lib\GlobalHelper;
use App\PATRIMONIO\DAO\CategoriaPatrimonio;

class Salvar extends GlobalHelper
{
    private CategoriaPatrimonio $categoriaDAO;
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
        $this->categoriaDAO = new CategoriaPatrimonio();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'cpa_titulo'  => mb_strtoupper($this->request->post('cpa_titulo')),
            'cpa_descricao' => mb_strtoupper($this->request->post('cpa_descricao')),
            'cpa_ativo' => $this->request->post('cpa_ativo')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $cpa_id = $this->categoriaDAO->insert($this->campos);

        if ($cpa_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $cpa_id = $this->request->post('cpa_id', '0');

        if ($cpa_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->categoriaDAO->get($cpa_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $atualizado = $this->categoriaDAO->update($cpa_id, $this->campos);

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
        $this->response->redirect("categorias.php");
    }
}
