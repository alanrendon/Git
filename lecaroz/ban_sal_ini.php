<?php
// CAPTURA DE SALDO INICIAL
// Tablas 'estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informaciσn de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Nϊmero de compaρνa no se encuentra en la Base de Datos";
$descripcion_error[2] = "La compaρνa ya tiene saldo inicial";

// --------------------------------- Insertar datos a la base ------------------------------------------------
if (isset($_GET['tabla'])) {
	$db = new DBclass($dsn,$_GET['tabla'],$_POST);
	$db->generar_script_insert("");
	$db->ejecutar_script();
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_ini.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una pαgina que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die;
}

if (!existe_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),$dsn)) {
	header("location: ./ban_sal_ini.php?codigo_error=1");
	die;
}

if (!existe_registro("saldos",array("num_cia"),array($_GET['num_cia']),$dsn)) {
	$tpl->newBlock("captura");
	$tpl->assign("tabla","saldos");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->printToScreen();
	die;
}
else {
	header("location: ./ban_sal_ini.php?codigo_error=2");
	die;
}
?>