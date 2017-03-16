<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'codigos':
			if (!isset($_REQUEST['abonos'])) {
				$condiciones[] = 'tipo_mov = \'TRUE\'';
			}
			
			if (!isset($_REQUEST['cargos'])) {
				$condiciones[] = 'tipo_mov = \'FALSE\'';
			}
			
			if (!isset($_REQUEST['banco']) || (isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
				$sql[] = '
					SELECT
						1
							AS
								banco,
						cod_mov
							AS
								value,
						LPAD(cod_mov::varchar(3), 3, \'0\') || \' \' || descripcion
							AS
								text
					FROM
						catalogo_mov_bancos
					' . (isset($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				';
			}
			
			if (!isset($_REQUEST['banco']) || (isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
				$sql[] = '
					SELECT
						2
							AS
								banco,
						cod_mov
							AS
								value,
						LPAD(cod_mov::varchar(3), 3, \'0\') || \' \' || descripcion
							AS
								text
					FROM
						catalogo_mov_santander
					' . (isset($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				';
			}
			
			$sql = implode(' UNION ', $sql) . ' GROUP BY banco, value, text ORDER BY banco, value';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data = array();
				
				$banco = NULL;
				foreach ($result as $r) {
					if ($banco != $r['banco']) {
						$banco = $r['banco'];
						
						$data[] = array(
							'text' => $banco == 1 ? 'BANORTE' : 'SANTANDER',
							'disabled' => 'disabled',
							'styles' => array(
								'color' => $banco == 1 ? '#00C' : '#C00',
								'text-decoration' => 'underline',
								'margin' => '4px 0',
								'font-weight' => 'bold',
							)
						);
					}
					$data[] = $r;
				}
				
				echo json_encode($data);
			}
		break;
		
		case 'consultar':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 26))) {
				if ($_SESSION['tipo_usuario'] == 2) {
					$condiciones[] = 'ec.num_cia BETWEEN 900 AND 998';
				}
				else {
					$condiciones[] = 'ec.num_cia BETWEEN 1 AND 899';
				}
			}
			
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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Banco
			*/
			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'ec.cuenta = ' . $_REQUEST['banco'];
			}
			
			/*
			@ Periodo
			*/
			if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
				if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
					$fecha1 = $_REQUEST['fecha1'];
					$fecha2 = $_REQUEST['fecha2'];
				}
				else if (isset($_REQUEST['fecha1'])) {
					$condiciones[] = 'ec.fecha >= \'' . $_REQUEST['fecha1'] . '\'';
					$fecha1 = $_REQUEST['fecha1'];
					$fecha2 = $_REQUEST['fecha1'];
				}
				else if (isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha2'] . '\'';
					$fecha1 = $_REQUEST['fecha2'];
					$fecha2 = date('d/m/Y');
				}
			}
			else if (!isset($_REQUEST['fecha_con1']) && !isset($_REQUEST['fecha_con2'])) {
				$condiciones[] = 'ec.fecha BETWEEN \'' . date('01/m/Y') . '\' AND \'' . date('d/m/Y') . '\'';
				$fecha1 = date('01/m/Y');
				$fecha2 = date('d/m/Y');
			}
			else {
				$fecha1 = date('01/m/Y');
				$fecha2 = date('d/m/Y');
			}
			
			/*
			@ Conciliados
			*/
			if (isset($_REQUEST['fecha_con1']) || isset($_REQUEST['fecha_con2'])) {
				if (isset($_REQUEST['fecha_con1']) && isset($_REQUEST['fecha_con2'])) {
					$condiciones[] = 'ec.fecha_con BETWEEN \'' . $_REQUEST['fecha_con1'] . '\' AND \'' . $_REQUEST['fecha_con2'] . '\'';
				}
				else if (isset($_REQUEST['fecha_con1'])) {
					$condiciones[] = 'ec.fecha_con >= \'' . $_REQUEST['fecha_con1'] . '\'';
				}
				else if (isset($_REQUEST['fecha_con2'])) {
					$condiciones[] = 'ec.fecha_con = \'' . $_REQUEST['fecha_con2'] . '\'';
				}
			}
			
			/*
			@ Intervalo de folios
			*/
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();
				
				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}
				
				if (count($folios) > 0) {
					$condiciones[] = 'ec.folio IN (' . implode(', ', $folios) . ')';
				}
			}
			
			/*
			@ Intervalo de importes
			*/
			if (isset($_REQUEST['importes']) && trim($_REQUEST['importes']) != '') {
				$importe_piezas = array();
				$importe_intervalos = array();
				
				$pieces = explode(',', $_REQUEST['importes']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$importe_intervalos[] =  'ec.importe BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					}
					else {
						$importe_piezas[] = $piece;
					}
				}
				
				if (count($importe_piezas) > 0) {
					$condiciones_importe[] = 'ec.importe IN (' . implode(', ', $importe_piezas) . ')';
				}
				
				if (count($importe_intervalos) > 0) {
					$condiciones_importe[] = implode(' OR ', $importe_intervalos);
				}
				
				if (count($condiciones_importe) > 0) {
					$condiciones[] = '(' . implode(' OR ', $condiciones_importe) . ')';
				}
			}
			
			if (!isset($_REQUEST['abonos'])) {
				$condiciones[] = 'ec.tipo_mov = \'TRUE\'';
			}
			
			if (!isset($_REQUEST['cargos'])) {
				$condiciones[] = 'ec.tipo_mov = \'FALSE\'';
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'ec.fecha_con IS NOT NULL';
			}
			
			if (!isset($_REQUEST['conciliados'])) {
				$condiciones[] = 'ec.fecha_con IS NULL';
			}
			
			if (isset($_REQUEST['codigos'])) {
				$condiciones[] = 'ec.cod_mov IN (' . implode(', ', $_REQUEST['codigos']) . ')';
			}
			
			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					cc.nombre || \' (\' || cc.nombre_corto || \')\'
						AS
							nombre_cia,
					cc.clabe_cuenta
						AS
							cuenta1,
					cc.clabe_cuenta2
						AS
							cuenta2,
					ec.fecha,
					ec.fecha_con
						AS
							conciliado,
					cuenta
						AS
							banco,
					CASE
						WHEN ec.tipo_mov = \'TRUE\' THEN
							-ec.importe
						ELSE
							ec.importe
					END
						AS
							importe,
					ec.folio,
					LPAD(c.num_proveedor::varchar(4), 4, \'0\') || \' \' || c.a_nombre
						AS
							beneficiario,
					ec.concepto,
					ec.cod_mov,
					LPAD(ec.cod_mov::varchar(3), 3, \'0\') || \' \' || (
															CASE
																WHEN cuenta = 1 THEN
																	(
																		SELECT
																			descripcion
																		FROM
																			catalogo_mov_bancos
																		WHERE
																			cod_mov = ec.cod_mov
																		LIMIT 1
																	)
																ELSE
																	(
																		SELECT
																			descripcion
																		FROM
																			catalogo_mov_santander
																		WHERE
																			cod_mov = ec.cod_mov
																		LIMIT 1
																	)
															END
														)
						AS
							codigo,
					/*
					@ Información de conciliación
					*/
					CASE
						WHEN ec.fecha_con IS NOT NULL AND ec.tipo_con > 0 THEN
							\'Usuario: \' || COALESCE(a.nombre, \'Imposible recuperar usuario\') || \'<br />Fecha: \' || (
								CASE
									WHEN ec.timestamp IS NOT NULL THEN
										ec.timestamp::timestamp(0)::text
									ELSE
										\'Imposible recuperar fecha de conciliaci&oacute;n\'
								END
							)
						WHEN ec.fecha_con IS NOT NULL AND (ec.tipo_con = 0 OR ec.tipo_con IS NULL) THEN
							\'Imposible recuperar datos de conciliaci&oacute;n\'
						ELSE
							NULL
					END
						AS
							info
				FROM
						estado_cuenta ec
					LEFT JOIN
						cheques c
							USING
								(
									num_cia,
									cuenta,
									folio
								)
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						auth a
							ON
								(
									a.iduser = ec.iduser
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					CASE
						WHEN ec.folio IS NOT NULL THEN
							ec.cuenta
						ELSE
							NULL
					END,
					ec.folio,
					ec.tipo_mov,
					ec.importe
						DESC
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/ReporteEstadoCuenta.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$num_cia = $r['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $r['nombre_cia']);
						$tpl->assign('fecha', date('d/m/Y'));
						$tpl->assign('hora', date('H:i'));
						
						$abonos = 0;
						$cargos = 0;
						
						$abonos_con = 0;
						$cargos_con = 0;
						
						if (isset($_REQUEST['conciliados']) &&
							isset($_REQUEST['abonos']) &&
							isset($_REQUEST['cargos']) &&
							!isset($_REQUEST['codigos']) &&
							!isset($_REQUEST['folios']) &&
							!isset($_REQUEST['importes'])) {
							$condiciones = array();
							
							$condiciones[] = 'num_cia = ' . $num_cia;
							
							if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
								$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
							}
							
							$sql = '
								SELECT
									cuenta
										AS
											banco,
									saldo_bancos - COALESCE(
														(
															SELECT
																SUM(
																	CASE
																		WHEN tipo_mov = \'TRUE\' THEN
																			-importe
																		ELSE
																			importe
																	END
																)
															FROM
																estado_cuenta
															WHERE
																	num_cia = s.num_cia
																AND
																	cuenta = s.cuenta
																AND
																	fecha >= \'' . $fecha1 . '\'
																AND
																	fecha_con IS NOT NULL
														),
														0
													)
										AS
											banco_ini,
									saldo_bancos + COALESCE(
														(
															SELECT
																SUM(
																	CASE
																		WHEN tipo_mov = \'TRUE\' THEN
																			-importe
																		ELSE
																			importe
																	END
																)
															FROM
																estado_cuenta
															WHERE
																	num_cia = s.num_cia
																AND
																	cuenta = s.cuenta
																AND
																	fecha > \'' . $fecha2 . '\'
																AND
																	fecha_con IS NOT NULL
														),
														0
													)
										AS
											banco_fin,
									saldo_libros - COALESCE(
														(
															SELECT
																SUM(
																	CASE
																		WHEN tipo_mov = \'TRUE\' THEN
																			-importe
																		ELSE
																			importe
																	END
																)
															FROM
																estado_cuenta
															WHERE
																	num_cia = s.num_cia
																AND
																	cuenta = s.cuenta
																AND
																	fecha >= \'' . $fecha1 . '\'
														),
														0
													)
										AS
											libro_ini,
									saldo_libros - COALESCE(
														(
															SELECT
																SUM(
																	CASE
																		WHEN tipo_mov = \'TRUE\' THEN
																			-importe
																		ELSE
																			importe
																	END
																)
															FROM
																estado_cuenta
															WHERE
																	num_cia = s.num_cia
																AND
																	cuenta = s.cuenta
																AND
																	fecha > \'' . $fecha2 . '\'
														),
														0
													)
										AS
											libro_fin
								FROM
									saldos s
								WHERE
									' . implode(' AND ', $condiciones) . '
								ORDER BY
									banco
							';
							$saldos = $db->query($sql);
							
							$tpl->newBlock('saldos_ini');
							$banco_ini = 0;
							$libro_ini = 0;
							foreach ($saldos as $s) {
								$tpl->newBlock('banco_ini');
								$tpl->assign('logo_banco', $s['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
								$tpl->assign('banco', $s['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($r['cuenta' . $s['banco']]) != '' ? $r['cuenta' . $s['banco']] : '[SIN CUENTA]');
								$tpl->assign('saldo_banco', round($s['banco_ini'], 2) != 0 ? number_format($s['banco_ini'], 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($s['libro_ini'], 2) != 0 ? number_format($s['libro_ini'], 2, '.', ',') : '--');
								
								$banco_ini += round($s['banco_ini'], 2);
								$libro_ini += round($s['libro_ini'], 2);
							}
							
							if (!isset($_REQUEST['banco'])) {
								$tpl->newBlock('total_ini');
								$tpl->assign('saldo_banco', round($banco_ini, 2) != 0 ? number_format($banco_ini, 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($libro_ini, 2) != 0 ? number_format($libro_ini, 2, '.', ',') : '--');
							}
							
							$tpl->newBlock('saldos_fin');
							$banco_fin = 0;
							$libro_fin = 0;
							foreach ($saldos as $s) {
								$tpl->newBlock('banco_fin');
								$tpl->assign('logo_banco', $s['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
								$tpl->assign('banco', $s['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($r['cuenta' . $s['banco']]) != '' ? $r['cuenta' . $s['banco']] : '[SIN CUENTA]');
								$tpl->assign('saldo_banco', round($s['banco_fin'], 2) != 0 ? number_format($s['banco_fin'], 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($s['libro_fin'], 2) != 0 ? number_format($s['libro_fin'], 2, '.', ',') : '--');
								
								$dif = round($s['banco_fin'] - $s['libro_fin'], 2);
								
								$tpl->assign('dif', $dif != 0 ? number_format($dif, 2, '.', ',') : '--');
								$tpl->assign('color_dif', round($dif, 2) != 0 ? ($dif > 0 ? ' blue' : ' red') : '');
								
								$banco_fin += round($s['banco_fin'], 2);
								$libro_fin += round($s['libro_fin'], 2);
							}
							
							
							if (!isset($_REQUEST['banco'])) {
								$tpl->newBlock('total_fin');
								$tpl->assign('saldo_banco', round($banco_fin, 2) != 0 ? number_format($banco_fin, 2, '.', ',') : '--');
								$tpl->assign('saldo_libro', round($libro_fin, 2) != 0 ? number_format($libro_fin, 2, '.', ',') : '--');
								$dif = $banco_fin - $libro_fin;
								$tpl->assign('dif', round($dif, 2) != 0 ? number_format($dif, 2, '.', ',') : '--');
								$tpl->assign('color_dif', round($dif, 2) != 0 ? ($dif > 0 ? ' blue' : ' red') : '');
							}
						}
						else {
							$condiciones = array();
							
							$condiciones[] = 'num_cia = ' . $num_cia;
							
							if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
								$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
							}
							
							$sql = '
								SELECT
									cuenta
										AS
											banco
								FROM
									saldos s
								WHERE
									' . implode(' AND ', $condiciones) . '
								ORDER BY
									banco
							';
							$cuentas = $db->query($sql);
							
							$tpl->newBlock('cuentas');
							
							foreach ($cuentas as $c) {
								$tpl->newBlock('cuenta');
								$tpl->assign('banco', $c['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
								$tpl->assign('cuenta', trim($r['cuenta' . $c['banco']]) != '' ? $r['cuenta' . $c['banco']] : '[SIN CUENTA]');
							}
						}
						
						if (isset($_REQUEST['pendientes'])) {
							$tpl->newBlock('totales');
						}
						
						if (isset($_REQUEST['conciliados'])) {
							$tpl->newBlock('conciliados');
						}
						
						if (!isset($_REQUEST['banco']) || empty($_REQUEST['banco'])) {
							$tpl->newBlock('th_banco');
						}
					}
					
					$tpl->newBlock('row');
					$tpl->assign('id', $r['id']);
					
					if (!isset($_REQUEST['banco']) || empty($_REQUEST['banco'])) {
						$tpl->newBlock('td_banco');
						$tpl->assign('logo_banco', $r['banco'] == 1 ? 'Banorte16x16.png' : 'Santander16x16.png');
						$tpl->assign('nombre_banco', $r['banco'] == 1 ? 'Banorte' : 'Santander');
						$tpl->gotoBlock('row');
					}
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('conciliado', $r['conciliado'] != '' ? $r['conciliado'] : '&nbsp;');
					$tpl->assign('info', $r['info']);
					$tpl->assign('abono', $r['importe'] > 0 ? number_format(abs($r['importe']), 2, '.', ',') : '&nbsp;');
					$tpl->assign('cargo', $r['importe'] < 0 ? number_format(abs($r['importe']), 2, '.', ',') : '&nbsp;');
					$tpl->assign('folio', $r['folio'] > 0 ? $r['folio'] : '&nbsp;');
					$tpl->assign('color_folio', $r['folio'] > 0 ? ($r['cod_mov'] == 5 ? ' purple' : ($r['cod_mov'] == 41 ? ' green' : ' red')) : '');
					$tpl->assign('beneficiario', $r['beneficiario'] != '' ? $r['beneficiario'] : '&nbsp;');
					$tpl->assign('concepto', trim($r['concepto']) != '' ? $r['concepto'] : '&nbsp;');
					$tpl->assign('codigo', trim($r['codigo']) != '' ? $r['codigo'] : '&nbsp;');
					
					$abonos += $r['importe'] > 0 ? $r['importe'] : 0;
					$cargos += $r['importe'] < 0 ? abs($r['importe']) : 0;
					
					$abonos_con += $r['conciliado'] != '' && $r['importe'] > 0 ? $r['importe'] : 0;
					$cargos_con += $r['conciliado'] != '' && $r['importe'] < 0 ? abs($r['importe']) : 0;
					
					if (isset($_REQUEST['pendientes'])) {
						$tpl->assign('totales.abonos', $abonos != 0 ? number_format($abonos, 2, '.', ',') : '--');
						$tpl->assign('totales.cargos', $cargos != 0 ? number_format($cargos, 2, '.', ',') : '--');
					}
					
					if (isset($_REQUEST['conciliados'])) {
						$tpl->assign('conciliados.abonos', $abonos_con != 0 ? number_format($abonos_con, 2, '.', ',') : '--');
						$tpl->assign('conciliados.cargos', $cargos_con != 0 ? number_format($cargos_con, 2, '.', ',') : '--');
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/EstadoCuenta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
?>
