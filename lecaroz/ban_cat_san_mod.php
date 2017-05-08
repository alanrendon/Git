<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Ya existe el código en el catálogo";
$descripcion_error[2] = "No capturo ningun movimiento";

if (isset($_POST['cod_mov'])) {
	$sql = "DELETE FROM catalogo_mov_santander WHERE cod_mov = $_POST[cod_mov];\n";
	for ($i = 0; $i < count($_POST['cod_banco']); $i++)
		if ($_POST['cod_banco'][$i] > 0 || $_POST['cod_banco'][$i] === "0") {
			$sql .= "INSERT INTO catalogo_mov_santander (cod_mov, cod_banco, descripcion, tipo_mov, entra_bal) VALUES (";
			$sql .= "$_POST[cod_mov], {$_POST['cod_banco'][$i]}, '" . strtoupper($_POST['descripcion']) . "', '$_POST[tipo_mov]', '$_POST[entra_bal]');\n";
		}
	$db->query($sql);
	header("location: ./ban_cat_san_mod.php");
	die;
}

if (isset($_GET['del'])) {
	$db->query("DELETE FROM catalogo_mov_santander WHERE cod_mov = $_GET[del]");
	header("location: ./ban_cat_san_mod.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_cat_san_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['mod'])) {
	$result = $db->query("SELECT * FROM catalogo_mov_santander WHERE cod_mov = $_GET[mod] ORDER BY cod_banco");
	
	$tpl->newBlock("mod");
	$cod_mov = NULL;
	foreach ($result as $i => $cod) {
		if ($cod_mov != $cod['cod_mov']) {
			$cod_mov = $cod['cod_mov'];
			
			$tpl->assign("cod_mov", $cod_mov);
			$tpl->assign("descripcion", $cod['descripcion']);
			$tpl->assign("tipo_$cod[tipo_mov]", "checked");
			$tpl->assign("bal_$cod[entra_bal]", "checked");
		}
		$tpl->assign("cod_banco$i", $cod['cod_banco']);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("list");

$sql = "SELECT * FROM catalogo_mov_santander ORDER BY cod_mov, cod_banco";
$result = $db->query($sql);

if (!$result) {
	$tpl->newBlock("no_result");
	$tpl->printToScreen();
	die;
}

$cod_mov = NULL;
foreach ($result as $cod) {
	if ($cod_mov != $cod['cod_mov']) {
		$cod_mov = $cod['cod_mov'];
		
		$tpl->newBlock("row");
		$tpl->assign("cod_mov", $cod_mov);
		$tpl->assign("descripcion", $cod['descripcion']);
		$tpl->assign("tipo_mov", $cod['tipo_mov'] == "f" ? "<font color='#0000CC'>ABONO</font>" : "<font color='#CC0000'>CARGO</font>");
		$tpl->assign("bal", $cod['entra_bal'] == "t" ? "SI" : "NO");
		
		$cod_banco = $cod['cod_banco'];
		$tpl->assign("cod_banco", $cod_banco);
	}
	else {
		$cod_banco .= " - $cod[cod_banco]";
		$tpl->assign("cod_banco", $cod_banco);
	}
}

$tpl->printToScreen();
?>