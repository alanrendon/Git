<?php
$url[0] = "../";
require_once "../class/tipo_poliza.class.php";
require_once "../class/cuentas_poliza.class.php";

$contador = 1;
$tipo = new Tipo_Poliza();
$ctas = new Cuentas_Poliza();
$rowid =(int)$_POST['id'];
$grupo = $tipo->get_tipo_poliza_id($rowid);


if( isset($_POST['function'])){
	$funcion= addslashes($_POST['function']);
	if(strcmp($funcion, 'Eliminar cuentas')==0){
		$ctas->eliminar_todas($grupo->abr);
	}
}else{
	if( $tipo->eliminar($rowid) ) {
		$ctas->eliminar_todas($grupo->abr);

	}else{
	    $contador++;
	}

	$return["json"] = ($contador == 0 ) ? json_encode(1) : json_encode(2);
	echo json_encode($return);
}







