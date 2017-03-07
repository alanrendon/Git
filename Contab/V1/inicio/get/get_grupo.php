<?php
	$url[0] = "../";
	require_once "../class/grupo.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$idgrupo = (int)$_POST['idGrupo'];
		$grupo = new Grupo();
		$respuesta = $grupo->get_grupo($idgrupo);

		if ( $respuesta ) {
			exit(json_encode($respuesta));
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, no se pudo cargar el grupo.")));
		}
	}

	
?>