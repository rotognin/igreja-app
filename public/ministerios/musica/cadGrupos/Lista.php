<?php

namespace View\Ministerios\Musica\Grupo;

use Funcoes\Layout\Layout as L;
use App\MINISTERIOS\MUSICA\Datatables\DatatableGrupos;
use App\MINISTERIOS\MUSICA\DAO\Grupos;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Form $formFiltros;
    private Datatable $table;
    private Grupos $gruposDAO;
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
        $this->gruposDAO = new Grupos();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Grupos</h1>',
            L::linkButton('Novo Grupo', '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_nome = FC::input('Nome', 'gru_nome', $this->request->get('gru_nome'), [
            'div_class' => 'col-md-4',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm'
        ]);

        $filtro_situacao = FC::select('Situação', 'gru_situacao', ['T' => 'Todas'] + $this->gruposDAO->getSituacao(), $this->request->get('gru_situacao', 'T'), [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm'
        ]);

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_nome . $filtro_situacao . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableGrupos::class);

        if (count($this->request->getArray()) == 0) {
            $this->table->addFilters([
                'gru_situacao' => 'T'
            ]);
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluir(gru_id){
                    confirm('Deseja realmente excluir este Grupo?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&gru_id=' + gru_id;
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
            ['title' => 'Cadastro de Grupos']
        );
    }
}
