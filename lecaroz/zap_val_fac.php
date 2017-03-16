<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_val_fac.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['total'])) {
	$sql = '';
	for ($i = 0; $i < count($_POST['total']); $i++)
		if (isset($_POST['por_aut' . $i])) {
			if (get_val($_POST['total'][$i]) != get_val($_POST['total_ant'][$i])) {
				$importe = get_val($_POST['importe'][$i]);
				$pdesc1 = get_val($_POST['pdesc1'][$i]);
				$pdesc2 = get_val($_POST['pdesc2'][$i]);
				$pdesc3 = get_val($_POST['pdesc3'][$i]);
				$pdesc4 = get_val($_POST['pdesc4'][$i]);
				$desc1 = round($importe * $pdesc1 / 100, 2);
				$desc2 = round(($importe - $desc1) * $pdesc2 / 100, 2);
				$desc3 = round(($importe - $desc1 - $desc2) * $pdesc3 / 100, 2);
				$desc4 = round(($importe - $desc1 - $desc2 - $desc3) * $pdesc4 / 100, 2);
				$subtotal = $importe - $desc1 - $desc2 - $desc3 - $desc4 - get_val($_POST['falt'][$i]);
				$iva = round($subtotal * 0.15, 2);
				$total = round($subtotal + $iva, 2);
				
				$sql .= "UPDATE facturas_zap SET pdesc1 = $pdesc1, pdesc2 = $pdesc2, pdesc3 = $pdesc3, pdesc4 = $pdesc4, desc1 = $desc1, desc2 = $desc2, desc3 = $desc3, desc4 = $desc4,";
				$sql .= " iva = $iva, total = $total, por_aut = 'TRUE' WHERE id = {$_POST['por_aut'. $i]};\n";
			}
			else
				$sql .= "UPDATE facturas_zap SET por_aut = 'TRUE' WHERE id = {$_POST['por_aut' . $i]};\n";
		}
	
	if ($sql != '') $db->query($sql);
	
	die(header('location: ./zap_val_fac.php'));
}

if (isset($_GET['num_cia'])) {
	$anio = date('Y');
	
	$sql = "SELECT f.id, num_cia, cc.nombre_corto AS nombre_cia, fecha, f.num_proveedor AS num_pro, clave, cp.nombre AS nombre_pro, num_fact, codgastos, descripcion AS desc, importe, pdesc1, pdesc2,";
	$sql .= " pdesc3, pdesc4, faltantes, iva, total FROM facturas_zap AS f LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia)";
	$sql .= " LEFT JOIN catalogo_gastos USING (codgastos) WHERE f.sucursal <> 'TRUE' AND por_aut = 'FALSE' AND folio IS NULL AND /*extract(year from fecha) = $anio*//* fecha >= CURRENT_DATE - interval '6 months' AND*/ tspago IS NULL";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND f.num_proveedor = $_GET[num_pro]" : '';
	$sql .= $_GET['num_pro'] > 0 ? ($_GET['clave'] > 0 ? " AND clave = $_GET[clave]" : ' AND clave = 0') : '';
	$sql .= $_GET['fecha1'] != '' && !isset($_GET['cred']) ? ($_GET['fecha2'] != '' ? " AND fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fecha = '$_GET[fecha]'") : '';
	$sql .= isset($_GET['cred']) ? " AND (fecha_rec, fecha_rec + interval '30 days') OVERLAPS (CURRENT_DATE, CURRENT_DATE + interval '30 days')" : '';
	$sql .= " ORDER BY num_pro, clave, num_fact";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./zap_val_fac.php?codigo_error=1');
		die;
	}
	
	$tpl->newBlock('result');
	
	$num_pro = NULL;
	$clave = NULL;
	foreach ($result as $i => $reg) {
		if ($num_pro != $reg['num_pro'] || $clave != $reg['clave']) {
			$num_pro = $reg['num_pro'];
			$clave = $reg['clave'];
			
			$tpl->newBlock('pro');
			$tpl->assign('num_pro', $reg['num_pro'] . ($clave > 0 ? "-$clave" : ''));
			$tpl->assign('nombre', $reg['nombre_pro']);
		}
		$tpl->newBlock('fac');
		$tpl->assign('i', $i);
		$tpl->assign('index', count($result) > 1 ? "[$i]" : '');
		$tpl->assign('back', count($result) > 1 ? '[' . ($i > 0 ? $i - 1 : count($result) - 1) . ']' : '');
		$tpl->assign('next', count($result) > 1 ? '[' . ($i < count($result) - 1 ? $i + 1 : 0) . ']' : '');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('codgastos', $reg['codgastos']);
		$tpl->assign('desc', $reg['desc']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('pdesc1', $reg['pdesc1'] > 0 ? number_format($reg['pdesc1'], 0, '.', ',') : '');
		$tpl->assign('pdesc2', $reg['pdesc2'] > 0 ? number_format($reg['pdesc2'], 0, '.', ',') : '');
		$tpl->assign('pdesc3', $reg['pdesc3'] > 0 ? number_format($reg['pdesc3'], 0, '.', ',') : '');
		$tpl->assign('pdesc4', $reg['pdesc4'] > 0 ? number_format($reg['pdesc4'], 0, '.', ',') : '');
		$tpl->assign('falt', $reg['faltantes'] > 0 ? number_format($reg['faltantes'], 2, '.', ',') : '');
		$tpl->assign('iva', $reg['iva'] > 0 ? number_format($reg['iva'], 2, '.', ',') : '');
		$tpl->assign('total', number_format($reg['total'], 2, '.', ','));
	}
	
	$result = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos');
	foreach ($result as $reg) {
		$tpl->newBlock('g');
		$tpl->assign('cod', $reg['cod']);
		$tpl->assign('desc', $reg['desc']);
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>