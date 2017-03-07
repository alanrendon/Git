<?php
	$url[0] = "../";
	require_once "../class/asiento.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['eliminar'])) {
		$idAsiento= (int)$_POST['eliminar'];
		$asiento    = new Asiento();
		if ($asiento->delete_asiento($idAsiento)) {
			exit(json_encode($idAsiento));
		}else{
			exit(json_encode( array('mensaje' =>"Error, no se pudo eliminar el asiento.")));
		}
	}else if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$rowid       =addslashes(trim($_POST['id_poliza']));
		$descripcion ='';
		$cuenta      =addslashes(trim($_POST['txt_cuenta']));
		$debe        =addslashes(trim($_POST['txt_debe']));
		$habe        =addslashes(trim($_POST['txt_haber']));
		
		 if(isset($_POST['txt_descripcion'])){
            $descripcion  = addslashes(trim($_POST['txt_descripcion']));
         }
		if ( $habe <> 0 && $debe <> 0 ) {
			exit(json_encode( array('mensaje' =>"Ingrese debe o haber, no los dos.")));
		}
		else if ( isset($debe) && $debe <> 0 ) {
			$habe = "0";
		}
		else {
			$debe = "0";
		}

		if ( !empty($rowid)  && !empty($cuenta) ) {
			$asiento = new Asiento();
			$resultado = $asiento->update_asiento($cuenta,$debe,$habe,$rowid,$descripcion);
			if ( $resultado ) {
				exit(json_encode( array('update' =>$resultado)));
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