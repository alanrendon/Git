<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_est_alz.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = date("01/$_GET[mes]/$_GET[anio]");
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT num_cia, nombre, cod_turno, descripcion, sum(piezas) AS piezas, sum(piezas) * $_GET[precio] AS total FROM produccion";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_turnos ON (cod_turno = produccion.cod_turnos) WHERE cod_turnos IN (";
	foreach ($_GET['cod_turno'] as $i => $turno)
		$sql .= $turno . ($i < count($_GET['cod_turno']) - 1 ? ", " : ")");
	$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= " GROUP BY num_cia, nombre, cod_turnos, cod_turno, descripcion ORDER BY num_cia, cod_turno";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_est_alz.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	
	$num_cia = NULL;
	$gran_total = 0;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $reg['nombre']);
			
			$total = 0;
		}
		$tpl->newBlock("turno");
		$tpl->assign("turno", $reg['descripcion']);
		$tpl->assign("precio", number_format($_GET['precio'], 2, ".", ","));
		$tpl->assign("piezas", $reg['piezas']);
		$tpl->assign("total", number_format($reg['total'], 2, ".", ","));
		
		$total += $reg['total'];
		$gran_total += $reg['total'];
		$tpl->assign("cia.total", number_format($total, 2, ".", ","));
	}
	$tpl->assign("listado.gran_total", number_format($gran_total, 2, ".", ","));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))), "selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>