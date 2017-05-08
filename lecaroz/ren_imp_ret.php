<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

if (isset($_GET['local'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ren/constancia_ret.tpl" );
	$tpl->prepare();
	
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT cart.rfc AS rfc_art, curp, nombre_arrendatario, renta, isr_retenido, iva_retenido, cc.nombre, cc.rfc AS rfc_cia FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios";
	$sql .= " AS cart ON (cart.id = local) LEFT JOIN catalogo_arrendadores AS carr USING (cod_arrendador) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE rr.status = 1 AND";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND iva_retenido > 0 AND cart.tipo_persona = 'FALSE'";
	$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
	$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local] AND cart.status = 1" : "";
	$sql .= " ORDER BY local";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	foreach ($result as $reg) {
		$tpl->newBlock("hoja");
		$tpl->assign("ini", $_GET['mes']);
		$tpl->assign("fin", $_GET['mes']);
		$tpl->assign("anio", $_GET['anio']);
		
		$tpl->assign("rfc_art", $reg['rfc_art']);
		$tpl->assign("curp", $reg['curp']);
		$tpl->assign("nombre_art", $reg['nombre_arrendatario']);
		$tpl->assign("renta", number_format($reg['renta'], 2, ".", ","));
		$tpl->assign("isr", number_format($reg['isr_retenido'], 2, ".", ","));
		$tpl->assign("iva", number_format($reg['iva_retenido'], 2, ".", ","));
		$tpl->assign("rfc_cia", $reg['rfc_cia']);
		$tpl->assign("nombre_cia", $reg['nombre']);
	}
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_imp_ret.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>