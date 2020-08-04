<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/* ini_set('memory_limit', '-1'); */
/* set_time_limit(600); */
require 'consultasTipificacion.php';

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
    public function getConsultasLucyHora($fechaInicio, $fechaFin){

        return getConsultas_LucyHora($this->conMongo,$fechaInicio, $fechaFin);
    }
    public function getMensajesDifusionHora($fechaInicio, $fechaFin){

        return getMensajes_DifusionHora($this->conMongo,$fechaInicio, $fechaFin);
    }
    public function getTipificacionHora($fechaInicio, $fechaFin){

        return get_TipificacionHora($this->conMongo,$fechaInicio, $fechaFin);
    }
    public function getConsultasLucyDia($fechaInicio, $fechaFin){

        return getConsultas_LucyDia($this->conMongo,$fechaInicio, $fechaFin);
    }
    public function getMensajesDifusionDia($fechaInicio, $fechaFin){

        return getMensajes_DifusionDia($this->conMongo,$fechaInicio, $fechaFin);
    }
    public function getTipificacionDia($fechaInicio, $fechaFin){

        return get_TipificacionDia($this->conMongo,$fechaInicio, $fechaFin);
    }

    public function getConsultasLucyAno($ano){

        return getConsultas_LucyAno($this->conMongo,$ano);
    }
    public function getMensajesDifusionAno($ano){

        return getMensajes_DifusionAno($this->conMongo,$ano);
    }
    public function getTipificacionAno($ano){

        return get_TipificacionAno($this->conMongo,$ano);
    }
    public function get_ConsultasLucyDifllamMesAno($ano, $mes, $campo, $coleccion){

        return get_ConsultasLucy_Dif_llam_MesAno($this->conMongo,$ano, $mes, $campo, $coleccion);
    }

}
