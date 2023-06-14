<?php

use App\SGC\DAO\Acao;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$acaoDAO = new Acao();

$aca_acao = $request->get('aca_acao');
$acaoDAO->delete($aca_acao);
$session->flash('success', 'Ação de usuário excluída com sucesso');
$response->back();
