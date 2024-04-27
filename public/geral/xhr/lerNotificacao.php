<?php

use Funcoes\DAO\Notificacao;

require_once('header.php');

$notificacaoDAO = new Notificacao();

$notificacaoDAO->update($request->get('not_id', 0, FILTER_SANITIZE_NUMBER_INT), ['not_data_lida' => date('Y-m-d H:i:s')]);
$response->json(200, ['status' => 'OK']);
