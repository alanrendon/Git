<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "El concepto esta siendo utilizado por un documento";

// --------------------------------- Delaracion de variables -------------------------------------------------

if (isset($_POST['tipo_doc'])) {
	$descripcion = strtoupper($_POST['descripcion']);
	
	// Veiricar que no exista la entrada
	if ($db->query("SELECT * FROM catalogo_doc_camionetas WHERE descripcion = '$descripcion'")) {
		$db->desconectar();
		header("location: ./doc_cat_doc_cam_con.php?accion=mod&id=$_POST[tipo_doc]&codigo_error=1");
		die;
	}
	
	$sql = "UPDATE catalogo_doc_camionetas SET descripcion = '$descripcion' WHERE tipo_doc = $_POST[tipo_doc]";
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./doc_cat_doc_cam_con.php");
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == "del") {
	// Verificar si el concepto esta siendo utilziado por algun concepto
	if ($db->query("SELECT id_doc FROM doc_camionetas WHERE tipo_doc = $_GET[id] LIMIT 1")) {
		$db->desconectar();
		header("location: ./doc_cat_doc_cam_con.php?codigo_error=1");
		die;
	}
	
	$sql = "DELETE FROM catalogo_doc_camionetas WHERE tipo_doc = $_GET[id]";
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./doc_cat_doc_cam_con.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/doc/doc_cat_doc_cam_con.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['accion']) && $_GET['accion'] == "mod") {
	$tpl->newBlock("modificar");
	
	$sql = "SELECT * FROM catalogo_doc_camionetas WHERE tipo_doc = $_GET[id]";
	$result = $db->query($sql);
	$db->desconectar();
	
	$tpl->assign("tipo_doc", $_GET['id']);
	$tpl->assign("descripcion", $result[0]['descripcion']);
	
	$tpl->printToScreen();
	die;
}

$sql = "SELECT * FROM catalogo_doc_camionetas ORDER BY descripcion";
$result = $db->query($sql);

$tpl->newBlock("listado");

if ($result)
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id", $result[$i]['tipo_doc']);
		$tpl->assign("descripcion", $result[$i]['descripcion']);
	}
else
	$tpl->newBlock("no_result");

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
?>