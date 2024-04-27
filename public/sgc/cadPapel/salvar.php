<?php

use App\SGC\DAO\Papel;

$papelDAO = new Papel();
$usuario = $session->get('credentials.default');

$edit = $request->post('alterar', 'N') == 'S';

if (!$edit) {
    $papel = $papelDAO->get($request->post('pap_id'));
    if (!empty($papel)) {
        $session->set('previous', $request->postArray());
        $session->flash('error', 'Papel já cadastrado');
        return $response->back();
    }

    $papelDAO->insert([
        'pap_descricao' => $request->post('pap_descricao'),
        'pap_data_inc'  => date('Y-m-d H:i:s'),
        'pap_usu_inc'   => $usuario
    ]);

    $session->flash('success', 'Papel cadastrado com sucesso');
} else {
    $papel = $papelDAO->get($request->post('pap_id'));
    if (empty($papel)) {
        $session->flash('error', 'Papel não encontrado');
        return $response->back();
    }

    $papelDAO->update($request->post('pap_id'), [
        'pap_descricao' => $request->post('pap_descricao'),
        'pap_data_alt'  => date('Y-m-d H:i:s'),
        'pap_usu_alt'   => $usuario
    ]);

    $session->flash('success', 'Papel atualizado com sucesso');
}
$response->back(-2);
