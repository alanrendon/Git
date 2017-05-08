<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";
$mensaje[1] = "Se han genreado los gastos con exito";

if (isset($_GET['fecha'])) {
	$sql = "SELECT * FROM gastos_caja_fijos ORDER BY num_cia, cod_gastos LIMIT 1";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_gas_caj_fij_gen.php?codigo_error=1");
		die;
	}
	
	$sql = "INSERT INTO gastos_caja (num_cia, cod_gastos, importe, tipo_mov, clave_balance, fecha, fecha_captura, comentario) ";
	$sql .= "SELECT num_cia, cod_gastos, importe, tipo_mov, clave_balance, '$_GET[fecha]', CURRENT_DATE, comentario FROM gastos_caja_fijos";
	$db->query($sql);
	
	header("location: ./bal_gas_caj_fij_gen.php?mensaje=1");
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_caj_fij_gen.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha", date("d/m/Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

// Si viene de una página que genero error
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign( "message", $mensaje[$_GET['mensaje']]);	
}

$tpl->printToScreen();
?>