<?php
//pruebas
//$dbname = "heroku_69tb2th4";
//produccion
//$dbname = "heroku_qqkvqh3x"; //lucy
$dbname = "heroku_69tb2th4"; //electrohuila
/* ini_set('memory_limit', '-1');
set_time_limit(600); */
date_default_timezone_set('America/Bogota');
function executeQuery($con, $sql)
{
    $result = $con->query($sql);
    if ($result) {
        $fetched_data = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($fetched_data, $row);
        }
        return $fetched_data;
    } else {
        return $con->errorInfo()[2];
    }
}

function logQuery($con, $usr, $pwd)
{
    $filter = ['USUARIO' => $usr, 'CONTRASENA' => $pwd];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".usuarios_portalweb", $query);
    $cliente = current($result->toArray());
    return $cliente;
}

function insertLogAcceso($con, $usuario, $tipo_acceso)
{
    $bulk = new MongoDB\Driver\BulkWrite;
    $a = $bulk->insert(
        [
            'FECHA' => new \MongoDB\BSON\UTCDateTime(new \DateTime()),
            'USUARIO' => $usuario,
            'TIPO_ACCESO' => $tipo_acceso,
        ]);
    $result = $con->executeBulkWrite($GLOBALS['dbname'] . '.log_acceso_web', $bulk);
    return $result;
}

//////////////////////////////VALORES PARA MONITOREO

function getRules_Difusion($con)
{
    $filter = [];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".reglas_difusion", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}
