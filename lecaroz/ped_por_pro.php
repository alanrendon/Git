<?php
// PORCENTAJES DE PRODUCTO POR PROVEEDOR
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

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No se han definido proveedores para este producto";

if (isset($_POST['numfilas'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		$sql = "UPDATE catalogo_productos_proveedor SET porcentaje = ".($_POST['porcentaje'.$i] > 0 ? $_POST['porcentaje'.$i] : 0)." WHERE id = {$_POST['id'.$i]}";
		ejecutar_script($sql,$dsn);
	}
	
	header("location: ./ped_por_pro.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_por_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['codmp'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

// Obtener los proveedores del producto
$sql = "SELECT id, num_proveedor, nombre, porcentaje, contenido, precio, tipo_presentacion.descripcion AS unidad FROM catalogo_productos_proveedor";
$sql .= " LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN tipo_presentacion ON (idpresentacion = unidad) WHERE codmp = $_GET[codmp] ORDER BY num_proveedor, precio";
$result = $db->query($sql);

//$sql = "SELECT id,num_proveedor,nombre,porcentaje FROM catalogo_productos_proveedor LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE codmp = $_GET[codmp] ORDER BY num_proveedor";
//$result = ejecutar_script($sql,$dsn);

if (!$result) {
	header("location: ./ped_por_pro.php?codigo_error=1");
	die;
}

$tpl->newBlock("porcentajes");
$tpl->assign("codmp", $_GET['codmp']);
$nombre = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]");
$tpl->assign("nombre_mp", $nombre[0]['nombre']);
$tpl->assign("numfilas", count($result));

$total = 0;
foreach ($result as $i => $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : count($result) - 1);
	$tpl->assign("id", $reg['id']);
	$tpl->assign("num_proveedor", $reg['num_proveedor']);
	$tpl->assign("nombre", $reg['nombre']);
	$tpl->assign("contenido", $reg['contenido']);
	$tpl->assign("unidad", $reg['unidad']);
	$tpl->assign("precio", number_format($reg['precio'], 2, ".", ","));
	$tpl->assign("porcentaje", $reg['porcentaje']);
	$total += $reg['porcentaje'];
}
$tpl->assign("porcentajes.total",$total);

$tpl->printToScreen();
?>