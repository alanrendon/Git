<?php

// FACTURAS PROVEEDORES VARIAS
// Tabla 'facturas'
// Menu Proveedores y Facturas -> Proveedores

include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass($dsn);


if(existe_registro("catalogo_porcentajes_facturas",array("num_cia"),array($_POST['num_cia']),$dsn)){
	$sql="DELETE FROM catalogo_porcentajes_facturas where num_cia=".$_POST['num_cia'];
	ejecutar_script($sql,$dsn);
}	
$aux=0;
for($i=0;$i<4;$i++){
	if($_POST['porcentaje'.$i]!="" or $_POST['porcentaje'.$i] > 0){
		$dato['num_cia'.$aux]=$_POST['num_cia'];
		$dato['porcentaje'.$aux]=$_POST['porcentaje'.$i];
		$aux++;
	}
}

$db = new DBclass($dsn, "catalogo_porcentajes_facturas", $dato);
$db->xinsertar();


// Consultar si existe la compañia
header("location: ./fac_porc_fac.php");
die();
?>