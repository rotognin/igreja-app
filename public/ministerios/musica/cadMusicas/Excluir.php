<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\MusicaAnexos;

class Excluir extends GlobalHelper
{
    private MusicaAnexos $musicaAnexosDAO;
    private array $aAnexo = [];
    private int $mus_id;

    public function __construct()
    {
        parent::__construct();
        $this->mus_id = $this->request->get('mus_id');
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->carregarRegistro();
        $this->excluirRegistro();
        $this->excluirArquivo();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->musicaAnexosDAO = new MusicaAnexos();
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $mua_id = $this->request->get('mua_id', '0');

        if ($mua_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aAnexo = $this->musicaAnexosDAO->get($mua_id);

        if (empty($this->aAnexo)) {
            $this->voltarErro('Registro não carregado');
        }
    }

    private function excluirRegistro()
    {
        $excluido = $this->musicaAnexosDAO->delete($this->aAnexo['mua_id']);

        if ($excluido) {
            $this->session->flash('success', 'Cadastro excluído');
        } else {
            $this->voltarErro('Cadastro não foi excluído');
        }
    }

    private function excluirArquivo()
    {
        //
    }

    public function saidaPagina()
    {
        $this->response->redirect("musicas.php?posicao=anexos&mus_id={$this->mus_id}");
    }
}
