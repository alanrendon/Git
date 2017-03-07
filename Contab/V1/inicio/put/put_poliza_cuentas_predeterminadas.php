<?php

$url[0] = "../";
require_once "../class/periodo.class.php";
require_once "../class/poliza.class.php";
require_once "../class/asiento.class.php";
require_once "../class/cat_cuentas.class.php";
require_once "../class/poliza_facture.class.php";
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {


    $txt_fecha        = addslashes(trim($_POST['txt_fecha']));
    $slc_tipoPoliza   = addslashes(trim($_POST['slc_tipoPoliza']));
    $txt_concepto     = addslashes(trim($_POST['txt_concepto']));
    $txt_type_payment = (int)$_POST['met_payment'];
    $txt_nombreCheque = addslashes(trim($_POST['txt_nombreCheque']));
    $txt_noCheque     = addslashes(trim($_POST['txt_noCheque']));
    $txt_comentario   = "";
    $slc_facture      = "";
    $txt_descripcion  ="";


    $cta = new Cuenta();
    if (isset($_POST['slc_facture'])) {
        $slc_facture = addslashes(trim($_POST['slc_facture']));
    }
    if (isset($_POST['txt_comentario'])) {
        $txt_comentario = addslashes(trim($_POST['txt_comentario']));
    }
    if (isset($_POST['met_payment'])) {
        $txt_type_payment = addslashes(trim($_POST['met_payment']));
    }

    if ($txt_type_payment == 7) {
        if (empty($txt_nombreCheque) && empty($txt_noCheque)) {
            exit(json_encode(array('mensaje' => "Error, faltan datos.")));
        }
    }
    if (!empty($txt_fecha) && !empty($slc_tipoPoliza) && !empty($txt_concepto ) && $txt_type_payment >0) {

        if (validarPeriodo($txt_fecha)) {
                $poliza_facture             = new Poliza_Facture();
                $poliza                     = new Poliza();
                $cons                       = $poliza->get_ConsPoliza($slc_tipoPoliza);
                $poliza_facture->id_facture = 0;
                $poliza_facture->type       = 0;
                
                if (isset($_POST['txt_cuenta']) && isset($_POST['txt_debe']) && isset($_POST['txt_haber'])) {

                    $cuenta    = $_POST['txt_cuenta'];
                    $debe      = $_POST['txt_debe'];
                    $habe      = $_POST['txt_haber'];
                    $max       = sizeof($cuenta);

                    if ( sizeof($cuenta) != sizeof($debe)  || sizeof($cuenta) != sizeof($habe)) {
                       exit(json_encode(array('mensaje' => "Hacen falta campos en las cuentas.")));
                    }

                    for ($i = 0; $i < $max; $i++) {

                        if ($debe[$i] <> 0 && $habe[$i] <> 0) {
                            exit(json_encode(array('mensaje' => "Ingrese debe o haber, no los dos.")));
                        } else if (isset($debe[$i]) && $debe[$i] != 0 && $debe[$i] != '') {
                            $_POST['txt_haber'][$i] = 0;
                        } else {
                            $_POST['txt_debe'][$i] = 0;
                        }

                        $cta_respaldo = explode(' - ', $cuenta[$i]);
                        $cta_respaldo = $cta_respaldo[0];

                        if ($cta->existe_cta($cta_respaldo) > 0) {
                           $_POST['txt_cuenta'][$i] = $cta_respaldo;
                        }

                        if (isset($_POST['descripcion'][$i])) {
                           $_POST['descripcion'][$i] = addslashes(trim($_POST['descripcion'][$i]));
                        }else{
                             $_POST['descripcion'][$i] = '';
                        }
                    }
                }
                if ($resultado = $poliza->put_Polizas($cons, $txt_fecha, $slc_tipoPoliza, $txt_concepto, 0 , $txt_nombreCheque, $txt_noCheque, $txt_comentario, 0,$txt_type_payment) ) {
                   $poliza_facture->id_poliza  = $resultado;
                   $poliza_facture->put_facture_poliza();

                    
                    if (!$resultado) {
                        exit(json_encode(array('mensaje' => "Error, no se ha podido agregar la póliza.")));
                    } else if (isset($_POST['txt_cuenta'])){

                        $asiento = new Asiento();
                        $cuenta  = $_POST['txt_cuenta'];
                        $debe    = $_POST['txt_debe'];
                        $habe    = $_POST['txt_haber'];
                        $descripcion   = $_POST['descripcion'];
                        foreach ($_POST['txt_cuenta'] as $key => $cuenta) {
                            $no_asiento = $asiento->get_lastAsiento($resultado);
                            
                            if (!$asiento->put_asiento($resultado, $no_asiento, $cuenta, $debe[$key], $habe[$key], $descripcion[$key])) {
                                exit(json_encode(array('mensaje' => "Error, no se pudo registrar el asiento.")));
                            }
                        }
                    }
                }
                exit(json_encode(array('agregado' => $resultado)));
        }
    } else {
        exit(json_encode(array('mensaje' => "Error, faltan datos.")));
    }
}

function validarPeriodo($periodo) {
    $anio = date("Y", strtotime($periodo));
    $mes = date("m", strtotime($periodo));
    $periodo = new Periodo();
    $periodo = $periodo->validate_Periodo($anio, $mes);
    if (is_array($periodo)) {
        return true;
    } else {
        exit(json_encode(array('mensaje' => "Error, el periodo no está abierto o no está registrado.")));
    }
}


?>
