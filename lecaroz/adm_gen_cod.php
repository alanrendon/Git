<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compaρνa
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia <= 300";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/adm/adm_gen_cod.tpl");
$tpl->prepare();

if (isset($_POST['c'])) {
	$codigo = 150 * intval($_POST['c'], 10) * intval(date('d'), 10) * intval(date('n'), 10);
	
	$tpl->newBlock('codigo');
	$tpl->assign('codigo', $codigo);echo $codigo;
	die(/*$tpl->getOutputContent()*/);
}

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock('datos');

$tpl->printToScreen();
?>