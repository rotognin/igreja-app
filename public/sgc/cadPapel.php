<?php

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$arquivo = 'cadPapel/' . $posicao . '.php';

if (!file_exists($arquivo)) {
    $session->flash('error', _('Arquivo não encontrado: ' . $arquivo));
    return $response->back();
}

require_once($arquivo);
