<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';
include 'includes/phpmailer/class.phpmailer.php';

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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'simular':
			if ($_REQUEST['periodo'] == 1) {
				list($dia, $mes, $anio) = explode('/', date('j') < 7 ? date('j/n/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('j/n/Y'));
			}
			else {
				$sql = '
					SELECT
						MAX(fecha)
							AS fecha
					FROM
						inventario_fin_mes
					WHERE
						num_cia < 300
				';
				$result = $db->query($sql);
				
				if ($result) {
					list($dia, $mes, $anio) = explode('/', $result[0]['fecha']);
				}
				else {
					list($dia, $mes, $anio) = date('j/n/Y');
				}
			}
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10), 1, $anio));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10), $dia, $anio));
			
			$fecha3 = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10) - 1, 1, $anio));
			$fecha4 = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10), 0, $anio));
			
			$fecha_historico = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10) + 1, 0, $anio));
			
//			list($mes1, $anio1) = explode('/', date('n/Y', mktime(0, 0, 0, intval($mes, 10) - 1, 1, $anio)));
//			list($mes2, $anio2) = explode('/', date('n/Y', mktime(0, 0, 0, intval($mes, 10) - 2, 1, $anio)));
			
			$dias_por_mes = array(
				1 =>  31,
				2 =>  ($anio % 4 == 0 && $anio % 100 != 0) || $anio % 400 ? 29 : 28,
				3 =>  31,
				4 =>  30,
				5 =>  31,
				6 =>  30,
				7 =>  31,
				8 =>  31,
				9 =>  30,
				10 => 31,
				11 => 30,
				12 => 31
			);
			
			$dias = isset($_REQUEST['complemento']) ? $dias_por_mes[intval($mes, 10)] - $dia + 7 : (isset($_REQUEST['dias']) && $_REQUEST['dias'] > 30 ? $_REQUEST['dias'] : 40);
			
			$meses = array(
				1 => 'Enero',
				2 => 'Febrero',
				3 => 'Marzo',
				4 => 'Abril',
				5 => 'Mayo',
				6 => 'Junio',
				7 => 'Julio',
				8 => 'Agosto',
				9 => 'Septiembre',
				10 => 'Octubre',
				11 => 'Noviembre',
				12 => 'Diciembre'
			);
			
			/*
			@ Productos por proveedor
			*/
			$condiciones = array();
			$condiciones[] = 'procpedautomat = \'TRUE\'';
			$condiciones[] = 'porcentaje > 0';
			
			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					codmp,
					contenido,
					unidad,
					tipo_presentacion.descripcion
						AS unidad_pedido,
					porcentaje,
					ajuste
				FROM
					catalogo_productos_proveedor
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
					LEFT JOIN tipo_presentacion
						ON (idpresentacion = unidad)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					codmp,
					num_pro,
					porcentaje
			';
			$result = $db->query($sql);
			
			$porcentajes = array();
			if ($result) {
				foreach ($result as $r) {
					$porcentajes[$r['codmp']][] = array(
						'num_pro' => $r['num_pro'],
						'contenido' => $r['contenido'],
						'unidad' => $r['unidad'],
						'unidad_pedido' => $r['unidad_pedido'],
						'porcentaje' => $r['porcentaje'],
						'ajuste' => $r['ajuste']
					);
				}
			}
			
			/*
			@ Inventario de mes
			*/
			$condiciones = array();
			
			$condiciones[] = 'procpedautomat = TRUE';
			
			$condiciones[] = 'num_cia <= 300';
			
			if (isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0) {
				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
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
			
			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();
				
				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}
				
				if (count($productos) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();
				
				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}
				
				if (count($omitir_cias) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}
			
			if (isset($_REQUEST['omitir_mp']) && trim($_REQUEST['omitir_mp']) != '') {
				$omitir_mp = array();
				
				$pieces = explode(',', $_REQUEST['omitir_mp']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mp[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mp[] = $piece;
					}
				}
				
				if (count($omitir_mp) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mp) . ')';
				}
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 21))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			if ($_REQUEST['orden'] == 1) {
				$orden = 'num_cia, controlada DESC, codmp';
			}
			else if ($_REQUEST['orden'] == 2) {
				$orden = 'controlada DESC, codmp, num_cia';
			}
			
			if ($_REQUEST['periodo'] == 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre,
						inv.existencia
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						presentacion,
						tp.descripcion
							AS unidad_pedido,
						controlada
					FROM
						inventario_virtual inv
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						WHERE
							' . implode(' AND ', $condiciones) . '
						ORDER BY
							' . $orden . '
				';
			}
			else {
				$condiciones[] = 'fecha = \'' . $fecha_historico . '\'';
				
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre,
						ifm.inventario
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						presentacion,
						tp.descripcion
							AS unidad_pedido,
						controlada
					FROM
						inventario_fin_mes ifm
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						' . $orden . '
				';
			}
			$result = $db->query($sql);
			
			$inventario = array();
			if ($result) {
				if ($_REQUEST['orden'] == 1) {
					$num_cia = NULL;
					foreach ($result as $r) {
						if ($num_cia != $r['num_cia']) {
							$num_cia = $r['num_cia'];
							
							$inventario[$num_cia]['nombre_cia'] = $r['nombre_cia'];
							$inventario[$num_cia]['productos'] = array();
						}
						
						$inventario[$num_cia]['productos'][$r['codmp']] = array(
							'nombre' => $r['nombre'],
							'existencia' => $r['existencia'],
							'unidad_consumo' => $r['unidad_consumo'],
							'presentacion' => $r['presentacion'],
							'unidad_pedido' => $r['unidad_pedido'],
							'controlada' => $r['controlada']
						);
					}
				}
				else if ($_REQUEST['orden'] == 2) {
					$codmp = NULL;
					foreach ($result as $r) {
						if ($codmp != $r['codmp']) {
							$codmp = $r['codmp'];
							
							$inventario[$codmp]['nombre_mp'] = $r['nombre'];
							$inventario[$codmp]['cias'] = array();
						}
						
						$inventario[$codmp]['cias'][$r['num_cia']] = array(
							'nombre' => $r['nombre_cia'],
							'existencia' => $r['existencia'],
							'unidad_consumo' => $r['unidad_consumo'],
							'presentacion' => $r['presentacion'],
							'unidad_pedido' => $r['unidad_pedido'],
							'controlada' => $r['controlada']
						);
					}
				}
			}
			
			/*
			@ Consumos de hace 1 mes
			*/
			$condiciones = array();
