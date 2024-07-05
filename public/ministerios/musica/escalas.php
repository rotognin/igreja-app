<?php

require_once("header.php");

$classe = ucfirst($request->get('posicao', 'lista'));

$arquivo = 'cadEscalas/' . $classe . '.php';

if (!file_exists($arquivo)) {
    $session->flash('error', 'Arquivo não encontrado: ' . $arquivo);
    return $response->back();
}

require_once($arquivo);

$classe = 'View\\Ministerios\\Musica\\Escala\\' . $classe;

$obj = new $classe();
$obj->executar();
