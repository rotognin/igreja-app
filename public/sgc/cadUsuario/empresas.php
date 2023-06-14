<?php

use App\SGC\DAO\Empresa;
use App\SGC\DAO\Usuario;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;

$usuarioDAO = new Usuario();
$empresaDAO = new Empresa();

$usu_login = $request->get('usu_login');

$usuario = $usuarioDAO->get($usu_login);
if (empty($usuario)) {
    $session->flash('error', 'Usuário não encontrado');
    return $response->back();
}

$empresasUsuario = $usuarioDAO->getEmpresas($usu_login);

$empresas = ['' => 'Selecione uma empresa'];
foreach ($empresaDAO->getArray() as $empresa) {
    if (in_array($empresa['emp_codigo'], array_column($empresasUsuario, 'emp_codigo'))) {
        continue;
    }
    $empresas[$empresa['emp_codigo']] = "{$empresa['emp_codigo']} - {$empresa['emp_nome']}";
}

$abas = L::tabs([
    L::tab('Autenticação', "?posicao=form_auth&usu_login=$usu_login", 'Provedor de autenticação', 'fas fa-key', '', 'aba-usu-autenticacao'),
    L::tab('Perfil', "?posicao=form&usu_login=$usu_login", 'Perfil do usuário', 'fas fa-user', '', 'aba-usu-perfil'),
    L::tab('Empresas', '?posicao=empresas&usu_login=' . $usu_login, 'Empresas do usuário', 'fas fa-building', 'active', 'aba-usu-empresas'),
    L::tab('Ações', '?posicao=acoes&usu_login=' . $usu_login, 'Ações do Usuário', 'fas fa-shield-halved', '', 'aba-usu-acoes'),
]);

$pageHeader = L::pageTitle(
    "<h1 class=\"m-0 text-dark\">Cadastro de usuários</h1><p>Usuário: {$usuario['usu_login']}</p>",
    L::linkbutton('Voltar', 'cadUsuario.php', 'Voltar', 'fas fa-angle-left'),
    $abas
);

$form = new Form();
$form->setTitle('Adicionar empresa');
$form->setForm('id="form-usuario-empresas" method="POST" action="?posicao=salvar_empresa"');
$form->addHidden(FC::hidden('usu_login', $usuario['usu_login']));
$form->setFields([
    [
        FC::select('Empresa', 'emp_codigo', $empresas),
        FC::switch('Empresa Padrão', 'emp_padrao', 'S', false, ['emptyLabel' => true]),
    ]
]);
$form->setActions(L::submit('Adicionar'));

$tableHTML = L::alert('info', 'Nenhuma empresa associada');
if (!empty($empresasUsuario)) {
    $table = new Table();
    $table->addHeader([
        'cols' => [
            ['value' => 'Empresa'],
            ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
        ]
    ]);
    foreach ($empresasUsuario as $empresa) {
        $padrao = $empresa['emp_padrao'] == 'S' ? ' <span class="badge badge-success">Padrão</span>' : '';
        $table->addRow([
            'cols' => [
                ['value' => "{$empresa['emp_codigo']} - {$empresa['emp_nome']} $padrao"],
                ['value' => L::linkButton('', "?posicao=remover_empresa&usu_login={$usuario['usu_login']}&emp_codigo={$empresa['emp_codigo']}", 'Remover empresa', 'fas fa-trash', 'outline-danger', 'sm'), 'attrs' => ['class' => 'text-center']],
            ]
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
                $tableHTML
            </div>
        </div>
        <script>
            $(function() {
                $('#form-usuario-empresas').validate({
                    rules: {
                        emp_codigo: {
                            required: true,
                        },
                    },
                    messages: {
                        emp_codigo: {
                            required: 'Selecione uma empresa',
                        },
                    },
                    invalidHandler: function(form, validator){
                        $('#overlay').remove();
                    }
                });
            });
        </script>
        HTML,
    ["title" => "Empresas do usuário"]
);
