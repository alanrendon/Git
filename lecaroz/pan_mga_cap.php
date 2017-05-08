<?php
// CAPTURA DE MOVIMIENTO DE GASTOS
// Tabla 'movimiento_gastos'
// Menu 'Panaderias->Gastos'

define ('IDSCREEN',1721); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

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

//empieza código para insertar un numero de renglones en un bloque
$tpl->assign("dia",date("d"));
$tpl->assign("mes",date("m"));
$tpl->assign("anio_actual",date("Y"));

if (isset($_SESSION['gastos'])) {
	$tpl->assign("num_cia",$_SESSION['gastos']['num_cia']);
	$tpl->assign("fecha",$_SESSION['gastos']['fecha']);
}

for ($i=0; $i<25; $i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	
	if($i+1 == 25)
		$tpl->assign("next","0");
	else
		$tpl->assign("next",$i+1);
	
	if($i > 0)
		$tpl->assign("ant",$i-1);
	else
		$tpl->assign("ant","24");
	
	if (isset($_SESSION['gastos'])) {
		$tpl->assign("codgastos",$_SESSION['gastos']['codgastos'.$i]);
		$tpl->assign("concepto",$_SESSION['gastos']['concepto'.$i]);
		$tpl->assign("importe",$_SESSION['gastos']['importe'.$i]);
	}
	
	$tpl->gotoBlock("_ROOT");
}

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

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

// Imprimir el resultado
$tpl->printToScreen();
?>