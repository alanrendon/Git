<?php
// ALTA DE PUESTOS
// Tabla 'catalogopuestos'
// Menu 'Facturas y Proveedores->Catalogos'

define ('IDSCREEN',3211); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include 'DB.php';
include './includes/class.db2.inc.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "C&oacute;digo de puesto ya existe en la Base de Datos";


// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass();
$session->validar_sesion();

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN,$dsn);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. fac_pue_altas.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM screens WHERE idscreen = ".IDSCREEN;
$result = $db->query($sql);
$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}

$sql = "SELECT * FROM menus WHERE idmenu = $screen->idmenu";
$result = $db->query($sql);
$menu = $result->fetchRow(DB_FETCHMODE_OBJECT);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$db->disconnect();

// --------------------------------- Generar pantalla ---------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$menu->path/$screen->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla",$screen->tabla);

// Obtener proximo ID en la tabla y asignarlo
$id = nextid($screen->tabla, "cod_puestos", $dsn);
$tpl->assign("id",$id);

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