<?php

require_once("header.php");

$classe = ucfirst($request->get('posicao', 'lista'));

$arquivo = 'cadEventos/' . $classe . '.php';

if (!file_exists($arquivo)) {
    $session->flash('error', _('Arquivo não encontrado: ' . $arquivo));
    return $response->back();
}

require_once($arquivo);

$classe = 'View\\Administracao\\' . $classe;

$obj = new $classe();
$obj->executar();
