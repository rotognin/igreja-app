<?php

/**
 * Entrada de requisições via Ajax
 */

require_once(__DIR__ . '/../app/bootstrap.php');

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

$arquivo = __DIR__ . '/';

$indice = 0;
$terminou = false;

while (!$terminou){
    $indice++;

    if ($request->get('c' . (string)$indice, '') != ''){
        $arquivo .= $request->get('c' . (string)$indice) . '/';
    } else {
        $terminou = true;
    }
}

$arquivo .= $request->get('arquivo', 'consulta') . '.php';

require_once($arquivo);