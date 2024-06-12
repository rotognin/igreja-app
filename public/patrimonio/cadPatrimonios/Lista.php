<?php

namespace View\Patrimonio;

use Funcoes\Layout\Layout as L;
use App\PATRIMONIO\DAO\CategoriaPatrimonio;
use App\PATRIMONIO\Datatables\DatatablePatrimonio;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private CategoriaPatrimonio $categoriaDAO;
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
        $this->iniciarDAO();
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

    private function iniciarDAO()
    {
        $this->categoriaDAO = new CategoriaPatrimonio();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Cadastro de Patrimônio</h1>',
            L::linkButton('Novo Patrimônio', '?posicao=form', '', 'fas fa-plus', 'primary')
        );
    }

    private function inicioFormFiltros()
    {
        $this->formFiltros = new Form();
        $this->formFiltros->setTitle('<i class="fas fa-filter"></i> Filtros');
        $this->formFiltros->setForm('action="" method="GET"');
        $this->formFiltros->setCollapsable(true);
        $this->formFiltros->setCollapsed(count($this->request->getArray()) == 0);
        $this->formFiltros->setActions(L::submit('Filtrar', 'fas fa-filter', 'primary', 'sm'));
    }

    private function montarCamposFiltros()
    {
        $filtro_titulo = FC::input('Descrição', 'pat_descricao', $this->request->get('pat_descricao', ''), [
            'div_class' => 'col-md-4',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm'
        ]);

        $aCategorias = $this->categoriaDAO->montarArray(['0' => 'Todas']);

        $filtro_categoria = FC::select(
            'Categoria',
            'cpa_id',
            $aCategorias,
            $this->request->get('cpa_id', '0'),
            [
                'div_class' => 'col-md-2',
                'class' => 'form-control form-control-sm',
            ]
        );

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_titulo . $filtro_categoria . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatablePatrimonio::class);
    }

    private function filtrosPadrao()
    {
        $this->table->addFilters([
            'cpa_id' => '0'
        ]);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirCategoria(cpa_id){
                    confirm('Deseja realmente excluir esta Categoria?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&cpa_id=' + cpa_id;
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
            ['title' => 'Cadastro de Patrimônio']
        );
    }
}
