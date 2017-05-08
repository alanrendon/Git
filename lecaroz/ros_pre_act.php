<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['codmp'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['codmp']); $i++)
		if (get_val($_POST['precio'][$i]) > 0)
			$sql .= "UPDATE precios_guerra SET precio_compra = {$_POST['precio'][$i]} WHERE codmp = {$_POST['codmp'][$i]} AND num_cia NOT IN (153, 155, 136, 147, 159, 178);\n";
	
	$db->query($sql);
	header("location: ./ros_pre_act.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pre_act.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT codmp, nombre, min(precio_compra) AS precio FROM precios_guerra LEFT JOIN catalogo_mat_primas USING (codmp) WHERE codmp IN (160, 700, 297, 363, 352, 600)";
$sql .= " AND num_cia NOT IN (153, 155, 136, 147, 159, 178) AND precio_compra > 0 GROUP BY codmp, nombre, orden ORDER BY orden";
$result = $db->query($sql);

foreach ($result as $i => $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("back", $i > 0 ? $i - 1 : count($result) - 1);
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("codmp", $reg['codmp']);
	$tpl->assign("nombre", $reg['nombre']);
	//$tpl->assign("precio", number_format($reg['precio'], 2, ".", ","));
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
die;

?>