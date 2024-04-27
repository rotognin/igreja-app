<?php

use App\SGC\DAO\Usuario;
use App\SGC\DAO\Papel;
use App\SGC\DAO\UsuarioPapel;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;

require_once("header.php");

$usuarioDAO = new Usuario();
$papelDAO = new Papel();

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Consulta de Usuários e Papéis</h1>');

$aUsuarios = $usuarioDAO->getArray();
$aPapeis   = $papelDAO->getArray();

$usuarios = [];
$papeis   = [];

foreach ($aUsuarios as $usuario) {
    $usuarios[$usuario['usu_login']] = $usuario['usu_nome'];
}

foreach ($aPapeis as $papel) {
    $papeis[$papel['pap_id']] = $papel['pap_descricao'];
}

$form = new Form();
$form->setTitle('<i class="fas fa-filter"></i> Selecione a forma de busca');
//$form->setForm('action="" method="GET"');
$form->setCollapsable(true);
$form->setCollapsed(false);

$selectUsuarios = FC::select2('Usuário', 'usu_login', ['' => ''] + $usuarios, $request->get('usu_login', ''), [
    'data' => [
        'placeholder' => 'Selecione um usuário',
        'allow-clear' => true,
    ],
]);

$selectPapeis = FC::select2('Papel', 'pap_id', ['' => ''] + $papeis, $request->get('pap_id', ''), [
    'data' => [
        'placeholder' => 'Selecione um papel',
        'allow-clear' => true,
    ],
]);

$botaoUsuarios = L::button('Buscar', 'buscarPorUsuario()', 'Buscar por Usuário', 'fas fa-users', 'default', '', '', 'mt-2');
$botaoPapeis = L::button('Buscar', 'buscarPorPapel()', 'Busca por papéis', 'fas fa-tools', 'default', '', '', 'mt-2');

$form->setFields([
    [$selectUsuarios, '<br>' . $botaoUsuarios],
    [$selectPapeis, '<br>' . $botaoPapeis]
]);

$html = '';

if ($request->get('filtrar', '') == 'S') {
    $nome = '';
    $alvo = $request->get('alvo');

    if ($alvo == 'usuario') {
        $aUsuario = $usuarioDAO->get($request->get('usu_login'));
        $nome = $aUsuario['usu_nome'];
    } else {
        $aPapel = $papelDAO->get($request->get('pap_id'));
        $nome = $aPapel['pap_descricao'];
    }

    $coluna = ($alvo == 'usuario') ? 'Papéis do usuário ' . $nome : 'Usuários com o papel ' . $nome;

    $table = new Table();
    $table->addHeader([
        'cols' => [
            ['value' => $coluna],
        ]
    ]);

    $usuPapelDAO = new UsuarioPapel();

    $where = array('');

    if ($request->get('usu_login', '') != '') {
        $where[0] .= ' AND x.usupap_usu_login = ?';
        $where[1][] = $request->get('usu_login');
    }

    if ($request->get('pap_id', '') != '') {
        $where[0] .= ' AND x.usupap_papel_id = ?';
        $where[1][] = $request->get('pap_id');
    }

    $registros = $usuPapelDAO->getArrayCompleta($where);

    $html = L::alert('warning', _('Nenhum registro encontrado'));

    if (!empty($registros)) {
        foreach ($registros as $registro) {
            $valor = ($alvo == 'usuario') ? $registro['pap_descricao'] : $registro['usu_nome'];

            $table->addRow([
                'cols' => [['value' => $valor]]
            ]);
        }
    }

    $table->setFooter(false);
    $table->setStriped(false);
    $html = $table->html();
}

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
            function buscarPorUsuario(){
                const usuario = $('#usu_login').val();

                if (usuario == ''){
                    mensagem('Por favor, selecione um usuário').then(() => { return false; });
                } else {
                    displayOverlay('Aguarde...');
                    window.location.assign("?usu_login=" + usuario + "&alvo=usuario&filtrar=S");
                }
            }

            function buscarPorPapel(){
                const papel = $('#pap_id').val();

                if (papel == ''){
                    mensagem('Por favor, selecione um papel').then(() => { return false; });
                } else {
                    displayOverlay('Aguarde...');
                    window.location.assign("?pap_id=" + papel + "&alvo=papel&filtrar=S");
                }
            }
        </script>
        HTML,
    ["title" => "Usuários e Papéis"]
);
