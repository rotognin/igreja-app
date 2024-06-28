<?php

namespace View\Ministerios\Musica\Categoria;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\Categorias;

class Salvar extends GlobalHelper
{
    private Categorias $categoriasDAO;
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
        $this->categoriasDAO = new Categorias();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'cam_descricao' => mb_strtoupper($this->request->post('cam_descricao'))
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $cam_id = $this->categoriasDAO->insert($this->campos);

        if ($cam_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $cam_id = $this->request->post('cam_id', '0');

        if ($cam_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->categoriasDAO->get($cam_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $atualizado = $this->categoriasDAO->update($cam_id, $this->campos);

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
        $this->response->redirect("categorias.php");
    }
}
