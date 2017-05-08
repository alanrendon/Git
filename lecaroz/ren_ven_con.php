<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_ven_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['arr'])) {
	$sql = "SELECT num_local, nombre_local, cod_arrendador, nombre, renta, fecha_pago  FROM recibos_rentas LEFT JOIN catalogo_arrendatarios AS ca ON (ca.id = local)";
	$sql .= " LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE fecha_pago BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'";
	$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
	$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
	$sql .= " ORDER BY cod_arrendador, num_local";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ren_ven_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	
	foreach ($result as $reg) {
		$tpl->newBlock("fila");
		$tpl->assign("num", $reg['num_local']);
		$tpl->assign("nombre", $reg['nombre_local']);
		$tpl->assign("arr", $reg['nombre']);
		$tpl->assign("renta", number_format($reg['renta'], 2, ".", ","));
		$tpl->assign("fecha_ven", $reg['fecha_pago']);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("fecha1", date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
$tpl->assign("fecha2", date("d/m/Y", mktime(0, 0, 0, date("m"), 0, date("Y"))));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>