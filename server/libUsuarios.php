<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'consultasUsuarios.php';

class ChatbotApi
{
    private $conPostgres;
    private $conMongo;

//Credenciales HEROKU Mlab pruebas
    //private $host = "mongodb://heroku_69tb2th4:m2oheamen7422pmnq3htdb56dt@ds113775.mlab.com:13775/heroku_69tb2th4";

    //Credenciales HEROKU Mlab produccion
    private $host = "mongodb://heroku_qqkvqh3x:b50q78jtvojl1aobh01eut4rpj@ds215358-a1.mlab.com:15358/heroku_qqkvqh3x";

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

    //------------------------- FILTER PARA EL MONITOREO ------------------------------------------

    public function getResultado($fechainicio, $fechafin)
    {
        $response = array();
        $response['Nodo'] = 0;
        $response['Programada'] = 0;
        $response['Efectiva'] = 0;
        $response['SinIndis'] = 0;

        $response['SiCuenta'] = 0;
        $response['NoCuenta'] = 0;
        $response['SiCedula'] = 0;
        $response['NoCedula'] = 0;
        $response['SiNit'] = 0;
        $response['NoNit'] = 0;
        $response['SiDireccion'] = 0;
        $response['NoDireccion'] = 0;
        $response['SiNombre'] = 0;
        $response['NoNombre'] = 0;
        $response['SiTelefono'] = 0;
        $response['NoTelefono'] = 0;

        $report = filterReporte($this->conMongo, $fechainicio, $fechafin);
        $response['SiReporte'] = $report->n;

        $result = filterResultado($this->conMongo, $fechainicio, $fechafin);

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                switch ($value->TIPO_INDISPONIBILIDAD) {
                    case 'Ningun Resultado de Consulta por Cuenta':
                        $response['NoCuenta'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por Cuenta':
                        $response['SiCuenta'] += 1;
                        break;
                    case 'Ningun Resultado de Consulta por Cedula':
                        $response['NoCedula'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por Cedula':
                        $response['SiCedula'] += 1;
                        break;
                    case 'Ningun Resultado de Consulta por NIT':
                        $response['NoNit'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por NIT':
                        $response['SiNit'] += 1;
                        break;
                    case 'Ningun Resultado de Consulta por Direccion':
                        $response['NoDireccion'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por Direccion':
                        $response['SiDireccion'] += 1;
                        break;
                    case 'Ningun Resultado de Consulta por Nombre':
                        $response['NoNombre'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por Nombre':
                        $response['SiNombre'] += 1;
                        break;
                    case 'Ningun Resultado de Consulta por Telefono':
                        $response['NoTelefono'] += 1;
                        break;
                    case 'Mas de 1 Resultado de Consulta por Telefono':
                        $response['SiTelefono'] += 1;
                        break;
                    case 'Indisponibilidad a nivel de Nodo':
                        $response['Nodo'] += 1;
                        break;
                    case 'Suspension Programada':
                        $response['Programada'] += 1;
                        break;
                    case 'Suspension Efectiva':
                        $response['Efectiva'] += 1;
                        break;
                    case 'Sin Indisponibilidad Reportada':
                        $response['SinIndis'] += 1;
                        break;
                }
            }
        }
        return $response;

    }
    public function getResultadoMenus($fechainicio, $fechafin)
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

    }
    public function getResultadoInvocar($fechainicio, $fechafin)
    {
        $result = filterResultadoInvocar($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['facebook'] = 0;
        $response['telegram'] = 0;
        $response['skype'] = 0;
        foreach ($result as $key => $value) {
            switch ($value->SOURCE) {
                case 'facebook':
                    $response['facebook'] += 1;
                    break;
                case 'telegram':
                    $response['telegram'] += 1;
                    break;
                case 'skype':
                    $response['skype'] += 1;
                    break;
            }
        }
        return $response;

    }

    public function getConsultasSegmentosUbicacionMunicipio($fechainicio, $fechafin)
    {
        $result = filterConsultasSegmentosUbicacionMunicipio($this->conMongo, $fechainicio, $fechafin);
        $respuestaFinal = array();
        $responseSeg = array();
        $responseSeg['hogares'] = 0;
        $responseSeg['empresas'] = 0;
        $responseSeg['grandesClientes'] = 0;
        $responseSeg['gobierno'] = 0;
        $responseUbi = array();
        $responseUbi['urbano'] = 0;
        $responseUbi['rural'] = 0;

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

        if (count($result) > 0) {

            foreach ($result as $key => $value) {

                switch ($value[0]->SEGMENTO) {
                    case 'Hogares':
                        $responseSeg['hogares'] += 1;
                        break;
                    case 'Empresas':
                        $responseSeg['empresas'] += 1;
                        break;
                    case 'Grandes Clientes':
                        $responseSeg['grandesClientes'] += 1;
                        break;
                    case 'Gobierno':
                        $responseSeg['gobierno'] += 1;
                        break;
                }

                switch ($value[0]->UBICACION) {
                    case 'U':
                        $responseUbi['urbano'] += 1;
                        break;
                    case 'R':
                        $responseUbi['rural'] += 1;
                        break;
                }

                $municipio = strtoupper($value[0]->MUNICIPIO);
                if (isset($municipiosArray[$municipio])) {
                    $municipiosArray[$municipio] = $municipiosArray[$municipio] + 1;
                    switch ($value[0]->UBICACION) {
                        case 'U':
                            $responseMunicipioUrbano[$municipio] = $responseMunicipioUrbano[$municipio] + 1;
                            break;
                        case 'R':
                            $responseMunicipioRural[$municipio] = $responseMunicipioRural[$municipio] + 1;
                            break;
                    }

                } else {
                    $cabecera = filterConsultaMunicipio($this->conMongo, $municipio);
                    $cabecera = strtoupper($cabecera[0]->MUNICIPIO_CABECERA);
                    //$cabecera = filterConsultaMunicipio($this->conMongo, $value[0]->MUNICIPIO);
                    if (isset($municipiosArray[$cabecera])) {
                        $municipiosArray[$cabecera] += 1;
                        switch ($value[0]->UBICACION) {
                            case 'U':
                                $responseMunicipioUrbano[$cabecera] = $responseMunicipioUrbano[$cabecera] + 1;
                                break;
                            case 'R':
                                $responseMunicipioRural[$cabecera] = $responseMunicipioRural[$cabecera] + 1;
                                break;
                        }
                    }
                }
            }
            $respuestaFinal['segmentos'] = $responseSeg;
            $respuestaFinal['ubicacion'] = $responseUbi;
            $respuestaFinal['municipio'] = $municipiosArray;
            $respuestaFinal['municipioUrbano'] = $responseMunicipioUrbano;
            $respuestaFinal['municipioRural'] = $responseMunicipioRural;

            return $respuestaFinal;
        } else {
            $respuestaFinal['segmentos'] = $responseSeg;
            $respuestaFinal['ubicacion'] = $responseUbi;
            $respuestaFinal['municipio'] = $municipiosArray;
            $respuestaFinal['municipioUrbano'] = $responseMunicipioUrbano;
            $respuestaFinal['municipioRural'] = $responseMunicipioRural;
        }
        return $respuestaFinal;

    }
    public function getResultadoDistribucion()
    {
        return filterResultadoDistribucion($this->conMongo);

    }
    public function getBusqueda($fechainicio, $fechafin)
    {
        return filterBusqueda($this->conMongo, $fechainicio, $fechafin);
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
    public function getCalificaciones($fechainicio, $fechafin)
    {
        $result = filterCalificaciones($this->conMongo, $fechainicio, $fechafin);
        $response = array();
        $response['excelente'] = 0;
        $response['bueno'] = 0;
        $response['regular'] = 0;
        $response['malo'] = 0;
        foreach ($result as $key => $value) {
            switch (strtoupper($value->CALIFICACION)) {
                case 'EXCELENTE':
                    $response['excelente'] += 1;
                    break;
                case '😍 EXCELENTE':
                    $response['excelente'] += 1;
                    break;
                case 'BUENO':
                    $response['bueno'] += 1;
                    break;
                case '😁 BUENO':
                    $response['bueno'] += 1;
                    break;
                case 'REGULAR':
                    $response['regular'] += 1;
                    break;
                case '😐 REGULAR':
                    $response['regular'] += 1;
                    break;
                case 'MALO':
                    $response['malo'] += 1;
                    break;
                case '😣 MALO':
                    $response['malo'] += 1;
                    break;
            }
        }
        return $response;
    }

    //Para Obtener la cantidad de suspensiones
    public function getSuspProgramadas($fechainicio, $fechafin)
    {
        $respProgramadas = filterSuspProgramadas($this->conMongo, $fechainicio, $fechafin);

        return $respProgramadas;
    }

    public function getSuspNoProgramadas($fechainicio, $fechafin)
    {
        $resNoProgra = filterSuspNoProgramadas($this->conMongo, $fechainicio, $fechafin);

        return $resNoProgra;
    }
    public function getSuspNoProgramadasUsuarios($fechainicio, $fechafin)
    {
        $resNoProgra = filterSuspNoProgramadasUsuarios($this->conMongo, $fechainicio, $fechafin);

        return $resNoProgra;
    }
    public function getSuspNoProgramadasUsuariosDiferentes($fechainicio, $fechafin)
    {
        $resNoProgra = filterSuspNoProgramadasUsuariosDiferentes($this->conMongo, $fechainicio, $fechafin);

        return $resNoProgra;
    }
    public function getSuspEfectivas($fechainicio, $fechafin)
    {

        $resEfectivas = filterSuspEfectivas($this->conMongo, $fechainicio, $fechafin);

        return $resEfectivas;
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
    //para obtener las consultas realizadas a lucy
    public function getconsultasLucyDiaHora($fechainicio, $fechafin)
    {
        return getconsultas_LucyDiaHora($this->conMongo, $fechainicio, $fechafin);
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

    // CONSULK6TAS POR ANO TENDENCIAS
    //consultas por mes
    public function getConsultasMes($ano)
    {
        $result = filterConsultasMes($this->conMongo, $ano);
        $response = [];
        $resultadosMeses = [];
        $resultadosCriterio = array();
        $criterios = [];
        foreach ($result as $key => $value) {
            $fecha = strtotime($value->FECHABUSQUEDA);
            switch (date('M', $fecha)) {
                case 'Jan':
                    if (isset($resultadosMeses[0])) {
                        $resultadosMeses[0] += 1;
                    } else {
                        $resultadosMeses[0] = 1;
                    }
                    if (isset($resultadosCriterio[0])) {
                        if (isset($resultadosCriterio[0][$value->CRITERIO])) {
                            $resultadosCriterio[0][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[0][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[0]['niu'] = 0;
                        $resultadosCriterio[0]['cedula'] = 0;
                        $resultadosCriterio[0]['nit'] = 0;
                        $resultadosCriterio[0]['direccion'] = 0;
                        $resultadosCriterio[0]['nombre'] = 0;
                        $resultadosCriterio[0]['telefono'] = 0;
                        $resultadosCriterio[0][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Feb':
                    if (isset($resultadosMeses[1])) {
                        $resultadosMeses[1] += 1;
                    } else {
                        $resultadosMeses[1] = 1;
                    }
                    if (isset($resultadosCriterio[1])) {
                        if (isset($resultadosCriterio[1][$value->CRITERIO])) {
                            $resultadosCriterio[1][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[1][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[1]['niu'] = 0;
                        $resultadosCriterio[1]['cedula'] = 0;
                        $resultadosCriterio[1]['nit'] = 0;
                        $resultadosCriterio[1]['direccion'] = 0;
                        $resultadosCriterio[1]['nombre'] = 0;
                        $resultadosCriterio[1]['telefono'] = 0;
                        $resultadosCriterio[1][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Mar':
                    if (isset($resultadosMeses[2])) {
                        $resultadosMeses[2] += 1;
                    } else {
                        $resultadosMeses[2] = 1;
                    }
                    if (isset($resultadosCriterio[2])) {
                        if (isset($resultadosCriterio[2][$value->CRITERIO])) {
                            $resultadosCriterio[2][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[2][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[2]['niu'] = 0;
                        $resultadosCriterio[2]['cedula'] = 0;
                        $resultadosCriterio[2]['nit'] = 0;
                        $resultadosCriterio[2]['direccion'] = 0;
                        $resultadosCriterio[2]['nombre'] = 0;
                        $resultadosCriterio[2]['telefono'] = 0;
                        $resultadosCriterio[2][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Apr':
                    if (isset($resultadosMeses[3])) {
                        $resultadosMeses[3] += 1;
                    } else {
                        $resultadosMeses[3] = 1;
                    }
                    if (isset($resultadosCriterio[3])) {
                        if (isset($resultadosCriterio[3][$value->CRITERIO])) {
                            $resultadosCriterio[3][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[3][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[3]['niu'] = 0;
                        $resultadosCriterio[3]['cedula'] = 0;
                        $resultadosCriterio[3]['nit'] = 0;
                        $resultadosCriterio[3]['direccion'] = 0;
                        $resultadosCriterio[3]['nombre'] = 0;
                        $resultadosCriterio[3]['telefono'] = 0;
                        $resultadosCriterio[3][$value->CRITERIO] = 1;
                    }
                    break;
                case 'May':
                    if (isset($resultadosMeses[4])) {
                        $resultadosMeses[4] += 1;
                    } else {
                        $resultadosMeses[4] = 1;
                    }
                    if (isset($resultadosCriterio[4])) {
                        if (isset($resultadosCriterio[4][$value->CRITERIO])) {
                            $resultadosCriterio[4][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[4][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[4]['niu'] = 0;
                        $resultadosCriterio[4]['cedula'] = 0;
                        $resultadosCriterio[4]['nit'] = 0;
                        $resultadosCriterio[4]['direccion'] = 0;
                        $resultadosCriterio[4]['nombre'] = 0;
                        $resultadosCriterio[4]['telefono'] = 0;
                        $resultadosCriterio[4][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Jun':
                    if (isset($resultadosMeses[5])) {
                        $resultadosMeses[5] += 1;
                    } else {
                        $resultadosMeses[5] = 1;
                    }
                    if (isset($resultadosCriterio[5])) {
                        if (isset($resultadosCriterio[5][$value->CRITERIO])) {
                            $resultadosCriterio[5][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[5][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[5]['niu'] = 0;
                        $resultadosCriterio[5]['cedula'] = 0;
                        $resultadosCriterio[5]['nit'] = 0;
                        $resultadosCriterio[5]['direccion'] = 0;
                        $resultadosCriterio[5]['nombre'] = 0;
                        $resultadosCriterio[5]['telefono'] = 0;
                        $resultadosCriterio[5][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Jul':
                    if (isset($resultadosMeses[6])) {
                        $resultadosMeses[6] += 1;
                    } else {
                        $resultadosMeses[6] = 1;
                    }
                    if (isset($resultadosCriterio[6])) {
                        if (isset($resultadosCriterio[6][$value->CRITERIO])) {
                            $resultadosCriterio[6][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[6][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[6]['niu'] = 0;
                        $resultadosCriterio[6]['cedula'] = 0;
                        $resultadosCriterio[6]['nit'] = 0;
                        $resultadosCriterio[6]['direccion'] = 0;
                        $resultadosCriterio[6]['nombre'] = 0;
                        $resultadosCriterio[6]['telefono'] = 0;
                        $resultadosCriterio[6][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Aug':
                    if (isset($resultadosMeses[7])) {
                        $resultadosMeses[7] += 1;
                    } else {
                        $resultadosMeses[7] = 1;
                    }
                    if (isset($resultadosCriterio[7])) {
                        if (isset($resultadosCriterio[7][$value->CRITERIO])) {
                            $resultadosCriterio[7][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[7][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[7]['niu'] = 0;
                        $resultadosCriterio[7]['cedula'] = 0;
                        $resultadosCriterio[7]['nit'] = 0;
                        $resultadosCriterio[7]['direccion'] = 0;
                        $resultadosCriterio[7]['nombre'] = 0;
                        $resultadosCriterio[7]['telefono'] = 0;
                        $resultadosCriterio[7][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Sep':
                    if (isset($resultadosMeses[8])) {
                        $resultadosMeses[8] += 1;
                    } else {
                        $resultadosMeses[8] = 1;
                    }
                    if (isset($resultadosCriterio[8])) {
                        if (isset($resultadosCriterio[8][$value->CRITERIO])) {
                            $resultadosCriterio[8][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[8][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[8]['niu'] = 0;
                        $resultadosCriterio[8]['cedula'] = 0;
                        $resultadosCriterio[8]['nit'] = 0;
                        $resultadosCriterio[8]['direccion'] = 0;
                        $resultadosCriterio[8]['nombre'] = 0;
                        $resultadosCriterio[8]['telefono'] = 0;
                        $resultadosCriterio[8][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Oct':
                    if (isset($resultadosMeses[9])) {
                        $resultadosMeses[9] += 1;
                    } else {
                        $resultadosMeses[9] = 1;
                    }
                    if (isset($resultadosCriterio[9])) {
                        if (isset($resultadosCriterio[9][$value->CRITERIO])) {
                            $resultadosCriterio[9][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[9][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[9]['niu'] = 0;
                        $resultadosCriterio[9]['cedula'] = 0;
                        $resultadosCriterio[9]['nit'] = 0;
                        $resultadosCriterio[9]['direccion'] = 0;
                        $resultadosCriterio[9]['nombre'] = 0;
                        $resultadosCriterio[9]['telefono'] = 0;
                        $resultadosCriterio[9][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Nov':
                    if (isset($resultadosMeses[10])) {
                        $resultadosMeses[10] += 1;
                    } else {
                        $resultadosMeses[10] = 1;
                    }
                    if (isset($resultadosCriterio[10])) {
                        if (isset($resultadosCriterio[10][$value->CRITERIO])) {
                            $resultadosCriterio[10][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[10][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[10]['niu'] = 0;
                        $resultadosCriterio[10]['cedula'] = 0;
                        $resultadosCriterio[10]['nit'] = 0;
                        $resultadosCriterio[10]['direccion'] = 0;
                        $resultadosCriterio[10]['nombre'] = 0;
                        $resultadosCriterio[10]['telefono'] = 0;
                        $resultadosCriterio[10][$value->CRITERIO] = 1;
                    }
                    break;
                case 'Dec':
                    if (isset($resultadosMeses[11])) {
                        $resultadosMeses[11] += 1;
                    } else {
                        $resultadosMeses[11] = 1;
                    }
                    if (isset($resultadosCriterio[11])) {
                        if (isset($resultadosCriterio[11][$value->CRITERIO])) {
                            $resultadosCriterio[11][$value->CRITERIO] += 1;
                        } else {
                            $resultadosCriterio[11][$value->CRITERIO] = 1;
                        }
                    } else {
                        $resultadosCriterio[11]['niu'] = 0;
                        $resultadosCriterio[11]['cedula'] = 0;
                        $resultadosCriterio[11]['nit'] = 0;
                        $resultadosCriterio[11]['direccion'] = 0;
                        $resultadosCriterio[11]['nombre'] = 0;
                        $resultadosCriterio[11]['telefono'] = 0;
                        $resultadosCriterio[11][$value->CRITERIO] = 1;
                    }
                    break;
            }
        }
        $response['meses'] = $resultadosMeses;
        $response['criterio'] = $resultadosCriterio;
        return $response;
    }
}
