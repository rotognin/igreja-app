<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\Datatables\DatatableFamilias;
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
        $this->montarScript();
        $this->saidaPagina();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Cadastro de Famílias') . '</h1>',
            L::linkButton(_('Nova Família'), '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_nome   = FC::input(_('Nome'), 'fam_nome', $this->request->get('fam_nome'), ['div_class' => 'col-md-6']);

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_nome . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableFamilias::class);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirFamilia(fam_id){
                    confirm('Deseja realmente excluir esta Família? <br> Todas as pessoas e membros associados também serão excluídos!').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&fam_id=' + fam_id;
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
            ['title' => 'Cadastro de Famílias']
        );
    }
}
