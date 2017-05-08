<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

if ( ! isset($_REQUEST['anio']) || ! isset($_REQUEST['mes']))
{
	die(-1);
}

$db = new DBclass($dsn, 'autocommit=yes');

$fecha1 = date('d-m-Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
$fecha2 = date('d-m-Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

$fecha = date('Y-m-d', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

$condiciones = array();

$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

$condiciones[] = "codmp = 90";

$condiciones[] = "tipo_mov = FALSE";

$condiciones[] = "descripcion LIKE 'TRASPASO GAS%'";

if (isset($_REQUEST['traspasa']) && isset($_REQUEST['recibe']) && $_REQUEST['traspasa'] > 0 && $_REQUEST['recibe'] > 0)
{
	$condiciones[] = "num_cia IN ({$_REQUEST['traspasa']}, {$_REQUEST['recibe']})";
}

// Validar que no se hayan generado antes las distribuciones de gas
$sql = "SELECT
	id
FROM
	mov_inv_real
WHERE
	" . implode(' AND ', $condiciones) . "
LIMIT 1";

if ( ! $db->query($sql))
{
	$condiciones = array();

	if (isset($_REQUEST['traspasa']) && isset($_REQUEST['recibe']) && $_REQUEST['traspasa'] > 0 && $_REQUEST['recibe'] > 0)
	{
		$condiciones[] = "(dg.num_cia = {$_REQUEST['traspasa']} AND dg.ros = {$_REQUEST['recibe']})";
	}

	$result = $db->query("SELECT
			dg.num_cia,
			cc1.tipo_cia,
			dg.ros,
			cc2.tipo_cia AS tipo_ros
		FROM
			distribucion_gas dg
			LEFT JOIN catalogo_companias cc1
				ON (cc1.num_cia = dg.num_cia)
			LEFT JOIN catalogo_companias cc2
				ON (cc2.num_cia = dg.ros)
		" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
		ORDER BY
			dg.num_cia,
			dg.ros");

	$sql = '';

	if ($result)
	{
		foreach ($result as $row) {
			if ($row['tipo_cia'] == 1 && $row['tipo_ros'] == 2)
			{
				$traspaso = $db->query("SELECT
					num_cia,
					pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox
						AS costo_gas,
					pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas
						AS cantidad_gas,
					precio_gas
				FROM
					(SELECT
						num_cia,
						pollos_vendidos,
						(
							SELECT
								num_cia
							FROM
								(
									SELECT
										mvr.num_cia,
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC
								) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS num_cia_aprox,
						(
							SELECT
								pollos_vendidos
							FROM
								(
									SELECT
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS pollos_vendidos_aprox,
						(
							SELECT
								consumo_gas
							FROM
								(
									SELECT
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC
								) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS costo_gas_aprox,
						precio_gas
					FROM
						(
							SELECT
								dg.ros
									AS num_cia,
								ROUND(SUM(cantidad)::NUMERIC, 2)
									AS pollos_vendidos,
								COALESCE((
									SELECT
										precio_unidad
									FROM
										mov_inv_real
									WHERE
										num_cia = dg.num_cia
										AND codmp = 90
										AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND tipo_mov = FALSE
									ORDER BY
										fecha DESC
									LIMIT
										1
								), (
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = dg.num_cia
										AND codmp = 90
										AND fecha = '{$fecha1}'::DATE - INTERVAL '1 DAY'
										AND precio_unidad > 0
								), NULL)
									AS precio_gas
							FROM
								mov_inv_real mvr
								LEFT JOIN distribucion_gas dg
									ON (mvr.num_cia = dg.ros)
							WHERE
								dg.num_cia = {$row['num_cia']}
								AND dg.ros = {$row['ros']}
								AND mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
								AND mvr.codmp IN (160, 334, 700, 600, 573)
								AND mvr.tipo_mov = TRUE
								AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
							GROUP BY
								dg.ros,
								dg.num_cia
							HAVING
								SUM(cantidad) > 0
						) AS result_pollos_vendidos) AS result_general");
			}
			else if ($row['tipo_cia'] == 1 && $row['tipo_ros'] == 1)
			{
				$traspaso = $db->query("SELECT
					dg.ros
						AS num_cia,
					diferencia * precio_unidad
						AS costo_gas,
					diferencia
						AS cantidad_gas,
					precio_unidad
						AS precio_gas
				FROM
					inventario_fin_mes ifm
					LEFT JOIN distribucion_gas dg
						ON (dg.num_cia = ifm.num_cia)
				WHERE
					dg.num_cia = {$row['num_cia']}
					AND dg.ros = {$row['ros']}
					AND ifm.fecha = '{$fecha2}'
					AND ifm.codmp = 90
					AND diferencia > 0
				LIMIT
					1");
			}
			else if ($row['tipo_cia'] == 2 && $row['tipo_ros'] == 1)
			{
				$traspaso = $db->query("SELECT
					num_cia,
					COALESCE((
						SELECT
							existencia * precio_unidad
						FROM
							historico_inventario
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha = '{$fecha1}'::DATE - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(total_mov)
						FROM
							mov_inv_real
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND tipo_mov = FALSE
					), 0) - COALESCE((
						SELECT
							inventario * precio_unidad
						FROM
							inventario_fin_mes
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha = '{$fecha2}'
						LIMIT
							1
					), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox)
						AS costo_gas,
					COALESCE((
						SELECT
							existencia
						FROM
							historico_inventario
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha = '{$fecha1}'::DATE - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(cantidad)
						FROM
							mov_inv_real
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND tipo_mov = FALSE
					), 0) - COALESCE((
						SELECT
							inventario
						FROM
							inventario_fin_mes
						WHERE
							num_cia = result_general.num_cia_traspasa
							AND codmp = 90
							AND fecha = '{$fecha2}'
						LIMIT
							1
					), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas)
						AS cantidad_gas,
					precio_gas
				FROM
					(SELECT
						num_cia,
						num_cia_traspasa,
						pollos_vendidos,
						(
							SELECT
								num_cia
							FROM
								(
									SELECT
										mvr.num_cia,
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC
								) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS num_cia_aprox,
						(
							SELECT
								pollos_vendidos
							FROM
								(
									SELECT
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS pollos_vendidos_aprox,
						(
							SELECT
								consumo_gas
							FROM
								(
									SELECT
										SUM(cantidad)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												SUM(importe)
											FROM
												movimiento_gastos
											WHERE
												num_cia = mvr.num_cia
												AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
												AND codgastos = 90
										), (
											SELECT
												SUM(diferencia * precio_unidad)
											FROM
												inventario_fin_mes
											WHERE
												num_cia = mvr.num_cia
												AND fecha = '{$fecha2}'
												AND codmp = 90
										), 0)
										AS consumo_gas
									FROM
										mov_inv_real mvr
									WHERE
										mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND num_cia NOT IN (
											SELECT
												ros
											FROM
												distribucion_gas
										)
										AND num_cia NOT IN (
											SELECT
												num_cia
											FROM
												distribucion_gas
										)
									GROUP BY
										num_cia
									ORDER BY
										pollos_vendidos DESC
								) AS tabuladores
							WHERE
								consumo_gas > 0
								AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
							LIMIT 1
						)
							AS costo_gas_aprox,
						precio_gas
					FROM
						(
							SELECT
								dg.ros
									AS num_cia,
								mvr.num_cia
									AS num_cia_traspasa,
								ROUND(SUM(cantidad)::NUMERIC, 2)
									AS pollos_vendidos,
								COALESCE((
									SELECT
										precio_unidad
									FROM
										mov_inv_real
									WHERE
										num_cia = mvr.num_cia
										AND codmp = 90
										AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
										AND tipo_mov = FALSE
									ORDER BY
										fecha DESC
									LIMIT
										1
								), (
									SELECT
										precio_unidad
									FROM
										historico_inventario
									WHERE
										num_cia = mvr.num_cia
										AND codmp = 90
										AND fecha = '{$fecha1}'::DATE - INTERVAL '1 DAY'
										AND precio_unidad > 0
								), NULL)
									AS precio_gas
							FROM
								mov_inv_real mvr
								LEFT JOIN distribucion_gas dg
									ON (mvr.num_cia = dg.num_cia)
							WHERE
								mvr.num_cia = {$row['num_cia']}
								AND mvr.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
								AND mvr.codmp IN (160, 334, 700, 600, 573)
								AND mvr.tipo_mov = TRUE
							GROUP BY
								mvr.num_cia,
								dg.ros
							HAVING
								SUM(cantidad) > 0
						) AS result_pollos_vendidos) AS result_general");
			}

			if ($traspaso)
			{
				// No traspasar gas si la cantidad de gas es menor o igual a cero
				if ($traspaso[0]['cantidad_gas'] <= 0)
				{
					continue;
				}

				// Ingresar entrada negativa en compañia que traspasa
				$sql .= "INSERT INTO
					mov_inv_real (
						num_cia,
						codmp,
						fecha,
						tipo_mov,
						cantidad,
						precio,
						total_mov,
						precio_unidad,
						descripcion
					) VALUES (
						{$row['num_cia']},
						90,
						'{$fecha2}',
						FALSE,
						-{$traspaso[0]['cantidad_gas']},
						{$traspaso[0]['precio_gas']},
						-{$traspaso[0]['costo_gas']},
						{$traspaso[0]['precio_gas']},
						'TRASPASO GAS (CIA {$row['ros']})'
					);";

				// Ingresar entrada positiva en rosticeria
				$sql .= "INSERT INTO
					mov_inv_real (
						num_cia,
						codmp,
						fecha,
						tipo_mov,
						cantidad,
						precio,
						total_mov,
						precio_unidad,
						descripcion
					) VALUES (
						{$row['ros']},
						90,
						'{$fecha2}',
						FALSE,
						{$traspaso[0]['cantidad_gas']},
						{$traspaso[0]['precio_gas']},
						{$traspaso[0]['costo_gas']},
						{$traspaso[0]['precio_gas']},
						'TRASPASO GAS (CIA {$row['num_cia']})'
					);";

				// Actualizar inventario de panaderia
				$sql .= "UPDATE
					inventario_real
				SET
					existencia = existencia - {$traspaso[0]['cantidad_gas']},
					precio_unidad = {$traspaso[0]['precio_gas']}
				WHERE
					num_cia = {$row['num_cia']}
					AND codmp = 90;";

				// Actualizar inventario de rosticeria
				$sql .= "UPDATE
					inventario_real
				SET
					existencia = existencia + {$traspaso[0]['cantidad_gas']},
					precio_unidad = {$traspaso[0]['precio_gas']}
				WHERE
					num_cia = {$row['ros']}
					AND codmp = 90;";
			}
		}

		echo "<pre>$sql</pre>";

		// $db->query($sql);
	}
	else
	{
		echo "No hay gastos para desglosar.";

		die(-3);
	}
}
else
{
	echo "Ya han sido generado los gastos.";

	die(-2);
}

