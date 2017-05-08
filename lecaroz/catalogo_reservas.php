<?php
// CONTROL PRODUCCION
// Tabla 'control_produccion'
// Menu

//define ('IDSCREEN',1212); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de reserva ya existe en la Base de Datos.";
//$descripcion_error[2] = "Número de turno no existe en la Base de Datos.";
//$descripcion_error[3] = "El código del producto no existe en la Base de Datos.";

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
$tpl->assignInclude("body","./plantillas/bal/catalogo_reservas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);
$tpl->assign("tabla","catalogo_reservas");

// Obtener ultimo registro
$next = nextID2("catalogo_reservas", "tipo_res", $dsn);
$tpl->assign("tipo_res",$next);

// Generar listado de gastos
$gas = ejecutar_script("SELECT codgastos,descripcion FROM catalogo_gastos ORDER BY codgastos ASC",$dsn);
for ($i=0; $i<count($gas); $i++) {
	$tpl->newBlock("nombre_gasto");
	$tpl->assign("codgasto",$gas[$i]['codgastos']);
	$tpl->assign("nombre_gasto",$gas[$i]['descripcion']);
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

// Imprimir el resultado
$tpl->printToScreen();

?>