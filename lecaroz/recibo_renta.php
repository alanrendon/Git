<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ren/recibo_renta.tpl" );
$tpl->prepare();

$sql = "SELECT nombre_arrendatario, rfc, cta_predial, rr.renta, rr.mantenimiento, rr.agua, rr.iva, isr_retenido, iva_retenido, neto, direccion_fiscal, direccion_local, fecha, fecha_pago, concepto, tipo_local";
$sql .= " FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS ca ON (ca.id = rr.local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE homoclave = $_GET[arr]";
$sql .= " AND num_recibo BETWEEN $_GET[ini] AND $_GET[fin] AND (rr.renta > 0 OR rr.mantenimiento > 0) ORDER BY num_recibo ASC";
$result = $db->query($sql);

foreach ($result as $reg) {
	$tpl->newBlock("recibo");
	$tpl->assign('predial', $reg['cta_predial']);
	$tpl->assign("arrendatario", $reg['nombre_arrendatario']);
	$tpl->assign("rfc", $reg['rfc']);
	$tpl->assign("local", $reg['tipo_local'] == 1 ? "LOCAL COMERCIAL" : 'VIVIENDA');
	$tpl->assign("direccion_fiscal", trim($reg['direccion_fiscal']));
	$tpl->assign("direccion_local", trim($reg['direccion_local']));
	$tpl->assign("renta", $reg['renta'] != 0 ? number_format($reg['renta'], 2, ".", ",") : NULL);
	$tpl->assign("mant", $reg['mantenimiento'] != 0 ? number_format($reg['mantenimiento'], 2, ".", ",") : NULL);
	$tpl->assign("subtotal", number_format(round($reg['renta'], 2) + round($reg['mantenimiento'], 2), 2, ".", ","));
	$tpl->assign("iva", $reg['iva'] != 0 ? number_format($reg['iva'], 2, ".", ",") : NULL);
	$tpl->assign("agua", $reg['agua'] != 0 ? number_format($reg['agua'], 2, ".", ",") : NULL);
	$tpl->assign("isr", $reg['isr_retenido'] != 0 ? number_format($reg['isr_retenido'], 2, ".", ",") : NULL);
	$tpl->assign("ret", $reg['iva_retenido'] != 0 ? number_format($reg['iva_retenido'], 2, ".", ",") : NULL);
	$tpl->assign("total", number_format($reg['neto'], 2, ".", ","));
	$tpl->assign("importe_escrito", num2string($reg['neto']));
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha'], $fecha);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha_pago'], $fecha_pago);
	
	$tpl->assign("mes", trim($reg['concepto']) != "" ? trim($reg['concepto']) : mes_escrito($fecha[2], TRUE));
	$tpl->assign("dia_mes", "$fecha_pago[1] DE " . mes_escrito($fecha_pago[2], TRUE));
	$tpl->assign("anio", $fecha_pago[3]);
}

$tpl->printToScreen();
?>