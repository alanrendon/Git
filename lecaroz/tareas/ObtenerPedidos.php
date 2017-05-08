<?php
include('/var/www/lecaroz/includes/class.db.inc.php');
include('/var/www/lecaroz/includes/dbstatus.php');
include('/var/www/lecaroz/includes/phpmailer/class.phpmailer.php');
include('/var/www/lecaroz/includes/class.TemplatePower.inc.php');

$db = new DBclass($dsn, 'autocommit=yes,mostrar_errores=no,en_error_desconectar=no');

$rep_path = '/home/lecaroz/pedidos/';

function filter($file) {
	return strpos($file, '.sql') && strpos($file, '.error') === FALSE;
}

$files = array_filter(scandir($rep_path), 'filter');

if (count($files) > 0) {
	echo "\n" . '--- ESCANEO DE REPOSITORIO: ' . date('d/m/Y H:i:s') . ' ---';

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
				$mail->Username = 'wendy.barona@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'wendy.barona@lecaroz.com';
				$mail->FromName = 'Lecaroz :: Compras';

				$mail->AddBCC('wendy.barona@lecaroz.com');

				// $mail->AddBCC('sistemas@lecaroz.com');

				if (trim($result[0]['email']) != '') {
					$mail->AddAddress($result[0]['email']);
				}

				$mail->Subject = '[' . $result[0]['num_cia'] . ' ' . $result[0]['nombre_cia'] . utf8_decode('] Error en solicitud de avío (') . date('d/m/Y H:i:s') . ')';

				$tpl = new TemplatePower('/var/www/lecaroz/plantillas/ped/EmailPedidoError.tpl');
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
					pedidos_panaderia
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					tsreg IS NULL
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
			$mail->Username = 'daniela.requena@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'wendy.barona@lecaroz.com';
			$mail->FromName = 'Lecaroz :: Compras';

			$mail->AddBCC('wendy.barona@lecaroz.com');

			// $mail->AddBCC('sistemas@lecaroz.com');

			if (trim($result[0]['email']) != '') {
				$mail->AddAddress($result[0]['email']);
			}

			$mail->Subject = '[' . $result[0]['num_cia'] . ' ' . $result[0]['nombre_cia'] . utf8_decode('] Solicitud de avío recibido (') . $result[0]['fecha'] . ')';

			$tpl = new TemplatePower('/var/www/lecaroz/plantillas/ped/EmailPedidoRecibido.tpl');
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
		DELETE FROM
			pedidos_panaderia
		WHERE
			producto IS NULL
			OR TRIM(producto) = \'\'
			OR cantidad IS NULL
			OR cantidad = 0
	' . ";\n";

	$sql .= '
		DELETE FROM
			pedidos_panaderia
		WHERE
			tsreg IS NULL
			AND idpedidopanaderia NOT IN (
				SELECT
					MIN(idpedidopanaderia)
				FROM
					pedidos_panaderia
				WHERE
					tsreg IS NULL
					AND tsdel IS NULL
				GROUP BY
					num_cia,
					tipo,
					producto,
					cantidad
			)
	' . ";\n";

	$db->query($sql);
}

?>
