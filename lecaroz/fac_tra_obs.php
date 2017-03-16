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
$tpl->assignInclude("body","./plantillas/fac/fac_tra_obs.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$obs = strtoupper(trim($_POST['obs']));
	$sql = "UPDATE catalogo_trabajadores SET observaciones = '$obs' WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock('close');
	die($tpl->printToScreen());
}

$sql = "SELECT num_emp, ap_paterno, ap_materno, nombre, observaciones FROM catalogo_trabajadores WHERE id = $_GET[id]";
$obs = $db->query($sql);

$tpl->newBlock('obs');
$tpl->assign('id', $_GET['id']);
$tpl->assign('num_emp', $obs[0]['num_emp']);
$tpl->assign('nombre', "{$obs[0]['nombre']} {$obs[0]['ap_paterno']} {$obs[0]['ap_materno']}");
$tpl->assign('obs', trim($obs[0]['observaciones']));

$tpl->printToScreen();
?>