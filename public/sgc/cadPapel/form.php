<?php

use Funcoes\Layout\Layout as L;
use App\SGC\DAO\Papel;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$papelDAO = new Papel();

$pap_id = $request->get('pap_id', '');

$aPapel = [];
if (!empty($pap_id)) {
    $aPapel = $papelDAO->get($pap_id);
    if (empty($aPapel)) {
        $session->flash('error', 'Papel do Usuário não encontrado');
        return $response->back();
    }
}
if ($session->check('previous')) {
    $aPapel = $session->get('previous');
}

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de Papel de Usuário</h1>', L::backButton());

$form = new Form();
$form->setTitle($pap_id ? "Editar Papel" : 'Novo Papel');
$form->setForm('id="form-papel" action="?posicao=salvar" method="post"');
if (!empty($pap_id)) {
    $form->addHidden(FC::hidden('pap_id', $pap_id));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$form->setFields([
    [FC::input('Descrição', 'pap_descricao', $aPapel['pap_descricao'] ?? '', ['autofocus' => 'autofocus'])]
]);
$form->setActions(L::submit('Salvar'));

$response->page(
    <<<HTML
        $pageHeader
        <div class="content pb-1">
            <div class="container-fluid pb-1">
                {$form->html()}
            </div>
        </div>
        <script>
        $(function() {
            $('#form-papel').validate({
                rules: {
                    pap_descricao: {
                        required: true,
                    }
                },
                messages: {
                    pap_descricao: {
                        required: 'Informe o Papel do usuário',
                    }
                },
                invalidHandler: function(form, validator){
                    $('#overlay').remove();
                }
            });
        });   
        </script>
        HTML,
    ["title" => "Cadastro de Papéis"]
);
