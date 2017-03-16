<?php
// BAJA DE USUARIOS DE SISTEMA
// Tablas 'auth'
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
$descripcion_error[1] = "El usuario ya existe en sistema";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/adm/adm_usr_del.tpl" );
$tpl->prepare();

// Alta de usuario
if (isset($_POST['iduser'])) {
	$sql = "DELETE FROM auth WHERE iduser = $_POST[iduser]";
	$result = ejecutar_script($sql,$dsn);
	
	$sql = "DELETE FROM menus_permisos WHERE iduser = $_POST[iduser]";
	$result = ejecutar_script($sql,$dsn);
	
	$sql = "DELETE FROM catalogo_operadoras WHERE iduser = $_POST[iduser]";
	$result = ejecutar_script($sql,$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("baja");

$tpl->assign("iduser",$_GET['iduser']);

$tpl->printToScreen();
?>