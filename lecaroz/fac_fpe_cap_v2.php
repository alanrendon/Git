<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_GET['c'])) {
	$sql = 'SELECT nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 899') . ' AND num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre_corto'];
	die;
}

if (isset($_GET['p'])) {
	$sql = 'SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = ' . $_GET['p'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	die;
}

if (isset($_GET['g'])) {
	$sql = 'SELECT descripcion FROM catalogo_gastos WHERE codgastos = ' . $_GET['g'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['descripcion'];
	die;
}

if (isset($_GET['pro']) && isset($_GET['fac'])) {
	$sql = 'SELECT id FROM facturas WHERE num_proveedor = ' . $_GET['pro'] . ' AND num_fact = ' . $_GET['fac'];
	$result = $db->query($sql);
	
	if ($result)
		echo '-1';
	else
		echo '0';
	die;
}

if (isset($_GET['f'])) {
	$sql = 'SELECT fecha FROM balances_pan WHERE fecha + interval \'1 month\' - interval \'1 day\' >= \'' . $_GET['f'] . '\' ORDER BY fecha DESC LIMIT 1';
	$result = $db->query($sql);
	
	if ($result)
		echo '-1';
	else
		echo '0';
	die;
}

if (isset($_POST['num_cia'])) {echo '<pre>' . print_r($_POST, TRUE) . '</pre>';
	$sql = 'INSERT INTO facturas (num_cia, num_proveedor, num_fact, fecha_mov, fecha_ven, imp_sin_iva, porciento_iva, importe_iva, porciento_ret_isr, porciento_ret_iva, codgastos, importe_total, tipo_factura, fecha_captura, iduser, concepto, anio, cuenta) VALUES (';
	$sql .= $_POST['num_cia'] . ', ';
	$sql .= $_POST['num_pro'] . ', ';
	$sql .= $_POST['num_fact'] . ', \'';
	$sql .= $_POST['fecha'] . '\', \'';
	$sql .= $_POST['fecha'] . '\', ';
	$sql .= round(get_val($_POST['total']) / 1.15, 2) . ', ';
	$sql .= '15, ';
	$sql .= round(get_val($_POST['total']) - (get_val($_POST['total']) / 1.15), 2) . ', ';
	$sql .= '0, 0, ';
	$sql .= $_POST['codgastos'] . ', ';
	$sql .= get_val($_POST['total']) . ', ';
	$sql .= '3, now()::date, ';
	$sql .= $_SESSION['iduser'] . ', \'';
	$sql .= strtoupper(trim($_POST['concepto'])) . '\', ';
	$sql .= ($_POST['anio'] > 0 ? $_POST['anio'] : 'NULL') . ', ';
	$sql .= $_POST['bimestre'] > 0 ? $_POST['bimestre'] : 'NULL';
	$sql .= ");\n";
	
	$sql .= 'INSERT INTO pasivo_proveedores (num_cia, num_proveedor, num_fact, fecha_mov, fecha_pago, codgastos, descripcion, total) VALUES (';
	$sql .= $_POST['num_cia'] . ', ';
	$sql .= $_POST['num_pro'] . ', ';
	$sql .= $_POST['num_fact'] . ', \'';
	$sql .= $_POST['fecha'] . '\', \'';
	$sql .= $_POST['fecha'] . '\', ';
	$sql .= $_POST['codgastos'] . ', \'';
	$sql .= strtoupper(trim($_POST['concepto'])) . '\', ';
	$sql .= get_val($_POST['total']);
	$sql .= ");\n";
	
	$db->query($sql);
	die(header('location: fac_fpe_cap_v2.php'));
}

$tpl = new TemplatePower('./plantillas/header.tpl');

$tpl->assignInclude('body', './plantillas/fac/fac_fpe_cap_v2.tpl');
$tpl->prepare();

$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

$tpl->printToScreen();
?>
