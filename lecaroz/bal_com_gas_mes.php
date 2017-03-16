<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

if (isset($_GET['c'])) {
	$reg = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800'));
	if (!$reg) die();
	else {
		echo $reg[0]['nombre_corto'];
		die;
	}
}

if (isset($_GET['g'])) {
	$reg = $db->query("SELECT descripcion AS desc FROM catalogo_gastos WHERE codgastos = $_GET[g]");
	if (!$reg) die();
	else {
		echo $reg[0]['desc'];
		die;
	}
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_gas_mes.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$cod = $_GET['cod'];
	$mes1 = $_GET['mes1'];
	$anio1 = $_GET['anio1'];
	$mes2 = $_GET['mes2'];
	$anio2 = $_GET['anio2'];
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes1, 1, $anio1));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes1 + 1, 0, $anio1));
	$fecha3 = date('d/m/Y', mktime(0, 0, 0, $mes2, 1, $anio2));
	$fecha4 = date('d/m/Y', mktime(0, 0, 0, $mes2 + 1, 0, $anio2));
	
	$sql = 'SELECT num_cia, nombre_corto AS nombre_cia, idadministrador AS idadmin, nombre_administrador AS nombre_admin, codgastos, descripcion,';
	$sql .= ' extract(month from fecha) AS mes, extract(year from fecha) AS anio, sum(importe) AS importe FROM movimiento_gastos g LEFT JOIN';
	$sql .= ' catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) LEFT JOIN catalogo_gastos cg USING (codgastos)';
	$sql .= " WHERE codgastos = $cod";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '';
	$sql .= " AND (fecha BETWEEN '$fecha1' AND '$fecha2' OR fecha BETWEEN '$fecha3' AND '$fecha4')";
	$sql .= ' GROUP BY num_cia, nombre_cia, idadmin, nombre_admin, codgastos, descripcion, mes, anio';
	$sql .= ' ORDER BY ' . (isset($_GET['div']) || isset($_GET['ord_adm']) ? 'nombre_admin, num_cia, anio, mes' : 'num_cia, anio, mes');
	
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./bal_com_gas_mes.php?codigo_error=1'));
	
	$idadmin = NULL;
	foreach ($result as $i => $reg) {
		if ($idadmin != $reg['idadmin']) {
			if ($idadmin != NULL) {
				if (isset($_GET['div']))
					$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
			}
			
			$idadmin = $reg['idadmin'];
			
			if (isset($_GET['div']) || $i == 0) {
				$tpl->newBlock('listado');
				$tpl->assign('cod', $cod);
				$tpl->assign('desc', $reg['descripcion']);
				if (isset($_GET['div']))
					$tpl->assign('admin', '<br />' . $reg['nombre_admin']);
				$tpl->assign('mes1', mes_escrito($mes1));
				$tpl->assign('anio1', $anio1);
				$tpl->assign('mes2', mes_escrito($mes2));
				$tpl->assign('anio2', $anio2);
				
				$total = array(1 => 0, 2 => 0);
			}
			
			$num_cia = NULL;
		}
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('fila');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
			
			$dif = 0;
		}
		$tpl->assign($reg['anio'] == $anio1 && $reg['mes'] == $mes1 ? 'importe1' : 'importe2', number_format($reg['importe'], 2, '.', ','));
		
		$dif += $reg['anio'] == $anio2 && $reg['mes'] == $mes2 ? $reg['importe'] : -$reg['importe'];
		$tpl->assign('dif', $dif != 0 ? '<span style="color:#' . ($dif <= 0 ? 'C00' : '00C') . ';">' . number_format($dif, 2, '.', ',') . '</span>' : '&nbsp;');
		
		$total[$reg['anio'] == $anio1 && $reg['mes'] == $mes1 ? 1 : 2] += $reg['importe'];
		
		$tpl->assign('listado.total1', number_format($total[1], 2, '.', ','));
		$tpl->assign('listado.total2', number_format($total[2], 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign('mes1_' . date('n', mktime(0, 0, 0, date('n') - 2, 1, date('Y'))), ' selected');
$tpl->assign('anio1', date('Y', mktime(0, 0, 0, date('n') - 2, 1, date('Y'))));
$tpl->assign('mes2_' . date('n', mktime(0, 0, 0, date('n') - 1, 1, date('Y'))), ' selected');
$tpl->assign('anio2', date('Y', mktime(0, 0, 0, date('n') - 1, 1, date('Y'))));

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($admins as $admin) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $admin['id']);
	$tpl->assign('admin', $admin['admin']);
}

$tpl->printToScreen();
?>