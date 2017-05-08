<?php
// MODIFICACION DE USUARIOS DE SISTEMA
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
$tpl->assignInclude("body", "./plantillas/adm/adm_usr_mod.tpl" );
$tpl->prepare();

// Alta de usuario
if (isset($_POST['iduser'])) {
	// Actualizar registro de usuario
	$sql = "UPDATE auth SET username='".strtoupper($_POST['username'])."',password='".strtoupper($_POST['password'])."',nombre='".strtoupper($_POST['nombre'])."',apellido='".strtoupper($_POST['apellido'])."' WHERE iduser = $_POST[iduser]";
	ejecutar_script($sql,$dsn);
	
	// Si es capturista, insertar registro en catalogo de capturistas
	if (isset($_POST['capturista'])) {
		if (!ejecutar_script("SELECT idoperadora FROM catalogo_operadoras WHERE iduser = $_POST[iduser]",$dsn)) {
			$sql = "INSERT INTO catalogo_operadoras (nombre_operadora,nombre,iduser) VALUES ('".strtoupper($_POST['nombre']." ".$_POST['apellido'])."','".strtoupper($_POST['nombre'])."',".$_POST['iduser'].")";
			ejecutar_script($sql,$dsn);
		}
	}
	else {
		$sql = "DELETE FROM catalogo_operadoras WHERE iduser=$_POST[iduser]";
		ejecutar_script($sql,$dsn);
	}
	
	// Generar permisos para menus
	$sql = "DELETE FROM menus_permisos WHERE iduser = $_POST[iduser]";
	ejecutar_script($sql,$dsn);
	for ($i=1; $i<=8; $i++)
		if (isset($_POST['menu'.$i])) {
			$sql = "INSERT INTO menus_permisos (iduser,authlevel,idmenu,permiso) VALUES ($_POST[iduser],1,$i,'TRUE')";
			ejecutar_script($sql,$dsn);
		}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("mod");
$tpl->assign("iduser",$_GET['iduser']);

// Obtener datos
$sql = "SELECT * FROM auth WHERE iduser = $_GET[iduser]";
$result = ejecutar_script($sql,$dsn);

$tpl->assign("username",$result[0]['username']);
$tpl->assign("password",$result[0]['password']);
$tpl->assign("nombre",$result[0]['nombre']);
$tpl->assign("apellido",$result[0]['apellido']);

// Obtener permisos de menus
$sql = "SELECT idmenu FROM menus_permisos WHERE iduser = $_GET[iduser]";
$menu = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($menu); $i++)
	$tpl->assign($menu[$i]['idmenu'],"checked");

if ($id = ejecutar_script("SELECT idoperadora FROM catalogo_operadoras WHERE iduser = $_GET[iduser]",$dsn))
	$tpl->assign("checked","checked");

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