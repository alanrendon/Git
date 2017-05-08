<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("la estoy modificando");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_gas_zap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$sql = "";
	$num_cia = $_POST['num_cia'];
	$fecha = $_POST['fecha'];
	
	// Validar fecha
	$tmp = $db->query("SELECT mes, anio FROM balances_zap WHERE num_cia = $num_cia ORDER BY anio DESC, mes DESC LIMIT 1");
	$ts_bal = $tmp ? mktime(0, 0, 0, $tmp[0]['mes'] + 1, 0, $tmp[0]['anio']) : mktime(0, 0, 0, date("n") - 1, 0, date("Y"));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $tmp);
	$ts_cap = mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]);
	$ts_now = mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"));
	
	/*if ($ts_cap <= $ts_bal && !in_array($_SESSION['iduser'], array(1, 4, 28))) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos del mes pasado porque ya se generaron balances");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	else */if ($ts_cap > $ts_now) {
		$tpl->newBlock("valid");
		$tpl->assign("mensaje", "No se pueden capturar gastos de dias posteriores al de ayer");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	
	for ($i = 0; $i < $numfilas; $i++) {
		$codgastos = get_val($_POST['codgastos'][$i]);
		$importe = get_val($_POST['importe'][$i]);
		if ($codgastos > 0 && $importe > 0) {
			$concepto = trim(strtoupper($_POST['concepto'][$i]));
			$num_pro = $_POST['num_pro'][$i] > 0 ? $_POST['num_pro'][$i] : 'NULL';
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, concepto, num_proveedor) VALUES ($codgastos, $num_cia, '$fecha', $importe, 'FALSE', '$concepto', $num_pro);\n";
		}
	}
	$total = get_val($_POST['total']);
	if ($id = $db->query("SELECT id FROM total_zapaterias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
		$sql .= "UPDATE total_zapaterias SET gastos = gastos + $total, efectivo = efectivo - $total WHERE id = {$id[0]['id']};\n";
	else
		$sql .= "INSERT INTO total_zapaterias (num_cia, fecha, venta, otros, gastos, efectivo) VALUES ($num_cia, '$fecha', 0, 0, $total, -$total);\n";
	
	$db->query($sql);
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");

// Filas de captura
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Catálogo de Compañías
$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	foreach ($cia as $tag => $value)
		$tpl->assign($tag, $value);
}

// Catálogo de Proveedores
$sql = "SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores WHERE num_proveedor BETWEEN 9000 AND 9999 ORDER BY num_pro";
$pros = $db->query($sql);
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	foreach ($pro as $tag => $value)
		$tpl->assign($tag, $value);
}

// Catálogo de Gastos
$gastos = $db->query("SELECT codgastos, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos");
foreach ($gastos as $gasto) {
	$tpl->newBlock("gasto");
	foreach ($gasto as $tag => $value)
		$tpl->assign($tag, $value);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>