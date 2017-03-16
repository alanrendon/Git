<?php
// ALTA DE PROVEEDORES
// Tabla 'catalogo_proveedores'
// Menu 'Facturas y Proveedores->Catalogos'

define ('IDSCREEN',3111); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include 'DB.php';
include './includes/class.db2.inc.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "N&uacute;mero de proveedor ya existe en la Base de Datos";


// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass();
$session->validar_sesion();

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN,$dsn);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. ban_con_altas.<br>";
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
$id = nextid($screen->tabla, "num_proveedor", $dsn);
$tpl->assign("id",$id);

// Generar listado de bancos
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. ban_con_altas.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM catalogo_bancos ORDER BY idbancos";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuebanco",$row->idbancos);
$tpl->assign("idbanco",$row->idbancos);
$tpl->assign("namebanco",$row->nom_banco);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("banco");
	$tpl->assign("valuebanco",$row->idbancos);
	$tpl->assign("idbanco",$row->idbancos);
	$tpl->assign("namebanco",$row->nom_banco);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de tipo de pagos
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. ban_con_altas.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM tipo_pago ORDER BY idtipopago";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuepago",$row->idtipopago);
$tpl->assign("idpago",$row->idtipopago);
$tpl->assign("namepago",$row->descripcion);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("pago");
	$tpl->assign("valuepago",$row->idtipopago);
	$tpl->assign("idpago",$row->idtipopago);
	$tpl->assign("namepago",$row->descripcion);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de tipo de proveedores
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. ban_con_altas.<br>";
	die($db->getMessage());
}

$sql = "SELECT * FROM tipo_proveedor ORDER BY idtipoproveedor";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valueproveedor",$row->idtipoproveedor);
$tpl->assign("idproveedor",$row->idtipoproveedor);
$tpl->assign("nameproveedor",$row->descripcion);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("proveedor");
	$tpl->assign("valueproveedor",$row->idtipoproveedor);
	$tpl->assign("idproveedor",$row->idtipoproveedor);
	$tpl->assign("nameproveedor",$row->descripcion);
}
$tpl->gotoBlock("_ROOT");
$db->disconnect();

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