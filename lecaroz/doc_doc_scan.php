<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_doc_scan.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['accion']) && $_GET['accion'] == "cancelar") {
	// Borrar cualquier imagen temporal que se encuentre en la base de datos
	$db_scans->query("DELETE FROM imagenes_temp");
	$db_scans->desconectar();
	$db->desconectar();
	
	unset($_SESSION['scan']);
	header("location: ./doc_doc_scan.php");
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == "terminar") {
	// Resetear ultimo indice
	$sql = "SELECT setval('imagenes_indice_seq', 1, false);\n";
	// Crear registro de documento
	$sql .= "INSERT INTO documentos (num_cia,fecha,descripcion,tipo_doc) VALUES ({$_SESSION['scan']['num_cia']},CURRENT_DATE,'" . strtoupper($_SESSION['scan']['descripcion']) . "',{$_SESSION['scan']['tipo_doc']});\n";
	// Mover todas las imagenes que se encuentran en temporal a la tabla de imagenes
	$sql .= "INSERT INTO imagenes (id_doc,imagen) SELECT (SELECT last_value FROM documentos_id_doc_seq),imagen FROM imagenes_temp;\n";
	// Borrar imagenes de la tabla temporal
	$sql .= "DELETE FROM imagenes_temp";
	
	$db_scans->query($sql);
	$db_scans->desconectar();
	$db->desconectar();
	
	// Destruir variable de sesión
	unset($_SESSION['scan']);
	
	header("location: ./doc_doc_scan.php");
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == "upload") {
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['image']['tmp_name'][0]));
	// Insertar la imagen en la base de datos
	$sql = "INSERT INTO imagenes_temp (imagen) VALUES ('$imgData')";
	$db_scans->query($sql);
	
	$db->desconectar();
	$db_scans->desconectar();
	die;
}

if (isset($_SESSION['scan']) && !isset($_GET['accion'])) {
	$tpl->newBlock("scan");
	
	$tpl->assign('server_addr', $_SERVER['SERVER_ADDR']);
	
	$tpl->printToScreen();
	$db_scans->desconectar();
	die;
}

if (isset($_POST['num_cia'])) {
	$_SESSION['scan']['num_cia'] = $_POST['num_cia'];
	$_SESSION['scan']['tipo_doc'] = $_POST['tipo_doc'];
	$_SESSION['scan']['descripcion'] = $_POST['descripcion'];
	
	header("location: ./doc_doc_scan.php");
}

// Pedir datos del documento
if (!isset($_SESSION['scan'])) {
	// Borrar cualquier imagen temporal que se encuentre en la base de datos
	$db_scans->query("DELETE FROM imagenes_temp");
	
	$tpl->newBlock("datos");
	
	$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_cia";
	$cia = $db->query($sql);
	
	for ($i = 0; $i < count($cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	}
	
	$sql = "SELECT * FROM catalogo_documentos ORDER BY descripcion";
	$tipo = $db_scans->query($sql);
	
	for ($i = 0; $i < count($tipo); $i++) {
		$tpl->newBlock("tipo");
		$tpl->assign("tipo_doc", $tipo[$i]['tipo_doc']);
		$tpl->assign("descripcion", $tipo[$i]['descripcion']);
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
	
	$tpl->printToScreen();
	$db->desconectar();
	$db_scans->desconectar();
	die;
}
?>