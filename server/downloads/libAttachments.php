<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '2048M');

require 'consultasAttachments.php';

class ApiAttachments
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

    public function getNiuSenderId($month, $year)
    {
        $niuSenderId = getNiuSenderId($this->conMongo, $month, $year);
        if (count($niuSenderId) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivoNiuSenderId($niuSenderId, "NumerosCuenta_IdChat.xlsx");
        }
    }
    public function getcalnegativas($month, $year)
    {
        $calnegativas = getcalnegativas($this->conMongo, $month, $year);
        if (count($calnegativas) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivogetcalnegativas($calnegativas, "calificacionesNegativas.xlsx");
        }
    }
    public function getUsuariosSegmentos($month, $year)
    {
        $UsuariosSegmentos = getUsuarios_Segmentos($this->conMongo, $month, $year);
        if (count($UsuariosSegmentos) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivogetUsuariosSegmentos($UsuariosSegmentos, "usuariosSegmentos.xlsx");
        }
    }
    public function getSegmentoDifusion($month, $year)
    {
        $SegmentoDifusion = getSegmento_Difusion($this->conMongo, $month, $year);
        if (count($SegmentoDifusion) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivogetSegmentoDifusion($SegmentoDifusion, "usuariosSegmentosDifusion.xlsx");
        }
    }
    public function getAcuseReciboDifusion($month, $year)
    {
        $acuseRecibo = getAcuseRecibo_Difusion($this->conMongo, $month, $year);

        if (count($acuseRecibo) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivogetAcuseReciboDifusion($acuseRecibo, "telefonosNiu_SistemaDifusion.xlsx");
        }
    }
    public function getSwitchesInexistentes($month, $year)
    {
        $SegmentoDifusion = getSwitches_Inexistentes($this->conMongo, $month, $year);
        if (count($SegmentoDifusion) == 0) {
            return 'No hay datos';
        } else {

            return $this->crearArchivogetSwitchesInexistentes($SegmentoDifusion, "SwitchesInexistentes.xlsx");
        }
    }

    public function getConsultasUsuarios($month, $year)
    {
        $consultasUsuarios = getConsultas_Usuarios($this->conMongo, $month, $year);
        if (count($consultasUsuarios) == 0) {
            return 'No hay datos';
        } else {
            
            return $this->crearArchivogetConsultasUsuarios($consultasUsuarios, "consultas_usuarios.xlsx");
        }
    }

    public function crearArchivoNiuSenderId($niuSenderId, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("ID_NIU")
            ->setSubject("ID_NIU")
            ->setDescription("IDENTIFICADOR DE CHAT DE CADA PLATAFORMA CON SU RESPECTIVO NIU.")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'SOURCE');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'ID_CHAT');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'NIU');

        $i = 2;

        foreach ($niuSenderId as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->source);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->idconversacion);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->niu);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('ID_NIU');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }

    public function crearArchivogetcalnegativas($calnegativas, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("Calificaciones Negativas")
            ->setSubject("Calificaciones Negativas")
            ->setDescription("LISTADO DE CALIFICACIONES NEGATIVAS DE LUCY")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'CALIFICACION');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'FECHA');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'VOC');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SOURCE');

        $i = 2;

        foreach ($calnegativas as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->calificacion);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->fecha);
            if (isset($value->voc)) {
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->voc);
            }
            if (isset($value->source)) {
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value->source);
            }

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('CALIFICACION');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }

    public function crearArchivogetUsuariosSegmentos($usuariosSegmentos, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("SEGMENTOS")
            ->setSubject("SEGMENTOS")
            ->setDescription("USUARIOS POR SEGMENTO QUE CONSULTAN LUCY")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'SOURCE');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'NIU');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'SEGMENTO');

        $i = 2;

        foreach ($usuariosSegmentos as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->source);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->niu);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->segmento[0]);

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('SEGMENTOS');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }

    public function crearArchivogetSegmentoDifusion($SegmentoDifusion, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("SEGMENTOS SISTEMA DE DIFUSION")
            ->setSubject("SEGMENTOS SISTEMA DE DIFUSION")
            ->setDescription("USUARIOS POR SEGMENTO")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'NIU');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'SEGMENTO');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'CLASE_SERVICIO');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'MUNICIPIO');

        $i = 2;

        foreach ($SegmentoDifusion as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->niu);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->segmento);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->clase_servicio);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value->municipio);

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('SEGMENTOS SISTEMA DIFUSION');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }

    public function crearArchivogetAcuseReciboDifusion($acuseRecibo, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("TEL No DE CUENTA DIFUNDIDOS")
            ->setSubject("SEGMENTOS SISTEMA DE DIFUSION")
            ->setDescription("ACUSE DE RECIBO")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'TIPO_MENSAJE');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'NIU');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'APERTURA');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'TELEFONO');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'ESTADO_PROMOCION');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'FECHA_PROMOCION');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'ESTADO_APERTURA');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'FECHA_APERTURA');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'ESTADO_CIERRE');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'FECHA_CIERRE');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'ESTADO_PROMOCION_PROGRAMADAS');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'FECHA_PROMOCION_PROGRAMADAS');

        $i = 2;

        foreach ($acuseRecibo as $key => $value) {
            if (isset($value->fechaPromocion)) {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'PROMOCIÓN DE LUCY');
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $value->estadoPromocion);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $value->fechaPromocion);
            } else if (isset($value->fechaApertura)) {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'DIFUSIÓN DE INTERRUPCIÓN NO PROGRAMADA');
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $value->estadoApertura);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $value->fechaApertura);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $value->estadoCierre);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $value->fechaCierre);
            } else if (isset($value->fechaPromocionProgramadas)) {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'PROMOCION DE INCRIPCIÓN A SUSPENSIONES PROGRAMADAS');
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $value->estadoPromocionProgramadas);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $value->fechaPromocionProgramadas);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->niu);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->apertura);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value->telefono);

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('TEL No DE CUENTA DIFUNDIDOS');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }

    public function crearArchivogetSwitchesInexistentes($switchs, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("SWITCHES INEXISTENTES")
            ->setSubject("SWITCHES INEXISTENTES")
            ->setDescription("SWITCHES INEXISTENTES")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'FECHA');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'TIPO');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'SWITCH');

        $i = 2;

        foreach ($switchs as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->fecha);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->tipo);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->switch);

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('SWITCHES INEXISTENTES');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }
    public function crearArchivogetConsultasUsuarios($usuarios, $file)
    {
        require_once "../../PHPExcel-1.8.1/Classes/PHPExcel.php";
        //require_once "./PHPExcel-1.8.1/Classes/PHPExcel/Writer/Excel2007/";
        require_once './../IOFactory.php';

        $nombreArchivo = '../../archivosDescargar/' . $file;

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();

        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Datalab")
            ->setLastModifiedBy("Datalab")
            ->setTitle("CONSULTAS DE USUARIOS")
            ->setSubject("CONSULTAS DE USUARIOS")
            ->setDescription("CONSULTAS DE USUARIOS")
            ->setKeywords("Excel Office")
            ->setCategory("Excel");

        //Asigno la hoja de calculo activa
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'FECHA');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'SOURCE');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'IDCONVERSATION');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'NIU');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'CANTIDAD_CONSULTAS');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'MUNICIPIO');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'CELULAR');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'SEGMENTO');

        $i = 2;

        foreach ($usuarios as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $value->fecha);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $value->source);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $value->idconversation);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $value->niu);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $value->total);
            if(count($value->usuario) > 0){
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $value->usuario[0]->MUNICIPIO);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $value->usuario[0]->CELULAR);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $value->usuario[0]->SEGMENTO);

            }

            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('CONSULTAS DE USUARIOS');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($nombreArchivo);

        return 'ok';
    }
}
