<?php

use Funcoes\Layout\Layout as L;
use App\SGC\DAO\Acao;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$acaoDAO = new Acao();

$aca_acao = $request->get('aca_acao', '');
$acao = [];
if (!empty($aca_acao)) {
    $acao = $acaoDAO->get($aca_acao);
    if (empty($acao)) {
        $session->flash('error', 'Ação de usuário não encontrada');
        return $response->back();
    }
}
$acao = $session->get('previous', $acao);
$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de ações de usuário</h1>', L::backButton());

$form = new Form();
$form->setTitle($aca_acao ? "Editar ação: $aca_acao" : 'Nova ação de usuário');
$form->setForm('id="form-acao" action="?posicao=salvar" method="post"');
if (!empty($aca_acao)) {
    $form->addHidden(FC::hidden('aca_acao', $aca_acao));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$acaAcaoAttrs = [];
if (!empty($aca_acao)) {
    $acaAcaoAttrs['disabled'] = 'disabled';
}

$form->setFields([
    [FC::input('Ação', 'aca_acao', $acao['aca_acao'] ?? '', $acaAcaoAttrs), FC::input('Grupo', 'aca_grupo', $acao['aca_grupo'] ?? '')],
    [FC::input('Descrição', 'aca_descricao', $acao['aca_descricao'] ?? '')],
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
            $('#form-acao').validate({
                rules: {
                    aca_acao: {
                        required: true,
                    },
                    aca_descricao: {
                        required: true
                    },
                    aca_grupo: {
                        required: true
                    }
                },
                messages: {
                    aca_acao: {
                        required: 'Informe a identificação da ação',
                    },
                    aca_descricao: {
                        required: 'Informe a descrição da ação',
                    },
                    aca_grupo: {
                        required: 'Informe o grupo da ação',
                    },
                },
                invalidHandler: function(form, validator){
                    $('#overlay').remove();
                }
            });
        });   
        </script>
        HTML,
    ["title" => "Cadastro de ações de usuário"]
);
