<?php

require_once(__DIR__ . '/../../app/bootstrap.php');

$term = $request->get('term', '');

if (empty($term)) {
    echo json_encode(array());
    exit;
}

$resultado = array(
    'results' => [
        array(
            'id' => 1,
            'text' => 'Rodrigo'
        ),
        array(
            'id' => 2,
            'text' => 'Tognin'
        )
    ]
);

echo json_encode($resultado);
