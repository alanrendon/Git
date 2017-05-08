<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_fac_det.tpl");
$tpl->prepare();

if ($_GET['tipo'] == 1) {
	$sql = "SELECT cantidad, codmp, nombre, contenido, descripcion AS unidad, precio, pdesc1 AS desc1, pdesc2 AS desc2, pdesc3 AS desc3,";
	$sql .= " piva AS iva, ieps, importe FROM entrada_mp LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo)";
	$sql .= " WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'";
}
else
	$sql = "SELECT litros, precio_unit AS precio, total FROM factura_gas WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'";
$result = $db->query($sql);

$datos_pro = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_pro]");
$datos_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$datos_fac = $db->query("SELECT fecha FROM facturas WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'");
$tpl->assign('num_cia', $_GET['num_cia']);
$tpl->assign('nombre_cia', $datos_cia[0]['nombre_corto']);
$tpl->assign('num_pro', $_GET['num_pro']);
$tpl->assign('nombre_pro', $datos_pro[0]['nombre']);
$tpl->assign('num_fact', $_GET['num_fact']);
$tpl->assign('fecha', $datos_fac[0]['fecha']);

if ($_GET['tipo'] == 1) {
	$tpl->newBlock('fac_mp');
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('mp');
		$tpl->assign('cantidad', number_format($reg['cantidad'], 2, '.', ','));
		$tpl->assign('codmp', $reg['codmp']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('contenido', number_format($reg['contenido'], 2, '.', ','));
		$tpl->assign('unidad', $reg['unidad']);
		$tpl->assign('precio', number_format($reg['precio'],2 , '.', ','));
		$tpl->assign('desc1', $reg['desc1'] != 0 ? number_format($reg['desc1'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('desc2', $reg['desc2'] != 0 ? number_format($reg['desc2'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('desc3', $reg['desc3'] != 0 ? number_format($reg['desc3'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('iva', $reg['iva'] != 0 ? number_format($reg['iva'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('ieps', $reg['ieps'] != 0 ? number_format($reg['ieps'], 2, '.', ',') : '&nbsp;');
		$desc1 = $reg['importe'] * $reg['desc1'] / 100;
		$desc2 = ($reg['importe'] - $desc1) * $reg['desc2'] / 100;
		$desc3 = ($reg['importe'] - $desc1 - $desc2) * $reg['desc3'] / 100;
		$tpl->assign('importe', number_format(($reg['importe'] - $desc1 - $desc2 - $desc3) * ($reg['iva'] > 0 && $reg['cantidad'] > 0 ? 1.15 : 1), 2, '.', ','));
		$total += ($reg['importe'] - $desc1 - $desc2 - $desc3) * ($reg['iva'] > 0 && $reg['cantidad'] > 0 ? 1.15 : 1);
	}
	$tpl->assign('fac_mp.total', number_format($total, 2, '.', ','));
}
else if ($_GET['tipo'] == 2) {
	$tpl->newBlock('fac_gas');
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('tanque');
		$tpl->assign('litros', number_format($reg['litros'], 2, '.', ','));
		$tpl->assign('precio', number_format($reg['precio'], 2, '.', ','));
		$tpl->assign('iva', $reg['litros'] * $reg['precio'] < $reg['total'] ? '15%' : '&nbsp;');
		$tpl->assign('importe', number_format($reg['total'], 2, '.', ','));
		$total += $reg['total'];
	}
	$tpl->assign('fac_gas.total', number_format($total, 2, '.', ','));
}

$tpl->printToScreen();
?>