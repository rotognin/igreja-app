<?php

// Select2 com Ajax, consulta em tempo real

require_once('header.php');

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Lib\Consultas\Cliente;

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">' . _('Teste de Ajax') . '</h1>'
);

$form = new Form();
$form->setTitle('Select2 com Ajax');
$form->setForm('id="form-teste" action="?posicao=salvar" method="post"');

$obj = new Cliente();

$campo_select = FC::select2ajax(
    'Clientes',
    'form_cliente',
    [],
    '0',
    ['url' => $obj->getUrl()]
);

$aForm = array([
    $campo_select
]);

$form->setFields($aForm);

$html = $form->html();

$response->page(
    <<<HTML
    $pageHeader
    <div class="content">
        <div class="container-fluid pb-1">
            $html
        </div>
    </div>
    HTML,
    ["title" => _("Testes com componentes")]
);
