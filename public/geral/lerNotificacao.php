<?php

use Funcoes\DAO\Notificacao;

require_once('header.php');

$dao = new Notificacao();
$notificacao = $dao->get($request->get('not_id'));

if ($notificacao['not_tipo_destino'] == $activeUser->getTipoNotificavel() && $notificacao['not_id_destino'] == $activeUser->getID()) {
    $dao->update($request->get('not_id'), ['not_data_lida' => date('Y-m-d H:i:s')]);
    $response->replace($notificacao['not_url']);
}

$response->back();
