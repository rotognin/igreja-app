<?php

require_once('header.php');

$req = $request->getArray();
if (empty($req['definitions'])) {
    $response->json(200, error('Parâmetros inválidos', $req));
}

$defs = new $req['definitions'];
$limit  = $req['length'];
$offset = $req['start'];
$orderBy = null;
if (!empty($req['order'][0])) {
    $orderBy = $req['columns'][$req['order'][0]['column']]['name'] . ' ' . $req['order'][0]['dir'];
}
$data = $defs->getData($limit, $offset, $orderBy);

$response->json(200, [
    'draw' => $req['draw'],
    'data' => $data['data'],
    'recordsTotal' => $req['length'],
    'recordsFiltered' => $data['total'],
]);

function error($msg, $data)
{
    return [
        'draw'            => $data['draw'],
        'recordsTotal'    => $data['length'],
        'recordsFiltered' => $data['length'],
        'error'           => $msg
    ];
}
