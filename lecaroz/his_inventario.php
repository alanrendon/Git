<?php
// CAPTURA DE INVENTARIO
// Tabla 'inventario_real' e 'inventario_virtual'
// Menu 'No definido'

//define ('IDSCREEN',1); // ID de pantalla

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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "Fecha de captura ya se encuentra en el sistema";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/his_inventario.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Insertar datos en la base -----------------------------------------------
if (isset($_GET['ok'])) {
	$inventario_real = new DBclass($dsn,"historico_inventario",$_POST);
	$inventario_real->xinsertar();
	
	header("location: ./his_inventario.php");
}

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_compania");
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	die();
}
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	die();
}
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_compania");
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	die();
}

// ---------------------------------- Trazar pantalla de captura ---------------------------------------------
// Verificar si existe la compañía
if (!$cia = obtener_registro("catalogo_companias", array("num_cia"), array($_GET['compania']), "", "", $dsn)) {
	header("location: ./inventario.php?codigo_error=1");
	die();
}

// Verificar formato de la fecha
if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha)) {
	header("location: ./inventario.php?codigo_error=3");
	die();
}

// Verificar rango de la fecha, debe corresponder al mes y año en curso
/*if ($fecha[1] < 0 || $fecha[1] > 31 || $fecha[2] <= 0 || $fecha[2] > date("m") || $fecha[3] != date("Y")) {
	header("location: ./inventario.php?codigo_error=4");
	die();
}*/

/*if (!existe_registro("inventario_real",array("num_cia","fecha_entrada"),array($_GET['compania'],$_GET['fecha']),$dsn)) {
	header("location: ./inventario.php?codigo_error=2");
	die();
}*/

// Crear bloque de captura
$tpl->newBlock("captura");

$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_GET['fecha']);

// Obtener listado de materias primas
$mp = obtener_registro("catalogo_mat_primas",array(),array(),"codmp","ASC",$dsn);
// Obtener listado de unidades de consumo
$unidades = obtener_registro("tipo_unidad_consumo",array(),array(),"idunidad","ASC",$dsn);

for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("nombre_mp");
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre_mp",$mp[$i]['nombre']);
	$tpl->assign("unidad",$unidades[$mp[$i]['unidadconsumo']-1]['descripcion']);
}

$num_filas = 100;

for ($i=0; $i<$num_filas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);
}

$tpl->printToScreen();
?>
