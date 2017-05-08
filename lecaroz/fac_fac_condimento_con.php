<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_GET['f'])) {
	$sql = 'SELECT id FROM facturas WHERE num_proveedor = 937 AND num_fact >= ' . $_GET['f'];
	$result = $db->query($sql);
	
	if (!$result)
		echo 1;
	else
		echo -1;
	die;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
	$sql = 'DELETE FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ')';
	$db->query($sql);
	
	die(header('location: ./fac_fac_condimento_con.php'));
}

if (isset($_GET['action']) && $_GET['action'] == 'print') {
	$folio = $_POST['folio_ini'];
	
	$sql = 'SELECT id, num_cia, fecha FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ') ORDER BY num_cia, fecha';
	$result = $db->query($sql);
	
	$sql = 'SELECT num_cia, min(fecha) AS fecha FROM facturacion_condimento WHERE (num_cia) NOT IN (SELECT num_cia FROM inventario_real WHERE codmp = 912) GROUP BY num_cia ORDER BY num_cia';
	$no_inv = $db->query($sql);
	
	$sql = '';
	foreach ($result as $reg)
		$sql .= 'UPDATE facturacion_condimento SET folio = ' . ($folio++) . ' WHERE id = ' . $reg['id'] . ";\n";
	
	if ($no_inv)
		foreach ($no_inv as $reg) {
			ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha'], $tmp);
			$fecha = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 0, $tmp[3]));
			$sql .= 'INSERT INTO historico_inventario (num_cia, codmp, fecha, existencia, precio_unidad) VALUES (';
			$sql .= $reg['num_cia'] . ', 912, \'' . $fecha . '\', 0, 0);' . "\n";
			$sql .= 'INSERT INTO inventario_real (num_cia, codmp, existencia, precio_unidad) VALUES (';
			$sql .= $reg['num_cia'] . ', 912, 0, 0);' . "\n";
		}
	
	$sql .= 'INSERT INTO pasivo_proveedores (num_cia, num_proveedor, num_fact, fecha_mov, fecha_pago, descripcion, codgastos, total, copia_fac) ';
	$sql .= 'SELECT num_cia, 937, folio, fecha, fecha, \'CONDIMENTO\', 200, importe, \'TRUE\' FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ");\n";
	
	$sql .= 'INSERT INTO facturas (num_cia, num_proveedor, num_fact, fecha_mov, fecha_ven, fecha_captura, iduser, codgastos, concepto, tipo_factura, imp_sin_iva, porciento_iva, importe_iva, porciento_ret_isr, porciento_ret_iva, importe_total) ';
	$sql .= 'SELECT num_cia, 937, folio, fecha, fecha, now()::date, ' . $_SESSION['iduser'] . ', 200, \'CONDIMENTO\', 0, importe, 0, 0, 0, 0, importe FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ");\n";
	
	$sql .= 'INSERT INTO entrada_mp (num_cia, num_proveedor, num_documento, fecha, fecha_pago, fecha_captura, iduser, codgasto, pagado, codmp, regalado, cantidad, contenido, precio, porciento_desc_normal, porciento_desc_adicional2, porciento_desc_adicional3, porciento_impuesto, ieps, costo_unitario, costo_total) ';
	$sql .= 'SELECT num_cia, 937, folio, fecha, fecha, now()::date, ' . $_SESSION['iduser'] . ', 200, \'FALSE\', 912, \'FALSE\', kilos, 1, precio, 0, 0, 0, 0, 0, importe, importe FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ");\n";
	
	$sql .= 'INSERT INTO mov_inv_real (num_cia, codmp, fecha, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion, num_proveedor) ';
	$sql .= 'SELECT num_cia, 912, fecha, \'FALSE\', kilos, precio, importe, precio, \'COMPRA F. NO. \' || folio, 937 FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ");\n";
	
	$sql .= 'UPDATE inventario_real SET existencia = existencia + result.kilos, precio_unidad = result.precio FROM (SELECT num_cia, kilos, precio, importe FROM facturacion_condimento WHERE id IN (' . implode(', ', $_POST['id']) . ')) result WHERE inventario_real.num_cia = result.num_cia AND codmp = 912;' . "\n";
	
	$sql .= 'UPDATE facturacion_condimento SET tsprint = now() WHERE id IN (' . implode(', ', $_POST['id']) . ");\n";
	
	$db->query($sql);
	
	$sql = 'SELECT num_cia, nombre, direccion, rfc, fecha, folio, kilos, precio, importe FROM facturacion_condimento LEFT JOIN catalogo_companias USING (num_cia) WHERE id IN (' . implode(', ', $_POST['id']) . ') ORDER BY folio ASC';
	$result = $db->query($sql);
	
	$tpl = new TemplatePower('./plantillas/ban/factura_carta.tpl');
	$tpl->prepare();
	
	foreach ($result as $reg) {
		$tpl->newBlock('factura');
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('direccion', $reg['direccion']);
		$tpl->assign('rfc', $reg['rfc']);
		ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $reg['fecha'], $tmp);
		$tpl->assign('dia1', $tmp[1]);
		$tpl->assign('mes', mes_escrito($tmp[2], TRUE));
		$tpl->assign('anio', $tmp[3]);
		$tpl->assign('cantidad1', number_format($reg['kilos'], 2, '.', ','));
		$tpl->assign('descripcion1', '[' . $reg['folio'] . ']CONDIMENTO');
		$tpl->assign('pu1', number_format($reg['precio'], 2, '.', ','));
		$tpl->assign('importe1', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('subtotal', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('iva', '0.00');
		$tpl->assign('total', number_format($reg['importe'], 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

$tpl = new TemplatePower('./plantillas/header.tpl');

$tpl->assignInclude('body', './plantillas/fac/fac_fac_condimento_con.tpl');
$tpl->prepare();

$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

$sql = 'SELECT id, num_cia, nombre_corto, fecha, kilos, precio, importe FROM facturacion_condimento LEFT JOIN catalogo_companias USING (num_cia) WHERE tsprint IS NULL ORDER BY num_cia, fecha';
$result = $db->query($sql);

$totalKilos = 0;
$totalFac = 0;
if ($result)
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('kilos', number_format($reg['kilos'], 2, '.', ','));
		$tpl->assign('precio', number_format($reg['precio'], 2, '.', ','));
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		
		$totalKilos += $reg['kilos'];
		$totalFac += $reg['importe'];
	}

$tpl->assign('_ROOT.kilos', number_format($totalKilos, 2, '.', ','));
$tpl->assign('_ROOT.total', number_format($totalFac, 2, '.', ','));

$tpl->printToScreen();
?>
