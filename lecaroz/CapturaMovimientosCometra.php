<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'validarComprobante':
			$sql = '
				SELECT
					comprobante
				FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						comprobante = ' . $_REQUEST['comprobante'] . '
				LIMIT 1
			';
			$result = $db->query($sql);

			if ($result) {
				echo -1;
			}
		break;

		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				$data['nombre_cia'] = $result[0]['nombre_cia'];

				if (isset($_REQUEST['primaria'])) {
					$sql = '
						SELECT
							num_cia
						FROM
							catalogo_companias
						WHERE
							num_cia_primaria
								IN
									(
										SELECT
											num_cia_primaria
										FROM
											catalogo_companias
										WHERE
											num_cia = ' . $_REQUEST['num_cia'] . '
									)
						ORDER BY
							num_cia
					';
					$tmp = $db->query($sql);

					foreach ($tmp as $t) {
						$data['homoclaves'][] = $t['num_cia'];
					}
				}

				echo json_encode($data);
			}
			else {
				echo -1;
			}
		break;

		case 'obtenerFecha':
			$sql = '
				SELECT
					(now() - interval \'2 days\')::date
						AS
							fecha
			';
			$result = $db->query($sql);

			echo $result[0]['fecha'];
		break;

		case 'validarFecha':
			$sql = '
				SELECT
					\'' . $_REQUEST['fecha'] . '\' BETWEEN now() - interval \'15 days\' AND now()
						AS
							status
			';

			$result = $db->query($sql);

			if ($result[0]['status'] == 'f') {
				echo -1;
			}
		break;

		case 'registrar':
			$sql = '';

			$cias = array();

			foreach ($_REQUEST['num_cia'] as $i => $id) {
				if ($_REQUEST['comprobante'] > 0
					&& $_REQUEST['num_cia'][$i] > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['cod_mov'][$i] > 0
					&& get_val($_REQUEST['importe'][$i]) > 0) {
					$sql .= '
						INSERT INTO
							cometra
								(
									comprobante,
									tipo_comprobante,
									num_cia,
									fecha,
									cod_mov,
									concepto,
									importe,
									iduser_ins,
									tsins,
									iduser_mod,
									tsmod
								)
							VALUES
								(
									' . $_REQUEST['comprobante'] . ',
									2,
									' . $_REQUEST['num_cia'][$i] . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									' . $_REQUEST['cod_mov'][$i] . ',
									\'' . $_REQUEST['concepto'][$i] . '\',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . $_SESSION['iduser'] . ',
									now(),
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";

					$cias[] = $_REQUEST['num_cia'][$i];
				}
			}

			$sql .= '
				UPDATE
					cometra
				SET
					banco = actual.banco
				FROM
					(
						SELECT
							banco
						FROM
							cometra
						WHERE
								tsend IS NULL
							AND
								banco IS NOT NULL
						LIMIT
							1
					)
						actual
				WHERE
					cometra.banco IS NULL
			' . ";\n";

			if ($sql != '') {
				$db->query($sql);

				$sql = '
					SELECT
						cc.num_cia,
						cc.email
							AS email_cia,
						ca.email
							AS email_adm,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado
					FROM
						catalogo_companias cc
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						num_cia IN (
							SELECT
								num_cia_primaria
							FROM
								catalogo_companias
							WHERE
								num_cia IN (' . implode(', ', $cias) . ')
							GROUP BY
								num_cia_primaria
						)
					ORDER BY
						num_cia
				';

				$emails = $db->query($sql);

				if ($emails) {
					include_once('includes/phpmailer/class.phpmailer.php');

					foreach ($emails as $email) {
						if (trim($email['email_cia']) != '') {
							$mail = new PHPMailer();

							$mail->IsSMTP();
							$mail->Host = 'mail.lecaroz.com';
							$mail->Port = 587;
							$mail->SMTPAuth = true;
							$mail->Username = 'mollendo@lecaroz.com';
							$mail->Password = 'L3c4r0z*';

							$mail->From = 'mollendo@lecaroz.com';
							$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

							$mail->AddAddress($email['email_cia']);

							if (trim($email['email_adm']) != '') {
								$mail->AddAddress($email['email_adm']);
							}

							$mail->AddBCC('miguelrebuelta@lecaroz.com');

							if ($email['num_cia'] >= 900) {
								$mail->AddBCC('irigoyenramon@hotmail.com');
							}

							// $mail->AddBCC('sistemas@lecaroz.com');

							$mail->Subject = utf8_decode('RECORDATORIO IMPORTANTE');

							$tpl = new TemplatePower('plantillas/cometra/CapturaMovimientosCometraEmail.tpl');
							$tpl->prepare();

							$tpl->assign('encargado', utf8_encode($email['encargado']));

							$sql = '
								SELECT
									num_cia,
									nombre_corto
										AS nombre_cia,
									cod_mov,
									CASE
										WHEN banco = 1 THEN
											(
												SELECT
													descripcion
												FROM
													catalogo_mov_bancos
												WHERE
													cod_mov = c.cod_mov
												LIMIT
													1
											)
										WHEN banco = 2 THEN
											(
												SELECT
													descripcion
												FROM
													catalogo_mov_santander
												WHERE
													cod_mov = c.cod_mov
												LIMIT
													1
											)
									END
										AS descripcion,
									fecha,
									concepto,
									importe
								FROM
									cometra c
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
								WHERE
									comprobante = ' . $_REQUEST['comprobante'] . '
								ORDER BY
									fecha,
									num_cia
							';

							$result = $db->query($sql);

							$total = 0;

							foreach ($result as $rec) {
								$tpl->newBlock('row');
								$tpl->assign('num_cia', $rec['num_cia']);
								$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
								$tpl->assign('cod_mov', $rec['cod_mov']);
								$tpl->assign('descripcion', utf8_encode($rec['descripcion']));
								$tpl->assign('fecha', $rec['fecha']);
								$tpl->assign('concepto', utf8_encode($rec['concepto']));
								$tpl->assign('importe', number_format((in_array($rec['cod_mov'], array(19, 48)) ? -1 : 1) * $rec['importe'], 2));

								$total += (in_array($rec['cod_mov'], array(19, 48)) ? -1 : 1) * $rec['importe'];
							}

							$tpl->assign('_ROOT.total', number_format($total, 2));

							$mail->Body = $tpl->getOutputContent();

							$mail->IsHTML(true);

							if(!$mail->Send()) {
								echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
							} else {
								echo 'Correo enviado a todos los destinatarios';
							}
						}
					}
				}
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/CapturaMovimientosCometra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		CASE
			WHEN banco = 1 THEN
				\'BANORTE\'
			WHEN banco = 2 THEN
				\'SANTANDER\'
			ELSE
				\'SIN DEFINIR\'
		END
			AS
				nombre_banco
	FROM
		cometra
	WHERE
			tsend IS NULL
		AND
			banco IS NOT NULL
	LIMIT
		1
';
$banco = $db->query($sql);

if ($banco)  {
	$tpl->assign('nombre_banco', $banco[0]['nombre_banco']);
}
else {
	$tpl->assign('nombre_banco', 'SIN DEFINIR');
}

$maxRows = 15;

for ($i = 0; $i < $maxRows; $i++) {
	$tpl->newBlock('row');
}

$tpl->printToScreen();
?>
