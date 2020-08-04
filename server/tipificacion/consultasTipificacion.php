<?php
//pruebas
//$dbname = "heroku_69tb2th4";
//produccion
$dbname = "heroku_qqkvqh3x";
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

function getConsultas_LucyHora($con, $fechaInicio, $fechaFin)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_resultados_usuarios',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_RESULTADO' => [
                        '$gte' => $fechaInicio,
                        '$lte' => $fechaFin,
                    ],
                ],
            ],
            [
                '$project' => [
                    'hour' => [
                        '$dateToString' => [
                            'format' => '%H',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_RESULTADO',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$hour',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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

function getMensajes_DifusionHora($con, $fechaInicio, $fechaFin)
{
    $fechaInicioSuma = strtotime('+7 hour', strtotime($fechaInicio));
    $fechaFinSuma = strtotime('+7 hour', strtotime($fechaFin));

    $fechaInicioSuma = date('Y-m-d H:i:s', $fechaInicioSuma);
    $fechaFinSuma = date('Y-m-d H:i:s', $fechaFinSuma);

    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_difusion_niu',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_ENVIO_APERTURA' => ['$gte' => $fechaInicio, '$lt' => $fechaFin],
                    'NIU' => ['$ne' => ''],
                ],
            ],
            [
                '$project' => [
                    'hour' => [
                        '$dateToString' => [
                            'format' => '%H',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENVIO_APERTURA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$hour',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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
function get_TipificacionHora($con, $fechaInicio, $fechaFin)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'tipificacion',
        'pipeline' => [
            [
                '$match' => [
                    'Fecha' => [
                        '$gte' => $fechaInicio,
                        '$lte' => $fechaFin,
                    ],
                ],
            ],
            [
                '$project' => [
                    'hora' => [
                        '$split' => ['$Hora', ':'],
                    ],
                ],
            ],
            [
                '$unwind' => [
                    'path' => '$hora',
                    'includeArrayIndex' => 'indice',
                ],
            ],
            [
                '$match' => [
                    'indice' => 0,
                ],
            ],
            [
                '$group' => [
                    '_id' => '$hora',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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
function getConsultas_LucyDia($con, $fechaInicio, $fechaFin)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_resultados_usuarios',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_RESULTADO' => [
                        '$gte' => $fechaInicio,
                        '$lte' => $fechaFin,
                    ],
                ],
            ],
            [
                '$project' => [
                    'day' => [
                        '$dayOfWeek' => [
                            '$dateFromString' => [
                                'dateString' => '$FECHA_RESULTADO',
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$day',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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

function getMensajes_DifusionDia($con, $fechaInicio, $fechaFin)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_difusion_niu',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_ENVIO_APERTURA' => ['$gte' => $fechaInicio, '$lt' => $fechaFin],
                    'NIU' => ['$ne' => ''],
                ],
            ],
            [
                '$project' => [
                    'day' => [
                        '$dayOfWeek' => [
                            '$dateFromString' => [
                                'dateString' => '$FECHA_ENVIO_APERTURA',
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$day',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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
function get_TipificacionDia($con, $fechaInicio, $fechaFin)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'tipificacion',
        'pipeline' => [
            [
                '$match' => [
                    'Fecha' => [
                        '$gte' => $fechaInicio,
                        '$lte' => $fechaFin,
                    ],
                ],
            ],
            [
                '$project' => [
                    'day' => [
                        '$dayOfWeek' => [
                            '$dateFromString' => [
                                'dateString' => '$Fecha',
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$day',
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

function getConsultas_LucyAno($con, $ano)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_resultados_usuarios',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_RESULTADO' => new MongoDB\BSON\Regex($ano, 'i'),
                ],
            ],
            [
                '$project' => [
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_RESULTADO',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$month',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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

function getMensajes_DifusionAno($con, $ano)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_difusion_niu',
        'pipeline' => [
            [
                '$match' => [
                    'FECHA_ENVIO_APERTURA' => new MongoDB\BSON\Regex($ano, 'i'),
                    'NIU' => ['$ne' => ''],
                ],
            ],
            [
                '$project' => [
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENVIO_APERTURA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$month',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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
function get_TipificacionAno($con, $ano)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'tipificacion',
        'pipeline' => [
            [
                '$match' => [
                    'Fecha' => new MongoDB\BSON\Regex($ano, 'i'),
                ],
            ],
            [
                '$project' => [
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$Fecha',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$group' => [
                    '_id' => '$month',
                    'cantidad' => [
                        '$sum' => 1,
                    ],
                ],
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

function get_ConsultasLucy_Dif_llam_MesAno($con, $ano, $mes, $campo, $coleccion)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => $coleccion,
        'pipeline' => [
            [
                '$project' => [
                    'ano' => [

                        '$year' => [
                            '$dateFromString' => [
                                'dateString' => "$campo",
                            ],
                        ],
                    ],
                    'mes' => [
                        '$month' => [
                            '$dateFromString' => [
                                'dateString' => "$campo",
                            ],
                        ],
                    ],

                    'dia' => [
                        '$dayOfMonth' => [
                            '$dateFromString' => [
                                'dateString' => "$campo",
                            ],
                        ],
                    ],

                ],
            ],
            [
                '$match' => [
                    'ano' => (int)$ano,
                    'mes' => (int)$mes,
                ],
            ],
            [
                '$group' => [
                    '_id' => '$dia',
                    'suma' => [
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
