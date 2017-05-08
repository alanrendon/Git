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
$tpl->assignInclude("body","./plantillas/zap/zap_rem_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	if ($_GET['num_fact'] != '' && $_GET['num_pro'] > 0) {
		$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, fecha, total, copia_fac AS ori,";
		$sql .= " por_aut AS aut FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING";
		$sql .= " (num_cia) WHERE fz.num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
		$result = $db->query($sql);
		
		if (!$result)
			die(header('location: ./zap_rem_con.php?codigo_error=1'));
		
		$tpl->newBlock('detalle_remision');
		$tpl->assign('num_cia', $result[0]['num_cia']);
		$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
		$tpl->assign('num_pro', $result[0]['num_pro']);
		$tpl->assign('nombre_pro', $result[0]['nombre_pro']);
		$tpl->assign('num_fact', $result[0]['num_fact']);
		$tpl->assign('fecha', $result[0]['fecha']);
		$tpl->assign('total', number_format($result[0]['total'], 2, '.', ','));
		$tpl->assign('ori', $result[0]['ori'] == 't' ? 'SI' : '&nbsp;');
		$tpl->assign('aut', $result[0]['aut'] == 't' ? 'SI' : '&nbsp;');
		
		// Seleccionar id del nombre del proveedor
		$sql = "SELECT id FROM catalogo_proveedores LEFT JOIN catalogo_nombres ON (num = clave_seguridad) WHERE num_proveedor = $_GET[num_pro]";
		$id = $db->query($sql);
		
		// Buscar depositos acreditados para el proveedor
		$sql = "SELECT num_cia, nombre_corto AS nombre, fecha, importe, num_fact1, pag1, num_fact2, pag2, num_fact3, pag3, num_fact4, pag4 FROM otros_depositos LEFT JOIN catalogo_companias USING (num_cia) WHERE idnombre = {$id[0]['id']} AND fecha >= '{$result[0]['fecha']}' AND (num_fact1 = '$_GET[num_fact]' OR num_fact2 = '$_GET[num_fact]' OR num_fact3 = '$_GET[num_fact]' OR num_fact4 = '$_GET[num_fact]')";
		//$sql .= $_GET['num_cia'] > 0 ? " AND (num_cia = $_GET[num_cia] OR acre = $_GET[num_cia])" : '';
		$sql .= " ORDER BY fecha, id";
		$dep = $db->query($sql);
		
		$dif = $result[0]['total'];
		if ($dep)
			foreach ($dep as $d) {
				$tpl->newBlock('pago');
				$tpl->assign('fecha', $d['fecha']);
				$tpl->assign('num_cia', $d['num_cia']);
				$tpl->assign('nombre', $d['nombre']);
				
				if ($d['num_fact1'] == $_GET['num_fact']) {
					$tpl->assign('importe', $d['pag1'] > 0 ? number_format($d['pag1'], 2, '.', ',') : number_format($dif, 2, '.', ','));
					$dif -= $d['pag1'] > 0 ? $d['pag1'] : $dif;
				}
				else if ($d['num_fact2'] == $_GET['num_fact']) {
					$tpl->assign('importe', $d['pag2'] > 0 ? number_format($d['pag2'], 2, '.', ',') : number_format($dif, 2, '.', ','));
					$dif -= $d['pag2'] > 0 ? $d['pag2'] : $dif;
				}
				else if ($d['num_fact3'] == $_GET['num_fact']) {
					$tpl->assign('importe', $d['pag3'] > 0 ? number_format($d['pag3'], 2, '.', ',') : number_format($dif, 2, '.', ','));
					$dif -= $d['pag3'] > 0 ? $d['pag3'] : $dif;
				}
				else if ($d['num_fact4'] == $_GET['num_fact']) {
					$tpl->assign('importe', $d['pag4'] > 0 ? number_format($d['pag4'], 2, '.', ',') : number_format($dif, 2, '.', ','));
					$dif -= $d['pag4'] > 0 ? $d['pag4'] : $dif;
				}
				$tpl->assign('detalle_remision.resto', number_format($dif, 2, '.', ','));
			}
	}
	else if ($_GET['num_pro'] > 0 && $_GET['num_fact'] == '') {
		$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, fecha, total, cn.id AS idnombre";
		$sql .= " FROM facturas_zap AS fz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia)";
		$sql .= " LEFT JOIN catalogo_nombres AS cn ON (cn.num = clave_seguridad) WHERE fz.num_proveedor = $_GET[num_pro] AND folio IS NULL AND clave > 0";
		$sql .= $_GET['num_cia'] > 0 ? " AND fz.num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND fz.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fz.fecha = '$_GET[fecha1]'") : '';
		$sql .= " ORDER BY num_cia, num_fact";
		$result = $db->query($sql);
		
		if (!$result)
			die(header('location: ./zap_rem_con.php?codigo_error=1'));
		
		$tpl->newBlock('pendientes_proveedor');
		
		$num_cia = NULL;
		$gimporte = 0;
		$ganticipo = 0;
		$gresto = 0;
		foreach ($result as $reg) {
			if ($num_cia != $reg['num_cia']) {
				$num_cia = $reg['num_cia'];
				
				$tpl->newBlock('bloque_cia');
				$tpl->assign('num_cia', $num_cia);
				$tpl->assign('nombre', $reg['nombre_cia']);
				
				$importe = 0;
				$anticipo = 0;
				$resto = 0;
			}
			$sql = "SELECT sum((CASE WHEN num_fact1 = $reg[num_fact] THEN pag1 ELSE 0 END) + (CASE WHEN num_fact2 = $reg[num_fact] THEN pag2 ELSE 0 END) +";
			$sql .= " (CASE WHEN num_fact3 = $reg[num_fact] THEN pag3 ELSE 0 END) + (CASE WHEN num_fact4 = $reg[num_fact] THEN pag4 ELSE 0 END)) AS pagado";
			$sql .= " FROM otros_depositos WHERE idnombre = $reg[idnombre] AND (num_fact1 = '$reg[num_fact]' OR num_fact2 = '$reg[num_fact]' OR num_fact3 =";
			$sql .= " '$reg[num_fact]' OR num_fact4 = '$reg[num_fact]')";
			$pag = $db->query($sql);
			
			$tpl->newBlock('fact');
			$tpl->assign('num_fact', $reg['num_fact']);
			$tpl->assign('fecha', $reg['fecha']);
			$tpl->assign('importe', number_format($reg['total'], 2, '.', ','));
			$tpl->assign('anticipo', number_format($pag[0]['pagado'], 2, '.', ','));
			$tpl->assign('resto', number_format($reg['total'] - $pag[0]['pagado'], 2, '.', ','));
			
			$importe += $reg['total'];
			$anticipo += $pag[0]['pagado'];
			$resto += $reg['total'] - $pag[0]['pagado'];
			
			$tpl->assign('bloque_cia.importe', number_format($importe, 2, '.', ','));
			$tpl->assign('bloque_cia.anticipo', number_format($anticipo, 2, '.', ','));
			$tpl->assign('bloque_cia.resto', number_format($resto, 2, '.', ','));
			
			$gimporte += $reg['total'];
			$ganticipo += $pag[0]['pagado'];
			$gresto += $reg['total'] - $pag[0]['pagado'];
			
			$tpl->assign('pendientes_proveedor.importe', number_format($gimporte, 2, '.', ','));
			$tpl->assign('pendientes_proveedor.anticipo', number_format($ganticipo, 2, '.', ','));
			$tpl->assign('pendientes_proveedor.resto', number_format($gresto, 2, '.', ','));
		}
	}
	else {
		$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, sum(total) AS importe FROM facturas_zap AS fz LEFT JOIN catalogo_companias AS cc USING (num_cia)";
		$sql .= " WHERE folio IS NULL AND clave > 0";
		$sql .= $_GET['num_cia'] > 0 ? " AND fz.num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['fecha1'] != '' ? ($_GET['fecha2'] != '' ? " AND fz.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " AND fz.fecha = '$_GET[fecha1]'") : '';
		$sql .= " GROUP BY num_cia, nombre_cia ORDER BY num_cia";
		$result = $db->query($sql);
		
		if (!$result)
			die(header('location: ./zap_rem_con.php?codigo_error=1'));
		
		$tpl->newBlock('pendientes_cias');
		
		$importe = 0;
		$anticipo = 0;
		$resto = 0;
		foreach ($result as $reg) {
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_cia']);
			$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
			$importe += $reg['importe'];
			
			$sql = "SELECT sum((CASE WHEN pag1 > 0 THEN pag1 ELSE 0 END) + (CASE WHEN pag2 > 0 THEN pag2 ELSE 0 END) + (CASE WHEN pag3 > 0 THEN pag3 ELSE 0 END) + (CASE WHEN pag4 > 0 THEN pag4 ELSE 0 END)) AS anticipo FROM otros_depositos WHERE (idnombre, num_fact1) IN (SELECT cn.id, num_fact FROM facturas_zap LEFT JOIN catalogo_nombres AS cn ON (cn.num = clave) WHERE num_cia = $reg[num_cia] AND clave > 0 AND tspago IS NULL) OR (idnombre, num_fact2) IN (SELECT cn.id, num_fact FROM facturas_zap LEFT JOIN catalogo_nombres AS cn ON (cn.num = clave) WHERE num_cia = $reg[num_cia] AND clave > 0 AND tspago IS NULL) OR (idnombre, num_fact3) IN (SELECT cn.id, num_fact FROM facturas_zap LEFT JOIN catalogo_nombres AS cn ON (cn.num = clave) WHERE num_cia = $reg[num_cia] AND clave > 0 AND tspago IS NULL) OR (idnombre, num_fact4) IN (SELECT cn.id, num_fact FROM facturas_zap LEFT JOIN catalogo_nombres AS cn ON (cn.num = clave) WHERE num_cia = $reg[num_cia] AND clave > 0 AND tspago IS NULL)";
			$dep = $db->query($sql);
			$anticipo += $dep[0]['anticipo'];
			$tpl->assign('anticipo', $dep[0]['anticipo'] > 0 ? number_format($dep[0]['anticipo'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('resto', $reg['importe'] - $dep[0]['anticipo'] > 0 ? number_format($reg['importe'] - $dep[0]['anticipo'], 2, '.', ',') : '&nbsp;');
			$resto += $reg['importe'] - $dep[0]['anticipo'];
		}
		$tpl->assign('pendientes_cias.importe', number_format($importe, 2, '.', ','));
		$tpl->assign('pendientes_cias.anticipo', number_format($anticipo, 2, '.', ','));
		$tpl->assign('pendientes_cias.resto', number_format($resto, 2, '.', ','));
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia AS num, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT num_proveedor AS num, nombre FROM catalogo_proveedores WHERE clave_seguridad > 0 ORDER BY num');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num', $reg['num']);
	$tpl->assign('nombre', $reg['nombre']);
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