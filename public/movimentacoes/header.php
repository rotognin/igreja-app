<?php

use App\SGC\DAO\Menu;
use App\SGC\DAO\LogPrograma;

require_once(__DIR__ . '/../../app/bootstrap.php');

global $config;
$config = new App\SGC\Config();

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

$menu = Menu::loadFromRequest($request);
if (!$menu) {
    $session->flash('warning', _('Programa inativo'));
    $response->back();
}
$response->checkAction($menu['aca_acao'], _('Você não tem permissão para acessar esta página'));
LogPrograma::log($menu, $activeUser);
