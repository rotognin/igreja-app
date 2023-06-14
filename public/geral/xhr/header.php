<?php

require_once(__DIR__ . '/../../../app/bootstrap.php');

global $config;
$config = new App\Geral\Config();

global $authManager;
global $activeUser;
$activeUser = $authManager->get('default');
if (!$activeUser) {
    $response->json(401, ['status' => 'erro', 'mensagem' => 'Você precisa estar autenticado para acessar este recurso']);
}
