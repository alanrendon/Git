<?php
// COMPARATIVO DE GAS MENSUAL
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Conectarse a la base de datos
$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_ent_men.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$dias = intval(date('d', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio'])));
	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	
	$sql = "SELECT " . (isset($_GET['primaria']) ? 'num_cia_primaria AS ' : '') . "num_cia FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE codmp = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
	if (count($cias) > 0) {
		$sql .= ' AND num_cia IN (';
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ', ' : ')');
	}
	$sql .= $_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '';
	$sql .= " AND descripcion NOT LIKE '%DIFERENCIA%' GROUP BY " . (isset($_GET['primaria']) ? 'num_cia_primaria' : 'num_cia') . " ORDER BY " . (isset($_GET['primaria']) ? 'num_cia_primaria' : 'num_cia');
	$cias = $db->query($sql);
	
	if (!$cias) {
		header('location: ./bal_gas_ent_men.php?codigo_error=1');
		die;
	}
	
	function search($dia) {
		global $movs;
		
		if (!$movs) return 0;
		
		foreach ($movs as $mov)
			if ($mov['dia'] == $dia)
				return $mov['cantidad'];
		
		return 0;
	}
	
	function dia($dia) {
		switch ($dia) {
			case 0: $d = 'Do'; break;
			case 1: $d = 'Lu'; break;
			case 2: $d = 'Ma'; break;
			case 3: $d = 'Mi'; break;
			case 4: $d = 'Ju'; break;
			case 5: $d = 'Vi'; break;
			case 6: $d = 'Sa'; break;
		}
		return $d;
	}
	
	$tpl->newBlock('listado');
	$tpl->assign('mes', mes_escrito($_GET['mes']));
	$tpl->assign('anio', $_GET['anio']);
	for ($dia = 1; $dia <= $dias; $dia++) {
		$tpl->newBlock('num_dia');
		$tpl->assign('dia', $dia . "<br>" . dia(date('w', mktime(0, 0, 0, $_GET['mes'], $dia, $_GET['anio']))));
	}
	$tpl->newBlock('num_dia');
	$tpl->assign('dia', 'Total');
	
	$total_dia = array();
	for ($i = 1; $i <= $dias; $i++)
		$total_dia[$i] = 0;
	
	foreach ($cias as $cia) {
		$sql = "SELECT extract(day from fecha) AS dia, sum(cantidad) AS cantidad FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE codmp = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE'";
		$sql .= " AND " . (isset($_GET['primaria']) ? 'num_cia_primaria' : 'num_cia') . " = $cia[num_cia] AND descripcion NOT LIKE '%DIFERENCIA%' GROUP BY dia ORDER BY dia";
		$movs = $db->query($sql);
		
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $cia['num_cia']);
		$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $cia[num_cia]");
		$tpl->assign('nombre', $nombre[0]['nombre_corto']);
		
		$total = 0;
		
		for ($dia = 1; $dia <= $dias; $dia++) {
			$tpl->newBlock('dia');
			$cant = search($dia);
			$tpl->assign('dia', $cant > 0 ? number_format($cant, 0, '.', ',') : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			$total += $cant;
			$total_dia[$dia] += $cant;
		}
		$tpl->newBlock('dia');
		$tpl->assign('dia', '<strong style="color:#0000CC;">' . number_format($total, 0, '.', ',') . '</strong>');
	}
	
	foreach ($total_dia as $total) {
		$tpl->newBlock('total');
		$tpl->assign('total', $total > 0 ? number_format($total, 0, '.', ',') : '&nbsp;');
	}
	$tpl->newBlock('total');
	$tpl->assign('total', number_format(array_sum($total_dia), 0, '.', ','));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$admins = $db->query("SELECT idadministrador AS idadmin, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre");
foreach ($admins as $admin) {
	$tpl->newBlock('admin');
	$tpl->assign('idadmin', $admin['idadmin']);
	$tpl->assign('nombre', $admin['nombre']);
}

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