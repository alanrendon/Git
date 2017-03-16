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
$tpl->assignInclude("body","./plantillas/ren/ren_local_alta.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['cod_local'])) {

	$tpl->newBlock("obtener_datos");
	$tpl->assign("id",nextID2("catalogo_locales","num_local",$dsn));

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
	$locales=ejecutar_script("select num_local from catalogo_locales order by num_local",$dsn);
	for($i=0;$i<count($locales);$i++){
		$tpl->newBlock("ocupados");
		$tpl->assign("i",$i);
		$tpl->assign("cod_local",$locales[$i]['num_local']);
	}
	
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die();

$tpl->printToScreen();
}
//---------------------------------------------------------

if(existe_registro("catalogo_locales",array("num_local"),array($_GET['cod_local']),$dsn))
	ejecutar_script("INSERT INTO catalogo_locales(num_local,nombre,num_cia,metros,metros_cuadrados,cta_predial,cod_arrendador,ocupado,bloque,locales) VALUES(".nextID2("catalogo_locales","num_local",$dsn).", '".$_GET['nombre']."', ".$_GET['num_cia'].", ".$_GET['metros'].", ".$_GET['metros_cuadrados'].", '".$_GET['cta_predial']."', ".$_GET['cod_arrendador'].",'false', ".$_GET['bloque'].", ".$_GET['locales'].")",$dsn);

else
	ejecutar_script("INSERT INTO catalogo_locales(num_local,nombre,num_cia,metros,metros_cuadrados,cta_predial,cod_arrendador,ocupado,bloque,locales) VALUES(".$_GET['cod_local'].", '".$_GET['nombre']."', ".$_GET['num_cia'].", ".$_GET['metros'].", ".$_GET['metros_cuadrados'].", '".$_GET['cta_predial']."', ".$_GET['cod_arrendador'].",'false', ".$_GET['bloque'].", ".$_GET['locales'].")",$dsn);
	
header("location: ./ren_local_alta.php");
die();
?>