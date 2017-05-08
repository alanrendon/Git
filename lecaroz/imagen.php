<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans");	// Coneccion a la base de datos de las imagenes

if (isset($_GET['id'])) {
	$sql = "SELECT imagen FROM imagenes WHERE id_img = $_GET[id]";
	$result = $db_scans->query($sql);
	$db_scans->desconectar();
	
	if ($result) {;
		// Decodificar datos de la imagen
		$imgData = pg_unescape_bytea($result[0]['imagen']);
		
		// Crear manejador de imagen
		$src = imagecreatefromstring($imgData);
		
		// Tipo de archivo (default JPEG)
		$tipo = isset($_GET['tipo']) && $_GET['tipo'] != "" ? $_GET['tipo'] : "jpeg";
		// Tamaño de la imagen
		$width = imagesx($src);		// Ancho
		$height = imagesy($src);	// Largo
		$aspect_ratio_v = $height / $width;	// Razón de aspecto
		$aspect_ratio_h = $width / $height;	// Razón de aspecto
		
		if (isset($_GET['width'])) {
			$sizeW = $_GET['width'] > 0 ? $_GET['width'] : $width;	// Nuevo ancho de la imagen
			$sizeH = isset($_GET['height']) && $_GET['height'] > 0 && $_GET['width'] > 0 ? $_GET['height'] : abs($_GET['width'] * $aspect_ratio_v);
		}
		else if (isset($_GET['tipo_hoja'])) {
			if ($_GET['tipo_hoja'] == 1) {	// Carta
				if (960 * $aspect_ratio_v <= 1250) {
					$sizeW = 960;
					$sizeH = 960 * $aspect_ratio_v;
				}
				else {
					$sizeW = 1250 * $aspect_ratio_h;
					$sizeH = 1250;
				}
			}
			else if ($_GET['tipo_hoja'] == 2) {	// Oficio
				if (960 * $aspect_ratio_v <= 1560) {
					$sizeW = 960;
					$sizeH = 960 * $aspect_ratio_v;
				}
				else {
					$sizeW = 1560 * $aspect_ratio_h;
					$sizeH = 1560;
				}
			}
			else {
				$sizeW = $width;
				$sizeH = $height;
			}
		}
		else {
			$sizeW = $width;
			$sizeH = $height;
		}
		
		// Crear imagen redimensionada
		$img = imagecreatetruecolor($sizeW, $sizeH);
		// Copiar imagen a su nuevo locación
		imagecopyresized($img, $src, 0, 0, 0, 0, $sizeW, $sizeH, $width, $height);
		
		//imagefilter($img, IMG_FILTER_NEGATE);
		
		// Mandar encabezado del tipo de archivo que se envia
		header("Content-Type: image/$tipo");
		
		// Mostrar imagen
		imagejpeg($img);
		
		// Destruir manejador de imagen
		imagedestroy($img);
	}
}

?>