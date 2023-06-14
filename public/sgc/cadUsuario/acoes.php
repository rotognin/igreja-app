<?php

use App\SGC\DAO\Acao;
use App\SGC\DAO\Usuario;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;

$usuarioDAO = new Usuario();
$acaoDAO = new Acao();

$usuario = $usuarioDAO->get($request->get('usu_login'));
if (empty($usuario)) {
    $session->flash('error', 'Usuário não encontrado');
    return $response->back();
}

$usu_login = $usuario['usu_login'];

$acoesUsuario = $usuarioDAO->getAcoes($request->get('usu_login'));

$acoes = ['' => 'Selecione uma ação'];
foreach ($acaoDAO->getArray() as $acao) {
    if (in_array($acao['aca_acao'], array_column($acoesUsuario, 'aca_acao'))) {
        continue;
    }
    if (!isset($acoes[$acao['aca_grupo']])) {
        $acoes[$acao['aca_grupo']] = [];
    }
    $acoes[$acao['aca_grupo']][$acao['aca_acao']] = "{$acao['aca_acao']} - {$acao['aca_descricao']}";
}

$abas = L::tabs([
    L::tab('Autenticação', "?posicao=form_auth&usu_login=$usu_login", 'Provedor de autenticação', 'fas fa-key', '', 'aba-usu-autenticacao'),
    L::tab('Perfil', "?posicao=form&usu_login=$usu_login", 'Perfil do usuário', 'fas fa-user', '', 'aba-usu-perfil'),
    L::tab('Empresas', '?posicao=empresas&usu_login=' . $usu_login, 'Empresas do usuário', 'fas fa-building', '', 'aba-usu-empresas'),
    L::tab('Ações', '?posicao=acoes&usu_login=' . $usu_login, 'Ações do Usuário', 'fas fa-shield-halved', 'active', 'aba-usu-acoes'),
]);

$pageHeader = L::pageTitle(
    "<h1 class=\"m-0 text-dark\">Cadastro de usuários</h1><p>Usuário: {$usu_login}</p>",
    L::linkbutton('Voltar', 'cadUsuario.php', 'Voltar', 'fas fa-angle-left'),
    $abas
);

$form = new Form();
$form->setTitle('Adicionar ação');
$form->setCollapsable(true);
$form->setCollapsed(true);
$form->setForm('id="form-usuario-acoes" method="POST" action="?posicao=salvar_acao"');
$form->addHidden(FC::hidden('usu_login', $usu_login));
$form->setFields([
    [
        FC::select2('Ação', 'aca_acao', $acoes, "", ['data' => ['placeholder' => 'Selecione uma ação', 'allow-clear' => true]]),
    ]
]);
$form->setActions(L::submit('Adicionar'));

$usuariosCopia = ['' => 'Selecione um usuário'];
foreach ($usuarioDAO->getArray([' AND u.usu_login <> ?', [$usu_login]]) as $u) {
    $usuariosCopia[$u['usu_login']] = "{$u['usu_login']} - {$u['usu_nome']}";
}

$formCopia = new Form();
$formCopia->setTitle('Copiar ações do usuário');
$formCopia->setCollapsable(true);
$formCopia->setCollapsed(true);
$formCopia->setForm('id="form-usuario-copia" method="POST" action="?posicao=copiar_acao"');
$formCopia->addHidden(FC::hidden('usu_login', $usu_login));
$formCopia->setFields([
    [
        FC::select2('Usuário', 'usu_login_copia', $usuariosCopia, "", ['data' => ['placeholder' => 'Selecione um usuário', 'allow-clear' => true]]),
    ]
]);
$formCopia->setActions(L::submit('Copiar'));

$tableHTML = L::alert('info', 'Nenhuma ação de usuário associada');
if (!empty($acoesUsuario)) {
    $table = new Table();
    $table->addHeader([
        'cols' => [
            ['value' => 'Ação'],
            ['value' => 'Grupo'],
            ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
        ], 'attrs' => ['class' => 'text-center text-nowrap']
    ]);
    foreach ($acoesUsuario as $acao) {
        $table->addRow([
            'cols' => [
                ['value' => $acao['aca_acao']],
                ['value' => $acao['aca_grupo']],
                ['value' => L::linkButton('', "?posicao=remover_acao&usu_login={$usu_login}&aca_acao={$acao['aca_acao']}", 'Remover ação', 'fas fa-trash', 'outline-danger', 'sm')],
            ], 'attrs' => ['class' => 'text-center text-nowrap']
        ]);
    }
    $tableHTML = $table->html();
}

$response->page(
    <<<HTML
        $pageHeader
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                {$formCopia->html()}
                $tableHTML
            </div>
        </div>
        <script>
            $(function() {
                $('#form-usuario-acoes').validate({
                    rules: {
                        aca_acao: {
                            required: true,
                        },
                    },
                    messages: {
                        aca_acao: {
                            required: 'Selecione uma ação de usuário',
                        },
                    },
                    invalidHandler: function(form, validator){
                        $('#overlay').remove();
                    }
                });

                $('#form-usuario-copia').validate({
                    rules: {
                        usu_login_copia: {
                            required: true,
                        },
                    },
                    messages: {
                        usu_login_copia: {
                            required: 'Selecione um usuário para copiar as ações',
                        },
                    },
                    invalidHandler: function(form, validator){
                        $('#overlay').remove();
                    }
                });
            });
        </script>
        HTML,
    ["title" => "Ações do usuário"]
);
