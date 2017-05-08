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

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/doc/impresion_doc.tpl" );
$tpl->prepare();

if (isset($_POST['id_doc'])) {
	$sql = "SELECT id_img, tipo_hoja FROM imagenes LEFT JOIN documentos USING (id_doc) LEFT JOIN catalogo_documentos USING (tipo_doc) WHERE id_doc IN (";
	for ($i = 0; $i < count($_POST['id_doc']); $i++)
		$sql .= $_POST['id_doc'][$i] . ($i < count($_POST['id_doc']) - 1 ? "," : ")");
	$sql .= " ORDER BY id_doc, indice";
	
	$result = $db_scans->query($sql);
}
else if (isset($_POST['id_img'])) {
	$result = array();
	
	for ($i = 0; $i < count($_POST['id_img']); $i++) {
		$result[$i]['id_img'] = $_POST['id_img'][$i];
		$result[$i]['tipo_hoja'] = 2;
	}
}

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("imagen");
	$tpl->assign("id", $result[$i]['id_img']);
	$tpl->assign("tipo_hoja", $result[$i]['tipo_hoja']);
}

$tpl->printToScreen();
$db_scans->desconectar();
?>