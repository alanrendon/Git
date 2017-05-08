<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/pan/pan_imp_hoj.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 1, date('Y'))));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia <= 300 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre');
foreach ($result as $reg) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('nombre', $reg['nombre']);
}

if (!in_array($_SESSION['iduser'], array(1, 4, 19, 8, 6, 62, 49, 64))) {
	$tpl->assign('disabled', ' disabled');
}

$tpl->printToScreen();
?>