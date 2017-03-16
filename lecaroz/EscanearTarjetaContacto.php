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
$tpl = new TemplatePower('smarty/templates/EscanearTarjetaContacto.tpl');
$tpl->prepare();

if (isset($_GET['accion']) && $_GET['accion'] == "upload") {
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['tarjeta']['tmp_name']));
	
	// Borrar cualquier imagen temporal que se encuetre en la base de datos
	$sql = "DELETE FROM img_tar_tmp;\n";
	// Insertar la imagen en la base de datos
	$sql .= "INSERT INTO img_tar_tmp (img) VALUES ('$imgData');\n";
	die($db_scans->query($sql));
}

if (isset($_GET['accion']) && $_GET['accion'] == "cerrar") {	
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$tpl->newBlock('scan');

$tpl->printToScreen();
?>