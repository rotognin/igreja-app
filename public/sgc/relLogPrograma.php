<?php

use App\SGC\DAO\LogPrograma;
use App\SGC\DAO\Menu;
use App\SGC\DAO\Usuario;
use App\SGC\Datatables\DatatableSGC01;
use Funcoes\Layout\Form;
use Funcoes\Layout\Layout as L;
use Funcoes\Layout\FormControls as FC;
use Funcoes\Layout\Datatable;

require_once("header.php");

$posicao = $request->get('posicao', 'entrada');

$logDAO = new LogPrograma();
$usuarioDAO = new Usuario();
$menuDAO = new Menu();

if ($posicao == 'entrada' || $posicao == 'relatorio') {
    $pageHeader = L::pageTitle('<h1 class="m-0 text-dark">SGC01 - Acesso à programas</h1>');

    $programas = ['' => 'Todos os programas'];
    foreach ($menuDAO->getArray() as $prg) {
        $programas[$prg['prg_codigo']] = str_repeat('&nbsp;', $prg['prg_nivel'] * 8) . $prg['prg_descricao'];
    }

    $usuarios = ['' => 'Todos os usuários'];
    foreach ($usuarioDAO->getArray() as $usuario) {
        $usuarios[$usuario['usu_login']] = "{$usuario['usu_login']} - {$usuario['usu_nome']}";
    }

    $form = new Form();
    $form->setTitle('<i class="fas fa-filter"></i> Filtros');
    $form->setForm('action="" method="GET"');
    $form->setCollapsable(true);
    $form->setCollapsed(count($request->getArray()) == 0);
    $form->setActions(L::submit('Filtrar', 'fas fa-filter'));

    $inicio = date('d/m/Y 00:00', strtotime('-1 month'));
    $fim = date('d/m/Y 23:59');

    $periodo = $request->get('log_datahora');
    if (!empty($periodo)) {
        [$inicio, $fim] = explode(' - ', $periodo);
    }

    $picker = FC::dateRange('Período', 'log_datahora', $request->get('log_datahora'), [
        'data' => [
            'time-picker' => true,
            'start-date' => $inicio,
            'end-date' => $fim,
        ],
    ]);

    $selectUsuarios = FC::select2('Usuário', 'usu_login', $usuarios, $request->get('usu_login'), [
        'data' => [
            'placeholder' => 'Todos',
            'allow-clear' => true,
        ],
    ]);

    $selectProgramas = FC::select2('Programa', 'prg_codigo', $programas, $request->get('prg_codigo'), [
        'data' => [
            'placeholder' => 'Todos',
            'allow-clear' => true,
        ],
    ]);

    $form->setFields([
        [$picker, $selectUsuarios],
        [$selectProgramas, FC::input('URL do programa', 'prg_url', $request->get('prg_url'))],
        [FC::input('IP', 'log_ip', $request->get('log_ip')), FC::input('Navegador', 'log_navegador', $request->get('log_navegador'))],
    ]);

    $table = new Datatable(DatatableSGC01::class);

    $html = $table->html();

    $response->page(
        <<<HTML
        $pageHeader
        <div class="content">
            <div class="container-fluid pb-1">
                {$form->html()}
                $html
            </div>
        </div>
        HTML,
        ["title" => "SGC01 - Acesso à programas"]
    );
}
