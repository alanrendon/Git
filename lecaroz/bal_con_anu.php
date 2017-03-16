<?php
// LISTADO DE ESTADOS DE CUENTA
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_con_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$anio1 = $_GET['anio'] - 4;
	$anio2 = $_GET['anio'];
	$anios = array();
	for ($anio = $anio1; $anio <= $anio2; $anio++)
		$anios[] = $anio;
	
	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, codmp, cmp.nombre AS nombre_mp, anio, avg(consumo) AS promedio FROM consumos_mensuales cm LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_mat_primas cmp USING (codmp) WHERE anio BETWEEN $anio1 AND $anio2";
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : '';
	if (count($cias) > 0) {
		$sql .= ' AND num_cia IN (';
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ', ' : ')');
	}
	$sql .= ' GROUP BY num_cia, nombre_cia, codmp, nombre_mp, anio ORDER BY codmp, num_cia, anio';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./bal_con_anu.php?codigo_error=1'));
	
	$numfilas_x_hoja = 58;
	$codmp = NULL;
	foreach ($result as $i => $reg) {
		if ($codmp != $reg['codmp']) {
			if ($codmp != NULL) {
				$tpl->newBlock('totales');
				foreach ($total as $j => $t)
					$tpl->assign('prom' . $j, number_format($t, 2, '.', ','));
			}
			
			$codmp = $reg['codmp'];
			
			$tpl->newBlock('listado');
			$tpl->assign('mp', $reg['nombre_mp']);
			
			foreach ($anios as $j => $anio)
				$tpl->assign('anio' . $j, $anio);
			
			$num_cia = NULL;
			$total = array(0, 0, 0, 0, 0);
			$numfilas = 0;
		}
		if ($num_cia != $reg['num_cia']) {
			if ($numfilas >= $numfilas_x_hoja) {
				$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
				
				$tpl->newBlock('listado');
				$tpl->assign('mp', $reg['nombre_mp']);
				
				foreach ($anios as $j => $anio)
					$tpl->assign('anio' . $j, $anio);
				
				$numfilas = 0;
			}
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('fila');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
			
			$numfilas++;
		}
		switch ($codmp) {
			case 1:
				$promedio = $reg['promedio'] / 44;
				break;
			case 3:
			case 4:
				$promedio = $reg['promedio'] / 50;
				break;
			default:
				$promedio = $reg['promedio'];
		}
		$tpl->assign('prom' . array_search($reg['anio'], $anios), number_format($promedio, 2, '.', ','));
		$total[array_search($reg['anio'], $anios)] += $promedio;
	}
	if ($codmp != NULL) {
		$tpl->newBlock('totales');
		foreach ($total as $j => $t)
			$tpl->assign('prom' . $j, number_format($t, 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>