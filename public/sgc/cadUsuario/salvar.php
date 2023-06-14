<?php

use App\SGC\DAO\Usuario;
use App\SGC\DAO\UsuarioPapel;

$usuarioDAO = new Usuario();

$usuario = $usuarioDAO->get($request->post('usu_login'));
if (empty($usuario)) {
    $session->flash('error', 'Usuário não encontrado');
    return $response->back();
}
$dados = [
    'usu_nome' => $request->post('usu_nome'),
    'usu_email' => $request->post('usu_email'),
    'usu_celular' => $request->post('usu_celular'),
    'usu_ramal' => $request->post('usu_ramal', null),
    'usu_celular_whatsapp' => $request->post('usu_celular_whatsapp', 'N'),
];
$usuarioDAO->update($request->post('usu_login'), $dados);
$session->flash('success', 'Usuário alterado com sucesso');

// Atualizar os Papéis do usuário
$usuPapelDAO = new UsuarioPapel();

// Excluir todos os papéis ligados ao usuário
$where = array('');
$where[0] = ' AND usupap_usu_login = ?';
$where[1][] = $request->post('usu_login');

$aUsuPapel = $usuPapelDAO->getArray($where);

if (!empty($aUsuPapel)) {
    foreach ($aUsuPapel as $usuPapel) {
        $usuPapelDAO->update($usuPapel['usupap_id'], [
            'usupap_data_exc' => date('Y-m-d H:i:s'),
            'usupap_usu_exc'  => $request->post('usu_login')
        ]);
    }
}

$arrayPapeis = $request->post('usu_papel');

// Adicionar os papéis aos usuários
if (!empty($arrayPapeis)) {
    foreach ($arrayPapeis as $papel) {
        $codigo = htmlspecialchars(filter_var($papel, FILTER_VALIDATE_INT));

        if ($papel > 0) {
            $usuPapelDAO->insert([
                'usupap_usu_login' => $request->post('usu_login'),
                'usupap_papel_id'  => $codigo,
                'usupap_data_inc'  => date('Y-m-d H:i:s'),
                'usupap_usu_inc'   => $request->post('usu_login')
            ]);
        }
    }
}

$response->back();
