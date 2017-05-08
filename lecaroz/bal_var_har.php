<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, 'autocommit=yes');

$descripcion_error[1] = 'No hay facturas por pagar';

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_var_har.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$anio = $_GET['anio'];
	$mes = $_GET['mes'];
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));
	
	$precio_pieza = get_val($_GET['precio_pieza']);
	$precio_harina = get_val($_GET['precio_harina']);
	
	$turnos = '';
	foreach ($_GET['turno'] as $i => $t)
		$turnos .= ($i > 0 ? ', ' : '') . $t;
	
	$sql = 'SELECT num_cia, nombre_corto AS nombre, ' . ($_GET['idadmin'] != 0 ? 'idadministrador' : 0) . " AS idadmin, nombre_administrador AS admin, sum(piezas) AS piezas, (SELECT sum(cantidad) / 44 FROM mov_inv_real WHERE num_cia = p.num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp = 1 AND tipo_mov = 'TRUE' AND cod_turno IN ($turnos)) AS bultos, (SELECT sum(ctes) FROM captura_efectivos WHERE num_cia = p.num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2') AS clientes FROM produccion p LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_administradores USING (idadministrador) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_turnos IN ($turnos)";
	$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '';
	$sql .= ' GROUP BY idadmin, admin, num_cia, nombre_corto ORDER BY idadmin, num_cia';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./bal_var_har.php?codigo_error=1'));
	
	$idadmin = NULL;
	foreach ($result as $reg) {
		if ($idadmin != $reg['idadmin']) {
			if ($idadmin != NULL)
				$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
			
			$idadmin = $reg['idadmin'];
			
			$tpl->newBlock('listado');
			$tpl->assign('mes', mes_escrito($mes));
			$tpl->assign('anio', $anio);
			$tpl->assign('precio_pieza', number_format($precio_pieza, 2, '.', ','));
			$tpl->assign('precio_harina', number_format($precio_harina, 2, '.', ','));
			if ($idadmin > 0)
				$tpl->assign('admin', '<br />' . ucwords(strtolower($reg['admin'])));
			
			$total_piezas = 0;
			$total_bultos = 0;
			$total_pro = 0;
			$total_harina = 0;
			$total_dif = 0;
			$total_clientes = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('piezas', number_format($reg['piezas']));
		$tpl->assign('bultos', number_format($reg['bultos']));
		$costo_pro = $reg['piezas'] * $precio_pieza;
		$costo_harina = $reg['bultos'] * $precio_harina;
		$dif = $costo_pro - ($costo_pro < 0 ? -1 : 1) * $costo_harina;
		$tpl->assign('costo_pro', number_format($costo_pro, 2, '.', ','));
		$tpl->assign('costo_harina', number_format($costo_harina, 2, '.', ','));
		$tpl->assign('dif', '<span style="color:#' . ($dif < 0 ? 'C00' : '00C') . ';">' . number_format($dif, 2, '.', ',') . '</span>');
		$tpl->assign('clientes', number_format($reg['clientes']));
		
		$total_piezas += $reg['piezas'];
		$total_bultos += $reg['bultos'];
		$total_pro += $costo_pro;
		$total_harina += $costo_harina;
		$total_dif += $dif;
		$total_clientes += $reg['clientes'];
		
		$tpl->assign('listado.piezas', number_format($total_piezas));
		$tpl->assign('listado.bultos', number_format($total_bultos));
		$tpl->assign('listado.costo_pro', number_format($total_pro, 2, '.', ','));
		$tpl->assign('listado.costo_harina', number_format($total_harina, 2, '.', ','));
		$tpl->assign('listado.dif', number_format($total_dif, 2, '.', ','));
		$tpl->assign('listado.clientes', number_format($total_clientes));
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('idadmin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

$tpl->printToScreen();
?>