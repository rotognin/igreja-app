<?php

use App\SGC\DAO\Usuario;

$usuarioDAO = new Usuario();

$usuarioDAO->removerAcao($request->get('usu_login'), $request->get('aca_acao'));
$session->flash('success', 'Ação de usuário removida com sucesso');
$response->back();
