<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['id'])) {
	$sql = "UPDATE maquinaria SET status = 0 WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	
	$db->query($sql);
	header("location: ./fac_maq_con.php?num_cia=$_POST[num_cia]&num_maquina=$_POST[num_maquina]");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_maq_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
	}
	$tpl->printToScreen();
	die;
}

$sql = "SELECT id, num_maquina, marca, m.descripcion, capacidad, ct.descripcion AS turno, num_serie, num_cia, cc.nombre_corto AS nombre, fecha FROM maquinaria AS m LEFT JOIN";
$sql .= " catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_turnos AS ct USING (cod_turno) WHERE m.status = 1";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['num_maquina'] > 0 ? " AND num_maquina = $_GET[num_maquina]" : "";
$sql .= " ORDER BY num_maquina";
$result = $db->query($sql);

if (!$result) {
	header("location: ./fac_maq_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("num_maquina", $_GET['num_maquina']);
foreach ($result as $reg) {
	$tpl->newBlock("fila");
	foreach ($reg as $tag => $value)
		$tpl->assign($tag, $value != "" ? $value : "&nbsp;");
}
$tpl->printToScreen();
?>