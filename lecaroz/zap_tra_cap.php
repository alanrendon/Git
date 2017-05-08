<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

if (isset($_POST['mes'])) {
	$sql = "DELETE FROM traspaso_pares WHERE mes = $_POST[mes] AND anio = $_POST[anio];\n";
	foreach ($_POST['num_cia'] as $i => $num_cia)
		if (get_val($_POST['importe'][$i]) != 0)
			$sql .= "INSERT INTO traspaso_pares (num_cia, mes, anio, importe) VALUES ($num_cia, $_POST[mes], $_POST[anio], " . get_val($_POST['importe'][$i]) . ");\n";
	
	$db->query($sql);
	die(header('location: ./zap_tra_cap.php'));
}

if (isset($_GET['mes'])) {
	$sql = "SELECT num_cia, importe FROM traspaso_pares WHERE mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) die();
	
	$data = '';
	foreach ($result as $i => $reg)
		$data .= "$reg[num_cia],$reg[importe]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_tra_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));
$tpl->assign('total', '0.00');

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia";
$result = $db->query($sql);

foreach ($result as $i => $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
	$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
}

$tpl->printToScreen();
?>