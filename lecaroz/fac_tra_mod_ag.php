<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if (!in_array($_SESSION['iduser'], array(1))) die("la estoy modificando");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_tra_mod_ag.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "UPDATE catalogo_trabajadores SET solo_aguinaldo = '$_POST[check]' WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("value", $_POST['check'] == 'TRUE' ? "§" : '');
	$tpl->printToScreen();
	die;
}

$datos = $db->query("SELECT nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno, solo_aguinaldo FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno) WHERE id = $_GET[id]");

$tpl->newBlock("datos");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign("ap_paterno", $datos[0]['ap_paterno']);
$tpl->assign("ap_materno", $datos[0]['ap_materno']);
$tpl->assign("nombre", $datos[0]['nombre']);
$tpl->assign($datos[0]['solo_aguinaldo'] == 't' ? 't' : 'f', "checked");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>
