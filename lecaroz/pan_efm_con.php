<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existen registros para modificar";
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
$tpl->assignInclude("body","./plantillas/pan/pan_efm_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error




if(!isset($_GET['num_cia']))
{
	$tpl->newBlock("obtener_datos");
	
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

if($_GET['consulta']==0)
{
	$tpl->newBlock("autorizados");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET["num_cia"]),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	$sql="select * from modificacion_efectivos where num_cia=".$_GET['num_cia']." and fecha_modificacion is NULL order by fecha_solicitud,fecha";
	$efectivos=ejecutar_script($sql,$dsn);
	if(!$efectivos)
	{
		header("location: ./pan_efm_con.php?codigo_error=1");
		die();
	}
	for($i=0;$i<count($efectivos);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("fecha_solicitud",$efectivos[$i]['fecha_solicitud']);
		$tpl->assign("descripcion",$efectivos[$i]['descripcion']);
		

		if($efectivos[$i]['fecha_autorizacion'] !="")
		{	
			$tpl->assign("fecha_autorizacion",$efectivos[$i]['fecha_autorizacion']);
			$tpl->newBlock("modificar");
			$tpl->assign("num_cia",$_GET['num_cia']);
			$tpl->assign("id",$efectivos[$i]['id']);
			$tpl->assign("fecha",$efectivos[$i]['fecha']);
		}
		else
			$tpl->assign("fecha_autorizacion","En proceso");
	}

}
else
{
	$tpl->newBlock("actualizados");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET["num_cia"]),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);

	$sql="select * from modificacion_efectivos where num_cia=".$_GET['num_cia']." and fecha_modificacion is  not NULL order by fecha_solicitud,fecha";
	$efe=ejecutar_script($sql,$dsn);
	if(!$efe)
	{
		header("location: ./pan_efm_con.php?codigo_error=1");
		die();
	}
	for($j=0;$j<count($efe);$j++)
	{
		$tpl->newBlock("reg");
		$tpl->assign("descripcion",$efe[$j]['descripcion']);
		$tpl->assign("fecha_solicitud1",$efe[$j]['fecha_solicitud']);
		$tpl->assign("fecha_autorizacion1",$efe[$j]['fecha_autorizacion']);
		$tpl->assign("fecha_modificacion1",$efe[$j]['fecha_modificacion']);
	}
}
$tpl->printToScreen();
?>