<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_bus_fac.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro'])) {
	$_SESSION['fbf']['num_pro'] = $_GET['num_pro'];
	$_SESSION['fbf']['nombre_pro'] = $_GET['nombre_pro'];
	
	$facts = array();
	foreach ($_GET['num_fact'] as $value)
		if (trim(strtoupper($value)) != '')
			$facts[] = "'" . trim(strtoupper($value)) . "'";
	
	$condiciones = array();
	
	if ($_GET['num_cia'] > 0) {
		$condiciones[] = 'f.num_cia = ' . $_GET['num_cia'];
	}
	
	if ($_GET['num_pro'] > 0) {
		$condiciones[] = 'f.num_proveedor = ' . $_GET['num_pro'];
	}
	
	if (count($facts) > 0) {
		$condiciones[] = 'f.num_fact IN (' . implode(', ', $facts) . ')';
	}
	
	if ($_GET['fecha1'] != '') {
		if ($_GET['fecha2'] != '') {
			$condiciones[] = 'f.fecha BETWEEN \'' . $_GET['fecha1'] . '\' AND \'' . $_GET['fecha2'] . '\'';
		}
		else {
			$condiciones[] = 'f.fecha = \'' . $_GET['fecha1'] . '\'';
		}
	}
	
	if (isset($_GET['user'])) {
		$condiciones[] = 'f.iduser = ' . $_SESSION['iduser'];
	}
	
	if ($_GET['status'] > 0) {
		$condiciones[] = 'fecha_cheque IS ' . ($_GET['status'] == 1 ? 'NULL' : 'NOT NULL');
		
		if ($_GET['status'] == 2 && $_GET['pag'] > 0) {
			$condiciones[] = 'fecha_con IS ' . ($_GET['pag'] == 1 ? 'NULL' : 'NOT NULL');
		}
	}
	
	if ($_SESSION['iduser'] == 15) {
		$condiciones[] = 'f.num_proveedor IN (28, 74, 78, 113, 133, 156, 207, 208, 210, 211, 220, 222, 226, 239, 258, 291, 293, 348, 388, 413, 427, 458, 461, 578, 588, 614, 641, 749, 458, 910, 926, 927, 954, 1155, 1185, 1199, 1357, 1374, 1409, 215, 1605)';
	}
	
	$sql = "
		SELECT
			f.id,
			f.num_proveedor
				AS num_pro,
			cp.nombre
				AS nombre_pro,
			f.num_fact,
			f.fecha,
			f.num_cia,
			cc.nombre_corto
				AS nombre_cia,
			f.concepto,
			f.total
				AS importe,
			fp.folio_cheque
				AS folio,
			fecha_cheque,
			fecha_con,
			f.codgastos,
			ch.cuenta,
			ch.cod_mov,
			ch.fecha_cancelacion
		FROM
			facturas f
			LEFT JOIN facturas_pagadas fp
				ON (fp.num_proveedor = f.num_proveedor AND fp.num_fact = f.num_fact AND fp.fecha = f.fecha)
			LEFT JOIN cheques ch
				ON (ch.num_cia = fp.num_cia AND ch.folio = fp.folio_cheque AND ch.fecha = fp.fecha_cheque)
			LEFT JOIN estado_cuenta ec
				ON (ec.num_cia = fp.num_cia AND ec.folio = fp.folio_cheque AND ec.fecha = fp.fecha_cheque)
			LEFT JOIN catalogo_proveedores cp
				ON (cp.num_proveedor = f.num_proveedor)
			LEFT JOIN catalogo_companias cc
				ON (cc.num_cia = f.num_cia)
		WHERE
	";
	/*$sql .= $_GET['num_cia'] > 0 ? " f.num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " f.num_proveedor = $_GET[num_pro]" : '';
	if (count($facts) > 0) {
		$sql .= ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 ? ' AND' : '') . ' f.num_fact IN (';
		foreach ($facts as $i => $fact)
			$sql .= $fact . ($i < count($facts) - 1 ? ', ' : ')');
	}
	$sql .= $_GET['fecha1'] != '' ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || count($facts) > 0 ? ' AND' : '') . ($_GET['fecha2'] != '' ? " f.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " f.fecha = '$_GET[fecha1]'") : '';
	$sql .= isset($_GET['user']) ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || count($facts) > 0 || $_GET['fecha1'] != '' ? ' AND' : '') . " f.iduser = $_SESSION[iduser]" : '';
	if ($_GET['status'] > 0) {
		$sql .= $_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || count($facts) > 0 || $_GET['fecha1'] != '' || isset($_GET['user']) ? ' AND' : '';
		$sql .= ' fecha_cheque IS ' . ($_GET['status'] == 1 ? 'NULL' : 'NOT NULL');
		if ($_GET['status'] == 2 && $_GET['pag'] > 0)
			$sql .= ' AND fecha_con IS ' . ($_GET['pag'] == 1 ? 'NULL' : 'NOT NULL');
	}*/
	$sql .= implode(' AND ', $condiciones);
	$sql .= "
		ORDER BY num_pro, f.num_cia, f.fecha, f.num_fact
	";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./fac_bus_fac.php?codigo_error=1');
		die;
	}
	
	$tpl->newBlock('result');
	//$tpl->assign('num_pro', $result[0]['num_pro']);
	//$tpl->assign('nombre', $result[0]['nombre_pro']);
	
	$num_pro = NULL;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			$num_pro = $reg['num_pro'];
			
			$tpl->newBlock('prov');
			$tpl->assign('num_pro', $reg['num_pro']);
			$tpl->assign('nombre', $reg['nombre_pro']);
			$total = 0;
		}
		$tpl->newBlock('fac');
		
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('concepto', trim($reg['concepto']) != '' ? trim($reg['concepto']) : '&nbsp;');
		$tpl->assign('codgastos', $reg['codgastos']);
		$desc = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = $reg[codgastos]");
		$tpl->assign('desc', $desc[0]['descripcion']);
		$tpl->assign('importe', $reg['importe'] != 0 ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('fecha_cheque', $reg['fecha_cheque'] != '' ? $reg['fecha_cheque'] : '&nbsp;');
		$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? '<span style="color:#990000">BANORTE</span>' : '<span style="color:#000099">SANTANDER</span>') : '&nbsp;');
		$tpl->assign('folio', $reg['folio'] > 0 ? '<span style="color:' . ($reg['fecha_cancelacion'] == '' ? ($reg['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $reg['folio'] . '</span>' : '&nbsp;');
		$tpl->assign('fecha_con', $reg['fecha_con'] != '' ? $reg['fecha_con'] : '&nbsp;');
		$total += $reg['importe'];
		$tpl->assign('prov.total', number_format($total, 2, '.', ','));
		
		if (strpos(trim($reg['concepto']), 'MATERIA PRIMA'))
			$tpl->assign('detalle', " style=\"color:#0000CC; text-decoration:underline;\" onMouseOver=\"this.style.cursor='pointer'\" onMouseOut=\"this.style.cursor='default'\" onClick=\"detalle($reg[num_cia],$reg[num_pro],'$reg[num_fact]',1)\"");
		else if (strpos(trim($reg['concepto']), 'GAS'))
			$tpl->assign('detalle', " style=\"color:#0000CC; text-decoration:underline;\" onMouseOver=\"this.style.cursor='pointer'\" onMouseOut=\"this.style.cursor='default'\" onClick=\"detalle($reg[num_cia],$reg[num_pro],'$reg[num_fact]',2)\"");
		
		if ($reg['fecha_cheque'] == '')
			$tpl->assign('edit', " style=\"color:#0000CC; text-decoration:underline;\" onMouseOver=\"this.style.cursor='pointer'\" onMouseOut=\"this.style.cursor='default'\" onClick=\"edit($reg[id])\"");
		
		$facts[array_search("'" . $reg['num_fact'] . "'", $facts)] = NULL;
	}
	foreach ($facts as $fact)
		if ($fact != NULL) {
			$tpl->newBlock('no_fac');
			$tpl->assign('num_fact', str_replace("'", '', $fact));
		}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$tpl->assign('checked', in_array($_SESSION['iduser'], array(8, 14,39 )) ? ' checked' : '');

$fecha1 = date('d') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
$fecha2 = date('d') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');
$tpl->assign('fecha1', $fecha1);
$tpl->assign('fecha2', $fecha2);

if (!in_array($_SESSION['iduser'], array(1, 4, 5, 8, 14, 15, 17, 18, 19, 22, 37, 38, 39, 42, 43, 44, 49, 63, 64))) {
	$tpl->assign('disabled', ' disabled');
}

if (isset($_SESSION['fbf'])) {
	$tpl->assign('num_pro', $_SESSION['fbf']['num_pro']);
	$tpl->assign('nombre_pro', $_SESSION['fbf']['nombre_pro']);
}

$cias = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia');
foreach ($cias as $cia) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre', $cia['nombre_corto']);
}

$pros = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
foreach ($pros as $pro) {
	$tpl->newBlock('pro');
	$tpl->assign('num_pro', $pro['num_pro']);
	$tpl->assign('nombre', $pro['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>