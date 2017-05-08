<?php
include 'DB.php';
include './includes/dbstatus.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass();
$session->validar_sesion();

// Obtener menus para usuario activo
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
	die($db->getMessage());
}

$sql = "SELECT idmenu, menus.path, menus.descripcion FROM menus_permisos WHERE iduser = $_SESSION[iduser] AND authlevel = $_SESSION[authlevel] AND permiso = TRUE AND menus.idmenu = idmenu";
$result = $db->query($sql);
$db->disconnect();
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/status.tpl" );

$tpl->prepare();

// Crear enlaces a menus
while ($menu = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("menus");
	$tpl->assign("menupath",$menu->path);
	$tpl->assign("descripcion",$menu->descripcion);
}

$tpl->gotoBlock("_ROOT");
$tpl->assign("user", $_SESSION['username']);
$fecha = date("d/m/Y");
$hora = date("H:i:s");
$tpl->assign("fecha", $fecha);
$tpl->assign("hora", $hora);

// Imprimir el resultado
$tpl->printToScreen();
?>
