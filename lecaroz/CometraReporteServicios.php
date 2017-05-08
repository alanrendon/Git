<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode'))
{
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value)
	{
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value)
	{
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}

function roundBetter($number, $precision = 0, $mode = PHP_ROUND_HALF_UP, $direction = NULL)
{
	if (!isset($direction) || is_null($direction))
	{
		return round($number, $precision, $mode);
	} else {
		$factor = pow(10, -1 * $precision);

		return strtolower(substr($direction, 0, 1)) == 'd'
			? floor($number / $factor) * $factor
			: ceil($number / $factor) * $factor;
	}
}

function roundBetterUp($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
{
	return roundBetter($number, $precision, $mode, 'up');
}

function roundBetterDown($number, $precision = 0, $mode = PHP_ROUND_HALF_UP)
{
	return roundBetter($number, $precision, $mode, 'down');
}

function round_down($value)
{
	return roundBetterDown($value, -3);
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{
		case 'guardar':
			if ($id = $db->query("SELECT id FROM servicios_cometra_valores"))
			{
				$db->query("UPDATE servicios_cometra_valores
				SET
					costo_servicio = " . (isset($_REQUEST['costo_servicio']) && $_REQUEST['costo_servicio'] != 0 ? get_val($_REQUEST['costo_servicio']) : 0) . ",
					costo_millar = " . (isset($_REQUEST['costo_millar']) && $_REQUEST['costo_millar'] != 0 ? get_val($_REQUEST['costo_millar']) : 0) . ",
					costo_llave = " . (isset($_REQUEST['costo_llave']) && $_REQUEST['costo_llave'] != 0 ? get_val($_REQUEST['costo_llave']) : 0) . ",
					costo_servicio_fijo = " . (isset($_REQUEST['costo_servicio_fijo']) && $_REQUEST['costo_servicio_fijo'] != 0 ? get_val($_REQUEST['costo_servicio_fijo']) : 0) . ",
					cias_servicio_fijo = '" . (isset($_REQUEST['cias_servicio_fijo']) ? $_REQUEST['cias_servicio_fijo'] : '') . "',
					ts = NOW(),
					iduser = {$_SESSION['iduser']}
				WHERE
					id = {$id[0]['id']}");
			}
			else
			{
				$db->query("INSERT INTO servicios_cometra_valores (
					costo_servicio,
					costo_millar,
					costo_llave,
					costo_servicio_fijo,
					cias_servicio_fijo,
					ts,
					iduser
				)
				VALUES (
					" . (isset($_REQUEST['costo_servicio']) && $_REQUEST['costo_servicio'] != 0 ? get_val($_REQUEST['costo_servicio']) : 0) . ",
					" . (isset($_REQUEST['costo_millar']) && $_REQUEST['costo_millar'] != 0 ? get_val($_REQUEST['costo_millar']) : 0) . ",
					" . (isset($_REQUEST['costo_llave']) && $_REQUEST['costo_llave'] != 0 ? get_val($_REQUEST['costo_llave']) : 0) . ",
					" . (isset($_REQUEST['costo_servicio_fijo']) && $_REQUEST['costo_servicio_fijo'] != 0 ? get_val($_REQUEST['costo_servicio_fijo']) : 0) . ",
					'" . (isset($_REQUEST['cias_servicio_fijo']) ? $_REQUEST['cias_servicio_fijo'] : '') . "',
					NOW(),
					{$_SESSION['iduser']}
				)");
			}

			break;

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$dias = (int) date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

			$condiciones[] = 'tsreg::DATE BETWEEN \'' . $fecha1 . '\' AND \''  . $fecha2 . '\'';

			$omitir = array();

			$omitir[] = 'num_cia NOT IN (/*84, */114, 67,/* 73,*/ 32)';

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '')
			{
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0)
				{
					$omitir[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			$sql = '
				SELECT
					num_cia,
					(
						SELECT
							nombre
						FROM
							catalogo_companias
						WHERE
							num_cia = result.num_cia
					)
						AS nombre_cia,
					dia,
					SUM(comprobante)
						AS comprobantes,
					SUM(importe)
						AS importe
				FROM
					(
						SELECT
							CASE
								WHEN reporte = TRUE THEN
									1
								ELSE
									0
							END
								AS comprobante,
							/*MIN(cc.num_cia_primaria)*/
							MIN(CASE
								WHEN (
									SELECT
										num_cia_pri
									FROM
										cometra_prioridades
									WHERE
										num_cia = cc.num_cia_primaria
										AND num_cia_pri IN (
											SELECT
												num_cia
											FROM
												cometra
											WHERE
												comprobante = c.comprobante
										)
									LIMIT
										1
								) > 0 THEN
									(
										SELECT
											num_cia_pri
										FROM
											cometra_prioridades
										WHERE
											num_cia = cc.num_cia_primaria
											AND num_cia_pri IN (
												SELECT
													num_cia
												FROM
													cometra
												WHERE
													comprobante = c.comprobante
											)
										LIMIT
											1
									)
								ELSE
									cc.num_cia_primaria
							END)
								AS num_cia,
							EXTRACT(DAY FROM tsreg)
								AS dia,
							SUM(
								CASE
									WHEN cod_mov IN (19, 48) THEN
										-total
									ELSE
										total
								END
							)
								AS importe
						FROM
							cometra c
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							' . implode(' AND ', $condiciones) . '
							AND concepto NOT IN (\'COMPLEMENTO VENTA\', \'NOMINA CACELADA SEM 3\')
						GROUP BY
							comprobante,
							dia,
							reporte

						UNION

						SELECT
							0
								AS comprobante,
							num_cia,
							EXTRACT(DAY FROM tsreg)
								AS dia,
							importe
						FROM
							cometra
						WHERE
							' . implode(' AND ', $condiciones) . '
							AND concepto IN (\'COMPLEMENTO VENTA\', \'NOMINA CACELADA SEM 3\')
					)
						result
				' . ($omitir ? 'WHERE ' . implode(' AND ', $omitir) : '') . '
				GROUP BY
					num_cia,
					nombre_cia,
					dia
				ORDER BY
					num_cia,
					dia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/cometra/CometraReporteServiciosReporte.tpl');
			$tpl->prepare();

			if ($result)
			{
				// if ($_REQUEST['anio'] < 2014)
				// {
				// 	$precio_llave = 213.98;
				// 	$precio_servicio = 200.51;
				// 	$precio_miles = 2.31;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if ($_REQUEST['anio'] == 2014)
				// {
				// 	$precio_llave = 222.54;
				// 	$precio_servicio = 208.53;
				// 	$precio_miles = 2.31;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if ($_REQUEST['anio'] == 2015)
				// {
				// 	$precio_llave = 233.67;
				// 	$precio_servicio = 218.96;
				// 	$precio_miles = 2.43;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if (/*$_REQUEST['anio'] == 2016*/time() < mktime(0, 0, 0, 9, 1, 2016))
				// {
				// 	$precio_llave = 233.67;
				// 	$precio_servicio = 218.96;
				// 	$precio_miles = 2.43;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else
				// {
					$valores = $db->query("SELECT
						costo_servicio,
						costo_millar,
						costo_llave,
						costo_servicio_fijo,
						cias_servicio_fijo
					FROM
						servicios_cometra_valores
					ORDER BY
						ts DESC
					LIMIT 1");

					$precio_llave = floatval($valores[0]['costo_llave']);
					$precio_servicio = floatval($valores[0]['costo_servicio']);
					$precio_miles = floatval($valores[0]['costo_millar']);

					$cias_servicio_fijo = array();
					$precio_servicio_fijo = floatval($valores[0]['costo_servicio_fijo']);

					if (trim($valores[0]['cias_servicio_fijo']) != '')
					{
						$pieces = explode(',', $valores[0]['cias_servicio_fijo']);

						foreach ($pieces as $piece)
						{
							if (count($exp = explode('-', $piece)) > 1)
							{
								$cias_servicio_fijo[] =  implode(', ', range($exp[0], $exp[1]));
							}
							else {
								$cias_servicio_fijo[] = $piece;
							}
						}
					}
				// }

				/*
				@ [27-Dic-2012] Obtener ajustes de servicios
				*/
				$sql = '
					SELECT
						num_cia,
						ajuste
					FROM
						cometra_servicios_ajustes
					WHERE
						anio = ' . $_REQUEST['anio'] . '
						AND mes = ' . $_REQUEST['mes'] . '
					ORDER BY
						num_cia
				';

				$query = $db->query($sql);

				$ajustes = array();

				if ($query)
				{
					foreach ($query as $row)
					{
						$ajustes[$row['num_cia']] = $row['ajuste'];
					}
				}

				$datos = array();

				$totales = array_fill(1, $dias, 0);

				$comprobantes = array_fill(1, $dias, 0);

				$rango_dias = array();

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						$num_cia = $rec['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'   => utf8_encode($rec['nombre_cia']),
							'importes'     => array_fill(1, $dias, 0),
							'comprobantes' => array_fill(1, $dias, 0)
						);
					}

					$datos[$num_cia]['importes'][$rec['dia']] = $rec['importe'];
					$datos[$num_cia]['comprobantes'][$rec['dia']] = $rec['comprobantes'] - (isset($ajustes[$num_cia]) && $ajustes[$num_cia] > 0 ? 1 : 0);

					$totales[$rec['dia']] += $rec['importe'];
					$comprobantes[$rec['dia']] += $rec['comprobantes'] - (isset($ajustes[$num_cia]) && $ajustes[$num_cia] > 0 ? 1 : 0);

					if (isset($ajustes[$num_cia]) && $ajustes[$num_cia] > 0)
					{
						$ajustes[$num_cia]--;
					}
				}

				foreach ($totales as $dia => $total)
				{
					if ($total == 0)
					{
						unset($rango_dias[$dia], $totales[$dia], $comprobantes[$dia]);

						foreach ($datos as $num_cia => $datos_cia)
						{
							unset($datos[$num_cia]['importes'][$dia], $datos[$num_cia]['comprobantes'][$dia]);
						}
					}
					else {
						$rango_dias[] = $dia;
					}
				}

				$tpl->newBlock('reporte');
				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($rango_dias as $dia)
				{
					$tpl->newBlock('dia');
					$tpl->assign('dia', $dia);
				}

				$t_servicios = 0;
				$t_miles = 0;
				$t_llave = 0;
				$t_total_servicios = 0;
				$t_comprobantes = 0;
				$t_iva = 0;
				$t_retenciones = 0;
				$t_gran_total = 0;

				foreach ($datos as $num_cia => $datos_cia)
				{
					if (isset($_REQUEST['omitir_complementos']) && array_sum($datos_cia['comprobantes']) == 0)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);

					$suma_comprobantes = 0;

					foreach ($datos_cia['importes'] as $dia => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? ($datos_cia['comprobantes'][$dia] > 0 && !in_array($num_cia, array(115, 364, 33)) ? '<span style="float:left;" class="orange font6">(' . $datos_cia['comprobantes'][$dia] . ')</span>&nbsp;' : '') . number_format($importe, 2) : '&nbsp;');

						$suma_comprobantes += $datos_cia['comprobantes'][$dia] > 0 && !in_array($num_cia, array(115, 364, 33)) ? $datos_cia['comprobantes'][$dia] : 0;
					}

					//$comprobantes_cia = array_sum($datos_cia['comprobantes']) > $dias ? $dias : array_sum($datos_cia['comprobantes']);
					$comprobantes_cia = $suma_comprobantes > $dias ? $dias : $suma_comprobantes;

					$servicios = ! in_array($num_cia, $cias_servicio_fijo) ? round($comprobantes_cia * $precio_servicio, 2) : 0;
					$miles =  ! in_array($num_cia, $cias_servicio_fijo) ? (array_sum($datos_cia['importes']) > 0 ? round(array_sum($datos_cia['importes']) / 1000 * $precio_miles, 2) : 0) : 0;
					$llave =  ! in_array($num_cia, $cias_servicio_fijo) ? ($comprobantes_cia > 10 ? $precio_llave : 0) : 0;
					$total_servicios =  in_array($num_cia, $cias_servicio_fijo) ? $precio_servicio_fijo : round($servicios + $miles + $llave, 2);
					$iva = round($total_servicios * 0.16, 2);
					$retenciones = round($total_servicios * 0.04, 2);
					$gran_total = $total_servicios + $iva - $retenciones;

					$t_servicios += $servicios;
					$t_miles += $miles;
					$t_llave += $llave;
					$t_total_servicios += $total_servicios;
					$t_comprobantes += $comprobantes_cia;
					$t_iva += $iva;
					$t_retenciones += $retenciones;
					$t_gran_total += $gran_total;

					$tpl->assign('row.total', ($comprobantes_cia > 0 ? '<span style="float:left;" class="orange font6">(' . $comprobantes_cia . ')</span>&nbsp;' : '') . number_format(array_sum($datos_cia['importes']), 2));
					$tpl->assign('row.servicios', $servicios != 0 ? number_format($servicios, 2) : '&nbsp;');
					$tpl->assign('row.miles', $miles != 0 ? number_format($miles, 2) : '&nbsp;');
					$tpl->assign('row.llave', $llave != 0 ? number_format($llave, 2) : '&nbsp;');
					$tpl->assign('row.total_servicios', $total_servicios != 0 ? number_format($total_servicios, 2) : '&nbsp;');
					$tpl->assign('row.iva', $iva != 0 ? number_format($iva, 2) : '&nbsp;');
					$tpl->assign('row.retenciones', $retenciones != 0 ? number_format($retenciones, 2) : '&nbsp;');
					$tpl->assign('row.gran_total', $gran_total != 0 ? number_format($gran_total, 2) : '&nbsp;');
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->newBlock('total');
					$tpl->assign('total', $total != 0 ? '<span style="float:left;" class="orange font6">(' . $comprobantes[$mes] . ')</span>&nbsp;' . number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('reporte.total', '<span style="float:left;" class="orange font6">(' . /*array_sum($comprobantes)*/$t_comprobantes . ')</span>&nbsp;' . number_format(array_sum($totales), 2));

				$tpl->assign('reporte.servicios', number_format($t_servicios, 2));
				$tpl->assign('reporte.miles', number_format($t_miles, 2));
				$tpl->assign('reporte.llave', number_format($t_llave, 2));
				$tpl->assign('reporte.total_servicios', number_format($t_total_servicios, 2));
				$tpl->assign('reporte.iva', number_format($t_iva, 2));
				$tpl->assign('reporte.retenciones', number_format($t_retenciones, 2));
				$tpl->assign('reporte.gran_total', number_format($t_gran_total, 2));
			}

			$tpl->printToScreen();
		break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$dias = (int) date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'tsreg::DATE BETWEEN \'' . $fecha1 . '\' AND \''  . $fecha2 . '\'';

			$omitir = array();

			$omitir[] = 'num_cia NOT IN (/*84, */114, 67,/* 73,*/ 32)';

			if (isset($_REQUEST['omitir_zap']))
			{
				$omitir[] = 'num_cia < 900';
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '')
			{
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0)
				{
					$omitir[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			$sql = '
				SELECT
					num_cia,
					(
						SELECT
							nombre
						FROM
							catalogo_companias
						WHERE
							num_cia = result.num_cia
					)
						AS nombre_cia,
					dia,
					SUM(comprobante)
						AS comprobantes,
					SUM(importe)
						AS importe
				FROM
					(
						SELECT
							CASE
								WHEN reporte = TRUE THEN
									1
								ELSE
									0
							END
								AS comprobante,
							/*MIN(cc.num_cia_primaria)*/
							MIN(CASE
								WHEN (
									SELECT
										num_cia_pri
									FROM
										cometra_prioridades
									WHERE
										num_cia = cc.num_cia_primaria
										AND num_cia_pri IN (
											SELECT
												num_cia
											FROM
												cometra
											WHERE
												comprobante = c.comprobante
										)
									LIMIT
										1
								) > 0 THEN
									(
										SELECT
											num_cia_pri
										FROM
											cometra_prioridades
										WHERE
											num_cia = cc.num_cia_primaria
											AND num_cia_pri IN (
												SELECT
													num_cia
												FROM
													cometra
												WHERE
													comprobante = c.comprobante
											)
										LIMIT
											1
									)
								ELSE
									cc.num_cia_primaria
							END)
								AS num_cia,
							EXTRACT(DAY FROM tsreg)
								AS dia,
							SUM(
								CASE
									WHEN cod_mov IN (19, 48) THEN
										-total
									ELSE
										total
								END
							)
								AS importe
						FROM
							cometra c
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							' . implode(' AND ', $condiciones) . '
							AND concepto NOT IN (\'COMPLEMENTO VENTA\', \'NOMINA CACELADA SEM 3\')
						GROUP BY
							comprobante,
							dia,
							reporte

						UNION

						SELECT
							0
								AS comprobante,
							num_cia,
							EXTRACT(DAY FROM tsreg)
								AS dia,
							importe
						FROM
							cometra
						WHERE
							' . implode(' AND ', $condiciones) . '
							AND concepto IN (\'COMPLEMENTO VENTA\', \'NOMINA CACELADA SEM 3\')
					)
						result
				' . ($omitir ? ' WHERE ' . implode(' AND ', $omitir) : '') . '
				GROUP BY
					num_cia,
					nombre_cia,
					dia
				ORDER BY
					num_cia,
					dia
			';

			$result = $db->query($sql);

			$data = '';

			if ($result)
			{
				function Format($value)
				{
					return $value != 0 ? number_format($value, 2) : '';
				}

				// if ($_REQUEST['anio'] < 2014)
				// {
				// 	$precio_llave = 213.98;
				// 	$precio_servicio = 200.51;
				// 	$precio_miles = 2.31;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if ($_REQUEST['anio'] == 2014)
				// {
				// 	$precio_llave = 222.54;
				// 	$precio_servicio = 208.53;
				// 	$precio_miles = 2.31;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if ($_REQUEST['anio'] == 2015)
				// {
				// 	$precio_llave = 233.67;
				// 	$precio_servicio = 218.96;
				// 	$precio_miles = 2.43;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else if (/*$_REQUEST['anio'] == 2016*/time() < mktime(0, 0, 0, 9, 1, 2016))
				// {
				// 	$precio_llave = 233.67;
				// 	$precio_servicio = 218.96;
				// 	$precio_miles = 2.43;

				// 	$cias_servicio_fijo = array();
				// 	$precio_servicio_fijo = 0;
				// }
				// else
				// {
					$valores = $db->query("SELECT
						costo_servicio,
						costo_millar,
						costo_llave,
						costo_servicio_fijo,
						cias_servicio_fijo
					FROM
						servicios_cometra_valores
					ORDER BY
						ts DESC
					LIMIT 1");

					$precio_llave = floatval($valores[0]['costo_llave']);
					$precio_servicio = floatval($valores[0]['costo_servicio']);
					$precio_miles = floatval($valores[0]['costo_millar']);

					$cias_servicio_fijo = array();
					$precio_servicio_fijo = floatval($valores[0]['costo_servicio_fijo']);

					if (trim($valores[0]['cias_servicio_fijo']) != '')
					{
						$pieces = explode(',', $valores[0]['cias_servicio_fijo']);

						foreach ($pieces as $piece)
						{
							if (count($exp = explode('-', $piece)) > 1)
							{
								$cias_servicio_fijo[] =  implode(', ', range($exp[0], $exp[1]));
							}
							else {
								$cias_servicio_fijo[] = $piece;
							}
						}
					}
				// }

				$datos = array();

				$totales = array_fill(1, $dias, 0);

				$comprobantes = array_fill(1, $dias, 0);

				$rango_dias = array();

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						$num_cia = $rec['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'   => utf8_decode(utf8_encode($rec['nombre_cia'])),
							'importes'     => array_fill(1, $dias, 0),
							'comprobantes' => array_fill(1, $dias, 0)
						);
					}

					$datos[$num_cia]['importes'][$rec['dia']] = $rec['importe'];
					$datos[$num_cia]['comprobantes'][$rec['dia']] = $rec['comprobantes'];

					$totales[$rec['dia']] += $rec['importe'];
					$comprobantes[$rec['dia']] += $rec['comprobantes'];
				}

				foreach ($totales as $dia => $total)
				{
					if ($total == 0)
					{
						unset($rango_dias[$dia], $totales[$dia], $comprobantes[$dia]);

						foreach ($datos as $num_cia => $datos_cia)
						{
							unset($datos[$num_cia]['importes'][$dia], $datos[$num_cia]['comprobantes'][$dia]);
						}
					}
					else {
						$rango_dias[] = $dia;
					}
				}

				$data .= '"Reporte de Servicios Cometra ' . $_meses[$_REQUEST['mes']] . ' ' . $_REQUEST['anio'] . '"' . "\r\n\r\n";

				$data .= utf8_decode('"C=Número de comprobantes"' . "\r\n");
				$data .= utf8_decode('"I=Importe de comprobantes"' . "\r\n\r\n");

				$data .= utf8_decode('"#","Compañía",');

				foreach ($rango_dias as $dia)
				{
					$data .= '"' . $dia . '(C)","' . $dia . '(I)",';
				}

				$data .= '"Total(C)","Total(I)","Servicios","Miles","M. de llave","Total servicios","I.V.A.","Retenciones","Gran total"' . "\r\n";

				$t_servicios = 0;
				$t_miles = 0;
				$t_llave = 0;
				$t_total_servicios = 0;
				$t_comprobantes = 0;
				$t_iva = 0;
				$t_retenciones = 0;
				$t_gran_total = 0;

				foreach ($datos as $num_cia => $datos_cia)
				{
					if (isset($_REQUEST['omitir_complementos']) && array_sum($datos_cia['comprobantes']) == 0)
					{
						continue;
					}

					$data .= '"' . $num_cia . '","' . $datos_cia['nombre_cia'] . '",';

					$suma_comprobantes = 0;

					foreach ($datos_cia['importes'] as $dia => $importe)
					{
						$data .= '"' . ($datos_cia['comprobantes'][$dia] != 0 && !in_array($num_cia, array(115, 364, 33)) ? $datos_cia['comprobantes'][$dia] : '') . '","' . ($importe != 0 ? number_format($importe, 2) : '') . '",';

						$suma_comprobantes += $datos_cia['comprobantes'][$dia] > 0 && !in_array($num_cia, array(115, 364, 33)) ? $datos_cia['comprobantes'][$dia] : 0;
					}

					//$comprobantes_cia = array_sum($datos_cia['comprobantes']) > $dias ? $dias : array_sum($datos_cia['comprobantes']);
					$comprobantes_cia = $suma_comprobantes > $dias ? $dias : $suma_comprobantes;

					$data .= '"' . number_format($comprobantes_cia, 2) . '","' . number_format(array_sum($datos_cia['importes']), 2) . '",';

					$servicios = ! in_array($num_cia, $cias_servicio_fijo) ? round($comprobantes_cia * $precio_servicio, 2) : 0;
					$miles =  ! in_array($num_cia, $cias_servicio_fijo) ? (array_sum($datos_cia['importes']) > 0 ? round(array_sum($datos_cia['importes']) / 1000 * $precio_miles, 2) : 0) : 0;
					$llave =  ! in_array($num_cia, $cias_servicio_fijo) ? ($comprobantes_cia > 10 ? $precio_llave : 0) : 0;
					$total_servicios =  in_array($num_cia, $cias_servicio_fijo) ? $precio_servicio_fijo : round($servicios + $miles + $llave, 2);
					$iva = round($total_servicios * 0.16, 2);
					$retenciones = round($total_servicios * 0.04, 2);
					$gran_total = $total_servicios + $iva - $retenciones;

					$t_servicios += $servicios;
					$t_miles += $miles;
					$t_llave += $llave;
					$t_total_servicios += $total_servicios;
					$t_comprobantes += $comprobantes_cia;
					$t_iva += $iva;
					$t_retenciones += $retenciones;
					$t_gran_total += $gran_total;

					$data .= '"' . number_format($servicios, 2) . '","' . number_format($miles, 2) . '","' . number_format($llave, 2) . '","' . number_format($total_servicios, 2) . '","' . number_format($iva, 2) . '","' . number_format($retenciones, 2) . '","' . number_format($gran_total, 2) . '"' . "\r\n";
				}

				$data .= ',"Totales",';

				foreach ($totales as $mes => $total)
				{
					$data .= '"' . ($comprobantes[$mes] != 0 ? $comprobantes[$mes] : '') . '","' . number_format($total, 2) . '",';
				}

				$data .= '"' . /*array_sum($comprobantes)*/$t_comprobantes . '","' . number_format(array_sum($totales), 2) . '",';
				$data .= '"' . number_format($t_servicios, 2) . '","' . number_format($t_miles, 2) . '","' . number_format($t_llave, 2) . '","' . number_format($t_total_servicios, 2) . '","' . number_format($t_iva, 2) . '","' . number_format($t_retenciones, 2) . '","' . number_format($t_gran_total, 2) . '"' . "\r\n";
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ReporteServiciosCometra' . $_meses[$_REQUEST['mes']] . $_REQUEST['anio'] . '.csv"');

			echo $data;
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/CometraReporteServicios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$tpl->assign(date('n'), ' selected');

$tpl->assign('omitir_cias', $_SESSION['tipo_usuario'] == 1 ? '28,312,336,347,359,378,35,319,45,346,66,132,61,58' : '');

$result = $db->query("SELECT * FROM servicios_cometra_valores");

if ($result)
{
	$datos = $result[0];

	$tpl->assign('costo_servicio', number_format($datos['costo_servicio'], 2));
	$tpl->assign('costo_millar', number_format($datos['costo_millar'], 2));
	$tpl->assign('costo_llave', number_format($datos['costo_llave'], 2));
	$tpl->assign('costo_servicio_fijo', number_format($datos['costo_servicio_fijo'], 2));
	$tpl->assign('cias_servicio_fijo', $datos['cias_servicio_fijo']);
}

$tpl->printToScreen();
?>
