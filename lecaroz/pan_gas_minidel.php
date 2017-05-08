<?php
// BORRAR RAPIDO DE UN PRODUCTO EN CONTROL DE PRODUCCION
// Tablas 'control_produccion'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_gas_minidel.tpl");
$tpl->prepare();


if (isset($_POST['bandera'])) {
	//MODIFICA LOS EFECTIVOS
	$sql="UPDATE total_panaderias SET gastos = gastos - ".$_POST['importe'].", efectivo=efectivo + ".$_POST['importe']." WHERE num_cia=".$_POST['num_cia']." AND fecha='".$_POST['fecha']."'";
	ejecutar_script($sql,$dsn);
	
	$sql="SELECT * FROM movimiento_gastos WHERE idmovimiento_gastos=".$_POST['id'];
	$gas=ejecutar_script($sql,$dsn);

	$sql="INSERT INTO movimiento_gastos_cancelados(num_cia,codgastos,importe,revisado,concepto_gasto,concepto_cancela,fecha_can) VALUES(".$_POST['num_cia'].",".$_POST['codgastos'].",".$_POST['importe'].",false,'".$gas[0]['concepto']."','".$_POST['concepto']."','".date("d/m/Y")."')";
	ejecutar_script($sql,$dsn);
	$sql="DELETE FROM movimiento_gastos where idmovimiento_gastos=".$_POST['id'];	
	ejecutar_script($sql,$dsn);
	$tpl->newBlock("cerrar");

	
	$tpl->printToScreen();
	die();
}


// Generar pantalla de captura
$tpl->newBlock("question");

$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("importe",$_GET['importe']);
$tpl->assign("codgastos",$_GET['codgastos']);
$tpl->assign("fecha",$_GET['fecha']);
$tpl->assign("bandera","true");


// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message","No se puede eliminar el gasto");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>