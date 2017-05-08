<?php
// CONSULTA DE FACTURAS DE PAN
// Tablas 'auth'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

$users = array(28, 29, 30, 31, 32, 33, 34, 35);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/doc/doc_doc_con.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$tipo_doc = $db_scans->query("SELECT tipo_doc, descripcion FROM catalogo_documentos ORDER BY descripcion");
	for ($i = 0; $i < count($tipo_doc); $i++) {
		$tpl->newBlock("tipo_doc");
		$tpl->assign("tipo_doc", $tipo_doc[$i]['tipo_doc']);
		$tpl->assign("descripcion", $tipo_doc[$i]['descripcion']);
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
	die;
}

$tpl->newBlock("listado");

$sql = "SELECT id_doc, num_cia, fecha, descripcion, tipo_doc FROM documentos";
$sql .= $_GET['num_cia'] > 0 || $_GET['tipo_doc'] > 0 || in_array($_SESSION['iduser'], $users) ? " WHERE" . ($_SESSION['tipo_usuario'] == 2 ? " num_cia BETWEEN 900 AND 998" . ($_GET['num_cia'] > 0 || $_GET['tipo_doc'] > 0 ? " AND" : "") : "") : "";
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" . ($_GET['tipo_doc'] > 0 ? " AND" : "") : "";
$sql .= $_GET['tipo_doc'] > 0 ? " tipo_doc = $_GET[tipo_doc]" : "";
$sql .= " ORDER BY num_cia, fecha";
$result = $db_scans->query($sql);

if ($result) {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];

			$tpl->newBlock("cia");
			$tpl->assign("ini", $i);
			$tpl->assign("num_cia", $num_cia);
			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
		}
		$tpl->newBlock("fila");
		$tpl->assign("cia.fin", $i);
		$tpl->assign("id", $result[$i]['id_doc']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tipo = $db_scans->query("SELECT descripcion FROM catalogo_documentos WHERE tipo_doc = {$result[$i]['tipo_doc']}");
		$tpl->assign("tipo", $tipo[0]['descripcion']);
		$tpl->assign("descripcion", $result[$i]['descripcion'] != "" ? $result[$i]['descripcion'] : "&nbsp;");
	}
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
$db->desconectar();
?>
