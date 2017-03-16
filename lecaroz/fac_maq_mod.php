<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_maq_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_maquina'])) {
	$sql = "UPDATE maquinaria SET marca = '" . trim(strtoupper($_POST['marca'])) . "',";
	$sql .= " descripcion = '" . trim(strtoupper($_POST['descripcion'])) . "',";
	$sql .= " capacidad = " . ($_POST['capacidad'] != 0 ? $_POST['capacidad'] : 0) . ",";
	$sql .= " cod_turno = $_POST[cod_turno],";
	$sql .= " num_serie = '" . trim(strtoupper($_POST['num_serie'])) . "',";
	$sql .= " num_cia = $_POST[num_cia],";
	$sql .= " fecha = " . ($_POST['fecha'] != "" ? "'$_POST[fecha]'" : "NULL");
	$sql .= " WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id, num_maquina, marca, descripcion, capacidad, num_serie, fecha, num_cia, nombre_corto, cod_turno FROM maquinaria LEFT JOIN catalogo_companias USING (num_cia)";
$sql .= " WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->newBlock("datos");
$tpl->assign("id", $result[0]['id']);
$tpl->assign("num_maquina", $result[0]['num_maquina']);
$tpl->assign("marca", $result[0]['marca']);
$tpl->assign("descripcion", $result[0]['descripcion']);
$tpl->assign("capacidad", $result[0]['capacidad'] != 0 ? number_format($result[0]['capacidad'], 2, ".", "") : "");
$tpl->assign("num_serie", $result[0]['num_serie']);
$tpl->assign("fecha", $result[0]['fecha']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre", $result[0]['nombre_corto']);

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$turnos = $db->query("SELECT cod_turno, descripcion FROM catalogo_turnos WHERE cod_turno NOT IN (5, 6, 7, 10) ORDER BY cod_turno");
foreach ($turnos as $turno) {
	$tpl->newBlock("turno");
	$tpl->assign("cod", $turno['cod_turno']);
	$tpl->assign("turno", $turno['descripcion']);
	if ($turno['cod_turno'] == $result[0]['cod_turno']) $tpl->assign("selected", " selected");
}

$tpl->printToScreen();
?>