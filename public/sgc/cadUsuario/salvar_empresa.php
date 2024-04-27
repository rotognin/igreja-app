<?php

use App\SGC\DAO\Empresa;
use App\SGC\DAO\Usuario;

$usuarioDAO = new Usuario();
$empresaDAO = new Empresa();

$usuario = $usuarioDAO->get($request->post('usu_login'));
if (empty($usuario)) {
    $session->flash('error', 'Usuário não encontrado');
    return $response->back();
}

$empresa = $empresaDAO->get($request->post('emp_codigo'));
if (empty($empresa)) {
    $session->flash('error', 'Empresa não encontrada');
    return $response->back();
}

if ($request->post('emp_padrao') == 'S') {
    $usuarioDAO->limparEmpresaPadrao($request->post('usu_login'));
}

$usuarioDAO->adicionarEmpresa($request->post('usu_login'), [
    'emp_codigo' => $request->post('emp_codigo'),
    'emp_padrao' => $request->post('emp_padrao', 'N'),
]);
$session->flash('success', 'Empresa associada com sucesso');
$response->back();
