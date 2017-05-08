<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

if (isset($_GET['folio'])) {
	$sql = "SELECT fp.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, total, num_cia, cc.nombre_corto AS nombre_cia, folio_cheque, fecha_cheque FROM facturas_pagadas fp LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = fp.num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE (fp.num_cia, fp.folio_cheque, fp.cuenta) IN (SELECT num_cia, folio, cuenta FROM transferencias_electronicas WHERE folio_archivo = $_GET[folio] AND status IN (0, 1))";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./ban_por_arc.php?codigo_error=1'));
	
	function filler($str, $length, $chr, $side = TRUE) {
		$tmp = '';
		
		if (strlen($str) >= $length) return $str;
		
		for ($i = 0; $i < $length - strlen($str); $i++)
			$tmp .= $chr;
		
		return $side ? $str . $tmp : $tmp . $str;
	}
	
	$data = '';
	foreach ($result as $reg) {
		$data .= filler($reg['num_pro'], 5, '0', FALSE);
		$data .= filler($reg['num_fact'], 20, '0', FALSE);
		$data .= filler(number_format($reg['total'], 2, '', ''), 20, '0', FALSE);
		$data .= filler(substr($reg['nombre_cia'], 0, 60), 60, ' ');
		$data .= filler($reg['folio_cheque'], 10, '0', FALSE);
		$data .= str_replace('/', '', $reg['fecha_cheque']);
		$data .= filler('', 130, ' ');
		$data .= "\r\n";
	}
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=pagos.txt");
	
	echo $data;
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_por_arc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();
?>