<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> 

//define ('IDSCREEN',1); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de proveedor no existe en el Catalogo de productos por proveedor.";
$descripcion_error[2] = "Número de producto no existe en la Base de Datos.";
$descripcion_error[3] = "El número de factura ya existe en la Base de Datos.";
$descripcion_error[4] = "Número de compañia no existe en la Base de Datos.";

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

$tpl->assignInclude("body","./plantillas/fac/fac_fac_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
$tpl->assign("tabla","entrada_mp");

	

	$tpl->newBlock("obtener_compania");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("dia",date("d"));
	
	if(ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", date("d/m/Y"), $fecha)){
		$fecha[1] = $fecha[1] -1;
		$tpl->assign("fecha","$fecha[1]/$fecha[2]/$fecha[3]");
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
?>