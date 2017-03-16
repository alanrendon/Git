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

// --------------------------------- Obtener informaciσn de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Lo siento pero no encontre facturas";
$descripcion_error[2] = "Lo siento pero la factura ya esta pagada, no se puede cancelar";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_prov_can.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compaρνa -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

	// Si viene de una pαgina que genero error
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

// -------------------------------- Mostrar listado ---------------------------------------------------------
// Obtener rosticerias
$sql = "SELECT * FROM facturas WHERE num_cia='".$_GET['num_cia']."' and num_fact='".$_GET['num_fac']."' and num_proveedor=".$_GET['proveedor'];
$factura = ejecutar_script($sql,$dsn);

if(!$factura)
{
	header("location: ./fac_fac_can.php?codigo_error=1");
	die;
}
else
{
	if(existe_registro("facturas_pagadas",array("num_cia","num_fact","num_proveedor"),array($_GET['num_cia'],$_GET['num_fac'],$_GET['proveedor']),$dsn))
	{
		header("location: ./fac_fac_can.php?codigo_error=2");
		die;
//		$prueba=obtener_registro("facturas_pagadas",array('num_cia','num_fact','num_proveedor'),array($_GET['num_cia'],$_GET['num_fac'],$_GET['proveedor']),"","",$dsn);
//		print_r($prueba);
	}
	$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['proveedor']),"","",$dsn);
	// Crear bloque de listado
	$tpl->newBlock("factura");
	$tpl->assign('numero_cia',$_GET['num_cia']);
	$tpl->assign('nombre_cia',$nomcia[0]['nombre_corto']);
	$tpl->assign('num_proveedor',$_GET['proveedor']);
	$tpl->assign('nom_proveedor',$nomproveedor[0]['nombre']);
	$tpl->assign('num_factura',$factura[0]['num_fact']);
	$tpl->assign('fecha_mov',$factura[0]['fecha_mov']);
	$tpl->assign('codgastos',$factura[0]['codgastos']);
	$tpl->assign("total",$factura[0]['importe_total']);

	$tpl->printToScreen();
}
?>