<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
);

$__meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporteNormal':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$dias_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$cias = array();
			/*if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
			}*/
			for ($i = 0; $i < 30; $i++) {
				if (isset($_REQUEST['cia' . $i]) && $_REQUEST['cia' . $i] > 0) {
					$cias[] = $_POST['cia' . $i];
				}
			}

			$condiciones = array();

			$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : '(num_cia BETWEEN 1 AND 599 OR num_cia BETWEEN 701 AND 799)';

			if ($cias) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['idadmin'];
			}

			$sql = '
				SELECT
					num_cia,
					num_cia_primaria,
					nombre,
					nombre_corto,
					turno_cometra
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia_primaria,
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensualNormal.tpl');
			$tpl->prepare();

			if ($result) {
				$data = array();

				foreach ($result as $rec) {
					$data[intval($rec['num_cia_primaria'])][intval($rec['num_cia'])] = array(
						'nombre'     => $rec['nombre'],
						'alias'      => $rec['nombre_corto'],
						'dias'       => range(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0)),
						'cometra'    => $rec['turno_cometra'],
						'status'     => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), FALSE),
						'efectivo'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'deposito'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'mayoreo'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'oficina'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'faltante'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'diferencia' => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'total'      => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'totales'    => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'faltante'   => 0,
							'diferencia' => 0,
							'total'      => 0
						),
						'promedios'  => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'total'      => 0
						)
					);
				}

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						(efe AND exp AND pro AND gas AND pas)
							AS status,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_panaderias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						/*AND (efe AND exp AND pro AND gas AND pas) = TRUE*/

					UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						TRUE,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_companias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						(venta > 0),
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_zapaterias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND venta > 0

					/*UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						FALSE,
						ROUND(importe::NUMERIC, 2)
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND (num_cia, fecha) NOT IN (
							SELECT
								num_cia,
								fecha
							FROM
								total_panaderias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND (efe AND exp AND pro AND gas AND pas) = TRUE

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_companias
							WHERE
								' . implode(' AND ', $condiciones) . '

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_zapaterias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND venta > 0
						)*/

					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					$importes_agosto_2012 = array(
						21  => 10000,
						31  => 4000,
						32  => 3000,
						34  => 5000,
						49  => 3000,
						73  => 3000,
						79  => 2000,
						121 => 5000
					);

					$sql = '
						SELECT
							num_cia,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							cometra
						WHERE
							comprobante IN (41355658, 40759126)
						ORDER BY
							num_cia,
							fecha,
							importe
					';

					$tmp = $db->query($sql);

					$importes_septiembre_2012 = array();

					if ($tmp) {
						foreach ($tmp as $t) {
							$importes_septiembre_2012[$t['num_cia']][$t['dia']] = $t['importe'];
						}
					}

					foreach ($result as $rec) {
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['efectivo']);
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = $rec['status'] == 't' ? 1 : -1;

						/*
						@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
						@
						@ 21 - 10,000.00
						@ 31 -  4,000.00
						@ 32 -  3,000.00
						@ 34 -  5,000.00
						@ 49 -  3,000.00
						@ 73 -  3,000.00
						@ 79 -  2,000.00
						@ 121 - 5,000.00
						*/

						if (in_array($rec['num_cia'], array(
							21,
							31,
							32,
							34,
							49,
							73,
							79,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 8
							&& $rec['dia'] < 31) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_agosto_2012[$rec['num_cia']];
						}

						/*
						@ [04-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
						@
						*/

						if (in_array($rec['num_cia'], array(
							31,
							32,
							33,
							34,
							73,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 9
							&& isset($importes_septiembre_2012[$rec['num_cia']][$rec['dia']])) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_septiembre_2012[$rec['num_cia']][$rec['dia']];
						}

						/*
						@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 10) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 11) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							49,
							57,
							67,
							34
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							32
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10
							&& $rec['dia'] <= 11) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							20,
							50
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10
							&& $rec['dia'] <= 21) {
								$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
							}
						}
				}

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						ROUND(importe::NUMERIC, 2)
							AS capturado
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if ($data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] <= 0) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['capturado']);
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = -2;
						}
					}
				}

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia_primaria,
						num_cia,
						dia
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['oficina'][$rec['dia']] = floatval($rec['importe']);
					}
				}

				// Faltantes y sobrantes
				if ($anio_corte >= 2015)
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "cod_mov IN (7, 13, 19, 48)";

					$condiciones_faltantes[] = "fecha >= '01-01-2015'";

					$sql = "
						SELECT
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
								END
							)
								AS faltante
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia_primaria,
							num_cia,
							dia
					";

					$query = $db->query($sql);

					if ($query) {
						foreach ($query as $row) {
							$data[$row['num_cia_primaria']][$row['num_cia']]['faltante'][$row['dia']] = floatval($row['faltante']);
						}
					}
				}
				else
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "fecha_con IS NULL";

					$condiciones_faltantes[] = "fecha >= '19-11-2014'";

					$sql = "
						SELECT
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo = FALSE THEN
										-importe
									WHEN tipo = TRUE THEN
										importe
								END
							)
								AS faltante
						FROM
							faltantes_cometra
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia_primaria,
							num_cia,
							dia
					";

					$query = $db->query($sql);

					if ($query) {
						foreach ($query as $row) {
							$data[$row['num_cia_primaria']][$row['num_cia']]['faltante'][$row['dia']] = floatval($row['faltante']);
						}
					}
				}

				$condiciones = array();

				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_corte < $dias_mes ? ' + INTERVAL \'1 DAY\'' : '');

				$condiciones[] = 'cod_mov IN (1, 16, 44, 99)';

				if ($cias) {
					$condiciones[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}

				if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . '))';
				}

				$sql = '
					SELECT
						COALESCE(num_cia_sec, num_cia)
							AS num_cia,
						CASE
							WHEN num_cia_sec IS NOT NULL THEN
								(
									SELECT
										num_cia_primaria
									FROM
										catalogo_companias
									WHERE
										num_cia = ec.num_cia_sec
								)
							ELSE
								num_cia_primaria
						END
							AS num_cia_primaria,
						extract(day FROM fecha)
							AS dia,
						cod_mov,
						importe
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						fecha,
						importe DESC
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']])
							&& $data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] == 0) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] = $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);
						}
						else if (isset($data[$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']])) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']] += $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);
						}
					}
				}

				foreach ($data as $num_cia_primaria => &$d) {
					foreach ($d as $num_cia => &$dc) {
						if (array_sum($dc['efectivo']) != 0 || array_sum($dc['deposito']) != 0 || array_sum($dc['mayoreo']) != 0 || array_sum($dc['oficina']) != 0) {
							foreach ($dc['efectivo'] as $dia => $efectivo) {
								if ($dia <= $dia_corte) {
									$dc['total'][$dia] = $dc['deposito'][$dia] + $dc['mayoreo'][$dia] + $dc['oficina'][$dia] + $dc['faltante'][$dia];
									$dc['diferencia'][$dia] = $dc['efectivo'][$dia] - $dc['total'][$dia];
								}
							}

							$dc['totales']['efectivo'] = array_sum($dc['efectivo']);
							$dc['totales']['deposito'] = array_sum($dc['deposito']) - ($dc['cometra'] == 2 && $dia_corte < $dias_mes ? $dc['deposito'][$dia_corte + 1] : 0);
							$dc['totales']['mayoreo'] = array_sum($dc['mayoreo']);
							$dc['totales']['oficina'] = array_sum($dc['oficina']);
							$dc['totales']['faltante'] = array_sum($dc['faltante']);
							$dc['totales']['diferencia'] = array_sum($dc['diferencia']);
							$dc['totales']['total'] = array_sum($dc['total']);

							$dc['promedios']['efectivo'] = round($dc['totales']['efectivo'] / $dia_corte, 2);
							$dc['promedios']['deposito'] = round($dc['totales']['deposito'] / $dia_corte, 2);
							$dc['promedios']['mayoreo'] = round($dc['totales']['mayoreo'] / $dia_corte, 2);
							$dc['promedios']['oficina'] = round($dc['totales']['oficina'] / $dia_corte, 2);
							$dc['promedios']['total'] = round($dc['totales']['total'] / $dia_corte, 2);
						}
						else {
							unset($data[$num_cia_primaria][$num_cia]);
						}
					}
				}

				foreach ($data as $num_cia_primaria => &$d) {
					$bloque = FALSE;

					foreach ($d as $num_cia => &$dc) {
						if (!$bloque) {
							$tpl->newBlock('hoja');
							$tpl->assign('salto', '<br style="page-break-before:always;" />');
						}

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre', utf8_encode(strpos($dc['nombre'], ' (') !== FALSE ? substr($dc['nombre'], 0, strpos($dc['nombre'], ' (')) : $dc['nombre']));
						$tpl->assign('alias', utf8_encode($dc['alias']));
						$tpl->assign('periodo', $_meses[$mes_corte] . ' ' . substr($anio_corte, -2));

						foreach ($dc['efectivo'] as $dia => $efectivo) {
							$tpl->newBlock('row');

							$tpl->assign('dia', str_pad($dia, 2, '0', STR_PAD_LEFT));
							$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');
							$tpl->assign('deposito', $dc['deposito'][$dia] != 0 ? number_format($dc['deposito'][$dia], 2) : '&nbsp;');
							$tpl->assign('mayoreo', $dc['mayoreo'][$dia] != 0 ? number_format($dc['mayoreo'][$dia], 2) : '&nbsp;');
							$tpl->assign('oficina', $dc['oficina'][$dia] != 0 ? number_format($dc['oficina'][$dia], 2) : '&nbsp;');
							$tpl->assign('faltante', $dc['faltante'][$dia] != 0 ? number_format($dc['faltante'][$dia], 2) : '&nbsp;');
							$tpl->assign('diferencia', $dc['diferencia'][$dia] != 0 ? number_format($dc['diferencia'][$dia], 2) : '&nbsp;');
							$tpl->assign('total', $dc['total'][$dia] != 0 ? number_format($dc['total'][$dia], 2) : '&nbsp;');

							$tpl->assign('color_faltante', $dc['faltante'][$dia] >= 0 ? 'blue' : 'red');
							$tpl->assign('color_diferencia', $dc['diferencia'][$dia] >= 0 ? 'blue' : 'red');
						}

						$tpl->assign('bcelda', 'bcelda');

						foreach ($dc['totales'] as $key => $value) {
							$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2) : ($key == 'diferencia' ? '0.00' : '&nbsp;'));
						}

						$tpl->assign('reporte.color_faltante', $dc['totales']['faltante'] >= 0 ? 'blue' : 'red');
						$tpl->assign('reporte.color_diferencia', $dc['totales']['diferencia'] >= 0 ? 'blue' : 'red');

						foreach ($dc['promedios'] as $key => $value) {
							$tpl->assign('reporte.p' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
						}

						$bloque = !$bloque;
					}
				}
			}

			$tpl->printToScreen();
		break;

		case 'reporteAdministrador':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$dias_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$cias = array();
			/*if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
			}*/
			for ($i = 0; $i < 30; $i++) {
				if (isset($_REQUEST['cia' . $i]) && $_REQUEST['cia' . $i] > 0) {
					$cias[] = $_POST['cia' . $i];
				}
			}

			$condiciones = array();

			$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : '(num_cia BETWEEN 1 AND 599 OR num_cia BETWEEN 701 AND 799)';

			if ($cias) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['idadmin'];
			}

			$sql = '
				SELECT
					num_cia,
					num_cia_primaria,
					nombre,
					nombre_corto,
					turno_cometra
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia_primaria,
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensualNormal.tpl');
			$tpl->prepare();

			$tpl->assign('rand', time());

			if ($result) {
				$data = array();

				foreach ($result as $rec) {
					$data[intval($rec['num_cia_primaria'])][intval($rec['num_cia'])] = array(
						'nombre'     => $rec['nombre'],
						'alias'      => $rec['nombre_corto'],
						'dias'       => range(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0)),
						'status'     => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), FALSE),
						'cometra'    => $rec['turno_cometra'],
						'efectivo'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'deposito'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'mayoreo'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'oficina'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'faltante'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'diferencia' => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'total'      => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'totales'    => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'faltantes'  => 0,
							'diferencia' => 0,
							'total'      => 0
						),
						'promedios'  => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'total'      => 0
						)
					);
				}

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						(efe AND exp AND pro AND gas AND pas)
							AS status,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_panaderias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						/*AND (efe AND exp AND pro AND gas AND pas) = TRUE*/

					UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						TRUE,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_companias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						(venta > 0),
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_zapaterias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND venta > 0

					/*UNION

					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						FALSE,
						ROUND(importe::NUMERIC, 2)
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND (num_cia, fecha) NOT IN (
							SELECT
								num_cia,
								fecha
							FROM
								total_panaderias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND (efe AND exp AND pro AND gas AND pas) = TRUE

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_companias
							WHERE
								' . implode(' AND ', $condiciones) . '

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_zapaterias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND venta > 0
						)*/

					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					$importes_agosto_2012 = array(
						21  => 10000,
						31  => 4000,
						32  => 3000,
						34  => 5000,
						49  => 3000,
						73  => 3000,
						79  => 2000,
						121 => 5000
					);

					$sql = '
						SELECT
							num_cia,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							cometra
						WHERE
							comprobante IN (41355658, 40759126)
						ORDER BY
							num_cia,
							fecha,
							importe
					';

					$tmp = $db->query($sql);

					$importes_septiembre_2012 = array();

					if ($tmp) {
						foreach ($tmp as $t) {
							$importes_septiembre_2012[$t['num_cia']][$t['dia']] = $t['importe'];
						}
					}

					foreach ($result as $rec) {
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['efectivo']);
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = $rec['status'] == 't' ? 1 : -1;

						/*
						@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
						@
						@ 21 - 10,000.00
						@ 31 -  4,000.00
						@ 32 -  3,000.00
						@ 34 -  5,000.00
						@ 49 -  3,000.00
						@ 73 -  3,000.00
						@ 79 -  2,000.00
						@ 121 - 5,000.00
						*/

						if (in_array($rec['num_cia'], array(
							21,
							31,
							32,
							34,
							49,
							73,
							79,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 8
							&& $rec['dia'] < 31) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_agosto_2012[$rec['num_cia']];
						}

						/*
						@ [04-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
						@
						*/

						if (in_array($rec['num_cia'], array(
							31,
							32,
							33,
							34,
							73,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 9
							&& isset($importes_septiembre_2012[$rec['num_cia']][$rec['dia']])) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_septiembre_2012[$rec['num_cia']][$rec['dia']];
						}

						/*
						@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 10) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 11) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							49,
							57,
							67,
							34
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}
					}
				}

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						ROUND(importe::NUMERIC, 2)
							AS capturado
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if ($data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] <= 0) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['capturado']);
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = -2;
						}
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = 'comprobante IS NOT NULL';

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						comprobante,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						num_cia_primaria,
						num_cia,
						dia,
						comprobante
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				$otros = array();

				if ($result) {
					foreach ($result as $rec) {
						$otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']] = array(
							'num_cia_primaria' => $rec['num_cia_primaria'],
							'importe'          => floatval($rec['importe']),
							'status'           => FALSE
						);
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = 'comprobante IS NULL';

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						num_cia_primaria,
						num_cia,
						dia
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data[$rec['num_cia_primaria']][$rec['num_cia']]['oficina'][$rec['dia']] = floatval($rec['importe']);
					}
				}

				// Faltantes y sobrantes
				if ($anio_corte >= 2015)
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "cod_mov IN (7, 13, 19, 48)";

					$condiciones_faltantes[] = "fecha >= '01-01-2015'";

					$sql = "
						SELECT
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
								END
							)
								AS faltante
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia_primaria,
							num_cia,
							dia
					";

					$query = $db->query($sql);

					if ($query) {
						foreach ($query as $row) {
							$data[$row['num_cia_primaria']][$row['num_cia']]['faltante'][$row['dia']] = floatval($row['faltante']);
						}
					}
				}
				else
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "fecha_con IS NULL";

					$condiciones_faltantes[] = "fecha >= '19-11-2014'";

					$sql = "
						SELECT
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo = FALSE THEN
										-importe
									WHEN tipo = TRUE THEN
										importe
								END
							)
								AS faltante
						FROM
							faltantes_cometra
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia_primaria,
							num_cia,
							dia
					";

					$query = $db->query($sql);

					if ($query) {
						foreach ($query as $row) {
							$data[$row['num_cia_primaria']][$row['num_cia']]['faltante'][$row['dia']] = floatval($row['faltante']);
						}
					}
				}

				$condiciones = array();

				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_corte < $dias_mes ? ' + INTERVAL \'1 DAY\'' : '');

				$condiciones[] = 'cod_mov IN (1, 16, 44, 99)';

				$condiciones[] = 'concepto NOT LIKE \'COMPLEMENTO VENTA%\'';

				if ($cias) {
					$condiciones[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}

				if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . '))';
				}

				$sql = '
					SELECT
						COALESCE(num_cia_sec, num_cia)
							AS num_cia,
						CASE
							WHEN num_cia_sec IS NOT NULL THEN
								(
									SELECT
										num_cia_primaria
									FROM
										catalogo_companias
									WHERE
										num_cia = ec.num_cia_sec
								)
							ELSE
								num_cia_primaria
						END
							AS num_cia_primaria,
						comprobante,
						EXTRACT(day FROM fecha)
							AS dia,
						cod_mov,
						/*importe + COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								comprobante = ec.comprobante
								AND num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
								AND fecha = ec.fecha
						), 0)
							AS */importe
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						fecha,
						importe DESC
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']])
							&& $data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] == 0) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] = $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]) && !$otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status']) {
								$data[$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] += $otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
						else if (isset($data[$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']])) {
							$data[$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']] += $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]) && !$otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status']) {
								$data[$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']] += $otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
					}
				}

				foreach ($otros as $num_cia => $otro) {
					foreach ($otro as $dia => $comprobantes) {
						foreach ($comprobantes as $rec) {
							if (!$rec['status']) {
								$data[$rec['num_cia_primaria']][$num_cia]['oficina'][$dia] += $rec['importe'];
							}
						}
					}
				}

				foreach ($data as $num_cia_primaria => &$d) {
					foreach ($d as $num_cia => &$dc) {
						if (array_sum($dc['efectivo']) != 0 || array_sum($dc['deposito']) != 0 || array_sum($dc['mayoreo']) != 0 || array_sum($dc['oficina']) != 0) {
							foreach ($dc['efectivo'] as $dia => $efectivo) {
								if ($dia <= $dia_corte) {
									$dc['total'][$dia] = $dc['deposito'][$dia] + $dc['mayoreo'][$dia] + $dc['oficina'][$dia] + $dc['faltante'][$dia];
									$dc['diferencia'][$dia] = $dc['efectivo'][$dia] - $dc['total'][$dia];
								}
							}

							$dc['totales']['efectivo'] = array_sum($dc['efectivo']);
							$dc['totales']['deposito'] = array_sum($dc['deposito']) - ($dc['cometra'] == 2 && $dia_corte < $dias_mes ? $dc['deposito'][$dia_corte + 1] : 0);
							$dc['totales']['mayoreo'] = array_sum($dc['mayoreo']);
							$dc['totales']['oficina'] = array_sum($dc['oficina']);
							$dc['totales']['faltante'] = array_sum($dc['faltante']);
							$dc['totales']['diferencia'] = array_sum($dc['diferencia']);
							$dc['totales']['total'] = array_sum($dc['total']);

							$dc['promedios']['efectivo'] = round($dc['totales']['efectivo'] / $dia_corte, 2);
							$dc['promedios']['deposito'] = round($dc['totales']['deposito'] / $dia_corte, 2);
							$dc['promedios']['mayoreo'] = round($dc['totales']['mayoreo'] / $dia_corte, 2);
							$dc['promedios']['oficina'] = round($dc['totales']['oficina'] / $dia_corte, 2);
							$dc['promedios']['total'] = round($dc['totales']['total'] / $dia_corte, 2);
						}
						else {
							unset($data[$num_cia_primaria][$num_cia]);
						}
					}
				}

				foreach ($data as $num_cia_primaria => &$d) {
					$bloque = FALSE;

					foreach ($d as $num_cia => &$dc) {
						if (!$bloque) {
							$tpl->newBlock('hoja');
							$tpl->assign('salto', '<br style="page-break-before:always;" />');
						}

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre', utf8_encode(strpos($dc['nombre'], ' (') !== FALSE ? substr($dc['nombre'], 0, strpos($dc['nombre'], ' (')) : $dc['nombre']));
						$tpl->assign('alias', utf8_encode($dc['alias']));
						$tpl->assign('periodo', $_meses[$mes_corte] . ' ' . substr($anio_corte, -2));

						foreach ($dc['efectivo'] as $dia => $efectivo) {
							$tpl->newBlock('row');

							$tpl->assign('dia', str_pad($dia, 2, '0', STR_PAD_LEFT));
							$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');
							$tpl->assign('deposito', $dc['deposito'][$dia] != 0 ? number_format($dc['deposito'][$dia], 2) : '&nbsp;');
							$tpl->assign('mayoreo', $dc['mayoreo'][$dia] != 0 ? number_format($dc['mayoreo'][$dia], 2) : '&nbsp;');
							$tpl->assign('oficina', $dc['oficina'][$dia] != 0 ? number_format($dc['oficina'][$dia], 2) : '&nbsp;');
							$tpl->assign('faltante', $dc['faltante'][$dia] != 0 ? number_format($dc['faltante'][$dia], 2) : '&nbsp;');
							$tpl->assign('diferencia', $dc['diferencia'][$dia] != 0 ? number_format($dc['diferencia'][$dia], 2) : '&nbsp;');
							$tpl->assign('total', $dc['total'][$dia] != 0 ? number_format($dc['total'][$dia], 2) : '&nbsp;');

							$tpl->assign('color_faltante', $dc['faltante'][$dia] >= 0 ? 'blue' : 'red');
							$tpl->assign('color_diferencia', $dc['diferencia'][$dia] >= 0 ? 'blue' : 'red');
						}

						$tpl->assign('bcelda', 'bcelda');

						foreach ($dc['totales'] as $key => $value) {
							$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2) : ($key == 'diferencia' ? '0.00' : '&nbsp;'));
						}

						$tpl->assign('reporte.color_faltante', $dc['totales']['faltante'] >= 0 ? 'blue' : 'red');
						$tpl->assign('reporte.color_diferencia', $dc['totales']['diferencia'] >= 0 ? 'blue' : 'red');

						foreach ($dc['promedios'] as $key => $value) {
							$tpl->assign('reporte.p' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
						}

						$bloque = !$bloque;
					}
				}

				$query_array = array();

				foreach ($_REQUEST as $key => $value)
				{
					if ($key == 'accion')
					{
						$query_array[] = "{$key}=emailAdministrador";
					}
					else
					{
						$query_array[] = "{$key}={$value}";
					}
				}

				$tpl->newBlock('boton_email');
				$tpl->assign('data-request', implode('&', $query_array));
			}

			$tpl->printToScreen();
		break;

		/*
		@@ [16-May-2014] Envio de correo a administradores
		*/

		case 'emailAdministrador':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$dias_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$cias = array();
			/*if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
			}*/
			for ($i = 0; $i < 30; $i++) {
				if (isset($_REQUEST['cia' . $i]) && $_REQUEST['cia' . $i] > 0) {
					$cias[] = $_POST['cia' . $i];
				}
			}

			$condiciones = array();

			$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : '(num_cia BETWEEN 1 AND 599 OR num_cia BETWEEN 701 AND 799)';

			if ($cias) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['idadmin'];
			}

			$sql = '
				SELECT
					ca.idadministrador
						AS admin,
					ca.nombre_administrador
						AS nombre_admin,
					ca.email
						AS email_admin,
					num_cia,
					num_cia_primaria,
					nombre,
					nombre_corto,
					turno_cometra
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					admin,
					num_cia_primaria,
					num_cia
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array();

				$admin = NULL;

				foreach ($result as $rec) {
					if ($admin != $rec['admin'])
					{
						$admin = $rec['admin'];

						$data[$admin] = array(
							'nombre'	=> $rec['nombre_admin'],
							'email'		=> $rec['email_admin'],
							'data'		=> array()
						);
					}

					$data[$admin]['data'][intval($rec['num_cia_primaria'])][intval($rec['num_cia'])] = array(
						'nombre'     => $rec['nombre'],
						'alias'      => $rec['nombre_corto'],
						'dias'       => range(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0)),
						'status'     => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), FALSE),
						'cometra'    => $rec['turno_cometra'],
						'efectivo'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'deposito'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'mayoreo'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'oficina'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'faltante'   => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'diferencia' => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'total'      => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'totales'    => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'faltante'   => 0,
							'diferencia' => 0,
							'total'      => 0
						),
						'promedios'  => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'total'      => 0
						)
					);
				}

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						(efe AND exp AND pro AND gas AND pas)
							AS status,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_panaderias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						/*AND (efe AND exp AND pro AND gas AND pas) = TRUE*/

					UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						TRUE,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_companias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						(venta > 0),
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_zapaterias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND venta > 0

					/*UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						FALSE,
						ROUND(importe::NUMERIC, 2)
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND (num_cia, fecha) NOT IN (
							SELECT
								num_cia,
								fecha
							FROM
								total_panaderias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND (efe AND exp AND pro AND gas AND pas) = TRUE

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_companias
							WHERE
								' . implode(' AND ', $condiciones) . '

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_zapaterias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND venta > 0
						)*/

					ORDER BY
						admin,
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					$importes_agosto_2012 = array(
						21  => 10000,
						31  => 4000,
						32  => 3000,
						34  => 5000,
						49  => 3000,
						73  => 3000,
						79  => 2000,
						121 => 5000
					);

					$sql = '
						SELECT
							idadministrador
								AS admin,
							num_cia,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							cometra
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							comprobante IN (41355658, 40759126)
						ORDER BY
							num_cia,
							fecha,
							importe
					';

					$tmp = $db->query($sql);

					$importes_septiembre_2012 = array();

					if ($tmp) {
						foreach ($tmp as $t) {
							$importes_septiembre_2012[$t['num_cia']][$t['dia']] = $t['importe'];
						}
					}

					foreach ($result as $rec) {
						$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['efectivo']);
						$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = $rec['status'] == 't' ? 1 : -1;

						/*
						@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
						@
						@ 21 - 10,000.00
						@ 31 -  4,000.00
						@ 32 -  3,000.00
						@ 34 -  5,000.00
						@ 49 -  3,000.00
						@ 73 -  3,000.00
						@ 79 -  2,000.00
						@ 121 - 5,000.00
						*/

						if (in_array($rec['num_cia'], array(
							21,
							31,
							32,
							34,
							49,
							73,
							79,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 8
							&& $rec['dia'] < 31) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_agosto_2012[$rec['num_cia']];
						}

						/*
						@ [04-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
						@
						*/

						if (in_array($rec['num_cia'], array(
							31,
							32,
							33,
							34,
							73,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 9
							&& isset($importes_septiembre_2012[$rec['num_cia']][$rec['dia']])) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += $importes_septiembre_2012[$rec['num_cia']][$rec['dia']];
						}

						/*
						@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 10) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 11) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							49,
							57,
							67,
							34
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] += 10000;
						}
					}
				}

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						ROUND(importe::NUMERIC, 2)
							AS capturado
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if ($data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] <= 0) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['capturado']);
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['status'][$rec['dia']] = -2;
						}
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						comprobante,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						admin,
						num_cia_primaria,
						num_cia,
						dia,
						comprobante
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				$otros = array();

				if ($result) {
					foreach ($result as $rec) {
						$otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']] = array(
							'num_cia_primaria' => $rec['num_cia_primaria'],
							'importe'          => floatval($rec['importe']),
							'status'           => FALSE
						);
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = 'comprobante IS NULL';

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						admin,
						num_cia_primaria,
						num_cia,
						dia
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['oficina'][$rec['dia']] = floatval($rec['importe']);
					}
				}

				// Faltantes y sobrantes

				$condiciones_faltantes = $condiciones;

				$condiciones_faltantes[] = "fecha_con IS NULL";

				$condiciones_faltantes[] = "fecha >= '19-11-2014'";

				$sql = "
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(
							CASE
								WHEN tipo = FALSE THEN
									-importe
								WHEN tipo = TRUE THEN
									importe
							END
						)
							AS faltante
					FROM
						faltantes_cometra
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						" . implode(' AND ', $condiciones_faltantes) . "
					GROUP BY
						admin,
						num_cia_primaria,
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				";

				$query = $db->query($sql);

				if ($query) {
					foreach ($query as $row) {
						$data[$row['admin']]['data'][$row['num_cia_primaria']][$row['num_cia']]['faltante'][$row['dia']] = floatval($row['faltante']);
					}
				}

				$condiciones = array();

				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_corte < $dias_mes ? ' + INTERVAL \'1 DAY\'' : '');

				$condiciones[] = 'cod_mov IN (1, 16, 44, 99)';

				$condiciones[] = 'concepto NOT LIKE \'COMPLEMENTO VENTA%\'';

				if ($cias) {
					$condiciones[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}

				if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . '))';
				}

				$sql = '
					SELECT
						idadministrador
							AS admin,
						COALESCE(num_cia_sec, num_cia)
							AS num_cia,
						CASE
							WHEN num_cia_sec IS NOT NULL THEN
								(
									SELECT
										num_cia_primaria
									FROM
										catalogo_companias
									WHERE
										num_cia = ec.num_cia_sec
								)
							ELSE
								num_cia_primaria
						END
							AS num_cia_primaria,
						comprobante,
						EXTRACT(day FROM fecha)
							AS dia,
						cod_mov,
						/*importe + COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								comprobante = ec.comprobante
								AND num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
								AND fecha = ec.fecha
						), 0)
							AS */importe
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						fecha,
						importe DESC
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']])
							&& $data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] == 0) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] = $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]) && !$otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status']) {
								$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['deposito'][$rec['dia']] += $otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
						else if (isset($data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']])) {
							$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']] += $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]) && !$otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status']) {
								$data[$rec['admin']]['data'][$rec['num_cia_primaria']][$rec['num_cia']]['mayoreo'][$rec['dia']] += $otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['admin']][$rec['num_cia']][$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
					}
				}

				foreach ($otros as $admin => $cias) {
					foreach ($cias as $num_cia => $otro) {
						foreach ($otro as $dia => $comprobantes) {
							foreach ($comprobantes as $rec) {
								if (!$rec['status']) {
									$data[$admin]['data'][$rec['num_cia_primaria']][$num_cia]['oficina'][$dia] += $rec['importe'];
								}
							}
						}
					}
				}

				foreach ($data as $admin => &$data_admin) {
					foreach ($data_admin['data'] as $num_cia_primaria => &$d) {
						foreach ($d as $num_cia => &$dc) {
							if (array_sum($dc['efectivo']) != 0 || array_sum($dc['deposito']) != 0 || array_sum($dc['mayoreo']) != 0 || array_sum($dc['oficina']) != 0) {
								foreach ($dc['efectivo'] as $dia => $efectivo) {
									if ($dia <= $dia_corte) {
										$dc['total'][$dia] = $dc['deposito'][$dia] + $dc['mayoreo'][$dia] + $dc['oficina'][$dia] + $dc['faltante'][$dia];
										$dc['diferencia'][$dia] = $dc['efectivo'][$dia] - $dc['total'][$dia];
									}
								}

								$dc['totales']['efectivo'] = array_sum($dc['efectivo']);
								$dc['totales']['deposito'] = array_sum($dc['deposito']) - ($dc['cometra'] == 2 && $dia_corte < $dias_mes ? $dc['deposito'][$dia_corte + 1] : 0);
								$dc['totales']['mayoreo'] = array_sum($dc['mayoreo']);
								$dc['totales']['oficina'] = array_sum($dc['oficina']);
								$dc['totales']['faltante'] = array_sum($dc['faltante']);
								$dc['totales']['diferencia'] = array_sum($dc['diferencia']);
								$dc['totales']['total'] = array_sum($dc['total']);

								$dc['promedios']['efectivo'] = round($dc['totales']['efectivo'] / $dia_corte, 2);
								$dc['promedios']['deposito'] = round($dc['totales']['deposito'] / $dia_corte, 2);
								$dc['promedios']['mayoreo'] = round($dc['totales']['mayoreo'] / $dia_corte, 2);
								$dc['promedios']['oficina'] = round($dc['totales']['oficina'] / $dia_corte, 2);
								$dc['promedios']['total'] = round($dc['totales']['total'] / $dia_corte, 2);
							}
							else {
								unset($data[$admin]['data'][$num_cia_primaria][$num_cia]);
							}
						}
					}
				}

				include_once('includes/phpmailer/class.phpmailer.php');
				require_once('includes/WkHtmlToPdf.php');

				$path = '/var/www/lecaroz';
				// $path = '/home/carlos/Sitios/lecaroz';

				foreach ($data as $admin => $data_admin)
				{
					$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensualEmail.tpl');
					$tpl->prepare();

					foreach ($data_admin['data'] as $num_cia_primaria => &$d) {
						$bloque = FALSE;

						foreach ($d as $num_cia => &$dc) {
							if (!$bloque) {
								$tpl->newBlock('hoja');
							}

							$tpl->newBlock('reporte');

							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre', utf8_encode(strpos($dc['nombre'], ' (') !== FALSE ? substr($dc['nombre'], 0, strpos($dc['nombre'], ' (')) : $dc['nombre']));
							$tpl->assign('alias', utf8_encode($dc['alias']));
							$tpl->assign('periodo', $_meses[$mes_corte] . ' ' . substr($anio_corte, -2));

							foreach ($dc['efectivo'] as $dia => $efectivo) {
								$tpl->newBlock('row');

								$tpl->assign('dia', str_pad($dia, 2, '0', STR_PAD_LEFT));
								$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');
								$tpl->assign('deposito', $dc['deposito'][$dia] != 0 ? number_format($dc['deposito'][$dia], 2) : '&nbsp;');
								$tpl->assign('mayoreo', $dc['mayoreo'][$dia] != 0 ? number_format($dc['mayoreo'][$dia], 2) : '&nbsp;');
								$tpl->assign('oficina', $dc['oficina'][$dia] != 0 ? number_format($dc['oficina'][$dia], 2) : '&nbsp;');
								$tpl->assign('faltante', $dc['faltante'][$dia] != 0 ? number_format($dc['faltante'][$dia], 2) : '&nbsp;');
								$tpl->assign('diferencia', $dc['diferencia'][$dia] != 0 ? number_format($dc['diferencia'][$dia], 2) : '&nbsp;');
								$tpl->assign('total', $dc['total'][$dia] != 0 ? number_format($dc['total'][$dia], 2) : '&nbsp;');

								$tpl->assign('color_faltante', $dc['faltante'][$dia] >= 0 ? 'blue' : 'red');
								$tpl->assign('color_diferencia', $dc['diferencia'][$dia] >= 0 ? 'blue' : 'red');
							}

							$tpl->assign('bcelda', 'bcelda');

							foreach ($dc['totales'] as $key => $value) {
								$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2) : ($key == 'diferencia' ? '0.00' : '&nbsp;'));
							}

							$tpl->assign('reporte.color_faltante', $dc['totales']['faltante'] >= 0 ? 'blue' : 'red');
							$tpl->assign('reporte.color_diferencia', $dc['totales']['diferencia'] >= 0 ? 'blue' : 'red');

							foreach ($dc['promedios'] as $key => $value) {
								$tpl->assign('reporte.p' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
							}

							$bloque = !$bloque;
						}
					}

					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'miguelrebuelta@lecaroz.com';
					$mail->Password = 'L3c4r0z*';

					$mail->From = 'miguelrebuelta@lecaroz.com';
					$mail->FromName = utf8_decode('Lic. Miguel Angel Rebuelta Diez');

					if ($data_admin['email'] != '')
					{
						$mail->AddAddress($data_admin['email']);
					}

					$mail->AddCC('miguelrebuelta@lecaroz.com');

					// $mail->AddBCC('carlos.candelario@lecaroz.com');
					// $mail->AddAddress('carlos.candelario@lecaroz.com');

					$mail->Subject = utf8_decode('Reportes de efectivos del mes de ' . $__meses[$mes_corte] . ' de ' . $anio_corte);

					$pdf = new WkHtmlToPdf(array(
						'binPath'		=> '/usr/local/bin/wkhtmltopdf',
						// 'no-outline',								// Make Chrome not complain
						'margin-top'	=> 0,
						'margin-right'	=> 0,
						'margin-bottom'	=> 0,
						'margin-left'	=> 0,
						'page-size'		=> 'Letter',
						'orientation'	=> 'Landscape'
					));

					$pdf->setPageOptions(array(
						'disable-smart-shrinking',
						'user-style-sheet' => $path . '/styles/reporte-efectivos-pdf.css',
					));

					$pdf->addPage($tpl->getOutputContent());



					if ( ! $pdf->saveAs($path . '/tmp/reporte_efectivos_' . $anio_corte . '_' . strtolower($__meses[$mes_corte]) . '.pdf'))
					{
						throw new Exception('Could not create PDF: '.$pdf->getError());
					}

					$mail->AddAttachment($path . '/tmp/reporte_efectivos_' . $anio_corte . '_' . strtolower($__meses[$mes_corte]) . '.pdf');
					// $mail->AddAttachment($path . '/tmp/reporte_efectivos_' . $anio_corte . '_' . strtolower($__meses[$mes_corte]) . '.pdf');

					// $mail->Body = $tpl->getOutputContent();
					$mail->Body = 'Favor de descargar y abrir el archivo adjunto con Acrobat Reader o similares.';

					$mail->IsHTML(true);

					if(!$mail->Send()) {
						// echo $mail->ErrorInfo;
					}
					else {
						// echo 'OK';
					}
				}

			}

		break;

		case 'reporteCompleto':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$dias_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$cias = array();

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
			}

			$condiciones = array();

			$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'num_cia BETWEEN 900 AND 998' : '(num_cia BETWEEN 1 AND 599 OR num_cia BETWEEN 701 AND 799)';

			if ($cias) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = '
				SELECT
					num_cia,
					nombre,
					nombre_corto,
					turno_cometra
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensualCompleto.tpl');
			$tpl->prepare();

			if ($result) {
				$data = array();

				foreach ($result as $rec) {
					$data[intval($rec['num_cia'])] = array(
						'nombre'     => $rec['nombre'],
						'alias'      => $rec['nombre_corto'],
						'dias'       => range(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0)),
						'cometra'    => $rec['turno_cometra'],
						'status'     => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), FALSE),
						'efectivo'   => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'deposito'   => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'mayoreo'    => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'oficina'    => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'diferencia' => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'total'      => array_fill(1, $dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'totales'    => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'diferencia' => 0,
							'total'      => 0
						),
						'promedios'  => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'total'      => 0
						)
					);
				}

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$sql = '
					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha)
							AS dia,
						(efe AND exp AND pro AND gas AND pas)
							AS status,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_panaderias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						/*AND (efe AND exp AND pro AND gas AND pas) = TRUE*/

					UNION

					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha),
						TRUE,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_companias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha),
						(venta > 0),
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_zapaterias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND venta > 0

					/*UNION

					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha),
						FALSE,
						ROUND(importe::NUMERIC, 2)
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND (num_cia, fecha) NOT IN (
							SELECT
								num_cia,
								fecha
							FROM
								total_panaderias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND (efe AND exp AND pro AND gas AND pas) = TRUE

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_companias
							WHERE
								' . implode(' AND ', $condiciones) . '

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_zapaterias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND venta > 0
						)*/

					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data[$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['efectivo']);
						$data[$rec['num_cia']]['status'][$rec['dia']] = $rec['status'] == 't' ? 1 : -1;
					}
				}

				$sql = '
					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha)
							AS dia,
						ROUND(importe::NUMERIC, 2)
							AS capturado
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if ($data[$rec['num_cia']]['status'][$rec['dia']] <= 0) {
							$data[$rec['num_cia']]['efectivo'][$rec['dia']] = floatval($rec['capturado']);
							$data[$rec['num_cia']]['status'][$rec['dia']] = -2;
						}
					}
				}

				$sql = '
					SELECT
						num_cia,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data[$rec['num_cia']]['oficina'][$rec['dia']] = floatval($rec['importe']);
					}
				}

				$condiciones = array();

				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_corte < $dias_mes ? ' + INTERVAL \'1 DAY\'' : '');

				$condiciones[] = 'cod_mov IN (1, 16, 44, 99)';

				if ($cias) {
					$condiciones[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}

				if (isset($_REQUEST['idadmin']) && $_REQUEST['idadmin'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['idadmin'] . '))';
				}

				$sql = '
					SELECT
						COALESCE(num_cia_sec, num_cia)
							AS num_cia,
						extract(day FROM fecha)
							AS dia,
						cod_mov,
						importe
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						fecha,
						importe DESC
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($data[$rec['num_cia']]['deposito'][$rec['dia']])
							&& $data[$rec['num_cia']]['deposito'][$rec['dia']] == 0) {
							$data[$rec['num_cia']]['deposito'][$rec['dia']] = $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);
						}
						else if (isset($data[$rec['num_cia']]['mayoreo'][$rec['dia']])) {
							$data[$rec['num_cia']]['mayoreo'][$rec['dia']] += $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);
						}
					}
				}

				foreach ($data as $num_cia => &$d) {
					if (array_sum($d['efectivo']) != 0 || array_sum($d['deposito']) != 0 || array_sum($d['mayoreo']) != 0 || array_sum($d['oficina']) != 0) {
						foreach ($d['efectivo'] as $dia => $efectivo) {
							if ($dia <= $dia_corte) {
								$d['total'][$dia] = $d['deposito'][$dia] + $d['mayoreo'][$dia] + $d['oficina'][$dia];
								$d['diferencia'][$dia] = $d['efectivo'][$dia] - $d['total'][$dia];
							}
						}

						$d['totales']['efectivo'] = array_sum($d['efectivo']);
						$d['totales']['deposito'] = array_sum($d['deposito']) - ($d['cometra'] == 2 && $dia_corte < $dias_mes ? $d['deposito'][$dia_corte + 1] : 0);
						$d['totales']['mayoreo'] = array_sum($d['mayoreo']);
						$d['totales']['oficina'] = array_sum($d['oficina']);
						$d['totales']['diferencia'] = array_sum($d['diferencia']);
						$d['totales']['total'] = array_sum($d['total']);

						$d['promedios']['efectivo'] = round($d['totales']['efectivo'] / $dia_corte, 2);
						$d['promedios']['deposito'] = round($d['totales']['deposito'] / $dia_corte, 2);
						$d['promedios']['mayoreo'] = round($d['totales']['mayoreo'] / $dia_corte, 2);
						$d['promedios']['oficina'] = round($d['totales']['oficina'] / $dia_corte, 2);
						$d['promedios']['total'] = round($d['totales']['total'] / $dia_corte, 2);
					}
					else {
						unset($data[$num_cia_primaria][$num_cia]);
					}
				}

				foreach ($data as $num_cia => &$d) {
					$tpl->newBlock('hoja');
					$tpl->newBlock('reporte');
					$tpl->assign('salto', '<br style="page-break-before:always;" />');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre', utf8_encode(strpos($d['nombre'], ' (') !== FALSE ? substr($d['nombre'], 0, strpos($d['nombre'], ' (')) : $d['nombre']));
					$tpl->assign('alias', utf8_encode($d['alias']));
					$tpl->assign('periodo', $_meses[$mes_corte] . ' ' . substr($anio_corte, -2));

					foreach ($d['efectivo'] as $dia => $efectivo) {
						$tpl->newBlock('row');

						$tpl->assign('dia', str_pad($dia, 2, '0', STR_PAD_LEFT));
						$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');
						$tpl->assign('deposito', $d['deposito'][$dia] != 0 ? number_format($d['deposito'][$dia], 2) : '&nbsp;');
						$tpl->assign('mayoreo', $d['mayoreo'][$dia] != 0 ? number_format($d['mayoreo'][$dia], 2) : '&nbsp;');
						$tpl->assign('oficina', $d['oficina'][$dia] != 0 ? number_format($d['oficina'][$dia], 2) : '&nbsp;');
						$tpl->assign('diferencia', $d['diferencia'][$dia] != 0 ? number_format($d['diferencia'][$dia], 2) : '&nbsp;');
						$tpl->assign('total', $d['total'][$dia] != 0 ? number_format($d['total'][$dia], 2) : '&nbsp;');

						$tpl->assign('color_diferencia', $d['diferencia'][$dia] >= 0 ? 'blue' : 'red');
					}

					$tpl->assign('bcelda', 'bcelda');

					foreach ($d['totales'] as $key => $value) {
						$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
					}

					$tpl->assign('reporte.color_diferencia', $d['totales']['diferencia'] >= 0 ? 'blue' : 'red');

					foreach ($d['promedios'] as $key => $value) {
						$tpl->assign('reporte.p' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
					}

					$tpl->assign('reporte.porcentaje_depositos');
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2)));

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$admins = $db->query($sql);

if ($admins) {
	foreach ($admins AS $a) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
