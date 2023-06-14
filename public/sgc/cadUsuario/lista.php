<?php

use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;
use App\SGC\Datatables\DatatableUsuarios;


$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">' . _('Cadastro de usuários') . '</h1>', L::linkButton("Novo usuário", '?posicao=form_auth', '', 'fas fa-plus', 'primary'));

$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Filtros');
$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(count($request->getArray()) == 0);
$form->setActions(L::submit('Filtrar', 'fas fa-filter'));
$form->setFields([
    [
        FC::input('Login', 'usu_login', $request->get('usu_login')),
        FC::input('Nome', 'usu_nome', $request->get('usu_nome'))
    ],
    [
        FC::select('Celular c/ Whatsapp', 'usu_celular_whatsapp', ['' => 'Todos', 'S' => 'Sim', 'N' => 'Não'], $request->get('usu_celular_whatsapp')),
        FC::select('Usuário ativo', 'usu_ativo', ['' => 'Todos', 'S' => 'Sim', 'N' => 'Não'], $request->get('usu_ativo', 'S'))
    ]
]);

$table = new Datatable(DatatableUsuarios::class);
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
        HTML,
    ["title" => "Cadastro de usuários"]
);
