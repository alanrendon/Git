<?php
// CATALOGO DE TANQUES DE GAS
// Tabla 'control_produccion'
// Menu

//define ('IDSCREEN',1212); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "Registro del tanque ya se encuentra en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Capturar datos ----------------------------------------------------------
if (isset($_GET['tabla'])) {
	if (!existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),$dsn)) {
		header("location: ./fac_glp_altas.php?codigo_error=1");
		die;
	}
	if (existe_registro("catalogo_tanques",array("num_cia","num_tanque"),array($_POST['num_cia'],$_POST['num_tanque']),$dsn)) {
		header("location: ./fac_glp_altas.php?codigo_error=2");
		die;
	}
	
	$db = new DBclass($dsn,$_GET['tabla'],$_POST);
	$db->generar_script_insert("");
	$db->ejecutar_script();
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_glp_altas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","catalogo_tanques");

$cia = ejecutar_script("SELECT * FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
// Generar listado de compañías
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
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