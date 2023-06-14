<?php

require_once(__DIR__ . '/../../app/bootstrap.php');

global $config;
$config = new App\Geral\Config();

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');
