<?php

namespace View\Ministerios\Musica\Categoria;

use Funcoes\Layout\Layout as L;
use App\MINISTERIOS\MUSICA\Datatables\DatatableCategorias;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Datatable $table;
    private string $script;

    public function __construct()
    {
        parent::__construct();
    }

    public function executar()
    {
        $this->montarCabecalho();
        $this->montarTabela();
        $this->montarScript();
        $this->saidaPagina();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Categorias</h1>',
            L::linkButton('Nova Categoria', '?posicao=form', '', 'fas fa-plus', 'primary')
        );
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableCategorias::class);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirCategoria(cam_id){
                    confirm('Deseja realmente excluir esta Categoria?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&cam_id=' + cam_id;
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
                        {$this->table->html()}
                    </div>
                </div>
                {$this->script}
            HTML,
            ['title' => 'Cadastro de Categorias de Músicas']
        );
    }
}
