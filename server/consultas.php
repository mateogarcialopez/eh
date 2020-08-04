<?php
//pruebas
//$dbname = "heroku_69tb2th4";
//produccion
//$dbname = "heroku_qqkvqh3x";
$dbname = "heroku_69tb2th4";

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

function checkPermQuery($con, $idusr)
{
    $filter = ['IDUSUARIO' => $idusr];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".usuarios_portalweb", $query);
    $user = current($result->toArray());
    return $user;
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
function filterResultado($con, $tipoIndispo, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA_RESULTADO' => ['$gte' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechainicio)), '$lt' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechafin))],
        'TIPO_INDISPONIBILIDAD' => new MongoDB\BSON\Regex($tipoIndispo, 'i'),
    ];
    $Command = new MongoDB\Driver\Command(["count" => "log_resultados", "query" => $filter]);
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
function filterBusqueda($con, $contexto, $fechainicio, $fechafin)
{
    $filter = [
        'FECHABUSQUEDA' => ['$gte' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechainicio)), '$lt' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechafin))],
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

function filterCriterioBusqueda($con, $criterio, $fechainicio, $fechafin)
{
    /* $filter = ['CRITERIO' => $criterio];
    $query = new MongoDB\Driver\Query($filter);
    $result = $con->executeQuery($GLOBALS['dbname'] . ".log_busqueda", $query);
    $respuesta = current($result->toArray());
    return $respuesta; */

    $filter = [
        'FECHABUSQUEDA' => ['$gte' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechainicio)), '$lt' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechafin))],
        'CRITERIO' => $criterio,
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

function filterCalificaciones($con, $calificacion, $fechainicio, $fechafin)
{
    $filter = [
        'FECHA' => ['$gte' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechainicio)), '$lt' => new \MongoDB\BSON\UTCDateTime(new \DateTime($fechafin))],
        'CALIFICACION' => $calificacion,
    ];
    $Command = new MongoDB\Driver\Command(["count" => "calificacion", "query" => $filter]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = current($result->toArray());
    return $respuesta;
}

/*
FILTROS DE SUSPENSIONES POR FECHAS ELEGIDAS DESDE EL FRONT END
 */
function filterSuspProgramadas($con, $fechainicio, $fechafin)
{
    $inicio = new DateTime($fechainicio);
    //$fin = new DateTime($fechafin);
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_programadas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_INICIO' => [
                            '$gte' => $inicio->format('Y-m-d'),
                            '$lte' => $fechafin,
                        ],
                        'ESTADO' => 'ABIERTO',
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

function filterSuspNoProgramadas($con, $fechainicio, $fechafin)
{
    $inicio = new DateTime($fechainicio);
    //$fin = new DateTime($fechafin);

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
                            '$gte' => $inicio->format('Y-m-d'),
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
    $inicio = new DateTime($fechainicio);
    $fin = new DateTime($fechafin);
    $Command = new MongoDB\Driver\Command(
        [
            'aggregate' => 'susp_efectivas',
            'pipeline' => [
                [
                    '$match' => [
                        'FECHA_ATENCION' => [
                            /* '$gte' => $inicio->format('Y/m/d'),
                            '$lte' => $fin->format('Y/m/d'), */
                            '$gte' => $inicio->format('d/m/Y'),
                            //'$lte' => $fin->format('d/m/Y'),
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
