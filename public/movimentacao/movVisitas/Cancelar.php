<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form as Formulario;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Lib\GlobalHelper;
use App\MOVIMENTACOES\DAO\Visitas;

class Cancelar extends GlobalHelper
{
    private Visitas $visitasDAO;
    private array $aVisita;
    private string $cabecalho;
    private Formulario $form;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->iniciarDAO();
        $this->carregarRegistro();
        $this->montarCabecalho();
        $this->inicioForm();
        $this->montarCampos();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->visitasDAO = new Visitas();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cancelar Visita</h1>',
            L::linkbutton('Voltar', 'visitas.php', 'Voltar', 'fas fa-angle-left')
        );
    }

    private function inicioForm()
    {
        $this->form = new Formulario();
        $this->form->setTitle("{$this->aVisita['vis_id']} - {$this->aVisita['vis_titulo']}");
        $this->form->setForm('id="form-visitas" action="?posicao=alterar" method="post"');
    }

    private function voltarErro(string $mensagem)
    {
        $this->session->flash('error', $mensagem);
        $this->response->back();
    }

    private function carregarRegistro()
    {
        $vis_id = $this->request->get('vis_id', '0');

        if ($vis_id == '0') {
            $this->voltarErro('Registro não encontrado');
        }

        $this->aVisita = $this->visitasDAO->get($vis_id);

        if (empty($this->aVisita)) {
            $this->voltarErro('Registro não carregado');
        }
    }

    private function montarCampos()
    {
        $this->form->addHidden(FC::hidden('vis_id', $this->aVisita['vis_id']));
        $this->form->addHidden(FC::hidden('acao', 'cancelar'));

        $campo_relatorio = FC::textarea(
            'Relatório de Cancelamento',
            'vis_relatorio',
            $this->aVisita['vis_relatorio'] ?? '',
            ['autofocus' => 'autofocus']
        );

        $this->form->setFields([
            [$campo_relatorio]
        ]);

        $this->form->setActions(L::submit('Salvar'));
    }

    private function saidaPagina()
    {
        $this->response->page(
            <<<HTML
                {$this->cabecalho}
                <div class="content">
                    <div class="container-fluid pb-1">
                        {$this->form->html()}
                    </div>
                </div>
            HTML,
            ['title' => 'Cancelamento de Visita']
        );
    }
}
