<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\Datatables\DatatablePessoas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\ViewHelper;

class Lista extends ViewHelper
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
        $this->montarScript();
        $this->saidaPagina();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Pessoas') . '</h1>',
            L::linkButton(_('Nova Pessoa'), '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_nome   = FC::input(_('Nome'), 'pes_nome', $this->request->get('pes_nome'));
        $filtro_bairro  = FC::input(_('Bairro'), 'pes_bairro', $this->request->get('pes_bairro'));

        $this->formFiltros->setFields([
            [$filtro_nome, $filtro_bairro]
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatablePessoas::class);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirPessoa(mem_id){
                    confirm('Deseja realmente excluir esta pessoa?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&pes_id=' + pes_id;
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
            ['title' => 'Cadastro de Pessoas']
        );
    }
}
