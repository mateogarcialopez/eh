<?php

require 'lib.php';

$api = new ChatbotApi();
$usr = $_POST['user'];
$pwd = $_POST['pwd'];

if (isset($usr, $pwd)) {
    $bdResponse = $api->login($usr, $pwd);
    if ($bdResponse) {
        $json['idusuario'] = $bdResponse->IDUSUARIO;
        $json['nombre'] = $bdResponse->NOMBRE;
        $json['tipo_usuario'] = $bdResponse->TIPO_USUARIO;
        $api->setLogin($json['idusuario']);
        session_start();
        $_SESSION['idusuario'] = $json['idusuario'];
    }else {
        $json = $bdResponse;
    }
}

echo json_encode($json);
