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
$tpl->assignInclude("body","./plantillas/zap/zap_con_val.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, cp.nombre AS nombre_pro, fecha, num_fact, total, copia_fac, por_aut";
	$sql .= " FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
	$sql .= " (copia_fac = 'FALSE' OR por_aut = 'FALSE')";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND fz.num_proveedor = $_GET[num_pro]" : '';
	$sql .= " ORDER BY" . ($_GET['orden'] == 1 ? ' num_cia, num_pro' : ' num_pro, num_cia');
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./zap_con_val.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	
	$num = NULL;
	foreach ($result as $reg) {
		if ($num != $reg[$_GET['orden'] == 1 ? 'num_cia' : 'num_pro']) {
			$num = $reg[$_GET['orden'] == 1 ? 'num_cia' : 'num_pro'];
			
			$tpl->newBlock('main');
			$tpl->assign('num', $num);
			$tpl->assign('nombre', $reg[$_GET['orden'] == 1 ? 'nombre_cia' : 'nombre_pro']);
			$tpl->assign('title', $_GET['orden'] == 1 ? 'Proveedor' : 'Compaρνa');
			$total = 0;
		}
		$tpl->newBlock('row');
		$tpl->assign('num', $reg[$_GET['orden'] == 1 ? 'num_pro' : 'num_cia']);
		$tpl->assign('nombre', $reg[$_GET['orden'] == 1 ? 'nombre_pro' : 'nombre_cia']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('importe', number_format($reg['total'], 2, '.', ','));
		$tpl->assign('val', $reg['por_aut'] == 't' ? 'X' : '&nbsp;');
		$tpl->assign('cop', $reg['copia_fac'] == 't' ? 'X' : '&nbsp;');
		$total += $reg['total'];
		$tpl->assign('main.total', number_format($total, 2, '.', ','));
	}
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia AS num, nombre FROM catalogo_companias WHERE num_cia >= 900 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT num_proveedor AS num, nombre FROM catalogo_proveedores ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una pαgina que genero error
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