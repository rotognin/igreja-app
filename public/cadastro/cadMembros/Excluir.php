<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Membros;

class Excluir extends ViewHelper
{
    private Membros $membrosDAO;
    private array $aMembro = [];

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
        $this->membrosDAO = new Membros();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $mem_id = $this->request->get('mem_id', '0');

        if ($mem_id == '0') {
            $this->voltarErro(_('Registro não encontrado'));
        }

        $this->aMembro = $this->membrosDAO->get($mem_id);

        if (empty($this->aMembro)) {
            $this->voltarErro(_('Registro não carregado'));
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->membrosDAO->update($this->aMembro['mem_id'], [
            'mem_data_exc' => date('Y-m-d H:i:s'),
            'mem_usu_exc' => $this->session->get('credentials.default')
        ]);

        if ($excluido) {
            $this->session->flash('success', _('Cadastro excluído'));
        } else {
            $this->voltarErro(_('Cadastro não foi excluído'));
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("membros.php");
    }
}
