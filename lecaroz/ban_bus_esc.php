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
$tpl->assignInclude("body", "./plantillas/ban/ban_bus_esc.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha", date("1/m/Y"));
	
	$cod_mov = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_bancos GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	for ($i = 0; $i < count($cod_mov); $i++) {
		$tpl->newBlock("cod_mov");
		$tpl->assign("cod_mov", $cod_mov[$i]['cod_mov']);
		$tpl->assign("descripcion", $cod_mov[$i]['descripcion']);
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
	die;
}

$tpl->newBlock("listado");

$sql = "SELECT num_cia, nombre_corto, fecha, fecha_con, estado_cuenta.tipo_mov AS tipo_mov, importe, folio, cod_mov, descripcion, concepto";
$sql .= " FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_mov_bancos USING (cod_mov) WHERE";
//$sql .= " cuenta = $_GET[cuenta] AND"
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" . ($_GET['fecha1'] != "" || $_GET['tipo_mov'] != "" || $_GET['importe'] || $_GET['cod_mov'] != "" || $_GET['folio'] > 0 || $_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['fecha1'] != "" ? ($_GET['fecha2'] != "" ? " fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : " fecha >= '$_GET[fecha1]'") . ($_GET['tipo_mov'] != "" || $_GET['importe'] || $_GET['cod_mov'] != "" || $_GET['folio'] > 0 || $_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['tipo_mov'] != "" ? " estado_cuenta.tipo_mov = '$_GET[tipo_mov]'" . ($_GET['importe'] || $_GET['cod_mov'] != "" || $_GET['folio'] > 0 || $_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['importe'] > 0 ? ($_GET['tipo_importe'] == "exacto" ? " importe = $_GET[importe]" : " importe BETWEEN " . ($_GET['importe'] - 0.99) . " AND " . ($_GET['importe'] + 0.99)) . ($_GET['cod_mov'] != "" || $_GET['folio'] > 0 || $_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['cod_mov'] > 0 ? " cod_mov = $_GET[cod_mov]" . ($_GET['folio'] > 0 || $_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['folio'] > 0 ? " folio = $_GET[folio]" . ($_GET['concepto'] != "" ? " AND" : "") : "";
$sql .= $_GET['concepto'] != "" ? ($_GET['tipo_concepto'] == "exacto" ? " concepto = '$_GET[concepto]'" : " concepto LIKE '%$_GET[concepto]%'") : "";
$sql .= " GROUP BY num_cia, nombre_corto, fecha, fecha_con, estado_cuenta.tipo_mov, importe, folio, cod_mov, descripcion, concepto";
$sql .= " ORDER BY num_cia, fecha, estado_cuenta.tipo_mov";

$result = $db->query($sql);

if ($result) {
	$tpl->assign("num_reg", count($result));
	
	$tpl->newBlock("result");
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("fecha_con", $result[$i]['fecha_con'] != "" ? $result[$i]['fecha_con'] : "&nbsp;");
		$tpl->assign("deposito", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("retiro", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("folio", $result[$i]['folio'] > 0 ? $result[$i]['folio'] : "&nbsp;");
		$tpl->assign("cod_mov", $result[$i]['cod_mov']);
		$tpl->assign("descripcion", $result[$i]['descripcion']);
		$tpl->assign("concepto", $result[$i]['concepto'] != "" ? $result[$i]['concepto'] : "&nbsp;");
	}
}
else {
	$tpl->assign("num_reg", "0");
	$tpl->newBlock("no_result");
}

$tpl->printToScreen();
$db->desconectar();
?>