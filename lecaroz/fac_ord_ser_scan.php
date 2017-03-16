<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
//$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_ord_ser_scan.tpl");
$tpl->prepare();

if (isset($_REQUEST['accion']) && $_REQUEST['accion'] == "upload") {
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['orden']['tmp_name']));
	// Insertar la imagen en la base de datos
	$sql = "INSERT INTO img_tmp_ord (folio, imagen) VALUES ($_GET[folio], '$imgData')";
	$db_scans->query($sql);
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == "cerrar") {	
	$tpl->newBlock('cerrar');
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("scan");
$tpl->assign('folio', $_GET['folio']);

$tpl->printToScreen();
?>