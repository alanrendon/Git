<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cambiarBanco':
			$sql = '
				UPDATE
					cometra
				SET
					banco = ' . $_REQUEST['banco'] . '
				WHERE
					tsend IS NULL
			';
			$db->query($sql);
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

				if (isset($_REQUEST['primaria']) || isset($_REQUEST['cias'])) {
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
											num_cia IN (' . $_REQUEST['num_cia'] . (isset($_REQUEST['cias']) ? ', ' . $_REQUEST['cias'] : '') . ')
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

		case 'listado':
			$sql = '
				SELECT
					CASE
						WHEN c.banco IS NOT NULL THEN
							c.banco
						ELSE
							0
					END
						AS
							banco,
					c.comprobante,
					/*cc.num_cia_primaria*/
					CASE
						WHEN (
							SELECT
								num_cia_pri
							FROM
								cometra_prioridades
							WHERE
								num_cia = cc.num_cia_primaria
								AND num_cia_pri IN (
									SELECT
										num_cia
									FROM
										cometra
									WHERE
										comprobante = c.comprobante
								)
							LIMIT
								1
						) > 0 THEN
							(
								SELECT
									num_cia_pri
								FROM
									cometra_prioridades
								WHERE
									num_cia = cc.num_cia_primaria
									AND num_cia_pri IN (
										SELECT
											num_cia
										FROM
											cometra
										WHERE
											comprobante = c.comprobante
									)
								LIMIT
									1
							)
						ELSE
							cc.num_cia_primaria
					END
						AS "num_cia_primaria",
					ccp.nombre
						AS
							nombre_cia_primaria,
					c.num_cia,
					cc.nombre
						AS
							nombre_cia,
					fecha,
					EXTRACT(day from fecha)
						AS
							dia,
					cod_mov,
					importe
				FROM
						cometra c
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						catalogo_companias ccp
							ON
								(
									ccp.num_cia = cc.num_cia_primaria
								)
				WHERE
					tsend IS NULL
				ORDER BY
					c.comprobante,
					c.fecha,
					/*cc.num_cia_primaria*/"num_cia_primaria",
					c.num_cia
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array();
				$comprobante = NULL;
				$cont = 0;
				foreach ($result as $r) {
					if ($comprobante != $r['comprobante']) {
						if ($comprobante != NULL) {
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont]['num_cia'] = $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'];
						$data[$cont]['nombre_cia'] = $r['nombre_cia_primaria'];
						$data[$cont]['comprobante'] = $comprobante;
						$data[$cont]['total'] = 0;
					}

					$data[$cont]['depositos'][] = array(
						'dia' => $r['dia'],
						'num_cia' => $r['num_cia'],
						'cod_mov' => $r['cod_mov'],
						'importe' => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];

					$tmp = explode('/', $r['fecha']);

					$data[$cont]['timestamp'] = mktime(0, 0, 0, $tmp[1], $tmp[0], $tmp[2]);
				}

				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['timestamp'] == $b['timestamp']) {
							if ($a['comprobante'] == $b['comprobante']) {
								return 0;
							}
							else {
								return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
							}
						}
						else {
							return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}

				usort($data, 'cmp');

				$tpl = new TemplatePower('plantillas/cometra/DepositosCometraListado.tpl');
				$tpl->prepare();

				$tpl->assign($result[0]['banco'], ' selected');

				$color = FALSE;
				foreach ($data as $info) {
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('comprobante', $info['comprobante']);
					$tpl->assign('num_cia', $info['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($info['nombre_cia']));
					$tpl->assign('fecha', $info['fecha']);
					$tpl->assign('total', number_format($info['total'], 2, '.', ','));

					$depositos = array();
					foreach ($info['depositos'] as $d) {
						switch ($d['cod_mov']) {
							case 1:
								if ($d['num_cia'] <= 300) {
									$tipo = 'Pan';
								}
								else if ($d['num_cia'] >= 900) {
									$tipo = 'Zapateria';
								}
							break;

							case 2:
								$tipo = 'Renta';
							break;

							case 7:
								$tipo = 'Pago falt.';
							break;

							case 16:
								$tipo = 'Pollos';
							break;

							case 13:
								$tipo = 'Sobrante';
							break;

							case 19:
								$tipo = 'Faltante';
							break;

							case 48:
								$tipo = 'Falso';
							break;

							case 99:
								$tipo = 'Cheque';
							break;

							case 21:
								$tipo = 'Canc. dep.';
							break;

							default:
								$tipo = 'Error cod.';
						}

						$depositos[] = '<tr><td>D&iacute;a: ' . $d['dia'] . '&nbsp;</td><td>Cia: ' . $d['num_cia'] . '&nbsp;</td><td>' . $tipo . '&nbsp;</td><td align=&quot;right&quot; class=&quot;bold ' . ($d['importe'] > 0 ? 'blue' : 'red') . '&quot;>' . number_format($d['importe'], 2, '.', ',') . '</td></tr>';
					}
					$tpl->assign('depositos', '<table style=&quot;border-collapse:collapse;&quot;>' . implode('', $depositos) . '<tr style=&quot;border-top:solid 1px #000;&quot;><td align=&quot;right&quot; colspan=&quot;3&quot; class=&quot;bold&quot;>Total&nbsp;</td><td class=&quot;bold ' . ($info['total'] > 0 ? 'blue' : 'red') . '&quot;>' . number_format($info['total'], 2, '.', ',') . '</td></tr></table>');

					$color = !$color;
				}

				echo $tpl->getOutputContent();
			}
			else {
				$tpl = new TemplatePower('plantillas/cometra/DepositosCometraVacio.tpl');
				$tpl->prepare();

				echo $tpl->getOutputContent();
			}
		break;

		case 'nuevo':
			$sql = '
				UPDATE
					cometra
				SET
					iduser_end = ' . $_SESSION['iduser'] . ',
					tsend = now()
				WHERE
					tsend IS NULL
			';

			$db->query($sql);
		break;

		case 'modComprobante':
			$tpl = new TemplatePower('plantillas/cometra/DepositosCometraModificarComprobante.tpl');
			$tpl->prepare();

			$tpl->assign('comprobante', $_REQUEST['comprobante']);

			echo $tpl->getOutputContent();
		break;

		case 'actComprobante':
			$sql = '
				UPDATE
					cometra
				SET
					comprobante = ' . $_REQUEST['comprobante_nuevo'] . '
				WHERE
						comprobante = ' . $_REQUEST['comprobante_actual'] . '
					AND
						tsend IS NULL
			';
			$db->query($sql);

			echo $_REQUEST['comprobante_nuevo'];
		break;

		case 'modDepositos':
			$sql = '
				SELECT
					id,
					comprobante,
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					cod_mov,
					CASE
						WHEN cod_mov = 1 AND num_cia BETWEEN 1 AND 300 THEN
							\'PAN\'
						WHEN cod_mov = 1 AND num_cia BETWEEN 900 AND 998 THEN
							\'ZAPATERIA\'
						WHEN cod_mov = 2 THEN
							\'RENTA\'
						WHEN cod_mov = 7 THEN
							\'PAGO FALT.\'
						WHEN cod_mov = 16 THEN
							\'POLLOS\'
						WHEN cod_mov = 13 THEN
							\'SOBRANTE\'
						WHEN cod_mov = 19 THEN
							\'FALTANTE\'
						WHEN cod_mov = 48 THEN
							\'FALSO\'
						WHEN cod_mov = 99 THEN
							\'CHEQUE\'
						WHEN cod_mov = 21 THEN
							\'CANC. DEP.\'
						ELSE
							\'ERROR COD.\'
					END
						AS
							descripcion,
					fecha,
					concepto,
					importe
				FROM
						cometra c
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
						comprobante = ' . $_REQUEST['comprobante'] . '
					AND
						tsend IS NULL
				ORDER BY
					fecha,
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/cometra/DepositosCometraModificarDepositos.tpl');
			$tpl->prepare();

			$tpl->assign('comprobante', $_REQUEST['comprobante']);

			$total = 0;
			foreach ($result as $r) {
				$tpl->newBlock('row');
				$tpl->assign('id', $r['id']);
				$tpl->assign('num_cia', $r['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
				$tpl->assign('cod_mov', $r['cod_mov']);
				$tpl->assign('descripcion', $r['descripcion']);
				$tpl->assign('fecha', $r['fecha']);
				$tpl->assign('concepto', $r['concepto']);
				$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));

				$total += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];
			}
			$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));

			$rows = 3;
			for ($i = 0; $i < $rows; $i++) {
				$tpl->newBlock('row');
			}

			$tpl->assign('_ROOT.fecha', $r['fecha']);

			echo $tpl->getOutputContent();
		break;

		case 'actDepositos':
			/*
			* [28-May-2012] Obtener registros actuales
			*/

			$sql = '
				SELECT
					*
				FROM
					cometra
				WHERE
					comprobante = ' . $_REQUEST['comprobante'] . '
				ORDER BY
					id
			';

			$result = $db->query($sql);

			$sql = '
				SELECT
					tipo_comprobante
				FROM
					cometra
				WHERE
					comprobante = ' . $_REQUEST['comprobante'] . '
				LIMIT
					1
			';

			$tmp = $db->query($sql);

			$tipo_comprobante = $tmp ? $tmp[0]['tipo_comprobante'] : 2;

			$sql = '';

			$faltantes = array();

			$cheques = array();

			$cias = array();

			foreach ($_REQUEST['id'] as $i => $id) {
				if ($id > 0
					&& $_REQUEST['num_cia'][$i] > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['cod_mov'][$i] > 0
					&& get_val($_REQUEST['importe'][$i]) > 0) {
					$sql .= '
						UPDATE
							cometra
						SET
							num_cia = ' . $_REQUEST['num_cia'][$i] . ',
							fecha = \'' . $_REQUEST['fecha'][$i] . '\',
							cod_mov = ' . $_REQUEST['cod_mov'][$i] . ',
							concepto = \'' . $_REQUEST['concepto'][$i] . '\',
							importe = ' . get_val($_REQUEST['importe'][$i]) . ',
							iduser_mod = ' . $_SESSION['iduser'] . ',
							tsmod = now()
						WHERE
							id = ' . $id . '
					' . ";\n";
				}
				else if ($id > 0
					&& (
							$_REQUEST['num_cia'][$i] == ''
							|| $_REQUEST['fecha'][$i] == ''
							|| $_REQUEST['cod_mov'][$i] == 0
							|| get_val($_REQUEST['importe'][$i]) == 0
						)) {
					$sql .= '
						DELETE FROM
							cometra
						WHERE
							id = ' . $id . '
					' . ";\n";
				}
				else if ($id == ''
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
									' . $tipo_comprobante . ',
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
				}

				if ($_REQUEST['num_cia'][$i] > 0) {
					$cias[] = $_REQUEST['num_cia'][$i];
				}

				if (in_array($_REQUEST['cod_mov'][$i], array(13, 19, 48))) {
					$faltantes[] = array(
						'comprobante' => $_REQUEST['comprobante'],
						'num_cia'     => $_REQUEST['num_cia'][$i],
						'fecha'       => $_REQUEST['fecha'][$i],
						'cod_mov'     => $_REQUEST['cod_mov'][$i],
						'concepto'    => $_REQUEST['concepto'][$i],
						'importe'     => get_val($_REQUEST['importe'][$i])
					);
				}
				else if (in_array($_REQUEST['cod_mov'][$i], array(99))) {
					$cheques[] = array(
						'comprobante' => $_REQUEST['comprobante'],
						'num_cia'     => $_REQUEST['num_cia'][$i],
						'fecha'       => $_REQUEST['fecha'][$i],
						'cod_mov'     => $_REQUEST['cod_mov'][$i],
						'concepto'    => $_REQUEST['concepto'][$i],
						'importe'     => get_val($_REQUEST['importe'][$i])
					);
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
			}

			if ($faltantes) {
				$sql = '
					SELECT
						num_cia,
						nombre
							AS nombre_cia,
						nombre_corto,
						email,
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
					LIMIT
						1
				';

				$tmp = $db->query($sql);

				$email = $tmp ? $tmp[0] : FALSE;

				if ($email && trim($email['email']) != '') {
					include_once('includes/phpmailer/class.phpmailer.php');

					foreach ($faltantes as $faltante) {
						$mail = new PHPMailer();

						$mail->IsSMTP();
						$mail->Host = 'mail.lecaroz.com';
						$mail->Port = 587;
						$mail->SMTPAuth = true;
						$mail->Username = 'noreply@lecaroz.com';
						$mail->Password = 'L3c4r0z*';

						$mail->From = 'noreply@lecaroz.com';
						$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

						$mail->AddAddress($email['email']);

						$mail->AddBCC('miguelrebuelta@lecaroz.com');

						if ($email['num_cia'] >= 900) {
							$mail->AddBCC('irigoyenramon@hotmail.com');
						}

						$mail->Subject = utf8_decode('[' . $email['num_cia'] . ' ' . utf8_encode($email['nombre_corto']) . '] RECORDATORIO IMPORTANTE');

						$tpl = new TemplatePower('plantillas/cometra/DepositosCometraEmailFaltante.tpl');
						$tpl->prepare();

						$tpl->assign('num_cia', $email['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($email['nombre_cia']));

						$tpl->assign('encargado', utf8_encode($email['encargado']));

						$tpl->assign('fecha', $faltante['fecha']);
						$tpl->assign('comprobante', $faltante['comprobante']);

						switch ($faltante['cod_mov']) {
							case 13:
								$tpl->assign('color_tipo', '00C');
								$tpl->assign('tipo', 'SOBRANTE');
							break;

							case 19:
								$tpl->assign('color_tipo', 'C00');
								$tpl->assign('tipo', 'FALTANTE');
							break;

							case 48:
								$tpl->assign('color_tipo', 'C00');
								$tpl->assign('tipo', 'FALTANTE (FALSO)');
							break;
						}

						$tpl->assign('importe', number_format($faltante['importe'], 2));

						$total = 0;

						foreach ($_REQUEST['id'] as $i => $id) {
							if (get_val($_REQUEST['importe'][$i]) > 0) {
								$tpl->newBlock('row');

								if (in_array($_REQUEST['cod_mov'][$i], array(13, 19, 48))) {
									switch ($_REQUEST['cod_mov'][$i]) {
										case 13:
											$tpl->assign('row_color', ' style="background-color:#6CF;"');
										break;

										case 19:
											$tpl->assign('row_color', ' style="background-color:#F30;"');
										break;

										case 48:
											$tpl->assign('row_color', ' style="background-color:#F30;"');
										break;
									}
								}

								$tpl->assign('num_cia', $_REQUEST['num_cia'][$i]);
								$tpl->assign('nombre_cia', $_REQUEST['nombre_cia'][$i]);
								$tpl->assign('cod_mov', $_REQUEST['cod_mov'][$i]);
								$tpl->assign('descripcion', $_REQUEST['descripcion'][$i]);
								$tpl->assign('fecha', $_REQUEST['fecha'][$i]);
								$tpl->assign('concepto', $_REQUEST['concepto'][$i]);
								$tpl->assign('importe', $_REQUEST['importe'][$i]);
							}
						}

						$tpl->assign('_ROOT.total', $_REQUEST['total']);

						$mail->Body = $tpl->getOutputContent();

						$mail->IsHTML(true);

						@$mail->Send();
					}
				}
			}

			if ($cheques) {
				$sql = '
					SELECT
						num_cia,
						nombre
							AS nombre_cia,
						nombre_corto,
						email,
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
					LIMIT
						1
				';

				$tmp = $db->query($sql);

				$email = $tmp ? $tmp[0] : FALSE;

				if ($email && trim($email['email']) != '') {
					include_once('includes/phpmailer/class.phpmailer.php');

					foreach ($faltantes as $faltante) {
						$mail = new PHPMailer();

						$mail->IsSMTP();
						$mail->Host = 'mail.lecaroz.com';
						$mail->Port = 587;
						$mail->SMTPAuth = true;
						$mail->Username = 'noreply+lecaroz.com';
						$mail->Password = 'L3c4r0z*';

						$mail->From = 'noreply@lecaroz.com';
						$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

						$mail->AddAddress($email['email']);

						$mail->AddBCC('miguelrebuelta@lecaroz.com');
						// $mail->AddBCC('carlos.candelario@lecaroz.com');

						if ($email['num_cia'] >= 900) {
							$mail->AddBCC('irigoyenramon@hotmail.com');
						}

						$mail->Subject = utf8_decode('[' . $email['num_cia'] . ' ' . utf8_encode($email['nombre_corto']) . '] RECORDATORIO IMPORTANTE');

						$tpl = new TemplatePower('plantillas/cometra/DepositosCometraEmailCheque.tpl');
						$tpl->prepare();

						$tpl->assign('num_cia', $email['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($email['nombre_cia']));

						$tpl->assign('encargado', utf8_encode($email['encargado']));

						$total = 0;

						foreach ($result as $rec) {
							$tpl->newBlock('row1');

							$tpl->assign('num_cia', $rec['num_cia']);
							$tpl->assign('nombre_cia', $rec['nombre_cia']);
							$tpl->assign('cod_mov', $rec['cod_mov']);
							$tpl->assign('descripcion', $rec['descripcion']);
							$tpl->assign('fecha', $rec['fecha']);
							$tpl->assign('concepto', $rec['concepto']);
							$tpl->assign('importe', $rec['importe']);

							$total += in_array($rec['cod_mov'], array(19, 48)) ? -$rec['importe'] : $rec['importe'];
						}

						$tpl->assign('_ROOT.total1', $total);

						foreach ($_REQUEST['id'] as $i => $id) {
							if (get_val($_REQUEST['importe'][$i]) > 0) {
								$tpl->newBlock('row2');

								$tpl->assign('num_cia', $_REQUEST['num_cia'][$i]);
								$tpl->assign('nombre_cia', $_REQUEST['nombre_cia'][$i]);
								$tpl->assign('cod_mov', $_REQUEST['cod_mov'][$i]);
								$tpl->assign('descripcion', $_REQUEST['descripcion'][$i]);
								$tpl->assign('fecha', $_REQUEST['fecha'][$i]);
								$tpl->assign('concepto', $_REQUEST['concepto'][$i]);
								$tpl->assign('importe', $_REQUEST['importe'][$i]);
							}
						}

						$tpl->assign('_ROOT.total2', $_REQUEST['total']);

						$mail->Body = $tpl->getOutputContent();

						$mail->IsHTML(true);

						@$mail->Send();
					}
				}
			}

			echo $_REQUEST['comprobante'];
		break;

		case 'borrarDepositos':
			$sql = '
				DELETE FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						comprobante = ' . $_REQUEST['comprobante'] . '
			';
			$db->query($sql);
		break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/DepositosCometra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
