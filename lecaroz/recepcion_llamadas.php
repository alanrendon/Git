<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['iduser'])) {
	$llamada = $_POST;
	
	$llamada['de'] = strtoupper($llamada['de']);
	$llamada['hora'] = "$llamada[horas]:$llamada[minutos]";
	$llamada['recado'] = strtoupper($llamada['recado']);
	$llamada['status'] = isset($llamada['contestada']) ? "1" : "0";
	$llamada['comentario'] = isset($llamada['comentario']) ? strtoupper($llamada['comentario']) : "";
	
	$sql = "INSERT INTO llamadas (iduser, de, fecha, hora, recado, status, comentario) VALUES ($llamada[iduser], '$llamada[de]', '$llamada[fecha]', '$llamada[hora]', '$llamada[recado]', $llamada[status])";
	$db->query($sql);
	
	header("location: ./recepcion_llamadas.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ren/recepcion_llamadas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha", date("d/m/Y"));
$tpl->assign("hora", date("G"));
$tpl->assign("min", date("i"));

$tpl->printToScreen();
?>