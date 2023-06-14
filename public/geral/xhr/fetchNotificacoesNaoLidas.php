<?php

use Funcoes\DAO\Notificacao;

require_once('header.php');

$notificacaoDAO = new Notificacao();

$usuario = $activeUser->getID();
$response->json(200, $notificacaoDAO->getArray([" AND n.not_tipo_destino = 'usuario' AND n.not_id_destino = ? AND n.not_data_lida IS NULL", [$usuario]]));
