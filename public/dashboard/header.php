<?php

require_once(__DIR__ . '/../../app/bootstrap.php');

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

global $config;
$config  = new App\Dashboard\Config();
