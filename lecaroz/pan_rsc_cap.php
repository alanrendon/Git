<?php
// AQUI VA EL NOMBRE DE LA CAPTURA
// Tabla 'captura_efectivos'
// Menu 'Nombre del menu->Nombre del submenu'

define ('IDSCREEN',1631); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';


// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe numero de compañia ".$_GET['cia'];
$descripcion_error[2] = "No existe numero de factura ".$_GET['fac'];
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");



// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

for ($i=0;$i<10;$i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->gotoBlock("_ROOT");
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>





