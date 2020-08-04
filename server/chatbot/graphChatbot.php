<?php

require 'libChatbot.php';
ini_set('memory_limit', '-1');
$api = new ChatbotApi();

$fechainicio = '';
$fechafin = '';
$fechainicio2 = '';
$fechafin2 = '';
$fechainicio3 = '';
$fechafin3 = '';
$fechainicio4 = '';
$fechafin4 = '';
$ano = '';
$rule = false;
$reglas = '';

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
if (isset($_POST['fechaInicio3'])) {

    $fechainicio3 = $_POST['fechaInicio3'];
}
if (isset($_POST['fechaFin3'])) {

    $fechafin3 = $_POST['fechaFin3'];
}
if (isset($_POST['fechaInicio4'])) {

    $fechainicio4 = $_POST['fechaInicio4'];
}
if (isset($_POST['fechaFin4'])) {

    $fechafin4 = $_POST['fechaFin4'];
}
if (isset($_POST['ano'])) {

    $ano = $_POST['ano'];
}
if (isset($_POST['reglas'])) {

    $reglas = $_POST['reglas'];
}
if (isset($_POST['rules'])) {

    $rule = $_POST['rules'];
}

$respuestaJSON = array();
if ($fechainicio != '' && $fechafin != '') {
    //kpi de cantidad registros por correo ekectronico scada
    $respuestaJSON['res_kpi']['scada'] = $api->getEventosScada($fechainicio, $fechafin);

    //kpi de cantidad eventos registrados por sacada apertura cierre
    $respuestaJSON['res_kpi']['eventosScada'] = $api->getEventosScadaAperturaCierre($fechainicio, $fechafin);

    //kpi de cantidad eventos sin cierre
    $respuestaJSON['res_kpi']['eventosSinCierre'] = $api->getEventosSinCierre($fechainicio, $fechafin);

    // KPI CANTIDAD EVENTOS VALIDADOS POR REGLAS
    $respuestaJSON['res_kpi']['reglas'] = $api->getCantidadEventosReglas($fechainicio, $fechafin, $reglas);
    $respuestaJSON['res_kpi']['reglas_switch_invalido'] = $api->getCantidadSwitchsInvalidos($fechainicio, $fechafin);

    // KPI eventos validados con indisponibilidad
    $respuestaJSON['res_kpi']['indispo'] = $api->getEventosIndisponibilidad($fechainicio, $fechafin, $reglas);

    // KPI eventos en los que se enviaron mensajes
    $respuestaJSON['res_kpi']['eventosMensajes'] = $api->getEventosMensajesEnviados($fechainicio, $fechafin, $reglas);

    // KPI susp progamas y no programadas
    $respuestaJSON['res_kpi']['eventosProgramada'] = $api->getEventosSuspProgramada($fechainicio, $fechafin, $reglas);
    $respuestaJSON['res_kpi']['eventosNoProgramado'] = $api->getEventosSusNoProgramada($fechainicio, $fechafin, $reglas);

    //consultas por mes, busqueas y criterios por mes
    $respuestaJSON['res_busqueda']['consultasCantidadUsuarios'] = $api->getDifusionCantidadUsuarios($fechainicio, $fechafin, $reglas);

    // KPI eventos validados con indisponibilidad
    $respuestaJSON['res_kpi']['normali'] = $api->getEventosNormalizacion($fechainicio, $fechafin, $reglas);

    // KPI eventos validados con su cierre indisp_circuito2
    $respuestaJSON['res_kpi']['eventosAperturas'] = $api->getEventosAperturas($fechainicio, $fechafin);

    
    //usuarios impactados
    //$respuestaJSON['res_kpi']['usuariosImpactados'] = $api->getUsuariosImpactados($fechainicio, $fechafin, $reglas);

    //$respuestaJSON['res_busqueda']['promedioHoraDia'] = $api->getDifusionPromedioHora2($fechainicio, $fechafin, $reglas);
    
    
    //------------------------------------------------------------------electrohuila----------------------
    
    // KPI cantidad de usuarios que consultaron menu general de consultas generales
    $respuestaJSON['res_kpi']['temasInteres'] = $api->getResultadoMenus($fechainicio, $fechafin)['menuConsultas'];
    // KPI cantidad de usuarios que consultaron menu general de factura
    $respuestaJSON['res_kpi']['menuFactura'] = $api->getResultadoMenus($fechainicio, $fechafin)['menuFactura'];
    // KPI cantidad de usuarios que consultaron menu general de pago en linea
    $respuestaJSON['res_kpi']['menuPagoEnLinea'] = $api->getResultadoMenus($fechainicio, $fechafin)['menuPagoEnLinea'];
    // KPI cantidad de usuarios que consultaron menu general de puntos de atencion
    $respuestaJSON['res_kpi']['menuPuntosDeAtencion'] = $api->getResultadoMenus($fechainicio, $fechafin)['menuPuntosDeAtencion'];
    // KPI cantidad de usuarios que consultaron menu general de hablar con asesor
    $respuestaJSON['res_kpi']['consultar_asesor'] = $api->getResultadoMenus($fechainicio, $fechafin)['menu_asesor'];
    // KPI cantidad de usuarios que consultaron menu general de otros
    $respuestaJSON['res_kpi']['otros'] = $api->getResultadoMenus($fechainicio, $fechafin)['otros'];
    //Para el grafico del menus principales
    $respuestaJSON['res_kpi']['interaccionMuenus'] = $api->getResultadoMenus($fechainicio, $fechafin);
    //KPI  de las calificaciones buenas
    $respuestaJSON['res_kpi']['Bueno'] = $api->getCalificaciones($fechainicio, $fechafin)['Bueno'];
    //KPI  de las calificaciones regulares
    $respuestaJSON['res_kpi']['Regular'] = $api->getCalificaciones($fechainicio, $fechafin)['Regular'];
    //KPI  de las calificaciones malas
    $respuestaJSON['res_kpi']['Malo'] = $api->getCalificaciones($fechainicio, $fechafin)['Malo'];
    //Para el KPI de la calificaciones(porcentaje)
    $respuestaJSON['res_kpi']['calificaciones']= $api->getCalificaciones($fechainicio, $fechafin);
    //consultas por dias y horas
    $respuestaJSON['res_busqueda']['promedioHoraDia'] = $api->getConsultasXdiayHora($fechainicio, $fechafin);
    //TABLA RESULTADOS sankey difusion
    $respuestaJSON['res_resultados']['sankeyDifusion'] = $api->getFlujoConversacion($fechainicio, $fechafin);
    

} else if ($fechainicio2 != '' && $fechafin2 != '') {
    
    $respuestaJSON['res_usuarios']['usuarios'] = $api->getConsultasSegmentosUbicacionMunicipio($fechainicio2, $fechafin2, $reglas);

    $respuestaJSON['res_usuarios']['mensajesUsuarios'] = $api->getDifusionCantidadUsuarios($fechainicio2, $fechafin2, $reglas);

    $respuestaJSON['res_usuarios']['acuseRecibo'] = $api->getAcuseRecibo($fechainicio2, $fechafin2);
    
    $respuestaJSON['res_usuarios']['promocionLucy'] = $api->getAcuseReciboPromocionLucy($fechainicio2, $fechafin2);
    
    $respuestaJSON['res_usuarios']['promocionProgramadas'] = $api->getAcuseReciboPromocionProgramadas($fechainicio2, $fechafin2);

  
    

} else if ($fechainicio3 != '' && $fechafin3 != '') {

    //Para tendendias
    //consultas por mes, busqueas y criterios por mes
    //$respuestaJSON['res_busqueda']['consultasTendencias'] = $api->getConsultasMes($fechainicio3, $fechafin3);

    //$respuestaJSON['res_busqueda']['promedioHora'] = $api->getDifusionPromedioHora($fechainicio3, $fechafin3);

    // para electrohuila

    //cantidad deconsultas por mes
    $respuestaJSON['res_busqueda']['consultasXmes'] = $api->getConsultasMes($fechainicio3, $fechafin3);
    
    //cantidad deconsultas por hora
    $respuestaJSON['res_busqueda']['promedioHora'] = $api->getConsultasPromedioHora($fechainicio3, $fechafin3);

    $respuestaJSON['res_usuarios']['ConsultasFaq'] = $api->getConsultasFaq($fechainicio3, $fechafin3);
}

//funcion para hacer el llamado a la base de datos y obtener la cantidad de registros por cada mes
function indispoMeses($api, $tipoConsulta, $inicio, $fin)
{

    $valorRespuesta = 0;
    if ($tipoConsulta == 'Progra') {
        $mesProgramadas = $api->getsusProgramadas2018($inicio, $fin);
        if (count($mesProgramadas) > 0) {
            $valorRespuesta = $mesProgramadas[0]->{'count'};
        } else {
            $valorRespuesta = 0;
        }
    } else if ($tipoConsulta == 'NoProgra') {
        $mesNoProgramadas = $api->getsusNoProgramadas2018($inicio, $fin);
        if (count($mesNoProgramadas) > 0) {
            $valorRespuesta = $mesNoProgramadas[0]->{'count'};
        } else {
            $valorRespuesta = 0;
        }
    } else if ($tipoConsulta == 'Efec') {
        $mesEfectivas = $api->getsusEfectivas2018($inicio, $fin);
        if (count($mesEfectivas) > 0) {
            $valorRespuesta = $mesEfectivas[0]->{'count'};
        } else {
            $valorRespuesta = 0;
        }
    }

    return $valorRespuesta;
}

echo json_encode($respuestaJSON);
