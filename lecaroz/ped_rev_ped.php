<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_rev_ped.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['codmp'])) {
	// Obtener listado de productos por proveedor
	$sql = "SELECT num_proveedor, codmp, contenido, porcentaje, unidadconsumo FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " ORDER BY codmp, num_proveedor, porcentaje";
	$por = $db->query($sql);
	
	$mes = date("n");
	$anio = date("Y");
	$sql = "";
	$cont = 0;
	for ($i = 0; $i < count($_POST['id']); $i++)
		if ($_POST['codmp'][$i] > 0 && $_POST['num_pro'][$i] > 0 && get_val($_POST['cantidad'][$i]) > 0) {
			$cantidad = get_val($_POST['cantidad'][$i]);
			$sql .= "UPDATE pedidos_tmp SET status = 3, codmp = {$_POST['codmp'][$i]}, num_proveedor = {$_POST['num_pro'][$i]}, importe = $cantidad, tsaut = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['id'][$i]};\n";
			$cont++;
		}
		else if (isset($_POST['elim' . $i]))
			$sql .= "UPDATE pedidos_tmp SET status = 2, tsaut = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['elim' . $i]};\n";
	
	if ($cont++) {
		$sql .= "INSERT INTO pedidos (num_cia, num_proveedor, codmp, mes, anio, cantidad, unidad, contenido) SELECT num_cia, num_proveedor, codmp, $mes,";
		$sql .= " $anio, importe, (SELECT unidadconsumo FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
		$sql .= " codmp = tmp.codmp AND num_proveedor = tmp.num_proveedor LIMIT 1), (SELECT contenido FROM catalogo_productos_proveedor WHERE codmp = tmp.codmp LIMIT 1)";
		$sql .= " FROM pedidos_tmp AS tmp WHERE status = 3;\n";
		
		$sql .= "UPDATE pedidos_tmp SET status = 1, tsaut = now(), iduser = $_SESSION[iduser] WHERE status = 3;\n";
	}
	
	$sql .= "DELETE FROM pedidos_tmp WHERE importe = 0;\n";
	
	if ($sql != '') $db->query($sql);
	
	header("location: ./ped_rev_ped.php");
	die;
}

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto AS nombre_cia, fecha, concepto AS producto, unidad, importe AS cantidad, obs FROM pedidos_tmp AS p LEFT JOIN";
	$sql .= " catalogo_companias AS cc USING (num_cia) WHERE num_cia = $_GET[num_cia] AND p.status = 0 AND importe > 0 ORDER BY producto";
	$result = $db->query($sql);
	
	$tpl->newBlock("captura");
	$tpl->assign('num_cia', $result[0]['num_cia']);
	$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
	
	foreach ($result as $i => $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('index', count($result) > 1 ? "[$i]" : '');
		$tpl->assign('back', count($result) > 1 ? ($i > 0 ? '[' . ($i - 1) . ']' : '[' . (count($result) - 1) . ']') : '');
		$tpl->assign('next', count($result) > 1 ? ($i < count($result) - 1 ? '[' . ($i + 1) . ']' : '[0]') : '');
		
		$tpl->assign('id', $reg['id']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('producto', $reg['producto']);
		$tpl->assign('unidad', $reg['unidad']);
		$tpl->assign('cantidad', number_format($reg['cantidad'], 2, '.', ','));
		$tpl->assign('obs', trim(strtoupper($reg['obs'])) != '' ? trim(strtoupper($reg['obs'])) : '');
	}
	
	//$sql = "SELECT codmp, nombre FROM catalogo_mat_primas WHERE tipo_cia = 'TRUE' ORDER BY codmp";
	$sql = "SELECT codmp, nombre FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas USING (codmp) GROUP BY codmp, nombre ORDER BY codmp";
	$mp = $db->query($sql);
	for ($i = 0; $i < count($mp); $i++) {
		$tpl->newBlock("mp");
		$tpl->assign("cod", $mp[$i]['codmp']);
		$tpl->assign("nombre", str_replace("\"", "\\\"", $mp[$i]['nombre']));
	}
	
	$sql = "SELECT num_proveedor, nombre FROM catalogo_proveedores WHERE num_proveedor < 9000 ORDER BY num_proveedor";
	$pro = $db->query($sql);
	for ($i = 0; $i < count($pro); $i++) {
		$tpl->newBlock("pro");
		$tpl->assign("num_pro", $pro[$i]['num_proveedor']);
		$tpl->assign("nombre", str_replace(array("\""), array(""), $pro[$i]['nombre']));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');

$sql = "SELECT num_cia, nombre_corto AS nombre FROM pedidos_tmp AS p LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE p.status = 0 AND importe > 0";
$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
if ($result = $db->query($sql))
	foreach ($result as $reg) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
	}

$tpl->printToScreen();
?>