function getCantidad_EventosReglas($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
function getCantidad_SwitchsInvalidos($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];

    $Command = new MongoDB\Driver\Command([
        "count" => "log_sinDatos_difusion",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function getEventos_SuspProgramada($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'TIPO_SUSPENSION' => 'programada',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'TIPO_SUSPENSION' => 'programada',
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
function getEventos_SusNoProgramada($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'TIPO_SUSPENSION' => 'no programada',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'TIPO_SUSPENSION' => 'no programada',
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
function getEventos_MensajesEnviados($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'CANTIDAD_DIFUNDIDA' => ['$ne' => 0],
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'CANTIDAD_DIFUNDIDA' => ['$ne' => 0],
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function getEventos_Indisponibilidad($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function getUsuarios_impactados($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'normalizacion',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'normalizacion',
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function filterResultadosIndefinidos($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => ['$nin' => ['indisponibilidad', 'normalizacion']],
            'CANTIDAD_DIFUNDIDA' => ['$ne' => 0],
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => ['$nin' => ['indisponibilidad', 'normalizacion']],
            'CANTIDAD_DIFUNDIDA' => ['$ne' => 0],
        ];
    }
    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
function filterResultadosSinEnvio($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => ['$nin' => ['indisponibilidad', 'normalizacion']],
            'CANTIDAD_DIFUNDIDA' => 0,
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => ['$nin' => ['indisponibilidad', 'normalizacion']],
            'CANTIDAD_DIFUNDIDA' => 0,
        ];
    }
    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function filterConsultasSegmentosUbicacionMunicipio2($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    $switches = array();
    $circuitos = array();
    $circuitosUnique = array();
    $usuarios = array();
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
            'TIPO_SUSPENSION' => 'no programada',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lte' => $fechafin],
            'SGO' => 'indisponibilidad',
            'TIPO_SUSPENSION' => 'no programada',
        ];
    }
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion_prueba2", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}
function filterConsultaMunicipioUbicacion($con, $fechainicio, $fechafin, $reglas)
{
    $Command = '';
    if ($reglas != '') {
        $Command = new MongoDB\Driver\Command([
            'aggregate' => 'log_difusion_niu',
            'pipeline' => [
                [
                    '$match' => [
                        "FECHA_ENVIO_APERTURA" => [
                            '$gte' => $fechainicio, '$lt' => $fechafin,
                        ],
                        "NIU" => ['$ne' => ''],
                        'REGLA' => ['$in' => $reglas],
                    ],
                ],
                [
                    '$lookup' => [
                        'from' => 'usuarios',
                        'localField' => 'NIU',
                        'foreignField' => 'NIU',
                        'as' => 'usuario',
                    ],
                ],
                [
                    '$project' => [
                        'niu' => '$NIU',
                        'municipio' => '$MUNICIPIO',
                        'ubicacion' => '$usuario.UBICACION',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            'municipio' => '$municipio',
                            'ubicacion' => '$ubicacion',
                        ],
                        'suma' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]);
    } else {
        $Command = new MongoDB\Driver\Command([
            'aggregate' => 'log_difusion_niu',
            'pipeline' => [
                [
                    '$match' => [
                        "FECHA_ENVIO_APERTURA" => [
                            '$gte' => $fechainicio, '$lt' => $fechafin,
                        ],
                        "NIU" => ['$ne' => ''],
                    ],
                ],
                [
                    '$lookup' => [
                        'from' => 'usuarios',
                        'localField' => 'NIU',
                        'foreignField' => 'NIU',
                        'as' => 'usuario',
                    ],
                ],
                [
                    '$project' => [
                        'niu' => '$NIU',
                        'municipio' => '$MUNICIPIO',
                        'ubicacion' => '$usuario.UBICACION',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            'municipio' => '$municipio',
                            'ubicacion' => '$ubicacion',
                        ],
                        'suma' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]);
    }

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;

}

function filterConsultaUsuariosXMunicipio($con)
{

    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'usuarios',
        'pipeline' => [
            [
                '$group' => [
                    '_id' => '$MUNICIPIO',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function getAcuse_Recibo($con, $fechainicio, $fechafin)
{
    $fechaInicioSuma = strtotime('+7 hour', strtotime($fechainicio));
    $fechaFinSuma = strtotime('+7 hour', strtotime($fechafin));

    $fechaInicioSuma = date('Y-m-d H:i:s', $fechaInicioSuma);
    $fechaFinSuma = date('Y-m-d H:i:s', $fechaFinSuma);

    $filter = [
        'FECHA_ENTREGA_APERTURA' => ['$gte' => $fechaInicioSuma, '$lte' => $fechaFinSuma],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_prueba_acuse", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}
function getAcuseRecibo_PromocionLucy($con, $fechainicio, $fechafin)
{
    $fechaInicioSuma = strtotime('+7 hour', strtotime($fechainicio));
    $fechaFinSuma = strtotime('+7 hour', strtotime($fechafin));

    $fechaInicioSuma = date('Y-m-d H:i:s', $fechaInicioSuma);
    $fechaFinSuma = date('Y-m-d H:i:s', $fechaFinSuma);

    $filter = [
        'FECHA_ENTREGA' => ['$gte' => $fechaInicioSuma, '$lte' => $fechaFinSuma],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_prueba_acuse", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}

function getAcuseReciboPromocion_Programadas($con, $fechainicio, $fechafin)
{
    $fechaInicioSuma = strtotime('+7 hour', strtotime($fechainicio));
    $fechaFinSuma = strtotime('+7 hour', strtotime($fechafin));

    $fechaInicioSuma = date('Y-m-d H:i:s', $fechaInicioSuma);
    $fechaFinSuma = date('Y-m-d H:i:s', $fechaFinSuma);

    $filter = [
        'FECHA_PROMOCION_PROGRAMADAS' => ['$gte' => $fechaInicioSuma, '$lte' => $fechaFinSuma],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_prueba_acuse", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}



function filterConsultaMunicipio($con, $municipio)
{
    $filter = [
        'MUNICIPIO' => new MongoDB\BSON\Regex($municipio, 'i'),
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".municipios_chec", $query);
    return $result->toArray();

}

function filterDifusionTendencias($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
        ];
    }
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion_prueba2", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

function difusionCantidadDifundida($con, $fechainicio, $fechafin, $reglas)
{
    $response = array();
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_ENVIO_APERTURA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'NIU' => ['$ne' => ''],
            'REGLA' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_ENVIO_APERTURA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'NIU' => ['$ne' => ''],
        ];
    }
    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_niu",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $response['aperturas'] = current($result->toArray());

    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_ENVIO_CIERRE' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'NIU' => ['$ne' => ''],
            'MENSAJE_CIERRE' => 'ok',
            'REGLA' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_ENVIO_CIERRE' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'NIU' => ['$ne' => ''],
            'MENSAJE_CIERRE' => 'ok',
        ];
    }
    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_niu",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $response['cierres'] = current($result->toArray());

    return $response;
}

function getEventos_Normalizacion($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'normalizacion',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'normalizacion',
        ];
    }

    $Command = new MongoDB\Driver\Command([
        "count" => "log_difusion_prueba2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

function filterDifusionUsuariosImpactados($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
        ];
    }
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

/*function filterDifusionMes($con, $fechainicio3, $fechafin3)
{
    $ano = date('Y', strtotime($fechainicio3));
    $filter = [
        'FECHA_EVENTO' => new MongoDB\BSON\Regex($ano, 'i'),
        'SGO' => 'indisponibilidad',
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion_prueba2", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}*/

/*function filterDifusionPromedio($con, $fechainicio, $fechafin)
{

    $filter = [
        'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        'SGO' => 'indisponibilidad',
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion_prueba2", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}*/

/*function filterDifusionPromedio2($con, $fechainicio, $fechafin, $reglas)
{
    $filter = '';
    if ($reglas != '') {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
            'REGLAS' => ['$in' => $reglas],
        ];
    } else {
        $filter = [
            'FECHA_EVENTO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
            'SGO' => 'indisponibilidad',
        ];
    }
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_difusion_prueba2", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}*/

//
//
//
//
//
//

//-------------------------------------- OBTENER VALORES PARA EL MONITOREO ------------------------------

function filterResultadoMenus($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_RESULTADO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_menu_usuarios", $query);
    $respuesta = $result->toArray();
    //var_dump($respuesta);
    return $respuesta;

}

//-------------------------------------- Filtro reportes------------------------------
function filterReporte($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_REPORTE' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        'TELEFONO' => ['$exists' => true],
        'NOMBREUSUARIO' => ['$exists' => true],
    ];
    $Command = new MongoDB\Driver\Command(["count" => "reportes_sgo_chatbot", "query" => $filter]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;

}

//RESULTADOS PARA DISTRIBUCION POR MESES
function filterResultadoDistribucion($con)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_resultados',
        'pipeline' => [
            [
                '$project' => [
                    "mes" => [
                        '$month' => '$FECHA_RESULTADO',
                    ],
                    "tipo" => '$TIPO_INDISPONIBILIDAD',
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'mes' => '$mes',
                        'tipo' => '$tipo',
                    ],
                    'sum' => ['$sum' => 1]],
            ],
            [
                '$sort' => ["_id" => 1],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;

}

//TABLA DE indisp circuito 2
function filterEventosScadaAperturaCierre($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        'TIPOINTERACCION' => 'saludoSkype'
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "interacciones",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
//TABLA DE APERTURAS_INDISP_CIRCUITO
function filterEventosSinCierre($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_HORA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        'ESTADO' => 'APERTURA',
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "aperturas_indisp_circuito",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}
//TABLA DE INDISP_CIRCUITO2
function getEventosAperturas($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_APERTURA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "indisp_circuito2",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

/* TABLA DE BUSQUEDAS DISTRIBUIDAS EN EL TIEMPO */
//CANTIDAD DE REGISTROS TOTALES POR CONTEXTO
function filterBusquedaDistribucion($con, $contexto)
{
    $filter = [
        'CONTEXTO' => $contexto,
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "log_busqueda",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

//CANTIDAD DE REGISTROS TOTALES POR CADA MES POR CONTEXTO
function filterBusquedaDistribucionMeses($con, $contexto)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_busqueda',
        'pipeline' => [
            [
                '$match' => ['CONTEXTO' => $contexto],
            ],
            [
                '$project' => [
                    "mes" => [
                        '$month' => '$FECHABUSQUEDA',
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$mes',
                    'sum' => ['$sum' => 1]],
            ],
            [
                '$sort' => ["_id" => 1],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterCriterioBusqueda($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHABUSQUEDA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_busqueda_usuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;

}

//FILTRO DE CRITERIO E BUSQUEDA POR MESES PARA LA DISTRIBUCION EN GRAFICA
function filterCriterioBusquedaDistribucion($con)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_busqueda',
        'pipeline' => [
            [
                '$project' => [
                    "mes" => [
                        '$month' => '$FECHABUSQUEDA',
                    ],
                    "crit" => '$CRITERIO',
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'mes' => '$mes',
                        'crit' => '$crit',
                    ],
                    'sum' => ['$sum' => 1]],
            ],
            [
                '$sort' => ["_id" => 1],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;

}

function filterIngresoPorHora($con)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_busqueda',
        'pipeline' => [
            [
                '$project' => [
                    "hora_dia" => [
                        '$hour' => '$FECHABUSQUEDA',
                    ],
                ],
            ],
            [
                '$group' => ['_id' => '$hora_dia',
                    'sum' => ['$sum' => 1]],
            ],
            [
                '$sort' => ["_id" => 1],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterIngresoPorDia($con)
{
    $dbname = "heroku_qqkvqh3x";
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_busqueda',
        'pipeline' => [
            [
                '$project' => [
                    "dia_semana" => [
                        '$dayOfWeek' => '$FECHABUSQUEDA',
                    ],
                ],
            ],
            [
                '$group' => ['_id' => '$dia_semana',
                    'sum' => ['$sum' => 1]],
            ],
            [
                '$sort' => ["_id" => 1],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($dbname, $Command);
    $respuesta = $result->toArray();
    return $respuesta;

}

/*function filterCalificaciones($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechainicio)), '$lt' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechafin))],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".calificacion_usuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}*/

//pruebas sgo
function filterPruebasSgo($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_tiempos_sgo", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}
//errores sgo
function filterErrorSgo($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_SGOerrorUsuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}

/*
FILTROS DE SUSPENSIONES POR FECHAS ELEGIDAS DESDE EL FRONT END
 */
function filterSuspProgramadas($con, $fechainicio, $fechafin)
{
    $fechainicio = date("Y-m-d", strtotime($fechainicio));
    $fechafin = date("Y-m-d", strtotime($fechafin));
    //$fin = new DateTime($fechafin);
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_programadas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_INICIO' => [
                            '$gte' => $fechainicio,
                            '$lte' => $fechafin,
                        ],
                        'ESTADO' => 'ABIERTO',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$NIU',
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterSuspNoProgramadas($con, $fechainicio, $fechafin)
{
    $fechainicio = date("Y-m-d", strtotime($fechainicio));
    $fechafin = date("Y-m-d", strtotime($fechafin));

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'indisp_circuito',
            'pipeline' => [
                [
                    '$match' => [
                        'ESTADO' => [
                            '$eq' => 'APERTURA',
                        ],
                        'FECHA_HORA' => [
                            '$gte' => $fechainicio,
                            '$lte' => $fechafin,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '_id',
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterSuspEfectivas($con, $fechainicio, $fechafin)
{
    $fechainicio = date("Y-m-d", strtotime($fechainicio));
    $fechafin = date("Y-m-d", strtotime($fechafin));
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_ATENCION' => [
                            '$gte' => $fechainicio,
                            '$lte' => $fechafin,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '_id',
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

/*
FILTROS DE LAS ULTIMAS SUSPENSIONES PARA OBTENER LA FECHA DE LA ULTIMA ACTUALIZACION
 */
function filterUltimaSuspProgramadas($con)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_programadas',
            'pipeline' => [
                [
                    '$sort' => [
                        'FECHA_INICIO' => -1,
                    ],
                ],
                [
                    '$limit' => 1,
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterUltimaSuspNoProgramadas($con)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'indisp_circuito',
            'pipeline' => [
                [
                    '$sort' => [
                        'FECHA_HORA' => -1,
                    ],
                ],
                [
                    '$limit' => 1,
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterUltimaEfectivas($con)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$sort' => [
                        '_id' => -1,
                    ],
                ],
                [
                    '$limit' => 1,
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

/*
Obtener cantidad de registros de cada mes para la grafica de indisponibilidades totales
 */
function susProgramadas2018($con, $inicio, $fin)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_programadas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_INICIO' => [
                            '$gte' => $inicio,
                            '$lt' => $fin,
                        ],
                        'ESTADO' => [
                            '$eq' => 'ABIERTO',
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '_id',
                        'count' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
function susNoProgramadas2018($con, $inicio, $fin)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'indisp_circuito',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_HORA' => [
                            '$gte' => $inicio,
                            '$lt' => $fin,
                        ],
                        'ESTADO' => [
                            '$eq' => 'APERTURA',
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '_id',
                        'count' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
function susEfectivas2018($con, $inicio, $fin)
{
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_ATENCION' => [
                            '$gte' => $inicio,
                            '$lt' => $fin,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '_id',
                        'count' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterResultadosMes($con, $ano)
{
    //'MUNICIPIO' => new MongoDB\BSON\Regex($municipio, 'i'),

    $filter = [
        'FECHA_RESULTADO' => new MongoDB\BSON\Regex($ano, 'i'),
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_resultados_usuarios", $query);
    $respuesta = $result->toArray();

    return $respuesta;

}

function filterAccesosMenuMes($con, $ano)
{
    $filter = [
        'FECHA_RESULTADO' => new MongoDB\BSON\Regex($ano, 'i'),
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_menu_usuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;

}
//-----------------------------------------------------para electrohuila---------------------------------

//cantidad de saludos iniciales que hace el usurios desde facebook
function filterEventosScada($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
        'TIPOINTERACCION' => 'saludo'
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "interacciones",
        "query" => $filter,
        "cursor" => "stdClass",
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}


//ingreso a los menus
function getResultadoMenus($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();
    return $respuesta;

}


function getResultadosCalificaciones($con, $fechainicio, $fechafin){

    
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".calificacion_usuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filtrarXdiasYhoras($con, $fechainicio, $fechafin)
{

    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],  
        'TIPOINTERACCION' => 'saludo'      
    ];
    
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}


function filterConsultasMes($con, $fechainicio3, $fechafin3)
{
    $ano = date('Y', strtotime($fechainicio3));
    $filter = [
        'FECHA' => new MongoDB\BSON\Regex($ano, 'i'),                
        '$or' => [
            ['TIPOINTERACCION' => 'menuConsultas'],
            ['TIPOINTERACCION' => 'menuFactura'],
            ['TIPOINTERACCION' => 'menuPagoEnLinea'],
            ['TIPOINTERACCION' => 'menuPuntosDeAtencion'],
            ['TIPOINTERACCION' => 'consultar asesor'],
            ['TIPOINTERACCION' => 'otros'],
        ],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

/*function filterPromedioConsulstasHoras($con, $fechainicio, $fechafin){

    $filter = [
        'FECHA'=> ['$gte' => $fechainicio, '$lt'=> $fechafin],
        '$or' => [
            ['TIPOINTERACCION' => 'menuConsultas'],
            ['TIPOINTERACCION' => 'menuFactura'],
            ['TIPOINTERACCION' => 'menuPagoEnLinea'],
            ['TIPOINTERACCION' => 'menuPuntosDeAtencion'],
            ['TIPOINTERACCION' => 'consultar asesor'],
            ['TIPOINTERACCION' => 'otros'],
        ],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}*/

function filterPromedioConsulstasHoras($con, $fechainicio, $fechafin){

    $filter = [
        'FECHA'=> ['$gte' => $fechainicio, '$lt'=> $fechafin],
        '$or' => [
            ['TIPOINTERACCION' => 'saludo'],
        ],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

function filterFaq($con, $fechainicio, $fechafin){

    $filter=[
        'FECHA'=> ['$gte' => $fechainicio, '$lt'=> $fechafin],
        '$or' => [
            ['TIPOINTERACCION' => 'tramites'],
            ['TIPOINTERACCION' => 'proveedores'],
            ['TIPOINTERACCION' => 'tarifas'],
            ['TIPOINTERACCION' => 'hoja de vida'],
            ['TIPOINTERACCION' => 'proyectos'],
            ['TIPOINTERACCION' => 'convenios'],
            ['TIPOINTERACCION' => 'leyes'],
            ['TIPOINTERACCION' => 'usuarios'],
            ['TIPOINTERACCION' => 'normatividad'],
            ['TIPOINTERACCION' => 'alumbrado publico'],
            ['TIPOINTERACCION' => 'factura'],
        ],
    ];
    
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

function getConsultasFacturaFaq($con, $fechainicio, $fechafin){
    $filter = [
        'Fecha' => [$gte=>getConsultasFacturaFaq, $lt=>$fechafin],
        'con_nombre' => 'factura',
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".factura", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}

function getNumFaqFactura($con, $fechainicio, $fechafin)
{
    $filter = [
        'con_nombre' => 'factura',
    ];
    $Command = new MongoDB\Driver\Command(["count" => "factura", "query" => $filter]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;

}

function getAllConsultasFlujo($con, $fechainicio, $fechafin){


    $filter=[
        'FECHA'=> ['$gte' => $fechainicio, '$lt'=> $fechafin],
        '$or' => [

        ['TIPOINTERACCION' => 'menuConsultas'],
        ['TIPOINTERACCION' => 'menuFactura'],
        ['TIPOINTERACCION' => 'menuPagoEnLinea'],
        ['TIPOINTERACCION' => 'menuPuntosDeAtencion'],
        ['TIPOINTERACCION' => 'consultar asesor'],
        ['TIPOINTERACCION' => 'otros'],
        ['TIPOINTERACCION' => 'tramites'],
        ['TIPOINTERACCION' => 'proveedores'],
        ['TIPOINTERACCION' => 'tarifas'],
        ['TIPOINTERACCION' => 'hoja de vida'],
        ['TIPOINTERACCION' => 'proyectos'],
        ['TIPOINTERACCION' => 'convenios'],
        ['TIPOINTERACCION' => 'leyes'],
        ['TIPOINTERACCION' => 'usuarios'],
        ['TIPOINTERACCION' => 'normatividad'],
        ['TIPOINTERACCION' => 'alumbrado publico'],
        ['TIPOINTERACCION' => 'factura'],
        ['TIPOINTERACCION' => 'PagoEnlineaTenerEnCuenta'],
        ['TIPOINTERACCION' => 'comoPagoEnLinea'],
        ['TIPOINTERACCION' => 'sede_Garzon'],
        ['TIPOINTERACCION' => 'sede_LaPlata'],
        ['TIPOINTERACCION' => 'sede_Pitalito'],
        ['TIPOINTERACCION' => 'sede_Saire'],
        //['TIPOINTERACCION' => 'otros_Telefonos'],        
        ['TIPOINTERACCION' => 'menu_asesor'],
        
        ],
    ];

    $query = new MOngoDb\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".interacciones", $query);
    $respuesta = $result->toArray();

    return $respuesta;
}