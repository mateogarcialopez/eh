<?php
require 'lib.php';

session_start();

$api = new ChatbotApi();
$api->setLogout($_SESSION['idusuario']);

session_destroy();

echo json_encode(true);
