<?php

$url[0] = "../";
require_once "../class/periodo.class.php";
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $txt_anio = addslashes(trim($_POST['txt_anio']));

    $fecha = explode("-", $txt_anio);
    $acceso = false;

    if (!empty($fecha[0]) && !empty($fecha[1]) && !isset($_POST['ajuste'])) {
        $periodo = new Periodo();
        $lastPeriodo = $periodo->get_ultimo_periodo_cerrado();

        if ($periodo->validar_nuevo_periodo() < 1) {
            if (!$periodo->validar_existe_Periodo($fecha[0], $fecha[1])) {
                $periodos_registrados = $periodo->get_all_Periodos();

                if (count($periodos_registrados) > 0) {
                    if($lastPeriodo->mes<13){
                        if ( ($lastPeriodo->anio == $fecha[0] && $lastPeriodo->mes == ($fecha[1] - 1)) || (($lastPeriodo->anio + 1) == $fecha[0] && $fecha[1] == 1 && $lastPeriodo->mes == 12)) {
                            $acceso = true;
                        }
                    }else{
                        $acceso = true;
                    }
                    
                } else {

                    $acceso = true;
                }

                if ($acceso) {

                    if ($periodo->put_Periodo($fecha[0], $fecha[1])) {
                        exit(json_encode(true));
                    } else {
                        exit(json_encode(array('mensaje' => "Error, no se pudo agregar el periodo.")));
                    }
                } else {
                    exit(json_encode(array('mensaje' => "Error, debe seguir un orden para agregar los periodos.")));
                }
            } else {
                exit(json_encode(array('mensaje' => "Error, el periodo ya está registrado.")));
            }
        } else {
            exit(json_encode(array('mensaje' => "Error, para registrar un periodo los demás deben estar cerrados.")));
        }
    }else if(isset($_POST['ajuste'])){
         $periodo = new Periodo();
         if ($periodo->validar_nuevo_periodo() < 1) {
             $fecha[1] = 13;
              if ($periodo->put_Periodo($fecha[0], $fecha[1])) {
                    exit(json_encode(true));
                } else {
                    exit(json_encode(array('mensaje' => "Error, no se pudo agregar el periodo.")));
                }
         }else {
            exit(json_encode(array('mensaje' => "Error, para registrar un periodo los demás deben estar cerrados.")));
        }
       
    } else {
        exit(json_encode(array('mensaje' => "Error, faltan datos.")));
    }
}
?>