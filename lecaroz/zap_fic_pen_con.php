<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// [AJAX] Obtener nombre de compaρνa
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN 900 AND 998";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_fic_pen_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, nombre_corto, fecha, acre, cn.nombre, importe FROM otros_depositos LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_nombres cn ON (cn.id = idnombre) WHERE ficha = 'FALSE' AND num_cia BETWEEN 900 AND 998";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['fecha1'] != '' ? ' AND fecha ' . ($_GET['fecha2'] != '' ? "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : "= '$_GET[fecha1]'") : '';
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./zap_fic_pen_con.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('dia', date('d'));
	$tpl->assign('mes', mes_escrito(date('n')));
	$tpl->assign('anio', date('Y'));
	
	$num_cia = NULL;
	$gran_total = 0;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_corto']);
			$total = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('fecha', $reg['fecha']);
		if ($reg['acre'] > 0) {
			$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $reg[acre]");
			$tpl->assign('acre', $tmp[0]['nombre_corto']);
		}
		else $tpl->assign('acre', '&nbsp;');
		$tpl->assign('nombre', trim($reg['nombre']) != '' ? trim($reg['nombre']) : '&nbsp;');
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$total += $reg['importe'];
		$gran_total += $reg['importe'];
		$tpl->assign('cia.total', number_format($total, 2, '.', ','));
	}
	$tpl->assign('listado.gran_total', number_format($gran_total, 2, '.', ','));
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign('fecha1', date('1/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
?>