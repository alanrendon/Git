<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/adm/imageUpload.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['filename'])) {
	if (file_exists($_POST['filename'])) {
		// Preparar la imagen para insercion
		$imgData = pg_escape_bytea(file_get_contents($_POST['filename']));
		
		// Insertar la imagen en la base de datos
		$sql = "INSERT INTO image_scans (num_cia,img_data,fecha,descripcion,tipo_doc) VALUES ($_POST[num_cia],'$imgData',CURRENT_DATE,'$_POST[descripcion]',$_POST[tipo_doc])";
		$db->query($sql);
	}
	
	header("location: ./imageUpload.php");
	die;
}

if (isset($_FILES['userfile'])) {
	$fileName = "/tempImages/" . time();
	$fileTemp = $_FILES['userfile']['tmp_name'];
	
	if (move_uploaded_file($fileTemp, $fileName)) {
		$tpl->newBlock("guardar");
		
		$tpl->assign("fileName", $fileName);
		
		$tpl->printToScreen();
		
		die;
	}
	else {
		die("No se pudo subir la imagen");
	}
}

$tpl->newBlock("adquirir");
$tpl->printToScreen();
?>