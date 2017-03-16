<?php
// CAPTURA DE PORCENTAJES DE COMPAÑIAS
// Tabla ''
// Menu ''
//define ('IDSCREEN',1721); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";


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
$tpl->assignInclude("body","./plantillas/fac/fac_porc_fac.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['compania'])) 
{
	$tpl->newBlock("obtener_dato");
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
}


$tpl->newBlock("porcentajes");
$tpl->assign("num_cia",$_GET['compania']);
$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['compania']),"","",$dsn);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);

$sql="select * from catalogo_porcentajes_facturas where num_cia=".$_GET['compania'];
$porcentajes=ejecutar_script($sql,$dsn);
$porc=count($porcentajes);
for($i=0;$i<4;$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	if(($i+1) < 4)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
}
$tpl->printToScreen();
?>