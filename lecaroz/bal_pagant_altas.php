<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------

$db = new DBclass($dsn, "autocommit=yes");

$numfilas = 20;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$sql = "";
	$count = 0;
	for ($i = 0; $i < $numfilas; $i++) {
		if ($_POST['num_cia'][$i] > 0 && $_POST['mes1'][$i] > 0 && $_POST['anio1'][$i] > 0 && $_POST['mes2'][$i] > 0 && $_POST['anio2'][$i] > 0 && $_POST['importe'][$i] > 0) {
			$datos['num_cia'] = $_POST['num_cia'][$i];
			$datos['importe'] = $_POST['importe'][$i];
			$datos['fecha_ini'] = "1/{$_POST['mes1'][$i]}/{$_POST['anio1'][$i]}";
			$datos['fecha_fin'] = date("d/m/Y", mktime(0, 0, 0, $_POST['mes2'][$i] + 1, 0, $_POST['anio2'][$i]));
			$datos['concepto'] = strtoupper($_POST['concepto'][$i]);
			
			$sql .= $db->preparar_insert("pagos_anticipados", $datos) . ";\n";
			$count++;
		}
	}
	if ($count > 0)
		$db->query($sql);
	$db->desconectar();
	
	header("location: ./bal_pagant_altas.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_pagant_altas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("day", date("d"));
$tpl->assign("month", date("n"));
$tpl->assign("year", date("Y"));

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = $db->query($sql);

for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

for ($i=0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign("mes", date("n"));
	$tpl->assign("anio", date("Y"));
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
$db->desconectar();
die;
?>