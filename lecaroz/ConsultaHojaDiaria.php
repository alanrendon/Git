<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

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

$semana = array(
	0 => 'DOMINGO',
	1 => 'LUNES',
	2 => 'MARTES',
	3 => 'MIERCOLES',
	4 => 'JUEVES',
	5 => 'VIERNES',
	6 => 'SABADO'
);

$meses = array(
	1 => 'ENERO',
	2 => 'FEBRERO',
	3 => 'MARZO',
	4 => 'ABRIL',
	5 => 'MAYO',
	6 => 'JUNIO',
	7 => 'JULIO',
	8 => 'AGOSTO',
	9 => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
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
			@ Periodo
			*/
			if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
				if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1'])) {
					$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			else if (isset($_REQUEST['fecha'])) {
				$condiciones[] = 'fecha = \'' . $_REQUEST['fecha'] . '\'';
			}
			else {
				$condiciones[] = 'fecha BETWEEN \'' . date('01/m/Y', mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))) . '\' AND \'' . date('d/m/Y') . '\'';
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha,
					cajaam
						AS
							am,
					clientesam
						AS
							clientes_am,
					erroramcaja
						AS
							error_am,
					erroramclientes
						AS
							error_clientes_am,
					cajapm
						AS
							pm,
					clientespm
						AS
							clientes_pm,
					errorpmcaja
						AS
							error_pm,
					errorpmclientes
						AS
							error_clientes_pm,
					pastelam
						AS
							pastel_am,
					clientespastelam
						AS
							clientes_am_pastel,
					pastelpm
						AS
							pastel_pm,
					clientespastelpm
						AS
							clientes_pm_pastel,
					cambioayer
						AS
							cambio_ayer,
					barredura,
					pasteles,
					bases,
					esquilmos,
					botes,
					pastillaje,
					costales,
					efectivo
				FROM
						efectivos_tmp
							efe
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/HojaDiaria.tpl');
			$tpl->prepare();
			
			if ($result) {
				$datos = $result[0];
				
				foreach ($result as $datos) {
					$tpl->newBlock('hoja');
					
					$tpl->assign('num_cia', $datos['num_cia']);
					$tpl->assign('nombre_cia', $datos['nombre_cia']);
					
					$pieces = explode('/', $datos['fecha']);
					
					$tpl->assign('fecha', $semana[date('w', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]))] . ' ' . $pieces[0] . ' DE ' . $meses[date('n', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]))] . ' DE ' . $pieces[2]);
					
					/*
					@ Cortes de caja
					*/
					$tpl->assign('am', $datos['am'] != 0 ? number_format($datos['am'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('clientes_am', $datos['clientes_am'] != 0 ? number_format($datos['clientes_am'], 0, '.', ',') : '&nbsp;');
					$tpl->assign('error_am', $datos['error_am'] != 0 ? number_format($datos['error_am']) : '&nbsp;');
					$tpl->assign('error_clientes_am', $datos['error_clientes_am'] != 0 ? number_format($datos['error_clientes_am']) : '&nbsp;');
					$tpl->assign('pm', $datos['pm'] != 0 ? number_format($datos['pm'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('clientes_pm', $datos['clientes_pm'] != 0 ? number_format($datos['clientes_pm'], 0, '.', ',') : '&nbsp;');
					$tpl->assign('error_pm', $datos['error_pm'] != 0 ? number_format($datos['error_pm']) : '&nbsp;');
					$tpl->assign('error_clientes_pm', $datos['error_clientes_pm'] != 0 ? number_format($datos['error_clientes_pm']) : '&nbsp;');
					$tpl->assign('pastel_am', $datos['pastel_am'] != 0 ? number_format($datos['pastel_am'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('clientes_am_pastel', $datos['clientes_am_pastel'] != 0 ? number_format($datos['clientes_am_pastel'], 0, '.', ',') : '&nbsp;');
					$tpl->assign('pastel_pm', $datos['pastel_pm'] != 0 ? number_format($datos['pastel_pm'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('clientes_pm_pastel', $datos['clientes_pm_pastel'] != 0 ? number_format($datos['clientes_pm_pastel'], 0, '.', ',') : '&nbsp;');
					
					$total_caja = $datos['am'] + $datos['pm'] + $datos['pastel_am'] + $datos['pastel_pm'] - $datos['error_am'] - $datos['error_pm'];
					$total_clientes = $datos['clientes_am'] + $datos['clientes_pm'] + $datos['clientes_am_pastel'] + $datos['clientes_pm_pastel'] - $datos['error_clientes_am'] - $datos['error_clientes_pm'];
					
					$tpl->assign('total_caja', number_format($total_caja, 2, '.', ','));
					$tpl->assign('total_clientes', number_format($total_clientes, 0, '.', ','));
					
					/*
					@ Prueba de efectivo
					*/
					$tpl->assign('cambio_ayer', $datos['cambio_ayer'] != 0 ? number_format($datos['cambio_ayer'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('barredura', $datos['barredura'] != 0 ? number_format($datos['barredura'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('pasteles', $datos['pasteles'] != 0 ? number_format($datos['pasteles'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('bases', $datos['bases'] != 0 ? number_format($datos['bases'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('esquilmos', $datos['esquilmos'] != 0 ? number_format($datos['esquilmos'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('botes', $datos['botes'] != 0 ? number_format($datos['botes'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('pastillaje', $datos['pastillaje'] != 0 ? number_format($datos['pastillaje'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('costales', $datos['costales'] != 0 ? number_format($datos['costales'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('efectivo', $datos['efectivo'] != 0 ? number_format($datos['efectivo'], 2, '.', ',') : '&nbsp;');
					
					$suma1 = $datos['barredura'] + $datos['pasteles'] + $datos['bases'] + $datos['esquilmos'] + $datos['botes'] + $datos['pastillaje'] + $datos['costales'];
					
					/*
					@ Consecutivo de corte
					*/
					$sql = '
						SELECT
							CASE
								WHEN tipo = 1 THEN
									\'pan\'
								WHEN tipo = 2 THEN
									\'pastel\'
							END
								AS
									tipo,
							ticket
						FROM
							corte_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
						ORDER BY
							tipo,
							ticket
					';
					$cortes = $db->query($sql);
					
					if ($cortes) {
						foreach ($cortes as $i => $corte) {
							$tpl->assign('corte_' . $corte['tipo'] . '_' . $i, $corte['ticket']);
						}
					}
					
					/*
					@ Producción
					*/
					$sql = '
						SELECT
							codturno
								AS
									turno,
							raya_ganada
								AS
									raya,
							total_produccion
								AS
									produccion
						FROM
							total_produccion_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha_total = \'' . $datos['fecha'] . '\'
					';
					$produccion = $db->query($sql);
					
					$produccion_total = 0;
					$raya_total = 0;
					$produccion_turno = array(
						1 => 0,
						2 => 0,
						3 => 0,
						4 => 0,
						8 => 0,
						9 => 0
					);
					
					if ($produccion) {
						foreach ($produccion as $p) {
							$tpl->assign('produccion_' . $p['turno'], $p['produccion'] != 0 ? number_format($p['produccion'], 2, '.', ',') : '&nbsp;');
							$tpl->assign('raya_' . $p['turno'], $p['raya'] != 0 ? number_format($p['raya'], 2, '.', ',') : '&nbsp;');
							$produccion_total += $p['produccion'];
							$raya_total += $p['raya'];
						}
						$tpl->assign('produccion_total', $produccion_total != 0 ? number_format($produccion_total, 2, '.', ',') : '&nbsp;');
						$tpl->assign('raya_total', $raya_total != 0 ? number_format($raya_total, 2, '.', ',') : '');
						
						foreach ($produccion as $p)
							$produccion_turno[$p['turno']] = $p['produccion'];
					}
					
					// Rendimientos
					$sql = '
						SELECT
							cod_turno
								AS
									turno,
							SUM(cantidad)
								AS
									bultos
						FROM
							mov_inv_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
							AND
								codmp = 1
							AND
								tipomov = \'TRUE\'
							AND
								cod_turno < 10
						GROUP BY
							turno
						ORDER BY
							turno
					';
					$consumos = $db->query($sql);
					if ($consumos) {
						foreach ($consumos as $c) {
							$tpl->assign('bultos_' . $c['turno'], number_format($c['bultos'], 2, '.', ','));
							$tpl->assign('rendimiento_' . $c['turno'], number_format($produccion_turno[$c['turno']] / $c['bultos'], 2, '.', ','));
						}
					}
					
					/*
					@ Agua
					*/
					$sql = '
						SELECT
							toma1,
							horatoma1,
							toma2,
							horatoma2,
							toma3,
							horatoma3
						FROM
							mediciones_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$agua = $db->query($sql);
					if ($agua) {
						$tpl->assign('med1', $agua[0]['toma1'] != 0 ? number_format($agua[0]['toma1'], 2, '.', ',') : '');
						$tpl->assign('hora1', $agua[0]['toma1'] != 0 ? substr($agua[0]['horatoma1'], 0, 5) : '');
						$tpl->assign('med2', $agua[0]['toma2'] != 0 ? number_format($agua[0]['toma2'], 2, '.', ',') : '');
						$tpl->assign('hora2', $agua[0]['toma2'] != 0 ? substr($agua[0]['horatoma2'], 0, 5) : '');
						$tpl->assign('med3', $agua[0]['toma3'] != 0 ? number_format($agua[0]['toma3'], 2, '.', ',') : '');
						$tpl->assign('hora3', $agua[0]['toma3'] != 0 ? substr($agua[0]['horatoma3'], 0, 5) : '');
					}
					
					/*
					@ Camionetas
					*/
					$sql = '
						SELECT
							medunidad1,
							dinunidad1,
							medunidad2,
							dinunidad2,
							medunidad3,
							dinunidad3,
							medunidad4,
							dinunidad4,
							medunidad5,
							dinunidad5
						FROM
							camionetas_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$cam = $db->query($sql);
					if ($cam) {
						$tpl->assign('km1', $cam[0]['medunidad1'] != 0 ? number_format($cam[0]['medunidad1'], 2, '.', ',') : '');
						$tpl->assign('din1', $cam[0]['dinunidad1'] != 0 ? number_format($cam[0]['dinunidad1'], 2, '.', ',') : '');
						$tpl->assign('km2', $cam[0]['medunidad2'] != 0 ? number_format($cam[0]['medunidad2'], 2, '.', ',') : '');
						$tpl->assign('din2', $cam[0]['dinunidad2'] != 0 ? number_format($cam[0]['dinunidad2'], 2, '.', ',') : '');
						$tpl->assign('km3', $cam[0]['medunidad3'] != 0 ? number_format($cam[0]['medunidad3'], 2, '.', ',') : '');
						$tpl->assign('din3', $cam[0]['dinunidad3'] != 0 ? number_format($cam[0]['dinunidad3'], 2, '.', ',') : '');
						$tpl->assign('km4', $cam[0]['medunidad4'] != 0 ? number_format($cam[0]['medunidad4'], 2, '.', ',') : '');
						$tpl->assign('din4', $cam[0]['dinunidad4'] != 0 ? number_format($cam[0]['dinunidad4'], 2, '.', ',') : '');
						$tpl->assign('km5', $cam[0]['medunidad5'] != 0 ? number_format($cam[0]['medunidad5'], 2, '.', ',') : '');
						$tpl->assign('din5', $cam[0]['dinunidad5'] != 0 ? number_format($cam[0]['dinunidad5'], 2, '.', ',') : '');
					}
					
					/*
					@ Avio recibido
					*/
					$sql = '
						SELECT
							proveedor,
							factura,
							observaciones
						FROM
							facturas_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\' LIMIT 12
					';
					$facturas = $db->query($sql);
					
					if ($facturas) {
						foreach ($facturas as $f) {
							$tpl->newBlock('factura');
							$tpl->assign('prov', $f['proveedor']);
							$tpl->assign('fac', $f['factura']);
							$tpl->assign('obs', trim($f['observaciones']) != '' ? strtoupper(trim($f['observaciones'])) : '&nbsp;');
						}
					}
					
					/*
					@ Desglose de gastos
					*/
					$sql = '
						SELECT
							concepto,
							importe
						FROM
							gastos_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
						
						UNION
						
						SELECT
							\'PRESTAMO \' || nombre
								AS
									concepto,
							importe
						FROM
							prestamos_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
							AND
								tipo_mov = \'FALSE\'
					';
					$gastos = $db->query($sql);
					
					$pan_comprado = 0;
					$total_gastos = 0;
					if ($gastos)
						foreach ($gastos as $gasto) {
							$tpl->newBlock('gasto');
							$tpl->assign('concepto', trim($gasto['concepto']) != '' ? trim($gasto['concepto']) : '&nbsp;');
							$tpl->assign('importe', $gasto['importe'] != 0 ? number_format($gasto['importe'], 2, '.', ',') : '&nbsp;');
							
							$total_gastos += $gasto['importe'];
							
							if (trim($gasto['concepto']) == 'PAN COMPRADO') {
								$pan_comprado += $gasto['importe'];
							}
						}
					$tpl->gotoBlock('hoja');
					$tpl->assign('total_gastos', $total_gastos != 0 ? number_format($total_gastos, 2, '.', ',') : '&nbsp;');
					
					// Pastillaje
					$sql = '
						SELECT
							existenciainicial
								AS
									existencia_inicial,
							venta
								AS
									venta_pastillaje,
							compra
								AS
									compra_pastillaje,
							existenciafinal
								AS
									existencia_final
						FROM
							pastillaje_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$pastillaje = $db->query($sql);
					
					if ($pastillaje) {
						$tpl->assign('existencia_inicial', $pastillaje[0]['existencia_inicial'] != 0 ? number_format($pastillaje[0]['existencia_inicial'], 2, '.', ',') : '');
						$tpl->assign('venta_pastillaje', $pastillaje[0]['venta_pastillaje'] != 0 ? number_format($pastillaje[0]['venta_pastillaje'], 2, '.', ',') : '');
						$tpl->assign('compra_pastillaje', $pastillaje[0]['compra_pastillaje'] != 0 ? number_format($pastillaje[0]['compra_pastillaje'], 2, '.', ',') : '');
						$tpl->assign('existencia_final', $pastillaje[0]['existencia_final'] != 0 ? number_format($pastillaje[0]['existencia_final'], 2, '.', ',') : '');
					}
					
					/*
					@ Prueba de pan
					*/
					$venta_puerta = $total_caja + $datos['pasteles'];
					
					$sql = '
						SELECT
							SUM(pan_p_venta)
								AS
									reparto,
							SUM(abono)
								AS
									abonos
						FROM
							mov_exp_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$total_expendios = $db->query($sql);
					
					$reparto = $total_expendios[0]['reparto'] != 0 ? $total_expendios[0]['reparto'] : 0;
					$abonos_exp = $total_expendios[0]['abonos'] != 0 ? $total_expendios[0]['abonos'] : 0;
					
					$sql = '
						SELECT
							descuentos,
							pan_contado,
							sobranteayer
						FROM
							prueba_pan_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$prueba_pan = $db->query($sql);
					
					if ($prueba_pan[0]['sobranteayer'] != 0) {
						$sobrante_ayer = $prueba_pan[0]['sobranteayer'];
					}
					else {
						$sql = '
							SELECT
								fecha,
								sobranteayer,
								COALESCE	
								(
									(
										SELECT
											SUM(total_produccion)
										FROM
											total_produccion_tmp
										WHERE
												num_cia = pp.num_cia
											AND
												fecha_total = pp.fecha
									),
									0
								)
									AS
										produccion,
								COALESCE
								(
									(
										SELECT
											SUM(importe)
										FROM
											gastos_tmp
										WHERE
												num_cia = pp.num_cia
											AND
												fecha = pp.fecha
											AND
												TRIM(concepto) = \'PAN COMPRADO\'
									),
									0
								)
									AS
										pan_comprado,
								COALESCE
								(
									(
										SELECT
											porcentaje
										FROM
											porcentaje_pan_comprado
										WHERE
											num_cia = pp.num_cia
									),
									0
								)
									AS
										porcentaje_pan_comprado,
								(
									SELECT
										COALESCE(cajaam, 0) + COALESCE(cajapm, 0) + COALESCE(pastelam, 0) + COALESCE(pastelpm, 0) - COALESCE(erroramcaja, 0) - COALESCE(errorpmcaja)
									FROM
										efectivos_tmp
									WHERE
											num_cia = pp.num_cia
										AND
											fecha = pp.fecha
								)
									AS
										total_caja,
								(
									SELECT
										COALESCE(pasteles, 0)
									FROM
										efectivos_tmp
									WHERE
											num_cia = pp.num_cia
										AND
											fecha = pp.fecha
								)
									AS
										pasteles,
								(
									SELECT
										SUM(pan_p_venta)
									FROM
										mov_exp_tmp
									WHERE
											num_cia = pp.num_cia
										AND
											fecha = pp.fecha
								)
									AS
										reparto,
								descuentos,
								pan_contado
							FROM
								prueba_pan_tmp pp
							WHERE
									num_cia = ' . $datos['num_cia'] . '
								AND
									fecha
										BETWEEN
												(
													SELECT
														fecha
													FROM
														prueba_pan_tmp
													WHERE
															num_cia = ' . $datos['num_cia'] . '
														AND
															fecha < \'' . $datos['fecha'] . '\'
														AND
															pan_contado <> 0
													ORDER BY
														fecha DESC
													LIMIT
														1
												)
											AND
												\'' . $datos['fecha'] . '\'::date - interval \'1 day\'
							ORDER BY
								fecha
						';
						$prueba_pan_ant = $db->query($sql);
						
						if ($prueba_pan_ant) {
							foreach ($prueba_pan_ant as $i => $ppa) {
								if ($i == 0) {
									$sobrante_ayer = $ppa['pan_contado'];
								}
								else {
									if ($ppa['sobranteayer'] == 0 && $ppa['pan_contado'] != 0) {
										$sobrante_ayer = $ppa['pan_contado'];
									}
									else {
										$sobrante_ayer = $sobrante_ayer + $ppa['produccion'] + $ppa['pan_comprado'] * 100 / (100 - $ppa['porcentaje_pan_comprado']) - $ppa['total_caja'] - $ppa['pasteles'] - $ppa['reparto'] - $ppa['descuentos'];
									}
								}
							}
						}
						else {
							$sobrante_ayer = 0;
						}
					}
					
					$sql = '
						SELECT
							porcentaje
						FROM
							porcentaje_pan_comprado
						WHERE
							num_cia = ' . $datos['num_cia'] . '
					';
					$tmp = $db->query($sql);
					$porcentaje_pan_comprado = $tmp ? $tmp[0]['porcentaje'] : 0;
					
					$total_dia = $sobrante_ayer + $produccion_total + $pan_comprado * 100 / (100 - $porcentaje_pan_comprado);
					$sobrante = $total_dia - $total_caja - $datos['pasteles'] - $reparto - $prueba_pan[0]['descuentos'];
					$faltante = $sobrante - $prueba_pan[0]['pan_contado'];
					
					$tpl->assign('sobrante_ayer', $sobrante_ayer != 0 ? number_format($sobrante_ayer, 2, '.', ',') : '&nbsp;');
					$tpl->assign('pan_comprado', $pan_comprado != 0 ? number_format($pan_comprado * 100 / (100 - $porcentaje_pan_comprado), 2, '.', ',') : '&nbsp;');
					$tpl->assign('total_dia', $total_dia != 0 ? number_format($total_dia, 2, '.', ',') : '&nbsp;');
					$tpl->assign('venta_puerta', $venta_puerta != 0 ? number_format($venta_puerta, 2, '.', ',') : '');
					$tpl->assign('reparto', $reparto != 0 ? number_format($reparto, 2, '.', ',') : '');
					$tpl->assign('desc', $prueba_pan[0]['descuentos'] != 0 ? number_format($prueba_pan[0]['descuentos'], 2, '.', ',') : '');
					$tpl->assign('sobrante_manana', $sobrante != 0 ? number_format($sobrante, 2, '.', ',') : '');
					$tpl->assign('pan_contado', $prueba_pan[0]['pan_contado'] != 0 ? number_format($prueba_pan[0]['pan_contado'], 2, '.', ',') : '');
					
					
					
					$tpl->assign('faltante', $faltante != 0 ? number_format($faltante, 2, '.', ',') : '');
					
					/*
					@ Prestamos a plazo
					*/
					$sql = '
						SELECT
							nombre,
							saldo,
							tipo_mov,
							importe
						FROM
							prestamos_tmp
						WHERE
								num_cia = ' . $datos['num_cia'] . '
							AND
								fecha = \'' . $datos['fecha'] . '\'
					';
					$prestamos = $db->query($sql);
					
					$saldo_ant = 0;
					$cargos = 0;
					$abonos = 0;
					$saldo_act = 0;
					if ($prestamos) {
						foreach ($prestamos as $p) {
							$tpl->newBlock('prestamo');
							$tpl->assign('nombre', $p['nombre']);
							$tpl->assign('saldo_ant', $p['saldo'] != 0 ? number_format($p['saldo'], 2, '.', ',') : '&nbsp;');
							
							if ($p['tipo_mov'] != '') {
								$tpl->assign($p['tipo_mov'] == 'f' ? 'cargo' : 'abono', $p['importe'] != 0 ? number_format($p['importe'], 2, '.', ',') : '&nbsp;');
							}
							
							$saldo_emp = $p['saldo'] + ($p['tipo_mov'] == 'f' ? $p['importe'] : -$p['importe']);
							
							$tpl->assign('saldo_act', $saldo_emp != 0 ? number_format($saldo_emp, 2, '.', ',') : '');
							
							$cargos += $p['tipo_mov'] == 'f' ? $p['importe'] : 0;
							$abonos += $p['tipo_mov'] == 't' ? $p['importe'] : 0;
							$saldo_ant += $p['saldo'];
							$saldo_act += $saldo_emp;
						}
						
						$tpl->assign('hoja.saldo_ant', number_format($saldo_ant, 2, '.', ','));
						$tpl->assign('hoja.cargo', number_format($cargos, 2, '.', ','));
						$tpl->assign('hoja.abono_obreros', number_format($abonos, 2, '.', ','));
						$tpl->assign('hoja.saldo_act', number_format($saldo_act, 2, '.', ','));
					}
					
					$suma1 += $abonos + $abonos_exp + $total_caja;
					$tpl->assign('hoja.abonos', number_format($abonos_exp, 2, '.', ','));
					$tpl->assign('hoja.suma_prueba1', number_format($suma1, 2, '.', ','));
					
					$suma2 = $datos['efectivo'] - $cargos + $total_gastos + $raya_total;
					$tpl->assign('hoja.suma_prueba2', number_format($suma2, 2, '.', ','));
					$tpl->assign('hoja.efectivo', number_format($datos['efectivo'] - $cargos, 2, '.', ','));
					
					/*
					@ Expendios
					*/
					$sql = '
						SELECT
							tmp.num_expendio
								AS
									num_tmp,
							num_referencia
								AS
									num_cat,
							cat.num_expendio
								AS
									num_exp,
							nombre_expendio
								AS
									nombre_tmp,
							nombre
								AS
									nombre_cat,
							porc_ganancia
								AS
									por_tmp,
							porciento_ganancia
								AS
									por_cat,
							importe_fijo
								AS
									fijo,
							rezago_anterior,
							pan_p_venta,
							pan_p_expendio,
							abono,
							devolucion,
							rezago
						FROM
								mov_exp_tmp
									tmp
							LEFT JOIN
								catalogo_expendios 
									cat
										ON
											(
													cat.num_cia = tmp.num_cia
												AND
													num_referencia = tmp.num_expendio
											)
						WHERE
								tmp.num_cia = ' . $datos['num_cia'] . '
							AND
								tmp.fecha = \'' . $datos['fecha'] . '\'
						ORDER BY
							num_tmp
					';
					$expendios = $db->query($sql);
					
					if ($expendios) {
						$tpl->newBlock('expendios');
						$tpl->assign('num_cia', $datos['num_cia']);
						$tpl->assign('nombre_cia', $datos['nombre_cia']);
						$tpl->assign('fecha', $semana[date('w', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]))] . ' ' . $pieces[0] . ' DE ' . $meses[date('n', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]))] . ' DE ' . $pieces[2]);
						
						$pan_venta = 0;
						$pan_exp = 0;
						$abono = 0;
						$devuelto = 0;
						$rezago = 0;
						$rezago_ant = 0;
						foreach ($expendios as $exp) {
							$tpl->newBlock('expendio');
							$tpl->assign('num_exp', $exp['num_cat']);
							$tpl->assign('nombre_exp', $exp['nombre_tmp']);
							
							// Rezago anterior
							$tpl->assign('rezago_ant', $exp['rezago_anterior'] != 0 ? number_format($exp['rezago_anterior'], 2, '.', ',') : '&nbsp;');
							
							// Pan para venta
							$tpl->assign('pan_venta', $exp['pan_p_venta'] != 0 ? number_format($exp['pan_p_venta'], 2, '.', ',') : '&nbsp;');
							
							// Devolución
							$tpl->assign('dev', $exp['devolucion'] != 0 ? number_format($exp['devolucion'], 2, '.', ',') : '&nbsp;');
							
							// Pan para expendio
							$tpl->assign('por', $exp['por_tmp'] != 0 ? '% ' . number_format($exp['por_tmp']) : '&nbsp;');
							$tpl->assign('pan_exp', $exp['pan_p_expendio'] != 0 ? number_format($exp['pan_p_expendio'], 2, '.', ',') : '&nbsp;');
							
							$tpl->assign('abono', $exp['abono'] != 0 ? number_format($exp['abono'], 2, '.', ',') : '&nbsp;');
							$tpl->assign('rezago', $exp['rezago'] != 0 ? number_format($exp['rezago'], 2, '.', ',') : '&nbsp;');
							if ($exp['rezago'] < 0) $tpl->assign('color_rezago', ' bgcolor="#FFFF66"');
							
							$pan_venta += $exp['pan_p_venta'];
							$pan_exp += $exp['pan_p_expendio'];
							$abono += $exp['abono'];
							$devuelto += $exp['devolucion'];
							$rezago_ant += $exp['rezago_anterior'];
							$rezago += $exp['rezago'];
						}
						$tpl->assign('expendios.rezago_ant', number_format($rezago_ant, 2, '.', ','));
						$tpl->assign('expendios.pan_venta', number_format($pan_venta, 2, '.', ','));
						$tpl->assign('expendios.dev', number_format($devuelto, 2, '.', ','));
						$tpl->assign('expendios.pan_exp', number_format($pan_exp, 2, '.', ','));
						$tpl->assign('expendios.abono', number_format($abono, 2, '.', ','));
						$tpl->assign('expendios.rezago', number_format($rezago, 2, '.', ','));
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ConsultaHojaDiaria.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
			catalogo_companias
		WHERE
			num_cia <= 300
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);
	
	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
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
		$tpl->newBlock('admin_ipad');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}
}
else {
	$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	
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
}

$tpl->printToScreen();
?>
