<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia < 100";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_com_con_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$num_cia = $_GET['num_cia'];
	$mes = $_GET['mes'];
	$anio1 = $_GET['anio1'];
	$anio2 = $_GET['anio2'];
	
	$sql = "SELECT codmp, nombre, anio, mes, consumo FROM consumos_mensuales LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $num_cia AND anio IN ($anio1, $anio2) AND mes = $mes ";
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : '';
	$sql .= 'ORDER BY codmp, anio';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./bal_com_con_anu.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
	$tpl->assign('nombre_cia', $nombre[0]['nombre']);
	$tpl->assign('anio1', $anio1);
	$tpl->assign('anio2', $anio2);
	$tpl->assign('mes', mes_escrito($mes));
	
	$codmp = NULL;
	foreach ($result as $reg) {
		if ($codmp != $reg['codmp']) {
			$codmp = $reg['codmp'];
			
			$tpl->newBlock('fila');
			$tpl->assign('codmp', $reg['codmp']);
			$tpl->assign('nombre', $reg['nombre']);
			$dif = 0;
			$con1 = 0;
			$con2 = 0;
		}
		switch ($reg['anio']) {
			case $anio1:
				$tpl->assign('consumo1', number_format(in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'], 2, '.', ','));
				$dif -= in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
				$con1 = in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
				break;
			case $anio2:
				$tpl->assign('consumo2', number_format(in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'], 2, '.', ','));
				$dif += in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
				$con2 = in_array($reg['codmp'], array(1, 3, 4)) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
				break;
		}
		if ($dif < 0)
			@$por = 100 - ($con2 * 100 / $con1);
		else
			@$por = 100 - ($con1 * 100 / $con2);
		
		$tpl->assign('color', $dif < 0 ? 'C00' : '00C');
		$tpl->assign('dif', number_format($dif, 2, '.', ','));
		$tpl->assign('por', number_format($por, 2, '.', ',') . '%');
	}
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
$tpl->assign('anio1', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y') - 1)));
$tpl->assign('anio2', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$tpl->printToScreen();
?>