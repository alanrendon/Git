<?php
// SISTEMA DE PEDIDOS AUTOMÁTICO
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_ped_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto AS nombre_cia, codmp, catalogo_mat_primas.nombre AS nombre_mp, pedidos.num_proveedor AS num_pro, catalogo_proveedores.nombre AS nombre_pro,";
	$sql .= " cantidad, tipo_presentacion.descripcion AS unidad_pedido, contenido, tipo_unidad_consumo.descripcion AS unidad_consumo FROM pedidos LEFT JOIN catalogo_companias USING";
	$sql .= " (num_cia) LEFT JOIN catalogo_proveedores ON (catalogo_proveedores.num_proveedor = pedidos.num_proveedor) LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN";
	$sql .= " tipo_presentacion ON (idpresentacion = unidad) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo)";
	$sql .= $_GET['num_cia'] > 0 || $_GET['admin'] > 0 || $_GET['num_pro'] > 0 || $_GET['codmp'] ? " WHERE" : "";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" . ($_GET['admin'] > 0 || $_GET['num_pro'] > 0 || $_GET['codmp'] > 0 ? " AND" : "") : "";
	$sql .= $_GET['admin'] > 0 ? " idadministrador = $_GET[admin]" . ($_GET['num_pro'] > 0 || $_GET['codmp'] > 0 ? " AND" : "") : "";
	$sql .= $_GET['num_pro'] > 0 ? " num_proveedor = $_GET[num_pro]" . ($_GET['codmp'] > 0 ? " AND" : "") : "";
	$sql .= $_GET['codmp'] > 0 ? " codmp = $_GET[codmp]" : "";
	$sql .= " ORDER BY num_cia, codmp, nombre_mp";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ped_ped_mod.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("pedidos");
	
	$num_cia = NULL;
	foreach ($result as $i => $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $reg['nombre_cia']);
			
			$tpl->assign("ini", $i);
		}
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("cia.fin", $i);
		$tpl->assign("id", $reg['id']);
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre_mp", $reg['nombre_mp']);
		$tpl->assign("num_pro", $reg['num_pro']);
		$tpl->assign("nombre_pro", $reg['nombre_pro']);
		$tpl->assign("pedido", number_format($reg['cantidad'], 2, ".", ","));
		$tpl->assign("unidad_pedido", $reg['unidad_pedido']);
		$tpl->assign("contenido", number_format($reg['contenido'], 2, ".", ","));
		$tpl->assign("unidad_consumo", $reg['unidad_consumo']);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $r) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $r['id']);
	$tpl->assign('admin', $r['admin']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;
?>