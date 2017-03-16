<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['id'])) {
	$db->query("DELETE FROM gastos_caja_fijos WHERE id = $_GET[id]");
	header("location: ./bal_gas_caj_fij_mod.php?num_cia=$_GET[num_cia]&cod_gastos=$_GET[cod_gastos]");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_caj_fij_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT gastos_caja_fijos.id, num_cia, nombre_corto, cod_gastos, descripcion, importe, comentario, tipo_mov, clave_balance FROM gastos_caja_fijos LEFT JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id=gastos_caja_fijos.cod_gastos) LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= $_GET['num_cia'] > 0 || $_GET['cod_gastos'] > 0 ? " WHERE" : "";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['cod_gastos'] > 0 ? ($_GET['num_cia'] > 0 ? " AND" : "") . " cod_gastos = $_GET[cod_gastos]" : "";
	$sql .= " ORDER BY num_cia, tipo_mov, cod_gastos";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_gas_caj_fij_mod.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tpl->assign("cod_gastos", $_GET['cod_gastos']);
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("cod_gastos", $result[$i]['descripcion']);
		$tpl->assign("comentario", $result[$i]['comentario']);
		$tpl->assign("tipo_mov", $result[$i]['tipo_mov'] == "f" ? "EGRESO" : "INGRESO");
		$tpl->assign("balance", $result[$i]['clave_balance'] == "f" ? "NO" : "SI");
		$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$cod_gastos = $db->query("SELECT * FROM catalogo_gastos_caja ORDER BY descripcion");

for ($i = 0; $i < count($cod_gastos); $i++) {
	$tpl->newBlock("cod_gastos");
	$tpl->assign("id", $cod_gastos[$i]['id']);
	$tpl->assign("descripcion", $cod_gastos[$i]['descripcion']);
}

$tpl->printToScreen();
?>