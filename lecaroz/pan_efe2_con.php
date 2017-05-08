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

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No eres usuario operadora de compañía";
$descripcion_error[2] = "No tienes compañías asignadas, revise con su administrador";
$descripcion_error[3] = "No se encontraron movimientos ese dia";


// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_efe2_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

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
// MOSTRAR LISTADO POR FECHA ---------------------------------------------------------------------------------

$nombremes[1]="Enero";
$nombremes[2]="Febrero";
$nombremes[3]="Marzo";
$nombremes[4]="Abril";
$nombremes[5]="Mayo";
$nombremes[6]="Junio";
$nombremes[7]="Julio";
$nombremes[8]="Agosto";
$nombremes[9]="Septiembre";
$nombremes[10]="Octubre";
$nombremes[11]="Noviembre";
$nombremes[12]="Diciembre";


$_fecha=explode("/",$_GET['fecha']);

if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
	$sql="SELECT num_cia, nombre_corto from catalogo_companias WHERE num_cia < 101 or num_cia in (702,703) order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	$sql="select * from total_panaderias where fecha='".$_GET['fecha']."' and num_cia in( select num_cia from catalogo_companias where num_cia < 101 or num_cia in(702,703)) order by num_cia";
	$efectivos=ejecutar_script($sql,$dsn);
	if(!$efectivos){
		header("location: ./pan_efe2_con.php?codigo_error=3");
		die();
	}
	
}

else{
	$operadora=obtener_registro("catalogo_operadoras",array("iduser"),array($_SESSION['iduser']),"","",$dsn);
	
	if(!$operadora){
		header("location: ./pan_efe2_con.php?codigo_error=1");
		die();
	}
	$cias=obtener_registro("catalogo_companias",array("idoperadora"),array($operadora[0]['idoperadora']),"","",$dsn);
	if(!$cias){
		header("location: ./pan_efe2_con.php?codigo_error=2");
		die();
	}
	
	$sql="select * from total_panaderias where fecha='".$_GET['fecha']."' and num_cia in( select num_cia from catalogo_companias where idoperadora=".$operadora[0]['idoperadora'].") order by num_cia";
	$efectivos=ejecutar_script($sql,$dsn);
	if(!$efectivos){
		header("location: ./pan_efe2_con.php?codigo_error=3");
		die();
	}
	
	
}

$tpl->newBlock("consulta");
$tpl->assign("fecha",$_fecha[0]." DE ".strtoupper($nombremes[$_fecha[1]])." DEL ".$_fecha[2]);

if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
	$tpl->assign("operadora","ADMINISTRADOR SISTEMA");
}
else{
	$tpl->assign("operadora",$operadora[0]['nombre']);
}


for($i=0;$i<count($efectivos);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("num_cia",$efectivos[$i]['num_cia']);
	$ncia=obtener_registro("catalogo_companias",array("num_cia"),array($efectivos[$i]['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$ncia[0]['nombre_corto']);
	$tpl->assign("venta_puerta",number_format($efectivos[$i]['venta_puerta'],2,'.',','));
	
	if($efectivos[$i]['pastillaje']==0) $tpl->assign("pastillaje","");
	else
		$tpl->assign("pastillaje",number_format($efectivos[$i]['pastillaje'],2,'.',','));
	if($efectivos[$i]['otros']==0) $tpl->assign("otros","");
	else
		$tpl->assign("otros",number_format($efectivos[$i]['otros'],2,'.',','));
	
	if($efectivos[$i]['abono']==0) $tpl->assign("abono","");
	else
		$tpl->assign("abono",number_format($efectivos[$i]['abono'],2,'.',','));
		
	$tpl->assign("gastos",number_format($efectivos[$i]['gastos'],2,'.',','));
	
	if($efectivos[$i]['raya_pagada']==0) $tpl->assign("raya_pagada","");
	else
		$tpl->assign("raya_pagada",number_format($efectivos[$i]['raya_pagada'],2,'.',','));
		
	$tpl->assign("efectivo",number_format($efectivos[$i]['efectivo'],2,'.',','));
}

$tpl->printToScreen();

?>