//			$condiciones[] = 'anio = ' . $anio1;
//			$condiciones[] = 'mes = ' . $mes1;
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$condiciones[] = 'procpedautomat = TRUE';
			$condiciones[] = 'tipo_mov = TRUE';
			
			if (isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0) {
				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
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
			
			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();
				
				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}
				
				if (count($productos) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 21))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
//			$sql = '
//				SELECT
//					num_cia,
//					codmp,
//					consumo
//				FROM
//						consumos_mensuales
//					LEFT JOIN
//						catalogo_mat_primas
//							USING
//								(
//									codmp
//								)
//					LEFT JOIN
//						catalogo_companias cc
//							USING
//								(
//									num_cia
//								)
//					LEFT JOIN
//						catalogo_administradores ca
//							USING
//								(
//									idadministrador
//								)
//				WHERE
//					' . implode(' AND ', $condiciones) . '
//				ORDER BY
//					num_cia,
//					codmp
//			';
			$sql = '
				SELECT
					num_cia,
					codmp,
					SUM(cantidad)
						AS consumo
				FROM
					mov_inv_real
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					codmp
				ORDER BY
					num_cia,
					codmp
			';
			$result = $db->query($sql);
			
			$consumos1 = array();
			if ($result) {
				foreach ($result as $r) {
					$consumos1[$r['num_cia']][$r['codmp']] = $r['consumo'];
				}
			}
			
			
			/*
			@ Consumos de hace 2 meses
			*/
			$condiciones = array();
