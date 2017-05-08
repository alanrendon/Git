<?php
	$url[0] = "../";
	require_once "../class/asiento.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_POST['idAsiento'])) {
		$idAsiento= (int)$_POST['idAsiento'];
		$asiento    = new Asiento();
		if ($asiento=$asiento->get_asiento($idAsiento)) {
			exit(json_encode($asiento));
		}else{
			exit(json_encode( array('mensaje' =>"Error, no se pudo registrar el asiento.")));
		}
	}

	
?>