<?php

namespace View\Movimentacoes;

use Funcoes\Lib\GlobalHelper;
use App\MOVIMENTACOES\DAO\Visitas;
use Funcoes\Lib\Traits\TraitVisitaIntegrantes;
use Funcoes\Helpers\Format;

class Salvar extends GlobalHelper
{
    use TraitVisitaIntegrantes;

    private Visitas $visitasDAO;
    private array $campos = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();

        if (!$this->informacoesCorretas()) {
            $this->voltarErro(_('Faltam preencher todas as informações'));
        }

        $this->preencherCampos();

        ($this->novoCadastro()) ? $this->inserirRegistro() : $this->atualizarRegistro();

        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->visitasDAO = new Visitas();
    }

    private function informacoesCorretas(): bool
    {
        // Checar se vieram todas as informações necessárias
        if (empty($this->request->post('vis_membros')) && empty($this->request->post('vis_pessoas'))) {
            return false;
        }

        return true;
    }

    private function preencherCampos()
    {
        $this->campos = [
            'vis_data'       => Format::sqlDatetime($this->request->post('vis_data'), 'd/m/Y', 'Y-m-d'),
            'vis_hora'       => $this->request->post('vis_hora'),
            'vis_titulo'     => mb_strtoupper($this->request->post('vis_titulo')),
            'vis_descricao'  => mb_strtoupper($this->request->post('vis_descricao')),
            'vis_familia_id' => $this->request->post('vis_familia_id')
        ];
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'vis_status'   => 'A Realizar',
            'vis_data_inc' => date('Y-m-d H:i:s'),
            'vis_usu_inc'  => $this->session->get('credentials.default')
        ]);

        $vis_id = $this->visitasDAO->insert($this->campos);

        if ($vis_id) {
            // Gravar os integrantes da visita (trait)
            $arrayMembros = ($this->request->post('vis_membros', '') != '') ? $this->request->post('vis_membros') : [];
            $arrayPessoas = ($this->request->post('vis_pessoas', '') != '') ? $this->request->post('vis_pessoas') : [];

            $this->adicionarVisitantes($vis_id, 'Membro', $arrayMembros);
            $this->adicionarVisitantes($vis_id, 'Pessoa', $arrayPessoas);

            $this->session->flash('success', _('Visita gravada com sucesso'));
        } else {
            $this->voltarErro(_('Visita não pôde ser criada'));
        }
    }

    private function atualizarRegistro()
    {
        $vis_id = $this->request->post('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $registro = $this->visitasDAO->get($vis_id);

        if (empty($registro)) {
            $this->voltarErro(_('Registro não existente'));
        }

        $this->campos = array_merge($this->campos, [
            'vis_data_alt' => date('Y-m-d H:i:s'),
            'vis_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->visitasDAO->update($vis_id, $this->campos);

        if ($atualizado) {
            // Regravar as pessoas e membros da visita
            $this->excluirVisitantes($vis_id);

            $arrayMembros = ($this->request->post('vis_membros', '') != '') ? $this->request->post('vis_membros') : [];
            $arrayPessoas = ($this->request->post('vis_pessoas', '') != '') ? $this->request->post('vis_pessoas') : [];

            $this->adicionarVisitantes($vis_id, 'Membro', $arrayMembros);
            $this->adicionarVisitantes($vis_id, 'Pessoa', $arrayPessoas);

            $this->session->flash('success', _('Visita atualizada com sucesso'));
        } else {
            $this->voltarErro(_('Visita não foi atualizada'));
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
        $this->response->redirect("visitas.php");
    }
}
