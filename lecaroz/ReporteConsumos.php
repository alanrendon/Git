<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

function toint($value)
{
	return intval($value, 10);
}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'getCia':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);

			if ($result)
				echo $result[0]['nombre'];
		break;

		case 'getReporte':
			// $anyo = $_REQUEST['anio'];
			// $mes = $_REQUEST['mes'];
			list($dia, $mes, $anyo) = array_map('toint', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes, $dia, $anyo));
			// $fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));

			if (isset($_REQUEST['terminado'])) {
				$sql = '
					SELECT
						num_cia,
						nombre,
						nombre_corto,
						mp_pro
					FROM
						catalogo_companias cc
						LEFT JOIN balances_pan bal
							USING (num_cia)
					WHERE
							num_cia < 300
						AND
							anio = ' . $anyo . '
						AND
							mes = ' . $mes . '
				';
			}
			else {
				$sql = '
					SELECT
						num_cia,
						nombre,
						nombre_corto,
						0
							AS mp_pro
					FROM
						catalogo_companias cc
					WHERE
							num_cia < 300
				';
			}


			if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0)
				$sql .= '
					AND
						num_cia = ' . $_REQUEST['num_cia'] . '
				';
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				$sql .= '
					AND
						idadministrador = ' . $_REQUEST['admin'] . '
				';
			$sql .= '
				ORDER BY
					num_cia
			';
			$cias = $db->query($sql);

			if (!$cias)
				die;

			$tpl = new TemplatePower('plantillas/pan/ReporteConsumos.tpl');
			$tpl->prepare();

			$num_cia = NULL;
			foreach ($cias as $cia) {
				/*
				@ Obtener costos y consumos
				*/
				$aux = new AuxInvClass($cia['num_cia'], $anyo, $mes, NULL, 'real', '', '', NULL, (isset($_REQUEST['diferencias']) ? TRUE : FALSE), $dia);

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
							numcia = ' . $cia['num_cia'] . '
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

				/*
				@ Si no tuvo producción, omitir compañía
				*/
				if (!$tmp) {
					continue;
				}

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
						num_cia = ' . $cia['num_cia'] . '
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

				/*
				@ Bandera de creación de bloque de no controlados
				*/
				$bloque_no_controlados = FALSE;

				/*
				@ Validar el cambio de compañía
				*/
				if ($num_cia != $cia['num_cia']) {
					/*
					@ Insertar un salto de página entre casa reporte
					*/
					if ($num_cia != NULL) {
						$tpl->assign('reporte.salto', '<br style="page-break-after:always;">');
					}

					$num_cia = $cia['num_cia'];

					/*
					@ Crear nuevo reporte para la compañía
					*/
					$tpl->newBlock('reporte');
					$tpl->assign('num_cia', $cia['num_cia']);
					$tpl->assign('nombre', $cia['nombre']);
					$tpl->assign('nombre_corto', $cia['nombre_corto']);
					$tpl->assign('dia', $dia);
					$tpl->assign('mes', mes_escrito(/*$_REQUEST['mes']*/$mes));
					$tpl->assign('anyo', $anyo);
				}

				/*
				@ Iterara todos los productos
				*/
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
						@ Si el bloque de productos no controlados no existe, crearlo
						*/
						if ($mp['controlado'] == 'f' && !$bloque_no_controlados) {
							$tpl->newBlock('no_controlados');
							$bloque_no_controlados = TRUE;
						}

						$tpl->newBlock($mp['controlado'] == 't' ? 'producto_controlado' : 'producto_no_controlado');
						$tpl->assign('codmp', $cod);
						$tpl->assign('nombre', $mp['nombre']);
						$tpl->assign('precio', number_format($mp['precio'], 4, '.', ','));

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
										@ [10-Dic-2010] Para la compañía 72 código 156 aplicar el 5% a los franceseros y el 90% al gelatinero
										*/
//										if ($cod == 156) {
//											switch ($turno) {
//												case 1:
//													$aux->consumos[$cod][1] = $diferencia * 0.05;
//												break;
//
//												case 2:
//													$aux->consumos[$cod][2] = $diferencia * 0.05;
//												break;
//
//												case 3:
//													$aux->consumos[$cod][9] = $diferencia * 0.90;
//												break;
//											}
//										}
										/*
										@ Distribuir a partir del porcentaje de los turnos que si tubieron consumo
										*/
										/*else */if ($consumo_total > 0) {
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

									/*
									@ Asignar el consumo del turno
									*/
									$tpl->assign($turno, $consumo != 0 ? number_format($consumo * $mp['precio'], 2, '.', ',') : '&nbsp;');

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
							/*
							@ Asignar el total de consumo del producto
							*/
							$tpl->assign('consumo', number_format($total_producto[$cod], 2, '.', ','));
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
									$tpl->assign($turno, $consumo > 0 ? number_format($consumo * $mp['precio'], 2, '.', ',') : '&nbsp;');

									$total_producto[$cod] += $consumo * $mp['precio'];
									$total_turnos_nc[$turno] += $consumo * $mp['precio'];
								}
							}
							/*
							@ Asignar el total de consumo del turno
							*/
							$tpl->assign('consumo', number_format($total_producto[$cod], 2, '.', ','));
						}
					}
				}

				/*
				@ Asignar totales por turno
				*/
				foreach ($total_turnos as $turno => $consumo) {
					$tpl->assign('reporte.c' . $turno, $consumo > 0 ? number_format($consumo, 2, '.', ',') : '&nbsp;');
				}
				/*
				@ Asignar total de consumo
				*/
				$tpl->assign('reporte.ctotal', number_format(array_sum($total_turnos), 2, '.', ','));

				/*
				@ Para productos no controlados
				*/
				if ($bloque_no_controlados) {
					foreach ($total_turnos_nc as $turno => $consumo) {
						$tpl->assign('no_controlados.c' . $turno, $consumo > 0 ? number_format($consumo, 2, '.', ',') : '&nbsp;');
					}
					/*
					@ Asignar total de consumo
					*/
					$tpl->assign('no_controlados.ctotal', number_format(array_sum($total_turnos_nc), 2, '.', ','));
				}

				/*
				@ Obtener mercancias y ordenarlas por turnos
				@@ [12-Feb-2014] Omitido el código 9 porque ya esta en gastos
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
							num_cia = ' . $cia['num_cia'] . '
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
										-- 9,
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
						num_cia = ' . $cia['num_cia'] . '
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
				@ Asignar mercancias
				*/
				foreach ($mercancias as $turno => $mer) {
					$tpl->assign('reporte.mercancias' . $turno, $mer > 0 ? number_format($mer, 2, '.', ',') : '&nbsp;');
				}
				$tpl->assign('reporte.mercancias', number_format(array_sum($mercancias), 2, '.', ','));

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

					$tpl->assign('reporte.consumo' . $turno, $consumo_total[$turno] > 0 ? number_format($consumo_total[$turno], 2, '.', ',') : '&nbsp;');
				}
				$tpl->assign('reporte.consumos', number_format(array_sum($consumo_total), 2, '.', ','));

				/*
				@ Asignar producción
				*/
				foreach ($produccion as $turno => $pro) {
					$tpl->assign('reporte.produccion' . $turno, $pro > 0 ? number_format($pro, 2, '.', ',') : '&nbsp;');
				}
				$tpl->assign('reporte.produccion', number_format(array_sum($produccion), 2, '.', ','));

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

					$tpl->assign('reporte.prom' . $turno, round($promedios[$turno], 3) != 0 ? number_format($promedios[$turno], 3, '.', ',') : '&nbsp;');
				}
				$tpl->assign('reporte.prom', number_format(array_sum($consumo_total) / array_sum($produccion), 3, '.', ','));

				$tpl->assign('reporte.prom_bal', $cia['mp_pro'] != 0 ? number_format($cia['mp_pro'], 3, '.', ',') : '&nbsp;');
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ReporteConsumosDatos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

// $tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
// $tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);
foreach ($admins as $admin) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $admin['id']);
	$tpl->assign('nombre', $admin['nombre']);
}

$tpl->printToScreen();
?>
