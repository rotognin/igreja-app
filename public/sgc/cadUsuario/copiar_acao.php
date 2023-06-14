<?php

use App\SGC\DAO\Usuario;

$usuarioDAO = new Usuario();

$usuarioDAO->copiarAcoes($request->post('usu_login_copia'), $request->post('usu_login'));
$session->flash('success', 'Ações de usuário copiadas com sucesso');
$response->back();
