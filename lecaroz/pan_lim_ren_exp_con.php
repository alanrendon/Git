<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

$numfilas = 10;

if (isset($_GET['actualizar'])) {
	$sql = '';
	foreach ($_POST['idreg'] as $i => $id) {
		$sql .= "UPDATE catalogo_renta_exp SET importe = " . get_val($_POST['importe'][$i]) . " WHERE id = $id;\n";
	}
	
	$db->query($sql);
	
	die(header('location: pan_lim_ren_exp_con.php'));
}

// Borrar datos
if (isset($_GET['borrar'])) {
	$sql = 'UPDATE catalogo_renta_exp SET status = 0 WHERE id IN (' . implode(', ', $_POST['id']) . ')';
	$db->query($sql);
	
	die(header('location: pan_lim_ren_exp_con.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_lim_ren_exp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto, exp.nombre, importe FROM catalogo_renta_exp AS exp LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE exp.status = 1";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? " AND iduser = $_SESSION[iduser]" : '';
	$sql .= "ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./pan_lim_ren_exp_con.php?codigo_error=1'));
	
	$tpl->newBlock('consulta');
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_corto']);
		}
		$tpl->newBlock('fila');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('importe', $reg['importe']);
	}
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$sql = "SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora)";
$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? " WHERE iduser = $_SESSION[iduser]" : '';
$sql .= " ORDER BY num_cia";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>