<?php
require_once('header.php');

$response->page(
    <<<HTML
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Bem vindo ao SGC, {$activeUser->data['usu_nome']}</h1>
                </div>
            </div>
        </div>
    </div>
    HTML,
    ['title' => 'SGC']
);
