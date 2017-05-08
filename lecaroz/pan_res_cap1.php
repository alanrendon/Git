<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA

define ('IDSCREEN',1321); // ID de pantalla

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
$tpl->assignInclude("body","./plantillas/pan/pan_res_cap1.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla

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

$tpl->assign("num_cia",$_POST['num_cia']);
$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$tpl->assign("nom_cia",$nomcia[0]['nombre_corto']);
$tpl->assign("fecha",$_POST['fecha']);
$tpl->assign("cont",$_POST['cont']);
$total=0;

for($i=0;$i<$_POST['cont'];$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$exp = obtener_registro("catalogo_expendios",array("num_expendio","num_cia"),array($_POST['num_exp'.$i],$_POST['num_cia']),"","",$dsn);
	$tpl->assign("nombre_exp",$exp[0]['nombre']);
	$tpl->assign("num_exp",$exp[0]['num_expendio']);
	$tpl->assign("importe",$_POST['importe'.$i]);
	$tpl->assign("importe1",number_format($_POST['importe'.$i],2,'.',','));
	$total+=$_POST['importe'.$i];
}

$tpl->gotoBlock("_ROOT");
$tpl->assign("total",number_format($total,2,'.',','));

// Imprimir el resultado
$tpl->printToScreen();
?>