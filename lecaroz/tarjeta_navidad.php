<?php

// $rImg = imagecreatefromjpeg('imagenes/tarjeta_navidad.jpg');
// $rImg = imagecreatefromjpeg('imagenes/tarjeta_navidad_2014.jpg');
$rImg = imagecreatefromjpeg('imagenes/tarjeta_navidad_2015.jpg');

// $color = imagecolorallocate($rImg, 0, 0, 0);
$color = imagecolorallocate($rImg, 121, 67, 43);

putenv('GDFONTPATH=' . realpath('.'));

$fuente = 'arial';

//imagestring($rImg, 5, 290, 100, urldecode($_REQUEST['nombre']), $color);
// imagettftext($rImg, 20, 0, 30, 50, $color, $fuente, urldecode('PARA: ' . $_REQUEST['nombre']));

$angle = 8.27;
$size = 14;

$bbox = imagettfbbox($size, $angle, $fuente, urldecode($_REQUEST['nombre']));

$x = 277 - ($bbox[4] / 2);
$y = 180 - ($bbox[5] / 2);

imagettftext($rImg, $size, $angle, $x, $y, $color, $fuente, urldecode($_REQUEST['nombre']));

header('Content-type: image/jpeg');

imagejpeg($rImg);
