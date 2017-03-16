<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

if (isset($_GET['num_cia'])) {
	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	$sql = "SELECT f.num_cia, cc.nombre_corto, f.num_fact, f.num_proveedor, cp.nombre, upper(cp.rfc) AS rfc, f.imp_sin_iva, f.importe_total, f.importe_iva, f.porciento_iva,";
	$sql .= " (f.imp_sin_iva * f.porciento_ret_iva) / 100 AS iva_retenido, (f.imp_sin_iva * f.porciento_ret_isr) / 100 AS isr_retenido, f.fecha_mov, f.concepto";
	$sql .= " FROM facturas AS f LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = f.num_cia) WHERE (f.num_cia,";
	$sql .= " f.num_proveedor, f.num_fact) IN (SELECT num_cia, num_proveedor, num_fact FROM pasivo_proveedores";
	if (count($cias) > 0 || $_GET['num_pro'] > 0 || strlen($_GET['fecha']) >= 8) {
		$sql .= " WHERE";
		if (count($cias) > 0) {
			$sql .= " num_cia IN (";
			foreach ($cias as $i => $cia)
				$sql .= $cia . ($i < count($cias) - 1 ? ', ' : ')');
		}
		$sql .= $_GET['num_pro'] > 0 ? (count($cias) > 0 ? ' AND' : '') . " num_proveedor = $_GET[num_pro]" : '';
		$sql .= strlen($_GET['fecha']) >= 8 ? (count($cias) > 0 || strlen($_GET['num_pro'] > 0) ? ' AND' : '') . " fecha_mov <= '$_GET[fecha]'" : '';
	}
	$sql .= ") ORDER BY f.num_cia, f.num_proveedor, f.fecha_mov";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./fac_fac_pen.php?codigo_error=1');
		die;
	}
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=facturas_pendientes.csv");
	
	echo "Número de compañía,";
	echo "Nombre cia,";
	echo "Número de factura,";
	echo "Número de proveedor,";
	echo "Nombre proveedor,";
	echo "R.F.C. proveedor,";
	echo "Importe factura,";
	echo "Importe I.V.A.,";
	echo "Importe I.V.A. retenido,";
	echo "Importe I.S.R. retenido,";
	echo "Fecha del movimiento\n";
	$espacio=" ";
	foreach ($result as $reg) {
		echo "\"$reg[num_cia]\",";
		echo "\"$reg[nombre_corto]\",";
		echo "\"$reg[num_fact]\",";
		echo "\"$reg[num_proveedor]\",";
		echo "\"$reg[nombre]\",";
		echo "\"$reg[rfc]\",";
		echo "\"" . (stristr($reg['concepto'], "ESPECIAL") !== FALSE ? number_format($reg['importe_total'] * 1.15, 2, ".", ",") : number_format($reg['importe_total'], 2, '.', ',')) . "\",";
		if ($reg['importe_iva'] != "") echo "\"" . (stristr($reg['concepto'], "ESPECIAL") !== FALSE ? number_format($reg['importe_total'] * 0.15, 2, '.', ',') : (stristr($reg['concepto'], "FACTURA MATERIA PRIMA") !== FALSE ? number_format($reg['importe_iva'] / 1.15, 2, '.', ',') : number_format($reg['importe_iva'], 2, '.', ','))) . "\",";
		else /*echo "\"" . number_format($reg['importe_iva'], 2, '.', ',') . "\","*/echo "\"$espacio\",";
		if ($reg['iva_retenido'] > 0) echo "\"" . number_format($reg['iva_retenido'], 2, '.', ',') . "\",";
		else echo "\"$espacio\",";
		if ($reg['isr_retenido'] > 0) echo "\"" . number_format($reg['isr_retenido'], 2, '.', ',') . "\",";
		else echo "\"$espacio\",";
		echo "\"$reg[fecha_mov]\"\n";
	}
	die;
}

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/fac/fac_fac_pen.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign('fecha', date('d/m/Y'));

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>