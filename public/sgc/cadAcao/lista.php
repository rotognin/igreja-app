<?php

use Funcoes\Layout\Layout as L;
use App\SGC\DAO\Acao;
use App\SGC\Datatables\DatatableAcoes;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$acaoDAO = new Acao();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de ações de usuário</h1>', L::linkButton("Nova ação", '?posicao=form', '', 'fas fa-plus', 'primary'));

$grupos = ['' => 'Todos'];
foreach ($acaoDAO->grupos() as $g) {
    $grupos[$g['aca_grupo']] = $g['aca_grupo'];
}

$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setFields([
    [FC::input('Ação', 'aca_acao', $request->get('aca_acao')), FC::input('Descrição', 'aca_descricao', $request->get('aca_descricao'))],
    [FC::select('Grupo', 'aca_grupo', $grupos, $request->get('aca_grupo')), ''],
]);

$table = new Datatable(DatatableAcoes::class);
$html = $table->html();

$response->page(
    <<<HTML
        $pageHeader
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                $html
            </div>
        </div>
        <script>
            function deleteAcao(aca_acao) {
                confirm('Deseja realmente excluir esta ação de usuário?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '?posicao=delete&aca_acao=' + aca_acao;
                    }
                });
            }
        </script>
        HTML,
    ["title" => "Cadastro de ações de usuário"]
);
