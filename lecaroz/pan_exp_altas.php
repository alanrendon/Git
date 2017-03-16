<?php
// ALTA DE EXPENDIOS
// Tabla 'catalogo_expendios'
// Menu 'Panaderias->Expendios'

define ('IDSCREEN',1111); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include 'DB.php';
include './includes/class.db2.inc.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "N&uacute;mero de expendio ya existe en la Base de Datos";
$descripcion_error[2] = "N&uacute;mero de compa&ntilde;&iacute;a no existe en la Base de Datos";


// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass();
$session->validar_sesion();

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN,$dsn);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. pan_exp_altas.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM screens WHERE idscreen = ".IDSCREEN;
$result = $db->query($sql);
$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador. pan_exp_altas.<br>";
	die($result->getMessage());
}

$sql = "SELECT * FROM menus WHERE idmenu = $screen->idmenu";
$result = $db->query($sql);
$menu = $result->fetchRow(DB_FETCHMODE_OBJECT);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador. pan_exp_altas.<br>";
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

// Generar listado de tipos de expendios
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. pan_exp_altas.<br>";
	die($db->getMessage());
}
$sql = "SELECT * FROM tipo_expendio ORDER BY idtipoexpendio";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador. pan_exp_altas.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuetipo",$row->idtipoexpendio);
$tpl->assign("idtipo",$row->idtipoexpendio);
$tpl->assign("nametipo",$row->descripcion);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("tipo");
	$tpl->assign("valuetipo",$row->idtipoexpendio);
	$tpl->assign("idtipo",$row->idtipoexpendio);
	$tpl->assign("nametipo",$row->descripcion);
}
$tpl->gotoBlock("_ROOT");

$sql = "SELECT * FROM catalogo_agentes_venta ORDER BY nombre";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador. pan_exp_altas.<br>";
	die($result->getMessage());
}
if ($result->numRows() > 0)
	while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
		$tpl->newBlock("agente");
		$tpl->assign("id",$row->idagven);
		$tpl->assign("nombre",$row->nombre);
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