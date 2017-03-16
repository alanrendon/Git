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

$descripcion_error[1] = "No hay proveedores que surtan el o los productos seleccionados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_des_ped_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['codmp'])) {
	$por = array();
	$cont = 0;
	$mes = date("n");
	$anio = date("Y");
	$query = "";
	// Productos con ajuste
	$pro_ajuste = array(44, 45, 47);
	
	// Guardar porcentajes en una arreglo
	$mp = array();
	$codmp = NULL;
	for ($i = 0; $i < count($_POST['codmp']); $i++) {
		$porcentaje = $_POST['porc'][$i] != 0 ? $_POST['porc'][$i] : 0;
		$query  .= "UPDATE catalogo_productos_proveedor SET porcentaje = $porcentaje WHERE id = {$_POST['id'][$i]};\n";
		if ($_POST['porc'][$i] > 0) {
			if ($codmp != $_POST['codmp'][$i]) {
				$codmp = $_POST['codmp'][$i];
				$mp[] = $codmp;
			}
			$porc[$cont]['codmp'] = $_POST['codmp'][$i];
			$porc[$cont]['num_pro'] = $_POST['num_pro'][$i];
			$porc[$cont]['contenido'] = $_POST['contenido'][$i];
			$porc[$cont]['unidad'] = $_POST['unidad'][$i];
			$porc[$cont]['porcentaje'] = $_POST['porc'][$i];
			$cont++;
		}
	}
	
	// Obtener pedidos
	$sql = "SELECT num_cia, codmp, pedido FROM pedidos_bruto WHERE codmp IN (";
	foreach ($mp as $i => $m)
		$sql .= $m . ($i < count($mp) - 1 ? ", " : ")");
	$sql .= " ORDER BY num_cia, codmp";
	$result = $db->query($sql);
	
	function buscar($codmp) {
		global $porc;
		
		if (!$porc)
			return FALSE;
		
		$pro = array();
		foreach ($porc as $i => $reg)
			if ($reg['codmp'] == $codmp)
				$pro[] = $i;
		
		return count($pro) > 0 ? $pro : FALSE;
	}
	
	$cont = 0;
	foreach ($result as $pro) {
		$indexes = buscar($pro['codmp']);
		
		foreach ($indexes as $i) {
			$pedido_pro = ceil(($pro['pedido'] * ($porc[$i]['porcentaje'] / 100)) / $porc[$i]['contenido']);
			if (in_array($pro['codmp'], $pro_ajuste) && $pedido_pro > 0)
				$pedido_pro = $pedido_pro + (10 - $pedido_pro % 10);
			
			if ($pedido_pro > 0) {
				$data[$cont]['num_cia'] = $pro['num_cia'];
				$data[$cont]['num_proveedor'] = $porc[$i]['num_pro'];
				$data[$cont]['codmp'] = $pro['codmp'];
				$data[$cont]['mes'] = $mes;
				$data[$cont]['anio'] = $anio;
				$data[$cont]['cantidad'] = $pedido_pro;
				$data[$cont]['unidad'] = $porc[$i]['unidad'];
				$data[$cont]['contenido'] = $porc[$i]['contenido'];
				$cont++;
			}
		}
	}
	
	$query .= $db->multiple_insert("pedidos", $data);
	$query .= "DELETE FROM pedidos_bruto WHERE codmp IN (";
	foreach ($mp as $i => $m)
		$query .= $m . ($i < count($mp) - 1 ? ", " : ");\n");
	//echo "<pre>$query</pre>";
	$db->query($query);
	header("location: ./ped_des_ped_cap.php");
	die;
}

if (isset($_POST['pro'])) {
	$sql = "SELECT catalogo_productos_proveedor.id AS id_cat, codmp, catalogo_mat_primas.nombre AS nombre_mp, num_proveedor AS num_pro, catalogo_proveedores.nombre AS nombre_pro, contenido,";
	$sql .= " unidad, unidadconsumo, tipo_presentacion.descripcion AS unidad_presentacion, tipo_unidad_consumo.descripcion AS unidad_consumo, porcentaje FROM pedidos_bruto LEFT JOIN";
	$sql .= " catalogo_mat_primas USING (codmp) LEFT JOIN catalogo_productos_proveedor USING (codmp) LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN tipo_presentacion ON";
	$sql .= " (idpresentacion = unidad) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) WHERE codmp IN (";
	foreach ($_POST['pro'] as $i => $mp)
		$sql .= $mp . ($i < count($_POST['pro']) - 1 ? ", " : ")");
	$sql .= " GROUP BY id_cat, codmp, nombre_mp, num_pro, nombre_pro, contenido, unidad, unidadconsumo, unidad_presentacion, unidad_consumo, porcentaje, controlada ORDER BY controlada DESC, codmp, num_pro";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ped_des_ped_cap.php?codigo_error=1");
		die;
	}
	
	$vocales = array("A", "E", "I", "O", "U");
	
	$tpl->newBlock("productos");
	
	$codmp = NULL;
	foreach ($result as $i => $reg) {
		if ($codmp != $reg['codmp']) {
			$codmp = $reg['codmp'];
			
			$tpl->newBlock("fila");
			$tpl->assign("codmp", $codmp);
			$tpl->assign("nombre", $reg['nombre_mp']);
		}
		$tpl->newBlock("prov");
		$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
		$tpl->assign("id", $reg['id_cat']);
		$tpl->assign("codmp", $codmp);
		$tpl->assign("nombre_mp", $reg['nombre_mp']);
		$tpl->assign("num_pro", $reg['num_pro']);
		$tpl->assign("contenido", $reg['contenido']);
		$tpl->assign("unidad", $reg['unidad']);
		$tpl->assign("nombre", $reg['nombre_pro']);
		$tpl->assign("porc", $reg['porcentaje'] != 0 ? $reg['porcentaje'] : "");
		$tpl->assign("presentacion", $reg['unidad'] != $reg['unidadconsumo'] ? "$reg[contenido] $reg[unidad_consumo]" . ($reg['contenido'] > 1 ? (in_array($reg['unidad_consumo'][strlen($reg['unidad_consumo']) - 1], $vocales) ? "S" : "ES") : "") . " POR " . $reg['unidad_presentacion'] : $reg['unidad_presentacion'] . (in_array($reg['unidad_presentacion'][strlen($reg['unidad_presentacion']) - 1], $vocales) ? "S" : "ES"));
		$tpl->assign("br", isset($result[$i + 1]) && $result[$i]['codmp'] == $codmp ? "<br>" : "");
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$sql = "SELECT codmp, nombre FROM pedidos_bruto LEFT JOIN catalogo_mat_primas USING (codmp) GROUP BY controlada, codmp, nombre ORDER BY controlada DESC, codmp";
$result = $db->query($sql);

if ($result) {
	$tpl->newBlock("result");
	
	foreach ($result as $reg) {
		$tpl->newBlock("producto");
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre", $reg['nombre']);
	}
}
else {
	$tpl->newBlock("no_result");
	$tpl->assign("datos.disabled", " disabled");
}

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