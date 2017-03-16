<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_ped_sta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, fecha, concepto AS producto, unidad, codmp, cmp.nombre AS desc, importe AS cantidad, p.num_proveedor AS num_pro, cp.nombre AS nombre_pro, obs, tsins::date AS pedido, tsaut::date AS aut, iduser FROM pedidos_tmp AS p LEFT JOIN catalogo_mat_primas AS cmp USING (codmp) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE p.status = $_GET[status] AND importe > 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fecha = '$_GET[fecha]'") : '';
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : '';
	$sql .= " ORDER BY num_cia, p.status, tsins";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./ped_ped_sta.php?codigo_error=1'));
	
	$tpl->newBlock('result');
	switch ($_GET['status']) {
		case 0: $tpl->assign('status', 'Pendientes'); break;
		case 1: $tpl->assign('status', 'Validados'); break;
		case 2: $tpl->assign('status', 'Cancelados'); break;
	}
	$num_cia = NULL;
	foreach ($result as $i => $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
		}
		$tpl->newBlock('fila');
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('producto', $reg['producto']);
		$tpl->assign('unidad', $reg['unidad']);
		$tpl->assign('codmp', $reg['codmp'] > 0 ? $reg['codmp'] : '&nbsp;');
		$tpl->assign('desc', $reg['codmp'] > 0 ? $reg['desc'] : '&nbsp;');
		$tpl->assign('cantidad', number_format($reg['cantidad'], 2, '.', ','));
		$tpl->assign('num_pro', $reg['num_pro'] > 0 ? $reg['num_pro'] : '&nbsp;');
		$tpl->assign('nombre_pro', $reg['num_pro'] > 0 ? $reg['nombre_pro'] : '&nbsp;');
		$tpl->assign('obs', strlen(trim($reg['obs'])) > 0 ? trim($reg['obs']) : '&nbsp;');
		$tpl->assign('pedido', $reg['pedido'] != '' ? $reg['pedido'] : '&nbsp;');
		$tpl->assign('aut', $reg['aut'] != '' ? $reg['aut'] : '&nbsp;');
		if ($reg['iduser'] > 0)
			$user = $db->query("SELECT nombre FROM auth WHERE iduser = $reg[iduser]");
		$tpl->assign('user', isset($user) ? $user[0]['nombre'] : '&nbsp;');
	}
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT codmp, nombre FROM catalogo_mat_primas ORDER BY codmp');
foreach ($result as $reg) {
	$tpl->newBlock('mp');
	$tpl->assign('codmp', $reg['codmp']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $reg) {
	$tpl->newBlock('idadmin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['admin']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die();
?>