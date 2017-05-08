<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';
include './includes/pcl.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^|");

$numfilas = 10;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_not_cre_cap.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	
	
	die;
}

$tpl->newBlock("captura");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre']);
}

$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores WHERE num_proveedor BETWEEN 9000 AND 9999 ORDER BY num_pro");
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", $pro['nombre']);
}

$cods = $db->query("SELECT codgastos, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos");
foreach ($cods as $cod) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $cod['codgastos']);
	$tpl->assign('desc', $cod['desc']);
}

$tpl->printToScreen();
?>