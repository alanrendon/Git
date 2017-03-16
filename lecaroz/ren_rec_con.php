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
$tpl->assignInclude("body", "./plantillas/ren/ren_rec_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['local'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n"), "selected");
	$tpl->assign("anio", date("Y"));
	
	$arr = $db->query("SELECT cod_arrendador, nombre FROM catalogo_arrendadores ORDER BY cod_arrendador");
	foreach ($arr as $reg) {
		$tpl->newBlock('codarr');
		$tpl->assign('cod', $reg['cod_arrendador']);
		$tpl->assign('nombre', $reg['nombre']);
	}
	
	$local = $db->query("SELECT num_local, nombre_local FROM catalogo_arrendatarios WHERE status = 1");
	foreach ($local as $reg) {
		$tpl->newBlock('numlocal');
		$tpl->assign('num', $reg['num_local']);
		$tpl->assign('nombre', $reg['nombre_local']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	$tpl->printToScreen();
	die;
}

$fecha1 = $_GET['anio'] > 0 ? ($_GET['mes'] > 0 ? "01/$_GET[mes]/$_GET[anio]" : "01/01/$_GET[anio]") : FALSE;
$fecha2 = $_GET['anio'] > 0 ? (date("d/m/Y", $_GET['mes'] > 0 ? mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']) : ($_GET['anio'] == date("Y") ? mktime(0, 0, 0, date("n") + 1, 0, $_GET['anio']) : mktime(0, 0, 0, 12, 31, $_GET['anio'])))) : FALSE;

$sql = "SELECT cod_arrendador, nombre, num_recibo, num_local, nombre_local, nombre_arrendatario, renta, rr.agua, rr.mantenimiento, iva, isr_retenido, iva_retenido, neto, rr.concepto,";
$sql .= " rr.bloque, rr.status, fecha_con, cuenta FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS cat ON (cat.id = local) LEFT JOIN catalogo_arrendadores AS car USING (cod_arrendador) LEFT JOIN estado_cuenta ec ON (ec.local = rr.local AND fecha_renta = rr.fecha AND ec.importe = rr.neto) WHERE rr.status IN (1" . (isset($_GET['cancelados']) ? ', 0' : '') . ")";
$sql .= $_GET['anio'] > 0 ? " AND rr.fecha BETWEEN '$fecha1' AND '$fecha2'" : '';
$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
$sql .= $_GET['recibo'] > 0 ? " AND num_recibo = '$_GET[recibo]'" : "";
$sql .= $_GET['bloque'] > 0 ? " AND rr.bloque = $_GET[bloque]" : "";
$sql .= " ORDER BY rr.bloque, cod_arrendador, num_recibo";
$result = $db->query($sql);

if (!$result) {
	header("location: ./ren_rec_con.php?codigo_error=1");
	die;
}

$bloque = NULL;
foreach ($result as $rec) {
	if ($bloque != $rec['bloque']) {
		$bloque = $rec['bloque'];
		
		$tpl->newBlock("listado");
		$tpl->assign('leyenda', $_GET['anio'] > 0 ? ($_GET['mes'] > 0 ? ' del Mes de ' . mes_escrito($_GET['mes']) . " del $_GET[anio]" : " del Año $_GET[anio]") : '');
		$tpl->assign("mes", mes_escrito($_GET['mes']));
		$tpl->assign("anio", $_GET['anio']);
		$tpl->assign("bloque", $rec['bloque'] == 1 ? "Internos" : "Externos");
		$total_bloque = 0;
		$arr = NULL;
	}
	if ($arr != $rec['cod_arrendador']) {
		$arr = $rec['cod_arrendador'];
		
		$tpl->newBlock("arr");
		$tpl->assign("cod", $rec['cod_arrendador']);
		$tpl->assign("arr", $rec['nombre']);
		
		$total = 0;
	}
	$tpl->newBlock("recibo");
	$tpl->assign("recibo", $rec['num_recibo']);
	$tpl->assign("num", $rec['num_local']);
	$tpl->assign("nombre", $rec['nombre_local']);
	$tpl->assign("arr", $rec['nombre_arrendatario']);
	$tpl->assign("renta", $rec['renta'] != 0 ? number_format($rec['renta'], 2, ".", ",") : "");
	$tpl->assign("agua", $rec['agua'] != 0 ? number_format($rec['agua'], 2, ".", ",") : "");
	$tpl->assign("mant", $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2, ".", ",") : "");
	$tpl->assign("iva", $rec['iva'] != 0 ? number_format($rec['iva'], 2, ".", ",") : "");
	$tpl->assign("isr", $rec['isr_retenido'] != 0 ? number_format($rec['isr_retenido'], 2, ".", ",") : "");
	$tpl->assign("ret", $rec['iva_retenido'] != 0 ? number_format($rec['iva_retenido'], 2, ".", ",") : "");
	$tpl->assign("neto", $rec['neto'] != 0 ? number_format($rec['neto'], 2, ".", ",") : "");
	$tpl->assign('fecha_con', $rec['fecha_con'] != '' ? $rec['fecha_con'] : '&nbsp;');
	$tpl->assign('banco', $rec['cuenta'] > 0 ? ($rec['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
	$total += $rec['neto'];
	$total_bloque += $rec['neto'];
	$tpl->assign("arr.total", number_format($total, 2, ".", ","));
	$tpl->assign("listado.total", number_format($total_bloque, 2, ".", ","));
	if (trim($rec['concepto']) != "") {
		$tpl->newBlock("concepto");
		$tpl->assign("concepto", ($rec['status'] == 0 ? '[CANCELADO] ' : '') . trim($rec['concepto']));
	}
	else if (trim($rec['concepto']) == '' && $rec['status'] == 0) {
		$tpl->newBlock("concepto");
		$tpl->assign("concepto", '[CANCELADO]');
	}
}
$tpl->printToScreen();
?>