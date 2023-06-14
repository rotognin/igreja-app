<?php
require_once(__DIR__ . '/../vendor/autoload.php');

global $config;
$config = new \Funcoes\Lib\Config();

global $request;
$request = new \Funcoes\Lib\Request();

global $response;
$response = new \Funcoes\Lib\Response();

global $session;
$session = new \Funcoes\Lib\Session();

global $dbManager;
$dbManager = new \Funcoes\Lib\DBManager();

global $authManager;
$authManager = new \Funcoes\Lib\AuthManager();

function debug($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
