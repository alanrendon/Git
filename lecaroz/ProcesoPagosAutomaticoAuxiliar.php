<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'datos':
			$tpl = new TemplatePower('plantillas/ban/ProcesoPagosAutomaticoAuxiliarBloques.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('datos');
			
			$tpl->assign('fecha', date('d/m/Y'));
			
			echo $tpl->getOutputContent();
		break;
		
		case 'buscar':
			$tpl = new TemplatePower('plantillas/ban/ProcesoPagosAutomaticoAuxiliarBloques.tpl');
			$tpl->prepare();
			
			$cias = '';
			$pros = '';
			$pros_sin_pago = '';
			$cias_no_pago = '';
			$pagos_obligados = '';
			
			// Intervalo de compañías
			if (isset($_REQUEST['cias_intervalo'])) {
				$cias = '
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
				';
				
				$pieces = explode(',', $_REQUEST['cias_intervalo']);
				$list = array();
				$opt = array();
				foreach ($pieces as $piece)
					if (count($exp = explode('-', $piece)) > 1)
						$opt[] = 'num_cia BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					else
						$list[] = $piece;
				
				if (count($list) > 0)
					$opt[] = 'num_cia IN (' . implode(', ', $list) . ')';
				
				$cias .= implode(' OR ', $opt);
			}
			
			// Intervalo de proveedores
			if (isset($_REQUEST['pros_intervalo'])) {
				$pros = '
					SELECT
						num_proveedor
					FROM
						catalogo_proveedores
					WHERE
				';
				
				$pieces = explode(',', $_REQUEST['pros_intervalo']);
				$list = array();
				$opt = array();
				foreach ($pieces as $piece)
					if (count($exp = explode('-', $piece)) > 1)
						$opt[] = 'num_proveedor BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					else
						$list[] = $piece;
				
				if (count($list) > 0)
					$opt[] = 'num_proveedor IN (' . implode(', ', $list) . ')';
				
				$pros .= implode(' OR ', $opt);
			}
			
			// Intervalo de proveedores sin pago
			if (isset($_REQUEST['pros_sin_pago'])) {
				$pros_sin_pago = '
					SELECT
						num_proveedor
					FROM
						catalogo_proveedores
					WHERE
				';
				
				$pieces = explode(',', $_REQUEST['pros_sin_pago']);
				$list = array();
				$opt = array();
				foreach ($pieces as $piece)
					if (count($exp = explode('-', $piece)) > 1)
						$opt[] = 'num_proveedor BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					else
						$list[] = $piece;
				
				if (count($list) > 0)
					$opt[] = 'num_proveedor IN (' . implode(', ', $list) . ')';
				
				$pros_sin_pago .= implode(' OR ', $opt);
			}
			
			// Intervalo de compañías que no pagaran
			if (isset($_REQUEST['cias_no_pago'])) {
				$cias_no_pago = '
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
				';
				
				$pieces = explode(',', $_REQUEST['cias_no_pago']);
				$list = array();
				$opt = array();
				foreach ($pieces as $piece)
					if (count($exp = explode('-', $piece)) > 1)
						$opt[] = 'num_cia BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					else
						$list[] = $piece;
				
				if (count($list) > 0)
					$opt[] = 'num_cia IN (' . implode(', ', $list) . ')';
				
				$cias_no_pago .= implode(' OR ', $opt);
			}
			
			// Intervalo de proveedores con pago obligatorio
			if (isset($_REQUEST['pagos_obligados'])) {
				$pagos_obligados = '
					SELECT
						num_proveedor
					FROM
						catalogo_proveedores
					WHERE
				';
				
				$pieces = explode(',', $_REQUEST['pagos_obligados']);
				$list = array();
				$opt = array();
				foreach ($pieces as $piece)
					if (count($exp = explode('-', $piece)) > 1)
						$opt[] = 'num_proveedor BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					else
						$list[] = $piece;
				
				if (count($list) > 0)
					$opt[] = 'num_proveedor IN (' . implode(', ', $list) . ')';
				
				$pagos_obligados .= implode(' OR ', $opt);
			}
			
			// Query para obtener los saldos por compañía
			$sql = '
				SELECT
					*,
					CASE
						WHEN (
									SELECT
										max(fecha)
									FROM
										estado_cuenta
									WHERE
											num_cia = saldos.num_cia
										AND
											cuenta = saldos.cuenta
										AND
											cod_mov
												IN
													(
														1,
														16,
														44,
														99
													)
								) < (5 || \'-\' || date_part(\'month\', now()) || \'-\' || date_part(\'year\', now()))::date THEN
							(
								SELECT
									round(avg(importe::numeric), 2)
								FROM
									(
										SELECT
											fecha,
											sum(importe)
												AS
													importe
										FROM
											estado_cuenta
										WHERE
												num_cia = saldos.num_cia
											AND
												cuenta = saldos.cuenta
											AND
												cod_mov
													IN
														(
															1,
															16,
															44,
															99
														)
											AND
												date_part(\'year\', fecha) = date_part(\'year\', now() - interval \'1 month\')
											AND
												date_part(\'month\', fecha) = date_part(\'month\', now() - interval \'1 month\')
										GROUP BY
											fecha
									)
										promedios
							)
						ELSE
							(
								SELECT
									round(avg(importe::numeric), 2)
								FROM
									(
										SELECT
											fecha,
											sum(importe)
												AS
													importe
										FROM
											estado_cuenta
										WHERE
												num_cia = saldos.num_cia
											AND
												cuenta = saldos.cuenta
											AND
												cod_mov
													IN
														(
															1,
															16,
															44,
															99
														)
											AND
												date_part(\'year\', fecha) = date_part(\'year\', now())
											AND
												date_part(\'month\', fecha) = date_part(\'month\', now())
										GROUP BY
											fecha
									)
										promedios
							) END
								AS
									promedio,
					(
						SELECT
							sum(ec.importe)
						FROM
								estado_cuenta
									ec
							LEFT JOIN
								cheques
									c
										USING
											(
												num_cia,
												folio,
												cuenta
											)
						WHERE
								acuenta = \'TRUE\'
							AND
								fecha_con IS NULL
							AND
								num_cia = saldos.num_cia
							AND
								cuenta = saldos.cuenta
					)
						AS
							acuenta
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS
									nombre,
							cuenta,
							CASE
								WHEN cuenta = 1 THEN
									clabe_cuenta
								WHEN cuenta = 2 THEN
									clabe_cuenta2
								ELSE
									NULL
							END
								AS
									num_cuenta,
							round(saldo_libros::numeric, 2)
								AS
									saldo
						FROM
								catalogo_companias
									cc
							LEFT JOIN
								saldos
									s
										USING
											(
												num_cia
											)
			';
			
			$conditions = array();
			
			$conditions[] = 'num_cia < 900';
			if ($_REQUEST['cuenta'] > 0)
				$conditions[] = 'cuenta = ' . $_REQUEST['cuenta'];
			if ($cias != '')
				$conditions[] = 'num_cia IN (' . $cias . ')';
			
			$sql .= '
						WHERE
							' . implode(' AND ', $conditions) . '
					)
						saldos
				WHERE
					length(trim(num_cuenta)) = 11
				ORDER BY
					num_cia,
					
			';
			if ($_REQUEST['cuenta'] == -1)
				$sql .= '
					cuenta
						ASC
				';
			else if ($_REQUEST['cuenta'] == -2)
				$sql .= '
					cuenta
						DESC
				';
			else if ($_REQUEST['cuenta'] == -3)
				$sql .= '
					promedio
						DESC
				';
			else
				$sql .= '
					cuenta
				';
			
			// Obtener saldos por compañía
			$result = $db->query($sql);
			
			if (!$result) {
				echo -1;
				die;
			}
			
			// [30-Dic-2009] Días de depósito
			$dias_deposito = isset($_REQUEST['dias_deposito']) ? $_REQUEST['dias_deposito'] : 0;
			
			// Si la selecciñon de saldos es normal (-3) organizar saldos por compañía del mayor al menor
			if ($_REQUEST['cuenta'] == -3) {
				foreach ($result as $key => $value) {
					$num_cia[$key] = $value['num_cia'];
					$saldo[$key] = $value['saldo'] + ($_REQUEST['cuenta'] < 0 ? ($_REQUEST['prox'] == $value['cuenta'] ? $value['promedio'] : 0) : $r['promedio']) * $dias_deposito;
				}
				
				array_multisort($num_cia, SORT_ASC, $saldo, SORT_DESC, $result);
			}
			
			// Organizar saldos
			// [11-Ene-2010] Los cheques a cuenta se sumaran al saldo
			$saldos = array();
			foreach ($result as $r)
				if ($r['saldo'] + ($_REQUEST['cuenta'] < 0 ? ($_REQUEST['prox'] == $r['cuenta'] ? $r['promedio'] : 0) : $r['promedio']) * $dias_deposito > 0)
					$saldos[$r['num_cia']][] = array(
						'nombre' => $r['nombre'],
						'banco' => $r['cuenta'],
						'num_cuenta' => $r['num_cuenta'],
						'saldo_actual' => $r['saldo'],
						'promedio' => $r['promedio'],
						'saldo_pago' => $r['saldo'] + $r['acuenta'] + ($_REQUEST['cuenta'] < 0 ? ($_REQUEST['prox'] == $r['cuenta'] ? $r['promedio'] : 0) : $r['promedio']) * $dias_deposito
					);
			
			$sql = '';
			// Query para pagos obligados
			if ($pagos_obligados != '') {
				$sql .= '
					SELECT
						id,
						num_cia,
						cc.nombre_corto
							AS
								nombre_cia,
						pp.num_proveedor
							AS
								num_pro,
						cp.nombre
							AS
								nombre_pro,
						num_fact,
						prioridad,
						fecha,
						descripcion
							AS
								concepto,
						total
							AS
								importe,
						1
							AS
								bloque,
						NULL
							AS
								banco,
						trans
					FROM
							pasivo_proveedores
								pp
						LEFT JOIN
							catalogo_proveedores
								cp
									USING
										(
											num_proveedor
										)
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
					WHERE
				';
				
				$conditions = array();
				
				$conditions[] = '
							num_cia IN (' . implode(', ', array_keys($saldos)) . ')
				';
				$conditions[] = '
							fecha <= \'' . $_REQUEST['fecha_corte'] . '\'
				';
				$conditions[] = '
							total > 0
				';
				$conditions[] = '
							(
								pp.num_proveedor,
								num_fact
							)
								NOT IN
									(
										SELECT
											num_proveedor,
											num_fact
										FROM
											facturas_pendientes
										WHERE
											fecha_aclaracion IS NULL
									)';
				$conditions[] = '
							(
									copia_fac = \'TRUE\'
								OR
									verfac = \'FALSE\'
							)
				';
				if ($cias_no_pago != '')
					$conditions[] = 'pp.num_cia NOT IN (' . $cias_no_pago . ')';
				if ($pagos_obligados != '')
					$conditions[] = 'pp.num_proveedor IN (' . $pagos_obligados . ')';

				$conditions[] = "COALESCE((SELECT TRUE FROM entrada_mp WHERE num_proveedor = pp.num_proveedor AND num_fact = pp.num_fact LIMIT 1), FALSE) = TRUE";
				
				$sql .= implode(' AND ', $conditions);
				
				$sql .= '
					UNION
				';
			}
			
			// Query para obtener facturas
			$sql .= '
				SELECT
					id,
					num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					pp.num_proveedor
						AS
							num_pro,
					cp.nombre
						AS
							nombre_pro,
					num_fact,
					prioridad,
					fecha,
					descripcion
						AS
							concepto,
					total
						AS
							importe,
					2
						AS
							bloque,
					NULL
							AS
								banco,
					trans
				FROM
						pasivo_proveedores
							pp
					LEFT JOIN
						catalogo_proveedores
							cp
								USING
									(
										num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
				WHERE
			';
			
			$conditions = array();
			
			$conditions[] = '
						num_cia IN (' . implode(', ', array_keys($saldos)) . ')
			';
			if ($pros != '')
				$conditions[] = 'pp.num_proveedor IN (' . $pros . ')';
			$conditions[] = '
						fecha <= \'' . $_REQUEST['fecha_corte'] . '\'
			';
			$conditions[] = '
						total > 0
			';
			$conditions[] = '
						(
							pp.num_proveedor,
							num_fact
						)
							NOT IN
								(
									SELECT
										num_proveedor,
										num_fact
									FROM
										facturas_pendientes
									WHERE
										fecha_aclaracion IS NULL
								)';
			$conditions[] = '
						(
								copia_fac = \'TRUE\'
							OR
								verfac = \'FALSE\'
						)
			';
			if ($_REQUEST['tipo_pago'] > 0) {
				switch ($_REQUEST['tipo_pago']) {
					case 1:
					break;
					
					case 2:
						$conditions[] = 'trans = \'FALSE\'';
					break;
					
					case 3:
						$conditions[] = 'trans = \'TRUE\'';
					break;
				}
			}
			if ($pros_sin_pago != '')
				$conditions[] = 'pp.num_proveedor NOT IN (' . $pros_sin_pago . ')';
			if ($cias_no_pago != '')
				$conditions[] = 'pp.num_cia NOT IN (' . $cias_no_pago . ')';
			if ($pagos_obligados != '')
				$conditions[] = 'pp.num_proveedor NOT IN (' . $pagos_obligados . ')';

			$conditions[] = "COALESCE((SELECT TRUE FROM entrada_mp WHERE num_proveedor = pp.num_proveedor AND num_fact = pp.num_fact LIMIT 1), FALSE) = TRUE";
			
			$sql .= implode(' AND ', $conditions);
			
			$sql .= '
				ORDER BY
					num_cia,
					bloque,
			';
			$sql .= $_REQUEST['criterio'] == 1 ? '
					prioridad
						DESC,
			' : '';
			$sql .= '
					fecha,
					importe
						DESC
			';
			$facturas = $db->query($sql);
			
			if (!$facturas) {
				echo -2;
				die;
			}
			
			$tpl->newBlock('facturas');
			$tpl->assign('fecha_cheque', $_REQUEST['fecha_cheque']);
			
			$num_cia = NULL;
			$total_pago = 0;
			$num_facts = 0;
			foreach ($facturas as $f) {
				if ($num_cia != $f['num_cia']) {
					$num_cia = $f['num_cia'];
					
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre', utf8_encode($f['nombre_cia']));
					
					$total_saldo = 0;
					foreach ($saldos[$num_cia] as $saldo) {
						$tpl->newBlock('saldo');
						$tpl->assign('banco', $saldo['banco'] == 1 ? 'Banorte' : 'Santander');
						$tpl->assign('saldo', number_format($saldo['saldo_pago'], 2, '.', ','));
						$tpl->assign('color_banco', $saldo['banco'] == 1 ? '95B3D7' : 'D99795');
						$total_saldo += $saldo['saldo_pago'];
					}
					$tpl->assign('cia.saldo', number_format($total_saldo, 2, '.', ','));
					
					$total = 0;
					
					$ok = TRUE;
				}
				if ($f['bloque'] == 1) {
					$banco = NULL;
					foreach ($saldos[$num_cia] as $i => $saldo)
						if (($saldo['saldo_pago'] - $f['importe']) >= 0) {
							$saldos[$num_cia][$i]['saldo_pago'] = $saldo['saldo_pago'] - $f['importe'];
							$banco = $saldo['banco'];
							break;
						}
					
					if ($banco == NULL) {
						$index_saldo = rand(0, count($saldos[$num_cia]) - 1);
						$saldos[$num_cia][$index_saldo]['saldo_pago'] = $saldos[$num_cia][$index_saldo]['saldo_pago'] - $f['importe'];
						$banco = $saldos[$num_cia][$index_saldo]['banco'];
					}
					
					$tpl->newBlock('row');
					$tpl->assign('id', $f['bloque'] . '|' . $f['id'] . '|' . $banco . '|' . ($_REQUEST['tipo_pago'] == 1 ? 'f' : $f['trans']) . '|' . $f['importe']);
					$tpl->assign('num_cia', $f['num_cia']);
					$tpl->assign('flag_color', 'red');
					$tpl->assign('color_banco', $banco == 1 ? 'C5D9F1' : 'F2DDDC');
					$tpl->assign('num_pro', $f['num_pro']);
					$tpl->assign('nombre', utf8_encode($f['nombre_pro']));
					$tpl->assign('fecha', $f['fecha']);
					$tpl->assign('num_fact', $f['num_fact']);
					$tpl->assign('concepto', utf8_encode($f['concepto']));
					$tpl->assign('importe', number_format($f['importe'], 2, '.', ','));
					
					$total += $f['importe'];
					$total_pago += $f['importe'];
					$num_facts++;
				}
				else if ($f['bloque'] == 2 && $ok) {
					$banco_ok = FALSE;
					$banco = NULL;
					
					foreach ($saldos[$num_cia] as $i => $saldo)
						if (($saldo['saldo_pago'] - $f['importe']) >= 0) {
							$banco_ok = TRUE;
							$saldos[$num_cia][$i]['saldo_pago'] = $saldo['saldo_pago'] - $f['importe'];
							$banco = $saldo['banco'];
							break;
						}
					
					if ($banco_ok) {
						$tpl->newBlock('row');
						$tpl->assign('id', $f['bloque'] . '|' . $f['id'] . '|' . $banco . '|' . ($_REQUEST['tipo_pago'] == 1 ? 'f' : $f['trans']) . '|' . $f['importe']);
						$tpl->assign('num_cia', $f['num_cia']);
						$tpl->assign('flag_color', 'blue');
						$tpl->assign('color_banco', $banco == 1 ? 'C5D9F1' : 'F2DDDC');
						$tpl->assign('num_pro', $f['num_pro']);
						$tpl->assign('nombre', utf8_encode($f['nombre_pro']));
						$tpl->assign('fecha', $f['fecha']);
						$tpl->assign('num_fact', $f['num_fact']);
						$tpl->assign('concepto', utf8_encode($f['concepto']));
						$tpl->assign('importe', number_format($f['importe'], 2, '.', ','));
						
						$total += $f['importe'];
						$total_pago += $f['importe'];
						$num_facts++;
					}
					else {
						$ok = FALSE;
						
						continue;
					}
				}
				
				$tpl->assign('cia.total', number_format($total, 2, '.', ','));
			}
			$tpl->assign('facturas.total', number_format($total_pago, 2, '.', ','));
			$tpl->assign('facturas.num_facts', number_format($num_facts, 0, '.', ','));
			
			echo $tpl->getOutputContent();
			
		break;
		
		case 'generar':
			// [20-Jul-2013] Conexión a la base de datos de facturas
			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');
			// $dbf = new DBclass('pgsql://carlos:D4n13l4*@127.0.0.1:5432/ob_lecaroz', 'autocommit=yes');

			$bloques = array();
			$ids = array();
			foreach ($_REQUEST['id'] as $id) {
				$pieces = explode('|', $id);
				
				$ids[$pieces[1]] = array(
					'cuenta' => $pieces[2],
					'tipo' => $pieces[3]
				);
				
				$bloques[$pieces[0]][] = $pieces[1];
			}
			
			$sql = '
				SELECT
					id,
					num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					num_fact,
					total
						AS
							importe,
					pp.descripcion,
					fecha,
					pp.num_proveedor
						AS
							num_pro,
					cp.nombre
						AS
							nombre_pro,
					codgastos,
					cg.descripcion
						AS
							gasto,
					cp.facturas_por_pago,
					cp.cuenta,
					cp.clabe,
					cp.idbanco,
					CASE
			';
			foreach ($bloques as $b => $i)
				$sql .= '
						WHEN id IN (' . implode(', ', $i) . ') THEN
							' . $b . '
				';
			$sql .= '
					END
						AS
							bloque
				FROM
						pasivo_proveedores
							pp
					LEFT JOIN
						catalogo_proveedores
							cp
								USING
									(
										num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
					LEFT JOIN
						catalogo_gastos
							cg
								USING
									(
										codgastos
									)
				WHERE
					id
						IN
							(
								' . implode(', ', array_keys($ids)) . '
							)
			';
			$facturas = $db->query($sql);
			
			// Anexar datos adicionales a los datos de facturas
			foreach ($facturas as $key => $row) {
				$facturas[$key]['cuenta'] = $ids[$row['id']]['cuenta'];
				$facturas[$key]['tipo'] = $ids[$row['id']]['tipo'];
			}
			$total_para_checar = 0;
			// Obtener una lista de las columnas
			foreach ($facturas as $key => $fac) {
				$bloque[$key] = $fac['bloque'];
				$cuenta[$key] = $fac['cuenta'];
				$num_cia[$key] = $fac['num_cia'];
				$num_pro[$key] = $fac['num_pro'];
				$codgastos[$key] = $fac['codgastos'];
				$importe[$key] = $fac['importe'];$total_para_checar += $fac['importe'];
			}
			
			array_multisort($bloque, SORT_ASC, $cuenta, SORT_ASC, $num_cia, SORT_ASC, $num_pro, SORT_ASC, $codgastos, SORT_ASC, $importe, SORT_DESC, $facturas);
			
			$max_cheques = 10000;
			$max_facs = 10;
			$importe_min = 100;
			
			$folios = array();
			
			$num_cheques = 0;
			$num_facs = 0;
			
			$num_cia = NULL;
			$cuenta = NULL;
			$num_pro = NULL;
			$codgastos = NULL;
			$sql = '';

			// [20-Jul-2013] Variable para almacenar los querys de inserción en OpenBravo
			$sql_ob = '';
			
			$ids = array();
			$fecha = $_REQUEST['fecha_cheque'];
			$total_facs = 0;
			$total_pagado = 0;
			
			$tpl = new TemplatePower('plantillas/ban/ProcesoPagosAutomaticoAuxiliarBloques.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('pagado');
			$color = FALSE;
			
			foreach ($facturas as $fac) {
				if ($num_cia != $fac['num_cia'] || $cuenta != $fac['cuenta'] || $num_pro != $fac['num_pro'] || $codgastos != $fac['codgastos'] || $num_facs == $max_facs) {
					if ($num_cia != NULL && $num_pro != NULL && $codgastos != NULL && count($ids) > 0 && count($ids) <= $max_facs) {
						if (($importe_cheque >= $importe_min || $tipo == 't') && $num_cheques <= $max_cheques) {
							$tpl->newBlock('pago');
							$tpl->assign('color', $color ? 'on' : 'off');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', utf8_encode($nombre_cia));
							$tpl->assign('banco', $cuenta == 1 ? 'Banorte' : 'Santander');
							$tpl->assign('folio', $folios[$num_cia][$cuenta]);
							$tpl->assign('tipo', $tipo == 'f' ? 'CH' : 'TR');
							$tpl->assign('num_pro', $num_pro);
							$tpl->assign('nombre_pro', utf8_encode($nombre_pro));
							$tpl->assign('concepto', count($ids) == 1 ? $descripcion : $gasto);
							$tpl->assign('facturas', implode(' ', $facs));
							$tpl->assign('importe', number_format($importe_cheque, 2, '.', ','));
							$color = !$color;
							
							$total_facs += count($ids);
							$total_pagado += $importe_cheque;
							
							$sql .= '
								INSERT INTO
									cheques
										(
											num_cia,
											cuenta,
											folio,
											cod_mov,
											fecha,
											a_nombre,
											num_proveedor,
											codgastos,
											concepto,
											facturas,
											importe,
											imp,
											proceso,
											poliza,
											archivo,
											site,
											iduser
										)
								VALUES
									(
										' . $num_cia . ',
										' . $cuenta . ',
										' . $folios[$num_cia][$cuenta] . ',
										' . ($tipo == 't' ? 41 : 5) . ',
										\'' . $fecha . '\',
										\'' . $nombre_pro . '\',
										' . $num_pro . ',
										' . $codgastos . ',
										\'' . (count($ids) == 1 ? $descripcion : $gasto) . '\',
										\'' . implode(' ', $facs) . '\',
										' . $importe_cheque . ',
										\'FALSE\',
										\'TRUE\',
										\'' . ($tipo == 't' ? 'TRUE' : 'FALSE') . '\',
										\'' . ($tipo == 't' ? 'FALSE' : 'TRUE') . '\',
										\'TRUE\',
										' . $_SESSION['iduser'] . '
									)
							' . ";\n";
							
							$sql .= '
								INSERT INTO
									estado_cuenta
										(
											num_cia,
											fecha,
											tipo_mov,
											cod_mov,
											cuenta,
											folio,
											concepto,
											importe,
											iduser
										)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										\'TRUE\',
										' . ($tipo == 't' ? 41 : '5') . ',
										' . $cuenta . ',
										' . $folios[$num_cia][$cuenta] . ',
										\'' . implode(' ', $facs) . '\',
										' . $importe_cheque . ',
										' . $_SESSION['iduser'] . '
									)
							' . ";\n";
							
							$sql .= '
								INSERT INTO
									folios_cheque
										(
											num_cia,
											cuenta,
											folio,
											fecha,
											reservado,
											utilizado
										)
								VALUES
									(
										' . $num_cia . ',
										' . $cuenta . ',
										' . $folios[$num_cia][$cuenta] . ',
										\'' . $fecha . '\',
										\'FALSE\',
										\'TRUE\'
									)
								
							' . ";\n";
							
							$sql .= '
								INSERT INTO
									movimiento_gastos
										(
											num_cia,
											fecha,
											codgastos,
											cuenta,
											folio,
											concepto,
											importe,
											captura
										)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										' . $codgastos . ',
										' . $cuenta . ',
										' . $folios[$num_cia][$cuenta] . ',
										\'' . (count($ids) == 1 ? $descripcion : $gasto) . '\',
										' . $importe_cheque . ',
										\'TRUE\'
									)
							' . ";\n";
							
							if ($tipo == 't') {
								$sql .= '
									INSERT INTO
										transferencias_electronicas
											(
												num_cia,
												cuenta,
												folio,
												num_proveedor,
												importe,
												fecha_gen,
												status,
												iduser
											)
									VALUES
										(
											' . $num_cia . ',
											' . $cuenta . ',
											' . $folios[$num_cia][$cuenta] . ',
											' . $num_pro . ',
											' . $importe_cheque . ',
											\'' . $fecha . '\',
											0,
											' . $_SESSION['iduser'] . '
										)
								' . ";\n";
							}
							
							$sql .= '
								INSERT INTO
									facturas_pagadas
										(
											num_cia,
											num_proveedor,
											num_fact,
											total,
											descripcion,
											fecha,
											fecha_cheque,
											folio_cheque,
											codgastos,
											proceso,
											imp,
											cuenta
										)
								SELECT
									num_cia,
									num_proveedor,
									num_fact,
									total,
									descripcion,
									fecha,
									\'' . $fecha . '\',
									' . $folios[$num_cia][$cuenta] . ',
									codgastos,
									\'TRUE\',
									\'FALSE\',
									' . $cuenta . '
								FROM
									pasivo_proveedores
								WHERE
									id
										IN
											(
												' . implode(', ', $ids) . '
											)
							' . ";\n";

							// [20-Jul-2013] Obtener datos para insertar en OpenBravo
							$sql_datos = "
								SELECT
									num_cia,
									num_proveedor
										AS num_pro,
									cp.nombre
										AS nombre_pro,
									cp.rfc
										AS rfc_pro,
									f.num_fact,
									f.fecha,
									cp.calle,
									cp.no_exterior,
									cp.no_interior,
									cp.colonia,
									cp.localidad,
									cp.referencia,
									cp.municipio,
									cp.estado,
									cp.pais,
									cp.codigo_postal,
									cp.email1
										AS email,
									COALESCE(cmp.nombre, f.concepto)
										AS descripcion,
									COALESCE(d.precio, f.importe)
										AS precio,
									COALESCE(d.cantidad, 1)
										AS cantidad,
									COALESCE(d.piva, f.piva)
										AS iva,
									CASE
										WHEN d.codmp IS NOT NULL THEN
											'C'
										WHEN f.codgastos IS NOT NULL THEN
											'G'
									END
										AS tipo,
									COALESCE(d.codmp, f.codgastos)
										AS codigo,
									COALESCE(d.ieps)
										AS ieps
								FROM
									pasivo_proveedores pp
									LEFT JOIN facturas f
										USING (num_cia, num_proveedor, num_fact)
									LEFT JOIN entrada_mp d
										USING (num_cia, num_proveedor, num_fact)
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
									LEFT JOIN catalogo_proveedores cp
										USING (num_proveedor)
								WHERE
									pp.id IN (" . implode(', ', $ids) . ")
								ORDER BY
									COALESCE(d.ieps, 0) DESC,
									pp.id
							";

							$datos_fac = $db->query($sql_datos);

							// [20-Jul-2013] Query's de inserción a OpenBravo

							$ieps = 0;

							foreach ($datos_fac as $k => $df)
							{
								if ($df['ieps'] > 0)
								{
									$ieps += $df['ieps'];
								}

								$sql_ob .= "
									INSERT INTO
										i_invoice (
											i_invoice_id,
											ad_client_id,		-- 1000000
											ad_org_id,			-- num_cia
											createdby,			-- iduser
											updatedby,			-- iduser
											c_currency_id,		-- 130 MX
											bpartnervalue,		-- RFC de cliente
											name,				-- Razon social o nombre de cliente
											postal,				-- Codigo postal
											regionname,			-- Nombre del estado/entidad
											c_region_id,      	-- Id del estado/entidad
											c_country_id,		-- Mexico (247)
											email,				-- Email de cliente
											contactname,		-- Razon social o nombre de cliente
											phone,				-- Telefono de cliente
											doctypename,		-- Tipo de documento + num_cia (F102), (N102)...
											documentno,			-- Folio y serie de la factura
											productvalue,		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
											linedescription,	-- Descripcion
											priceactual,		-- Precio actual
											dateinvoiced,		-- Fecha factura (timestamp)
											qtyinvoiced,		-- Cantidad
											calle,
											noexterior,
											nointerior,
											colonia,
											localidad,
											referencia,
											municipio,
											bpartner_rfc,		-- RFC
											description,		-- Observaciones
											paymentrule,		-- Tipo de pago, 4=No especificado, B=Efectivo, 1=Transferencia bancaria, S=Cheque, K=Tarjeta de credito
											numctapago,			-- Cuenta de cargo
											paymenttermvalue,	-- Condiciones de pago
											aduana,
											pedimento,
											fecha_pedimento,
											facturaefilename,
											isready,
											uomname,				-- Unidad de medida
											issotrx
										) VALUES (
											COALESCE((
												SELECT
													MAX(i_invoice_id)
												FROM
													i_invoice
											), 0) + 1,
											1000000,
											{$df['num_cia']},
											{$_SESSION['iduser']},
											{$_SESSION['iduser']},
											130,
											'{$df['rfc_pro']}',
											'{$df['nombre_pro']}',
											'{$df['codigo_postal']}',
											'{$df['estado']}',
											NULL,
											247,
											'{$df['email']}',
											'{$df['nombre_pro']}',
											NULL,
											'{$df['tipo']}-{$df['num_cia']}',
											'{$df['num_fact']}',
											'{$df['tipo']}{$df['codigo']}',
											'{$df['descripcion']}',
											{$df['precio']},
											'{$df['fecha']}',
											'{$df['cantidad']}',
											'{$df['calle']}',
											'{$df['no_exterior']}',
											'{$df['no_interior']}',
											'{$df['colonia']}',
											'{$df['localidad']}',
											'{$df['referencia']}',
											'{$df['municipio']}',
											'{$df['rfc_pro']}',
											NULL,
											'4',
											NULL,
											0,
											NULL,
											NULL,
											NULL,
											NULL,
											" . ($k == count($datos_fac) - 1 && $ieps == 0 ? "'Y'" : "'N'") . ",
											NULL,
											'N'
										)
								;\n";
							}

							if ($ieps > 0)
							{
								$sql_ob .= "
									INSERT INTO
										i_invoice (
											i_invoice_id,
											ad_client_id,		-- 1000000
											ad_org_id,			-- num_cia
											createdby,			-- iduser
											updatedby,			-- iduser
											c_currency_id,		-- 130 MX
											bpartnervalue,		-- RFC de cliente
											name,				-- Razon social o nombre de cliente
											postal,				-- Codigo postal
											regionname,			-- Nombre del estado/entidad
											c_region_id,      	-- Id del estado/entidad
											c_country_id,		-- Mexico (247)
											email,				-- Email de cliente
											contactname,		-- Razon social o nombre de cliente
											phone,				-- Telefono de cliente
											doctypename,		-- Tipo de documento + num_cia (F102), (N102)...
											documentno,			-- Folio y serie de la factura
											productvalue,		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
											linedescription,	-- Descripcion
											priceactual,		-- Precio actual
											dateinvoiced,		-- Fecha factura (timestamp)
											qtyinvoiced,		-- Cantidad
											calle,
											noexterior,
											nointerior,
											colonia,
											localidad,
											referencia,
											municipio,
											bpartner_rfc,		-- RFC
											description,		-- Observaciones
											paymentrule,		-- Tipo de pago, 4=No especificado, B=Efectivo, 1=Transferencia bancaria, S=Cheque, K=Tarjeta de credito
											numctapago,			-- Cuenta de cargo
											paymenttermvalue,	-- Condiciones de pago
											aduana,
											pedimento,
											fecha_pedimento,
											facturaefilename,
											isready,
											uomname,				-- Unidad de medida
											issotrx
										) VALUES (
											COALESCE((
												SELECT
													MAX(i_invoice_id)
												FROM
													i_invoice
											), 0) + 1,
											1000000,
											{$df['num_cia']},
											{$_SESSION['iduser']},
											{$_SESSION['iduser']},
											130,
											'{$df['rfc_pro']}',
											'{$df['nombre_pro']}',
											'{$df['codigo_postal']}',
											'{$df['estado']}',
											NULL,
											247,
											'{$df['email']}',
											'{$df['nombre_pro']}',
											NULL,
											'{$df['tipo']}-{$df['num_cia']}',
											'{$df['num_fact']}',
											'G1140',
											'I.E.P.S.',
											{$ieps},
											'{$df['fecha']}',
											'1',
											'{$df['calle']}',
											'{$df['no_exterior']}',
											'{$df['no_interior']}',
											'{$df['colonia']}',
											'{$df['localidad']}',
											'{$df['referencia']}',
											'{$df['municipio']}',
											'{$df['rfc_pro']}',
											NULL,
											'4',
											NULL,
											0,
											NULL,
											NULL,
											NULL,
											NULL,
											'Y',
											NULL,
											'N'
										)
								;\n";
							}
							
							$sql .= '
								DELETE FROM
									pasivo_proveedores
								WHERE
									id
										IN
											(
												' . implode(', ', $ids) . '
											)
							' . ";\n";
							
							$folios[$num_cia][$cuenta]++;
							$ids = array();
							$facs = array();
							$importe_cheque = 0;
							$num_cheques++;
						}
					}
					
					if ($num_cia != $fac['num_cia']) {
						$num_cia = $fac['num_cia'];
						$nombre_cia = $fac['nombre_cia'];
						
						$cuenta = NULL;
						$num_pro = NULL;
					}
					if ($cuenta != $fac['cuenta'] && !isset($folios[$num_cia][$cuenta])) {
						$cuenta = $fac['cuenta'];
						
						$num_pro = NULL;
						
						if (!isset($folios[$num_cia][$cuenta])) {
							$sql_folio = '
								SELECT
									folio
								FROM
									folios_cheque
								WHERE
										num_cia = ' . $num_cia . '
									AND
										cuenta = ' . $cuenta . '
								ORDER BY
									folio
										DESC
								LIMIT
									1
							';
							$result = $db->query($sql_folio);
							
							$folios[$num_cia][$cuenta] = $result ? $result[0]['folio'] + 1 : 51;
						}
					}
					if ($num_pro != $fac['num_pro']) {
						$num_pro = $fac['num_pro'];
						$nombre_pro = $fac['nombre_pro'];
						
						$tipo = $fac['tipo'];
						
						/*
						@ [22-Ene-2013] Ahora la cantidad de facturas por pago esta definido en el catálogo de proveedores
						*/
						$max_facs = /*in_array($num_pro, array(13, 482)) ? 20 : 10*/$fac['facturas_por_pago'];
					}
					if ($codgastos != $fac['codgastos']) {
						$codgastos = $fac['codgastos'];
					}
					
					$bloque = NULL;
					$num_facs = 0;
					$importe_cheque = 0;
					$descripcion = '';
					$gasto = '';
				}
				
				$ids[] = $fac['id'];
				$facs[] = str_pad($fac['num_fact'], 7, '0', STR_PAD_LEFT);
				$importe_cheque += $fac['importe'];
				$descripcion = $fac['descripcion'];
				$gasto = $fac['gasto'];
				$num_facs++;
				$bloque = $fac['bloque'];
			}
			
			if ($num_cia != NULL && $num_pro != NULL && $codgastos != NULL && count($ids) > 0 && count($ids) <= $max_facs) {
				if (($importe_cheque >= $importe_min/* || $tipo == 't'*/) && $num_cheques <= $max_cheques) {
					$tpl->newBlock('pago');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($nombre_cia));
					$tpl->assign('banco', $cuenta == 1 ? 'Banorte' : 'Santander');
					$tpl->assign('folio', $folios[$num_cia][$cuenta]);
					$tpl->assign('tipo', $tipo == 'f' ? 'CH' : 'TR');
					$tpl->assign('num_pro', $num_pro);
					$tpl->assign('nombre_pro', utf8_encode($nombre_pro));
					$tpl->assign('concepto', count($ids) == 1 ? $descripcion : $gasto);
					$tpl->assign('facturas', implode(' ', $facs));
					$tpl->assign('importe', number_format($importe_cheque, 2, '.', ','));
					$color = !$color;
					
					$total_facs += count($ids);
					$total_pagado += $importe_cheque;
					
					$sql .= '
						INSERT INTO
							cheques
								(
									num_cia,
									cuenta,
									folio,
									cod_mov,
									fecha,
									a_nombre,
									num_proveedor,
									codgastos,
									concepto,
									facturas,
									importe,
									imp,
									proceso,
									poliza,
									archivo,
									site,
									iduser
								)
						VALUES
							(
								' . $num_cia . ',
								' . $cuenta . ',
								' . $folios[$num_cia][$cuenta] . ',
								' . ($tipo == 't' ? 41 : 5) . ',
								\'' . $fecha . '\',
								\'' . $nombre_pro . '\',
								' . $num_pro . ',
								' . $codgastos . ',
								\'' . (count($ids) == 1 ? $descripcion : $gasto) . '\',
								\'' . implode(' ', $facs) . '\',
								' . $importe_cheque . ',
								\'FALSE\',
								\'TRUE\',
								\'' . ($tipo == 't' ? 'TRUE' : 'FALSE') . '\',
								\'' . ($tipo == 't' ? 'FALSE' : 'TRUE') . '\',
								\'TRUE\',
								' . $_SESSION['iduser'] . '
							)
					' . ";\n";
					
					$sql .= '
						INSERT INTO
							estado_cuenta
								(
									num_cia,
									fecha,
									tipo_mov,
									cod_mov,
									cuenta,
									folio,
									concepto,
									importe,
									iduser
								)
						VALUES
							(
								' . $num_cia . ',
								\'' . $fecha . '\',
								\'TRUE\',
								' . ($tipo == 't' ? 41 : '5') . ',
								' . $cuenta . ',
								' . $folios[$num_cia][$cuenta] . ',
								\'' . implode(' ', $facs) . '\',
								' . $importe_cheque . ',
								' . $_SESSION['iduser'] . '
							)
					' . ";\n";
					
					$sql .= '
						INSERT INTO
							folios_cheque
								(
									num_cia,
									cuenta,
									folio,
									fecha,
									reservado,
									utilizado
								)
						VALUES
							(
								' . $num_cia . ',
								' . $cuenta . ',
								' . $folios[$num_cia][$cuenta] . ',
								\'' . $fecha . '\',
								\'FALSE\',
								\'TRUE\'
							)
						
					' . ";\n";
					
					$sql .= '
						INSERT INTO
							movimiento_gastos
								(
									num_cia,
									fecha,
									codgastos,
									cuenta,
									folio,
									concepto,
									importe,
									captura
								)
						VALUES
							(
								' . $num_cia . ',
								\'' . $fecha . '\',
								' . $codgastos . ',
								' . $cuenta . ',
								' . $folios[$num_cia][$cuenta] . ',
								\'' . (count($ids) == 1 ? $descripcion : $gasto) . '\',
								' . $importe_cheque . ',
								\'TRUE\'
							)
					' . ";\n";
					
					if ($tipo == 't') {
						$sql .= '
							INSERT INTO
								transferencias_electronicas
									(
										num_cia,
										cuenta,
										folio,
										num_proveedor,
										importe,
										fecha_gen,
										status,
										iduser
									)
							VALUES
								(
									' . $num_cia . ',
									' . $cuenta . ',
									' . $folios[$num_cia][$cuenta] . ',
									' . $num_pro . ',
									' . $importe_cheque . ',
									\'' . $fecha . '\',
									0,
									' . $_SESSION['iduser'] . '
								)
						' . ";\n";
					}
					
					$sql .= '
						INSERT INTO
							facturas_pagadas
								(
									num_cia,
									num_proveedor,
									num_fact,
									total,
									descripcion,
									fecha,
									fecha_cheque,
									folio_cheque,
									codgastos,
									proceso,
									imp,
									cuenta
								)
						SELECT
							num_cia,
							num_proveedor,
							num_fact,
							total,
							descripcion,
							fecha,
							\'' . $fecha . '\',
							' . $folios[$num_cia][$cuenta] . ',
							codgastos,
							\'TRUE\',
							\'FALSE\',
							' . $cuenta . '
						FROM
							pasivo_proveedores
						WHERE
							id
								IN
									(
										' . implode(', ', $ids) . '
									)
					' . ";\n";

					// [20-Jul-2013] Obtener datos para insertar en OpenBravo
					$sql_datos = "
						SELECT
							num_cia,
							num_proveedor
								AS num_pro,
							cp.nombre
								AS nombre_pro,
							cp.rfc
								AS rfc_pro,
							f.num_fact,
							f.fecha,
							cp.calle,
							cp.no_exterior,
							cp.no_interior,
							cp.colonia,
							cp.localidad,
							cp.referencia,
							cp.municipio,
							cp.estado,
							cp.pais,
							cp.codigo_postal,
							cp.email1
								AS email,
							COALESCE(cmp.nombre, f.concepto)
								AS descripcion,
							COALESCE(d.precio, f.importe)
								AS precio,
							COALESCE(d.cantidad, 1)
								AS cantidad,
							COALESCE(d.piva, f.piva)
								AS iva,
							CASE
								WHEN d.codmp IS NOT NULL THEN
									'C'
								WHEN f.codgastos IS NOT NULL THEN
									'G'
							END
								AS tipo,
							COALESCE(d.codmp, f.codgastos)
								AS codigo,
							COALESCE(d.ieps)
								AS ieps
						FROM
							pasivo_proveedores pp
							LEFT JOIN facturas f
								USING (num_cia, num_proveedor, num_fact)
							LEFT JOIN entrada_mp d
								USING (num_cia, num_proveedor, num_fact)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
							LEFT JOIN catalogo_proveedores cp
								USING (num_proveedor)
						WHERE
							pp.id IN (" . implode(', ', $ids) . ")
						ORDER BY
							COALESCE(d.ieps, 0) DESC,
							pp.id
					";

					$datos_fac = $db->query($sql_datos);

					// [20-Jul-2013] Query's de inserción a OpenBravo

					$ieps = 0;

					foreach ($datos_fac as $k => $df)
					{
						if ($df['ieps'] > 0)
						{
							$ieps += $df['ieps'];
						}

						$sql_ob .= "
							INSERT INTO
								i_invoice (
									i_invoice_id,
									ad_client_id,		-- 1000000
									ad_org_id,			-- num_cia
									createdby,			-- iduser
									updatedby,			-- iduser
									c_currency_id,		-- 130 MX
									bpartnervalue,		-- RFC de cliente
									name,				-- Razon social o nombre de cliente
									postal,				-- Codigo postal
									regionname,			-- Nombre del estado/entidad
									c_region_id,      	-- Id del estado/entidad
									c_country_id,		-- Mexico (247)
									email,				-- Email de cliente
									contactname,		-- Razon social o nombre de cliente
									phone,				-- Telefono de cliente
									doctypename,		-- Tipo de documento + num_cia (F102), (N102)...
									documentno,			-- Folio y serie de la factura
									productvalue,		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
									linedescription,	-- Descripcion
									priceactual,		-- Precio actual
									dateinvoiced,		-- Fecha factura (timestamp)
									qtyinvoiced,		-- Cantidad
									calle,
									noexterior,
									nointerior,
									colonia,
									localidad,
									referencia,
									municipio,
									bpartner_rfc,		-- RFC
									description,		-- Observaciones
									paymentrule,		-- Tipo de pago, 4=No especificado, B=Efectivo, 1=Transferencia bancaria, S=Cheque, K=Tarjeta de credito
									numctapago,			-- Cuenta de cargo
									paymenttermvalue,	-- Condiciones de pago
									aduana,
									pedimento,
									fecha_pedimento,
									facturaefilename,
									isready,
									uomname,				-- Unidad de medida
									issotrx
								) VALUES (
									COALESCE((
										SELECT
											MAX(i_invoice_id)
										FROM
											i_invoice
									), 0) + 1,
									1000000,
									{$df['num_cia']},
									{$_SESSION['iduser']},
									{$_SESSION['iduser']},
									130,
									'{$df['rfc_pro']}',
									'{$df['nombre_pro']}',
									'{$df['codigo_postal']}',
									'{$df['estado']}',
									NULL,
									247,
									'{$df['email']}',
									'{$df['nombre_pro']}',
									NULL,
									'{$df['tipo']}-{$df['num_cia']}',
									'{$df['num_fact']}',
									'{$df['tipo']}{$df['codigo']}',
									'{$df['descripcion']}',
									{$df['precio']},
									'{$df['fecha']}',
									'{$df['cantidad']}',
									'{$df['calle']}',
									'{$df['no_exterior']}',
									'{$df['no_interior']}',
									'{$df['colonia']}',
									'{$df['localidad']}',
									'{$df['referencia']}',
									'{$df['municipio']}',
									'{$df['rfc_pro']}',
									NULL,
									'4',
									NULL,
									0,
									NULL,
									NULL,
									NULL,
									NULL,
									" . ($k == count($datos_fac) - 1 && $ieps == 0 ? "'Y'" : "'N'") . ",
									NULL,
									'N'
								)
						;\n";
					}

					if ($ieps > 0)
					{
						$sql_ob .= "
							INSERT INTO
								i_invoice (
									i_invoice_id,
									ad_client_id,		-- 1000000
									ad_org_id,			-- num_cia
									createdby,			-- iduser
									updatedby,			-- iduser
									c_currency_id,		-- 130 MX
									bpartnervalue,		-- RFC de cliente
									name,				-- Razon social o nombre de cliente
									postal,				-- Codigo postal
									regionname,			-- Nombre del estado/entidad
									c_region_id,      	-- Id del estado/entidad
									c_country_id,		-- Mexico (247)
									email,				-- Email de cliente
									contactname,		-- Razon social o nombre de cliente
									phone,				-- Telefono de cliente
									doctypename,		-- Tipo de documento + num_cia (F102), (N102)...
									documentno,			-- Folio y serie de la factura
									productvalue,		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
									linedescription,	-- Descripcion
									priceactual,		-- Precio actual
									dateinvoiced,		-- Fecha factura (timestamp)
									qtyinvoiced,		-- Cantidad
									calle,
									noexterior,
									nointerior,
									colonia,
									localidad,
									referencia,
									municipio,
									bpartner_rfc,		-- RFC
									description,		-- Observaciones
									paymentrule,		-- Tipo de pago, 4=No especificado, B=Efectivo, 1=Transferencia bancaria, S=Cheque, K=Tarjeta de credito
									numctapago,			-- Cuenta de cargo
									paymenttermvalue,	-- Condiciones de pago
									aduana,
									pedimento,
									fecha_pedimento,
									facturaefilename,
									isready,
									uomname,				-- Unidad de medida
									issotrx
								) VALUES (
									COALESCE((
										SELECT
											MAX(i_invoice_id)
										FROM
											i_invoice
									), 0) + 1,
									1000000,
									{$df['num_cia']},
									{$_SESSION['iduser']},
									{$_SESSION['iduser']},
									130,
									'{$df['rfc_pro']}',
									'{$df['nombre_pro']}',
									'{$df['codigo_postal']}',
									'{$df['estado']}',
									NULL,
									247,
									'{$df['email']}',
									'{$df['nombre_pro']}',
									NULL,
									'{$df['tipo']}-{$df['num_cia']}',
									'{$df['num_fact']}',
									'G1140',
									'I.E.P.S.',
									{$ieps},
									'{$df['fecha']}',
									'1',
									'{$df['calle']}',
									'{$df['no_exterior']}',
									'{$df['no_interior']}',
									'{$df['colonia']}',
									'{$df['localidad']}',
									'{$df['referencia']}',
									'{$df['municipio']}',
									'{$df['rfc_pro']}',
									NULL,
									'4',
									NULL,
									0,
									NULL,
									NULL,
									NULL,
									NULL,
									'Y',
									NULL,
									'N'
								)
						;\n";
					}
					
					$sql .= '
						DELETE FROM
							pasivo_proveedores
						WHERE
							id
								IN
									(
										' . implode(', ', $ids) . '
									)
					' . ";\n";
					
					$sql .= '
						UPDATE
							saldos
						SET
							saldo_libros = saldo_libros - ' . $importe_cheque . '
						WHERE
								num_cia = ' . $num_cia . '
							AND
								cuenta = ' . $cuenta . '
					' . ";\n";;
					
					$folios[$num_cia][$cuenta]++;
					$ids = array();
					$facs = array();
					$importe_cheque = 0;
					$num_cheques++;
				}
			}
			
			$tpl->assign('pagado.total', number_format($total_pagado, 2, '.', ','));
			$tpl->assign('pagado.facs', number_format($total_facs));
			
			$db->query($sql);
			// $dbf->query($sql_ob);
			
			echo $tpl->getOutputContent();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/ProcesoPagosAutomaticoAuxiliar.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
$tpl->printToScreen();
?>