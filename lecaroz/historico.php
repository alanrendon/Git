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
$tpl->assignInclude("body","./plantillas/bal/historico.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
///$tpl->assign("tabla",$session->tabla);


// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) 
	{

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


	$tpl->assign("tabla","historico");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("anio_actual",$_GET['anio']);
	$tpl->assign("anio_anterior",$_GET['anio']-1);
	
	$cia_r = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$cia_r[0]['nombre_corto']);
	
	//print_r ($_GET);
	$total=0;
	for ($i=0; $i<12; $i++) {
		$tpl->newBlock("meses");
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("i",$i);
		$tpl->assign("m",$i+1);
		$anio_an=$_GET['anio']-1;
		$mes=$i+1;
		$tpl->assign("fecha_anio_anterior","1/".$mes."/".$anio_an);
		$tpl->assign("fecha_anio_actual","1/".$mes."/".$_GET['anio']);
	
		switch ($i) {
			   case 0:
				   $tpl->assign("nombre_mes","Enero");
				   break;
			   case 1:
				   $tpl->assign("nombre_mes","Febrero");
				   break;
			   case 2:
				   $tpl->assign("nombre_mes","Marzo");
				   break;
			   case 3:
				   $tpl->assign("nombre_mes","Abril");
				   break;
			   case 4:
				   $tpl->assign("nombre_mes","Mayo");
				   break;
			   case 5:
				   $tpl->assign("nombre_mes","Junio");
				   break;
			   case 6:
				   $tpl->assign("nombre_mes","Julio");
				   break;
			   case 7:
				   $tpl->assign("nombre_mes","Agosto");
				   break;
			   case 8:
				   $tpl->assign("nombre_mes","Septiembre");
				   break;
			   case 9:
				   $tpl->assign("nombre_mes","Octubre");
				   break;
			   case 10:
				   $tpl->assign("nombre_mes","Noviembre");
				   break;
			   case 11:
				   $tpl->assign("nombre_mes","Diciembre");
				   break;
			}
	}

	

// Imprimir el resultado
$tpl->printToScreen();
?>