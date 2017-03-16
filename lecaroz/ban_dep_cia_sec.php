<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN 1 AND 800";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_cia_sec.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = 'UPDATE estado_cuenta SET num_cia_sec = ' . ($_POST['num_cia_sec'] > 0 ? $_POST['num_cia_sec'] : 'NULL') . " WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$tpl->newBlock('mod');
$tpl->assign('id', $_GET['id']);

$sql = "SELECT ec.num_cia, cc.nombre_corto AS nombre_cia, cuenta, fecha, importe, num_cia_sec, ccs.nombre_corto AS nombre_cia_sec FROM estado_cuenta ec LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_companias ccs ON (ccs.num_cia = ec.num_cia_sec) WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->assign('num_cia', $result[0]['num_cia']);
$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
$tpl->assign('banco', $result[0]['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
$tpl->assign('fecha', $result[0]['fecha']);
$tpl->assign('importe', number_format($result[0]['importe'], 2, '.', ','));

$tpl->assign('num_cia_sec', $result[0]['num_cia_sec']);
$tpl->assign('nombre_cia_sec', $result[0]['nombre_cia_sec']);

$tpl->printToScreen();
?>
