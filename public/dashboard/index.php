<?php

use Funcoes\Layout\Layout;
use App\Dashboard\DAO\Dashboard;

require_once('header.php');

$usuario = $session->get('credentials.default');

$pageHeader = Layout::pageTitle(
    '<h1 class="m-0 text-dark">Bem vindo ao Dashboard, ' . $activeUser->data['usu_nome'] . '</h1>'
);

// Buscar últimos programas e programas mais acessados do usuário logado
$dashboardDAO = new Dashboard();

$html = $dashboardDAO->inicio();
$html .= $dashboardDAO->montarMaisAcessados($usuario);
$html .= $dashboardDAO->montarUltimosAcessos($usuario);
$html .= $dashboardDAO->fechamento();

$response->page(
    <<<HTML
    $pageHeader
    $html
    HTML,
    ['title' => 'Dashboard']
);
