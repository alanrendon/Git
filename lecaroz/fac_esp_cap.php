<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> 
//define ('IDSCREEN',); //ID de pantalla sin ID
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
$descripcion_error[1] = "No tengo productos registrados para este proveedor";
$descripcion_error[2] = "Ya existe esta factura";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_esp_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla


if (!isset($_GET['compania'])) 
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("dia",date("d"));
	
	if (isset($_SESSION['fac_cap']['num_pro'])) {
		$tpl->assign('num_pro', $_SESSION['fac_cap']['num_pro']);
	}

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
	$tpl->printToScreen();
	die();
}



if(existe_registro("facturas",array("num_proveedor","num_fact"),array($_GET['num_proveedor'],$_GET['num_documento']),$dsn))
{
	header("location: ./fac_esp_cap.php?codigo_error=2");
	die;
}
$sql="select catalogo_mat_primas.codmp, nombre, num_proveedor, contenido, desc1, desc2, iva, precio from catalogo_productos_proveedor join 
catalogo_mat_primas on(catalogo_mat_primas.codmp = catalogo_productos_proveedor.codmp and num_proveedor = ".$_GET['num_proveedor'].") 
order by catalogo_productos_proveedor.codmp";
$mp=ejecutar_script($sql,$dsn);

$tpl->newBlock("factura");
if(!$mp)
{
	header("location: ./fac_esp_cap.php?codigo_error=1");
	die();
}
else
{
//	$tpl->newBlock("factura");
	for($i=0;$i<count($mp);$i++){
		$tpl->newBlock("nom_mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre_mp",$mp[$i]['nombre']);
		$tpl->assign("des",$mp[$i]['desc1']);
		$tpl->assign("des1",$mp[$i]['desc2']);
		$tpl->assign("iva",$mp[$i]['iva']);
		$tpl->assign("cont",$mp[$i]['contenido']);
		$tpl->assign("prec",$mp[$i]['precio']);
		}
	$tpl->gotoBlock("factura");

}

$_SESSION['fac_cap']['num_pro'] = $_GET['num_proveedor'];

$tpl->assign("fecha_hoy",date("d/m/Y"));
$tpl->assign("tabla","inventario_real");
$tpl->assign("num_cia",$_GET['compania']);
$nom_cia=obtener_registro("catalogo_companias", array("num_cia"), array($_GET['compania']),"","",$dsn);
$tpl->assign("nombre_corto",$nom_cia[0]['nombre_corto']);
$tpl->assign("num_proveedor",$_GET['num_proveedor']);
$nom_prov=obtener_registro("catalogo_proveedores", array("num_proveedor"), array($_GET['num_proveedor']),"","",$dsn);
$tpl->assign("nombre",$nom_prov[0]['nombre']);
$tpl->assign("fecha",$_GET['fecha']);
$tpl->assign("totalf",$_GET['totalf']);
$tpl->assign("num_documento",$_GET['num_documento']);

for($i=0;$i<10;$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);
	}
$tpl->printToScreen();
?>