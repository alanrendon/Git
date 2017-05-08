<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_dot_div.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = '';
	for ($i = 0; $i < 3; $i++) {
		$importe = get_val($_POST['importe_div'][$i]);
		if ($_POST['fecha_div'][$i] != '' && $importe > 0)
			$sql .= "INSERT INTO otros_depositos (num_cia, fecha, importe, fecha_cap, acumulado, concepto, iduser, comprobante) SELECT num_cia, '{$_POST['fecha_div'][$i]}', $importe, fecha_cap, acumulado, concepto, iduser, comprobante FROM otros_depositos WHERE id = $_POST[id];\n";
	}
	$sql .= "DELETE FROM otros_depositos WHERE id = $_POST[id];\n";
	$db->query($sql);
	
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$tpl->newBlock('div');
$tpl->assign('id', $_GET['id']);

$sql = "SELECT * FROM otros_depositos WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->assign('fecha', $result[0]['fecha']);
$tpl->assign('importe', number_format($result[0]['importe'], 2, '.', ','));

$tpl->printToScreen();
?>