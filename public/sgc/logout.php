<?php
require_once(__DIR__ . '/../../app/bootstrap.php');

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

$activeUser->logout();
$response->redirect('/sgc/login.php');
