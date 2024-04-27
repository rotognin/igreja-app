<?php

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Table;
use App\SGC\DAO\Papel;
use App\SGC\DAO\UsuarioPapel;

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">' . _('Cadastro de Papéis') . '</h1>', L::linkButton("Novo papel", '?posicao=form', '', 'fas fa-plus', 'primary'));

$papelDAO = new Papel();
$aPapeis = $papelDAO->getArray([], ' pap_descricao ASC ');

$usuPapelDAO = new UsuarioPapel();

$html = L::alert('warning', 'Nenhum papel cadastrado');

if ($aPapeis) {
    $table = new Table();
    $table->setSize('sm');

    $table->addHeader([
        'cols' => [
            ['value' => 'Descrição'],
            ['value' => 'Ações', 'attrs' => ['class' => 'text-center']],
        ],
    ]);

    foreach ($aPapeis as $papel) {
        // Verificar se o papel está em algum usuário. Se estiver, desabilitar o
        // botão de excluir
        $where = array('');
        $where[0] = ' AND usupap_papel_id = ?';
        $where[1][] = $papel['pap_id'];

        $aUsuPapeis = $usuPapelDAO->getArray($where);

        $attrs = (empty($aUsuPapeis)) ? '' : ' disabled aria-disabled="true"';

        $buttons = L::buttonGroup([
            L::linkButton('', "?posicao=form&pap_id={$papel['pap_id']}", 'Editar papel', 'fas fa-edit', 'outline-secondary', 'sm'),
            L::button('', "excluirPapel({$papel['pap_id']})", _('Excluir Papel'), 'fas fa-trash', 'outline-danger', 'sm', $attrs)
        ]);
        $table->addRow([
            'cols' => [
                ['value' => $papel['pap_descricao']],
                ['value' => $buttons, 'attrs' => ['class' => 'text-center']]
            ],
        ]);
    }

    $table->setFooter(false);

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
        <script>
            function excluirPapel(pap_id){
                confirm('Deseja realmente excluir este papel?').then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '?posicao=excluir&pap_id=' + pap_id;
                    }
                });
            }
        </script>
        HTML,
    ["title" => "Cadastro de Papéis de Usuários"]
);
