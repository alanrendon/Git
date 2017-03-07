<?php
	$url[0] = "../";
	require_once "../class/grupo.class.php";
	
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$grupo				= addslashes(trim($_POST['grupo']));
		$codigo_agrupador	= '';
		$cuenta_inicial		= $_POST['cuenta_inicial'];
		$cuenta_final		= $_POST['cuenta_final'];
		$grupo_padre		= $_POST['grupo_padre'];
		$tipo				= isset ($_POST['tipo']) ? $_POST['tipo'] : 1;
		
		if ( empty($grupo) && empty($cuenta_inicial) && empty($cuenta_final)) {
			exit(json_encode( array('mensaje' =>"Datos incompletos")));
		}

		$nuevo_grupo = new Grupo();
		$respuesta = $nuevo_grupo->put_grupo($grupo,$codigo_agrupador,$cuenta_inicial,$cuenta_final,$grupo_padre,$tipo);

		if ( $respuesta ) {
			exit(json_encode( array('insert' =>$respuesta)));
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, no se pudo registrar el grupo.")));
		}
	}	
?>