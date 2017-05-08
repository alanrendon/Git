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
$tpl->assignInclude("body","./plantillas/fac/fac_fac_acl.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$sql = '';
	for ($i = 0; $i < count($_POST['id']); $i++)
		if ($_POST['num_fact_nuevo'][$i] > 0) {
			$sql .= "UPDATE facturas SET num_fact = {$_POST['num_fact_nuevo'][$i]} WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = {$_POST['num_fact'][$i]};\n";
			$sql .= "UPDATE pasivo_proveedores SET num_fact = {$_POST['num_fact_nuevo'][$i]} WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = {$_POST['num_fact'][$i]};\n";
			$sql .= "UPDATE entrada_mp SET num_documento = {$_POST['num_fact_nuevo'][$i]} WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_documento = {$_POST['num_fact'][$i]};\n";
			$sql .= "UPDATE factura_gas SET num_fact = {$_POST['num_fact_nuevo'][$i]} WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = {$_POST['num_fact'][$i]};\n";
			$sql .= "UPDATE mov_inv_real SET descripcion = 'COMPRA F. NO. {$_POST['num_fact_nuevo'][$i]} WHERE num_proveedor = {$_POST['num_pro'][$i]}' AND concepto LIKE 'COMPRA F. NO. {$_POST['num_fact'][$i]}';\n";
			$sql .= "UPDATE facturas_pendientes SET fecha_aclaracion = CURRENT_DATE, num_fact_nuevo = {$_POST['num_fact_nuevo'][$i]}, imp = 'TRUE' WHERE id = {$_POST['id'][$i]};\n";
		}
	
	if ($sql != '') $db->query($sql);
	die(header('location: ./fac_fac_acl.php?list=1'));
}

if (isset($_GET['list'])) {
	$sql = "SELECT num_cia, cc.nombre_corto, fp.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, fecha_solicitud, obs, importe_total, fecha_aclaracion, num_fact_nuevo";
	$sql .= " FROM facturas_pendientes AS fp LEFT JOIN facturas AS f USING (num_proveedor, num_fact) LEFT JOIN catalogo_proveedores AS cp USING";
	$sql .= " (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE imp = 'TRUE' ORDER BY num_pro, num_fact";
	$result = $db->query($sql);
	
	$db->query("UPDATE facturas_pendientes SET imp = 'FALSE' WHERE imp = 'TRUE'");
	
	$tpl->newBlock('aclarados');
	$tpl->aasign('leyenda', '<br />al ' . date('d') . ' de ' . mes_escrito(date('n')) . ' de ' . date('Y'));
	foreach ($result as $reg) {
		$tpl->newBlock('acl');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre_cia', $reg['nombre_corto']);
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre_pro', $reg['nombre_pro']);
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('fecha', $reg['fecha_solicitud']);
		$tpl->assign('total', number_format($reg['importe_total'], 2, '.', ','));
		$tpl->assign('obs', $reg['obs']);
		$tpl->assign('num_fact_nuevo', $reg['num_fact_nuevo']);
		$tpl->assign('fecha_aclaracion', $reg['fecha_aclaración']);
	}
	$tpl->printToScreen();
	die;
}

if (isset($_GET['num_cia'])) {
	if ($_GET['tipo'] == 1) {
		$sql = "SELECT fp.id, num_cia, cc.nombre_corto, fp.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, fecha_solicitud, obs, importe_total";
		$sql .= " FROM facturas_pendientes AS fp LEFT JOIN facturas AS f USING (num_proveedor, num_fact) LEFT JOIN catalogo_proveedores AS cp USING";
		$sql .= " (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE fecha_aclaracion IS NULL";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['num_pro'] > 0 ? " AND fp.num_proveedor = $_GET[num_pro]" : '';
		$sql .= $_GET['num_fact'] > 0 ? " AND num_fact = $_GET[num_fact]" : '';
		$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND fecha_solicitud BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fecha_solicitud = $_GET[fecha1]") : '';
		$sql .= " ORDER BY num_pro, num_fact";
		$result = $db->query($sql);
		
		if (!$result) die(header('location: ./fac_fac_acl.php?codigo_error=1'));
		
		$tpl->newBlock('aclarar');
		foreach ($result as $reg) {
			$tpl->newBlock('fila');
			$tpl->assign('id', $reg['id']);
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre_cia', $reg['nombre_corto']);
			$tpl->assign('num_pro', $reg['num_pro']);
			$tpl->assign('nombre_pro', $reg['nombre_pro']);
			$tpl->assign('num_fact', $reg['num_fact']);
			$tpl->assign('fecha', $reg['fecha_solicitud']);
			$tpl->assign('total', number_format($reg['importe_total'], 2, '.', ','));
			$tpl->assign('obs', $reg['obs']);
		}
	}
	else {
		$sql = "SELECT num_cia, cc.nombre_corto, fp.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, fecha_solicitud, obs, importe_total, fecha_aclaracion, num_fact_nuevo";
		$sql .= " FROM facturas_pendientes AS fp LEFT JOIN facturas AS f USING (num_proveedor, num_fact) LEFT JOIN catalogo_proveedores AS cp USING";
		$sql .= " (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE fecha_aclaracion IS " . ($_GET['tipo'] == 2 ? 'NULL' : 'NOT NULL');
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['num_pro'] > 0 ? " AND fp.num_proveedor = $_GET[num_pro]" : '';
		$sql .= $_GET['num_fact'] > 0 ? " AND num_fact = $_GET[num_fact]" : '';
		$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND fecha_" . ($_GET['tipo'] == 2 ? 'solicitud' : 'aclaracion') . " BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fecha_" . ($_GET['tipo'] == 2 ? 'solicitud' : 'aclaracion') . " = $_GET[fecha1]") : '';
		$sql .= " ORDER BY num_pro, num_fact";
		$result = $db->query($sql);
		
		if (!$result) die(header('location: ./fac_fac_acl.php?codigo_error=1'));
		
		$tpl->newBlock($_GET['tipo'] == 2 ? 'pendientes' : 'aclarados');
		foreach ($result as $reg) {
			$tpl->newBlock($_GET['tipo'] == 2 ? 'pen' : 'acl');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre_cia', $reg['nombre_corto']);
			$tpl->assign('num_pro', $reg['num_pro']);
			$tpl->assign('nombre_pro', $reg['nombre_pro']);
			$tpl->assign('num_fact', $reg['num_fact']);
			$tpl->assign('fecha', $reg['fecha_solicitud']);
			$tpl->assign('total', number_format($reg['importe_total'], 2, '.', ','));
			$tpl->assign('obs', $reg['obs']);
			if ($_GET['tipo'] == 3) {
				$tpl->assign('num_fact_nuevo', $reg['num_fact_nuevo']);
				$tpl->assign('fecha_aclaracion', $reg['fecha_aclaración']);
			}
		}
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia AS num, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia < 900 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT num_proveedor AS num, nombre FROM catalogo_proveedores WHERE num_proveedor < 9000 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die();
?>