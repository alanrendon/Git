<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Ya se han capturado las reservas";

if (isset($_POST['num_cia'])) {
	if ($db->query("SELECT id FROM reservas_cias WHERE num_cia = $_POST[num_cia] AND anio = $_POST[anio] AND cod_reserva = $_POST[cod_reserva] LIMIT 1")) {
		header("location: ./bal_cap_res_v2.php?codigo_error=1");
		die;
	}
	
	$num_cia = $_POST['num_cia'];
	$anio = $_POST['anio'];
	$cod_reserva = $_POST['cod_reserva'];
	
	$sql = "";
	foreach ($_POST['importe'] as $i => $imp) {
		$importe = floatval(str_replace(",", "", $imp));
		$mes = $i + 1;
		$fecha = "01/$mes/$anio";
		$sql .= "INSERT INTO reservas_cias (num_cia, importe, fecha, cod_reserva, anio) VALUES ($num_cia, $importe, '$fecha', $cod_reserva, $anio);\n";
	}
	$db->query($sql);
	
	header("location: ./bal_cap_res_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_cap_res_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("anio", date("Y"));

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$cods = $db->query("SELECT tipo_res, descripcion FROM catalogo_reservas ORDER BY tipo_res");
foreach ($cods as $cod) {
	$tpl->newBlock("cod");
	$tpl->assign("cod", $cod['tipo_res']);
	$tpl->assign("nombre", $cod['descripcion']);
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>