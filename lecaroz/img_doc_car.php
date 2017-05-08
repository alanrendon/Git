<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

// Coneccion a la base de datos de las imagenes
$db = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");

$sql = "SELECT imagen FROM img_doc_car WHERE id = $_GET[id]";
$result = $db->query($sql);
$db->desconectar();

if ($result) {
	// Decodificar datos de la imagen
	$imgData = pg_unescape_bytea($result[0]['imagen']);
	
	// Crear manejador de imagen
	$src = imagecreatefromstring($imgData);
	
	// Tipo de archivo (default JPEG)
	$tipo = isset($_GET['tipo']) && $_GET['tipo'] != '' ? $_GET['tipo'] : 'jpeg';
	// Tamaño de la imagen
	$width = imagesx($src);		// Ancho
	$height = imagesy($src);	// Largo
	$aspect_ratio = $height / $width;	// Razón de aspecto
	
	// Nuevo ancho de la imagen
	$sizeW = isset($_GET['width']) && $_GET['width'] > 0 ? $_GET['width'] : $width;
	$sizeH = isset($_GET['height']) && $_GET['height'] > 0 ? $_GET['height'] : (isset($_GET['width']) && $_GET['width'] > 0 ? abs($_GET['width'] * $aspect_ratio) : $height);
	
	// Crear imagen redimensionada
	$img = imagecreatetruecolor($sizeW, $sizeH);
	// Copiar imagen a su nuevo locación
	imagecopyresampled($img, $src, 0, 0, 0, 0, $sizeW, $sizeH, $width, $height);
	
	// Mandar encabezado del tipo de archivo que se envia
	header("Content-Type: image/$tipo");
	
	// Mostrar imagen
	imagejpeg($img);
	
	// Destruir manejador de imagen
	imagedestroy($img);
}
?>