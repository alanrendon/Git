<?php

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod_tur.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "UPDATE catalogo_trabajadores SET cod_turno = $_POST[turno] WHERE id = $_POST[id]";
	$db->query($sql);
	
	$puesto = $db->query("SELECT descripcion FROM catalogo_turnos WHERE cod_turno = $_POST[turno]");
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("descripcion", $puesto[0]['descripcion']);
	$tpl->printToScreen();
	die;
}

$sql = "SELECT cod_turno FROM catalogo_trabajadores WHERE id = $_GET[id]";
$cod_turno = $db->query($sql);

$turno = $db->query("SELECT * FROM catalogo_turnos ORDER BY cod_turno");

$sql = "SELECT nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno)";
$sql .= " WHERE id = $_GET[id]";
$datos = $db->query($sql);

$tpl->newBlock("modificar");
$tpl->assign("nombre", "{$datos[0]['ap_paterno']} {$datos[0]['ap_materno']} {$datos[0]['nombre']}");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
for ($i = 0; $i < count($turno); $i++) {
	$tpl->newBlock("turno");
	$tpl->assign("turno", $turno[$i]['descripcion']);
	$tpl->assign("id", $turno[$i]['cod_turno']);
	if ($turno[$i]['cod_turno'] == $cod_turno[0]['cod_turno']) $tpl->assign("selected", "selected");
}

// Imprimir el resultado
$tpl->printToScreen();
?>