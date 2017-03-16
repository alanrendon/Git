<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pre_altas_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	if (floatval(str_replace(",", "", $_POST['total'])) > 0)
		$_SESSION['psr']['p'] = $_POST;
	else if (isset($_SESSION['psr']['p']))
		unset($_SESSION['psr']['p']);
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id, num_emp, nombre, ap_paterno, ap_materno FROM catalogo_trabajadores WHERE num_cia = {$_SESSION['psr']['num_cia']} OR num_cia_emp = {$_SESSION['psr']['num_cia']} ORDER BY num_emp";
$emps = $db->query($sql);

/*if (!$emps) {
	$tpl->newBlock("no_result");
	$tpl->printToScreen();
	die;
}*/

$tpl->newBlock("prestamos");

if ($emps)
	foreach ($emps as $emp) {
		$tpl->newBlock("emp");
		$tpl->assign("num_emp", $emp['num_emp']);
		$tpl->assign("id", $emp['id']);
		$tpl->assign("nombre", "$emp[nombre] $emp[ap_paterno] $emp[ap_materno]");
	}

$inicio = 0;
if (!isset($_SESSION['psr']['p'])) {
	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND tipo_mov = 'FALSE'";
	$result = $db->query($sql);
	if ($result) {
		foreach ($result as $i => $reg) {
			$tpl->newBlock("fila");
			$tpl->assign("i", $i);
			$tpl->assign("next", $i + 1);
			$tpl->assign('pseudonimo', $reg['nombre']);
			$tpl->assign("importe", number_format($reg['importe'], 2, '.', ','));
		}
		$inicio = count($result);
	}
}

$numfilas = 5;
for ($i = $inicio; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('pseudonimo', '&nbsp;');
	
	if (isset($_SESSION['psr']['p']) && $_SESSION['psr']['p']['num_emp'][$i] > 0) {
		$tpl->assign("id", $_SESSION['psr']['p']['id'][$i]);
		$tpl->assign("num_emp", $_SESSION['psr']['p']['num_emp'][$i]);
		$tpl->assign("nombre", $_SESSION['psr']['p']['nombre'][$i]);
		$tpl->assign("importe", $_SESSION['psr']['p']['importe'][$i]);
	}
}
$tpl->assign("prestamos.total", isset($_SESSION['psr']['p']) ? $_SESSION['psr']['p']['total'] : "0.00");

$tpl->printToScreen();
?>