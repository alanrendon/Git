<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(4))) die;

if (isset($_GET['fecha'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body", "./plantillas/verEfe.tpl");
	$tpl->prepare();
	
	$tpl->assign('fecha', date('d/m/Y', date('d') < 5 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));
	
	die($tpl->printToScreen());
}

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia FROM alerta_efectivos ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) echo 0;
else echo 1;
?>