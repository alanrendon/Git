<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasPanaderiasInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('anio', date('d') < 5 ? date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('Y', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));
			$tpl->assign(date('d') < 5 ? date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('n', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))), ' selected');
			$tpl->assign('dia', date('d') < 5 ? date('d', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('j', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));
			
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
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			/*
			@ Número de días del mes solicitado
			*/
			$dias_del_mes = date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			/*
			@ Crear rango con los días del mes
			*/
			$dias = range(1, $dias_del_mes);
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			
			if (isset($_REQUEST['dia']) && $_REQUEST['dia'] > 0) {
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio']));
			}
			else {
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			}
			
			/*
			@ [05-Ene-2011] Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					matriz,
					sucursal,
					porcentaje,
					CASE
						WHEN sucursal = matriz THEN
							2
						ELSE
							1
					END
						AS
							tipo
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					matriz,
					tipo,
					sucursal
			';
			$result = $db->query($sql);
			
			$sucursales = array();
			if ($result) {
				/*
				@ Reordenar porcentajes
				*/
				foreach ($result as $rec) {
					$porcentajes[$rec['matriz']][] = array(
						'sucursal' => $rec['sucursal'],
						'porcentaje' => $rec['porcentaje'],
						'tipo' => $rec['tipo']
					);
					
					$sucursales[$rec['sucursal']] = $rec['matriz'];
				}
			}
			
			/*
			@ Condiciones [Estado de Cuenta]
			*/
			$condiciones1 = array();
			$condiciones2 = array();
			
			$condiciones1[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones1[] = 'cod_mov IN (1, 16, 44, 99)';
			
			$condiciones1[] = 'num_cia BETWEEN 1 AND 899';
			
			$condiciones1[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? '\'FALSE\'' : '\'TRUE\'') : '\'TRUE\'';
			
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
					$condiciones1[] = '((num_cia IN (' . implode(', ', $cias) . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . implode(', ', $cias) . '))';
				}
			}
			
			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();
				
				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}
				
				if (count($omitir) > 0) {
					$condiciones1[] = '((num_cia NOT IN (' . implode(', ', $omitir) . ') AND num_cia_sec IS NULL) OR num_cia_sec NOT IN (' . implode(', ', $omitir) . '))';
				}
			}
			
			$condiciones2[] = '\'TRUE\'';
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (count($sucursales) > 0) {
				$condiciones2[] = 'num_cia NOT IN (' . implode(', ', array_keys($sucursales)) . ')';
			}
			
			/*
			@ Obtener depositos del mes [Estado de Cuenta]
			*/
			$sql = '
				SELECT
					num_cia,
					dia,
					SUM(importe)
						AS
							importe
				FROM
						(
							SELECT
								CASE
									WHEN num_cia_sec IS NULL THEN
										num_cia
									WHEN num_cia_sec IS NOT NULL THEN
										num_cia_sec
								END
									AS
										num_cia,
								EXTRACT(day from fecha)
									AS
										dia,
								importe
							FROM
									estado_cuenta
								LEFT JOIN
									catalogo_companias
										USING
											(
												num_cia
											)
							WHERE
								' . implode(' AND ', $condiciones1) . '
						)
							AS
								result
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				GROUP BY
					num_cia,
					dia
			';
			
			/*
			@ [05-Ene-2011] Condiciones para desglose de puntos calientes
			*/
			$condiciones = array();
			
			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? '\'FALSE\'' : '\'TRUE\'') : '\'TRUE\'';
			
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
				
				/*
				@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
				*/
				foreach ($cias as $c) {
					if (isset($sucursales[$c])) {
						foreach ($porcentajes[$sucursales[$c]] as $p) {
							$cias[] = $p['sucursal'];
						}
					}
				}
				
				$cias = array_unique($cias);
				
				if (count($cias) > 0) {
					$condiciones[] = 'sucursal IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();
				
				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}
				
				if (count($omitir) > 0) {
					$condiciones[] = 'sucursal NOT IN (' . implode(', ', $omitir) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ [05-Ene-2011] Obtener registros para desglose de puntos calientes
			*/
			$sql .= '
				UNION
				
				SELECT
					sucursal
						AS
							num_cia,
					EXTRACT(day from \'' . $fecha1 . '\'::date)
						AS
							dia,
					0
						AS
							importe
				FROM
						porcentajes_puntos_calientes ppc
					LEFT JOIN
						catalogo_companias cc
							ON
								(
									cc.num_cia = ppc.sucursal
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			
			/*
			@ Condiciones [Ventas Zapaterias]
			*/
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'num_cia BETWEEN 900 AND 998';
			
			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? '\'TRUE\'' : '\'FALSE\'') : '\'TRUE\'';
			
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
			
			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
				$omitir = array();
				
				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}
				
				if (count($omitir) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ Obtener depositos del mes [Ventas Zapaterias]
			*/
			$sql .= '
				UNION
				
				SELECT
					num_cia,
					EXTRACT(day from fecha)
						AS
							dia,
					importe
				FROM
						ventas_zapaterias vz
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			
			
			
			$sql .= '
				ORDER BY
					num_cia,
					dia
			';
			
			$result = $db->query($sql);
			
			/*
			@ Reordenar depósitos
			*/
			$lista_cias = array();
			
			$depositos = array();
			
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$lista_cias[] = $num_cia;
					}
					
					$depositos[$rec['num_cia']][$rec['dia']] = $rec['importe'];
				}
			}
			
			/*
			@ [05-Ene-2011] Desglosar ventas de puntos calientes
			*/
			foreach ($sucursales as $sucursal => $matriz) {
				if (isset($depositos[$sucursal])) {
					if (!isset($depositos_matriz[$matriz])) {
						/*
						@ Obtener depósitos de la matriz
						*/
						$sql = '
							SELECT
								dia,
								SUM(importe)
									AS
										importe
							FROM
								(
									SELECT
										EXTRACT(day from fecha)
											AS
												dia,
										importe
									FROM
											estado_cuenta
										LEFT JOIN
											catalogo_companias
												USING
													(
														num_cia
													)
									WHERE
											((num_cia IN (' . $matriz . ') AND num_cia_sec IS NULL) OR num_cia_sec IN (' . $matriz . '))
										AND
											fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND
											cod_mov IN (1, 16, 44, 99)
								)
									AS
										result
							GROUP BY
								dia
							ORDER BY
								dia
						';
						
						$result = $db->query($sql);
						
						/*
						@ Reordenar los depósitos de la matriz
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_matriz[$matriz][$rec['dia']] = $rec['importe'];
							}
						}
						
						/*
						@ Obtener depósitos de sucursales
						*/
						$sql = '
							SELECT
								num_cia,
								EXTRACT(day from fecha)
									AS
										dia,
								importe
							FROM
								ventas_sucursales
							WHERE
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND
									num_cia
										IN
											(
												SELECT
													sucursal
												FROM
													porcentajes_puntos_calientes
												WHERE
													matriz = ' . $matriz . '
											)
							ORDER BY
								num_cia,
								dia
						';
						
						$result = $db->query($sql);
						
						/*
						@ Reordenar los depósitos de las sucursales
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_sucursal[$rec['num_cia']][$rec['dia']] = $rec['importe'];
							}
						}
						
						/*
						@ Desglosar depósitos entre todas las sucursales
						*/
						if (isset($depositos_matriz[$matriz])) {
							foreach ($depositos_matriz[$matriz] as $dia => $importe) {
								if ($importe > 0) {
									$total_sucursales = 0;
									foreach ($porcentajes[$matriz] as $por) {
										/*
										@ Es sucursal y no se ha generado la venta del día
										*/
										if ($por['tipo'] == 1 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$porcentaje = $por['porcentaje'] + round(mt_rand(-99, 99) / 100, 2);
											
											$importe_sucursal = round($importe * $porcentaje / 100, 2);
											
											$total_sucursales += $importe_sucursal;
											
											$depositos_sucursal[$por['sucursal']][$dia] = $importe_sucursal;
										}
										/*
										@ Es sucursal y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 1 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia];
										}
										/*
										@ Es matriz y no se ha generado la venta del día
										*/
										else if ($por['tipo'] == 2 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$depositos_sucursal[$por['sucursal']][$dia] = $importe - $total_sucursales;
											
											$total_sucursales += $importe - $total_sucursales;
										}
										/*
										@ Es matriz y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 2 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$depositos_sucursal[$por['sucursal']][$dia] = $depositos_sucursal[$por['sucursal']][$dia];
											
											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia];
										}
									}
									
									/*
									@ [17-Ago-2011] Comparar variación de los depósitos del día y la suma total de las sucursales
									*/
									if ($total_sucursales != $importe) {
										$dif = round($importe - $total_sucursales, 2);
										
										$depositos_sucursal[$por['sucursal']][$dia] += $dif;
									}
								}
							}
						}
					}
					
					if (isset($depositos_sucursal[$sucursal])) {
						$depositos[$sucursal] = $depositos_sucursal[$sucursal];
					}
					else {
						unset($depositos[$sucursal]);
					}
				}
			}
			
			if (count($depositos) > 0) {
				/*
				@ Condiciones para obtener facturas electrónicas (PAGADAS)
				*/
				$condiciones = array();
				
				$condiciones[] = 'fecha_pago BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo = 2';
				
				$condiciones[] = 'status = 1';
				
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
					
					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}
					
					$cias = array_unique($cias);
					
					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();
					
					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}
					
					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}
				
				/*
				@ Obtener facturas electrónicas del mes generadas en panaderías (PAGADAS)
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha_pago)
							AS
								dia,
						COUNT(id)
							AS
								cantidad,
						SUM(total)
							AS
								importe
					FROM
						facturas_electronicas
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';
				$result = $db->query($sql);
				
				$facturas_pagadas = array();
				
				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_pagadas[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe' => $rec['importe']
						);
					}
				}
				
				/*
				@ Condiciones para obtener facturas electrónicas (PENDIENTES)
				*/
				$condiciones = array();
				
				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo = 2';
				
				$condiciones[] = 'status = 1';
				
				$condiciones[] = 'fecha_pago IS NULL';
				
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
					
					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}
					
					$cias = array_unique($cias);
					
					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();
					
					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}
					
					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}
				
				/*
				@ Obtener facturas electrónicas del mes generadas en panaderías (PENDIENTES)
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS
								dia,
						COUNT(id)
							AS
								cantidad,
						SUM(total)
							AS
								importe
					FROM
						facturas_electronicas
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';
				$result = $db->query($sql);
				
				$facturas_pendientes = array();
				
				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_pendientes[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe' => $rec['importe']
						);
					}
				}
				
				/*
				@ Condiciones para obtener facturas electrónicas del mes
				*/
				$condiciones = array();
				
				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo = 1';
				
				$condiciones[] = 'status = 1';
				
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
					
					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}
					
					$cias = array_unique($cias);
					
					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();
					
					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}
					
					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}
				
				/*
				@ Obtener facturas electrónicas del mes generadas en panaderías
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS
								dia,
						COUNT(id)
							AS
								cantidad,
						SUM(total)
							AS
								importe
					FROM
						facturas_electronicas
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';
				$result = $db->query($sql);
				
				$facturas_panaderia = array();
				
				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_panaderia[$rec['num_cia']][$rec['dia']] = array(
							'cantidad' => $rec['cantidad'],
							'importe' => $rec['importe']
						);
					}
				}
				
				/*
				@ Condiciones para obtener facturas electrónicas del mes [CANCELADAS]
				*/
				$condiciones = array();
				
				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo = 1';
				
				$condiciones[] = 'status = 0';
				
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
					
					/*
					@ [18-Ago-2011] En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
					*/
					foreach ($cias as $c) {
						if (isset($sucursales[$c])) {
							foreach ($porcentajes[$sucursales[$c]] as $p) {
								$cias[] = $p['sucursal'];
							}
						}
					}
					
					$cias = array_unique($cias);
					
					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '') {
					$omitir = array();
					
					$pieces = explode(',', $_REQUEST['omitir']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir[] = $piece;
						}
					}
					
					if (count($omitir) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
					}
				}
				
				/*
				@ Obtener facturas electrónicas del mes generadas en panaderías [CANCELADAS]
				*/
				$sql = '
					SELECT
						num_cia,
						EXTRACT(day from fecha)
							AS
								dia,
						MAX(consecutivo)
							AS
								folio
					FROM
						facturas_electronicas
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						dia
					ORDER BY
						num_cia,
						dia
				';
				$result = $db->query($sql);
				
				$facturas_panaderia_canceladas = array();
				
				if ($result) {
					/*
					@ Reordenar facturas
					*/
					foreach ($result as $rec) {
						$facturas_panaderia_canceladas[$rec['num_cia']][$rec['dia']] = $rec['folio'];
					}
				}
				
				/*
				@ [11-Mar-2011] Condiciones para obtener diferencia iniciales
				*/
				$condiciones = array();
				
				$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';
				
				$condiciones[] = 'anio = ' . date('Y', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));
				
				$condiciones[] = 'mes = ' . date('n', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));
				
				/*
				@ [11-Mar-2011] Obtener diferencias iniciales
				*/
				$sql = '
					SELECT
						num_cia,
						diferencia
					FROM
						diferencia_ventas
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);
				
				$diferencias_iniciales = array();
				
				/*
				@ [11-Mar-2011] Reordenar diferencias iniciales
				*/
				if ($result) {
					foreach ($result as $rec) {
						$diferencias_iniciales[$rec['num_cia']] = floatval($rec['diferencia']);
					}
				}
				
				/*
				@ Condiciones para obtener compañías
				*/
				$condiciones = array();
				
				$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';
				
				/*
				@ Obtener compañías
				*/
				$sql = '
					SELECT
						num_cia,
						nombre
							AS
								nombre_cia
					FROM
						catalogo_companias
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);
				
				$companias = array();
				
				foreach ($result as $rec) {
					$companias[$rec['num_cia']] = $rec['nombre_cia'];
				}
				
				/*
				@ Generar listado
				*/
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasPanaderiasResultado.tpl');
				$tpl->prepare();
				
				foreach ($companias as $num_cia => $nombre_cia) {
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($nombre_cia));
					
					$tpl->assign('diferencia_inicial', isset($diferencias_iniciales[$num_cia]) ? number_format($diferencias_iniciales[$num_cia], 2, '.', ',') : 0);
					
					$arrastre_diferencia = isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0;
					
					$totales = array(
						'depositos'           => 0,
						'facturas_pagadas'    => 0,
						'facturas_pendientes' => 0,
						'facturas_panaderia'  => 0,
						'diferencia'          => isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0
					);
					
					$color = FALSE;
					foreach ($dias as $dia) {
						$tpl->newBlock('dia');
						$tpl->assign('color_row', $color ? 'on' : 'off');
						$tpl->assign('dia', $dia);
						$tpl->assign('num_cia', $num_cia);
						
						$color = !$color;
						
						$_depositos = isset($depositos[$num_cia][$dia]) ? $depositos[$num_cia][$dia] : 0;
						
						/*
						@ AJUSTE TEMPORAL
						@ A LAS COMPAÑIAS 31, 34, 49, 69, 79, 92, 37, 68, 73, 115 DEL 13/06/2011 AL 30/06/2011 AUMENTAR $4,000.00 AL EFECTIVO
						@ [20-JUL-2011] APLICAR TAMBIEN PARA TODO EL MES DE JULIO DE 2011 EXCEPTUANDO LA COMPAÑIA 37
						*/
						
//						if (mktime(0, 0, 0, intval($_REQUEST['mes']), intval($dia), intval($_REQUEST['anio'])) >= mktime(0, 0, 0, 7, 1, 2011) && mktime(0, 0, 0, intval($_REQUEST['mes']), intval($dia), intval($_REQUEST['anio'])) <= mktime(0, 0, 0, 7, 31, 2011) && in_array($num_cia, array(31, 34, 49, 69, 79, 92, /*37,*/ 68, 73, 115))) {
//							$_depositos = $_depositos + 4000;
//						}
						
						$_facturas_pagadas = isset($facturas_pagadas[$num_cia][$dia]) ? $facturas_pagadas[$num_cia][$dia]['importe'] : 0;
						$_facturas_pagadas_cantidad = isset($facturas_pagadas[$num_cia][$dia]) ? $facturas_pagadas[$num_cia][$dia]['cantidad'] : 0;
						
						$_facturas_pendientes = isset($facturas_pendientes[$num_cia][$dia]) ? $facturas_pendientes[$num_cia][$dia]['importe'] : 0;
						$_facturas_pendientes_cantidad = isset($facturas_pendientes[$num_cia][$dia]) ? $facturas_pendientes[$num_cia][$dia]['cantidad'] : 0;
						
						$json_data = array(
							'num_cia'             => intval($num_cia),
							'anio'                => intval($_REQUEST['anio']),
							'mes'                 => intval($_REQUEST['mes']),
							'dia'                 => intval($dia),
							'depositos'           => round(floatval($_depositos), 2),
							'facturas_pagadas'    => round(floatval($_facturas_pagadas), 2),
							'facturas_pendientes' => round(floatval($_facturas_pendientes), 2),
							'facturas_panaderia'  => 0,
							'diferencia'          => 0,
							'arrastre'            => 0,
							'sustituye'           => 0
						);
						
						if (isset($facturas_panaderia[$num_cia][$dia])) {
							$_facturas_panaderia = $facturas_panaderia[$num_cia][$dia]['importe'];
							$_facturas_panaderia_cantidad = $facturas_panaderia[$num_cia][$dia]['cantidad'];
							
							$_diferencia = $_depositos - $_facturas_pagadas - $_facturas_panaderia;
							
							$json_data['facturas_panaderia'] = round(floatval($_facturas_panaderia), 2);
							$json_data['diferencia'] = round(floatval($_diferencia), 2);
							
							$arrastre_diferencia += $_diferencia;
							
							$tpl->assign('disabled', ' disabled="disabled"');
						}
						else if ($_depositos > 0) {
							$_diferencia = $_depositos - $_facturas_pagadas;
							
							$_facturas_panaderia = $_diferencia + $arrastre_diferencia;
							
							/*
							@ [07-Ene-2010] La factura de venta es negativa, poner importe en 0 y arrastrar diferencia
							*/
							if ($_facturas_panaderia < 0) {
								$_diferencia = $_facturas_panaderia;
								
								$_facturas_panaderia = 0;
							}
							else {
								$_facturas_panaderia_cantidad = 1;
								
								$_diferencia = $_depositos - $_facturas_pagadas - $_facturas_panaderia;
							}
							
							$json_data['facturas_panaderia'] = round(floatval($_facturas_panaderia), 2);
							$json_data['diferencia'] = round(floatval($_diferencia), 2);
							$json_data['arrastre'] = round(floatval($arrastre_diferencia), 2);
							
							/*
							@ [14-Ene-2010] La factura sustituye a una cancelada
							*/
							if (isset($facturas_panaderia_canceladas[$num_cia][$dia])) {
								$json_data['sustituye'] = intval($facturas_panaderia_canceladas[$num_cia][$dia]);
							}
							
							if ($_facturas_panaderia == 0) {
								$arrastre_diferencia = $_diferencia;
							}
							else {
								$arrastre_diferencia += $_diferencia;
							}
							
							$tpl->assign('checked', ' checked="checked"');
						}
						else {
							$tpl->assign('disabled', ' disabled="disabled"');
							
							$_facturas_panaderia = 0;
							$_diferencia = 0;
						}
						
						$totales['depositos'] += $_depositos;
						$totales['facturas_pagadas'] += $_facturas_pagadas;
						$totales['facturas_pendientes'] += $_facturas_pendientes;
						$totales['facturas_panaderia'] += $_facturas_panaderia;
						$totales['diferencia'] += $_diferencia;
						
						$tpl->assign('depositos', $_depositos != 0 ? number_format($_depositos, 2, '.', ',') : '&nbsp;');
						$tpl->assign('facturas_pagadas', $_facturas_pagadas != 0 ? '(' . $_facturas_pagadas_cantidad . ') ' . number_format($_facturas_pagadas, 2, '.', ',') : '&nbsp;');
						$tpl->assign('facturas_pendientes', $_facturas_pendientes != 0 ? '(' . $_facturas_pendientes_cantidad . ') ' . number_format($_facturas_pendientes, 2, '.', ',') : '&nbsp;');
						$tpl->assign('facturas_panaderia', $_facturas_panaderia != 0 ? '(' . $_facturas_panaderia_cantidad . ') ' . number_format($_facturas_panaderia, 2, '.', ',') : '&nbsp;');
						$tpl->assign('diferencia', round($_diferencia, 2) != 0 ? number_format($_diferencia, 2, '.', ',') : '&nbsp;');
						$tpl->assign('color', $_diferencia >= 0 ? 'blue' : 'red');
						
						$tpl->assign('datos', htmlentities(json_encode($json_data)));
					}
					
					foreach ($totales as $key => $value) {
						$tpl->assign('cia.' . $key, $value != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');
						
						if ($key == 'diferencia') {
							$tpl->assign('cia.color', $value >= 0 ? 'blue' : 'red');
						}
					}
					
					$tpl->assign('cia.arrastre_diferencia', $arrastre_diferencia);
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'generar':
			include_once('includes/class.facturas.inc.php');
			
			$fac = new FacturasClass();
			
			if ($fac->ultimoCodigoError() < 0) {
				return -1;
			}
			
			/*
			@ [05-Ene-2011] Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					sucursal
						AS
							num_cia
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);
			
			/*
			@ Reordenar porcentajes
			*/
			$sucursales = array();
			if ($result) {
				foreach ($result as $rec) {
					$sucursales[] = $rec['num_cia'];
				}
			}
			
			/*
			@ Generar reporte
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasPanaderiasReporte.tpl');
			$tpl->prepare();
			
			$num_cia = NULL;
			foreach ($_REQUEST['datos'] as $json_string) {
				$data = json_decode($json_string);
				
				if ($num_cia != $data->num_cia) {
					$num_cia = $data->num_cia;
					
					$status_emisor = TRUE;
					
					$sql = '
						SELECT
							nombre
								AS
									nombre_cia,
							aplica_iva
								AS
									aplicar_iva,
							email
						FROM
							catalogo_companias
						WHERE
							num_cia = ' . $num_cia . '
					';
					$cia = $db->query($sql);
					
					$piva = $cia[0]['aplicar_iva'] == 't' ? 16 : 0;
					
					$tpl->newBlock('emisor');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $cia[0]['nombre_cia']);
					
					$total_cia = 0;
				}
				
				$fecha = date('d/m/Y', mktime(0, 0, 0, $data->mes, $data->dia, $data->anio));
				$hora = date('H:i');
				
				$importe = round($data->facturas_panaderia / (1 + $piva / 100), 2);
				$iva = $data->facturas_panaderia - $importe;
				$total = $data->facturas_panaderia;
				
				$tpl->newBlock('row');
				$tpl->assign('fecha', $fecha);
				$tpl->assign('importe', number_format($total, 2, '.', ','));
				
				$total_cia += $total;
				
				$tpl->assign('emisor.total', number_format($total_cia, 2, '.', ','));
				
				if (!$status_emisor) {
					$tpl->assign('status', '<span style="color:#C00;">Error en d&iacute;as anteriores</span>');
				}
				else if ($total < 0) {
					$tpl->assign('status', '<span style="color:#C00;">El importe de la factura para este d&iacute;a no puede ser negativo o 0</span>');
					
					$status_emisor = FALSE;
				}
				else if ($total == 0) {
					$tpl->assign('status', 'No se gener&oacute; factura para este d&iacute;a');
					
					$sql = '
						INSERT INTO
							facturas_electronicas
								(
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
								)
							VALUES
								(
									' . $num_cia . ',
									\'' . $fecha . '\',
									\'' . $hora . '\',
									1,
									0,
									1,
									1,
									\'PUBLICO EN GENERAL\',
									\'XAXX010101000\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									\'\',
									0,
									0,
									0,
									' . $_SESSION['iduser'] . ',
									\'' . $fecha . '\'
								)
					' . ";\n";
					
					/*
					@ [11-Mar-2011] Actualizar diferencia del emisor
					*/
					if ($id = $db->query('
						SELECT
							id
						FROM
							diferencia_ventas
						WHERE
								num_cia = ' . $num_cia . '
							AND
								anio = ' . $data->anio . '
							AND
								mes = ' . $data->mes . '
					')) {
						$sql .= '
							UPDATE
								diferencia_ventas
							SET
								diferencia = ' . $data->diferencia . ',
								iduser_mod = ' . $_SESSION['iduser'] . ',
								tsmod = now()
							WHERE
								id = ' . $id[0]['id'] . '
						' . ";\n";
					}
					else {
						$sql .= '
							INSERT INTO
								diferencia_ventas
									(
										num_cia,
										anio,
										mes,
										diferencia,
										iduser_ins,
										iduser_mod
									)
								VALUES
									(
										' . $num_cia . ',
										' . $data->anio . ',
										' . $data->mes . ',
										' . $data->diferencia . ',
										' . $_SESSION['iduser'] . ',
										' . $_SESSION['iduser'] . '
									)
						' . ";\n";
					}
					
					$db->query($sql);
				}
				else {
					$datos = array(
						'cabecera' => array (
							'num_cia'               => $num_cia,
							'clasificacion'         => 1,
							'fecha'                 => $fecha,
							'hora'                  => $hora,
							'clave_cliente'         => 1,
							'nombre_cliente'        => 'PUBLICO EN GENERAL',
							'rfc_cliente'           => 'XAXX010101000',
							'calle'                 => '',
							'no_exterior'           => '',
							'no_interior'           => '',
							'colonia'               => '',
							'localidad'             => '',
							'referencia'            => '',
							'municipio'             => '',
							'estado'                => '',
							'pais'                  => 'MEXICO',
							'codigo_postal'         => '',
							'email'                 => '',
							'observaciones'         => $data->sustituye > 0 ? ' (SUSTITUYE A LA FACTURA ' . $data->sustituye . ')' : '',
							'importe'               => $importe,
							'porcentaje_descuento'  => 0,
							'descuento'             => 0,
							'porcentaje_iva'        => $piva,
							'importe_iva'           => $iva,
							'aplicar_retenciones'   => 'N',
							'importe_retencion_isr' => 0,
							'importe_retencion_iva' => 0,
							'total'                 => $total
						),
						'consignatario' => array (
							'nombre'        => '',
							'rfc'           => '',
							'calle'         => '',
							'no_exterior'   => '',
							'no_interior'   => '',
							'colonia'       => '',
							'localidad'     => '',
							'referencia'    => '',
							'municipio'     => '',
							'estado'        => '',
							'pais'          => '',
							'codigo_postal' => ''
						),
						'detalle' => array(
							array (
								'clave'            => 1,
								'descripcion'      => 'VENTA DEL DIA ' . $fecha,
								'cantidad'         => 1,
								'unidad'           => 'SIN UNIDAD',
								'precio'           => $importe,
								'importe'          => $importe,
								'descuento'        => 0,
								'porcentaje_iva'   => $piva > 0 ? 16 : 0,
								'importe_iva'      => $iva,
								'numero_pedimento' => '',
								'fecha_entrada'    => '',
								'aduana_entrada'   => ''
							)
						)
					);
					
					$status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $datos);
					
					if ($status < 0) {
						$status_emisor = FALSE;
						
						$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');
					}
					else {
						$pieces = explode('-', $status);
						$folio = $pieces[1];
						
						$tpl->assign('folio', $folio);
						$tpl->assign('status', '<span style="color:#060;">OK</span>');
						
						if (in_array($num_cia, $sucursales)) {
							$sql = '
								INSERT INTO
									ventas_sucursales
										(
											num_cia,
											fecha,
											importe
										)
									VALUES
										(
											' . $num_cia . ',
											\'' . $fecha . '\',
											' . $data->depositos . '
										)
							' . ";\n";
							
							$db->query($sql);
						}
						
						/*
						@ [11-Mar-2011] Actualizar diferencia del emisor
						*/
						if ($id = $db->query('
							SELECT
								id
							FROM
								diferencia_ventas
							WHERE
									num_cia = ' . $num_cia . '
								AND
									anio = ' . $data->anio . '
								AND
									mes = ' . $data->mes . '
						')) {
							$sql = '
								UPDATE
									diferencia_ventas
								SET
									diferencia = ' . $data->diferencia . ',
									iduser_mod = ' . $_SESSION['iduser'] . ',
									tsmod = now()
								WHERE
									id = ' . $id[0]['id'] . '
							' . ";\n";
						}
						else {
							$sql = '
								INSERT INTO
									diferencia_ventas
										(
											num_cia,
											anio,
											mes,
											diferencia,
											iduser_ins,
											iduser_mod
										)
									VALUES
										(
											' . $num_cia . ',
											' . $data->anio . ',
											' . $data->mes . ',
											' . $data->diferencia . ',
											' . $_SESSION['iduser'] . ',
											' . $_SESSION['iduser'] . '
										)
							' . ";\n";
						}
						
						$db->query($sql);
					}
				}
			}
			
			echo $tpl->getOutputContent();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasPanaderias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
