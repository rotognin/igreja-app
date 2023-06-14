<?php

use App\SGC\DAO\Usuario;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;

$usuarioDAO = new Usuario();

$usu_login = $request->get('usu_login', '');
$usuario = [];
if (!empty($usu_login)) {
    $usuario = $usuarioDAO->get($usu_login);
    if (empty($usuario)) {
        $session->flash('error', 'Usuário não encontrado');
        return $response->back();
    }
}
$formData = $session->get('previous', $usuario);

$abaDisabled = empty($usu_login) ? 'disabled' : '';
$abas = L::tabs([
    L::tab('Autenticação', '?posicao=form_auth', 'Provedor de autenticação', 'fas fa-key', 'active', 'aba-usu-autenticacao'),
    L::tab('Perfil', "?posicao=form&usu_login=$usu_login", 'Perfil do usuário', 'fas fa-user', $abaDisabled, 'aba-usu-perfil'),
    L::tab('Empresas', '?posicao=empresas&usu_login=' . $usu_login, 'Empresas do usuário', 'fas fa-building', $abaDisabled, 'aba-usu-empresas'),
    L::tab('Ações', '?posicao=acoes&usu_login=' . $usu_login, 'Ações do Usuário', 'fas fa-shield-halved', $abaDisabled, 'aba-usu-acoes'),
]);

$pageHeader = L::pageTitle(
    "<h1 class=\"m-0 text-dark\">Cadastro de usuários</h1>" . (!empty($usu_login) ? "<p>Usuário: {$usuario['usu_login']}</p>" : 'Novo usuário'),
    L::linkbutton('Voltar', 'cadUsuario.php', 'Voltar', 'fas fa-angle-left'),
    $abas
);

$form = new Form();
$form->setTitle('Informações de autenticação');
$form->setForm('id="form-usuario" action="?posicao=salvar_auth" method="post"');
if (!empty($usu_login)) {
    $form->addHidden(FC::hidden('usu_login', $usu_login));
    $form->addHidden(FC::hidden('alterar', 'S'));
}

$usuLoginAttrs = [];
if (!empty($usu_login)) {
    $usuLoginAttrs['disabled'] = 'disabled';
}

$senhaAttrs = !empty($usu_login) ? ['disabled' => 'disabled'] : [];
$alterarSenha = !empty($usu_login) ? [FC::switch('Alterar Senha', 'alterar_senha', 'S', false)] : [];
$form->setFields([
    [FC::radio('Provedor de autenticação', 'usu_provedor_auth', ['interno' => 'Interno', 'ad' => 'AD'], $formData['usu_provedor_auth'] ?? 'ad')],
    [L::alert('info', 'Usuários vinculados ao AD tem seus perfis sincronizados a cada login')],
    ['<hr>'],
    [FC::switch('Usuário ativo', 'usu_ativo', 'S', ($usuario['usu_ativo'] ?? 'N') == 'S')],
    [FC::input('Login', 'usu_login', $formData['usu_login'] ?? '', $usuLoginAttrs)],
    $alterarSenha,
    [FC::input('Senha', 'usu_senha', '', ['type' => 'password'] + $senhaAttrs), FC::input('Confirme a senha', 'confirme_senha', '', ['type' => 'password'] + $senhaAttrs)],
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
        function changeProvedor() {
            const provedor = $('input[name="usu_provedor_auth"]:checked').val();
            if (provedor == 'ad') {
                $('#usu_senha').prop('disabled', true).parents('.form-group').hide();
                $('#confirme_senha').prop('disabled', true).parents('.form-group').hide();
                $('#alterar_senha').prop('disabled', true).parents('.form-group').hide();
            } else {
                $('#usu_senha').prop('disabled', false).parents('.form-group').show();
                $('#confirme_senha').prop('disabled', false).parents('.form-group').show();
                $('#alterar_senha').prop('disabled', false).parents('.form-group').show();
            }
        }

        $(function() {
            changeProvedor();
            $('input[name="usu_provedor_auth"]').change(changeProvedor);

            $('#alterar_senha').change(function() {
                $('#usu_senha').prop('disabled', !$(this).prop('checked'));
                $('#confirme_senha').prop('disabled', !$(this).prop('checked')); 
            });
            $('#alterar_senha').change();

            $('#form-usuario').validate({
                rules: {
                    usu_nome: {
                        required: true,
                        minlength: 3
                    },
                    usu_email: {
                        email: true
                    },
                    usu_login: {
                        required: true,
                    },
                    usu_senha: {
                        required: true,
                        minlength: 6
                    },
                    confirme_senha: {
                        required: true,
                        minlength: 6,
                        equalTo: '#usu_senha'
                    }
                },
                messages: {
                    usu_nome: {
                        required: 'Informe o nome do usuário',
                        minlength: 'O nome deve ter no mínimo 3 caracteres'
                    },
                    usu_email: {
                        email: 'Informe um e-mail válido'
                    },
                    usu_login: {
                        required: 'Informe o login do usuário',
                    },
                    usu_senha: {
                        required: 'Informe a senha do usuário',
                        minlength: 'A senha deve ter no mínimo 6 caracteres'
                    },
                    confirme_senha: {
                        required: 'Confirme a senha do usuário',
                        minlength: 'A senha deve ter no mínimo 6 caracteres',
                        equalTo: 'As senhas não conferem'
                    }
                },
                invalidHandler: function(form, validator){
                    $('#overlay').remove();
                }
            });
        });   
        </script>
        HTML,
    ["title" => "Cadastro de usuários"]
);
