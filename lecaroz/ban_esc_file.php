<?php
//define ('IDSCREEN',1241); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener informaci�n de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_esc_file.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compa��a -------------------------------------------------


	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha",date("d/m/Y"));


//echo date( "d/m/Y", mktime(0,0,0,12,30,1997) );
	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("num_mes",$i);
		$tpl->assign("nom_mes",mes_escrito($i,1));
		if($i==date("n"))
			$tpl->assign("selected","selected");
	}

	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes_con");
		$tpl->assign("num_mes",$i);
		$tpl->assign("nom_mes",mes_escrito($i,1));
		if($i==date("n"))
			$tpl->assign("selected","selected");
	}

// Si viene de una p�gina que genero error
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

?>
