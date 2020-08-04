<?php

require 'libUsuarios.php';

$api = new ChatbotApi();

$fechainicio = $_POST['fechaInicio'];
$fechafin = $_POST['fechaFin'];
$fechainicio2 = $_POST['fechaInicio2'];
$fechafin2 = $_POST['fechaFin2'];
$ano = $_POST['ano'];

$respuestaJSON = array();
if ($fechainicio != '' && $fechafin != '') {
    //PARA LAS BUSQUEDAS
    $respuestaJSON['res_busqueda']['busqueda'] = $api->getBusqueda($fechainicio, $fechafin);
    //TABLA RESULTADOS sankey
    $respuestaJSON['res_resultados']['sankey'] = $api->getResultado($fechainicio, $fechafin);
    //grafica acceso menu
    $respuestaJSON['res_resultados']['menus'] = $api->getResultadoMenus($fechainicio, $fechafin);
    //grafica invocar chatbot
    $respuestaJSON['res_resultados']['invocar'] = $api->getResultadoInvocar($fechainicio, $fechafin);
    //TABLA CRITERIO DE BUSQUEDA
    $respuestaJSON['res_criterioBusqueda']['criterio'] = $api->getCriterioBusqueda($fechainicio, $fechafin);
    //CALIFICACIONES
    $respuestaJSON['res_calificaciones']['calificacion'] = $api->getCalificaciones($fechainicio, $fechafin);
    //segmentos
    $respuestaJSON['res_segmentos']['segmentos'] = $api->getConsultasSegmentosUbicacionMunicipio($fechainicio, $fechafin);

    $respuestaJSON['res_consultas_lucy']['consultasLucyHoraDia'] = $api->getconsultasLucyDiaHora($fechainicio, $fechafin);

} else if ($fechainicio2 != '' && $fechafin2 != '') {

    //tabla de fuentes de datos
    $respuestaJSON['res_indispo']['programadas'] = $api->getSuspProgramadas($fechainicio2, $fechafin2);

    $respuestaJSON['res_indispo']['efectivas'] = $api->getSuspEfectivas($fechainicio2, $fechafin2);

    $respuestaJSON['res_indispo']['noprogramadas'] = $api->getSuspNoProgramadas($fechainicio2, $fechafin2);

    $respuestaJSON['res_indispo']['noprogramadasUsuariosAfectados'] = $api->getSuspNoProgramadasUsuarios($fechainicio2, $fechafin2);
    $respuestaJSON['res_indispo']['noprogramadasUsuariosAfectadosDistintos'] = $api->getSuspNoProgramadasUsuariosDiferentes($fechainicio2, $fechafin2);

    //LLAMADOS PARA OBTENER LOS ULTIMOS REGISTROS
    $resUltimaPro = $api->getUltimaSuspProgramadas();
    $resUltimaNoPro = $api->getUltimaSuspNoProgramadas();
    $resUltimaEfec = $api->getUltimaSuspEfectivas();
//$fomatoFechaEfectivas = date("Y-m-d", strtotime($resUltimaEfec[0]->{'FECHA_ATENCION'}));
    $respuestaJSON['res_indispo']['ultimaProgramada'] = $resUltimaPro[0]->{'FECHA_INICIO'};
    $respuestaJSON['res_indispo']['UltimaNoProgramada'] = $resUltimaNoPro[0]->{'FECHA_HORA'};

    $resUltimaEfec[0]->{'FECHA_ATENCION'} = date("Y-m-d");
    $respuestaJSON['res_indispo']['UltimaEfectiva'] = $resUltimaEfec[0]->{'FECHA_ATENCION'};

    $respuestaJSON['res_indispo']['wssgo'] = $api->getPruebasSgo($fechainicio2, $fechafin2);

    $respuestaJSON['res_indispo']['wssgoError'] = $api->getErrorSgo($fechainicio2, $fechafin2);

} else if ($ano != '') {

    //Para tendendias
    //consultas por mes, busqueas y criterios por mes
    $respuestaJSON['res_busqueda']['consultasTendencias'] = $api->getConsultasMes($ano);

    //resultados por mes
    $respuestaJSON['res_busqueda']['resultadosTendencias'] = $api->getResultadosMes($ano);

    //menus por mes
    $respuestaJSON['res_busqueda']['accesoMenus'] = $api->getAccesosMenuMes($ano);

}

