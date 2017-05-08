<?php
// MODIFICACION DE COMPAямAS V2
// Tabla 'catalogo_companias'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_prov_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_pro'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una pАgina que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

$sql = "SELECT num_proveedor AS num, nombre, rfc FROM catalogo_proveedores ORDER BY $_GET[orden]";
$result = $db->query($sql);

if (!$result) {
	header("location: ./fac_prov_con_v2.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
foreach ($result as $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("num", $reg['num']);
	$tpl->assign("nombre", $reg['nombre']);
	$tpl->assign("rfc", $reg['rfc']);
}

$tpl->printToScreen();
?>