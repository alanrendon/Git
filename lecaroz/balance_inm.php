<?php
include './includes/class.db.inc.php';
include './includes/class.TemplatePower.inc.php';
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

if (!$nivel || $nivel[0]['nivel'] == 0) {
	die('NO TIENEN AUTORIZACION PARA IMPRIMIR BALANCES.');
}

/*
@@@
@ -- Ventana emergente --
@
@ Desglose de gastos
@
*/
if (isset($_GET['c'])) {
	$num_cia = $_GET['c'];
	$anyo = $_GET['y'];
	$mes = $_GET['m'];
	$cod = $_GET['g'];
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
	
	if ($_GET['t'] == 3) {
		$sql = '
			SELECT
				fecha,
				descripcion
					AS
						concepto,
				round
					(
						CASE
							WHEN tipo_mov = \'FALSE\' THEN
								-importe
							ELSE
								importe
						END
							::
								numeric,
						2
					)
						AS
							importe,
				\'\'
					AS
						num_pro,
				\'\'
					AS
						facturas,
				\'\'
					AS
						cuenta,
				\'\'
					AS
						folio,
				\'\'
					AS
						fecha_con
			FROM
					gastos_caja
						gc
				LEFT JOIN
					catalogo_gastos_caja
						cgc
							ON
								(
									cgc.id = gc.cod_gastos
								)
			WHERE
					num_cia = ' . $num_cia . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					cod_gastos = ' . $cod . '
			ORDER BY
				fecha,
				importe
		';
	}
	else {
		$sql = '
			SELECT
				mg.fecha,
				mg.concepto,
				mg.importe,
				c.num_proveedor
					AS
						num_pro,
				a_nombre
					AS
						nombre,
				facturas,
				c.folio,
				ec.cuenta,
				ec.fecha_con
			FROM
					movimiento_gastos
						mg
				LEFT JOIN
					cheques
						c
							ON
								(
										c.num_cia = mg.num_cia
									AND
										c.fecha = mg.fecha
									AND
										c.folio = mg.folio
									AND
										c.importe = mg.importe
								)
				LEFT JOIN
					estado_cuenta
						ec
							ON
								(
										ec.num_cia = c.num_cia
									AND
										ec.cuenta = c.cuenta
									AND
										ec.folio = c.folio
								)
			WHERE
					mg.num_cia = ' . $num_cia . '
				AND
					mg.fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					mg.codgastos = ' . $cod . '
			ORDER BY
				fecha,
				importe
		';
	}
	$result = $db->query($sql);
	
	$tpl = new TemplatePower('./plantillas/bal/desglose_gastos_balance.tpl');
	$tpl->prepare();
	
	$total = 0;
	$color = FALSE;
	foreach ($result as $reg) {
		$tpl->newBlock('row');
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('concepto', strtoupper(trim($reg['concepto'])));
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('num_pro', $reg['num_pro'] > 0 ? $reg['num_pro'] : '&nbsp;');
		$tpl->assign('nombre', $reg['num_pro'] > 0 ? $reg['nombre'] : '&nbsp;');
		$tpl->assign('facturas', trim($reg['facturas']) != '' ? trim($reg['facturas']) : '&nbsp;');
		$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
		$tpl->assign('folio', $reg['folio'] > 0 ? $reg['folio'] : '&nbsp;');
		$tpl->assign('fecha_con', $reg['fecha_con'] != '' ? $reg['fecha_con'] : '&nbsp;');
		$tpl->assign('RowData', $color ? 'RowData' : '');
		$color = !$color;
		
		$total += $reg['importe'];
	}
	
	$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
	
	die($tpl->printToScreen());
}
/*
@@@
@ Termina ventana emergente
@
*/

/*
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@                                                @
@ Programa Principal de Balance de Inmobiliarias @
@                                                @
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/

/*
@@@ Mes de balance
*/
$mes = $_REQUEST['mes'];
/*
@@@ Año de balance
*/
$anyo = $_REQUEST['anyo'];

/*
@@@ Fecha de inicio de mes
*/
$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
/*
@@@ Fecha de fin de mes
*/
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));

/*
@@@ Largo estándar del área imprimible de una hoja de balance de panadería (milímetros)
*/
$page_size = 260.00;

function hoja2($num_cia, $anyo, $mes, $tipo, $new) {
	global $MonthName, $tpl;
	
	$tpl->newBlock('hoja2');
	$tpl->assign('num_cia', $num_cia);
	$tpl->assign('mes', $MonthName[$mes]['name']);
	$tpl->assign('anyo', $anyo);
	
	if (!$new)
		return;
	
	$tpl->newBlock('tipo_gasto');
	switch ($tipo) {
		case 1:
			$concepto_tipo = 'DE OPERACI&Oacute;N';
			break;
		case 2:
			$concepto_tipo = 'GENERALES';
			break;
		case 3:
			$concepto_tipo = 'DE CAJA';
			break;
	}
	$tpl->assign('tipo', $concepto_tipo);
	$tpl->assign('leyenda', '<span class="Font6">(Continuaci&oacute;n...)</span>');
	
	$tpl->assign('mes1', $MonthName[$mes]['name']);
	$tpl->assign('anyo1', $anyo);
	$tpl->assign('mes2', $MonthName[$mes]['name']);
	$tpl->assign('anyo2', $anyo - 1);
	$tpl->assign('mes3', $MonthName[date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo))]['name']);
	$tpl->assign('anyo3', date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)));
}

