<?php

return [
    'app' => [
        'title' => 'Igreja-APP',
        'language' => 'pt_BR',
        'timezone' => 'America/Sao_Paulo'
    ],
    'db' => [
        'default' => [
            'dsn' => 'mysql:host=127.0.0.1;port=3306;charset=utf8mb4',
            'username' => 'root',
            'password' => '',

            'options' => [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ],
            'db_suffix' => '_dev',
        ],
    ],
    'folders' => [
        'upload' => '/upload',
        'temp' => '/temp'
    ]
];
