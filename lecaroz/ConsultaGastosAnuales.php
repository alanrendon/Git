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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'reporte':
			$fecha1 = '01/01/' . $_REQUEST['anio'];
			$fecha2 = '31/12/' . $_REQUEST['anio'];
			
			$condiciones1[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
					$condiciones1[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones1[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
						
			$condiciones1[] = 'codigo_edo_resultados IN (1, 2)';
			
			$condiciones1[] = 'codgastos NOT IN (141)';
			
			$condiciones1[] = 'ROUND(importe::numeric, 2) <> 0';
			
			$condiciones2[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
					$condiciones2[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones1[] = '
				(
					num_cia,
					fecha,
					codgastos,
					importe
				)
					NOT IN
						(
							SELECT
								num_cia,
								fecha,
								codgastos,
								importe
							FROM
									pagos_otras_cias
								LEFT JOIN
									cheques
										USING
											(
												num_cia,
												folio,
												cuenta
											)
							WHERE
								' . implode(' AND ', $condiciones2) . '
						)
			';
			
			$condiciones3[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
					$condiciones3[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones3[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones3[] = 'clave_balance = \'TRUE\'';
			
			$condiciones4[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
					$condiciones4[] = 'num_cia_aplica IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones4[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones4[] = 'codigo_edo_resultados IN (1, 2)';
			
			$condiciones4[] = 'codgastos NOT IN (141)';
			
			$condiciones4[] = 'ROUND(importe::numeric, 2) <> 0';
			
			$condiciones5[] = 'fecha_con >= \'01/07/2008\'';
			
			$condiciones5[] = 'fecha_con BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
					$condiciones5[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones5[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones5[] = 'cod_mov IN (1, 16, 13, 7, 79)';
			
			$sql = '
				SELECT
					num_cia,
					nombre,
					cod,
					descripcion,
					tipo,
					anio,
					mes,
					orden,
					SUM(importe)
						AS
							importe
				FROM
					(
							SELECT
								num_cia,
								nombre,
								codgastos
									AS
										cod,
								descripcion,
								codigo_edo_resultados
									AS
										tipo,
								EXTRACT(year from fecha)
									AS
										anio,
								EXTRACT(month from fecha)
									AS
										mes,
								orden,
								ROUND(SUM(importe)::numeric, 2)
									AS
										importe
							FROM
									movimiento_gastos mg
								LEFT JOIN
									catalogo_gastos cg
										USING
											(
												codgastos
											)
								LEFT JOIN
									catalogo_companias cc
										USING
											(
												num_cia
											)
							WHERE
								' . implode(' AND ', $condiciones1) . '
							GROUP BY
								num_cia,
								nombre,
								codgastos,
								descripcion,
								tipo,
								anio,
								mes,
								orden
						
						UNION
						
							SELECT
								num_cia,
								nombre,
								182
									AS
										cod,
								\'IMPUESTO EROGACIONES\'
									AS
										descripcion,
								2
									AS
										tipo,
								EXTRACT(year from fecha_con)
									AS
										anio,
								EXTRACT(month from fecha_con)
									AS
										mes,
								2
									AS
										orden,
								ROUND((SUM(importe)::numeric - 25000) * (CASE WHEN EXTRACT(year FROM fecha_con) < 2010 THEN 0.02 ELSE 0.03 END), 2)
									AS
										importe
							FROM
									estado_cuenta ec
								LEFT JOIN
									catalogo_companias cc
										USING
											(
												num_cia
											)
							WHERE
								' . implode(' AND ', $condiciones5) . '
							GROUP BY
								num_cia,
								nombre,
								cod,
								descripcion,
								tipo,
								anio,
								mes,
								orden
						
						UNION
						
							SELECT
								num_cia,
								nombre,
								cod_gastos
									AS
										cod,
								descripcion,
								3
									AS
										tipo,
								EXTRACT(year from fecha)
									AS
										anio,
								EXTRACT(month from fecha)
									AS
										mes,
								1
									AS
										orden,
								ROUND(SUM(
										CASE
											WHEN tipo_mov = \'FALSE\' THEN
												-importe
											ELSE
												importe
										END
									)::numeric, 2)
									AS
										importe
							FROM
									gastos_caja gc
								LEFT JOIN
									catalogo_gastos_caja cgc
										ON
											(
												cgc.id = gc.cod_gastos
											)
								LEFT JOIN
									catalogo_companias cc
										USING
											(
												num_cia
											)
									
							WHERE
								' . implode(' AND ', $condiciones3) . '
							GROUP BY
								num_cia,
								nombre,
								cod,
								descripcion,
								tipo,
								anio,
								mes,
								orden
						
						UNION
						
							/*
							@@@
							@@@ Incluir pagos hechos por otras compañías para la compañía consultada
							@@@
							*/
							SELECT
								num_cia,
								nombre,
								codgastos
									AS
										cod,
								descripcion,
								codigo_edo_resultados
									AS
										tipo,
								EXTRACT(year from fecha)
									AS
										anio,
								EXTRACT(month from fecha)
									AS
										mes,
								orden,
								ROUND(SUM(importe)::numeric, 2)
									AS
										importe
							FROM
									pagos_otras_cias
								LEFT JOIN
									cheques
										USING
											(
												num_cia,
												folio,
												cuenta
											)
								LEFT JOIN
									catalogo_gastos
										USING
											(
												codgastos
											)
								LEFT JOIN
									catalogo_companias
										USING
											(
												num_cia
											)
							WHERE
								' . implode(' AND ', $condiciones4) . '
							GROUP BY
								num_cia,
								nombre,
								cod,
								descripcion,
								tipo,
								anio,
								mes,
								orden
					)
						result
				GROUP BY
					num_cia,
					nombre,
					cod,
					descripcion,
					tipo,
					anio,
					mes,
					orden
				
				ORDER BY
					num_cia,
					tipo,
					orden,
					cod,
					anio,
					mes
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/ReporteGastosAnuales.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tipos = array(
					1 => 'DE OPERACI&Oacute;N',
					2 => 'GENERALES',
					3 => 'DE CAJA'
				);
				
				function new_page($num_cia, $nombre_cia, $tipo) {
					$GLOBALS['tpl']->newBlock('reporte');
					$GLOBALS['tpl']->assign('num_cia', $num_cia);
					$GLOBALS['tpl']->assign('nombre_cia', $nombre_cia);
					$GLOBALS['tpl']->assign('anio', $_REQUEST['anio']);
					
					if ($tipo != NULL) {
						$GLOBALS['tpl']->newBlock('tipo');
						$GLOBALS['tpl']->assign('tipo', $GLOBALS['tipos'][$tipo]);
						$GLOBALS['tpl']->assign('leyenda', '<span style="font-size:6pt;">(continuaci&oacute;n)</span>');
					}
				}
				
				$num_cia = NULL;
				
				$max_data_size = 18.0;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							if ($data_size + 1.0 > $max_data_size) {
								$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
								
								new_page($num_cia, $nombre_cia, $tipo);
								
								$data_size = 0;
								
								$page_count++;
							}
							
							$tpl->newBlock('subtotales');
							
							foreach($subtotales as $m => $t) {
								$tpl->assign($m, number_format($t, 2, '.', ','));
							}
							
							$data_size += 1.0;
							
							if ($data_size + 0.5 > $max_data_size) {
								$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
								
								new_page($num_cia, $nombre_cia, $tipo);
								
								$data_size = 0;
								
								$page_count++;
							}
							
							$tpl->newBlock('totales');
							
							foreach ($totales as $m => $t) {
								$tpl->assign($m, number_format($t, 2, '.', ','));
							}
							
							$data_size += 0.5;
							
							if ($page_count % 2 != 0) {
								$tpl->assign('reporte.salto', '<div class="saltopagina"></div><br class="saltopagina" />');
							}
							else {
								$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
							}
						}
						
						$num_cia = $rec['num_cia'];
						$nombre_cia = $rec['nombre'];
						
						$totales = array(
							1  => 0,
							2  => 0,
							3  => 0,
							4  => 0,
							5  => 0,
							6  => 0,
							7  => 0,
							8  => 0,
							9  => 0,
							10 => 0,
							11 => 0,
							12 => 0
						);
						
						$tipo = NULL;
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $nombre_cia);
						$tpl->assign('anio', $_REQUEST['anio']);
						
						$data_size = 0;
						$page_count = 1;
					}
					
					if ($tipo != $rec['tipo']) {
						if ($tipo != NULL) {
							if ($data_size + 1.0 > $max_data_size) {
								$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
								
								new_page($num_cia, $nombre_cia, $tipo);
								
								$data_size = 1.0;
								
								$page_count++;
							}
							
							$tpl->newBlock('subtotales');
							
							foreach ($subtotales as $m => $t) {
								$tpl->assign($m, number_format($t, 2, '.', ','));
							}
							
							$data_size += 1.0;
						}
						
						if ($data_size + 1.0 + 0.42 > $max_data_size) {
							$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
							
							new_page($num_cia, $nombre_cia, NULL);
							
							$data_size = 0;
							
							$page_count++;
						}
						
						$tipo = $rec['tipo'];
						
						$tpl->newBlock('tipo');
						$tpl->assign('tipo', $tipos[$tipo]);
						
						$subtotales = array(
							1  => 0,
							2  => 0,
							3  => 0,
							4  => 0,
							5  => 0,
							6  => 0,
							7  => 0,
							8  => 0,
							9  => 0,
							10 => 0,
							11 => 0,
							12 => 0
						);
						
						$cod = NULL;
						
						$data_size += 1.0;
					}
					
					if ($cod != $rec['cod']) {
						$cod = $rec['cod'];
						
						/*
						@@@ Claúsula: Todos los movimientos con código 140 'IMPUESTOS' y con fecha mayor al 1 de Octubre de 2006 omitirlo
						*/
						if ($cod == 140 && mktime(0, 0, 0, $rec['mes'], 1, $rec['anio']) >= mktime(0, 0, 0, 10, 1, 2006)) {
							continue;
						}
						
						if ($data_size + 0.4 > $max_data_size) {
							$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
							
							new_page($num_cia, $nombre_cia, $tipo);
							
							$data_size = 0;
							
							$page_count++;
						}
						
						$tpl->newBlock('row');
						$tpl->assign('cod', $cod);
						$tpl->assign('descripcion', $rec['descripcion']);
						
						$data_size += 0.42;
					}
					
					/*
					@@@ Claúsula: Todos los movimientos con código 140 'IMPUESTOS' y con fecha mayor al 1 de Octubre de 2006 omitirlo
					*/
					if ($cod == 140 && mktime(0, 0, 0, $rec['mes'], 1, $rec['anio']) >= mktime(0, 0, 0, 10, 1, 2006)) {
						continue;
					}
					
					$tpl->assign($rec['mes'], number_format($rec['importe'], 2, '.', ','));
					
					$subtotales[$rec['mes']] += $rec['importe'];
					$totales[$rec['mes']] += $rec['importe'];
				}
				
				if ($num_cia != NULL) {
					if ($data_size + 1.0 > $max_data_size) {
						$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
						
						new_page($num_cia, $nombre_cia, $tipo);
						
						$data_size = 0;
						
						$page_count++;
					}
					
					$tpl->newBlock('subtotales');
					
					foreach($subtotales as $m => $t) {
						$tpl->assign($m, number_format($t, 2, '.', ','));
					}
					
					$data_size += 1.0;
					
					if ($data_size + 0.5 > $max_data_size) {
						$tpl->assign('reporte.salto', '<div class="saltopagina"></div>');
						
						new_page($num_cia, $nombre_cia, $tipo);
						
						$data_size = 0;
						
						$page_count++;
					}
					
					$tpl->newBlock('totales');
					
					foreach ($totales as $m => $t) {
						$tpl->assign($m, number_format($t, 2, '.', ','));
					}
					
					$data_size += 0.5;
				}
			}
			
			$tpl->printToScreen();
			
		break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ConsultaGastosAnuales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

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
