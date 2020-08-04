<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/* ini_set('memory_limit', '-1'); */
/* set_time_limit(600); */
require 'consultasChatbot.php';

class ChatbotApi
{
    private $conPostgres;
    private $conMongo;

//Credenciales HEROKU Mlab pruebas
    //private $host = "mongodb://heroku_69tb2th4:m2oheamen7422pmnq3htdb56dt@ds113775.mlab.com:13775/heroku_69tb2th4";

    //Credenciales HEROKU Mlab produccion
    //private $host = "mongodb://heroku_qqkvqh3x:b50q78jtvojl1aobh01eut4rpj@ds215358-a1.mlab.com:15358/heroku_qqkvqh3x"; //lucy
    private $host = "mongodb://heroku_69tb2th4:m2oheamen7422pmnq3htdb56dt@ds113775.mlab.com:13775/heroku_69tb2th4"; //electrohuila
    public function __construct()
    {
        $this->connectDBmongo();
    }

    public function connectDBmongo()
    {
        try {
            $this->conMongo = new MongoDB\Driver\Manager($this->host);
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $filename = basename(__FILE__);
            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";
            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }
    }

    public function login($usr, $pwd)
    {
        return logQuery($this->conMongo, $usr, $pwd);
    }

    //----------------------------INSERTS DE LOG PARA MONITOREO------------------------------------
    public function setLogin($usuario)
    {
        return insertLogAcceso($this->conMongo, $usuario, 'Ingreso');
    }

    public function setLogout($usuario)
    {
        return insertLogAcceso($this->conMongo, $usuario, 'Salida');
    }

