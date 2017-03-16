<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Ya existe en código en el catálogo";
$descripcion_error[2] = "No capturo ningun movimiento";

if (isset($_POST['cod_mov'])) {
	if ($db->query("SELECT cod_mov FROM catalogo_mov_santander WHERE cod_mov = $_POST[cod_mov] GROUP BY cod_mov")) {
		header("location: ./ban_cat_san_altas.php?codigo_error=1");
		die;
	}
	
	$sql = "";
	for ($i = 0; $i < count($_POST['cod_banco']); $i++)
		if ($_POST['cod_banco'][$i] > 0 || $_POST['cod_banco'][$i] === "0") {
			$sql .= "INSERT INTO catalogo_mov_santander (cod_mov, cod_banco, descripcion, tipo_mov, entra_bal) VALUES (";
			$sql .= "$_POST[cod_mov], {$_POST['cod_banco'][$i]}, '" . strtoupper($_POST['descripcion']) . "', '$_POST[tipo_mov]', '$_POST[entra_bal]');\n";
		}
	
	if ($sql != "") $db->query($sql);
	header("location: ./ban_cat_san_altas.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_cat_san_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

function ultimoCod() {
	global $db;
	
	$result = $db->query("SELECT cod_mov FROM catalogo_mov_santander GROUP BY cod_mov ORDER BY cod_mov");
	
	if (!$result)
		return 1;
		
	for ($i = 0, $j = 1; $i < count($result); $i++, $j++)
		if ($result[$i]['cod_mov'] != $j)
			return $j;
	
	return $j;
}

$tpl->assign("cod_mov", ultimoCod());

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>