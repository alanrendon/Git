<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_gas_caj_fij_minimod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$sql = "UPDATE gastos_caja_fijos SET";
	$sql .= " num_cia = $_POST[num_cia],";
	$sql .= " cod_gastos = $_POST[cod_gastos],";
	$sql .= " importe = " . get_val($_POST['importe']) . ",";
	$sql .= " comentario = '" . strtoupper($_POST['comentario']) . "',";
	$sql .= " tipo_mov = " . $_POST['tipo_mov'] . ",";
	$sql .= " clave_balance = " . $_POST['bal'];
	$sql .= " WHERE id = $_POST[id];\n";
	
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("num_cia", $_POST['num_cia']);
	$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_POST[num_cia]");
	$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
	$gasto = $db->query("SELECT descripcion FROM catalogo_gastos_caja WHERE id = $_POST[cod_gastos]");
	$tpl->assign("cod_gastos", $gasto[0]['descripcion']);
	$tpl->assign("importe", number_format($_POST['importe'], 2, ".", ","));
	$tpl->assign("comentario", $_POST['comentario']);
	$tpl->assign("tipo_mov", $_POST['tipo_mov'] == "FALSE" ? "EGRESO" : "INGRESO");
	$tpl->assign("balance", $_POST['bal'] == "FALSE" ? "NO" : "SI");
	
	$tpl->printToScreen();
	
	die;
}

$result = $db->query("SELECT * FROM gastos_caja_fijos WHERE id = $_GET[id]");

$tpl->newBlock("mod");
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));
$tpl->assign("comentario", $result[0]['comentario']);
$tpl->assign("bal_" . ($result[0]['clave_balance'] == "f" ? "false" : "true"), "checked");
$tpl->assign("tipo_" . ($result[0]['tipo_mov'] == "f" ? "false" : "true"), "checked");

$gasto = $db->query("SELECT * FROM catalogo_gastos_caja ORDER BY descripcion");

for ($j = 0; $j < count($gasto); $j++) {
	$tpl->newBlock("gasto");
	$tpl->assign("id", $gasto[$j]['id']);
	$tpl->assign("descripcion", $gasto[$j]['descripcion']);
	if ($result[0]['cod_gastos'] == $gasto[$j]['id']) $tpl->assign("selected", "selected");
}

$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$tpl->printToScreen();
?>