//			$condiciones[] = 'anio = ' . $anio2;
//			$condiciones[] = 'mes = ' . $mes2;
			$condiciones[] = 'fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha4 . '\'';
			$condiciones[] = 'procpedautomat = TRUE';
			$condiciones[] = 'tipo_mov = TRUE';
			
			if (isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0) {
				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
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
			
			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();
				
				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}
				
				if (count($productos) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 21))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
//			$sql = '
//				SELECT
//					num_cia,
//					codmp,
//					consumo
//				FROM
//						consumos_mensuales
//					LEFT JOIN
//						catalogo_mat_primas
//							USING
//								(
//									codmp
//								)
//					LEFT JOIN
//						catalogo_companias cc
//							USING
//								(
//									num_cia
//								)
//					LEFT JOIN
//						catalogo_administradores ca
//							USING
//								(
//									idadministrador
//								)
//				WHERE
//					' . implode(' AND ', $condiciones) . '
//				ORDER BY
//					num_cia,
//					codmp
//			';
			$sql = '
				SELECT
					num_cia,
					codmp,
					SUM(cantidad)
						AS consumo
				FROM
					mov_inv_real
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					codmp
				ORDER BY
					num_cia,
					codmp
			';
			$result = $db->query($sql);
			
			$consumos2 = array();
			if ($result) {
				foreach ($result as $r) {
					$consumos2[$r['num_cia']][$r['codmp']] = $r['consumo'];
				}
			}
			
			/*
			@ Realizar cálculo de pedido
			*/
			
			if ($_REQUEST['orden'] == 1) {
				$sql = '
					SELECT
						codmp,
						num_proveedor
							AS num_pro,
						nombre
							AS nombre_pro,
						telefono1
							AS telefono,
						email1,
						email2,
						email3,
						contenido,
						tp.descripcion
							AS unidad
					FROM
						catalogo_productos_proveedor
						LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = unidad)
						LEFT JOIN catalogo_proveedores cp
							USING (num_proveedor)
					WHERE
						(num_proveedor, codmp) IN (
							SELECT
								num_proveedor,
								codmp
							FROM
								productos_proveedores
						)
					ORDER BY
						codmp,
						num_proveedor
				';
				$result = $db->query($sql);
				
				$productos_pro = array();
				if ($result) {
					foreach ($result as $rec) {
						$productos_pro[$rec['codmp']] = array(
							'num_pro' => $rec['num_pro'],
							'nombre_pro' => $rec['nombre_pro'],
							'telefono' => $rec['telefono'],
							'email1' => $rec['email1'],
							'email2' => $rec['email2'],
							'email3' => $rec['email3'],
							'contenido' => $rec['contenido'],
							'unidad' => $rec['unidad']
						);
					}
				}
				
				$pedidos = array();
				$pedidos_proveedor = array();
				foreach ($inventario as $cia => $datos) {
					foreach ($datos['productos'] as $codmp => $pro) {
						$con1 = isset($consumos1[$cia][$codmp]) ? $consumos1[$cia][$codmp] : 0;
						$con2 = isset($consumos2[$cia][$codmp]) ? $consumos2[$cia][$codmp] : 0;
						
						//$consumo = $con1 >= $con2 ? $con1 : $con2;
						$consumo = $con1 > 0 ? $con1 : $con2;
						$existencia = $pro['controlada'] == 'TRUE' ? $pro['existencia'] : ($pro['existencia'] - $consumo >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo / 30 * $dias - $existencia, 2);
						
						if ($consumo != 0 || $existencia != 0) {
							if ($pedido > 0) {
								$total_pedido = /*0*/$pedido;
								$total_pedido_pro = round($pedido / (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1));
								
								/*if (isset($porcentajes[$codmp])) {
									foreach ($porcentajes[$codmp] as $p) {
										$pedido_proveedor = ceil($pedido * $p['porcentaje'] / 100 / $p['contenido']);
										
										if ($p['ajuste'] == 't') {
											$pedido_proveedor = $pedido_proveedor + 10 - $pedido_proveedor % 10;
										}
										
										if ($pedido_proveedor > 0) {
											$pedidos_proveedor[] = array(
												'num_cia' => $cia,
												'num_proveedor' => $p['num_pro'],
												'codmp' => $codmp,
												'mes' => $mes,
												'anio' => $anio,
												'cantidad' => $pedido_proveedor,
												'unidad' => $p['unidad'],
												'contenido' => $p['contenido'],
												'iduser' => $_SESSION['iduser']
											);
											
											$total_pedido += $pedido_proveedor * $p['contenido'];
										}
									}
								}*/
								
								if ($total_pedido > 0) {//if ($consumo == 0) {echo "$num_cia-$codmp<br />";}
									$pedidos[] = array(
										'num_cia' => $cia,
										'nombre_cia' => $datos['nombre_cia'],
										'codmp' => $codmp,
										'nombre' => $pro['nombre'],
										'unidad' => $pro['unidad_consumo'],
										'consumo' => $consumo,
										'inventario' => $existencia,
										'pedido' => $total_pedido,
										'pedido_pro' => $total_pedido,
										'diferencia' => $existencia + $total_pedido - $consumo,
										'estimado' => $existencia + $total_pedido,
										'dias_consumo' => $consumo > 0 ? ($existencia + $total_pedido) / ($consumo / 30) : 0,
										'num_pro' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['num_pro'] : NULL,
										'nombre_pro' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['nombre_pro'] : NULL,
										'telefono' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['telefono'] : NULL
									);
								}
								else {
									$pedidos[] = array(
										'num_cia' => $cia,
										'nombre_cia' => $datos['nombre_cia'],
										'codmp' => $codmp,
										'nombre' => $pro['nombre'],
										'unidad' => $pro['unidad_consumo'],
										'consumo' => $consumo,
										'inventario' => $existencia,
										'pedido' => -1,
										'pedido_pro' => -1,
										'diferencia' => 0,
										'estimado' => $existencia,
										'dias_consumo' => $consumo > 0 ? $existencia / ($consumo / 30) : 0,
										'num_pro' => NULL,
										'nombre_pro' => NULL,
										'telefono' => NULL
									);
								}
							}
							else {
								$pedidos[] = array(
									'num_cia' => $cia,
									'nombre_cia' => $datos['nombre_cia'],
									'codmp' => $codmp,
									'nombre' => $pro['nombre'],
									'unidad' => $pro['unidad_consumo'],
									'consumo' => $consumo,
									'inventario' => $existencia,
									'pedido' => 0,
									'pedido_pro' => 0,
									'diferencia' => 0,
									'estimado' => $existencia,
									'dias_consumo' => $consumo > 0 ? $existencia / ($consumo / 30) : 0,
									'num_pro' => NULL,
									'nombre_pro' => NULL,
									'telefono' => NULL
								);
							}
						}
					}
				}
			}
			else if ($_REQUEST['orden'] == 2) {
				$sql = '
					SELECT
						codmp,
						num_proveedor
							AS num_pro,
						nombre
							AS nombre_pro,
						telefono1
							AS telefono,
						email1,
						email2,
						email3,
						contenido,
						tp.descripcion
							AS unidad
					FROM
						catalogo_productos_proveedor
						LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = unidad)
						LEFT JOIN catalogo_proveedores cp
							USING (num_proveedor)
					WHERE
						(num_proveedor, codmp) IN (
							SELECT
								num_proveedor,
								codmp
							FROM
								productos_proveedores
						)
					ORDER BY
						codmp,
						num_proveedor
				';
				$result = $db->query($sql);
				
				$productos_pro = array();
				if ($result) {
					foreach ($result as $rec) {
						$productos_pro[$rec['codmp']] = array(
							'num_pro' => $rec['num_pro'],
							'nombre_pro' => $rec['nombre_pro'],
							'telefono' => $rec['telefono'],
							'email1' => $rec['email1'],
							'email2' => $rec['email2'],
							'email3' => $rec['email3'],
							'contenido' => $rec['contenido'],
							'unidad' => $rec['unidad']
						);
					}
				}
				
				
				$pedidos = array();
				$pedidos_proveedor = array();
				foreach ($inventario as $codmp => $datos) {
					foreach ($datos['cias'] as $cia => $pro) {
						$con1 = isset($consumos1[$cia][$codmp]) ? $consumos1[$cia][$codmp] : 0;
						$con2 = isset($consumos2[$cia][$codmp]) ? $consumos2[$cia][$codmp] : 0;
						
						$consumo = $con1 > 0 ? $con1 : $con2;
						$existencia = $pro['controlada'] == 'TRUE' ? $pro['existencia'] : ($pro['existencia'] - $consumo >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo / 30 * $dias - $existencia, 2);
						
						if ($pedido > 0) {
							$total_pedido = round($pedido / (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1));
							
							if ($total_pedido > 0) {
								$pedidos[] = array(
									'num_cia' => $cia,
									'nombre_cia' => $pro['nombre'],
									'num_pro' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['num_pro'] : NULL,
									'nombre_pro' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['nombre_pro'] : NULL,
									'telefono' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['telefono'] : NULL,
									'email1' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['email1'] : NULL,
									'email2' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['email2'] : NULL,
									'email3' => isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['email3'] : NULL,
									'codmp' => $codmp,
									'nombre' => $datos['nombre_mp'],
									'unidad' => $pro['unidad_consumo'],
									'consumo' => $consumo,
									'inventario' => $existencia,
									'pedido' => $total_pedido * (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1),
									'pedido_pro' => $total_pedido,
									'diferencia' => $existencia + ($total_pedido * (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1)) - $consumo,
									'estimado' => $existencia + ($total_pedido * (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1)),
									'dias_consumo' => ($existencia + ($total_pedido * (isset($productos_pro[$codmp]) ? $productos_pro[$codmp]['contenido'] : 1))) / ($consumo / 30)
								);
							}
						}
					}
				}
			}
			
			$tpl = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosReporte' . ($_REQUEST['orden'] == 1 ? '' : '3') . '.tpl');
			$tpl->prepare();
			
			if (count($pedidos) > 0 && $_REQUEST['orden'] == 1) {
				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['pedido'] > 0 && $b['pedido'] > 0) {
							if ($a['codmp'] == $b['codmp']) {
								return 0;
							}
							else {
								return ($a['codmp'] < $b['codmp']) ? -1 : 1;
							}
						}
						else {
							if ($a['pedido'] <= 0 && $b['pedido'] <= 0) {
								if ($a['codmp'] == $b['codmp']) {
									return 0;
								}
								else {
									return ($a['codmp'] < $b['codmp']) ? -1 : 1;
								}
							}
							if ($a['pedido'] > 0 && $b['pedido'] <= 0) {
								return -1;
							}
							else if ($b['pedido'] > 0 && $a['pedido'] <= 0) {
								return 1;
							}
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}
				
				usort($pedidos, 'cmp');
				
				$num_cia = NULL;//echo '<table>';
				foreach ($pedidos as $p) {
					if ($num_cia != $p['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$num_cia = $p['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $p['nombre_cia']);
						$tpl->assign('dia', $dia);
						$tpl->assign('mes', $meses[intval($mes, 10)]);
						$tpl->assign('anio', $anio);
						$tpl->assign('dias', $dias);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('cod', $p['codmp']);
					$tpl->assign('producto', $p['nombre']);
					$tpl->assign('unidad', $p['unidad']);
					$tpl->assign('consumo', $p['consumo'] != 0 ? number_format($p['consumo'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('inventario', $p['inventario'] != 0 ? number_format($p['inventario'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 0, '.', ',') : '&nbsp;');
					$tpl->assign('pedido_pro', $p['pedido_pro'] != 0 ? number_format($p['pedido_pro'], 0, '.', ',') : '&nbsp;');
					$tpl->assign('diferencia', $p['diferencia'] != 0 ? number_format($p['diferencia'], 2, '.', ',') : '&nbsp;');
					
					$tpl->assign('estimado', $p['estimado'] != 0 ? number_format($p['estimado'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('dias_consumo', $p['dias_consumo'] != 0 ? round($p['dias_consumo'], 1) : '&nbsp;');
					
					if ($p['dias_consumo'] > 60) {
						$tpl->assign('color_dias', ' red');
						$tpl->assign('checar', 'C/EXIS');
					}
					else if ($p['consumo'] <= 0) {
						$tpl->assign('checar', 'C/CON');
					}
					else {
						$tpl->assign('checar', '&nbsp;');
					}
					
					
					$tpl->assign('num_pro', $p['num_pro'] > 0 ? $p['num_pro'] : '&nbsp;');
					$tpl->assign('nombre_pro', $p['nombre_pro'] != '' ? $p['nombre_pro'] : '&nbsp;');
					$tpl->assign('telefono', $p['telefono'] != '' ? $p['telefono'] : '&nbsp;');
					
//					if ($p['pedido'] > 0 && !$p['num_pro']) {
//						echo '<tr><td>' . $p['codmp'] . ' ' . $p['nombre'] . '</td></tr>';
//					}
				}//echo '</table>';
			}
			else if (count($pedidos) > 0 && $_REQUEST['orden'] == 2) {
				function cmp($a, $b) {
					if ($a['num_pro'] == $b['num_pro']) {
						if ($a['num_cia'] == $b['num_cia']) {
							if ($a['codmp'] == $b['codmp']) {
								return 0;
							}
							else {
								($a['codmp'] < $b['codmp']) ? -1 : 1;
							}
						}
						else {
							return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
						}
					}
					else {
						return ($a['num_pro'] < $b['num_pro']) ? -1 : 1;
					}
				}
				
				usort($pedidos, 'cmp');
				
				$num_pro = NULL;
				foreach ($pedidos as $p) {
					if ($num_pro != $p['num_pro']) {
						if ($num_pro != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
							
							if ($send_email) {
								$mail = new PHPMailer();
								
								$mail->IsSMTP();
								$mail->Host = 'mail.lecaroz.com';
								$mail->Port = 587;
								$mail->SMTPAuth = true;
								$mail->Username = 'wendy.barona+lecaroz.com';
								$mail->Password = 'L3c4r0z*';
								
								$mail->From = 'margarita.hernandez@lecaroz.com';
								$mail->FromName = 'Lecaroz :: Compras';
								
								foreach ($emails as $email) {
									$mail->AddAddress($email);
								}
								
								$mail->AddBCC('wendy.barona@lecaroz.com');
								
								$mail->AddBCC('margarita.hernandez@lecaroz.com');
								
								$mail->AddBCC('carlos.candelario@lecaroz.com');
								
								$mail->AddBCC('p_master5@hotmail.com');
								
								$mail->Subject = 'Lecaroz :: Pedido de Materias Primas [' . date('d/m/Y H:i') . ']';
								
								$mail->Body = $tpl_email->getOutputContent();
								
								$mail->IsHTML(true);
								
								if(!$mail->Send()) {
									return $mail->ErrorInfo;
								}
							}
						}
						
						$num_pro = $p['num_pro'];
						
						if (isset($_REQUEST['email']) && ($p['email1'] != '' || $p['email2'] != '' || $p['email3'] != '')) {
							$tpl_email = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosEmail2.tpl');
							$tpl_email->prepare();
							
							$emails = array();
							
							if ($p['email1'] != '') {
								$emails[] = $p['email1'];
							}
							else if ($p['email2'] != '') {
								$emails[] = $p['email2'];
							}
							else if ($p['email3']) {
								$emails[] = $p['email3'];
							}
							
							$send_email = TRUE;
						}
						else {
							$send_email = FALSE;
						}
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_pro', $p['num_pro']);
						$tpl->assign('nombre_pro', $p['nombre_pro']);
						$tpl->assign('telefono', $p['telefono']);
						$tpl->assign('dia', date('j'));
						$tpl->assign('mes', $meses[intval(date('n'), 10)]);
						$tpl->assign('anio', date('Y'));
						$tpl->assign('dias', $dias);
						
						if ($send_email) {
							$tpl_email->assign('num_pro', $p['num_pro']);
							$tpl_email->assign('nombre_pro', $p['nombre_pro']);
							$tpl_email->assign('telefono', $p['telefono']);
							$tpl_email->assign('dia', date('j'));
							$tpl_email->assign('mes', $meses[intval(date('n'), 10)]);
							$tpl_email->assign('anio', date('Y'));
						}
						
						$num_cia = NULL;
					}
					
					if ($num_pro > 0 && $num_cia != $p['num_cia']) {
						$num_cia = $p['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $p['nombre_cia']);
						
						if ($send_email) {
							$tpl_email->newBlock('cia');
							$tpl_email->assign('num_cia', $num_cia);
							$tpl_email->assign('nombre_cia', $p['nombre_cia']);
						}
					}
					
					if ($num_pro > 0) {
						$tpl->newBlock('pro');
						$tpl->assign('codmp', $p['codmp']);
						$tpl->assign('nombre_mp', $p['nombre']);
						$tpl->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 0, '.', ',') : '&nbsp;');
						$tpl->assign('unidad', $p['unidad']);
						$tpl->assign('pedido_pro', $p['pedido_pro'] != 0 ? number_format($p['pedido_pro'], 0, '.', ',') : '&nbsp;');
						
						if ($send_email) {
							$tpl_email->newBlock('pro');
							$tpl_email->assign('codmp', $p['codmp']);
							$tpl_email->assign('nombre_mp', $p['nombre']);
							$tpl_email->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 0, '.', ',') : '&nbsp;');
							$tpl_email->assign('unidad', $p['unidad']);
							$tpl_email->assign('pedido_pro', $p['pedido_pro'] != 0 ? number_format($p['pedido_pro'], 0, '.', ',') : '&nbsp;');
						}
					}
				}
				
				if ($num_pro != NULL) {
					if ($send_email) {
						$mail = new PHPMailer();
						
						$mail->IsSMTP();
						$mail->Host = 'mail.lecaroz.com';
						$mail->Port = 587;
						$mail->SMTPAuth = true;
						$mail->Username = 'wendy.barona+lecaroz.com';
						$mail->Password = 'L3c4r0z*';
						
						$mail->From = 'margarita.hernandez@lecaroz.com';
						$mail->FromName = 'Lecaroz :: Compras';
						
						foreach ($emails as $email) {
							$mail->AddAddress($email);
						}
						
						$mail->AddBCC('wendy.barona@lecaroz.com');
						
						$mail->AddBCC('margarita.hernandez@lecaroz.com');
						
						$mail->AddBCC('carlos.candelario@lecaroz.com');
						
						$mail->AddBCC('p_master5@hotmail.com');
						
						$mail->Subject = 'Lecaroz :: Pedido de Materias Primas [' . date('d/m/Y H:i') . ']';
						
						$mail->Body = $tpl_email->getOutputContent();
						
						$mail->IsHTML(true);
						
						if(!$mail->Send()) {
							return $mail->ErrorInfo;
						}
					}
				}
				
//				$codmp = NULL;
//				foreach ($pedidos as $p) {
//					if ($codmp != $p['codmp']) {
//						if ($codmp != NULL) {
//							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
//							
//							if ($send_email) {
//								$mail = new PHPMailer();
//								
//								$mail->IsSMTP();
//								$mail->Host = 'mail.lecaroz.com';
//								$mail->Port = 587;
//								$mail->SMTPAuth = true;
//								$mail->Username = 'wendy.barona+lecaroz.com';
//								$mail->Password = 'L3c4r0z*';
//								
//								$mail->From = 'margarita.hernandez@lecaroz.com';
//								$mail->FromName = 'Lecaroz :: Compras';
//								
//								foreach ($emails as $email) {
//									$mail->AddAddress($email);
//								}
//								
//								$mail->AddBCC('wendy.barona@lecaroz.com');
//								
//								$mail->AddBCC('margarita.hernandez@lecaroz.com');
//								
//								$mail->AddBCC('carlos.candelario@lecaroz.com');
//								
//								$mail->Subject = 'Lecaroz :: Pedido de Materias Primas [' . date('d/m/Y H:i') . ']';
//								
//								$mail->Body = $tpl_email->getOutputContent();
//								
//								$mail->IsHTML(true);
//								
//								if(!$mail->Send()) {
//									return $mail->ErrorInfo;
//								}
//							}
//						}
//						
//						$codmp = $p['codmp'];
//						
//						if (isset($_REQUEST['email']) && ($p['email1'] != '' || $p['email2'] != '' || $p['email3'] != '')) {
//							$tpl_email = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosEmail.tpl');
//							$tpl_email->prepare();
//							
//							$emails = array();
//							
//							if ($p['email1'] != '') {
//								$emails[] = $p['email1'];
//							}
//							else if ($p['email2'] != '') {
//								$emails[] = $p['email2'];
//							}
//							else if ($p['email3']) {
//								$emails[] = $p['email3'];
//							}
//							
//							$send_email = TRUE;
//						}
//						else {
//							$send_email = FALSE;
//						}
//						
//						$tpl->newBlock('reporte');
//						$tpl->assign('num_pro', $p['num_pro']);
//						$tpl->assign('nombre_pro', $p['nombre_pro']);
//						$tpl->assign('telefono', $p['telefono']);
//						$tpl->assign('codmp', $codmp);
//						$tpl->assign('nombre_mp', $p['nombre']);
//						$tpl->assign('dia', date('j'));
//						$tpl->assign('mes', $meses[intval(date('n'), 10)]);
//						$tpl->assign('anio', date('Y'));
//						$tpl->assign('dias', $dias);
//						
//						if ($send_email) {
//							$tpl_email->assign('num_pro', $p['num_pro']);
//							$tpl_email->assign('nombre_pro', $p['nombre_pro']);
//							$tpl_email->assign('telefono', $p['telefono']);
//							$tpl_email->assign('codmp', $codmp);
//							$tpl_email->assign('nombre_mp', $p['nombre']);
//							$tpl_email->assign('dia', date('j'));
//							$tpl_email->assign('mes', $meses[intval(date('n'), 10)]);
//							$tpl_email->assign('anio', date('Y'));
//						}
//					}
//					
//					$tpl->newBlock('row');
//					$tpl->assign('num_cia', $p['num_cia']);
//					$tpl->assign('nombre_cia', $p['nombre_cia']);
//					$tpl->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 0, '.', ',') : '&nbsp;');
//					$tpl->assign('unidad', $p['unidad']);
//					$tpl->assign('pedido_pro', $p['pedido_pro'] != 0 ? number_format($p['pedido_pro'], 0, '.', ',') : '&nbsp;');
//					
//					if ($send_email) {
//						$tpl_email->newBlock('row');
//						$tpl_email->assign('num_cia', $p['num_cia']);
//						$tpl_email->assign('nombre_cia', $p['nombre_cia']);
//						$tpl_email->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 0, '.', ',') : '&nbsp;');
//						$tpl_email->assign('unidad', $p['unidad']);
//						$tpl_email->assign('pedido_pro', $p['pedido_pro'] != 0 ? number_format($p['pedido_pro'], 0, '.', ',') : '&nbsp;');
//					}
//				}
			}
			
			$tpl->printToScreen();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 19, 21))) {
		$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
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
	if (!in_array($_SESSION['iduser'], array(1, 4, 19, 21))) {
		$condiciones[] = 'iduser = ' . $_SESSION['iduser'];
	}
	else {
		$condiciones[] = '\'TRUE\'';
	}
	
	$sql = '
		SELECT
			idadministrador
				AS id,
			nombre_administrador
				AS nombre
		FROM
			catalogo_administradores
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);
	
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}
}

$tpl->printToScreen();
?>
