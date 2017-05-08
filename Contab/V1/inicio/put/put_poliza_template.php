<?php
error_reporting(-1);
$url[0] = "../";
require_once "../class/periodo.class.php";
require_once "../class/poliza.class.php";
require_once "../class/cat_cuentas.class.php";
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $txt_fecha = '00-00-0000';
    $slc_tipoPoliza = addslashes(trim($_POST['slc_tipoPoliza']));
    $txt_concepto = addslashes(trim($_POST['txt_concepto']));
    $txt_doctoRelacionado = 0;
    $txt_nombreCheque = addslashes(trim($_POST['txt_nombreCheque']));
    $txt_noCheque = addslashes(trim($_POST['txt_noCheque']));
    $txt_comentario = "";
    $slc_facture = "";
    $cta = new Cuenta();
    if (isset($_POST['slc_facture'])) {
        $slc_facture = addslashes(trim($_POST['slc_facture']));
    }
    if (isset($_POST['txt_comentario'])) {
        $txt_comentario = addslashes(trim($_POST['txt_comentario']));
    }

    if ($slc_tipoPoliza == 'C') {
        if (empty($txt_nombreCheque) && empty($txt_noCheque)) {
            exit(json_encode(array('mensaje' => "Error, faltan datos.")));
        }
    }
    if (!empty($slc_tipoPoliza) && !empty($txt_concepto)) {

        $poliza = new Poliza();
        $cons = $poliza->get_ConsPoliza($slc_tipoPoliza);
        $resultado = $poliza->put_Polizas($cons, $txt_fecha, $slc_tipoPoliza, $txt_concepto, $txt_doctoRelacionado, $txt_nombreCheque, $txt_noCheque, $txt_comentario, $slc_facture);
        if (!$resultado) {
            exit(json_encode(array('mensaje' => "Error, no se ha podido agregar la póliza.")));
        } else if (isset($_POST['txt_cuenta']) && isset($_POST['txt_debe']) && isset($_POST['txt_haber'])) {

            $fk_poliza = $resultado;
            $cuenta = $_POST['txt_cuenta'];
            $debe = $_POST['txt_debe'];
            $habe = $_POST['txt_haber'];

            $max = sizeof($cuenta);
            for ($i = 0; $i < $max; $i++) {

                if ($debe[$i] > 0 && $habe[$i] > 0) {
                    exit(json_encode(array('mensaje' => "Ingrese debe o haber, no los dos.")));
                } else if (isset($debe[$i]) && $debe[$i] != 0 && $debe[$i] != '') {
                    $habe[$i] = "";
                } else {
                    $debe[$i] = "";
                }
                $cta_respaldo = explode(' - ', $cuenta[$i]);
                $cta_respaldo= $cta_respaldo[0];
                if($cta->existe_cta($cta_respaldo)>0){
                    $cuenta[$i]= $cta_respaldo;
                                
                }
                                
                if(isset($_POST['descripcion'][$i])){
                    $txt_descripcion  = addslashes(trim($_POST['descripcion'][$i]));
                }
                
                if (!empty($fk_poliza) && !empty($cuenta[$i])) {
                    $asiento = new Asiento();
                    $no_asiento = $asiento->get_lastAsiento($fk_poliza);
                    if (!$asiento->put_asiento($fk_poliza, $no_asiento, $cuenta[$i], $debe[$i], $habe[$i], $txt_descripcion)) {
                        exit(json_encode(array('mensaje' => "Error, no se pudo registrar el asiento.")));
                    }
                } else {
                    exit(json_encode(array('mensaje' => "Error, faltan datos .")));
                }
                
                  $txt_descripcion="";
            }
        }
        exit(json_encode(array('agregado' => $resultado)));
    } else {
        exit(json_encode(array('mensaje' => "Error, faltan datos.")));
    }
}
?>