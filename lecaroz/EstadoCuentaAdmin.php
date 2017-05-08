<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/EstadoCuentaAdminInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))));
			$tpl->assign('fecha2', date('d/m/Y'));
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'obtener_codigos':
			$condiciones = array();
			
			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'banco = ' . $_REQUEST['banco'];
			}
			
			if (!isset($_REQUEST['depositos']) && !isset($_REQUEST['tipo_mov'])) {
				$condiciones[] = 'tipo_mov = TRUE';
			}
			
			if (!isset($_REQUEST['cargos']) && !isset($_REQUEST['tipo_mov'])) {
				$condiciones[] = 'tipo_mov = FALSE';
			}
			
			if (isset($_REQUEST['tipo_mov'])) {
				$condiciones[] = 'tipo_mov = ' . $_REQUEST['tipo_mov'];
			}
			
			$sql = '
				SELECT
					*
				FROM
					(
						SELECT
							1
								AS banco,
							tipo_mov,
							cod_mov
								AS value,
							cod_mov || \' \' || descripcion
								AS text
						FROM
							catalogo_mov_bancos
						
						UNION
						
						SELECT
							2
								AS banco,
							tipo_mov,
							cod_mov
								AS value,
							cod_mov || \' \' || descripcion
								AS text
						FROM
							catalogo_mov_santander
						
						GROUP BY
							banco,
							tipo_mov,
							value,
							text
					)
						AS result
				
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					banco,
					value
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$data = array();
				
				$banco = NULL;
				foreach ($query as $row) {
					if ($banco != $row['banco'] && !isset($_REQUEST['no_banco'])) {
						$banco = $row['banco'];
						
						$data[] = array(
							'text'     => $banco == 1 ? 'BANORTE' : 'SANTANDER',
							'disabled' => 'disabled',
							'class'    => 'bold underline logo_banco logo_banco_' . $banco,
							'styles' => array(
								'margin' => '4px 0'
							)
						);
					}
					$data[] = $row;
				}
				
				echo json_encode($data);
			}
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
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
			
			if (isset($_REQUEST['acreditados']) && trim($_REQUEST['acreditados']) != '') {
				$acre = array();
				
				$pieces = explode(',', $_REQUEST['acreditados']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$acre[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$acre[] = $piece;
					}
				}
				
				if (count($acre) > 0) {
					$condiciones[] = 'ec.num_cia_sec IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha >= \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			if ((isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '')
				|| (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != '')) {
				if ((isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '')
					&& (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != '')) {
					$condiciones[] = 'ec.fecha_con BETWEEN \'' . $_REQUEST['conciliado1'] . '\' AND \'' . $_REQUEST['conciliado2'] . '\'';
				} else if (isset($_REQUEST['conciliado1']) && $_REQUEST['conciliado1'] != '') {
					$condiciones[] = 'ec.fecha_con >= \'' . $_REQUEST['conciliado1'] . '\'';
				} else if (isset($_REQUEST['conciliado2']) && $_REQUEST['conciliado2'] != '') {
					$condiciones[] = 'ec.fecha_con = \'' . $_REQUEST['conciliado2'] . '\'';
				}
			}
			
			if (isset($_REQUEST['comprobantes']) && trim($_REQUEST['comprobantes']) != '') {
				$comprobantes = array();
				
				$pieces = explode(',', $_REQUEST['comprobantes']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$comprobantes[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$comprobantes[] = $piece;
					}
				}
				
				if (count($comprobantes) > 0) {
					$condiciones[] = 'ec.comprobante IN (' . implode(', ', $comprobantes) . ')';
				}
			}
			
			if (!isset($_REQUEST['depositos'])) {
				$condiciones[] = 'ec.tipo_mov = TRUE';
			}
			
			if (!isset($_REQUEST['cargos'])) {
				$condiciones[] = 'ec.tipo_mov = FALSE';
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'ec.fecha_con IS NOT NULL';
			}
			
			if (!isset($_REQUEST['conciliados'])) {
				$condiciones[] = 'ec.fecha_con IS NULL';
			}
			
			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();
				
				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}
				
				if (count($pros) > 0) {
					$condiciones[] = 'c.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}
			
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
			
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();
				
				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}
				
				if (count($gastos) > 0) {
					$condiciones[] = 'c.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}
			
			if (isset($_REQUEST['importes']) && trim($_REQUEST['importes']) != '') {
				$importes = array();
				$rangos = array();
				
				$pieces = explode(',', $_REQUEST['importes']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$rangos[] =  'ec.importe BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					}
					else {
						$importes[] = $piece;
					}
				}
				
				$filtros = array();
				
				if ($importes) {
					$filtros[] = 'ec.importe IN (' . implode(', ', $importes) . ')';
				}
				
				if ($rangos) {
					$filtros[] = implode(' OR ', $rangos);
				}
				
				if ($filtros) {
					$condiciones[] = '(' . implode(' OR ', $filtros) . ')';
				}
			}
			
			if (isset($_REQUEST['codigos']) && count($_REQUEST['codigos']) > 0) {
				$condiciones[] = 'ec.cod_mov IN (' . implode(', ', $_REQUEST['codigos']) . ')';
			}
			
			if (isset($_REQUEST['concepto']) && $_REQUEST['concepto'] != '') {
				$condiciones[] = 'ec.concepto LIKE \'%' . $_REQUEST['concepto'] . '%\'';
			}
			
			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ec.cuenta
						AS banco,
					ec.fecha,
					ec.fecha_con
						AS conciliado,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					c.codgastos || \' \' || (
						SELECT
							descripcion
						FROM
							catalogo_gastos
						WHERE
							codgastos = c.codgastos
					)
						AS gasto,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo,
					ec.cod_mov
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$tpl = new TemplatePower('plantillas/ban/EstadoCuentaAdminConsulta.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				
				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];
						
						$tpl->newBlock('cia');
						
						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('banco', $row['banco'] == 1 ? 'Banorte' : 'Santander');
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('conciliado', $row['conciliado'] != '' ? $row['conciliado'] : '&nbsp;');
					$tpl->assign('deposito', $row['deposito'] != 0 ? number_format($row['deposito'], 2) : '&nbsp;');
					$tpl->assign('cargo', $row['cargo'] != 0 ? number_format($row['cargo'], 2) : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span class="' . ($row['cod_mov'] == 41 ? 'purple' : ($row['cod_mov'] == 41 ? 'orange' : 'green')) . '" info="' . $row['gasto'] . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('beneficiario', $row['beneficiario'] != '' ? utf8_encode($row['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', $row['concepto'] != '' ? utf8_encode($row['concepto']) : '&nbsp;');
					$tpl->assign('codigo', utf8_encode($row['codigo']));
					
					$tpl->assign('baja_disabled', $row['conciliado'] != '' || $row['folio'] > 0 ? '_gray' : '');
				}
				
				echo $tpl->getOutputContent();
			}
			
			break;
			
		case 'datos_movimiento':
			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					(
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					)
						AS nombre_cia,
					(
						SELECT
							CASE
								WHEN ec.cuenta = 1 THEN
									clabe_cuenta
								WHEN ec.cuenta = 2 THEN
									clabe_cuenta2
							END
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					)
						AS cuenta_cia,
					ec.num_cia_sec,
					(
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia_sec
					)
						AS nombre_cia_sec,
					(
						SELECT
							CASE
								WHEN ec.cuenta = 1 THEN
									clabe_cuenta
								WHEN ec.cuenta = 2 THEN
									clabe_cuenta2
							END
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia_sec
					)
						AS cuenta_cia_sec,
					ec.cuenta
						AS banco,
					ec.fecha,
					ec.fecha_con
						AS conciliado,
					ec.tipo_mov,
					ec.importe,
					ec.folio,
					c.a_nombre
						AS beneficiario,
					c.codgastos
						AS gasto,
					(
						SELECT
							descripcion
						FROM
							catalogo_gastos
						WHERE
							codgastos = c.codgastos
					)
						AS descripcion_gasto,
					ec.concepto,
					ec.cod_mov,
					ec.idarrendatario,
					ec.idreciborenta
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio, fecha)
				WHERE
					ec.id = ' . $_REQUEST['id'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$row = $query[0];
				
				$row['id'] = intval($_REQUEST['id']);
				$row['num_cia'] = intval($row['num_cia']);
				$row['nombre_cia'] = utf8_encode($row['nombre_cia']);
				$row['num_cia_sec'] = intval($row['num_cia_sec']);
				$row['nombre_cia_sec'] = utf8_encode($row['nombre_cia_sec']);
				$row['banco'] = intval($row['banco']);
				$row['tipo_mov'] = $row['tipo_mov'] == 'f' ? FALSE : TRUE;
				$row['importe'] = floatval($row['importe']);
				$row['folio'] = intval($row['folio']);
				$row['beneficiario'] = utf8_encode($row['beneficiario']);
				$row['gasto'] = intval($row['gasto']);
				$row['descripcion_gasto'] = utf8_encode($row['descripcion_gasto']);
				$row['concepto'] = utf8_encode($row['concepto']);
				$row['cod_mov'] = intval($row['cod_mov']);
				$row['idarrendatario'] = intval($row['idarrendatario']);
				$row['idreciborenta'] = intval($row['idreciborenta']);
				$row['codigos'] = array();
				$row['arrendatarios'] = array(
					array(
						'value'    => NULL,
						'text'     => NULL,
						'selected' => TRUE
					)
				);
				$row['recibos_renta'] = array(
					array(
						'value' => NULL,
						'text'  => NULL,
						'selected' => TRUE
					)
				);
				
				$sql = '
					SELECT
						cod_mov
							AS value,
						cod_mov || \' \' || descripcion
							AS text
					FROM
						' . ($row['banco'] == 1 ? 'catalogo_mov_bancos' : 'catalogo_mov_santander') . '
					WHERE
						tipo_mov = ' . ($row['tipo_mov'] == 't' ? 'TRUE' : 'FALSE') . '
					GROUP BY
						value,
						text
					ORDER BY
						value
				';
				
				$query = $db->query($sql);
				
				if ($query) {
					foreach ($query as $i => $r) {
						$row['codigos'][$i] = array(
							'value' => intval($r['value']),
							'text'  => utf8_encode($r['text'])
						);
						
						if ($r['value'] == $row['cod_mov']) {
							$row['codigos'][$i]['selected'] = TRUE;
						}
					}
				}
				
				$sql = '
					SELECT
						idarrendatario
							AS value,
						arrendatario || \' \' || alias_arrendatario || (
							CASE
								WHEN tsbaja IS NOT NULL THEN
									\' [** BAJA **]\'
								ELSE
									\'\'
							END
						)
							AS text,
						CASE
							WHEN tsbaja IS NOT NULL THEN
								TRUE
							ELSE
								FALSE
						END
							AS baja
					FROM
						rentas_arrendatarios arr
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						arrendador = ' . $row['num_cia'] . '
						AND bloque = 2
						AND (
							tsbaja IS NULL
							OR idarrendatario IN (
								SELECT
									idarrendatario
								FROM
									rentas_recibos recibos
									LEFT JOIN rentas_arrendatarios arrendatarios
										USING (idarrendatario)
									LEFT JOIN rentas_arrendadores arrendadores
										USING (idarrendador)
								WHERE
									arrendador = ' . $row['num_cia'] . '
									AND fecha >= \'2012/04/01\'
									AND (
										(
											recibos.tsbaja IS NULL
											AND bloque = 2
											AND idreciborenta NOT IN (
												SELECT
													idreciborenta
												FROM
													estado_cuenta
												WHERE
													num_cia = ' . $row['num_cia'] . '
													AND cod_mov = 2
													AND idreciborenta IS NOT NULL
											)
										)
										OR idreciborenta = ' . $row['idreciborenta'] . '
									)
								GROUP BY
									idarrendatario
							)
						)
					ORDER BY
						nombre_arrendatario
				';
				
				$query = $db->query($sql);
				
				if ($query && ($row['cod_mov'] == 2 || $row['idarrendatario'] > 0 || $row['idreciborenta'] > 0)) {
					foreach ($query as $i => $r) {
						$row['arrendatarios'][$i + 1] = array(
							'value' => intval($r['value']),
							'text'  => utf8_encode($r['text']),
							'class' => $r['baja'] == 't' ? 'underline red' : ''
						);
						
						if ($r['value'] == $row['idarrendatario']) {
							$row['arrendatarios'][$i + 1]['selected'] = TRUE;
							$row['arrendatarios'][$i + 1]['text'] = '** ' . $row['arrendatarios'][$i + 1]['text'];
							$row['arrendatarios'][$i + 1]['class'] .= ' purple';
							
							unset($row['arrendatarios'][0]['selected']);
						}
					}
				}
				
				$sql = '
					SELECT
						idreciborenta,
						EXTRACT(YEAR FROM fecha)
							AS anio,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						fecha,
						idarrendatario,
						arrendatario,
						alias_arrendatario
							AS nombre_arrendatario,
						recibos.total
							AS renta
					FROM
						rentas_recibos recibos
						LEFT JOIN rentas_arrendatarios arrendatarios
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores arrendadores
							USING (idarrendador)
					WHERE
						arrendador = ' . $row['num_cia'] . '
						AND fecha >= \'2012/04/01\'
						AND (
							(
								recibos.tsbaja IS NULL
								AND bloque = 2
								AND idreciborenta NOT IN (
									SELECT
										idreciborenta
									FROM
										estado_cuenta
									WHERE
										num_cia = ' . $row['num_cia'] . '
										AND cod_mov = 2
										AND idreciborenta IS NOT NULL
								)
							)
							OR idreciborenta = ' . $row['idreciborenta'] . '
						)
					ORDER BY
						recibos.total DESC,
						anio,
						mes
				';
				
				$query = $db->query($sql);
				
				if ($query && ($row['cod_mov'] == 2 || $row['idarrendatario'] > 0 || $row['idreciborenta'] > 0)) {
					foreach ($query as $i => $r) {
						$row['recibos_renta'][$i + 1] = array(
							'text'  => '[' . strtoupper($_meses[$r['mes']]) . ' ' . $r['anio'] . '] ' . $r['arrendatario'] . ' ' . utf8_encode($r['nombre_arrendatario']) . ' - ' . number_format($r['renta'], 2),
							'value' => json_encode(array(
								'idreciborenta'       => intval($r['idreciborenta']),
								'idarrendatario'      => intval($r['idarrendatario']),
								'nombre_arrendatario' => utf8_encode($r['nombre_arrendatario']),
								'fecha'               => $r['fecha'],
								'anio'                => intval($r['anio']),
								'mes'                 => strtoupper($_meses[$r['mes']]),
								'renta'               => floatval($r['renta'])
							))
						);
						
						if ($r['idreciborenta'] == $row['idreciborenta']) {
							$row['recibos_renta'][$i + 1]['selected'] = TRUE;
							$row['recibos_renta'][$i + 1]['text'] = '** ' . $row['recibos_renta'][$i + 1]['text'];
							$row['recibos_renta'][$i + 1]['class'] = 'orange';
							
							unset($row['recibos_renta'][0]['selected']);
						}
					}
				}
				
				echo json_encode($row);
			}
			
			break;
		
		case 'do_modificar':
			$renta = isset($_REQUEST['recibo_renta']) && $_REQUEST['recibo_renta'] != '' ? json_decode($_REQUEST['recibo_renta']) : FALSE;
			
			$sql = '';
			
			$sql .= '
				UPDATE
					saldos
				SET
					saldo_libros = saldo_libros + result.importe ' . ($_REQUEST['tipo_mov'] == 't' ? '-' : '+') . ' ' . get_val($_REQUEST['importe']) . '
				FROM (
					SELECT
						num_cia,
						cuenta,
						CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END
							AS importe
					FROM
						estado_cuenta
					WHERE
						id = ' . $_REQUEST['id'] . '
				)
					AS result
				WHERE
					saldos.num_cia = result.num_cia
					AND saldos.cuenta = result.cuenta
			' . ";\n";
			
			if ($_REQUEST['folio'] > 0) {
				$sql .= '
					UPDATE
						movimiento_gastos
					SET
						fecha = \'' . $_REQUEST['fecha'] . '\',
						concepto = \'' . $_REQUEST['concepto'] . '\',
						codgastos = ' . $_REQUEST['gasto'] . '
					WHERE
						(num_cia, fecha, folio) IN (
							SELECT
								num_cia,
								fecha,
								folio
							FROM
								cheques
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
								AND cuenta = ' . $_REQUEST['banco'] . '
								AND folio = ' . $_REQUEST['folio'] . '
						)
				' . ";\n";
				
				$sql .= '
					UPDATE
						cheques
					SET
						fecha = \'' . $_REQUEST['fecha'] . '\',
						concepto = \'' . $_REQUEST['concepto'] . '\',
						codgastos = ' . $_REQUEST['gasto'] . '
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND cuenta = ' . $_REQUEST['banco'] . '
						AND folio = ' . $_REQUEST['folio'] . '
				' . ";\n";
			}
			
			$sql .= '
				UPDATE
					estado_cuenta
				SET
					num_cia = ' . $_REQUEST['num_cia'] . ',
					num_cia_sec = ' . (isset($_REQUEST['num_cia_sec']) && $_REQUEST['num_cia_sec'] > 0 ? $_REQUEST['num_cia_sec'] : 'NULL') . ',
					cuenta = ' . $_REQUEST['banco'] . ',
					fecha = \'' . $_REQUEST['fecha'] . '\',
					importe = ' . get_val($_REQUEST['importe']) . ',
					cod_mov = ' . $_REQUEST['cod_mov'] . ',
					concepto = \'' . $_REQUEST['concepto'] . '\',
					idarrendatario = ' . ($renta ? $renta->idarrendatario : 'NULL') . ',
					idreciborenta = ' . ($renta ? $renta->idreciborenta : 'NULL') . ',
					fecha_renta = ' . ($renta ? '\'' . $renta->fecha . '\'' : 'NULL') . ',
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				UPDATE
					saldos
				SET
					saldo_libros = saldo_libros + result.importe
				FROM (
					SELECT
						num_cia,
						cuenta,
						CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END
							AS importe
					FROM
						estado_cuenta
					WHERE
						id = ' . $_REQUEST['id'] . '
				)
					AS result
				WHERE
					saldos.num_cia = result.num_cia
					AND saldos.cuenta = result.cuenta
			' . ";\n";
			
			$sql .= '
				DELETE FROM
					estado_cuenta
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto,
					' . ($_REQUEST['banco'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
						AS cuenta
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				echo json_encode(array(
					'nombre' => utf8_encode($query[0]['nombre_corto']),
					'cuenta' => $query[0]['cuenta']
				));
			}
			
			break;
		
		case 'obtener_cia_sec':
			$sql = '
				SELECT
					nombre_corto,
					' . ($_REQUEST['banco'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
						AS cuenta
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia_sec'] . '
					AND rfc IN (
						SELECT
							rfc
						FROM
							catalogo_companias
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					)
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				echo json_encode(array(
					'nombre' => utf8_encode($query[0]['nombre_corto']),
					'cuenta' => $query[0]['cuenta']
				));
			}
			
			break;
		
		case 'obtener_gasto':
			$sql = '
				SELECT
					descripcion
				FROM
					catalogo_gastos
				WHERE
					codgastos = ' . $_REQUEST['gasto'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				echo utf8_encode($query[0]['descripcion']);
			}
			
			break;
		
		case 'obtener_rentas':
			$datos = array(
				'arrendatarios' => array(
					array(
						'value' => NULL,
						'text'  => NULL
					)
				),
				'recibos_renta' => array(
					array(
						'value' => NULL,
						'text'  => NULL
					)
				)
			);
			
			$sql = '
				SELECT
					idarrendatario
						AS value,
					arrendatario || \' \' || alias_arrendatario || (
						CASE
							WHEN tsbaja IS NOT NULL THEN
								\' [** BAJA **]\'
							ELSE
								\'\'
						END
					)
						AS text,
					CASE
						WHEN tsbaja IS NOT NULL THEN
							TRUE
						ELSE
							FALSE
					END
						AS baja,
					CASE
						WHEN idarrendatario = (
							SELECT
								idarrendatario
							FROM
								estado_cuenta
							WHERE
								id = ' . $_REQUEST['id'] . '
						) THEN
							TRUE
						ELSE
							FALSE
					END
						AS selected
				FROM
					rentas_arrendatarios arr
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
				WHERE
					arrendador = ' . $_REQUEST['num_cia'] . '
					AND bloque = 2
					AND (
						tsbaja IS NULL
						OR idarrendatario IN (
							SELECT
								idarrendatario
							FROM
								rentas_recibos recibos
								LEFT JOIN rentas_arrendatarios arrendatarios
									USING (idarrendatario)
								LEFT JOIN rentas_arrendadores arrendadores
									USING (idarrendador)
							WHERE
								arrendador = ' . $_REQUEST['num_cia'] . '
								AND fecha >= \'2012/04/01\'
								AND (
									(
										recibos.tsbaja IS NULL
										AND bloque = 2
										AND idreciborenta NOT IN (
											SELECT
												idreciborenta
											FROM
												estado_cuenta
											WHERE
												num_cia = ' . $_REQUEST['num_cia'] . '
												AND cod_mov = 2
												AND idreciborenta IS NOT NULL
										)
									)
									OR idreciborenta = (
										SELECT
											idreciborenta
										FROM
											estado_cuenta
										WHERE
											id = ' . $_REQUEST['id'] . '
									)
								)
							GROUP BY
								idarrendatario
						)
					)
				ORDER BY
					nombre_arrendatario
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				foreach ($query as $i => $r) {
					$datos['arrendatarios'][$i + 1] = array(
						'value' => intval($r['value']),
						'text'  => utf8_encode($r['text']),
						'class' => $r['baja'] == 't' ? 'underline red' : ''
					);
					
					if ($r['selected'] == 't') {
						$datos['arrendatarios'][$i + 1]['selected'] = TRUE;
						$datos['arrendatarios'][$i + 1]['text'] = '** ' . $datos['arrendatarios'][$i + 1]['text'];
						$datos['arrendatarios'][$i + 1]['class'] .= ' purple';
						
						unset($datos['arrendatarios'][0]['selected']);
					}
				}
			}
			
			$sql = '
				SELECT
					idreciborenta,
					EXTRACT(YEAR FROM fecha)
						AS anio,
					EXTRACT(MONTH FROM fecha)
						AS mes,
					fecha,
					idarrendatario,
					arrendatario,
					alias_arrendatario
						AS nombre_arrendatario,
					recibos.total
						AS renta,
					CASE
						WHEN idreciborenta = (
							SELECT
								idreciborenta
							FROM
								estado_cuenta
							WHERE
								id = ' . $_REQUEST['id'] . '
						) THEN
							TRUE
						ELSE
							FALSE
					END
						AS selected
				FROM
					rentas_recibos recibos
					LEFT JOIN rentas_arrendatarios arrendatarios
						USING (idarrendatario)
					LEFT JOIN rentas_arrendadores arrendadores
						USING (idarrendador)
				WHERE
					arrendador = ' . $_REQUEST['num_cia'] . '
					AND fecha >= \'2012/04/01\'
					AND (
						(
							recibos.tsbaja IS NULL
							AND bloque = 2
							AND idreciborenta NOT IN (
								SELECT
									idreciborenta
								FROM
									estado_cuenta
								WHERE
									num_cia = ' . $_REQUEST['num_cia'] . '
									AND cod_mov = 2
									AND idreciborenta IS NOT NULL
							)
						)
						OR idreciborenta = (
							SELECT
								idreciborenta
							FROM
								estado_cuenta
							WHERE
								id = ' . $_REQUEST['id'] . '
						)
					)
				ORDER BY
					recibos.total DESC,
					anio,
					mes
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				foreach ($query as $i => $r) {
					$datos['recibos_renta'][$i + 1] = array(
						'text'  => '[' . strtoupper($_meses[$r['mes']]) . ' ' . $r['anio'] . '] ' . $r['arrendatario'] . ' ' . utf8_encode($r['nombre_arrendatario']) . ' - ' . number_format($r['renta'], 2),
						'value' => json_encode(array(
							'idreciborenta'       => intval($r['idreciborenta']),
							'idarrendatario'      => intval($r['idarrendatario']),
							'nombre_arrendatario' => utf8_encode($r['nombre_arrendatario']),
							'fecha'               => $r['fecha'],
							'anio'                => intval($r['anio']),
							'mes'                 => strtoupper($_meses[$r['mes']]),
							'renta'               => floatval($r['renta'])
						))
					);
					
					if ($r['selected'] == 't') {
						$datos['recibos_renta'][$i + 1]['selected'] = TRUE;
						$datos['recibos_renta'][$i + 1]['text'] = '** ' . $datos['recibos_renta'][$i + 1]['text'];
						$datos['recibos_renta'][$i + 1]['class'] = 'orange';
						
						unset($datos['recibos_renta'][0]['selected']);
					}
				}
			}
			
			echo json_encode($datos);
			
			break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/EstadoCuentaAdmin.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
