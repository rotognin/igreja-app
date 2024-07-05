<?php

namespace View\Ministerios\Musica\Escala;

use Funcoes\Layout\Layout as L;
use App\MINISTERIOS\MUSICA\Datatables\DatatableEscalas;
use App\MINISTERIOS\MUSICA\DAO\Escalas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;

class Lista extends GlobalHelper
{
    private string $cabecalho;
    private Form $formFiltros;
    private Datatable $table;
    private Escalas $escalasDAO;
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
        $this->escalasDAO = new Escalas();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">Escalas</h1>',
            L::linkButton('Nova Escala', '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_titulo = FC::input('Título', 'esc_titulo', $this->request->get('esc_titulo'), [
            'div_class' => 'col-md-4',
            'style' => 'text-transform:uppercase',
            'class' => 'form-control form-control-sm'
        ]);

        $filtro_situacao = FC::select('Situação', 'esc_situacao', ['T' => 'Todas'] + $this->escalasDAO->getSituacao(), $this->request->get('esc_situacao', 'T'), [
            'div_class' => 'col-md-2',
            'class' => 'form-control form-control-sm'
        ]);

        $filtro_data_inicial = FC::input('Data inicial', 'esc_data_ini', $this->request->get('esc_data_ini', ''), [
            'class' => 'form-control form-control-sm data-mask', 'div_class' => 'col-md-2'
        ]);

        $filtro_data_final = FC::input('Data final', 'esc_data_fim', $this->request->get('esc_data_fim', ''), [
            'class' => 'form-control form-control-sm data-mask', 'div_class' => 'col-md-2'
        ]);

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_titulo . $filtro_situacao . '</div>'],
            ['<div class="row">' . $filtro_data_inicial . $filtro_data_final . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableEscalas::class);

        if (count($this->request->getArray()) == 0) {
            $this->table->addFilters([
                'esc_situacao' => 'T'
            ]);
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $('.data-mask').mask('00/00/0000');

                function excluir(esc_id){
                    confirm('Deseja realmente excluir esta Escala?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&esc_id=' + esc_id;
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
            ['title' => 'Cadastro de Escalas']
        );
    }
}
