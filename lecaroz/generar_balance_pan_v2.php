<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$cs = array();
foreach ($_GET['num_cia'] as $num_cia)
	if ($num_cia > 0)
		$cs[] = $num_cia;

$anyo = $_GET['anyo'];
$mes = $_GET['mes'];

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
$fecha_his = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo));
$dias = date('j', mktime(0, 0, 0, $mes + 1, 0, $anyo));

$sql = '
	SELECT
		num_cia
	FROM
		catalogo_companias
	WHERE
			num_cia
				BETWEEN
						1
					AND
						300
		AND
			num_cia
				IN
					(
						SELECT
							num_cia
						FROM
							total_panaderias
						WHERE
							fecha
								BETWEEN
										\'' . $fecha1 . '\'
									AND
										\'' . $fecha2 . '\'
						GROUP BY
							num_cia
					)
';
if (count($cs) > 0)
	$sql .= '
		AND
			num_cia
				IN
					(
						' . implode(', ', $cs) . '
					)
';
$sql .= '
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

if (!$cias) die('NO HAY RESULTADOS');

/*
@ Borrar cualquier dato de balance generado anteriormente
*/
$balance = '
	DELETE FROM
		balances_pan
	WHERE
			anio = ' . $anyo . '
		AND
			mes = ' . $mes . '
';
if (count($cs) > 0)
	$balance .= '
		AND
			num_cia
				IN
					(
						' . implode(', ', $cs) . '
					)
	';
$balance .= ";\n";
/*
@ Borrar cualquier dato de historico generado anteriormente
*/
$balance .= '
	DELETE FROM
		historico
	WHERE
			num_cia
					BETWEEN
							1
						AND
							300
		AND
			anio = ' . $anyo . '
		AND
			mes = ' . $mes . '
';
if (count($cs) > 0)
	$balance .= '
		AND
			num_cia
				IN
					(
						' . implode(', ', $cs) . '
					)
	';
$balance .= ";\n";

foreach ($cias as $c) {
	$data = array(
		'num_cia' => $c['num_cia'],
		'anio' => $anyo,
		'mes' => $mes,
		'fecha' => '\'' . date('d/m/Y',  mktime(0, 0, 0, $mes, 1, $anyo)) . '\''
	);
	
	/*
	@
	@@ VENTAS DEL MES
	@
	*/
	
	/*
	@ Venta en puerta, Pastillaje, Otros
	*/
	$sql = '
		SELECT
			sum
				(
					venta_puerta
				)
					AS
						venta_puerta,
			sum
				(
					pastillaje
				)
					AS
						pastillaje,
			sum
				(
					otros
				)
					AS
						otros
		FROM
			total_panaderias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['venta_puerta'] = $tmp[0]['venta_puerta'] != 0 ? $tmp[0]['venta_puerta'] : 0;
	$data['pastillaje'] = $tmp[0]['pastillaje'] != 0 ? $tmp[0]['pastillaje'] : 0;
	$data['otros'] = $tmp[0]['otros'] != 0 ? $tmp[0]['otros'] : 0;
	
	/*
	@ Bases (no definido aun)
	*/
	$data['bases'] = 0;
	
	/*
	@ Barredura
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						barredura
		FROM
			barredura
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha_pago
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['barredura'] = $tmp[0]['barredura'] != 0 ? $tmp[0]['barredura'] : 0;
	
	/*
	@ Abono empleados
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						abono_emp
		FROM
			prestamos
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				tipo_mov = \'TRUE\'
	';
	$tmp = $db->query($sql);
	$data['abono_emp'] = $tmp[0]['abono_emp'] != 0 ? $tmp[0]['abono_emp'] : 0;
	
	/*
	@ Total Otros
	@
	@ = 'Bases' + 'Pastillaje' + 'Barredura' + 'Abono empleados' + 'Otros'
	*/
	$data['total_otros'] = $data['bases'] + $data['pastillaje'] + $data['barredura'] + $data['abono_emp'] + $data['otros'];
	
	/*
	@ Abono reparto
	*/
	$sql = '
		SELECT
			sum
				(
					abono
				)
					AS
						abono_reparto
		FROM
			mov_expendios
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['abono_reparto'] = $tmp[0]['abono_reparto'] != 0 ? $tmp[0]['abono_reparto'] : 0;
	
	/*
	@ Errores
	*/
	$sql = '
		SELECT
			sum
				(
					am_error + pm_error
				)
					AS
						errores
		FROM
			captura_efectivos
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['errores'] = $tmp[0]['errores'] != 0 ? $tmp[0]['errores'] : 0;
	
	/*
	@ Sumar errores a la venta en puerta
	*/
	$data['venta_puerta'] += $data['errores'];
	
	/*
	@ Ventas Netas
	@
	@ = 'Venta en puerta' + 'Total Otros' + 'Abono reparto' - 'Errores'
	*/
	$data['ventas_netas'] = $data['venta_puerta'] + $data['total_otros'] + $data['abono_reparto'] - $data['errores'];
	
	/*
	@
	@@ COSTO DE PRODUCCION
	@
	*/
	
	/*
	@ Obtener costos y consumos
	*/
	$aux = new AuxInvClass($c['num_cia'], $anyo, $mes, NULL, 'real');
	
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
				numcia = ' . $c['num_cia'] . '
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
			num_cia = ' . $c['num_cia'] . '
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
		if (array_sum($aux->consumos[$cod]) > 0) {
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
						if ($diferencia > 0) {
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
						else {
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
	*/
	$sql = '
		SELECT
			cod_turno,
			sum(importe)
				AS
					importe
		FROM
			movimiento_gastos
		WHERE
				num_cia = ' . $c['num_cia'] . '
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
							9,
							23,
							76
						)
		GROUP BY
			cod_turno
		ORDER BY
			cod_turno
	';
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
	
	/*
	@ 
	*/
}

echo '<strong>SE HAN GENERADO/ACTUALIZADO TODOS LOS DATOS DE BALANCE SOLICITADOS</strong>';
?>