//GRAFICA DE RESULTADOS DISTRIBUIDOS EN EL TIEMPO
/* $respuestaJSON['res_resultados']['resultadoDistribucion'] = $api->getResultadoDistribucion(); */

//GRAFICA DE criterios BUSQUEDAS DISTRIBUIDOS EN EL TIEMPO
/* $respuestaJSON['res_criterioBusqueda']['busquedaDistribucion'] = $api->getCriterioBusquedaDistribucion(); */

//Traer la cantidad de registros por cada mes
/* $respuestaJSON['res_indispo_mes']['MayoProgra'] = indispoMeses($api,'Progra','2018-05-01','2018-05-31');
$respuestaJSON['res_indispo_mes']['MayoNoProgra'] = indispoMeses($api,'NoProgra','2018-05-01','2018-05-31');
$respuestaJSON['res_indispo_mes']['MayoEfec'] = indispoMeses($api,'Efec','2018/05/01','2018/05/31');

$respuestaJSON['res_indispo_mes']['JunioProgra'] = indispoMeses($api,'Progra','2018-06-01','2018-06-30');
$respuestaJSON['res_indispo_mes']['JunioNoProgra'] = indispoMeses($api,'NoProgra','2018-06-01','2018-06-31');
$respuestaJSON['res_indispo_mes']['JunioEfec'] = indispoMeses($api,'Efec','2018/05/01','2018/05/31');

$respuestaJSON['res_indispo_mes']['JulioProgra'] = indispoMeses($api,'Progra','2018-07-01','2018-07-31');
$respuestaJSON['res_indispo_mes']['JulioNoProgra'] = indispoMeses($api,'NoProgra','2018-07-01','2018-07-31');
$respuestaJSON['res_indispo_mes']['JulioEfec'] = indispoMeses($api,'Efec','2018/07/01','2018/07/31');

$respuestaJSON['res_indispo_mes']['AgostoProgra'] = indispoMeses($api,'Progra','2018-08-01','2018-08-31');
$respuestaJSON['res_indispo_mes']['AgostoNoProgra'] = indispoMeses($api,'NoProgra','2018-08-01','2018-08-31');
$respuestaJSON['res_indispo_mes']['AgostoEfec'] = indispoMeses($api,'Efec','2018/08/01','2018/08/31');

$respuestaJSON['res_indispo_mes']['SeptiembreProgra'] = indispoMeses($api,'Progra','2018-09-01','2018-09-30');
$respuestaJSON['res_indispo_mes']['SeptiembreNoProgra'] = indispoMeses($api,'NoProgra','2018-09-01','2018-09-30');
$respuestaJSON['res_indispo_mes']['SeptiembreEfec'] = indispoMeses($api,'Efec','2018/09/01','2018/09/30');

$respuestaJSON['res_indispo_mes']['OctubreProgra'] = indispoMeses($api,'Progra','2018-10-01','2018-10-31');
$respuestaJSON['res_indispo_mes']['OctubreNoProgra'] = indispoMeses($api,'NoProgra','2018-10-01','2018-10-31');
$respuestaJSON['res_indispo_mes']['OctubreEfec'] = indispoMeses($api,'Efec','2018/10/01','2018/10/31');

$respuestaJSON['res_indispo_mes']['NoviembreProgra'] = indispoMeses($api,'Progra','2018-11-01','2018-11-30');
$respuestaJSON['res_indispo_mes']['NoviembreNoProgra'] = indispoMeses($api,'NoProgra','2018-11-01','2018-11-30');
$respuestaJSON['res_indispo_mes']['NoviembreEfec'] = indispoMeses($api,'Efec','2018/11/01','2018/11/30');

$respuestaJSON['res_indispo_mes']['DiciembreProgra'] = indispoMeses($api,'Progra','2018-12-01','2018-12-31');
$respuestaJSON['res_indispo_mes']['DiciembreNoProgra'] = indispoMeses($api,'NoProgra','2018-12-01','2018-12-31');
$respuestaJSON['res_indispo_mes']['DiciembreEfec'] = indispoMeses($api,'Efec','2018/12/01','2018/12/31'); */

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
