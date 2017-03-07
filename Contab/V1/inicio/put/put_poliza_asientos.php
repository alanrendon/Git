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
    $tipoSociedad     = addslashes(trim($_POST['tipoSociedad']));
    $txt_nombreCheque = addslashes(trim($_POST['txt_nombreCheque']));
    $txt_noCheque     = addslashes(trim($_POST['txt_noCheque']));
    $txt_type_payment = (int)$_POST['met_payment'];
    $txt_comentario   = "";


    if (isset($_POST['txt_comentario'])) {
        $txt_comentario = addslashes(trim($_POST['txt_comentario']));
    }

    if (isset($_POST['txt_doctoRelacionado'])) {
        $txt_doctoRelacionado = addslashes(trim($_POST['txt_doctoRelacionado']));
    }

    if ($txt_type_payment == 7) {
        if (empty($txt_nombreCheque) && empty($txt_noCheque)) {
            exit(json_encode(array('mensaje' => "Error, faltan datos. 1")));
        }
    }

    if (!empty($txt_fecha) && !empty($slc_tipoPoliza) && !empty($txt_concepto) && isset($_POST['txt_idFactura']) && $txt_type_payment>0) {
        
        $max            = count($_POST['txt_idFactura']);
        $poliza_facture = new Poliza_Facture();
        $resultado      = false;


        if (validarPeriodo($txt_fecha)) {
 
            if (isset($_POST['txt_cuenta']) && isset($_POST['txt_debe']) && isset($_POST['txt_haber'])) {

                $fk_poliza = $resultado;
                $cuenta    = $_POST['txt_cuenta'];
                $debe      = $_POST['txt_debe'];
                $habe      = $_POST['txt_haber'];
                $max       = sizeof($cuenta);
                $cta       = new Cuenta();

                if ( sizeof($cuenta) != sizeof($debe)  || sizeof($cuenta) != sizeof($habe)) {
                       exit(json_encode(array('mensaje' => "Hacen falta campos en las cuentas.")));
                }
                for ($i = 0; $i < $max; $i++) {

                    if (isset($_POST['descripcion'][$i])) {
                        $_POST['descripcion'][$i] = addslashes(trim($_POST['descripcion'][$i]));
                    }

                    if ($debe[$i] <> 0 && $habe[$i] <> 0) {
                        exit(json_encode(array('mensaje' => "Ingrese debe o haber, no los dos.")));
                    } else if (isset($debe[$i]) && $debe[$i] != 0 && $debe[$i] != '') {
                        $_POST['txt_haber'][$i] = 0;
                    } else {
                        $_POST['txt_debe'][$i] = 0;
                    }
    
                    $cta_respaldo = explode(' - ', $cuenta[$i]);
                    $cta_respaldo = $cta_respaldo[0];

                    if ($cta->existe_cta($cta_respaldo)) 
                        $_POST['txt_cuenta'][$i] = $cta_respaldo;
                    if (isset($_POST['descripcion'][$i])) 
                        $_POST['descripcion'][$i] = addslashes(trim($_POST['descripcion'][$i]));
                    else
                        $_POST['descripcion'][$i] = '';

                }
            }

            $poliza    = new Poliza();
            $cons      = $poliza->get_ConsPoliza($slc_tipoPoliza);
            $resultado = $poliza->put_Polizas($cons, $txt_fecha, $slc_tipoPoliza, $txt_concepto, $tipoSociedad, $txt_nombreCheque, $txt_noCheque, $txt_comentario, 0,$txt_type_payment);
                    
            if (!$resultado) 
                exit(json_encode(array('mensaje' => "Error, no se ha podido agregar la póliza.")));

            foreach ($_POST['txt_idFactura'] as $idFactura) {


                if ($tipoSociedad ==1) {
                    require_once $url[0]."class/fact_clie_pendientes.class.php";

                }else if ($tipoSociedad ==2) {
                    require_once $url[0]."class/fact_prov_pendientes.class.php";
                }

                if ($tipoSociedad ==2 || $tipoSociedad ==1) {
                    $facpen =new FacPolizaPendiente();

                    $fac            = $facpen->fetch_facture($idFactura);
                    $a_payment_term = $facpen->getCondiciones_de_Pago();

                    if (is_array($fac)) {
                        $fac = (object)$fac[0];
                    }

                    $name           = (is_object($fac)) ? 'cond_pago_'.$fac->fk_cond_reglement : null ;
                    $cp             = (isset($a_payment_term[$name])) ? $a_payment_term[$name] : 0 ;  

                    switch ($cp) {
                        case 1:
                                $poliza_facture->type = $tipoSociedad;
                                break;
                        case 2:
                             if ( $fac->paye==1) {
                                $poliza_facture->type = $tipoSociedad.'11';
                             }else{
                                $poliza_facture->type = $tipoSociedad.'1';
                             }
                             break;

                        default:
                            $poliza_facture->type = $tipoSociedad;
                             break;
                     }

                }
                else
                {
                    $poliza_facture->type = $tipoSociedad;
                }
               
                $poliza_facture->id_facture = $idFactura;
                $poliza_facture->id_poliza  = $resultado;

                $a_poliza_facture =  $poliza_facture->get_poliza_facture_id();

                $poliza_facture->put_facture_poliza();
            }

            if (isset($_POST['txt_cuenta'])){

                $asiento     = new Asiento();
                $cuenta      = $_POST['txt_cuenta'];
                $debe        = $_POST['txt_debe'];
                $habe        = $_POST['txt_haber'];
                $descripcion = $_POST['descripcion'];
                foreach ($_POST['txt_cuenta'] as $key => $cuenta) {
                    $no_asiento = $asiento->get_lastAsiento($resultado);
                    if (!$asiento->put_asiento($resultado, $no_asiento, $cuenta, $debe[$key], $habe[$key], $descripcion[$key])) {
                        exit(json_encode(array('mensaje' => "Error, no se pudo registrar el asiento.")));
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
