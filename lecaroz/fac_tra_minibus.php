<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_tra_minibus.tpl");
$tpl->prepare();

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_emp, ct.nombre, ap_paterno, ap_materno, num_cia, cc.nombre_corto AS nombre_cia, fecha_alta, fecha_alta_imss, fecha_baja, fecha_baja_imss FROM catalogo_trabajadores AS ct LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : '';
	$sql .= strlen(trim($_GET['nombre'])) > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " ct.nombre LIKE '%" . strtoupper(trim($_GET['nombre'])) . "%'" : '';
	$sql .= strlen(trim($_GET['ap_paterno'])) > 0 ? ($_GET['num_cia'] > 0 || strlen(trim($_GET['nombre'])) > 0 ? ' AND' : '') . " ap_paterno LIKE '%" . strtoupper(trim($_GET['ap_paterno'])) . "%'" : '';
	$sql .= strlen(trim($_GET['ap_materno'])) > 0 ? ($_GET['num_cia'] > 0 || strlen(trim($_GET['nombre'])) > 0 || strlen(trim($_GET['ap_paterno'])) > 0 ? ' AND' : '') . " ap_materno LIKE '%" . strtoupper(trim($_GET['ap_materno'])) . "%'" : '';
	$sql .= $_GET['num_emp'] > 0 ? ($_GET['num_cia'] > 0 || strlen(trim($_GET['nombre'])) > 0 || strlen(trim($_GET['ap_paterno'])) > 0 || strlen(trim($_GET['ap_materno'])) > 0 ? ' AND' : '') . " num_emp = $_GET[num_emp]" : '';
	$sql .= " ORDER BY id";
	$result = $db->query($sql);
	
	$tpl->newBlock('result');
	if (!$result)
		$tpl->newBlock('no_result');
	else
		foreach ($result as $reg) {
			$tpl->newBlock('fila');
			$tpl->assign('num_emp', $reg['num_emp']);
			$tpl->assign('nombre', "$reg[nombre] $reg[ap_paterno] $reg[ap_materno]");
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre_cia', $reg['nombre_cia']);
			$tpl->assign('fecha_alta', $reg['fecha_alta'] != '' ? $reg['fecha_alta'] : '&nbsp;');
			$tpl->assign('alta_imss', $reg['fecha_alta_imss'] != '' ? $reg['fecha_alta_imss'] : '&nbsp;');
			$tpl->assign('fecha_baja', $reg['fecha_baja'] != '' ? $reg['fecha_baja'] : '&nbsp;');
			$tpl->assign('baja_imss', $reg['fecha_baja_imss'] != '' ? $reg['fecha_baja_imss'] : '&nbsp;');
		}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$result = $db->query('SELECT num_cia, nombre FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$tpl->printToScreen();
?>