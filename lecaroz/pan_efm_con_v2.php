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
$tpl->assignInclude("body", "./plantillas/pan/pan_efm_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha1", date("01/m/Y"));
	$tpl->assign("fecha2", date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

$num_cia = $_GET['num_cia'];
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];

if ($fecha1 != "" && $fecha2 != "") {
	$sql = "SELECT num_cia, nombre_corto, sum(am) AS am, sum(am_error) AS am_error, sum(pm) AS pm, sum(pm_error) AS pm_error, sum(pastel) AS pastel, sum(venta_pta) AS venta_pta,";
	$sql .= " sum(pastillaje) AS pastillaje, sum(otros) AS otros, sum(desc_pastel) AS desc_pastel FROM captura_efectivos LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora)" : "";
	$sql .= " WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " AND iduser = $_SESSION[iduser]" : "";
	$sql .= $num_cia > 0 ? " AND num_cia = $num_cia" : "";
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
}
else {
	$sql = "SELECT num_cia, nombre_corto, am, am_error, pm, pm_error, pastel, venta_pta, pastillaje, otros, ctes, corte1, corte2, desc_pastel FROM captura_efectivos";
	$sql .= " LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora)" : "";
	$sql .= " WHERE fecha = '$fecha1'";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " AND iduser = $_SESSION[iduser]" : "";
	$sql .= $num_cia > 0 ? " AND num_cia = $num_cia" : "";
	$sql .= " ORDER BY num_cia";
}

$result = $db->query($sql);

if (!$result) {
	header("location: ./pan_efm_con_v2.php?codigo_error=1");
	die;
}

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha1, $fecha);

$tpl->newBlock("listado");
$tpl->assign("periodo", $fecha2 != "" ? "del '$fecha1' al '$fecha2'" : "del dia $fecha[1] de " . mes_escrito($fecha[2]) . " de $fecha[3]");

if ($fecha2 == "") $tpl->newBlock("hbloque");

foreach ($result as $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre_corto']);
	$tpl->assign("am", $reg['am'] != 0 ? number_format($reg['am'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("am_error", $reg['am_error'] != 0 ? number_format($reg['am_error'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("pm", $reg['pm'] != 0 ? number_format($reg['pm'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("pm_error", $reg['pm_error'] != 0 ? number_format($reg['pm_error'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("pastel", $reg['pastel'] != 0 ? number_format($reg['pastel'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("venta_pta", $reg['venta_pta'] != 0 ? number_format($reg['venta_pta'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("pastillaje", $reg['pastillaje'] != 0 ? number_format($reg['pastillaje'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("otros", $reg['otros'] != 0 ? number_format($reg['otros'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("desc_pastel", $reg['desc_pastel'] != 0 ? number_format($reg['desc_pastel'], 2, ".", ",") : "&nbsp;");
	if ($fecha2 == "") {
		$tpl->newBlock("bloque");
		$tpl->assign("ctes", $reg['ctes'] != 0 ? number_format($reg['ctes'], 0, "", ",") : "&nbsp;");
		$tpl->assign("corte1", $reg['corte1'] != 0 ? $reg['corte1'] : "&nbsp;");
		$tpl->assign("corte2", $reg['corte2'] != 0 ? $reg['corte2'] : "&nbsp;");
	}
}

$tpl->printToScreen();
?>