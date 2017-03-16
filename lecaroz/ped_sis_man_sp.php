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

if (isset($_POST['num_cia'])) {
	$cont = 0;
	$sql = "";
	for ($i = 0; $i < count($_POST['codmp']); $i++) {
		$pedido = floatval(str_replace(",", "", $_POST['pedido'][$i]));
		if ($_POST['codmp'][$i] > 0 && $pedido > 0) {
			if ($id = $db->query("SELECT id FROM pedidos_bruto WHERE num_cia = $_POST[num_cia] AND codmp = {$_POST['codmp'][$i]}"))
				$sql .= "UPDATE pedidos_bruto SET pedido = $pedido WHERE id = {$id[0]['id']};\n";
			else
				$sql .= "INSERT INTO pedidos_bruto (num_cia, codmp, pedido) VALUES ($_POST[num_cia], {$_POST['codmp'][$i]}, $pedido);\n";
			
			/*$reg[$cont]['num_cia'] = $_POST['num_cia'];
			$reg[$cont]['codmp'] = $_POST['codmp'][$i];
			$reg[$cont]['pedido'] = $pedido;*/
			//$cont++;
		}
		else if ($_POST['codmp'][$i] > 0 && $pedido == 0)
			$sql .= "DELETE FROM pedidos_bruto WHERE num_cia = $_POST[num_cia] AND codmp = {$_POST['codmp'][$i]};\n";
	}
	
	//$sql = "DELETE FROM pedidos_bruto WHERE num_cia = $_POST[num_cia];\n";
	/*if ($cont > 0)
		$sql .= $db->multiple_insert("pedidos_bruto", $reg);*/
	$db->query($sql);
	
	header("location: ./ped_sis_man_sp.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_sis_man_sp.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia']) && $_GET['tipo'] == 1) {
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m"), 0, date("Y")));
	$fecha_mov = date("d/m/Y", mktime(0, 0, 0, date("m") - 2, 1, date("Y")));
	
	$sql = "SELECT num_cia, codmp, inventario AS existencia, nombre, controlada, tipo_unidad_consumo.descripcion AS unidad FROM inventario_fin_mes";
	$sql .= " LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha'";
	$sql .= " UNION SELECT num_cia, codmp, 0 AS existencia, nombre, controlada, tipo_unidad_consumo.descripcion AS unidad FROM pedidos_bruto LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) WHERE (num_cia, codmp) NOT IN (SELECT num_cia, codmp FROM inventario_fin_mes WHERE num_cia = $_GET[num_cia]";
	$sql .= " AND fecha = '$fecha') AND num_cia = $_GET[num_cia] ORDER BY nombre";
	$inv = $db->query($sql);
	if (!$inv) {
		$sql = "SELECT num_cia, codmp, existencia, nombre, controlada, tipo_unidad_consumo.descripcion AS unidad FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp)";
		$sql .= " LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) WHERE num_cia = $_GET[num_cia] AND (codmp IN (SELECT codmp FROM mov_inv_real";
		$sql .= " WHERE fecha >= '$fecha_mov' AND num_cia = $_GET[num_cia] GROUP BY codmp UNION SELECT codmp FROM pedidos_bruto WHERE num_cia = $_GET[num_cia])";
		$sql .= " OR existencia != 0) ORDER BY nombre";
		$inv = $db->query($sql);
	}
	
	if (!$inv) {
		header("location: ./ped_sis_man_sp.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("captura");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign("nombre", $nombre[0]['nombre_corto']);
	
	// Obtener captura anterior
	$pedidos = $db->query("SELECT codmp, pedido FROM pedidos_bruto WHERE num_cia = $_GET[num_cia]");
	
	function buscar($codmp) {
		global $pedidos;
		
		if (!$pedidos)
			return FALSE;
		
		foreach ($pedidos as $reg)
			if ($reg['codmp'] == $codmp)
				return $reg['pedido'];
		
		return FALSE;
	}
	
	$numpro = count($inv);
	foreach ($inv as $i => $pro) {
		$tpl->newBlock("pro");
		$tpl->assign("next", $i < $numpro - 1 ? $i + 1 : 0);
		$tpl->assign("back", $i > 0 ? $i - 1 : $numpro);
		$tpl->assign("codmp", $pro['codmp']);
		$tpl->assign("nombre", $pro['nombre']);
		$tpl->assign("existencia", $pro['existencia'] != 0 ? number_format($pro['existencia'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("unidad", strtoupper($pro['unidad']));
		$tpl->assign("color", $pro['controlada'] == "TRUE" ? "0000CC" : "CC0000");
		$pedido = buscar($pro['codmp']);
		$tpl->assign("pedido", $pedido ? number_format($pedido, 2, ".", ",") : "");
	}
	
	$numfilas = 40;
	$ini = $numpro;
	$fin = $numpro + $numfilas;
	for ($i = $ini; $i < $fin; $i++) {
		$tpl->newBlock("extra");
		$tpl->assign("i", $i);
		$tpl->assign("next", $i < $fin - 1 ? $i + 1 : $ini);
	}
	
	$mps = $db->query("SELECT codmp, nombre, tipo_unidad_consumo.descripcion AS unidad FROM catalogo_mat_primas LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) WHERE tipo_cia = 'TRUE' ORDER BY codmp");
	foreach ($mps as $mp) {
		$tpl->newBlock("mp");
		$tpl->assign("codmp", $mp['codmp']);
		$tpl->assign("nombre", str_replace("\"", "'", $mp['nombre']));
		$tpl->assign("unidad", strtoupper($mp['unidad']));
	}
	
	$tpl->printToScreen();
	die;
}
else if (isset($_GET['num_cia']) && $_GET['tipo'] == 2) {
	$sql = "SELECT num_cia, catalogo_companias.nombre AS nombre_cia, codmp, catalogo_mat_primas.nombre AS nombre_mp, pedido, tipo_unidad_consumo.descripcion AS unidad FROM pedidos_bruto";
	$sql .= " LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= $_GET['num_cia'] > 0 ? " WHERE num_cia = $_GET[num_cia]" : "";
	$sql .= " ORDER BY num_cia, tipo, nombre_mp";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ped_sis_man_sp.php?codigo_error=1");
		die;
	}
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("listado");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $reg['nombre_cia']);
		}
		$tpl->newBlock("fila");
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre", $reg['nombre_mp']);
		$tpl->assign("pedido", number_format($reg['pedido'], 2, ".", ","));
		$tpl->assign("unidad", strtoupper($reg['unidad']));
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

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