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
			$sql = "SELECT
				c.comprobante,
				cc.num_cia_primaria,
				ccp.nombre AS nombre_cia_primaria,
				c.num_cia,
				cc.nombre AS nombre_cia,
				fecha,
				EXTRACT(day from fecha) AS dia,
				cod_mov,
				importe
			FROM
				cometra_tmp c
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_companias ccp ON (ccp.num_cia = cc.num_cia_primaria)
			WHERE
				tsreg IS NULL
				AND tsins::DATE >= NOW()::DATE - INTERVAL '5 DAYS'
				AND c.comprobante::VARCHAR NOT LIKE '35%'
				AND c.comprobante::VARCHAR NOT LIKE '28%'
				AND c.comprobante::VARCHAR NOT LIKE '908%'
				AND c.comprobante::VARCHAR NOT LIKE '910%'
				AND c.comprobante::VARCHAR NOT LIKE '911%'
				AND c.comprobante::VARCHAR NOT LIKE '916%'
				AND c.comprobante::VARCHAR NOT LIKE '917%'
				AND c.comprobante::VARCHAR NOT LIKE '918%'
			ORDER BY
				c.comprobante,
				c.fecha,
				cc.num_cia_primaria,
				c.num_cia";

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
						'importe' => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];

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

				// usort($data, 'cmp');

				$tpl = new TemplatePower('plantillas/cometra/DepositosServidorListado.tpl');
				$tpl->prepare();

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
								if ($info['num_cia'] <= 300) {
									$tipo = 'Pan';
								}
								else if ($info['num_cia'] >= 900) {
									$tipo = 'Zap';
								}
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

							default:
								$tipo = 'Pan';
						}

						$depositos[] = '<tr><td>D&iacute;a: ' . $d['dia'] . '&nbsp;</td><td>Cia: ' . $d['num_cia'] . '&nbsp;</td><td>' . $tipo . '&nbsp;</td><td align=&quot;right&quot; class=&quot;bold  ' . ($d['importe'] > 0 ? 'blue' : 'red') . '&quot;>' . number_format($d['importe'], 2, '.', ',') . '</td></tr>';
					}
					$tpl->assign('depositos', '<table style=&quot;border-collapse:collapse;&quot;>' . implode('', $depositos) . '<tr style=&quot;border-top:solid 1px #000;&quot;><td align=&quot;right&quot; colspan=&quot;3&quot; class=&quot;bold&quot;>Total&nbsp;</td><td class=&quot;bold' . ($info['total'] > 0 ? 'blue' : 'red') . '&quot;>' . number_format($info['total'], 2, '.', ',') . '</td></tr></table>');

					$color = !$color;
				}

				echo $tpl->getOutputContent();
			}
			else {
				$tpl = new TemplatePower('plantillas/cometra/DepositosServidorVacio.tpl');
				$tpl->prepare();

				echo $tpl->getOutputContent();
			}
		break;

		case 'modComprobante':
			$tpl = new TemplatePower('plantillas/cometra/DepositosServidorModificarComprobante.tpl');
			$tpl->prepare();

			$tpl->assign('comprobante', $_REQUEST['comprobante']);

			echo $tpl->getOutputContent();
		break;

		case 'actComprobante':
			$sql = '
				UPDATE
					cometra_tmp
				SET
					comprobante = ' . $_REQUEST['comprobante_nuevo'] . '
				WHERE
					comprobante = ' . $_REQUEST['comprobante_actual'] . '
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
						ELSE
							\'ERROR\'
					END
						AS
							descripcion,
					fecha,
					concepto,
					importe
				FROM
						cometra_tmp c
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
					comprobante = ' . $_REQUEST['comprobante'] . '
				ORDER BY
					fecha,
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/cometra/DepositosServidorModificarDepositos.tpl');
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

				$total += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
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
			$sql = '';

			foreach ($_REQUEST['id'] as $i => $id) {
				if ($id > 0
					&& $_REQUEST['num_cia'][$i] > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['cod_mov'][$i] > 0
					&& get_val($_REQUEST['importe'][$i]) > 0) {
					$sql .= '
						UPDATE
							cometra_tmp
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
							cometra_tmp
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
							cometra_tmp
								(
									comprobante,
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
			}

			if ($sql != '') {
				$db->query($sql);
			}

			echo $_REQUEST['comprobante'];
		break;

		case 'borrarDepositos':
			$sql = '
				DELETE FROM
					cometra_tmp
				WHERE
					comprobante = ' . $_REQUEST['comprobante'] . '
			';
			$db->query($sql);
		break;

		case 'actualizar':
			$cometra_db = new DBclass('mysql://ramses:r29a76@192.168.1.250:3306/cometra', 'autocommit=yes');

			$sql = '
				SELECT
					`id`,
					`comprobante`,
					`cia` AS `num_cia`,
					`fechamov` AS `fecha`,
					`concepto`,
					`tipomov` AS `cod_mov`,
					`importe`,
					\'' . $_SESSION['iduser'] . '\' AS `iduser_ins`
				FROM
					`captura_cometra_tmp`
				ORDER BY
					`num_cia`
			';

			$result = $cometra_db->query($sql);

			if ($result) {
				$limit_day = 6;
				$days_offset = 10;
				$day = 86400;

				$limit_date_min = date('j') <= $limit_day ? mktime(0, 0, 0, date('n'), 0, date('Y')) - $day * $days_offset : mktime(0, 0, 0, date('n'), 1, date('Y'));
				$limit_date_max = mktime(0, 0, 0, date('n'), date('j'), date('Y'));

				$default_date = date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y')));


				$ids = array();
				foreach ($result as $index => $row) {
					if (strtotime($row['fecha']) >= $limit_date_min && strtotime($row['fecha']) <= $limit_date_max) {
						$result[$index]['fecha'] = date('d/m/Y', strtotime($row['fecha']));
					}
					else {
						$result[$index]['fecha'] = date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y')));
					}
					$ids[] = $row['id'];
					unset($result[$index]['id']);
				}

				$sql = '';
				foreach ($result as $row) {
					$sql .= '
						INSERT INTO
							cometra_tmp
								(
									' . implode(', ', array_keys($row)) . '
								)
							VALUES
								(
									\'' . implode('\', \'', $row) . '\'
								)
					' . ";\n";
				}

				/*
				@ Borrar duplicados
				*/
				$sql .= '
					DELETE FROM
						cometra_tmp
					WHERE
						id
							NOT IN
								(
									SELECT
										MIN(id)
									FROM
										cometra_tmp
									GROUP BY
										comprobante,
										fecha,
										num_cia,
										cod_mov,
										importe
								)
				' . ";\n";

				/*
				@ Borrar todos los registros con 1 semana de antigüedad
				*/
				$sql .= '
					DELETE FROM
						cometra_tmp
					WHERE
						tsins < now() - interval \'7 days\'
				' . ";\n";

				$db->query($sql);

				$sql = '
					DELETE FROM
						`captura_cometra_tmp`
					WHERE
						`id`
							IN
								(
									' . implode(', ', $ids) . '
								)
				';

				$cometra_db->query($sql);
			}
		break;

		case 'registrar':
			$sql = '
				INSERT INTO
					cometra
						(
							comprobante,
							tipo_comprobante,
							num_cia,
							fecha,
							banco,
							concepto,
							cod_mov,
							importe,
							iduser_ins
						)
					SELECT
						comprobante,
						1,
						num_cia,
						fecha,
						banco,
						concepto,
						cod_mov,
						importe,
						iduser_ins
					FROM
						cometra_tmp
					WHERE
						comprobante IN (' . implode(', ', $_REQUEST['comprobante']) . ')
						AND tsreg IS NULL
			' . ";\n";

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

			$sql .= '
				UPDATE
					cometra_tmp
				SET
					tsreg = now(),
					iduser_reg = ' . $_SESSION['iduser'] . '
				WHERE
					comprobante IN (' . implode(', ', $_REQUEST['comprobante']) . ')
					AND tsreg IS NULL
			' . ";\n";

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/DepositosServidor.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
