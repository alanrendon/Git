<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$_meses = array(
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
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

function ieps_produccion($num_cia, $anio, $mes)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

	$condiciones = array();

	$condiciones[] = "num_cia = {$num_cia}";

	$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

	$condiciones[] = "cod_turnos IN (1, 2, 3, 4, 8, 9)";

	$sql = "SELECT
		cod_turnos AS turno,
		SUM(imp_produccion) AS produccion
	FROM
		produccion p
	WHERE
		" . implode(' AND ', $condiciones) . "
	GROUP BY
		turno
	ORDER BY
		turno";

	$result = $db->query($sql);

	if ( ! $result)
	{
		return 0;
	}

	$turnos = array(
		1	=> 'FD',
		2	=> 'FN',
		3	=> 'BIZ',
		4	=> 'REP',
		8	=> 'PIC',
		9	=> 'GEL'
	);

	$total_turnos = array_fill_keys(array_keys($turnos), 0);
	$total_ieps = 0;

	$datos = array(
		'produccion_turnos'			=> array_fill_keys(array_keys($turnos), 0),
		'porcentaje_turnos'			=> array_fill_keys(array_keys($turnos), 0),
		'porcentaje_turnos_excento'	=> array_fill_keys(array_keys($turnos), 0),
		'total_produccion'			=> 0,
		'total_produccion_excento'	=> 0,
		'efectivo_turnos'			=> array_fill_keys(array_keys($turnos), 0),
		'efectivo_porcentajes'		=> array_fill_keys(array_keys($turnos), 0),
		'efectivo'					=> 0,
		'efectivo_pan_dulce'		=> 0,
		'efectivo_gravado_3'		=> 0,
		'efectivo_excento_3'		=> 0,
		'efectivo_gravado_4'		=> 0,
		'efectivo_excento_4'		=> 0,
		'faltante_pan'				=> 0,
		'total_general'				=> 0,
		'porcentaje'				=> 0,
		'ieps'						=> 0
	);

	foreach ($result as $row)
	{
		$datos['produccion_turnos'][$row['turno']] = $row['produccion'];

		$datos['total_produccion'] += $row['produccion'];

		if ( ! in_array($row['turno'], array(3, 4)))
		{
			$datos['total_produccion_excento'] += $row['produccion'];
		}
	}

	// Calcular porcentajes de produccion
	foreach ($datos['produccion_turnos'] as $turno => $produccion)
	{
		if ($produccion > 0)
		{
			$por = $produccion * 100 / $datos['total_produccion'];

			$datos['porcentaje_turnos'][$turno] = $por;

			if ( ! in_array($row['turno'], array(3, 4)))
			{
				$por = $produccion * 100 / $datos['total_produccion_excento'];

				$datos['porcentaje_turnos_excento'][$turno] = $por;
			}
		}
	}

	// Efectivos
	$condiciones = array();

	$condiciones[] = "((ec.num_cia = {$num_cia} AND ec.num_cia_sec IS NULL) OR ec.num_cia_sec <= {$num_cia})";

	$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

	$condiciones[] = 'ec.cod_mov IN (1, 16, 44, 99)';

	$sql = "SELECT
		SUM(ec.importe) AS efectivo
	FROM
		estado_cuenta ec
	WHERE
		" . implode(' AND ', $condiciones);

	$result = $db->query($sql);

	if ($result)
	{
		$datos['efectivo'] = $result[0]['efectivo'];

		foreach ($datos['porcentaje_turnos'] as $turno => $porcentaje)
		{
			$datos['efectivo_turnos'][$turno] = $result[0]['efectivo'] * $porcentaje / 100;

			if (in_array($turno, array(3, 4)))
			{
				$datos['efectivo_pan_dulce'] += $result[0]['efectivo'] * $porcentaje / 100;
			}
		}
	}

	// Porcentajes
	$condiciones = array();

	$condiciones[] = "num_cia = {$num_cia}";

	$condiciones[] = "anio = {$anio}";

	$condiciones[] = "mes = {$mes}";

	$sql = "SELECT
		porcentaje
	FROM
		porcentajes_ieps por
	WHERE
		" . implode(' AND ', $condiciones);

	$result = $db->query($sql);

	if ($result)
	{
		$datos['porcentaje'] = $result[0]['porcentaje'];

		if ($datos['porcentaje'] > 0)
		{
			if ($datos['efectivo_turnos'][3] > 0)
			{
				$datos['efectivo_gravado_3'] = $datos['efectivo_turnos'][3] * $datos['porcentaje'] / 100;
				$datos['efectivo_excento_3'] = $datos['efectivo_turnos'][3] - $datos['efectivo_gravado_3'];

				$datos['efectivo_turnos'][3] = $datos['efectivo_gravado_3'];
			}

			if ($datos['efectivo_turnos'][4] > 0)
			{
				$datos['efectivo_gravado_4'] = $datos['efectivo_turnos'][4] * $datos['porcentaje'] / 100;
				$datos['efectivo_excento_4'] = $datos['efectivo_turnos'][4] - $datos['efectivo_gravado_4'];

				$datos['efectivo_turnos'][4] = $datos['efectivo_gravado_4'];
			}

			$importe_gravado = $datos['efectivo_gravado_3'] + $datos['efectivo_gravado_4'];

			$importe_excento = $datos['efectivo_excento_3'] + $datos['efectivo_excento_4'];

			$datos['ieps'] = $importe_gravado * 0.08;

			$total_ieps += $datos['ieps'];

			// Distribuir importe excento entre los turnos exceptuando 3 y 4
			foreach ($datos['porcentaje_turnos_excento'] as $turno => $porcentaje)
			{
				if ( ! in_array($turno, array(3, 4)))
				{
					$importe_porcentaje = $importe_excento * $porcentaje / 100;

					$datos['efectivo_turnos'][$turno] += $importe_porcentaje;
				}
			}
		}
	}

	return $datos['ieps'];
}

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'fe.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');

			$condiciones[] = "fe.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			// $condiciones[] = "fe.tipo IN (1, 2)";

			$condiciones[] = "fe.tscan IS NULL";

			$condiciones[] = "fe.status = 1";

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
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'fe.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				fe.num_cia,
				cc.nombre_corto AS nombre_cia,
				EXTRACT(MONTH FROM fe.fecha) AS mes,
				SUM(fe.iva) AS iva,
				SUM(fe.ieps) AS ieps,
				SUM(fe.retencion_iva) AS ret_iva,
				SUM(fe.retencion_isr) AS ret_isr
			FROM
				facturas_electronicas fe
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				fe.num_cia,
				nombre_cia,
				mes
			ORDER BY
				fe.num_cia,
				mes";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/ReporteFacturasImpuestosAnualesListado.tpl');
			$tpl->prepare();

			if ($result)
			{
				$datos = array();

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						$num_cia = $rec['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'		=> utf8_encode($rec['nombre_cia']),
							'iva_compras'		=> array_fill(1, 12, 0),
							'iva_ventas'		=> array_fill(1, 12, 0),
							'iva_total'			=> array_fill(1, 12, 0),
							'ieps_compras'		=> array_fill(1, 12, 0),
							'ieps_ventas'		=> array_fill(1, 12, 0),
							'ieps_produccion'	=> array_fill(1, 12, 0),
							'ieps_total'		=> array_fill(1, 12, 0),
							'ret_iva_compras'	=> array_fill(1, 12, 0),
							'ret_iva_ventas'	=> array_fill(1, 12, 0),
							'ret_iva_total'		=> array_fill(1, 12, 0),
							'ret_isr_compras'	=> array_fill(1, 12, 0),
							'ret_isr_ventas'	=> array_fill(1, 12, 0),
							'ret_isr_total'		=> array_fill(1, 12, 0)
						);
					}

					$datos[$num_cia]['iva_ventas'][$rec['mes']] = floatval($rec['iva']);
					$datos[$num_cia]['ieps_ventas'][$rec['mes']] = floatval($rec['ieps']);
					$datos[$num_cia]['ret_iva_ventas'][$rec['mes']] = floatval($rec['ret_iva']);
					$datos[$num_cia]['ret_isr_ventas'][$rec['mes']] = floatval($rec['ret_isr']);
				}

				foreach ($datos as $num_cia => $cia)
				{
					foreach ($cia['ieps_produccion'] as $mes => $importe)
					{
						$datos[$num_cia]['ieps_produccion'][$mes] = ieps_produccion($num_cia, $_REQUEST['anio'], $mes);
					}
				}

				$condiciones = array();

				if ($_SESSION['iduser'] != 1)
				{
					$condiciones1[] = 'f.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');
					$condiciones2[] = 'f.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');
					$condiciones3[] = 'f.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');
				}

				$condiciones1[] = "ec.fecha_con BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones2[] = "ec.fecha_con BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones3[] = "ec.fecha_con BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
						else
						{
							$cias[] = $piece;
						}
					}

					if (count($cias) > 0)
					{
						$condiciones1[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
						$condiciones2[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
						$condiciones3[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones1[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones2[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones3[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
				{
					$condiciones1[] = "cc.rfc = '{$_REQUEST['rfc']}'";
					$condiciones2[] = "cc.rfc = '{$_REQUEST['rfc']}'";
					$condiciones3[] = "cc.rfc = '{$_REQUEST['rfc']}'";
				}

				$condiciones3[] = "(f.clave IS NULL OR f.clave = 0)";

				$condiciones1_string = implode(' AND ', $condiciones1);
				$condiciones2_string = implode(' AND ', $condiciones2);
				$condiciones3_string = implode(' AND ', $condiciones3);

				$sql = "SELECT
					num_cia,
					nombre_cia,
					mes,
					SUM(ieps) AS ieps,
					SUM(iva) AS iva,
					SUM(ret_iva) AS ret_iva,
					SUM(ret_isr) AS ret_isr
				FROM
					(
						SELECT
							f.num_cia,
							cc.nombre_corto AS nombre_cia,
							EXTRACT(MONTH FROM ec.fecha_con) AS mes,
							SUM(COALESCE((
								SELECT
									SUM(ieps)
								FROM
									entrada_mp
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
									AND regalado = FALSE
							), f.ieps, 0)) AS ieps,
							SUM(COALESCE((
								SELECT
									SUM(iva)
								FROM
									entrada_mp
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
									AND regalado = FALSE
							), f.iva, 0)) AS iva,
							SUM(f.retencion_iva) AS ret_iva,
							SUM(f.retencion_isr) AS ret_isr
						FROM
							facturas f
							LEFT JOIN facturas_pagadas fp ON (
								fp.num_proveedor = f.num_proveedor
								AND fp.num_fact = f.num_fact
								AND fp.fecha = f.fecha
							)
							LEFT JOIN estado_cuenta ec ON (
								ec.num_cia = fp.num_cia
								AND ec.cuenta = fp.cuenta
								AND ec.folio = fp.folio_cheque
								AND ec.fecha = fp.fecha_cheque
							)
							LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
						WHERE
							{$condiciones1_string}
						GROUP BY
							f.num_cia,
							nombre_cia,
							mes

						UNION

						SELECT
							f.num_cia,
							cc.nombre_corto AS nombre_cia,
							EXTRACT(MONTH FROM ec.fecha_con) AS mes,
							0 AS ieps,
							0 AS iva,
							0 AS ret_iva,
							0 AS ret_isr
						FROM
							total_fac_ros f
							LEFT JOIN facturas_pagadas fp ON (
								fp.num_proveedor = f.num_proveedor
								AND fp.num_fact = f.num_fac
								AND fp.fecha = f.fecha
							)
							LEFT JOIN estado_cuenta ec ON (
								ec.num_cia = fp.num_cia
								AND ec.cuenta = fp.cuenta
								AND ec.folio = fp.folio_cheque
								AND ec.fecha = fp.fecha_cheque
							)
							LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
						WHERE
							{$condiciones2_string}
						GROUP BY
							f.num_cia,
							nombre_cia,
							mes

						UNION

						SELECT
							f.num_cia,
							cc.nombre_corto AS nombre_cia,
							EXTRACT(MONTH FROM /*f.tscap::DATE*/ec.fecha_con) AS mes,
							0 AS ieps,
							SUM(factura_zap_iva(
								f.importe,
								f.faltantes,
								f.dif_precio,
								CASE
									WHEN f.dev > 0 THEN
										f.dev
									ELSE
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												devoluciones_zap
											WHERE
												num_proveedor = f.num_proveedor
												AND num_fact = f.num_fact
										), 0)
								END,
								f.pdesc1,
								f.pdesc2,
								f.pdesc3,
								f.pdesc4,
								f.desc1,
								f.desc2,
								f.desc3,
								f.desc4,
								f.iva,
								CASE
									WHEN f.pivaret != 0 AND COALESCE(f.ivaret, 0) = 0 THEN
										f.importe * ABS(f.pivaret) / 100
									ELSE
										ABS(f.ivaret)
								END,
								CASE
									WHEN f.pisr != 0 AND COALESCE(f.isr, 0) = 0 THEN
										f.importe * ABS(f.pisr) / 100
									ELSE
										ABS(f.isr)
								END,
								f.fletes,
								f.otros
							)) AS iva,
							SUM(factura_zap_ret_iva(
								f.importe,
								f.faltantes,
								f.dif_precio,
								CASE
									WHEN f.dev > 0 THEN
										f.dev
									ELSE
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												devoluciones_zap
											WHERE
												num_proveedor = f.num_proveedor
												AND num_fact = f.num_fact
										), 0)
								END,
								f.pdesc1,
								f.pdesc2,
								f.pdesc3,
								f.pdesc4,
								f.desc1,
								f.desc2,
								f.desc3,
								f.desc4,
								f.iva,
								CASE
									WHEN f.pivaret != 0 AND COALESCE(f.ivaret, 0) = 0 THEN
										f.importe * ABS(f.pivaret) / 100
									ELSE
										ABS(f.ivaret)
								END,
								CASE
									WHEN f.pisr != 0 AND COALESCE(f.isr, 0) = 0 THEN
										f.importe * ABS(f.pisr) / 100
									ELSE
										ABS(f.isr)
								END,
								f.fletes,
								f.otros
							)) AS ret_iva,
							SUM(factura_zap_ret_isr(
								f.importe,
								f.faltantes,
								f.dif_precio,
								CASE
									WHEN f.dev > 0 THEN
										f.dev
									ELSE
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												devoluciones_zap
											WHERE
												num_proveedor = f.num_proveedor
												AND num_fact = f.num_fact
										), 0)
								END,
								f.pdesc1,
								f.pdesc2,
								f.pdesc3,
								f.pdesc4,
								f.desc1,
								f.desc2,
								f.desc3,
								f.desc4,
								f.iva,
								CASE
									WHEN f.pivaret != 0 AND COALESCE(f.ivaret, 0) = 0 THEN
										f.importe * ABS(f.pivaret) / 100
									ELSE
										ABS(f.ivaret)
								END,
								CASE
									WHEN f.pisr != 0 AND COALESCE(f.isr, 0) = 0 THEN
										f.importe * ABS(f.pisr) / 100
									ELSE
										ABS(f.isr)
								END,
								f.fletes,
								f.otros
							)) AS ret_isr
						FROM
							facturas_zap f
							LEFT JOIN estado_cuenta ec ON (
								ec.num_cia = f.num_cia
								AND ec.cuenta = f.cuenta
								AND ec.folio = f.folio
							)
							LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
						WHERE
							{$condiciones3_string}
						GROUP BY
							f.num_cia,
							nombre_cia,
							mes
					) AS datos

				GROUP BY
					num_cia,
					nombre_cia,
					mes
				ORDER BY
					num_cia,
					mes";

				$result = $db->query($sql);

				if ($result)
				{
					$num_cia = NULL;

					foreach ($result as $rec)
					{
						if ($num_cia != $rec['num_cia'])
						{
							$num_cia = $rec['num_cia'];

							if ( ! isset($datos[$num_cia]))
							{
								$datos[$num_cia] = array(
									'nombre_cia'		=> utf8_encode($rec['nombre_cia']),
									'iva_compras'		=> array_fill(1, 12, 0),
									'iva_ventas'		=> array_fill(1, 12, 0),
									'iva_total'			=> array_fill(1, 12, 0),
									'ieps_compras'		=> array_fill(1, 12, 0),
									'ieps_ventas'		=> array_fill(1, 12, 0),
									'ieps_total'		=> array_fill(1, 12, 0),
									'ret_iva_compras'	=> array_fill(1, 12, 0),
									'ret_iva_ventas'	=> array_fill(1, 12, 0),
									'ret_iva_total'		=> array_fill(1, 12, 0),
									'ret_isr_compras'	=> array_fill(1, 12, 0),
									'ret_isr_ventas'	=> array_fill(1, 12, 0),
									'ret_isr_total'		=> array_fill(1, 12, 0)
								);
							}
						}

						$datos[$num_cia]['iva_compras'][$rec['mes']] = floatval($rec['iva']);
						$datos[$num_cia]['ieps_compras'][$rec['mes']] = floatval($rec['ieps']);
						$datos[$num_cia]['ret_iva_compras'][$rec['mes']] = floatval($rec['ret_iva']);
						$datos[$num_cia]['ret_isr_compras'][$rec['mes']] = floatval($rec['ret_isr']);
					}
				}

				$tpl->newBlock('reporte');
				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $num_cia => $cia)
				{
					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $cia['nombre_cia']);

					foreach (range(1, 12) as $mes)
					{
						$tpl->newBlock('mes');
						$tpl->assign('mes', $_meses[$mes]);
					}

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.V.A. Compras');
					foreach ($cia['iva_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['iva_compras']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['iva_compras'])) != 0 ? number_format(array_sum($cia['iva_compras']) / count(array_filter($cia['iva_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.V.A. Ventas');
					foreach ($cia['iva_ventas'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['iva_ventas']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['iva_ventas'])) != 0 ? number_format(array_sum($cia['iva_ventas']) / count(array_filter($cia['iva_ventas'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.V.A. Total');
					foreach ($cia['iva_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', ($importe - $cia['iva_ventas'][$mes]) != 0 ? '<strong>' . number_format(($importe - $cia['iva_ventas'][$mes]), 2) . '</strong>' : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['iva_compras']) - array_sum($cia['iva_ventas']), 2));
					// $tpl->assign('concepto.promedio', count(array_filter($cia['iva_compras'])) != 0 ? number_format(array_sum($cia['iva_compras']) / count(array_filter($cia['iva_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.E.P.S. Compras');
					foreach ($cia['ieps_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ieps_compras']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ieps_compras'])) != 0 ? number_format(array_sum($cia['ieps_compras']) / count(array_filter($cia['ieps_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.E.P.S. Ventas');
					foreach ($cia['ieps_ventas'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ieps_ventas']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ieps_ventas'])) != 0 ? number_format(array_sum($cia['ieps_ventas']) / count(array_filter($cia['ieps_ventas'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.E.P.S. Producci&oacute;n');
					foreach ($cia['ieps_produccion'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ieps_produccion']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ieps_produccion'])) != 0 ? number_format(array_sum($cia['ieps_produccion']) / count(array_filter($cia['ieps_produccion'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'I.E.P.S. Total');
					foreach ($cia['ieps_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', ($importe - $cia['ieps_ventas'][$mes] - $cia['ieps_produccion'][$mes]) != 0 ? '<strong>' . number_format(($importe - $cia['ieps_ventas'][$mes] - $cia['ieps_produccion'][$mes]), 2) . '</strong>' : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ieps_compras']) - array_sum($cia['ieps_ventas']), 2));
					// $tpl->assign('concepto.promedio', count(array_filter($cia['ieps_compras'])) != 0 ? number_format(array_sum($cia['ieps_compras']) / count(array_filter($cia['ieps_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.V.A. Compras');
					foreach ($cia['ret_iva_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_iva_compras']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ret_iva_compras'])) != 0 ? number_format(array_sum($cia['ret_iva_compras']) / count(array_filter($cia['ret_iva_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.V.A. Ventas');
					foreach ($cia['ret_iva_ventas'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_iva_ventas']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ret_iva_ventas'])) != 0 ? number_format(array_sum($cia['ret_iva_ventas']) / count(array_filter($cia['ret_iva_ventas'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.V.A. Total');
					foreach ($cia['ret_iva_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', ($importe - $cia['ret_iva_ventas'][$mes]) != 0 ? '<strong>' . number_format(($importe - $cia['ret_iva_ventas'][$mes]), 2) . '</strong>' : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_iva_compras']) - array_sum($cia['ret_iva_ventas']), 2));
					// $tpl->assign('concepto.promedio', count(array_filter($cia['ret_iva_compras'])) != 0 ? number_format(array_sum($cia['ret_iva_compras']) / count(array_filter($cia['ret_iva_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.S.R. Compras');
					foreach ($cia['ret_isr_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_isr_compras']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ret_isr_compras'])) != 0 ? number_format(array_sum($cia['ret_isr_compras']) / count(array_filter($cia['ret_isr_compras'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.S.R. Ventas');
					foreach ($cia['ret_isr_ventas'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_isr_ventas']), 2));
					$tpl->assign('concepto.promedio', count(array_filter($cia['ret_isr_ventas'])) != 0 ? number_format(array_sum($cia['ret_isr_ventas']) / count(array_filter($cia['ret_isr_ventas'])), 2) : '');

					$tpl->newBlock('concepto');
					$tpl->assign('concepto', 'Retenci&oacute;n I.S.R. Total');
					foreach ($cia['ret_isr_compras'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', ($importe - $cia['ret_isr_ventas'][$mes]) != 0 ? '<strong>' . number_format(($importe - $cia['ret_isr_ventas'][$mes]), 2) . '</strong>' : '&nbsp;');
					}
					$tpl->assign('concepto.total', number_format(array_sum($cia['ret_isr_compras']) - array_sum($cia['ret_isr_ventas']), 2));
					// $tpl->assign('concepto.promedio', count(array_filter($cia['ret_isr_compras'])) != 0 ? number_format(array_sum($cia['ret_isr_compras']) / count(array_filter($cia['ret_isr_compras'])), 2) : '');
				}
			}

			$tpl->printToScreen();

			break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteFacturasImpuestosAnualesInicio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

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

if ($admins)
{
	foreach ($admins as $a)
	{
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
