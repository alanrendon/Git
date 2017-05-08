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
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
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
$tpl->assignInclude("body","./plantillas/ren/ren_arrendador_alta.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['cod_arrendador'])) {

	$tpl->newBlock("obtener_datos");
	$tpl->assign("id",nextID2("catalogo_arrendadores","cod_arrendador",$dsn));
	
	$notario=ejecutar_script("select * from catalogo_notario order by cod_notario",$dsn);
	for($i=0;$i<count($notario);$i++){
		$tpl->newBlock("notario");
		$tpl->assign("cod_notario",$notario[$i]['cod_notario']);
		$tpl->assign("nombre_notario",$notario[$i]['nombre']);
	}
	$arrendadores=ejecutar_script("select cod_arrendador from catalogo_arrendadores order by cod_arrendador",$dsn);
	for($i=0;$i<count($arrendadores);$i++){
		$tpl->newBlock("ocupados");
		$tpl->assign("i",$i);
		$tpl->assign("cod_arrendador",$arrendadores[$i]['cod_arrendador']);
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


if($_GET['tipo_persona']==0)
	$tipo='f';
else
	$tipo='t';
ejecutar_script("INSERT INTO catalogo_arrendadores(cod_arrendador,nombre,representante,tipo_persona,num_acta,num_notario,ent_fed) VALUES(".$_GET['cod_arrendador'].", '".$_GET['nombre']."', '".$_GET['representante']."', '".$tipo."', '".$_GET['num_acta']."', ".$_GET['notario'].", '".$_GET['ent_fed']."')",$dsn);
header("location: ./ren_arrendador_alta.php");
die();
?>