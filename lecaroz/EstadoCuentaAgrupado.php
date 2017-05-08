<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

		case 'obtener_codigos':
			$condiciones = array();

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 'banco = ' . $_REQUEST['banco'];
			}

			if (!isset($_REQUEST['depositos']) && !isset($_REQUEST['tipo_mov']))
			{
				$condiciones[] = 'tipo_mov = TRUE';
			}

			if (!isset($_REQUEST['cargos']) && !isset($_REQUEST['tipo_mov']))
			{
				$condiciones[] = 'tipo_mov = FALSE';
			}

			if (isset($_REQUEST['tipo_mov']))
			{
				$condiciones[] = 'tipo_mov = ' . $_REQUEST['tipo_mov'];
			}

			$query = $db->query("SELECT
				*
			FROM
				(
					SELECT
						1 AS banco,
						tipo_mov,
						cod_mov AS value,
						cod_mov || ' ' || descripcion AS text
					FROM
						catalogo_mov_bancos

					UNION

					SELECT
						2 AS banco,
						tipo_mov,
						cod_mov AS value,
						cod_mov || ' ' || descripcion AS text
					FROM
						catalogo_mov_santander

					GROUP BY
						banco,
						tipo_mov,
						value,
						text
				)
					AS result

			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			ORDER BY
				banco,
				value");

			if ($query)
			{
				$data = array();

				$banco = NULL;
				foreach ($query as $row)
				{
					if ($banco != $row['banco'] && !isset($_REQUEST['no_banco']))
					{
						$banco = $row['banco'];

						$data[] = array(
							'text'     => $banco == 1 ? 'BANORTE' : 'SANTANDER',
							'disabled' => 'disabled',
							'class'    => 'bold underline logo_banco logo_banco_' . $banco,
							'styles' => array(
								'margin' => '4px 0'
							)
						);
					}
					$data[] = $row;
				}

				echo json_encode($data);
			}

			break;

		case 'consultar':
			$condiciones1 = array();
			$condiciones2 = array();


			$condiciones1[] = "ec.fecha_con IS NOT NULL";
			$condiciones2[] = "ec.fecha_con IS NULL";

			if (!in_array($_SESSION['iduser'], array(1, 4, 26)))
			{
				if ($_SESSION['tipo_usuario'] == 2)
				{
					$condiciones1[] = 'ec.num_cia BETWEEN 900 AND 998';
					$condiciones2[] = 'ec.num_cia BETWEEN 900 AND 998';
				}
				else {
					$condiciones1[] = 'ec.num_cia BETWEEN 1 AND 899';
					$condiciones2[] = 'ec.num_cia BETWEEN 1 AND 899';
				}
			}

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
					$condiciones1[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
					$condiciones2[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones1[] = 'ec.cuenta = ' . $_REQUEST['banco'];
				$condiciones2[] = 'ec.cuenta = ' . $_REQUEST['banco'];
			}

			// if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
			// 	|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			// {
			// 	if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
			// 		&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			// 	{
			// 		$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			// 	}
			// 	else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
			// 	{
			// 		$condiciones[] = 'ec.fecha >= \'' . $_REQUEST['fecha1'] . '\'';
			// 	}
			// 	else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
			// 	{
			// 		$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha2'] . '\'';
			// 	}
			// }

			if ((isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '')
				|| (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != ''))
			{
				if ((isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '')
					&& (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != ''))
				{
					$condiciones1[] = 'ec.fecha_con BETWEEN \'' . $_REQUEST['conciliado1'] . '\' AND \'' . $_REQUEST['conciliado2'] . '\'';
					$condiciones2[] = 'ec.fecha BETWEEN \'' . $_REQUEST['conciliado1'] . '\' AND \'' . $_REQUEST['conciliado2'] . '\'';
				}
				else if (isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '')
				{
					$condiciones1[] = 'ec.fecha_con >= \'' . $_REQUEST['conciliado1'] . '\'';
					$condiciones2[] = 'ec.fecha >= \'' . $_REQUEST['conciliado1'] . '\'';
				}
				else if (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != '')
				{
					$condiciones1[] = 'ec.fecha_con = \'' . $_REQUEST['conciliado2'] . '\'';
					$condiciones2[] = 'ec.fecha = \'' . $_REQUEST['conciliado2'] . '\'';
				}
			}

			// if (!isset($_REQUEST['depositos']))
			// {
			// 	$condiciones[] = 'ec.tipo_mov = TRUE';
			// }

			// if (!isset($_REQUEST['cargos']))
			// {
			// 	$condiciones[] = 'ec.tipo_mov = FALSE';
			// }

			// if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			// {
			// 	$pros = array();

			// 	$pieces = explode(',', $_REQUEST['pros']);
			// 	foreach ($pieces as $piece)
			// 	{
			// 		if (count($exp = explode('-', $piece)) > 1)
			// 		{
			// 			$pros[] =  implode(', ', range($exp[0], $exp[1]));
			// 		}
			// 		else {
			// 			$pros[] = $piece;
			// 		}
			// 	}

			// 	if (count($pros) > 0)
			// 	{
			// 		$condiciones[] = 'c.num_proveedor IN (' . implode(', ', $pros) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '')
			// {
			// 	$folios = array();

			// 	$pieces = explode(',', $_REQUEST['folios']);
			// 	foreach ($pieces as $piece)
			// 	{
			// 		if (count($exp = explode('-', $piece)) > 1)
			// 		{
			// 			$folios[] =  implode(', ', range($exp[0], $exp[1]));
			// 		}
			// 		else {
			// 			$folios[] = $piece;
			// 		}
			// 	}

			// 	if (count($folios) > 0)
			// 	{
			// 		$condiciones[] = 'ec.folio IN (' . implode(', ', $folios) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '')
			// {
			// 	$gastos = array();

			// 	$pieces = explode(',', $_REQUEST['gastos']);
			// 	foreach ($pieces as $piece)
			// 	{
			// 		if (count($exp = explode('-', $piece)) > 1)
			// 		{
			// 			$gastos[] =  implode(', ', range($exp[0], $exp[1]));
			// 		}
			// 		else {
			// 			$gastos[] = $piece;
			// 		}
			// 	}

			// 	if (count($gastos) > 0)
			// 	{
			// 		$condiciones[] = 'c.codgastos IN (' . implode(', ', $gastos) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['importes']) && trim($_REQUEST['importes']) != '')
			// {
			// 	$importes = array();
			// 	$rangos = array();

			// 	$pieces = explode(',', $_REQUEST['importes']);
			// 	foreach ($pieces as $piece)
			// 	{
			// 		if (count($exp = explode('-', $piece)) > 1)
			// 		{
			// 			$rangos[] =  'ec.importe BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
			// 		}
			// 		else {
			// 			$importes[] = $piece;
			// 		}
			// 	}

			// 	$filtros = array();

			// 	if ($importes)
			// 	{
			// 		$filtros[] = 'ec.importe IN (' . implode(', ', $importes) . ')';
			// 	}

			// 	if ($rangos)
			// 	{
			// 		$filtros[] = implode(' OR ', $rangos);
			// 	}

			// 	if ($filtros)
			// 	{
			// 		$condiciones[] = '(' . implode(' OR ', $filtros) . ')';
			// 	}
			// }

			// if (isset($_REQUEST['codigos']) && count($_REQUEST['codigos']) > 0)
			// {
			// 	$condiciones[] = 'ec.cod_mov IN (' . implode(', ', $_REQUEST['codigos']) . ')';
			// }

			// if (isset($_REQUEST['concepto']) && $_REQUEST['concepto'] != '')
			// {
			// 	$condiciones[] = 'ec.concepto LIKE \'%' . $_REQUEST['concepto'] . '%\'';
			// }

			$query = $db->query("SELECT
				ec.id,
				ec.num_cia,
				cc.nombre || '(' || cc.nombre_corto || ')'AS nombre_cia,
				cc.clabe_cuenta AS cuenta1,
				cc.clabe_cuenta2 AS cuenta2,
				ec.cuenta AS banco,
				ec.fecha,
				ec.fecha_con AS conciliado,
				CASE
					WHEN ec.tipo_mov = FALSE THEN
						ec.importe
					ELSE
						NULL
				END AS deposito,
				CASE
					WHEN ec.tipo_mov = TRUE THEN
						ec.importe
					ELSE
						NULL
				END AS cargo,
				ec.folio,
				c.num_proveedor || ' ' || c.a_nombre AS beneficiario,
				c.codgastos || ' ' || (
					SELECT
						descripcion
					FROM
						catalogo_gastos
					WHERE
						codgastos = c.codgastos
				) AS gasto,
				ec.concepto,
				ec.cod_mov || ' ' || (
					CASE
						WHEN cuenta = 1 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_bancos
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
						WHEN cuenta = 2 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_santander
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
					END
				) AS codigo,
				ec.cod_mov,
				/*
				@ Información de conciliación
				*/
				CASE
					WHEN ec.fecha_con IS NOT NULL AND ec.tipo_con > 0 THEN
						'Usuario: ' || COALESCE(a.nombre, 'Imposible recuperar usuario') || '<br />Fecha: ' || (
							CASE
								WHEN ec.timestamp IS NOT NULL THEN
									ec.timestamp::timestamp(0)::text
								ELSE
									'Imposible recuperar fecha de conciliaci&oacute;n'
							END
						)
					WHEN ec.fecha_con IS NOT NULL AND (ec.tipo_con = 0 OR ec.tipo_con IS NULL) THEN
						'Imposible recuperar datos de conciliaci&oacute;n'
					ELSE
						NULL
				END AS info,
				CASE
					WHEN ec.fecha_con IS NOT NULL AND ec.fecha BETWEEN '{$_REQUEST['conciliado1']}' AND '{$_REQUEST['conciliado2']}' THEN
						1
					ELSE
						2
				END AS tipo
			FROM
				estado_cuenta ec
				LEFT JOIN cheques c USING (num_cia, cuenta, folio, fecha)
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN auth a ON (a.iduser = ec.iduser)
			WHERE
				" . implode(' AND ', $condiciones1) . "

			UNION ALL

			SELECT
				ec.id,
				ec.num_cia,
				cc.nombre || '(' || cc.nombre_corto || ')'AS nombre_cia,
				cc.clabe_cuenta AS cuenta1,
				cc.clabe_cuenta2 AS cuenta2,
				ec.cuenta AS banco,
				ec.fecha,
				ec.fecha_con AS conciliado,
				CASE
					WHEN ec.tipo_mov = FALSE THEN
						ec.importe
					ELSE
						NULL
				END AS deposito,
				CASE
					WHEN ec.tipo_mov = TRUE THEN
						ec.importe
					ELSE
						NULL
				END AS cargo,
				ec.folio,
				c.num_proveedor || ' ' || c.a_nombre AS beneficiario,
				c.codgastos || ' ' || (
					SELECT
						descripcion
					FROM
						catalogo_gastos
					WHERE
						codgastos = c.codgastos
				) AS gasto,
				ec.concepto,
				ec.cod_mov || ' ' || (
					CASE
						WHEN cuenta = 1 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_bancos
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
						WHEN cuenta = 2 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_santander
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
					END
				) AS codigo,
				ec.cod_mov,
				/*
				@ Información de conciliación
				*/
				CASE
					WHEN ec.fecha_con IS NOT NULL AND ec.tipo_con > 0 THEN
						'Usuario: ' || COALESCE(a.nombre, 'Imposible recuperar usuario') || '<br />Fecha: ' || (
							CASE
								WHEN ec.timestamp IS NOT NULL THEN
									ec.timestamp::timestamp(0)::text
								ELSE
									'Imposible recuperar fecha de conciliaci&oacute;n'
							END
						)
					WHEN ec.fecha_con IS NOT NULL AND (ec.tipo_con = 0 OR ec.tipo_con IS NULL) THEN
						'Imposible recuperar datos de conciliaci&oacute;n'
					ELSE
						NULL
				END AS info,
				2 AS tipo
			FROM
				estado_cuenta ec
				LEFT JOIN cheques c USING (num_cia, cuenta, folio, fecha)
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN auth a ON (a.iduser = ec.iduser)
			WHERE
				" . implode(' AND ', $condiciones2) . "

			ORDER BY
				num_cia,
				tipo,
				fecha,
				id");

			$tpl = new TemplatePower('plantillas/ban/EstadoCuentaAgrupadoReporte.tpl');
			$tpl->prepare();

			if ($query)
			{
				$num_cia = NULL;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						if ($num_cia != NULL)
						{
							if ($tipo != NULL)
							{
								if (isset($_REQUEST['conciliados']) && $tipo == 1)
								{
									$tpl->newBlock('conciliados');
									$tpl->assign('conciliados.depositos', $depositos_con != 0 ? number_format($depositos_con, 2) : '--');
									$tpl->assign('conciliados.cargos', $cargos_con != 0 ? number_format($cargos_con, 2) : '--');
								}

								if (isset($_REQUEST['pendientes']) && $tipo == 2)
								{
									$tpl->newBlock('totales');
									$tpl->assign('totales.depositos', $depositos_pen != 0 ? number_format($depositos_pen, 2) : '--');
									$tpl->assign('totales.cargos', $cargos_pen != 0 ? number_format($cargos_pen, 2) : '--');
								}
							}

							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$num_cia = $row['num_cia'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
						$tpl->assign('fecha', date('d/m/Y'));
						$tpl->assign('hora', date('H:i'));

						$depositos = 0;
						$cargos = 0;

						$depositos_con = 0;
						$cargos_con = 0;

						$depositos_pen = 0;
						$cargos_pen = 0;

						$tipo = NULL;

						if (TRUE)
						{
							$condiciones = array();

							$condiciones[] = 'num_cia = ' . $num_cia;

							if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
							{
								$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
							}

							$saldos = $db->query("SELECT
								cuenta AS banco,
								saldo_bancos - COALESCE((
									SELECT
										SUM(
											CASE
												WHEN tipo_mov = TRUE THEN
													-importe
												ELSE
													importe
											END
										)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha >= '{$_REQUEST['conciliado1']}'
										AND fecha_con IS NOT NULL
								), 0) AS banco_ini,
								saldo_bancos - COALESCE((
									SELECT
										SUM(
											CASE
												WHEN tipo_mov = TRUE THEN
													-importe
												ELSE
													importe
											END
										)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha > '{$_REQUEST['conciliado2']}'
										AND fecha_con IS NOT NULL
								), 0) AS banco_fin,
								saldo_libros - COALESCE((
									SELECT
										SUM(
											CASE
												WHEN tipo_mov = TRUE THEN
													-importe
												ELSE
													importe
											END
										)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha >= '{$_REQUEST['conciliado1']}'
								), 0) AS libro_ini,
								saldo_libros - COALESCE((
									SELECT
										SUM(
											CASE
												WHEN tipo_mov = TRUE THEN
													-importe
												ELSE
													importe
											END
										)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha > '{$_REQUEST['conciliado2']}'
								), 0) AS libro_fin
							FROM
								saldos s
							WHERE
								" . implode(' AND ', $condiciones) . "
							ORDER BY
								banco");

							$tpl->newBlock('saldos_ini');

							$banco_ini = 0;
							$libro_ini = 0;

							foreach ($saldos as $s)
							{
								$tpl->newBlock('banco_ini');

								$tpl->assign('logo_banco', $s['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
								$tpl->assign('banco', $s['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($row['cuenta' . $s['banco']]) != '' ? $row['cuenta' . $s['banco']] : '[SIN CUENTA]');
								$tpl->assign('saldo_banco', round($s['banco_ini'], 2) != 0 ? number_format($s['banco_ini'], 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($s['libro_ini'], 2) != 0 ? number_format($s['libro_ini'], 2, '.', ',') : '--');

								$banco_ini += round($s['banco_ini'], 2);
								$libro_ini += round($s['libro_ini'], 2);
							}

							if (!isset($_REQUEST['banco']) || $_REQUEST['banco'] < 1)
							{
								$tpl->newBlock('total_ini');
								$tpl->assign('saldo_banco', round($banco_ini, 2) != 0 ? number_format($banco_ini, 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($libro_ini, 2) != 0 ? number_format($libro_ini, 2, '.', ',') : '--');
							}

							$tpl->newBlock('saldos_fin');
							$banco_fin = 0;
							$libro_fin = 0;
							foreach ($saldos as $s)
							{
								$tpl->newBlock('banco_fin');
								$tpl->assign('logo_banco', $s['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
								$tpl->assign('banco', $s['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($row['cuenta' . $s['banco']]) != '' ? $row['cuenta' . $s['banco']] : '[SIN CUENTA]');
								$tpl->assign('saldo_banco', round($s['banco_fin'], 2) != 0 ? number_format($s['banco_fin'], 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($s['libro_fin'], 2) != 0 ? number_format($s['libro_fin'], 2, '.', ',') : '--');

								$dif = round($s['banco_fin'] - $s['libro_fin'], 2);

								$tpl->assign('dif', $dif != 0 ? number_format($dif, 2, '.', ',') : '--');
								$tpl->assign('color_dif', round($dif, 2) != 0 ? ($dif > 0 ? ' blue' : ' red') : '');

								$banco_fin += round($s['banco_fin'], 2);
								$libro_fin += round($s['libro_fin'], 2);
							}


							if (!isset($_REQUEST['banco']) || $_REQUEST['banco'] < 1)
							{
								$tpl->newBlock('total_fin');
								$tpl->assign('saldo_banco', round($banco_fin, 2) != 0 ? number_format($banco_fin, 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($libro_fin, 2) != 0 ? number_format($libro_fin, 2, '.', ',') : '--');
								$dif = $banco_fin - $libro_fin;
								$tpl->assign('dif', round($dif, 2) != 0 ? number_format($dif, 2, '.', ',') : '--');
								$tpl->assign('color_dif', round($dif, 2) != 0 ? ($dif > 0 ? ' blue' : ' red') : '');
							}
						}
						else {
							$condiciones = array();

							$condiciones[] = 'num_cia = ' . $num_cia;

							if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
							{
								$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
							}

							$cuentas = $db->query("SELECT cuenta AS banco FROM saldos s WHERE " . implode(' AND ', $condiciones) . " ORDER BY banco");

							$tpl->newBlock('cuentas');

							foreach ($cuentas as $c)
							{
								$tpl->newBlock('cuenta');
								$tpl->assign('banco', $c['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($row['cuenta' . $c['banco']]) != '' ? $row['cuenta' . $c['banco']] : '[SIN CUENTA]');
							}
						}

						if (!isset($_REQUEST['banco']) || empty($_REQUEST['banco']))
						{
							$tpl->newBlock('th_banco');
						}
					}

					if ($tipo != $row['tipo'])
					{
						if ($tipo != NULL)
						{
							$tpl->newBlock('blank_row');

							if ($tipo == 1)
							{
								$tpl->newBlock('conciliados');
								$tpl->assign('conciliados.depositos', $depositos_con != 0 ? number_format($depositos_con, 2) : '--');
								$tpl->assign('conciliados.cargos', $cargos_con != 0 ? number_format($cargos_con, 2) : '--');
							}

							if ($tipo == 2)
							{
								$tpl->newBlock('totales');
								$tpl->assign('totales.depositos', $depositos_pen != 0 ? number_format($depositos_pen, 2) : '--');
								$tpl->assign('totales.cargos', $cargos_pen != 0 ? number_format($cargos_pen, 2) : '--');
							}

						}

						$tipo = $row['tipo'];
					}

					$tpl->newBlock('row');

					if (!isset($_REQUEST['banco']) || empty($_REQUEST['banco']))
					{
						$tpl->newBlock('td_banco');
						$tpl->assign('logo_banco', $row['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
						$tpl->assign('nombre_banco', $row['banco'] == 1 ? 'Banorte' : 'Santander');
						$tpl->gotoBlock('row');
					}

					// [17-Sep-2015] Para los códigos 1, 16, 44 y 99 obtener las facturas de venta y clientes asociadas al depósito
					if (in_array($row['cod_mov'], array(1, 16, 44, 99)))
					{
						$facs = $db->query("SELECT
							fe.num_cia,
							cc.nombre_corto AS emisor,
							fes.serie || fe.consecutivo AS folio,
							fe.nombre_cliente AS receptor,
							fe.rfc,
							fe.total
						FROM
							facturas_electronicas fe
						LEFT JOIN facturas_electronicas_series fes ON (
							fes.num_cia = fe.num_cia
							AND fes.tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN fes.folio_inicial
							AND fes.folio_final
						)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = fe.num_cia)
						WHERE
							(fe.num_cia = {$row['num_cia']} OR fe.num_cia IN (SELECT sucursal FROM porcentajes_puntos_calientes WHERE matriz = {$row['num_cia']}))
							AND fe.fecha = '{$row['fecha']}'
							AND fe.tipo IN (1, 2)
							AND fe.status = 1");

						if ($facs)
						{
							$facs_info = '<table class="info-table"><tr><th>Emisor</th><th>Folio</th><th>Receptor</th><th>R.F.C.</th><th>Importe</th></tr>';

							$total_facs = 0;

							foreach ($facs as $f)
							{
								$facs_info .= '<tr><td>' . $f['num_cia'] . ' ' . $f['emisor'] . '</td><td align="right">' . $f['folio'] . '</td><td>' . $f['receptor'] . '</td><td>' . $f['rfc'] . '</td><td align="right">' . number_format($f['total'], 2) . '</td></tr>';

								$total_facs += $f['total'];
							}

							$facs_info .= '<tr><th colspan="4" align="right">Total</th><th align="right">' . number_format($total_facs, 2) . '</th></tr></table>';
						}
					}

					$tpl->assign('id', $row['id']);
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('conciliado', $row['conciliado'] != '' ? $row['conciliado'] : '&nbsp;');
					$tpl->assign('info', utf8_encode($row['info']));
					$tpl->assign('deposito', $row['deposito'] != 0 ? number_format($row['deposito'], 2) : '&nbsp;');
					$tpl->assign('cargo', $row['cargo'] != 0 ? number_format($row['cargo'], 2) : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span class="' . ($row['cod_mov'] == 41 ? 'purple' : ($row['cod_mov'] == 41 ? 'orange' : 'green')) . '" info="' . $row['gasto'] . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('beneficiario', $row['beneficiario'] != '' ? utf8_encode($row['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', (in_array($row['cod_mov'], array(1, 16, 44, 99)) && $facs ? '<img src="/lecaroz/iconos/info.png" class="info-facs" data-tooltip="' . htmlentities($facs_info) . '"> ' : '') . ($row['concepto'] != '' ? utf8_encode($row['concepto']) : '&nbsp;'));
					$tpl->assign('codigo', utf8_encode($row['codigo']));

					$depositos += $row['deposito'] > 0 ? $row['deposito'] : 0;
					$cargos += $row['cargo'] > 0 ? abs($row['cargo']) : 0;

					$depositos_con += $row['tipo'] == 1 && $row['deposito'] > 0 ? $row['deposito'] : 0;
					$cargos_con += $row['tipo'] == 1 && $row['cargo'] > 0 ? abs($row['cargo']) : 0;

					$depositos_pen += $row['tipo'] == 2 && $row['deposito'] > 0 ? $row['deposito'] : 0;
					$cargos_pen += $row['tipo'] == 2 && $row['cargo'] > 0 ? abs($row['cargo']) : 0;
				}

				if ($num_cia != NULL)
				{
					if ($tipo != NULL)
					{
						if ($tipo == 1)
						{
							$tpl->newBlock('conciliados');
							$tpl->assign('conciliados.depositos', $depositos_con != 0 ? number_format($depositos_con, 2) : '--');
							$tpl->assign('conciliados.cargos', $cargos_con != 0 ? number_format($cargos_con, 2) : '--');
						}

						if ($tipo == 2)
						{
							$tpl->newBlock('totales');
							$tpl->assign('totales.depositos', $depositos_pen != 0 ? number_format($depositos_pen, 2) : '--');
							$tpl->assign('totales.cargos', $cargos_pen != 0 ? number_format($cargos_pen, 2) : '--');
						}
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/EstadoCuentaAgrupado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
