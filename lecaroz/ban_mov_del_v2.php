<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_mov_del_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$tabla_mov = $_POST['cuenta'] == 1 ? "mov_banorte" : "mov_santander";
	
	$sql = "DELETE FROM $tabla_mov WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia", $_POST['num_cia']);
	$tpl->printToScreen();
	die;
}

$num_cia = $_GET['num_cia'];
$cuenta = $_GET['cuenta'];
$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("question");
$tpl->assign("cuenta", $cuenta);
$tpl->assign("num_cia", $num_cia);

foreach ($_GET['id'] as $id) {
	$tpl->newBlock("id");
	$tpl->assign("id", $id);
}

$tpl->printToScreen();
?>