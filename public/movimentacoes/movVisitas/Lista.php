<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Familias;
use App\MOVIMENTACOES\DAO\Visitas;
use App\MOVIMENTACOES\Datatables\DatatableVisitas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\ViewHelper;
use Funcoes\Lib\Traits\TraitFamilia;

class Lista extends ViewHelper
{
    use TraitFamilia;

    private string $cabecalho;
    private Form $formFiltros;
    private Datatable $table;
    private string $script;
    private Visitas $visitasDAO;
    private Familias $familiasDAO;

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
        $this->visitasDAO = new Visitas();
        $this->familiasDAO = new Familias();
    }

    private function montarCabecalho()
    {
        $this->cabecalho = L::pageTitle(
            '<h1 class="m-0 text-dark">' . _('Visitas') . '</h1>',
            L::linkButton(_('Nova Visita'), '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_data_inicial = FC::date(_('Data inicial'), 'vis_data_ini', $this->request->get('vis_data_ini', ''));
        $filtro_data_final = FC::date(_('Data final'), 'vis_data_fim', $this->request->get('vis_data_fim', ''));

        $familias = $this->buscarFamilias();
        $filtro_familias = FC::select2(_('Família'), 'fam_nome', $familias, $this->request->get('fam_nome', '0'));

        $arraySituacao = array_merge(['0' => 'Todas'], $this->visitasDAO->getSituacoes());
        $filtro_situacao = FC::select(_('Situação da Visita'), 'vis_status', $arraySituacao, $this->request->get('vis_status', '0'));

        $filtro_periodo = FC::switch(_('Filtrar pelo período'), 'vis_periodo', '1', $this->request->get('vis_periodo', '0') == '1', ['div_class' => 'm-0']);

        $this->formFiltros->setFields([
            [$filtro_data_inicial, $filtro_data_final, $filtro_familias, $filtro_situacao],
            [$filtro_periodo]
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableVisitas::class);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function cancelarVisita(vis_id){
                    confirm('Deseja realmente cancelar esta visita?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=cancelar&vis_id=' + vis_id;
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
            ['title' => 'Cadastro de Visitas']
        );
    }
}
