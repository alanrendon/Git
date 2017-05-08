<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_del.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "DELETE FROM catalogo_camionetas WHERE idcamioneta = $_POST[id];\n";
	$db->query($sql);
	$sql = "DELETE FROM img_camionetas WHERE id_doc IN (SELECT id_doc FROM doc_camionetas WHERE idcamioneta = $_POST[id]);\n";
	$sql .= "DELETE FROM doc_camionetas WHERE idcamioneta = $_POST[id];\n";
	$db_scans->query($sql);
	$db->desconectar();
	$db_scans->desconectar();
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("pregunta");
$tpl->assign("id", $_GET['id']);

$tpl->printToScreen();
?>