<?php

ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
$dbname = "heroku_qqkvqh3x";
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

//-------------------------------------- OBTENER VALORES PARA EL MONITOREO ------------------------------
function filterResultado($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_RESULTADO' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_resultados_usuarios", $query);
    $respuesta = $result->toArray();

    return $respuesta;

}

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
function filterResultadoInvocar($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_invocar_lucy", $query);
    $respuesta = $result->toArray();

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
//TABLA DE BUSQUEDAS
function filterBusqueda($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHABUSQUEDA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $Command = new MongoDB\Driver\Command([
        "count" => "log_busqueda_usuarios",
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

function filterCalificaciones($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => $fechainicio, '$lt' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".calificacion_usuarios", $query);
    $respuesta = $result->toArray();
    return $respuesta;
}

function filterConsultasSegmentosUbicacionMunicipio($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_RESULTADO' => [
            '$gte' => $fechainicio,
            '$lt' => $fechafin],
        'NIU' => ['$exists' => true],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_resultados_usuarios", $query);
    $respuesta = $result->toArray();
    $response = array();
    if (count($respuesta) > 0) {
        foreach ($respuesta as $key => $value) {
            $filter = ['NIU' => $value->NIU];
            $query = new MongoDB\Driver\Query($filter);
            $result = $con->executeQuery($GLOBALS['dbname'] . ".usuarios", $query);
            array_push($response, $result->toArray());
        }
    }

    return $response;

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
//get difusion dia hora
function getconsultas_LucyDiaHora($con, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_RESULTADO' => ['$gte' => $fechainicio, '$lte' => $fechafin],
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_menu_usuarios", $query);
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
    $salida = array();
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
                        '_id' => '$ORDEN_OP',
                    ],
                ],
                [
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    //$respuesta = current($result->toArray());
    $salida['total'] = current($result->toArray());

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
                    ],
                ],
                [
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    //$respuesta = current($result->toArray());
    $salida['cuentasDistintas'] = current($result->toArray());

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
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    //$respuesta = current($result->toArray());
    $salida['cuentasRepetidas'] = current($result->toArray());

    return $salida;
}

function filterSuspNoProgramadas($con, $fechainicio, $fechafin)
{
    $salida = array();
    //$fechainicio = date("Y-m-d", strtotime($fechainicio));
    //$fechafin = date("Y-m-d", strtotime($fechafin));

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
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    $salida['total'] = $respuesta;

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
                        '_id' => '$CIRCUITO',
                    ],
                ],
                [
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    $salida['switchesDiferentes'] = $respuesta;

    return $salida;
}

function filterSuspEfectivas($con, $fechainicio, $fechafin)
{
    $salida = array();
    //$fechainicio = date("Y-m-d", strtotime($fechainicio));
    //$fechafin = date("Y-m-d", strtotime($fechafin));
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$match' => [
                        'HORA_INI' => [
                            '$gte' => $fechainicio,
                            '$lte' => $fechafin,
                        ],
                        'VALOR' => 's',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$_id',
                    ],
                ],
                [
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $salida['total'] = current($result->toArray());

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$match' => [
                        'HORA_INI' => [
                            '$gte' => $fechainicio,
                            '$lte' => $fechafin,
                        ],
                        'VALOR' => 's',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$NIU',

                    ],
                ],
                [
                    '$count' => 'total',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $salida['cuentasDistintas'] = current($result->toArray());

    /* $Command = new MongoDB\Driver\Command(
    [
    'aggregate' => 'susp_efectivas',
    'pipeline' => [
    [
    '$match' => [
    'HORA_INI' => [
    '$gte' => $fechainicio,
    '$lte' => $fechafin,
    ],
    'VALOR' => 's',
    ],
    ],
    [
    '$count' => 'total',
    ],
    ],
    'cursor' => new stdClass,
    ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $salida['cuentasRepetidas'] = current($result->toArray()); */

    return $salida;
}

function filterSuspNoProgramadasUsuarios($con, $fechainicio, $fechafin)
{
    $salida = array();
    //$fechainicio = date("Y-m-d", strtotime($fechainicio));
    //$fechafin = date("Y-m-d", strtotime($fechafin));

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
            ],
            'cursor' => new stdClass,
        ]
    );
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();

    $switchesArray = array();
    $switchesArray2 = array();
    foreach ($respuesta as $switches) {
        if (!in_array($switches->CIRCUITO, $switchesArray)) {
            array_push($switchesArray, $switches->CIRCUITO);
        } else {
            array_push($switchesArray2, $switches->CIRCUITO);
        }
    }

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'trafos_switch',
            'pipeline' => [
                [
                    '$match' => [
                        'switches' => [
                            '$in' => $switchesArray,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();

    $trafosArray = array();

    //var_dump($respuesta[0]->trafos);
    foreach ($respuesta as $trafos) {
        array_push($trafosArray, $trafos->trafos);
    }

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'usuarios',
            'pipeline' => [
                [
                    '$match' => [
                        'NODO' => [
                            '$in' => $trafosArray,
                        ],
                    ],
                ],
                [
                    '$count' => 'totalUsuarios',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuestaFinal1 = current($result->toArray());

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'trafos_switch',
            'pipeline' => [
                [
                    '$match' => [
                        'switches' => [
                            '$in' => $switchesArray2,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();

    $trafosArray = array();

    //var_dump($respuesta[0]->trafos);
    foreach ($respuesta as $trafos) {
        array_push($trafosArray, $trafos->trafos);
    }

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'usuarios',
            'pipeline' => [
                [
                    '$match' => [
                        'NODO' => [
                            '$in' => $trafosArray,
                        ],
                    ],
                ],
                [
                    '$count' => 'totalUsuarios',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuestaFinal2 = current($result->toArray());
    if ($respuestaFinal1 && $respuestaFinal2) {

        if (is_numeric($respuestaFinal1->totalUsuarios) && is_numeric($respuestaFinal2->totalUsuarios)) {

            return $respuestaFinal1->totalUsuarios + $respuestaFinal2->totalUsuarios;
        }
    } else {
        return 0;
    }
}

function filterSuspNoProgramadasUsuariosDiferentes($con, $fechainicio, $fechafin)
{
    $salida = array();
    //$fechainicio = date("Y-m-d", strtotime($fechainicio));
    //$fechafin = date("Y-m-d", strtotime($fechafin));

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
                        '_id' => '$CIRCUITO',
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();

    $switchesArray = array();
    foreach ($respuesta as $switches) {
        array_push($switchesArray, $switches->_id);
    }

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'trafos_switch',
            'pipeline' => [
                [
                    '$match' => [
                        'switches' => [
                            '$in' => $switchesArray,
                        ],
                    ],
                ],
            ],
            'cursor' => new stdClass,
        ]
    );
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();

    $trafosArray = array();

    //var_dump($respuesta[0]->trafos);
    foreach ($respuesta as $trafos) {
        array_push($trafosArray, $trafos->trafos);
    }

    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'usuarios',
            'pipeline' => [
                [
                    '$match' => [
                        'NODO' => [
                            '$in' => $trafosArray,
                        ],
                    ],
                ],
                [
                    '$count' => 'totalUsuarios',
                ],
            ],
            'cursor' => new stdClass,
        ]
    );

    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());

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

function filterConsultasMes($con, $ano)
{
    //'MUNICIPIO' => new MongoDB\BSON\Regex($municipio, 'i'),

    $filter = [
        'FECHABUSQUEDA' => new MongoDB\BSON\Regex($ano, 'i'),
    ];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_busqueda_usuarios", $query);
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
