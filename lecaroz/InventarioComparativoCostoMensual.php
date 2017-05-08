<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/auxinv.inc.php');

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

function toInt($value) {
	return intval($value);
}

$_meses = array(
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$fecha1_com = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes_com'], 1, $_REQUEST['anio_com']));
			$fecha2_com = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes_com'] + 1, 0, $_REQUEST['anio_com']));

			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'tipo_mov = TRUE';

			// $condiciones[] = 'codmp NOT IN (90, 310)';
			$condiciones[] = 'codmp NOT IN (90)';

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

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
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

			if (isset($_REQUEST['desglosar'])) {
				$sql = '
					SELECT
						num_cia,
						nombre_cia,
						codmp,
						nombre
							AS nombre_mp,
						consumo,
						costo1,
						precio1,
						costo2,
						precio2,
						status
					FROM
						(
							SELECT
								num_cia,
								nombre_corto
									AS nombre_cia,
								codmp,
								SUM(cantidad)
									AS consumo,
								ROUND((SUM(cantidad) * COALESCE((
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2 . '\'
									LIMIT
										1
								), 0))::NUMERIC, 2)
									AS costo1,
								COALESCE((
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2 . '\'
									LIMIT
										1
								), 0)
									AS precio1,
								ROUND((SUM(cantidad) * COALESCE((
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2_com . '\'
									LIMIT
										1
								),
								(
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2 . '\'
									LIMIT
										1
								), 0))::NUMERIC, 2)
									AS costo2,
								COALESCE((
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2_com . '\'
									LIMIT
										1
								),
								(
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2 . '\'
									LIMIT
										1
								), 0)
									AS precio2,
								COALESCE((
									SELECT
										1
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2_com . '\'
									LIMIT
										1
								),
								(
									SELECT
										2
									FROM
										historico_inventario
									WHERE
										num_cia = mov.num_cia
										AND codmp = mov.codmp
										AND fecha = \'' . $fecha2 . '\'
									LIMIT
										1
								), 0)
									AS status
							FROM
								mov_inv_real mov
								LEFT JOIN catalogo_companias cc
									USING (num_cia)
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								nombre_cia,
								codmp
						) result
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
					ORDER BY
						num_cia,
						codmp
				';

				$result = $db->query($sql);

				$tpl = new TemplatePower('plantillas/fac/InventarioComparativoCostoMensualReporteDesglosado.tpl');
				$tpl->prepare();

				if ($result) {
					$tpl->assign('anio1', $_REQUEST['anio']);
					$tpl->assign('mes1', $_meses[$_REQUEST['mes']]);

					$tpl->assign('anio2', $_REQUEST['anio_com']);
					$tpl->assign('mes2', $_meses[$_REQUEST['mes_com']]);

					$num_cia = NULL;

					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];

							$tpl->newBlock('cia');
							$tpl->assign('num_cia', $rec['num_cia']);
							$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

							$tmp = $db->query("SELECT
								SUM(total_produccion) AS produccion
							FROM
								total_produccion
							WHERE
								numcia = {$rec['num_cia']}
								AND fecha_total BETWEEN '{$fecha1}' AND '{$fecha2}'");

							$produccion1 = $tmp ? $tmp[0]['produccion'] : 0;

							$tpl->assign('produccion1', $produccion1 != 0 ? number_format($produccion1, 2) : '&nbsp;');

							$tmp = $db->query("SELECT
								SUM(total_produccion) AS produccion
							FROM
								total_produccion
							WHERE
								numcia = {$rec['num_cia']}
								AND fecha_total BETWEEN '{$fecha1_com}' AND '{$fecha2_com}'");

							$produccion2 = $tmp ? $tmp[0]['produccion'] : 0;

							$tpl->assign('produccion2', $produccion1 != 0 ? number_format($produccion1, 2) : '&nbsp;');

							$tmp = $db->query("SELECT
								SUM(importe) AS mercancias
							FROM
								movimiento_gastos
							WHERE
								num_cia = {$rec['num_cia']}
								AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
								AND codgastos IN (23, 76)");

							$mercancias = $tmp ? $tmp[0]['mercancias'] : 0;

							$tpl->assign('mercancias1', $mercancias != 0 ? number_format($mercancias, 2) : '&nbsp;');
							$tpl->assign('mercancias2', $mercancias != 0 ? number_format($mercancias, 2) : '&nbsp;');

							$consumos1 = 0;
							$consumos2 = 0;

							$consumos_dif = 0;
						}

						$tpl->newBlock('row');
						$tpl->assign('codmp', $rec['codmp']);
						$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
						$tpl->assign('consumo', $rec['consumo'] != 0 ? number_format($rec['consumo'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('costo1', $rec['costo1'] != 0 ? number_format($rec['costo1'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('precio1', $rec['precio1'] != 0 ? number_format($rec['precio1'], 4, '.', ',') : '&nbsp;');
						$tpl->assign('costo2', $rec['costo2'] != 0 ? number_format($rec['costo2'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('precio2', $rec['precio2'] != 0 ? number_format($rec['precio2'], 4, '.', ',') : '&nbsp;');

						$dif = $rec['costo1'] - $rec['costo2'];

						$por = $rec['costo1'] > 0 ? round($dif * 100 / $rec['costo1'], 2) : 0;

						$tpl->assign('por_dif', $por != 0 ? number_format($por, 2, '.', ',') . '%' : '&nbsp;');
						$tpl->assign('dif', $dif != 0 ? number_format($dif, 2, '.', ',') : '&nbsp;');
						$tpl->assign('color', ($por < 0 ? 'red' : 'blue') . ($rec['status'] == 2 ? ' underline' : ''));

						$consumos1 += $rec['costo1'];
						$consumos2 += $rec['costo2'];

						$consumos_dif += $dif;

						$tpl->assign('cia.consumos1', number_format($consumos1, 2));
						$tpl->assign('cia.consumos2', number_format($consumos2, 2));
						$tpl->assign('cia.por_dif', $consumos1 > 0 ? '<span class="' . (round(($consumos1 - $consumos2) * 100 / $consumos1, 2) < 0 ? 'red' : 'blue') . '">' . number_format(round(($consumos1 - $consumos2) * 100 / $consumos1, 2), 2) . '%</span>' : '&nbsp;');
						$tpl->assign('cia.dif', '<span class="' . ($consumos_dif < 0 ? 'red' : 'blue') . '">' . number_format($consumos_dif, 2) . '</span>');

						$tpl->assign('cia.total1', number_format($consumos1 + $mercancias, 2));
						$tpl->assign('cia.total2', number_format($consumos2 + $mercancias, 2));

						$tpl->assign('cia.mp_pro1', $produccion1 != 0 ? number_format(($consumos1 + $mercancias) / $produccion1, 3) . '%' : '&nbsp;');
						$tpl->assign('cia.mp_pro2', $produccion1 != 0 ? number_format(($consumos2 + $mercancias) / $produccion1, 3) . '%' : '&nbsp;');
						$tpl->assign('cia.mp_pro_dif', $produccion1 != 0 ? number_format((($consumos1 + $mercancias) / $produccion1 - ($consumos2 + $mercancias) / $produccion1) * 100, 2) . '%' : '&nbsp;');
					}
				}
			} else {
				$sql = '
					SELECT
						num_cia,
						nombre_cia,
						tipo_cia,
						SUM(costo1)
							AS costo1,
						SUM(costo2)
							AS costo2,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos
							WHERE
								num_cia = result.num_cia
								AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND codgastos IN (23, 76)
						), 0)
							AS mercancias,
						COALESCE((
							SELECT
								SUM(total_produccion)
							FROM
								total_produccion
							WHERE
								numcia = result.num_cia
								AND fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						), 0)
							AS produccion
					FROM
						(
							SELECT
								num_cia,
								nombre_corto
									AS nombre_cia,
								tipo_cia,
								codmp,
								ROUND((SUM(cantidad) * COALESCE(
									(
										SELECT
											precio_unidad
										FROM
											historico_inventario
										WHERE
											num_cia = mov.num_cia
											AND codmp = mov.codmp
											AND fecha = \'' . $fecha2 . '\'
										LIMIT
											1
									),
									0
								))::NUMERIC, 2)
									AS costo1,
								ROUND((SUM(cantidad) * COALESCE(
									(
										SELECT
											precio_unidad
										FROM
											historico_inventario
										WHERE
											num_cia = mov.num_cia
											AND codmp = mov.codmp
											AND fecha = \'' . $fecha2_com . '\'
										LIMIT
											1
									),
									(
										SELECT
											precio_unidad
										FROM
											historico_inventario
										WHERE
											num_cia = mov.num_cia
											AND codmp = mov.codmp
											AND fecha = \'' . $fecha2 . '\'
										LIMIT
											1
									),
									0
								))::NUMERIC, 2)
									AS costo2
							FROM
								mov_inv_real mov
								LEFT JOIN catalogo_companias cc
									USING (num_cia)
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								nombre_cia,
								tipo_cia,
								codmp
						) result
					GROUP BY
						num_cia,
						nombre_cia,
						tipo_cia
					ORDER BY
						num_cia
				';

				$result = $db->query($sql);

				$tpl = new TemplatePower('plantillas/fac/InventarioComparativoCostoMensualReporte.tpl');
				$tpl->prepare();

				if ($result) {
					$tpl->assign('anio1', $_REQUEST['anio']);
					$tpl->assign('mes1', $_meses[$_REQUEST['mes']]);

					$tpl->assign('anio2', $_REQUEST['anio_com']);
					$tpl->assign('mes2', $_meses[$_REQUEST['mes_com']]);

					$tipo_cia = NULL;

					foreach ($result as $rec) {
						if ($tipo_cia != $rec['tipo_cia'])
						{
							if ($tipo_cia != NULL)
							{
								$tpl->newBlock('totales');

								$tpl->assign('costo1', $total_costo1 != 0 ? number_format($total_costo1, 2) : '&nbsp;');
								$tpl->assign('costo2', $total_costo2 != 0 ? number_format($total_costo2, 2) : '&nbsp;');

								$dif = $total_costo1 - $total_costo2;

								$por = round($dif * 100 / $total_costo1, 2);

								$tpl->assign('dif', $por != 0 ? number_format($por, 2, '.', ',') . '%' : '&nbsp;');

								$tpl->assign('mercancias', $total_mercancias != 0 ? number_format($total_mercancias, 2) : '&nbsp;');
								$tpl->assign('produccion', $total_produccion != 0 ? number_format($total_produccion, 2) : '&nbsp;');

								$mp_pro_dif = (($total_produccion != 0 ? ($total_costo1 + $total_mercancias) / $total_produccion : 0) - ($total_produccion != 0 ? ($total_costo2 + $total_mercancias) / $total_produccion : 0)) * 100;

								$tpl->assign('mp_pro_dif', $mp_pro_dif != 0 ? number_format($mp_pro_dif, 2, '.', ',') . '%' : '&nbsp;');
							}

							$tipo_cia = $rec['tipo_cia'];

							$total_costo1 = 0;
							$total_costo2 = 0;
							$total_mercancias = 0;
							$total_produccion = 0;
						}

						$tpl->newBlock('row');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('costo1', $rec['costo1'] != 0 ? number_format($rec['costo1'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('costo2', $rec['costo2'] != 0 ? number_format($rec['costo2'], 2, '.', ',') : '&nbsp;');

						$dif = $rec['costo1'] - $rec['costo2'];

						$por = round($dif * 100 / $rec['costo1'], 2);

						$tpl->assign('dif', $por != 0 ? number_format($por, 2, '.', ',') . '%' : '&nbsp;');
						$tpl->assign('color', $por < 0 ? 'red' : 'blue');

						$tpl->assign('mercancias', $rec['mercancias'] != 0 ? number_format($rec['mercancias'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('produccion', $rec['produccion'] != 0 ? number_format($rec['produccion'], 2, '.', ',') : '&nbsp;');

						$mp_pro_dif = (($rec['produccion'] != 0 ? ($rec['costo1'] + $rec['mercancias']) / $rec['produccion'] : 0) - ($rec['produccion'] != 0 ? ($rec['costo2'] + $rec['mercancias']) / $rec['produccion'] : 0)) * 100;

						$tpl->assign('mp_pro_dif', $mp_pro_dif != 0 ? number_format($mp_pro_dif, 2, '.', ',') . '%' : '&nbsp;');
						$tpl->assign('color_mp_pro', $mp_pro_dif < 0 ? 'red' : 'blue');

						$total_costo1 += $rec['costo1'];
						$total_costo2 += $rec['costo2'];
						$total_mercancias += $rec['mercancias'];
						$total_produccion += $rec['produccion'];
					}

					if ($tipo_cia != NULL)
					{
						$tpl->newBlock('totales');

						$tpl->assign('costo1', $total_costo1 != 0 ? number_format($total_costo1, 2) : '&nbsp;');
						$tpl->assign('costo2', $total_costo2 != 0 ? number_format($total_costo2, 2) : '&nbsp;');

						$dif = $total_costo1 - $total_costo2;

						$por = round($dif * 100 / $total_costo1, 2);

						$tpl->assign('dif', $por != 0 ? number_format($por, 2, '.', ',') . '%' : '&nbsp;');

						$tpl->assign('mercancias', $total_mercancias != 0 ? number_format($total_mercancias, 2) : '&nbsp;');
						$tpl->assign('produccion', $total_produccion != 0 ? number_format($total_produccion, 2) : '&nbsp;');

						$mp_pro_dif = (($total_produccion != 0 ? ($total_costo1 + $total_mercancias) / $total_produccion : 0) - ($total_produccion != 0 ? ($total_costo2 + $total_mercancias) / $total_produccion : 0)) * 100;

						$tpl->assign('mp_pro_dif', $mp_pro_dif != 0 ? number_format($mp_pro_dif, 2, '.', ',') . '%' : '&nbsp;');
					}
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/InventarioComparativoCostoMensual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n') - 1)));
$tpl->assign(date('n', mktime(0, 0, 0, date('n') - 1)), ' selected');

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

$tpl->printToScreen();
?>
