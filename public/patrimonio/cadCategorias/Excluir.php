<?php

namespace View\Patrimonio;

use Funcoes\Lib\GlobalHelper;
use App\PATRIMONIO\DAO\CategoriaPatrimonio;

class Excluir extends GlobalHelper
{
    private CategoriaPatrimonio $categoriaDAO;
    private array $aCategoria = [];

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
        $this->categoriaDAO = new CategoriaPatrimonio();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $cpa_id = $this->request->get('cpa_id', '0');

        if ($cpa_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aCategoria = $this->categoriaDAO->get($cpa_id);

        if (empty($this->aCategoria)) {
            $this->voltarErro('Registro não carregado');
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->categoriaDAO->delete($this->aCategoria['cpa_id']);

        if ($excluido) {
            $this->session->flash('success', 'Cadastro excluído');
        } else {
            $this->voltarErro('Cadastro não foi excluído');
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("categorias.php");
    }
}
