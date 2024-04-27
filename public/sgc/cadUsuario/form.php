<?php

use App\SGC\DAO\Usuario;
use App\SGC\DAO\Papel;
use App\SGC\DAO\UsuarioPapel;
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

$abaDisabled = empty($usu_login) ? 'disabled' : '';
$abas = L::tabs([
    L::tab('Autenticação', "?posicao=form_auth&usu_login=$usu_login", 'Provedor de autenticação', 'fas fa-key', '', 'aba-usu-autenticacao'),
    L::tab('Perfil', '?posicao=form', 'Perfil do usuário', 'fas fa-user', 'active', 'aba-usu-perfil'),
    L::tab('Empresas', '?posicao=empresas&usu_login=' . $usu_login, 'Empresas do usuário', 'fas fa-building', $abaDisabled, 'aba-usu-empresas'),
    L::tab('Ações', '?posicao=acoes&usu_login=' . $usu_login, 'Ações do Usuário', 'fas fa-shield-halved', $abaDisabled, 'aba-usu-acoes'),
]);

$pageHeader = L::pageTitle(
    "<h1 class=\"m-0 text-dark\">Cadastro de usuários</h1><p>Usuário: {$usuario['usu_login']}</p>",
    L::linkbutton('Voltar', 'cadUsuario.php', 'Voltar', 'fas fa-angle-left'),
    $abas
);

$form = new Form();
$form->setTitle('Informações de perfil');
$form->setForm('id="form-usuario" action="?posicao=salvar" method="post"');
$form->addHidden(FC::hidden('usu_login', $usu_login));

$adAttrs = $usuario['usu_provedor_auth'] == 'ad' ? ['readonly' => 'readonly'] : [];

$papelDAO = new Papel();
$aPapeis = $papelDAO->getArray();

$aPapeisOpcoes = [];

foreach ($aPapeis as $papel) {
    $aPapeisOpcoes[$papel['pap_id']] = $papel['pap_descricao'];
}

$usuPapelDAO = new UsuarioPapel();
$where = array('');
$where[0] = ' AND usupap_usu_login = ?';
$where[1][] = $usu_login;

$aUsuPapeis = $usuPapelDAO->getArray($where);
$aPapeisUsuario = '';

$script = '';

if (!empty($aUsuPapeis)) {
    foreach ($aUsuPapeis as $papel) {
        $aPapeisUsuario .= $papel['usupap_papel_id'] . ', ';
    }

    $script = "$('#usu_papel_select').val([{$aPapeisUsuario}]).trigger('change');";
}

$campo_papeis = FC::select2(
    _('Papéis'),
    'usu_papel[]',
    $aPapeisOpcoes,
    '',
    [
        'class' => 'js-example-basic-multiple col-12',
        'multiple' => 'multiple',
        'id' => 'usu_papel_select'
    ]
);

$form->setFields([
    [FC::input('Nome', 'usu_nome', $usuario['usu_nome'] ?? '', $adAttrs)],
    [FC::input('Celular', 'usu_celular', $usuario['usu_celular'] ?? '', ['type' => 'tel', 'data-mask' => '(00) 00000-0000']), FC::switch('<i class="fab fa-whatsapp"></i> Whatsapp', 'usu_celular_whatsapp', 'S', ($usuario['usu_celular_whatsapp'] ?? 'N') == 'S', ['emptyLabel' => true])],
    [FC::input('Ramal', 'usu_ramal', $usuario['usu_ramal'], $adAttrs), ''],
    [FC::input('E-mail', 'usu_email', $usuario['usu_email'] ?? '', array_merge(['type' => 'email'], $adAttrs)), $campo_papeis],
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
            $('#form-usuario').validate({
                rules: {
                    usu_nome: {
                        required: true,
                        minlength: 3
                    },
                    usu_email: {
                        email: true
                    },
                },
                messages: {
                    usu_nome: {
                        required: 'Informe o nome do usuário',
                        minlength: 'O nome deve ter no mínimo 3 caracteres'
                    },
                    usu_email: {
                        email: 'Informe um e-mail válido'
                    },
                },
                invalidHandler: function(form, validator){
                    $('#overlay').remove();
                }
            });

            {$script}
        });   
        </script>
        HTML,
    ["title" => "Cadastro de usuários"]
);
