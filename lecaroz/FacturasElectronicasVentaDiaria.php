<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

//if (!in_array($_SESSION['iduser'], array(1/*, 10, 34*/))) die('EN PROCESO DE ACTUALIZACION.');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('d') < 5 ? date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('Y', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));
			$tpl->assign(date('d') < 5 ? date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('n', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))), ' selected');
			$tpl->assign('dia', date('d') < 5 ? date('d', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('j', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));

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

			foreach ($admins as $a) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $a['value']);
				$tpl->assign('text', utf8_encode($a['text']));
			}

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			/*
			@ Número de días del mes solicitado
			*/
			$dias_del_mes = date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			/*
			@ Crear rango con los días del mes
			*/
			$dias = range(1, $dias_del_mes);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));

			if (isset($_REQUEST['dia']) && $_REQUEST['dia'] > 0) {
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio']));
			}
			else {
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			}

			/*
			@ [05-Ene-2011] Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					matriz,
					sucursal,
					porcentaje,
					CASE
						WHEN sucursal = matriz THEN
							2
						ELSE
							1
					END
						AS tipo
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					matriz,
					tipo,
					sucursal
			';
			$result = $db->query($sql);

			$sucursales = array();
			if ($result) {
				/*
				@ Reordenar porcentajes
				*/
				foreach ($result as $rec) {
					$porcentajes[$rec['matriz']][] = array(
						'sucursal' => $rec['sucursal'],
						'porcentaje' => $rec['porcentaje'],
						'tipo' => $rec['tipo']
					);

					$sucursales[$rec['sucursal']] = $rec['matriz'];
				}
			}

			/*
			@ Condiciones [Estado de Cuenta]
			*/
			$condiciones1 = array();
			$condiciones2 = array();

			$condiciones1[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones1[] = 'cod_mov IN (1, 16, 44, 99)';

			$condiciones1[] = 'num_cia BETWEEN 1 AND 899';

			$condiciones1[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'FALSE' : 'TRUE') : 'TRUE';

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
					$condiciones1[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}
			}

			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();

				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}

				if (count($omitir) > 0) {
					$condiciones1[] = '((num_cia NOT IN (' . implode(', ', $omitir) . ') AND num_cia_sec IS NULL) OR num_cia_sec NOT IN (' . implode(', ', $omitir) . '))';
				}
			}

			$condiciones2[] = 'TRUE';

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			if (count($sucursales) > 0) {
				$condiciones2[] = 'num_cia NOT IN (' . implode(', ', array_keys($sucursales)) . ')';
			}

			// $condiciones2[] = "(num_cia, fecha) NOT IN (SELECT num_cia, fecha FROM ventas_soldado)";

			/*
			@ Obtener depositos del mes [Estado de Cuenta]
			*/
			$sql = '
				SELECT
					num_cia,
					fecha,
					dia,
					SUM(importe)
						AS importe
				FROM
					(
						SELECT
							CASE
								WHEN num_cia_sec IS NULL THEN
									num_cia
								WHEN num_cia_sec IS NOT NULL THEN
									num_cia_sec
							END
								AS num_cia,
							fecha,
							EXTRACT(day from fecha)
								AS dia,
							importe
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							' . implode(' AND ', $condiciones1) . '
					) result
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				GROUP BY
					num_cia,
					fecha,
					dia
			';

			/*
			@ [05-Ene-2011] Condiciones para desglose de puntos calientes
			*/
			$condiciones = array();

			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'FALSE' : 'TRUE') : 'TRUE';

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

				/*
				@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
				*/
				foreach ($cias as $c) {
					if (isset($sucursales[$c])) {
						foreach ($porcentajes[$sucursales[$c]] as $p) {
							$cias[] = $p['sucursal'];
						}
					}
				}

				$cias = array_unique($cias);

				if (count($cias) > 0) {
					$condiciones[] = 'sucursal IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();

				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}

				if (count($omitir) > 0) {
					$condiciones[] = 'sucursal NOT IN (' . implode(', ', $omitir) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ [05-Ene-2011] Obtener registros para desglose de puntos calientes
			*/
			$sql .= '
				UNION

				SELECT
					sucursal
						AS num_cia,
					\'' . $fecha1 . '\'::date
						AS fecha,
					EXTRACT(day from \'' . $fecha1 . '\'::date)
						AS dia,
					0
						AS importe
				FROM
					porcentajes_puntos_calientes ppc
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = ppc.sucursal)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			/*
			@ Condiciones [Ventas Zapaterias]
			*/
			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'num_cia BETWEEN 900 AND 998';

			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'TRUE' : 'FALSE') : 'TRUE';

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();

				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}

				if (count($omitir) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ Obtener depositos del mes [Ventas Zapaterias]
			*/
			$sql .= '
				UNION

				SELECT
					num_cia,
					fecha,
					EXTRACT(day from fecha)
						AS dia,
					importe
				FROM
					ventas_zapaterias vz
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			/*
			@ [19-Mar-2015] Condiciones para el soldado (17)
			*/
			// $condiciones = array();

			// $condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			// $condiciones[] = 'num_cia = 17';

			// $condiciones[] = ! in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'TRUE' : 'FALSE') : 'TRUE';

			// if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
			// 	$cias = array();

			// 	$pieces = explode(',', $_REQUEST['cias']);
			// 	foreach ($pieces as $piece) {
			// 		if (count($exp = explode('-', $piece)) > 1) {
			// 			$cias[] =  implode(', ', range($exp[0], $exp[1]));
			// 		}
			// 		else {
			// 			$cias[] = $piece;
			// 		}
			// 	}

			// 	if (count($cias) > 0) {
			// 		$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
			// 	$omitir = array();

			// 	$pieces = explode(',', $_REQUEST['omitir']);
			// 	foreach ($pieces as $piece) {
			// 		if (count($exp = explode('-', $piece)) > 1) {
			// 			$omitir[] =  implode(', ', range($exp[0], $exp[1]));
			// 		}
			// 		else {
			// 			$omitir[] = $piece;
			// 		}
			// 	}

			// 	if (count($omitir) > 0) {
			// 		$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
			// 	$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			// }

			/*
			@ [19-Mar-2015] Obtener depositos del soldado (17) del año 2013
			*/
			// $sql .= '
			// 	UNION

			// 	SELECT
			// 		num_cia,
			// 		fecha,
			// 		EXTRACT(day from fecha)
			// 			AS dia,
			// 		importe
			// 	FROM
			// 		ventas_soldado vsoldado
			// 		LEFT JOIN catalogo_companias cc
			// 			USING (num_cia)
			// 	WHERE
			// 		' . implode(' AND ', $condiciones) . '
			// ';

			$sql .= '
				ORDER BY
					num_cia,
					dia
			';

			$result = $db->query($sql);//echo $sql;

			/*
			@ Reordenar depósitos
			*/
			$lista_cias = array();

			$depositos = array();

			if ($result) {
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$lista_cias[] = $num_cia;
					}

					$depositos[$rec['num_cia']][$rec['dia']] = floatval($rec['importe']);
				}
			}

			/*
			@ [21-Ago-2011] Hacer copia de los depositos para calculo de diferencia de efectivos
			*/

			$depositos_copia = $depositos;

			/*
			@ [05-Ene-2011] Desglosar ventas de puntos calientes
			*/
			foreach ($sucursales as $sucursal => $matriz) {
				if (isset($depositos[$sucursal])) {
					if (!isset($depositos_matriz[$matriz])) {
						/*
						@ Obtener depósitos de la matriz
						*/
						$sql = '
							SELECT
								dia,
								SUM(importe)
									AS importe
							FROM
								(
									SELECT
										EXTRACT(day from fecha)
											AS dia,
										importe
									FROM
										estado_cuenta
										LEFT JOIN catalogo_companias
											USING (num_cia)
									WHERE
										(
											(
												num_cia IN (' . $matriz . ')
												AND num_cia_sec IS NULL
											)
											OR num_cia_sec IN (' . $matriz . ')
										)
										AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND cod_mov IN (1, 16, 44, 99)
								) result
							GROUP BY
								dia
							ORDER BY
								dia
						';

						$result = $db->query($sql);

						/*
						@ Reordenar los depósitos de la matriz
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_matriz[$matriz][$rec['dia']] = floatval($rec['importe']);

								$depositos_copia[$matriz][$rec['dia']] = floatval($rec['importe']);
							}
						}

						/*
						@ Obtener depósitos de sucursales
						*/
						$sql = '
							SELECT
								num_cia,
								EXTRACT(day from fecha)
									AS
										dia,
								importe
							FROM
								ventas_sucursales
							WHERE
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND
									num_cia
										IN
											(
												SELECT
													sucursal
												FROM
													porcentajes_puntos_calientes
												WHERE
													matriz = ' . $matriz . '
											)
							ORDER BY
								num_cia,
								dia
						';

						$result = $db->query($sql);

						/*
						@ Reordenar los depósitos de las sucursales
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_sucursal[$rec['num_cia']][$rec['dia']] = array(
									'importe' => $rec['importe'],
									'status'  => TRUE
								);
							}
						}

						/*
						@ Desglosar depósitos entre todas las sucursales
						*/
						if (isset($depositos_matriz[$matriz])) {
							foreach ($depositos_matriz[$matriz] as $dia => $importe) {
								if ($importe > 0) {
									$total_sucursales = 0;

									foreach ($porcentajes[$matriz] as $por) {
										/*
										@ Es sucursal y no se ha generado la venta del día
										*/
										if ($por['tipo'] == 1 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$porcentaje = $por['porcentaje'] + round(mt_rand(-99, 99) / 100, 2);

											$importe_sucursal = round($importe * $porcentaje / 100, 2);

											$total_sucursales += $importe_sucursal;

											$depositos_sucursal[$por['sucursal']][$dia] = array(
												'importe' => $importe_sucursal,
												'status'  => FALSE
											);
										}
										/*
										@ Es sucursal y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 1 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia]['importe'];
										}
										/*
										@ Es matriz y no se ha generado la venta del día
										*/
										else if ($por['tipo'] == 2 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$depositos_sucursal[$por['sucursal']][$dia] = array(
												'importe' => $importe - $total_sucursales,
												'status'  => FALSE
											);

											$total_sucursales += $importe - $total_sucursales;
										}
										/*
										@ Es matriz y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 2 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											//$depositos_sucursal[$por['sucursal']][$dia] = $depositos_sucursal[$por['sucursal']][$dia];

											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia]['importe'];
										}
									}

									/*
									@ [17-Ago-2011] Comparar variación de los depósitos del día y la suma total de las sucursales
									*/
									if ($total_sucursales != $importe) {
										$dif = round($importe - $total_sucursales, 2);

										foreach ($porcentajes[$matriz] as $por) {
											if (!$depositos_sucursal[$por['sucursal']][$dia]['status']) {
												if ($dif > 0 || ($dif < 0 && abs($dif) < $depositos_sucursal[$por['sucursal']][$dia]['importe'])) {
													$depositos_sucursal[$por['sucursal']][$dia]['importe'] += $dif;

													break;
												}
												else if ($dif < 0 && abs($dif) > $depositos_sucursal[$por['sucursal']][$dia]['importe']) {
													$dif += round($depositos_sucursal[$por['sucursal']][$dia]['importe'] / 2, 2);

													$depositos_sucursal[$por['sucursal']][$dia]['importe'] -= round($depositos_sucursal[$por['sucursal']][$dia]['importe'] / 2, 2);
												}
											}
										}

										$depositos_sucursal[$por['sucursal']][$dia]['importe'] += $dif;
									}
								}
							}
						}
					}

					if (isset($depositos_sucursal[$sucursal])) {
						$depositos[$sucursal] = array();

						foreach ($depositos_sucursal[$sucursal] as $dia => $dep) {
							$depositos[$sucursal][$dia] = $dep['importe'];
						}

						//$depositos[$sucursal] = $depositos_sucursal[$sucursal];
					}
					else {
						unset($depositos[$sucursal]);
					}
				}
			}

			if (count($depositos) > 0) {
				/*
				@ Condiciones para obtener facturas de clientes en tránsito
				*/
				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$condiciones[] = 'RFC <> \'XAXX010101000\'';

				$condiciones[] = 'tsreg IS NULL';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				/*
				@ Obtener facturas de clientes en tránsito del mes
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						COUNT(num_cia)
							AS cantidad,
						SUM(total)
							AS importe
					FROM
						(
							SELECT
								num_cia,
								fecha,
								consecutivo,
								nombre_cliente,
								total
							FROM
								facturas_panaderias_tmp
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								fecha,
								consecutivo,
								nombre_cliente,
								total
						) result
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				$facturas_transito = array();

				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_transito[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe'  => $rec['importe']
						);
					}
				}

				/*
				@ Condiciones para obtener facturas de clientes
				*/
				$condiciones = array();

				$condiciones[] = 'fecha_pago BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$condiciones[] = 'tipo = 2';

				$condiciones[] = 'status = 1';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				/*
				@ Obtener facturas de clientes del mes
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha_pago)
							AS dia,
						COUNT(id)
							AS cantidad,
						SUM(total)
							AS importe
					FROM
						facturas_electronicas
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

				$facturas_clientes = array();

				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_clientes[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe'  => $rec['importe']
						);
					}
				}

				/*
				@ Condiciones para obtener facturas de venta del mes
				*/
				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$condiciones[] = 'tipo = 1';

				$condiciones[] = 'status = 1';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				/*
				@ Obtener facturas de venta mes
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						iduser_ins
							AS iduser,
						COUNT(id)
							AS cantidad,
						SUM(total)
							AS importe
					FROM
						facturas_electronicas
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia,
						iduser
					ORDER BY
						num_cia,
						dia
				';
				$result = $db->query($sql);

				$facturas_venta = array();

				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_venta[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe'  => $rec['importe'],
							'iduser'   => $rec['iduser']
						);
					}
				}

				/*
				@ Condiciones para obtener facturas electrónicas del mes [CANCELADAS]
				*/
				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$condiciones[] = 'tipo = 1';

				$condiciones[] = 'status = 0';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				/*
				@ Obtener facturas electrónicas del mes generadas en panaderías [CANCELADAS]
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						MAX(consecutivo)
							AS folio
					FROM
						facturas_electronicas
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

				$facturas_venta_canceladas = array();

				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_venta_canceladas[$rec['num_cia']][$rec['dia']] = $rec['folio'];
					}
				}

				/*
				@ [11-Mar-2011] Condiciones para obtener diferencia iniciales
				*/
				$condiciones = array();

				$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';

				$condiciones[] = 'anio = ' . date('Y', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));

				$condiciones[] = 'mes = ' . date('n', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));

				/*
				@ [11-Mar-2011] Obtener diferencias iniciales
				*/
				$sql = '
					SELECT
						num_cia,
						diferencia
					FROM
						diferencia_ventas
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);

				$diferencias_iniciales = array();

				/*
				@ [11-Mar-2011] Reordenar diferencias iniciales
				*/
				if ($result) {
					foreach ($result as $rec) {
						$diferencias_iniciales[$rec['num_cia']] = floatval($rec['diferencia']);
					}
				}


				/*
				@ [21-Ago-2011] Recavar información para calculo de diferencia de efectivos del mes
				*/

				/*
				@ [21-Ago-2011] Obtener efectivos del mes
				*/

				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						efectivo
					FROM
						total_panaderias
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						efectivo
					FROM
						total_companias
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						efectivo
					FROM
						total_zapaterias
					WHERE
						' . implode(' AND ', $condiciones) . '

					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				/*
				@ [21-Ago-2011] Reordenar efectivos
				*/

				$efectivos = array();

				if ($result) {
					foreach ($result as $rec) {
						$efectivos[$rec['num_cia']][$rec['dia']] = $rec['efectivo'];
					}
				}

				/*
				@ [21-Ago-2011] Obtener otros depositos del mes
				*/

				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

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

					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}

					$cias = array_unique($cias);

					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();

					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}

					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}

				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
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

				/*
				@ [21-Ago-2011] Reordenar otros depositos
				*/

				$otros_depositos = array();

				if ($result) {
					foreach ($result as $rec) {
						$otros_depositos[$rec['num_cia']][$rec['dia']] = $rec['importe'];
					}
				}

				/*
				@ [21-Ago-2011] Calcular diferencia de efectivos
				*/

				$diferencia_efectivos = array();

				foreach ($efectivos as $num_cia => $efectivo) {
					foreach ($efectivo as $dia => $importe) {
						$diferencia = $importe - (isset($depositos_copia[$num_cia][$dia]) ? $depositos_copia[$num_cia][$dia] : 0) - (isset($otros_depositos[$num_cia][$dia]) ? $otros_depositos[$num_cia][$dia] : 0);

						if ($diferencia < -100 || $diferencia > 100) {
							$diferencia_efectivos[$num_cia][$dia] = round($diferencia, 2);
						}
					}
				}

				/*
				@ Condiciones para obtener compañías
				*/
				$condiciones = array();

				$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';

				/*
				@ Obtener compañías
				*/
				$sql = '
					SELECT
						num_cia,
						nombre
							AS
								nombre_cia
					FROM
						catalogo_companias
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);

				$companias = array();

				foreach ($result as $rec) {
					$companias[$rec['num_cia']] = $rec['nombre_cia'];
				}

				/*
				@ [22-Ago-2011] Obtener usuarios
				*/
				 $sql = '
				 	SELECT
						iduser,
						username
					FROM
						auth
					ORDER BY
						iduser
				 ';

				 $result = $db->query($sql);

				 $usuarios = array();

				 foreach ($result as $rec) {
					 $usuarios[$rec['iduser']] = $rec['username'];
				 }

				/*
				@ Generar listado
				*/
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaResultado.tpl');
				$tpl->prepare();

				$index = 0;

				foreach ($companias as $num_cia => $nombre_cia) {
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($nombre_cia));

					$tpl->assign('diferencia_inicial', isset($diferencias_iniciales[$num_cia]) ? number_format($diferencias_iniciales[$num_cia], 2, '.', ',') : 0);

					$arrastre_diferencia = isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0;

					$totales = array(
						'depositos'         => 0,
						'facturas_transito' => 0,
						'facturas_clientes' => 0,
						'facturas_venta'    => 0,
						'diferencia'        => isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0
					);

					$es_sucursal = in_array($num_cia, array_keys($sucursales));

					$periodo1 = '';
					$periodo2 = '';

					$color = FALSE;
					foreach ($dias as $dia) {
						$tpl->newBlock('dia');
						$tpl->assign('color_row', $color ? 'on' : 'off');
						$tpl->assign('dia', $dia);
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('index', $index);

						$index++;

						$color = !$color;

						$_depositos = isset($depositos[$num_cia][$dia]) ? $depositos[$num_cia][$dia] : 0;

						/*
						@ AJUSTE TEMPORAL
						@ A LAS COMPAÑIAS 31, 34, 49, 69, 79, 92, 37, 68, 73, 115 DEL 13/06/2011 AL 30/06/2011 AUMENTAR $4,000.00 AL EFECTIVO
						@ [20-JUL-2011] APLICAR TAMBIEN PARA TODO EL MES DE JULIO DE 2011 EXCEPTUANDO LA COMPAÑIA 37
						*/

//						if (mktime(0, 0, 0, intval($_REQUEST['mes']), intval($dia), intval($_REQUEST['anio'])) >= mktime(0, 0, 0, 7, 1, 2011) && mktime(0, 0, 0, intval($_REQUEST['mes']), intval($dia), intval($_REQUEST['anio'])) <= mktime(0, 0, 0, 7, 31, 2011) && in_array($num_cia, array(31, 34, 49, 69, 79, 92, /*37,*/ 68, 73, 115))) {
//							$_depositos = $_depositos + 4000;
//						}

						$_facturas_transito = isset($facturas_transito[$num_cia][$dia]) ? $facturas_transito[$num_cia][$dia]['importe'] : 0;
						$_facturas_transito_cantidad = isset($facturas_transito[$num_cia][$dia]) ? $facturas_transito[$num_cia][$dia]['cantidad'] : 0;

						$_facturas_clientes = isset($facturas_clientes[$num_cia][$dia]) ? $facturas_clientes[$num_cia][$dia]['importe'] : 0;
						$_facturas_clientes_cantidad = isset($facturas_clientes[$num_cia][$dia]) ? $facturas_clientes[$num_cia][$dia]['cantidad'] : 0;

						$json_data = array(
							'num_cia'           => intval($num_cia),
							'anio'              => intval($_REQUEST['anio'], 10),
							'mes'               => intval($_REQUEST['mes'], 10),
							'dia'               => intval($dia),
							'depositos'         => round(floatval($_depositos), 2),
							'facturas_transito' => round(floatval($_facturas_transito), 2),
							'facturas_clientes' => round(floatval($_facturas_clientes), 2),
							'facturas_venta'    => 0,
							'diferencia'        => 0,
							'arrastre'          => 0,
							'sustituye'         => 0
						);

						if (isset($facturas_venta[$num_cia][$dia])) {
							$_facturas_venta = $facturas_venta[$num_cia][$dia]['importe'];
							$_facturas_venta_cantidad = $facturas_venta[$num_cia][$dia]['cantidad'];

							$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes - $_facturas_venta;

							$json_data['facturas_venta'] = round(floatval($_facturas_venta), 2);
							$json_data['diferencia'] = round(floatval($_diferencia), 2);

							$arrastre_diferencia += $_diferencia;

							$tpl->assign('disabled', ' disabled="disabled"');

							$tpl->assign('usuario', $facturas_venta[$num_cia][$dia]['iduser'] > 0 ? $usuarios[$facturas_venta[$num_cia][$dia]['iduser']] : '&nbsp;');
						}
						else if ($_depositos > 0) {
							$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes;

							$_facturas_venta = $_diferencia + $arrastre_diferencia;

							/*
							@ [07-Ene-2010] La factura de venta es negativa, poner importe en 0 y arrastrar diferencia
							*/
							if ($_facturas_venta < 0) {
								$_diferencia = $_facturas_venta;

								$_facturas_venta = 0;
							}
							else {
								$_facturas_venta_cantidad = 1;

								$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes - $_facturas_venta;
							}

							$json_data['facturas_venta'] = round(floatval($_facturas_venta), 2);
							$json_data['diferencia'] = round(floatval($_diferencia), 2);
							$json_data['arrastre'] = round(floatval($arrastre_diferencia), 2);

							/*
							@ [14-Ene-2010] La factura sustituye a una cancelada
							*/
							if (isset($facturas_venta_canceladas[$num_cia][$dia])) {
								$json_data['sustituye'] = intval($facturas_venta_canceladas[$num_cia][$dia]);
							}

							if ($_facturas_venta == 0) {
								$arrastre_diferencia = $_diferencia;
							}
							else {
								$arrastre_diferencia += $_diferencia;
							}

							$tpl->assign('checked', ' checked="checked"');

							if ($periodo1 == '') {
								$periodo1 = date('j/n/Y', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']));
							}
						}
//						else if ($_facturas_transito > 0 || $_facturas_clientes > 0) {
//						}
						else {
							$tpl->assign('disabled', $dia > $_REQUEST['dia'] ? ' disabled="disabled"' : '');

							$_facturas_venta = 0;
							$_diferencia = 0;
						}

						$totales['depositos'] += $_depositos;
						$totales['facturas_transito'] += $_facturas_transito;
						$totales['facturas_clientes'] += $_facturas_clientes;
						$totales['facturas_venta'] += $_facturas_venta;
						$totales['diferencia'] += $_diferencia;

						$periodo2 = date('j/n/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio']));

						if ($_depositos != 0) {
							$tpl->assign('depositos', ($es_sucursal ? '<span style="float:left">*&nbsp;</span>' : '') . number_format($_depositos, 2, '.', ','));
						}
						else if ($dia <= $_REQUEST['dia']) {
							$tpl->assign('depositos', '<a id="dep-' . $num_cia . '-' . $dia . '" title="' . $num_cia . '|' . $dia . '" class="enlace blue">----------</a>');
						}
						else {
							$tpl->assign('depositos', '&nbsp;');
						}


						$tpl->assign('param', htmlentities(json_encode(array(
							'num_cia' => intval($num_cia),
							'anio'    => intval($_REQUEST['anio']),
							'mes'     => intval($_REQUEST['mes']),
							'dia'     => intval($dia)
						))));
						$tpl->assign('facturas_transito', $_facturas_transito != 0 ? '<span style="float:left;" class="font6">(' . $_facturas_transito_cantidad . ')</span>&nbsp;' . number_format($_facturas_transito, 2, '.', ',') : '&nbsp;');
						$tpl->assign('facturas_clientes', $_facturas_clientes != 0 ? '<span style="float:left;" class="font6">(' . $_facturas_clientes_cantidad . ')</span>&nbsp;' . number_format($_facturas_clientes, 2, '.', ',') : '&nbsp;');
						$tpl->assign('facturas_venta', $_facturas_venta != 0 ? '<span style="float:left;" class="font6">(' . $_facturas_venta_cantidad . ')</span>&nbsp;' . number_format($_facturas_venta, 2, '.', ',') : '&nbsp;');
						$tpl->assign('diferencia_venta', round($_diferencia, 2) != 0 ? number_format($_diferencia, 2, '.', ',') : '&nbsp;');
						$tpl->assign('color', $_diferencia >= 0 ? 'blue' : 'red');

						$tpl->assign('datos', htmlentities(json_encode($json_data)));

						/*
						@ [21-Ago-2011] [INFORMATIVO] Diferencia de efectivo
						*/
						$tpl->assign('diferencia_efectivo', isset($diferencia_efectivos[$num_cia][$dia]) && !isset($facturas_venta[$num_cia][$dia]) ? number_format($diferencia_efectivos[$num_cia][$dia], 2, '.', ',') : '&nbsp;');
						$tpl->assign('color_dif_efectivo', isset($diferencia_efectivos[$num_cia][$dia]) && $diferencia_efectivos[$num_cia][$dia] >= 0 ? 'blue' : 'red');
					}

					foreach ($totales as $key => $value) {
						$tpl->assign('cia.' . $key, $value != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');

						if ($key == 'diferencia') {
							$tpl->assign('cia.color', $value >= 0 ? 'blue' : 'red');
						}
					}

					$diferencia_venta = $totales['depositos'] - $totales['facturas_transito'] - $totales['facturas_clientes'] - $totales['facturas_venta'];

					$tpl->assign('cia.diferencia_venta', $diferencia_venta != 0 ? number_format($diferencia_venta, 2, '.', ',') : '&nbsp;');

					$tpl->assign('cia.arrastre_diferencia', $arrastre_diferencia);

					$tpl->assign('cia.periodo', $periodo1 . '|' . $periodo2);
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'modificarFacturasTransito':
			$sql = '
				SELECT
					num_cia,
					fecha,
					hora,
					consecutivo
						AS folio,
					nombre_cliente,
					rfc,
					importe,
					iva,
					total
				FROM
					facturas_panaderias_tmp
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND fecha = \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio'])) . '\'
					AND RFC <> \'XAXX010101000\'
					AND tsreg IS NULL
				GROUP BY
					num_cia,
					fecha,
					hora,
					consecutivo,
					nombre_cliente,
					rfc,
					importe,
					iva,
					total
				ORDER BY
					consecutivo
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaModificarTransito.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('result');

				$tpl->assign('num_cia', $_REQUEST['num_cia']);

				$row_color = FALSE;

				$total = 0;

				foreach ($result as $i => $rec) {
					$tpl->newBlock('row');

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					$tpl->assign('i', $i);
					$tpl->assign('folio', $rec['folio']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('nombre_cliente', utf8_encode($rec['nombre_cliente']));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('importe', number_format($rec['importe'], 2, '.', ','));
					$tpl->assign('iva', $rec['iva'] ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', number_format($rec['total'], 2, '.', ','));

					$total += $rec['total'];
				}

				$tpl->assign('result.total', number_format($total, 2, '.', ','));
			}
			else {
				$tpl->newBlock('no_result');
			}

			echo $tpl->getOutputContent();
		break;

		case 'actualizarCambiosTransito':
			$sql = '';

			foreach ($_REQUEST['folio'] as $i => $folio) {
				$sql .= '
					UPDATE
						facturas_panaderias_tmp
					SET
						fecha = \'' . $_REQUEST['fecha'][$i] . '\'
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND consecutivo = ' . $folio . '
				' . ";\n";
			}

			if (isset($_REQUEST['del'])) {
				foreach ($_REQUEST['del'] as $folio) {
					$sql .= '
						DELETE FROM
							facturas_panaderias_tmp
						WHERE
							num_cia = \'' . $_REQUEST['num_cia'] . '\'
							AND consecutivo = ' . $folio . '
					' . ";\n";
				}
			}

			$db->query($sql);
		break;

		case 'modificarFacturasClientes':
			$sql = '
				SELECT
					id,
					num_cia,
					fecha,
					fecha_pago,
					hora,
					consecutivo,
					nombre_cliente,
					rfc,
					importe,
					iva,
					total
				FROM
					facturas_electronicas
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND fecha_pago = \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio'])) . '\'
					AND tipo = 2
					AND status = 1
				ORDER BY
					consecutivo
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaModificarClientes.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('result');

				$row_color = FALSE;

				$total = 0;

				foreach ($result as $rec) {
					$tpl->newBlock('row');

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('fecha_pago', $rec['fecha_pago']);
					$tpl->assign('folio', $rec['consecutivo']);
					$tpl->assign('nombre_cliente', utf8_encode($rec['nombre_cliente']));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('importe', number_format($rec['importe'], 2, '.', ','));
					$tpl->assign('iva', $rec['iva'] ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', number_format($rec['total'], 2, '.', ','));

					$total += $rec['total'];
				}

				$tpl->assign('result.total', number_format($total, 2, '.', ','));
			}
			else {
				$tpl->newBlock('no_result');
			}

			echo $tpl->getOutputContent();
		break;

		case 'actualizarCambiosClientes':
			$sql = '';

			foreach ($_REQUEST['id'] as $i => $id) {
				$sql .= '
					UPDATE
						facturas_electronicas
					SET
						fecha_pago = \'' . $_REQUEST['fecha_pago'][$i] . '\'
					WHERE
						id = ' . $id . '
				' . ";\n";
			}

			$db->query($sql);
		break;

		case 'imponerEfectivo':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaImponerEfectivo.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('dia', $_REQUEST['dia']);

			/*
			@ [24-Ago-2011] Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					sucursal
						AS num_cia,
					porcentaje,
					CASE
						WHEN matriz = sucursal THEN
							2
						ELSE
							1
					END
						AS tipo
				FROM
					porcentajes_puntos_calientes
				WHERE
					matriz = ' . $_REQUEST['num_cia'] . '
				ORDER BY
					tipo,
					porcentaje DESC
			';

			$porcentajes = $db->query($sql);

			if ($porcentajes) {
				foreach ($porcentajes as &$p) {
					$p['num_cia'] = intval($p['num_cia']);
					$p['porcentaje'] = floatval($p['porcentaje']);
					$p['tipo'] = intval($p['tipo']);
				}

				$tpl->assign('porcentajes', htmlentities(json_encode($porcentajes)));
			}

			echo $tpl->getOutputContent();
		break;

		case 'generar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			$fac = new FacturasClass();

			if ($fac->ultimoCodigoError() < 0) {
				return -1;
			}

			/*
			@ [05-Ene-2011] Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					sucursal
						AS
							num_cia
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);

			/*
			@ Reordenar porcentajes
			*/
			$sucursales = array();
			if ($result) {
				foreach ($result as $rec) {
					$sucursales[] = $rec['num_cia'];
				}
			}

			/*
			@ Generar reporte
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaReporte.tpl');
			$tpl->prepare();

			if (isset($_REQUEST['datos'])) {
				$num_cia = NULL;

				foreach ($_REQUEST['datos'] as $json_string) {
					$data = json_decode($json_string);

					if ($num_cia != $data->num_cia) {
						$num_cia = $data->num_cia;

						$status_emisor = TRUE;

						$sql = '
							SELECT
								nombre
									AS
										nombre_cia,
								aplica_iva
									AS
										aplicar_iva,
								email
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $num_cia . '
						';
						$cia = $db->query($sql);

						$piva = $cia[0]['aplicar_iva'] == 't' ? 16 : 0;

						$tpl->newBlock('emisor');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $cia[0]['nombre_cia']);

						$total_cia = 0;
					}

					$fecha = date('d/m/Y', mktime(0, 0, 0, $data->mes, $data->dia, $data->anio));
					$hora = '21:30:00';

					$importe = round($data->facturas_venta / (1 + $piva / 100), 2);
					$iva = $data->facturas_venta - $importe;
					$total = $data->facturas_venta;

					$total_cia += $total;

					$tpl->assign('emisor.total', number_format($total_cia, 2, '.', ','));

					if (!$status_emisor) {
						//fwrite($flog, '[' . date('Y-m-d H:i:s') . '] ERROR en dias anteriores' . "\n");

						$tpl->newBlock('row');
						$tpl->assign('cliente', 'PUBLICO EN GENERAL');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('status', '<span style="color:#C00;">Error en d&iacute;as anteriores</span>');
					}
					else if ($total < 0) {
						$tpl->newBlock('row');
						$tpl->assign('cliente', 'PUBLICO EN GENERAL');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('status', '<span style="color:#C00;">El importe de la factura para este d&iacute;a no puede ser negativo o 0</span>');

						$status_emisor = FALSE;
					}
					else if ($total == 0) {
						$tpl->newBlock('row');
						$tpl->assign('cliente', 'PUBLICO EN GENERAL');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('status', 'No se gener&oacute; factura para este d&iacute;a');

						$sql = '
							INSERT INTO
								facturas_electronicas
									(
										num_cia,
										fecha,
										hora,
										tipo_serie,
										consecutivo,
										tipo,
										clave_cliente,
										nombre_cliente,
										rfc,
										calle,
										no_exterior,
										no_interior,
										colonia,
										localidad,
										referencia,
										municipio,
										estado,
										pais,
										codigo_postal,
										importe,
										iva,
										total,
										iduser_ins,
										fecha_pago
									)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										\'' . $hora . '\',
										1,
										0,
										1,
										1,
										\'PUBLICO EN GENERAL\',
										\'XAXX010101000\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										0,
										0,
										0,
										' . $_SESSION['iduser'] . ',
										\'' . $fecha . '\'
									)
						' . ";\n";

						/*
						@ [11-Mar-2011] Actualizar diferencia del emisor
						*/
						if ($id = $db->query('
							SELECT
								id
							FROM
								diferencia_ventas
							WHERE
									num_cia = ' . $num_cia . '
								AND
									anio = ' . $data->anio . '
								AND
									mes = ' . $data->mes . '
						')) {
							$sql .= '
								UPDATE
									diferencia_ventas
								SET
									diferencia = ' . $data->diferencia . ',
									iduser_mod = ' . $_SESSION['iduser'] . ',
									tsmod = now()
								WHERE
									id = ' . $id[0]['id'] . '
							' . ";\n";
						}
						else {
							$sql .= '
								INSERT INTO
									diferencia_ventas
										(
											num_cia,
											anio,
											mes,
											diferencia,
											iduser_ins,
											iduser_mod
										)
									VALUES
										(
											' . $num_cia . ',
											' . $data->anio . ',
											' . $data->mes . ',
											' . $data->diferencia . ',
											' . $_SESSION['iduser'] . ',
											' . $_SESSION['iduser'] . '
										)
							' . ";\n";
						}

						if (in_array($num_cia, $sucursales)) {
							if ($id_venta = $db->query('
								SELECT
									id
								FROM
									ventas_sucursales
								WHERE
									num_cia = ' . $num_cia . '
									AND fecha = \'' . $fecha . '\'
								ORDER BY
									id DESC
								LIMIT
									1
							')) {
								$sql = '
									UPDATE
										ventas_sucursales
									SET
										importe = ' . $data->depositos . '
									WHERE
										id = ' . $id_venta[0]['id'] . '
								' . ";\n";
							} else {
								$sql = '
									INSERT INTO
										ventas_sucursales
											(
												num_cia,
												fecha,
												importe
											)
										VALUES
											(
												' . $num_cia . ',
												\'' . $fecha . '\',
												' . $data->depositos . '
											)
								' . ";\n";
							}
						}
					}
					else {
						$status_clientes = TRUE;

						$facturas_clientes = 0;

						/*
						@ [21-Ago-2011] Obtener las facturas de clientes del día de venta
						*/

						$sql = '
							SELECT
								id,
								num_cia,
								fecha,
								EXTRACT(hour from hora) || \':\' || EXTRACT(minute from hora)
									AS hora,
								consecutivo,
								clave_cliente,
								nombre_cliente,
								rfc,
								calle,
								no_exterior,
								no_interior,
								colonia,
								localidad,
								referencia,
								municipio,
								estado,
								pais,
								codigo_postal,
								email_cliente,
								observaciones,
								cantidad,
								descripcion,
								precio,
								unidad,
								importe,
								iva,
								total,
								status
							FROM
								facturas_panaderias_tmp
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = \'' . $fecha . '\'
								AND tsreg IS NULL
							ORDER BY
								num_cia,
								consecutivo
						';

						$result = $db->query($sql);

						if ($result) {
							$datos = array();

							$cont = 0;

							$consecutivo = NULL;

							foreach ($result as $rec) {
								if ($consecutivo != $rec['consecutivo']) {
									if ($consecutivo != NULL) {
										$cont++;
									}

									$consecutivo = $rec['consecutivo'];

									$datos[$cont] = array(
										'cabecera'          => array(
											'num_cia'               => $num_cia,
											'clasificacion'         => 2,
											'fecha'                 => $rec['fecha'],
											'hora'                  => $rec['hora'],
											'clave_cliente'         => $rec['clave_cliente'],
											'nombre_cliente'        => strtoupper(trim($rec['nombre_cliente'])),
											'rfc_cliente'           => strtoupper(trim($rec['rfc'])),
											'calle'                 => strtoupper(trim($rec['calle'])),
											'no_exterior'           => strtoupper(trim($rec['no_exterior'])),
											'no_interior'           => strtoupper(trim($rec['no_interior'])),
											'colonia'               => strtoupper(trim($rec['colonia'])),
											'localidad'             => strtoupper(trim($rec['localidad'])),
											'referencia'            => strtoupper(trim($rec['referencia'])),
											'municipio'             => strtoupper(trim($rec['municipio'])),
											'estado'                => strtoupper(trim($rec['estado'])),
											'pais'                  => trim($rec['pais']) != '' ? strtoupper(trim($rec['pais'])) : 'MEXICO',
											'codigo_postal'         => $rec['codigo_postal'],
											'email'                 => $rec['email_cliente'],
											'observaciones'         => strtoupper(trim($rec['observaciones'])),
											'importe'               => $rec['importe'],
											'porcentaje_descuento'  => 0,
											'descuento'             => 0,
											'ieps'                  => 0,
											'porcentaje_iva'        => $rec['iva'] > 0 ? 16 : 0,
											'importe_iva'           => $rec['iva'],
											'aplicar_retenciones'   => 'N',
											'importe_retencion_isr' => 0,
											'importe_retencion_iva' => 0,
											'total'                 => $rec['total']
										),
										'consignatario' => array (
											'nombre'        => '',
											'rfc'           => '',
											'calle'         => '',
											'no_exterior'   => '',
											'no_interior'   => '',
											'colonia'       => '',
											'localidad'     => '',
											'referencia'    => '',
											'municipio'     => '',
											'estado'        => '',
											'pais'          => '',
											'codigo_postal' => ''
										),
										'detalle'       => array(),
										'ids'           => array(),
										'num_cia'       => $num_cia,
										'consecutivo'   => $consecutivo,
										'status'        => $rec['status']
									);

									$clave_producto = 1;
								}

								$datos[$cont]['detalle'][] = array(
									'clave'            => $clave_producto++,
									'descripcion'      => strtoupper(trim($rec['descripcion'])),
									'cantidad'         => $rec['cantidad'],
									'unidad'           => strtoupper(trim($rec['unidad'])),
									'precio'           => $rec['precio'],
									'importe'          => round($rec['cantidad'] * $rec['precio'], 2),
									'descuento'        => 0,
									'porcentaje_iva'   => $rec['iva'] > 0 ? 16 : 0,
									'importe_iva'      => $rec['iva'] > 0 ? round($rec['cantidad'] * $rec['precio'] * 0.16, 2) : 0,
									'numero_pedimento' => '',
									'fecha_entrada'    => '',
									'aduana_entrada'   => ''
								);

								$datos[$cont]['ids'][] = $rec['id'];
							}

							foreach ($datos as $d) {
								$status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $d);

								$tpl->newBlock('row');
								$tpl->assign('cliente', $d['cabecera']['nombre_cliente']);
								$tpl->assign('fecha', $d['cabecera']['fecha']);
								$tpl->assign('importe', number_format($d['cabecera']['total'], 2, '.', ','));

								if ($status < 0) {
									$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');

									$status_emisor = FALSE;

									$status_clientes = FALSE;
								}
								else {
									$pieces = explode('-', $status);
									$folio = $pieces[1];

									$tpl->assign('folio', $folio);

									$tpl->assign('status', '<span style="color:#060;">OK</span>');

									$email_status = $fac->enviarEmail();

									$sql = '
										UPDATE
											facturas_panaderias_tmp
										SET
											tsreg = now()
										WHERE
											id IN (' . implode(', ', $d['ids']) . ')
									';

									$db->query($sql);

									$facturas_clientes++;
								}
							}
						}

						/*
						@ [05-Jul-2012] Obtener entidad de la compañía
						*/

						$sql = '
							SELECT
								estado
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $num_cia . '
						';

						$estado = $db->query($sql);

						$datos = array(
							'cabecera' => array (
								'num_cia'               => $num_cia,
								'clasificacion'         => 1,
								'fecha'                 => $fecha,
								'hora'                  => '22:00:00',
								'clave_cliente'         => 1,
								'nombre_cliente'        => 'PUBLICO EN GENERAL',
								'rfc_cliente'           => 'XAXX010101000',
								'calle'                 => '',
								'no_exterior'           => '',
								'no_interior'           => '',
								'colonia'               => '',
								'localidad'             => '',
								'referencia'            => '',
								'municipio'             => '',
								'estado'                => $estado[0]['estado'],
								'pais'                  => 'MEXICO',
								'codigo_postal'         => '',
								'email'                 => '',
								'observaciones'         => $data->sustituye > 0 ? ' (SUSTITUYE A LA FACTURA ' . $data->sustituye . ')' : '',
								'importe'               => $importe,
								'porcentaje_descuento'  => 0,
								'descuento'             => 0,
								'ieps'                  => 0,
								'porcentaje_iva'        => $piva,
								'importe_iva'           => $iva,
								'aplicar_retenciones'   => 'N',
								'importe_retencion_isr' => 0,
								'importe_retencion_iva' => 0,
								'total'                 => $total
							),
							'consignatario' => array (
								'nombre'        => '',
								'rfc'           => '',
								'calle'         => '',
								'no_exterior'   => '',
								'no_interior'   => '',
								'colonia'       => '',
								'localidad'     => '',
								'referencia'    => '',
								'municipio'     => '',
								'estado'        => '',
								'pais'          => '',
								'codigo_postal' => ''
							),
							'detalle' => array(
								array (
									'clave'            => 1,
									'descripcion'      => 'VENTA DEL DIA ' . $fecha . (isset($_REQUEST['nota-ini-' . $num_cia . '-' . $data->dia]) && isset($_REQUEST['nota-fin-' . $num_cia . '-' . $data->dia]) ? ' QUE CORRESPONDE A LOS FOLIOS DE LAS NOTAS DE VENTA ' . $_REQUEST['nota-ini-' . $num_cia . '-' . $data->dia] . ' AL ' . $_REQUEST['nota-fin-' . $num_cia . '-' . $data->dia] : ''),
									'cantidad'         => 1,
									'unidad'           => 'NO APLICA',
									'precio'           => $importe,
									'importe'          => $importe,
									'descuento'        => 0,
									'porcentaje_iva'   => $piva > 0 ? 16 : 0,
									'importe_iva'      => $iva,
									'numero_pedimento' => '',
									'fecha_entrada'    => '',
									'aduana_entrada'   => ''
								)
							)
						);

						$tpl->newBlock('row');
						$tpl->assign('cliente', 'PUBLICO EN GENERAL');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$folio_reservado = $fac->recuperarFolio($num_cia, 1, $fecha);

						if (!$status_clientes) {
							$tpl->assign('status', '<span style="color:#C00;">Error en facturas de clientes</span>');

							$status_emisor = FALSE;
						}
						else if (($status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $datos, $folio_reservado)) < 0) {
							$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');

							$status_emisor = FALSE;
						}
						else {
							$pieces = explode('-', $status);
							$folio = $pieces[1];

							$tpl->assign('folio', $folio);
							$tpl->assign('status', '<span style="color:#060;">OK' . ($facturas_clientes > 0 ? ' [' . $facturas_clientes . ' factura(s) de cliente emitidas]' : '') . '</span>');

							if ($folio_reservado > 0) {
								$fac->utilizarFolio($_SESSION['iduser'], $num_cia, 1, $folio_reservado);
							}

							if (in_array($num_cia, $sucursales)) {
								if ($id_venta = $db->query('
									SELECT
										id
									FROM
										ventas_sucursales
									WHERE
										num_cia = ' . $num_cia . '
										AND fecha = \'' . $fecha . '\'
									ORDER BY
										id DESC
									LIMIT
										1
								')) {
									$sql = '
										UPDATE
											ventas_sucursales
										SET
											importe = ' . $data->depositos . '
										WHERE
											id = ' . $id_venta[0]['id'] . '
									' . ";\n";
								} else {
									$sql = '
										INSERT INTO
											ventas_sucursales
												(
													num_cia,
													fecha,
													importe
												)
											VALUES
												(
													' . $num_cia . ',
													\'' . $fecha . '\',
													' . $data->depositos . '
												)
									' . ";\n";
								}

								$db->query($sql);
							}

							/*
							@ [11-Mar-2011] Actualizar diferencia del emisor
							*/
							if ($id = $db->query('
								SELECT
									id
								FROM
									diferencia_ventas
								WHERE
										num_cia = ' . $num_cia . '
									AND
										anio = ' . $data->anio . '
									AND
										mes = ' . $data->mes . '
							')) {
								$sql = '
									UPDATE
										diferencia_ventas
									SET
										diferencia = ' . $data->diferencia . ',
										iduser_mod = ' . $_SESSION['iduser'] . ',
										tsmod = now()
									WHERE
										id = ' . $id[0]['id'] . '
								' . ";\n";
							}
							else {
								$sql = '
									INSERT INTO
										diferencia_ventas
											(
												num_cia,
												anio,
												mes,
												diferencia,
												iduser_ins,
												iduser_mod
											)
										VALUES
											(
												' . $num_cia . ',
												' . $data->anio . ',
												' . $data->mes . ',
												' . $data->diferencia . ',
												' . $_SESSION['iduser'] . ',
												' . $_SESSION['iduser'] . '
											)
								' . ";\n";
							}

							//$db->query($sql);
						}
					}
				}
			}

			/*
			@ [23-08-2011] Emitir facturas para el dia posterior
			*/

			$sql = '
				SELECT
					id,
					num_cia,
					fecha,
					EXTRACT(hour from hora) || \':\' || EXTRACT(minute from hora)
						AS hora,
					consecutivo,
					clave_cliente,
					nombre_cliente,
					rfc,
					calle,
					no_exterior,
					no_interior,
					colonia,
					localidad,
					referencia,
					municipio,
					estado,
					pais,
					codigo_postal,
					email_cliente,
					observaciones,
					cantidad,
					descripcion,
					precio,
					unidad,
					importe,
					iva,
					total,
					status
				FROM
					facturas_panaderias_tmp
				WHERE
					tsreg IS NULL
					AND (num_cia, fecha) IN (
						SELECT
							num_cia,
							MAX(fecha) + INTERVAL \'1 day\'
						FROM
							facturas_electronicas
						WHERE
							status = 1
							AND tipo = 1
							AND num_cia IN (' . implode(', ', $_REQUEST['cia']) . ')
						GROUP BY
							num_cia
					)
				ORDER BY
					num_cia,
					consecutivo
			';

			$result = $db->query($sql);

			if ($result) {
				$datos = array();

				$num_cia = NULL;
				$consecutivo = NULL;

				$cont = 0;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia'] || $consecutivo != $rec['consecutivo']) {
						if ($consecutivo != NULL) {
							$cont++;
						}

						$num_cia = $rec['num_cia'];
						$consecutivo = $rec['consecutivo'];

						$datos[$cont] = array(
							'cabecera'          => array(
								'num_cia'               => $num_cia,
								'clasificacion'         => 2,
								'fecha'                 => $rec['fecha'],
								'hora'                  => $rec['hora'],
								'clave_cliente'         => $rec['clave_cliente'],
								'nombre_cliente'        => strtoupper(trim($rec['nombre_cliente'])),
								'rfc_cliente'           => strtoupper(trim($rec['rfc'])),
								'calle'                 => strtoupper(trim($rec['calle'])),
								'no_exterior'           => strtoupper(trim($rec['no_exterior'])),
								'no_interior'           => strtoupper(trim($rec['no_interior'])),
								'colonia'               => strtoupper(trim($rec['colonia'])),
								'localidad'             => strtoupper(trim($rec['localidad'])),
								'referencia'            => strtoupper(trim($rec['referencia'])),
								'municipio'             => strtoupper(trim($rec['municipio'])),
								'estado'                => strtoupper(trim($rec['estado'])),
								'pais'                  => trim($rec['pais']) != '' ? strtoupper(trim($rec['pais'])) : 'MEXICO',
								'codigo_postal'         => $rec['codigo_postal'],
								'email'                 => $rec['email_cliente'],
								'observaciones'         => strtoupper(trim($rec['observaciones'])),
								'importe'               => $rec['importe'],
								'porcentaje_descuento'  => 0,
								'descuento'             => 0,
								'porcentaje_iva'        => $rec['iva'] > 0 ? 16 : 0,
								'importe_iva'           => $rec['iva'],
								'aplicar_retenciones'   => 'N',
								'importe_retencion_isr' => 0,
								'importe_retencion_iva' => 0,
								'total'                 => $rec['total']
							),
							'consignatario' => array (
								'nombre'        => '',
								'rfc'           => '',
								'calle'         => '',
								'no_exterior'   => '',
								'no_interior'   => '',
								'colonia'       => '',
								'localidad'     => '',
								'referencia'    => '',
								'municipio'     => '',
								'estado'        => '',
								'pais'          => '',
								'codigo_postal' => ''
							),
							'detalle'       => array(),
							'ids'           => array(),
							'num_cia'       => $num_cia,
							'consecutivo'   => $consecutivo,
							'status'        => $rec['status']
						);

						$clave_producto = 1;
					}

					$datos[$cont]['detalle'][] = array(
						'clave'            => $clave_producto++,
						'descripcion'      => strtoupper(trim($rec['descripcion'])),
						'cantidad'         => $rec['cantidad'],
						'unidad'           => strtoupper(trim($rec['unidad'])),
						'precio'           => $rec['precio'],
						'importe'          => round($rec['cantidad'] * $rec['precio'], 2),
						'descuento'        => 0,
						'porcentaje_iva'   => $rec['iva'] > 0 ? 16 : 0,
						'importe_iva'      => $rec['iva'] > 0 ? round($rec['cantidad'] * $rec['precio'] * 0.16, 2) : 0,
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);

					$datos[$cont]['ids'][] = $rec['id'];
				}

				$num_cia = NULL;

				foreach ($datos as $d) {
					if ($num_cia != $d['cabecera']['num_cia']) {
						$num_cia = $d['cabecera']['num_cia'];

						$cia = $db->query('
							SELECT
								nombre_corto
									AS nombre
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $num_cia . '
						');

						$tpl->newBlock('emisor');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $cia[0]['nombre']);
					}

					$status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $d);

					$tpl->newBlock('row');
					$tpl->assign('cliente', $d['cabecera']['nombre_cliente']);
					$tpl->assign('fecha', $d['cabecera']['fecha']);
					$tpl->assign('importe', number_format($d['cabecera']['total'], 2, '.', ','));

					if ($status < 0) {
						$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');
					}
					else {
						$pieces = explode('-', $status);
						$folio = $pieces[1];

						$tpl->assign('folio', $folio);

						$tpl->assign('status', '<span style="color:#060;">OK</span>');

						$email_status = $fac->enviarEmail();

						$sql = '
							UPDATE
								facturas_panaderias_tmp
							SET
								tsreg = now()
							WHERE
								id IN (' . implode(', ', $d['ids']) . ')
						';

						$db->query($sql);
					}
				}
			}

			//fclose($flog);

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiaria.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
