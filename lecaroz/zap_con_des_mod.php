<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_con_des_mod.tpl");
$tpl->prepare();

if (isset($_POST['cod'])) {
	$sql = "UPDATE cat_conceptos_descuentos SET tipo = $_POST[tipo] WHERE cod = $_POST[cod]";
	$db->query($sql);
	
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$sql = "SELECT * FROM cat_conceptos_descuentos WHERE cod = $_GET[cod]";
$result = $db->query($sql);

$tpl->newBlock('datos');
$tpl->assign('cod', $result[0]['cod']);
$tpl->assign('concepto', $result[0]['concepto']);
$tpl->assign('tipo_' . $result[0]['tipo'], ' checked');

$tpl->printToScreen();
?>