<?php

use App\SGC\DAO\Acao;

require_once("header.php");

$posicao = $request->get('posicao', 'lista');

$acaoDAO = new Acao();

$edit = $request->post('alterar', 'N') == 'S';
if (!$edit) {
    $acao = $acaoDAO->get($request->post('aca_acao'));
    if (!empty($acao)) {
        $session->set('previous', $request->postArray());
        $session->flash('error', 'Ação de usuário já cadastrada');
        return $response->back();
    }
    $acaoDAO->insert([
        'aca_acao' => $request->post('aca_acao'),
        'aca_descricao' => $request->post('aca_descricao'),
        'aca_grupo' => $request->post('aca_grupo'),
    ]);

    $session->flash('success', 'Ação de usuário cadastrada com sucesso');
} else {
    $acao = $acaoDAO->get($request->post('aca_acao'));
    if (empty($acao)) {
        $session->flash('error', 'Ação de usuário não encontrada');
        return $response->back();
    }
    $acaoDAO->update($request->post('aca_acao'), [
        'aca_descricao' => $request->post('aca_descricao'),
        'aca_grupo' => $request->post('aca_grupo'),
    ]);
    $session->flash('success', 'Ação de usuário atualizada com sucesso');
}
$response->back(-2);
