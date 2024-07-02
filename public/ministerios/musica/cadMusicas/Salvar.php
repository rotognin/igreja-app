<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\Musicas;

class Salvar extends GlobalHelper
{
    private Musicas $musicasDAO;
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
        $this->musicasDAO = new Musicas();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'mus_nome' => $this->request->post('mus_nome'),
            'mus_artista' => $this->request->post('mus_artista', ''),
            'mus_link' => $this->request->post('mus_link', ''),
            'mus_situacao' => $this->request->post('mus_situacao'),
            'mus_categoria_id' => $this->request->post('mus_categoria_id')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $mus_id = $this->musicasDAO->insert($this->campos);

        if ($mus_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $mus_id = $this->request->post('mus_id', '0');

        if ($mus_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->musicasDAO->get($mus_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $atualizado = $this->musicasDAO->update($mus_id, $this->campos);

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
        $this->response->redirect("musicas.php");
    }
}
