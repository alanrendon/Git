<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/cheques.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existen registros para modificar";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ren/contrato_renta2.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ren/contrato_renta2.tpl");
$tpl->prepare();
//Seleccionar el script para menu
/*
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
*/
// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
//print_r($_POST);

$direccion_oficina="DIAGONAL PATRIOTISMO NO. 1-501, COLONIA HIPODROMO CONDESA, CODIGO POSTAL 06170, MÉXICO, DISTRITO FEDERAL";

$f1=explode("/",$_POST['fecha_inicial']);
$f2=explode("/",$_POST['fecha_final']);
$fecha_inicial=$f1[0]." DE ".strtoupper(mes_escrito($f1[1]))." DEL ".$f1[2];
$fecha_final=$f2[0]." DE ".strtoupper(mes_escrito($f2[1]))." DEL ".$f2[2];

$local=ejecutar_script("select * from catalogo_locales where num_local={$_POST['num_local']}",$dsn);
$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador={$local[0]['cod_arrendador']}",$dsn);
//$notario=ejecutar_script("select * from catalogo_notario where cod_notario={$arrendador[0]['num_notario']}",$dsn);

$renta=number_format($_POST['con_recibo'],2,'.','')+number_format($_POST['agua'],2,'.','')+number_format($_POST['mantenimiento'],2,'.','');

$tpl->assign("arrendatario",strtoupper($_POST['nombre_arrendatario']));
$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
$tpl->assign("representante_arrendador",strtoupper($arrendador[0]['representante']));
$tpl->assign("representante_arrendatario",strtoupper($_POST['representante']));
//$tpl->assign("notario",strtoupper($notario[0]['nombre']));
//$tpl->assign("num_notario",$notario[0]['num_notario']);
$tpl->assign("giro",strtoupper($_POST['giro']));
//$tpl->assign("escritura",$arrendador[0]['num_acta']);
$tpl->assign("direccion_oficina",$direccion_oficina);
$tpl->assign("direccion_arrendador",strtoupper($_POST['direccion']));

if($_POST['nombre_aval']!=""){
	$tpl->assign("aval",strtoupper($_POST['nombre_aval']));
	$tpl->assign("direccion_aval",strtoupper($_POST['bien_avaluo']));
}
else{
	$tpl->assign("aval","NO SE REQUIRIO AVAL");
	$tpl->assign("direccion_aval","NO SE REQUIRIO AVAL");
}

$tpl->assign("fecha_inicial",$fecha_inicial);
$tpl->assign("fecha_final",$fecha_final);
$tpl->assign("cantidad_numero",number_format($renta,2,'.',','));
$tpl->assign("cantidad_numero1",number_format($_POST['daños'],2,'.',','));
$tpl->assign("cantidad_numero2",number_format($_POST['termino'],2,'.',','));

$tpl->assign("cantidad_letra",num2string($renta));
if($_POST['daños']!="")
	$tpl->assign("cantidad_letra1",num2string($_POST['daños']));
if($_POST['termino']!="")
	$tpl->assign("cantidad_letra2",num2string($_POST['termino']));


if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();

}
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
	$tpl->printToScreen();
	die();

}
$tpl->printToScreen();
?>