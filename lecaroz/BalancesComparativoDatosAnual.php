<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
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

$conceptos = array(
	'venta_puerta'			=> "Venta en Puerta",
	'bases'					=> "Bases",
	'barredura'				=> "Barredura",
	'pastillaje'			=> "Pastillaje",
	'abono_emp'				=> "Abono Empleados",
	'otros'					=> "Otros",
	'total_otros'			=> "Total Otros",
	'abono_reparto'			=> "Abono Reparto",
	'errores'				=> "Errores",
	'ventas_netas'			=> "Ventas Netas",
	'inv_ant'				=> "Inventario Anterior",
	'compras'				=> "Compras",
	'mercancias'			=> "Mercancias",
	'inv_act'				=> "Inventario Actual",
	'mat_prima_utilizada'	=> "Mat. Prima Utilizada",
	'mano_obra'				=> "Mano de Obra",
	'panaderos'				=> "Panaderos",
	'gastos_fab'			=> "Gastos de Fabricación",
	'costo_produccion'		=> "Costo de Producción",
	'utilidad_bruta'		=> "Utilidad Bruta",
	'pan_comprado'			=> "Pan Comprado",
	'gastos_generales'		=> "Gastos Generales",
	'gastos_caja'			=> "Gastos por Caja",
	'reserva_aguinaldos'	=> "Reserva para Aguinaldos",
	'gastos_otras_cias'		=> "Gastos Pagados por Otras Cias.",
	'total_gastos'			=> "Total de Gastos",
	'ingresos_ext'			=> "Ingresos Extraordinarios",
	'utilidad_neta'			=> "Utilidad del Mes",
	'produccion_total'		=> "Producción Total",
	'faltante_pan'			=> "Faltante de Pan",
	'rezago_ini'			=> "Rezago Inicial",
	'rezago_fin'			=> "Rezago Final",
	'efectivo'				=> "Efectivo",
	'utilidad_pro'			=> "Utilidad Neta / Producción",
	'utilidad_bruta_pro'	=> "Utilidad Bruta / Producción",
	'mp_pro'				=> "Materia prima / Producción",
	'clientes'				=> "Clientes",
	'utilidad_ventas'		=> "Utilidad neta / Ventas",
	'utilidad_mat_prima'	=> "Utilidad neta / Materia prima",
	'mat_prima_ventas'		=> "Materia prima / Ventas",
	'pollos'				=> "Pollos",
	'pescuezos'				=> "Pescuezos",
	'precio_pollo'			=> "Precio por kilo",
	'kilos_pollo'			=> "Kilos de pollo comprado"
);

function consulta_listado($params)
{
	global $db;

	$condiciones = array();

	$condiciones[] = "bal.anio IN (" . implode(', ', array_filter($params['anio'])) . ")";

	$condiciones[] = "bal.mes <= {$params['mes']}";

	$condiciones[] = "bal.num_cia <= 800";

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
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
			$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones[] = "cc.idadministrador = {$params['admin']}";
	}

	$condiciones_string = implode(' AND ', $condiciones);

	if ($params['concepto'] == 'venta_puerta')
	{
		$campo_pan = 'bal.venta_puerta' . (isset($params['descontar_errores']) ? ' - bal.errores' : '');

		$campo_ros = 'bal.venta';
	}
	else if ($params['concepto'] == 'total_otros')
	{
		$campo_pan = $params['concepto'];

		$campo_ros = 'bal.otros';
	}
	else if ($params['concepto'] == 'utilidad_neta')
	{
		$campo_pan = "bal.utilidad_neta + COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN scc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta sec
				LEFT JOIN catalogo_companias scc
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0)" . ( ! isset($params['incluir_ingresos_ext']) ? ' - bal.ingresos_ext' : '') . ( isset($_REQUEST['sumar_importes']) ? ' + importes.cantidad' : '');

		$campo_ros = "bal.utilidad_neta - COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta sec
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0) - (CASE
			WHEN bal.anio < 2016 THEN
				COALESCE((
					SELECT
						ROUND(SUM(importe)::NUMERIC - 25000, 2) * 0.03 AS importe
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
					AND fecha_con BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					AND cod_mov IN (1, 7, 13, 16, 79)
				), 0)
			ELSE
				0
		END)" . ( ! isset($params['incluir_ingresos_ext']) ? ' - bal.ingresos_ext' : '') . ( isset($_REQUEST['sumar_importes']) ? ' + importes.cantidad' : '');
	}
	else if ($params['concepto'] == 'utilidad_bruta_pro')
	{
		$campo_pan = "CASE WHEN produccion_total != 0 THEN utilidad_bruta / produccion_total ELSE 0 END";

		$campo_ros = "0";
	}
	else if ($params['concepto'] == 'utilidad_ventas')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN ventas_netas != 0 THEN utilidad_neta / ventas_netas ELSE 0 END";
	}
	else if ($params['concepto'] == 'utilidad_mat_prima')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN mat_prima_utilizada != 0 THEN utilidad_neta / mat_prima_utilizada ELSE 0 END";
	}
	else if ($params['concepto'] == 'mat_prima_ventas')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN ventas_netas > 0 THEN mat_prima_utilizada / ventas_netas ELSE 0 END";
	}
	else if ($params['concepto'] == 'kilos_pollo')
	{
		$campo_pan = "0";

		$campo_ros = "COALESCE((
			SELECT
				SUM(kilos)
			FROM
				fact_rosticeria
			WHERE
				num_cia = bal.num_cia
				AND fecha_mov BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codmp IN (160, 600, 573)
		), 0)";
	}
	else if (in_array($params['concepto'], array(
		'bases',
		'barredura',
		'pastillaje',
		'abono_emp',
		'total_otros',
		'abono_reparto',
		'errores',
		'mano_obra',
		'panaderos',
		'pan_comprado',
		'produccion_total',
		'faltante_pan',
		'rezago_ini',
		'rezago_fin',
		'utilidad_pro',
		'mp_pro'
	)))
	{
		$campo_pan = $params['concepto'];

		$campo_ros = "0";
	}
	else if (in_array($params['concepto'], array(
		'pollos',
		'pescuezos',
		'precio_pollo'
	)))
	{
		$campo_pan = "0";

		$campo_ros = $params['concepto'];
	}
	else
	{
		$campo_pan = $params['concepto'];

		$campo_ros = $params['concepto'];
	}

	$result = $db->query("SELECT
		cc.idadministrador AS admin,
		ca.nombre_administrador AS nombre_admin,
		bal.num_cia,
		cc.nombre_corto AS nombre,
		bal.anio,
		bal.mes,
		{$campo_pan} AS importe
	FROM
		balances_pan bal
		LEFT JOIN historico his USING (num_cia, anio, mes)
		LEFT JOIN desc_utilidad_mes importes USING (num_cia)
		LEFT JOIN catalogo_companias cc USING (num_cia)
		LEFT JOIN catalogo_administradores ca USING (idadministrador)
	WHERE
		{$condiciones_string}

	UNION

	SELECT
		cc.idadministrador AS admin,
		ca.nombre_administrador AS nombre_admin,
		bal.num_cia,
		cc.nombre_corto AS nombre,
		bal.anio,
		bal.mes,
		{$campo_ros}
	FROM
		balances_ros bal
		LEFT JOIN historico his USING (num_cia, anio, mes)
		LEFT JOIN desc_utilidad_mes importes USING (num_cia)
		LEFT JOIN catalogo_companias cc USING (num_cia)
		LEFT JOIN catalogo_administradores ca USING (idadministrador)
	WHERE
		{$condiciones_string}

	ORDER BY
		" . (isset($params['admin']) && $params['admin'] < 0 ? 'admin,' : '') . "
		num_cia,
		anio DESC,
		mes");

	if ($result)
	{
		$datos = array();

		$num_cia = NULL;
		$admin = NULL;

		$index = 0;

		$totales = array();

		$totales_admin = array();

		$anios = array_filter($_REQUEST['anio']);

		sort($anios);

		$anios = array_reverse($anios);

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				if ($num_cia != NULL)
				{
					if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
					{
						$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
						$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));
					}
					// else
					// {
					// 	unset($datos[$index]['series'][$anio]);
					// }

					if (count(array_filter($datos[$index]['series'][$anios[0]]['importes'])) > 0)
					{
						$index++;
					}
					else
					{
						unset($datos[$index]);
					}
				}

				$num_cia = $row['num_cia'];
				$admin = $row['admin'];

				$datos[$index] = array(
					'admin'			=> $row['admin'],
					'nombre_admin'	=> $row['nombre_admin'],
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre'],
					'series'		=> array()
				);

				foreach ($anios as $anio)
				{
					$datos[$index]['series'][$anio] = array(
						'anio'		=> intval($anio),
						'importes'	=> array_fill(1, 12, 0),
						'total'		=> 0,
						'promedio'	=> 0
					);
				}

				$anio = NULL;
			}

			if ($anio != $row['anio'])
			{
				if ($anio != NULL)
				{
					if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
					{
						$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
						$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));

						// $anio++;
					}
					// else
					// {
					// 	unset($datos[$index]['series'][$anio]);
					// }
				}

				$anio = $row['anio'];

				if ( ! isset($totales[$anio]))
				{
					$totales[$anio] = array_fill(1, 12, 0);
				}

				if ( ! isset($totales_admin[$row['admin']][$anio]))
				{
					$totales_admin[$row['admin']][$anio] = array_fill(1, 12, 0);
				}
			}

			$datos[$index]['series'][$anio]['importes'][$row['mes']] = floatval($row['importe']);

			$totales[$anio][$row['mes']] += floatval($row['importe']);
			$totales_admin[$row['admin']][$anio][$row['mes']] += floatval($row['importe']);
		}

		if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
		{
			$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
			$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));
		}
		// else
		// {
		// 	unset($datos[$index]['series'][$anio]);
		// }

		// if (count($datos[$index]['series']) == 0)
		if (count(array_filter($datos[$index]['series'][$anios[0]]['importes'])) == 0)
		{
			unset($datos[$index]);
		}

		return array(
			'datos' 		=> $datos,
			'totales'		=> $totales,
			'totales_admin'	=> $totales_admin
		);
	}

	return NULL;
}

