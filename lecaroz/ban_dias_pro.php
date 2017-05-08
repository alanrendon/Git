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

if (isset($_GET['p'])) {
	$result = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]");
	
	if ($result)
		die($result[0]['nombre']);
	else
		die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dias_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro'])) {
	$meses = $_GET['meses'] > 0 ? $_GET['meses'] : 2;
	$intervalo = $_GET['meses'] == 1 ? '1 month' : "$_GET[meses] months";
	
	
	$sql = "SELECT num_proveedor AS num_pro, nombre, round(avg(fecha_con - fp.fecha)) AS dias, max(fecha_con - fp.fecha) AS maximo FROM facturas_pagadas fp LEFT JOIN estado_cuenta ec ON (ec.num_cia = fp.num_cia AND ec.cuenta = fp.cuenta AND ec.folio = fp.folio_cheque) LEFT JOIN catalogo_proveedores cp USING (num_proveedor) WHERE fp.fecha BETWEEN now()::date - interval '$intervalo' AND now()::date AND fecha_con > fp.fecha AND idtipoproveedor = 0";
	//$sql = "SELECT num_proveedor AS num_pro, nombre, round(avg(fecha_cheque - fecha_mov)) AS dias, max(fecha_cheque - fecha_mov) AS maximo FROM facturas_pagadas LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE fecha_mov BETWEEN now()::date - interval '$intervalo' AND now()::date AND fecha_cheque > fecha_mov AND idtipoproveedor = 0";
	$sql .= $_GET['num_pro'] > 0 ? " AND num_proveedor = $_GET[num_pro]" : '';
	$sql .= " GROUP BY num_pro, nombre ORDER BY dias ASC";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ban_dias_pro.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('meses', $meses . ($meses > 1 ? ' meses' : 'mes'));
	
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('dias', $reg['dias']);
		$tpl->assign('maximo', $reg['maximo']);
		
		if ($_GET['num_pro'] > 0) {
			$sql = "SELECT fecha_con - fp.fecha AS dias, count(fp.id) AS facs FROM facturas_pagadas fp LEFT JOIN estado_cuenta ec ON (ec.num_cia = fp.num_cia AND ec.cuenta = fp.cuenta AND ec.folio = fp.folio_cheque) WHERE fp.fecha BETWEEN now()::date - interval '$intervalo' AND now()::date AND fecha_con > fp.fecha AND num_proveedor = $reg[num_pro] GROUP BY dias ORDER BY facs DESC, dias DESC";
			$facs_x_dia = $db->query($sql);
			
			$total_facs = 0;
			foreach ($facs_x_dia as $f)
				$total_facs += $f['facs'];
			
			$tpl->newBlock('porcentajes');
			$tpl->assign('facs', number_format($total_facs, 0, '.', ','));
			
			foreach ($facs_x_dia as $f) {
				$tpl->newBlock('por');
				$tpl->assign('dias', $f['dias']);
				$tpl->assign('facs', $f['facs']);
				$por_fac = round($f['facs'] * 100 / $total_facs, 2);
				$tpl->assign('por', number_format($por_fac, 2, '.', ','));
			}
		}
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

$tpl->printToScreen();
?>