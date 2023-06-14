<?php

use App\SGC\DAO\Papel;

$papelDAO = new Papel();

$pap_id  = $request->get('pap_id');
$usuario = $session->get('credentials.default');

$aPapeis = $papelDAO->get($pap_id);
if (empty($aPapeis)) {
    $session->flash('error', _('Papel não encontrado'));
    return $response->back();
}

$papelDAO->update($pap_id, [
    'pap_data_exc'  => date('Y-m-d H:i:s'),
    'pap_usu_exc'   => $usuario
]);
$session->flash('success', _('Papel de Usuário excluido'));

$response->back();
