<?php

namespace View\Ministerios\Musica\Musica;

use Funcoes\Layout\Layout as L;
use App\MINISTERIOS\MUSICA\Datatables\DatatableMusicas;
use App\MINISTERIOS\MUSICA\DAO\Musicas;
use App\MINISTERIOS\MUSICA\DAO\Categorias;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Form $formFiltros;
    private Datatable $table;
    private Musicas $musicasDAO;
    private Categorias $categoriaDAO;
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
        $this->montarScript();
        $this->saidaPagina();
    }

    private function iniciarDAO()
    {
        $this->musicasDAO = new Musicas();
        $this->categoriaDAO = new Categorias();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Músicas</h1>',
            L::linkButton('Nova Música', '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_nome = FC::input('Nome', 'mus_nome', $this->request->get('mus_nome'), [
            'div_class' => 'col-md-4',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm'
        ]);

        $filtro_situacao = FC::select('Situação', 'mus_situacao', ['T' => 'Todas'] + $this->musicasDAO->getSituacao(), $this->request->get('mus_situacao', 'T'), [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm'
        ]);

        $filtro_categoria = FC::select('Categoria', 'mus_categoria_id', $this->categoriaDAO->montarArray(['T' => 'Todas']));

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_nome . $filtro_situacao . $filtro_categoria . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableMusicas::class);

        if (count($this->request->getArray()) == 0) {
            $this->table->addFilters([
                'mus_situacao' => 'T'
            ]);
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function abrir(mus_link){
                    window.open(mus_link, '_blank');
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
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Cadastro de Músicas']
        );
    }
}
