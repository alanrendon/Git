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
	function toInt($val) {
		return intval($val, 10);
	}

	switch ($_REQUEST['accion']) {
		case 'cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);

			echo $result[0]['nombre_corto'];
		break;

		case 'mp':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			$result = $db->query($sql);

			echo $result[0]['nombre'];
		break;

		case 'reporte':
			$colores = array(
				'FD'  => '099',
				'FN'  => '00C',
				'BIZ' => 'C00',
				'REP' => '60C',
				'PIC' => '600',
				'GEL' => '666',
				'DES' => 'F30',
				'ROS' => '099'
			);

			$aux = new AuxInvClass($_REQUEST['num_cia'], $_REQUEST['anio'], $_REQUEST['mes'], isset($_REQUEST['codmp']) ? $_REQUEST['codmp'] : NULL, isset($_REQUEST['inv']) ? $_REQUEST['inv'] : 'real', isset($_REQUEST['cont']) ? $_REQUEST['cont'] : NULL, isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : NULL, !isset($_REQUEST['gas']) ? array(90) : NULL);

			$tpl = new TemplatePower('plantillas/bal/AuxInv.tpl');
			$tpl->prepare();

			if (isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0) {
				$tpl->newBlock('detallado_' . $aux->inv);

				$tpl->assign('num_cia', $aux->num_cia);

				$nombre = $db->query('SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ' . $aux->num_cia);

				$tpl->assign('nombre_cia', $nombre[0]['nombre_corto']);
				$tpl->assign('anio', $aux->anio);
				$tpl->assign('mes', mes_escrito($aux->mes));
				$tpl->assign('codmp', $aux->codmp);
				$tpl->assign('nombre', $aux->mps[$aux->codmp]['nombre']);

				$tpl->assign('existencia_ini', number_format($aux->mps[$aux->codmp]['existencia_ini'], 2, '.', ','));
				$tpl->assign('costo_ini', number_format($aux->mps[$aux->codmp]['costo_ini'], 2, '.', ','));
				$tpl->assign('precio_ini', number_format($aux->mps[$aux->codmp]['precio_ini'], 4, '.', ','));

				$tpl->assign('unidades_entrada', number_format($aux->mps[$aux->codmp]['entradas'], 2, '.', ','));
				$tpl->assign('costo_entrada', number_format($aux->mps[$aux->codmp]['compras'], 2, '.', ','));

				$tpl->assign('unidades_compras', number_format($aux->mps[$aux->codmp]['entradas'] - $aux->mps[$aux->codmp]['entradas_mercancias'], 2, '.', ','));
				$tpl->assign('costo_compras', number_format($aux->mps[$aux->codmp]['compras'] - $aux->mps[$aux->codmp]['mercancias'], 2, '.', ','));

				$tpl->assign('unidades_mercancias', number_format($aux->mps[$aux->codmp]['entradas_mercancias'], 2, '.', ','));
				$tpl->assign('costo_mercancias', number_format($aux->mps[$aux->codmp]['mercancias'], 2, '.', ','));

				$tpl->assign('unidades_salida', number_format($aux->mps[$aux->codmp]['salidas'], 2, '.', ','));
				$tpl->assign('costo_salida', number_format($aux->mps[$aux->codmp]['consumos'], 2, '.', ','));

				$tpl->assign('existencia', number_format($aux->mps[$aux->codmp]['existencia'], 2, '.', ','));
				$tpl->assign('costo', number_format($aux->mps[$aux->codmp]['costo'], 2, '.', ','));
				$tpl->assign('precio', number_format($aux->mps[$aux->codmp]['precio'], 4, '.', ','));

				$precio = $aux->mps[$aux->codmp]['precio_ini'];
				$ultimo_dia_consumo = '';
				$dif = 0;

				$consumos = array(
					''    => 0,
					'FD'  => 0,
					'FN'  => 0,
					'BIZ' => 0,
					'REP' => 0,
					'PIC' => 0,
					'GEL' => 0,
					'DES' => 0,
					'ROS' => 0
				);

				if (count($aux->movs) > 0) {
					$dia = NULL;
					$arrastre_dia = 0;
					$bgcolor = TRUE;

					foreach ($aux->movs[$aux->codmp] as $mov) {
						list($dia_mov, $mes_mov, $anio_mov) = array_map('toInt', explode('/', $mov['fecha']));

						if ($dia != $dia_mov) {
							$dia = $dia_mov;
							$arrastre_dia++;

							if ($dia != $arrastre_dia) {
								$dif_dias = $dia - $arrastre_dia;

								for ($i = 0; $i < $dif_dias; $i++) {
									$bgcolor = !$bgcolor;

									$tpl->newBlock('mov_' . $aux->inv);
									$tpl->newBlock('mov_' . $aux->inv . '_no');
									$tpl->assign('bgcolor', $bgcolor ? 'ddd' : 'fff');
									$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, $mes_mov, $arrastre_dia + $i, $anio_mov)));
								}

								$arrastre_dia = $dia;
							}

							$bgcolor = !$bgcolor;
						}

						$tpl->newBlock('mov_' . $aux->inv);
						$tpl->newBlock('mov_' . $aux->inv . '_yes');
						$tpl->assign('bgcolor', $bgcolor ? 'ddd' : 'fff');

						$tpl->assign('fecha', $mov['fecha']);
						$tpl->assign('concepto', $mov['concepto']);
						if ($mov['num_pro'] > 0) {
							$proveedor = $db->query('SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = ' . $mov['num_pro']);
							$tpl->assign('proveedor', $proveedor[0]['nombre']);
						}

						$tpl->assign('precio_mov', number_format($mov['precio'], 4, '.', ','));

						if ($mov['tipo_mov'] == 'f') {
							$tpl->assign('unidades_entrada', number_format($mov['cantidad'], 2, '.', ','));
							$tpl->assign('costo_entrada', number_format($mov['total'], 2, '.', ','));
						}
						else if ($mov['tipo_mov'] == 't') {
							$tpl->assign('unidades_salida', number_format($mov['cantidad'], 2, '.', ','));
							$tpl->assign('turno', $mov['turno']);
							$tpl->assign('color_turno', $mov['turno'] != '' ? ' style="color:#' . $colores[$mov['turno']] . '"' : '');

							$consumos[$mov['turno']] += $mov['cantidad'];

							$ultimo_dia_consumo = $mov['fecha'];
						}

						$tpl->assign('style', $mov['existencia'] >= 0 ? 'green' : 'red underline');
						$tpl->assign('existencia', number_format($mov['existencia'], 2, '.', ','));
						$tpl->assign('costo', number_format($mov['costo'], 2, '.', ','));
						$tpl->assign('precio', number_format($mov['precio_pro'], 4, '.', ','));

						$dif = round($mov['precio_pro'], 4) - round($precio, 4);

						if ($dif != 0) {
							$tpl->assign('dif', number_format($dif, 4, '.', ','));
						}

						$precio = $mov['precio_pro'];
					}
				}

				$tpl->gotoBlock('detallado_' . $aux->inv);

				/*
				@ Consumos del año pasado
				*/

				$condiciones = array();

				$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];

				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];

				$condiciones[] = 'tipo_mov = TRUE';

				$condiciones[] = 'fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio'] - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'] - 1)) . '\'';

				$sql = '
					SELECT
						EXTRACT(month FROM fecha)
							AS mes,
						SUM(cantidad)
							AS consumo
					FROM
						mov_inv_real
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						mes
					ORDER BY
						mes
				';

				$consumos_anuales1 = $db->query($sql);

				$tpl->assign('anio1', $_REQUEST['anio'] - 1);

				if ($consumos_anuales1) {
					foreach ($consumos_anuales1 as $con) {
						$tpl->assign('con1_' . $con['mes'], number_format($con['consumo'], 2, '.', ','));
					}
				}

				/*
				@ Consumos del año pasado
				*/

				$condiciones = array();

				$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];

				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];

				$condiciones[] = 'tipo_mov = TRUE';

				$condiciones[] = 'fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio'])) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'])) . '\'';

				$sql = '
					SELECT
						EXTRACT(month FROM fecha)
							AS mes,
						SUM(cantidad)
							AS consumo
					FROM
						mov_inv_real
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						mes
					ORDER BY
						mes
				';

				$consumos_anuales2 = $db->query($sql);

				$tpl->assign('anio2', $_REQUEST['anio']);

				if ($consumos_anuales2) {
					foreach ($consumos_anuales2 as $con) {
						$tpl->assign('con2_' . $con['mes'], number_format($con['consumo'], 2, '.', ','));
					}
				}

				if (array_sum($consumos) != 0) {
					$tpl->newBlock('consumos_' . $aux->inv);

					foreach ($consumos as $turno => $consumo) {
						if ($consumo != 0) {
							$tpl->newBlock('consumo_' . $aux->inv);
							$tpl->assign('turno', $turno);
							$tpl->assign('consumo', number_format($consumo, 2, '.', ','));
							$tpl->assign('costo', number_format($consumo * $precio, 2, '.', ','));
						}
					}

					list($dia, $mes, $anio) = array_map('toInt', explode('/', $ultimo_dia_consumo));

					$consumo_promedio = round(array_sum($consumos) / $dia, 2);

					$existencia_estimada = floor($aux->mps[$aux->codmp]['existencia'] / $consumo_promedio);

					$tpl->assign('consumos_' . $aux->inv . '.consumo_promedio', number_format($consumo_promedio, 2, '.', ','));
					$tpl->assign('consumos_' . $aux->inv . '.existencia_estimada', number_format($existencia_estimada) . ($existencia_estimada > 1 ? ' DIAS' : ' DIA'));
				}
				else {
					$tpl->newBlock('no_consumo_' . $aux->inv);
				}



				if ($aux->inv == 'real' && in_array($_SESSION['iduser'], array(44, 21))) {
					$sql = '
						SELECT
							num_proveedor
								AS num_pro,
							nombre
								AS nombre_pro,
							fecha,
							AVG(precio_unidad)
								AS precio
						FROM
							mov_inv_real mov
							LEFT JOIN catalogo_proveedores cp
								USING (num_proveedor)
						WHERE
							codmp = ' . $_REQUEST['codmp'] . '
							AND tipo_mov = FALSE
							AND num_proveedor > 0
							AND (num_proveedor, fecha) IN (
								SELECT
									num_proveedor
										AS num_pro,
									MAX(fecha)
										AS fecha
								FROM
									mov_inv_real
								WHERE
									codmp = ' . $_REQUEST['codmp'] . '
									AND tipo_mov = FALSE
									AND num_proveedor > 0
									AND fecha BETWEEN NOW()::DATE - INTERVAL \'1 YEAR\' AND NOW()::DATE
								GROUP BY
									num_pro
							)
						GROUP BY
							num_pro,
							nombre_pro,
							fecha
						ORDER BY
							fecha DESC,
							num_pro
					';

					$precios = $db->query($sql);

					if ($precios) {
						$tpl->newBlock('precios_' . $aux->inv);

						foreach ($precios as $precio) {
							$tpl->newBlock('precio_' . $aux->inv);
							$tpl->assign('num_pro', $precio['num_pro']);
							$tpl->assign('nombre_pro', $precio['nombre_pro']);
							$tpl->assign('fecha', $precio['fecha']);
							$tpl->assign('precio', number_format($precio['precio'], 4));
						}
					}
				}
			}
			else {
				$tpl->newBlock('totales_' . $aux->inv);
				$tpl->assign('num_cia', $aux->num_cia);
				$nombre = $db->query('SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ' . $aux->num_cia);
				$tpl->assign('nombre_cia', $nombre[0]['nombre_corto']);
				$tpl->assign('anio', $aux->anio);
				$tpl->assign('mes', mes_escrito($aux->mes));
				$tpl->assign('_mes', $aux->mes);
				$tpl->assign('inv', $aux->inv);

				$totales = array(
					'costo_ini' => 0,
					'compras' => 0,
					'compras_facturas' => 0,
					'mercancias' => 0,
					'consumos' => 0,
					'costo' =>0,
				);
				foreach ($aux->mps as $cod => $mp) {
					if (round($mp['existencia_ini'], 2) == 0 && round($mp['entradas'], 2) == 0 && round($mp['salidas'], 2) == 0 && round($mp['existencia'], 2) == 0) {
						continue;
					}

					$tpl->newBlock('mp_' . $aux->inv);
					$tpl->assign('codmp', $cod);
					$tpl->assign('nombre', $mp['nombre']);

					$tpl->assign('existencia_ini', round($mp['existencia_ini'], 2) != 0 ? number_format($mp['existencia_ini'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('precio_ini', round($mp['precio_ini'], 4) != 0 ? number_format($mp['precio_ini'], 4, '.', ',') : '&nbsp;');
					$tpl->assign('costo_ini', round($mp['costo_ini'], 2) != 0 ? number_format($mp['costo_ini'], 2, '.', ',') : '&nbsp;');
					$totales['costo_ini'] += round($mp['costo_ini'], 2);

					$tpl->assign('entradas', round($mp['entradas'], 2) != 0 ? number_format($mp['entradas'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('compras', round($mp['compras'], 2) != 0 ? number_format($mp['compras'], 2, '.', ',') : '&nbsp;');
					$totales['compras'] += round($mp['compras'], 2);

					$totales['compras_facturas'] += round($mp['compras'] - $mp['mercancias'], 2);
					$totales['mercancias'] += round($mp['mercancias'], 2);

					$tpl->assign('salidas', round($mp['salidas'], 2) != 0 ? number_format($mp['salidas'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumos', round($mp['consumos'], 2) != 0 ? number_format($mp['consumos'], 2, '.', ',') : '&nbsp;');
					$totales['consumos'] += round($mp['consumos'], 2);

					$tpl->assign('existencia', round($mp['existencia'], 2) != 0 ? number_format($mp['existencia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('style', round($mp['existencia'], 2) < 0 ? ' red underline' : 'green');
					$tpl->assign('precio', round($mp['precio'], 4) != 0 ? number_format($mp['precio'], 4, '.', ',') : '&nbsp;');
					$tpl->assign('costo', round($mp['costo'], 2) != 0 ? number_format($mp['costo'], 2, '.', ',') : '&nbsp;');
					$totales['costo'] += round($mp['costo'], 2);
				}

				foreach ($totales as $k => $v) {
					$tpl->assign('totales_' . $aux->inv . '.' . $k, $v != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
				}
			}

			$tpl->printToScreen();
		break;

		case 'pedido':
			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro,
					fecha,
					folio,
					pedido
				FROM
					pedidos_new p
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND codmp = ' . $_REQUEST['codmp'] . '
					AND num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND fecha BETWEEN \'' . $_REQUEST['fecha'] . '\'::date - INTERVAL \'1 month\' AND \'' . $_REQUEST['fecha'] . '\'
					AND pedido = ' . $_REQUEST['cantidad'] . '
				ORDER BY
					fecha DESC
			';

			$result = $db->query($sql);

			if ($result) {

			}
			else {

			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/AuxiliarInventario.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), date('d') < 5 ? 0 : date('d'), date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), date('d') < 5 ? 0 : date('d'), date('Y'))), ' selected');

$tpl->printToScreen();
?>
