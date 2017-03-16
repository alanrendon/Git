<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosV2Inicio.tpl');
			$tpl->prepare();
			
			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
			
			$tpl->newBlock($isIpad ? 'ipad' : 'normal');
			
			if ($isIpad) {
				$condiciones[] = 'num_cia <= 300';
				
				if (!in_array($_SESSION['iduser'], array(1, 4, 5, 19, 21, 44))) {
					$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
				}
				
				$sql = '
					SELECT
						num_cia
							AS value,
						nombre_corto
							AS text
					FROM
						catalogo_companias cc
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia
				';
				$cias = $db->query($sql);
				
				foreach ($cias as $c) {
					$tpl->newBlock('cia');
					$tpl->assign('value', $c['value']);
					$tpl->assign('text', $c['text']);
				}
			}
			else {
				$sql = '
					SELECT
						idadministrador
							AS value,
						nombre_administrador
							AS text
					FROM
						catalogo_administradores
					ORDER BY
						text
				';
				
				$admins = $db->query($sql);
				
				foreach ($admins as $admin) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $admin['value']);
					$tpl->assign('text', utf8_encode($admin['text']));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'simular':
			if ($_REQUEST['existencia'] == 1) {
				list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j') < 7 ? date('j/n/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('j/n/Y')));
			}
			else if ($_REQUEST['existencia'] == 2) {
				$sql = '
					SELECT
						MAX(fecha)
							AS fecha
					FROM
						historico_inventario
					WHERE
						num_cia < 300
				';
				$result = $db->query($sql);
				
				if ($result) {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $result[0]['fecha']));
				}
				else {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j/n/Y')));
				}
			}
			else if ($_REQUEST['existencia'] == 3) {
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
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $result[0]['fecha']));
				}
				else {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', date('j/n/Y')));
				}
			}
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes, $dia, $anio));
			
			$fecha3 = date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anio));
			$fecha4 = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio));
			
			$fecha_historico = date('d/m/Y', mktime(0, 0, 0, intval($mes, 10) + 1, 0, $anio));
			
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
			
			$dias = isset($_REQUEST['complemento']) ? $dias_por_mes[intval($mes, 10)] - $dia + 7 : (isset($_REQUEST['dias']) && $_REQUEST['dias'] > 30 ? $_REQUEST['dias'] : 37);
			
			/*
			@ Existencias
			*/
			$condiciones = array();
			
			$condiciones[] = 'procpedautomat = TRUE';
			
			/*$condiciones[] = '(num_cia, codmp) IN (
				SELECT
					num_cia,
					codmp
				FROM
					mov_inv_real
				WHERE
					num_cia <= 300
					AND tipo_mov = FALSE
					AND cantidad > 0
					AND fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha2 . '\'
					AND descripcion <> \'DIFERENCIA INVENTARIO\'
					AND descripcion NOT LIKE \'TRASPASO DE AVIO%\'
				GROUP BY
					num_cia,
					codmp
			)';*/
			
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
			
			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();
				
				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}
				
				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
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
			
			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();
				
				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}
				
				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 5, 19, 21, 44))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			if ($_REQUEST['existencia'] == 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						inv.existencia
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						/*presentacion,
						tp.descripcion
							AS unidad_pedido,*/
						controlada
					FROM
						inventario_virtual inv
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}
			else if ($_REQUEST['existencia'] == 2) {
				$condiciones[] = 'fecha = \'' . $fecha_historico . '\'';
				
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						existencia,
						tuc.descripcion
							AS unidad_consumo,
						/*presentacion,
						tp.descripcion
							AS unidad_pedido,*/
						controlada
					FROM
						historico_inventario hi
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}
			else if ($_REQUEST['existencia'] == 3) {
				$condiciones[] = 'fecha = \'' . $fecha_historico . '\'';
				
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						codmp,
						cmp.nombre
							AS nombre_mp,
						ifm.inventario
							AS existencia,
						tuc.descripcion
							AS unidad_consumo,
						controlada
					FROM
						inventario_fin_mes ifm
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
						/*LEFT JOIN tipo_presentacion tp
							ON (idpresentacion = presentacion)*/
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						controlada DESC,
						codmp
				';
			}
			
			$result = $db->query($sql);
			
			$inventario = array();
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$inventario[$num_cia]['nombre_cia'] = $r['nombre_cia'];
						$inventario[$num_cia]['productos'] = array();
					}
					
					$inventario[$num_cia]['productos'][$r['codmp']] = array(
						'nombre_mp'      => $r['nombre_mp'],
						'existencia'     => $r['existencia'],
						'unidad_consumo' => $r['unidad_consumo'],
						'controlada'     => $r['controlada']
					);
				}
			}
			
			/*
			@ Consumos de hace 1 mes
			*/
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'procpedautomat = TRUE';
			
			$condiciones[] = 'tipo_mov = TRUE';
			
			$condiciones[] = 'num_cia <= 300';
			
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
			
			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();
				
				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}
				
				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
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
			
			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();
				
				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}
				
				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					codmp,
					MAX(fecha)
						AS fecha,
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
					$consumos1[$r['num_cia']][$r['codmp']] = array(
						'fecha'   => $r['fecha'],
						'consumo' => $r['consumo']
					);
				}
			}
			
			/*
			@ Consumos de hace 2 meses
			*/
			/*$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha4 . '\'';
			
			$condiciones[] = 'procpedautomat = TRUE';
			
			$condiciones[] = 'tipo_mov = TRUE';
			
			$condiciones[] = 'num_cia <= 300';
			
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
			
			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();
				
				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}
				
				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
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
			
			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();
				
				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}
				
				if (count($omitir_mps) > 0) {
					$condiciones[] = 'codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					codmp,
					MAX(fecha)
						AS fecha,
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
					$consumos2[$r['num_cia']][$r['codmp']] = array(
						'fecha'   => $r['fecha'],
						'consumo' => $r['consumo']
					);
				}
			}*/
			
			/*
			@ Realizar cálculo de pedido
			*/
			
			$pedidos = array();
			
			foreach ($inventario as $cia => $datos) {
				foreach ($datos['productos'] as $codmp => $pro) {
					$con1 = isset($consumos1[$cia][$codmp]) ? $consumos1[$cia][$codmp] : array('fecha' => NULL, 'consumo' => 0);
					//$con2 = isset($consumos2[$cia][$codmp]) ? $consumos2[$cia][$codmp] : array('fecha' => NULL, 'consumo' => 0);
					
					if ($_REQUEST['existencia'] > 1) {
						$consumo_mes = /*$con1['consumo'] > 0 ?*/ $con1['consumo'] /*: $con2['consumo']*/;
						$consumo_dia = $consumo_mes / 30;
						
						$consumo_fecha = /*$con1['consumo'] > 0 ?*/ $con1['fecha'] /*: $con2['fecha']*/;
						
						$existencia = $pro['controlada'] == 'TRUE' ? $pro['existencia'] : ($pro['existencia'] - $consumo_mes >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo_dia * $dias - $existencia);
					}
					else {
						$consumo_mes = /*$con1['consumo'] > 0 ?*/ $con1['consumo'] /*: $con2['consumo']*/;
						
						if ($con1['fecha'] != '') {
							list($con_dia, $con_mes, $con_anio) = explode('/', $con1['fecha']);
						}
						else {
							$con_dia = 0;
						}
						
						$consumo_dia = $con1['consumo'] > 0 ? ($con_dia > 0 ? $consumo_mes / intval($con_dia, 10) : 0) : $consumo_mes / 30;
						
						$consumo_fecha = /*$con1['consumo'] > 0 ?*/ $con1['fecha'] /*: $con2['fecha']*/;
						
						$existencia = $pro['controlada'] == 'TRUE' ? $pro['existencia'] : ($pro['existencia'] - $consumo_mes >= 0 ? $pro['existencia'] : 0);
						$pedido = round($consumo_dia * $dias - $existencia);
					}
					
					if ($consumo_mes != 0 || $existencia != 0) {
						if ($pedido > 0) {
							$pedidos[] = array(
								'num_cia'       => intval($cia),
								'nombre_cia'    => utf8_encode($datos['nombre_cia']),
								'codmp'         => intval($codmp),
								'nombre_mp'     => utf8_encode($pro['nombre_mp']),
								'unidad'        => utf8_encode($pro['unidad_consumo']),
								'consumo_mes'   => floatval($consumo_mes),
								'consumo_dia'   => floatval($consumo_dia),
								'consumo_fecha' => $consumo_fecha,
								'existencia'    => floatval($existencia),
								'pedido'        => floatval($pedido),
								'diferencia'    => $existencia + $pedido - $consumo_mes,
								'estimado'      => $existencia + $pedido,
								'dias_consumo'  => $consumo_mes > 0 ? round(($existencia + $pedido) / ($consumo_dia)) : 0,
								'urgente'       => $consumo_mes > 0 ? (round($existencia / $consumo_dia) <= 5 ? 'TRUE' : 'FALSE') : 'FALSE'
							);
						}
//						else {
//							$pedidos[] = array(
//								'num_cia'       => intval($cia),
//								'nombre_cia'    => utf8_encode($datos['nombre_cia']),
//								'codmp'         => intval($codmp),
//								'nombre_mp'     => utf8_encode($pro['nombre_mp']),
//								'unidad'        => utf8_encode($pro['unidad_consumo']),
//								'consumo_mes'   => floatval($consumo_mes),
//								'consumo_dia'   => floatval($consumo_dia),
//								'consumo_fecha' => $consumo_fecha,
//								'existencia'    => floatval($existencia),
//								'pedido'        => 0,
//								'diferencia'    => $existencia - $consumo_mes,
//								'estimado'      => floatval($existencia),
//								'dias_consumo'  => $consumo_mes > 0 ? round($existencia / ($consumo_dia)) : 0,
//								'urgente'       => 'FALSE'
//							);
//						}
					}
				}
			}
			
			if (count($pedidos) > 0) {
				$tpl = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosV2Reporte.tpl');
				$tpl->prepare();
				
				$tpl->assign('dias', $_REQUEST['dias']);
				
				$tpl->assign('complemento', isset($_REQUEST['complemento']) ? 'TRUE' : 'FALSE');
				
				$tpl->assign('complemento_leyenda', isset($_REQUEST['complemento']) ? ' (Complemento del mes)' : '');
				
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
				
				$num_cia = NULL;
				
				foreach ($pedidos as $p) {
					if ($num_cia != $p['num_cia']) {
						$num_cia = $p['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $p['nombre_cia']);
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('producto');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('codmp', $p['codmp']);
					$tpl->assign('nombre_mp', $p['nombre_mp']);
					$tpl->assign('unidad', $p['unidad'] . (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES'));
					$tpl->assign('consumo_mes', $p['consumo_mes'] != 0 ? number_format($p['consumo_mes'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumo_dia', $p['consumo_dia'] != 0 ? number_format($p['consumo_dia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumo_fecha', $p['consumo_fecha'] != '' ? $p['consumo_fecha'] : '&nbsp;');
					$tpl->assign('existencia', $p['existencia'] != 0 ? number_format($p['existencia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('pedido', $p['pedido'] != 0 ? number_format($p['pedido'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('diferencia', $p['diferencia'] != 0 ? number_format($p['diferencia'], 2, '.', ',') : '&nbsp;');
					
					$tpl->assign('estimado', $p['estimado'] != 0 ? number_format($p['estimado'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('dias', $p['dias_consumo'] != 0 ? round($p['dias_consumo'], 1) : '&nbsp;');
					
					$tpl->assign('dias_color', $p['dias_consumo'] > 60 ? 'red bold' : 'blue');
				}
				
				echo $tpl->getOutputContent();
			}
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/SimulacionPedidosAutomaticosV2.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
