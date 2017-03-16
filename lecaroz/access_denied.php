<?php
// ACCESS_DENIED.PHP -- Pantalla que registra un acceso ilegal del usuario.

include 'DB.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass();
$session->validar_sesion();

$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador.<br>";
	die($db->getMessage());
}

// Pantalla de error de acceso
$sql = "SELECT * FROM screens WHERE idscreen = $_GET[idscreen]";
$result = $db->query($sql);
$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);

$operacion = 'Acceso denegado a la pantalla $screen->idscreen:$screen->descripcion';

if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}

$session->guardar_registro_acceso($operacion,$dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/access_denied.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");

// Imprimir el resultado
$tpl->printToScreen();
?>