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

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
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

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];

			$condiciones[] = 'fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'numcia <= 300';

			$condiciones[] = 'codturno IN (1, 2, 3, 4)';

			$condiciones[] = 'total_produccion > 0';

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'codturno IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			/*
			@ Intervalo de compañías
			*/
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

				if (count($cias) > 0) {
					$condiciones[] = 'numcia IN (' . implode(', ', $cias) . ')';
				}
			}

			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ Operadora
			*/
			if (isset($_REQUEST['operadora']) && $_REQUEST['operadora'] > 0) {
				$condiciones[] = 'idoperadora = ' . $_REQUEST['operadora'];
			}

			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 57, 50))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}

			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

			$tpl = new TemplatePower('plantillas/pan/' . ($isIpad ? 'RendimientosHarinaReporteIpad.tpl' : 'RendimientosHarinaReporte.tpl'));
			$tpl->prepare();

			if ($_REQUEST['tipo'] == 'efectivos') {
				$sql = '
					SELECT
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						fecha_total
							AS fecha,
						EXTRACT(day FROM fecha_total)
							AS dia,
						EXTRACT(month FROM fecha_total)
							AS mes,
						EXTRACT(year FROM fecha_total)
							AS anio,
						codturno
							AS turno,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento,
						SUM(efectivo)
							AS
								efectivo
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN total_panaderias efe
							ON (efe.num_cia = tp.numcia AND efe.fecha = fecha_total)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						numcia,
						nombre_cia,
						fecha_total,
						dia,
						mes,
						anio,
						turno
					ORDER BY
						num_cia,
						dia,
						turno
				';

				$result = $db->query($sql);

				if ($result) {
					$data = array();

					$num_cia = NULL;
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];

							$data[$num_cia]['nombre_cia'] = $rec['nombre_cia'];

							$data[$num_cia]['dias'] = array_fill(1, $fecha_pieces[0], array(
								'efectivo' => 0,
								'turnos' => array_fill(1, 4, array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								))
							));

							$dia = NULL;
						}

						if ($dia != $rec['dia']) {
							$dia = $rec['dia'];

							$data[$num_cia]['dias'][$dia]['efectivo'] = $rec['efectivo'];
						}

						$data[$num_cia]['dias'][$dia]['turnos'][$rec['turno']] = array(
							'consumo'     => $rec['consumo'],
							'produccion'  => $rec['produccion'],
							'rendimiento' => $rec['rendimiento']
						);
					}

					foreach ($data as $cia => $data_cia) {
						$tpl->newBlock('reporte3');

						$tpl->assign('num_cia', $cia);
						$tpl->assign('nombre_cia', utf8_encode($data_cia['nombre_cia']));

						$tpl->assign('dia', $fecha_pieces[0]);
						$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
						$tpl->assign('anio', $fecha_pieces[2]);

						$totales = array(
							'consumo_1'     => 0,
							'rendimiento_1' => 0,
							'consumo_2'     => 0,
							'rendimiento_2' => 0,
							'consumo_3'     => 0,
							'rendimiento_3' => 0,
							'consumo_4'     => 0,
							'rendimiento_4' => 0,
							'efectivo'      => 0
						);

						$totales_produccion = array(
							1 => 0,
							2 => 0,
							3 => 0,
							4 => 0
						);

						foreach ($data_cia['dias'] as $dia => $data_dia) {
							$tpl->newBlock('row3');

							$tpl->assign('dia', $dia);

							$tpl->assign('efectivo', $data_dia['efectivo'] != 0 ? number_format($data_dia['efectivo'], 2, '.', ',') : '&nbsp;');

							$totales['efectivo'] += $data_dia['efectivo'];

							foreach ($data_dia['turnos'] as $turno => $data_turno) {
								$tpl->assign('consumo_' . $turno, $data_turno['consumo'] > 0 ? number_format($data_turno['consumo'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('rendimiento_' . $turno, $data_turno['rendimiento'] > 0 ? number_format($data_turno['rendimiento'], 2, '.', ',') : '&nbsp;');

								$totales['consumo_' . $turno] += $data_turno['consumo'];
								$totales_produccion[$turno] += $data_turno['produccion'];
								$totales['rendimiento_' . $turno] = $totales['consumo_' . $turno] > 0 ? round($totales_produccion[$turno] / $totales['consumo_' . $turno], 2) : 0;
							}
						}

						foreach ($totales as $campo => $total) {
							$tpl->assign('reporte3.' . $campo, $total != 0 ? number_format($total, 2, '.', ',') : '&nbsp;');
						}
					}
				}
			}
			else if ($_REQUEST['tipo'] == 'totales') {
				$sql = '
					SELECT
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						codturno
							AS turno,
						EXTRACT(day FROM MAX(fecha_total))
							AS
								dia,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						SUM(raya_pagada)
							AS raya,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						numcia,
						nombre_cia,
						turno
					ORDER BY
						turno,
						' . $_REQUEST['orden'] . '
				';

				$result = $db->query($sql);

				if ($result) {
					$rows_per_sheet = 43;

					$turno = NULL;

					foreach ($result as $rec) {
						if ($turno != $rec['turno'] || $rows == $rows_per_sheet) {
							$turno = $rec['turno'];

							$tpl->newBlock('reporte2');

							$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
							$tpl->assign('anio', $fecha_pieces[2]);

							switch ($turno) {
								case 1:
									$tpl->assign('turno', 'FRANCESERO DE DIA');
								break;

								case 2:
									$tpl->assign('turno', 'FRANCESERO DE NOCHE');
								break;

								case 3:
									$tpl->assign('turno', 'BIZCOCHERO');
								break;

								case 4:
									$tpl->assign('turno', 'REPOSTERO');
								break;
							}

							$rows = 0;
						}

						$tpl->newBlock('row2');

						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$tpl->assign('dia', $rec['dia']);
						$tpl->assign('consumo', $rec['consumo'] > 0 ? number_format($rec['consumo'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('produccion', $rec['produccion'] > 0 ? number_format($rec['produccion'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('raya', $rec['raya'] > 0 ? number_format($rec['raya'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('rendimiento', $rec['rendimiento'] > 0 ? number_format($rec['rendimiento'], 2, '.', ',') : '&nbsp;');

						$rows++;
					}
				}
			}
			else {
				$sql = '
					SELECT
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						fecha_total
							AS fecha,
						EXTRACT(day FROM fecha_total)
							AS dia,
						EXTRACT(month FROM fecha_total)
							AS mes,
						EXTRACT(year FROM fecha_total)
							AS anio,
						codturno
							AS turno,
						CASE
							WHEN codturno IN (1, 2) THEN
								1
							WHEN codturno IN (3, 4) THEN
								2
						END
							AS bloque,
						CASE
							WHEN codturno IN (1, 3) THEN
								1
							WHEN codturno IN (2, 4) THEN
								2
						END
							AS columna,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						SUM(raya_pagada)
							AS raya,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						numcia,
						nombre_cia,
						fecha_total,
						dia,
						mes,
						anio,
						bloque,
						turno
					ORDER BY
						num_cia,
						bloque,
						dia,
						columna
				';

				$result = $db->query($sql);

				if ($result) {
					$data = array();

					$num_cia = NULL;
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];

							$data[$num_cia]['nombre_cia'] = $rec['nombre_cia'];

							$bloque = NULL;
						}

						if ($bloque != $rec['bloque']) {
							$bloque = $rec['bloque'];

							$data[$num_cia]['bloques'][$bloque] = array_fill(1, $fecha_pieces[0], array(
								1 => array(
									'turno'       => NULL,
									'consumo'     => NULL,
									'produccion'  => NULL,
									'raya'        => NULL,
									'rendimiento' => NULL
								),
								2 => array(
									'turno'       => NULL,
									'consumo'     => NULL,
									'produccion'  => NULL,
									'raya'        => NULL,
									'rendimiento' => NULL
								)
							));
						}

						$data[$num_cia]['bloques'][$bloque][$rec['dia']][$rec['columna']] = array(
							'turno'       => $rec['turno'],
							'consumo'     => $rec['consumo'],
							'produccion'  => $rec['produccion'],
							'raya'        => $rec['raya'],
							'rendimiento' => $rec['rendimiento']
						);
					}

					$bloque_cont = 0;
					foreach ($data as $num_cia => $datos) {
						foreach ($datos['bloques'] as $num_bloque => $bloque) {
							$tpl->newBlock('reporte1');

							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', utf8_encode($datos['nombre_cia']));

							$tpl->assign('dia', $fecha_pieces[0]);
							$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
							$tpl->assign('anio', $fecha_pieces[2]);

							if ($num_bloque == 1) {
								$tpl->assign('turno_1', 'FRANCESERO DE DIA');
								$tpl->assign('turno_2', 'FRANCESERO DE NOCHE');
							}
							else if ($num_bloque == 2) {
								$tpl->assign('turno_1', 'BIZCOCHERO');
								$tpl->assign('turno_2', 'REPOSTERO');
							}

							$totales = array(
								'consumo_1'     => 0,
								'produccion_1'  => 0,
								'raya_1'        => 0,
								'rendimiento_1' => 0,
								'consumo_2'     => 0,
								'produccion_2'  => 0,
								'raya_2'        => 0,
								'rendimiento_2' => 0
							);

							foreach ($bloque as $dia => $columnas) {
								$tpl->newBlock('row1');

								foreach ($columnas as $columna => $rec) {
									$tpl->assign('dia_' . $columna, $dia);
									if ($rec['rendimiento'] != 0 || $rec['consumo'] != 0 || $rec['produccion'] != 0 || $rec['raya'] != 0) {
										$tpl->assign('consumo_' . $columna, $rec['consumo'] != 0 ? number_format($rec['consumo'], 2, '.', ',') : '&nbsp;');
										$tpl->assign('produccion_' . $columna, $rec['produccion'] != 0 ? number_format($rec['produccion'], 2, '.', ',') : '&nbsp;');
										$tpl->assign('raya_' . $columna, $rec['raya'] != 0 ? number_format($rec['raya'], 2, '.', ',') : '&nbsp;');
										$tpl->assign('rendimiento_' . $columna, $rec['rendimiento'] != 0 ? number_format($rec['rendimiento'], 2, '.', ',') : '&nbsp;');

										$totales['consumo_' . $columna] += $rec['consumo'];
										$totales['produccion_' . $columna] += $rec['produccion'];
										$totales['raya_' . $columna] += $rec['raya'];
										$totales['rendimiento_' . $columna] = $totales['consumo_' . $columna] > 0 ? round($totales['produccion_' . $columna] / $totales['consumo_' . $columna], 2) : 0;
									}
								}
							}

							foreach ($totales as $campo => $total) {
								$tpl->assign('reporte1.' . $campo, $total > 0 ? number_format($total, 2, '.', ',') : '&nbsp;');
							}
						}
					}
				}
			}

			$tpl->printToScreen();
		break;

		case 'exportar':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];

			$condiciones[] = 'fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'numcia <= 300';

			$condiciones[] = 'total_produccion > 0';

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'codturno IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			/*
			@ Intervalo de compañías
			*/
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

				if (count($cias) > 0) {
					$condiciones[] = 'numcia IN (' . implode(', ', $cias) . ')';
				}
			}

			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ Operadora
			*/
			if (isset($_REQUEST['operadora']) && $_REQUEST['operadora'] > 0) {
				$condiciones[] = 'idoperadora = ' . $_REQUEST['operadora'];
			}

			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 57, 50))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}

			$data = '';

			if ($_REQUEST['tipo'] == 'efectivos') {
				$sql = '
					SELECT
						nombre_administrador
							AS administrador,
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						fecha_total
							AS fecha,
						EXTRACT(day FROM fecha_total)
							AS dia,
						EXTRACT(month FROM fecha_total)
							AS mes,
						EXTRACT(year FROM fecha_total)
							AS anio,
						codturno
							AS turno,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento,
						SUM(efectivo)
							AS
								efectivo
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN total_panaderias efe
							ON (efe.num_cia = tp.numcia AND efe.fecha = fecha_total)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						administrador,
						numcia,
						nombre_cia,
						fecha_total,
						dia,
						mes,
						anio,
						turno
					ORDER BY
						administrador,
						num_cia,
						dia,
						turno
				';

				$tmp = $db->query($sql);

				if ($tmp) {
					$result = array();

					$admin = NULL;
					foreach ($tmp as $rec) {
						if ($admin != $rec['administrador']) {
							$admin = $rec['administrador'];

							$result[$admin] = array();

							$num_cia = NULL;
						}

						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];

							$result[$admin][$num_cia]['nombre_cia'] = $rec['nombre_cia'];

							$result[$admin][$num_cia]['dias'] = array_fill(1, $fecha_pieces[0], array());

							$dia = NULL;
						}

						if ($dia != $rec['dia']) {
							$dia = $rec['dia'];

							$result[$admin][$num_cia]['dias'][$dia]['efectivo'] = $rec['efectivo'];
							$result[$admin][$num_cia]['dias'][$dia]['turnos'] =  array(
								1 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								2 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								3 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								4 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
							);
						}

						$result[$admin][$num_cia]['dias'][$dia]['turnos'][$rec['turno']] = array(
							'consumo'     => $rec['consumo'],
							'produccion'  => $rec['produccion'],
							'rendimiento' => $rec['rendimiento']
						);
					}

					$data .= '"RENDIMIENTOS DE HARINA ' . $_meses[intval($fecha_pieces[1], 10)] . ' DE ' . $fecha_pieces[2] . '"' . "\r\n";
					$data .= "\r\n";
					$data .= '"","","","","FRANCESERO DE DIA","","FRANCESERO DE NOCHE","","BIZCOCHERO","","REPOSTERO"' . "\r\n";
					$data .= '"ADMINISTRADOR","#","PANADERIA","DIA","CONSUMO","RENDIMIENTO","CONSUMO","RENDIMIENTO","CONSUMO","RENDIMIENTO","CONSUMO","RENDIMIENTO","EFECTIVO"' . "\r\n";

					foreach ($result as $admin => $data_admin) {
						foreach ($data_admin as $cia => $data_cia) {
							$totales = array(
								1 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								2 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								3 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								),
								4 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'rendimiento' => 0
								)
							);

							$total_efectivo = 0;

							foreach ($data_cia['dias'] as $dia => $data_dia) {
								$data .= '"' . $admin . '","' . $cia . '","' . $data_cia['nombre_cia'] . '","' . $dia . '"';

								foreach ($data_dia['turnos'] as $turno => $data_turno) {
									$data .= ',"' . $data_turno['consumo'] . '","' . $data_turno['rendimiento'] . '"';

									foreach ($data_turno as $campo => $importe) {
										if ($campo != 'rendimiento') {
											$totales[$turno][$campo] += $importe;
										}
									}

									$totales[$turno]['rendimiento'] = $totales[$turno]['consumo'] > 0 ? round($totales[$turno]['produccion'] / $totales[$turno]['consumo'],2) : 0;
								}

								$data .= ',"' . $data_dia['efectivo'] . '"';

								$total_efectivo += $data_dia['efectivo'];

								$data .= "\r\n";
							}

							$data .= '"' . $admin . '","' . $cia . '","' . $data_cia['nombre_cia'] . '","TOTALES"';

							foreach ($totales as $turno => $importes) {
								$data .= ',"' . $importes['consumo'] . '","' . $importes['rendimiento'] . '"';
							}

							$data .= ',"' . $total_efectivo . '"';

							$data .= "\r\n";
						}
					}
				}
			}
			else if ($_REQUEST['tipo'] == 'totales') {
				$sql = '
					SELECT
						nombre_administrador
							AS administrador,
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						codturno
							AS turno,
						EXTRACT(day FROM MAX(fecha_total))
							AS
								dia,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						SUM(raya_pagada)
							AS raya,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						numcia,
						nombre_cia,
						administrador,
						turno
					ORDER BY
						turno,
						' . $_REQUEST['orden'] . '
				';

				$result = $db->query($sql);

				if ($result) {
					$turno = NULL;

					foreach ($result as $rec) {
						if ($turno != $rec['turno']) {
							if ($turno != NULL) {
								$data .= "\r\n";
							}

							$turno = $rec['turno'];

							$data .= '"RENDIMIENTOS DE HARINA ' . $_meses[intval($fecha_pieces[1], 10)] . ' DE ' . $fecha_pieces[2] . '"' . "\r\n";

							switch ($turno) {
								case 1:
									$data .= '"FRANCESERO DE DIA"' . "\r\n";
								break;

								case 2:
									$data .= '"FRANCESERO DE NOCHE"' . "\r\n";
								break;

								case 3:
									$data .= '"BIZCOCHERO"' . "\r\n";
								break;

								case 4:
									$data .= '"REPOSTERO"' . "\r\n";
								break;
							}

							$data .= "\r\n" . utf8_decode('"ADMINISTRADOR","#","COMPAÑIA","DIA","CONSUMO","PRODUCCION","RAYA","RENDIMIENTO"') . "\r\n";
						}

						unset($rec['turno']);

						$data .= '"' . implode('","', $rec) . '"' . "\r\n";
					}
				}
			}
			else {
				$sql = '
					SELECT
						nombre_administrador
							AS administrador,
						numcia
							AS num_cia,
						nombre_corto
							AS nombre_cia,
						EXTRACT(day FROM fecha_total)
							AS dia,
						codturno
							AS turno,
						SUM(cantidad / 44)
							AS consumo,
						SUM(total_produccion)
							AS produccion,
						SUM(raya_pagada)
							AS raya,
						CASE
							WHEN SUM(cantidad / 44) != 0 THEN
								ROUND((SUM(total_produccion) / SUM(cantidad / 44))::numeric, 2)
							ELSE
								0
						END
							AS rendimiento
					FROM
						total_produccion tp
						LEFT JOIN mov_inv_real mv
							ON (mv.num_cia = numcia AND mv.fecha = fecha_total AND cod_turno = codturno AND tipo_mov = TRUE AND codmp = 1)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = numcia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						administrador,
						numcia,
						nombre_cia,
						dia,
						turno
					ORDER BY
						administrador,
						num_cia,
						dia,
						turno
				';

				$tmp = $db->query($sql);

				if ($tmp) {
					$result = array();

					$admin = NULL;
					foreach ($tmp as $rec) {
						if ($admin != $rec['administrador']) {
							$admin = $rec['administrador'];

							$result[$admin] = array();

							$num_cia = NULL;
						}

						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];

							$result[$admin][$num_cia]['nombre_cia'] = $rec['nombre_cia'];

							$result[$admin][$num_cia]['dias'] = array_fill(1, $fecha_pieces[0], array(
								1 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								2 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								3 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								4 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								)
							));
						}

						$result[$admin][$num_cia]['dias'][$rec['dia']][$rec['turno']] = array(
							'consumo'     => $rec['consumo'],
							'produccion'  => $rec['produccion'],
							'raya'        => $rec['raya'],
							'rendimiento' => $rec['rendimiento']
						);
					}

					$data .= '"RENDIMIENTOS DE HARINA ' . $_meses[intval($fecha_pieces[1], 10)] . ' DE ' . $fecha_pieces[2] . '"' . "\r\n";
					$data .= "\r\n";
					$data .= '"","","","","FRANCESERO DE DIA","","","","FRANCESERO DE NOCHE","","","","BIZCOCHERO","","","","REPOSTERO"' . "\r\n";
					$data .= '"ADMINISTRADOR","#","PANADERIA","DIA","CONSUMO","PRODUCCION","RAYA","RENDIMIENTO","CONSUMO","PRODUCCION","RAYA","RENDIMIENTO","CONSUMO","PRODUCCION","RAYA","RENDIMIENTO","CONSUMO","PRODUCCION","RAYA","RENDIMIENTO"' . "\r\n";

					foreach ($result as $admin => $cias) {
						foreach ($cias as $cia => $datos) {
							$totales = array(
								1 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								2 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								3 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								),
								4 => array(
									'consumo'     => 0,
									'produccion'  => 0,
									'raya'        => 0,
									'rendimiento' => 0
								)
							);

							foreach ($datos['dias'] as $dia => $turnos) {
								$data .= '"' . $admin . '","' . $cia . '","' . $datos['nombre_cia'] . '","' . $dia . '"';

								foreach ($turnos as $turno => $importes) {
									$data .= ',"' . implode('","', $importes) . '"';

									foreach ($importes as $campo => $importe) {
										if ($campo != 'rendimiento') {
											$totales[$turno][$campo] += $importe;
										}
									}

									$totales[$turno]['rendimiento'] = $totales[$turno]['consumo'] > 0 ? round($totales[$turno]['produccion'] / $totales[$turno]['consumo'],2) : 0;
								}

								$data .= "\r\n";
							}

							$data .= '"' . $admin . '","' . $cia . '","' . $datos['nombre_cia'] . '","TOTALES"';

							foreach ($totales as $turno => $importes) {
								$data .= ',"' . implode('","', $importes) . '"';
							}

							$data .= "\r\n";
						}
					}
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="RendimientosHarina.csv"');

			echo $data;
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/RendimientosHarina.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

	$condiciones[] = 'num_cia <= 300';

	if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 57, 50))) {
		$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
	}

	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
			LEFT JOIN
				catalogo_operadoras co
					USING
						(
							idoperadora
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);

	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}
else {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

	$sql = '
		SELECT
			idadministrador
				AS
					id,
			nombre_administrador
				AS
					nombre
		FROM
			catalogo_administradores
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);

	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', utf8_encode($a['nombre']));
	}

	$sql = '
		SELECT
			idoperadora
				AS
					id,
			nombre_operadora
				AS
					nombre
		FROM
			catalogo_operadoras
		WHERE
			iduser IS NOT NULL
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);

	foreach ($admins as $a) {
		$tpl->newBlock('operadora');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', utf8_encode($a['nombre']));
	}
}

$tpl->printToScreen();
?>
