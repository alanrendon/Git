<?php
//define ('IDSCREEN',6213); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Lo siento pero ya exite esta reserva para esta compañía";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_his_pan.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",/*date("Y")*/2004);

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) {
		$tpl->newBlock("nom_cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
	
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
//------------------------------------------------***Reservas***------------------------------------------------------------

$tpl->newBlock("historico");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("anio",$_GET['anio']);
$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre']);

$meses = 12;

for ($i=0; $i<$meses; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",($i > 0)?$i-1:$meses-1);
	$tpl->assign("next",($i < $meses-1)?$i+1:0);
	
	$tpl->assign("nombre_mes",mes_escrito($i+1,TRUE));
}

// Imprimir el resultado
$tpl->printToScreen();
?>