function hoja3($num_cia, $anyo, $mes) {
	global $MonthName, $tpl;
	
	$tpl->newBlock('hoja4');
	$tpl->assign('num_cia', $num_cia);
	$tpl->assign('mes', $MonthName[$mes]['name']);
	$tpl->assign('anyo', $anyo);
	
	$tpl->newBlock('gasto_cheque');
}

/*
@
@ Query de consulta de datos principales de balance
@
*/

$condiciones[] = 'anio = ' . $anyo;
$condiciones[] = 'mes = ' . $mes;

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

$sql = '
	SELECT
		num_cia,
		nombre,
		nombre_corto,
		mes,
		anio
			AS
				anyo,
		rentas_cobradas,
		(
			SELECT
				rentas_cobradas
			FROM
				balances_inm
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				rentas_cobradas_ant,
		utilidad_bruta,
		gastos_generales,
		gastos_caja,
		comisiones,
		reserva_aguinaldos
			AS
				reservas,
		pagos_anticipados,
		gastos_otras_cias,
		total_gastos,
		ingresos_ext,
		(
			SELECT
				ingresos_ext
			FROM
				balances_inm
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				ingresos_ext_ant,
		utilidad_neta,
		(
			SELECT
				utilidad_neta
			FROM
				balances_inm
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				utilidad_neta_ant,
		saldo_inicial,
		saldo_final,
		diferencia_saldo,
		emp_afi
	FROM
			balances_inm bal
		LEFT JOIN
			catalogo_companias cc
				USING
					(
						num_cia
					)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';
$balances = $db->query($sql);

/*
@
@ Si no hay datos de balance, abortar proceso
@
*/

if (!$balances) die('NO HAY RESULTADOS');

/*
@
@ Arreglo con los nombres completos y abreviados de cada mes del año
@
*/

$MonthName = array(
	1 =>  array('name' => 'Enero',      'short_name' => 'Ene'),
	2 =>  array('name' => 'Febrero',    'short_name' => 'Feb'),
	3 =>  array('name' => 'Marzo',      'short_name' => 'Mar'),
	4 =>  array('name' => 'Abril',      'short_name' => 'Abr'),
	5 =>  array('name' => 'Mayo',       'short_name' => 'May'),
	6 =>  array('name' => 'Junio',      'short_name' => 'Jun'),
	7 =>  array('name' => 'Julio',      'short_name' => 'Jul'),
	8 =>  array('name' => 'Agosto',     'short_name' => 'Ago'),
	9 =>  array('name' => 'Septiembre', 'short_name' => 'Sep'),
	10 => array('name' => 'Octubre',    'short_name' => 'Oct'),
	11 => array('name' => 'Noviembre',  'short_name' => 'Nov'),
	12 => array('name' => 'Diciembre',  'short_name' => 'Dic')
);

$Campos = array(
	'text' => array(
		'nombre',
		'nombre_corto'
	),
	'month' => array(
		'mes'
	),
	'integer' => array(
		'num_cia',
		'anyo'
	),
	'double2' => array(
		'rentas_cobradas',
		'gastos_generales',
		'gastos_caja',
		'comisiones',
		'reservas',
		'pagos_anticipados',
		'gastos_otras_cias',
		'total_gastos',
		'ingresos_ext',
	),
	'double3' => array(
	),
	'double4' => array(
	),
	'colored_double2' => array(
		'utilidad_bruta',
		'utilidad_neta',
		'utilidad_neta_ant',
		'saldo_inicial',
		'saldo_final',
		'diferencia_saldo'
	),
	'colored_double3' => array(
	),
	'colored_double4' => array(
	),
	'colored_double5' => array(
	)
);

/*
@
@ Crear objeto TemplatePower
@
*/

$tpl = new TemplatePower('./plantillas/bal/balance_inm.tpl');
$tpl->prepare();

foreach ($balances as $bal) {
	/*
	@
	@ Crear balance para la compañía
	@
	*/
	
	$tpl->newBlock('balance');
	
	/*
	@ Contador de hojas de balance
	*/
	$hojas = 0;
	
	/*
	@
	@ Hoja 1 : Ventas, Costo de Producción, Gastos, Utilidad, Estadísticas
	@
	*/
	
	$tpl->newBlock('hoja1');
	
	/*
	@ Incrementar el número de hojas +1
	*/
	$hojas++;
	
	foreach ($bal as $k => $v) {
		if (in_array($k, $Campos['text']))
			$tpl->assign($k, trim($v));
		else if (in_array($k, $Campos['month']))
			$tpl->assign($k, $MonthName[$v]['name']);
		else if (in_array($k, $Campos['integer']))
			$tpl->assign($k, $v);
		else if (in_array($k, $Campos['double2'])) {
			/*
			@@@ Claúsula: No mostrar los Ingresos Extraordinarios si esta habilitada la opción 'no_ing'
			*/
			if (isset($_GET['no_ing']) && $k == 'ingresos_ext')
				$v = 0;
			
			$tpl->assign($k, round($v, 2) != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
		}
		else if (in_array($k, $Campos['double3']))
			$tpl->assign($k, round($v, 3) != 0 ? number_format($v, 3, '.', ',') : '&nbsp;');
		else if (in_array($k, $Campos['double4']))
			$tpl->assign($k, round($v, 4) != 0 ? number_format($v, 4, '.', ',') : '&nbsp;');
		else if (in_array($k, $Campos['colored_double2'])) {
			/*
			@@@ Claúsula: Restar a la utilidad neta del año anterior los ingresos extraordinarios
			*/
			if ($k == 'utilidad_neta_ant')
				$v = $v - $bal['ingresos_ext_ant'];
			
			/*
			@@@ Claúsula: Restar Ingresos Extraordinarios a la Utilidad Neta si esta habilitada la opción 'no_ing'
			*/
			if (isset($_GET['no_ing']) && $k == 'utilidad_neta')
				$v = $v - $bal['ingresos_ext'];
			
			/*
			@@@ Claúsula: Restar Ingresos Extraordinarios a la Utilidad Neta si esta habilitada la opción 'no_ing'
			*/
			if (isset($_GET['no_ing']) && $k == 'utilidad_neta_ant')
				$v = $v - $bal['ingresos_ext_ant'];
			
			$tpl->assign($k, round($v, 2) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
		}
		else if (in_array($k, $Campos['colored_double3']))
			$tpl->assign($k, round($v, 3) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 3, '.', ',') . '</span>' : '&nbsp;');
		else if (in_array($k, $Campos['colored_double4']))
			$tpl->assign($k, round($v, 4) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 4, '.', ',') . '</span>' : '&nbsp;');
		else if (in_array($k, $Campos['colored_double5']))
			$tpl->assign($k, round($v, 5) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 5, '.', ',') . '</span>' : '&nbsp;');
	}
	
	/*
	@ Reservas de la compañía
	*/
	$sql = '
		SELECT
			cod_reserva,
			descripcion,
			importe,
			codgastos
		FROM
				reservas_cias
			LEFT JOIN
				catalogo_reservas
					ON
						(
							tipo_res = cod_reserva
						)
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				fecha = \'' . $fecha1 . '\'
		ORDER BY
			cod_reserva
	';
	$reservas = $db->query($sql);
	
	if ($reservas) {
		$total_reservas = 0;
		foreach ($reservas as $r) {
			$tpl->newBlock('reserva');
			$tpl->assign('reserva', $r['descripcion']);
			$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
			$total_reservas += $r['importe'];
		}
		$tpl->assign('hoja1.total_reservas', number_format($total_reservas, 2, '.', ','));
		$tpl->assign('hoja1.asegurados', $bal['emp_afi'] > 0 ? $bal['emp_afi'] : '&nbsp;');
		$tpl->gotoBlock('hoja1');
	}
	
	/*
	@ Estadísticas Año Anterior
	*/
	$sql = '
		SELECT
			mes,
			utilidad
				AS
					util,
			ingresos
				AS
					ing,
			rentas_cobradas,
			saldo_final
				AS
					saldo
		FROM
				historico
			LEFT JOIN
				balances_inm
					USING
						(
							num_cia,
							anio,
							mes
						)
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				anio = ' . ($anyo - 1) . '
		ORDER BY
			mes
	';
	$est_ant = $db->query($sql);
	
	$tpl->assign('anyo_ant', $anyo - 1);
	
	if ($est_ant) {
		$totales = array(
			'rentas_ant' => 0
		);
		
		foreach ($est_ant as $est) {
			/*
			@ Utilidades Año Anterior
			@
			@ [6-Ago-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_ant', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_ant', $est['util'] != 0 ? number_format($est['util'] - (isset($_GET['no_ing']) ? $est['ing'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_ant', $est['ing'] != 0 && !isset($_GET['no_ing']) ? '(' . number_format($est['ing'], 2, '.', ',') . ')' : '&nbsp;');
			
			/*
			@ Estadísticas Año Anterior
			*/
			$tpl->assign('rentas_ant_' . $est['mes'], $est['rentas_cobradas'] != 0 ? number_format($est['rentas_cobradas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_ant_' . $est['mes'], $est['saldo'] != 0 ? number_format($est['saldo'], 2, '.', ',') : '&nbsp;');
			
			$totales['rentas_ant'] += $est['rentas_cobradas'];
		}
		
		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
	}
	
	/*
	@ Estadísticas Año Actual
	*/
	$sql = '
		SELECT
			mes,
			utilidad
				AS
					util,
			ingresos
				AS
					ing,
			rentas_cobradas,
			saldo_final
				AS
					saldo
		FROM
				historico
			LEFT JOIN
				balances_inm
					USING
						(
							num_cia,
							anio,
							mes
						)
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes <= ' . $mes . '
		ORDER BY
			mes
	';
	$est_act = $db->query($sql);
	
	$tpl->assign('anyo_act', $anyo);
	
	if ($est_act) {
		$totales = array(
			'rentas' => 0
		);
		
		foreach ($est_act as $est) {
			/*
			@ Utilidades Año Actual
			@
			@ [6-Ago-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_act', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_act', $est['util'] != 0 ? number_format($est['util'] - (isset($_GET['no_ing']) ? $est['ing'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_act', $est['ing'] != 0 && !isset($_GET['no_ing']) ? '(' . number_format($est['ing'], 2, '.', ',') . ')' : '&nbsp;');
			
			/*
			@ Estadísticas Año Actual
			*/
			$tpl->assign('rentas_' . $est['mes'], $est['rentas_cobradas'] != 0 ? number_format($est['rentas_cobradas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('saldo_' . $est['mes'], $est['saldo'] != 0 ? number_format($est['saldo'], 2, '.', ',') : '&nbsp;');
			
			$totales['rentas'] += $est['rentas_cobradas'];
		}
		
		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
	}
	
	/*
	@ Rentas pendientes
	*/
	
	$sql = '
		SELECT
			id,
			num_local || \' \' || nombre_local
				AS
					"local",
			(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0))
			+ (CASE WHEN tipo_local = 1 THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.16 ELSE 0 END)
			+ COALESCE(agua, 0)
			- (CASE WHEN retencion_isr = \'t\' THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.10 ELSE 0 END)
			- (CASE WHEN retencion_iva = \'t\' THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.10666666667 ELSE 0 END)
				AS
					"importe"
		FROM
				catalogo_arrendatarios
		WHERE
				cod_arrendador = ' . $bal['num_cia'] . '
			AND
				status = 1
			AND
				bloque = 2
		ORDER BY
			num_local
	';
	$locales = $db->query($sql);
	
	if ($locales) {
		$fecha1_renta = date('d/m/Y', mktime(0, 0, 0, $mes - 10, 0, $anyo));
		$fecha2_renta = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
		
		$current_year = $anyo;
		$current_month = $mes;
		$current_day = date('d', mktime(0, 0, 0, $mes + 1, 0, $anyo));
		
		$sql = '
			SELECT
				local,
				extract(month from fecha_renta)
					AS
						mes,
				extract(year from fecha_renta)
					AS
						anio,
				CASE
					WHEN fecha_con IS NOT NULL THEN
						\'t\'
					ELSE
						\'f\'
				END
					AS
						status
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $bal['num_cia'] . '
				AND
					local
						IN
							(
								SELECT
									ca.id
								FROM
										catalogo_arrendatarios ca
								WHERE
										ca.cod_arrendador = ' . $bal['num_cia'] . '
									AND
										ca.status = 1
									AND
										ca.bloque = 2
							)
				AND
					cod_mov = 2
				AND
					fecha_renta BETWEEN \'' . $fecha1_renta . '\' AND \'' . $fecha2_renta . '\'
			ORDER BY
				fecha_renta
		';
		$tmp = $db->query($sql);
		foreach ($tmp as $t)
			$rentas[$t['local']][$t['anio']][$t['mes']] = $t['status'] == 't' ? 1 : 10;
		
		$sql = '
			SELECT
				local,
				anio,
				mes,
				tipo
			FROM
					estatus_locales el
				LEFT JOIN
					catalogo_arrendatarios ca
						ON
							(ca.id = el.local)
			WHERE
					cod_arrendador = ' . $bal['num_cia'] . '
				AND
					tsmod <= \'' . $fecha2_renta . ' 23:59:59\'::timestamp
				AND
					local
						IN
							(
								SELECT
									ca.id
								FROM
										catalogo_arrendatarios ca
								WHERE
										ca.cod_arrendador = ' . $bal['num_cia'] . '
									AND
										ca.status = 1
									AND
										ca.bloque = 2
							)
				AND
					anio IN (' . $anyo . ', ' . $anyo . ' - 1, ' . $anyo . ' - 2)
				AND
					(local, mes, anio)
						NOT IN
							(
								SELECT
									local,
									extract(month from fecha_renta)
										AS
											mes,
									extract(year from fecha_renta)
										AS
											anio
								FROM
									estado_cuenta
								WHERE
										num_cia = ' . $bal['num_cia'] . '
									AND
										local
											IN
												(
													SELECT
														ca.id
													FROM
															catalogo_arrendatarios ca
													WHERE
															cod_arrendador = ' . $bal['num_cia'] . '
														AND
															ca.status = 1
														AND
															ca.bloque = 2
												)
									AND
										cod_mov = 2
									AND
										fecha_renta BETWEEN \'' . $fecha1_renta . '\' AND \'' . $fecha2_renta . '\'
							)
			ORDER BY
				local,
				anio,
				mes
		';
		$tmp = $db->query($sql);
		
		// [22-Oct-2008] Ordenar estados
		$estados = array();
		$ultimo_estado = array();
		
		if ($tmp) {
			foreach ($tmp as $t) {
				switch ($t['tipo']) {
					case 0:
						$estado = 2;
					break;
					case 1:
						$estado = 1;
					break;
					case 2:
						$estado = 0;
					break;
				}
				$estados[$t['local']][$t['anio']][$t['mes']] = $estado;
				$ultimo_estado[$t['local']] = $estado;
			}
		}
		
		$rentas_pendientes = array();
		
		foreach ($locales as $l) {
			$months = array();
			$ok = TRUE;
			$pen = FALSE;
			$last = NULL;
			for ($y = $anyo - 1; $y <= $anyo; $y++) {
				for ($m = ($y == $anyo - 1 ? (12 - $mes) : 1); $m <= ($y == $anyo - 1 ? 12 : $mes); $m++) {
					if (isset($rentas[$l['id']][$y][$m])) {
						$months[$y][$m] = $rentas[$l['id']][$y][$m];
						$ok = TRUE;
						// Poner los demas meses como pendientes
						$last = 2;
					}
					else if (isset($estados[$l['id']][$y][$m])) {
						$months[$y][$m] = $estados[$l['id']][$y][$m];
						$ok = $estados[$l['id']][$y][$m] == 1 ? TRUE : FALSE;
						$last = $l;
					}
					else if ($last == NULL && isset($ultimo_estado[$l['id']])) {
						$months[$y][$m] = $ultimo_estado[$l['id']];
						$ok = $ultimo_estado[$l['id']] == 1 ? TRUE : FALSE;
						$last = $months[$y][$m];
						
						if ($last == 0 && ($y < $anyo || ($y == $anyo && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15)))))
							$pen = TRUE;
					}
					else if (!$ok) {
						$months[$y][$m] = $last;
					}
					else {
						$months[$y][$m] = 0;
						if ($y < $anyo || ($y == $anyo && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15))))
							$pen = TRUE;
					}
				}
			}
			
			if (!$pen)
				continue;
			
			foreach ($months as $y => $m) {
				foreach ($m as $i => $s) {
					if ($s == 0) {
						$rentas_pendientes[] = array(
							'fecha'   => $y . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
							'local'   => $l['local'],
							'importe' => $l['importe']
						);
					}
				}
			}
		}
		
		if (count($rentas_pendientes) > 0) {
			$tpl->newBlock('rentas_pendientes');
			
			foreach ($rentas_pendientes as $i => $r) {
				$tpl->newBlock('renta_pendiente_' . ($i < 10 ? 1 : 2));
				$tpl->assign('fecha', $r['fecha']);
				$tpl->assign('local', $r['local']);
				$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
			}
		}
	}
	
	
	/*
	@
	@ Hoja 2 : Relación de Gastos Totales
	@
	*/
	
	/*
	@
	@ Query de consulta de gastos
	@
	*/
	$sql = '
			(
				SELECT
					cod,
					"desc",
					tipo,
					anyo,
					mes,
					orden,
					sum
						(
							importe
						)
							AS
								importe
				FROM
					(
							SELECT
								codgastos
									AS
										cod,
								descripcion
									AS
										desc,
								codigo_edo_resultados
									AS
										tipo,
								extract
									(
										year
											from
												fecha
									)
										AS
											anyo,
								extract
									(
										month
											from
												fecha
									)
										AS
											mes,
								orden,
								round
									(
										sum
											(
												importe
											)
												::
													numeric,
										2
									)
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
							WHERE
									num_cia = ' . $bal['num_cia'] . '
								AND
									(
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\'
													AND
														\'' . $fecha2 . '\'
										OR
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\'
													AND
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
									)
								AND
									codigo_edo_resultados
										IN
											(
												1,
												2
											)
								AND
									codgastos
										NOT IN
												(
													140,
													141
												)
								AND
									round
										(
											importe
												::
													numeric,
											2
										)
											<> 0
								/*
								@@@
								@@@ [04-Sep-2008]
								@@@ Claúsula: Omitir todos los pagos hechos para otras compañías
								@@@ [03-Oct-2008]
								@@@ Modificación: En el conjunto de campos se excluyo "cuenta" y "folio" y se agregaron los campos "codgastos" y "fecha"
								@@@
								*/
								AND
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
														num_cia = ' . $bal['num_cia'] . '
													AND
														(
																fecha
																	BETWEEN
																			\'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\'
																		AND
																			\'' . $fecha2 . '\'
															OR
																fecha
																	BETWEEN
																			\'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\'
																		AND
																			\'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
														)
											)
								/*
								@@@
								@@@ Termina claúsula
								@@@
								*/
							
							GROUP BY
								codgastos,
								descripcion,
								tipo,
								anyo,
								mes,
								orden
						
						UNION
							
							SELECT
								CASE
									WHEN cod_mov = 33 THEN
										140
									WHEN cod_mov = 12 THEN
										202
									WHEN cod_mov = 78 THEN
										203
								END
									AS
										cod,
								CASE
									WHEN cod_mov = 33 THEN
										\'IMPUESTOS FEDERALES\'
									WHEN cod_mov = 12 THEN
										\'I.S.R. RETENCIONES\'
									WHEN cod_mov = 78 THEN
										\'IMPUESTOS I.D.E.\'
								END
									AS
										desc,
								2
									AS
										tipo,
								extract
									(
										year
											from
												fecha
									)
										AS
											anyo,
								extract
									(
										month
											from
												fecha
									)
										AS
											mes,
								3
									AS
										orden,
								round
									(
										sum
											(
												importe
											)
												::
													numeric,
										2
									)
										AS
											importe
							FROM
								estado_cuenta
							WHERE
									num_cia = ' . $bal['num_cia'] . '
								AND
									(
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\'
													AND
														\'' . $fecha2 . '\'
										OR
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\'
													AND
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
									)
								AND
									cod_mov
										IN
											(
												33,
												12,
												78
											)
							GROUP BY
								cod,
								"desc",
								tipo,
								anyo,
								mes,
								orden
						
						UNION
				
							/*
							@@@
							@@@ [04-Sep-2008]
							@@@ Claúsula: Incluir pagos hechos por otras compañías para la compañía consultada
							@@@ [03-Oct-2008]
							@@@ Corrección: Campo "num_cia" cambiado por "num_cia_aplica"
							@@@
							*/
							SELECT
								codgastos
									AS
										cod,
								descripcion
									AS
										desc,
								codigo_edo_resultados
									AS
										tipo,
								extract
									(
										year
											from
												fecha
									)
										AS
											anyo,
								extract
									(
										month
											from
												fecha
									)
										AS
											mes,
								orden,
								round
									(
										sum
											(
												importe
											)
												::
													numeric,
										2
									)
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
							WHERE
									num_cia_aplica = ' . $bal['num_cia'] . '
								AND
									(
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\'
													AND
														\'' . $fecha2 . '\'
										OR
											fecha
												BETWEEN
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\'
													AND
														\'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
									)
								AND
									codigo_edo_resultados
										IN
											(
												1,
												2
											)
								AND
									codgastos
										NOT IN
											(
												140,
												141
											)
								AND
									round
										(
											importe
												::
													numeric,
											2
										)
											<> 0
							GROUP BY
								cod,
								descripcion,
								tipo,
								anyo,
								mes,
								orden
							/*
							@@@
							@@@ Termina claúsula @@@
							@@@
							*/
					)
						AS
							result_gastos
				GROUP BY
					cod,
					"desc",
					tipo,
					anyo,
					mes,
					orden
			)
			
		UNION
		
			SELECT
				cod_gastos
					AS
						cod,
				descripcion
					AS
						desc,
				3
					AS
						tipo,
				extract
					(
						year
							from
								fecha
					)
						AS
							anyo,
				extract
					(
						month
							from
								fecha
					)
						AS
							mes,
				1
					AS
						orden,
				round
					(
						sum
							(
								CASE
									WHEN tipo_mov = \'FALSE\' THEN
										-importe
									ELSE
										importe
								END
							)
								::
									numeric,
							2
					)
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
			WHERE
					num_cia = ' . $bal['num_cia'] . '
				AND
					(
							fecha
								BETWEEN
										\'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\'
									AND
										\'' . $fecha2 . '\'
						OR
							fecha
								BETWEEN
										\'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\'
									AND
										\'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
					)
				AND
					clave_balance = \'TRUE\'
			GROUP BY
				cod,
				descripcion,
				tipo,
				anyo,
				mes,
				orden
		
		
		
		ORDER BY
			tipo,
			orden,
			cod,
			mes
				DESC,
			anyo
				DESC
	';
	$gastos = $db->query($sql);
	
	if ($gastos) {
		$tpl->newBlock('hoja2');
		$tpl->assign('num_cia', $bal['num_cia']);
		$tpl->assign('mes', $MonthName[$mes]['name']);
		$tpl->assign('anyo', $anyo);
		
		/*
		@ Incrementar el número de hojas +1
		*/
		$hojas++;
		$size = 10;
		
		$tipo = NULL;
		$cod = NULL;
		$total = array(
			1 => 0,
			2 => 0,
			3 => 0
		);
		
		// @@ [5-Agosto-2008] Bandera que indica si ha sido insertado el impuesto IDE en el apartado de Gastos Generales
		$imp_ide_ins = array(
			1 => FALSE,
			2 => FALSE,
			3 => FALSE
		);
		
		foreach ($gastos as $g) {
			if ($tipo != $g['tipo']) {
				if ($tipo != NULL) {
					if (in_array($tipo, array(1, 2))) {
						$size += 4.2;
						
						$tpl->newBlock('subtotal_gastos');
						
						foreach ($subtotal as $k => $v)
							$tpl->assign('subtotal' . $k, number_format($v, 2, '.', ','));
					}
					if (in_array($tipo, array(2, 3))) {
						$size += 5;
						
						$tpl->newBlock('total_gastos');
						
						foreach ($total as $k => $v)
							$tpl->assign('total' . $k, number_format($v, 2, '.', ','));
						
						$tpl->newBlock('total_gastos_empty');
						$tpl->gotoBlock('tipo_gasto');
					}
				}
				
				$tipo = $g['tipo'];
				
				$size += 7.5;
				if ($size > $page_size) {
					hoja2($bal['num_cia'], $anyo, $mes, $tipo, FALSE);
					$size = 0;
					/*
					@ Incrementar el número de hojas +1
					*/
					$hojas++;
				}
				
				$tpl->newBlock('tipo_gasto');
				switch ($tipo) {
					case 1:
						$concepto_tipo = 'DE OPERACI&Oacute;N';
						break;
					case 2:
						$concepto_tipo = 'GENERALES';
						break;
					case 3:
						$concepto_tipo = 'DE CAJA';
						break;
				}
				$tpl->assign('tipo', $concepto_tipo);
				
				$tpl->assign('mes1', $MonthName[$mes]['name']);
				$tpl->assign('anyo1', $anyo);
				$tpl->assign('mes2', $MonthName[$mes]['name']);
				$tpl->assign('anyo2', $anyo - 1);
				$tpl->assign('mes3', $MonthName[date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo))]['name']);
				$tpl->assign('anyo3', date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)));
				
				$subtotal = array(
					1 => 0,
					2 => 0,
					3 => 0
				);
				
				if ($tipo == 3)
					$total = array(
						1 => 0,
						2 => 0,
						3 => 0
					);
				
				$orden = $g['orden'];
				
				$color = FALSE;
			}
			if ($cod != $g['cod']) {
				$size += 4.2 + ($tipo == 2 && $orden != $g['orden'] ? 4.2 : 0);
				if ($size > $page_size) {
					hoja2($bal['num_cia'], $anyo, $mes, $tipo, TRUE);
					$size = 0;
					/*
					@ Incrementar el número de hojas +1
					*/
					$hojas++;
				}
				
				$cod = $g['cod'];
				
				$tpl->newBlock('row_gasto');
				$tpl->assign('RowData', $color ? 'RowData' : '');
				$color = !$color;
				
				$tpl->assign('cod', $g['cod']);
				$tpl->assign('desc', trim($g['desc']));
				
				$tpl->assign('num_cia', $bal['num_cia']);
				$tpl->assign('tipo', $tipo);
				
				if ($orden != $g['orden']) {
					$orden = $g['orden'];
				}
				
			}
			
			/*
			@@@ Determinar a que columna pertenece el gasto
			*/
			if ($g['anyo'] == $anyo) {
				if ($g['mes'] == $mes)
					$col = 1;
				else
					$col = 3;
			}
			else {
				if ($g['mes'] == $mes)
					$col = 2;
				else
					$col = 3;
			}
			
			/*
			@@@ Claúsula: Todos los movimientos con código 140 'IMPUESTOS' y con fecha mayor al 1 de Octubre de 2006 omitirlo
			*/
			if ($cod == 140 && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) >= mktime(0, 0, 0, 10, 1, 2006) && $orden != 3)
				continue;
			
			/*
			@ 
			*/
			$tpl->assign('row_gasto.anyo' . $col, $g['anyo']);
			$tpl->assign('row_gasto.mes' . $col, $g['mes']);
			
			$tpl->assign('row_gasto.importe' . $col, number_format($g['importe'], 2, '.', ','));
			
			$subtotal[$col] += $g['importe'];
			$total[$col] += $g['importe'];
		}
		
		if ($tipo != NULL) {
			if (in_array($tipo, array(1, 2))) {
				$tpl->newBlock('subtotal_gastos');
				
				foreach ($subtotal as $k => $v)
					$tpl->assign('subtotal' . $k, number_format($v, 2, '.', ','));
			}
			if (in_array($tipo, array(2, 3))) {
				$tpl->newBlock('total_gastos');
				
				foreach ($total as $k => $v)
					$tpl->assign('total' . $k, number_format($v, 2, '.', ','));
				
				if ($tipo != 3) {
					$tpl->newBlock('total_gastos_empty');
					$tpl->gotoBlock('tipo_gasto');
				}
			}
		}
	}
	
	/*
	@
	@ Hoja 3 : Listado de Cheques
	@
	*/
	
	$sql = '
		SELECT
			id,
			codgastos
				AS
					cod,
			descripcion
				AS
					desc,
			a_nombre,
			concepto,
			(
				SELECT
					descripcion
				FROM
					facturas_pagadas
				WHERE
						num_cia = cheques.num_cia
					AND
						folio_cheque = cheques.folio
					AND
						cuenta = cheques.cuenta
				LIMIT 1
			)
				AS
					concepto_fac,
			facturas,
			fecha,
			folio,
			importe
		FROM
				cheques
			LEFT JOIN
				catalogo_gastos
					cg
						USING
							(
								codgastos
							)
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				fecha
					BETWEEN 
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codgastos
					NOT IN
						(
							33,
							134,
							154,
							999,
							140
						)
			AND
				(
						fecha_cancelacion
							IS NULL
					OR
						fecha_cancelacion > \'' . $fecha2 . '\'
				)
			AND
				(
					num_cia,
					folio,
					cuenta
				)
					NOT IN
						(
							SELECT
								num_cia,
								folio,
								cuenta
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
									num_cia = ' . $bal['num_cia'] . '
								AND
									fecha
										BETWEEN 
												\'' . $fecha1 . '\'
											AND
												\'' . $fecha2 . '\'
						)
			
		UNION
		
		SELECT
			cheques.id,
			codgastos
				AS
					cod,
			descripcion
				AS
					desc,
			a_nombre,
			concepto,
				(
					SELECT
						descripcion
					FROM
						facturas_pagadas
					WHERE
							num_cia = poc.num_cia
						AND
							folio_cheque = poc.folio
						AND
							cuenta = poc.cuenta
					LIMIT 1
				)
					AS
						concepto_fac,
			facturas,
			fecha,
			folio,
			importe
		FROM
				pagos_otras_cias
					poc
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
					cg
						USING
							(
								codgastos
							)
		WHERE
				num_cia_aplica = ' . $bal['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
		
		ORDER BY
			cod,
			fecha
	';
	$cheques = $db->query($sql);
	
	if ($cheques) {
		$tpl->newBlock('hoja3');
		$tpl->assign('num_cia', $bal['num_cia']);
		$tpl->assign('mes', $MonthName[$mes]['name']);
		$tpl->assign('anyo', $anyo);
		
		$hojas++;
		$size = 10;
		
		$total = 0;
		$cod = NULL;
		foreach ($cheques as $c) {
			if ($cod != $c['cod']) {
				if ($cod != NULL && $cont > 1) {
					$size += 4.1;
					if ($size > $page_size) {
						hoja3($bal['num_cia'], $anyo, $mes);
						$size = 0;
						$hojas++;
					}
					
					$tpl->newBlock('cheque_subtotal');
					$tpl->assign('subtotal',  '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
				}
				
				$cod = $c['cod'];
				
				$size += 8.4;
				if ($size > $page_size) {
					hoja3($bal['num_cia'], $anyo, $mes);
					$size = 0;
					$hojas++;
				}
				
				$tpl->newBlock('gasto_cheque');
				
				$subtotal = 0;
				$cont = 0;
			}
			
			$size += 10;
			if ($size > $page_size) {
				hoja3($bal['num_cia'], $anyo, $mes);
				$size = 0;
				$hojas++;
			}
			
			$tpl->newBlock('row_cheque');
			$tpl->assign('cod', $c['cod']);
			$tpl->assign('desc', $c['desc']);
			$tpl->assign('a_nombre', $c['a_nombre']);
			$tpl->assign('facturas', trim($c['facturas']));
			$tpl->assign('concepto', (trim($c['facturas']) != '' ? '<br>' : '') . (trim($c['concepto_fac']) != '' ? strtoupper(trim($c['concepto_fac'])) : strtoupper(trim($c['concepto']))));
			$tpl->assign('fecha', $c['fecha']);
			$tpl->assign('folio', $c['folio']);
			$tpl->assign('importe', '<span style="color:#' . ($c['importe'] > 0 ? '00C' : 'C00') . '">' . number_format($c['importe'], 2, '.', ',') . '</span>');
			
			/*
			@ [6-Agosto-2008] Asignar el número de compañía y el importe sin formato al elemento oculto 'checkbox' del listado de cheques
			*/
			$tpl->assign('num_cia', $bal['num_cia']);
			$tpl->assign('importe_checkbox', round($c['importe'], 2));
			
			$subtotal += $c['importe'];
			$total += $c['importe'];
			
			$cont++;
		}
		if ($cod != NULL && $cont > 1) {
			$tpl->newBlock('cheque_subtotal');
			$tpl->assign('subtotal', '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
		}
		
		$size += 7;
		if ($size > $page_size) {
			hoja4($bal['num_cia'], $anyo, $mes);
			$size = 0;
			$hojas++;
		}
	}
	
	$tpl->newBlock('total_cheques');
	$tpl->assign('total', '<span style="color:#' . ($total > 0 ? '00C' : 'C00') . '">' . number_format($total, 2, '.', ',') . '</span>');
	
	if ($hojas % 2 != 0)
		$tpl->newBlock('hoja_blanca');
}

$tpl->printToScreen();
?>
