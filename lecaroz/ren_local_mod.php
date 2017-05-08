<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Error";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ren/ren_local_mod.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


if(isset($_POST['cod_local'])){
	ejecutar_script("UPDATE catalogo_locales SET nombre='".$_POST['nombre']."', num_cia=".$_POST['num_cia'].", cta_predial='".$_POST['cta_predial']."', cod_arrendador=".$_POST['cod_arrendador'].",metros=".$_POST['metros'].",metros_cuadrados=".$_POST['metros_cuadrados'].", bloque=".$_POST['bloque']." where num_local= ".$_POST['cod_local'],$dsn);
	
//	print_r($_POST);
	
	header("location: ./ren_local_mod.php");
	die();
}

if(!isset($_GET['local'])){
	$tpl->newBlock("obtener_local");
	$local=ejecutar_script("select * from catalogo_locales order by num_local",$dsn);
	
	for($i=0;$i<count($local);$i++){
		$tpl->newBlock("nombre_local");
		$tpl->assign("cod_local",$local[$i]['num_local']);
		$tpl->assign("nombre_local",$local[$i]['nombre']);
	}
	$tpl->printToScreen();
	die();
}



$tpl->newBlock("modificar_datos");

$local=ejecutar_script("select * from catalogo_locales where num_local=".$_GET['local'],$dsn);
$cia=ejecutar_script("select * from catalogo_companias where num_cia=".$local[0]['num_cia'],$dsn);
$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$local[0]['cod_arrendador'],$dsn);

$tpl->assign("cod",$_GET['local']);
$tpl->assign("id",$local[0]['id']);
$tpl->assign("nombre",$_GET['nombre_local']);
$tpl->assign("predial",$local[0]['cta_predial']);
$tpl->assign("metros",$local[0]['metros']);
$tpl->assign("m2",$local[0]['metros_cuadrados']);
$tpl->assign("num_cia",$local[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("arrendador",$local[0]['cod_arrendador']);
$tpl->assign("nombre_arrendador",$arrendador[0]['nombre']);

if($local[0]['bloque']==1)
	$tpl->assign("selected1","selected");
else
	$tpl->assign("selected2","selected");



$cias=ejecutar_script("select num_cia, nombre_corto from catalogo_companias where num_cia < 999 order by num_cia",$dsn);
for($i=0;$i<count($cias);$i++){
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
}

$arrendador=ejecutar_script("select cod_arrendador, nombre from catalogo_arrendadores order by cod_arrendador",$dsn);
if($arrendador){
	for($i=0;$i<count($arrendador);$i++){
		$tpl->newBlock("nombre_arrendador");
		$tpl->assign("cod_arrendador",$arrendador[$i]['cod_arrendador']);
		$tpl->assign("nombre_arrendador",$arrendador[$i]['nombre']);
	}
}
// Si viene de una página que genero error


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
die();

?>