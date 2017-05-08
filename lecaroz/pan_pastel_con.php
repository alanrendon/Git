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
$descripcion_error[1] = "No existe la factura para esta compañía";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

if (isset($_POST['id_sol'])) {
	$sql = '';
	foreach ($_POST['id_sol'] as $id)
		$sql .= "UPDATE modificacion_pastel SET fecha_modificacion = CURRENT_DATE WHERE id = $id;\n";
	
	ejecutar_script($sql, $dsn);
	die(header('location: ./pan_pastel_con.php'));
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pastel_con.tpl");
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
	$sql="select * from modificacion_pastel where num_cia=".$_GET['num_cia']." and fecha_modificacion is NULL order by fecha_solicitud, num_remi";
	$facturas=ejecutar_script($sql,$dsn);
	if(!$facturas)
	{
		header("location: ./pan_pastel_con.php?codigo_error=1");
		die();
	}
	for($i=0;$i<count($facturas);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign('id_sol', $facturas[$i]['id']);
		if($facturas[$i]['let_folio']=='X')
			$tpl->assign("let_folio",'');
		else
			$tpl->assign("let_folio",$facturas[$i]['let_folio']);
		$tpl->assign("num_remi",$facturas[$i]['num_remi']);
		$tpl->assign("fecha_solicitud",$facturas[$i]['fecha_solicitud']);
		$tpl->assign("descripcion",$facturas[$i]['descripcion']);

		if($facturas[$i]['fecha_autorizacion'] !="")
		{	
			$tpl->assign("fecha_autorizacion",$facturas[$i]['fecha_autorizacion']);
			$tpl->newBlock("modificar");
			$tpl->assign("let_remi",$facturas[$i]['let_folio']);
			$tpl->assign("num_remi",$facturas[$i]['num_remi']);
			$tpl->assign("num_cia",$_GET['num_cia']);
			$tpl->assign("id",$facturas[$i]['id']);
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

	$sql="select * from modificacion_pastel where num_cia=".$_GET['num_cia']." and fecha_modificacion is  not NULL order by fecha_solicitud, num_remi";
	$fac=ejecutar_script($sql,$dsn);
	if(!$fac)
	{
		header("location: ./pan_pastel_con.php?codigo_error=1");
		die();
	}
	for($j=0;$j<count($fac);$j++)
	{
		$tpl->newBlock("reg");
		if($fac[$j]['let_folio']=='X')
			$tpl->assign("letra_folio1","");
		else
			$tpl->assign("letra_folio1",$fac[$j]['let_folio']);
		$tpl->assign("num_remi1",$fac[$j]['num_remi']);
		$tpl->assign("fecha_solicitud1",$fac[$j]['fecha_solicitud']);
		$tpl->assign("fecha_autorizacion1",$fac[$j]['fecha_autorizacion']);
		$tpl->assign("fecha_modificacion1",$fac[$j]['fecha_modificacion']);
	}
}
$tpl->printToScreen();
?>