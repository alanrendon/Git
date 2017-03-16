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

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'X',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

function consulta($params)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $params['mes'], 1, $params['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $params['mes'] + 1, 0, $params['anio']));

	$dias = date('j', mktime(0, 0, 0, $params['mes'] + 1, 0, $params['anio']));

	$condiciones = array();

	$condiciones[] = "mov.fecha_mov BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
			$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones[] = "cc.idadministrador = {$params['admin']}";
	}

	if (isset($params['num_pro']) && $params['num_pro'] > 0)
	{
		$condiciones[] = "mov.num_proveedor = {$params['num_pro']}";
	}

	if (isset($params['codmp']) && count($params['codmp']) > 0)
	{
		$condiciones[] = "mov.codmp IN (" . implode(', ', $params['codmp']) . ")";
	}
	else
	{
		$condiciones[] = "mov.codmp IN (160, 600, 700, 573)";
	}

	$condiciones_string = implode(' AND ', $condiciones);

	$result = $db->query("SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		EXTRACT(DAY FROM mov.fecha_mov) AS dia,
		AVG(mov.precio) AS precio,
		SUM(mov.kilos) AS kilos
	FROM
		fact_rosticeria mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = mov.num_proveedor)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		dia
	ORDER BY
		num_cia,
		dia");

	if ($result)
	{
		$datos = array();
		$totales = array_fill(1, $dias, 0);

		$num_cia = NULL;

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$datos[$num_cia] = array(
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre_cia'],
					'kilos'			=> array_fill(1, $dias, 0),
					'precios'		=> array_fill(1, $dias, 0),
					'total'			=> 0
				);
			}

			$datos[$num_cia]['kilos'][$row['dia']] = floatval($row['kilos']);
			$datos[$num_cia]['precios'][$row['dia']] = floatval($row['precio']);
			$datos[$num_cia]['total'] += floatval($row['kilos']);

			$totales[$row['dia']] += floatval($row['kilos']);
		}

		return array(
			'dias'		=> $dias,
			'datos' 	=> $datos,
			'totales'	=> $totales
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
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaAutomaticoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha_corte', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));

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

			if ($query = $db->query("SELECT diferencia_maxima FROM maxima_diferencia_efectivo WHERE tsbaja IS NULL ORDER BY id DESC LIMIT 1"))
			{
				$diferencia_maxima = $query[0]['diferencia_maxima'];
			}
			else
			{
				$db->query("INSERT INTO maxima_diferencia_efectivo (diferencia_maxima, idalta) VALUES (0, {$_SESSION['iduser']})");

				$diferencia_maxima = 0;
			}

			$tpl->assign('_ROOT.diferencia_maxima', number_format($diferencia_maxima, 2));

			echo $tpl->getOutputContent();

			break;

		case 'generar':
			$diferencia_maxima = get_val($_REQUEST['diferencia_maxima']);

			$result = $db->query("SELECT id, diferencia_maxima FROM maxima_diferencia_efectivo WHERE tsbaja IS NULL ORDER BY id DESC LIMIT 1");

			if ($diferencia_maxima != $result[0]['diferencia_maxima'])
			{
				$db->query("UPDATE maxima_diferencia_efectivo SET tsbaja = NOW(), idbaja = {$_SESSION['iduser']} WHERE id = {$result[0]['id']}");
				$db->query("INSERT INTO maxima_diferencia_efectivo (diferencia_maxima, idalta) VALUES ({$diferencia_maxima}, {$_SESSION['iduser']})");
			}

			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha_corte']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$condiciones1 = array();

			$condiciones1[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones1[] = "ec.cod_mov IN (1, 16, 44, 99)";

			$condiciones1[] = "ec.num_cia BETWEEN 1 AND 899";

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
					$condiciones1[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '')
			{
				$omitir = array();

				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}

				if (count($omitir) > 0)
				{
					$condiciones1[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) NOT IN (' . implode(', ', $omitir) . ')';
				}
			}

			$condiciones2 = array();

			$condiciones2[] = "num_cia NOT IN (SELECT sucursal FROM porcentajes_puntos_calientes WHERE matriz != sucursal)";

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones2[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			// Obtener compañías para generar comprobantes
			$sql = "SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				estado,
				aplica_iva AS aplicar_iva,
				COALESCE((
					SELECT
						TRUE
					FROM
						porcentajes_puntos_calientes
					WHERE
						sucursal = result.num_cia
					LIMIT 1
				), FALSE) AS desglosa,
				COALESCE((
					SELECT
						SUM(efectivo)
					FROM
						total_panaderias
					WHERE
						num_cia = result.num_cia
						AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
				), (
					SELECT
						SUM(efectivo)
					FROM
						total_companias
					WHERE
						num_cia = result.num_cia
						AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
				), 0) AS efectivo,
				depositos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						otros_depositos
					WHERE
						num_cia = result.num_cia
						AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
				), 0) AS oficina,
				COALESCE((
					SELECT
						SUM(CASE WHEN tipo_mov = TRUE THEN -importe ELSE importe END)
					FROM
						estado_cuenta
					WHERE
						num_cia = result.num_cia
						AND cod_mov IN (7, 13, 19, 48)
						AND fecha BETWEEN '{$fecha1}'::DATE AND '{$fecha2}'::DATE
						AND fecha >= '01-01-2015'
				), 0) AS faltantes
			FROM
				(
					SELECT
						COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
						SUM(importe) AS depositos
					FROM
						estado_cuenta ec
					WHERE
						" . implode(' AND ', $condiciones1) . "
					GROUP BY
						COALESCE(ec.num_cia_sec, ec.num_cia)
				) result
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones2) . "
			ORDER BY
				num_cia";

			// Si no hay resultados, terminar
			if ( ! $result = $db->query($sql))
			{
				return FALSE;
			}

			include('includes/class.facturas.v3.inc.php');

			// Crear instancia de FacturasClass
			$fac = new FacturasClass();

			// En caso de error, terminar
			if ($fac->ultimoCodigoError() < 0)
			{
				return -1;
			}

			// Crear vista a partir de una plantilla
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaAutomaticoResultado.tpl');
			$tpl->prepare();

			$tpl->assign('fecha_corte', $_REQUEST['fecha_corte']);

			// Recorrer compañías
			foreach ($result as $cia)
			{
				$condiciones = array();

				$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones[] = "ec.cod_mov IN (1, 16, 44, 99)";

				$condiciones[] = "COALESCE(ec.num_cia_sec, ec.num_cia) = {$cia['num_cia']}";

				// Obtener depósitos de compañía
				$sql = "SELECT
					COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
					fecha,
					EXTRACT(DAY from fecha) AS dia,
					SUM(importe) AS importe
				FROM
					estado_cuenta ec
				WHERE
					" . implode(' AND ', $condiciones) . "
				GROUP BY
					COALESCE(ec.num_cia_sec, ec.num_cia),
					fecha,
					dia
				ORDER BY
					fecha";

				$depositos = $db->query($sql);

				// Obtener último registro para validación
				$last_row = end($depositos);

				// Si útimo día de depósitos es menor al día de corte, saltar compañía y continuar
				if ($last_row['dia'] < $dia_corte)
				{
					continue;
				}

				// Estructurar datos de compañía
				$datos = array();

				$datos[$cia['num_cia']] = array(
					'num_cia'		=> $cia['num_cia'],
					'nombre_cia'	=> $cia['nombre_cia'],
					'estado'		=> $cia['estado'],
					'aplicar_iva'	=> $cia['aplicar_iva']== 't' ? 16 : 0,
					'tipo'			=> 'matriz',
					'desglosa'		=> $cia['desglosa'] == 't' ? TRUE : FALSE,
					'facturado'		=> array_fill(1, $dia_corte, FALSE),
					'depositos'		=> array_fill(1, $dia_corte, 0),
					'efectivos'		=> array_fill(1, $dia_corte, 0),
					'desglosado'	=> array_fill(1, $dia_corte, FALSE),
					'clientes'		=> array_fill(1, $dia_corte, 0),
					'ventas'		=> array_fill(1, $dia_corte, 0),
					'diferencia'	=> round($cia['efectivo'] - $cia['depositos'] - $cia['oficina'] - $cia['faltantes'], 2),
					'facturable'	=> TRUE
				);

				// Si la diferencia de efectivo contra depositos sobrepasa la diferencia máxima no facturar
				if (abs($datos[$cia['num_cia']]['diferencia']) > $diferencia_maxima)
				{
					$datos[$cia['num_cia']]['facturable'] = FALSE;
				}

				foreach ($depositos as $deposito)
				{
					$datos[$cia['num_cia']]['depositos'][$deposito['dia']] = floatval($deposito['importe']);
					$datos[$cia['num_cia']]['efectivos'][$deposito['dia']] = floatval($deposito['importe']);
				}

				// Obtener facturas de clientes emitidas en el periodo
				$sql = "SELECT
					EXTRACT(DAY FROM fecha_pago) AS dia,
					SUM(total) AS importe
				FROM
					facturas_electronicas
				WHERE
					num_cia = {$cia['num_cia']}
					AND fecha_pago BETWEEN '{$fecha1}' AND '{$fecha2}'
					AND tipo = 2
					AND status = 1
				GROUP BY
					dia
				ORDER BY
					dia";

				$clientes = $db->query($sql);

				if ($clientes)
				{
					foreach ($clientes as $c)
					{
						$datos[$cia['num_cia']]['clientes'][$c['dia']] = floatval($c['importe']);
					}
				}

				// Calcular importes de venta a facturar
				foreach ($datos[$cia['num_cia']]['efectivos'] as $dia => $efectivo)
				{
					$datos[$cia['num_cia']]['ventas'][$dia] = $efectivo - $datos[$cia['num_cia']]['clientes'][$dia];

					// Si alguno de los importes de venta es menor a 0, la compañía no puede ser facturable
					if ($datos[$cia['num_cia']]['ventas'][$dia] < 0)
					{
						$datos[$cia['num_cia']]['facturable'] = FALSE;
					}
				}

				// Obtener estatus de días facturados
				$sql = "SELECT
					EXTRACT(DAY FROM fecha) AS dia
				FROM
					facturas_electronicas
				WHERE
					num_cia = {$cia['num_cia']}
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
					AND tipo = 1
					AND status = 1
				GROUP BY
					dia
				ORDER BY
					dia";

				$facturado = $db->query($sql);

				if ($facturado)
				{
					foreach ($facturado as $f)
					{
						$datos[$cia['num_cia']]['facturado'][$f['dia']] = TRUE;
					}
				}

				// En caso de ser una compañía que desglosa, obtener sucursales y calcular efectivos a facturar
				if ($cia['desglosa'] == 't')
				{
					// Obtener efectivos desglosados de compañía matriz
					$sql = "SELECT
						num_cia,
						EXTRACT(DAY FROM fecha) AS dia,
						importe
					FROM
						ventas_sucursales
					WHERE
						num_cia = {$cia['num_cia']}
						AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
					ORDER BY
						dia";

					$efectivos = $db->query($sql);

					// Si hay efectivos desglosados, asignarlos a los días que pertenecen en el arreglo de datos
					if ($efectivos)
					{
						foreach ($efectivos as $efectivo)
						{
							$datos[$cia['num_cia']]['efectivos'][$efectivo['dia']] = floatval($efectivo['importe']);
							$datos[$cia['num_cia']]['desglosado'][$efectivo['dia']] = TRUE;
						}
					}

					// Obtener sucursales de la matriz y sus porcentajes de desglose
					$sql = "SELECT
						s.sucursal AS num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.estado,
						cc.aplica_iva AS aplicar_iva,
						s.porcentaje
					FROM
						porcentajes_puntos_calientes s
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = s.sucursal)
					WHERE
						s.matriz = {$cia['num_cia']}
						AND s.sucursal != s.matriz
					ORDER BY
						s.sucursal";

					$sucursales = $db->query($sql);

					$total_sucursales = array_fill(1, $dia_corte, 0);

					// Estructurar datos de las sucursales
					foreach ($sucursales as $sucursal)
					{
						$datos[$sucursal['num_cia']] = array(
							'num_cia'		=> $sucursal['num_cia'],
							'nombre_cia'	=> $sucursal['nombre_cia'],
							'estado'		=> $sucursal['estado'],
							'aplicar_iva'	=> $sucursal['aplicar_iva']== 't' ? 16 : 0,
							'tipo'			=> 'sucursal',
							'desglosa'		=> TRUE,
							'facturado'		=> array_fill(1, $dia_corte, FALSE),
							'depositos'		=> array_fill(1, $dia_corte, 0),
							'efectivos'		=> array_fill(1, $dia_corte, 0),
							'desglosado'	=> array_fill(1, $dia_corte, FALSE),
							'clientes'		=> array_fill(1, $dia_corte, 0),
							'ventas'		=> array_fill(1, $dia_corte, 0),
							'diferencia'	=> 0,
							'facturable'	=> $datos[$cia['num_cia']]['facturable']
						);

						// Obtener efectivos desglosados de la sucursal
						$sql = "SELECT
							num_cia,
							EXTRACT(DAY FROM fecha) AS dia,
							importe
						FROM
							ventas_sucursales
						WHERE
							num_cia = {$sucursal['num_cia']}
							AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
						ORDER BY
							dia";

						$efectivos = $db->query($sql);

						// Si hay efectivos desglosados, asignarlos a los días que pertenecen en el arreglo de datos
						if ($efectivos)
						{
							foreach ($efectivos as $efectivo)
							{
								$datos[$sucursal['num_cia']]['efectivos'][$efectivo['dia']] = floatval($efectivo['importe']);
								$datos[$sucursal['num_cia']]['desglosado'][$efectivo['dia']] = TRUE;
							}
						}

						// Obtener facturas de clientes emitidas en el periodo
						$sql = "SELECT
							EXTRACT(DAY FROM fecha_pago) AS dia,
							SUM(total) AS importe
						FROM
							facturas_electronicas
						WHERE
							num_cia = {$sucursal['num_cia']}
							AND fecha_pago BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND tipo = 2
							AND status = 1
						GROUP BY
							dia
						ORDER BY
							dia";

						$clientes = $db->query($sql);

						if ($clientes)
						{
							foreach ($clientes as $c)
							{
								$datos[$sucursal['num_cia']]['clientes'][$c['dia']] = floatval($c['importe']);
							}
						}

						// Obtener estatus de días facturados
						$sql = "SELECT
							EXTRACT(DAY FROM fecha) AS dia
						FROM
							facturas_electronicas
						WHERE
							num_cia = {$sucursal['num_cia']}
							AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND tipo = 1
							AND status > 0
						GROUP BY
							dia
						ORDER BY
							dia";

						$facturado = $db->query($sql);

						if ($facturado)
						{
							foreach ($facturado as $f)
							{
								$datos[$cia['num_cia']]['facturado'][$f['dia']] = TRUE;
							}
						}

						// Verificar el desglose de efectivos de la sucursal
						foreach ($datos[$sucursal['num_cia']]['efectivos'] as $dia => $importe)
						{
							// Si no ha sido desglosado el importe para la sucursal, calcularlo
							if ( ! $datos[$sucursal['num_cia']]['desglosado'][$dia])
							{
								$porcentaje = $sucursal['porcentaje'] + round(mt_rand(-99, 99) / 100, 2);

								$importe_sucursal = round($datos[$cia['num_cia']]['depositos'][$dia] * $porcentaje / 100, 2);

								$datos[$sucursal['num_cia']]['efectivos'][$dia] = $importe_sucursal;
								$datos[$sucursal['num_cia']]['desglosado'][$dia] = TRUE;

								$total_sucursales[$dia] += $importe_sucursal;
							}
							// Si ya ha sido desglosado el importe de la sucursal, sumarlo al total de sucursales para calcular importe de la matriz
							else
							{
								$total_sucursales[$dia] += $datos[$sucursal['num_cia']]['efectivos'][$dia];
							}
						}
					}

					// Verificar el desglose de efectivos de la matriz
					foreach ($datos[$cia['num_cia']]['efectivos'] as $dia => $importe)
					{
						if ( ! $datos[$cia['num_cia']]['desglosado'][$dia])
						{
							$datos[$cia['num_cia']]['efectivos'][$dia] = $datos[$cia['num_cia']]['depositos'][$dia] - $total_sucursales[$dia];
							$datos[$cia['num_cia']]['desglosado'][$dia] = TRUE;
						}

						$total_sucursales[$dia] += $datos[$cia['num_cia']]['efectivos'][$dia];
					}

					// Comparar variación de los depósitos del periodo y la suma total de las sucursales
					$dif = round(array_sum($datos[$cia['num_cia']]['depositos']) - array_sum($total_sucursales), 2);

					// Si hay diferencia, distribuirla entre todas sucursales en los días no facturados
					if ($dif != 0)
					{
						foreach ($datos as $num_cia => $cia)
						{
							foreach ($cia['efectivos'] as $dia => $importe)
							{
								// Si el día ya esta facturado, omitirlo
								if ($cia['facturado'][$dia])
								{
									continue;
								}

								// Si la diferencia es mayor a 0 o su valor absoluto es menor al efectivo del día,
								// adicionarlo al importe y terminar ciclo de distribución
								if ($dif > 0 || ($dif < 0 && abs($dif) < $importe))
								{
									$datos[$num_cia]['efectivos'][$dia] += $dif;

									break(2);
								}
								// Si la diferencia es menor a 0 y su valor absoluto es mayor al efectivo del día,
								// dividir el importe en dos para adicionar a la diferencia y restar al importe
								else if ($dif < 0 && abs($dif) > $importe)
								{
									$dif += round($importe / 2, 2);

									$datos[$num_cia]['efectivos'][$dia] -= round($importe / 2, 2);
								}
							}
						}
					}

					// Calcular importes de venta a facturar de la matriz
					foreach ($datos[$cia['num_cia']]['efectivos'] as $dia => $efectivo)
					{
						$datos[$cia['num_cia']]['ventas'][$dia] = $efectivo - $datos[$cia['num_cia']]['clientes'][$dia];

						// Si alguno de los importes de venta es menor a 0, la compañía no puede ser facturable
						if ($datos[$cia['num_cia']]['ventas'][$dia] < 0)
						{
							$datos[$cia['num_cia']]['facturable'] = FALSE;
						}
					}

					// Calcular importes de venta a facturar de las sucursales
					foreach ($sucursales as $sucursal)
					{
						foreach ($datos[$sucursal['num_cia']]['efectivos'] as $dia => $efectivo)
						{
							$datos[$sucursal['num_cia']]['ventas'][$dia] = $efectivo - $datos[$sucursal['num_cia']]['clientes'][$dia];

							// Si alguno de los importes de venta es menor a 0, la compañía no puede ser facturable
							if ($datos[$sucursal['num_cia']]['ventas'][$dia] < 0)
							{
								$datos[$sucursal['num_cia']]['facturable'] = $datos[$cia['num_cia']]['facturable'];
							}
						}
					}

					// Actualizar efectivos desglosadas de sucursales en la base de datos
					foreach ($datos as $num_cia => $cia)
					{
						foreach ($cia['efectivos'] as $dia => $efectivo)
						{
							$fecha = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia, $anio_corte));

							if ($id = $db->query("SELECT id FROM ventas_sucursales WHERE num_cia = {$num_cia} AND fecha = '{$fecha}'"))
							{
								$db->query("UPDATE ventas_sucursales SET importe = {$efectivo} WHERE id = {$id[0]['id']}");
							}
							else
							{
								$db->query("INSERT INTO ventas_sucursales (num_cia, fecha, importe) VALUES ({$num_cia}, '{$fecha}', {$efectivo})");
							}
						}
					}
				}

				// Iniciar proceso para generar comprobantes
				foreach ($datos as $num_cia => $cia)
				{
					$status_emisor = TRUE;

					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $cia['nombre_cia']);

					$tpl->assign('efectivo', number_format(array_sum($cia['efectivos']), 2));
					$tpl->assign('clientes', number_format(array_sum($cia['clientes']), 2));
					$tpl->assign('venta', number_format(array_sum($cia['ventas']), 2));

					// Si la compañía no es facturable, poner fila con observaciones de lo ocurrido
					if ( ! $cia['facturable'])
					{
						$tpl->newBlock('obs');

						if (abs($cia['diferencia']) > $diferencia_maxima)
						{
							$tpl->assign('obs', 'La compa&ntilde;&iacute;a no es facturable, tiene una diferencia en efectivos de ' . number_format($cia['diferencia'], 2));
						}
						else
						{
							$tpl->assign('obs', 'La compa&ntilde;&iacute;a tiene errores y no puede ser facturada');
						}
					}

					// Recorrer ventas de compañía para generar comprobantes
					foreach ($cia['ventas'] as $dia => $venta)
					{
						// Fecha de emisión
						$fecha = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia, $anio_corte));
						// Hora de emisión, por omisión 22:00:00 horas
						$hora = '22:00:00';

						// Subtotal de factura, descontar IVA en caso de gravar este impuesto
						$subtotal = round($venta / (1 + $cia['aplicar_iva'] / 100), 2);
						// Calcular iva en caso de gravar este impuesto
						$iva = $venta - $subtotal;
						// Calcular total de factura
						$total = $venta;

						$tpl->newBlock('row');

						$tpl->assign('dia', $dia);
						$tpl->assign('efectivo', $cia['efectivos'][$dia] != 0 ? number_format($cia['efectivos'][$dia], 2) : '&nbsp;');
						$tpl->assign('clientes', $cia['clientes'][$dia] != 0 ? number_format($cia['clientes'][$dia], 2) : '&nbsp;');
						$tpl->assign('venta', $venta != 0 ? number_format($venta, 2) : '&nbsp;');

						// Si existe un error general en la compañía, ya no generar comprobantes y pasar a la siguiente compañía
						if ( ! $cia['facturable'])
						{
							$tpl->assign('estatus', '<span class="red">Error en diferencia</span>');
						}
						// Si existe un error general en la compañía, ya no generar comprobantes y pasar a la siguiente compañía
						else if ( ! $cia['facturable'] || ! $status_emisor)
						{
							$tpl->assign('estatus', '<span class="red">Error en d&iacute;as anteriores</span>');
						}
						// No se pueden generar comprobantes con importe de venta negativo
						else if ($venta < 0)
						{
							$tpl->assign('estatus', '<span class="red">El importe de la factura para este d&iacute;a no puede ser negativo</span>');

							$status_emisor = FALSE;
						}
						// Ya se ha facturado el día con anterioridad
						else if ($cia['facturado'][$dia])
						{
							$tpl->assign('estatus', '<span class="orange">D&iacute;a ya facturado</span>');
						}
						// Comprobante de venta con importe 0, solo se guarda registro como referencia
						else if ($venta == 0)
						{
							$tpl->assign('estatus', '<span class="green">No se gener&oacute; factura para este d&iacute;a</span>');

							$db->query("INSERT INTO facturas_electronicas (
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
							) VALUES (
								{$num_cia},
								'{$fecha}',
								'{$hora}',
								1,
								0,
								1,
								1,
								'PUBLICO EN GENERAL',
								'XAXX010101000',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								0,
								0,
								0,
								{$_SESSION['iduser']},
								'{$fecha}'
							)");
						}
						// Generar comprobante
						else
						{
							// Construir arreglo de datos para OpenBravo
							$ob_data = array(
								'cabecera' => array (
									'num_cia'				=> $num_cia,
									'clasificacion'			=> 1,
									'fecha'					=> $fecha,
									'hora'					=> $hora,
									'clave_cliente'			=> 1,
									'nombre_cliente'		=> 'PUBLICO EN GENERAL',
									'rfc_cliente'			=> 'XAXX010101000',
									'calle'					=> '',
									'no_exterior'			=> '',
									'no_interior'			=> '',
									'colonia'				=> '',
									'localidad'				=> '',
									'referencia'			=> '',
									'municipio'				=> '',
									'estado'				=> $cia['estado'],
									'pais'					=> 'MEXICO',
									'codigo_postal'			=> '',
									'email'					=> '',
									'observaciones'			=> /*$data->sustituye > 0 ? ' (SUSTITUYE A LA FACTURA ' . $data->sustituye . ')' : ''*/'',
									'importe'				=> $subtotal,
									'porcentaje_descuento'	=> 0,
									'descuento'				=> 0,
									'porcentaje_iva'		=> $cia['aplicar_iva'],
									'importe_iva'			=> $iva,
									'aplicar_retenciones'	=> 'N',
									'importe_retencion_isr'	=> 0,
									'importe_retencion_iva'	=> 0,
									'total'					=> $total
								),
								'consignatario' => array (
									'nombre'		=> '',
									'rfc'			=> '',
									'calle'			=> '',
									'no_exterior'	=> '',
									'no_interior'	=> '',
									'colonia'		=> '',
									'localidad'		=> '',
									'referencia'	=> '',
									'municipio'		=> '',
									'estado'		=> '',
									'pais'			=> '',
									'codigo_postal'	=> ''
								),
								'detalle' => array(
									array (
										'clave'				=> 1,
										'descripcion'		=> "VENTA DEL DIA {$fecha}",
										'cantidad'			=> 1,
										'unidad'			=> 'NO APLICA',
										'precio'			=> $subtotal,
										'importe'			=> $subtotal,
										'descuento'			=> 0,
										'porcentaje_iva'	=> $cia['aplicar_iva'],
										'importe_iva'		=> $iva,
										'numero_pedimento'	=> '',
										'fecha_entrada'		=> '',
										'aduana_entrada'	=> ''
									)
								)
							);

							// Obtener folio reservado para el día de emisión de comprobante
							$folio_reservado = $fac->recuperarFolio($num_cia, 1, $fecha);

							// Timbrar comprobante, en caso de error, obtener último mensaje
							if (($status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $ob_data, $folio_reservado)) < 0)
							{
								$tpl->assign('estatus', '<span class="red">' . $fac->ultimoError() . '</span>');

								$status_emisor = FALSE;
							}
							else
							{
								$pieces = explode('-', $status);
								$folio = $pieces[1];

								$tpl->assign('estatus', '<span class="green">' . $folio . '</span>');

								if ($folio_reservado > 0)
								{
									$fac->utilizarFolio($_SESSION['iduser'], $num_cia, 1, $folio_reservado);
								}
							}
						}
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaAutomatico.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
