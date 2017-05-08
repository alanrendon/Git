<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31, 32);

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_proveedor'][$i] && $_POST['codgastos'][$i] && $_POST['importe'][$i] > 0 && $_POST['total'][$i] > 0) {
			$sql .= "INSERT INTO pre_cheques (num_cia, num_proveedor, codgastos, concepto, importe, iva, ret_iva, isr, total) VALUES (";
			$sql .= "{$_POST['num_cia'][$i]}, {$_POST['num_proveedor'][$i]}, {$_POST['codgastos'][$i]}, ";
			$sql .= "'" . strtoupper($_POST['concepto'][$i]) . "', ";
			$sql .= str_replace(",", "", $_POST['importe'][$i]) . ", ";
			$sql .= ($_POST['iva'][$i] > 0 ? str_replace(",", "", $_POST['iva'][$i]) : "NULL") . ", ";
			$sql .= ($_POST['ret_iva'][$i] != 0 ? str_replace(",", "", $_POST['ret_iva'][$i]) : "NULL") . ", ";
			$sql .= ($_POST['isr'][$i] != 0 ? str_replace(",", "", $_POST['isr'][$i]) : "NULL") . ", ";
			$sql .= str_replace(",", "", $_POST['total'][$i]);
			$sql .= ");\n";
		}
	$db->query($sql);
	header("location: ./ban_cap_che_fij.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cap_che_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$numfilas = 20;

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	
}

$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias" . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '') . " ORDER BY num_cia");
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$pro = $db->query("SELECT num_proveedor, nombre FROM catalogo_proveedores ORDER BY num_proveedor");
for ($i = 0; $i < count($pro); $i++) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro[$i]['num_proveedor']);
	$tpl->assign("nombre_pro", $pro[$i]['nombre']);
}

$gas = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
for ($i = 0; $i < count($gas); $i++) {
	$tpl->newBlock("gas");
	$tpl->assign("codgastos", $gas[$i]['codgastos']);
	$tpl->assign("nombre_gas", $gas[$i]['descripcion']);
}

$tpl->printToScreen();
?>