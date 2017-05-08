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

if (isset($_GET['accion']) && $_GET['accion'] == "upload") {
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['image']['tmp_name'][0]));
	
	// Insertar la imagen en la base de datos
	$sql = "INSERT INTO imagenes (id_doc, imagen) VALUES ($_GET[id_doc], '$imgData');\n";
	$db_scans->query($sql);
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_doc_add_page.tpl");
$tpl->prepare();

$tpl->assign('id_doc', $_GET['id_doc']);
$tpl->assign('server_addr', $_SERVER['SERVER_ADDR']);

$tpl->printToScreen();
?>