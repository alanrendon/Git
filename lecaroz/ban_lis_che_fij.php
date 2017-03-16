<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31, 32);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_lis_che_fij.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto, pre_cheques.num_proveedor, catalogo_proveedores.nombre AS nombre_pro, codgastos, catalogo_gastos.descripcion AS nombre_gas, concepto, importe, iva, ret_iva, isr, total";
	$sql .= " FROM pre_cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_proveedores ON (pre_cheques.num_proveedor = catalogo_proveedores.num_proveedor) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['codgastos'] > 0 ? " AND codgastos = $_GET[codgastos]" : "";
	$sql .= " ORDER BY num_cia, codgastos, pre_cheques.num_proveedor";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_lis_che_fij.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("num_proveedor", $result[$i]['num_proveedor']);
		$tpl->assign("nombre_pro", $result[$i]['nombre_pro']);
		$tpl->assign("codgastos", $result[$i]['codgastos']);
		$tpl->assign("nombre_gas", $result[$i]['nombre_gas']);
		$tpl->assign("concepto", $result[$i]['concepto']);
		$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
		$tpl->assign("iva", $result[$i]['iva'] != 0 ? number_format($result[$i]['iva']) : "&nbsp;");
		$tpl->assign("ret_iva", $result[$i]['ret_iva'] != 0 ? number_format($result[$i]['ret_iva'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("isr", $result[$i]['isr'] != 0 ? number_format($result[$i]['isr'], 2, ".", ",") : "");
		$tpl->assign("total", number_format($result[$i]['total'], 2, ".", ","));
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>