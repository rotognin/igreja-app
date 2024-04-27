<?php

namespace View\Cadastro;

use Funcoes\Lib\GlobalHelper;
use App\CADASTRO\DAO\Ministerios;

class Excluir extends GlobalHelper
{
    private Ministerios $ministeriosDAO;
    private array $aMinisterios = [];

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
        $this->ministeriosDAO = new Ministerios();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $min_id = $this->request->get('min_id', '0');

        if ($min_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aMinisterios = $this->ministeriosDAO->get($min_id);

        if (empty($this->aMinisterios)) {
            $this->voltarErro('Registro não carregado');
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->ministeriosDAO->update($this->aMinisterios['min_id'], [
            'min_data_exc' => date('Y-m-d H:i:s'),
            'min_usu_exc' => $this->session->get('credentials.default')
        ]);

        if ($excluido) {
            $this->session->flash('success', 'Cadastro excluído');
        } else {
            $this->voltarErro('Cadastro não foi excluído');
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("ministerios.php");
    }
}
