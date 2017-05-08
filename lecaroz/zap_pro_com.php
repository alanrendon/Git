<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Validar número de factura
if (isset($_GET['c'])) {
	$cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 AND num_cia = $_GET[c]");
	
	if (!$cia)
		die();
	else
		die($cia[0]['nombre_corto']);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_pro_com.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, ($mes > 0 ? $mes : 1), 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, ($mes > 0 ? $mes + 1 : 13), 0, $anio));
	
	$sql = 'SELECT ' . (isset($_GET['agrupar']) ? 'num_cia_primaria AS ' : '') . "num_cia, (SELECT sum(importe) FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) WHERE " . (isset($_GET['agrupar']) ? 'num_cia_primaria = cc.num_cia_primaria' : 'num_cia = cc.num_cia') . " AND cod_mov IN (1, 16, 44) AND fecha BETWEEN '$fecha1' AND '$fecha2') AS depositos, (SELECT sum(importe) FROM facturas_zap LEFT JOIN catalogo_companias USING (num_cia) WHERE " . (isset($_GET['agrupar']) ? 'num_cia_primaria = cc.num_cia_primaria' : 'num_cia = cc.num_cia') . " AND (clave = 0 OR clave IS NULL) AND fecha_inv BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 33) AS compras, (SELECT sum(importe) FROM facturas_zap LEFT JOIN catalogo_companias USING (num_cia) WHERE " . (isset($_GET['agrupar']) ? 'num_cia_primaria = cc.num_cia_primaria' : 'num_cia = cc.num_cia') . " AND (clave = 0 OR clave IS NULL) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos <> 33) AS otros, (SELECT sum(saldo_bancos) FROM his_sal_ban LEFT JOIN catalogo_companias USING (num_cia) WHERE " . (isset($_GET['agrupar']) ? 'num_cia_primaria = cc.num_cia_primaria' : 'num_cia = cc.num_cia') . " AND fecha = '$fecha1'::date - interval '1 day') AS saldo_ini, (SELECT sum(saldo_bancos) FROM his_sal_ban LEFT JOIN catalogo_companias USING (num_cia) WHERE " . (isset($_GET['agrupar']) ? 'num_cia_primaria = cc.num_cia_primaria' : 'num_cia = cc.num_cia') . " AND fecha = '$fecha2') AS saldo_fin FROM catalogo_companias cc WHERE num_cia BETWEEN 900 AND 998";
	$sql .= $_GET['num_cia'] > 0 ? ' AND ' . (isset($_GET['agrupar']) ? 'num_cia_primaria' : 'num_cia') . " = $_GET[num_cia]" : '';
	$sql .= ' GROUP BY ';
	$sql .= isset($_GET['agrupar']) ? 'num_cia_primaria' : 'num_cia';
	$sql .= ' ORDER BY ';
	$sql .= isset($_GET['agrupar']) ? 'num_cia_primaria' : 'num_cia';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./zap_pro_com.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	if ($mes > 0) $tpl->assign('mes', mes_escrito($mes));
	$tpl->assign('anio', $anio);
	
	$total = array('depositos' => 0, 'compras' => 0, 'otros' => 0, 'iva' => 0);
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		
		$nombre = $db->query("SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $reg[num_cia]");
		
		$tpl->assign('nombre', $nombre[0]['nombre']);
		$tpl->assign('depositos', $reg['depositos'] != 0 ? number_format($reg['depositos'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('compras', $reg['compras'] != 0 ? number_format($reg['compras'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('otros', $reg['otros'] != 0 ? number_format($reg['otros'], 2, '.', ',') : '&nbsp;');
		
		$iva = $reg['compras'] + $reg['otros'] - $reg['depositos'] / 1.15;
		
		$tpl->assign('iva', $iva != 0 ? number_format($iva, 2, '.', ',') : '&nbsp;');
		
		$dif_saldos = $reg['saldo_ini'] - $reg['saldo_fin'];
		
		$tpl->assign('dif_saldos', $dif_saldos != 0 ? number_format($dif_saldos, 2, '.', ',') : '&nbsp;');
		
		$total['depositos'] += $reg['depositos'];
		$total['compras'] += $reg['compras'];
		$total['otros'] += $reg['otros'];
		$total['iva'] += $iva;
	}
	foreach ($total as $k => $t)
		$tpl->assign('listado.' . $k, number_format($t, 2, '.', ','));
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
//$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$tpl->printToScreen();
?>