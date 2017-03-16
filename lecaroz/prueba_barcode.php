<?php
require_once 'Image/Barcode.php';
$barcode = Image_Barcode::draw('2200', 'code128', 'png', FALSE);

//header("Content-Type: image/png");

// Mostrar imagen
imagepng($barcode, 'barcodes/barcodeprueba.png');

// Destruir manejador de imagen
imagedestroy($barcode);
?>