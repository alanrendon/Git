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
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "Número de proveedor no existe en la Base de Datos.";
$descripcion_error[3] = "Código de gasto no existe en la Base de Datos.";
$descripcion_error[4] = "El número de factura ya existe en la Base de Datos, favor de verificar";


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
$tpl->assignInclude("body","./plantillas/fac/fac_arren_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","facturas");

$tpl->gotoBlock("_ROOT");

$tpl->assign("dia",date("d"));
$tpl->assign("mes",date("m"));
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("fecha_hoy",date("d/m/Y"));
$tpl->assign("user",$_SESSION['iduser']);


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


$sql="select * from catalogo_companias order by num_cia";
$cia=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cia);$i++){
		$tpl->newBlock("nom_cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		}

// Imprimir el resultado
$tpl->printToScreen();

?>