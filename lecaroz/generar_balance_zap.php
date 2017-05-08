<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';
include './includes/class.session2.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$sql = '
	SELECT
		nivel
	FROM
		balances_aut
	WHERE
		iduser = ' . $_SESSION['iduser'] . '
';
$nivel = $db->query($sql);

if (!$nivel || $nivel[0]['nivel'] == 0 || $nivel[0]['nivel'] == 1) {
	die('NO TIENEN AUTORIZACION PARA GENERAR BALANCES.');
}

$anyo = $_REQUEST['anyo'];
$mes = $_REQUEST['mes'];

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
$fecha_his = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo));
$dias = date('j', mktime(0, 0, 0, $mes + 1, 0, $anyo));

/*
@ Validar que ya se hayan capturado los encargados de las tiendas para el mes generado
*/
$sql = '
	SELECT
		id
	FROM
		encargados
	WHERE
			num_cia
				BETWEEN
						900
					AND
						998
		AND
			anio = ' . $anyo . '
		AND
			mes = ' . $mes . '
	LIMIT
		1
';
$encargados = $db->query($sql);

if (!$encargados) {
	die('NO SE HAN CAPTURADO LOS ENCARGADOS PARA EL A&Ntilde;O Y MES SOLICITADO');
}

$condiciones = array();
$condiciones[] = 'num_cia BETWEEN 900 AND 998';
$condiciones[] = 'num_cia IN (SELECT num_cia FROM total_zapaterias WHERE fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' GROUP BY num_cia)';

/*
@ Intervalo de compañías
*/
if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
	$cias = array();
	
	$pieces = explode(',', $_REQUEST['cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$cias[] = implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$cias[] = $piece;
		}
	}
	
	if (count($cias) > 0) {
		$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
	}
}

$sql = '
	SELECT
		num_cia
	FROM
		catalogo_companias
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

if (!$cias) die('NO HAY RESULTADOS');

