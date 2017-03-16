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
$tpl->assignInclude("body", "./plantillas/ped/ped_exi_mes.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['codmp'])) {
	$codmp = $_GET['codmp'];
	$fecha_his = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$end_day = date('d', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT num_cia, nombre_corto, idadministrador AS admin, nombre_administrador, existencia, (SELECT sum(cantidad) FROM mov_inv_real WHERE num_cia = hi.num_cia AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND tipo_mov = 'FALSE' AND descripcion NOT LIKE 'DIFERENCIA%') AS entradas, (SELECT sum(CASE WHEN tipo_mov = 'TRUE' THEN cantidad ELSE -cantidad END) FROM mov_inv_real WHERE num_cia = hi.num_cia AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND (tipo_mov = 'TRUE' OR (tipo_mov = 'FALSE' AND descripcion LIKE 'DIFERENCIA%'))) AS salidas, (SELECT extract(day from max(fecha)) FROM mov_inv_real WHERE num_cia = hi.num_cia AND codmp = $codmp AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND (tipo_mov = 'TRUE' OR (tipo_mov = 'FALSE' AND descripcion LIKE 'DIFERENCIA%'))) AS dia FROM historico_inventario AS hi LEFT JOIN catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE codmp = $codmp AND fecha = '$fecha_his'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '';
	$sql .= " AND num_cia < 900 ORDER BY ";
	$sql .= isset($_GET['separar']) ? 'nombre_administrador, num_cia' : 'num_cia';
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./ped_exi_mes.php?codigo_error=1');
		die;
	}//echo '<pre>' . print_r($result, TRUE) . '</pre>';
	
	$filas_x_hoja = 54;
	$filas = $filas_x_hoja;
	$admin = NULL;
	
	$t_ini = 0;
	$t_con = 0;
	$t_com = 0;
	$t_fin = 0;
	foreach ($result as $reg) {
		
		
		if ($filas >= $filas_x_hoja || (isset($_GET['separar']) && $admin != $reg['admin'])) {
			$admin = $reg['admin'];
			
			$tpl->newBlock('listado');
			$tpl->assign('codmp', $codmp);
			$tpl->assign('nombre', $_GET['desc']);
			$tpl->assign('mes', mes_escrito($_GET['mes']));
			$tpl->assign('anio', $_GET['anio']);
			
			if (isset($_GET['separar'])) $tpl->assign('admin', $reg['nombre_administrador']);
			
			$filas = 0;
		}
		
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		
		$ini = in_array($codmp, array(1, 3, 4)) ? ($codmp == 1 ? round($reg['existencia'] / 44, 2) : round($reg['existencia'] / 50, 2)) : $reg['existencia'];
		$con = in_array($codmp, array(1, 3, 4)) ? ($codmp == 1 ? round($reg['salidas'] / 44, 2) : round($reg['salidas'] / 50, 2)) : $reg['salidas'];
		$com = in_array($codmp, array(1, 3, 4)) ? ($codmp == 1 ? round($reg['entradas'] / 44, 2) : round($reg['entradas'] / 50, 2)) : $reg['entradas'];
		$fin = round($ini + $com - $con, 2);
		@$prom = in_array($codmp, array(1, 3, 4)) ? ($codmp == 1 ? round($reg['salidas'] / $reg['dia'] / 44, 2) : round($reg['salidas'] / $reg['dia'] / 50, 2)) : round($reg['salidas'] / $reg['dia'], 2);
		
		// [23-May-2008] Calcular dias restantes de consumo
		@$dias = floor($fin / $prom);
		
		$tpl->assign('ini', $ini != 0 ? number_format($ini, 2, '.', ',') : '&nbsp;');
		$tpl->assign('con', $con != 0 ? number_format($con, 2, '.', ',') : '&nbsp;');
		$tpl->assign('com', $com != 0 ? number_format($com, 2, '.', ',') : '&nbsp;');
		$tpl->assign('fin', $fin != 0 ? number_format($fin, 2, '.', ',') : '&nbsp;');
		$tpl->assign('prom', $prom != 0 ? number_format($prom, 2, '.', ',') : '&nbsp;');
		$tpl->assign('dias', $dias > 0 ? $dias : '&nbsp;');
		
		$t_ini += $ini;
		$t_con += $con;
		$t_com += $com;
		$t_fin += $fin;
		
		$filas++;
	}
	$tpl->newBlock('totales');
	$tpl->assign('ini', number_format($t_ini));
	$tpl->assign('con', number_format($t_con));
	$tpl->assign('com', number_format($t_com));
	$tpl->assign('fin', number_format($t_fin));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia < 900 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre');
foreach ($result as $reg) {
	$tpl->newBlock('idadmin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['nombre']);
}

$result = $db->query('SELECT codmp, nombre FROM catalogo_mat_primas ORDER BY codmp');
foreach ($result as $reg) {
	$tpl->newBlock('pro');
	$tpl->assign('codmp', $reg['codmp']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>