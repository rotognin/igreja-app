<?php

use App\Geral\Datatables\DatatableNotificacoes;
use Funcoes\Layout\Datatable;
use Funcoes\Layout\Layout as L;

require('header.php');

$pageHeader = L::pageTitle('<h1 class="m-0 text-dark">Notificações</h1>');

$table = new Datatable(DatatableNotificacoes::class);

$html = $table->html();
$response->page(
    <<<HTML
        {$pageHeader}
        <div class="content">
            <div class="container-fluid pb-1">
                $html
            </div>
        </div>
        <script>
            function ler(not_id, btn) {
                $.get(`/geral/xhr/lerNotificacao.php?not_id=\${not_id}`);
                $(btn).parents('tr').find('.font-weight-bold').removeClass('font-weight-bold');
                $(btn).remove();
            }
        </script>
    HTML,
    ["title" => "Notificações"]
);
