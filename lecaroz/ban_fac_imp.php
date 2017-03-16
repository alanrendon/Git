<?php
// DATOS PARA LA IMPRESIÓN DE FACTURAS
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ---------------------------------------------------------------<strong>-</strong>
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por imprimir";

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
$tpl->assignInclude("body","./plantillas/ban/ban_fac_imp.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date("n",mktime(0,0,0,date("m"),1,date("Y"))),"selected");
$tpl->assign("anio",date("Y",mktime(0,0,0,date("m")-1,1,date("Y"))));

$tpl->printToScreen();
?>