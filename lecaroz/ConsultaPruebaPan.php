<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];
			$fecha_fin_mes = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1] + 1, 0, $fecha_pieces[2]));

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$condiciones[] = 'num_cia <= 300';
			$condiciones[] = 'efe = \'TRUE\' AND exp = \'TRUE\' AND gas = \'TRUE\' AND pro = \'TRUE\' AND pas = \'TRUE\'';

			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ Operadora
			*/
			if (isset($_REQUEST['operadora']) && $_REQUEST['operadora'] > 0) {
				$condiciones[] = 'idoperadora = ' . $_REQUEST['operadora'];
			}

			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 50, 57, 48, 62))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}

			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha,
					EXTRACT(day FROM fecha)
						AS
							dia,
					EXTRACT(month FROM fecha)
						AS
							mes,
					EXTRACT(year FROM fecha)
						AS
							anio,
					efectivo,
					venta_puerta
				FROM
						total_panaderias tp
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						catalogo_administradores ca
							USING
								(
									idadministrador
								)
					LEFT JOIN
						catalogo_operadoras co
							USING
								(
									idoperadora
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha
			';

			$result = $db->query($sql);

			if ($result) {
				$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

				$tpl = new TemplatePower('plantillas/pan/' . ($isIpad ? 'ReportePruebaPanIpad.tpl' : 'ReportePruebaPan.tpl'));
				$tpl->prepare();

				if (isset($_REQUEST['totales'])) {


					$num_cia = NULL;
					$max_rows = 35;
					$rows = $max_rows;
					foreach ($result as $index => $rec) {
							if ($num_cia != $rec['num_cia']) {
								if ($rows >= $max_rows) {
								$tpl->newBlock('reporte3');
								$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
								$tpl->assign('anio', $fecha_pieces[2]);

								$tpl->assign('salto', '<br style="page-break-after:always;" />');

								$rows = 0;
							}

							if ($num_cia != NULL) {
								$tpl->newBlock('row3');

								if ($isIpad) {
									$tpl->assign('bgcolor', $bgcolor ? 'bgGray' : 'bgWhite');
									$bgcolor = !$bgcolor;
								}

								$tpl->assign('num_cia', $num_cia);
								$tpl->assign('nombre_cia', $nombre_cia);

								$tpl->assign('dia', $dias);

								foreach ($totales as $key => $value) {
									$tpl->assign($key, round($value, 2) != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');
								}

								$efectivo_produccion = $totales['efectivo'] / $totales['produccion'];
								$promedio_faltante = $totales['diferencia'] / $dias;
								$diferencia_produccion = $totales['diferencia'] * 100 / ($totales['produccion'] + $totales['pan_comprado'] - (isset($_REQUEST['pasteles']) ? $totales['pasteles'] : 0));

								$tpl->assign('efectivo_produccion', number_format($efectivo_produccion, 2, '.', ','));
								$tpl->assign('promedio_faltante', number_format($promedio_faltante, 2, '.', ','));
								$tpl->assign('diferencia_produccion', number_format($diferencia_produccion, 2, '.', ','));
								$tpl->assign('tipo_diferencia', $diferencia_produccion != 0 ? ($diferencia_produccion < 0 ? ' Faltante' : ' Sobrante') : '');

								$tpl->assign('pdevuelto', $pdevuelto > 0 ? '<span style="float:left; color:#C00; font-size:8pt;">(' . number_format($pdevuelto, 2) . '%)</span> ' : '');

								$tpl->assign('color_diferencia', $totales['diferencia'] >= 0 ? 'blue' : 'red');
								$tpl->assign('color_promedio_faltante', $promedio_faltante >= 0 ? 'blue' : 'red');
								$tpl->assign('color_diferencia_produccion', $diferencia_produccion >= 0 ? 'blue' : 'red');

								$rows++;
							}

							$num_cia = $rec['num_cia'];
							$nombre_cia = $rec['nombre_cia'];

							/*
							@
							@@ Obtener todos los datos del reporte para la compañía dada
							@
							*/

							/*
							@ Pasteles
							*/
							if (isset($_REQUEST['pasteles'])) {
								$sql = '
									SELECT
										fecha_entrega
											AS
												fecha,
										SUM(total_factura - base - otros - pastillaje - otros_efectivos)
											AS
												importe
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha_entrega BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											estado <> 2
										AND
											total_factura > 0
									GROUP BY
										fecha_entrega
									ORDER BY
										fecha_entrega
								';
								$tmp = $db->query($sql);

								$pastel_entregado = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$pastel_entregado[$t['fecha']] = $t['importe'];
									}
								}

								$sql = '
									SELECT
										fecha,
										SUM(cuenta - base - otros - pastillaje - otros_efectivos)
											AS
												importe
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											fecha_entrega > \'' . $fecha_fin_mes . '\'
										AND
											estado <> 2
										AND
											cuenta > 0
									GROUP BY
										fecha
									ORDER BY
										fecha
								';
								$tmp = $db->query($sql);

								$pastel_anticipo = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$pastel_anticipo[$t['fecha']] = $t['importe'];
									}
								}

								/*
								@ [21-Ene-2013] Desglose de notas de pastel
								*/

								$sql = '
									SELECT
										fecha_entrega
											AS fecha,
										fecha_entrega,
										total_factura - base - otros - pastillaje - otros_efectivos
											AS importe,
										1
											AS tipo
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha_entrega BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											estado <> 2
										AND
											total_factura > 0

									UNION

									SELECT
										fecha,
										fecha_entrega,
										cuenta - base - otros - pastillaje - otros_efectivos
											AS importe,
										2
											AS tipo
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											fecha_entrega > \'' . $fecha_fin_mes . '\'
										AND
											estado <> 2
										AND
											cuenta > 0

									ORDER BY
										fecha,
										tipo
								';

								$tmp = $db->query($sql);

								$desglose_pasteles = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$desglose_pasteles[$t['fecha']][] = array(
											'tipo'          => $t['tipo'],
											'fecha_entrega' => $t['fecha_entrega'],
											'importe'       => $t['importe']
										);
									}
								}
							}

							/*
							@ Descuento
							*/
							$sql = '
								SELECT
									fecha,
									SUM(desc_pastel)
										AS
											importe
								FROM
									captura_efectivos
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
									AND
										desc_pastel <> 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$descuento = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$descuento[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Producción
							*/
							$sql = '
								SELECT
									fecha_total
										AS
											fecha,
									SUM(total_produccion)
										AS
											importe
								FROM
									total_produccion
								WHERE
										numcia = ' . $num_cia . '
									AND
										fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$produccion = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$produccion[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Reparto
							*/
							$sql = '
								SELECT
									fecha,
									SUM(pan_p_venta)
										AS
											importe
								FROM
									mov_expendios
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$reparto = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$reparto[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Devuelto
							*/
							$sql = '
								SELECT
									fecha,
									SUM(devolucion)
										AS
											importe
								FROM
									mov_expendios
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$devuelto = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$devuelto[$t['fecha']] = $t['importe'];
								}
							}

							/*
							* [29-May-2012] Porcentaje de devuelto contra reparto
							*/
							$sql = '
								SELECT
									ROUND(AVG(porcentaje)::NUMERIC, 2)
										AS porcentaje_devolucion
								FROM
									(
										SELECT
											num_expendio,
											SUM(devolucion),
											SUM(pan_p_expendio),
											CASE
												WHEN SUM(pan_p_expendio) > 0 THEN
													SUM(devolucion) * 100 / SUM(pan_p_venta)
												ELSE
													0
											END
												AS porcentaje
										FROM
											mov_expendios
										WHERE
											num_cia = ' . $num_cia . '
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										GROUP BY
											num_expendio
									) result
								WHERE
									porcentaje > 0
							';

							$tmp = $db->query($sql);

							$pdevuelto = 0;
							if ($tmp) {
								$pdevuelto = $tmp[0]['porcentaje_devolucion'];
							}

							/*
							@ Pan comprado
							*/
							// $sql = '
							// 	SELECT
							// 		fecha,
							// 		SUM(
							// 			CASE
							// 				WHEN codgastos = 5 THEN
							// 					importe * 100 / (100 - COALESCE(
							// 												(
							// 													SELECT
							// 														porcentaje
							// 													FROM
							// 														porcentaje_pan_comprado
							// 													WHERE
							// 														num_cia = mv.num_cia
							// 												),
							// 												0
							// 											))
							// 				WHEN codgastos = 159 THEN
							// 					importe * 100 / 90
							// 				WHEN codgastos = 152 THEN
							// 					importe
							// 			END
							// 		)
							// 			AS
							// 				importe
							// 	FROM
							// 		movimiento_gastos mv
							// 	WHERE
							// 			num_cia = ' . $num_cia . '
							// 		AND
							// 			fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							// 		AND
							// 			codgastos IN (5, 159, 152)
							// 		AND
							// 			captura = \'FALSE\'
							// 	GROUP BY
							// 		fecha
							// 	ORDER BY
							// 		fecha
							// ';
							$sql = "
								SELECT
									fecha,
									SUM(importe * 100 / (100 - pan_comprado_descuento))
										AS importe
								FROM
									movimiento_gastos mv
									LEFT JOIN catalogo_gastos cg
										USING (codgastos)
								WHERE
									num_cia = {$num_cia}
									AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
									AND pan_comprado = TRUE
									AND captura = FALSE
								GROUP BY
									fecha
								ORDER BY
									fecha
							";
							$tmp = $db->query($sql);

							$pan_comprado = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$pan_comprado[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Sobrante de ayer
							*/
							$sql = '
								SELECT
									(fecha + interval \'1 day\')::date
										AS
											fecha,
									SUM(importe)
										AS
											importe
								FROM
									prueba_pan
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\'::date - interval \'1 day\' AND \'' . $fecha2 . '\'::date - interval \'1 day\'
									AND
										importe > 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$sobrante_ayer = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$sobrante_ayer[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Pan contado
							*/
							$sql = '
								SELECT
									fecha,
									SUM(importe)
										AS
											importe
								FROM
									prueba_pan
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
									AND
										importe > 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$pan_contado = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$pan_contado[$t['fecha']] = $t['importe'];
								}
							}

							$sobrante = 0;
							$dias = 0;

							$totales = array(
								'efectivo'      => 0,
								'produccion'    => 0,
								'pan_comprado'  => 0,
								'venta_puerta'  => 0,
								'pasteles'      => 0,
								'reparto'       => 0,
								'devuelto'      => 0,
								'descuento'     => 0,
								'diferencia'    => 0
							);

							$bgcolor = FALSE;
						}

						$totales['efectivo'] += $rec['efectivo'];
						$totales['produccion'] += isset($produccion[$rec['fecha']]) ? $produccion[$rec['fecha']] : 0;
						$totales['pan_comprado'] += isset($pan_comprado[$rec['fecha']]) ? $pan_comprado[$rec['fecha']] : 0;

						$sobrante = isset($sobrante_ayer[$rec['fecha']]) && $sobrante_ayer[$rec['fecha']] != 0 ? $sobrante_ayer[$rec['fecha']] : $sobrante;

						$total_pan = (isset($produccion[$rec['fecha']]) ? $produccion[$rec['fecha']] : 0)
										+ (isset($pan_comprado[$rec['fecha']]) ? $pan_comprado[$rec['fecha']] : 0)
										+ $sobrante;

						if (isset($_REQUEST['pasteles'])) {
							$totales['pasteles'] += (isset($pastel_entregado[$rec['fecha']]) ? $pastel_entregado[$rec['fecha']] : 0) + (isset($pastel_anticipo[$rec['fecha']]) ? $pastel_anticipo[$rec['fecha']] : 0);
						}

						$totales['venta_puerta'] += $rec['venta_puerta'];
						$totales['reparto'] += isset($reparto[$rec['fecha']]) ? $reparto[$rec['fecha']] : 0;
						$totales['devuelto'] += isset($devuelto[$rec['fecha']]) ? $devuelto[$rec['fecha']] : 0;
						$totales['descuento'] += isset($descuento[$rec['fecha']]) ? $descuento[$rec['fecha']] : 0;

						$sobrante = $total_pan
									- $rec['venta_puerta']
									- (isset($reparto[$rec['fecha']]) ? $reparto[$rec['fecha']] : 0)
									- (isset($descuento[$rec['fecha']]) ? $descuento[$rec['fecha']] : 0)
									- (isset($_REQUEST['pasteles']) ?  (isset($pastel_entregado[$rec['fecha']]) ? $pastel_entregado[$rec['fecha']] : 0) + (isset($pastel_anticipo[$rec['fecha']]) ? $pastel_anticipo[$rec['fecha']] : 0) : 0);

						$diferencia = isset($pan_contado[$rec['fecha']]) && $pan_contado[$rec['fecha']] != 0 ? $pan_contado[$rec['fecha']] - $sobrante : 0;

						$totales['diferencia'] += $diferencia;

						$dias = $rec['dia'];
					}
					if ($num_cia != NULL) {
						$tpl->newBlock('row3');

						if ($isIpad) {
							$tpl->assign('bgcolor', $bgcolor ? 'bgGray' : 'bgWhite');
							$bgcolor = !$bgcolor;
						}

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $nombre_cia);

						$tpl->assign('dia', $dias);

						foreach ($totales as $key => $value) {
							$tpl->assign($key, round($value, 2) != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');
						}

						$efectivo_produccion = $totales['efectivo'] / $totales['produccion'];
						$promedio_faltante = $totales['diferencia'] / $dias;
						$promedio_venta_puerta = $totales['venta_puerta'] / $dias;
						$diferencia_produccion = $totales['diferencia'] * 100 / ($totales['produccion'] + $totales['pan_comprado'] - (isset($_REQUEST['pasteles']) ? $totales['pasteles'] : 0));
						$pfaltante = $promedio_faltante * 100 / $promedio_venta_puerta;

						$tpl->assign('efectivo_produccion', number_format($efectivo_produccion, 2, '.', ','));
						$tpl->assign('promedio_faltante', number_format($promedio_faltante, 2, '.', ','));
						$tpl->assign('diferencia_produccion', number_format($diferencia_produccion, 2, '.', ','));
						$tpl->assign('tipo_diferencia', $diferencia_produccion != 0 ? ($diferencia_produccion < 0 ? ' Faltante' : ' Sobrante') : '');

						$tpl->assign('pdevuelto', $pdevuelto > 0 ? '<span style="float:left; color:#C00; font-size:8pt;">(' . number_format($pdevuelto, 2) . '%)</span> ' : '');
						$tpl->assign('pfaltante', $pfaltante != 0 ? '<span style="float:left; color:#C00; font-size:8pt;">(' . number_format($pfaltante, 2) . '%)</span> ' : '');

						$tpl->assign('color_diferencia', $totales['diferencia'] >= 0 ? 'blue' : 'red');
						$tpl->assign('color_promedio_faltante', $promedio_faltante >= 0 ? 'blue' : 'red');
						$tpl->assign('color_diferencia_produccion', $diferencia_produccion >= 0 ? 'blue' : 'red');
					}
				}
				else {
					$num_cia = NULL;
					$hoja = 0;
					foreach ($result as $index => $rec) {
						if ($num_cia != $rec['num_cia']) {
							if ($num_cia != NULL) {
								foreach ($totales as $key => $value) {
									$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2.' : 'reporte1.') . $key, number_format($value, 2, '.', ','));
								}

								$efectivo_produccion = $totales['efectivo'] / $totales['produccion'];
								$promedio_faltante = $totales['diferencia'] / $dias;
								$diferencia_produccion = $totales['diferencia'] * 100 / ($totales['produccion'] + $totales['pan_comprado'] - (isset($_REQUEST['pasteles']) ? $totales['pasteles'] : 0));

								$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.efectivo_produccion', number_format($efectivo_produccion, 2, '.', ','));
								$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.promedio_faltante', number_format($promedio_faltante, 2, '.', ','));
								$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.diferencia_produccion', number_format($diferencia_produccion, 2, '.', ','));
								$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.tipo_diferencia', $diferencia_produccion != 0 ? ($diferencia_produccion < 0 ? ' Faltante' : ' Sobrante') : '');
								$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.pdevuelto', $pdevuelto > 0 ? '<span style="color:#C00; font-size:8pt;">(' . number_format($pdevuelto, 2) . '%)</span> ' : '');

								if ($hoja % 2 == 0) {
									$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.salto', '<br style="page-break-after:always;" />');
								}
							}

							$num_cia = $rec['num_cia'];

							$hoja++;

							/*
							@
							@@ Obtener todos los datos del reporte para la compañía dada
							@
							*/

							/*
							@ Pasteles
							*/
							if (isset($_REQUEST['pasteles'])) {
								$sql = '
									SELECT
										fecha_entrega
											AS
												fecha,
										SUM(total_factura - base - otros - pastillaje - otros_efectivos)
											AS
												importe
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha_entrega BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											estado <> 2
										AND
											total_factura > 0
									GROUP BY
										fecha_entrega
									ORDER BY
										fecha_entrega
								';
								$tmp = $db->query($sql);

								$pastel_entregado = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$pastel_entregado[$t['fecha']] = $t['importe'];
									}
								}

								$sql = '
									SELECT
										fecha,
										SUM(cuenta - base - otros - pastillaje - otros_efectivos)
											AS
												importe
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											fecha_entrega > \'' . $fecha_fin_mes . '\'
										AND
											estado <> 2
										AND
											cuenta > 0
									GROUP BY
										fecha
									ORDER BY
										fecha
								';
								$tmp = $db->query($sql);

								$pastel_anticipo = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$pastel_anticipo[$t['fecha']] = $t['importe'];
									}
								}

								/*
								@ [21-Ene-2013] Desglose de notas de pastel
								*/

								$sql = '
									SELECT
										fecha_entrega
											AS fecha,
										fecha_entrega,
										total_factura - base - otros - pastillaje - otros_efectivos
											AS importe,
										1
											AS tipo
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha_entrega BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											estado <> 2
										AND
											total_factura > 0

									UNION

									SELECT
										fecha,
										fecha_entrega,
										cuenta - base - otros - pastillaje - otros_efectivos
											AS importe,
										2
											AS tipo
									FROM
										venta_pastel
									WHERE
											num_cia = ' . $num_cia . '
										AND
											fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											fecha_entrega > \'' . $fecha_fin_mes . '\'
										AND
											estado <> 2
										AND
											cuenta > 0

									ORDER BY
										fecha,
										tipo
								';

								$tmp = $db->query($sql);

								$desglose_pasteles = array();
								if ($tmp) {
									foreach ($tmp as $t) {
										$desglose_pasteles[$t['fecha']][] = array(
											'tipo'          => $t['tipo'],
											'fecha_entrega' => $t['fecha_entrega'],
											'importe'       => $t['importe']
										);
									}
								}
							}

							/*
							@ Descuento
							*/
							$sql = '
								SELECT
									fecha,
									SUM(desc_pastel)
										AS
											importe
								FROM
									captura_efectivos
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
									AND
										desc_pastel <> 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$descuento = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$descuento[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Producción
							*/
							$sql = '
								SELECT
									fecha_total
										AS
											fecha,
									SUM(total_produccion)
										AS
											importe
								FROM
									total_produccion
								WHERE
										numcia = ' . $num_cia . '
									AND
										fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$produccion = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$produccion[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Reparto
							*/
							$sql = '
								SELECT
									fecha,
									SUM(pan_p_venta)
										AS
											importe
								FROM
									mov_expendios
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$reparto = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$reparto[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Devuelto
							*/
							$sql = '
								SELECT
									fecha,
									SUM(devolucion)
										AS
											importe
								FROM
									mov_expendios
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$devuelto = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$devuelto[$t['fecha']] = $t['importe'];
								}
							}

							/*
							* [29-May-2012] Porcentaje de devuelto contra reparto
							*/
							$sql = '
								SELECT
									ROUND(AVG(porcentaje)::NUMERIC, 2)
										AS porcentaje_devolucion
								FROM
									(
										SELECT
											num_expendio,
											SUM(devolucion),
											SUM(pan_p_expendio),
											CASE
												WHEN SUM(pan_p_expendio) > 0 THEN
													SUM(devolucion) * 100 / SUM(pan_p_venta)
												ELSE
													0
											END
												AS porcentaje
										FROM
											mov_expendios
										WHERE
											num_cia = ' . $num_cia . '
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										GROUP BY
											num_expendio
									) result
								WHERE
									porcentaje > 0
							';

							$tmp = $db->query($sql);

							$pdevuelto = 0;
							if ($tmp) {
								$pdevuelto = $tmp[0]['porcentaje_devolucion'];
							}

							/*
							@ Pan comprado
							*/
							// $sql = '
							// 	SELECT
							// 		fecha,
							// 		SUM(
							// 			CASE
							// 				WHEN codgastos = 5 THEN
							// 					importe * 100 / (100 - COALESCE(
							// 												(
							// 													SELECT
							// 														porcentaje
							// 													FROM
							// 														porcentaje_pan_comprado
							// 													WHERE
							// 														num_cia = mv.num_cia
							// 												),
							// 												0
							// 											))
							// 				WHEN codgastos = 159 THEN
							// 					importe * 100 / 90
							// 				WHEN codgastos = 152 THEN
							// 					importe
							// 			END
							// 		)
							// 			AS
							// 				importe
							// 	FROM
							// 		movimiento_gastos mv
							// 	WHERE
							// 			num_cia = ' . $num_cia . '
							// 		AND
							// 			fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							// 		AND
							// 			codgastos IN (5, 159, 152)
							// 		AND
							// 			captura = \'FALSE\'
							// 	GROUP BY
							// 		fecha
							// 	ORDER BY
							// 		fecha
							// ';
							$sql = "
								SELECT
									fecha,
									SUM(importe * 100 / (100 - pan_comprado_descuento))
										AS importe
								FROM
									movimiento_gastos mv
									LEFT JOIN catalogo_gastos cg
										USING (codgastos)
								WHERE
									num_cia = {$num_cia}
									AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
									AND pan_comprado = TRUE
									AND captura = FALSE
								GROUP BY
									fecha
								ORDER BY
									fecha
							";
							$tmp = $db->query($sql);

							$pan_comprado = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$pan_comprado[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Sobrante de ayer
							*/
							$sql = '
								SELECT
									(fecha + interval \'1 day\')::date
										AS
											fecha,
									SUM(importe)
										AS
											importe
								FROM
									prueba_pan
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\'::date - interval \'1 day\' AND \'' . $fecha2 . '\'::date - interval \'1 day\'
									AND
										importe > 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$sobrante_ayer = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$sobrante_ayer[$t['fecha']] = $t['importe'];
								}
							}

							/*
							@ Pan contado
							*/
							$sql = '
								SELECT
									fecha,
									SUM(importe)
										AS
											importe
								FROM
									prueba_pan
								WHERE
										num_cia = ' . $num_cia . '
									AND
										fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
									AND
										importe > 0
								GROUP BY
									fecha
								ORDER BY
									fecha
							';
							$tmp = $db->query($sql);

							$pan_contado = array();
							if ($tmp) {
								foreach ($tmp as $t) {
									$pan_contado[$t['fecha']] = $t['importe'];
								}
							}



							$tpl->newBlock(isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', $rec['nombre_cia']);
							$tpl->assign('dia', $fecha_pieces[0]);
							$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
							$tpl->assign('anio', $fecha_pieces[2]);

							$sobrante = 0;
							$dias = 0;

							$totales = array(
								'efectivo'      => 0,
								'produccion'    => 0,
								'pan_comprado'  => 0,
								'venta_puerta'  => 0,
								'pasteles'      => 0,
								'reparto'       => 0,
								'devuelto'      => 0,
								'descuento'     => 0,
								'diferencia'    => 0
							);

							$bgcolor = FALSE;
						}

						$tpl->newBlock(isset($_REQUEST['pasteles']) ? 'row2' : 'row1');

						if ($isIpad) {
							$tpl->assign('bgcolor', $bgcolor ? 'bgGray' : 'bgWhite');
							$bgcolor = !$bgcolor;
						}

						$tpl->assign('dial', $_dias[date('w', mktime(0, 0, 0, $rec['mes'], $rec['dia'], $rec['anio']))]);
						$tpl->assign('dian', str_pad($rec['dia'], 2, '0', STR_PAD_LEFT));

						$tpl->assign('produccion', isset($produccion[$rec['fecha']]) ? number_format($produccion[$rec['fecha']], 2, '.', ',') : '&nbsp;');
						$tpl->assign('pan_comprado', isset($pan_comprado[$rec['fecha']]) ? number_format($pan_comprado[$rec['fecha']], 2, '.', ',') : '&nbsp;');

						$totales['efectivo'] += $rec['efectivo'];
						$totales['produccion'] += isset($produccion[$rec['fecha']]) ? $produccion[$rec['fecha']] : 0;
						$totales['pan_comprado'] += isset($pan_comprado[$rec['fecha']]) ? $pan_comprado[$rec['fecha']] : 0;

						$sobrante = isset($sobrante_ayer[$rec['fecha']]) && $sobrante_ayer[$rec['fecha']] != 0 ? $sobrante_ayer[$rec['fecha']] : $sobrante;

						$tpl->assign('sobrante_ayer', $sobrante != 0 ? number_format($sobrante, 2, '.', ',') : '&nbsp;');

						$total_pan = (isset($produccion[$rec['fecha']]) ? $produccion[$rec['fecha']] : 0)
										 + (isset($pan_comprado[$rec['fecha']]) ? $pan_comprado[$rec['fecha']] : 0)
									 + $sobrante;

						$tpl->assign('total_pan', $total_pan != 0 ? number_format($total_pan, 2, '.', ',') : '&nbsp;');
						$tpl->assign('venta_puerta', $rec['venta_puerta'] != 0 ? number_format($rec['venta_puerta'], 2, '.', ',') : '&nbsp;');

						if (isset($_REQUEST['pasteles'])) {
							if (isset($desglose_pasteles[$rec['fecha']])) {
								$info = '<table style="border-collapse:collapse; border:solid 1px black;" align="center">';

								$tipo = NULL;
								foreach ($desglose_pasteles[$rec['fecha']] as $pastel) {
									if ($tipo != $pastel['tipo']) {
										$tipo = $pastel['tipo'];

										$info .= '<tr><th style="background-color:#ccc;" colspan="2">' . ($tipo == 1 ? 'Entregado' : 'Anticipo') . '</td></tr>';
									}
									$info .= '<tr><td style="background-color:#fff;" align="center">' . $pastel['fecha_entrega'] . '</td><td style="background-color:#fff;" align="right">' . number_format($pastel['importe'], 2) . '</td></tr>';
								}

								$info .= '</table>';
							}


							$tpl->assign('pasteles', isset($pastel_entregado[$rec['fecha']]) || isset($pastel_anticipo[$rec['fecha']]) ? '<a id="info" title="' . htmlentities($info) . '">' . number_format((isset($pastel_entregado[$rec['fecha']]) ? $pastel_entregado[$rec['fecha']] : 0) + (isset($pastel_anticipo[$rec['fecha']]) ? $pastel_anticipo[$rec['fecha']] : 0), 2, '.', ',') . '</a>' : '&nbsp;');

							$totales['pasteles'] += (isset($pastel_entregado[$rec['fecha']]) ? $pastel_entregado[$rec['fecha']] : 0) + (isset($pastel_anticipo[$rec['fecha']]) ? $pastel_anticipo[$rec['fecha']] : 0);
						}

						$tpl->assign('reparto', isset($reparto[$rec['fecha']]) && $reparto[$rec['fecha']] != 0 ? number_format($reparto[$rec['fecha']], 2, '.', ',') : '&nbsp;');
						$tpl->assign('devuelto', isset($devuelto[$rec['fecha']]) && $devuelto[$rec['fecha']] != 0 ? number_format($devuelto[$rec['fecha']], 2, '.', ',') : '&nbsp;');
						$tpl->assign('descuento', isset($descuento[$rec['fecha']]) && $descuento[$rec['fecha']] != 0 ? number_format($descuento[$rec['fecha']], 2, '.', ',') : '&nbsp;');

						$totales['venta_puerta'] += $rec['venta_puerta'];
						$totales['reparto'] += isset($reparto[$rec['fecha']]) ? $reparto[$rec['fecha']] : 0;
						$totales['devuelto'] += isset($devuelto[$rec['fecha']]) ? $devuelto[$rec['fecha']] : 0;
						$totales['descuento'] += isset($descuento[$rec['fecha']]) ? $descuento[$rec['fecha']] : 0;

						$sobrante = $total_pan
									- $rec['venta_puerta']
									- (isset($reparto[$rec['fecha']]) ? $reparto[$rec['fecha']] : 0)
									- (isset($descuento[$rec['fecha']]) ? $descuento[$rec['fecha']] : 0)
									- (isset($_REQUEST['pasteles']) ?  (isset($pastel_entregado[$rec['fecha']]) ? $pastel_entregado[$rec['fecha']] : 0) + (isset($pastel_anticipo[$rec['fecha']]) ? $pastel_anticipo[$rec['fecha']] : 0) : 0);

						$tpl->assign('sobrante_hoy', $sobrante != 0 ? number_format($sobrante, 2, '.', ',') : '&nbsp;');
						$tpl->assign('pan_contado', isset($pan_contado[$rec['fecha']]) && $pan_contado[$rec['fecha']] != 0 ? number_format($pan_contado[$rec['fecha']], 2, '.', ',') : '&nbsp;');

						$diferencia = isset($pan_contado[$rec['fecha']]) && $pan_contado[$rec['fecha']] != 0 ? $pan_contado[$rec['fecha']] - $sobrante : 0;

						$tpl->assign('diferencia', $diferencia != 0 ? number_format($diferencia, 2, '.', ',') : '&nbsp;');
						$tpl->assign('color_diferencia', $diferencia < 0 ? 'red' : 'blue');

						$totales['diferencia'] += $diferencia;

						$dias = $rec['dia'];
					}
					if ($num_cia != NULL) {
						foreach ($totales as $key => $value) {
							$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2.' : 'reporte1.') . $key, number_format($value, 2, '.', ','));
						}

						$efectivo_produccion = $totales['efectivo'] / $totales['produccion'];if (!$totales['produccion']) echo $totales['produccion'];
						$promedio_faltante = $totales['diferencia'] / $dias;
						$promedio_venta_puerta = $totales['venta_puerta'] / $dias;
						$diferencia_produccion = $totales['diferencia'] * 100 / ($totales['produccion'] + $totales['pan_comprado'] - (isset($_REQUEST['pasteles']) ? $totales['pasteles'] : 0));
						$pfaltante = $promedio_faltante * 100 / $promedio_venta_puerta;

						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.efectivo_produccion', number_format($efectivo_produccion, 2, '.', ','));
						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.promedio_faltante', number_format($promedio_faltante, 2, '.', ','));
						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.diferencia_produccion', number_format($diferencia_produccion, 2, '.', ','));
						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.tipo_diferencia', $diferencia_produccion != 0 ? ($diferencia_produccion < 0 ? ' Faltante' : ' Sobrante') : '');
						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.pdevuelto', $pdevuelto > 0 ? '<span style="color:#C00; font-size:8pt;">(' . number_format($pdevuelto, 2) . '%)</span> ' : '');
						$tpl->assign((isset($_REQUEST['pasteles']) ? 'reporte2' : 'reporte1') . '.pfaltante', $pfaltante != 0 ? '<span style="color:#C00; font-size:8pt;">(' . number_format($pfaltante, 2) . '%)</span> ' : '');
					}
				}

				$tpl->printToScreen();
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ConsultaPruebaPan.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

	$condiciones[] = 'num_cia <= 300';

	if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 50, 57, 48, 62))) {
		$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
	}

	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
			LEFT JOIN
				catalogo_operadoras co
					USING
						(
							idoperadora
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);

	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}
else {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

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

	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}

	$sql = '
		SELECT
			idoperadora
				AS
					id,
			nombre_operadora
				AS
					nombre
		FROM
			catalogo_operadoras
		WHERE
			iduser IS NOT NULL
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);

	foreach ($admins as $a) {
		$tpl->newBlock('operadora');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', utf8_encode($a['nombre']));
	}
}

$tpl->printToScreen();
?>
