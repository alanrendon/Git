<?php
// ALTA DE USUARIOS DE SISTEMA
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
$tpl->assignInclude("body", "./plantillas/adm/adm_usr_altas.tpl" );
$tpl->prepare();

// Alta de usuario
if (isset($_POST['username'])) {
	// Verificar que el usuario existe en el sistema
	$sql = "SELECT iduser FROM auth WHERE username = '".strtoupper($_POST['username'])."'";
	$result = ejecutar_script($sql,$dsn);
	
	if ($result) {
		header("location: ./adm_usr_altas.php?codigo_error=1");
		die;
	}
	
	// Insertar registro de usuario
	$sql = "INSERT INTO auth (username,password,nombre,apellido,authlevel) VALUES ('".strtoupper($_POST['username'])."','".strtoupper($_POST['password'])."','".strtoupper($_POST['nombre'])."','".strtoupper($_POST['apellido'])."',1)";
	ejecutar_script($sql,$dsn);
	
	// Obtener ID del ultimo usuario
	$sql = "SELECT iduser FROM auth WHERE username = '".strtoupper($_POST['username'])."'";
	$id = ejecutar_script($sql,$dsn);

	
	// Si es capturista, insertar registro en catalogo de capturistas
	if (isset($_POST['capturista'])) {
		$sql = "INSERT INTO catalogo_operadoras (nombre_operadora,nombre,iduser) VALUES ('".strtoupper($_POST['nombre']." ".$_POST['apellido'])."','".strtoupper($_POST['nombre'])."',".$id[0]['iduser'].")";
		ejecutar_script($sql,$dsn);
	}
	
	// Generar permisos para menus
	for ($i=1; $i<=8; $i++)
		if (isset($_POST['menu'.$i])) {
			$sql = "INSERT INTO menus_permisos (iduser,authlevel,idmenu,permiso) VALUES ({$id[0]['iduser']},1,$i,'TRUE')";
			ejecutar_script($sql,$dsn);
		}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("alta");

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
?>