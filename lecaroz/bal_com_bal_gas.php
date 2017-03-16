<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_bal_gas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
	$campo = $_GET['campo'];
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes > 0 ? $mes : 1, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes > 0 ? $mes + 1 : 12, 0, $anio));
	$cods = array();
	foreach ($_GET['cod'] as $cod)
		if ($cod > 0) $cods[] = $cod;
	
	// Obtener datos
	$sql = "SELECT num_cia, nombre_corto AS nombre, sum($campo) AS $campo FROM balances_pan LEFT JOIN catalogo_companias USING (num_cia) WHERE anio = $anio";
	$sql .= $mes > 0 ? " AND mes = $mes" : '';
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= ' GROUP BY num_cia, nombre_corto';
	$sql .= " ORDER BY $campo DESC";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./bal_com_bal_gas.php?codigo_error=1');
		die;
	}
	
	// Obtener importes de gastos
	$sql = "SELECT num_cia, codgastos AS cod, sum(importe) AS importe FROM movimiento_gastos LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos IN (";
	foreach ($cods as $i => $cod)
		$sql .= $cod . ($i < count($cods) - 1 ? ', ' : ')');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= " GROUP BY num_cia, cod ORDER BY num_cia, cod";
	$gastos = $db->query($sql);
	
	function titulo($campo) {
		switch ($campo) {
			case 'venta_puerta': $campo = 'Venta en Puerta'; break;
			case 'ventas_netas': $campo = 'Ventas Netas'; break;
			case 'abono_reparto': $campo = 'Abono Reparto'; break;
			case 'produccion_total': $campo = 'Producci&oacute;n'; break;
		}
		
		return $campo;
	}
	
	function buscar($num_cia, $cod) {
		global $gastos;
		
		if (!$gastos)
			return 0;
		
		foreach ($gastos as $reg)
			if ($num_cia == $reg['num_cia'] && $cod == $reg['cod'])
				return $reg['importe'];
		
		return 0;
	}
	
	$tpl->newBlock('listado');
	$tpl->assign('campo', titulo($campo));
	$tpl->assign('mes', mes_escrito($mes));
	$tpl->assign('anio', $anio);
	
	// Crear encabezados de gastos
	foreach ($cods as $cod) {
		$tpl->newBlock('gasto_title');
		$desc = $db->query("SELECT descripcion AS desc FROM catalogo_gastos WHERE codgastos = $cod");
		$tpl->assign('gasto', ucwords(strtolower($desc[0]['desc'])));
	}
	
	// Construir listado
	$total = 0;
	$totales = array();
	foreach ($cods as $cod)
		$totales[$cod] = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('dato', $reg[$campo] != 0 ? number_format($reg[$campo], 2, '.', ',') : '&nbsp;');
		$total += $reg[$campo];
		
		foreach ($cods as $cod) {
			$tpl->newBlock('gasto');
			$gasto = buscar($reg['num_cia'], $cod);
			$tpl->assign('gasto', $gasto != 0 ? number_format($gasto, 2, '.', ',') . '&nbsp;&nbsp;&nbsp;<span style="color:#0099CC;">' . (@round($gasto * 100 / $reg[$campo], 2)) . '%</span>' : '&nbsp;');
			$totales[$cod] += $gasto;
		}
	}
	$tpl->assign('listado.dato', number_format($total, 2, '.', ','));
	foreach ($totales as $total) {
		$tpl->newBlock('gasto_total');
		$tpl->assign('gasto', number_format($total, 2, '.', ','));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
//$tpl->assign(date('n'), ' selected');
$tpl->assign("anio", date("Y", mktime(0, 0, 0, date('n'), 0, date('Y'))));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia <= 300 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $reg) {
	$tpl->newBlock('idadmin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['admin']);
}

$result = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos');
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', $reg['desc']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>