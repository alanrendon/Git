<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$numfilas = 20;

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['codgastos'][$i] > 0 && !$db->query("SELECT id FROM gastos_obligados WHERE num_cia = {$_POST['num_cia'][$i]} AND codgastos = {$_POST['codgastos'][$i]}"))
			$sql .= "INSERT INTO gastos_obligados (num_cia, codgastos) VALUES ({$_POST['num_cia'][$i]}, {$_POST['codgastos'][$i]});";
	
	$db->query($sql);
	header("location: ./bal_gas_obl.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gas_obl.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia < 100 OR num_cia IN (702, 703) ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$gastos = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
foreach ($gastos as $gasto) {
	$tpl->newBlock("gasto");
	$tpl->assign("cod", $gasto['codgastos']);
	$tpl->assign("desc", $gasto['descripcion']);
}

$tpl->printToScreen();
?>