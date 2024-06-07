<?php

namespace View\Movimentacoes;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\DAO\Familias;
use App\MOVIMENTACOES\DAO\Visitas;
use App\MOVIMENTACOES\Datatables\DatatableVisitas;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use Funcoes\Lib\GlobalHelper;
use Funcoes\Lib\Traits\TraitFamilia;

class Lista extends GlobalHelper
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
            '<h1 class="m-0 text-dark">' . 'Visitas' . '</h1>',
            L::linkButton('Nova Visita', '?posicao=form', '', 'fas fa-plus', 'primary')
        );
    }

    private function inicioFormFiltros()
    {
        $this->formFiltros = new Form();
        $this->formFiltros->setTitle('<i class="fas fa-filter"></i> Filtros');
        $this->formFiltros->setForm('id="form-filtros" action="" method="GET"');
        $this->formFiltros->setCollapsable(true);
        $this->formFiltros->setCollapsed(count($this->request->getArray()) == 0);
        $this->formFiltros->setActions(L::submit('Filtrar', 'fas fa-filter'));
    }

    private function montarCamposFiltros()
    {
        $filtro_data_inicial = FC::input('Data inicial', 'vis_data_ini', $this->request->get('vis_data_ini', ''), [
            'class' => 'form-control form-control-sm data-mask', 'div_class' => 'col-md-2'
        ]);
        $filtro_data_final = FC::input('Data final', 'vis_data_fim', $this->request->get('vis_data_fim', ''), [
            'class' => 'form-control form-control-sm data-mask', 'div_class' => 'col-md-2'
        ]);

        $arraySituacao = array_merge(['0' => 'Todas'], $this->visitasDAO->getSituacoes());
        $filtro_situacao = FC::select('Situação da Visita', 'vis_situacao', $arraySituacao, $this->request->get('vis_situacao', 'A'), [
            'class' => 'form-control form-control-sm', 'div_class' => 'col-md-2'
        ]);

        $familias = $this->buscarFamilias();
        $filtro_familias = FC::select2('Família', 'fam_nome', $familias, $this->request->get('fam_nome', '0'), [
            'class' => 'form-control form-control-sm', 'div_class' => 'col-md-4'
        ]);

        $this->formFiltros->setFields([
            ['<div class="row">' . $filtro_data_inicial . $filtro_data_final . $filtro_situacao . $filtro_familias . '</div>']
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableVisitas::class);

        if (count($this->request->getArray()) == 0) {
            $this->table->addFilters(['vis_situacao' => 'A']);
        }
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                $('.data-mask').mask('00/00/0000');
                
                $(function(){
                    $.validator.addMethod("checarDataInicial", function(campo){
                        if (campo == '' || campo == undefined){
                            data_fim = $("#vis_data_fim").val();
                            if (data_fim != ''){
                                mensagem('Para pesquisar pelo perído, informe as duas datas');
                                return false;
                            }

                            return true;
                        }

                        if (!dataValida(campo)){
                            return false;
                        }

                        data_ini = campo;
                        data_fim = $("#vis_data_fim").val();

                        if (!inicioMenor(data_ini, data_fim)){
                            mensagem('Data inicial maior que a final');
                            return false;
                        }

                        return true;
                    }, "Data inválida");

                    $.validator.addMethod("checarDataFinal", function(campo){
                        if (campo == '' || campo == undefined){
                            data_ini = $("#vis_data_ini").val();
                            if (data_ini != ''){
                                mensagem('Para pesquisar pelo perído, informe as duas datas');
                                return false;
                            }

                            return true;
                        }

                        if (!dataValida(campo)){
                            return false;
                        }

                        data_ini = $("#vis_data_ini").val();
                        data_fim = campo;

                        if (!inicioMenor(data_ini, data_fim)){
                            mensagem('Data inicial maior que a final');
                            return false;
                        }

                        return true;
                    }, "Data inválida");

                    $('#form-filtros').validate({
                        onfocusout: false,
                        onkeyup: false,
                        onclick: false,
                        onsubmit: true,
                        rules: {
                            vis_data_ini: "checarDataInicial",
                            vis_data_fim: "checarDataFinal"
                        }
                    });
                });

                function editarVisita(vis_id){
                    window.location.href = '?posicao=form&vis_id=' + vis_id;
                }

                function realizarVisita(vis_id){
                    window.location.href = '?posicao=realizar&vis_id=' + vis_id;
                }

                function cancelarVisita(vis_id){
                    confirm('Deseja realmente cancelar esta visita?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=cancelar&vis_id=' + vis_id;
                        }
                    });
                }

                function excluirVisita(vis_id){
                    confirm('Deseja realmente excluir esta visita?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&vis_id=' + vis_id;
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
