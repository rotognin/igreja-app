<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Pessoas;

class Excluir extends ViewHelper
{
    private Pessoas $pessoasDAO;
    private array $aPessoa = [];

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
        $this->pessoasDAO = new Pessoas();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $pes_id = $this->request->get('pes_id', '0');

        if ($pes_id == '0') {
            $this->voltarErro(_('Registro não encontrado'));
        }

        $this->aPessoa = $this->pessoasDAO->get($pes_id);

        if (empty($this->aPessoa)) {
            $this->voltarErro(_('Registro não carregado'));
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->pessoasDAO->update($this->aPessoa['pes_id'], [
            'pes_data_exc' => date('Y-m-d H:i:s'),
            'pes_usu_exc' => $this->session->get('credentials.default')
        ]);

        if ($excluido) {
            $this->session->flash('success', _('Cadastro excluído'));
        } else {
            $this->voltarErro(_('Cadastro não foi excluído'));
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("pessoas.php");
    }
}
