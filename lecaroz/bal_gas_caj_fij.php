<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['importe'][$i] > 0) {
			$sql .= "INSERT INTO gastos_caja_fijos (num_cia, cod_gastos, importe, comentario, tipo_mov, clave_balance) VALUES (";
			$sql .= "{$_POST['num_cia'][$i]},";
			$sql .= " {$_POST['cod_gastos'][$i]},";
			$sql .= " {$_POST['importe'][$i]},";
			$sql .= " '" . strtoupper(trim($_POST['comentario'][$i])) . "',";
			$sql .= " {$_POST['tipo_mov'][$i]},";
			$sql .= " {$_POST['bal'][$i]});\n";
		}
	$db->query($sql);
	header("location: ./bal_gas_caj_fij.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_caj_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$numfilas = 20;

$gasto = $db->query("SELECT * FROM catalogo_gastos_caja ORDER BY descripcion");

for ($j = 0; $j < count($gasto); $j++) {
	$tpl->newBlock("gasto_all");
	$tpl->assign("id", $gasto[$j]['id']);
	$tpl->assign("descripcion", $gasto[$j]['descripcion']);
}

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	
	for ($j = 0; $j < count($gasto); $j++) {
		$tpl->newBlock("gasto");
		$tpl->assign("id", $gasto[$j]['id']);
		$tpl->assign("descripcion", $gasto[$j]['descripcion']);
	}
}

$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$tpl->printToScreen();
?>