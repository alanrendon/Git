<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para esta compañía";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

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
$tpl->assignInclude("body","./plantillas/ban/ban_pas_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($j=0;$j<12;$j++)
	{
		$tpl->newBlock("mes");
		switch ($j) {
		   case 0:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Enero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 1:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Febrero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 2:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Marzo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
  		   case 3:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Abril");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 4:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Mayo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 5:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Junio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 6:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Julio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 7:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Agosto");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 8:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Septiembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 9:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Octubre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 10:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Noviembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 11:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Diciembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;

		}		
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
}

$fecha="1/".$_GET['mes']."/".$_GET['anio'];


$tpl->printToScreen();

?>