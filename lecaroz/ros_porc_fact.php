<?php
// ROSTICERIAS PESOS PROMEDIOS
// Tabla 'pesos_companias'

//define ('IDSCREEN',1212); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "Revisa los porcentajes";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Si hay datos de captura -------------------------------------------------
if (isset($_GET['tabla'])) {
	$db = new DBclass($dsn,$_GET['tabla'],$_POST);
	$db->xinsertar();
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_porc_fact.tpl");//plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","porcentajes_facturas");

for ($i=0; $i<10; $i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->gotoBlock("_ROOT");
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}


if(isset($_GET['mensaje']))
{
	$tpl->newBlock("message");
	$tpl->assign("message",$_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();

?>