<?php

namespace View\Ministerios\Musica\Categoria;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\Categorias;

class Excluir extends GlobalHelper
{
    private Categorias $categoriasDAO;
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
        $this->categoriasDAO = new Categorias();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $cam_id = $this->request->get('cam_id', '0');

        if ($cam_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aCategoria = $this->categoriasDAO->get($cam_id);

        if (empty($this->aCategoria)) {
            $this->voltarErro('Registro não carregado');
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->categoriasDAO->delete($this->aCategoria['cam_id']);

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
