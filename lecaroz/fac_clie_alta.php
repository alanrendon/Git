<?php
// REGISTRO DE VENTA DE BARREDURA
// Tabla 'barredura'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El cσdigo no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informaciσn de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_clie_alta.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (!isset($_GET['id'])) {
	$tpl->assign("id",nextID2("catalogo_clientes","id",$dsn));
//	$tpl->assign("tabla","catalogo_clientes");
// Si viene de una pαgina que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", "La compaρνa no. $_GET[codigo_error] no tiene saldo inicial");	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die();
}

$sql="INSERT INTO catalogo_clientes(id,nombre,direccion,rfc) VALUES(".$_GET['id'].",'".strtoupper($_GET['nombre'])."','".strtoupper($_GET['direccion'])."','".strtoupper($_GET['rfc'])."')";
ejecutar_script($sql,$dsn);

// Imprimir el resultado

header("location: ./fac_clie_alta.php");
die();

$tpl->printToScreen();
?>