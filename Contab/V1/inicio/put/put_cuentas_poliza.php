<?php
	$url[0] = "../";
	require_once "../class/cuentas_poliza.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		if ( isset($_POST['tipo']) && isset($_POST['cuentas']) ) {
            $txt_tipo = addslashes(trim($_POST['tipo']));   
           	$cta_poliza = new Cuentas_Poliza();
            foreach($_POST['cuentas'] as $cta){
                 $cta_poliza->put_cuenta_poiliza($txt_tipo,addslashes(trim($cta)));
            }
            exit(json_encode( array('mensaje' =>"Se han guardado los datos.")));
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	
?>

