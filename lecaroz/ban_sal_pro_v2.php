<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_pro_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT num_cia, nombre_cia, num_pro, nombre_pro, saldo_ini, compras, pagos, saldo_fin FROM (SELECT fz.num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro,";
	$sql .= " cp.nombre AS nombre_pro FROM facturas_zap AS fz LEFT JOIN cheques AS c USING (num_cia, folio, cuenta) LEFT JOIN catalogo_proveedores AS cp ON (cp.num_proveedor =";
	$sql .= " fz.num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE (folio IS NULL AND fz.fecha < '$fecha1') OR (fz.fecha < '$fecha1' AND c.fecha BETWEEN '$fecha1'";
	$sql .= " AND '$fecha2') UNION SELECT fz.num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, cp.nombre AS nombre_pro FROM facturas_zap AS fz LEFT JOIN cheques AS c";
	$sql .= " USING (num_cia, folio, cuenta) LEFT JOIN catalogo_proveedores AS cp ON (cp.num_proveedor = fz.num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE";
	$sql .= " (folio IS NULL AND fz.fecha <= '$fecha2') OR (fz.fecha <= '$fecha2' AND c.fecha > '$fecha2') GROUP BY num_cia, nombre_cia, num_pro, nombre_pro ORDER BY num_cia, nombre_pro)";
	$sql .= " AS cias LEFT JOIN (SELECT fz.num_cia, fz.num_proveedor AS num_pro, sum(total) AS saldo_ini FROM facturas_zap AS fz LEFT JOIN cheques AS c USING (num_cia, folio, cuenta)";
	$sql .= " WHERE (folio IS NULL AND fz.fecha < '$fecha1') OR (fz.fecha < '$fecha1' AND c.fecha BETWEEN '$fecha1' AND '$fecha2') GROUP BY num_cia, num_pro) AS saldos_ini USING";
	$sql .= " (num_cia, num_pro) LEFT JOIN (SELECT fz.num_cia, fz.num_proveedor AS num_pro, sum(total) AS saldo_fin FROM facturas_zap AS fz LEFT JOIN cheques AS c USING (num_cia,";
	$sql .= " folio, cuenta) WHERE (folio IS NULL AND fz.fecha <= '$fecha2') OR (fz.fecha <= '$fecha2' AND c.fecha > '$fecha2') GROUP BY num_cia, num_pro) AS saldos_fin USING (num_cia,";
	$sql .= " num_pro) LEFT JOIN (SELECT fz.num_cia, fz.num_proveedor AS num_pro, sum(total) AS compras FROM facturas_zap AS fz LEFT JOIN cheques AS c USING (num_cia, folio, cuenta)";
	$sql .= " WHERE fz.fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, num_pro) AS compras_pro USING (num_cia, num_pro) LEFT JOIN (SELECT fz.num_cia, fz.num_proveedor AS num_pro,";
	$sql .= " sum(total) AS pagos FROM facturas_zap AS fz LEFT JOIN cheques AS c USING (num_cia, folio, cuenta) WHERE c.fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, num_pro)";
	$sql .= " AS pagos_pro USING (num_cia, num_pro)";
	$sql .= $_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 ? ' WHERE' : '';
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " num_pro = $_GET[num_pro]" : '';
	$sql .= " ORDER BY num_cia, num_pro";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./ban_sal_pro_v2.php?codigo_error=1'));
	
	$tpl->newBlock('result');
	$tpl->assign('mes', mes_escrito($_GET['mes']));
	$tpl->assign('anio', $_GET['anio']);
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_cia']);
			
			$saldo_ini = 0;
			$compras = 0;
			$pagos = 0;
			$saldo_fin = 0;
		}
		$tpl->newBlock('pro');
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre', $reg['nombre_pro']);
		$tpl->assign('saldo_ini', $reg['saldo_ini'] != 0 ? number_format($reg['saldo_ini'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('compras', $reg['compras'] != 0 ? number_format($reg['compras'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('pagos', $reg['pagos'] != 0 ? number_format($reg['pagos'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('saldo_fin', $reg['saldo_fin'] != 0 ? number_format($reg['saldo_fin'], 2, '.', ',') : '&nbsp;');
		$saldo_ini += $reg['saldo_ini'];
		$compras += $reg['compras'];
		$pagos += $reg['pagos'];
		$saldo_fin += $reg['saldo_fin'];
		$tpl->assign('cia.saldo_ini', number_format($saldo_ini, 2, '.', ','));
		$tpl->assign('cia.compras', number_format($compras, 2, '.', ','));
		$tpl->assign('cia.pagos', number_format($pagos, 2, '.', ','));
		$tpl->assign('cia.saldo_fin', number_format($saldo_fin, 2, '.', ','));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $reg['num_pro']);
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