<?php
	$url[0] = "../";
	require_once "../class/tipo_poliza.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$txt_tipo =  addslashes(trim($_POST['txt_nombre']));                          
		$txt_abr = addslashes(trim($_POST['txt_abr']));                

		if ( !empty($txt_tipo) && !empty($txt_abr) ) {
            
           	$tipo = new Tipo_Poliza();
            
            if( $tipo->put_Tipopoliza($txt_tipo,$txt_abr) ) {
                exit(json_encode( array('mensaje' =>"Se agregó el nuevo grupo.")));
            }
            else {
                exit(json_encode( array('mensaje' =>"El grupo ya existe.")));
            }
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	
?>

