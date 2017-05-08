<?php
// CONSULTA DE PRODUCTOS
// Tablas 'catalogo_productos'
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
$descripcion_error[1] = "No hay registros en el catálogo";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pts_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['orden'])) {
	$tpl->newBlock("orden");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die;
}

$sql = "SELECT * FROM catalogo_productos ORDER BY ".(($_GET['orden'] == "codigo")?"cod_producto":"nombre")." ASC";
$result = ejecutar_script($sql,$dsn);

if (!$result) {
	header("location: ./pan_pts_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");

for ($i=0; $i<count($result); $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("cod_producto",$result[$i]['cod_producto']);
	$tpl->assign("nombre",$result[$i]['nombre']);
	$tpl->assign("precio",$result[$i]['precio']);
}

$tpl->printToScreen();
?>