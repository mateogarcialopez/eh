<?php

ini_set('display_errors', '1');
$dbname = "heroku_qqkvqh3x";
date_default_timezone_set('America/Bogota');

function getNiuSenderId($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_sender_niu',
        'pipeline' => [
            [
                '$project' => [
                    'source' => '$SOURCE',
                    'idconversacion' => '$IDCONVERSATION',
                    'niu' => '$NIU',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}

function getcalnegativas($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'calificacion_usuarios',
        'pipeline' => [
            [
                '$project' => [
                    'calificacion' => '$CALIFICACION',
                    'fecha' => '$FECHA',
                    'voc' => '$VOC',
                    'source' => '$SOURCE',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                    'calificacion' => [
                        '$in' => [
                            new MongoDB\BSON\Regex('REGULAR', 'i'),
                            new MongoDB\BSON\Regex('MALO', 'i'),
                        ],
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
function getUsuarios_Segmentos($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_sender_niu',
        'pipeline' => [
            [
                '$lookup' => [
                    'from' => 'usuarios',
                    'localField' => 'NIU',
                    'foreignField' => 'NIU',
                    'as' => 'usuario',
                ],
            ],
            [
                '$match' => [
                    'usuario.SEGMENTO' => [
                        '$exists' => true,
                    ],
                ],
            ],
            [
                '$project' => [
                    'source' => '$SOURCE',
                    'niu' => '$NIU',
                    'segmento' => '$usuario.SEGMENTO',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
function getSegmento_Difusion($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_difusion_niu',
        'pipeline' => [
            [
                '$match' => [
                    'NIU' => [
                        '$ne' => '',
                    ],
                ],
            ],
            [
                '$project' => [
                    'niu' => '$NIU',
                    'segmento' => '$SEGMENTO',
                    'clase_servicio' => '$CLASE_SERVICIO',
                    'municipio' => '$MUNICIPIO',
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
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
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
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
function getAcuseRecibo_Difusion($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_prueba_acuse',
        'pipeline' => [
            [
                '$project' => [
                    'niu' => '$NIU',
                    'apertura' => '$APERTURA',
                    'telefono' => '$TELEFONO',
                    'estadoPromocion' => '$ESTADO_ENVIO',
                    'estadoApertura' => '$ESTADO_APERTURA',
                    'estadoPromocionProgramadas' => '$ESTADO_PROMOCION_PROGRAMADAS',
                    'fechaPromocionProgramadas' => '$FECHA_PROMOCION_PROGRAMADAS',
                    'estadoCierre' => '$ESTADO_CIERRE',
                    'fechaPromocion' => '$FECHA_ENTREGA',
                    'fechaApertura' => '$FECHA_ENTREGA_APERTURA',
                    'fechaCierre' => '$FECHA_ENTREGA_CIERRE',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENTREGA',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENTREGA',
                                ],
                            ],
                        ],
                    ],
                    'month2' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENTREGA_APERTURA',
                                ],
                            ],
                        ],
                    ],
                    'year2' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_ENTREGA_APERTURA',
                                ],
                            ],
                        ],
                    ],
                    'month3' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_PROMOCION_PROGRAMADAS',
                                ],
                            ],
                        ],
                    ],
                    'year3' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA_PROMOCION_PROGRAMADAS',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$match' => [
                    '$or' => [
                        [
                            'month' => $month,
                            'year' => $year,
                        ],
                        [
                            'month2' => $month,
                            'year2' => $year,
                        ],
                        [
                            'month3' => $month,
                            'year3' => $year,
                        ],
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
function getSwitches_Inexistentes($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_sinDatos_difusion',
        'pipeline' => [
            [
                '$project' => [
                    'fecha' => '$FECHA',
                    'tipo' => '$TIPO',
                    'switch' => '$SWITCH',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$FECHA',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
function getConsultas_Usuarios($con, $month, $year)
{
    $Command = new MongoDB\Driver\Command([
        'aggregate' => 'log_sender_niu',
        'pipeline' => [
            [
                '$group' => [
                    '_id' => '$IDCONVERSATION',
                    'suma' => [
                        '$sum' => 1
                    ],
                    'niu' => [
                        '$first' => '$NIU'
                    ],
                    'fecha' => [
                        '$first' => '$FECHA'
                    ],
                    'source' => [
                        '$first' => '$SOURCE'
                    ],
                ],
            ],
            [
                '$project' => [
                    'fecha' => '$fecha',
                    'source' => '$source',
                    'idconversation' => '$_id',
                    'niu' => '$niu',
                    'total' => '$suma',
                    'month' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$fecha',
                                ],
                            ],
                        ],
                    ],
                    'year' => [
                        '$dateToString' => [
                            'format' => '%Y',
                            'date' => [
                                '$dateFromString' => [
                                    'dateString' => '$fecha',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$lookup' => [
                    'from' => 'usuarios',
                    'localField' => 'niu',
                    'foreignField' => 'NIU',
                    'as' => 'usuario',
                ],
            ],
            [
                '$match' => [
                    'month' => $month,
                    'year' => $year,
                ],
            ],
        ],
        'cursor' => new stdClass,
    ]);
    $result = $con->executeCommand($GLOBALS['dbname'], $Command);
    $respuesta = $result->toArray();
    return $respuesta;
}
