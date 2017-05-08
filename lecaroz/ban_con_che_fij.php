<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31, 32);

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['id'])) {
	$sql = "DELETE FROM pre_cheques WHERE id IN (";
	for ($i = 0; $i < count($_POST['id']); $i++)
		$sql .= $_POST['id'][$i] . ($i < count($_POST['id']) - 1 ? ", " : ")");
	
	$db->query($sql);
	header("location: ./ban_con_che_fij.php?num_cia=$_POST[num_cia]&num_pro=$_POST[num_pro]&codgastos=$_POST[codgastos]");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_con_che_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto, pre_cheques.num_proveedor, catalogo_proveedores.nombre AS nombre_pro, codgastos, catalogo_gastos.descripcion AS nombre_gas, concepto, importe, iva, ret_iva, isr, total";
	$sql .= " FROM pre_cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_proveedores ON (pre_cheques.num_proveedor = catalogo_proveedores.num_proveedor) LEFT JOIN catalogo_gastos USING (codgastos)";
	$sql .= $_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || $_GET['codgastos'] > 0 || $_SESSION['iduser'] != 1 ? " WHERE" : "";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['num_pro'] > 0 ? ($_GET['num_cia'] > 0 ? " AND" : "") . " pre_cheques.num_proveedor = $_GET[num_pro]" : "";
	$sql .= $_GET['codgastos'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 ? " AND" : "") . " codgastos = $_GET[codgastos]" : "";
	$sql .= $_SESSION['iduser'] != 1 ? (($_GET['num_cia'] > 0 || $_GET['num_pro'] > 0 || $_GET['codgastos'] > 0 ? " AND" : "") . ' num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '';
	$sql .= " ORDER BY num_cia, codgastos, pre_cheques.num_proveedor";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_con_che_fij.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tpl->assign("num_pro", $_GET['num_pro']);
	$tpl->assign("codgastos", $_GET['codgastos']);
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("num_cia", "{$result[$i]['num_cia']} {$result[$i]['nombre_corto']}");
		$tpl->assign("num_pro", "{$result[$i]['num_proveedor']} {$result[$i]['nombre_pro']}");
		$tpl->assign("codgastos", "{$result[$i]['codgastos']} {$result[$i]['nombre_gas']}");
		$tpl->assign("concepto", $result[$i]['concepto']);
		$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
		$tpl->assign("iva", $result[$i]['iva'] > 0 ? number_format($result[$i]['iva']) : "");
		$tpl->assign("ret_iva", $result[$i]['ret_iva'] != 0 ? number_format($result[$i]['ret_iva'], 2, ".", ",") : "");
		$tpl->assign("isr", $result[$i]['isr'] != 0 ? number_format($result[$i]['isr'], 2, ".", ",") : "");
		$tpl->assign("total", number_format($result[$i]['total'], 2, ".", ","));
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$gasto = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
for ($i = 0; $i < count($gasto); $i++) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $gasto[$i]['codgastos']);
	$tpl->assign("descripcion", $gasto[$i]['descripcion']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>