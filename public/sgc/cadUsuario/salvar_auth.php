<?php

use App\SGC\DAO\Usuario;
use App\SGC\Usuario as SGCUsuario;
use Funcoes\Lib\ADProvider;

$usuarioDAO = new Usuario();

$edit = $request->post('alterar', 'N') == 'S';

$usu_login = $request->post('usu_login');

if (!$edit) {
    $usuario = $usuarioDAO->get($usu_login);
    if (!empty($usuario)) {
        $session->set('previous', $request->postArray());
        $session->flash('error', 'Usuário já cadastrado');
        return $response->back();
    }

    if ($request->post('usu_provedor_auth', 'interno') == 'interno') {
        $registro = [
            'usu_login' => $usu_login,
            'usu_senha' => SGCUsuario::hashPassword($request->post('usu_senha')),
            'usu_ativo' => $request->post('usu_ativo', 'N'),
            'usu_nome'  => $usu_login,
            'usu_provedor_auth' => 'interno',
        ];
    } else {
        $provider = new ADProvider();
        $userProfile = $provider->getUserProfile($usu_login);
        if ($userProfile === false) {
            $session->set('previous', $request->postArray());
            $session->flash('error', 'Falha ao recuperar o usuário no AD');
            return $response->back();
        } else if (empty($userProfile)) {
            $session->set('previous', $request->postArray());
            $session->flash('error', 'Usuário não encontrado no AD');
            return $response->back();
        }

        $registro = [
            'usu_provedor_auth' => 'ad',
            'usu_login' => $usu_login,
            'usu_nome' => $userProfile['displayname'],
            'usu_email' => $userProfile['mail'],
            'usu_ramal' => $userProfile['telephonenumber'],
            'usu_ativo' => $request->post('usu_ativo', 'N'),
            // ($userProfile['useraccountcontrol'] & 2) == 0 && ($userProfile['useraccountcontrol'] & 512) == 512 ? 'S' : 'N',
        ];
    }

    $usuarioDAO->insert($registro);
    $session->flash('success', 'Usuário registrado com sucesso.');
    $response->replace("?posicao=form&usu_login=$usu_login");
} else {
    $usuario = $usuarioDAO->get($request->post('usu_login'));
    if (empty($usuario)) {
        $session->flash('error', 'Usuário não encontrado');
        return $response->back();
    }

    if ($request->post('usu_provedor_auth', 'interno') == 'interno') {
        $dados = [
            'usu_ativo' => $request->post('usu_ativo', 'N'),
            'usu_provedor_auth' => 'interno',
        ];
        if ($request->post('alterar_senha') == 'S') {
            $dados['usu_senha'] = SGCUsuario::hashPassword($request->post('usu_senha'));
        }
    } else {
        $provider = new ADProvider();
        $userProfile = $provider->getUserProfile($request->post('usu_login'));
        if ($userProfile === false) {
            $session->set('previous', $request->postArray());
            $session->flash('error', 'Falha ao recuperar o usuário no AD');
            return $response->back();
        } else if (empty($userProfile)) {
            $session->set('previous', $request->postArray());
            $session->flash('error', 'Usuário não encontrado no AD');
            return $response->back();
        }

        $dados = [
            'usu_provedor_auth' => 'ad',
            'usu_nome' => $userProfile['displayname'],
            'usu_email' => $userProfile['mail'],
            'usu_ramal' => $userProfile['telephonenumber'],
            'usu_ativo' => $request->post('usu_ativo', 'N'),
        ];
    }

    $usuarioDAO->update($request->post('usu_login'), $dados);
    $session->flash('success', 'Usuário alterado com sucesso');
    $response->back();
}
