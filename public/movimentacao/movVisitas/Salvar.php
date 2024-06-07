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
    private array $aVisita = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();

        if (!$this->informacoesCorretas()) {
            $this->voltarErro('Faltam preencher todas as informações');
        }

        if (!$this->novoCadastro()) {
            $this->carregarVisita();
        }
        $this->preencherCampos();

        ($this->novoCadastro()) ? $this->inserirRegistro() : $this->atualizarRegistro();

        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->visitasDAO = new Visitas();
    }

    private function carregarVisita()
    {
        $vis_id = $this->request->post('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro(_('Identificador não informado'));
        }

        $this->aVisita = $this->visitasDAO->get($vis_id);

        if (empty($this->aVisita)) {
            $this->voltarErro(_('Registro não existente'));
        }
    }

    private function informacoesCorretas(): bool
    {
        // Checar se vieram todas as informações necessárias
        if (empty($this->request->post('vis_pessoas'))) {
            return false;
        }

        return true;
    }

    private function preencherCampos()
    {
        $this->campos = [
            'vis_titulo'     => $this->request->post('vis_titulo'),
            'vis_descricao'  => $this->request->post('vis_descricao'),
            'vis_quem'       => $this->request->post('vis_quem'),
            'vis_local'       => $this->request->post('vis_local'),
            'vis_observacao' => $this->request->post('vis_observacao'),
            'vis_familia_id' => $this->request->post('vis_familia_id')
        ];

        if ($this->request->post('vis_data', '') != '') {
            $this->campos['vis_data'] = Format::sqlDatetime($this->request->post('vis_data'), 'd/m/Y', 'Y-m-d');
        }

        if ($this->request->post('vis_hora', '') != '') {
            $this->campos['vis_hora'] = $this->request->post('vis_hora');
        }
    }

    private function novoCadastro()
    {
        return $this->request->post('novo') == 'S';
    }

    private function inserirRegistro()
    {
        $this->campos = array_merge($this->campos, [
            'vis_situacao' => ($this->request->post('vis_data', '') == '') ? 'P' : 'A',
            'vis_data_inc' => date('Y-m-d H:i:s'),
            'vis_usu_inc'  => $this->session->get('credentials.default')
        ]);

        $vis_id = $this->visitasDAO->insert($this->campos);

        if ($vis_id) {
            // Gravar os integrantes da visita (trait)
            $arrayPessoas = ($this->request->post('vis_pessoas', '') != '') ? $this->request->post('vis_pessoas') : [];

            $this->adicionarVisitantes($vis_id, $arrayPessoas);

            $this->session->flash('success', _('Visita gravada com sucesso'));
        } else {
            $this->voltarErro(_('Visita não pôde ser criada'));
        }
    }

    private function atualizarRegistro()
    {
        // Se a visita estiver como "Prospecção" mas for informada uma data, altetar para "Agendada"
        if ($this->aVisita['vis_situacao'] == 'P') {
            if ($this->request->post('vis_data', '') != '') {
                $this->campos = array_merge($this->campos, [
                    'vis_situacao' => 'A'
                ]);
            }
        }

        $this->campos = array_merge($this->campos, [
            'vis_data_alt' => date('Y-m-d H:i:s'),
            'vis_usu_alt' => $this->session->get('credentials.default')
        ]);

        $atualizado = $this->visitasDAO->update($this->aVisita['vis_id'], $this->campos);

        if ($atualizado) {
            // Regravar as pessoas e membros da visita
            $this->excluirVisitantes($this->aVisita['vis_id']);

            $arrayPessoas = ($this->request->post('vis_pessoas', '') != '') ? $this->request->post('vis_pessoas') : [];

            $this->adicionarVisitantes($this->aVisita['vis_id'], $arrayPessoas);

            $this->session->flash('success', 'Visita atualizada com sucesso');
        } else {
            $this->voltarErro('Visita não foi atualizada');
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
