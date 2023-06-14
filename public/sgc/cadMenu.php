<?php

use App\SGC\DAO\Acao;
use App\SGC\DAO\Menu;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$menuDAO = new Menu();

if ($posicao == 'lista') {
    $pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Gerenciamento de menu</h1>', L::linkButton("Novo menu raiz", '?posicao=form', '', 'fas fa-plus', 'primary'));
    $programas = $menuDAO->getArray();

    $html = L::alert('warning', 'Nenhum registro de menu encontrado');
    if (!empty($programas)) {
        $TL = new Table();
        $TL->setSize('sm');

        $TL->addHeader(['cols' => [
            ['value' => '#', 'attrs' => ['class' => 'text-center']],
            ['value' => 'Sequência', 'attrs' => ['class' => 'text-center']],
            ['value' => 'Descrição'],
            ['value' => 'URL'],
            ['value' => 'Ação necessária', 'attrs' => ['class' => 'text-center']],
            ['value' => 'Ativo', 'attrs' => ['class' => 'text-center']],
            ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
        ]]);

        foreach ($programas as $programa) {
            $buttons = [
                L::linkButton('', '?posicao=form&prg_codigo_pai=' . $programa['prg_codigo'], 'Adicionar submenu', 'fas fa-indent', 'outline-secondary', 'sm'),
                L::linkButton('', '?posicao=form&prg_codigo=' . $programa['prg_codigo'], 'Editar', 'fas fa-edit', 'outline-secondary', 'sm'),
                L::button('', "deletePrograma({$programa['prg_codigo']})", 'Excluir programa', 'fas fa-trash', 'outline-danger', 'sm'),
            ];

            $TL->addRow(['cols' => [
                ['value' => $programa['prg_codigo'], 'attrs' => ['class' => 'text-center']],
                ['value' => $programa['prg_sequencia'], 'attrs' => ['class' => 'text-center']],
                ['value' => str_repeat('&nbsp;', $programa['prg_nivel'] * 8) . "<i class='{$programa['prg_icone']}'></i> {$programa['prg_descricao']}", 'attrs' => ['class' => 'text-nowrap']],
                ['value' => $programa['prg_url']],
                ['value' => $programa['aca_acao'], 'attrs' => ['class' => 'text-center']],
                ['value' => $programa['prg_ativo'] ? 'Sim' : 'Não', 'attrs' => ['class' => 'text-center']],
                ['value' => L::buttonGroup($buttons), 'attrs' => ['class' => 'text-center']],
            ], 'attrs' => ['data-prg_codigo' => $programa['prg_codigo']]]);
        }

        $html = $TL->html();
    }

    $response->page(
        <<<HTML
        $pageHeader
        <div class="content pb-1">
            $html
        </div>
        <script>
            function deletePrograma(prg_codigo) {
                confirm('Deseja realmente excluir este programa?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '?posicao=delete&prg_codigo=' + prg_codigo;
                    }
                });
            }
        </script>
        HTML,
        ['title' => 'Gerenciamento de Menu']
    );
} elseif ($posicao == 'form') {
    $prg_codigo = $request->get('prg_codigo', "");
    if (!empty($prg_codigo)) {
        $programa = $menuDAO->get($prg_codigo);
    } else {
        $programa = [
            'prg_descricao' => '',
            'prg_url' => '',
            'prg_icone' => '',
            'prg_ativo' => 'S',
            'prg_sequencia' => '',
            'prg_codigo_pai' => $request->get('prg_codigo_pai', '0'),
            'aca_acao' => '',
        ];
    }

    $acoes = ['' => 'Selecione uma ação'];
    $acaoDAO = new Acao();
    foreach ($acaoDAO->getArray() as $acao) {
        if (!isset($acoes[$acao['aca_grupo']])) {
            $acoes[$acao['aca_grupo']] = [];
        }
        $acoes[$acao['aca_grupo']][$acao['aca_acao']] = "{$acao['aca_acao']} - {$acao['aca_descricao']}";
    }

    $programas = ['0' => 'Menu raiz'];
    $where = [];
    if (!empty($prg_codigo)) {
        $where = [' AND NOT (prg_lft >= ? AND prg_rgt <= ?)', [$programa['prg_lft'], $programa['prg_rgt']]];
    }
    foreach ($menuDAO->getArray($where) as $prg) {
        $programas[$prg['prg_codigo']] = str_repeat('&nbsp;', $prg['prg_nivel'] * 8) . $prg['prg_descricao'];
    }

    $pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de menu</h1>', L::backButton());

    $form = new Form();

    if (empty($prg_codigo)) {
        $form->setTitle('Novo menu');
    } else {
        $form->setTitle('Editar menu');
        $form->addHidden(FC::hidden('prg_codigo', $prg_codigo));
    }

    $form->setForm('method="POST" id="form-menu" action="?posicao=salvar"');
    $form->setFields([
        [FC::select('Menu pai', 'prg_codigo_pai', $programas, $programa['prg_codigo_pai'])],
        [FC::input('Descrição', 'prg_descricao', $programa['prg_descricao'])],
        [FC::input('URL', 'prg_url', $programa['prg_url'])],
        [FC::select2('Ação necessária p/ acesso', 'aca_acao', $acoes, $programa['aca_acao'], ['data' => ['placeholder' => 'Selecione uma ação', 'allow-clear' => true]])],
        [FC::input('Sequencia', 'prg_sequencia', $programa['prg_sequencia']), FC::input('Ícone <i id="preview-icone"></i>', 'prg_icone', $programa['prg_icone'])],
        [FC::switch('Programa ativo', 'prg_ativo', 'S', $programa['prg_ativo'] == 'S')],
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
                $('#form-menu').validate({ 
                    rules: {
                        prg_descricao: { required: true },
                        prg_sequencia: { required: true, digits: true },
                    },
                    messages: {
                        prg_descricao: { required: 'Informe a descrição do programa' },
                        prg_sequencia: { required: 'Informe a sequência do programa', digits: 'Apenas números' },
                    },
                    invalidHandler: function(form, validator){
                        $('#overlay').remove();
                    }
                });

                $('#prg_icone').keyup(function() {
                    $('#preview-icone').attr('class', $('#prg_icone').val());
                });
                $('#preview-icone').attr('class', $('#prg_icone').val());
            });
        </script>
        HTML,
        ['title' => 'Gerenciamento de Menu']
    );
} elseif ($posicao == 'salvar') {

    $prg_codigo_pai = $request->post('prg_codigo_pai', '0');
    $prg_codigo = $request->post('prg_codigo');
    $prg_descricao = $request->post('prg_descricao');
    $prg_url = $request->post('prg_url');
    $prg_sequencia = $request->post('prg_sequencia', '0');
    $prg_icone = $request->post('prg_icone');
    $prg_ativo = $request->post('prg_ativo', 'N');
    $aca_acao = $request->post('aca_acao');

    if (empty($prg_codigo)) {
        $menuDAO->insert([
            'prg_codigo_pai' => $prg_codigo_pai,
            'prg_descricao' => $prg_descricao,
            'prg_url' => $prg_url,
            'prg_sequencia' => $prg_sequencia,
            'prg_icone' => $prg_icone,
            'prg_ativo' => $prg_ativo,
            'prg_lft' => 0,
            'prg_rgt' => 0,
            'prg_nivel' => 0,
            'aca_acao' => $aca_acao,
        ]);
    } else {
        $menuDAO->update($prg_codigo, [
            'prg_codigo_pai' => $prg_codigo_pai,
            'prg_descricao' => $prg_descricao,
            'prg_url' => $prg_url,
            'prg_sequencia' => $prg_sequencia,
            'prg_icone' => $prg_icone,
            'prg_ativo' => $prg_ativo,
            'aca_acao' => $aca_acao,
        ]);
    }

    $menuDAO->recalcularArvore();

    $session->flash('success', 'Menu salvo com sucesso');

    return $response->back(-2);
} elseif ($posicao == 'delete') {

    $prg_codigo = $request->get('prg_codigo');

    if (!empty($menuDAO->getArray([' AND p.prg_codigo_pai = ?', [$prg_codigo]]))) {
        $session->flash('error', 'Não é possível excluir um menu que possui submenus');
        return $response->back();
    }

    $menuDAO->delete($prg_codigo);

    $session->flash('success', 'Menu excluído com sucesso');

    return $response->back();
}
