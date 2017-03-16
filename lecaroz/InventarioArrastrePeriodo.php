<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/class.auxinv.inc.php');

function toInt($value)
{
	return intval($value, 10);
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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes2'] + 1, 0, $_REQUEST['anio2']));

			$condiciones = array();

			$condiciones[] = "h.fecha BETWEEN '{$fecha1}'::DATE - INTERVAL '1 DAY' AND '{$fecha2}'::DATE - INTERVAL '1 MONTH'";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'h.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '')
			{
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0)
				{
					$condiciones[] = 'h.codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			$sql = "SELECT
				h.num_cia,
				cc.nombre_corto AS nombre_cia,
				CASE
					WHEN h.codmp = 90 THEN
						3
					ELSE
						cmp.tipo
				END AS tipo_mp,
				h.codmp,
				cmp.nombre AS nombre_mp,
				EXTRACT(MONTH FROM MIN(fecha) + INTERVAL '1 DAY') AS mes,
				EXTRACT(YEAR FROM MIN(fecha) + INTERVAL '1 DAY') AS anio,
				COALESCE((SELECT COUNT(*) FROM mov_inv_real WHERE num_cia = h.num_cia AND codmp = h.codmp AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'), 0) AS num_movs,
				COALESCE((SELECT existencia FROM historico_inventario WHERE num_cia = h.num_cia AND codmp = h.codmp AND fecha = '{$fecha1}'::DATE - INTERVAL '1 DAY' LIMIT 1), 0) AS existencia_inicial,
				COALESCE((SELECT existencia FROM historico_inventario WHERE num_cia = h.num_cia AND codmp = h.codmp AND fecha = '{$fecha2}' LIMIT 1), 0) AS existencia_final
			FROM
				historico_inventario h
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
			WHERE
				" . implode(' AND ', $condiciones) . "
			GROUP BY
				h.num_cia,
				cc.nombre_corto,
				tipo_mp,
				h.codmp,
				cmp.nombre
			ORDER BY
				h.num_cia,
				tipo_mp,
				h.codmp";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/InventarioArrastrePeriodoReporte.tpl');
			$tpl->prepare();

			if ($result)
			{
				$meses = $db->query("SELECT fecha::DATE, EXTRACT(MONTH FROM fecha) AS mes, EXTRACT(YEAR FROM fecha) AS anio FROM generate_series('{$fecha1}'::TIMESTAMP, '{$fecha2}', '1 MONTH') AS s(fecha)");

				$num_cia = NULL;
				$tipo = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						if ($num_cia != NULL)
						{
							$tpl->newBlock('totales');

							foreach ($totales as $tipo => $total)
							{
								$tpl->newBlock('tipo');

								$tpl->assign('tipo', $tipo == 1 ? 'Materia Prima' : ($tipo == 2 ? 'Material de empaque' : 'Gas'));

								$total['fin'] = $total['inicio'] + $total['entradas'] - $total['salidas'];

								$tpl->assign('inicio', number_format($total['inicio'], 2));
								$tpl->assign('entradas', number_format($total['entradas'], 2));
								$tpl->assign('salidas', number_format($total['salidas'], 2));
								$tpl->assign('fin', number_format($total['fin'], 2));
							}

							$tpl->newBlock('tipo');

							$tpl->assign('tipo', 'General');

							$total_fin = $total_inicio + $total_entradas - $total_salidas;

							$tpl->assign('inicio', number_format($total_inicio, 2));
							$tpl->assign('entradas', number_format($total_entradas, 2));
							$tpl->assign('salidas', number_format($total_salidas, 2));
							$tpl->assign('fin', number_format($total_fin, 2));
						}

						$num_cia = $row['num_cia'];

						$total_inicio = 0;
						$total_entradas = 0;
						$total_salidas = 0;
						$total_fin = 0;

						$totales = array();
					}

					if ($row['num_movs'] == 0 && $row['existencia_inicial'] == 0 && $row['existencia_final'] == 0)
					{
						continue;
					}

					if ($tipo != $row['tipo_mp'])
					{
						$tipo = $row['tipo_mp'];

						$totales[$tipo] = array(
							'inicio'	=> 0,
							'entradas'	=> 0,
							'salidas'	=> 0,
							'fin'		=> 0
						);
					}

					$tpl->newBlock('reporte');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_decode($row['nombre_cia']));

					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_decode($row['nombre_mp']));

					$tpl->assign('mes1', mb_strtoupper($_meses[$_REQUEST['mes1']]));
					$tpl->assign('anio1', $_REQUEST['anio1']);

					$tpl->assign('mes2', mb_strtoupper($_meses[$_REQUEST['mes2']]));
					$tpl->assign('anio2', $_REQUEST['anio2']);

					$ts = NULL;

					$ts_arrastre = mktime(0, 0, 0, $row['mes'], 0, $row['anio']);

					$total_unidades_entrada = 0;
					$total_costo_entrada = 0;

					$total_unidades_salida = 0;
					$total_costo_salida = 0;

					$bgcolor = TRUE;

					foreach ($meses as $mes)
					{
						if (intval($mes['anio'] . $mes['mes']) < intval($row['anio'] . $row['mes']))
						{
							continue;
						}

						$aux = new AuxInvClass($row['num_cia'], $mes['anio'], $mes['mes'], $row['codmp'], 'real', NULL, NULL, NULL);

						if ( ! isset($aux->mps[$aux->codmp]))
						{
							continue;
						}

						if (intval($mes['anio'] . $mes['mes']) == intval($row['anio'] . $row['mes']))
						{
							$tpl->assign('existencia_ini', number_format($aux->mps[$aux->codmp]['existencia_ini'], 2));
							$tpl->assign('costo_ini', number_format($aux->mps[$aux->codmp]['costo_ini'], 2));
							$tpl->assign('precio_ini', number_format($aux->mps[$aux->codmp]['precio_ini'], 4));

							$existencia_ini = $aux->mps[$aux->codmp]['existencia_ini'];
							$costo_ini = $aux->mps[$aux->codmp]['costo_ini'];

							$total_inicio += $aux->mps[$aux->codmp]['costo_ini'];
							$totales[$tipo]['inicio'] += $aux->mps[$aux->codmp]['costo_ini'];
						}

						$precio = $aux->mps[$aux->codmp]['precio_ini'];
						$dif = 0;

						if (count($aux->movs) > 0)
						{
							foreach ($aux->movs[$aux->codmp] as $mov)
							{
								list($dia_mov, $mes_mov, $anio_mov) = array_map('toInt', explode('/', $mov['fecha']));

								$ts_mov = mktime(0, 0, 0, $mes_mov, $dia_mov, $anio_mov);

								if ($ts != $ts_mov || $mov['tipo_mov'] == 'f')
								{
									$ts = $ts_mov;

									$ts_arrastre += 86400;

									if ($ts != $ts_arrastre)
									{
										// $dif_dias = ($ts - $ts_arrastre) / 86400;

										// for ($i = 0; $i < $dif_dias; $i++)
										// {
										// 	$tpl->newBlock('mov');
										// 	$tpl->newBlock('mov_no');

										// 	$tpl->assign('bgcolor', $bgcolor ? 'ddd' : 'fff');
										// 	$tpl->assign('fecha', date('d/m/Y', $ts_arrastre + $i * 86400));

										// 	$bgcolor = ! $bgcolor;
										// }

										$ts_arrastre = $ts;
									}

									$tpl->newBlock('mov');
									$tpl->newBlock('mov_yes');

									$tpl->assign('existencia_ini', '<span class="' . ($existencia_ini > 0 ? 'green' : 'red') . '">' . number_format($existencia_ini, 2) . '</span>');
									$tpl->assign('costo_ini', '<span class="' . ($existencia_ini > 0 ? 'green' : 'red') . '">' . number_format($costo_ini, 2) . '</span>');

									$tpl->assign('bgcolor', $bgcolor ? 'ddd' : 'fff');
									$tpl->assign('fecha', $mov['fecha']);

									$bgcolor = ! $bgcolor;

									$unidades_entrada = 0;
									$costo_entrada = 0;

									$unidades_salida = 0;
									$costo_salida = 0;
								}

								if ($mov['tipo_mov'] == 'f')
								{
									$unidades_entrada += $mov['cantidad'];
									$costo_entrada += $mov['total'];

									$total_unidades_entrada += $mov['cantidad'];
									$total_costo_entrada += $mov['total'];

									$total_entradas += $mov['total'];
									$totales[$tipo]['entradas'] += $mov['total'];

									if ($mov['num_pro'] > 0) {
										$proveedor = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$mov['num_pro']}");

										$tpl->assign('num_pro', $mov['num_pro']);
										$tpl->assign('nombre_pro', utf8_decode($proveedor[0]['nombre']));
										$tpl->assign('num_fact', utf8_decode($mov['num_fact']));
									}

									$tpl->assign('unidades_entrada', number_format($unidades_entrada, 2));
									$tpl->assign('costo_entrada', number_format($costo_entrada, 2));
								}
								else if ($mov['tipo_mov'] == 't')
								{
									$unidades_salida += $mov['cantidad'];
									$costo_salida += $mov['cantidad'] * $aux->mps[$aux->codmp]['precio'];

									$total_unidades_salida += $mov['cantidad'];
									$total_costo_salida += $mov['cantidad'] * $aux->mps[$aux->codmp]['precio'];

									$total_salidas += $mov['cantidad'] * $aux->mps[$aux->codmp]['precio'];
									$totales[$tipo]['salidas'] += $mov['cantidad'] * $aux->mps[$aux->codmp]['precio'];

									$tpl->assign('unidades_salida', number_format($unidades_salida, 2));
									$tpl->assign('costo_salida', number_format($costo_salida, 2));
								}

								$tpl->assign('style', $mov['existencia'] >= 0 ? 'green' : 'red underline');

								$tpl->assign('existencia_fin', '<span class="' . ($mov['existencia'] > 0 ? 'green' : 'red') . '">' . number_format($mov['existencia'], 2) . '</span>');
								$tpl->assign('costo_fin', '<span class="' . ($mov['costo'] > 0 ? 'green' : 'red') . '">' . number_format($mov['costo'], 2) . '</span>');

								$existencia_ini = $mov['existencia'];
								$costo_ini = $mov['costo'];

								$tpl->assign('precio', number_format($mov['precio_pro'], 4));

								$dif = round($mov['precio_pro'], 4) - round($precio, 4);

								if (round($dif, 4) != 0)
								{
									$tpl->assign('dif', '<span class="' . ($dif < 0 ? 'blue' : 'red') . '">' . number_format($dif, 4) . '</span>');
								}

								$precio = $mov['precio_pro'];

								$tpl->assign('reporte.unidades_entrada', number_format($total_unidades_entrada, 2));
								$tpl->assign('reporte.costo_entrada', number_format($total_costo_entrada, 2));

								$tpl->assign('reporte.unidades_salida', number_format($total_unidades_salida, 2));
								$tpl->assign('reporte.costo_salida', number_format($total_costo_salida, 2));

								$tpl->assign('reporte.existencia', number_format($mov['existencia'], 2));
								$tpl->assign('reporte.costo', number_format($mov['costo'], 2));
								$tpl->assign('reporte.precio', number_format($mov['precio_pro'], 4));
							}
						}
						else
						{
							$tpl->assign('reporte.unidades_entrada', number_format($total_unidades_entrada, 2));
							$tpl->assign('reporte.costo_entrada', number_format($total_costo_entrada, 2));

							$tpl->assign('reporte.unidades_salida', number_format($total_unidades_salida, 2));
							$tpl->assign('reporte.costo_salida', number_format($total_costo_salida, 2));

							$tpl->assign('reporte.existencia', number_format($aux->mps[$aux->codmp]['existencia'], 2));
							$tpl->assign('reporte.costo', number_format($aux->mps[$aux->codmp]['costo'], 2));
							$tpl->assign('reporte.precio', number_format($aux->mps[$aux->codmp]['precio'], 4));
						}
					}
				}

				if ($num_cia != NULL)
				{
					$tpl->newBlock('totales');

					foreach ($totales as $tipo => $total)
					{
						$tpl->newBlock('tipo');

						$tpl->assign('tipo', $tipo == 1 ? 'Materia Prima' : ($tipo == 2 ? 'Material de empaque' : 'Gas'));

						$total['fin'] = $total['inicio'] + $total['entradas'] - $total['salidas'];

						$tpl->assign('inicio', number_format($total['inicio'], 2));
						$tpl->assign('entradas', number_format($total['entradas'], 2));
						$tpl->assign('salidas', number_format($total['salidas'], 2));
						$tpl->assign('fin', number_format($total['fin'], 2));
					}

					$tpl->newBlock('tipo');

					$tpl->assign('tipo', 'General');

					$total_fin = $total_inicio + $total_entradas - $total_salidas;

					$tpl->assign('inicio', number_format($total_inicio, 2));
					$tpl->assign('entradas', number_format($total_entradas, 2));
					$tpl->assign('salidas', number_format($total_salidas, 2));
					$tpl->assign('fin', number_format($total_fin, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/InventarioArrastrePeriodo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio1', date('Y'));

$tpl->assign(date('n'), ' selected="selected"');
$tpl->assign('anio2', date('Y'));

$tpl->printToScreen();