$balance = '';
foreach ($cias as $c) {
	$balance .= '
		DELETE FROM
			balances_zap
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";
	
	$balance .= '
		DELETE FROM
			historico
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";
	
	$balance .= '
		DELETE FROM
			historico_saldos_zap
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";
	
	$data = array(
		'num_cia' => $c['num_cia'],
		'anio' => $anyo,
		'mes' => $mes,
		'fecha' => '\'' . date('d/m/Y',  mktime(0, 0, 0, $mes, 1, $anyo)) . '\''
	);
	
	/*
	@
	@@ VENTAS DEL MES
	@
	*/
	
	/*
	@ Venta del mes
	*/
	$sql = '
		SELECT
			sum
				(
					venta
				)
					AS
						venta
		FROM
			total_zapaterias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['venta_zap'] = $tmp[0]['venta'] != 0 ? $tmp[0]['venta'] : 0;
	
	/*
	@ Abono empleados
	*/
	$sql = '
		SELECT
			sum(importe)
				AS
					abono_emp
		FROM
			prestamos
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				tipo_mov = \'TRUE\'
	';
	$tmp = $db->query($sql);
	$data['abono_emp'] = $tmp[0]['abono_emp'] != 0 ? $tmp[0]['abono_emp'] : 0;
	
	/*
	@ Otros
	*/
	$sql = '
		SELECT
			sum(otros)
				AS
					otros
		FROM
			total_zapaterias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['otros'] = $tmp[0]['otros'] != 0 ? $tmp[0]['otros'] : 0;
	
	/*
	@ Total otros
	*/
	$data['total_otros'] = $data['abono_emp'] + $data['otros'];
	
	/*
	@ Errores
	*/
	$sql = '
		SELECT
			sum(errores)
				AS
					errores
		FROM
			efectivos_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['errores'] = $tmp[0]['errores'] != 0 ? $tmp[0]['errores'] : 0;
	
	/*
	@ Ventas Netas
	*/
	$data['ventas_netas'] = $data['venta_zap'] + $data['total_otros'] - $data['errores'];
	
	/*
	@ Inventario Anterior
	*/
	$sql = '
		SELECT
			importe
		FROM
			inventario_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				anio = ' . date('Y', mktime(0, 0, 0, $mes, 0, $anyo)) . '
			AND
				mes = ' . date('n', mktime(0, 0, 0, $mes, 0, $anyo)) . '
	';
	$tmp = $db->query($sql);
	$data['inv_ant'] = $tmp ? $tmp[0]['importe'] : 0;
	
	/*
	@ Compras
	*/
	$sql = '
		SELECT
			importe
		FROM
			compras_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
	';
	$tmp = $db->query($sql);
	if (!$tmp) {
		$sql = '
			SELECT
				importe,
				faltantes,
				dif_precio,
				CASE
					WHEN dev > 0 THEN
						dev
					WHEN (
						SELECT
							sum(importe)
						FROM
							devoluciones_zap
						WHERE
								num_proveedor = fz.num_proveedor
							AND
								num_fact = fz.num_fact
						) IS NOT NULL THEN
						(
							SELECT
								sum(importe)
							FROM
								devoluciones_zap
							WHERE
									num_proveedor = fz.num_proveedor
								AND
									num_fact = fz.num_fact
						)
					ELSE
						0
				END
					AS
						dev,
				pdesc1,
				pdesc2,
				pdesc3,
				pdesc4,
				desc1,
				desc2,
				desc3,
				desc4,
				(
					SELECT
						tipo
					FROM
						cat_conceptos_descuentos
					WHERE
						cod = fz.cod_desc1
				)
					AS
						tipo_desc1,
				(
					SELECT
						tipo
					FROM
						cat_conceptos_descuentos
					WHERE
						cod = fz.cod_desc2
				)
					AS
						tipo_desc2,
				(
					SELECT
						tipo
					FROM
						cat_conceptos_descuentos
					WHERE
						cod = fz.cod_desc3
				)
					AS
						tipo_desc3,
				(
					SELECT
						tipo
					FROM
						cat_conceptos_descuentos
					WHERE
						cod = fz.cod_desc4
				)
					AS
						tipo_desc4,
				iva,
				total
			FROM
				facturas_zap
					AS
						fz
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha_inv
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					codgastos = 33
		';
		$tmp = $db->query($sql);
		
		$data['compras'] = 0;
		$data['desc_compras'] = 0;
		$data['desc_pagos'] = 0;
		
		if ($tmp)
			foreach ($tmp as $i => $reg) {
				$subimporte = $reg['importe'] - $reg['faltantes'] - $reg['dif_precio'] - $reg['dev'];
				$desc1 = $reg['pdesc1'] > 0 ? round($subimporte * $reg['pdesc1'] / 100, 2) : ($reg['desc1'] > 0 ? $reg['desc1'] : 0);
				$data['desc_compras'] += $reg['tipo_desc1'] == 1 && $desc1 > 0 ? $desc1 : 0;
				$data['desc_pagos'] += $reg['tipo_desc1'] == 2 && $desc1 > 0 ? $desc1 : 0;
				$desc2 = $reg['pdesc2'] > 0 ? round(($subimporte - $desc1) * $reg['pdesc2'] / 100, 2) : ($reg['desc2'] > 0 ? $reg['desc2'] : 0);
				$data['desc_compras'] += $reg['tipo_desc2'] == 1 && $desc2 > 0 ? $desc2 : 0;
				$data['desc_pagos'] += $reg['tipo_desc2'] == 2 && $desc2 > 0 ? $desc2 : 0;
				$desc3 = $reg['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $reg['pdesc3'] / 100, 2) : ($reg['desc3'] > 0 ? $reg['desc3'] : 0);
				$data['desc_compras'] += $reg['tipo_desc3'] == 1 && $desc3 > 0 ? $desc3 : 0;
				$data['desc_pagos'] += $reg['tipo_desc3'] == 2 && $desc3 > 0 ? $desc3 : 0;
				$desc4 = $reg['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $reg['pdesc4'] / 100, 2) : ($reg['desc4'] > 0 ? $reg['desc4'] : 0);
				$data['desc_compras'] += $reg['tipo_desc4'] == 1 && $desc4 > 0 ? $desc4 : 0;
				$data['desc_pagos'] += $reg['tipo_desc4'] == 2 && $desc4 > 0 ? $desc4 : 0;
				$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
				
				$data['compras'] += $reg['importe'] + $reg['faltantes'] + $reg['dif_precio'] + $reg['dev'];
			}
	}
	else {
		$data['compras'] = $tmp && $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
		$data['desc_compras'] = 0;
		$data['desc_pagos'] = 0;
	}
	
	/*
	@ Traspaso de Pares
	*/
	$sql = '
		SELECT
			importe
		FROM
			traspaso_pares
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
	';
	$tmp = $db->query($sql);
	$data['traspaso_pares'] = $tmp ? $tmp[0]['importe'] : 0;
	
	/*
	@ Devoluciones
	@
	@ Nota: Para la misma tienda pero que no han sido aplicadas a ninguna factura
	*/
	$sql = '
		SELECT
			sum(d.importe)
				AS
					importe
		FROM
				devoluciones_zap
					AS
						d
			LEFT JOIN
				cheques
					AS
						c
							ON
								(
										c.num_cia = d.num_cia_cheque
									AND
										c.folio = folio_cheque
									AND
										c.cuenta = d.cuenta
								)
		WHERE
				d.num_cia = ' . $c['num_cia'] . '
			AND
				d.fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				(
						folio_cheque IS NULL
					OR
						(
								c.fecha > \'' . $fecha2 . '\'
							AND
								d.num_cia_cheque = ' . $c['num_cia'] . '
						)
				)
	';
	$tmp = $db->query($sql);
	$data['devoluciones'] = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	
	/*
	@ Inventario Actual
	*/
	$sql = '
		SELECT
			importe
		FROM
			inventario_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
	';
	$tmp = $db->query($sql);
	$data['inv_act'] = $tmp ? $tmp[0]['importe'] : 0;
	
	/*
	@ Materia Prima Utilizada
	@
	@ = 'Inventario Anterior' + 'Compras + Descuentos' - 'Descuento en Compras' + 'Traspado de Pares' - 'Devoluciones' - 'Inventario Actual'
	*/
	$data['mat_prima_utilizada'] = $data['inv_ant'] + ($data['compras'] + $data['desc_compras'] + $data['desc_pagos']) - $data['desc_compras'] + $data['traspaso_pares'] - $data['devoluciones'] - $data['inv_act'];
	
	/*
	@ Devoluciones otros meses
	*/
	$sql = '
		SELECT
			sum(d.importe)
				AS
					importe
		FROM
				devoluciones_zap
					d
			LEFT JOIN
				cheques
					c
						ON
							(
									c.num_cia = d.num_cia_cheque
								AND
									c.folio = folio_cheque
								AND
									c.cuenta = d.cuenta
							)
		WHERE
				d.num_cia = ' . $c['num_cia'] . '
			AND
				d.fecha < \'' . $fecha1 . '\'
			AND
				(
						folio_cheque IS NULL
					OR
						(
								c.fecha > \'' . $fecha2 . '\'
							AND
								d.num_cia_cheque = ' . $c['num_cia'] . '
						)
				)
	';
	$tmp = $db->query($sql);
	$data['dev_otros_meses'] = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	
	/*
	@ Devoluciones otras tiendas
	*/
	$sql = '
		SELECT
			sum(d.importe)
				AS
					importe
		FROM
				devoluciones_zap 
					d
			LEFT JOIN
				cheques 
					c
						ON
							(
									c.num_cia = d.num_cia_cheque
								AND
									c.folio = folio_cheque
								AND
									c.cuenta = d.cuenta
							)
		WHERE
				d.num_cia = ' . $c['num_cia'] . '
			AND
				d.num_cia_cheque <> ' . $c['num_cia'] . '
			AND
				d.fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				(
						folio_cheque IS NULL
					OR
						(
								c.fecha > \'' . $fecha2 . '\'
							AND
								d.num_cia_cheque <> ' . $c['num_cia'] . '
						)
				)
	';
	$tmp = $db->query($sql);
	$data['dev_otras_tiendas'] = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	
	/*
	@ Devoluciones por otras tiendas
	*/
	$sql = '
		SELECT
			sum(d.importe)
				AS
					importe
		FROM
				devoluciones_zap 
					d
			LEFT JOIN
				cheques 
					c
						ON
							(
									c.num_cia = d.num_cia_cheque
								AND
									c.folio = folio_cheque
								AND
									c.cuenta = d.cuenta
							)
		WHERE
				d.num_cia_cheque = ' . $c['num_cia'] . '
			AND
				d.num_cia <> ' . $c['num_cia'] . '
			AND
				d.fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				(
						folio_cheque IS NULL
					OR
						(
								c.fecha > \'' . $fecha2 . '\'
							AND
								d.num_cia <> ' . $c['num_cia'] . '
						)
				)
	';
	$tmp = $db->query($sql);
	$data['dev_por_otras_tiendas'] = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	
	/*
	@ Costo de Venta
	@
	@ = 'Materia Prima Utilizada' - 'Descuento en pagos' - 'Devoluciones otros meses' - 'Devoluciones otras tiendas' - 'Devoluciones por otras tiendas'
	*/
	$data['costo_venta'] = $data['mat_prima_utilizada'] - $data['desc_pagos'] - $data['dev_otros_meses'] - $data['dev_otras_tiendas'] - $data['dev_por_otras_tiendas'];
	
	/*
	@ Utilidad Bruta
	@
	@ = 'Ventas Netas' - 'Costo Producción'
	*/
	$data['utilidad_bruta'] = $data['ventas_netas'] - $data['costo_venta'];
	
	/*
	@
	@@ GASTOS
	@
	*/
	
	/*
	@ Gastos de Operación
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						gastos_operacion
		FROM
				movimiento_gastos
			LEFT JOIN
				catalogo_gastos
					USING
						(
							codgastos
						)
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codigo_edo_resultados = 1
			AND
				codgastos
					NOT IN
						(
							9,
							76,
							140,
							141
						)
	';
	$tmp = $db->query($sql);
	$data['gastos_operacion'] = $tmp[0]['gastos_operacion'] != 0 ? -$tmp[0]['gastos_operacion'] : 0;
	
	/*
	@ Gastos Generales
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						gastos_generales
		FROM
				movimiento_gastos
			LEFT JOIN
				catalogo_gastos
					USING
						(
							codgastos
						)
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codigo_edo_resultados = 2
			AND
				codgastos
					NOT IN
						(
							9,
							76,
							140,
							141
						)
	';
	$tmp = $db->query($sql);
	$data['gastos_generales'] = $tmp[0]['gastos_generales'] != 0 ? -$tmp[0]['gastos_generales'] : 0;
	
	/*
	@ Impuesto IDE
	@
	@ [03-Feb-2010] A partir del año 2010 el porcentaje es del 3% en lugar del 2%
	*/
	$por_ide = $anyo < 2010 ? 0.02 : 0.03;
	$sql = '
		SELECT
			round
				(
					sum(
						importe
					)
						::
							numeric
								- 25000,
					2
				)
					* ' . $por_ide . '
						AS
							impuesto_ide
		FROM
			estado_cuenta
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha_con
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				cod_mov
					IN
						(
							1,
							7,
							13,
							16,
							79
						)
	';
	$tmp = $db->query($sql);
	$impuesto_ide = $tmp[0]['impuesto_ide'] != 0 ? -$tmp[0]['impuesto_ide'] : 0;
	
	/*
	@ Sumar impuesto IDE a gastos generales
	*/
	$data['gastos_generales'] += $impuesto_ide;
	
	/*
	@ Gastos de Caja
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							-importe
						ELSE
							importe
						END
				)
					AS
						gastos_caja
		FROM
			gastos_caja
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				clave_balance = \'TRUE\'
	';
	$tmp = $db->query($sql);
	$data['gastos_caja'] = $tmp[0]['gastos_caja'] != 0 ? $tmp[0]['gastos_caja'] : 0;
	
	/*
	@ Comisiones Bancarias
	@
	@ Válido a partir de Marzo de 2007
	@
	@ No incluir código 78 IMPUESTO IDE
	*/
	$data['comisiones'] = 0;
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 3, 1, 2007)) {
		$sql = '
			SELECT
				sum
					(
						CASE
							WHEN tipo_mov = \'TRUE\' THEN
								-importe
							ELSE
								importe
						END
					)
						AS
							comisiones
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND \'' . $fecha2 . '\'
				AND
					(
							(
									cuenta = 1
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_bancos
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	78
																)
												GROUP BY
													cod_mov
											)
							)
						OR
							(
									cuenta = 2
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_santander
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	78
																)
												GROUP BY
													cod_mov
											)
							)
					)
		';
		$tmp = $db->query($sql);
		$data['comisiones'] = $tmp[0]['comisiones'] != 0 ? $tmp[0]['comisiones'] : 0;
	}
	
	/*
	@ Reservas
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						reserva
		FROM
			reservas_cias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha = \'' . $fecha1 . '\'
	';
	$tmp = $db->query($sql);
	$data['reservas'] = $tmp[0]['reserva'] != 0 ? -$tmp[0]['reserva'] : 0;
	
	/*
	@ Gastos Pagados por otras Compañías
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN num_cia_egreso = ' . $c['num_cia'] . ' THEN
							monto
						WHEN num_cia_ingreso = ' . $c['num_cia'] . ' THEN
							-monto
					END
				)
					AS
						gastos_otras_cias
		FROM
			gastos_otras_cia
		WHERE
				(
						num_cia_egreso = ' . $c['num_cia'] . '
					OR
						num_cia_ingreso = ' . $c['num_cia'] . '
				)
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['gastos_otras_cias'] = $tmp[0]['gastos_otras_cias'] != 0 ? $tmp[0]['gastos_otras_cias'] : 0;
	
	/*
	@ Pagos anticipados
	*/
	$sql = '
		SELECT
			sum(importe)
				AS
					importe
		FROM
			pagos_anticipados
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				(
					fecha_ini,
					fecha_fin
				)
					OVERLAPS
						(
							DATE \'' . $fecha1 . '\',
							DATE \'' . $fecha2 . '\'
						)
	';
	$tmp = $db->query($sql);
	$data['pagos_anticipados'] = $tmp[0]['importe'] != 0 ? -$tmp[0]['importe'] : 0;
	
	/*
	@ Total de Gastos
	*/
	$data['total_gastos'] = $data['gastos_operacion'] + $data['gastos_generales'] + $data['gastos_caja'] + $data['comisiones'] + $data['reservas'] + $data['gastos_otras_cias'] + $data['pagos_anticipados'];
	
	/*
	@ Ingresos Extraordinarios
	*/
	$sql = '
		SELECT
			sum(importe)
				AS
					importe
		FROM
			estado_cuenta
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				cod_mov = 18
	';
	$tmp = $db->query($sql);
	$data['ingresos_ext'] = $tmp[0]['importe'] != 0 ? $tmp[0]['importe'] : 0;
	
	/*
	@ Utilidad Neta
	*/
	$data['utilidad_neta'] = $data['utilidad_bruta'] + $data['total_gastos'] + $data['ingresos_ext'];
	
	/*
	@
	@@ Datos para históricos
	@
	*/
	
	$historico['num_cia'] = $c['num_cia'];
	$historico['anio'] = $anyo;
	$historico['mes'] = $mes;
	
	/*
	@ Saldo en banco
	*/
	$sql = '
		SELECT
			num_cia,
			sum(saldo_libros)
				AS
					saldo,
			(
				SELECT
					sum(
						CASE
							WHEN tipo_mov = \'FALSE\' THEN
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
						fecha > \'' . $fecha2 . '\'
			)
				AS
					movs
		FROM
			saldos
				s
		WHERE
			num_cia = ' . $c['num_cia'] . '
		GROUP BY
			num_cia
	';
	$tmp = $db->query($sql);
	$historico['saldo_banco'] = $tmp ? $tmp[0]['saldo'] + $tmp[0]['movs'] : 0;
	
	/*
	@ Saldo proveedores
	*/
	$sql = '
		SELECT
			sum(total)
				AS
					saldo
		FROM
			facturas_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha <= \'' . $fecha2 . '\'
			AND
				(
						tspago IS NULL
					OR
						tspago > \'' . $fecha2 . '\'::date
				)
	';
	$tmp = $db->query($sql);
	$historico['saldo_pro'] = $tmp[0]['saldo'] != 0 ? $tmp[0]['saldo'] : 0;
	
	/*
	@ Pares vendidos
	*/
	$sql = '
		SELECT
			sum(pares)
				AS
					pares
		FROM
			efectivos_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$historico['pares_vendidos'] = $tmp[0]['pares'] != 0 ? $tmp[0]['pares'] : 0;
	
	/*
	@ Inventario final
	*/
	$sql = '
		SELECT
			importe
		FROM
			inventario_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
	';
	$tmp = $db->query($sql);
	$historico['inventario'] = $tmp ? $tmp[0]['importe'] : 0;
	
	/*
	@ Efectivo
	*/
	$sql = '
		SELECT
			sum(efectivo)
				AS
					efectivo
		FROM
			total_zapaterias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$historico['efectivo'] = $tmp[0]['efectivo'] != 0 ? $tmp[0]['efectivo'] : 0;
	
	/*
	@ Clientes
	*/
	$sql = '
		SELECT
			sum(clientes)
				AS
					clientes
		FROM
			efectivos_zap
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$clientes = $tmp[0]['clientes'] != 0 ? $tmp[0]['clientes'] : 0;
	
	/*
	@ Crear querys de inserción
	*/
	
	/*
	@ Tabla: balances_zap
	*/
	$balance .= 'INSERT INTO balances_zap (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $data) . ');' . "\n";
	$balance .= 'INSERT INTO historico_saldos_zap (' . implode(', ', array_keys($historico)) . ') VALUES (' . implode(', ', $historico) . ');' . "\n";
	$balance .= 'INSERT INTO historico (num_cia, anio, mes, venta, utilidad, ingresos, clientes) SELECT num_cia, anio, mes, venta_zap, utilidad_neta, ingresos_ext, ' . $clientes . ' FROM balances_zap WHERE num_cia = ' . $data['num_cia'] . ' AND anio = ' . $data['anio'] . ' AND mes = ' . $data['mes'] . ";\n";
}
//echo "<pre>$balance</pre>";
$db->query($balance);

echo '<strong>SE HAN GENERADO/ACTUALIZADO TODOS LOS DATOS DE BALANCE SOLICITADOS</strong>';
?>