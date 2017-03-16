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
$tpl->assignInclude("body", "./plantillas/ren/ren_art_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
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
	$art['clausula'] = strtoupper(trim($art['clausula']));
	$art['parrafo'] = strtoupper(trim($art['parrafo']));
	$art['metros'] = $art['metros'] != "" ? floatval($art['metros']) : "0";
	$art['metros_cuadrados'] = $art['metros_cuadrados'] != "" ? floatval($art['metros_cuadrados']) : "0";
	$art['renta_con_recibo'] = $art['renta_con_recibo'] != "" ? floatval(str_replace(",", "", $art['renta_con_recibo'])) : "0";
	$art['renta_sin_recibo'] = $art['renta_sin_recibo'] != "" ? floatval(str_replace(",", "", $art['renta_sin_recibo'])) : "0";
	$art['mantenimiento'] = $art['mantenimiento'] != "" ? floatval(str_replace(",", "", $art['mantenimiento'])) : "0";
	$art['agua'] = $art['agua'] != "" ? floatval(str_replace(",", "", $art['agua'])) : "0";
	$art['cargo_daños'] = $art['cargo_daños'] != "" ? floatval(str_replace(",", "", $art['cargo_daños'])) : "0";
	$art['cargo_termino'] = $art['cargo_termino'] != "" ? floatval(str_replace(",", "", $art['cargo_termino'])) : "0";
	$art['rentas_en_deposito'] = $art['rentas_en_deposito'] != "" ? floatval(str_replace(",", "", $art['rentas_en_deposito'])) : "0";
	$art['status'] = 1;
	$art['contacto'] = strtoupper($art['contacto']);
	$art['por_incremento'] = get_val($art['por_incremento']);
	
	$sql = "UPDATE catalogo_arrendatarios SET rfc = '$art[rfc]', email = '$art[email]', fecha_inicio = '$art[fecha_inicio]', fecha_final = '$art[fecha_final]', incremento_anual = '$art[incremento_anual]',";
	$sql .= "renta_con_recibo = $art[renta_con_recibo], renta_sin_recibo = $art[renta_sin_recibo], agua = $art[agua], mantenimiento = $art[mantenimiento], rentas_en_deposito = ";
	$sql .= "$art[rentas_en_deposito], retencion_isr = $art[retencion_isr], retencion_iva = $art[retencion_iva], fianza = '$art[fianza]', tipo_persona = '$art[tipo_persona]',";
	$sql .= " nombre_arrendatario = '$art[nombre_arrendatario]', representante = '$art[representante]', nombre_aval = '$art[nombre_aval]', cargo_daños = $art[cargo_daños],";
	$sql .= " cargo_termino = $art[cargo_termino], bien_avaluo = '$art[bien_avaluo]', direccion_fiscal = '$art[direccion_fiscal]', recibo_mensual = '$art[recibo_mensual]',";
	$sql .= "giro = '$art[giro]', direccion_local = '$art[direccion_local]', cta_predial = '$art[cuenta_predial]', metros = $art[metros], metros_cuadrados = $art[metros_cuadrados],";
	$sql .= " bloque = $art[bloque], cod_arrendador = $art[cod_arrendador], nombre_local = '$art[nombre_local]', contacto = '$art[contacto]', telefono = '$art[telefono]', num_cia = $art[num_cia], tipo_local = $art[tipo_local], por_incremento = $art[por_incremento], clausula = '$art[clausula]', parrafo = '$art[parrafo]' WHERE id = $art[id]";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$result = $db->query("SELECT * FROM catalogo_arrendatarios WHERE id = $_GET[id]");
foreach ($result as $value)
	$reg = $value;

$tpl->newBlock("datos");
$tpl->assign("id", $reg['id']);
$tpl->assign("num_local", $reg['num_local']);
$tpl->assign("nombre_local", $reg['nombre_local']);
$tpl->assign("direccion_local", $reg['direccion_local']);
$tpl->assign("email", $reg['email']);
$tpl->assign("fecha1", $reg['fecha_inicio']);
$tpl->assign("fecha2", $reg['fecha_final']);
$tpl->assign("metros", $reg['metros']);
$tpl->assign("metros_cuadrados", $reg['metros_cuadrados']);
$tpl->assign("cuenta_predial", $reg['cta_predial']);
$tpl->assign("nombre_arrendatario", $reg['nombre_arrendatario']);
$tpl->assign("rfc", $reg['rfc']);
$tpl->assign("direccion_fiscal", $reg['direccion_fiscal']);
$tpl->assign("persona_$reg[tipo_persona]", " checked");
$tpl->assign("giro", $reg['giro']);
$tpl->assign("bloque_$reg[bloque]", " checked");
$tpl->assign("representante", $reg['representante']);
$tpl->assign("nombre_aval", $reg['nombre_aval']);
$tpl->assign("bien_avaluo", $reg['bien_avaluo']);
$tpl->assign('clausula', $reg['clausula']);
$tpl->assign('parrafo', $reg['parrafo']);

$tpl->assign("renta_con_recibo", $reg['renta_con_recibo'] != 0 ? number_format($reg['renta_con_recibo'], 2, ".", ",") : "");
$tpl->assign("renta_sin_recibo", $reg['renta_sin_recibo'] != 0 ? number_format($reg['renta_sin_recibo'], 2, ".", ",") : "");
$tpl->assign("mantenimiento", $reg['mantenimiento'] != 0 ? number_format($reg['mantenimiento'], 2, ".", ",") : "");
$tpl->assign("agua", $reg['agua'] != 0 ? number_format($reg['agua'], 2, ".", ",") : "");
$tpl->assign("ret_iva_$reg[retencion_iva]", " checked");
$tpl->assign("ret_isr_$reg[retencion_isr]", " checked");
$tpl->assign("fianza_$reg[fianza]", " checked");
$tpl->assign("inc_$reg[incremento_anual]", " checked");
$tpl->assign("cargo_daños", $reg['cargo_daños'] != 0 ? number_format($reg['cargo_daños'], 2, ".", ",") : "");
$tpl->assign("cargo_termino", $reg['cargo_termino'] != 0 ? number_format($reg['cargo_termino'], 2, ".", ",") : "");
$tpl->assign("mensual_$reg[recibo_mensual]", " checked");
$tpl->assign("rentas_en_deposito", $reg['rentas_en_deposito'] != 0 ? number_format($reg['rentas_en_deposito'], 2, ".", ",") : "");
$tpl->assign("contacto", $reg['contacto']);
$tpl->assign("telefono", $reg['telefono']);
$tpl->assign("num_cia", $reg['num_cia']);
$tpl->assign('tipo_local_' . $reg['tipo_local'], ' checked');
$tpl->assign('por_incremento', $reg['por_incremento'] > 0 ? number_format($reg['por_incremento'], 2, '.', ',') : '');

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
	if ($arr['cod_arrendador'] == $reg['cod_arrendador']) $tpl->assign("selected", " selected");
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>