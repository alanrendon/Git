<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$options = getopt("", array(
	'fecha_corte:',
	'dif_max:',
	'iduser:',
	'cias:',
	'omitir:',
	'admin:',
	'help'
));

if (isset($_REQUEST['help']) || isset($options['help']))
{
	echo "FacturadorVentaDiariaAutomatico.php Ver 2.15.9.8";
	echo "\nCopyright (c) 2016, Lecaroz";
	echo "\n\nModo de empleo: php FacturadorVentaDiariaAutomatico.php [OPCIONES]";
	echo "\nGenera los datos de contabilidad para pagos de compras y gastos.";
	echo "\n\nLos argumentos obligatorios para las opciones largas son también obligatorios\npara las opciones cortas.";
	echo "\n\n  --help\t\tmuestra esta ayuda y finaliza";
	echo "\n\n  --fecha_corte=FECHA1\tfecha de corte de facturación";
	echo "\n\n  --dif_max=IMPORTE\timporte máximo de diferencia en efectivos";
	echo "\n\n  --iduser=ID\t\tid de usuario";
	echo "\n\n  --cias=CIAS\t\tcompañías, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros";
	echo "\n\n  --omitir=PROS\t\tcompañías a omitir, separados por comas (1,2,3,...) o\n\t\t\trangos (1-5,20-26,...) de búsqueda de registros";
	echo "\n\n  --admin=ID\t\tid de administrador";
	echo "\n\nComunicar de errores en el script a carlos.candelario@lecaroz.com";
	echo "\n\n";

	die;
}

echo "\n(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Parámetros de búsqueda";

if (isset($options['fecha_corte']) || isset($_REQUEST['fecha_corte']))
{
	$params['fecha_corte'] = isset($options['fecha_corte']) ? $options['fecha_corte'] : $_REQUEST['fecha_corte'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) fecha_corte={$params['fecha_corte']}";
}

if (isset($options['dif_max']) || isset($_REQUEST['dif_max']))
{
	$params['diferencia_maxima'] = isset($options['dif_max']) ? $options['dif_max'] : $_REQUEST['dif_max'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) diferencia_maxima={$params['diferencia_maxima']}";
}

if (isset($options['iduser']) || isset($_REQUEST['iduser']))
{
	$params['iduser'] = isset($options['iduser']) ? $options['iduser'] : $_REQUEST['iduser'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) diferencia_maxima={$params['diferencia_maxima']}";
}

if (isset($options['cias']) || isset($_REQUEST['cias']))
{
	$params['cias'] = isset($options['cias']) ? $options['cias'] : $_REQUEST['cias'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) cias={$params['cias']}";
}

if (isset($options['omitir']) || isset($_REQUEST['omitir']))
{
	$params['omitir'] = isset($options['omitir']) ? $options['omitir'] : $_REQUEST['omitir'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) omitir={$params['omitir']}";
}

if (isset($options['admin']) || isset($_REQUEST['admin']))
{
	$params['admin'] = isset($options['admin']) ? $options['admin'] : $_REQUEST['admin'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) admin={$params['admin']}";
}

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

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Conectando a la base de datos";

$db = new DBclass($dsn, 'autocommit=yes');

function actualizar_mensaje($mensaje)
{
	global $db;

	$db->query("DELETE FROM facturas_electronicas_mensajes");

	if (trim($mensaje) != '')
	{
		$db->query("INSERT INTO facturas_electronicas_mensajes (mensaje) VALUES ('{$mensaje}')");
	}
}

if ($db->query("SELECT * FROM facturas_electronicas_status_automatico"))
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Proceso bloqueado, terminado";

	return -1;
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Bloqueando proceso";

$db->query("INSERT INTO facturas_electronicas_status_automatico (tipo) VALUES (1)");

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Construyendo enunciado de búsqueda";
actualizar_mensaje('Construyendo enunciado de búsqueda');

$diferencia_maxima = get_val($params['diferencia_maxima']);

list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $params['fecha_corte']));

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

$condiciones1 = array();

$condiciones1[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

$condiciones1[] = "ec.cod_mov IN (1, 16, 44, 99)";

$condiciones1[] = "ec.num_cia BETWEEN 1 AND 899";

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
		$condiciones1[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
	}
}

