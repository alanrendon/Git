<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_prc_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['codmp'])) {
	$sql = "";
	
	for ($i = 0; $i < count($_POST['codmp']); $i++)
		if ($_POST['codmp'][$i] > 0 && $_POST['precio_nuevo'][$i] > 0) {
			if ($id = $db->query("SELECT id FROM precios_guerra WHERE num_cia = {$_SESSION['psr']['num_cia']} AND codmp = {$_POST['codmp'][$i]}"))
				$sql .= "UPDATE precios_guerra SET precio_compra = {$_POST['precio_nuevo'][$i]} WHERE id = {$id[0]['id']};\n";
			else {
				$sql .= "INSERT INTO precios_guerra (num_cia, codmp, num_proveedor, precio_compra, precio_venta) VALUES (";
				$sql .= "{$_SESSION['psr']['num_cia']}, {$_POST['codmp'][$i]}, 13, {$_POST['precio_nuevo'][$i]}, 0);\n";
			}
		}
	
	if ($sql != "") $db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("precios");

$numfilas = 10;

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$mps = $db->query("SELECT codmp, nombre FROM catalogo_mat_primas WHERE tipo_cia = 'FALSE' ORDER BY codmp");
$precios = $db->query("SELECT codmp, precio_compra FROM precios_guerra WHERE num_cia = {$_SESSION['psr']['num_cia']} AND precio_compra > 0 ORDER BY codmp");
$invs = $db->query("SELECT codmp, precio_unidad FROM inventario_real WHERE num_cia = {$_SESSION['psr']['num_cia']} ORDER BY codmp");

function buscarPrecio($codmp) {
	global $precios;
	
	if (!$precios)
		return FALSE;
	
	foreach ($precios as $precio)
		if ($precio['codmp'] == $codmp)
			return $precio['precio_compra'];
	
	return FALSE;
}

function buscarInv($codmp) {
	global $invs;
	
	if (!$invs)
		return FALSE;
	
	foreach ($invs as $inv)
		if ($inv['codmp'] == $codmp)
			return $inv['precio_unidad'];
	
	return FALSE;
}

foreach ($mps as $mp) {
	$tpl->newBlock("mp");
	$tpl->assign("codmp", $mp['codmp']);
	$tpl->assign("nombre", $mp['nombre']);
	$precio = buscarPrecio($mp['codmp']);
	$inv = buscarInv($mp['codmp']);
	$tpl->assign("precio", $precio ? $precio : '"SIN PRECIO"');
	$tpl->assign("min", $inv > 0 ? $inv * 0.80 : 0);
	$tpl->assign("max", $inv > 0 ? $inv * 1.20 : 0);
}

$tpl->printToScreen();
?>