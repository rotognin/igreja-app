<?php

namespace View\Patrimonio;

use Funcoes\Layout\Layout as L;
use App\PATRIMONIO\Datatables\DatatableCategoriaPatrimonio;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Form $formFiltros;
    private Datatable $table;
    private string $script;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->montarCabecalho();
        $this->inicioFormFiltros();
        $this->montarCamposFiltros();
        $this->montarTabela();

        if (count($this->request->getArray()) == 0) {
            $this->filtrosPadrao();
        }

        $this->montarScript();
        $this->saidaPagina();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Ministérios') . '</h1>',
            L::linkButton(_('Novo Ministério'), '?posicao=form', '', 'fas fa-plus', 'primary')
        );
    }

    private function inicioFormFiltros()
    {
        $this->formFiltros = new Form();
        $this->formFiltros->setTitle('<i class="fas fa-filter"></i> Filtros');
        $this->formFiltros->setForm('action="" method="GET"');
        $this->formFiltros->setCollapsable(true);
        $this->formFiltros->setCollapsed(count($this->request->getArray()) == 0);
        $this->formFiltros->setActions(L::submit('Filtrar', 'fas fa-filter'));
    }

    private function montarCamposFiltros()
    {
        $filtro_nome   = FC::input(_('Nome'), 'min_nome', $this->request->get('min_nome'), [
            'div_class' => 'col-md-4',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm',
        ]);
        $filtro_sigla  = FC::input(_('Sigla'), 'min_sigla', $this->request->get('min_sigla'), [
            'div_class' => 'col-md-1',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm',
        ]);
        $filtro_ativo = FC::select(
            'Situação',
            'min_ativo',
            ['T' => 'Todas', 'S' => 'Ativo', 'N' => 'Inativo'],
            $this->request->get('min_ativo', 'S'),
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm',
            ]
        );

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_nome . $filtro_sigla . $filtro_ativo . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableCategoriaPatrimonio::class);
    }

    private function filtrosPadrao()
    {
        $this->table->addFilters([
            'min_ativo' => 'S'
        ]);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirMinisterio(min_id){
                    confirm('Deseja realmente excluir este Ministério?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&min_id=' + min_id;
                        }
                    });
                }
            </script>
        HTML;
    }

    private function saidaPagina()
    {
        $this->response->page(
            <<<HTML
                {$this->cabecalho}
                <div class="content">
                    <div class="container-fluid pb-1">
                        {$this->formFiltros->html()}
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Cadastro de Ministérios']
        );
    }
}
