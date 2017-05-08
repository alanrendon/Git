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
$tpl->assignInclude("body", "./plantillas/ren/ren_art_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_local'])) {
	//$_SESSION['art'] = $_POST;
	$art = $_POST;
	
	$art['nombre_local'] = strtoupper($art['nombre_local']);
	$art['direccion_local'] = strtoupper($art['direccion_local']);
	$art['nombre_arrendatario'] = strtoupper($art['nombre_arrendatario']);
	$art['rfc'] = strtoupper($art['rfc']);
	$art['direccion_fiscal'] = strtoupper($art['direccion_fiscal']);
	$art['email'] = strtolower($art['email']);
	$art['giro'] = strtoupper($art['giro']);
	$art['representante'] = strtoupper($art['representante']);
	$art['nombre_aval'] = strtoupper($art['nombre_aval']);
	$art['bien_avaluo'] = strtoupper($art['bien_avaluo']);
	$art['clausula'] = strtoupper(trim('clausula'));
	$art['parrafo'] = strtoupper(trim('parrafo'));
	$art['renta_con_recibo'] = str_replace(",", "", $art['renta_con_recibo']);
	$art['renta_sin_recibo'] = str_replace(",", "", $art['renta_sin_recibo']);
	$art['mantenimiento'] = str_replace(",", "", $art['mantenimiento']);
	$art['agua'] = str_replace(",", "", $art['agua']);
	$art['cargo_daños'] = str_replace(",", "", $art['cargo_daños']);
	$art['cargo_termino'] = str_replace(",", "", $art['cargo_termino']);
	$art['rentas_en_deposito'] = str_replace(",", "", $art['rentas_en_deposito']);
	$art['status'] = 1;
	$art['contacto'] = strtoupper($art['contacto']);
	$art['por_incremento'] = get_val($art['por_incremento']);
	
	$db->query($db->preparar_insert("catalogo_arrendatarios", $art));
	header("location: ./ren_art_alta.php");
	die;
}

$locales = $db->query("SELECT num_local FROM catalogo_arrendatarios WHERE status = 1 ORDER BY num_local");
function numLocal() {
	global $locales;
	
	if (!$locales)
		return 1;
	
	$num = 1;
	foreach ($locales as $local)
		if ($local['num_local'] == $num)
			$num++;
		else
			return $num;
	
	return $num;
}

$tpl->assign("num_local", numLocal());

$cias = $db->query("SELECT num_cia AS num, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock('c');
	$tpl->assign('num', $cia['num']);
	$tpl->assign('nombre', $cia['nombre']);
}

$arrs = $db->query("SELECT cod_arrendador, nombre FROM catalogo_arrendadores ORDER BY cod_arrendador");
foreach ($arrs as $arr) {
	$tpl->newBlock("arr");
	$tpl->assign("cod", $arr['cod_arrendador']);
	$tpl->assign("nombre", $arr['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>