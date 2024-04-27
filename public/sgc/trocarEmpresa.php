<?php

use App\SGC\DAO\Empresa;
use App\SGC\DAO\Usuario;

require_once('header.php');

$usuarioDAO = new Usuario();
$empresaDAO = new Empresa();

$empresas = $usuarioDAO->getEmpresas($activeUser->data['usu_login']);
if (!in_array($request->get('emp_codigo'), array_column($empresas, 'emp_codigo'))) {
    $session->flash('error', 'Você não tem direito de visualizar dados desta empresa');
}

$empresa = $empresaDAO->get($request->get('emp_codigo'));
if (!$empresa) {
    $session->flash('error', 'Empresa inválida');
}

$session->set('establishment', $empresa);

$response->back();
