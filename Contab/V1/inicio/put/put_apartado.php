<?php
	$url[0] = "../";
	require_once "../class/conf_apartados.class.php";

	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		
		$txt_tipo = $_POST['txt_tipo'];                      
		$txt_descrpcion = addslashes(trim($_POST['txt_descrpcion']));                
		$reporte = $_POST['reporte'];

		if ( !empty($txt_tipo) && !empty($txt_descrpcion) ) {
            
           	$apartado = new Apartados();
            
            if( $apartado->put_apartado($txt_descrpcion,$txt_tipo,$reporte) ) {
                exit(json_encode( array('mensaje' =>"Se agregó el apartado.")));
            }
            else {
                exit(json_encode( array('mensaje' =>"El apartado ya existe.")));
            }
		}
		else {
			exit(json_encode( array('mensaje' =>"Error, faltan datos.")));
		}
	}

	
?>

