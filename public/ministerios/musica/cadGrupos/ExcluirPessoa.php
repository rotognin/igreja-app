<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\GrupoPessoas;

class ExcluirPessoa extends GlobalHelper
{
    private GrupoPessoas $grupoPessoasDAO;
    private int $grp_id;
    private int $gru_id;
    private array $aGrupo = [];

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
        $this->grupoPessoasDAO = new GrupoPessoas();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $this->grp_id = $this->request->get('grp_id', '0');

        if ($this->grp_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aGrupo = $this->grupoPessoasDAO->get($this->grp_id);

        if (empty($this->aGrupo)) {
            $this->voltarErro('Registro não carregado');
        }

        $this->gru_id = $this->aGrupo['grp_grupo_id'];
    }

    private function excluirRegistro()
    {
        $excluido = $this->grupoPessoasDAO->delete($this->grp_id);

        if ($excluido) {
            $this->session->flash('success', 'Cadastro excluído');
        } else {
            $this->voltarErro('Cadastro não foi excluído');
        }
    }

    public function saidaPagina()
    {
        $this->response->redirect("grupos.php?posicao=composicao&gru_id={$this->gru_id}");
    }
}
