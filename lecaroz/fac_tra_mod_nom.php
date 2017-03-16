<?php

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre_corto'];
	
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod_nom.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$num_cia_emp = $_POST['num_cia_emp'] > 0 ? $_POST['num_cia_emp'] : 'num_cia';
	
	$sql = "UPDATE catalogo_trabajadores SET ap_paterno = '" . strtoupper(trim($_POST['ap_paterno'])) . "', ap_materno = '" . strtoupper(trim($_POST['ap_materno'])) . "', nombre = '" . strtoupper(trim($_POST['nombre'])) . "', num_cia_emp = $num_cia_emp WHERE id = $_POST[id]";
	$db->query($sql);
	
	$nombre = strtoupper(trim($_POST['ap_paterno'])) . " " . (trim($_POST['ap_materno']) != "" ? strtoupper(trim($_POST['ap_materno'])) . " " : "") . strtoupper(trim($_POST['nombre']));
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("nombre", $nombre);
	$tpl->printToScreen();
	die;
}

$sql = "SELECT num_cia_emp, nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno)";
$sql .= " WHERE id = $_GET[id]";
$datos = $db->query($sql);

$cia_emp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$datos[0]['num_cia_emp']}");

$tpl->newBlock("modificar");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign('num_cia_emp', $datos[0]['num_cia_emp']);
$tpl->assign('nombre_cia_emp', $cia_emp[0]['nombre_corto']);
$tpl->assign("ap_paterno", $datos[0]['ap_paterno']);
$tpl->assign("ap_materno", $datos[0]['ap_materno']);
$tpl->assign("nombre", $datos[0]['nombre']);

// Imprimir el resultado
$tpl->printToScreen();
?>