<?php

namespace View\Ministerios\Musica\Escala;

use Funcoes\Lib\GlobalHelper;
use App\MINISTERIOS\MUSICA\DAO\Escalas;
use Funcoes\Helpers\Format;

class Salvar extends GlobalHelper
{
    private Escalas $escalasDAO;
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
        $this->escalasDAO = new Escalas();
    }

    private function preencherCampos()
    {
        $this->campos = [
            'esc_titulo' => $this->request->post('esc_titulo'),
            'esc_data' => Format::sqlDatetime($this->request->post('esc_data'), 'd/m/Y', 'Y-m-d'),
            'esc_hora' => $this->request->post('esc_hora'),
            'esc_situacao' => 'A'
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos['esc_data_hora_gravacao'] = date('Y-m-d H:i:s');
        $this->campos['esc_usuario_gravacao'] = $this->session->get('credentials.default');

        $esc_id = $this->escalasDAO->insert($this->campos);

        if ($esc_id) {
            $this->session->flash('success', 'Cadastro efetuado com sucesso');
        } else {
            $this->voltarErro('Cadastro não foi gravado');
        }
    }

    private function atualizarRegistro()
    {
        $esc_id = $this->request->post('esc_id', '0');

        if ($esc_id == '0') {
            $this->voltarErro('Identificador não informado');
        }

        $registro = $this->escalasDAO->get($esc_id);

        if (empty($registro)) {
            $this->voltarErro('Registro não existente');
        }

        $this->campos['esc_data_hora_alteracao'] = date('Y-m-d H:i:s');
        $this->campos['esc_usuario_alteracao'] = $this->session->get('credentials.default');

        $atualizado = $this->escalasDAO->update($esc_id, $this->campos);

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
        $this->response->redirect("escalas.php");
    }
}
