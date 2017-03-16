<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		email,
		COALESCE((
			SELECT
				TRIM(nombre_fin)
			FROM
				encargados
			WHERE
				num_cia = cc.num_cia
			ORDER BY
				anio DESC,
				mes DESC
			LIMIT
				1
		), NULL)
			AS nombre
	FROM
		catalogo_companias cc
	WHERE
		email IS NOT NULL AND LENGTH(email) > 0

	UNION

	SELECT
		email,
		nombre_administrador
			AS nombre
	FROM
		catalogo_administradores
	WHERE
		email IS NOT NULL AND LENGTH(email) > 0
';

$query = $db->query($sql);

if ($query) {
	putenv('GDFONTPATH=' . realpath('.'));

	$fuente = 'arial';

	$query[] = array('email' => 'carlos.candelario@lecaroz.com', 'nombre' => 'CARLOS A. CANDELARIO CORONA');
	$query[] = array('email' => 'miguelrebuelta@lecaroz.com', 'nombre' => 'MIGUEL ANGEL REBUELTA DIEZ');
	$query[] = array('email' => 'marioal@yaznicotelecom.com', 'nombre' => 'MARIO EL PUÑALON REMILGOSO');

	foreach ($query as $rec) {
		if ($rec['email'] != '' && $rec['nombre'] != '') {
			// $rImg = ImageCreateFromJPEG('imagenes/tarjeta_navidad.jpg');
			// $rImg = ImageCreateFromJPEG('imagenes/Postal2.jpg');
			// $rImg = imagecreatefromjpeg('imagenes/tarjeta_navidad_2014.jpg');
			$rImg = imagecreatefromjpeg('imagenes/tarjeta_navidad_2015.jpg');

			// $color = imagecolorallocate($rImg, 0, 0, 0);
			// $color = imagecolorallocate($rImg, 255, 255, 255);
			$color = imagecolorallocate($rImg, 121, 67, 43);

			//imagestring($rImg, 5, 290, 100, urldecode($_REQUEST['nombre']), $color);
			// imagettftext($rImg, 12, 0, 290, 116, $color, $fuente, $rec['nombre']);
			// imagettftext($rImg, 12, 0, 291, 116, $color, $fuente, $rec['nombre']);
			// imagettftext($rImg, 20, 0, 200, 365, $color, $fuente, $rec['nombre']);
			// imagettftext($rImg, 20, 0, 201, 365, $color, $fuente, $rec['nombre']);
			// imagettftext($rImg, 20, 0, 30, 50, $color, $fuente, urldecode('PARA: ' . $rec['nombre']));
			// imagettftext($rImg, 20, 0, 31, 51, $color, $fuente, urldecode('PARA: ' . $rec['nombre']));

			$angle = 8.27;
			$size = 14;

			$bbox = imagettfbbox($size, $angle, $fuente, $rec['nombre']);

			$x = 277 - ($bbox[4] / 2);
			$y = 180 - ($bbox[5] / 2);

			imagettftext($rImg, $size, $angle, $x, $y, $color, $fuente, $rec['nombre']);

			imagejpeg($rImg, 'tmp/tarjeta-navidad.jpg');

			imagedestroy($rImg);

			$mail = new PHPMailer();

			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'mollendo@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S.A. de C.V.');

			$mail->AddAddress($rec['email']);
			// $mail->AddBCC('miguelrebuelta@lecaroz.com');
			// $mail->AddBCC('carlos.candelario@lecaroz.com');

			$mail->Subject = utf8_decode('¡¡Feliz Navidad y Prospero Año Nuevo 2016!!');

			$mail->AddEmbeddedImage('tmp/tarjeta-navidad.jpg', 'tarjeta-navidad', 'tarjeta-navidad.jpg');

			$mail->Body = '<img src="cid:tarjeta-navidad" alt="tarjeta-navidad" />';

			$mail->IsHTML(true);

			if(!$mail->Send()) {
				echo 'No se pudo enviar el correo a ' . $rec['email'] . '<br />';
			}
		}
	}
}
