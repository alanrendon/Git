<?php
// ALTA DE COMPAÑIAS
// Tabla 'catalogo_compañias'
// Menu 'Facturas y Proveedores->Catalogos'

define ('IDSCREEN',3214); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include 'DB.php';
include './includes/class.db2.inc.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "N&uacute;mero de compa&ntilde;&iacute;a ya existe en la Base de Datos";
$descripcion_error[2] = "N&uacute;mero de compa&ntilde;&iacute;a dependiente no existe en la Base de Datos";


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
$id = nextid($screen->tabla, "num_cia", $dsn);
$tpl->assign("id",$id);

// Generar listado de Bancos
$db = DB::connect($dsn);
if (DB::isError($db)) {
	echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. fac_pue_altas.<br>";
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

// Generar listado de Delegaciones IMSS
$sql = "SELECT * FROM catalogo_del_imss ORDER BY iddelimss";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuedelimss",$row->iddelimss);
$tpl->assign("iddelimss",$row->iddelimss);
$tpl->assign("namedelimss",$row->nombre_del_imss);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("delimss");
	$tpl->assign("valuedelimss",$row->iddelimss);
	$tpl->assign("iddelimss",$row->iddelimss);
	$tpl->assign("namedelimss",$row->nombre_del_imss);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Subdelegaciones IMSS
$sql = "SELECT * FROM catalogo_subdel_imss ORDER BY idsubdelimss";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuesubdelimss",$row->idsubdelimss);
$tpl->assign("idsubdelimss",$row->idsubdelimss);
$tpl->assign("namesubdelimss",$row->nombre_subdel_imss);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("subdelimss");
	$tpl->assign("valuesubdelimss",$row->idsubdelimss);
	$tpl->assign("idsubdelimss",$row->idsubdelimss);
	$tpl->assign("namesubdelimss",$row->nombre_subdel_imss);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Contadores
$sql = "SELECT * FROM catalogo_contadores ORDER BY idcontador";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuecontador",$row->idcontador);
$tpl->assign("idcontador",$row->idcontador);
$tpl->assign("namecontador",$row->nombre_contador);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("contador");
	$tpl->assign("valuecontador",$row->idcontador);
	$tpl->assign("idcontador",$row->idcontador);
	$tpl->assign("namecontador",$row->nombre_contador);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Administradores
$sql = "SELECT * FROM catalogo_administradores ORDER BY idadministrador";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valueadministrador",$row->idadministrador);
$tpl->assign("idadministrador",$row->idadministrador);
$tpl->assign("nameadministrador",$row->nombre_administrador);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("administrador");
	$tpl->assign("valueadministrador",$row->idadministrador);
	$tpl->assign("idadministrador",$row->idadministrador);
	$tpl->assign("nameadministrador",$row->nombre_administrador);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Auditores
$sql = "SELECT * FROM catalogo_auditores ORDER BY idauditor";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valueauditor",$row->idauditor);
$tpl->assign("idauditor",$row->idauditor);
$tpl->assign("nameauditor",$row->nombre_auditor);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("auditor");
	$tpl->assign("valueauditor",$row->idauditor);
	$tpl->assign("idauditor",$row->idauditor);
	$tpl->assign("nameauditor",$row->nombre_auditor);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Aseguradoras
$sql = "SELECT * FROM catalogo_aseguradoras ORDER BY idaseguradora";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valueaseguradora",$row->idaseguradora);
$tpl->assign("idaseguradora",$row->idaseguradora);
$tpl->assign("nameaseguradora",$row->nombre_aseguradora);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("aseguradora");
	$tpl->assign("valueaseguradora",$row->idaseguradora);
	$tpl->assign("idaseguradora",$row->idaseguradora);
	$tpl->assign("nameaseguradora",$row->nombre_aseguradora);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Sindicatos
$sql = "SELECT * FROM catalogo_sindicatos ORDER BY idsindicato";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valuesindicato",$row->idsindicato);
$tpl->assign("idsindicato",$row->idsindicato);
$tpl->assign("namesindicato",$row->nombre_sindicato);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("sindicato");
	$tpl->assign("valuesindicato",$row->idsindicato);
	$tpl->assign("idsindicato",$row->idsindicato);
	$tpl->assign("namesindicato",$row->nombre_sindicato);
}
$tpl->gotoBlock("_ROOT");

// Generar listado de Operadoras
$sql = "SELECT * FROM catalogo_operadoras ORDER BY idoperadora";
$result = $db->query($sql);
if (DB::isError($result)) {
	$db->disconnect();
	echo "Error en script SQL: $sql<br>Avisar al administrador.<br>";
	die($result->getMessage());
}
$row = $result->fetchRow(DB_FETCHMODE_OBJECT);
$tpl->assign("valueoperadora",$row->idoperadora);
$tpl->assign("idoperadora",$row->idoperadora);
$tpl->assign("nameoperadora",$row->nombre_operadora);
while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
	$tpl->newBlock("operadora");
	$tpl->assign("valueoperadora",$row->idoperadora);
	$tpl->assign("idoperadora",$row->idoperadora);
	$tpl->assign("nameoperadora",$row->nombre_operadora);
}
$tpl->gotoBlock("_ROOT");

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