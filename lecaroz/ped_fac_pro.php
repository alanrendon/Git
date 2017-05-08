<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_fac_pro.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT entrada_mp.num_cia, nombre_corto, entrada_mp.fecha, fecha_con, entrada_mp.num_fact, folio_cheque, contenido, cantidad FROM entrada_mp LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " LEFT JOIN facturas_pagadas ON (facturas_pagadas.num_proveedor = entrada_mp.num_proveedor AND facturas_pagadas.num_fact = entrada_mp.num_fact)";
	$sql .= " LEFT JOIN estado_cuenta ON (estado_cuenta.num_cia = entrada_mp.num_cia AND estado_cuenta.folio = facturas_pagadas.folio_cheque AND estado_cuenta.cuenta = facturas_pagadas.cuenta)";
	$sql .= " WHERE entrada_mp.num_proveedor = $_GET[num_pro] AND codmp = $_GET[codmp] AND entrada_mp.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'";
	$sql .= $_GET['num_cia'] > 0 ? " AND entrada_mp.num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ped_fac_pro.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$nombre = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_pro]");
	$tpl->assign("nombre", $nombre[0]['nombre']);
	$tpl->assign("fecha1", $_GET['fecha1']);
	$tpl->assign("fecha2", $_GET['fecha2']);
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_corto']);
		$tpl->assign("fecha", $reg['fecha']);
		$tpl->assign("con", $reg['fecha_con'] != "" ? $reg['fecha_con'] : "&nbsp;");
		$tpl->assign("factura", $reg['num_fact']);
		$tpl->assign("folio", $reg['folio_cheque'] > 0 ? $reg['folio_cheque'] : "&nbsp;");
		$tpl->assign("cantidad", $_GET['num_pro'] == 1 && $reg['contenido'] > 44 ? number_format($reg['contenido'] / 44, 2, ".", ",") : number_format($reg['cantidad'], 2, '.', ','));
		
		$total += $reg['cantidad'];
	}
	$tpl->assign("listado.total", number_format($total, 2, ".", ","));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("fecha1", date("01/m/Y"));
$tpl->assign("fecha2", date("d/m/Y"));

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
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>