<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_pro_men.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha_his = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("n") == $_GET['mes'] && date("Y") == $_GET['anio'] ? date("d/m/Y") : date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$mes1 = date("n", mktime(0, 0, 0, $_GET['mes'] - 2, 1, $_GET['anio']));
	$mes2 = date("n", mktime(0, 0, 0, $_GET['mes'] - 1, 1, $_GET['anio']));
	$anio1 = date("Y", mktime(0, 0, 0, $_GET['mes'] - 2, 1, $_GET['anio']));
	$anio2 = date("Y", mktime(0, 0, 0, $_GET['mes'] - 1, 1, $_GET['anio']));
	
	$sql = "SELECT num_cia, catalogo_companias.nombre AS nombre_cia, codmp, catalogo_mat_primas.nombre AS nombre_mp, existencia, tipo_unidad_consumo.descripcion AS unidad";
	$sql .= " FROM historico_inventario LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo";
	$sql .= " ON (idunidad = unidadconsumo) WHERE fecha = '$fecha_his' AND codmp NOT IN (1, 90, 148)";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : "";
	$sql .= " ORDER BY " . ($_GET['admin'] == -1 ? "idadministrador, num_cia," : "num_cia,") . " controlada DESC, nombre_mp";
	$mps = $db->query($sql);
	
	if (!$mps) {
		header("location: ./ped_pro_men.php?codigo_error=1");
		die;
	}
	
	function buscar_compras($codmp) {
		global $compras;
		
		if (!$compras)
			return FALSE;
		
		foreach ($compras as $reg)
			if ($codmp == $reg['codmp'])
				return $reg['compras'];
		
		return FALSE;
	}
	
	function buscar_consumo($codmp) {
		global $consumos;
		
		if (!$consumos)
			return FALSE;
		
		foreach ($consumos as $consumo)
			if ($codmp == $consumo['codmp'])
				return $consumo['consumo'];
		
		return FALSE;
	}
	
	function buscar_pedido($codmp) {
		global $pedidos;
		
		if (!$pedidos)
			return FALSE;
		
		foreach ($pedidos as $pedido)
			if ($codmp == $pedido['codmp'])
				return $pedido['pedido'];
		
		return FALSE;
	}
	
	$numfilas_x_hoja = 60;
	
	$num_cia = NULL;
	foreach ($mps as $mp) {
		if ($num_cia != $mp['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			
			$num_cia = $mp['num_cia'];
			
			$tpl->newBlock("listado");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $mp['nombre_cia']);
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			
			// Compras del Mes
			$sql = "SELECT codmp, sum(cantidad) AS compras FROM mov_inv_real WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE' AND descripcion != ";
			$sql .= " 'DIFERENCIA INVENTARIO' GROUP BY codmp ORDER BY codmp";
			$compras = $db->query($sql);
			
			// Consumos Promedio
			$sql = "SELECT codmp, max(consumo) AS consumo FROM consumos_mensuales WHERE num_cia = $num_cia AND ((mes, anio) IN (SELECT $mes1, $anio1) OR (mes, anio)";
			$sql .= " IN (SELECT $mes2, $anio2)) GROUP BY codmp ORDER BY codmp";
			$consumos = $db->query($sql);
			
			// Pedidos
			$sql = "SELECT codmp, pedido FROM pedidos_bruto WHERE num_cia = $num_cia";
			$pedidos = $db->query($sql);
			
			$numfilas = 0;
		}
		if ($numfilas == $numfilas_x_hoja) {
			$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			$tpl->newBlock("listado");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $mp['nombre_cia']);
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			
			$numfilas = 0;
		}
		$compra = buscar_compras($mp['codmp']);
		$consumo = $_GET['dias'] > 0 ? buscar_consumo($mp['codmp']) / 30 * $_GET['dias'] : buscar_consumo($mp['codmp']);
		$pedido = buscar_pedido($mp['codmp']);
		$dif = $mp['existencia'] + $compra - $consumo + $pedido;
		if (!$mp['existencia'] && !$compra && !$consumo && !$pedido)
			continue;
		if ($_GET['tipo'] == 1 && $dif < 0)
			continue;
		if ($_GET['tipo'] == 2 && $dif >= 0)
			continue;
		$tpl->newBlock("fila");
		$tpl->assign("codmp", $mp['codmp']);
		$tpl->assign("nombre", $mp['nombre_mp']);
		$tpl->assign("existencia", $mp['existencia'] != 0 ? number_format($mp['existencia'], 2, ".", ",") : "");
		$tpl->assign("compras", $compra ? number_format($compra, 2, ".", ",") : "");
		$tpl->assign("consumo", $consumo ? number_format($consumo, 2, ".", ",") : "");
		$tpl->assign("pedido", $pedido ? number_format($pedido, 2, ".", ",") : "");
		$tpl->assign("dif", $dif != 0 ? number_format($dif, 2, ".", ",") : "");
		
		$numfilas++;
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre");
foreach ($admins as $admin) {
	$tpl->newBlock("admin");
	$tpl->assign("id", $admin['id']);
	$tpl->assign("nombre", $admin['nombre']);
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>