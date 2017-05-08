<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_com_gue.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['cias'])) {
	$fecha1 = $_GET['fecha1'];
	$fecha2 = $_GET['fecha2'];
	
	$cias = '';
	if ($_GET['cias'] != '') {
		$list = array();
		$range = array();
		
		$pieces = explode(',', $_GET['cias']);
		foreach ($pieces as $p) {
			$r = explode('-', $p);
			if (count($r) > 1)
				$range[] = 'num_cia BETWEEN ' . $r[0] . ' AND ' . $r[1];
			else
				$list[] = $p;
		}
		
		if (count($list) > 0)
			$cias[] = 'num_cia IN (' . implode(', ', $list) . ')';
		if (count($range) > 0)
			$cias[] = implode(' OR ', $range);
		
		$cias = '(' . implode(' OR ', $cias) . ')';
	}
	
	$omitir = '';
	if ($_GET['omitir'] != '') {
		$list = array();
		$range = array();
		
		$pieces = explode(',', $_GET['omitir']);
		foreach ($pieces as $p) {
			$r = explode('-', $p);
			if (count($r) > 1)
				$range[] = 'num_cia NOT BETWEEN ' . $r[0] . ' AND ' . $r[1];
			else
				$list[] = $p;
		}
		
		if (count($list) > 0)
			$omitir[] = 'num_cia NOT IN (' . implode(', ', $list) . ')';
		if (count($range) > 0)
			$omitir[] = implode(' AND ', $range);
		
		$omitir = '(' . implode(' AND ', $omitir) . ')';
	}
	
	if ($_GET['tipo'] == 1) {
		if ($_GET['consultar'] == 'compras') {
			$sql = "SELECT codmp, mp.nombre, sum(cantidad) AS cantidad FROM fact_rosticeria f LEFT JOIN catalogo_mat_primas mp USING (codmp) LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_mov BETWEEN '$fecha1' AND '$fecha2'";
			$sql .= $cias != '' ? ' AND ' . $cias : '';
			$sql .= $omitir != '' ? ' AND ' . $omitir : '';
			$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
			$sql .= $_GET['num_pro'] > 0 ? " AND f.num_proveedor = $_GET[num_pro]" : '';
			$sql .= " AND codmp IN (";
			foreach ($_GET['codmp'] as $i => $mp)
				$sql .= $mp . ($i < count($_GET['codmp']) - 1 ? ', ' : ')');
			$sql .= " GROUP BY codmp, mp.nombre ORDER BY codmp";
		}
		else {
			$sql = "
				SELECT
					codmp,
					mp.nombre,
					SUM(cantidad)
						AS cantidad
				FROM
					mov_inv_real
					LEFT JOIN catalogo_mat_primas mp USING (codmp)
					LEFT JOIN catalogo_companias USING (num_cia)
				WHERE
					fecha BETWEEN '$fecha1' AND '$fecha2'
					AND tipo_mov = TRUE
			";
			$sql .= $_GET['num_cia'] > 0 ? ' AND ' . $cias : '';
			$sql .= $omitir != '' ? ' AND ' . $omitir : '';
			$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
			$sql .= " AND codmp IN (" . implode(', ', $_GET['codmp']) . ")";
			$sql .= "
				GROUP BY
					codmp,
					mp.nombre
				ORDER BY
					codmp";
		}
		
		$result = $db->query($sql);
		
		if (!$result) {
			header('location: ./ros_com_gue.php?codigo_error=1');
			die;
		}
		
		$tpl->newBlock('total');
		$tpl->assign('fecha1', $fecha1);
		$tpl->assign('fecha2', $fecha2);
		
		$total = 0;
		foreach ($result as $reg) {
			$tpl->newBlock('fila');
			$tpl->assign('codmp', $reg['codmp']);
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('cantidad', number_format($reg['cantidad']));
			$total += $reg['cantidad'];
		}
		$tpl->assign('total.total', number_format($total));
	}
	else {
		if ($_GET['consultar'] == 'compras') {
			$sql = "SELECT codmp, mp.nombre, extract(dow from fecha_mov) AS dia, sum(cantidad) AS cantidad FROM fact_rosticeria f LEFT JOIN catalogo_mat_primas mp USING (codmp) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
			$sql .= " fecha_mov BETWEEN '$fecha1' AND '$fecha2'";
			$sql .= $cias != '' ? ' AND ' . $cias : '';
			$sql .= $omitir != '' ? ' AND ' . $omitir : '';
			$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
			$sql .= $_GET['num_pro'] > 0 ? " AND f.num_proveedor = $_GET[num_pro]" : '';
			$sql .= " AND codmp IN (";
			foreach ($_GET['codmp'] as $i => $mp)
				$sql .= $mp . ($i < count($_GET['codmp']) - 1 ? ', ' : ')');
			$sql .= " GROUP BY codmp, mp.nombre, dia ORDER BY codmp";
		}
		else {
			$sql = "
				SELECT
					codmp,
					mp.nombre,
					EXTRACT(dow FROM fecha)
						AS dia,
					SUM(cantidad)
						AS cantidad
				FROM
					mov_inv_real mv
					LEFT JOIN catalogo_mat_primas mp
						USING (codmp)
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					fecha BETWEEN '$fecha1' AND '$fecha2'
					AND tipo_mov = TRUE
			";
			$sql .= $cias != '' ? ' AND ' . $cias : '';
			$sql .= $omitir != '' ? ' AND ' . $omitir : '';
			$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
			$sql .= " AND codmp IN (" . implode(', ', $_GET['codmp']) . ")";
			$sql .= "
				GROUP BY
					codmp,
					mp.nombre,
					dia
				ORDER BY
					codmp";
		}
		
		$result = $db->query($sql);
		
		if (!$result) {
			header('location: ./ros_com_gue.php?codigo_error=1');
			die;
		}
		
		$tpl->newBlock('semanal');
		$tpl->assign('fecha1', $fecha1);
		$tpl->assign('fecha2', $fecha2);
		
		$totales = array(0, 0, 0, 0, 0, 0, 0);
		$codmp = NULL;
		foreach ($result as $reg) {
			if ($codmp != $reg['codmp']) {
				$codmp = $reg['codmp'];
				
				$tpl->newBlock('pro');
				$tpl->assign('codmp', $reg['codmp']);
				$tpl->assign('nombre', $reg['nombre']);
				$total = 0;
			}
			$tpl->assign('cantidad' . $reg['dia'], number_format($reg['cantidad']));
			$total += $reg['cantidad'];
			$totales[$reg['dia']] += $reg['cantidad'];
			$tpl->assign('total', number_format($total));
		}
		foreach ($totales as $i => $total)
			$tpl->assign('semanal.total' . $i, number_format($total));
		
		$tpl->assign('semanal.total', number_format(array_sum($totales)));
	}
	
	$tpl->newBlock($_GET['tipo'] == 1 ? 'back_total' : 'back_semanal');
	$tpl->printToScreen();
	die;
}

$descripcion_error[1] = "No hay resultados";

$tpl->newBlock("datos");
$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores WHERE idadministrador NOT IN (11, 12) ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
die;
?>