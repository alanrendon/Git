<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if (!in_array($_SESSION['iduser'], array(1))) die("la estoy modificando");

$descripcion_error[1] = "";

if (isset($_GET['num_cia'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/fac/carta_gas.tpl" );
	$tpl->prepare();
	
	$sql = "SELECT num_cia, cc.nombre AS nombre_cia, cc.direccion AS dir, cp.num_proveedor AS num_pro, cp.nombre AS nombre_pro FROM factura_gas AS fg LEFT JOIN catalogo_companias AS cc";
	$sql .= " USING (num_cia) LEFT JOIN catalogo_proveedores AS cp ON (fg.num_proveedor = cp.num_proveedor) WHERE fecha >= CURRENT_DATE - interval '2 months'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " GROUP BY num_cia, nombre_cia, dir, num_pro, nombre_pro ORDER BY num_cia, num_pro";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock('cerrar');
		$tpl->printToScreen();
		die;
	}
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			//if ($num_cia != NULL)
				//$tpl->assign('salto', '<br style="page-break-after:always;">');
			
			$num_cia = $reg['num_cia'];
		}
		$tpl->newBlock('carta');
		$tpl->assign('nombre_cia', $reg['nombre_cia']);
		$tpl->assign('dir', $reg['dir']);
		$tpl->assign('fecha', date('d') . ' DE ' . mes_escrito(date('n'), TRUE) . ' DEL ' . date('Y'));
		$tpl->assign('nombre_pro', $reg['nombre_pro']);
	}
	
	// Listados
	$sql = "SELECT num_cia, cc.nombre AS nombre_cia, cc.direccion AS dir, cp.num_proveedor AS num_pro, cp.nombre AS nombre_pro FROM factura_gas AS fg LEFT JOIN catalogo_companias AS cc";
	$sql .= " USING (num_cia) LEFT JOIN catalogo_proveedores AS cp ON (fg.num_proveedor = cp.num_proveedor) WHERE fecha >= CURRENT_DATE - interval '2 months'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " GROUP BY num_cia, nombre_cia, dir, num_pro, nombre_pro ORDER BY num_pro, num_cia";
	$result = $db->query($sql);
	
	$num_pro = NULL;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			if ($num_pro != NULL)
				$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
			
			$num_pro = $reg['num_pro'];
			$tpl->newBlock('listado');
			$tpl->assign('nombre_pro', $reg['nombre_pro']);
			$tpl->assign('fecha', date('d/m/Y'));
		}
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
	}
	
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_gas_car.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>
