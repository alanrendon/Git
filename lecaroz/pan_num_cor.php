<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		$sql .= "UPDATE catalogo_companias SET cortes_caja = " . get_val($_POST['cortes'][$i]) . " WHERE num_cia = {$_POST['num_cia'][$i]};\n";
	
	$db->query($sql);
	header("location: ./pan_num_cor.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_num_cor.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$cias = $db->query("SELECT num_cia, nombre_corto, cortes_caja FROM catalogo_companias WHERE num_cia < 100 OR num_cia IN (702, 703) ORDER BY num_cia");

foreach ($cias as $i => $cia) {
	$tpl->newBlock("fila");
	$tpl->assign("next", $i < count($cias) - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : count($cias) - 1);
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
	$tpl->assign("cortes", $cia['cortes_caja']);
}

$tpl->printToScreen();
?>