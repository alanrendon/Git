<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mod_che_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$sql = "UPDATE pre_cheques SET";
	$sql .= " num_cia = $_POST[num_cia], num_proveedor = $_POST[num_proveedor], codgastos = $_POST[codgastos],";
	$sql .= " concepto = '" . strtoupper($_POST['concepto']) . "', ";
	$sql .= " importe = " . str_replace(",", "", $_POST['importe']) . ", ";
	$sql .= " iva = " . ($_POST['iva'] > 0 ? str_replace(",", "", $_POST['iva']) : "NULL") . ", ";
	$sql .= " ret_iva = " . ($_POST['ret_iva'] != 0 ? str_replace(",", "", $_POST['ret_iva']) : "NULL") . ", ";
	$sql .= " isr = " . ($_POST['isr'] != 0 ? str_replace(",", "", $_POST['isr']) : "NULL") . ", ";
	$sql .= " total = " . str_replace(",", "", $_POST['total']);
	$sql .= " WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("num_cia", "$_POST[num_cia] $_POST[nombre_cia]");
	$tpl->assign("num_pro", "$_POST[num_proveedor] $_POST[nombre_pro]");
	$tpl->assign("codgastos", "$_POST[codgastos] $_POST[nombre_gas]");
	$tpl->assign("concepto", $_POST['concepto']);
	$tpl->assign("importe", $_POST['importe']);
	$tpl->assign("iva", $_POST['iva']);
	$tpl->assign("ret_iva", $_POST['ret_iva']);
	$tpl->assign("isr", $_POST['isr']);
	$tpl->assign("total", $_POST['total']);
	$tpl->printToScreen();
	
	die;
}

$sql = "SELECT id, num_cia, nombre_corto, pre_cheques.num_proveedor AS num_pro, catalogo_proveedores.nombre AS nombre_pro, codgastos, catalogo_gastos.descripcion AS nombre_gas, concepto, importe, iva, ret_iva, isr, total";
$sql .= " FROM pre_cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_proveedores ON (pre_cheques.num_proveedor = catalogo_proveedores.num_proveedor) LEFT JOIN catalogo_gastos USING (codgastos)";
$sql .= " WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->newBlock("mod");
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre_cia", $result[0]['nombre_corto']);
$tpl->assign("num_pro", $result[0]['num_pro']);
$tpl->assign("nombre_pro", $result[0]['nombre_pro']);
$tpl->assign("codgastos", $result[0]['codgastos']);
$tpl->assign("nombre_gas", $result[0]['nombre_gas']);
$tpl->assign("concepto", $result[0]['concepto']);
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));
$tpl->assign("iva", $result[0]['iva'] > 0 ? number_format($result[0]['iva'], 2, ".", ",") : "");
$tpl->assign("ret_iva", $result[0]['ret_iva'] != 0 ? number_format($result[0]['ret_iva'], 2, ".", ",") : "");
$tpl->assign("isr", $result[0]['isr'] != 0 ? number_format($result[0]['isr'], 2, ".", ",") : "");
$tpl->assign("total", number_format($result[0]['total'], 2, ".", ","));

$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
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