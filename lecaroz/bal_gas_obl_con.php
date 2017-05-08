<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gas_obl_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$sql = "DELETE FROM gastos_obligados WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	$db->query($sql);
	header("location: ./bal_gas_obl_con.php?num_cia=$_POST[num_cia]&codgastos=$_POST[codgastos]");
	die;
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);
	}
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id, num_cia, nombre_corto, codgastos, descripcion FROM gastos_obligados LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_gastos USING (codgastos)";
$sql .= $_GET['num_cia'] > 0 || $_GET['codgastos'] > 0 ? " WHERE" : "";
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['codgastos'] > 0 ? ($_GET['num_cia'] > 0 ? " AND" : "") . " codgastos = $_GET[codgastos]" : "";
$sql .= " ORDER BY num_cia, codgastos";
$result = $db->query($sql);

if (!$result) {
	header("location: ./bal_gas_obl_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
foreach ($result as $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("id", $reg['id']);
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre_corto']);
	$tpl->assign("codgastos", $reg['codgastos']);
	$tpl->assign("desc", $reg['descripcion']);
}

$tpl->printToScreen();
?>