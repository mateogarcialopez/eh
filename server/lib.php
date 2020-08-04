<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'consultas.php';

class ChatbotApi
{
    private $conPostgres;
    private $conMongo;

//Credenciales HEROKU Mlab pruebas
    private $host = "mongodb://heroku_69tb2th4:m2oheamen7422pmnq3htdb56dt@ds113775.mlab.com:13775/heroku_69tb2th4?retryWrites=false";

    //Credenciales HEROKU Mlab produccion
    //private $host = "mongodb://heroku_qqkvqh3x:b50q78jtvojl1aobh01eut4rpj@ds215358-a1.mlab.com:15358/heroku_qqkvqh3x?retryWrites=false";

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

    //----------------------------LOG Y CONTROL DE ACCESO------------------------------------

    public function login($usr, $pwd)
    {
        return logQuery($this->conMongo, $usr, $pwd);
    }

    public function checkPermission($idusr)
    {
        return checkPermQuery($this->conMongo, $idusr);
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

    public function getResultado($tipoIndispo, $fechainicio, $fechafin)
    {
        return filterResultado($this->conMongo,$tipoIndispo,$fechainicio, $fechafin);

    }
    public function getResultadoDistribucion()
    {
        return filterResultadoDistribucion($this->conMongo);

    }
    public function getBusqueda($contexto, $fechainicio, $fechafin)
    {
        return filterBusqueda($this->conMongo, $contexto, $fechainicio, $fechafin);
    }
    public function getBusquedaDistribucion($contexto)
    {
        return filterBusquedaDistribucion($this->conMongo, $contexto);
    }
    public function getBusquedaDistribucionMeses($contexto)
    {
        return filterBusquedaDistribucionMeses($this->conMongo, $contexto);
    }

    public function getCriterioBusqueda($criterio, $fechainicio, $fechafin)
    {
        return filterCriterioBusqueda($this->conMongo, $criterio, $fechainicio, $fechafin);
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
    public function getCalificaciones($calificacion,$fechainicio, $fechafin)
    {
        return filterCalificaciones($this->conMongo,$calificacion, $fechainicio, $fechafin);
    }

    //Para Obtener la cantidad de suspensiones
    public function getSuspProgramadas($fechainicio, $fechafin)
    {
        return filterSuspProgramadas($this->conMongo, $fechainicio, $fechafin);
    }
    public function getSuspNoProgramadas($fechainicio, $fechafin)
    {
        return filterSuspNoProgramadas($this->conMongo, $fechainicio, $fechafin);
    }
    public function getSuspEfectivas($fechainicio, $fechafin)
    {
        return filterSuspEfectivas($this->conMongo, $fechainicio, $fechafin);
    }

    //para obtener el ultimo registro de las suspensiones
    public function getUltimaSuspProgramadas()
    {
        return filterUltimaSuspProgramadas($this->conMongo);
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
    public function getsusProgramadas2018($inicio,$fin)
    {
        return susProgramadas2018($this->conMongo,$inicio,$fin);
    }
    public function getsusNoProgramadas2018($inicio,$fin)
    {
        return susNoProgramadas2018($this->conMongo,$inicio,$fin);
    }
    public function getsusEfectivas2018($inicio,$fin)
    {
        return susEfectivas2018($this->conMongo,$inicio,$fin);
    }
}
