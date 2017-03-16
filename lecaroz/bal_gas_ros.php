<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_ros.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$descripcion_error[1] = "No hay resultados";

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))), "selected");
	$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))));
	
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
	die;
}

$fecha1 = "01/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

$sql = "SELECT num_cia, nombre_corto, sum(contado) AS egreso FROM total_fac_ros LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) {
	header("location: ./bal_gas_ros.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
$tpl->assign("dia", date("d", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio'])));
$tpl->assign("mes", mes_escrito($_GET['mes']));
$tpl->assign("anio", $_GET['anio']);

$total = 0;
$sql = "DELETE FROM gastos_caja WHERE fecha = '$fecha2' AND cod_gastos = 59;\n";
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
	$tpl->assign("concepto", "POLLOS");
	$tpl->assign("egreso", number_format($result[$i]['egreso'], 2, '.', ','));
	$tpl->assign("fecha", $fecha2);
	$tpl->assign("total", number_format($result[$i]['egreso'], 2, '.', ','));
	
	$total += $result[$i]['egreso'];
	$sql .= "INSERT INTO gastos_caja (num_cia, cod_gastos, importe, tipo_mov, clave_balance, fecha, fecha_captura, comentario) VALUES ({$result[$i]['num_cia']}, 59, {$result[$i]['egreso']}, 'FALSE', 'FALSE', '$fecha2', CURRENT_DATE, 'POLLOS');\n";
}
$tpl->assign("listado.egreso", number_format($total, 2, ".", ","));
$tpl->assign("listado.neto", number_format($total, 2, ".", ","));

$db->query($sql);
$tpl->printToScreen();
?>