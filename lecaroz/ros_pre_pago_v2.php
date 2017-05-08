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
$tpl->assignInclude("body","./plantillas/ros/ros_pre_pago_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	if (floatval(str_replace(",", "", $_POST['total'])) > 0)
		$_SESSION['psr']['pp'] = $_POST;
	else if (isset($_SESSION['psr']['pp']))
		unset($_SESSION['psr']['pp']);
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id_empleado AS id, num_emp, nombre, ap_paterno, ap_materno, importe FROM prestamos LEFT JOIN catalogo_trabajadores ON (catalogo_trabajadores.id = prestamos.id_empleado)";
$sql .= " WHERE (prestamos.num_cia = {$_SESSION['psr']['num_cia']} OR catalogo_trabajadores.num_cia_emp = {$_SESSION['psr']['num_cia']}) AND tipo_mov = 'FALSE' AND pagado = 'FALSE' ORDER BY num_emp";
$prestamos = $db->query($sql);

if (!$prestamos) {
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("pagos");

function buscarID($id) {
	if (!isset($_SESSION['psr']['pp']))
		return FALSE;
	
	foreach ($_SESSION['psr']['pp']['id'] as $i => $value)
		if ($id == $value && $_SESSION['psr']['pp']['importe'][$i] > 0)
			return $i;
	
	return FALSE;
}

foreach ($prestamos as $i => $prestamo) {
	$tpl->newBlock("prestamo");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < count($prestamos) - 1 ? $i + 1 : 0);
	$tpl->assign("id", $prestamo['id']);
	$tpl->assign("num_emp", $prestamo['num_emp']);
	$tpl->assign("nombre", trim("$prestamo[nombre] $prestamo[ap_paterno] $prestamo[ap_materno]"));
	$tpl->assign("prestamo", number_format($prestamo['importe'], 2, ".", ","));
	$abono = $db->query("SELECT sum(importe) AS importe FROM prestamos WHERE id_empleado = $prestamo[id] AND pagado = 'FALSE' AND tipo_mov = 'TRUE'");
	$tpl->assign("resta", number_format($prestamo['importe'] - $abono[0]['importe'], 2, ".", ","));
	
	if (!isset($_SESSION['psr']['pp']))
		$tpl->assign("resta_real", number_format($prestamo['importe'] - $abono[0]['importe'], 2, ".", ","));
	else if (($index = buscarID($prestamo['id'])) !== FALSE) {
		$tpl->assign("importe", $_SESSION['psr']['pp']['importe'][$i]);
		$tpl->assign("resta_real", $_SESSION['psr']['pp']['resta_real'][$i]);
	}
}
$tpl->assign("pagos.total", isset($_SESSION['psr']['pp']) ? $_SESSION['psr']['pp']['total'] : "0.00");

// [25-Feb-2007] Buscar abonos en temporales
if (!isset($_SESSION['psr']['pp'])) {
	$sql = "SELECT nombre, importe FROM prestamos_tmp WHERE num_cia = {$_SESSION['psr']['num_cia']} AND fecha = '{$_SESSION['psr']['fecha']}' AND tipo_mov = 'TRUE'";
	$result = $db->query($sql);
	if ($result) {
		$tpl->newBlock('tmp');
		foreach ($result as $i => $reg) {
			$tpl->newBlock('tmprow');
			$tpl->assign('i', $i);
			$tpl->assign('next', $i < count($result) - 1 ? $i - 1 : 0);
			$tpl->assign('pseudonimo', $reg['nombre']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		}
	}
}

$tpl->printToScreen();
?>