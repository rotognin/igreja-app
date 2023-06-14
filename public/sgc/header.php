<?php

use App\SGC\DAO\LogPrograma;
use App\SGC\DAO\Menu;

require_once(__DIR__ . '/../../app/bootstrap.php');

global $config;
$config = new App\SGC\Config();

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

$menu = Menu::loadFromRequest($request);
if (!$menu) {
    $session->flash('warning', 'Programa inativo');
    $response->back();
}
$response->checkAction($menu['aca_acao'], 'Você não tem permissão para acessar esta página');
LogPrograma::log($menu, $activeUser);
