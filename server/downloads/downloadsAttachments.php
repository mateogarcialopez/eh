<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'libAttachments.php';

$api = new ApiAttachments();

$month = $_POST['month'];
$year = $_POST['year'];
$type = $_POST['type'];
$respuesta = '';

if ($type == 'NiuSenderId') {
    $respuesta = $api->getNiuSenderId($month, $year);
} else if ($type == 'calnegativas') {
    $respuesta = $api->getcalnegativas($month, $year);
} else if ($type == 'usuariosSegmentos') {
    $respuesta = $api->getUsuariosSegmentos($month, $year);
} else if ($type == 'segmentoDifusion') {
    $respuesta = $api->getSegmentoDifusion($month, $year);
} else if ($type == 'AcuseReciboDifusion') {
    $respuesta = $api->getAcuseReciboDifusion($month, $year);
} else if ($type == 'switches_inexistentes') {
    $respuesta = $api->getSwitchesInexistentes($month, $year);
} else if ($type == 'consultas_usuarios') {
    $respuesta = $api->getConsultasUsuarios($month, $year);
}

echo json_encode($respuesta);
