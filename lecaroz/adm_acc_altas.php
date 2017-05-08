<?php
// ALTA DE ACCIONISTAS
// Tablas 'catalogo_accionistas'
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

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El número de accionista ya existe en la base de datos";

// --------------------------------- Insertar registro en la tabla -------------------------------------------
if (isset($_GET['tabla'])) {
	if (existe_registro($_GET['tabla'],array("num"),array($_POST['num']),$dsn)) {
		header("location: ./adm_acc_altas.php?codigo_error=1");
		die;
	}
	
	// Organizar datos
	
	$db = new DBclass($dsn,$_GET['tabla'],$_POST);
	$db->generar_script_insert("");
	$db->ejecutar_script();
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/adm/adm_acc_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla de insercion
$tpl->assign("tabla","catalogo_accionistas");

// Obtener ultimo ID del catalogo
$num = nextID2("catalogo_accionistas","num",$dsn);

// Asignar ID
$tpl->assign("num",$num);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>