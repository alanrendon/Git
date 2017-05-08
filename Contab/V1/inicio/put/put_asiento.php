<?php
	$url[0] = "../";
	require_once "../class/asiento.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset( $_POST['eliminar']) ) {
		$idAsiento = (int)$_POST['eliminar'];
		$asiento = new Asiento();
		if ( $asiento->delete_asiento($idAsiento) ) {
			exit(json_encode($idAsiento));
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, no se pudo eliminar el asiento.")));
		}
	}
	else if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$fk_poliza       = addslashes(trim($_POST['id_poliza']));
		$cuenta          = addslashes(trim($_POST['txt_cuenta']));
		$debe            = addslashes(trim($_POST['txt_debe']));
		$habe            = addslashes(trim($_POST['txt_haber']));
		$txt_descripcion="";
		
        if(isset($_POST['descripcion'])){
            $txt_descripcion  = addslashes(trim($_POST['descripcion']));
        }
		if ( (!empty($debe) && ($debe<>0) ) &&  (!empty($habe) && ($habe<>0) ) ) {
			exit(json_encode( array('mensaje' =>"Ingrese debe o haber, no los dos.")));
		}
		else if (isset($debe) && $debe!=0 && $debe!='') {
			$habe = "";
		}
		else {
			$debe = "";
		}

		if ( !empty($fk_poliza) && !empty($cuenta) ) {
			$asiento = new Asiento();
			$no_asiento = $asiento->get_lastAsiento($fk_poliza);
			if ( $asiento->put_asiento($fk_poliza,$no_asiento,$cuenta,$debe,$habe,$txt_descripcion ) ) {
				exit(json_encode( $no_asiento));
			}
			else {
				exit(json_encode( array('mensaje' =>"Error, no se pudo registrar el asiento.")));
			}

		}
		else {
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	
?>