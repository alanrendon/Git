<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_rei_pol.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$numfilas = 10;

if (isset($_POST['num_cia'])) {
	$sql = "SELECT id, num_cia, nombre_corto AS nombre_cia, cheques.num_proveedor AS num_pro, a_nombre AS nombre_pro, fecha, folio, concepto, importe, cuenta FROM cheques LEFT JOIN";
	$sql .= " catalogo_companias USING (num_cia) WHERE num_cia = 1 AND fecha = '2006/12/21'";
}

$tpl->newBlock("datos");
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila_cap");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$tpl->printToScreen();
?>