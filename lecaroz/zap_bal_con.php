<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_bal_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['password'])) {
	if (!$db->query("SELECT iduser FROM auth WHERE iduser = 28 AND password = '$_POST[password]'"))
		die(header('location: ./zap_bal_con.php'));
	
	$tpl->newBlock('datos');
	$tpl->assign(date('n', mktime(0, 0, 0, date('n') - 1)), ' selected');
	$tpl->assign('anio', date('Y'));
	
	$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia');
	foreach ($result as $reg) {
		$tpl->newBlock('c');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
	}
	die($tpl->printToScreen());
}

$tpl->newBlock('password');

$tpl->printToScreen();
?>