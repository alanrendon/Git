<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_del.tpl");
$tpl->prepare();

// Insertar datos
if (isset($_POST['id'])) {
	$db->query("DELETE FROM faltantes_cometra WHERE id = $_POST[id]");
	$db->desconectar();
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("question");

$tpl->assign("id", $_GET['id']);

$tpl->printToScreen();
$db->desconectar();
?>