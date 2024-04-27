<?php

use Funcoes\Layout\Layout as L;
use App\SGC\DAO\Empresa;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Table;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$empresaDAO = new Empresa();

if ($posicao == 'lista') {
    $pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de empresas</h1>', L::linkButton("Nova empresa", '?posicao=form', '', 'fas fa-plus', 'primary'));

    $empresas = $empresaDAO->getArray();

    $html = L::alert('warning', 'Nenhuma empresa encontrada');

    if ($empresas) {
        $table = new Table();
        $table->setSize('sm');
        $table->addHeader([
            'cols' => [
                ['value' => 'Código', 'attrs' => ['class' => 'text-center']],
                ['value' => 'Nome'],
                ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
            ],
        ]);

        foreach ($empresas as $e) {
            $buttons = L::buttonGroup([
                L::linkButton('', "?posicao=form&emp_codigo={$e['emp_codigo']}", 'Editar empresa', 'fas fa-edit', 'outline-secondary', 'sm'),
            ]);
            $table->addRow([
                'cols' => [
                    ['value' => $e['emp_codigo'], 'attrs' => ['class' => 'text-center']],
                    ['value' => $e['emp_nome'], 'attrs' => ['class' => 'text-nowrap']],
                    ['value' => $buttons, 'attrs' => ['class' => 'text-center']]
                ],
            ]);
        }

        $html = $table->html();
    }

    $response->page(
        <<<HTML
        $pageHeader
        <div class="content">
            <div class="container-fluid pb-1">
                $html
            </div>
        </div>
        HTML,
        ["title" => "Cadastro de empresas"]
    );
} elseif ($posicao == 'form') {
    $emp_codigo = $request->get('emp_codigo', '');
    $empresa = [];
    if (!empty($emp_codigo)) {
        $empresa = $empresaDAO->get($emp_codigo);
        if (empty($empresa)) {
            $session->flash('error', 'Empresa não encontrada');
            return $response->back();
        }
    }
    $empresa = $session->get('previous', $empresa);
    $pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Cadastro de empresas</h1>', L::backButton());

    $form = new Form();
    $form->setTitle($emp_codigo ? "Editar empresa: $emp_codigo - {$empresa['emp_nome']}" : 'Nova empresa');
    $form->setForm('id="form-empresa" action="?posicao=salvar" method="post"');
    if (!empty($emp_codigo)) {
        $form->addHidden(FC::hidden('emp_codigo', $emp_codigo));
        $form->addHidden(FC::hidden('alterar', 'S'));
    }

    $empCodigoAttrs = [];
    if (!empty($emp_codigo)) {
        $empCodigoAttrs['disabled'] = 'disabled';
    }

    $form->setFields([
        [FC::input('Código', 'emp_codigo', $empresa['emp_codigo'] ?? '', $empCodigoAttrs)],
        [FC::input('Nome', 'emp_nome', $empresa['emp_nome'] ?? '')],
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
            $('#form-empresa').validate({
                rules: {
                    emp_codigo: {
                        required: true,
                    },
                    emp_nome: {
                        required: true
                    },
                },
                messages: {
                    emp_codigo: {
                        required: 'Informe o código da empresa',
                    },
                    emp_nome: {
                        required: 'Informe o nome da empresa',
                    },
                },
                invalidHandler: function(form, validator){
                    $('#overlay').remove();
                }
            });
        });   
        </script>
        HTML,
        ["title" => "Cadastro de empresas"]
    );
} elseif ($posicao == 'salvar') {
    $edit = $request->post('alterar', 'N') == 'S';
    if (!$edit) {
        $empresa = $empresaDAO->get($request->post('emp_codigo'));
        if (!empty($empresa)) {
            $session->set('previous', $request->postArray());
            $session->flash('error', 'Empresa já cadastrada');
            return $response->back();
        }
        $empresaDAO->insert([
            'emp_codigo' => $request->post('emp_codigo'),
            'emp_nome' => $request->post('emp_nome'),
        ]);

        $session->flash('success', 'Empresa cadastrada com sucesso');
    } else {
        $empresa = $empresaDAO->get($request->post('emp_codigo'));
        if (empty($empresa)) {
            $session->flash('error', 'Empresa não encontrada');
            return $response->back();
        }
        $empresaDAO->update($request->post('emp_codigo'), [
            'emp_nome' => $request->post('emp_nome'),
        ]);
        $session->flash('success', 'Empresa atualizada com sucesso');
    }
    $response->back(-2);
}
