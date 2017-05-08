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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));
			
			$condiciones = array();
			
			//$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$condiciones[] = 'fecha <= \'' . $fecha1 . '\'';
			
			$condiciones[] = 'tipo_mov = \'FALSE\'';
			
			$condiciones[] = 'descripcion <> \'DIFERENCIA INVENTARIO\'';
			
			$condiciones[] = 'descripcion NOT LIKE \'TRASPASO%\'';
			
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
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							ROUND(precio_unidad::numeric, 2)
						FROM
							mov_inv_real
						WHERE
								num_cia = result.num_cia
							AND
								codmp = result.codmp
							AND
								fecha = result.fecha
							AND
								tipo_mov = \'FALSE\'
							AND
								descripcion <> \'DIFERENCIA INVENTARIO\'
							AND
								descripcion NOT LIKE \'TRASPASO%\'
						ORDER BY
							precio_unidad DESC
						LIMIT
							1
					)
						AS
							precio,
					(
						SELECT
							nombre
								AS
									proveedor
						FROM
								mov_inv_real
							LEFT JOIN
								catalogo_proveedores
									USING
										(num_proveedor)
						WHERE
								num_cia = result.num_cia
							AND
								codmp = result.codmp
							AND
								fecha = result.fecha
							AND
								tipo_mov = \'FALSE\'
							AND
								descripcion <> \'DIFERENCIA INVENTARIO\'
							AND
								descripcion NOT LIKE \'TRASPASO%\'
						ORDER BY
							precio_unidad DESC
						LIMIT
							1
					)
						AS
							proveedor,
					(
						SELECT
							descripcion
						FROM
							mov_inv_real
						WHERE
								num_cia = result.num_cia
							AND
								codmp = result.codmp
							AND
								fecha = result.fecha
							AND
								tipo_mov = \'FALSE\'
							AND
								descripcion <> \'DIFERENCIA INVENTARIO\'
							AND
								descripcion NOT LIKE \'TRASPASO%\'
						ORDER BY
							precio_unidad DESC
						LIMIT
							1
					)
						AS
							descripcion
				FROM
					(
						SELECT
							num_cia,
							cc.nombre_corto
								AS
									nombre_cia,
							codmp,
							cmp.nombre
								AS
									nombre_mp,
							MAX(fecha)
								AS
									fecha
						FROM
								mov_inv_real mv
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
							LEFT JOIN
								catalogo_mat_primas cmp
									USING
										(codmp)
						WHERE
							' . implode(' AND ', $condiciones) . '
						GROUP BY
							num_cia,
							nombre_cia,
							codmp,
							nombre_mp
						ORDER BY
							num_cia,
							codmp
					)
						result
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/VariacionAnualPreciosCompraReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$condiciones = array();
				
				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo_mov = \'FALSE\'';
				
				$condiciones[] = 'descripcion <> \'DIFERENCIA INVENTARIO\'';
				
				$condiciones[] = 'descripcion NOT LIKE \'TRASPASO%\'';
				
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
				
				$sql = '
					SELECT
						*,
						(
							SELECT
								ROUND(MAX(precio_unidad)::numeric, 2)
							FROM
								mov_inv_real
							WHERE
									num_cia = result.num_cia
								AND
									codmp = result.codmp
								AND
									fecha = result.fecha
								AND
									tipo_mov = \'FALSE\'
								AND
									descripcion <> \'DIFERENCIA INVENTARIO\'
								AND
									descripcion NOT LIKE \'TRASPASO%\'
						)
							AS
								precio,
						(
							SELECT
								nombre
									AS
										proveedor
							FROM
									mov_inv_real
								LEFT JOIN
									catalogo_proveedores
										USING
											(num_proveedor)
							WHERE
									num_cia = result.num_cia
								AND
									codmp = result.codmp
								AND
									fecha = result.fecha
								AND
									tipo_mov = \'FALSE\'
								AND
									descripcion <> \'DIFERENCIA INVENTARIO\'
								AND
									descripcion NOT LIKE \'TRASPASO%\'
							ORDER BY
								precio_unidad DESC
							LIMIT
								1
						)
							AS
								proveedor,
						(
							SELECT
								descripcion
							FROM
								mov_inv_real
							WHERE
									num_cia = result.num_cia
								AND
									codmp = result.codmp
								AND
									fecha = result.fecha
								AND
									tipo_mov = \'FALSE\'
								AND
									descripcion <> \'DIFERENCIA INVENTARIO\'
								AND
									descripcion NOT LIKE \'TRASPASO%\'
							ORDER BY
								precio_unidad DESC
							LIMIT
								1
						)
							AS
								descripcion
					FROM
						(
							SELECT
								num_cia,
								cc.nombre_corto
									AS
										nombre_cia,
								codmp,
								cmp.nombre
									AS
										nombre_mp,
								EXTRACT(year FROM fecha)
									AS
										anio,
								EXTRACT(month FROM fecha)
									AS
										mes,
								MAX(fecha)
									AS
										fecha
							FROM
									mov_inv_real mv
								LEFT JOIN
									catalogo_companias cc
										USING
											(num_cia)
								LEFT JOIN
									catalogo_mat_primas cmp
										USING
											(codmp)
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								nombre_cia,
								codmp,
								nombre_mp,
								anio,
								mes
							ORDER BY
								num_cia,
								codmp,
								anio,
								mes
						)
							result
				';
				
				$tmp = $db->query($sql);
				
				$precios = array();
				if ($tmp) {
					foreach ($tmp as $rec) {
						$precios[$rec['num_cia']][$rec['codmp']][$rec['mes']] = array(
							'proveedor' => $rec['proveedor'],
							'fecha' => $rec['fecha'],
							'descripcion' => $rec['descripcion'],
							'precio' => $rec['precio'],
							'precio_bulto' => $rec['codmp'] == 1 ? $rec['precio'] * 44 : 0
						);
					}
				}
				
				$sql = '
					SELECT
						*,
						(
							SELECT
								descripcion
							FROM
								mov_inv_real
							WHERE
									num_cia = result.num_cia
								AND
									codmp = result.codmp
								AND
									fecha = result.fecha
								AND
									tipo_mov = \'FALSE\'
								AND
									descripcion <> \'DIFERENCIA INVENTARIO\'
								AND
									descripcion NOT LIKE \'TRASPASO%\'
								AND
									num_proveedor = result.num_proveedor
								AND
									ROUND(precio_unidad::numeric, 2) = ROUND(result.precio::numeric, 2)
							ORDER BY
								precio_unidad DESC
							LIMIT
								1
						)
							AS
								descripcion
					FROM
						(
							SELECT
								num_cia,
								num_proveedor,
								nombre
									AS
										proveedor,
								codmp,
								EXTRACT(year FROM fecha)
									AS
										anio,
								EXTRACT(month FROM fecha)
									AS
										mes,
								MAX(fecha)
									AS
										fecha,
								ROUND(precio_unidad::numeric, 2)
									AS
										precio
							FROM
									mov_inv_real mv
								LEFT JOIN
									catalogo_proveedores cp
										USING
											(num_proveedor)
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								num_proveedor,
								proveedor,
								codmp,
								anio,
								mes,
								precio_unidad
							ORDER BY
								num_cia,
								codmp,
								anio,
								mes,
								fecha
						)
							AS
								result
				';
				
				$tmp = $db->query($sql);
				
				$facturas = array();
				if ($tmp) {
					foreach ($tmp as $rec) {
						$facturas[$rec['num_cia']][$rec['codmp']][$rec['mes']][] = array(
							'proveedor' => $rec['proveedor'],
							'fecha' => $rec['fecha'],
							'descripcion' => $rec['descripcion'],
							'precio' => $rec['precio'],
							'precio_bulto' => $rec['codmp'] == 1 ? $rec['precio'] * 44 : 0
						);
					}
				}
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('anio', $_REQUEST['anio']);
					}
					
					$tpl->newBlock('producto');
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', $rec['nombre_mp']);
					$tpl->assign('precio_ini', number_format($rec['precio'], 2, '.', ','));
					
					$tpl->assign('info_ini', '<strong>' . $rec['proveedor'] . '<br />Fecha: ' . $rec['fecha'] . '<br />' . $rec['descripcion'] . '<br />Precio: $' . number_format($rec['precio'], 2, '.', ',') . '</strong>');
					
					$precio_arrastre = $rec['precio'];
					
					$precio_fin = $rec['precio'];
					
					if (isset($precios[$num_cia][$rec['codmp']])) {
						foreach ($precios[$num_cia][$rec['codmp']] as $mes => $data) {
							$variacion = round($data['precio'] * 100 / $precio_arrastre - 100, 2);
							
							if ($variacion != 0) {
								$tpl->assign('precio_' . $mes, number_format($data['precio'], 2, '.', ','));
								
								$tpl->assign('var_' . $mes, $variacion != 0 ? '<span class="' . ($variacion < 0 ? 'blue' : 'red') . '">(' . number_format($variacion, 2, '.', ',') . '%)</span> ' : '');
								
								$precio_fin = $data['precio'];
								
								if (isset($facturas[$num_cia][$rec['codmp']][$mes])) {
									$info = array();
									
									foreach ($facturas[$num_cia][$rec['codmp']][$mes] as $fac) {
										$info[] = '<strong>' . $fac['proveedor'] . '<br />Fecha: ' . $fac['fecha'] . '<br />' . $fac['descripcion'] . '<br />Precio: $' . number_format($fac['precio'], 2, '.', ',') . ($fac['precio_bulto'] != 0 ? '<br />Precio bulto: $' . number_format($fac['precio_bulto'], 2) : '') . '</strong>';
									}
									
									$tpl->assign('info_' . $mes, implode('<hr />', $info));
								}
							}
							
							if ($_REQUEST['tipo'] == 2) {
								$precio_arrastre = $data['precio'];
							}
						}
					}
					
					$tpl->assign('precio_fin', number_format($precio_fin, 2, '.', ','));
					
					$variacion = round($precio_fin * 100 / $rec['precio'] - 100, 2);
					
					$tpl->assign('var', $variacion != 0 ? '<span class="' . ($variacion < 0 ? 'blue' : 'red') . '">(' . number_format($variacion, 2, '.', ',') . '%)</span> ' : '');
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'descargar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));
			
			$condiciones = array();
			
			$condiciones[] = 'fecha <= \'' . $fecha1 . '\'';
			
			$condiciones[] = 'tipo_mov = \'FALSE\'';
			
			$condiciones[] = 'descripcion <> \'DIFERENCIA INVENTARIO\'';
			
			$condiciones[] = 'descripcion NOT LIKE \'TRASPASO%\'';
			
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
			
			$sql = '
				SELECT
					*,
					(
						SELECT
							ROUND(precio_unidad::numeric, 2)
						FROM
							mov_inv_real
						WHERE
								num_cia = result.num_cia
							AND
								codmp = result.codmp
							AND
								fecha = result.fecha
							AND
								tipo_mov = \'FALSE\'
							AND
								descripcion <> \'DIFERENCIA INVENTARIO\'
							AND
								descripcion NOT LIKE \'TRASPASO%\'
						ORDER BY
							precio_unidad DESC
						LIMIT
							1
					)
						AS
							precio
				FROM
					(
						SELECT
							num_cia,
							cc.nombre_corto
								AS
									nombre_cia,
							codmp,
							cmp.nombre
								AS
									nombre_mp,
							MAX(fecha)
								AS
									fecha
						FROM
								mov_inv_real mv
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
							LEFT JOIN
								catalogo_mat_primas cmp
									USING
										(codmp)
						WHERE
							' . implode(' AND ', $condiciones) . '
						GROUP BY
							num_cia,
							nombre_cia,
							codmp,
							nombre_mp
						ORDER BY
							num_cia,
							codmp
					)
						result
			';
			
			$result = $db->query($sql);
			
			$data = '';
			
			if ($result) {
				$condiciones = array();
				
				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
				
				$condiciones[] = 'tipo_mov = \'FALSE\'';
				
				$condiciones[] = 'descripcion <> \'DIFERENCIA INVENTARIO\'';
				
				$condiciones[] = 'descripcion NOT LIKE \'TRASPASO%\'';
				
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
				
				$sql = '
					SELECT
						*,
						(
							SELECT
								ROUND(MAX(precio_unidad)::numeric, 2)
							FROM
								mov_inv_real
							WHERE
									num_cia = result.num_cia
								AND
									codmp = result.codmp
								AND
									fecha = result.fecha
								AND
									tipo_mov = \'FALSE\'
								AND
									descripcion <> \'DIFERENCIA INVENTARIO\'
								AND
									descripcion NOT LIKE \'TRASPASO%\'
						)
							AS
								precio
					FROM
						(
							SELECT
								num_cia,
								cc.nombre_corto
									AS
										nombre_cia,
								codmp,
								cmp.nombre
									AS
										nombre_mp,
								EXTRACT(year FROM fecha)
									AS
										anio,
								EXTRACT(month FROM fecha)
									AS
										mes,
								MAX(fecha)
									AS
										fecha
							FROM
									mov_inv_real mv
								LEFT JOIN
									catalogo_companias cc
										USING
											(num_cia)
								LEFT JOIN
									catalogo_mat_primas cmp
										USING
											(codmp)
							WHERE
								' . implode(' AND ', $condiciones) . '
							GROUP BY
								num_cia,
								nombre_cia,
								codmp,
								nombre_mp,
								anio,
								mes
							ORDER BY
								num_cia,
								codmp,
								anio,
								mes
						)
							result
				';
				
				$tmp = $db->query($sql);
				
				$precios = array();
				if ($tmp) {
					foreach ($tmp as $rec) {
						$precios[$rec['num_cia']][$rec['codmp']][$rec['mes']] = $rec['precio'];
					}
				}
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$data .= '""' . "\r\n";
						}
						
						$num_cia = $rec['num_cia'];
						
						$data .= '"' . $num_cia . ' ' . utf8_encode($rec['nombre_cia']) . '"' . "\r\n";
						$data .= '""' . "\r\n";
						$data .= '"PRODUCTO","PRECIO INICIAL","ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC","PRECIO FINAL"' . "\r\n";
					}
					
					$data .= '"' . $rec['codmp'] . ' ' . utf8_encode($rec['nombre_mp']) . '","' . number_format($rec['precio'], 2, '.', ',') . '",';
					
					$precio_arrastre = $rec['precio'];
					
					$precio_fin = $rec['precio'];
					
					if (isset($precios[$num_cia][$rec['codmp']])) {
						$meses = array();
						
						for ($mes = 1; $mes <= 12; $mes++) {
							if (isset($precios[$num_cia][$rec['codmp']][$mes])) {
								$variacion = round($precios[$num_cia][$rec['codmp']][$mes] * 100 / $precio_arrastre - 100, 2);
								
								if ($variacion != 0) {
									$meses[] = '"' . ($variacion != 0 ? '(' . $variacion . '%) ' : '') . number_format($precios[$num_cia][$rec['codmp']][$mes], 2, '.', ',') . '"';
									
									$precio_fin = $precios[$num_cia][$rec['codmp']][$mes];
								}
								else {
									$meses[] .= '""';
								}
								
								if ($_REQUEST['tipo'] == 2) {
									$precio_arrastre = $precios[$num_cia][$rec['codmp']][$mes];
								}
							}
							else {
								$meses[] .= '""';
							}
						}
						
						$data .= implode(',', $meses) . ",";
					}
					
					$variacion = round($precio_fin * 100 / $rec['precio'] - 100, 2);
					
					$data .= '"' . ($variacion != 0 ? '(' . $variacion . '%) ' : '') . number_format($precio_fin, 2, '.', ',') . '"' . "\r\n";
				}
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="VariacionAnualPreciosCompra' . $_REQUEST['anio'] . '.csv"');
			
			echo $data;
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/VariacionAnualPreciosCompra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

if ($isIpad) {
	$select = '<select name="anio" id="anio">';
	
	for ($anio = date('Y') - 4; $anio <= date('Y'); $anio++) {
		$select .= '<option value="' . $anio . '"' . ($anio == date('Y') ? 'selected' : '') . '>' . $anio . '</option>';
	}
	
	$select .= '</select>';
	
	$tpl->assign('anio', $select);
}
else {
	$tpl->assign('anio', '<input name="anio" type="text" class="valid Focus toPosInt center" id="anio" value="' . date('Y') . '" size="4" maxlength="4" />');
}

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

$tpl->printToScreen();
?>
