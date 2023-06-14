<?php

namespace View\Cadastro;

use Funcoes\Lib\ViewHelper;
use App\CADASTRO\DAO\Familias;
use App\CADASTRO\DAO\Membros;
use App\CADASTRO\DAO\Pessoas;

class Excluir extends ViewHelper
{
    private Familias $familiasDAO;
    private Membros $membrosDAO;
    private Pessoas $pessoasDAO;
    private array $aFamilia = [];

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
        $this->familiasDAO = new Familias();
        $this->membrosDAO = new Membros();
        $this->pessoasDAO = new Pessoas();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $fam_id = $this->request->get('fam_id', '0');

        if ($fam_id == '0') {
            $this->voltarErro(_('Registro não encontrado'));
        }

        $this->aFamilia = $this->familiasDAO->get($fam_id);

        if (empty($this->aFamilia)) {
            $this->voltarErro(_('Registro não carregado'));
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->familiasDAO->update($this->aFamilia['fam_id'], [
            'fam_data_exc' => date('Y-m-d H:i:s'),
            'fam_usu_exc' => $this->session->get('credentials.default')
        ]);

        if ($excluido) {
            $this->excluirMembros($this->aFamilia['fam_id']);
            $this->excluirPessoas($this->aFamilia['fam_id']);

            $this->session->flash('success', _('Cadastro excluído'));
        } else {
            $this->voltarErro(_('Cadastro não foi excluído'));
        }
    }

    private function excluirMembros(int $fam_id)
    {
        $where = array('');
        $where[0] = ' AND mem_familia_id = ?';
        $where[1][] = $fam_id;
        $membros = $this->membrosDAO->getArray($where);

        if ($membros) {
            foreach ($membros as $membro) {
                $this->membrosDAO->update($membro['mem_id'], [
                    'mem_data_exc' => date('Y-m-d H:i:s'),
                    'mem_usu_exc' => $this->session->get('credentials.default')
                ]);
            }
        }
    }

    private function excluirPessoas(int $fam_id)
    {
        $where = array('');
        $where[0] = ' AND pes_familia_id = ?';
        $where[1][] = $fam_id;
        $pessoas = $this->pessoasDAO->getArray($where);

        if ($pessoas) {
            foreach ($pessoas as $pessoa) {
                $this->pessoasDAO->update($pessoa['pes_id'], [
                    'pes_data_exc' => date('Y-m-d H:i:s'),
                    'pes_usu_exc' => $this->session->get('credentials.default')
                ]);
            }
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("familias.php");
    }
}
