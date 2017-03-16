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

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/doc/doc_doc_del.tpl" );
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "DELETE FROM imagenes WHERE id_doc = $_POST[id];\n";
	$sql .= "DELETE FROM documentos WHERE id_doc = $_POST[id];\n";
	$db_scans->query($sql);
	$db_scans->desconectar();
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("question");
$tpl->assign("id", $_GET['id']);

$tpl->printToScreen();
$db->desconectar();
?>