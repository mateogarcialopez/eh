<?php

require 'libTipificacion.php';
ini_set('memory_limit', '-1');
$api = new ChatbotApi();

$fechainicio = '';
$fechafin = '';
$fechainicio2 = '';
$fechafin2 = '';
$ano = '';
$mes = '';
$ano2 = '';
if (isset($_POST['fechaInicio'])) {

    $fechainicio = $_POST['fechaInicio'];
}
if (isset($_POST['fechaFin'])) {

    $fechafin = $_POST['fechaFin'];
}
if (isset($_POST['fechaInicio2'])) {

    $fechainicio2 = $_POST['fechaInicio2'];
}
if (isset($_POST['fechaFin2'])) {

    $fechafin2 = $_POST['fechaFin2'];
}
if (isset($_POST['ano'])) {

    $ano = $_POST['ano'];
}
if (isset($_POST['mes'])) {

    $mes = $_POST['mes'];
}
if (isset($_POST['ano2'])) {

    $ano2 = $_POST['ano2'];
}

$respuestaJSON = array();
if ($fechainicio != '' && $fechafin != '') {

    $respuestaJSON['res_tipificacion']['consultas_lucy_dia'] = $api->getConsultasLucyDia($fechainicio, $fechafin);

    $respuestaJSON['res_tipificacion']['mensajes_difusion_dia'] = $api->getMensajesDifusionDia($fechainicio, $fechafin);

    $respuestaJSON['res_tipificacion']['llamadas_dia'] = $api->getTipificacionDia($fechainicio, $fechafin);

} else if ($fechainicio2 != '' && $fechafin2 != '') {

    $respuestaJSON['res_tipificacion']['consultas_lucy_hora'] = $api->getConsultasLucyHora($fechainicio2, $fechafin2);

    $respuestaJSON['res_tipificacion']['mensajes_difusion_hora'] = $api->getMensajesDifusionHora($fechainicio2, $fechafin2);

    $respuestaJSON['res_tipificacion']['llamadas_hora'] = $api->getTipificacionHora($fechainicio2, $fechafin2);

} else if ($ano) {

    $respuestaJSON['res_tipificacion']['consultas_lucy_ano'] = $api->getConsultasLucyAno($ano);

    $respuestaJSON['res_tipificacion']['mensajes_difusion_ano'] = $api->getMensajesDifusionAno($ano);

    $respuestaJSON['res_tipificacion']['llamadas_ano'] = $api->getTipificacionAno($ano);

} else if ($mes != '' && $ano2 != ''){

    $respuestaJSON['res_tipificacion']['consultas_lucy_mesAno'] = $api->get_ConsultasLucyDifllamMesAno($ano2, $mes, '$FECHA_RESULTADO', 'log_resultados_usuarios');

    $respuestaJSON['res_tipificacion']['mensajes_difusion_mesAno'] = $api->get_ConsultasLucyDifllamMesAno($ano2, $mes, '$FECHA_ENVIO_APERTURA', 'log_difusion_niu');

    $respuestaJSON['res_tipificacion']['llamadas_mesAno'] = $api->get_ConsultasLucyDifllamMesAno($ano2, $mes, '$Fecha', 'tipificacion');

}

echo json_encode($respuestaJSON);
