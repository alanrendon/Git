<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

/*if ($_SESSION['iduser'] != 1) {
	die('MODIFICANDO PROGRAMA');
}*/

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/PedidosAutomaticosInicio.tpl');
			$tpl->prepare();

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

			foreach ($admins as $admin) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $admin['value']);
				$tpl->assign('text', utf8_encode($admin['text']));
			}

			echo $tpl->getOutputContent();
		break;

		case 'calculoInicial':
			if ($_REQUEST['existencia'] == 1) {
				list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j') < 7 ? date('j/n/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('j/n/Y')));
			}
			else if ($_REQUEST['existencia'] == 2) {
				$sql = '
					SELECT
						MAX(fecha)
							AS fecha
					FROM
						historico_inventario
					WHERE
						num_cia < 300
				';
				$result = $db->query($sql);

				if ($result) {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $result[0]['fecha']));
				}
				else {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j/n/Y')));
				}
			}
			else if ($_REQUEST['existencia'] == 3) {
				$sql = '
					SELECT
						MAX(fecha)
							AS fecha
					FROM
						inventario_fin_mes
					WHERE
						num_cia < 300
				';
				$result = $db->query($sql);

				if ($result) {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $result[0]['fecha']));
				}
				else {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j/n/Y')));
				}
			}

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes, $dia, $anio));

			$fecha3 = date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anio));
			$fecha4 = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio));

			$fecha_historico = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

			$dias_por_mes = array(
				1 =>  31,
				2 =>  ($anio % 4 == 0 && $anio % 100 != 0) || $anio % 400 ? 29 : 28,
				3 =>  31,
				4 =>  30,
				5 =>  31,
				6 =>  30,
				7 =>  31,
				8 =>  31,
				9 =>  30,
				10 => 31,
				11 => 30,
				12 => 31
			);

			$dias = isset($_REQUEST['complemento']) ? $dias_por_mes[$mes] - $dia + 7 : (isset($_REQUEST['dias']) && $_REQUEST['dias'] >= 15 ? $_REQUEST['dias'] : 37);

			/*
			@ Existencias
			*/
			$condiciones = array();

			$condiciones[] = 'procpedautomat = TRUE';

			/*$condiciones[] = '(num_cia, codmp) IN (
				SELECT
					num_cia,
					codmp
				FROM
					mov_inv_real
				WHERE
					num_cia <= 300
					AND tipo_mov = FALSE
					AND cantidad > 0
					AND fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha2 . '\'
					AND descripcion <> \'DIFERENCIA INVENTARIO\'
					AND descripcion NOT LIKE \'TRASPASO DE AVIO%\'
				GROUP BY
					num_cia,
					codmp
			)';*/

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

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

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();

				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}

				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}

			if ($_REQUEST['existencia'] == 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						inv.existencia
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						/*presentacion,
						tp.descripcion
							AS unidad_pedido,*/
						controlada
					FROM
						inventario_virtual inv
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}
			else if ($_REQUEST['existencia'] == 2) {
				$condiciones[] = 'fecha = \'' . $fecha_historico . '\'';

				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						existencia,
						tuc.descripcion
							AS unidad_consumo,
						/*presentacion,
						tp.descripcion
							AS unidad_pedido,*/
						controlada
					FROM
						historico_inventario hi
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}
			else if ($_REQUEST['existencia'] == 3) {
				$condiciones[] = 'fecha = \'' . $fecha_historico . '\'';

				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						ifm.inventario
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						controlada
					FROM
						inventario_fin_mes ifm
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}

			$result = $db->query($sql);

			$inventario = array();
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];

						$inventario[$num_cia]['nombre_cia'] = $r['nombre_cia'];
						$inventario[$num_cia]['productos'] = array();
					}

					$inventario[$num_cia]['productos'][$r['codmp']] = array(
						'nombre_mp'      => $r['nombre_mp'],
						'existencia'     => $r['existencia'],
						'unidad_consumo' => $r['unidad_consumo'],
						'controlada'     => $r['controlada']
					);
				}
			}

			/*
			@ Consumos de hace 1 mes
			*/
			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'procpedautomat = TRUE';

			$condiciones[] = 'tipo_mov = TRUE';

			$condiciones[] = 'num_cia <= 300';

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

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

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();

				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}

				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}

			$sql = '
				SELECT
					num_cia,
					codmp,
					MAX(fecha)
						AS fecha,
					SUM(cantidad)
						AS consumo
				FROM
					mov_inv_real
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					codmp
				ORDER BY
					num_cia,
					codmp
			';

			$result = $db->query($sql);

			$consumos1 = array();
			if ($result) {
				foreach ($result as $r) {
					$consumos1[$r['num_cia']][$r['codmp']] = array(
						'fecha'   => $r['fecha'],
						'consumo' => $r['consumo']
					);
				}
			}

			/*
			@ Consumos de hace 2 meses
			*/
			/*$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha4 . '\'';

			$condiciones[] = 'procpedautomat = TRUE';

			$condiciones[] = 'tipo_mov = TRUE';

			$condiciones[] = 'num_cia <= 300';

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

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

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();

				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}

				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}

			$sql = '
				SELECT
					num_cia,
					codmp,
					MAX(fecha)
						AS fecha,
					SUM(cantidad)
						AS consumo
				FROM
					mov_inv_real
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					codmp
				ORDER BY
					num_cia,
					codmp
			';

			$result = $db->query($sql);

			$consumos2 = array();
			if ($result) {
				foreach ($result as $r) {
					$consumos2[$r['num_cia']][$r['codmp']] = array(
						'fecha'   => $r['fecha'],
						'consumo' => $r['consumo']
					);
				}
			}*/

			/*
			@ Realizar cálculo de pedido
			*/

			$pedidos = array();

			foreach ($inventario as $cia => $datos) {
				foreach ($datos['productos'] as $codmp => $pro) {
					$con1 = isset($consumos1[$cia][$codmp]) ? $consumos1[$cia][$codmp] : array('fecha' => NULL, 'consumo' => 0);
					//$con2 = isset($consumos2[$cia][$codmp]) ? $consumos2[$cia][$codmp] : array('fecha' => NULL, 'consumo' => 0);

					if ($_REQUEST['existencia'] > 1) {
						$consumo_mes = /*$con1['consumo'] > 0 ?*/ $con1['consumo'] /*: $con2['consumo']*/;
						$consumo_dia = $consumo_mes / 30;

						$consumo_fecha = /*$con1['consumo'] > 0 ?*/ $con1['fecha'] /*: $con2['fecha']*/;

						$existencia = $pro['controlada'] == 't' ? $pro['existencia'] : ($pro['existencia'] - $consumo_mes >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo_dia * $dias - $existencia);
					}
					else {
						$consumo_mes = /*$con1['consumo'] > 0 ?*/ $con1['consumo'] /*: $con2['consumo']*/;

						if ($con1['fecha'] != '') {
							list($con_dia, $con_mes, $con_anio) = explode('/', $con1['fecha']);
						}
						else {
							$con_dia = 0;
						}

						$consumo_dia = $con1['consumo'] > 0 ? ($con_dia > 0 ? $consumo_mes / intval($con_dia, 10) : 0) : $consumo_mes / 30;

						$consumo_fecha = /*$con1['consumo'] > 0 ?*/ $con1['fecha'] /*: $con2['fecha']*/;

						$existencia = $pro['controlada'] == 't' ? $pro['existencia'] : ($pro['existencia'] - $consumo_mes >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo_dia * $dias - $existencia);
					}

					if ($consumo_mes != 0 || $existencia != 0) {
						if ($pedido > 0) {
							$pedidos[] = array(
								'num_cia'       => intval($cia),
								'nombre_cia'    => utf8_encode($datos['nombre_cia']),
								'codmp'         => intval($codmp),
								'nombre_mp'     => utf8_encode($pro['nombre_mp']),
								'unidad'        => utf8_encode($pro['unidad_consumo']),
								'consumo_mes'   => floatval($consumo_mes),
								'consumo_dia'   => floatval($consumo_dia),
								'consumo_fecha' => $consumo_fecha,
								'existencia'    => floatval($existencia),
								'pedido'        => floatval($pedido),
								'diferencia'    => $existencia + $pedido - $consumo_mes,
								'estimado'      => $existencia + $pedido,
								'dias_consumo'  => $consumo_mes > 0 ? round(($existencia + $pedido) / ($consumo_dia)) : 0,
								'urgente'       => $consumo_mes > 0 ? (round($existencia / $consumo_dia) <= 5 ? 'TRUE' : 'FALSE') : 'FALSE'
							);
						}
//						else {
//							$pedidos[] = array(
//								'num_cia'       => intval($cia),
//								'nombre_cia'    => utf8_encode($datos['nombre_cia']),
//								'codmp'         => intval($codmp),
//								'nombre_mp'     => utf8_encode($pro['nombre_mp']),
//								'unidad'        => utf8_encode($pro['unidad_consumo']),
//								'consumo_mes'   => floatval($consumo_mes),
//								'consumo_dia'   => floatval($consumo_dia),
//								'consumo_fecha' => $consumo_fecha,
//								'existencia'    => floatval($existencia),
//								'pedido'        => 0,
//								'diferencia'    => $existencia - $consumo_mes,
//								'estimado'      => floatval($existencia),
//								'dias_consumo'  => $consumo_mes > 0 ? round($existencia / ($consumo_dia)) : 0,
//								'urgente'       => 'FALSE'
//							);
//						}
					}
				}
			}

			if (count($pedidos) > 0) {
				$tpl = new TemplatePower('plantillas/ped/PedidosAutomaticosCalculoInicial.tpl');
				$tpl->prepare();

				$tpl->assign('dias', $_REQUEST['dias']);

				$tpl->assign('complemento', isset($_REQUEST['complemento']) ? 'TRUE' : 'FALSE');

				$tpl->assign('complemento_leyenda', isset($_REQUEST['complemento']) ? ' (Complemento del mes)' : '');

				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['pedido'] > 0 && $b['pedido'] > 0) {
							if ($a['codmp'] == $b['codmp']) {
								return 0;
							}
							else {
								return ($a['codmp'] < $b['codmp']) ? -1 : 1;
							}
						}
						else {
							if ($a['pedido'] <= 0 && $b['pedido'] <= 0) {
								if ($a['codmp'] == $b['codmp']) {
									return 0;
								}
								else {
									return ($a['codmp'] < $b['codmp']) ? -1 : 1;
								}
							}
							if ($a['pedido'] > 0 && $b['pedido'] <= 0) {
								return -1;
							}
							else if ($b['pedido'] > 0 && $a['pedido'] <= 0) {
								return 1;
							}
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}

				usort($pedidos, 'cmp');

				$num_cia = NULL;

				foreach ($pedidos as $p) {
					if ($num_cia != $p['num_cia']) {
						$num_cia = $p['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $p['nombre_cia']);

						$row_color = FALSE;
					}

					$tpl->newBlock('producto');

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					$tpl->assign('datos_pedido', htmlentities(json_encode($p)));
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('codmp', $p['codmp']);
					$tpl->assign('nombre_mp', $p['nombre_mp']);
					$tpl->assign('unidad', $p['unidad'] . (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES'));
					$tpl->assign('consumo_mes', $p['consumo_mes'] != 0 ? number_format($p['consumo_mes'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumo_dia', $p['consumo_dia'] != 0 ? number_format($p['consumo_dia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumo_fecha', $p['consumo_fecha'] != '' ? $p['consumo_fecha'] : '&nbsp;');
					$tpl->assign('existencia', $p['existencia'] != 0 ? number_format($p['existencia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('diferencia', $p['diferencia'] != 0 ? number_format($p['diferencia'], 2, '.', ',') : '&nbsp;');

					$tpl->assign('estimado', $p['estimado'] != 0 ? number_format($p['estimado'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('dias', $p['dias_consumo'] != 0 ? round($p['dias_consumo'], 1) : '&nbsp;');

					$tpl->assign('dias_color', $p['dias_consumo'] > 60 ? 'red bold' : 'blue');

					$tpl->assign('disabled', !$p['pedido'] ? ' disabled' : '');
				}

				echo $tpl->getOutputContent();
			}

		break;

		case 'reporte':

		break;

		case 'distribuirPedidos':
			$pedidos = array();
			$productos = array();

			foreach ($_REQUEST['pedido'] as $pedido) {
				$data = json_decode($pedido, TRUE);

				$pedidos[] = $data;

				$productos[] = $data['num_cia'] . ', ' . $data['codmp'];
			}

			$sql = '
				SELECT
					num_cia,
					cpp.codmp,
					cpp.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					telefono1,
					telefono2,
					email1,
					email2,
					email3,
					ppp.porcentaje,
					contenido,
					tuc.descripcion
						AS unidad,
					tp.descripcion
						AS presentacion,
					precio
				FROM
					porcentajes_pedidos_proveedores ppp
					LEFT JOIN catalogo_productos_proveedor cpp
						ON (cpp.id = ppp.presentacion)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = cpp.num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						ON (cmp.codmp = cpp.codmp)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (idunidad = cmp.unidadconsumo)
					LEFT JOIN tipo_presentacion tp
						ON (idpresentacion = cpp.presentacion)
				WHERE
					(num_cia, ppp.codmp) IN (VALUES (' . implode('), (', $productos) . '))
					AND cpp.para_pedido = TRUE
				ORDER BY
					num_cia,
					cpp.codmp,
					porcentaje DESC
			';

			$result = $db->query($sql);

			$porcentajes = array();

			if ($result) {
				foreach ($result as $rec) {
					$porcentajes[$rec['num_cia']][$rec['codmp']][] = array(
						'num_pro'      => intval($rec['num_pro']),
						'nombre_pro'   => utf8_encode($rec['nombre_pro']),
						'telefono1'    => $rec['telefono1'],
						'telefono2'    => $rec['telefono2'],
						'email1'       => $rec['email1'],
						'email2'       => $rec['email2'],
						'email3'       => $rec['email3'],
						'porcentaje'   => floatval($rec['porcentaje']),
						'contenido'    => floatval($rec['contenido']),
						'unidad'       => $rec['unidad'],
						'presentacion' => $rec['presentacion'],
						'precio'       => floatval($rec['precio'])
					);
				}
			}

			$pedidos_pro = array();
			foreach ($pedidos as $pedido) {
				if (isset($porcentajes[$pedido['num_cia']][$pedido['codmp']])) {
					foreach ($porcentajes[$pedido['num_cia']][$pedido['codmp']] as $porcentaje) {
						$parte_pedido = round($pedido['pedido'] * $porcentaje['porcentaje'] / 100);

						$entregar = $parte_pedido / $porcentaje['contenido'];

						if (round(($entregar - floor($entregar)) * 100) >= 30) {
							$entregar = ceil($entregar);
						}
						else {
							$entregar = floor($entregar);
						}

						if ($entregar > 0) {
							if (in_array($pedido['codmp'], array(3, 4))) {
								if ($entregar % 5 != 0) {
									$entregar += 5 - $entregar % 5;
								}
							}

							$pedidos_pro[] = array(
								'num_cia'      => intval($pedido['num_cia']),
								'nombre_cia'   => $pedido['nombre_cia'],
								'codmp'        => intval($pedido['codmp']),
								'nombre_mp'    => $pedido['nombre_mp'],
								'pedido'       => floatval($parte_pedido),
								'unidad'       => $pedido['unidad'],
								'entregar'     => floatval($entregar),
								'presentacion' => $porcentaje['presentacion'],
								'contenido'    => $porcentaje['contenido'],
								'precio'       => floatval($porcentaje['precio']),
								'porcentaje'   => floatval($porcentaje['porcentaje']),
								'num_pro'      => intval($porcentaje['num_pro']),
								'nombre_pro'   => $porcentaje['nombre_pro'],
								'telefono1'    => $porcentaje['telefono1'],
								'telefono2'    => $porcentaje['telefono2'],
								'email1'       => $porcentaje['email1'],
								'email2'       => $porcentaje['email2'],
								'email3'       => $porcentaje['email3'],
								'urgente'      => $pedido['urgente']
							);
						}
						else {
							$pedidos_pro[] = array(
								'num_cia'      => intval($pedido['num_cia']),
								'nombre_cia'   => $pedido['nombre_cia'],
								'codmp'        => intval($pedido['codmp']),
								'nombre_mp'    => $pedido['nombre_mp'],
								'pedido'       => floatval($parte_pedido),
								'unidad'       => $pedido['unidad'],
								'entregar'     => NULL,
								'presentacion' => NULL,
								'contenido'    => NULL,
								'precio'       => NULL,
								'porcentaje'   => NULL,
								'num_pro'      => NULL,
								'nombre_pro'   => NULL,
								'telefono1'    => NULL,
								'telefono2'    => NULL,
								'email1'       => NULL,
								'email2'       => NULL,
								'email3'       => NULL,
								'urgente'      => 'FALSE'
							);
						}
					}
				}
				else {
					$pedidos_pro[] = array(
						'num_cia'      => intval($pedido['num_cia']),
						'nombre_cia'   => $pedido['nombre_cia'],
						'codmp'        => intval($pedido['codmp']),
						'nombre_mp'    => $pedido['nombre_mp'],
						'pedido'       => floatval($pedido['pedido']),
						'unidad'       => $pedido['unidad'],
						'entregar'     => NULL,
						'presentacion' => NULL,
						'contenido'    => NULL,
						'precio'       => NULL,
						'porcentaje'   => NULL,
						'num_pro'      => NULL,
						'nombre_pro'   => NULL,
						'telefono1'    => NULL,
						'telefono2'    => NULL,
						'email1'       => NULL,
						'email2'       => NULL,
						'email3'       => NULL,
						'urgente'      => 'FALSE'
					);
				}
			}

			function cmp($a, $b) {
				if ($a['num_cia'] == $b['num_cia']) {
					if ($a['entregar'] > 0 && $b['entregar'] > 0) {
						if ($a['codmp'] == $b['codmp']) {
							return 0;
						}
						else {
							return ($a['codmp'] < $b['codmp']) ? -1 : 1;
						}
					}
					else {
						if ($a['entregar'] <= 0 && $b['entregar'] <= 0) {
							if ($a['codmp'] == $b['codmp']) {
								return 0;
							}
							else {
								return ($a['codmp'] < $b['codmp']) ? -1 : 1;
							}
						}
						if ($a['entregar'] > 0 && $b['entregar'] <= 0) {
							return -1;
						}
						else if ($b['entregar'] > 0 && $a['entregar'] <= 0) {
							return 1;
						}
					}
				}
				else {
					return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
				}

			}

			usort($pedidos_pro, 'cmp');

			$tpl = new TemplatePower('plantillas/ped/PedidosAutomaticosDistribucion.tpl');
			$tpl->prepare();

			$tpl->assign('dias', $_REQUEST['dias']);

			$tpl->assign('complemento', $_REQUEST['complemento']);

			$num_cia = NULL;
			foreach ($pedidos_pro as $p) {
				if ($num_cia != $p['num_cia']) {
					$num_cia = $p['num_cia'];

					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $p['nombre_cia']);

					$row_color = FALSE;
				}

				$tpl->newBlock('pedido');

				$tpl->assign('row_color', $row_color ? 'on' : 'off');

				$row_color = !$row_color;

				$tpl->assign('datos_pedido', htmlentities(json_encode($p)));
				$tpl->assign('num_cia', $num_cia);
				$tpl->assign('codmp', $p['codmp']);
				$tpl->assign('nombre_mp', $p['nombre_mp']);
				$tpl->assign('pedido', number_format($p['pedido'], 2, '.', ','));
				$tpl->assign('unidad', $p['unidad'] . ($p['pedido'] > 1 ? (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
				$tpl->assign('entregar', $p['entregar'] > 0 ? number_format($p['entregar'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('presentacion', $p['entregar'] > 0 ? $p['presentacion'] . ($p['entregar'] > 1 ? (in_array($p['presentacion'][strlen($p['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ' DE ' . $p['contenido'] . ' ' . $p['unidad'] . ($p['contenido'] > 1 ? (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '')  : '&nbsp;');
				$tpl->assign('precio', $p['entregar'] > 0 ? '<span style="float:left;">$&nbsp;</span>' . number_format($p['precio'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('costo', $p['entregar'] > 0 ? '<span style="float:left;">$&nbsp;</span>' . number_format($p['entregar'] * $p['precio'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('num_pro', $p['entregar'] > 0 ? '<input name="num_pro[]" type="hidden" id="num_pro" value="' . $p['num_pro'] . '" />' . $p['num_pro'] : '&nbsp;');
				$tpl->assign('nombre_pro', $p['entregar'] > 0 ? $p['nombre_pro'] : '&nbsp;');

				$tpl->assign('disabled', !$p['entregar'] ? ' disabled' : '');
			}

			echo $tpl->getOutputContent();

		break;

		case 'anotaciones':
			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor IN (' . implode(', ', array_unique($_REQUEST['num_pro'], SORT_NUMERIC)) . ')
				ORDER BY
					num_pro
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasAnotaciones.tpl');
			$tpl->prepare();

			$row_color = FALSE;
			foreach ($result as $i => $rec) {
				$tpl->newBlock('row');

				$tpl->assign('row_color', $row_color ? 'on' : 'off');

				$row_color = !$row_color;

				$tpl->assign('num_pro', $rec['num_pro']);
				$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
			}

			echo $tpl->getOutputContent();
		break;

		case 'terminarProceso':
			$sql = '
				SELECT
					COALESCE(MAX(folio), 0) + 1
						AS folio
				FROM
					pedidos_new
			';
			$result = $db->query($sql);

			$folio = $result[0]['folio'];

			$sql = '';

			foreach ($_REQUEST['pedido'] as $json) {
				$pedido = json_decode($json, TRUE);

				$sql .= '
					INSERT INTO
						pedidos_new
							(
								folio,
								fecha,
								dias,
								complemento,
								num_cia,
								codmp,
								pedido,
								unidad,
								entregar,
								presentacion,
								contenido,
								precio,
								num_proveedor,
								porcentaje,
								urgente,
								idins,
								tsins,
								programa
							)
						VALUES
							(
								' . $folio . ',
								now()::date,
								' . $_REQUEST['dias'] . ',
								' . $_REQUEST['complemento'] . ',
								' . $pedido['num_cia'] . ',
								' . $pedido['codmp'] . ',
								' . $pedido['pedido'] . ',
								\'' . $pedido['unidad'] . '\',
								' . $pedido['entregar'] . ',
								\'' . $pedido['presentacion'] . '\',
								' . $pedido['contenido'] . ',
								' . $pedido['precio'] . ',
								' . $pedido['num_pro'] . ',
								' . $pedido['porcentaje'] . ',
								' . $pedido['urgente'] . ',
								' . $_SESSION['iduser'] . ',
								now(),
								1
							)
				' . ";\n";
			}

			foreach ($_REQUEST['num_pro_anotacion'] as $i => $num_pro) {
				if ($_REQUEST['anotacion'][$i] != '') {
					$sql .= '
						INSERT INTO
							pedidos_anotaciones
								(
									folio,
									num_proveedor,
									anotaciones,
									idins,
									tsins
								)
							VALUES
								(
									' . $folio . ',
									' . $num_pro . ',
									\'' . $_REQUEST['anotacion'][$i] . '\',
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";
				}
			}

			$db->query($sql);

			$tpl = new TemplatePower('plantillas/ped/PedidosAutomaticosFin.tpl');
			$tpl->prepare();

			$tpl->assign('folio', $folio);
			$tpl->assign('fecha', date('d/m/Y'));
			$tpl->assign('dias', $_REQUEST['dias']);
			$tpl->assign('no_pedidos', count($_REQUEST['pedido']));

			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro,
					COUNT(id)
						AS no_pedidos,
					telefono1,
					telefono2,
					email1,
					email2,
					email3
				FROM
					pedidos_new p
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
				WHERE
					folio = ' . $folio . '
				GROUP BY
					num_pro,
					nombre_pro,
					telefono1,
					telefono2,
					email1,
					email2,
					email3
				ORDER BY
					num_pro
			';

			$pros = $db->query($sql);

			$row_color = FALSE;

			foreach ($pros as $i => $pro) {
				$tpl->newBlock('pro');

				$tpl->assign('row_color', $row_color ? 'on' : 'off');

				$tpl->assign('num_pro', $pro['num_pro']);
				$tpl->assign('nombre_pro', utf8_encode($pro['nombre_pro']));
				$tpl->assign('no_pedidos', number_format($pro['no_pedidos'], 0, '.', ','));

				$telefonos = array();

				if ($pro['telefono1'] != '') {
					$telefonos[] = $pro['telefono1'];
				}
				if ($pro['telefono2'] != '') {
					$telefonos[] = $pro['telefono2'];
				}

				$emails = array();

				if ($pro['email1'] != '') {
					$emails[] = $pro['email1'];
				}
				if ($pro['email2'] != '') {
					$emails[] = $pro['email2'];
				}
				if ($pro['email3'] != '') {
					$emails[] = $pro['email3'];
				}

				$tpl->assign('telefonos', count($telefonos) > 0 ? implode(', ', $telefonos) : 'NO HAY TELEFONOS REGISTRADOS');
				$tpl->assign('emails', count($emails) > 0 ? implode(', ', $emails) : 'NO HAY CORREOS ELECTRONICOS REGISTRADOS');
			}

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ped/PedidosAutomaticos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
