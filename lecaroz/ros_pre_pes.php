<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pre_pes.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = 'SELECT num_cia, cc.nombre_corto AS nombre_cia, pg.num_proveedor AS num_pro, cp.nombre AS nombre_pro, codmp, cmp.nombre AS nombre_mp, nombre_alt, precio_venta, peso_max, peso_min FROM precios_guerra pg LEFT JOIN pesos_companias pc USING (num_cia, codmp, num_proveedor) LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = pg.num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_mat_primas cmp USING (codmp) WHERE precio_venta > 0 AND codmp IN (160, 600, 700)';
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND pg.num_proveedor = $_GET[num_pro]" : '';
	$sql .= ' ORDER BY num_cia, num_pro, codmp, precio_venta';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./ros_pre_pes.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('fecha', date('d/m/Y'));
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
		}
		$tpl->newBlock('pro');
		$tpl->assign('codmp', $reg['codmp']);
		$tpl->assign('nombre', $reg['nombre_mp']);
		$tpl->assign('alt', trim($reg['nombre_alt']) != '' && trim($reg['nombre_alt']) != trim($reg['nombre_mp']) ? '(' . trim($reg['nombre_alt']) . ')' : '');
		$tpl->assign('precio_venta', number_format($reg['precio_venta'], 2, '.', ','));
		$tpl->assign('peso_max', $reg['peso_max'] > 0 ? $reg['peso_max'] : '&nbsp;');
		$tpl->assign('peso_min', $reg['peso_min'] > 0 ? $reg['peso_min'] : '&nbsp;');
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>