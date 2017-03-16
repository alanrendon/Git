<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compa��a
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN " . ($_SESSION['iduser'] == 2 ? '900 AND 998' : '1 AND 899');
	$result = $db->query($sql);
	
	die(trim($result ? $result[0]['nombre'] : ''));
}

// [AJAX] Obtener empleados con Infonavit
if (isset($_GET['ce'])) {
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[ce] AND credito_infonavit = 'TRUE' AND num_cia BETWEEN " . ($_SESSION['iduser'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_emp ASC";
	$result = $db->query($sql);
	
	if (!$result) die("-1");
	
	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_emp]-$reg[ap_paterno] $reg[ap_materno] $reg[nombre]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_inf_pen_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT idadministrador AS idadmin, nombre_administrador AS admin, pen.num_cia, cc.nombre_corto AS nombre_cia, num_emp, ct.nombre, ap_paterno, ap_materno, mes, anio, sum(importe) AS importe FROM infonavit_pendientes pen LEFT JOIN catalogo_trabajadores ct ON (ct.id = pen.id_emp) LEFT JOIN catalogo_companias cc ON (cc.num_cia = pen.num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE pen.status = 0";
	$sql .= ' AND pen.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$sql .= $_GET['num_cia'] > 0 && $_GET['id_emp'] == '' ? " AND pen.num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['id_emp'] > 0 ? " AND pen.id_emp = $_GET[id_emp]" : '';
	$sql .= $_GET['idadmin'] > 0 ? " AND cc.idadministrador = $_GET[idadmin]" : '';
	$sql .= " GROUP BY idadmin, admin, pen.num_cia, nombre_cia, num_emp, ct.nombre, ap_paterno, ap_materno, mes, anio ORDER BY idadmin, num_cia, nombre, anio, mes";
	
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./fac_inf_pen_con.php?codigo_error=1'));
	
	// Acomodar registros por compa��a, empleado, fecha, importe
	$data = array();
	foreach ($result as $reg) {
		$data[$reg['num_cia']]['idadmin'] = $reg['idadmin'];
		$data[$reg['num_cia']]['admin'] = $reg['admin'];
		$data[$reg['num_cia']]['nombre'] = $reg['nombre_cia'];
		$data[$reg['num_cia']]['empleados'][$reg['num_emp']]['nombre'] = "$reg[nombre] $reg[ap_paterno] $reg[ap_materno]";
		$data[$reg['num_cia']]['empleados'][$reg['num_emp']]['pendientes'][$reg['anio']][$reg['mes']] = $reg['importe'];
	}
	
	// Obtener n�mero de columnas y sus t�tulos
	/*$sql = "SELECT anio, mes FROM infonavit_pendientes pen LEFT JOIN catalogo_trabajadores ct ON (ct.id = pen.id_emp) LEFT JOIN catalogo_companias cc ON (cc.num_cia = pen.num_cia) WHERE pen.status = 0";
	$sql .= $_GET['num_cia'] > 0 && $_GET['id_emp'] == '' ? " AND pen.num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['id_emp'] > 0 ? " AND pen.id_emp = $_GET[id_emp]" : '';
	$sql .= $_GET['idadmin'] > 0 ? " AND cc.idadministrador = $_GET[idadmin]" : '';
	$sql .= " GROUP BY anio, mes ORDER BY anio, mes";
	$titulos = $db->query($sql);*/
	
	$idadmin = NULL;
	foreach ($data as $num_cia => $cia) {
		if ($idadmin != $cia['idadmin']) {
			$idadmin = $cia['idadmin'];
			
			$tpl->newBlock('listado');
			$tpl->assign('dia', date('d'));
			$tpl->assign('mes', mes_escrito(date('n')));
			$tpl->assign('anio', date('Y'));
			
			$tpl->assign('admin', $cia['admin']);
			$tpl->assign('salto', '<br style="page-break-after:always;">');
			
			// Obtener n�mero de columnas y sus t�tulos
			$sql = "SELECT anio, mes FROM infonavit_pendientes pen LEFT JOIN catalogo_trabajadores ct ON (ct.id = pen.id_emp) LEFT JOIN catalogo_companias cc ON (cc.num_cia = pen.num_cia) WHERE pen.status = 0 AND idadministrador = {$cia['idadmin']}";
			$sql .= $_GET['num_cia'] > 0 && $_GET['id_emp'] == '' ? " AND pen.num_cia = $_GET[num_cia]" : '';
			$sql .= $_GET['id_emp'] > 0 ? " AND pen.id_emp = $_GET[id_emp]" : '';
			$sql .= " GROUP BY anio, mes ORDER BY anio, mes";
			$titulos = $db->query($sql);
			$tpl->assign('colspan_total', count($titulos) + 1);
			
			$total_admin = 0;
		}
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $num_cia);
		$tpl->assign('nombre', $cia['nombre']);
		$tpl->assign('colspan', count($titulos) + 2);
		$tpl->assign('colspan_total', count($titulos) + 1);
		$total_cia = 0;
		
		// Trazar t�tulos
		foreach ($titulos as $t) {
			$tpl->newBlock('column_name');
			$tpl->assign('mes', substr(mes_escrito($t['mes']), 0, 3) . substr($t['anio'], 2, 2));
		}
		$tpl->newBlock('column_name');
		$tpl->assign('mes', 'Total');
		
		// Trazar empleados
		foreach ($cia['empleados'] as $num => $emp) {
			$tpl->newBlock('row');
			$tpl->assign('num', $num);
			$tpl->assign('nombre', $emp['nombre']);
			
			// Trazar pendientes
			$total_emp = 0;
			foreach ($titulos as $t) {
				$tpl->newBlock('cel');
				$tpl->assign('importe', isset($emp['pendientes'][$t['anio']][$t['mes']]) ? number_format($emp['pendientes'][$t['anio']][$t['mes']], 2, '.', ',') : '&nbsp;');
				$total_emp += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
				$total_cia += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
				$total_admin += isset($emp['pendientes'][$t['anio']][$t['mes']]) ? $emp['pendientes'][$t['anio']][$t['mes']] : 0;
				$tpl->assign('cia.total', number_format($total_cia, 2, '.', ','));
				$tpl->assign('listado.total', number_format($total_admin, 2, '.', ','));
			}
			$tpl->newBlock('cel');
			$tpl->assign('importe', '<span style="font-weight:bold;">' . number_format($total_emp, 2, '.', ',') . '</span>');
		}
	}
	$tpl->assign('listado.boton', '<p align=\'center\'><input name="" type="button" class=\'boton\' onClick="document.location=\'./fac_inf_pen_con.php\'" value="Regresar" /></p>');
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$sql = "SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['admin']);
}

$tpl->printToScreen();
?>