if (isset($params['omitir']) && trim($params['omitir']) != '')
{
	$omitir = array();

	$pieces = explode(',', $params['omitir']);
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

if (isset($params['admin']) && $params['admin'] > 0)
{
	$condiciones2[] = 'cc.idadministrador = ' . $params['admin'];
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Borrando efectivos directos repetidos.";

$db->query("DELETE
FROM
	importe_efectivos
WHERE
	fecha BETWEEN '{$fecha1}'AND '{$fecha2}'
	AND id NOT IN (
		SELECT
			MAX(id)
		FROM
			importe_efectivos
		WHERE
			fecha BETWEEN '{$fecha1}'AND '{$fecha2}'
		GROUP BY
			num_cia,
			fecha
	)");

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Ejecutando consulta para obtener compañías";

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
	CASE
		WHEN cc.tipo_cia = 1 THEN
			COALESCE((
				SELECT
					SUM(COALESCE(efectivo, importe))
				FROM
					total_panaderias
					FULL JOIN importe_efectivos USING (num_cia, fecha)
				WHERE
					num_cia = result.num_cia
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
			), 0)
		WHEN cc.tipo_cia = 2 THEN
			COALESCE((
				SELECT
					SUM(COALESCE(efectivo, importe))
				FROM
					total_companias
					FULL JOIN importe_efectivos USING (num_cia, fecha)
				WHERE
					num_cia = result.num_cia
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
			), 0)
		ELSE
			0
	END AS efectivo,
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
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No hay resultados, terminado proceso";
	actualizar_mensaje('');

	$db->query("DELETE FROM facturas_electronicas_status_automatico");

	return -1;
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Incluir librería de facturación";

include(dirname(__FILE__) . '/includes/class.facturas.v3.inc.php');

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Creando instancia de FacturasClass";

// Crear instancia de FacturasClass
$fac = new FacturasClass();

// En caso de error, terminar
if ($fac->ultimoCodigoError() < 0)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Error en el último intento de facturación, terminando proceso";
	actualizar_mensaje('');

	$db->query("DELETE FROM facturas_electronicas_status_automatico");

	return -1;
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Recorriendo compañías";

// Recorrer compañías
foreach ($result as $cia)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Compañía: {$cia['num_cia']} {$cia['nombre_cia']}";
	actualizar_mensaje("Compañía: {$cia['num_cia']} {$cia['nombre_cia']}\n\nRecuperando información y construyendo arreglo de datos");

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Recuperando información y contruyendo arreglo de datos";

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
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Recuperando información de sucursales y construyendo arreglo de datos";

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
			actualizar_mensaje("Compañía: {$sucursal['num_cia']} {$sucursal['nombre_cia']}\n\nRecuperando información y construyendo arreglo de datos");

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
					$datos[$sucursal['num_cia']]['facturado'][$f['dia']] = TRUE;
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

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Iniciando proceso de timbrado";

	// Iniciar proceso para generar comprobantes
	foreach ($datos as $num_cia => $cia)
	{
		$status_emisor = TRUE;

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Compañía: {$num_cia} {$cia['nombre_cia']}";

		// $tpl->assign('efectivo', number_format(array_sum($cia['efectivos']), 2));
		// $tpl->assign('clientes', number_format(array_sum($cia['clientes']), 2));
		// $tpl->assign('venta', number_format(array_sum($cia['ventas']), 2));

		// Si la compañía no es facturable, poner fila con observaciones de lo ocurrido
		if ( ! $cia['facturable'])
		{
			if (abs($cia['diferencia']) > $diferencia_maxima)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) La compañía no es facturable, tiene una diferencia en efectivos de " . number_format($cia['diferencia'], 2);
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) La compañía tiene errores y no puede ser facturada";
			}
		}

		// Recorrer ventas de compañía para generar comprobantes
		foreach ($cia['ventas'] as $dia => $venta)
		{
			// Fecha de emisión
			$fecha = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia, $anio_corte));
			// Hora de emisión, por omisión 22:00:00 horas
			$hora = '22:00:00';

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Facturando día: {$fecha}";
			actualizar_mensaje("Compañía: {$cia['num_cia']} {$cia['nombre_cia']}\n\nFacturando día: {$fecha}");

			// Subtotal de factura, descontar IVA en caso de gravar este impuesto
			$subtotal = round($venta / (1 + $cia['aplicar_iva'] / 100), 2);
			// Calcular iva en caso de gravar este impuesto
			$iva = $venta - $subtotal;
			// Calcular total de factura
			$total = $venta;

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Efectivo: " . number_format($cia['efectivos'][$dia], 2);
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Clientes: " . number_format($cia['clientes'][$dia], 2);
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Venta: " . number_format($venta, 2);

			// Si existe un error general en la compañía, ya no generar comprobantes y pasar a la siguiente compañía
			if ( ! $cia['facturable'])
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Error en diferencia";
			}
			// Si existe un error general en la compañía, ya no generar comprobantes y pasar a la siguiente compañía
			else if ( ! $cia['facturable'] || ! $status_emisor)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Error en días anteriores";
			}
			// No se pueden generar comprobantes con importe de venta negativo
			else if ($venta < 0)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) El importe de la factura para este día no puede ser negativo";

				$status_emisor = FALSE;
			}
			// Ya se ha facturado el día con anterioridad
			else if ($cia['facturado'][$dia])
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Día ya facturado";
			}
			// Comprobante de venta con importe 0, solo se guarda registro como referencia
			else if ($venta == 0)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se generó factura para este día";

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
					{$params['iduser']},
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

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Timbrando comprobante";

				// Timbrar comprobante, en caso de error, obtener último mensaje
				if (($status = $fac->generarFactura($params['iduser'], $num_cia, 1, $ob_data, $folio_reservado)) < 0)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$fac->ultimoError()}";

					$status_emisor = FALSE;
				}
				else
				{
					$pieces = explode('-', $status);
					$folio = $pieces[1];

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Folio generado: {$folio}";

					if ($folio_reservado > 0)
					{
						$fac->utilizarFolio($params['iduser'], $num_cia, 1, $folio_reservado);
					}
				}
			}
		}
	}
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Proceso terminado";
actualizar_mensaje('');

$db->query("DELETE FROM facturas_electronicas_status_automatico");

echo "\n\n";
