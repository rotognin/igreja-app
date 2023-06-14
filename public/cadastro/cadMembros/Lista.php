<?php

namespace View\Cadastro;

use Funcoes\Layout\Layout as L;
use App\CADASTRO\Datatables\DatatableMembros;
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
            '<h1 class="m-0 text-dark">' . _('Cadastro de Membros') . '</h1>',
            L::linkButton(_('Novo Membro'), '?posicao=form', '', 'fas fa-plus', 'primary')
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
        $filtro_nome   = FC::input(_('Nome'), 'mem_nome', $this->request->get('mem_nome'));
        $filtro_telefone  = FC::input(_('Telefone'), 'mem_telefone', $this->request->get('mem_telefone'));

        $this->formFiltros->setFields([
            [$filtro_nome, $filtro_telefone]
        ]);
    }

    private function montarTabela()
    {
        $this->table = new Datatable(DatatableMembros::class);
    }

    private function montarScript()
    {
        $this->script = <<<HTML
            <script>
                function excluirMembro(mem_id){
                    confirm('Deseja realmente excluir este membro?').then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '?posicao=excluir&mem_id=' + mem_id;
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
            ['title' => 'Cadastro de Membros']
        );
    }
}
