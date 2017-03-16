<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['accion']) && $_GET['accion'] == 'upload') {
	// Coneccion a la base de datos de las imagenes
	$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");
	
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['doc']['tmp_name']));
	
	// Insertar la imagen en la base de datos
	$sql = "INSERT INTO img_doc_car_tmp (id_car, imagen, iduser) VALUES ($_GET[id], '$imgData', $_SESSION[iduser])";
	$db_scans->query($sql);
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_car_fol_scan.tpl");
$tpl->prepare();

if (isset($_GET['accion']) && $_GET['accion'] == 'close') {
	$tpl->newBlock('close');
	die($tpl->printToScreen());
}

$tpl->newBlock('scan');
$tpl->assign('id', $_GET['id']);

$tpl->printToScreen();
?>