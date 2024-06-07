<?php

require_once("header.php");

$classe = ucfirst($request->get('posicao', 'lista'));

$arquivo = 'cadCategorias/' . $classe . '.php';

if (!file_exists($arquivo)) {
    $session->flash('error', 'Arquivo não encontrado: ' . $arquivo);
    return $response->back();
}

require_once($arquivo);

$classe = 'View\\Patrimonio\\' . $classe;

$obj = new $classe();
$obj->executar();
