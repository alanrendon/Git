<?php
// FACTURAS ESPECIALES
// Tabla 'facturas'
// Menu Proveedores y facturas -> Facturas de proveedores varios

//define ('IDSCREEN',3122); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Nϊmero de compaρνa no existe en la Base de Datos.";
$descripcion_error[2] = "Nϊmero de proveedor no existe en la Base de Datos.";
$descripcion_error[3] = "El proveedor no es vαlido para esta captura.";
$descripcion_error[4] = "Ya existe la factura";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener informaciσn de la pantalla --------------------------------------
//$session->info_pantalla();
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_luz_cap.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
$tpl->assign("tabla","facturas");

$tpl->gotoBlock("_ROOT");

$tpl->assign("user",$_SESSION['iduser']);

if(!isset($_GET['cia'])){

$tpl->newBlock("obtener_dato");
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
if(!existe_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),$dsn)){
	header('location: ./fac_luz_cap.php?codigo_error=1');
	die();
}
if(!($_GET['proveedor']==991 or $_GET['proveedor']==216 or $_GET['proveedor']==1035))
{
	header('location: ./fac_luz_cap.php?codigo_error=3');
	die();
}

$tpl->newBlock("factura");
$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
$proveedor=obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['proveedor']),"","",$dsn);
$tpl->assign("num_proveedor",$proveedor[0]['num_proveedor']);
$tpl->assign("nombre_proveedor",$proveedor[0]['nombre']);
$tpl->assign("num_cia",$_GET['cia']);
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("mes",date("m"));
$tpl->assign("dia",date("d"));
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$dia=date("d");
$mes=date("m");
$tpl->assign("num_fact",$_GET['cia'].$dia.$mes);


// Imprimir el resultado
$tpl->printToScreen();

?>