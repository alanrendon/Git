<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['id'])) {
	$datos = $_POST;
	
	$datos['modelo'] = strtoupper($datos['modelo']);
	$datos['placas'] = strtoupper($datos['placas']);
	$datos['propietario'] = strtoupper($datos['propietario']);
	$datos['usuario'] = strtoupper($datos['usuario']);
	$datos['num_serie'] = strtoupper($datos['num_serie']);
	$datos['num_motor'] = strtoupper($datos['num_motor']);
	$datos['clave_vehicular'] = strtoupper($datos['clave_vehicular']);
	$datos['num_poliza'] = strtoupper($datos['num_poliza']);
	$datos['inciso'] = strtoupper($datos['inciso']);
	$datos['localizacion_fac'] = strtoupper($datos['localizacion_fac']);
	
	$sql = $db->preparar_insert("catalogo_camionetas", $datos);
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./doc_cam_altas.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_altas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT last_value FROM catalogo_camionetas_idcamioneta_seq";
$id = $db->query($sql);

$tpl->assign("id", $id[0]['last_value'] + 1);

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = $db->query($sql);
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
$db->desconectar();
die;
?>