    ///////////////////FILTER PARA MONITOREO DIFUSION
    public function getRulesDifusion()
    {
        return getRules_Difusion($this->conMongo);
    }
    public function getCantidadEventosReglas($fechainicio, $fechafin, $reglas)
    {
        return getCantidad_EventosReglas($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getCantidadSwitchsInvalidos($fechainicio, $fechafin)
    {
        return getCantidad_SwitchsInvalidos($this->conMongo, $fechainicio, $fechafin);
    }


    public function getEventosSinCierre($fechainicio, $fechafin)
    {
        return filterEventosSinCierre($this->conMongo, $fechainicio, $fechafin);
    }
    public function getEventosScadaAperturaCierre($fechainicio, $fechafin)
    {
        return filterEventosScadaAperturaCierre($this->conMongo, $fechainicio, $fechafin);
    }
    public function getEventosIndisponibilidad($fechainicio, $fechafin, $reglas)
    {
        return getEventos_Indisponibilidad($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getEventosSuspProgramada($fechainicio, $fechafin, $reglas)
    {
        return getEventos_SuspProgramada($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getEventosSusNoProgramada($fechainicio, $fechafin, $reglas)
    {
        return getEventos_SusNoProgramada($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getEventosMensajesEnviados($fechainicio, $fechafin, $reglas)
    {
        return getEventos_MensajesEnviados($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getEventosNormalizacion($fechainicio, $fechafin, $reglas)
    {
        return getEventos_Normalizacion($this->conMongo, $fechainicio, $fechafin, $reglas);
    }
    public function getEventosAperturas($fechainicio, $fechafin)
    {
        return getEventosAperturas($this->conMongo, $fechainicio, $fechafin);
    }

    /*public function getResultado($fechainicio, $fechafin, $reglas, $eventos, $eventos_reglas, $reglas_switch_invalido, $indispo, $normali, $eventosAperturas)
    {
        $response = array();
        $response['cantidad_eventos'] = $eventos->n + $eventosAperturas->n;

        $response['eventos_entran'] = $eventos_reglas->n + $reglas_switch_invalido->n;
        $response['eventos_no_entran'] = ($eventos->n + $eventosAperturas->n) - ($eventos_reglas->n + $reglas_switch_invalido->n);

        $response['switch_validos'] = $eventos_reglas->n;
        $response['switch_no_validos'] = $reglas_switch_invalido->n;

        $response['indisponibilidad'] = $indispo->n;
        $response['normalizacion'] = $normali->n;
        $sinEnvio = filterResultadosSinEnvio($this->conMongo, $fechainicio, $fechafin, $reglas);
        $response['sinEnvio'] = $sinEnvio->n;
        $undefined = filterResultadosIndefinidos($this->conMongo, $fechainicio, $fechafin, $reglas);
        $response['undefined'] = $undefined->n;

        return $response;

    }*/

    public function getConsultasSegmentosUbicacionMunicipio($fechainicio, $fechafin, $reglas)
    {
        $result = filterConsultasSegmentosUbicacionMunicipio2($this->conMongo, $fechainicio, $fechafin, $reglas);
        $resultUsuariosporMunicipio = filterConsultaUsuariosXMunicipio($this->conMongo);
        $respuestaFinal = array();
        $responseSeg = array();
        $responseSeg['hogares'] = 0;
        $responseSeg['empresas'] = 0;
        $responseSeg['grandesClientes'] = 0;
        $responseSeg['gobierno'] = 0;
        $responseUbi = array();
        $responseUbi['urbano'] = 0;
        $responseUbi['rural'] = 0;
        $responseCls = array();
        $responseCls['alumbrado'] = 0;
        $responseCls['comercial'] = 0;
        $responseCls['industria'] = 0;
        $responseCls['oficial'] = 0;
        $responseCls['otros'] = 0;
        $responseCls['residencial'] = 0;
        $responseCls['asistencial'] = 0;
        $responseCls['educativo'] = 0;
        $responseCls['areasComunes'] = 0;
        $responseCls['oxigeno'] = 0;
        $responseCls['provisional'] = 0;

        $municipiosArray = array(
            "MANIZALES" => 0, "DOSQUEBRADAS" => 0, "LA VIRGINIA" => 0, "CHINCHINA" => 0, "PALESTINA" => 0, "VILLAMARIA" => 0,
            "MARSELLA" => 0, "SANTA ROSA" => 0, "RISARALDA" => 0, "ANSERMA" => 0, "VITERBO" => 0, "BELEN DE UMBRIA" => 0, "NEIRA" => 0,
            "MARMATO" => 0, "PACORA" => 0, "SUPIA" => 0, "VICTORIA" => 0, "NORCASIA" => 0, "LA DORADA" => 0,
            "TAMESIS" => 0, "JARDIN" => 0, "ANDES" => 0, "ABEJORRAL" => 0, "SANTA BARBARA" => 0, "LA PINTADA" => 0, "VALPARAISO" => 0,
            "CARAMANTA" => 0, "NARIÑO" => 0, "ARGELIA" => 0, "SONSON" => 0,
            "MARULANDA" => 0, "PENSILVANIA" => 0, "SAMANA" => 0, "SALAMINA" => 0, "AGUADAS" => 0, "ARANZAZU" => 0, "QUINCHIA" => 0,
            "SAN JOSE" => 0, "BELALCAZAR" => 0, "APIA" => 0, "SANTUARIO" => 0, "MISTRATO" => 0, "FILADELFIA" => 0, "LA MERCED" => 0,
            "RIOSUCIO" => 0, "GUATICA" => 0, "MARQUETALIA" => 0, "MANZANARES" => 0, "BALBOA" => 0, "LA CELIA" => 0, "PUEBLO RICO" => 0, "PEREIRA" => 0);

        $responseMunicipioUrbano = $municipiosArray;
        $responseMunicipioRural = $municipiosArray;
        $usuariosMunicipio = $municipiosArray;

        $sumaCiudad = 0;
        $cantidadCiudad = 0;

        if (count($resultUsuariosporMunicipio) > 0) {

            foreach ($resultUsuariosporMunicipio as $key => $value) {
                if (isset($usuariosMunicipio[strtoupper($value->_id)])) {
                    $usuariosMunicipio[strtoupper($value->_id)] += $value->cantidad;
                } else {
                    $cabecera = filterConsultaMunicipio($this->conMongo, $value->_id);
                    $cabecera = strtoupper($cabecera[0]->MUNICIPIO_CABECERA);

                    if (isset($usuariosMunicipio[$cabecera])) {
                        $usuariosMunicipio[$cabecera] += $value->cantidad;
                    }
                }
            }

        }

        if (count($result) > 0) {

            foreach ($result as $key => $value) {
                $rural = 0;
                $urbano = 0;
                if (isset($value->UBICACION->R)) {
                    $rural = $value->UBICACION->R;
                }
                if (isset($value->UBICACION->U)) {
                    $urbano = $value->UBICACION->U;
                }

                foreach ($value->UBICACION as $ubicacion => $cantidad) {
                    switch (strtoupper($ubicacion)) {
                        case 'U':
                            $responseUbi['urbano'] += $cantidad;
                            break;
                        case 'R':
                            $responseUbi['rural'] += $cantidad;
                            break;
                    }
                }

                foreach ($value->MUNICIPIOS as $municipio => $cantidad) {

                    if (isset($municipiosArray[strtoupper($municipio)])) {
                        $municipiosArray[strtoupper($municipio)] = $municipiosArray[strtoupper($municipio)] + $cantidad;

                        if (isset($value->UBICACION->R, $value->UBICACION->U)) {

                            if ($value->UBICACION->U >= $value->UBICACION->R) {
                                if (count((array) $value->MUNICIPIOS) > 1) {
                                    $cantidadMunicipio = $cantidad;

                                    while ($cantidadMunicipio > 0) {

                                        if ($urbano > 0) {

                                            if ($cantidadMunicipio < $urbano) {

                                                $responseMunicipioUrbano[strtoupper($municipio)] += $cantidadMunicipio;
                                                $urbano = $urbano - $cantidadMunicipio;
                                                $cantidadMunicipio = 0;

                                            } else {

                                                $responseMunicipioUrbano[strtoupper($municipio)] += $urbano;
                                                $cantidadMunicipio = $cantidadMunicipio - $urbano;
                                                $urbano = 0;
                                            }
                                        } else {
                                            if ($cantidadMunicipio < $rural) {

                                                $responseMunicipioRural[strtoupper($municipio)] += $cantidadMunicipio;
                                                $rural = $rural - $cantidadMunicipio;
                                                $cantidadMunicipio = 0;

                                            } else {

                                                $responseMunicipioRural[strtoupper($municipio)] += $rural;
                                                $cantidadMunicipio = $cantidadMunicipio - $rural;
                                                $rural = 0;
                                            }
                                        }

                                    }

                                } else {
                                    $responseMunicipioUrbano[strtoupper($municipio)] += $value->UBICACION->U;
                                    $responseMunicipioRural[strtoupper($municipio)] += $value->UBICACION->R;
                                }
                            } else {

                                if (count((array) $value->MUNICIPIOS) > 1) {
                                    $cantidadMunicipio = $cantidad;

                                    while ($cantidadMunicipio > 0) {
                                        if ($rural > 0) {

                                            if ($cantidadMunicipio < $rural) {

                                                $responseMunicipioRural[strtoupper($municipio)] += $cantidadMunicipio;
                                                $rural = $rural - $cantidadMunicipio;
                                                $cantidadMunicipio = 0;

                                            } else {

                                                $responseMunicipioRural[strtoupper($municipio)] += $rural;
                                                $cantidadMunicipio = $cantidadMunicipio - $rural;
                                                $rural = 0;
                                            }

                                        } else {
                                            if ($cantidadMunicipio < $urbano) {

                                                $responseMunicipioUrbano[strtoupper($municipio)] += $cantidadMunicipio;
                                                $urbano = $urbano - $cantidadMunicipio;
                                                $cantidadMunicipio = 0;

                                            } else {

                                                $responseMunicipioUrbano[strtoupper($municipio)] += $urbano;
                                                $cantidadMunicipio = $cantidadMunicipio - $urbano;
                                                $urbano = 0;
                                            }
                                        }

                                    }
                                } else {

                                    $responseMunicipioUrbano[strtoupper($municipio)] += $value->UBICACION->U;
                                    $responseMunicipioRural[strtoupper($municipio)] += $value->UBICACION->R;
                                }
                            }

                        } else if (isset($value->UBICACION->R)) {

                            $responseMunicipioRural[strtoupper($municipio)] += $cantidad;

                        } else if (isset($value->UBICACION->U)) {

                            $responseMunicipioUrbano[strtoupper($municipio)] += $cantidad;

                        }

                    } else {

                        $cabecera = filterConsultaMunicipio($this->conMongo, $municipio);
                        $cabecera = strtoupper($cabecera[0]->MUNICIPIO_CABECERA);

                        if (isset($municipiosArray[$cabecera])) {

                            $municipiosArray[$cabecera] += $cantidad;

                            if (isset($value->UBICACION->R, $value->UBICACION->U)) {

                                if ($value->UBICACION->U >= $value->UBICACION->R) {
                                    if (count((array) $value->MUNICIPIOS) > 1) {
                                        $cantidadMunicipio = $cantidad;

                                        while ($cantidadMunicipio > 0) {

                                            if ($urbano > 0) {

                                                if ($cantidadMunicipio < $urbano) {

                                                    $responseMunicipioUrbano[$cabecera] += $cantidadMunicipio;
                                                    $urbano = $urbano - $cantidadMunicipio;
                                                    $cantidadMunicipio = 0;

                                                } else {

                                                    $responseMunicipioUrbano[$cabecera] += $urbano;
                                                    $cantidadMunicipio = $cantidadMunicipio - $urbano;
                                                    $urbano = 0;
                                                }
                                            } else {
                                                if ($cantidadMunicipio < $rural) {

                                                    $responseMunicipioRural[$cabecera] += $cantidadMunicipio;
                                                    $rural = $rural - $cantidadMunicipio;
                                                    $cantidadMunicipio = 0;

                                                } else {

                                                    $responseMunicipioRural[$cabecera] += $rural;
                                                    $cantidadMunicipio = $cantidadMunicipio - $rural;
                                                    $rural = 0;
                                                }
                                            }

                                        }

                                    } else {
                                        $responseMunicipioUrbano[$cabecera] += $value->UBICACION->U;
                                        $responseMunicipioRural[$cabecera] += $value->UBICACION->R;
                                    }
                                } else {

                                    if (count((array) $value->MUNICIPIOS) > 1) {
                                        $cantidadMunicipio = $cantidad;

                                        while ($cantidadMunicipio > 0) {
                                            if ($rural > 0) {

                                                if ($cantidadMunicipio < $rural) {

                                                    $responseMunicipioRural[$cabecera] += $cantidadMunicipio;
                                                    $rural = $rural - $cantidadMunicipio;
                                                    $cantidadMunicipio = 0;

                                                } else {

                                                    $responseMunicipioRural[$cabecera] += $rural;
                                                    $cantidadMunicipio = $cantidadMunicipio - $rural;
                                                    $rural = 0;
                                                }

                                            } else {
                                                if ($cantidadMunicipio < $urbano) {

                                                    $responseMunicipioUrbano[$cabecera] += $cantidadMunicipio;
                                                    $urbano = $urbano - $cantidadMunicipio;
                                                    $cantidadMunicipio = 0;

                                                } else {

                                                    $responseMunicipioUrbano[$cabecera] += $urbano;
                                                    $cantidadMunicipio = $cantidadMunicipio - $urbano;
                                                    $urbano = 0;
                                                }
                                            }

                                        }
                                    } else {

                                        $responseMunicipioUrbano[$cabecera] += $value->UBICACION->U;
                                        $responseMunicipioRural[$cabecera] += $value->UBICACION->R;
                                    }
                                }

                            } else if (isset($value->UBICACION->R)) {

                                $responseMunicipioRural[$cabecera] += $cantidad;

                            } else if (isset($value->UBICACION->U)) {

                                $responseMunicipioUrbano[$cabecera] += $cantidad;

                            }

                        }
                    }

                }
                foreach ($value->SEGMENTOS as $segmento => $cantidad) {

                    switch (strtoupper($segmento)) {
                        case 'HOGARES':
                            $responseSeg['hogares'] += $cantidad;
                            break;
                        case 'EMPRESAS':
                            $responseSeg['empresas'] += $cantidad;
                            break;
                        case 'GRANDES CLIENTES':
                            $responseSeg['grandesClientes'] += $cantidad;
                            break;
                        case 'GOBIERNO':
                            $responseSeg['gobierno'] += $cantidad;
                            break;
                    }
                }

                foreach ($value->CLASE_SERVICIO as $clase => $cantidad) {
                    switch (strtoupper($clase)) {
                        case 'ALUMBRADO PUBLICO':
                            $responseCls['alumbrado'] += $cantidad;
                            break;
                        case 'COMERCIAL':
                            $responseCls['comercial'] += $cantidad;
                            break;
                        case 'INDUSTRIAL':
                            $responseCls['industria'] += $cantidad;
                            break;
                        case 'SERVICIOS Y OFICIAL':
                            $responseCls['oficial'] += $cantidad;
                            break;
                        case 'OTROS':
                            $responseCls['otros'] += $cantidad;
                            break;
                        case 'RESIDENCIAL':
                            $responseCls['residencial'] += $cantidad;
                            break;
                        case 'ESPECIAL ASISTENCIAL':
                            $responseCls['asistencial'] += $cantidad;
                            break;
                        case 'ESPECIAL EDUCATIVO':
                            $responseCls['educativo'] += $cantidad;
                            break;
                        case 'AREAS COMUNES':
                            $responseCls['areasComunes'] += $cantidad;
                            break;
                        case 'OXIGENODEPENDIENTES':
                            $responseCls['oxigeno'] += $cantidad;
                            break;
                        case 'PROVISIONAL':
                            $responseCls['provisional'] += $cantidad;
                            break;
                    }
                }

            }
            //var_dump($municipiosArray);
            $respuestaFinal['segmentos'] = $responseSeg;
            $respuestaFinal['ubicacion'] = $responseUbi;
            $respuestaFinal['clasesServicio'] = $responseCls;
            $respuestaFinal['municipio'] = $municipiosArray;
            $respuestaFinal['municipioUrbano'] = $responseMunicipioUrbano;
            $respuestaFinal['municipioRural'] = $responseMunicipioRural;
            $respuestaFinal['usuariosMunicipio'] = $usuariosMunicipio;

            return $respuestaFinal;
        } else {
            $respuestaFinal['segmentos'] = $responseSeg;
            $respuestaFinal['ubicacion'] = $responseUbi;
            $respuestaFinal['clasesServicio'] = $responseCls;
            $respuestaFinal['municipio'] = $municipiosArray;
            $respuestaFinal['municipioUrbano'] = $responseMunicipioUrbano;
            $respuestaFinal['municipioRural'] = $responseMunicipioRural;
            $respuestaFinal['usuariosMunicipio'] = $usuariosMunicipio;
        }
        //var_dump($respuestaFinal);
        return $respuestaFinal;

    }

    public function getUsuariosImpactados($fechainicio, $fechafin, $reglas)
    {
        $logdifusion = filterDifusionUsuariosImpactados($this->conMongo, $fechainicio, $fechafin, $reglas);
        $resultadosCriterio = array();
        $resultadosCriterio['CANTIDAD_USUARIOS_IMPACTADOS'] = 0;
        $resultadosUsuarios = array();
        $response = [];

        foreach ($logdifusion as $key => $value) {
            foreach ($value->USUARIOS as $posicion => $niu) {
                if (!in_array($niu, $resultadosUsuarios)) {
                    array_push($resultadosUsuarios, $niu);
                    $resultadosCriterio['CANTIDAD_USUARIOS_IMPACTADOS'] += 1;
                }
            }
        }

        $response['criterio'] = $resultadosCriterio;
        return $response;
    }
    public function getDifusionCantidadUsuarios($fechainicio, $fechafin, $reglas)
    {
        $difusionTendencias = filterDifusionTendencias($this->conMongo, $fechainicio, $fechafin, $reglas);
        $difusionCantidadDifundida = difusionCantidadDifundida($this->conMongo, $fechainicio, $fechafin, $reglas);
        $resultadosCriterio = array();

        $resultadosCriterio['CANTIDAD_DIFUNDIDA_APERTURAS'] = $difusionCantidadDifundida['aperturas']->n;
        $resultadosCriterio['CANTIDAD_DIFUNDIDA_CIERRES'] = $difusionCantidadDifundida['cierres']->n;

        $resultadosCriterio['CANTIDAD_POSIBLE_SIN_TELEFONO'] = 0;
        $resultadosCriterio['CANTIDAD_POSIBLE_TELEFONO'] = 0;
        $response = [];

        foreach ($difusionTendencias as $key => $value) {
            $resultadosCriterio['CANTIDAD_POSIBLE_SIN_TELEFONO'] += $value->CANTIDAD_POSIBLE_SIN_TELEFONO;
            $resultadosCriterio['CANTIDAD_POSIBLE_TELEFONO'] += $value->CANTIDAD_POSIBLE_TELEFONO;
        }

        $response['criterio'] = $resultadosCriterio;
        return $response;
    }

    /*public function getDifusionPromedioHora($fechainicio, $fechafin)
    {
        return filterDifusionPromedio($this->conMongo, $fechainicio, $fechafin);
    }*/
    public function getConsultasXdiayHora($fechainicio, $fechafin)
    {
        return filtrarXdiasYhoras($this->conMongo, $fechainicio, $fechafin);
    }
    public function getAcuseReciboPromocionLucy($fechainicio, $fechafin)
    {
        return count(getAcuseRecibo_PromocionLucy($this->conMongo, $fechainicio, $fechafin));
    }
    public function getAcuseReciboPromocionProgramadas($fechainicio, $fechafin)
    {
        return count(getAcuseReciboPromocion_Programadas($this->conMongo, $fechainicio, $fechafin));
    }
    public function getAcuseRecibo($fechainicio, $fechafin)
    {
        $response = getAcuse_Recibo($this->conMongo, $fechainicio, $fechafin);

        $respuesta = array();
        foreach ($response as $value) {
            if ($value->ESTADO_APERTURA == '1') {
                if (isset($respuesta['entregadoApertura'])) {
                    $respuesta['entregadoApertura'] += 1;
                } else {
                    $respuesta['entregadoApertura'] = 1;
                }
            } else if ($value->ESTADO_APERTURA == '2') {
                if (isset($respuesta['noEntregaApertura'])) {
                    $respuesta['noEntregaApertura'] += 1;
                } else {
                    $respuesta['noEntregaApertura'] = 1;
                }
            } else if ($value->ESTADO_APERTURA == '4') {
                if (isset($respuesta['entregaSMSCApertura'])) {
                    $respuesta['entregaSMSCApertura'] += 1;
                } else {
                    $respuesta['entregaSMSCApertura'] = 1;
                }
            } else if ($value->ESTADO_APERTURA == '16') {
                if (isset($respuesta['noEntregaOperadoraApertura'])) {
                    $respuesta['noEntregaOperadoraApertura'] += 1;
                } else {
                    $respuesta['noEntregaOperadoraApertura'] = 1;
                }
            }
            if ($value->ESTADO_CIERRE == '1') {
                if (isset($respuesta['entregadoCierre'])) {
                    $respuesta['entregadoCierre'] += 1;
                } else {
                    $respuesta['entregadoCierre'] = 1;
                }
            } else if ($value->ESTADO_CIERRE == '2') {
                if (isset($respuesta['noEntregaCierre'])) {
                    $respuesta['noEntregaCierre'] += 1;
                } else {
                    $respuesta['noEntregaCierre'] = 1;
                }
            } else if ($value->ESTADO_CIERRE == '4') {
                if (isset($respuesta['entregaSMSCCierre'])) {
                    $respuesta['entregaSMSCCierre'] += 1;
                } else {
                    $respuesta['entregaSMSCCierre'] = 1;
                }
            } else if ($value->ESTADO_CIERRE == '16') {
                if (isset($respuesta['noEntregaOperadoraCierre'])) {
                    $respuesta['noEntregaOperadoraCierre'] += 1;
                } else {
                    $respuesta['noEntregaOperadoraCierre'] = 1;
                }
            }
        }

        return $respuesta;
    }

   

    //
    //
    //
    //
    //
    //

    //------------------------- FILTER PARA EL MONITOREO ------------------------------------------

    /*public function getResultadoMenus($fechainicio, $fechafin)
    {
        $result = filterResultadoMenus($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['faltaEnergia'] = 0;
        $response['puntosAtencion'] = 0;
        $response['pagoLinea'] = 0;
        $response['vacantes'] = 0;
        $response['otros'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->MENU) {
                case 'Falta de Energia':
                    $response['faltaEnergia'] += 1;
                    break;
                case 'Pago en Linea':
                    $response['pagoLinea'] += 1;
                    break;
                case 'Puntos de Atencion':
                    $response['puntosAtencion'] += 1;
                    break;
                case 'Vacantes':
                    $response['vacantes'] += 1;
                    break;
                case 'Otros motivos':
                    $response['otros'] += 1;
                    break;
            }
        }
        return $response;

    }*/

    public function getResultadoDistribucion()
    {
        return filterResultadoDistribucion($this->conMongo);

    }
    public function getBusquedaDistribucion($contexto)
    {
        return filterBusquedaDistribucion($this->conMongo, $contexto);
    }
    public function getBusquedaDistribucionMeses($contexto)
    {
        return filterBusquedaDistribucionMeses($this->conMongo, $contexto);
    }

    public function getCriterioBusqueda($fechainicio, $fechafin)
    {
        $result = filterCriterioBusqueda($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['niu'] = 0;
        $response['cedula'] = 0;
        $response['nit'] = 0;
        $response['direccion'] = 0;
        $response['nombre'] = 0;
        $response['telefono'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->CRITERIO) {
                case 'niu':
                    $response['niu'] += 1;
                    break;
                case 'cedula':
                    $response['cedula'] += 1;
                    break;
                case 'nit':
                    $response['nit'] += 1;
                    break;
                case 'direccion':
                    $response['direccion'] += 1;
                    break;
                case 'nombre':
                    $response['nombre'] += 1;
                    break;
                case 'telefono':
                    $response['telefono'] += 1;
                    break;
            }
        }
        return $response;

    }
    public function getCriterioBusquedaDistribucion()
    {
        return filterCriterioBusquedaDistribucion($this->conMongo);
    }

    public function getIngresoPorHora()
    {
        return filterIngresoPorHora($this->conMongo);
    }

    public function getIngresoPorDia()
    {
        return filterIngresoPorDia($this->conMongo);
    }
    /*public function getCalificaciones($fechainicio, $fechafin)
    {
        $result = filterCalificaciones($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['excelente'] = 0;
        $response['bueno'] = 0;
        $response['regular'] = 0;
        $response['malo'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->CALIFICACION) {
                case 'Excelente':
                    $response['excelente'] += 1;
                    break;
                case 'Bueno':
                    $response['bueno'] += 1;
                    break;
                case 'Regular':
                    $response['regular'] += 1;
                    break;
                case 'Malo':
                    $response['malo'] += 1;
                    break;
            }
        }
        return $response;
    }*/

    //Para Obtener la cantidad de suspensiones
    public function getSuspProgramadas($fechainicio, $fechafin)
    {
        $respProgramadas = filterSuspProgramadas($this->conMongo, $fechainicio, $fechafin);

        if (count($respProgramadas) > 0) {
            $response = count($respProgramadas);
        } else {
            $response = 0;
        }

        return $response;
    }
    public function getSuspNoProgramadas($fechainicio, $fechafin)
    {
        $resNoProgra = filterSuspNoProgramadas($this->conMongo, $fechainicio, $fechafin);
        if (count($resNoProgra) > 0) {
            $response = $resNoProgra[0]->{'total'};
        } else {
            $response = 0;
        }
        return $response;
    }
    public function getSuspEfectivas($fechainicio, $fechafin)
    {

        $resEfectivas = filterSuspEfectivas($this->conMongo, $fechainicio, $fechafin);
        if (count($resEfectivas) > 0) {
            $response = $resEfectivas[0]->{'total'};
        } else {
            $response = 0;
        }
        return $response;
    }

    //para obtener el ultimo registro de las suspensiones
    public function getUltimaSuspProgramadas()
    {
        return filterUltimaSuspProgramadas($this->conMongo);
    }
    //para obtener los resultados de las pruebas ws sgo
    public function getPruebasSgo($fechainicio, $fechafin)
    {
        return filterPruebasSgo($this->conMongo, $fechainicio, $fechafin);
    }
    //para obtener los resultados de los errores al sgo
    public function getErrorSgo($fechainicio, $fechafin)
    {
        return filterErrorSgo($this->conMongo, $fechainicio, $fechafin);
    }
    public function getUltimaSuspNoProgramadas()
    {
        return filterUltimaSuspNoProgramadas($this->conMongo);
    }
    public function getUltimaSuspEfectivas()
    {
        return filterUltimaEfectivas($this->conMongo);
    }
    //obtener registros para la grafica de resultados
    public function getsusProgramadas2018($inicio, $fin)
    {
        return susProgramadas2018($this->conMongo, $inicio, $fin);
    }
    public function getsusNoProgramadas2018($inicio, $fin)
    {
        return susNoProgramadas2018($this->conMongo, $inicio, $fin);
    }
    public function getsusEfectivas2018($inicio, $fin)
    {
        return susEfectivas2018($this->conMongo, $inicio, $fin);
    }
    public function getAccesosMenuMes($ano)
    {
        $result = filterAccesosMenuMes($this->conMongo, $ano);
        $resultados = array();

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $fecha = strtotime($value->FECHA_RESULTADO);
                switch (date('M', $fecha)) {
                    case 'Jan':
                        if (isset($resultados[0])) {
                            if (isset($resultados[0][$value->MENU])) {
                                $resultados[0][$value->MENU] += 1;
                            } else {
                                $resultados[0][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[0]['Falta de Energia'] = 0;
                            $resultados[0]['Pago en Linea'] = 0;
                            $resultados[0]['Puntos de Atencion'] = 0;
                            $resultados[0]['Vacantes'] = 0;
                            $resultados[0]['Otros motivos'] = 0;
                            $resultados[0][$value->MENU] = 1;
                        }
                        break;
                    case 'Feb':
                        if (isset($resultados[1])) {
                            if (isset($resultados[1][$value->MENU])) {
                                $resultados[1][$value->MENU] += 1;
                            } else {
                                $resultados[1][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[1]['Falta de Energia'] = 0;
                            $resultados[1]['Pago en Linea'] = 0;
                            $resultados[1]['Puntos de Atencion'] = 0;
                            $resultados[1]['Vacantes'] = 0;
                            $resultados[1]['Otros motivos'] = 0;
                            $resultados[1][$value->MENU] = 1;
                        }
                        break;
                    case 'Mar':
                        if (isset($resultados[2])) {
                            if (isset($resultados[2][$value->MENU])) {
                                $resultados[2][$value->MENU] += 1;
                            } else {
                                $resultados[2][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[2]['Falta de Energia'] = 0;
                            $resultados[2]['Pago en Linea'] = 0;
                            $resultados[2]['Puntos de Atencion'] = 0;
                            $resultados[2]['Vacantes'] = 0;
                            $resultados[2]['Otros motivos'] = 0;
                            $resultados[2][$value->MENU] = 1;
                        }
                        break;
                    case 'Apr':
                        if (isset($resultados[3])) {
                            if (isset($resultados[3][$value->MENU])) {
                                $resultados[3][$value->MENU] += 1;
                            } else {
                                $resultados[3][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[3]['Falta de Energia'] = 0;
                            $resultados[3]['Pago en Linea'] = 0;
                            $resultados[3]['Puntos de Atencion'] = 0;
                            $resultados[3]['Vacantes'] = 0;
                            $resultados[3]['Otros motivos'] = 0;
                            $resultados[3][$value->MENU] = 1;
                        }
                        break;
                    case 'May':
                        if (isset($resultados[4])) {
                            if (isset($resultados[4][$value->MENU])) {
                                $resultados[4][$value->MENU] += 1;
                            } else {
                                $resultados[4][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[4]['Falta de Energia'] = 0;
                            $resultados[4]['Pago en Linea'] = 0;
                            $resultados[4]['Puntos de Atencion'] = 0;
                            $resultados[4]['Vacantes'] = 0;
                            $resultados[4]['Otros motivos'] = 0;
                            $resultados[4][$value->MENU] = 1;
                        }
                        break;
                    case 'Jun':
                        if (isset($resultados[5])) {
                            if (isset($resultados[5][$value->MENU])) {
                                $resultados[5][$value->MENU] += 1;
                            } else {
                                $resultados[5][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[5]['Falta de Energia'] = 0;
                            $resultados[5]['Pago en Linea'] = 0;
                            $resultados[5]['Puntos de Atencion'] = 0;
                            $resultados[5]['Vacantes'] = 0;
                            $resultados[5]['Otros motivos'] = 0;
                            $resultados[5][$value->MENU] = 1;
                        }
                        break;
                    case 'Jul':
                        if (isset($resultados[6])) {
                            if (isset($resultados[6][$value->MENU])) {
                                $resultados[6][$value->MENU] += 1;
                            } else {
                                $resultados[6][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[6]['Falta de Energia'] = 0;
                            $resultados[6]['Pago en Linea'] = 0;
                            $resultados[6]['Puntos de Atencion'] = 0;
                            $resultados[6]['Vacantes'] = 0;
                            $resultados[6]['Otros motivos'] = 0;
                            $resultados[6][$value->MENU] = 1;
                        }
                        break;
                    case 'Aug':
                        if (isset($resultados[7])) {
                            if (isset($resultados[7][$value->MENU])) {
                                $resultados[7][$value->MENU] += 1;
                            } else {
                                $resultados[7][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[7]['Falta de Energia'] = 0;
                            $resultados[7]['Pago en Linea'] = 0;
                            $resultados[7]['Puntos de Atencion'] = 0;
                            $resultados[7]['Vacantes'] = 0;
                            $resultados[7]['Otros motivos'] = 0;
                            $resultados[7][$value->MENU] = 1;
                        }
                        break;
                    case 'Sep':
                        if (isset($resultados[8])) {
                            if (isset($resultados[8][$value->MENU])) {
                                $resultados[8][$value->MENU] += 1;
                            } else {
                                $resultados[8][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[8]['Falta de Energia'] = 0;
                            $resultados[8]['Pago en Linea'] = 0;
                            $resultados[8]['Puntos de Atencion'] = 0;
                            $resultados[8]['Vacantes'] = 0;
                            $resultados[8]['Otros motivos'] = 0;
                            $resultados[8][$value->MENU] = 1;
                        }
                        break;
                    case 'Oct':
                        if (isset($resultados[9])) {
                            if (isset($resultados[9][$value->MENU])) {
                                $resultados[9][$value->MENU] += 1;
                            } else {
                                $resultados[9][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[9]['Falta de Energia'] = 0;
                            $resultados[9]['Pago en Linea'] = 0;
                            $resultados[9]['Puntos de Atencion'] = 0;
                            $resultados[9]['Vacantes'] = 0;
                            $resultados[9]['Otros motivos'] = 0;
                            $resultados[9][$value->MENU] = 1;
                        }
                        break;
                    case 'Nov':
                        if (isset($resultados[10])) {
                            if (isset($resultados[10][$value->MENU])) {
                                $resultados[10][$value->MENU] += 1;
                            } else {
                                $resultados[10][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[10]['Falta de Energia'] = 0;
                            $resultados[10]['Pago en Linea'] = 0;
                            $resultados[10]['Puntos de Atencion'] = 0;
                            $resultados[10]['Vacantes'] = 0;
                            $resultados[10]['Otros motivos'] = 0;
                            $resultados[10][$value->MENU] = 1;
                        }
                        break;
                    case 'Dec':
                        if (isset($resultados[11])) {
                            if (isset($resultados[11][$value->MENU])) {
                                $resultados[11][$value->MENU] += 1;
                            } else {
                                $resultados[11][$value->MENU] = 1;
                            }
                        } else {
                            $resultados[11]['Falta de Energia'] = 0;
                            $resultados[11]['Pago en Linea'] = 0;
                            $resultados[11]['Puntos de Atencion'] = 0;
                            $resultados[11]['Vacantes'] = 0;
                            $resultados[11]['Otros motivos'] = 0;
                            $resultados[11][$value->MENU] = 1;
                        }
                        break;
                }
            }
        }
        return $resultados;
    }
    public function getResultadosMes($ano)
    {
        $result = filterResultadosMes($this->conMongo, $ano);
        $resultados = array();

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $fecha = strtotime($value->FECHA_RESULTADO);
                switch (date('M', $fecha)) {
                    case 'Jan':
                        if (isset($resultados[0])) {
                            if (isset($resultados[0][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[0][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[0][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[0]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[0]['Suspension Programada'] = 0;
                            $resultados[0]['Suspension Efectiva'] = 0;
                            $resultados[0]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[0][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Feb':
                        if (isset($resultados[1])) {
                            if (isset($resultados[1][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[1][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[1][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[1]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[1]['Suspension Programada'] = 0;
                            $resultados[1]['Suspension Efectiva'] = 0;
                            $resultados[1]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[1][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Mar':
                        if (isset($resultados[2])) {
                            if (isset($resultados[2][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[2][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[2][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[2]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[2]['Suspension Programada'] = 0;
                            $resultados[2]['Suspension Efectiva'] = 0;
                            $resultados[2]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[2][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Apr':
                        if (isset($resultados[3])) {
                            if (isset($resultados[3][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[3][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[3][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[3]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[3]['Suspension Programada'] = 0;
                            $resultados[3]['Suspension Efectiva'] = 0;
                            $resultados[3]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[3][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'May':
                        if (isset($resultados[4])) {
                            if (isset($resultados[4][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[4][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[4][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[4]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[4]['Suspension Programada'] = 0;
                            $resultados[4]['Suspension Efectiva'] = 0;
                            $resultados[4]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[4][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Jun':
                        if (isset($resultados[5])) {
                            if (isset($resultados[5][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[5][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[5][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[5]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[5]['Suspension Programada'] = 0;
                            $resultados[5]['Suspension Efectiva'] = 0;
                            $resultados[5]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[5][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Jul':
                        if (isset($resultados[6])) {
                            if (isset($resultados[6][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[6][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[6][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[6]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[6]['Suspension Programada'] = 0;
                            $resultados[6]['Suspension Efectiva'] = 0;
                            $resultados[6]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[6][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Aug':
                        if (isset($resultados[7])) {
                            if (isset($resultados[7][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[7][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[7][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[7]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[7]['Suspension Programada'] = 0;
                            $resultados[7]['Suspension Efectiva'] = 0;
                            $resultados[7]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[7][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Sep':
                        if (isset($resultados[8])) {
                            if (isset($resultados[8][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[8][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[8][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[8]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[8]['Suspension Programada'] = 0;
                            $resultados[8]['Suspension Efectiva'] = 0;
                            $resultados[8]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[8][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Oct':
                        if (isset($resultados[9])) {
                            if (isset($resultados[9][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[9][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[9][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[9]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[9]['Suspension Programada'] = 0;
                            $resultados[9]['Suspension Efectiva'] = 0;
                            $resultados[9]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[9][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Nov':
                        if (isset($resultados[10])) {
                            if (isset($resultados[10][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[10][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[10][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[10]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[10]['Suspension Programada'] = 0;
                            $resultados[10]['Suspension Efectiva'] = 0;
                            $resultados[10]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[10][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                    case 'Dec':
                        if (isset($resultados[11])) {
                            if (isset($resultados[11][$value->TIPO_INDISPONIBILIDAD])) {
                                $resultados[11][$value->TIPO_INDISPONIBILIDAD] += 1;
                            } else {
                                $resultados[11][$value->TIPO_INDISPONIBILIDAD] = 1;
                            }
                        } else {
                            $resultados[11]['Indisponibilidad a nivel de Nodo'] = 0;
                            $resultados[11]['Suspension Programada'] = 0;
                            $resultados[11]['Suspension Efectiva'] = 0;
                            $resultados[11]['Sin Indisponibilidad Reportada'] = 0;
                            $resultados[11][$value->TIPO_INDISPONIBILIDAD] = 1;
                        }
                        break;
                }
            }
        }
        return $resultados;

    }





    //-----------------------------------para electroguila---------------------------------------------
   
    //consultando saludos iniciales(USO DE CHATBOT)
    public function getEventosScada($fechainicio, $fechafin)
    {
        return filterEventosScada($this->conMongo, $fechainicio, $fechafin);
    }


    //consultando entradas a los menus principales(USO DE CHATBOT)
    public function getResultadoMenus($fechainicio, $fechafin)
    {
        $result = getResultadoMenus($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['menuConsultas'] = 0;
        $response['menuFactura'] = 0;
        $response['menuPagoEnLinea'] = 0;
        $response['menuPuntosDeAtencion'] = 0;
        $response['otros'] = 0;
        $response['menu_asesor'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->TIPOINTERACCION) {
                case 'menuConsultas':
                    $response['menuConsultas'] += 1;
                    break;
                case 'menuFactura':
                    $response['menuFactura'] += 1;
                    break;
                case 'menuPagoEnLinea':
                    $response['menuPagoEnLinea'] += 1;
                    break;
                case 'menuPuntosDeAtencion':
                    $response['menuPuntosDeAtencion'] += 1;
                    break;
                case 'menu_asesor':
                    $response['menu_asesor'] += 1;
                    break;
                case 'otros':
                    $response['otros'] += 1;
                    break;
            }
        }
        return $response;

    }

    public function getCalificaciones($fechainicio, $fechafin)
    {
        $result = getResultadosCalificaciones($this->conMongo, $fechainicio, $fechafin);
        $response = array();        
        $response['Bueno'] = 0;
        $response['Regular'] = 0;
        $response['Malo'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->CALIFICACION) {

                case 'Bueno':
                    $response['Bueno'] += 1;
                    break;
                case 'Regular':
                    $response['Regular'] += 1;
                    break;
                case 'Malo':
                    $response['Malo'] += 1;
                    break;
            }
        }
        return $response;
    }

     
    //consultas por mes (TENDENCIAS)
    public function getConsultasMes($fechainicio, $fechafin)
    {
        $difusionMes = filterConsultasMes($this->conMongo, $fechainicio, $fechafin);
        $response = [];
        $resultadosMeses = [];
        $criterios = [];
        foreach ($difusionMes as $key => $value) {
            $fecha = strtotime($value->FECHA);
            switch (date('M', $fecha)) {
                case 'Jan':
                    if (isset($resultadosMeses[0])) {
                        $resultadosMeses[0] += 1;
                    } else {
                        $resultadosMeses[0] = 1;
                    }
                    break;
                case 'Feb':
                    if (isset($resultadosMeses[1])) {
                        $resultadosMeses += 1;
                    } else {
                        $resultadosMeses = 1;
                    }
                    break;
                case 'Mar':
                    if (isset($resultadosMeses[2])) {
                        $resultadosMeses[2] += 1;
                    } else {
                        $resultadosMeses[2] = 1;
                    }
                    break;
                case 'Apr':
                    if (isset($resultadosMeses[3])) {
                        $resultadosMeses[3] += 1;
                    } else {
                        $resultadosMeses[3] = 1;
                    }
                    break;
                case 'May':
                    if (isset($resultadosMeses[4])) {
                        $resultadosMeses[4] += 1;
                    } else {
                        $resultadosMeses[4] = 1;
                    }
                    break;
                case 'Jun':
                    if (isset($resultadosMeses[5])) {
                        $resultadosMeses[5] += 1;
                    } else {
                        $resultadosMeses[5] = 1;
                    }
                    break;
                case 'Jul':
                    if (isset($resultadosMeses[6])) {
                        $resultadosMeses[6] += 1;
                    } else {
                        $resultadosMeses[6] = 1;
                    }
                    break;
                case 'Aug':
                    if (isset($resultadosMeses[7])) {
                        $resultadosMeses[7] += 1;
                    } else {
                        $resultadosMeses[7] = 1;
                    }
                    break;
                case 'Sep':
                    if (isset($resultadosMeses[8])) {
                        $resultadosMeses[8] += 1;
                    } else {
                        $resultadosMeses[8] = 1;
                    }
                    break;
                case 'Oct':
                    if (isset($resultadosMeses[9])) {
                        $resultadosMeses[9] += 1;
                    } else {
                        $resultadosMeses[9] = 1;
                    }
                    break;
                case 'Nov':
                    if (isset($resultadosMeses[10])) {
                        $resultadosMeses[10] += 1;
                    } else {
                        $resultadosMeses[10] = 1;
                    }
                    break;
                case 'Dec':
                    if (isset($resultadosMeses[11])) {
                        $resultadosMeses[11] += 1;
                    } else {
                        $resultadosMeses[11] = 1;
                    }
                    break;
            }
        }

        $response['meses'] = $resultadosMeses;
        return $response;
    }


    //promedio por dia(TENDENCIAS)
    public function getConsultasPromedioHora($fechainicio, $fechafin)
    {
        return filterPromedioConsulstasHoras($this->conMongo, $fechainicio, $fechafin);
    }


    public function getConsultasFaq($fechainicio, $fechafin){

        //$faq = filterFaq($con, $fechainicio, $fechafin);
        $faq = filterFaq($this->conMongo, $fechainicio, $fechafin);
        $numeroFaq = [];
        $proveedores=0;
        $tarifas=0;
        $hojaDeVida=0;
        $proyectos=0;
        $convenios=0;
        $leyes=0;
        $usuarios=0;
        $alumbrado_Publico=0;
        $normatividad=0;        
        $factura = 0;

        foreach ($faq as $key => $value) {
            $interaccion = $value->TIPOINTERACCION;
            
            if($interaccion=='proveedores'){

                $proveedores += 1;

            }else if($interaccion=='tarifas'){
                                
                $tarifas += 1;

            }else if($interaccion=='hoja de vida'){
                                                
                $hojaDeVida += 1;
                
            }else if($interaccion=='proyectos'){

                $proyectos += 1;                                
                
            }else if($interaccion=='convenios'){

                $convenios += 1;                                                 
                
            }else if($interaccion=='leyes'){
                                                
                $leyes += 1;

            }else if($interaccion=='usuarios'){

                $usuarios += 1;                                                
                
            }else if($interaccion=='alumbrado publico'){

                $alumbrado_Publico += 1;                                                
                
            }else if($interaccion=='normatividad'){

                $normatividad += 1;                                                
                
            }else if($interaccion=='factura'){

                $factura += 1;                                                
                
            }
        }

        $numeroFaq[0] = $proveedores;
        $numeroFaq[1] = $tarifas;
        $numeroFaq[2] = $hojaDeVida;
        $numeroFaq[3] = $proyectos;
        $numeroFaq[4] = $convenios;
        $numeroFaq[5] = $leyes;
        $numeroFaq[6] = $usuarios;
        $numeroFaq[7] = $alumbrado_Publico;
        $numeroFaq[8] = $normatividad;
        $numeroFaq[9] = $factura;

        return $numeroFaq;

    }


    public function getFlujoConversacion($fechainicio, $fechafin){

        //$faq = filterFaq($con, $fechainicio, $fechafin);
        $faq = getAllConsultasFlujo($this->conMongo, $fechainicio, $fechafin);
        $numeroFaq = [];
        
        $menuConsultas=0;
        $menuFactura=0;
        $menuPagoEnLinea=0;
        $menuPuntosDeAtencion=0;
        $consultar_asesor=0;
        $otros=0;
        $tramites=0;
        $proveedores=0;
        $tarifas=0;
        $hoja_de_vida=0;
        $proyectos=0;
        $convenios=0;
        $leyes=0;
        $usuarios=0;
        $normatividad=0;        
        $alumbrado_publico = 0;
        $factura = 0;
        $PagoEnlineaTenerEnCuenta = 0;
        $comoPagoEnLinea = 0;
        $sede_Garzon = 0;
        $sede_LaPlata = 0;
        $sede_Pitalito = 0;
        $sede_Saire = 0;
        //$otros_Telefonos = 0; 
        $menu_asesor= 0;       


        foreach ($faq as $key => $value) {
            $interaccion = $value->TIPOINTERACCION;
            
            if($interaccion=='menuConsultas'){

                $menuConsultas += 1;

            }else if($interaccion=='menuFactura'){
                                
                $menuFactura += 1;

            }else if($interaccion=='menuPagoEnLinea'){
                                                
                $menuPagoEnLinea += 1;
                
            }else if($interaccion=='menuPuntosDeAtencion'){

                $menuPuntosDeAtencion += 1;                                
                
            }else if($interaccion=='consultar asesor'){

                $consultar_asesor += 1;                                                 
                
            }else if($interaccion=='otros'){
                                                
                $otros += 1;

            }else if($interaccion=='tramites'){

                $tramites += 1;                                                
                
            }else if($interaccion=='proveedores'){

                $proveedores += 1;                                                
                
            }else if($interaccion=='tarifas'){

                $tarifas += 1;                                                
                
            }else if($interaccion=='hoja de vida'){

                $hoja_de_vida += 1;                                                
                
            }else if($interaccion=='proyectos'){

                $proyectos += 1;                                                
                
            }
            else if($interaccion=='convenios'){

                $convenios += 1;                                                
                
            }else if($interaccion=='leyes'){

                $leyes += 1;                                                
                
            }else if($interaccion=='usuarios'){

                $usuarios += 1;                                                
                
            }else if($interaccion=='normatividad'){

                $normatividad += 1;                                                
                
            }else if($interaccion=='alumbrado publico'){

                $alumbrado_publico += 1;                                                
                
            }else if($interaccion=='factura'){

                $factura += 1;                                                
                
            }else if($interaccion=='PagoEnlineaTenerEnCuenta'){

                $PagoEnlineaTenerEnCuenta += 1;                                                
                
            }else if($interaccion=='comoPagoEnLinea'){

                $comoPagoEnLinea += 1;                                                
                
            }else if($interaccion=='sede_Garzon'){

                $sede_Garzon += 1;                                                
                
            }else if($interaccion=='sede_LaPlata'){

                $sede_LaPlata += 1;                                                
                
            }else if($interaccion=='sede_Pitalito'){

                $sede_Pitalito += 1;                                                
                
            }else if($interaccion=='sede_Saire'){

                $sede_Saire += 1;                                                
                
            }/*else if($interaccion=='otros_Telefonos'){

                $otros_Telefonos += 1;                                                
                
            }*/else if($interaccion=='menu_asesor'){

                $menu_asesor += 1;                                                
                
            }
        }

        $numeroFaq[0] = $menuConsultas;
        $numeroFaq[1] = $menuFactura;
        $numeroFaq[2] = $menuPagoEnLinea;
        $numeroFaq[3] = $menuPuntosDeAtencion;
        $numeroFaq[4] = $consultar_asesor;
        $numeroFaq[5] = $otros;
        $numeroFaq[6] = $tramites;
        $numeroFaq[7] = $proveedores;
        $numeroFaq[8] = $hoja_de_vida;
        $numeroFaq[9] = $proyectos;
        $numeroFaq[10] = $convenios;
        $numeroFaq[11] = $leyes;
        $numeroFaq[12] = $usuarios;
        $numeroFaq[13] = $normatividad;
        $numeroFaq[14] = $alumbrado_publico;
        $numeroFaq[15] = $factura;
        $numeroFaq[16] = $PagoEnlineaTenerEnCuenta;
        $numeroFaq[17] = $comoPagoEnLinea;
        $numeroFaq[18] = $sede_Garzon;
        $numeroFaq[19] = $sede_LaPlata;
        $numeroFaq[20] = $sede_Pitalito;
        $numeroFaq[21] = $sede_Saire;
        //$numeroFaq[22] = $otros_Telefonos;        
        $numeroFaq[22] = $tarifas; 
        $numeroFaq[23] = $menu_asesor;


        return $numeroFaq;

    }
}
