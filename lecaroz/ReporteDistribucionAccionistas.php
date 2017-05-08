<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			/*
			@ Obtener compañías y totales segun sea el caso
			*/
			switch ($_REQUEST['tipo']) {
				/*
				@ Porcentajes
				*/
				case 0:
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							sum(porcentaje)
								AS
									total
						FROM
								accionistas
							LEFT JOIN
								catalogo_companias
									USING
										(
											num_cia
										)
						' . ($_SESSION['tipo_usuario'] == 2 ? 'WHERE num_cia BETWEEN 900 AND 998' : 'WHERE num_cia BETWEEN 1 AND 899') . '
						GROUP BY
							num_cia,
							nombre_corto
						ORDER BY
							num_cia
					';
					$result = $db->query($sql);

					/*
					@ Reordenar compañías
					*/
					$cias = array();
					foreach ($result as $r) {
						$cias[$r['num_cia']] = array(
							'nombre' => $r['nombre'],
							'total' => $r['total']
						);
					}

					$precision = 4;
				break;

				/*
				@ Efectivo
				*/
				case 1:
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							(
								SELECT
									sum(importe)
								FROM
									otros_depositos
										WHERE
												num_cia = a.num_cia
											AND
												fecha
													BETWEEN
															\'' . $fecha1 . '\'
														AND
															\'' . $fecha2 . '\'
							)
								AS
									depositos,
							CASE
								WHEN num_cia < 900 THEN
									(
										SELECT
											sum(
												CASE
													WHEN tipo_mov = \'TRUE\' THEN
														importe
													ELSE
														-importe
												END
											)
										FROM
											gastos_caja
										WHERE
												num_cia = a.num_cia
											AND
												fecha
													BETWEEN
															\'' . $fecha1 . '\'
														AND
															\'' . $fecha2 . '\'
									)
								ELSE
									COALESCE((
										SELECT
											sum(
												CASE
													WHEN tipo_mov = \'TRUE\' THEN
														importe
													ELSE
														-importe
												END
											)
										FROM
											gastos_caja
										WHERE
												num_cia = a.num_cia
											AND
												fecha
													BETWEEN
															\'' . $fecha1 . '\'
														AND
															\'' . $fecha2 . '\'
									), 0) - COALESCE((
										SELECT
											sum(importe)
										FROM
											otros_depositos
										WHERE
												num_cia = a.num_cia
											AND
												fecha
													BETWEEN
															\'' . $fecha1 . '\'
														AND
															\'' . $fecha2 . '\'
											AND idnombre > 0
											AND (
												num_fact1 != \'\'
												OR num_fact2 != \'\'
												OR num_fact3 != \'\'
												OR num_fact4 != \'\'
											)
									), 0)
							END
								AS
									gastos
						FROM
								accionistas
									a
							LEFT JOIN
								catalogo_companias
									cc
										USING
											(
												num_cia
											)
						' . ($_SESSION['tipo_usuario'] == 2 ? 'WHERE num_cia BETWEEN 900 AND 998' : 'WHERE num_cia BETWEEN 1 AND 899') . '
						GROUP BY
							num_cia,
							nombre_corto
						ORDER BY
							num_cia
					';
					$result = $db->query($sql);

					/*
					@ Reordenar compañías
					*/
					$cias = array();
					$total = 0;
					foreach ($result as $r) {
						$cias[$r['num_cia']] = array(
							'nombre' => $r['nombre'],
							'total' => $r['depositos'] + $r['gastos']
						);

						$total += $r['depositos'] + $r['gastos'];
					}

					$precision = 2;
				break;

				case 2:
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							CASE
								WHEN tipo_cia = 1 THEN
									(
										SELECT
											utilidad_neta + COALESCE((
												SELECT
													ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
												FROM
													estado_cuenta
													LEFT JOIN catalogo_companias ccec
														USING (num_cia)
												WHERE
													((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = a.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = a.num_cia AND tipo_cia = 2))
													AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
													AND cod_mov IN (1, 16)
											), 0)
										FROM
											balances_pan bal
										WHERE
												num_cia = a.num_cia
											AND
												anio = ' . $_REQUEST['anio'] . '
											AND
												mes = ' . $_REQUEST['mes'] . '
									)
								WHEN tipo_cia = 4 THEN
									(
										SELECT
											utilidad_neta
										FROM
											balances_zap
										WHERE
												num_cia = a.num_cia
											AND
												anio = ' . $_REQUEST['anio'] . '
											AND
												mes = ' . $_REQUEST['mes'] . '
									)
								WHEN tipo_cia = 2 THEN
									(
										SELECT
											utilidad_neta - COALESCE((
												SELECT
													ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
												FROM
													estado_cuenta
												WHERE
													((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
													AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
													AND cod_mov IN (1, 16)
											), 0)
										FROM
											balances_ros bal
										WHERE
												num_cia = a.num_cia
											AND
												anio = ' . $_REQUEST['anio'] . '
											AND
												mes = ' . $_REQUEST['mes'] . '
									)
							END
								AS
									total
						FROM
								accionistas
									a
							LEFT JOIN
								catalogo_companias
									cc
										USING
											(
												num_cia
											)
						' . ($_SESSION['tipo_usuario'] == 2 ? 'WHERE num_cia BETWEEN 900 AND 998' : 'WHERE num_cia BETWEEN 1 AND 899') . '
						GROUP BY
							num_cia,
							nombre_corto,
							tipo_cia,
							persona_fis_moral
						ORDER BY
							num_cia
					';

					$result = $db->query($sql);

					/*
					@ Reordenar compañías
					*/
					$cias = array();
					$total = 0;
					foreach ($result as $r) {
						$cias[$r['num_cia']] = array(
							'nombre' => $r['nombre'],
							'total' => $r['total']
						);

						$total += $r['total'];
					}

					$precision = 2;
				break;
			}

			/*
			@ Obtener accionistas
			*/
			$sql = '
				SELECT
					accionista
						AS
							num,
					nombre_corto
						AS
							nombre
				FROM
						accionistas
							a
					LEFT JOIN
						catalogo_accionistas
							ca
								ON
									(
										ca.id = a.accionista
									)
				WHERE
					a.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
				GROUP BY
					accionista,
					nombre_corto
				ORDER BY
					accionista
			';
			$result = $db->query($sql);

			/*
			@ Reordenar accionistas por bloques de 7 registros
			*/
			$bloques = array();
			$bloque = 0;
			foreach ($result as $i => $r) {
				if (($i + 1) % 7 == 1) {
					$bloque++;
				}

				$bloques[$bloque][] = array(
					'num' => $r['num'],
					'nombre' => $r['nombre'],
					'total' => 0
				);
			}

			/*
			@ Obtener porcentajes de distribución
			*/
			$sql = '
				SELECT
					num_cia,
					accionista
						AS
							num,
					porcentaje
				FROM
					accionistas
				WHERE
					porcentaje > 0
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
				ORDER BY
					num_cia,
					num
			';
			$result = $db->query($sql);

			/*
			@ Reordenar porcentajes por compañía y accionista
			*/
			$porcentajes = array();
			foreach ($result as $r) {
				$porcentajes[$r['num_cia']][$r['num']] = $r['porcentaje'];
			}

			$tpl = new TemplatePower('plantillas/adm/ReporteAccionistas.tpl');
			$tpl->prepare();

			/*
			@ Recorrer bloques de accionistas
			*/

			$max_filas = 60;
			$nuevo_bloque = NULL;
			foreach ($bloques as $bloque => $accionistas) {
				if ($_REQUEST['tipo'] != 0 && $nuevo_bloque != NULL) {
					$tpl->newBlock('totales');
					$tpl->assign('total', number_format($total, $precision, '.', ','));

					foreach ($bloques[$nuevo_bloque] as $i => $accionista) {
						$tpl->assign('total' . $i, number_format($accionista['total'], $precision, '.', ','));
					}
				}

				$filas = $max_filas;

				$nuevo_bloque = $bloque;

				/*
				@ Recorrer compañías
				*/
				foreach ($cias as $cia => $datos) {
					if ($filas == $max_filas) {
						$tpl->newBlock('reporte');

						foreach ($accionistas as $i => $accionista) {
							$tpl->assign('accionista' . $i, $accionista['nombre']);
						}

						$filas = 0;
					}

					/*
					@ Saltar compañías sin total
					*/
					if (round($datos['total'], 2) == 0) {
						continue;
					}

					/*
					@ Saltar compañías que no tengan un porcentaje de distribución para el bloque activo
					*/
					$total_bloque = 0;
					foreach ($accionistas as $accionista) {
						if (isset($porcentajes[$cia][$accionista['num']])) {
							$total_bloque += $porcentajes[$cia][$accionista['num']];
						}
					}
					if ($total_bloque == 0) {
						continue;
					}

					/*
					@ Crear fila para la compañía
					*/
					$tpl->newBlock('fila');
					$tpl->assign('num_cia', $cia);
					$tpl->assign('nombre', $datos['nombre']);
					$tpl->assign('total', number_format($datos['total'], $precision, '.', ','));
					$tpl->assign('color', $datos['total'] <= 0 ? ' red' : ' blue');
					$filas++;

					/*
					@ Buscar por accionista el porcentaje de distribución
					*/
					foreach ($accionistas as $i => $accionista) {
						if (isset($porcentajes[$cia][$accionista['num']])) {
							if ($_REQUEST['tipo'] == 0) {
								$tpl->assign('valor' . $i, number_format($porcentajes[$cia][$accionista['num']], $precision, '.', ','));
							}
							else {
								$valor = $datos['total'] * $porcentajes[$cia][$accionista['num']] / 100;
								$bloques[$bloque][$i]['total'] += $valor;

								$tpl->assign('valor' . $i, number_format($valor, $precision, '.', ','));
								$tpl->assign('color' . $i, $valor <= 0 ? ' red' : '');
							}
						}
						else {
							$tpl->assign('valor' . $i, '&nbsp;');
						}
					}
				}
			}

			if ($_REQUEST['tipo'] != 0 && $nuevo_bloque != NULL) {
				$tpl->newBlock('totales');
				$tpl->assign('total', number_format($total, $precision, '.', ','));

				foreach ($bloques[$nuevo_bloque] as $i => $accionista) {
					$tpl->assign('total' . $i, number_format($accionista['total'], $precision, '.', ','));
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/adm/ReporteDistribucionAccionistas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$tpl->printToScreen();
?>
