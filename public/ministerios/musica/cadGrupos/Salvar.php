<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\Grupos;

class Salvar extends GlobalHelper
{
    private Grupos $gruposDAO;
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
        $this->gruposDAO = new Grupos();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'gru_nome' => $this->request->post('gru_nome'),
            'gru_sigla' => $this->request->post('gru_sigla', ''),
            'gru_observacoes' => $this->request->post('gru_observacoes', ''),
            'gru_situacao' => $this->request->post('gru_situacao')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $gru_id = $this->gruposDAO->insert($this->campos);

        if ($gru_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $gru_id = $this->request->post('gru_id', '0');

        if ($gru_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->gruposDAO->get($gru_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $atualizado = $this->gruposDAO->update($gru_id, $this->campos);

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
        $this->response->redirect("grupos.php");
    }
}
