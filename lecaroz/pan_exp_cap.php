<?php
// MOVIMIENTOS DE EXPENDIOS
// Tabla 'mov_expendios'
// Menu 'Panaderías->Expendios'

define ('IDSCREEN',1121); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
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
$descripcion_error[1] = "Expendio no existe en la Base de Datos";

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

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_compania");
	$tpl->assign("fecha",date("d/m/Y"));
	
	if (isset($_SESSION['exp'])) unset($_SESSION['exp']);
	
	// Obtener compañías por capturista
	if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia < 100 OR num_cia IN (702,703)) ORDER BY num_cia";
	else
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 OR num_cia IN (702,703) ORDER BY num_cia";
	$num_cia = ejecutar_script($sql,$dsn);
	
	for ($i=0; $i<count($num_cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$num_cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$num_cia[$i]['nombre_corto']);
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
	die();
}

// ------------------------------- Capturar expendios ------------------------------------------------------
// Verificar si existe la compañía
if (!existe_registro("catalogo_companias", array("num_cia"), array($_GET['compania']), $dsn)) {
	header("location: ./pan_exp_cap.php?codigo_error=1");
	die();
}

// Obtener la ultima fecha de captura
$ultima_fecha = ejecutar_script("SELECT fecha FROM mov_expendios WHERE num_cia = $_GET[compania] ORDER BY fecha DESC LIMIT 1",$dsn);
if ($ultima_fecha) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fecha[0]['fecha'],$temp);
	$fecha = date("d/m/Y",mktime(0,0,0,$temp[2],$temp[1]+1,$temp[3]));
}
else
	$fecha = date("1/d/Y");

$compania = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['compania']),"","",$dsn);
$rows = obtener_registro("catalogo_expendios",array("num_cia"),array($_GET['compania']),"num_referencia","ASC",$dsn);
$numrows = count($rows);

// Crear hoja de captura
$tpl->newBlock("hoja");

// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Asignar el número de filas a un campo oculto
$tpl->assign("numfilas",$numrows);

// Asignar valores a los campos del formulario
// Poner compañía
$tpl->assign("num_cia",$_GET['compania']);
$tpl->assign("nombre_cia",$compania[0]['nombre_corto']);
// Poner Fecha
$tpl->assign("fecha",/*$_GET['fecha']*/$fecha);

// Generar filas de captura
for ($i=0;$i<$numrows;$i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	
	if ($i < $numrows-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$numrows-1);
	
	$tpl->assign("exp",$rows[$i]['num_expendio']);
	$tpl->assign("exp_pan",$rows[$i]['num_referencia']);
	$tpl->assign("nombre",$rows[$i]['nombre']);
	//$tpl->gotoBlock("_ROOT");
	
	if (isset($_SESSION['exp'])) {
		$tpl->assign("pan_p_venta",$_SESSION['exp']['pan_p_venta'.$i]);
		$tpl->assign("devolucion",$_SESSION['exp']['devolucion'.$i]);
		$tpl->assign("pan_p_expendio",$_SESSION['exp']['pan_p_expendio'.$i]);
		$tpl->assign("abono",$_SESSION['exp']['abono'.$i]);
	}
}

// Imprimir el resultado
$tpl->printToScreen();
?>