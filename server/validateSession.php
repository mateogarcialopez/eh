<?php
require 'lib.php';
session_start();

$api = new ChatbotApi();
$idusr = $_POST['idusuario'];

if (isset($idusr)) {
    if (isset($_SESSION['idusuario']) && $_SESSION['idusuario'] == $idusr) {
        $json = $api->checkPermission($idusr)->TIPO_USUARIO;
    } else {
        session_destroy();
        $json = false;
    }
}

echo json_encode($json);
