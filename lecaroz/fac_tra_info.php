<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/fac/fac_tra_info.tpl");
$tpl->prepare();

$sql = "SELECT nombre, ap_paterno, ap_materno FROM catalogo_trabajadores WHERE id = $_GET[id]";
$result = $db->query($sql);

$emp['nombre'] = trim($result[0]['nombre']);
$emp['ap_paterno'] = trim($result[0]['ap_paterno']);
$emp['ap_materno'] = trim($result[0]['ap_materno']);

$tpl->assign('nombre', $emp['nombre']);
$tpl->assign('ap_paterno', $emp['ap_paterno']);
$tpl->assign('ap_materno', $emp['ap_materno']);

$sql = "SELECT num_cia, cc.nombre, num_emp, fecha_alta, fecha_alta_imss, fecha_baja, fecha_baja_imss, (SELECT importe FROM aguinaldos WHERE id_empleado = ct.id ORDER BY fecha DESC LIMIT 1) AS aguinaldo, (SELECT extract(year from fecha) FROM aguinaldos WHERE id_empleado = ct.id ORDER BY fecha DESC LIMIT 1) AS anio FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) WHERE ct.nombre = '$emp[nombre]' AND ap_paterno = '$emp[ap_paterno]' AND ap_materno = '$emp[ap_materno]' ORDER BY id DESC";
$result = $db->query($sql);

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
	$tpl->assign('num_emp', $reg['num_emp']);
	$tpl->assign('fecha_alta', $reg['fecha_alta'] != '' ? $reg['fecha_alta'] : '&nbsp;');
	$tpl->assign('fecha_alta_imss', $reg['fecha_alta_imss'] != '' ? $reg['fecha_alta_imss'] : '&nbsp;');
	$tpl->assign('fecha_baja', $reg['fecha_baja'] != '' ? $reg['fecha_baja'] : '&nbsp;');
	$tpl->assign('fecha_baja_imss', $reg['fecha_baja_imss'] != '' ? $reg['fecha_baja_imss'] : '&nbsp;');
	$tpl->assign('aguinaldo', $reg['aguinaldo'] > 0 ? number_format($reg['aguinaldo'], 2, '.', ',') : '&nbsp;');
	$tpl->assign('anio', $reg['anio'] > 0 ? $reg['anio'] : '&nbsp;');
	
	if ($reg['fecha_baja'] == '') $tpl->assign('style', 'bgcolor="#FFFF00"');
}

$tpl->printToScreen();
?>