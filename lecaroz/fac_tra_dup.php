<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_dup.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['search'])) {
	$repetidos = array();
	$encontrados = array();
	
	// BUSQUEDA DE NIVEL 1. EMPLEADOS REPETIDOS POR NOMBRE, AP. PATERNO, AP. MATERNO IGUALES
	$sql = "SELECT num_cia, catalogo_companias.nombre_corto, catalogo_trabajadores.num_emp, catalogo_trabajadores.nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, fecha_alta";
	$sql .= " FROM catalogo_trabajadores LEFT JOIN catalogo_companias USING(num_cia) LEFT JOIN catalogo_turnos USING(cod_turno) LEFT JOIN catalogo_puestos USING(cod_puestos)";
	$sql .= " WHERE fecha_baja IS NULL ORDER BY ap_paterno, ap_materno, nombre, fecha_alta";
	$result1 = $db->query($sql);
	
	for ($i = 0; $i < count($result1); $i++)
		for ($j = $i + 1; $j < count($result1); $j++)
			if ($result1[$i]['ap_paterno'] == $result1[$j]['ap_paterno'] && $result1[$i]['ap_materno'] == $result1[$j]['ap_materno'] && $result1[$i]['nombre'] == $result1[$j]['nombre']) {
				$repetidos[] = $result1[$i];
				$encontrados[] = $result1[$i]['num_emp'];
				$repetidos[] = $result1[$j];
				$encontrados[] = $result1[$j]['num_emp'];
				$i = $j;
			}
	
	// BUSQUEDA DE NIVEL 2. EMPLEADOS REPETIDOS POR NOMBRE, AP. PATERNO = AP. MATERNO, AP. MATERNO = AP. PATERNO
	$sql = "SELECT num_cia, catalogo_companias.nombre_corto, catalogo_trabajadores.num_emp, catalogo_trabajadores.nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, fecha_alta";
	$sql .= " FROM catalogo_trabajadores LEFT JOIN catalogo_companias USING(num_cia) LEFT JOIN catalogo_turnos USING(cod_turno) LEFT JOIN catalogo_puestos USING(cod_puestos)";
	$sql .= " WHERE fecha_baja IS NULL ORDER BY ap_materno, ap_paterno, nombre, fecha_alta";
	$result2 = $db->query($sql);
	
	for ($i = 0; $i < count($result1); $i++)
		for ($j = 0; $j < count($result2); $j++)
			if (array_search($result1[$i]['num_emp'], $encontrados) === FALSE && $result1[$i]['num_emp'] != $result2[$j]['num_emp'] && $result1[$i]['ap_paterno'] == $result2[$j]['ap_materno'] && $result1[$i]['ap_materno'] == $result2[$j]['ap_paterno'] && $result1[$i]['nombre'] == $result2[$j]['nombre']) {
				$repetidos[] = $result1[$i];
				$encontrados[] = $result1[$i]['num_emp'];
				$repetidos[] = $result2[$j];
				$encontrados[] = $result2[$j]['num_emp'];
			}
	
	// BUSQUEDA DE NIVEL 3. EMPLEADOS REPETIDOS POR NOMBRE Y AP. PATERNO
	/*$sql = "SELECT num_cia, catalogo_companias.nombre_corto, catalogo_trabajadores.num_emp, catalogo_trabajadores.nombre, ap_paterno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, fecha_alta";
	$sql .= " FROM catalogo_trabajadores LEFT JOIN catalogo_companias USING(num_cia) LEFT JOIN catalogo_turnos USING(cod_turno) LEFT JOIN catalogo_puestos USING(cod_puestos)";
	$sql .= " WHERE fecha_baja IS NULL AND ap_materno IS NULL ORDER BY ap_paterno, nombre, fecha_alta";
	$result3 = $db->query($sql);
	
	for ($i = 0; $i < count($result1); $i++)
		for ($j = 0; $j < count($result3); $j++)
			if (array_search($result1[$i]['num_emp'], $encontrados) === FALSE && $result1[$i]['num_emp'] != $result3[$j]['num_emp'] && $result1[$i]['ap_paterno'] == $result3[$j]['ap_paterno'] && $result1[$i]['nombre'] == $result3[$j]['nombre']) {
				$repetidos[] = $result1[$i];
				$encontrados[] = $result1[$i]['num_emp'];
				$repetidos[] = $result3[$j];
				$encontrados[] = $result3[$j]['num_emp'];
			}*/
	
	// MOSTRAR RESULTADOS
	if (count($repetidos) > 0) {
		$tpl->newBlock("listado");
		
		for ($i = 0; $i < count($repetidos); $i++) {
			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $repetidos[$i]['num_cia']);
			$tpl->assign("nombre_cia", $repetidos[$i]['nombre_corto']);
			$tpl->assign("num_emp", $repetidos[$i]['num_emp']);
			$tpl->assign("nombre", "{$repetidos[$i]['ap_paterno']} {$repetidos[$i]['ap_materno']} {$repetidos[$i]['nombre']}");
			$tpl->assign("puesto", $repetidos[$i]['puesto']);
			$tpl->assign("turno", $repetidos[$i]['turno']);
			$tpl->assign("fecha_alta", $repetidos[$i]['fecha_alta']);
		}
		$tpl->printToScreen();
	}
	
	die;
}

$tpl->newBlock("datos");

$tpl->printToScreen();
?>