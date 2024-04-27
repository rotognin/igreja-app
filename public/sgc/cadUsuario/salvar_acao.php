<?php

use App\SGC\DAO\Acao;
use App\SGC\DAO\Usuario;

$usuarioDAO = new Usuario();
$acaoDAO = new Acao();

$usuario = $usuarioDAO->get($request->post('usu_login'));
if (empty($usuario)) {
    $session->flash('error', 'Usuário não encontrado');
    return $response->back();
}

$acao = $acaoDAO->get($request->post('aca_acao'));
if (empty($acao)) {
    $session->flash('error', 'Ação de usuário não encontrada');
    return $response->back();
}

$usuarioDAO->adicionarAcao($request->post('usu_login'), $request->post('aca_acao'));
$session->flash('success', 'Ação de usuário associada com sucesso');
$response->back();