function consulta_promedios($params)
{
	global $db;

	$condiciones = array();

	$condiciones[] = "bal.anio IN ({$params['anio'][0]} - 1, {$params['anio'][0]})";

	$condiciones[] = "bal.mes <= {$params['mes']}";

	$condiciones[] = "bal.num_cia <= 800";

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
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
			$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones[] = "cc.idadministrador = {$params['admin']}";
	}

	$condiciones_string = implode(' AND ', $condiciones);

	if ($params['concepto'] == 'venta_puerta')
	{
		$campo_pan = 'bal.venta_puerta' . (isset($params['descontar_errores']) ? ' - bal.errores' : '');

		$campo_ros = 'bal.venta';
	}
	else if ($params['concepto'] == 'total_otros')
	{
		$campo_pan = $params['concepto'];

		$campo_ros = 'bal.otros';
	}
	else if ($params['concepto'] == 'utilidad_neta')
	{
		$campo_pan = "bal.utilidad_neta + COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN scc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta sec
				LEFT JOIN catalogo_companias scc
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0)" . ( ! isset($params['incluir_ingresos_ext']) ? ' - bal.ingresos_ext' : '') . ( isset($_REQUEST['sumar_importes']) ? ' + importes.cantidad' : '');

		$campo_ros = "bal.utilidad_neta - COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta sec
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0) - (CASE
			WHEN bal.anio < 2016 THEN
				COALESCE((
					SELECT
						ROUND(SUM(importe)::NUMERIC - 25000, 2) * 0.03 AS importe
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
					AND fecha_con BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					AND cod_mov IN (1, 7, 13, 16, 79)
				), 0)
			ELSE
				0
		END)" . ( ! isset($params['incluir_ingresos_ext']) ? ' - bal.ingresos_ext' : '') . ( isset($_REQUEST['sumar_importes']) ? ' + importes.cantidad' : '');
	}
	else if ($params['concepto'] == 'utilidad_bruta_pro')
	{
		$campo_pan = "CASE WHEN produccion_total != 0 THEN utilidad_bruta / produccion_total ELSE 0 END";

		$campo_ros = "0";
	}
	else if ($params['concepto'] == 'utilidad_ventas')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN ventas_netas != 0 THEN utilidad_neta / ventas_netas ELSE 0 END";
	}
	else if ($params['concepto'] == 'utilidad_mat_prima')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN mat_prima_utilizada != 0 THEN utilidad_neta / mat_prima_utilizada ELSE 0 END";
	}
	else if ($params['concepto'] == 'mat_prima_ventas')
	{
		$campo_pan = "0";

		$campo_ros = "CASE WHEN ventas_netas > 0 THEN mat_prima_utilizada / ventas_netas ELSE 0 END";
	}
	else if ($params['concepto'] == 'kilos_pollo')
	{
		$campo_pan = "0";

		$campo_ros = "COALESCE((
			SELECT
				SUM(kilos)
			FROM
				fact_rosticeria
			WHERE
				num_cia = bal.num_cia
				AND fecha_mov BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codmp IN (160, 600, 573)
		), 0)";
	}
	else if (in_array($params['concepto'], array(
		'bases',
		'barredura',
		'pastillaje',
		'abono_emp',
		'total_otros',
		'abono_reparto',
		'errores',
		'mano_obra',
		'panaderos',
		'pan_comprado',
		'produccion_total',
		'faltante_pan',
		'rezago_ini',
		'rezago_fin',
		'utilidad_pro',
		'mp_pro'
	)))
	{
		$campo_pan = $params['concepto'];

		$campo_ros = "0";
	}
	else if (in_array($params['concepto'], array(
		'pollos',
		'pescuezos',
		'precio_pollo'
	)))
	{
		$campo_pan = "0";

		$campo_ros = $params['concepto'];
	}
	else
	{
		$campo_pan = $params['concepto'];

		$campo_ros = $params['concepto'];
	}

	$result = $db->query("SELECT
		cc.idadministrador AS admin,
		ca.nombre_administrador AS nombre_admin,
		bal.num_cia,
		cc.nombre_corto AS nombre,
		cc.tipo_cia,
		bal.anio,
		bal.mes,
		{$campo_pan} AS importe
	FROM
		balances_pan bal
		LEFT JOIN historico his USING (num_cia, anio, mes)
		LEFT JOIN desc_utilidad_mes importes USING (num_cia)
		LEFT JOIN catalogo_companias cc USING (num_cia)
		LEFT JOIN catalogo_administradores ca USING (idadministrador)
	WHERE
		{$condiciones_string}

	UNION

	SELECT
		cc.idadministrador AS admin,
		ca.nombre_administrador AS nombre_admin,
		bal.num_cia,
		cc.nombre_corto AS nombre,
		cc.tipo_cia,
		bal.anio,
		bal.mes,
		{$campo_ros}
	FROM
		balances_ros bal
		LEFT JOIN historico his USING (num_cia, anio, mes)
		LEFT JOIN desc_utilidad_mes importes USING (num_cia)
		LEFT JOIN catalogo_companias cc USING (num_cia)
		LEFT JOIN catalogo_administradores ca USING (idadministrador)
	WHERE
		{$condiciones_string}

	ORDER BY
		" . (isset($params['admin']) && $params['admin'] < 0 ? 'admin,' : '') . "
		num_cia,
		anio,
		mes");

	if ($result)
	{
		$datos = array();
		$datos_no_incremento = array();

		$num_cia = NULL;
		$admin = NULL;

		$index = 0;

		$totales = array(
			'total_ant'				=> 0,
			'promedio_ant'			=> 0,
			'total'					=> 0,
			'promedio'				=> 0,
			'diferencia_total'		=> 0,
			'diferencia_promedio'	=> 0
		);

		$totales_no_incremento = array(
			'total_ant'				=> 0,
			'promedio_ant'			=> 0,
			'total'					=> 0,
			'promedio'				=> 0,
			'diferencia_total'		=> 0,
			'diferencia_promedio'	=> 0
		);

		$totales_admin = array();
		$totales_admin_no_incremento = array();

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				if ($num_cia != NULL)
				{
					if (count(array_filter($datos[$index]['importes_ant'])) > 0 || count(array_filter($datos[$index]['importes'])) > 0)
					{
						$datos[$index]['total_ant'] = array_sum($datos[$index]['importes_ant']);
						$datos[$index]['promedio_ant'] = count(array_filter($datos[$index]['importes_ant'])) > 0 ? $datos[$index]['total_ant'] / count(array_filter($datos[$index]['importes_ant'])) : 0;

						$datos[$index]['total'] = array_sum($datos[$index]['importes']);
						$datos[$index]['promedio'] = count(array_filter($datos[$index]['importes'])) > 0 ? $datos[$index]['total'] / count(array_filter($datos[$index]['importes'])) : 0;

						$datos[$index]['diferencia_total'] = $datos[$index]['total'] - $datos[$index]['total_ant'];
						$datos[$index]['incremento_total'] = $datos[$index]['total'] != 0 && $datos[$index]['total_ant'] != 0 ? $datos[$index]['diferencia_total'] / $datos[$index]['total_ant'] * 100 : 0;

						$datos[$index]['diferencia_promedio'] = $datos[$index]['promedio'] - $datos[$index]['promedio_ant'];
						$datos[$index]['incremento_promedio'] = $datos[$index]['promedio'] != 0 && $datos[$index]['promedio_ant'] != 0 ? $datos[$index]['diferencia_promedio'] / $datos[$index]['promedio_ant'] * 100 : 0;

						if (($_REQUEST['tipo_reporte'] == 'promedios' ? $datos[$index]['incremento_promedio'] : $datos[$index]['incremento_total']) == 0)
						{
							$datos_no_incremento[] = $datos[$index];

							$totales_no_incremento['total_ant'] += $datos[$index]['total_ant'];
							$totales_no_incremento['promedio_ant'] += $datos[$index]['promedio_ant'];
							$totales_no_incremento['total'] += $datos[$index]['total'];
							$totales_no_incremento['promedio'] += $datos[$index]['promedio'];
							$totales_no_incremento['diferencia_total'] += $datos[$index]['diferencia_total'];
							$totales_no_incremento['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

							$totales_admin_no_incremento[$admin]['total_ant'] += $datos[$index]['total_ant'];
							$totales_admin_no_incremento[$admin]['promedio_ant'] += $datos[$index]['promedio_ant'];
							$totales_admin_no_incremento[$admin]['total'] += $datos[$index]['total'];
							$totales_admin_no_incremento[$admin]['promedio'] += $datos[$index]['promedio'];
							$totales_admin_no_incremento[$admin]['diferencia_total'] += $datos[$index]['diferencia_total'];
							$totales_admin_no_incremento[$admin]['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

							unset($datos[$index]);
						}
						else
						{
							$totales['total_ant'] += $datos[$index]['total_ant'];
							$totales['promedio_ant'] += $datos[$index]['promedio_ant'];
							$totales['total'] += $datos[$index]['total'];
							$totales['promedio'] += $datos[$index]['promedio'];
							$totales['diferencia_total'] += $datos[$index]['diferencia_total'];
							$totales['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

							$totales_admin[$admin]['total_ant'] += $datos[$index]['total_ant'];
							$totales_admin[$admin]['promedio_ant'] += $datos[$index]['promedio_ant'];
							$totales_admin[$admin]['total'] += $datos[$index]['total'];
							$totales_admin[$admin]['promedio'] += $datos[$index]['promedio'];
							$totales_admin[$admin]['diferencia_total'] += $datos[$index]['diferencia_total'];
							$totales_admin[$admin]['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

							$index++;
						}
					}
				}

				$num_cia = $row['num_cia'];
				$admin = $row['admin'];

				$datos[$index] = array(
					'admin'					=> $row['admin'],
					'num_cia'				=> $row['num_cia'],
					'nombre_cia'			=> $row['nombre'],
					'tipo_cia'				=> $row['tipo_cia'],
					'importes_ant'			=> array_fill(1, 12, 0),
					'total_ant'				=> 0,
					'promedio_ant'			=> 0,
					'importes'				=> array_fill(1, 12, 0),
					'total'					=> 0,
					'promedio'				=> 0,
					'diferencia_total'		=> 0,
					'incremento_total'		=> 0,
					'diferencia_promedio'	=> 0,
					'incremento_promedio'	=> 0
				);

				if ( ! isset($totales_admin[$admin]))
				{
					$totales_admin[$admin] = array(
						'total_ant'				=> 0,
						'promedio_ant'			=> 0,
						'total'					=> 0,
						'promedio'				=> 0,
						'diferencia_total'		=> 0,
						'diferencia_promedio'	=> 0
					);

					$totales_admin_no_incremento[$admin] = array(
						'total_ant'				=> 0,
						'promedio_ant'			=> 0,
						'total'					=> 0,
						'promedio'				=> 0,
						'diferencia_total'		=> 0,
						'diferencia_promedio'	=> 0
					);
				}
			}

			if ($row['anio'] < $_REQUEST['anio'][0])
			{
				$datos[$index]['importes_ant'][$row['mes']] = floatval($row['importe']);
			}
			else
			{
				$datos[$index]['importes'][$row['mes']] = floatval($row['importe']);
			}
		}

		if ($num_cia != NULL)
		{
			if (count(array_filter($datos[$index]['importes_ant'])) > 0 || count(array_filter($datos[$index]['importes'])) > 0)
			{
				$datos[$index]['total_ant'] = array_sum($datos[$index]['importes_ant']);
					$datos[$index]['promedio_ant'] = count(array_filter($datos[$index]['importes_ant'])) > 0 ? $datos[$index]['total_ant'] / count(array_filter($datos[$index]['importes_ant'])) : 0;

					$datos[$index]['total'] = array_sum($datos[$index]['importes']);
					$datos[$index]['promedio'] = count(array_filter($datos[$index]['importes'])) > 0 ? $datos[$index]['total'] / count(array_filter($datos[$index]['importes'])) : 0;

					$datos[$index]['diferencia_total'] = $datos[$index]['total'] - $datos[$index]['total_ant'];
					$datos[$index]['incremento_total'] = $datos[$index]['total'] != 0 && $datos[$index]['total_ant'] != 0 ? $datos[$index]['diferencia_total'] / $datos[$index]['total_ant'] * 100 : 0;

					$datos[$index]['diferencia_promedio'] = $datos[$index]['promedio'] - $datos[$index]['promedio_ant'];
					$datos[$index]['incremento_promedio'] = $datos[$index]['promedio'] != 0 && $datos[$index]['promedio_ant'] != 0 ? $datos[$index]['diferencia_promedio'] / $datos[$index]['promedio_ant'] * 100 : 0;

					if (($_REQUEST['tipo_reporte'] == 'promedios' ? $datos[$index]['incremento_promedio'] : $datos[$index]['incremento_total']) == 0)
					{
						$datos_no_incremento[] = $datos[$index];

						$totales_no_incremento['total_ant'] += $datos[$index]['total_ant'];
						$totales_no_incremento['promedio_ant'] += $datos[$index]['promedio_ant'];
						$totales_no_incremento['total'] += $datos[$index]['total'];
						$totales_no_incremento['promedio'] += $datos[$index]['promedio'];
						$totales_no_incremento['diferencia_total'] += $datos[$index]['diferencia_total'];
						$totales_no_incremento['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

						$totales_admin_no_incremento[$admin]['total_ant'] += $datos[$index]['total_ant'];
						$totales_admin_no_incremento[$admin]['promedio_ant'] += $datos[$index]['promedio_ant'];
						$totales_admin_no_incremento[$admin]['total'] += $datos[$index]['total'];
						$totales_admin_no_incremento[$admin]['promedio'] += $datos[$index]['promedio'];
						$totales_admin_no_incremento[$admin]['diferencia_total'] += $datos[$index]['diferencia_total'];
						$totales_admin_no_incremento[$admin]['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

						unset($datos[$index]);
					}
					else
					{
						$totales['total_ant'] += $datos[$index]['total_ant'];
						$totales['promedio_ant'] += $datos[$index]['promedio_ant'];
						$totales['total'] += $datos[$index]['total'];
						$totales['promedio'] += $datos[$index]['promedio'];
						$totales['diferencia_total'] += $datos[$index]['diferencia_total'];
						$totales['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

						$totales_admin[$admin]['total_ant'] += $datos[$index]['total_ant'];
						$totales_admin[$admin]['promedio_ant'] += $datos[$index]['promedio_ant'];
						$totales_admin[$admin]['total'] += $datos[$index]['total'];
						$totales_admin[$admin]['promedio'] += $datos[$index]['promedio'];
						$totales_admin[$admin]['diferencia_total'] += $datos[$index]['diferencia_total'];
						$totales_admin[$admin]['diferencia_promedio'] += $datos[$index]['diferencia_promedio'];

						$index++;
					}

				$index++;
			}
		}

		function cmp($a, $b)
		{
			if ($_REQUEST['admin'] == -1)
			{
				if ($a['admin'] == $b['admin'])
				{
					if ($a['tipo_cia'] == $b['tipo_cia'])
					{
						if (($_REQUEST['tipo_reporte'] == 'promedios' ? $a['incremento_promedio'] : $a['incremento_total']) == ($_REQUEST['tipo_reporte'] == 'promedios' ? $b['incremento_promedio'] : $b['incremento_total']))
						{
							return 0;
						}
						else
						{
							return ($_REQUEST['tipo_reporte'] == 'promedios' ? $a['incremento_promedio'] : $a['incremento_total']) < ($_REQUEST['tipo_reporte'] == 'promedios' ? $b['incremento_promedio'] : $b['incremento_total']) ? 1 : -1;
						}
					}
					else
					{
						return $a['tipo_cia'] < $b['tipo_cia'] ? -1 : 1;
					}
				}
				else
				{
					return $a['admin'] < $b['admin'] ? -1 : 1;
				}
			}
			else
			{
				if ($a['tipo_cia'] == $b['tipo_cia'])
				{
					if (($_REQUEST['tipo_reporte'] == 'promedios' ? $a['incremento_promedio'] : $a['incremento_total']) == ($_REQUEST['tipo_reporte'] == 'promedios' ? $b['incremento_promedio'] : $b['incremento_total']))
					{
						return 0;
					}
					else
						return ($_REQUEST['tipo_reporte'] == 'promedios' ? $a['incremento_promedio'] : $a['incremento_total']) < ($_REQUEST['tipo_reporte'] == 'promedios' ? $b['incremento_promedio'] : $b['incremento_total']) ? 1 : -1;
					}
				else
				{
					return $a['tipo_cia'] < $b['tipo_cia'] ? -1 : 1;
				}
			}
		}

		usort($datos, 'cmp');
		usort($datos_no_incremento, 'cmp');

		return array(
			'datos'							=> $datos,
			'datos_no_incremento'			=> $datos_no_incremento,
			'totales'						=> $totales,
			'totales_no_incremento'			=> $totales_no_incremento,
			'totales_admin'					=> $totales_admin,
			'totales_admin_no_incremento'	=> $totales_admin_no_incremento
		);
	}

	return NULL;
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/BalancesComparativoDatosAnualInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

			$admins = $db->query("SELECT
				idadministrador AS value,
				nombre_administrador AS text
			FROM
				catalogo_administradores
			ORDER BY
				text");

			if ($admins)
			{
				foreach ($admins as $a)
				{
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			$meses = $db->query("SELECT
				mes AS value,
				nombre AS text
			FROM
				meses
			ORDER BY
				mes");

			if ($meses)
			{
				foreach ($meses as $m)
				{
					$tpl->newBlock('mes');

					$tpl->assign('value', $m['value']);
					$tpl->assign('text', utf8_encode($m['text']));

					if ($m['value'] == date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))))
					{
						$tpl->assign('selected', ' selected=""');
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consulta_listado':
			if ($result = consulta_listado($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$totales_admin = $result['totales_admin'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				$tpl = new TemplatePower('plantillas/bal/BalancesComparativoDatosAnualConsultaListado.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio'][0]);

				$tpl->assign('concepto', $conceptos[$_REQUEST['concepto']]);

				$tpl->assign('numcols', $_REQUEST['mes'] + 3);

				$meses = $db->query("SELECT
					mes,
					abreviatura
				FROM
					meses
				ORDER BY
					mes");

				if ($meses)
				{
					foreach ($meses as $m)
					{
						if ($m['mes'] > $_REQUEST['mes'])
						{
							continue;
						}

						$tpl->newBlock('mes');

						$tpl->assign('mes', $m['abreviatura']);
					}
				}

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$tpl->newBlock('totales');

						foreach ($totales_admin[$admin][$anio] as $mes => $total_mes)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							$tpl->newBlock('total_mes');

							$tpl->assign('total_mes', $total_mes != 0 ? number_format($total_mes, 2) : '&nbsp;');
						}

						$tpl->assign('totales.total_anio', number_format(array_sum($totales_admin[$admin][$anio]), 2));
						$tpl->assign('totales.promedio_anio', number_format(array_sum($totales_admin[$admin][$anio]) / count(array_filter($totales_admin[$admin][$anio])), 2));
					}

					$tpl->newBlock('row');

					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$tpl->newBlock('subtitle');

						$tpl->assign('numcols', $_REQUEST['mes'] + 3);

						if ($meses)
						{
							foreach ($meses as $m)
							{
								if ($m['mes'] > $_REQUEST['mes'])
								{
									continue;
								}

								$tpl->newBlock('submes');

								$tpl->assign('mes', $m['abreviatura']);
							}
						}

						$tpl->gotoBlock('row');
					}

					$admin = $row['admin'];

					// $anio = $row['series'][$anio]['anio'];

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

					$tpl->assign('total', number_format($row['series'][$anio]['total'], 2));
					$tpl->assign('promedio', number_format($row['series'][$anio]['promedio'], 2));

					foreach ($row['series'][$anio]['importes'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						$tpl->newBlock('importe');

						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}
				}

				$tpl->newBlock('totales');

				foreach ((isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]) as $mes => $total_mes)
				{
					if ($mes > $_REQUEST['mes'])
					{
						continue;
					}

					$tpl->newBlock('total_mes');

					$tpl->assign('total_mes', $total_mes != 0 ? number_format($total_mes, 2) : '&nbsp;');
				}

				$tpl->assign('totales.total_anio', number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]), 2));
				$tpl->assign('totales.promedio_anio', number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]) / count(array_filter(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio])), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte_listado':
			if ($result = consulta_listado($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$totales_admin = $result['totales_admin'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $conceptos;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, "COMPARATIVO DE CONCEPTOS DE BALANCE", 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$conceptos[$_REQUEST['concepto']]} {$_REQUEST['anio'][0]}")), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(50, 5, 'COMPAÑIA', 1, 0);

						foreach ($_meses as $mes => $nombre)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							$this->Cell(20, 5, mb_strtoupper($nombre), 1, 0, 'C');
						}

						$this->Cell(20, 5, 'TOTAL', 1, 0, 'C');
						$this->Cell(20, 5, 'PROMEDIO', 1, 0, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('L', 'mm', array(216, 340));

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('L', array(216, 340));

				$pdf->SetFont('ARIAL', '', 8);

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$pdf->Cell(50, 5, 'TOTALES', 1, 0, 'R');

						foreach ($totales_admin[$admin][$anio] as $mes => $total_mes)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							$pdf->Cell(20, 5, $total_mes != 0 ? number_format($total_mes, 2) : '', 1, 0, 'R');
						}

						$pdf->Cell(20, 5, number_format(array_sum($totales_admin[$admin][$anio]), 2), 1, 0, 'R');
						$pdf->Cell(20, 5, number_format(array_sum($totales_admin[$admin][$anio]) / count(array_filter($totales_admin[$admin][$anio])), 2), 1, 0, 'R');

						$pdf->AddPage('L', array(216, 340));
					}

					$admin = $row['admin'];

					// $anio = $row['series'][0]['anio'];

					$pdf->SetFont('ARIAL', 'B', 8);

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 50)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(50, 5, $nombre_cia, 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					foreach ($row['series'][$anio]['importes'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						$pdf->Cell(20, 5, $importe != 0 ? number_format($importe, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(20, 5, number_format($row['series'][$anio]['total'], 2), 1, 0, 'R');
					$pdf->Cell(20, 5, number_format($row['series'][$anio]['promedio'], 2), 1, 1, 'R');
				}

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->Cell(50, 5, 'TOTALES', 1, 0, 'R');

				foreach ((isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]) as $mes => $total_mes)
				{
					if ($mes > $_REQUEST['mes'])
					{
						continue;
					}

					$pdf->Cell(20, 5, $total_mes != 0 ? number_format($total_mes, 2) : '', 1, 0, 'R');
				}

				$pdf->Cell(20, 5, number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]), 2), 1, 0, 'R');
				$pdf->Cell(20, 5, number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]) / count(array_filter(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio])), 2), 1, 0, 'R');

				$pdf->Output('comparativo-conceptos-balances.pdf', 'I');
			}

			break;

		case 'exportar_listado':
			if ($result = consulta_listado($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$totales_admin = $result['totales_admin'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				$data = '"","COMPARATIVO DE CONCEPTOS DE BALANCE ' . $anio . '"' . "\n";
				$data .= '"","' . mb_strtoupper($conceptos[$_REQUEST['concepto']]) . '"' . "\n\n";

				$data .= utf8_decode('"#","COMPAÑIA","' . implode('","', array_map('mb_strtoupper', $_meses)) . '","TOTAL","PROMEDIO"') . "\n";

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$data .= '"","TOTALES","' . implode('","', array_map('toNumberFormat', $totales_admin[$admin][$anio])) . '",';

						$data .= '"' . number_format(array_sum($totales_admin[$admin][$anio]), 2) . '","' . number_format(array_sum($totales_admin[$admin][$anio]) / count(array_filter($totales_admin[$admin][$anio])), 2) . '"' . "\n";

						$data .= "\n" . utf8_decode('"#","COMPAÑIA","' . implode('","', array_map('mb_strtoupper', $_meses)) . '","TOTAL","PROMEDIO"') . "\n";
					}

					$admin = $row['admin'];

					$data .= '"' . $row['num_cia'] . '","' . $row['nombre_cia'] . '","';

					$data .= implode('","', array_map('toNumberFormat', $row['series'][$anio]['importes'])) . '",';

					$data .= '"' . number_format($row['series'][$anio]['total'], 2) . '","' . number_format($row['series'][$anio]['promedio'], 2) . '"' . "\n";
				}

				$data .= '"","TOTALES","' . implode('","', array_map('toNumberFormat', isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio])) . '",';

				$data .= '"' . number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]), 2) . '","' . number_format(array_sum(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio]) / count(array_filter(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin][$anio] : $totales[$anio])), 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=comparativo-conceptos-balances.csv');

				echo $data;
			}

			break;

		case 'graficas_listado':
			if ($result = consulta_listado($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$totales_admin = $result['totales_admin'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				if ( ! class_exists('pChart'))
				{
					include("includes/pChart/pData.php");
					include("includes/pChart/pChart.php");
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $conceptos;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, "COMPARATIVO DE CONCEPTOS DE BALANCE", 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$conceptos[$_REQUEST['concepto']]} {$_REQUEST['anio'][0]}")), 0, 1, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('L', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('L', 'Letter');

				$pdf->SetFont('ARIAL', 'B', 10);

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$pdf->AddPage('L', 'Letter');
					}

					$admin = $row['admin'];

					$pdf->Cell(0, 5, "{$row['num_cia']} {$row['nombre_cia']}", 0, 0, 'C');

					$pdf->Ln(5);

					$data = new pData();
					$labels = array();

					$series = array_reverse($row['series']);

					$min = array();
					$max = array();

					foreach ($series as $serie_index => $serie)
					{
						foreach ($serie['importes'] as $mes => $importe)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							if ( ! isset($min[$mes]))
							{
								$min[$mes] = $importe;
							}

							if ( ! isset($max[$mes]))
							{
								$max[$mes] = $importe;
							}

							if ($importe != 0)
							{
								$data->AddPoint(round($importe, -4), 'Serie' . ($serie_index + 1), substr($_meses[$mes], 0, 3));

								if ($importe <= $min[$mes])
								{
									$min[$mes] = $importe;

									$labels[$mes][0] = array(
										'serie'		=> 'Serie' . ($serie_index + 1),
										'mes'		=> substr($_meses[$mes], 0, 3),
										'importe'	=> $importe,
										'r'			=> 214,
										'g'			=> 92,
										'b'			=> 79
									);
								}

								if ($importe >= $max[$mes])
								{
									$max[$mes] = $importe;

									$labels[$mes][1] = array(
										'serie'		=> 'Serie' . ($serie_index + 1),
										'mes'		=> substr($_meses[$mes], 0, 3),
										'importe'	=> $importe,
										'r'			=> 66,
										'g'			=> 139,
										'b'			=> 202
									);
								}

								// $labels[substr($_meses[$mes], 0, 3)] = $importe;
							}
							else
							{
								$data->AddPoint('', 'Serie' . ($serie_index + 1), substr($_meses[$mes], 0, 3));
							}
						}

						$data->AddSerie('Serie' . ($serie_index + 1));
						// $data->SetSerieName("{$conceptos[$_REQUEST['concepto']]} {$serie['anio']}", 'Serie' . ($serie_index + 1));
						$data->SetSerieName($serie['anio'], 'Serie' . ($serie_index + 1));
					}


					if ( ! in_array($_REQUEST['concepto'], array(
						'utilidad_bruta_pro',
						'utilidad_pro',
						'mp_pro',
						'utilidad_ventas',
						'utilidad_mat_prima',
						'mat_prima_ventas'
					)))
					{
						$data->SetYAxisFormat('currency');
					}

					$chart = new pChart(1200, 780);

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->setGraphArea(160, 10, 1110, 760);
					$chart->drawGraphArea(252, 252, 252);
					$chart->drawScale($data->GetData(), $data->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 2, $_REQUEST['tipo'] == 'barras' ? TRUE : FALSE);
					$chart->drawGrid(4, TRUE, 230, 230, 230, 255);
					$chart->setCurrency("$");

					// $chart->loadColorPalette('chartcolors/tones-5.txt');

					if ($_REQUEST['tipo'] == 'barras')
					{
						$chart->drawBarGraph($data->GetData(), $data->GetDataDescription(), TRUE);
					}
					else if ($_REQUEST['tipo'] == 'lineas')
					{
						$chart->drawFilledLineGraph($data->GetData(), $data->GetDataDescription(), 10, TRUE);
						$chart->drawPlotGraph($data->GetData(), $data->GetDataDescription(), 3, 2, 255, 255, 255);
					}

					$chart->setFontProperties('fonts/tahoma.ttf', 10);

					foreach ($labels as $labels_mes)
					{
						foreach ($labels_mes as $label)
						{
							$chart->setLabel($data->GetData(), $data->GetDataDescription(), $label['serie'], $label['mes'], number_format($label['importe'], 2), $label['r'], $label['g'], $label['b']);
						}
					}

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->drawLegend(5, 20, $data->GetDataDescription(), 255, 255, 255);

					$chart_filename = "chart-" . str_replace('_', '-', $_REQUEST['concepto']) . "-{$_REQUEST['anio'][0]}-{$row['num_cia']}.png";

					$chart->Render("tmp/{$chart_filename}");

					$pdf->Image("tmp/{$chart_filename}", NULL, NULL, 265);

					$pdf->Ln(5);
				}

				$pdf->Output('comparativo-conceptos-balances-graficas.pdf', 'I');
			}

			break;

		case 'consulta_promedios':
		case 'consulta_acumulado':
			if ($result = consulta_promedios($_REQUEST))
			{
				$datos = $result['datos'];
				$datos_no_incremento = $result['datos_no_incremento'];
				$totales = $result['totales'];
				$totales_no_incremento = $result['totales_no_incremento'];
				$totales_admin = $result['totales_admin'];
				$totales_admin_no_incremento = $result['totales_admin_no_incremento'];

				$tpl = new TemplatePower('plantillas/bal/BalancesComparativoDatosAnualConsultaPromedio.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				$tpl->assign('concepto', $conceptos[$_REQUEST['concepto']]);

				$tpl->assign('anio_ant', $_REQUEST['anio'][0] - 1);
				$tpl->assign('anio', $_REQUEST['anio'][0]);

				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$tpl->newBlock('totales');

						$tpl->assign('total_ant', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['promedio_ant'] : $totales_admin[$admin]['total_ant'], 2));
						$tpl->assign('total', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['promedio'] : $totales_admin[$admin]['total'], 2));
						$tpl->assign('diferencia', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['diferencia_promedio'] : $totales_admin[$admin]['diferencia_total'], 2));
					}

					$tpl->newBlock('row');

					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$tpl->newBlock('subtitle');

						$tpl->assign('anio_ant', $_REQUEST['anio'][0] - 1);
						$tpl->assign('anio', $_REQUEST['anio'][0]);

						$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

						$tpl->gotoBlock('row');
					}

					$admin = $row['admin'];

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

					$tpl->assign('importe_ant', $row['total_ant'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) : '&nbsp;');
					$tpl->assign('importe', $row['total'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) : '&nbsp;');

					$tpl->assign('color', $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['incremento_promedio'] > 0 ? 'blue' : 'red') : ($row['incremento_total'] > 0 ? 'blue' : 'red'));

					$tpl->assign('diferencia', $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['diferencia_promedio'] != 0 ? number_format($row['diferencia_promedio'], 2) : '&nbsp;') : ($row['diferencia_total'] != 0 ? number_format($row['diferencia_total'], 2) : '&nbsp;'));

					$tpl->assign('incremento', $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['incremento_promedio'] != 0 ? number_format($row['incremento_promedio'], 2) . '%' : '&nbsp;') : ($row['incremento_total'] != 0 ? number_format($row['incremento_total'], 2) . '%' : '&nbsp;'));
				}

				$tpl->newBlock('totales');

				$tpl->assign('total_ant', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio_ant'] : $totales['promedio_ant']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total_ant'] : $totales['total_ant']), 2));
				$tpl->assign('total', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio'] : $totales['promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total'] : $totales['total']), 2));
				$tpl->assign('diferencia', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_promedio'] : $totales['diferencia_promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_total'] : $totales['diferencia_total']), 2));

				if (count($datos_no_incremento) > 0)
				{
					$tpl->newBlock('no_incremento');

					$tpl->assign('anio_ant', $_REQUEST['anio'][0] - 1);
					$tpl->assign('anio', $_REQUEST['anio'][0]);

					$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

					$admin = NULL;

					foreach ($datos_no_incremento as $index => $row)
					{
						if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
						{
							$tpl->newBlock('totales_no_incremento');

							$tpl->assign('total_ant', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['promedio_ant'] : $totales_admin_no_incremento[$admin]['total_ant'], 2));
							$tpl->assign('total', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['promedio'] : $totales_admin_no_incremento[$admin]['total'], 2));
							$tpl->assign('diferencia', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['diferencia_promedio'] : $totales_admin_no_incremento[$admin]['diferencia_total'], 2));
						}

						$tpl->newBlock('row_no_incremento');

						if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
						{
							$tpl->newBlock('subtitle_no_incremento');

							$tpl->assign('anio_ant', $_REQUEST['anio'][0] - 1);
							$tpl->assign('anio', $_REQUEST['anio'][0]);

							$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

							$tpl->gotoBlock('row_no_incremento');
						}

						$admin = $row['admin'];

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$tpl->assign('importe_ant', $row['total_ant'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) : '&nbsp;');
						$tpl->assign('importe', $row['total'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) : '&nbsp;');

						$tpl->assign('color', $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['diferencia_promedio'] > 0 ? 'blue' : 'red') : ($row['diferencia_total'] > 0 ? 'blue' : 'red'));

						$tpl->assign('diferencia', $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['diferencia_promedio'] != 0 ? number_format($row['diferencia_promedio'], 2) : '&nbsp;') : ($row['diferencia_total'] != 0 ? number_format($row['diferencia_total'], 2) : '&nbsp;'));
					}

					$tpl->newBlock('totales_no_incremento');

					$tpl->assign('total_ant', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio_ant'] : $totales_no_incremento['promedio_ant']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total_ant'] : $totales_no_incremento['total_ant']), 2));
					$tpl->assign('total', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio'] : $totales_no_incremento['promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total'] : $totales_no_incremento['total']), 2));
					$tpl->assign('diferencia', number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_promedio'] : $totales_no_incremento['diferencia_promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_total'] : $totales_no_incremento['diferencia_total']), 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte_promedios':
		case 'reporte_acumulado':
			if ($result = consulta_promedios($_REQUEST))
			{
				$datos = $result['datos'];
				$datos_no_incremento = $result['datos_no_incremento'];
				$totales = $result['totales'];
				$totales_no_incremento = $result['totales_no_incremento'];
				$totales_admin = $result['totales_admin'];
				$totales_admin_no_incremento = $result['totales_admin_no_incremento'];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $conceptos;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, "COMPARATIVO DE CONCEPTOS DE BALANCE", 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$conceptos[$_REQUEST['concepto']]} {$_REQUEST['anio'][0]}")), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(80, 5, '', 0, 0);
						$this->Cell(48, 5, mb_strtoupper(utf8_decode($_meses[$_REQUEST['mes']])), 1, 1, 'C');

						$this->Cell(30, 5, '', 0, 0);
						$this->Cell(50, 5, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(24, 5, $_REQUEST['anio'][0] - 1, 1, 0, 'C');
						$this->Cell(24, 5, $_REQUEST['anio'][0], 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('DIFERENCIA'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('INCREMENTO'), 1, 0, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 5);

				$pdf->AddPage('P', 'Letter');

				$pdf->SetFont('ARIAL', '', 8);

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$pdf->SetTextColor(0, 0, 0);

						$pdf->SetFont('ARIAL', 'B', 8);

						$pdf->Cell(30, 5, '', 0, 0);
						$pdf->Cell(50, 5, utf8_decode('TOTALES'), 1, 0, 'R');
						$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['promedio_ant'] : $totales_admin[$admin]['total_ant'], 2), 1, 0, 'R');
						$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['promedio'] : $totales_admin[$admin]['total'], 2), 1, 0, 'R');
						$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin[$admin]['diferencia_promedio'] : $totales_admin[$admin]['diferencia_total'], 2), 1, 0, 'R');
						$pdf->Cell(24, 5, '', 1, 0);

						$pdf->AddPage('P', 'Letter');
					}

					$admin = $row['admin'];

					$pdf->Cell(30, 5, '', 0, 0);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->SetFont('ARIAL', 'B', 8);

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 50)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(50, 5, $nombre_cia, 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->Cell(24, 5, $row['total_ant'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) : '', 1, 0, 'R');
					$pdf->Cell(24, 5, $row['total'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', 'B', 8);

					if (($_REQUEST['tipo_reporte'] == 'promedios' ? $row['incremento_promedio'] : $row['incremento_total']) > 0)
					{
						$pdf->SetTextColor(0, 0, 206);
					}
					else
					{
						$pdf->SetTextColor(206, 0, 0);
					}

					$pdf->Cell(24, 5, $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['diferencia_promedio'] != 0 ? number_format($row['diferencia_promedio'], 2) : '') : ($row['diferencia_total'] != 0 ? number_format($row['diferencia_total'], 2) : ''), 1, 0, 'R');

					$pdf->Cell(24, 5, $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['incremento_promedio'] != 0 ? number_format($row['incremento_promedio'], 2) . '%' : '') : ($row['incremento_total'] != 0 ? number_format($row['incremento_total'], 2) . '%' : ''), 1, 1, 'R');
				}

				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->Cell(30, 5, '', 0, 0);
				$pdf->Cell(50, 5, utf8_decode('TOTALES'), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio_ant'] : $totales['promedio_ant']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total_ant'] : $totales['total_ant']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio'] : $totales['promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total'] : $totales['total']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_promedio'] : $totales['diferencia_promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_total'] : $totales['diferencia_total']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, '', 1, 0);

				if (count($datos_no_incremento) > 0)
				{
					$pdf->AddPage('P', 'Letter');

					$admin = NULL;

					foreach ($datos_no_incremento as $index => $row)
					{
						if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
						{
							$pdf->SetTextColor(0, 0, 0);

							$pdf->SetFont('ARIAL', 'B', 8);

							$pdf->Cell(30, 5, '', 0, 0);
							$pdf->Cell(50, 5, utf8_decode('TOTALES'), 1, 0, 'R');
							$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['promedio_ant'] : $totales_admin_no_incremento[$admin]['total_ant'], 2), 1, 0, 'R');
							$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['promedio'] : $totales_admin_no_incremento[$admin]['total'], 2), 1, 0, 'R');
							$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $totales_admin_no_incremento[$admin]['diferencia_promedio'] : $totales_admin_no_incremento[$admin]['diferencia_total'], 2), 1, 0, 'R');
							$pdf->Cell(24, 5, '', 1, 0);

							$pdf->AddPage('P', 'Letter');
						}

						$admin = $row['admin'];

						$pdf->Cell(30, 5, '', 0, 0);

						$pdf->SetTextColor(0, 0, 0);

						$pdf->SetFont('ARIAL', 'B', 8);

						$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

						while ($pdf->GetStringWidth($nombre_cia) > 50)
						{
							$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
						}

						$pdf->Cell(50, 5, $nombre_cia, 1, 0);

						$pdf->SetFont('ARIAL', '', 8);

						$pdf->Cell(24, 5, $row['total_ant'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) : '', 1, 0, 'R');
						$pdf->Cell(24, 5, $row['total'] != 0 ? number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) : '', 1, 0, 'R');

						$pdf->SetFont('ARIAL', 'B', 8);

						if (($_REQUEST['tipo_reporte'] == 'promedios' ? $row['diferencia_promedio'] : $row['diferencia_total']) > 0)
						{
							$pdf->SetTextColor(0, 0, 206);
						}
						else
						{
							$pdf->SetTextColor(206, 0, 0);
						}

						$pdf->Cell(24, 5, $_REQUEST['tipo_reporte'] == 'promedios' ? ($row['diferencia_promedio'] != 0 ? number_format($row['diferencia_promedio'], 2) : '') : ($row['diferencia_total'] != 0 ? number_format($row['diferencia_total'], 2) : ''), 1, 0, 'R');

						$pdf->Cell(24, 5, '', 1, 1, 'R');
					}

					$pdf->SetTextColor(0, 0, 0);

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(30, 5, '', 0, 0);
					$pdf->Cell(50, 5, utf8_decode('TOTALES'), 1, 0, 'R');
					$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio_ant'] : $totales_no_incremento['promedio_ant']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total_ant'] : $totales_no_incremento['total_ant']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio'] : $totales_no_incremento['promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total'] : $totales_no_incremento['total']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_promedio'] : $totales_no_incremento['diferencia_promedio']) : (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_total'] : $totales_no_incremento['diferencia_total']), 2), 1, 0, 'R');
				$pdf->Cell(24, 5, '', 1, 0);
				}

				$pdf->Output('comparativo-conceptos-balances.pdf', 'I');
			}

			break;

		case 'exportar_promedios':
		case 'exportar_acumulado':
			if ($result = consulta_promedios($_REQUEST))
			{
				$datos = $result['datos'];
				$datos_no_incremento = $result['datos_no_incremento'];
				$totales = $result['totales'];
				$totales_no_incremento = $result['totales_no_incremento'];
				$totales_admin = $result['totales_admin'];
				$totales_admin_no_incremento = $result['totales_admin_no_incremento'];

				$data = '"","COMPARATIVO DE CONCEPTOS DE BALANCE ' . $_REQUEST['anio'][0] . '"' . "\n";
				$data .= '"","' . mb_strtoupper($conceptos[$_REQUEST['concepto']]) . '"' . "\n\n";

				$data .= utf8_decode('"","","' . $_meses[$_REQUEST['mes']] . '","' . $_meses[$_REQUEST['mes']] . '"') . "\n";
				$data .= utf8_decode('"#","COMPAÑIA","' . ($_REQUEST['anio'][0] - 1) . '","' . $_REQUEST['anio'][0] . '","TOTAL","PROMEDIO"') . "\n";

				$admin = NULL;

				foreach ($datos as $index => $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$data .= '"","TOTALES",';
						$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin[$admin]['promedio_ant'], 2) : number_format($totales_admin[$admin]['total_ant'], 2)) . '",';
						$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin[$admin]['promedio'], 2) : number_format($totales_admin[$admin]['total'], 2)) . '",';
						$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin[$admin]['diferencia_promedio'], 2) : number_format($totales_admin[$admin]['diferencia_total'], 2)) . '"' . "\n\n";

						$data .= utf8_decode('"","","' . $_meses[$_REQUEST['mes']] . '","' . $_meses[$_REQUEST['mes']] . '"') . "\n";
						$data .= utf8_decode('"#","COMPAÑIA","' . ($_REQUEST['anio'][0] - 1) . '","' . $_REQUEST['anio'][0] . '","TOTAL","PROMEDIO"') . "\n";
					}

					$admin = $row['admin'];

					$data .= '"' . $row['num_cia'] . '","' . $row['nombre_cia'] . '",';

					$data .= '"' . number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) . '",';
					$data .= '"' . number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) . '",';

					$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($row['diferencia_promedio'], 2) : number_format($row['diferencia_total'], 2)) . '",';
					$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($row['incremento_promedio'], 2) : number_format($row['incremento_total'], 2)) . '"' . "\n";
				}

				$data .= '"","TOTALES",';
				$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio_ant'] : $totales['promedio_ant'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total_ant'] : $totales['total_ant'], 2)) . '",';
				$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['promedio'] : $totales['promedio'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['total'] : $totales['total'], 2)) . '",';
				$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_promedio'] : $totales['diferencia_promedio'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin[$admin]['diferencia_total'] : $totales['diferencia_total'], 2)) . '"' . "\n";

				if ($datos_no_incremento)
				{
					$data .= "\n";

					$data .= utf8_decode('"","","' . $_meses[$_REQUEST['mes']] . '","' . $_meses[$_REQUEST['mes']] . '"') . "\n";
					$data .= utf8_decode('"#","COMPAÑIA","' . ($_REQUEST['anio'][0] - 1) . '","' . $_REQUEST['anio'][0] . '","TOTAL","PROMEDIO"') . "\n";

					$admin = NULL;

					foreach ($datos_no_incremento as $index => $row)
					{
						if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
						{
							$data .= '"","TOTALES",';
							$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin_no_incremento[$admin]['promedio_ant'], 2) : number_format($totales_admin_no_incremento[$admin]['total_ant'], 2)) . '",';
							$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin_no_incremento[$admin]['promedio'], 2) : number_format($totales_admin_no_incremento[$admin]['total'], 2)) . '",';
							$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($totales_admin_no_incremento[$admin]['diferencia_promedio'], 2) : number_format($totales_admin_no_incremento[$admin]['diferencia_total'], 2)) . '"' . "\n\n";

							$data .= utf8_decode('"","","' . $_meses[$_REQUEST['mes']] . '","' . $_meses[$_REQUEST['mes']] . '"') . "\n";
							$data .= utf8_decode('"#","COMPAÑIA","' . ($_REQUEST['anio'][0] - 1) . '","' . $_REQUEST['anio'][0] . '","TOTAL","PROMEDIO"') . "\n";
						}

						$admin = $row['admin'];

						$data .= '"' . $row['num_cia'] . '","' . $row['nombre_cia'] . '",';

						$data .= '"' . number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2) . '",';
						$data .= '"' . number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2) . '",';

						$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format($row['diferencia_promedio'], 2) : number_format($row['diferencia_total'], 2)) . '",';
						$data .= '"0.00"' . "\n";
					}

					$data .= '"","TOTALES",';
					$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio_ant'] : $totales_no_incremento['promedio_ant'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total_ant'] : $totales_no_incremento['total_ant'], 2)) . '",';
					$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['promedio'] : $totales_no_incremento['promedio'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['total'] : $totales_no_incremento['total'], 2)) . '",';
					$data .= '"' . ($_REQUEST['tipo_reporte'] == 'promedios' ? number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_promedio'] : $totales_no_incremento['diferencia_promedio'], 2) : number_format(isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? $totales_admin_no_incremento[$admin]['diferencia_total'] : $totales_no_incremento['diferencia_total'], 2)) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=comparativo-conceptos-balances.csv');

				echo $data;
			}

			break;

		case 'graficas_promedios':
			if ($result = consulta_promedios($_REQUEST))
			{
				$datos = $result['datos'];
				$datos_no_incremento = $result['datos_no_incremento'];
				$totales = $result['totales'];
				$totales_no_incremento = $result['totales_no_incremento'];
				$totales_admin = $result['totales_admin'];
				$totales_admin_no_incremento = $result['totales_admin_no_incremento'];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				if ( ! class_exists('pChart'))
				{
					include("includes/pChart/pData.php");
					include("includes/pChart/pChart.php");
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $conceptos;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, "COMPARATIVO DE CONCEPTOS DE BALANCE", 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$conceptos[$_REQUEST['concepto']]} {$_REQUEST['anio'][0]}")), 0, 1, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('P', 'Letter');

				$pdf->SetFont('ARIAL', 'B', 10);

				foreach ($datos as $row)
				{
					$pdf->Cell(0, 5, "{$row['num_cia']} {$row['nombre_cia']}", 0, 0, 'C');

					$pdf->Ln(5);

					$data = new pData();
					$labels = array();

					$data->AddPoint($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 'Serie1', $_meses[$_REQUEST['mes']]);
					$data->AddSerie('Serie1');
					$data->SetSerieName("{$_meses[$_REQUEST['mes']]} " . ($_REQUEST['anio'][0] - 1), 'Serie1');

					$labels1["{$_meses[$_REQUEST['mes']]} " . ($_REQUEST['anio'][0] - 1)] = $_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'];

					$data->AddPoint($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 'Serie2', $_meses[$_REQUEST['mes']]);
					$data->AddSerie('Serie2');
					$data->SetSerieName("{$_meses[$_REQUEST['mes']]} {$_REQUEST['anio'][0]}", 'Serie2');

					$labels2["{$_meses[$_REQUEST['mes']]} {$_REQUEST['anio'][0]}"] = $_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'];

					if ( ! in_array($_REQUEST['concepto'], array(
						'utilidad_bruta_pro',
						'utilidad_pro',
						'mp_pro',
						'utilidad_ventas',
						'utilidad_mat_prima',
						'mat_prima_ventas'
					)))
					{
						$data->SetYAxisFormat('currency');
					}

					$chart = new pChart(800, 480);

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->setGraphArea(80, 10, 700, 460);
					$chart->drawGraphArea(252, 252, 252);
					$chart->drawScale($data->GetData(), $data->GetDataDescription(), SCALE_START0, 0, 0, 0, TRUE, 0, 2, TRUE);
					$chart->drawGrid(4, TRUE, 230, 230, 230, 255);
					$chart->setCurrency("$");

					// $chart->setColorPalette(0, 0, 0, 206);
					// $chart->loadColorPalette('chartcolors/tones-5.txt');
					$chart->drawBarGraph($data->GetData(), $data->GetDataDescription(), TRUE);

					$chart->setFontProperties('fonts/tahoma.ttf', 8);

					$chart->setLabel($data->GetData(), $data->GetDataDescription(), 'Serie1', 0, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio_ant'] : $row['total_ant'], 2), 127, 201, 255);
					$chart->setLabel($data->GetData(), $data->GetDataDescription(), 'Serie2', 0, number_format($_REQUEST['tipo_reporte'] == 'promedios' ? $row['promedio'] : $row['total'], 2), 239, 233, 195);

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->drawLegend(705, 20, $data->GetDataDescription(), 255, 255, 255);

					$chart_filename = "chart-" . str_replace('_', '-', $_REQUEST['concepto']) . "-{$_REQUEST['anio'][0]}-{$row['num_cia']}.png";

					$chart->Render("tmp/{$chart_filename}");

					$pdf->Image("tmp/{$chart_filename}", NULL, NULL, 190);

					$pdf->Ln(5);
				}

				$pdf->Output('comparativo-conceptos-balances-graficas.pdf', 'I');
			}

			break;

		case 'graficas_acumulado':
			if ($result = consulta_promedios($_REQUEST))
			{
				$datos = $result['datos'];
				$datos_no_incremento = $result['datos_no_incremento'];
				$totales = $result['totales'];
				$totales_no_incremento = $result['totales_no_incremento'];
				$totales_admin = $result['totales_admin'];
				$totales_admin_no_incremento = $result['totales_admin_no_incremento'];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				if ( ! class_exists('pChart'))
				{
					include("includes/pChart/pData.php");
					include("includes/pChart/pChart.php");
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $conceptos;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, "COMPARATIVO DE CONCEPTOS DE BALANCE", 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$conceptos[$_REQUEST['concepto']]} {$_REQUEST['anio'][0]}")), 0, 1, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('P', 'Letter');

				$pdf->SetFont('ARIAL', 'B', 10);

				$admin = NULL;

				foreach ($datos as $row)
				{
					if (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 && $index > 0 && $admin != $row['admin'])
					{
						$pdf->AddPage('P', 'Letter');
					}

					$admin = $row['admin'];

					$pdf->Cell(0, 5, "{$row['num_cia']} {$row['nombre_cia']}", 0, 0, 'C');

					$pdf->Ln(5);

					$data = new pData();
					$labels1 = array();
					$labels2 = array();

					foreach ($row['importes_ant'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						if ($importe != 0)
						{
							$data->AddPoint($importe, 'Serie1', substr($_meses[$mes], 0, 3));

							$labels1[substr($_meses[$mes], 0, 3)] = $importe;
						}
						else
						{
							$data->AddPoint('', 'Serie1', substr($_meses[$mes], 0, 3));
						}
					}

					$data->AddSerie('Serie1');
					$data->SetSerieName($_REQUEST['anio'][0] - 1, 'Serie1');

					foreach ($row['importes'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						if ($importe != 0)
						{
							$data->AddPoint($importe, 'Serie2', substr($_meses[$mes], 0, 3));

							$labels2[substr($_meses[$mes], 0, 3)] = $importe;
						}
						else
						{
							$data->AddPoint('', 'Serie2', substr($_meses[$mes], 0, 3));
						}
					}

					$data->AddSerie('Serie2');
					$data->SetSerieName($_REQUEST['anio'][0], 'Serie2');

					if ( ! in_array($_REQUEST['concepto'], array(
						'utilidad_bruta_pro',
						'utilidad_pro',
						'mp_pro',
						'utilidad_ventas',
						'utilidad_mat_prima',
						'mat_prima_ventas'
					)))
					{
						$data->SetYAxisFormat('currency');
					}

					$chart = new pChart(800, 480);

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->setGraphArea(80, 10, 720, 460);
					$chart->drawGraphArea(252, 252, 252);
					$chart->drawScale($data->GetData(), $data->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 2, TRUE);
					$chart->drawGrid(4, TRUE, 230, 230, 230, 255);
					$chart->setCurrency("$");

					// $chart->setColorPalette(0, 0, 0, 206);
					// $chart->drawFilledLineGraph($data->GetData(), $data->GetDataDescription(), 10, TRUE);
					// $chart->drawPlotGraph($data->GetData(), $data->GetDataDescription(), 3, 2, 255, 255, 255);
					// $chart->loadColorPalette('chartcolors/tones-5.txt');
					$chart->drawBarGraph($data->GetData(), $data->GetDataDescription(), TRUE);

					$chart->setFontProperties('fonts/tahoma.ttf', 8);

					foreach ($labels1 as $mes => $importe)
					{
						$chart->setLabel($data->GetData(), $data->GetDataDescription(), 'Serie1', $mes, number_format($importe, 2), 127, 201, 255);
					}

					foreach ($labels2 as $mes => $importe)
					{
						$chart->setLabel($data->GetData(), $data->GetDataDescription(), 'Serie2', $mes, number_format($importe, 2), 239, 233, 195);
					}

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->drawLegend(730, 20, $data->GetDataDescription(), 255, 255, 255);

					$chart_filename = "chart-" . str_replace('_', '-', $_REQUEST['concepto']) . "-{$_REQUEST['anio'][0]}-{$row['num_cia']}.png";

					$chart->Render("tmp/{$chart_filename}");

					$pdf->Image("tmp/{$chart_filename}", NULL, NULL, 190);

					$pdf->Ln(5);
				}

				$pdf->Output('comparativo-conceptos-balances-graficas.pdf', 'I');
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/BalancesComparativoDatosAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
