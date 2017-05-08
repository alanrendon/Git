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
		case 'obtenerExpendios':
			$condiciones = array();

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if ($cias) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['agente_ventas']) && $_REQUEST['agente_ventas'] > 0) {
				$condiciones[] = 'idagven = ' . $_REQUEST['agente_ventas'];
			}

			if ($db->query('
				SELECT
					idoperadora
				FROM
					catalogo_operadoras
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($db->query('
				SELECT
					idagven
				FROM
					catalogo_agentes_venta
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}

			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					num_expendio
						AS num_exp,
					ce.nombre
						AS nombre_exp
				FROM
					catalogo_expendios ce
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_agentes_venta ca
						USING (idagven)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					num_exp,
					nombre_exp
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array(array(
					'value' => NULL,
					'text'  => NULL
				));

				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$data[] = array(
							'value'    => NULL,
							'text'     => $rec['num_cia'] . ' ' . utf8_encode($rec['nombre_cia']),
							'disabled' => TRUE,
							'style'    => 'color:#000; font-size:12pt; font-weight:bold; text-decoration:underline; background-color:#CCC; margin:5px 0;'
						);
					}

					$data[] = array(
						'value' => json_encode(array(
							intval($rec['num_cia']),
							intval($rec['num_exp']),
							utf8_encode($rec['nombre_exp'])
						)),
						'text'  => $rec['num_exp'] . ' ' . utf8_encode($rec['nombre_exp'])
					);
				}

				echo json_encode($data);
			}
		break;

		case 'reporteDetalladoExpendios':
			$condiciones = array();

			$condiciones[] = 'mov.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if ($cias) {
					$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['expendio']) && $_REQUEST['expendio'] != '') {
				$data = json_decode($_REQUEST['expendio']);

				$data[2] = '\'' . utf8_decode($data[2]) . '\'';

				$condiciones[] = '(mov.num_cia, mov.num_expendio, mov.nombre_expendio) IN (VALUES (' . implode(', ', $data) . '))';
			}

			if (isset($_REQUEST['agente_ventas']) && $_REQUEST['agente_ventas'] > 0) {
				$condiciones[] = 'ce.idagven = ' . $_REQUEST['agente_ventas'];
			}

			if ($db->query('
				SELECT
					idoperadora
				FROM
					catalogo_operadoras
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($db->query('
				SELECT
					idagven
				FROM
					catalogo_agentes_venta
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}

			$sql = '
				SELECT
					mov.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					mov.num_expendio
						AS num_exp,
					mov.nombre_expendio
						AS nombre_exp,
					(
						SELECT
							rezago_anterior
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND num_expendio = mov.num_expendio
							AND nombre_expendio = mov.nombre_expendio
							AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
						ORDER BY
							fecha
						LIMIT
							1
					)
						AS rezago_inicial,
					SUM(pan_p_venta)
						AS partidas,
					SUM(pan_p_expendio)
						AS total,
					AVG(porc_ganancia)
						AS ganancia,
					SUM(abono)
						AS abonos,
					SUM(devolucion)
						AS devuelto,
					(
						SELECT
							rezago
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND num_expendio = mov.num_expendio
							AND nombre_expendio = mov.nombre_expendio
							AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					)
						AS rezago_final,
					AVG(abono)
						AS abonos_promedio,
					(
						SELECT
							MIN(fecha)
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND num_expendio = mov.num_expendio
							AND nombre_expendio = mov.nombre_expendio
							AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
					)
						AS primer_dia,
					(
						SELECT
							MAX(fecha)
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND num_expendio = mov.num_expendio
							AND nombre_expendio = mov.nombre_expendio
							AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
					)
						AS ultimo_dia,
					(
						SELECT
							AVG(pan_p_expendio)
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND num_expendio = mov.num_expendio
							AND nombre_expendio = mov.nombre_expendio
							AND (EXTRACT(YEAR FROM fecha), EXTRACT(MONTH FROM fecha)) IN (
								SELECT
									EXTRACT(YEAR FROM fecha),
									EXTRACT(MONTH FROM fecha)
								FROM
									mov_expendios
								WHERE
									num_cia = mov.num_cia
									AND num_expendio = mov.num_expendio
									AND nombre_expendio = mov.nombre_expendio
									AND fecha <= \'' . $_REQUEST['fecha2'] . '\'
									AND pan_p_expendio > 0
								ORDER BY
									fecha DESC
								LIMIT
									1
							)
					)
						AS total_promedio
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_expendios ce
						ON (ce.num_cia = mov.num_cia AND ce.num_expendio = mov.num_expendio AND ce.nombre = mov.nombre_expendio)
					LEFT JOIN catalogo_agentes_venta ca
						USING (idagven)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					mov.num_cia,
					nombre_cia,
					num_exp,
					nombre_exp
				ORDER BY
					mov.num_cia,
					num_exp,
					primer_dia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ExpendiosReporteDetalladoExpendios.tpl');
			$tpl->prepare();

			if ($result && isset($_REQUEST['aumento'])) {
				foreach ($result as $i => $rec) {
					if ($rec['rezago_final'] > $rec['rezago_inicial']) {
						unset($result[$i]);
					}
				}
			}

			if ($result && isset($_REQUEST['devuelto'])) {
				foreach ($result as $i => $rec) {
					if ($rec['devuelto'] <= 0) {
						unset($result[$i]);
					}
				}
			}

			if ($result) {
				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							foreach ($totales as $campo => $total) {
								$tpl->assign('reporte.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
							}

							$porcentaje_devuelto = $totales['dias_devuelto'] != 0 ? round($totales['porcentaje_devuelto'] / $totales['dias_devuelto'], 2) : 0;

							$tpl->assign('reporte.porcentaje_devuelto', $porcentaje_devuelto != 0 ? number_format($porcentaje_devuelto, 2) : '&nbsp;');

							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$num_cia = $rec['num_cia'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);

						$totales = array(
							'rezago_inicial'      => 0,
							'partidas'            => 0,
							'total'               => 0,
							'diferencia'          => 0,
							'ganancia'            => 0,
							'abonos'              => 0,
							'devuelto'            => 0,
							'porcentaje_devuelto' => 0,
							'dias_devuelto'       => 0,
							'rezago_final'        => 0,
							'diferencia_devolucion'   => 0,
							'pdiferencia_pdevolucion' => 0
						);
					}

					$dias = 0;

					if ($rec['rezago_final'] > 0) {
						/*$sql = '
							SELECT
								pan_p_expendio
							FROM
								mov_expendios
							WHERE
								num_cia = ' . $rec['num_cia'] . '
								AND num_expendio = ' . $rec['num_exp'] . '
								AND nombre_expendio = \'' . $rec['nombre_exp'] . '\'
								AND fecha BETWEEN \'' . $_REQUEST['fecha2'] . '\'::DATE - INTERVAL \'1000 DAYS\' AND \'' . $_REQUEST['fecha2'] . '\'::DATE
							ORDER BY
								fecha DESC
						';

						$movs = $db->query($sql);

						if ($movs) {
							$saldo = $rec['rezago_final'];

							foreach ($movs as $mov) {
								$saldo -= $mov['pan_p_expendio'];

								if ($saldo >= 0) {
									$dias++;
								}
								else {
									break;
								}
							}
						}*/

						$dias = ceil($rec['rezago_final'] / $rec['total_promedio']);
					}

					$tpl->newBlock('expendio');
					$tpl->assign('num_exp', $rec['num_exp']);
					$tpl->assign('nombre_exp', utf8_encode($rec['nombre_exp']));
					$tpl->assign('rezago_inicial', $rec['rezago_inicial'] != 0 ? number_format($rec['rezago_inicial'], 2) : '&nbsp;');
					$tpl->assign('partidas', $rec['partidas'] != 0 ? number_format($rec['partidas'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $rec['partidas'] - $rec['total'] != 0 ? number_format($rec['partidas'] - $rec['total'], 2) : '&nbsp;');
					$tpl->assign('ganancia', $rec['ganancia'] != 0 ? number_format($rec['ganancia'], 2) : '&nbsp;');
					$tpl->assign('abonos', $rec['abonos'] != 0 ? number_format($rec['abonos'], 2) : '&nbsp;');
					$tpl->assign('devuelto', $rec['devuelto'] != 0 ? number_format($rec['devuelto'], 2) : '&nbsp;');

					$porcentaje_devuelto = $rec['total'] > 0 ? round($rec['devuelto'] * 100 / $rec['total'], 2) : 0;

					$tpl->assign('porcentaje_devuelto', $porcentaje_devuelto != 0 ? number_format($porcentaje_devuelto, 2) : '&nbsp;');

					$tpl->assign('rezago_final', $rec['rezago_final'] != 0 ? number_format($rec['rezago_final'], 2) : '&nbsp;');
					$tpl->assign('dias', $dias > 0 ? ($dias >= 90 ? '<span style="float:left; color:#C00; font-weight:bold;">EXCEDIDO</span>' : '') . '<a href="#" alt="' . htmlentities(json_encode(array(
						'num_cia'    => intval($rec['num_cia']),
						'num_exp'    => intval($rec['num_exp']),
						'nombre_exp' => utf8_encode($rec['nombre_exp']),
						'fecha1'     => $rec['primer_dia'],
						'fecha2'     => $rec['ultimo_dia'],
						'dias'       => intval($dias)
					))) . '">' . $dias . '</a>' : '&nbsp;');

					$diferencia_devolucion = $rec['partidas'] - $rec['total'] + $rec['devuelto'];

					$pdiferencia_pdevolucion = $rec['ganancia'] + $porcentaje_devuelto;

					$tpl->assign('diferencia_devolucion', $diferencia_devolucion != 0 ? number_format($diferencia_devolucion, 2) : '&nbsp;');
					$tpl->assign('pdiferencia_pdevolucion', $pdiferencia_pdevolucion != 0 ? number_format($pdiferencia_pdevolucion, 2) : '&nbsp;');

					if ($rec['rezago_inicial'] - $rec['rezago_final'] != 0) {

						$tpl->assign('diferencia_color', $rec['rezago_final'] < $rec['rezago_inicial'] ? ' red' : ' blue');

						$tpl->assign('diferencia_rezago', '<span style="float:left;">' . ($rec['rezago_final'] < $rec['rezago_inicial'] ? ' BAJO' : ' SUBIO') . ' </span>' . number_format(abs($rec['rezago_final'] - $rec['rezago_inicial']), 2));
					}
					else {
						$tpl->assign('diferencia_rezago', '&nbsp;');
					}

					$tpl->assign('abonos_promedio', $rec['abonos_promedio'] != 0 ? number_format($rec['abonos_promedio'], 2) : '&nbsp;');

					$totales['rezago_inicial'] += $rec['rezago_inicial'];
					$totales['partidas'] += $rec['partidas'];
					$totales['total'] += $rec['total'];
					$totales['diferencia'] += $rec['partidas'] - $rec['total'];
					$totales['ganancia'] += $rec['ganancia'];
					$totales['abonos'] += $rec['abonos'];
					$totales['devuelto'] += $rec['devuelto'];
					$totales['porcentaje_devuelto'] += $porcentaje_devuelto;
					$totales['dias_devuelto'] += $porcentaje_devuelto != 0 ? 1 : 0;
					$totales['rezago_final'] += $rec['rezago_final'];
					$totales['diferencia_devolucion'] += $diferencia_devolucion;
					$totales['pdiferencia_pdevolucion'] += $pdiferencia_pdevolucion;
				}

				if ($num_cia != NULL) {
					foreach ($totales as $campo => $total) {
						$tpl->assign('reporte.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
					}

					$porcentaje_ganancia = $totales['dias_devuelto'] != 0 ? round($totales['ganancia'] / $totales['dias_devuelto'], 2) : 0;
					$porcentaje_devuelto = $totales['dias_devuelto'] != 0 ? round($totales['porcentaje_devuelto'] / $totales['dias_devuelto'], 2) : 0;
					$porcentale_diferencia_devuelto = $totales['pdiferencia_pdevolucion'] != 0 &&$totales['dias_devuelto'] != 0 ? round($totales['pdiferencia_pdevolucion'] / $totales['dias_devuelto'], 2) : 0;

					$tpl->assign('reporte.ganancia', $porcentaje_ganancia != 0 ? number_format($porcentaje_ganancia, 2) : '&nbsp;');
					$tpl->assign('reporte.porcentaje_devuelto', $porcentaje_devuelto != 0 ? number_format($porcentaje_devuelto, 2) : '&nbsp;');
					$tpl->assign('reporte.pdiferencia_pdevolucion', $porcentale_diferencia_devuelto != 0 ? number_format($porcentale_diferencia_devuelto, 2) : '&nbsp;');
				}
			}

			$tpl->printToScreen();
		break;

		case 'reporteDetalladoDia':
			$condiciones = array();

			$condiciones[] = 'mov.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if ($cias) {
					$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['expendio']) && $_REQUEST['expendio'] != '') {
				$data = json_decode($_REQUEST['expendio']);

				$data[2] = '\'' . utf8_decode($data[2]) . '\'';

				$condiciones[] = '(mov.num_cia, mov.num_expendio, mov.nombre_expendio) IN (VALUES (' . implode(', ', $data) . '))';
			}

			if (isset($_REQUEST['agente_ventas']) && $_REQUEST['agente_ventas'] > 0) {
				$condiciones[] = 'ce.idagven = ' . $_REQUEST['agente_ventas'];
			}

			if ($db->query('
				SELECT
					idoperadora
				FROM
					catalogo_operadoras
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($db->query('
				SELECT
					idagven
				FROM
					catalogo_agentes_venta
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}

			$sql = '
				SELECT
					mov.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					mov.fecha,
					(
						SELECT
							SUM(rezago_anterior)
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND fecha = mov.fecha
					)
						AS rezago_inicial,
					SUM(pan_p_venta)
						AS partidas,
					SUM(pan_p_expendio)
						AS total,
					AVG(porc_ganancia)
						AS ganancia,
					SUM(abono)
						AS abonos,
					SUM(devolucion)
						AS devuelto,
					(
						SELECT
							SUM(rezago)
						FROM
							mov_expendios
						WHERE
							num_cia = mov.num_cia
							AND fecha = mov.fecha
					)
						AS rezago_final
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_expendios ce
						ON (ce.num_cia = mov.num_cia AND ce.num_expendio = mov.num_expendio AND ce.nombre = mov.nombre_expendio)
					LEFT JOIN catalogo_agentes_venta ca
						USING (idagven)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					mov.num_cia,
					nombre_cia,
					mov.fecha
				ORDER BY
					mov.num_cia,
					mov.fecha
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ExpendiosReporteDetalladoDia.tpl');
			$tpl->prepare();

			if ($result) {
				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							foreach ($totales as $campo => $total) {
								$tpl->assign('reporte.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
							}

							foreach ($totales as $campo => $total) {
								$tpl->assign('reporte.p' . $campo, $total != 0 ? number_format($total / $dias, 2) : '&nbsp;');
							}

							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$num_cia = $rec['num_cia'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);

						$tpl->assign('rezago_inicial', $rec['rezago_inicial'] != 0 ? number_format($rec['rezago_inicial'], 2) : '&nbsp;');

						$totales = array(
							'partidas'       => 0,
							'total'          => 0,
							'diferencia'     => 0,
							'abonos'         => 0,
							'devuelto'       => 0
						);

						$dias = 0;
					}

					$tpl->newBlock('dia');

					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('rezago_inicial', $rec['rezago_inicial'] != 0 ? number_format($rec['rezago_inicial'], 2) : '&nbsp;');
					$tpl->assign('partidas', $rec['partidas'] != 0 ? number_format($rec['partidas'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $rec['partidas'] - $rec['total'] != 0 ? number_format($rec['partidas'] - $rec['total'], 2) : '&nbsp;');
					$tpl->assign('ganancia', $rec['ganancia'] != 0 ? number_format($rec['ganancia'], 2) : '&nbsp;');
					$tpl->assign('abonos', $rec['abonos'] != 0 ? number_format($rec['abonos'], 2) : '&nbsp;');
					$tpl->assign('devuelto', $rec['devuelto'] != 0 ? number_format($rec['devuelto'], 2) : '&nbsp;');
					$tpl->assign('rezago_final', $rec['rezago_final'] != 0 ? number_format($rec['rezago_final'], 2) : '&nbsp;');

					$totales['partidas'] += $rec['partidas'];
					$totales['total'] += $rec['total'];
					$totales['diferencia'] += $rec['partidas'] - $rec['total'];
					$totales['abonos'] += $rec['abonos'];
					$totales['devuelto'] += $rec['devuelto'];

					$tpl->assign('reporte.rezago_final', $rec['rezago_final'] != 0 ? number_format($rec['rezago_final'], 2) : '&nbsp;');

					$dias++;
				}

				if ($num_cia != NULL) {
					foreach ($totales as $campo => $total) {
						$tpl->assign('reporte.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
					}

					foreach ($totales as $campo => $total) {
						$tpl->assign('reporte.p' . $campo, $total != 0 ? number_format($total / $dias, 2) : '&nbsp;');
					}
				}
			}

			$tpl->printToScreen();
		break;

		case 'reporteDetalladoExpendiosDia':
			$condiciones = array();

			$condiciones[] = 'mov.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if ($cias) {
					$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['expendio']) && $_REQUEST['expendio'] != '') {
				$data = json_decode($_REQUEST['expendio']);

				$data[2] = '\'' . utf8_decode($data[2]) . '\'';

				$condiciones[] = '(mov.num_cia, mov.num_expendio, mov.nombre_expendio) IN (VALUES (' . implode(', ', $data) . '))';
			}

			if (isset($_REQUEST['agente_ventas']) && $_REQUEST['agente_ventas'] > 0) {
				$condiciones[] = 'ce.idagven = ' . $_REQUEST['agente_ventas'];
			}

			if ($db->query('
				SELECT
					idoperadora
				FROM
					catalogo_operadoras
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($db->query('
				SELECT
					idagven
				FROM
					catalogo_agentes_venta
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
			')) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}

			$sql = '
				SELECT
					mov.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					mov.num_expendio
						AS num_exp,
					mov.nombre_expendio
						AS nombre_exp,
					mov.fecha,
					mov.rezago_anterior
						AS rezago_inicial,
					mov.pan_p_venta
						AS partidas,
					mov.pan_p_expendio
						AS total,
					mov.porc_ganancia
						AS ganancia,
					mov.abono
						AS abonos,
					mov.devolucion
						AS devuelto,
					mov.rezago
						AS rezago_final
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_expendios ce
						ON (ce.num_cia = mov.num_cia AND ce.num_expendio = mov.num_expendio AND ce.nombre = mov.nombre_expendio)
					LEFT JOIN catalogo_agentes_venta ca
						USING (idagven)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					mov.num_cia,
					num_exp,
					nombre_exp,
					mov.fecha
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ExpendiosReporteDetalladoExpendiosDia.tpl');
			$tpl->prepare();

			if ($result) {
				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							foreach ($totales as $campo => $total) {
								$tpl->assign('expendio.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
							}

							foreach ($totales as $campo => $total) {
								$tpl->assign('expendio.p' . $campo, $total != 0 ? number_format($total / $dias, 2) : '&nbsp;');
							}

							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$num_cia = $rec['num_cia'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);

						$num_exp = NULL;
						$nombre_exp = NULL;
					}

					if ($num_exp != $rec['num_exp'] && $nombre_exp != $rec['nombre_exp']) {
						if ($nombre_exp != NULL) {
							foreach ($totales as $campo => $total) {
								$tpl->assign('expendio.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
							}

							foreach ($totales as $campo => $total) {
								$tpl->assign('expendio.p' . $campo, $total != 0 ? number_format($total / $dias, 2) : '&nbsp;');
							}
						}

						$num_exp = $rec['num_exp'];
						$nombre_exp = $rec['nombre_exp'];

						$tpl->newBlock('expendio');

						$tpl->assign('num_exp', $rec['num_exp']);
						$tpl->assign('nombre_exp', utf8_encode($rec['nombre_exp']));

						$tpl->assign('rezago_inicial', $rec['rezago_inicial'] != 0 ? number_format($rec['rezago_inicial'], 2) : '&nbsp;');

						$totales = array(
							'partidas'       => 0,
							'total'          => 0,
							'diferencia'     => 0,
							'abonos'         => 0,
							'devuelto'       => 0
						);

						$dias = 0;
					}

					$tpl->newBlock('dia');

					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('rezago_inicial', $rec['rezago_inicial'] != 0 ? number_format($rec['rezago_inicial'], 2) : '&nbsp;');
					$tpl->assign('partidas', $rec['partidas'] != 0 ? number_format($rec['partidas'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $rec['partidas'] - $rec['total'] != 0 ? number_format($rec['partidas'] - $rec['total'], 2) : '&nbsp;');
					$tpl->assign('ganancia', $rec['ganancia'] != 0 ? number_format($rec['ganancia'], 2) : '&nbsp;');
					$tpl->assign('abonos', $rec['abonos'] != 0 ? number_format($rec['abonos'], 2) : '&nbsp;');
					$tpl->assign('devuelto', $rec['devuelto'] != 0 ? number_format($rec['devuelto'], 2) : '&nbsp;');
					$tpl->assign('rezago_final', $rec['rezago_final'] != 0 ? number_format($rec['rezago_final'], 2) : '&nbsp;');

					$totales['partidas'] += $rec['partidas'];
					$totales['total'] += $rec['total'];
					$totales['diferencia'] += $rec['partidas'] - $rec['total'];
					$totales['abonos'] += $rec['abonos'];
					$totales['devuelto'] += $rec['devuelto'];

					$tpl->assign('expendio.rezago_final', $rec['rezago_final'] != 0 ? number_format($rec['rezago_final'], 2) : '&nbsp;');

					$dias++;
				}

				if ($num_cia != NULL) {
					foreach ($totales as $campo => $total) {
						$tpl->assign('expendio.' . $campo, $total != 0 ? number_format($total, 2) : '&nbsp;');
					}

					foreach ($totales as $campo => $total) {
						$tpl->assign('expendio.p' . $campo, $total != 0 ? number_format($total / $dias, 2) : '&nbsp;');
					}
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ExpendiosReporte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('j') > 1 ? date('n') : date('n') - 1, 1)));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') > 1 ? date('j') - 1 : 0)));

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

$sql = '
	SELECT
		idagven
			AS value,
		nombre
			AS text
	FROM
		catalogo_agentes_venta
	ORDER BY
		text
';

$agentes = $db->query($sql);

if ($agentes) {
	foreach ($agentes AS $a) {
		$tpl->newBlock('agente_ventas');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
