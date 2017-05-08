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
$tpl->assignInclude("body","./plantillas/pan/pan_cli_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['anio'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['anio'] < date('Y') ? 12 : date('n'), $_GET['anio'] < date('Y') ? 31 : 0, $_GET['anio']));
	$cias = array();
	foreach ($_GET['num_cia'] as $c)
		if ($c > 0)
			$cias[] = $c;
	
	$sql = "SELECT num_cia, nombre_corto AS nombre, sum(ctes) AS clientes, extract(month from fecha) as mes FROM captura_efectivos LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
	if (count($cias) > 0) {
		$sql .= ' AND num_cia IN (';
		foreach ($cias as $i => $c)
			$sql .= $c . ($i < count($cias) - 1 ? ', ' : ')');
	}
	$sql .= ' GROUP BY num_cia, nombre_corto, mes ORDER BY num_cia, mes';
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./pan_cli_anu.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('anio', $_GET['anio']);
	
	$num_cia = NULL;
	$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre']);
			
			$total = 0;
			$promedio = 0;
		}
		$tpl->assign($reg['mes'], number_format($reg['clientes']));
		$total += $reg['clientes'];
		$total_mes[$reg['mes']] += $reg['clientes'];
		$tpl->assign('total_cia', number_format($total));
		$tpl->assign('prom_cia', number_format(round($total / $reg['mes'])));
	}
	
	foreach ($total_mes as $m => $t)
		$tpl->assign('listado.' . $m, $t > 0 ? number_format($t) : '');
	
	$tpl->assign('listado.total_cias', number_format(array_sum($total_mes)));
	$tpl->assign('listado.total_prom', number_format(array_sum($total_mes) / count($total_mes)));
	
	if (isset($_GET['excel'])) {
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename=Clientes_' . $_GET['anio'] . '.xls');
		
		die($tpl->getOutputContent());
	}
	else
		die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$tpl->assign('anio', date('Y'));

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