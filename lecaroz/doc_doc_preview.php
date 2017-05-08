<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans");	// Coneccion a la base de datos de las imagenes

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/doc/doc_doc_preview.tpl" );
$tpl->prepare();

$sql = "SELECT num_cia, fecha, tipo_doc FROM documentos WHERE id_doc = $_GET[id]";
$result = $db_scans->query($sql);
$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre_corto = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre_cia", $nombre_corto[0]['nombre_corto']);
$tpl->assign("fecha", $result[0]['fecha']);
$tipo = $db_scans->query("SELECT descripcion FROM catalogo_documentos WHERE tipo_doc = {$result[0]['tipo_doc']}");
$tpl->assign("tipo", $tipo[0]['descripcion']);

$tpl->assign('id_doc', $_GET['id']);

$sql = "SELECT id_img, indice FROM imagenes WHERE id_doc = $_GET[id] ORDER BY indice";
$result = $db_scans->query($sql);

$numcols = 4;
$cols = $numcols;

for ($i = 0; $i < count($result); $i++) {
	if ($cols == $numcols) {
		$tpl->newBlock("fila");
		$cols = 0;
	}
	$tpl->newBlock("col");
	$tpl->assign("id", $result[$i]['id_img']);
	$tpl->assign("indice", $result[$i]['indice']);
	$cols++;
}

$tpl->printToScreen();
$db->desconectar();
?>