<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_mod.tpl");
$tpl->prepare();

// Insertar datos
if (isset($_POST['id'])) {
	$datos['num_cia'] = $_POST['num_cia'];
	$datos['fecha'] = $_POST['fecha'];
	$datos['deposito'] = $_POST['deposito'];
	$datos['importe'] = $_POST['importe'];
	$datos['tipo'] = $_POST['tipo'];
	$datos['descripcion'] = strtoupper($_POST['descripcion']);
	$datos['imp'] = "FALSE";
	
	$db->query($db->preparar_update("faltantes_cometra", $datos, "id = $_POST[id]"));
	$db->desconectar();
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$sql = "SELECT id, num_cia, nombre_corto, fecha, deposito, importe, tipo, descripcion FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->assign("id", $result[0]['id']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$tpl->assign("nombre_cia", $result[0]['nombre_corto']);
$tpl->assign("fecha", $result[0]['fecha']);
$tpl->assign("deposito", $result[0]['deposito'] != 0 ? number_format($result[0]['deposito'], 2, ".", "") : "&nbsp;");
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ""));
$tpl->assign($result[0]['tipo'], "selected");
$tpl->assign("descripcion", $result[0]['descripcion']);

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = $db->query($sql);

for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$tpl->printToScreen();
$db->desconectar();
?>