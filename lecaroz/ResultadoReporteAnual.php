<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$anios = array();
			foreach ($_REQUEST['anio'] as $anio) {
				if ($anio > 0) {
					$anios[] = $anio;
				}
			}
			
			$cias = array();
			foreach ($_REQUEST['num_cia'] as $num_cia) {
				if ($num_cia > 0) {
					$cias[] = $num_cia;
				}
			}
			
			if ($_REQUEST['campo'] == 'intereses-impuestos') {
				$conditions = array();
				
				$conditions[] = 'cod_mov IN (11, 12)';
				
				if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
					$conditions[] = 'cuenta = ' . $_REQUEST['cuenta'];
				}
				
				if (count($anios) > 0) {
					$conditions[] = 'EXTRACT(year FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
				
				if (count($cias) > 0) {
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
				
				if ($_REQUEST['admin'] > 0) {
					$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
				}
			}
			else if ($_REQUEST['campo'] == 'ide') {
				$conditions = array();
				
				$conditions[] = 'cod_mov IN (78)';
				
				if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
					$conditions[] = 'cuenta = ' . $_REQUEST['cuenta'];
				}
				
				if (count($anios) > 0) {
					$conditions[] = 'EXTRACT(year FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
				
				if (count($cias) > 0) {
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
				
				if ($_REQUEST['admin'] > 0) {
					$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
				}
			}
			else {
				$conditions_pan = array();
				if (count($anios) > 0) {
					$conditions_pan[] = '
						anio
							IN
								(
									' . implode(', ', $anios) . '
								)
					';
				}
				if (count($cias) > 0) {
					$conditions_pan[] = '
						num_cia
							IN
								(
									' . implode(', ', $cias) . '
								)
					';
				}
				if ($_REQUEST['admin'] > 0) {
					$conditions_pan[] = '
						idadministrador = ' . $_REQUEST['admin'] . '
					';
				}
				
				if ($_REQUEST['campo'] == 'encargados') {
					$campo_pan = '
						(
							SELECT
								nombre_inicio
							FROM
								encargados
							WHERE
									num_cia = balances_pan.num_cia
								AND
									anio = balances_pan.anio
								AND
									mes = balances_pan.mes
							LIMIT
								1
						)
					';
				}
				else if ($_REQUEST['campo'] == 'sueldo_empleados') {
					$campo_pan = '
						(
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos
							WHERE
									num_cia = balances_pan.num_cia
								AND
									fecha BETWEEN (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::date AND (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::date + interval \'1 month\' - interval \'1 day\'
								AND
									codgastos = 1
						)
					';
				}
				else {
					$campo_pan = $_REQUEST['campo'];
				}
				
				$conditions_ros = array();
				if (count($anios) > 0) {
					$conditions_ros[] = '
						anio
							IN
								(
									' . implode(', ', $anios) . '
								)
					';
				}
				if (count($cias) > 0) {
					$conditions_ros[] = '
						num_cia
							IN
								(
									' . implode(', ', $cias) . '
								)
					';
				}
				if ($_REQUEST['admin'] > 0) {
					$conditions_ros[] = '
						idadministrador = ' . $_REQUEST['admin'] . '
					';
				}
				
				if ($_REQUEST['campo'] == 'encargados') {
					$campo_ros = '
						\'\'::varchar
					';
				}
				else if ($_REQUEST['campo'] == 'sueldo_empleados') {
					$campo_ros = '
						(
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos
							WHERE
									num_cia = balances_ros.num_cia
								AND
									fecha BETWEEN (\'01/\' || balances_ros.mes || \'/\' || balances_ros.anio)::date AND (\'01/\' || balances_ros.mes || \'/\' || balances_ros.anio)::date + interval \'1 month\' - interval \'1 day\'
								AND
									codgastos = 1
						)
					';
				}
				else if ($_REQUEST['campo'] == 'pastel_kilos') {
					$campo_ros = '
						0
					';
				}
				else if ($_REQUEST['campo'] == 'mp_pro') {
					$campo_ros = '
						0
					';
				}
				else {
					$campo_ros = $_REQUEST['campo'] == 'produccion_total' ? 0 : $_REQUEST['campo'];
				}
				
				if ($_REQUEST['campo'] != 'encargados') {
					$conditions[] = 'round(valor::numeric, 2) <> 0';
				}
				else {
					$conditions[] = '(TRIM(valor) <> \'\' AND valor IS NOT NULL)';
				}
			}
			
			/*
			@ Por año
			*/
			if ($_REQUEST['tipo'] == 1) {
				if ($_REQUEST['campo'] == 'intereses-impuestos') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(
								CASE
									WHEN cod_mov = 11 THEN
										importe
									WHEN cod_mov = 12 THEN
										-importe
								END
							)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							num_cia,
							mes
					';
				}
				else if ($_REQUEST['campo'] == 'ide') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(importe)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							num_cia,
							mes
					';
				}
				else {
					$sql = '
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre_corto
										AS
											nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_pan . '
										AS
											valor
								FROM
										balances_pan
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
										' . implode(' AND ', $conditions_pan) . '
							)
								pan
						WHERE
							' . implode(' AND ', $conditions) . '
						
						UNION
						
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre_corto
										AS
											nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_ros . '
										AS
											valor
								FROM
										balances_ros
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
										' . implode(' AND ', $conditions_ros) . '
							)
								pollo
						WHERE
							' . implode(' AND ', $conditions) . '
						
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							num_cia,
							mes
					';
				}
				
				$result = $db->query($sql);
				
				$valores = array();
				$anio = NULL;
				$admin = NULL;
				foreach ($result as $r) {
					if ($_REQUEST['admin'] == -1 && $admin != $r['admin']) {
						$admin = $r['admin'];
						$anio = NULL;
					}
					if ($anio != $r['anio']) {
						$anio = $r['anio'];
						$bloque = 1;
						$num_cia = NULL;
						$cont = 0;
					}
					if ($num_cia != $r['num_cia']) {
						if ($num_cia != NULL) {
							$cont++;
						}
						
						$num_cia = $r['num_cia'];
						
						if ($cont == 10) {
							$cont = 0;
							$bloque++;
						}
						
						if ($_REQUEST['admin'] == -1) {
							$valores[$admin][$anio][$bloque][$cont]['num_cia'] = $num_cia;
							$valores[$admin][$anio][$bloque][$cont]['nombre'] = $r['nombre'];
						}
						else {
							$valores[$anio][$bloque][$cont]['num_cia'] = $num_cia;
							$valores[$anio][$bloque][$cont]['nombre'] = $r['nombre'];
						}
					}
					
					if ($_REQUEST['admin'] == -1) {
						$valores[$admin][$anio][$bloque][$cont]['valores'][$r['mes']] = $r['valor'];
					}
					else {
						$valores[$anio][$bloque][$cont]['valores'][$r['mes']] = $r['valor'];
					}
				}
				
				$tpl = new TemplatePower('plantillas/bal/ResultadoReporteAnualListado.tpl');
				$tpl->prepare();
				
				if ($_REQUEST['admin'] == -1) {
					$reportes = 0;
					$anio_tmp = NULL;
					foreach ($valores as $admin => $datos) {
						foreach ($datos as $anio => $bloque) {
							if ($anio_tmp != NULL) {
								$tpl->assign('salto', '<br style="page-break-after:always;" />');
								$reportes = 0;
							}
							
							$anio_tmp = $anio;
							
							foreach ($bloque as $cias) {
								$tpl->newBlock('reporte_1');
								$tpl->assign('anio', $anio);
								
								if ($reportes < 1) {
									$tpl->assign('salto', '<br />');
									$reportes++;
								}
								else {
									$tpl->assign('salto', '<br style="page-break-after:always;" />');
									$reportes = 0;
								}
								
								foreach ($cias as $i => $cia) {
									$tpl->assign('num_cia' . $i, $cia['num_cia']);
									$tpl->assign('nombre' . $i, $cia['nombre']);
									
									foreach ($cia['valores'] as $mes => $valor) {
										$tpl->assign('mes' . $mes . '_' . $i, $_REQUEST['campo'] == 'encargados' ? $valor : (round($valor, 3) != 0 ? number_format($valor, $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', ',') : ''));
										
										if ($_REQUEST['campo'] != 'encargados') {
											$tpl->assign('color' . $mes . '_' . $i, round($valor, 2) < 0 ? ' red' : ' blue');
										}
									}
								}
								
								if (!in_array($_REQUEST['campo'], array('encargados', 'mp_pro'))) {
									$tpl->newBlock('totales1');
									
									foreach ($cias['valores'] as $i => $cia) {
										$tpl->assign('total' . $i, number_format(array_sum($cia['valores']), $_REQUEST['campo'] == 'clientes' ? 0 : 2, '.', ','));
									}
								}
							}
						}
					}
				}
				else {
					$reportes = 0;
					$anio_tmp = NULL;
					foreach ($valores as $anio => $bloque) {
						if ($anio_tmp != NULL) {
							$tpl->assign('salto', '<br style="page-break-after:always;" />');
							$reportes = 0;
						}
						
						$anio_tmp = $anio;
						
						foreach ($bloque as $cias) {
							$tpl->newBlock('reporte_1');
							$tpl->assign('anio', $anio);
							
							if ($reportes < 1) {
								$tpl->assign('salto', '<br />');
								$reportes++;
							}
							else {
								$tpl->assign('salto', '<br style="page-break-after:always;" />');
								$reportes = 0;
							}
							
							foreach ($cias as $i => $cia) {
								$tpl->assign('num_cia' . $i, $cia['num_cia']);
								$tpl->assign('nombre' . $i, $cia['nombre']);
								
								foreach ($cia['valores'] as $mes => $valor) {
									$tpl->assign('mes' . $mes . '_' . $i, $_REQUEST['campo'] == 'encargados' ? $valor : (round($valor, 3) != 0 ? number_format($valor, $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', ',') : ''));
									
									if ($_REQUEST['campo'] != 'encargados') {
										$tpl->assign('color' . $mes . '_' . $i, round($valor, 2) < 0 ? ' red' : ' blue');
									}
								}
							}
							
							if (!in_array($_REQUEST['campo'], array('encargados', 'mp_pro'))) {
								$tpl->newBlock('totales1');
								
								foreach ($cias as $i => $cia) {
									$tpl->assign('total' . $i, number_format(array_sum($cia['valores']), $_REQUEST['campo'] == 'clientes' ? 0 : 2, '.', ','));
								}
							}
						}
					}
				}
			}
			/*
			@ Un solo año
			*/
			else if ($_REQUEST['tipo'] == 2) {
				if ($_REQUEST['campo'] == 'intereses-impuestos') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(
								CASE
									WHEN cod_mov = 11 THEN
										importe
									WHEN cod_mov = 12 THEN
										-importe
								END
							)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							anio,
							mes
					';
				}
				else if ($_REQUEST['campo'] == 'ide') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(importe)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							anio,
							mes
					';
				}
				else {
					$sql = '
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_pan . '
										AS
											valor
								FROM
										balances_pan
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
									' . implode(' AND ', $conditions_pan) . '
							)
								pan
						WHERE
							' . implode(' AND ', $conditions) . '
						
						UNION
						
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_ros . '
										AS
											valor
								FROM
										balances_ros
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
									' . implode(' AND ', $conditions_ros) . '
							)
								pollo
						WHERE
							' . implode(' AND ', $conditions) . '
						
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							anio,
							mes
					';
				}
				
				$result = $db->query($sql);
				
				$valores = array();
				$num_cia = NULL;
				$cont_cia = 0;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						if ($num_cia != NULL) {
							$cont_cia++;
						}
						
						$num_cia = $r['num_cia'];
						
						$valores[$cont_cia]['num_cia'] = $num_cia;
						$valores[$cont_cia]['nombre'] = $r['nombre'];
						$valores[$cont_cia]['admin'] = $r['admin'];
						
						$anio = NULL;
						$cont = 0;
					}
					if ($anio != $r['anio']) {
						if ($anio != NULL) {
							$cont++;
						}
						
						$anio = $r['anio'];
						
						$valores[$cont_cia]['valores'][$cont]['anio'] = $anio;
					}
					
					$valores[$cont_cia]['valores'][$cont]['meses'][$r['mes']] = $r['valor'];
				}
				
				$tpl = new TemplatePower('plantillas/bal/ResultadoReporteAnualListado.tpl');
				$tpl->prepare();
				
				$reportes = 0;
				$hojas = 0;
				$admin = NULL;
				foreach ($valores as $datos) {
					if ($_REQUEST['admin'] == -1 && $admin != $datos['admin']) {
						if ($admin != NULL) {
							$tpl->assign('salto', '<br style="page-break-after:always;" />');
							
							if ($reportes > 0 && $reportes < 4) {
								$hojas++;
							}
							
							if ($hojas % 2 != 0) {
								$tpl->assign('blanco', '<br style="page-break-after:always;" />');
							}
							
							$reportes = 0;
							$hojas = 0;
						}
						
						$admin = $datos['admin'];
					}
					$tpl->newBlock('reporte_2');
					
					$tpl->assign('num_cia', $datos['num_cia']);
					$tpl->assign('nombre', $datos['nombre']);
					
					if ($reportes < 3) {
						$tpl->assign('salto', '<br />');
						$reportes++;
					}
					else {
						$tpl->assign('salto', '<br style="page-break-after:always;" />');
						$reportes = 0;
						$hojas++;
					}
					
					foreach ($datos['valores'] as $i => $val) {
						$tpl->assign('anio' . $i, $val['anio']);
						
						foreach ($val['meses'] as $mes => $valor) {
							$tpl->assign('mes' . $mes . '_' . $i, $_REQUEST['campo'] == 'encargados' ? $valor : (round($valor, 3) != 0 ? number_format($valor, $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', ',') : ''));
							
							if ($_REQUEST['campo'] != 'encargados') {
								$tpl->assign('color' . $mes . '_' . $i, round($valor, 2) < 0 ? ' red' : ' blue');
							}
						}
					}
					
					if (!in_array($_REQUEST['campo'], array('encargados', 'mp_pro'))) {
						$tpl->newBlock('totales2');
						
						foreach ($datos['valores'] as $i => $val) {
							$tpl->assign('total' . $i, number_format(array_sum($val['meses']), $_REQUEST['campo'] == 'clientes' ? 0 : 2, '.', ','));
						}
					}
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'exportar':
			$anios = array();
			foreach ($_REQUEST['anio'] as $anio) {
				if ($anio > 0) {
					$anios[] = $anio;
				}
			}
			
			$cias = array();
			foreach ($_REQUEST['num_cia'] as $num_cia) {
				if ($num_cia > 0) {
					$cias[] = $num_cia;
				}
			}
			
			if ($_REQUEST['campo'] == 'intereses-impuestos') {
				$conditions = array();
				
				$conditions[] = 'cod_mov IN (11, 12)';
				
				if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
					$conditions[] = 'cuenta = ' . $_REQUEST['cuenta'];
				}
				
				if (count($anios) > 0) {
					$conditions[] = 'EXTRACT(year FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
				
				if (count($cias) > 0) {
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
				
				if ($_REQUEST['admin'] > 0) {
					$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
				}
			}
			else if ($_REQUEST['campo'] == 'ide') {
				$conditions = array();
				
				$conditions[] = 'cod_mov IN (78)';
				
				if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
					$conditions[] = 'cuenta = ' . $_REQUEST['cuenta'];
				}
				
				if (count($anios) > 0) {
					$conditions[] = 'EXTRACT(year FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
				
				if (count($cias) > 0) {
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
				
				if ($_REQUEST['admin'] > 0) {
					$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
				}
			}
			else {
				$conditions_pan = array();
				if (count($anios) > 0) {
					$conditions_pan[] = '
						anio
							IN
								(
									' . implode(', ', $anios) . '
								)
					';
				}
				if (count($cias) > 0) {
					$conditions_pan[] = '
						num_cia
							IN
								(
									' . implode(', ', $cias) . '
								)
					';
				}
				if ($_REQUEST['admin'] > 0) {
					$conditions_pan[] = '
						idadministrador = ' . $_REQUEST['admin'] . '
					';
				}
				
				if ($_REQUEST['campo'] == 'encargados') {
					$campo_pan = '
						(
							SELECT
								nombre_inicio
							FROM
								encargados
							WHERE
									num_cia = balances_pan.num_cia
								AND
									anio = balances_pan.anio
								AND
									mes = balances_pan.mes
							LIMIT
								1
						)
					';
				}
				else if ($_REQUEST['campo'] == 'sueldo_empleados') {
					$campo_pan = '
						(
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos
							WHERE
									num_cia = balances_pan.num_cia
								AND
									fecha BETWEEN (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::date AND (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::date + interval \'1 month\' - interval \'1 day\'
								AND
									codgastos = 1
						)
					';
				}
				else {
					$campo_pan = $_REQUEST['campo'];
				}
				
				$conditions_ros = array();
				if (count($anios) > 0) {
					$conditions_ros[] = '
						anio
							IN
								(
									' . implode(', ', $anios) . '
								)
					';
				}
				if (count($cias) > 0) {
					$conditions_ros[] = '
						num_cia
							IN
								(
									' . implode(', ', $cias) . '
								)
					';
				}
				if ($_REQUEST['admin'] > 0) {
					$conditions_ros[] = '
						idadministrador = ' . $_REQUEST['admin'] . '
					';
				}
				
				if ($_REQUEST['campo'] == 'encargados') {
					$campo_ros = '
						\'\'::varchar
					';
				}
				else if ($_REQUEST['campo'] == 'sueldo_empleados') {
					$campo_ros = '
						(
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos
							WHERE
									num_cia = balances_ros.num_cia
								AND
									fecha BETWEEN (\'01/\' || balances_ros.mes || \'/\' || balances_ros.anio)::date AND (\'01/\' || balances_ros.mes || \'/\' || balances_ros.anio)::date + interval \'1 month\' - interval \'1 day\'
								AND
									codgastos = 1
						)
					';
				}
				else if ($_REQUEST['campo'] == 'pastel_kilos') {
					$campo_ros = '
						0
					';
				}
				else if ($_REQUEST['campo'] == 'mp_pro') {
					$campo_ros = '
						0
					';
				}
				else {
					$campo_ros = $_REQUEST['campo'] == 'produccion_total' ? 0 : $_REQUEST['campo'];
				}
				
				if ($_REQUEST['campo'] != 'encargados') {
					$conditions[] = 'round(valor::numeric, 2) <> 0';
				}
				else {
					$conditions[] = '(TRIM(valor) <> \'\' AND valor IS NOT NULL)';
				}
			}
			
			/*
			@ Por año
			*/
			if ($_REQUEST['tipo'] == 1) {
				if ($_REQUEST['campo'] == 'intereses-impuestos') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(
								CASE
									WHEN cod_mov = 11 THEN
										importe
									WHEN cod_mov = 12 THEN
										-importe
								END
							)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							mes,
							num_cia
					';
				}
				else if ($_REQUEST['campo'] == 'ide') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(importe)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							mes,
							num_cia
					';
				}
				else {
					$sql = '
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre_corto
										AS
											nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_pan . '
										AS
											valor
								FROM
										balances_pan
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
										' . implode(' AND ', $conditions_pan) . '
							)
								pan
						WHERE
							' . implode(' AND ', $conditions) . '
						
						UNION
						
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre_corto
										AS
											nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_ros . '
										AS
											valor
								FROM
										balances_ros
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
										' . implode(' AND ', $conditions_ros) . '
							)
								pollo
						WHERE
							' . implode(' AND ', $conditions) . '
						
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							anio,
							mes,
							num_cia
					';
				}
				
				$result = $db->query($sql);
				
				$cias = array();
				$nombres_cias = array();
				$totales = array();
				foreach ($result as $r) {
					if (!in_array($r['num_cia'], $cias)) {
						$cias[] = $r['num_cia'];
						$nombres_cias[] = $r['num_cia'] . ' ' . $r['nombre'];
						$totales[] = 0;
					}
				}
				
				// Meses
				$meses = array(
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
				
				foreach ($result as $r) {
					$datos[$r['anio']][$r['mes']][$r['num_cia']] = $r['valor'];
				}
				
				$data = '';
				foreach ($datos as $anio => $reg) {
					$data .= '"RESULTADO DE REPORTE ANUAL ' . $anio . '"' . "\r\n";
					$data .= '"","' . implode('","', $nombres_cias) . '"' . "\r\n";
					
					foreach ($meses as $mes => $nombre_mes) {
						$data .= '"' . $nombre_mes . '",';
						
						$valores_mes = array();
						foreach ($cias as $i => $cia) {
							$valores_mes[] = isset($reg[$mes][$cia]) ? $_REQUEST['campo'] == 'encargados' ? $reg[$mes][$cia] : (round($reg[$mes][$cia], 3) != 0 ? number_format($reg[$mes][$cia], $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', '') : '') : NULL;
							
							$totales[$i] += isset($reg[$mes][$cia]) ? $_REQUEST['campo'] == 'encargados' ? $reg[$mes][$cia] : (round($reg[$mes][$cia], 3) != 0 ? number_format($reg[$mes][$cia], $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', '') : '') : 0;
						}
						
						$data .= '"' . implode('","', $valores_mes) . '"' . "\r\n";
					}
					
					if (!in_array($_REQUEST['campo'], array('encargados', 'mp_pro'))) {
						$data .= '"Totales","' . implode('","', $totales) . '"' . "\r\n";
						
						foreach ($totales as &$value) {
							$value = 0;
						}
					}
					
					$data .= "\r\n";
				}
			}
			/*
			@ Un solo año
			*/
			else if ($_REQUEST['tipo'] == 2) {
				if ($_REQUEST['campo'] == 'intereses-impuestos') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(
								CASE
									WHEN cod_mov = 11 THEN
										importe
									WHEN cod_mov = 12 THEN
										-importe
								END
							)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							mes,
							anio
					';
				}
				else if ($_REQUEST['campo'] == 'ide') {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							idadministrador
								AS
									admin,
							EXTRACT(year FROM fecha)
								AS
									anio,
							EXTRACT(month FROM fecha)
								AS
									mes,
							SUM(importe)
								AS
									valor
						FROM
								estado_cuenta ec
							LEFT JOIN
								catalogo_companias cc
									USING
										(num_cia)
						WHERE
							' . implode(' AND ', $conditions) . '
						GROUP BY
							admin,
							num_cia,
							nombre_corto,
							anio,
							mes
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							mes,
							anio
					';
				}
				else {
					$sql = '
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_pan . '
										AS
											valor
								FROM
										balances_pan
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
									' . implode(' AND ', $conditions_pan) . '
							)
								pan
						WHERE
							' . implode(' AND ', $conditions) . '
						
						UNION
						
						SELECT
							*
						FROM
							(
								SELECT
									num_cia,
									nombre,
									idadministrador
										AS
											admin,
									anio,
									mes,
									' . $campo_ros . '
										AS
											valor
								FROM
										balances_ros
									LEFT JOIN
										historico
											USING
												(
													num_cia,
													anio,
													mes
												)
									LEFT JOIN
										catalogo_companias
											USING
												(
													num_cia
												)
								WHERE
									' . implode(' AND ', $conditions_ros) . '
							)
								pollo
						WHERE
							' . implode(' AND ', $conditions) . '
						
						ORDER BY
					';
					
					if ($_REQUEST['admin'] == -1) {
						$sql .= '
							admin,
						';
					}
					
					$sql .= '
							num_cia,
							mes,
							anio
					';
				}
				
				$result = $db->query($sql);
				
				$anios = array();
				$totales = array();
				foreach ($result as $r) {
					if (!in_array($r['anio'], $anios)) {
						$anios[] = $r['anio'];
						$totales[] = 0;
					}
				}
				
				// Meses
				$meses = array(
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
				
				foreach ($result as $r) {
					$datos[$r['num_cia']][$r['mes']][$r['anio']] = $r['valor'];
					$datos[$r['num_cia']]['nombre'] = $r['nombre'];
				}
				
				$data = '';
				foreach ($datos as $num_cia => $reg) {
					$data .= '"' . $num_cia . ' ' . $reg['nombre'] . '"' . "\r\n";
					$data .= '"","' . implode('","', $anios) . '"' . "\r\n";
					
					foreach ($meses as $mes => $nombre_mes) {
						$data .= '"' . $nombre_mes . '",';
						
						$valores_mes = array();
						foreach ($anios as $i => $anio) {
							$valores_mes[] = isset($reg[$mes][$anio]) ? $_REQUEST['campo'] == 'encargados' ? $reg[$mes][$anio] : (round($reg[$mes][$anio], 3) != 0 ? number_format($reg[$mes][$anio], $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', '') : '') : NULL;
							
							$totales[$i] += isset($reg[$mes][$anio]) ? $_REQUEST['campo'] == 'encargados' ? $reg[$mes][$anio] : (round($reg[$mes][$anio], 3) != 0 ? number_format($reg[$mes][$anio], $_REQUEST['campo'] == 'clientes' ? 0 : ($_REQUEST['campo'] == 'mp_pro' ? 3 : 2), '.', '') : '') : 0;
						}
						
						$data .= '"' . implode('","', $valores_mes) . '"' . "\r\n";
					}
					
					if (!in_array($_REQUEST['campo'], array('encargados', 'mp_pro'))) {
						$data .= '"Totales","' . implode('","', $totales) . '"' . "\r\n";
						
						foreach ($totales as &$value) {
							$value = 0;
						}
					}
					
					$data .= "\r\n";
				}
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ResultadoReporteMensual.csv"');
			
			echo $data;
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ResultadoReporteAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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