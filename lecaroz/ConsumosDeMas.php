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
	12 => 'DICIMEBRE'
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

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = 'cod_turno IN (1, 2, 3, 4, 8)';
			
			$condiciones[] = "reporte_consumos_mas = TRUE";
			
			$condiciones[] = 'tipo_mov = TRUE';
			
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
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 48, 50, 62))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							CASE
								WHEN cod_turno = 1 THEN
									frances_dia
								WHEN cod_turno = 2 THEN
									frances_noche
								WHEN cod_turno = 3 THEN
									bizcochero
								WHEN cod_turno = 4 THEN
									repostero
								WHEN cod_turno = 8 THEN
									piconero
							END
						FROM
							catalogo_avio_autorizado
						WHERE
							num_cia = result.num_cia
							AND codmp = result.cod
					)
						AS consumo_autorizado
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							CASE
								WHEN grasa = TRUE THEN
									38
								WHEN azucar = TRUE THEN
									4
								ELSE
									codmp
							END
								AS cod,
							CASE
								WHEN grasa = TRUE THEN
									\'GRASAS\'
								WHEN azucar = TRUE THEN
									\'AZUCAR\'
								ELSE
									cmp.nombre
							END
								AS nombre_mp,
							cod_turno,
							SUM(consumo)
								AS consumo,
							SUM(costo)
								AS costo
						FROM
							(
								SELECT
									num_cia,
									codmp,
									cod_turno,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 
											(
												CASE
													WHEN mv.cod_turno IN (1, 2) THEN
														44
													ELSE
														1
												END
											)
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									)
										AS consumo,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 
											(
												CASE
													WHEN mv.cod_turno IN (1, 2) THEN
														44
													ELSE
														1
												END
											)
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									) * (
										SELECT
											precio_unidad
										FROM
											inventario_real
										WHERE
											num_cia = mv.num_cia
											AND codmp = mv.codmp
									)
										AS costo
								FROM
									mov_inv_real mv
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
									LEFT JOIN catalogo_administradores ca
										USING (idadministrador)
									LEFT JOIN catalogo_operadoras co
										USING (idoperadora)
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									codmp,
									grasa,
									cod_turno
							) result
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
						GROUP BY
							num_cia,
							nombre_cia,
							cod,
							nombre_mp,
							cod_turno
					) result
				
				ORDER BY
					num_cia,
					cod,
					cod_turno
			';
			$tmp = $db->query($sql);
			
			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
			
			$tpl = new TemplatePower('plantillas/pan/' . ($isIpad ? 'ConsumosDeMasReporteIpad.tpl' : 'ConsumosDeMasReporte.tpl'));
			$tpl->prepare();
			
			if ($tmp) {
				$result = array();
				
				$num_cia = NULL;
				foreach ($tmp as $t) {
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];

						$result[$num_cia] = array(
							'nombre_cia'	=> $t['nombre_cia'],
							'productos'		=> array()
						);

						$cod = NULL;
						
						// $result[$num_cia] = array(
						// 	'nombre_cia' => $t['nombre_cia'],
						// 	'productos'  => array(
						// 		4 => array(
						// 			'nombre_mp' => 'AZUCAR SEGUNDA',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		),
						// 		21 => array(
						// 			'nombre_mp' => 'TOUPAN',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		),
						// 		38 => array(
						// 			'nombre_mp' => 'GRASAS',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		),
						// 		67 => array(
						// 			'nombre_mp' => 'ULTRAPAN',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		),
						// 		148 => array(
						// 			'nombre_mp' => 'HUEVO',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		),
						// 		149 => array(
						// 			'nombre_mp' => 'LEVADURA',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0,
						// 				'costo_diferencia'   => 0
						// 			)))
						// 		)
						// 	)
						// );
					}

					if ($cod != $t['cod'])
					{
						$cod = $t['cod'];

						$result[$t['num_cia']]['productos'][$t['cod']] = array(
							'nombre_mp'	=> $t['nombre_mp'],
							'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
								'consumo_autorizado'	=> 0,
								'consumo'				=> 1,
								'costo'					=> 0,
								'diferencia'			=> 0,
								'costo_diferencia'		=> 0
							)))
						);
					}
					
					$result[$t['num_cia']]['productos'][$t['cod']]['turnos'][$t['cod_turno']] = array(
						'consumo_autorizado' => round($t['consumo_autorizado'], 3),
						'consumo'            => round($t['consumo'], 3),
						'costo'              => round($t['costo'], 2),
						'diferencia'         => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? round($t['consumo'], 3) - round($t['consumo_autorizado'], 3) : 0,
						'costo_diferencia'   => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? (round($t['consumo'], 3) - round($t['consumo_autorizado'], 3)) * round($t['costo'], 2) / round($t['consumo'], 3) : 0
					);
				}
				
				/*
				@ Obtener costo del mes
				*/
				$sql = '
					SELECT
						num_cia,
						cod_turno,
						SUM(costo_diferencia)
							AS costo_mes
					FROM
						(
							SELECT
								*,
								CASE
									WHEN consumo_autorizado > 0 AND consumo - consumo_autorizado > 0 THEN
										consumo - consumo_autorizado
									ELSE
										0
								END
									AS diferencia,
								CASE
									WHEN consumo_autorizado > 0 AND consumo - consumo_autorizado > 0 THEN
										(consumo - consumo_autorizado) * costo / consumo
									ELSE
										0
								END
									AS costo_diferencia
								FROM
								(
									SELECT
										*,
										(
											SELECT
												CASE
													WHEN cod_turno = 1 THEN
														frances_dia
													WHEN cod_turno = 2 THEN
														frances_noche
													WHEN cod_turno = 3 THEN
														bizcochero
													WHEN cod_turno = 4 THEN
														repostero
													WHEN cod_turno = 8 THEN
														piconero
												END
											FROM
												catalogo_avio_autorizado
											WHERE
												num_cia = result.num_cia
												AND codmp = result.cod
										)
											AS consumo_autorizado
									FROM
										(
											SELECT
												num_cia,
												fecha,
												CASE
													WHEN grasa = TRUE THEN
														38
													WHEN azucar = TRUE THEN
														4
													ELSE
														codmp
												END
													AS cod,
												cod_turno,
												SUM(consumo)
													AS consumo,
												SUM(costo)
													AS costo
											FROM
												(
													SELECT
														num_cia,
														fecha,
														codmp,
														cod_turno,
														grasa,
														azucar,
														SUM(cantidad) / (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														)
															AS consumo,
														SUM(cantidad) / (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														) * (
															SELECT
																precio_unidad
															FROM
																inventario_real
															WHERE
																num_cia = mv.num_cia
																AND codmp = mv.codmp
														) * (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														)
															AS costo
													FROM
														mov_inv_real mv
														LEFT JOIN catalogo_mat_primas cmp
															USING (codmp)
														LEFT JOIN catalogo_companias cc
															USING (num_cia)
														LEFT JOIN catalogo_administradores ca
															USING (idadministrador)
														LEFT JOIN catalogo_operadoras co
															USING (idoperadora)
													WHERE
														' . implode(' AND ', $condiciones) . '
													GROUP BY
														num_cia,
														fecha,
														codmp,
														grasa,
														azucar,
														cod_turno
												) result
											GROUP BY
												num_cia,
												fecha,
												cod,
												cod_turno
										) result
								) result
						) result
					GROUP BY
						num_cia,
						cod_turno
					ORDER BY
						num_cia,
						cod_turno
				';
				
				$tmp = $db->query($sql);
				
				$costos = array();
				
				$num_cia = NULL;
				foreach ($tmp as $t) {
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];
						
						$costos[$num_cia] = array(
							1 => 0,
							2 => 0,
							3 => 0,
							4 => 0,
							8 => 0
						);
					}
					
					$costos[$num_cia][$t['cod_turno']] = $t['costo_mes'];
				}
				
				foreach ($result as $num_cia => $data_cia) {
					$tpl->newBlock('reporte');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $data_cia['nombre_cia']);
					$tpl->assign('dia', $fecha_pieces[0]);
					$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
					$tpl->assign('anio', $fecha_pieces[2]);
					
					$totales = array(
						1 => 0,
						2 => 0,
						3 => 0,
						4 => 0,
						8 => 0
					);
					
					foreach ($data_cia['productos'] as $codmp => $data_mp) {
						$tpl->newBlock('row');
						
						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', $data_mp['nombre_mp']);
						
						foreach ($data_mp['turnos'] as $turno => $data_turno) {
							$tpl->assign('aut' . $turno, $data_turno['consumo_autorizado'] > 0 ? number_format($data_turno['consumo_autorizado'], 3, '.', ',') : '&nbsp;');
							$tpl->assign('con' . $turno, $data_turno['consumo'] > 0 ? number_format($data_turno['consumo'], 3, '.', ',') : '&nbsp;');
							$tpl->assign('dif' . $turno, $data_turno['diferencia'] > 0 ? number_format($data_turno['diferencia'], 3, '.', ',') : '&nbsp;');
							$tpl->assign('costo' . $turno, $data_turno['costo_diferencia'] > 0 ? number_format($data_turno['costo_diferencia'], 2, '.', ',') : '&nbsp;');
							
							$totales[$turno] += $data_turno['costo_diferencia'];
						}
					}
					
					foreach ($totales as $turno => $total) {
						$tpl->assign('reporte.costo' . $turno, $total > 0 ? number_format($total, 2, '.', ',') : '&nbsp;');
					}
					
					foreach ($costos[$num_cia] as $turno => $costo) {
						$tpl->assign('reporte.mes' . $turno, $costo > 0 ? number_format($costo, 2, '.', ',') : '&nbsp;');
					}
					
					$tpl->assign('reporte.total', array_sum($costos[$num_cia]) > 0 ? number_format(array_sum($costos[$num_cia]), 2, '.', ',') : '&nbsp;');
				}
			}
			
			$tpl->printToScreen();
		break;

		case 'reporte2':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = 'cod_turno IN (1, 2, 3, 4, 8)';
			
			$condiciones[] = "reporte_consumos_mas = TRUE";
			
			$condiciones[] = 'tipo_mov = TRUE';
			
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
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 48, 50, 62))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							CASE
								WHEN cod_turno = 1 THEN
									frances_dia
								WHEN cod_turno = 2 THEN
									frances_noche
								WHEN cod_turno = 3 THEN
									bizcochero
								WHEN cod_turno = 4 THEN
									repostero
								WHEN cod_turno = 8 THEN
									piconero
							END
						FROM
							catalogo_avio_autorizado
						WHERE
							num_cia = result.num_cia
							AND codmp = result.cod
					)
						AS consumo_autorizado
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							CASE
								WHEN grasa = TRUE THEN
									38
								WHEN azucar = TRUE THEN
									4
								ELSE
									codmp
							END
								AS cod,
							CASE
								WHEN grasa = TRUE THEN
									\'GRASAS\'
								WHEN azucar = TRUE THEN
									\'AZUCAR\'
								ELSE
									cmp.nombre
							END
								AS nombre_mp,
							cod_turno,
							SUM(consumo)
								AS consumo,
							SUM(costo)
								AS costo
						FROM
							(
								SELECT
									num_cia,
									codmp,
									cod_turno,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 
											(
												CASE
													WHEN mv.cod_turno IN (1, 2) THEN
														44
													ELSE
														1
												END
											)
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									)
										AS consumo,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 
											(
												CASE
													WHEN mv.cod_turno IN (1, 2) THEN
														44
													ELSE
														1
												END
											)
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									) * (
										SELECT
											precio_unidad
										FROM
											inventario_real
										WHERE
											num_cia = mv.num_cia
											AND codmp = mv.codmp
									)
										AS costo
								FROM
									mov_inv_real mv
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
									LEFT JOIN catalogo_administradores ca
										USING (idadministrador)
									LEFT JOIN catalogo_operadoras co
										USING (idoperadora)
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									codmp,
									grasa,
									cod_turno
							) result
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
						GROUP BY
							num_cia,
							nombre_cia,
							cod,
							nombre_mp,
							cod_turno
					) result
				
				WHERE
					cod = ' . $_REQUEST['codmp'] . '
				
				ORDER BY
					num_cia,
					cod,
					cod_turno
			';
			$tmp = $db->query($sql);
			
			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
			
			$tpl = new TemplatePower('plantillas/pan/' . ($isIpad ? 'ConsumosDeMasReporteIpad2.tpl' : 'ConsumosDeMasReporte2.tpl'));
			$tpl->prepare();
			
			if ($tmp) {
				$result = array();
				
				$num_cia = NULL;
				foreach ($tmp as $t) {
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];
						
						$result[$num_cia] = array(
							'nombre_cia' => $t['nombre_cia'],
							'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
								'consumo_autorizado' => 0,
								'consumo'            => 0,
								'costo'              => 0,
								'diferencia'         => 0,
								'costo_diferencia'   => 0
							)))
						);
					}
					
					$result[$t['num_cia']]['turnos'][$t['cod_turno']] = array(
						'consumo_autorizado' => round($t['consumo_autorizado'], 3),
						'consumo'            => round($t['consumo'], 3),
						'costo'              => round($t['costo'], 2),
						'diferencia'         => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? round($t['consumo'], 3) - round($t['consumo_autorizado'], 3) : 0,
						'costo_diferencia'   => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? (round($t['consumo'], 3) - round($t['consumo_autorizado'], 3)) * round($t['costo'], 2) / round($t['consumo'], 3) : 0
					);
				}
				
				/*
				@ Obtener costo del mes
				*/
				$sql = '
					SELECT
						num_cia,
						cod_turno,
						SUM(costo_diferencia)
							AS costo_mes
					FROM
						(
							SELECT
								*,
								CASE
									WHEN consumo_autorizado > 0 AND consumo - consumo_autorizado > 0 THEN
										consumo - consumo_autorizado
									ELSE
										0
								END
									AS diferencia,
								CASE
									WHEN consumo_autorizado > 0 AND consumo - consumo_autorizado > 0 THEN
										(consumo - consumo_autorizado) * costo / consumo
									ELSE
										0
								END
									AS costo_diferencia
								FROM
								(
									SELECT
										*,
										(
											SELECT
												CASE
													WHEN cod_turno = 1 THEN
														frances_dia
													WHEN cod_turno = 2 THEN
														frances_noche
													WHEN cod_turno = 3 THEN
														bizcochero
													WHEN cod_turno = 4 THEN
														repostero
													WHEN cod_turno = 8 THEN
														piconero
												END
											FROM
												catalogo_avio_autorizado
											WHERE
												num_cia = result.num_cia
												AND codmp = result.cod
										)
											AS consumo_autorizado
									FROM
										(
											SELECT
												num_cia,
												fecha,
												CASE
													WHEN grasa = TRUE THEN
														38
													WHEN azucar = TRUE THEN
														4
													ELSE
														codmp
												END
													AS cod,
												cod_turno,
												SUM(consumo)
													AS consumo,
												SUM(costo)
													AS costo
											FROM
												(
													SELECT
														num_cia,
														fecha,
														codmp,
														cod_turno,
														grasa,
														azucar,
														SUM(cantidad) / (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														)
															AS consumo,
														SUM(cantidad) / (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														) * (
															SELECT
																precio_unidad
															FROM
																inventario_real
															WHERE
																num_cia = mv.num_cia
																AND codmp = mv.codmp
														) * (
															SELECT
																SUM(cantidad) / 
																(
																	CASE
																		WHEN mv.cod_turno IN (1, 2) THEN
																			44
																		ELSE
																			1
																	END
																)
															FROM
																mov_inv_real
															WHERE
																num_cia = mv.num_cia
																AND fecha = mv.fecha
																AND codmp = 1
																AND cod_turno = mv.cod_turno
																AND tipo_mov = TRUE
														)
															AS costo
													FROM
														mov_inv_real mv
														LEFT JOIN catalogo_mat_primas cmp
															USING (codmp)
														LEFT JOIN catalogo_companias cc
															USING (num_cia)
														LEFT JOIN catalogo_administradores ca
															USING (idadministrador)
														LEFT JOIN catalogo_operadoras co
															USING (idoperadora)
													WHERE
														' . implode(' AND ', $condiciones) . '
													GROUP BY
														num_cia,
														fecha,
														codmp,
														grasa,
														azucar,
														cod_turno
												) result
											GROUP BY
												num_cia,
												fecha,
												cod,
												cod_turno
										) result
									WHERE
										cod = ' . $_REQUEST['codmp'] . '
								) result
						) result
					GROUP BY
						num_cia,
						cod_turno
					ORDER BY
						num_cia,
						cod_turno
				';
				
				$tmp = $db->query($sql);
				
				$costos = array();
				
				$num_cia = NULL;
				foreach ($tmp as $t) {
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];
						
						$costos[$num_cia] = array(
							1 => 0,
							2 => 0,
							3 => 0,
							4 => 0,
							8 => 0
						);
					}
					
					$costos[$num_cia][$t['cod_turno']] = $t['costo_mes'];
				}
				
				$tpl->newBlock('reporte');

				$producto = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = {$_REQUEST['codmp']}");
				
				$tpl->assign('codmp', $_REQUEST['codmp']);
				$tpl->assign('nombre_mp', $producto[0]['nombre']);
				$tpl->assign('dia', $fecha_pieces[0]);
				$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
				$tpl->assign('anio', $fecha_pieces[2]);
				
				$totales = array(
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					8 => 0
				);

				foreach ($result as $num_cia => $data_cia) {
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $data_cia['nombre_cia']);
					
					foreach ($data_cia['turnos'] as $turno => $data_turno) {
						$tpl->assign('aut' . $turno, $data_turno['consumo_autorizado'] > 0 ? number_format($data_turno['consumo_autorizado'], 3, '.', ',') : '&nbsp;');
						$tpl->assign('con' . $turno, $data_turno['consumo'] > 0 ? number_format($data_turno['consumo'], 3, '.', ',') : '&nbsp;');
						$tpl->assign('dif' . $turno, $data_turno['diferencia'] > 0 ? number_format($data_turno['diferencia'], 3, '.', ',') : '&nbsp;');
						$tpl->assign('costo' . $turno, $data_turno['costo_diferencia'] > 0 ? number_format($data_turno['costo_diferencia'], 2, '.', ',') : '&nbsp;');
						
						$totales[$turno] += $data_turno['costo_diferencia'];
					}
					
					foreach ($totales as $turno => $total) {
						$tpl->assign('reporte.costo' . $turno, $total > 0 ? number_format($total, 2, '.', ',') : '&nbsp;');
					}
					
					foreach ($costos[$num_cia] as $turno => $costo) {
						$tpl->assign('reporte.mes' . $turno, $costo > 0 ? number_format($costo, 2, '.', ',') : '&nbsp;');
					}
					
					$tpl->assign('reporte.total', array_sum($costos[$num_cia]) > 0 ? number_format(array_sum($costos[$num_cia]), 2, '.', ',') : '&nbsp;');
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'exportar':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = 'cod_turno IN (1, 2, 3, 4, 8)';
			
			$condiciones[] = "reporte_consumos_mas = TRUE";
			
			$condiciones[] = 'tipo_mov = TRUE';
			
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
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 48, 50, 62))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							CASE
								WHEN cod_turno = 1 THEN
									frances_dia
								WHEN cod_turno = 2 THEN
									frances_noche
								WHEN cod_turno = 3 THEN
									bizcochero
								WHEN cod_turno = 4 THEN
									repostero
								WHEN cod_turno = 8 THEN
									piconero
							END
						FROM
							catalogo_avio_autorizado
						WHERE
							num_cia = result.num_cia
							AND codmp = result.cod
					)
						AS consumo_autorizado
				FROM
					(
						SELECT
							nombre_administrador
								AS administrador,
							nombre_operadora
								AS operadora,
							num_cia,
							nombre_corto
								AS nombre_cia,
							CASE
								WHEN grasa = TRUE THEN
									38
								WHEN azucar = TRUE THEN
									4
								ELSE
									codmp
							END
								AS cod,
							CASE
								WHEN grasa = TRUE THEN
									\'GRASAS\'
								WHEN azucar = TRUE THEN
									\'AZUCAR\'
								ELSE
									cmp.nombre
							END
								AS nombre_mp,
							cod_turno,
							SUM(consumo)
								AS consumo,
							SUM(costo)
								AS costo
						FROM
							(
								SELECT
									num_cia,
									codmp,
									cod_turno,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 44
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									)
										AS consumo,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 44
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									) * (
										SELECT
											precio_unidad
										FROM
											inventario_real
										WHERE
											num_cia = mv.num_cia
											AND codmp = mv.codmp
									)
										AS costo
								FROM
									mov_inv_real mv
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
									LEFT JOIN catalogo_administradores ca
										USING (idadministrador)
									LEFT JOIN catalogo_operadoras co
										USING (idoperadora)
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									codmp,
									cod_turno
							) result
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_administradores ca
								USING (idadministrador)
							LEFT JOIN catalogo_operadoras co
								USING (idoperadora)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
						GROUP BY
							administrador,
							operadora,
							num_cia,
							nombre_cia,
							cod,
							nombre_mp,
							cod_turno
					) result
				
				ORDER BY
					administrador,
					operadora,
					num_cia,
					cod,
					cod_turno
			';
			$tmp = $db->query($sql);
			
			$data = '';
			
			if ($tmp) {
				$result = array();
				
				$administrador = NULL;
				foreach ($tmp as $t) {
					if ($administrador != $t['administrador']) {
						$administrador = $t['administrador'];
						
						$result[$administrador] = array();
						
						$operadora = NULL;
					}
					
					if ($operadora != $t['operadora']) {
						$operadora = $t['operadora'];
						
						$result[$administrador][$operadora] = array();
						
						$num_cia = NULL;
					}
					
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];

						$result[$administrador][$operadora][$num_cia] = array(
							'nombre_cia'	=> $t['nombre_cia'],
							'productos'		=> array()
						);

						$cod = NULL;
						
						// $result[$administrador][$operadora][$num_cia] = array(
						// 	'nombre_cia' => $t['nombre_cia'],
						// 	'productos'  => array(
						// 		4 => array(
						// 			'nombre_mp' => 'AZUCAR SEGUNDA',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		),
						// 		21 => array(
						// 			'nombre_mp' => 'TOUPAN',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		),
						// 		38 => array(
						// 			'nombre_mp' => 'GRASAS',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		),
						// 		67 => array(
						// 			'nombre_mp' => 'ULTRAPAN',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		),
						// 		148 => array(
						// 			'nombre_mp' => 'HUEVO',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		),
						// 		149 => array(
						// 			'nombre_mp' => 'LEVADURA',
						// 			'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
						// 				'consumo_autorizado' => 0,
						// 				'consumo'            => 0,
						// 				'costo'              => 0,
						// 				'diferencia'         => 0
						// 			)))
						// 		)
						// 	)
						// );
					}

					if ($cod != $t['cod'])
					{
						$cod = $t['cod'];

						$result[$administrador][$operadora][$num_cia]['productos'][$t['cod']] = array(
							'nombre_mp'	=> $t['nombre_mp'],
							'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
								'consumo_autorizado'	=> 0,
								'consumo'				=> 1,
								'costo'					=> 0,
								'diferencia'			=> 0
							)))
						);
					}
					
					$result[$administrador][$operadora][$t['num_cia']]['productos'][$t['cod']]['turnos'][$t['cod_turno']] = array(
						'consumo_autorizado' => round($t['consumo_autorizado'], 3),
						'consumo'            => round($t['consumo'], 3),
						'costo'              => round($t['costo'], 2),
						'diferencia'         => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? round($t['consumo'], 3) - round($t['consumo_autorizado'], 3) : 0
					);
				}
				
				$data .= '"CONSUMOS DE MAS ' . $_meses[intval($fecha_pieces[1], 10)] . ' DE ' . $fecha_pieces[2] . '"' . "\r\n";
				$data .= "\r\n";
				$data .= '"","","","","","FRANCESERO DE DIA","","","","FRANCESERO DE NOCHE","","","","BIZCOCHERO","","","","REPOSTERO"' . "\r\n";
				$data .= '"ADMINISTRADOR","OPERADORA","#","PANADERIA","PRODUCTO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO"' . "\r\n";
				
				foreach ($result as $admin => $data_admin) {
					foreach ($data_admin as $operadora => $data_operadora) {
						foreach ($data_operadora as $num_cia => $data_cia) {
							foreach ($data_cia['productos'] as $cod => $data_producto) {
								$data .= '"' . $administrador . '","' . $operadora . '","' . $num_cia . '","' . $data_cia['nombre_cia'] . '","' . $cod . ' ' . $data_producto['nombre_mp'] . '"';
								
								foreach ($data_producto['turnos'] as $turno => $data_turno) {
									$data .= ',"' . implode('","', $data_turno) . '"';
									
								}
								
								$data .= "\r\n";
							}
						}
					}
				}
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ConsumosDeMas.csv"');
			
			echo $data;
		break;

		case 'exportar2':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = 'cod_turno IN (1, 2, 3, 4, 8)';
			
			$condiciones[] = "reporte_consumos_mas = TRUE";
			
			$condiciones[] = 'tipo_mov = TRUE';
			
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
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 48, 50, 62))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							CASE
								WHEN cod_turno = 1 THEN
									frances_dia
								WHEN cod_turno = 2 THEN
									frances_noche
								WHEN cod_turno = 3 THEN
									bizcochero
								WHEN cod_turno = 4 THEN
									repostero
								WHEN cod_turno = 8 THEN
									piconero
							END
						FROM
							catalogo_avio_autorizado
						WHERE
							num_cia = result.num_cia
							AND codmp = result.cod
					)
						AS consumo_autorizado
				FROM
					(
						SELECT
							nombre_administrador
								AS administrador,
							nombre_operadora
								AS operadora,
							num_cia,
							nombre_corto
								AS nombre_cia,
							CASE
								WHEN grasa = TRUE THEN
									38
								WHEN azucar = TRUE THEN
									4
								ELSE
									codmp
							END
								AS cod,
							CASE
								WHEN grasa = TRUE THEN
									\'GRASAS\'
								WHEN azucar = TRUE THEN
									\'AZUCAR\'
								ELSE
									cmp.nombre
							END
								AS nombre_mp,
							cod_turno,
							SUM(consumo)
								AS consumo,
							SUM(costo)
								AS costo
						FROM
							(
								SELECT
									num_cia,
									codmp,
									cod_turno,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 44
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									)
										AS consumo,
									SUM(cantidad) / (
										SELECT
											SUM(cantidad) / 44
										FROM
											mov_inv_real
										WHERE
											num_cia = mv.num_cia
											AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
											AND codmp = 1
											AND cod_turno = mv.cod_turno
											AND tipo_mov = TRUE
									) * (
										SELECT
											precio_unidad
										FROM
											inventario_real
										WHERE
											num_cia = mv.num_cia
											AND codmp = mv.codmp
									)
										AS costo
								FROM
									mov_inv_real mv
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
									LEFT JOIN catalogo_administradores ca
										USING (idadministrador)
									LEFT JOIN catalogo_operadoras co
										USING (idoperadora)
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									codmp,
									cod_turno
							) result
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_administradores ca
								USING (idadministrador)
							LEFT JOIN catalogo_operadoras co
								USING (idoperadora)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
						GROUP BY
							administrador,
							operadora,
							num_cia,
							nombre_cia,
							cod,
							nombre_mp,
							cod_turno
					) result

				WHERE
					cod = ' . $_REQUEST['codmp'] . '
				
				ORDER BY
					administrador,
					operadora,
					num_cia,
					cod,
					cod_turno
			';
			$tmp = $db->query($sql);
			
			$data = '';
			
			if ($tmp) {
				$result = array();
				
				$administrador = NULL;
				foreach ($tmp as $t) {
					if ($administrador != $t['administrador']) {
						$administrador = $t['administrador'];
						
						$result[$administrador] = array();
						
						$operadora = NULL;
					}
					
					if ($operadora != $t['operadora']) {
						$operadora = $t['operadora'];
						
						$result[$administrador][$operadora] = array();
						
						$num_cia = NULL;
					}
					
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];
						
						$result[$administrador][$operadora][$num_cia] = array(
							'nombre_cia' => $t['nombre_cia'],
							'turnos' => array_combine(array(1, 2, 3, 4, 8), array_fill(0, 5, array(
								'consumo_autorizado' => 0,
								'consumo'            => 0,
								'costo'              => 0,
								'diferencia'         => 0
							)))
						);
					}
					
					$result[$administrador][$operadora][$t['num_cia']]['turnos'][$t['cod_turno']] = array(
						'consumo_autorizado' => round($t['consumo_autorizado'], 3),
						'consumo'            => round($t['consumo'], 3),
						'costo'              => round($t['costo'], 2),
						'diferencia'         => round($t['consumo_autorizado'], 3) > 0 && round($t['consumo_autorizado'], 3) < round($t['consumo'], 3) ? round($t['consumo'], 3) - round($t['consumo_autorizado'], 3) : 0
					);
				}

				$producto = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = {$_REQUEST['codmp']}");
				
				$data .= '"' . $_REQUEST['codmp'] . ' ' . $producto[0]['nombre'] . '"' . "\r\n";
				$data .= '"CONSUMOS DE MAS ' . $_meses[intval($fecha_pieces[1], 10)] . ' DE ' . $fecha_pieces[2] . '"' . "\r\n";
				$data .= "\r\n";
				$data .= '"","","","","","FRANCESERO DE DIA","","","","FRANCESERO DE NOCHE","","","","BIZCOCHERO","","","","REPOSTERO"' . "\r\n";
				$data .= '"ADMINISTRADOR","OPERADORA","#","PANADERIA","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO","AUTORIZADO","CONSUMO","COSTO","EXCESO"' . "\r\n";
				
				foreach ($result as $admin => $data_admin) {
					foreach ($data_admin as $operadora => $data_operadora) {
						foreach ($data_operadora as $num_cia => $data_cia) {
							$data .= '"' . $administrador . '","' . $operadora . '","' . $num_cia . '","' . $data_cia['nombre_cia'] . '"';
							
							foreach ($data_cia['turnos'] as $turno => $data_turno) {
								$data .= ',"' . implode('","', $data_turno) . '"';
								
							}
							
							$data .= "\r\n";
						}
					}
				}
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ConsumosDeMas.csv"');
			
			echo $data;
		break;

	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ConsumosDeMas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 6, 14, 18, 19, 20, 24, 37, 42, 48, 50, 62))) {
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
		$tpl->assign('nombre', utf8_encode($a['nombre']));
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

	$sql = '
		SELECT
			*
		FROM
			(
				SELECT
					CASE
						WHEN grasa = TRUE THEN
							38
						WHEN azucar = TRUE THEN
							4
						ELSE
							codmp
					END
						AS value,
					CASE
						WHEN grasa = TRUE THEN
							\'GRASAS\'
						WHEN azucar = TRUE THEN
							\'AZUCAR\'
						ELSE
							nombre
					END
						AS text
				FROM
					catalogo_mat_primas
				WHERE
					reporte_consumos_mas = TRUE
			) result
		GROUP BY
			value,
			text
		ORDER BY
			value
	';
	$productos = $db->query($sql);
	
	foreach ($productos as $p) {
		$tpl->newBlock('codmp');
		$tpl->assign('value', $p['value']);
		$tpl->assign('text', utf8_encode($p['text']));
	}
}

$tpl->printToScreen();
?>
