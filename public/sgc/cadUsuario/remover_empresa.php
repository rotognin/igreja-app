<?php

use App\SGC\DAO\Usuario;

$usuarioDAO = new Usuario();

$usuarioDAO->removerEmpresa($request->get('usu_login'), $request->get('emp_codigo'));
$session->flash('success', 'Empresa removida com sucesso');
$response->back();
