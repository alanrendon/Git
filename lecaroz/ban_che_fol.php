<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_che_fol.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cheque'])) {
	$tpl->newBlock("datos");
	
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
}

// Obtener ultimo estatus de la impresión de los cheques
$sql = "SELECT * FROM cheques WHERE num_cheque = $_GET[num_cheque]";
//$sql .= " AND cuenta = $_GET[cuenta]";
$result = $db->query($sql);

if (!$result) {
	$db->desconectar();
	header("location: ./ban_che_fol.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");

$tpl->assign("num_cia", $result[0]['num_cia'] != "" ? $result[0]['num_cia'] : "---");
if ($result[0]['num_cia'] > 0)
	$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
else
	$nombre_cia[0]['nombre_corto'] = "---";
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("fecha", $result[0]['fecha'] != "" ? $result[0]['fecha'] : "---");
if ($result[0]['folio'] > 0)
	$fecha_con = $db->query("SELECT fecha_con FROM estado_cuenta WHERE num_cia = {$result[0]['num_cia']} AND folio = {$result[0]['folio']}");
else
	$fecha_con[0]['fecha_con'] = "---";
$tpl->assign("fecha_con", $fecha_con[0]['fecha_con'] != "" ? $fecha_con[0]['fecha_con'] : "---");
$tpl->assign("fecha_cancelacion", $result[0]['fecha_cancelacion'] != "" ? $result[0]['fecha_cancelacion'] : "---");
$tpl->assign("a_nombre", $result[0]['a_nombre'] != "" ? $result[0]['a_nombre'] : "---");
$tpl->assign("folio", $result[0]['folio'] != "" ? $result[0]['folio'] : "---");
$tpl->assign("num_cheque", $_GET['num_cheque']);
if ($result[0]['codgastos'] > 0)
	$codgastos = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$result[0]['codgastos']}");
else
	$codgastos[0]['descripcion'] = "---";
$tpl->assign("codgastos", $result[0]['codgastos'] != "" ? $result[0]['codgastos'] : "---");
$tpl->assign("descripcion", $codgastos[0]['descripcion']);
$tpl->assign("importe", $result[0]['importe'] != "" ? number_format($result[0]['importe'], 2, ".", ",") : "&nbsp;");
$tpl->assign("concepto", $result[0]['concepto'] != "" ? $result[0]['concepto'] : "FOLIO CANCELADO");
$tpl->assign("facturas", $result[0]['facturas'] != "" ? $result[0]['facturas'] : "---");

$tpl->printToScreen();
$db->desconectar();
?>