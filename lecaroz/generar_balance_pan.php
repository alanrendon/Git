<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$sql = '
	SELECT
		nivel
	FROM
		balances_aut
	WHERE
		iduser = ' . $_SESSION['iduser'] . '
';
$nivel = $db->query($sql);

if (!$nivel || $nivel[0]['nivel'] == 0 || $nivel[0]['nivel'] == 1) {
	die('NO TIENE AUTORIZACION PARA GENERAR BALANCES.');
}

if ($_SESSION['iduser'] != 1 && $_REQUEST['anyo'] < 2016)
{
	echo "NO ES POSIBLE GENERAR BALANCES.";
	die;
}

$mes = $_GET['mes'];
$anyo = $_GET['anyo'];

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));

$fecha_his = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo));

$dias = date('d', mktime(0, 0, 0, $mes + 1, 0, $anyo));

$condiciones = array();
$condiciones[] = 'tipo_cia = 1';
$condiciones[] = 'num_cia IN (SELECT num_cia FROM total_panaderias WHERE fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' GROUP BY num_cia)';

/*
@ Intervalo de compañías
*/
if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
	$cias = array();

	$pieces = explode(',', $_REQUEST['cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$cias[] = implode(', ', range($exp[0], $exp[1]));
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

$sql = '
	SELECT
		num_cia
	FROM
		catalogo_companias
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia';
$cias = $db->query($sql);

if (!$cias) die('NO HAY RESULTADOS');

$sql = '
	SELECT
		num_cia,
		nombre_corto
	FROM
		prueba_pan pp
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
	WHERE
		fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
		AND num_cia IN (
			SELECT
				num_cia
			FROM
				total_panaderias
			WHERE
				fecha = \'' . $fecha2 . '\'
			GROUP BY
				num_cia
		)
		AND num_cia NOT IN (
			SELECT
				num_cia
			FROM
				prueba_pan
			WHERE
				fecha = \'' . $fecha2 . '\'
		)
	GROUP BY
		num_cia,
		nombre_corto
	ORDER BY
		num_cia
';

$pan_contado = $db->query($sql);

if ($pan_contado) {
	$error = 'No se pueden generar balances porque las siguientes compa&ntilde;&iacute;as no tienen capturado pan contado al d&iacute;a ' . $fecha2 . ':<br /><br /><table>';

	foreach ($pan_contado as $p) {
		$error .= '<tr><td>' . $p['num_cia'] . '</td><td>' . $p['nombre_corto'] . '</tr></tr>';
	}

	$error .= '</table>';

	echo $error;
	die;
}

if ( ! $db->query("SELECT * FROM movimiento_gastos WHERE fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND codgastos IN (179, 180, 181, 187)")) {
	$error = 'No se pueden generar balances debido a que los contadores no han terminado de capturar los impuestos';

	echo $error;
	die;
}

function buscar_fal($array, $dia) {
	if (!$array)
		return 0;

	foreach ($array as $dato)
		if ($dato['dia'] == $dia || ($dia == 0 && $dato['dia'] == '0'))
			return $dato['dato'];

	return 0;
}
//
//function buscar_fac($mov, $dif, $costo_dif, $index, $num_cia, $codmp, $unidades) {
//	$cantidad = 0;
//	$total = 0;
//
//	$existencia = $unidades;
//
//	// Buscar en las facturas
//	for ($i = $index + 1; $i < count($mov); $i++)
//		if ($mov[$i]['tipo_mov'] == 'f' && $mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp) {
//			$cantidad += $mov[$i]['cantidad'];
//			$total += $mov[$i]['total_mov'];
//
//			$existencia += $mov[$i]['cantidad'];
//
//			if ($existencia >= 0)
//				return $total / $cantidad;
//		}
//		else if ($mov[$i]['tipo_mov'] == 't' && $mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp)
//			$existencia -= $mov[$i]['cantidad'];
//
//	// Buscar en las diferencias
//	if ($dif)
//		for ($i = 0; $i < count($dif); $i++)
//			if ($dif[$i]['tipo_mov'] == 'f' && $dif[$i]['num_cia'] == $num_cia && $dif[$i]['codmp'] == $codmp) {
//				$cantidad += $dif[$i]['cantidad'];
//				$total += $dif[$i]['cantidad'] * $costo_dif;
//
//				$existencia += $dif[$i]['cantidad'];
//
//				if ($existencia >= 0)
//					return $total / $cantidad;
//			}
//			else if ($dif[$i]['tipo_mov'] == 't' && $dif[$i]['num_cia'] == $num_cia && $dif[$i]['codmp'] == $codmp)
//				$existencia -= $dif[$i]['cantidad'];
//
//	return FALSE;
//}
//
//function buscar_dif($mov, $num_cia, $codmp) {
//	for ($i = 0; $i < count($mov); $i++)
//		if ($mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp)
//			return $i;
//
//	return FALSE;
//}
//
//function costo_dif($mov, $num_cia, $codmp, $costo_promedio) {
//	if (!$mov)
//		return $costo_promedio;
//
//	$cantidad = 0;
//	$valor = 0;
//	for ($i = 0; $i < count($mov); $i++)
//		if ($mov[$i]['num_cia'] == $num_cia && $mov[$i]['codmp'] == $codmp && $mov[$i]['tipo_mov'] == 'f') {
//			$cantidad += $mov[$i]['cantidad'];
//			$valor += $mov[$i]['total_mov'];
//		}
//
//	return $cantidad > 0 ? $valor / $cantidad : $costo_promedio;
//}

function balance($num_cia) {
	$sql_balance = '';

	global $db, $mes, $anyo, $fecha1, $fecha2, $fecha_his, $dias;

	// Validar que se hayan actualizado los encargados
	if (!$db->query("SELECT id FROM encargados WHERE num_cia = $num_cia AND anio = $anyo AND mes = $mes LIMIT 1"))
		$sql_balance .= "INSERT INTO encargados (num_cia, nombre_inicio, nombre_fin, anio, mes) SELECT num_cia, nombre_inicio, nombre_fin, $anyo, $mes FROM encargados WHERE anio = " . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . ' AND mes = ' . date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . ";\n";

	$sql_balance .= "DELETE FROM balances_pan WHERE num_cia = $num_cia AND anio = $anyo AND mes = $mes;\n";
	$sql_balance .= "DELETE FROM historico WHERE num_cia = $num_cia AND anio = $anyo AND mes = $mes;\n";
	$sql_balance .= "DELETE FROM consumo_produccion WHERE num_cia = $num_cia AND anio = $anyo AND mes = $mes;\n";

	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

	/**** BARREDURA ****/
	$sql = "SELECT sum(importe) FROM barredura WHERE num_cia = $num_cia AND fecha_pago BETWEEN '$fecha1' AND '$fecha2'";
	$barredura = $db->query($sql);

	/**** PASTILLAJE ****/
	$sql = "SELECT sum(pastillaje) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$pastillaje = $db->query($sql);

	/**** ABONO EMPLEADOS ****/
	$sql = "SELECT sum(importe) FROM prestamos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
	$abono_empleados = $db->query($sql);

	/**** OTROS ****/
	$sql = "SELECT sum(otros) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$otros = $db->query($sql);
	$otros[0]['sum'] = $otros[0]['sum'] - $abono_empleados[0]['sum'] - $barredura[0]['sum'];

	/**** TOTAL OTROS ****/
	$total_otros = $pastillaje[0]['sum'] + $otros[0]['sum'] + $barredura[0]['sum'] + $abono_empleados[0]['sum'];

	/**** ABONO REPARTO ****/
	$sql = "SELECT sum(abono) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$abono_reparto = $db->query($sql);

	/**** ERRORES ****/
	$sql = "SELECT sum(am_error + pm_error) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'";
	$errores = $db->query($sql);

	/**** VENTA EN PUERTA ****/
	$sql = "SELECT sum(venta_puerta) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$venta_puerta = $db->query($sql);
	$venta_puerta[0]['sum'] = $venta_puerta[0]['sum'] + $errores[0]['sum'];

	/**** VENTAS NETAS ****/
	$ventas_netas = $venta_puerta[0]['sum'] + $total_otros + $abono_reparto[0]['sum'] - $errores[0]['sum'];

	// [21-oct-2010] Obtener pasteles y kilos pedidos en el mes
	$sql = '
		SELECT
			SUM((COALESCE(cuenta, 0) - COALESCE(base, 0) - COALESCE(pastillaje, 0) - COALESCE(otros_efectivos, 0)) + COALESCE(resta, 0))
				AS
					pastel_pedido,
			SUM(COALESCE(kilos, 0))
				AS
					kilos
			FROM
				venta_pastel
			WHERE
					num_cia = ' . $num_cia . '
				AND
					fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND
					estado <> 2
				AND
					tipo
						IN
							(0, 1)
				AND
					(
							COALESCE(kilos, 0) > 0
						OR
							COALESCE(resta, 0) > 0
					)
			AND
				(num_cia, letra_folio, num_remi)
					NOT IN
						(
							SELECT
								num_cia,
								letra_folio,
								num_remi
							FROM
								venta_pastel
							WHERE
									num_cia = ' . $num_cia . '
								AND
									estado <> 2
								AND
									tipo = 0
								AND
									COALESCE(kilos, 0) = 0
								AND
									COALESCE(otros, 0) > 0
						)
	';
	$pasteles = $db->query($sql);

	$sql = '
		SELECT
			SUM((COALESCE(cuenta, 0) - COALESCE(base, 0) - COALESCE(pastillaje, 0) - COALESCE(otros_efectivos, 0)) + COALESCE(resta, 0))
				AS
					pan_pedido
			FROM
				venta_pastel
			WHERE
					num_cia = ' . $num_cia . '
				AND
					fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND
					estado <> 2
				AND
					tipo IN (0, 1)
				AND
					(
							(
								COALESCE(kilos, 0) = 0 AND COALESCE(cuenta, 0) > 0
							)
						OR
							COALESCE(resta, 0) > 0
					)
				AND
					(num_cia, letra_folio, num_remi)
						NOT IN
							(
								SELECT
									num_cia,
									letra_folio,
									num_remi
								FROM
									venta_pastel
								WHERE
										num_cia = ' . $num_cia . '
									AND
										estado <> 2
									AND
										tipo = 0
									AND
										COALESCE(kilos, 0) > 0
							)
	';
	$pan = $db->query($sql);

	$pastel_vitrina = 0;
	$pastel_pedido = 0;
	$pastel_kilos = 0;
	$pan_pedido = 0;
	if ($pasteles) {
		$pastel_pedido = $pasteles[0]['pastel_pedido'] != 0 ? $pasteles[0]['pastel_pedido'] : '0';
		$pastel_kilos = $pasteles[0]['kilos'] != 0 ? $pasteles[0]['kilos'] : '0';
	}

	if ($pan) {
		$pan_pedido = $pan[0]['pan_pedido'] != 0 ? $pan[0]['pan_pedido'] : '0';
	}

	/*
	@
	@@ COSTO DE PRODUCCION
	@
	*/

	/*
	@ Obtener costos y consumos
	*/
	$aux = new AuxInvClass($num_cia, $anyo, $mes, NULL, 'real');

	/*
	@ Inventario anterior
	*/
	$inv_ant = 0;
	$inv_act = 0;
	foreach ($aux->mps as $cod => $mp) {
		if ($cod != 90) {
			$inv_ant += $mp['costo_ini'];
			$inv_act += $mp['costo'];
		}
	}

	/*
	@ Obtener produccion y ordernalo por turnos
	*/
	$sql = '
		SELECT
			codturno
				AS
					cod_turno,
			sum(total_produccion)
				AS
					produccion
		FROM
			total_produccion
		WHERE
				numcia = ' . $num_cia . '
			AND
				fecha_total
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				total_produccion > 0
		GROUP BY
			cod_turno
		ORDER BY
			cod_turno
	';
	$tmp = $db->query($sql);

	$produccion = array(
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0
	);
	if ($tmp) {
		foreach ($tmp as $reg) {
			$produccion[$reg['cod_turno']] += $reg['produccion'];
		}
	}

	/*
	@ Obtener controles de avio y ordenarlos
	*/
	$sql = '
		SELECT
			codmp,
			cod_turno
		FROM
			control_avio
		WHERE
			num_cia = ' . $num_cia . '
		ORDER BY
			codmp,
			cod_turno
	';
	$tmp = $db->query($sql);

	$contro_avio = array();
	if ($tmp) {
		foreach ($tmp as $reg) {
			$control_avio[$reg['codmp']][$reg['cod_turno']] = 0;
		}
	}

	/*
	@ Inicializar total de consumo por turno a cero
	*/
	$total_turnos = array(
		0  => 0,
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0,
	);

	/*
	@ Inicializar total de consumo por turno de productos no controlados a cero
	*/
	$total_turnos_nc = array(
		0  => 0,
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0,
	);

	foreach ($aux->mps as $cod => $mp) {
		/*
		@ Omitir Gas (90)
		*/
		if ($cod == 90) {
			continue;
		}

		/*
		@ Si el producto tuvo consumos, realizar calculos y mostrar en pantalla
		*/
		if (array_sum($aux->consumos[$cod]) != 0) {
			/*
			@ Producto controlado
			*/
			if ($mp['controlado'] == 't') {
				$total_producto[$cod] = 0;
				$consumo_total = 0;
				$diferencia = 0;
				foreach ($aux->consumos[$cod] as $turno => $consumo) {
					/*
					@ Consumo por turno
					*/
					if ($turno > 0) {
						/*
						@ Si hay consumo por diferencia, distribuirla entre los turnos
						*/
						if ($diferencia != 0) {
							/*
							@ Distribuir a partir del porcentaje de los turnos que si tubieron consumo
							*/
							if ($consumo_total > 0) {
								/*
								@                  Consumo por turno * 100
								@ % de consumo = ---------------------------
								@                Consumo total de los turnos
								*/
								$promedio = $consumo * 100 / $consumo_total;
								/*
								@ Sumar porcentaje de la diferencia correspondiente al consumo del turno
								@
								@            Diferencia * % de consumo
								@ Consumo += -------------------------
								@                      100
								*/
								$aux->consumos[$cod][$turno] += $diferencia * $promedio / 100;
							}
							/*
							@ Distribuir a partir del control de consumo por turno y la producción
							*/
							else if (isset($control_avio[$cod][$turno])) {
								/*
								@ Calcular los porcentajes de distribución conforme a la producción del turno
								*/
								if ($produccion_turnos > 0) {
									$control_avio[$cod][$turno] = $produccion[$turno] > 0 ? $produccion[$turno] * 100 / $produccion_turnos : 0;
								}
								/*
								@ Dividir equitativamente el porcentaje
								*/
								else {
									$control_avio[$cod][$turno] = 100 / count($control_avio[$cod]);
								}

								/*
								@ Calcular consumo y sumarlo al turno
								*/
								$aux->consumos[$cod][$turno] += $diferencia * $control_avio[$cod][$turno] / 100;
							}

							/*
							@ Reasignar valor al consumo del turno
							*/
							$consumo = $aux->consumos[$cod][$turno];
						}

						$total_producto[$cod] += $consumo * $mp['precio'];
						$total_turnos[$turno] += $consumo * $mp['precio'];
					}
					/*
					@ Consumo por diferencia
					*/
					else {
						$consumo_total = array_sum($aux->consumos[$cod]) - $consumo;
						$diferencia = $consumo;
						$produccion_turnos = 0;

						/*
						@ En caso de que el producto no tenga consumo por turnos o no tenga control de avio
						@ repartir al repostero, bizcochero o franceceros a partir de su producción
						*/
						if ($consumo_total <= 0 && !isset($control_avio[$cod])) {
							/*
							@ Repartir 100% al repostero (4)
							*/
							if ($produccion[4] > 0) {
								$aux->consumos[$cod][4] += $diferencia;
							}
							/*
							@ Repartir 100% al bizcochero (3)
							*/
							else if ($produccion[3] > 0) {
								$aux->consumos[$cod][3] += $diferencia;
							}
							/*
							@ Repartir 50% al frances de noche y 50% al frances de día
							*/
							else {
								$aux->consumos[$cod][2] += $diferencia / 2;
								$aux->consumos[$cod][2] += $diferencia / 2;
							}
						}
						/*
						@ Calcular el total de producción de los turnos dentro del control de avio
						*/
						else if (isset($control_avio[$cod])) {
							foreach ($control_avio[$cod] as $turno_control => $valor_control) {
								$produccion_turnos += $produccion[$turno_control] > 0 ? $produccion[$turno_control] : 0;
							}
						}
					}
				}
			}
			/*
			@ Producto no controlado
			*/
			else {
				/*
				@ Distribuir a partir del tipo de producto
				*/

				/*
				@ Materia prima
				*/
				if ($mp['tipo'] == 1) {
					$aux->consumos[$cod][1] = $produccion[3] > 0 ? 0.025 * $aux->consumos[$cod][0] : 0.50 * $aux->consumos[$cod][0];
					$aux->consumos[$cod][2] = $produccion[3] > 0 ? 0.025 * $aux->consumos[$cod][0] : 0.50 * $aux->consumos[$cod][0];
					$aux->consumos[$cod][3] = $produccion[4] > 0 ? 0.15 * $aux->consumos[$cod][0] : ($produccion[3] > 0 ? 0.95 * $aux->consumos[$cod][0] : 0);
					$aux->consumos[$cod][4] = $produccion[4] > 0 ? 0.80 * $aux->consumos[$cod][0] : 0;
				}
				/*
				@ Material de empaque
				*/
				else {
					$aux->consumos[$cod][3] = $produccion[4] > 0 ? 0.20 * $aux->consumos[$cod][0] : ($produccion[3] > 0 ? 0.90 * $aux->consumos[$cod][0] : 0);
					$aux->consumos[$cod][4] = $produccion[4] > 0 ? 0.70 * $aux->consumos[$cod][0] : 0;
					$aux->consumos[$cod][10] = $produccion[3] > 0 ? 0.10 * $aux->consumos[$cod][0] : $aux->consumos[$cod][0];
				}

				$total_producto[$cod] = 0;

				/*
				@ Asignar los consumos del producto por turno
				*/
				foreach ($aux->consumos[$cod] as $turno => $consumo) {
					if ($turno > 0) {
						$total_producto[$cod] += $consumo * $mp['precio'];
						$total_turnos_nc[$turno] += $consumo * $mp['precio'];
					}
				}
			}
		}
	}

	/*
	@ Obtener mercancias y ordenarlas por turnos
	@@ [12-Feb-2014] Omitido el código 9 porque ya esta en gastos
	@@ [25-Jul-2016] Omitir los pagos hechos para otras cias e incluir los pagos hechos por otras compañías
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) < mktime(0, 0, 0, 6, 1, 2016))
	{
		$sql = '
			SELECT
				cod_turno,
				sum(importe)
					AS
						importe
			FROM
				movimiento_gastos
			WHERE
					num_cia = ' . $num_cia . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					codgastos
						IN
							(
								' . (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 12, 1, 2016) || in_array($num_cia, array(29, 42, 43, 48, 60, 61, 76)) ? '9,' : '') . '
								23,
								76
							)
			GROUP BY
				cod_turno
			ORDER BY
				cod_turno
		';
	}
	else
	{
		$sql = "SELECT
			cod_turno,
			SUM (importe) AS importe
		FROM
			movimiento_gastos
		WHERE
			num_cia = {$num_cia}
			AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
			AND codgastos IN (" . (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 12, 1, 2016) || in_array($num_cia, array(29, 42, 43, 48, 60, 61, 76)) ? '9, ' : '') . "23, 76)
			AND (num_cia, fecha, folio, importe) NOT IN (
				SELECT
					num_cia,
					fecha,
					folio,
					importe
				FROM
					pagos_otras_cias
					LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
				WHERE
					num_cia = 134
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
					AND fecha_cancelacion IS NULL
			)
		GROUP BY
			cod_turno

		UNION

		SELECT
			0 AS cod_turno,
			SUM (importe) AS importe
		FROM
			pagos_otras_cias
			LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
			LEFT JOIN catalogo_gastos USING (codgastos)
		WHERE
			num_cia_aplica = {$num_cia}
			AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
			AND codgastos IN (" . (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 12, 1, 2016) || in_array($num_cia, array(29, 42, 43, 48, 60, 61, 76)) ? '9, ' : '') . "23, 76)
			AND fecha_cancelacion IS NULL
		GROUP BY
			cod_turno

		ORDER BY
			cod_turno";
	}
	$tmp = $db->query($sql);

	$mercancias = array(
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0
	);
	if ($tmp) {
		foreach ($tmp as $reg) {
			/*
			@ Sumar mercancias al turno
			*/
			if ($reg['cod_turno'] > 0) {
				$mercancias[$reg['cod_turno']] += $reg['importe'];
			}
			/*
			@ Distribuir mercancias sin turno
			*/
			else {
				/*
				@ 60% al bizcochero
				*/
				$mercancias[3] += $reg['importe'] * 0.60;
				/*
				@ 20% al repostero
				*/
				$mercancias[4] += $reg['importe'] * 0.20;
				/*
				@ 20% al gelatinero
				*/
				$mercancias[9] += $reg['importe'] * 0.20;
			}
		}
	}

	/*
	@ [03-Jun-2011] Obtener Avio bocadillos de gastos de caja
	*/
	$sql = '
		SELECT
			SUM(importe)
				AS
					importe
		FROM
			gastos_caja
		WHERE
			num_cia = ' . $num_cia . '
			AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			AND cod_gastos = 154
	';
	$tmp = $db->query($sql);

	if ($mercancias[9] > 0) {
		$mercancias[9] += $tmp[0]['importe'];
	}
	else if ($mercancias[3] > 0) {
		$mercancias[3] += $tmp[0]['importe'];
	}
	else if ($mercancias[4] > 0) {
		$mercancias[4] += $tmp[0]['importe'];
	}

	/*
	@ Calcular y asignar consumo total
	*/
	$consumo_total = array(
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0
	);
	foreach ($consumo_total as $turno => $consumo) {
		$consumo_total[$turno] = $total_turnos[$turno] + $total_turnos_nc[$turno] + $mercancias[$turno];
	}

	/*
	@ Calcular y asignar promedios de consumo entre producción
	*/
	$promedios = array(
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0
	);

	foreach ($promedios as $turno => $promedio) {
		if ($produccion[$turno] > 0) {
			$promedios[$turno] = $consumo_total[$turno] / $produccion[$turno];
		}
	}

	/**** COMPRAS ****/
//	$compras = $total_valores_entrada;
	$compras = 0;
	foreach ($aux->mps as $cod => $mp) {
		if ($cod != 90) {
			$compras += $mp['compras'];
		}
	}

	// Restar las compras directas
//	$compras -= $compra_directa;
	$compras -= $aux->compra_directa;

	/**** INVENTARIO ACTUAL ****/
//	$inv_act = $total_valores;

	/**** MATERIA PRIMA UTILIZADA ****/
	$mat_prima_utilizada = $inv_ant/*[0]['sum']*/ + $compras + array_sum($mercancias) - $inv_act;

	/**** MANO DE OBRA ****/
	$sql = "SELECT sum(raya_pagada) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	$mano_obra = $db->query($sql);

	/**** PANADEROS ****/
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 3";
	$panaderos = $db->query($sql);

	/**** GASTOS DE FABRICACION ****/
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 GROUP BY num_cia";
	$tmp = $db->query($sql);
	$gastos_fab[0]['sum'] = $tmp ? $tmp[0]['sum'] : 0;

	// [04-Ago-2016] Para la compañía 132 y fecha anterior a julio de 2016 omitir las compras en panadería de gas de gastos de fabricación.
	if (mktime(0, 0, 0, $mes, 1, $anyo) < mktime(0, 0, 0, 8, 1, 2016))
	{
		$gas_comprado = $db->query("SELECT SUM(importe) AS importe FROM movimiento_gastos WHERE num_cia = {$num_cia} AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND codgastos = 90 AND concepto != 'DIFERENCIA INVENTARIO'");

		$gastos_fab[0]['sum'] -= $gas_comprado[0]['importe'];
	}

	// *** [3-Oct-2008] Excluir todos los pagos hechos para otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (1) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$gastos_fab[0]['sum'] -= isset($importe[0]) ? $importe[0]['importe'] : 0;
	// *** [3-Oct-2008] Incluir todos los pagos hechos por otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia_aplica = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (1) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$gastos_fab[0]['sum'] += isset($importe[0]) ? $importe[0]['importe'] : 0;

	// [03-Oct-2016] Incluir gastos en reserva
	$sql = "SELECT
		SUM(importe) AS importe
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
	WHERE
		rg.num_cia = {$num_cia}
		AND rg.anio = {$anyo}
		AND rg.mes = {$mes}
		AND cg.codigo_edo_resultados = 1";

	$reserva_gastos = $db->query($sql);
	$gastos_fab[0]['sum'] += isset($reserva_gastos[0]) ? $reserva_gastos[0]['importe'] : 0;

	/**** COSTO DE PRODUCCION ****/
	$costo_produccion = $mat_prima_utilizada + $mano_obra[0]['sum'] + $panaderos[0]['sum'] + $gastos_fab[0]['sum'];

	/**** UTILIDAD BRUTA ****/
	$utilidad_bruta = $ventas_netas - $costo_produccion;

	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

	/**** PAN COMPRADO ****/
	$ts_bal = mktime(0, 0, 0, $mes, 1, $anyo);
	$ts_limit = mktime(0, 0, 0, 9, 1, 2006);
	if ($ts_bal >= mktime(0, 0, 0, 6, 1, 2016))
	{
		$sql = "SELECT
			SUM(importe) AS importe
		FROM
			movimiento_gastos mv
			LEFT JOIN catalogo_gastos cg
				USING (codgastos)
		WHERE
			num_cia = {$num_cia}
			AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
			AND pan_comprado = TRUE
			AND captura = FALSE";

		$comp = $db->query($sql);

		$pan_comprado = -1 * $comp[0]['importe'];
	}
	else if ($ts_bal >= $ts_limit) {
		//PAN COMPRADO CON DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE'";
		$temp = $db->query($sql);
		//PAN COMPRADO SIN DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE'";
		$temp1 = $db->query($sql);
		//PAN COMPRADO 10% DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 159 AND captura = 'FALSE'";
		$comp10 = $db->query($sql);
		//PAN COMPRADO 10% DESCUENTO
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 177 AND captura = 'FALSE'";
		$comp15 = $db->query($sql);

		// [06-May-2009] Incluir pan comprado de panaderia a punto caliente (codigo 195)
		$sql = "SELECT sum(importe) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 195 AND captura = 'FALSE'";
		$comp195 = $db->query($sql);


		$pan_comprado = -1 * $temp[0]['pan_comprado'];
		$pan_comprado += -1 * $temp1[0]['pan_comprado'];
		$pan_comprado += -1 * $comp10[0]['pan_comprado'];
		$pan_comprado += -1 * $comp15[0]['pan_comprado'];
		$pan_comprado += -1 * $comp195[0]['pan_comprado'];
	}
	else {
		//PAN COMPRADO CON DESCUENTO
		$sql = "SELECT sum(importe) + sum(importe) * ((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia) / 100) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE'";
		$temp = $db->query($sql);
		//PAN COMPRADO SIN DESCUENTO
		$sql = "SELECT sum(importe) + sum(importe) * ((SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia) / 100) AS pan_comprado FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE'";
		$temp1 = $db->query($sql);

		$pan_comprado = -1 * $temp[0]['pan_comprado'] / 1.25;
		$pan_comprado += -1 * $temp1[0]['pan_comprado'];
	}

	// [2013-08-05] Faltante de pan
	// [2013-10-16] El descuento de pan comprado ahora se calcula a partir del porcentaje en el catalogo de gastos
	$sql = "
		SELECT
			CASE
				WHEN (produccion_total + COALESCE((
					SELECT
						SUM(
							/*CASE
								WHEN codgastos = 5 THEN
									importe * 100 / (100 - COALESCE((
										SELECT
											porcentaje
										FROM
											porcentaje_pan_comprado
										WHERE
											num_cia = mv.num_cia
									), 0))
								WHEN codgastos = 159 THEN
									importe * 100 / 90
								WHEN codgastos = 152 THEN
									importe
							END*/
							importe * 100 / (100 - pan_comprado_descuento)
						)
					FROM
						movimiento_gastos mv
						LEFT JOIN catalogo_gastos cg
							USING (codgastos)
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						/*AND codgastos IN (5, 159, 152)*/
						AND pan_comprado = TRUE
						AND captura = FALSE
				), 0)) > 0 THEN
					(faltante_pan/* + COALESCE((
						SELECT
							SUM(desc_pastel)
						FROM
							captura_efectivos
						WHERE
							num_cia = bal.num_cia
							AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							AND desc_pastel <> 0
					), 0)*/) * 100 / (produccion_total + COALESCE((
						SELECT
							SUM(
								/*CASE
									WHEN codgastos = 5 THEN
										importe * 100 / (100 - COALESCE((
											SELECT
												porcentaje
											FROM
												porcentaje_pan_comprado
											WHERE
												num_cia = mv.num_cia
										), 0))
									WHEN codgastos = 159 THEN
										importe * 100 / 90
									WHEN codgastos = 152 THEN
										importe
								END*/
								importe * 100 / (100 - pan_comprado_descuento)
							)
						FROM
							movimiento_gastos mv
							LEFT JOIN catalogo_gastos cg
								USING (codgastos)
						WHERE
							num_cia = bal.num_cia
							AND fecha BETWEEN bal.fecha AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							/*AND codgastos IN (5, 159, 152)*/
							AND pan_comprado = TRUE
							AND captura = FALSE
					), 0))
				ELSE
					0
			END
				AS por_faltante_pan
		FROM
			balances_pan bal
		WHERE
			num_cia = {$num_cia}
			AND anio = {$anyo}
			AND mes = {$mes}
		ORDER BY
			num_cia,
			anio,
			mes
	";

	$tmp = $db->query($sql);

	$por_faltante_pan = $tmp && $tmp[0]['por_faltante_pan'] != 0 ? $tmp[0]['por_faltante_pan'] : 0;

	/**** GASTOS GENERALES ****/
	/*
	@ [9-Abr-2014] No tomar en cuenta el codigo 84 ASEGURADORAS
	*/
	$sql = "SELECT sum(importe) FROM catalogo_gastos LEFT JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 AND codgastos NOT IN (9, 76" . (mktime(0, 0, 0, $mes, 1, $anyo) > mktime(0, 0, 0, 9, 1, 2006) ? ", 140, 141" : ", 141") . (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 1, 1, 2014) ? ', 84' : '') . ") GROUP BY num_cia";
	$gastos_gral = $db->query($sql);
	@$gastos_gral[0]['sum'] *= -1;
	// *** [4-Ago-2008] Sumar el 2% de IDE del estado de cuenta a los gastos generales
	// *** [5-Ago-2008] Omitir este paso
	/*if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 7, 1, 2008)) {
		$sql = "SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND cod_mov = 78 AND cuenta = 2 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$importe_2por_ide = $db->query($sql);
		$gastos_gral[0]['sum'] -= $importe_2por_ide[0]['sum'];
	}*/
	// *** [5-Ago-2008] Obetener todos los efectivos depositados con códigos 1, 16, 13, 7, 79 y multiplicarlo
	// *** por el 2% del impuesto IDE y sumarlo a gastos generales
	/*
	@ [03-Feb-2010] A partir del año 2010 cambio el porcentaje al 3%
	*/
	/*
	@ [09-Abr-2014] A partir de marzo de 2014 no se calcula IDE
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $mes, 1, $anyo) <= mktime(0, 0, 0, 2, 1, 2014)) {
		$por_ide = $anyo < 2010 ? 0.02 : 0.03;

		$sql = "SELECT round((sum(importe)::numeric - 25000) * $por_ide, 2) AS importe FROM estado_cuenta WHERE num_cia = $num_cia AND fecha_con BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 7, 13, 16, 79)";
		$importe = $db->query($sql);
		$gastos_gral[0]['sum'] -= $importe[0]['importe'];
	}
	// *** [3-Oct-2008] Excluir todos los pagos hechos para otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (2) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$gastos_gral[0]['sum'] += $importe[0]['importe'];

	// *** [3-Oct-2008] Incluir todos los pagos hechos por otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia_aplica = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (2) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$gastos_gral[0]['sum'] -= $importe[0]['importe'];

	// [03-Oct-2016] Incluir gastos en reserva
	$sql = "SELECT
		SUM(importe) AS importe
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
	WHERE
		rg.num_cia = {$num_cia}
		AND rg.anio = {$anyo}
		AND rg.mes = {$mes}
		AND cg.codigo_edo_resultados = 2";

	$reserva_gastos = $db->query($sql);
	$gastos_gral[0]['sum'] -= isset($reserva_gastos[0]) ? $reserva_gastos[0]['importe'] : 0;

	/**** GASTOS DE CAJA (no incluir cod. 28 abarrotes julild) ([03-Jun-2011] No incluir 154 Avio bocadillos) ****/
	$egresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE' AND clave_balance = 'TRUE' AND cod_gastos NOT IN (28, 154)");
	$ingresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' AND clave_balance = 'TRUE' AND cod_gastos NOT IN (28, 154)");
	$gastos_caja = $ingresos[0]['sum'] - $egresos[0]['sum'];

	/**** [27-Mar-2007] Comisiones bancarias ****/
	$comisiones = 0;
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 3, 1, 2007)) {
		$sql = "(SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 1 AND cod_mov IN (SELECT cod_mov FROM";
		$sql .= " catalogo_mov_bancos WHERE entra_bal = 'TRUE' AND cod_mov NOT IN (78) GROUP BY cod_mov) GROUP BY tipo_mov) UNION (SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia";
		$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 2 AND cod_mov IN (SELECT cod_mov FROM catalogo_mov_santander WHERE entra_bal = 'TRUE' AND cod_mov NOT IN (78) GROUP BY cod_mov) GROUP BY";
		$sql .= " tipo_mov)";
		$result = $db->query($sql);

		if ($result)
			foreach ($result as $reg)
				$comisiones += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
	}

	/**** RESERVAS ****/
	$sql = "SELECT sum(importe) FROM reservas_cias WHERE num_cia = $num_cia AND fecha = '$fecha1'";
	$reservas = $db->query($sql);
	$reservas[0]['sum'] *= -1;

	/**** PAGOS HECHOS POR ANTICIPADO ****/
	$sql = "SELECT sum(importe) FROM pagos_anticipados WHERE num_cia = $num_cia AND (fecha_ini, fecha_fin) OVERLAPS (DATE '$fecha1', DATE '$fecha2')";
	$pagos_anticipados = $db->query($sql);
	$pagos_anticipados[0]['sum'] *= -1;

	/**** GASTOS PAGADOS POR OTRAS COMPAÑIAS ****/
	$cia_gasto_egreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_egreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$cia_gasto_ingreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$gastos_otros = $cia_gasto_egreso[0]['sum'] - $cia_gasto_ingreso[0]['sum'];

	/**** TOTAL DE GASTOS ****/
	$gastos_totales = $pan_comprado + $gastos_gral[0]['sum'] + $gastos_caja + $comisiones + $reservas[0]['sum'] + $pagos_anticipados[0]['sum'] + $gastos_otros;

	/**** INGRESOS EXTRAORDINARIOS ****/
	if (empty($_GET['no_gastos'])) {
		$sql = "SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 18";
		$ingresos_ext = $db->query($sql);
	}
	else
		$ingresos_ext[0]['sum'] = 0;

	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

	/**** UTILIDAD NETA ****/
	$utilidad_neta = $gastos_totales + $ingresos_ext[0]['sum'] + $utilidad_bruta;

	/*
	@ [22-Jul-2010] Número de errores bancarios en el mes (faltantes y sobrantes)
	*/
	$sql = '
		SELECT
			count(id)
				AS
					errores_bancarios
		FROM
			faltantes_cometra
		WHERE
				num_cia = ' . $num_cia . '
			AND
				fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
	';
	$errores_bancarios = $db->query($sql);

	/**** PRODUCCION TOTAL ****/
	$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	$produccion_total = $db->query($sql);

	/**** GANANCIA ****/
	$sql = "SELECT sum(pan_p_venta - pan_p_expendio) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$ganancia = $db->query($sql);

	/**** PORCENTAJE DE GANANCIA ****/
	$sql = "SELECT (sum(pan_p_venta) - sum(pan_p_expendio)) * 100 / sum(pan_p_venta) AS sum FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND pan_p_venta > 0";
	$porc_ganancia = $db->query($sql);

	/**** FALTANTE DE PAN ****/
	$faltante_pan = 0;

	$pro = $db->query("SELECT sum(total_produccion) AS dato, extract(day FROM fecha_total) AS dia FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha_total ORDER BY fecha_total");
	$pc = $db->query("SELECT (sum(importe) / (100 - (SELECT porcentaje FROM porcentaje_pan_comprado WHERE num_cia = $num_cia))) * 100 AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 5 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	// (04/Mayo/2006) Pan comprado con descuento del 10%
	$pc2 = $db->query("SELECT (sum(importe) / (100 - 10)) * 100 AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 159 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	$pc1 = $db->query("SELECT sum(importe) AS dato, extract(day FROM fecha) AS dia FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 152 AND captura = 'FALSE' GROUP BY fecha ORDER BY fecha");
	$prueba_pan_ant = $db->query("SELECT sum(importe) AS dato, CASE WHEN fecha < '$fecha1' THEN 0 ELSE extract(day from fecha) END AS dia FROM prueba_pan WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha_his' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$vp = $db->query("SELECT sum(venta_puerta) AS dato, extract(day FROM fecha) AS dia FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$reparto = $db->query("SELECT sum(pan_p_venta) AS dato, extract(day FROM fecha) AS dia FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$prueba_pan = $db->query("SELECT sum(importe) AS dato, extract(day FROM fecha) AS dia FROM prueba_pan WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha");
	$ts_bal = mktime(0, 0, 0, $mes, 1, $anyo);
	$ts_limit = mktime(0, 0, 0, 9, 1, 2006);
	//echo "<table><tr><th>dia</th><th>Produccion</th><th>Pan comprado</th><th>Sobrante ayer</th><th>Total pan</th><th>Venta puerta</th><th>Reparto</th><th>Sobrante hoy</th><th>Faltante</th></tr>";
	for ($d = 1; $d <= $dias; $d++) {
		if ($ts_bal > mktime(0, 0, 0, 4, 30, 2006))
			$total_pan = buscar_fal($pro, $d) + buscar_fal($pc, $d) + buscar_fal($prueba_pan_ant, $d - 1) + buscar_fal($pc1, $d) + buscar_fal($pc2, $d);
		else
			$total_pan = buscar_fal($pro, $d) + buscar_fal($pc, $d) + buscar_fal($prueba_pan_ant, $d - 1) + buscar_fal($pc1, $d);
		$sobrante = $total_pan - buscar_fal($vp, $d) - buscar_fal($reparto, $d) - buscar_fal($pro, $d) * ($ts_bal >= $ts_limit ? 0 : 0.02);
		$faltante = buscar_fal($prueba_pan, $d) - $sobrante;
		$faltante_pan += $faltante;

		//echo "<tr><td>$d</td><td>" . buscar_fal($pro, $d) . "</td><td>" . (buscar_fal($pc, $d) + buscar_fal($pc1, $d) + buscar_fal($pc2, $d)) . "</td><td>" . buscar_fal($prueba_pan_ant, $d - 1) . "</td><td>$total_pan</td><td>" . buscar_fal($vp, $d) . "</td><td>" . buscar_fal($reparto, $d) . "</td><td>$sobrante</td><td>$faltante_pan</td></tr>";
	}//echo "</table>";

	/**** DEVOLUCIONES ****/
	$sql = "SELECT sum(devolucion) FROM mov_expendios WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$devoluciones = $db->query($sql);

	/**** REZAGO INICIAL ANUAL ****/
	$sql = "SELECT sum(rezago_anterior) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '01/01/$anyo'";
	$rezago_inicial_anual = $db->query($sql);
	if ($rezago_inicial_anual[0]['sum'] == '' || $rezago_inicial_anual[0]['sum'] == 0) {
		$sql = "SELECT sum(rezago) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '01/01/$anyo'::date - interval '1 day'";
		$rezago_inicial_anual = $db->query($sql);
	}

	/**** REZAGO INICIAL ****/
	$sql = "SELECT sum(rezago) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '$fecha_his'";
	$rezago_inicial = $db->query($sql);

	/**** REZAGO FINAL ****/
	$sql = "SELECT sum(rezago) FROM mov_expendios WHERE num_cia = $num_cia AND fecha = '$fecha2'";
	$rezago_final = $db->query($sql);

	/**** CAMBIO REZAGO ****/
	$cambio_rezago = $rezago_final[0]['sum'] - $rezago_inicial[0]['sum'];

	/**** CAMBIO REZAGO ANUAL ****/
	$cambio_rezago_anual = $rezago_final[0]['sum'] - $rezago_inicial_anual[0]['sum'];

	/**** EFECTIVO ****/
	$sql = "SELECT sum(efectivo) FROM total_panaderias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$efectivo = $db->query($sql);

	// [21-Ene-2010] Calcular excedente de efectivo
	$sql = "SELECT sum(saldo_libros) AS importe FROM saldos WHERE num_cia = $num_cia";
	$saldo = $db->query($sql);

	$sql = "SELECT sum(CASE WHEN tipo_mov = 'FALSE' THEN -importe ELSE importe END) AS importe FROM estado_cuenta WHERE num_cia = $num_cia AND fecha > '$fecha2'";
	$movs = $db->query($sql);

	$sql = "SELECT sum(total) AS importe FROM (SELECT sum(total) AS total FROM pasivo_proveedores WHERE num_cia = $num_cia AND fecha <= '$fecha2' UNION SELECT sum(total) FROM facturas_pagadas WHERE num_cia = $num_cia AND fecha <= '$fecha2' AND fecha_cheque > '$fecha2') result WHERE total > 0";
	$saldo_pro = $db->query($sql);

	$excedente_efectivo = $saldo[0]['importe'] + $movs[0]['importe'] - $saldo_pro[0]['importe'];

	// Insertar o actualizar historico
	$sql = "SELECT sum(ctes) FROM captura_efectivos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$tmp = $db->query($sql);
	$clientes = $tmp[0]['sum'] != 0 ? $tmp[0]['sum'] : 'NULL';
	$por_efe = $produccion_total[0]['sum'] > 0 ? $efectivo[0]['sum'] / $produccion_total[0]['sum'] : '0';

	/**** BULTOS ****/
	$sql = "SELECT cod_turno AS turno, sum(cantidad / 44) AS bultos FROM mov_inv_real WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codmp = 1 AND tipo_mov = 'TRUE' AND descripcion <> 'DIFERENCIA INVENTARIO' GROUP BY cod_turno";

	$tmp = $db->query($sql);

	$bultos[0]['bultos'] = 0;

	$bultos_turno = array(
		1  => 0,
		2  => 0,
		3  => 0,
		4  => 0,
		8  => 0,
		9  => 0,
		10 => 0
	);

	if ($tmp) {
		foreach ($tmp as $t) {
			$bultos[0]['bultos'] += $t['bultos'];
			$bultos_turno[$t['turno']] = $t['bultos'];
		}
	}

	$sql_balance .= "INSERT INTO historico (num_cia, mes, anio, utilidad, venta, reparto, clientes, gasto_ext, ingresos, por_efe, bultos, produccion) VALUES ($num_cia, $mes, $anyo, $utilidad_neta, " . ($venta_puerta[0]['sum'] - $errores[0]['sum']) . ", " . ($abono_reparto[0]['sum'] != 0 ? $abono_reparto[0]['sum'] : 0) . ", $clientes, '" . ($ingresos_ext[0]['sum'] != 0 ? "TRUE" : "FALSE") . "', " . ($ingresos_ext[0]['sum'] != 0 ? $ingresos_ext[0]['sum'] : 0) . ", $por_efe, " . ($bultos && $bultos[0]['bultos'] != 0 ? $bultos[0]['bultos'] : 0) . ", " . ($produccion_total[0]['sum'] != 0 ? $produccion_total[0]['sum'] : 0) . ");\n";

	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@ */

	/**** M. PRIMA / VENTAS - PAN COMPRADO ****/
	@$mp_vtas = $mat_prima_utilizada / ($ventas_netas + $pan_comprado);

	// [13-Dic-2007] Obtener produccion del gelatinero para descontar al total de la producción
	$gel = $db->query("SELECT sum(total_produccion) AS gel FROM total_produccion WHERE numcia = $num_cia AND codturno = 9 AND fecha_total BETWEEN '$fecha1' AND '$fecha2'");

	/**** UTILIDAD / PRODUCCION ****/
	/*
	@ [9-Jun-2013] La utilidad neta debe ser menos ingresos extraordinarios
	*/
	@$utilidad_produccion = ($utilidad_neta - $ingresos_ext[0]['sum']) / ($produccion_total[0]['sum']/* - $gel[0]['gel']*/);

	/**** UTILIDAD / PRODUCCION ****/
	/*
	@ [06-Ene-2015] Utilidad neta / (produccion + pan comprado)
	*/
	@$utilidad_produccion_pc = ($utilidad_neta - $ingresos_ext[0]['sum']) / ($produccion_total[0]['sum'] + abs($pan_comprado));

	/**** MATERIA PRIMA / PRODUCCION ****/
	@$mp_produccion = $mat_prima_utilizada / ($produccion_total[0]['sum']/* - $gel[0]['gel']*/);

	/**** GAS / PRODUCCION ****/
	// Gastos de código 90 GAS
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 90 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
	$gas = $db->query($sql);
	if ($gas[0]['sum'] == 0 || $gas[0]['sum'] == '') {
		// Gastos de código 128 GAS NATURAL
		$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 128 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1";
		$gas = $db->query($sql);

		if ($gas[0]['sum'] != 0 || $gas[0]['sum'] != '') {
			// Buscar buscar hasta que mes anterior al actual se pago gas natural
			$sql = "SELECT fecha FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND codgastos = 128 AND fecha < '$fecha1' AND codigo_edo_resultados = 1 ORDER BY fecha DESC LIMIT 1";
			$tmp = $db->query($sql);

			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $tmp[0]['fecha'], $tmp);
			$fecha_tmp1 = date("d/m/Y", mktime(0, 0, 0, $tmp[2] + 1, 1, $tmp[3]));
			$fecha_tmp2 = date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anyo));

			// Obtener produccion de los meses que no se pago el gas
			$sql = "SELECT sum(total_produccion) FROM total_produccion WHERE numcia = $num_cia AND fecha_total BETWEEN '$fecha_tmp1' AND '$fecha_tmp2' AND codturno NOT IN (9)";
			$tmp = $db->query($sql);
			$pro_ant = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
		}
	}
	else
		$pro_ant = 0;

	// Descuentos de gas
	$sql = "SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
	$des_gas = $db->query($sql);

	@$gas_produccion = ($gas[0]['sum'] - $des_gas[0]['sum']) / ($produccion_total[0]['sum']/* - $gel[0]['gel']*/ + $pro_ant);

	// Promedios de consumo / produccion
//	foreach ($pro_tur as $key => $value)
//		if ($value > 0 && $key <= 9) {
//			$sql_balance .= "INSERT INTO consumo_produccion (num_cia, anio, mes, cod_turno, con_pro, pro) VALUES ($num_cia, $anyo, $mes, $key, {$consumo[$key]} / $value, $value);\n";
//		}
	foreach ($promedios as $turno => $promedio) {
		if ($promedio > 0) {
			$sql_balance .= "INSERT INTO consumo_produccion (num_cia, anio, mes, cod_turno, con_pro, pro, bultos) VALUES ($num_cia, $anyo, $mes, $turno, $promedio, {$produccion[$turno]}, {$bultos_turno[$turno]});\n";
		}
	}

	/**** EMPLEADOS AFILIADOS AL IMSS (AGREGADO EL 23 DE NOVIEMBRE DE 2005) ****/
//	if ($mes == date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))) && $anyo == date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))) && date("d") < 6) {
		$temp = $db->query("SELECT count(id) FROM catalogo_trabajadores WHERE num_cia = $num_cia AND num_afiliacion IS NOT NULL AND fecha_baja IS NULL");
		$emp_afi = $temp[0]['count'];
//	}
//	else {
//		$temp = $db->query("SELECT emp_afi FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = $anyo");
//		$emp_afi = $temp ? $temp[0]['emp_afi'] : 0;
//	}

	$bal['num_cia'] = $num_cia;
	$bal['mes'] = $mes;
	$bal['anio'] = $anyo;
	$bal['fecha'] = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
	$bal['venta_puerta'] = $venta_puerta[0]['sum'] != 0 ? $venta_puerta[0]['sum'] : "0";
	$bal['pastel_vitrina'] = $pastel_vitrina;
	$bal['pastel_pedido'] = $pastel_pedido;
	$bal['pastel_kilos'] = $pastel_kilos;
	$bal['pan_pedido'] = $pan_pedido;
	$bal['bases'] = "0";
	$bal['barredura'] = $barredura[0]['sum'] != 0 ? $barredura[0]['sum'] : "0";
	$bal['pastillaje'] = $pastillaje[0]['sum'] != 0 ? $pastillaje[0]['sum'] : "0";
	$bal['abono_emp'] = $abono_empleados[0]['sum'] != 0 ? $abono_empleados[0]['sum'] : "0";
	$bal['otros'] = $otros[0]['sum'] != 0 ? $otros[0]['sum'] : "0";
	$bal['total_otros'] = $total_otros != 0 ? $total_otros : "0";
	$bal['abono_reparto'] = $abono_reparto[0]['sum'] != 0 ? $abono_reparto[0]['sum'] : "0";
	$bal['errores'] = $errores[0]['sum'] != 0 ? $errores[0]['sum'] : "0";
	$bal['ventas_netas'] = $ventas_netas != 0 ? $ventas_netas : "0";
	$bal['inv_ant'] = $inv_ant/*[0]['sum']*/ != 0 ? $inv_ant/*[0]['sum']*/ : "0";
	$bal['compras'] = $compras != 0 ? $compras : "0";
	$bal['mercancias'] = array_sum($mercancias) != 0 ? array_sum($mercancias) : "0";
	$bal['inv_act'] = $inv_act != 0 ? $inv_act : "0";
	$bal['mat_prima_utilizada'] = $mat_prima_utilizada != 0 ? $mat_prima_utilizada : "0";
	$bal['mano_obra'] = $mano_obra[0]['sum'] != 0 ? $mano_obra[0]['sum'] : "0";
	$bal['panaderos'] = $panaderos[0]['sum'] != 0 ? $panaderos[0]['sum'] : "0";
	$bal['gastos_fab'] = $gastos_fab[0]['sum'] != 0 ? $gastos_fab[0]['sum'] : "0";
	$bal['costo_produccion'] = $costo_produccion != 0 ? $costo_produccion : "0";
	$bal['utilidad_bruta'] = $utilidad_bruta != 0 ? $utilidad_bruta : "0";
	$bal['pan_comprado'] = $pan_comprado != 0 ? $pan_comprado : "0";
	$bal['gastos_generales'] = $gastos_gral[0]['sum'] != 0 ? $gastos_gral[0]['sum'] : "0";
	$bal['gastos_caja'] = $gastos_caja != 0 ? $gastos_caja : "0";
	$bal['comisiones'] = $comisiones != 0 ? $comisiones : '0';
	$bal['reserva_aguinaldos'] = $reservas[0]['sum'] != 0 ? $reservas[0]['sum'] : "0";
	$bal['gastos_otras_cias'] = $gastos_otros != 0 ? $gastos_otros : "0";
	$bal['total_gastos'] = $gastos_totales != 0 ? $gastos_totales : "0";
	$bal['ingresos_ext'] = $ingresos_ext[0]['sum'] != 0 ? $ingresos_ext[0]['sum'] : "0";
	$bal['utilidad_neta'] = $utilidad_neta != 0 ? $utilidad_neta : "0";
	$bal['errores_bancarios'] = $errores_bancarios[0]['errores_bancarios'] > 0 ? $errores_bancarios[0]['errores_bancarios'] : '0';
	$bal['mp_vtas'] = $mp_vtas != 0 ? $mp_vtas : "0";
	$bal['utilidad_pro'] = $utilidad_produccion != 0 ? $utilidad_produccion : "0";
	$bal['utilidad_pro_pc'] = $utilidad_produccion_pc != 0 ? $utilidad_produccion_pc : "0";
	$bal['mp_pro'] = $mp_produccion != 0 ? $mp_produccion : "0";
	$bal['gas_pro'] = $gas_produccion != 0 ? $gas_produccion : "0";
	$bal['produccion_total'] = $produccion_total[0]['sum'] != 0 ? $produccion_total[0]['sum'] : "0";
	$bal['ganancia'] = $ganancia[0]['sum'] != 0 ? $ganancia[0]['sum'] : '0';
	$bal['porc_ganancia'] = $porc_ganancia[0]['sum'] != 0 ? $porc_ganancia[0]['sum'] : '0';
	$bal['faltante_pan'] = $faltante_pan != 0 ? $faltante_pan : "0";
	$bal['devoluciones'] = $devoluciones[0]['sum'] != 0 ? $devoluciones[0]['sum'] : '0';
	$bal['rezago_ini'] = $rezago_inicial[0]['sum'] != 0 ? $rezago_inicial[0]['sum'] : "0";
	$bal['rezago_fin'] = $rezago_final[0]['sum'] != 0 ? $rezago_final[0]['sum'] : "0";
	$bal['var_rezago'] = $cambio_rezago != 0 ? $cambio_rezago : "0";
	$bal['var_rezago_anual'] = $cambio_rezago_anual != 0 ? $cambio_rezago_anual : "0";
	$bal['efectivo'] = $efectivo[0]['sum'] != 0 ? $efectivo[0]['sum'] : "0";
	$bal['pagos_anticipados'] = $pagos_anticipados[0]['sum'] != 0 ? $pagos_anticipados[0]['sum'] : "0";
	$bal['emp_afi'] = $emp_afi;
	$bal['excedente_efectivo'] = $excedente_efectivo;
	$bal['por_faltante_pan'] = $por_faltante_pan;

	//echo '<pre>' . print_r($bal, TRUE) . '</pre>';

	$sql_balance .= $db->preparar_insert("balances_pan", $bal) . ";\n";

	return $sql_balance;
}

$sql = '';
foreach ($cias as $cia) {
	$sql .= balance($cia['num_cia']);
}

//echo "<pre>$sql</pre>";
$db->query($sql);

?>
