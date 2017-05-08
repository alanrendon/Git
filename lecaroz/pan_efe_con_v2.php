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
$tpl->assignInclude("body", "./plantillas/pan/pan_efe_con_v2.tpl");
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
	$sql = "SELECT num_cia, nombre_corto, sum(venta_puerta) AS venta_puerta, sum(abono) AS abono, sum(pastillaje) AS pastillaje, sum(otros) AS otros, sum(raya_pagada) AS raya_pagada,";
	$sql .= " sum(gastos) AS gastos, sum(efectivo) AS efectivo FROM total_panaderias LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora)" : "";
	$sql .= " WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " AND iduser = $_SESSION[iduser]" : "";
	$sql .= $num_cia > 0 ? " AND num_cia = $num_cia" : "";
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
}
else {
	$sql = "SELECT num_cia, nombre_corto, venta_puerta, abono, pastillaje, otros, raya_pagada, gastos, efectivo FROM total_panaderias LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " LEFT JOIN catalogo_operadoras USING (idoperadora)" : "";
	$sql .= " WHERE fecha = '$fecha1'";
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 62)) ? " AND iduser = $_SESSION[iduser]" : "";
	$sql .= $num_cia > 0 ? " AND num_cia = $num_cia" : "";
	$sql .= " ORDER BY num_cia";
}

$result = $db->query($sql);

if (!$result) {
	header("location: ./pan_efe_con_v2.php?codigo_error=1");
	die;
}

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha1, $fecha);

$tpl->newBlock("listado");
$tpl->assign("periodo", $fecha2 != "" ? "del '$fecha1' al '$fecha2'" : "del dia $fecha[1] de " . mes_escrito($fecha[2]) . " de $fecha[3]");

foreach ($result as $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre_corto']);
	$tpl->assign("venta_puerta", $reg['venta_puerta'] != 0 ? number_format($reg['venta_puerta'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("abono", $reg['abono'] != 0 ? number_format($reg['abono'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("pastillaje", $reg['pastillaje'] != 0 ? number_format($reg['pastillaje'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("otros", $reg['otros'] != 0 ? number_format($reg['otros'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("raya_pagada", $reg['raya_pagada'] != 0 ? number_format($reg['raya_pagada'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("gastos", $reg['gastos'] != 0 ? number_format($reg['gastos'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("efectivo", $reg['efectivo'] != 0 ? number_format($reg['efectivo'], 2, ".", ",") : "&nbsp;");
}

$tpl->printToScreen();
?>