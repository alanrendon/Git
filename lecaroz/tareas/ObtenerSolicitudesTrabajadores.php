<?php
include('/var/www/lecaroz/includes/class.db.inc.php');
include('/var/www/lecaroz/includes/dbstatus.php');
include('/var/www/lecaroz/includes/phpmailer/class.phpmailer.php');
include('/var/www/lecaroz/includes/class.TemplatePower.inc.php');

$db = new DBclass($dsn, 'autocommit=yes,mostrar_errores=no,en_error_desconectar=no');

$rep_path = '/home/lecaroz/altaimss/';

function filter($file) {
	return strpos($file, '.sql') && strpos($file, '.error') === FALSE;
}

$files = array_filter(scandir($rep_path), 'filter');

if (count($files) > 0) {
	echo '--- ESCANEO DE REPOSITORIO: ' . date('d/m/Y H:i:s') . ' ---';
	
	foreach ($files as $file) {
		$content = file_get_contents($rep_path . $file);
		
		if ($db->query($content) < 0) {
			echo "\n@@ " . $file . ': El archivo contiene errores y no pudo ser procesado: ' . utf8_decode($db->ultimo_error);
			
			rename($rep_path . $file, $rep_path . $file . '.error');
			
			$pieces = explode('-', $file);
			
			if ($pieces[0] > 0) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						email
					FROM
						catalogo_companias
					WHERE
						num_cia = ' . $pieces[0] . '
				';
				
				$result = $db->query($sql);
				
				$mail = new PHPMailer();
				
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'margarita.hernandez+lecaroz.com';
				$mail->Password = 'L3c4r0z*';
				
				$mail->From = 'margarita.hernandez@lecaroz.com';
				$mail->FromName = 'Lecaroz :: Recursos humanos';
				
				$mail->AddBCC('margarita.hernandez@lecaroz.com');
				
				$mail->AddBCC('carlos.candelario@lecaroz.com');
				
				if (trim($result[0]['email']) != '') {
					$mail->AddAddress($result[0]['email']);
				}
				
				$mail->Subject = '[' . $result[0]['num_cia'] . ' ' . $result[0]['nombre_cia'] . '] Error en solicitud de altas/bajas de trabajadores (' . date('d/m/Y H:i:s') . ')';
				
				$tpl = new TemplatePower('/var/www/lecaroz/plantillas/nom/EmailSolicitudError.tpl');
				$tpl->prepare();
				
				$tpl->assign('num_cia', $result[0]['num_cia']);
				$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
				
				$tpl->assign('fecha', date('d/m/Y H:i:s'));
				
				$mail->Body = $tpl->getOutputContent();
				
				$mail->IsHTML(true);
				
				if(!$mail->Send()) {
					//return $mail->ErrorInfo;
				}
			}
			
			continue;
		}
		else {
			echo "\n@@ " . $file . ': Archivo insertado';
			
			unlink($rep_path . $file);
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					email,
					DATE_TRUNC(\'SECOND\', tsins)
						AS fecha
				FROM
					altaimss_tmp
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					tsreg IS NULL
					AND tsdel IS NULL
				ORDER BY
					tsins DESC
				LIMIT
					1
			';
			
			$result = $db->query($sql);
			
			$mail = new PHPMailer();
			
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'margarita.hernandez+lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'margarita.hernandez@lecaroz.com';
			$mail->FromName = 'Lecaroz :: Compras';
			
			$mail->AddBCC('margarita.hernandez@lecaroz.com');
			
			$mail->AddBCC('carlos.candelario@lecaroz.com');
			
			if (trim($result[0]['email']) != '') {
				$mail->AddAddress($result[0]['email']);
			}
			
			$mail->Subject = '[' . $result[0]['num_cia'] . ' ' . $result[0]['nombre_cia'] . '] Solicitud de altas/bajas de trabajadores recibido (' . $result[0]['fecha'] . ')';
			
			$tpl = new TemplatePower('/var/www/lecaroz/plantillas/nom/EmailSolicitudRecibido.tpl');
			$tpl->prepare();
			
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
			
			$tpl->assign('fecha', $result[0]['fecha']);
			
			$mail->Body = $tpl->getOutputContent();
			
			$mail->IsHTML(true);
			
			if(!$mail->Send()) {
				//return $mail->ErrorInfo;
			}
		}
	}
	
	$sql = '
		UPDATE
			altaimss_tmp
		SET
			nombre = UPPER(TRIM(regexp_replace(nombre, \'\s+\', \' \', \'g\'))),
			appaterno = UPPER(TRIM(regexp_replace(appaterno, \'\s+\', \' \', \'g\'))),
			apmaterno = UPPER(TRIM(regexp_replace(apmaterno, \'\s+\', \' \', \'g\'))),
			observaciones = UPPER(TRIM(regexp_replace(observaciones, \'\s+\', \' \', \'g\')))
		WHERE
			tsreg IS NULL
			AND tsdel IS NULL
	';
	
	$db->query($sql);
}

?>
