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
									AND
										ec.fecha = c.fecha
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
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@                                             @
@ Programa Principal de Balance de Panaderias @
@                                             @
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
*/

/*
@@@ Mes de balance
*/
$mes = $_GET['mes'];
/*
@@@ Año de balance
*/
$anyo = $_GET['anyo'];

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
$page_size = 317.00;

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

function hoja4($num_cia, $anyo, $mes) {
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

if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
	$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
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
		venta_zap,
		(
			SELECT
				venta_zap
			FROM
				balances_zap
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				venta_zap_ant,
		abono_emp,
		otros,
		total_otros,
		errores,
		(
			SELECT
				errores
			FROM
				balances_zap
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				errores_ant,
		ventas_netas,
		(
			SELECT
				ventas_netas
			FROM
				balances_zap
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				ventas_netas_ant,
		inv_ant,
		compras + desc_compras + desc_pagos
			AS
				compras,
		desc_compras,
		traspaso_pares,
		devoluciones,
		inv_act,
		mat_prima_utilizada,
		desc_pagos,
		dev_otros_meses,
		dev_otras_tiendas,
		dev_por_otras_tiendas,
		costo_venta,
		utilidad_bruta,
		gastos_operacion,
		gastos_generales,
		gastos_caja,
		comisiones,
		reservas,
		pagos_anticipados,
		gastos_otras_cias,
		total_gastos,
		ingresos_ext,
		(
			SELECT
				ingresos_ext
			FROM
				balances_zap
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
				balances_zap
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
		(
			SELECT
				nombre_inicio
			FROM
				encargados
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				inicio,
		(
			SELECT
				nombre_fin
			FROM
				encargados
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				termino
	FROM
			balances_zap bal
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
		'nombre_corto',
		'inicio',
		'termino'
	),
	'month' => array(
		'mes'
	),
	'integer' => array(
		'num_cia',
		'anyo'
	),
	'double2' => array(
		'venta_zap',
		'abono_emp',
		'otros',
		'total_otros',
		'errores',
		'ventas_netas',
		'inv_ant',
		'inv_act',
		'mat_prima_utilizada',
		'desc_pagos',
		'dev_otros_meses',
		'dev_otras_tiendas',
		'dev_por_otras_tiendas',
		'costo_venta',
		'gastos_operacion',
		'gastos_generales',
		'gastos_caja',
		'reservas',
		'pagos_anticipados',
		'gastos_otras_cias',
		'total_gastos',
		'ingresos_ext'
	),
	'double3' => array(
	),
	'double4' => array(
	),
	'colored_double2' => array(
		'compras',
		'desc_compras',
		'traspaso_pares',
		'devoluciones',
		'comisiones',
		'utilidad_bruta',
		'utilidad_neta',
		'utilidad_neta_ant'
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

$tpl = new TemplatePower('./plantillas/bal/balance_zap.tpl');
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
			//if ($k == 'utilidad_neta_ant')
				//$v = $v - $bal['ingresos_ext_ant'];

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
	@ Porcentaje correspondiente a la Venta con respecto a las Ventas Netas
	*/
	$vp_p = $bal['venta_zap'] != 0 ? ($bal['venta_zap'] - $bal['errores']) * 100 / $bal['ventas_netas'] : 0;
	$tpl->assign('vp_p', $vp_p != 0 ? '<span class="Orange">(' . number_format($vp_p, 2, '.', ',') . '%)</span>' : '&nbsp;');
	/*
	@ Comparativo de Venta con el año anterior
	*/
	$vz_act = $bal['venta_zap'] - $bal['errores'];
	$vz_ant = $bal['venta_zap_ant'] - $bal['errores_ant'];
	$vz_c = $vz_ant > 0 ? abs($vz_act * 100 / $vz_ant - 100) : 0;
	$tpl->assign('vz_c', $vz_act != $vz_ant ? '<span class="' . ($vz_act > $vz_ant ? 'Blue">SUBIO ' : 'Red">BAJO') . number_format($vz_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Ventas Netas con el año anterior
	*/
	$vn_act = $bal['ventas_netas'];
	$vn_ant = $bal['ventas_netas_ant'];
	$vn_c = $vn_ant > 0 ? abs(($vn_act) * 100 / ($vn_ant) - 100) : 0;
	$tpl->assign('vn_c', $vn_act != $vn_ant ? '<span class="' . ($vn_act > $vn_ant ? 'Blue">SUBIO ' : 'Red">BAJO ') . number_format($vn_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Utilidad Neta con el año anterior
	*/
	$un_act = $bal['utilidad_neta'] - $bal['ingresos_ext'];
	$un_ant = $bal['utilidad_neta_ant'] - $bal['ingresos_ext_ant'];
	$un_c = $un_ant > 0 ? abs(($un_act) * 100 / ($un_ant) - 100) : 0;
	$tpl->assign('un_c', $un_act != $un_ant && $un_c <= 100 ? '<span class="' . ($un_act > $un_ant ? 'Blue">MEJORO ' : 'Red">EMPEORO ') . number_format($un_c, 2, '.', ',') . '%</span>' : '&nbsp;');

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
	@ Historico de saldos, venta y efectivo
	*/
	$sql = '
		SELECT
			mes,
			saldo_banco,
			saldo_pro,
			pares_vendidos,
			inventario,
			efectivo
		FROM
			historico_saldos_zap
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes <= ' . $mes . '
		ORDER BY
			mes
	';
	$his_sal = $db->query($sql);

	if ($his_sal) {
		$totales = array(
			'sal' => 0,
			'salpro' => 0,
			'parven' => 0,
			'inv' => 0,
			'efe' => 0
		);

		foreach ($his_sal as $his) {
			$tpl->assign('sal' . $his['mes'], number_format($his['saldo_banco'], 2, '.', ','));
			$tpl->assign('salpro' . $his['mes'], number_format($his['saldo_pro'], 2, '.', ','));
			$tpl->assign('parven' . $his['mes'], number_format($his['pares_vendidos'], 0, '.', ','));
			$tpl->assign('inv' . $his['mes'], number_format($his['inventario'], 2, '.', ','));
			$tpl->assign('efe' . $his['mes'], number_format($his['efectivo'], 2, '.', ','));

			$totales['sal'] += $his['saldo_banco'];
			$totales['salpro'] += $his['saldo_pro'];
			$totales['parven'] += $his['pares_vendidos'];
			$totales['inv'] += $his['inventario'];
			$totales['efe'] += $his['efectivo'];
		}

		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($v, in_array($k, array('parven')) ? 0 : 2, '.', ',') : '&nbsp;');
	}

	/*
	@ Efectivo Depositado
	@
	@@@ Claúsula: A partir del 1 de Julio de 2008
	@@@
	@@@ [5-Ago-2008] Modificación de la claúsula, el impuesto IDE ahora se calcula a partir de todos los depósitos conciliados en el mes con código
	@@@ 1, 7, 13, 16, 79
	@@@ [9-Nov-2008] Modificación de porcentaje, a partir de Enero de 2010 el porcentaje cambia de 2% a 3%
	@@@ [4-Ene-2013] Omitido el código 7
	@@@ [2-Abr-2014] A partir de marzo de 2014 ya no se desglosa el IDE
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 7, 1, 2008)) {
		$sql = '
			SELECT
				sum(importe)
					AS
						efectivo
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $bal['num_cia'] . '
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
								-- 7,
								13,
								16,
								79
							)
		';
		$efectivo = $db->query($sql);

		$tpl->assign('porcentaje', $anyo < 2014 || ($anyo == 2014 && $mes < 3) ? ($anyo >= 2010 ? '3%:' : '2%:') : '&nbsp;');
		$porcentaje = $anyo < 2014 || ($anyo == 2014 && $mes < 3) ? ($anyo >= 2010 ? 0.03 : 0.02) : 0;

		$impuesto_ide = round(($efectivo[0]['efectivo'] - 25000) * $porcentaje, 2);

		if ($impuesto_ide > 0) {
			$tpl->assign('efe_fijo', number_format($efectivo[0]['efectivo'], 2, '.', ','));
			$tpl->assign('efe_2por', $impuesto_ide > 0 ? number_format($impuesto_ide, 2, '.', ',') : '&nbsp;');
		}
		else
			$tpl->assign('EfectivoDepositadoHidden', ' class="Hidden"');
	}
	// Si no cumple con la claúsula, esconder tabla
	else
		$tpl->assign('EfectivoDepositadoHidden', ' class="Hidden"');

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
			venta
				AS
					vta,
			clientes
		FROM
				historico
			LEFT JOIN
				balances_zap
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
			'vta_ant' => 0,
			'clientes_ant' => 0
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
			$tpl->assign('vta_ant_' . $est['mes'], $est['vta'] != 0 ? substr(number_format($est['vta'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('clientes_ant_' . $est['mes'], $est['clientes'] != 0 ? number_format($est['clientes']) : '&nbsp;');
			@$prom = $est['vta'] / $est['clientes'];
			$tpl->assign('prom_ant_' . $est['mes'], $prom != 0 ? number_format($prom, 2, '.', ',') : '&nbsp;');

			$totales['vta_ant'] += $est['vta'];
			$totales['clientes_ant'] += $est['clientes'];
		}

		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($v, in_array($k, array('vta_ant', 'abono_ant', 'bultos_ant')) ? /*2*/0 : 0, '.', ',') : '&nbsp;');
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
			venta
				AS
					vta,
			clientes
		FROM
				historico
			LEFT JOIN
				balances_zap
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
			'vta' => 0,
			'clientes' => 0
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
			$tpl->assign('vta_' . $est['mes'], $est['vta'] != 0 ? substr(number_format($est['vta'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('clientes_' . $est['mes'], $est['clientes'] != 0 ? number_format($est['clientes']) : '&nbsp;');
			@$prom = $est['vta'] / $est['clientes'];
			$tpl->assign('prom_' . $est['mes'], $prom != 0 ? number_format($prom, 2, '.', ',') : '&nbsp;');

			$totales['vta'] += $est['vta'];
			$totales['clientes'] += $est['clientes'];
		}

		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($v, in_array($k, array('vta', 'abono', 'bultos')) ? /*2*/0 : 0, '.', ',') : '&nbsp;');
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
								@@@ [03-Octu-2008]
								@@@ Modificación: En el conjunto de campos se excluyo "cuenta" y se agregaron los campos "codgastos" y "fecha"
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
																	cuenta,
																	fecha
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
												cuenta,
												fecha
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

						if ($tipo != 3) {
							$tpl->newBlock('total_gastos_empty');
							$tpl->gotoBlock('tipo_gasto');
						}
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

				// Si no fue insertado el código 182
				// [2-Abr-2014] A partir de marzo de 2014 ya no se desglosara IDE
				if ((mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 10, 1, 2006) && mktime(0, 0, 0, $mes, 1, $anyo) <= mktime(0, 0, 0, 2, 1, 2014))
						&&
							($tipo == 2 && $orden != $g['orden'] && !$imp_ide_ins[1] && !$imp_ide_ins[2] && !$imp_ide_ins[3])
						||
							($cod == 182 && (!$imp_ide_ins[1] || !$imp_ide_ins[2] || !$imp_ide_ins[3]))) {
					if ($cod != 182) {
						$tpl->newBlock('row_gasto');
						$tpl->assign('RowData', $color ? 'RowData' : '');
						$color = !$color;

						$tpl->assign('cod', 182);
						$tpl->assign('desc', 'IMPUESTO EROGACIONES + ' . ($anyo < 2010 ? 2 : 3) . '% IDE');
					}

					for ($col = 1; $col <= 3; $col++) {
						switch ($col) {
							case 1:
								$anyo_gasto = $anyo;
								$mes_gasto = $mes;
								break;
							case 2:
								$anyo_gasto = $anyo - 1;
								$mes_gasto = $mes;
								break;
							case 3:
								$anyo_gasto = date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo));
								$mes_gasto = date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo));
								break;
						}

						if (mktime(0, 0, 0, $mes_gasto, 1, $anyo_gasto) >= mktime(0, 0, 0, 7, 1, 2008) && !$imp_ide_ins[$col]) {
							/*
							@ [03-Feb-2010] A partir del año 2010 el porcentaje IDE pasa a ser 3% en lugar de 2%
							*/
							$por_ide = $anyo < 2010 ? 0.02 : 0.03;
							$sql = '
								SELECT
									round(
										(sum(importe)::numeric - 25000) * ' . $por_ide . ',
											2
									)
										AS
											importe
								FROM
									estado_cuenta
								WHERE
										num_cia = ' . $bal['num_cia'] . '
									AND
										fecha_con
											BETWEEN
													\'' . date('d/m/Y', mktime(0, 0, 0, $mes_gasto, 1, $anyo_gasto)) . '\'
												AND
													\'' . date('d/m/Y', mktime(0, 0, 0, $mes_gasto + 1, 0, $anyo_gasto)) . '\'
									AND
										cod_mov
											IN
												(
													1,
													16,
													13,
													7,
													79
												)
							';
							$impuesto_ide = $db->query($sql);

							// Poner como insertado el impuesto
							$imp_ide_ins[$col] = TRUE;

							if ($impuesto_ide[0]['importe'] > 0) {
								$tpl->assign('row_gasto.importe' . $col, number_format($impuesto_ide[0]['importe'], 2, '.', ','));

								$subtotal[$col] += $impuesto_ide[0]['importe'];
								$total[$col] += $impuesto_ide[0]['importe'];
							}
						}
						else if ($cod == 182 && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) < mktime(0, 0, 0, 7, 1, 2008))
							$imp_ide_ins[$col] = TRUE;
					}
				}

				$cod = $g['cod'];

				if ($tipo == 2 && $orden != $g['orden'])
					$color = !$color;

				$tpl->newBlock('row_gasto');
				$tpl->assign('RowData', $color ? 'RowData' : '');
				$color = !$color;

				$tpl->assign('cod', $g['cod']);
				$tpl->assign('desc', trim($g['desc']));

				$tpl->assign('num_cia', $bal['num_cia']);
				$tpl->assign('tipo', $tipo);

				if ($tipo == 2 && $orden != $g['orden']) {
					$orden = $g['orden'];

					$tpl->newBlock('row_gasto_empty');
					$tpl->assign('RowData', $color ? 'RowData' : '');
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
			if ($cod == 140 && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) >= mktime(0, 0, 0, 10, 1, 2006))
				continue;

			/*
			@@@ Claúsula: Sumar al importe del código 182 el importe del movimiento con código 78 'IMPUESTO IDE' del estado de cuenta
			@@@ correspondiente a la compañía, año y mes a partir del 1 de Julio de 2008
			@@@
			@@@ [5-Agosto-2008] Modificación de la claúsula, el impuesto IDE ahora se calcula a partir de todos los depósitos conciliados en el mes con código
			@@@ 1, 7, 13, 16, 79
			@@@ [3-Feb-2010] Modificación de la claúsula, el porcentaje IDE a partir del año 2010 es del 3% en lugar del 2%
			@@@ [2-Abr-2014] A partir de marzo de 2014 no se desglosa el IDE
			*/
			if ($cod == 182 && (mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) >= mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) <= mktime(0, 0, 0, 2, 1, 2014))) {
				$por_ide = $anyo < 2010 ? 0.02 : 0.03;
				$sql = '
					SELECT
						round
							(
								(
									sum
										(
											importe
										)
											::
												numeric
													- 25000
								)
									* ' . $por_ide . ',
								2
							)
								AS
									importe
					FROM
						estado_cuenta
					WHERE
							num_cia = ' . $bal['num_cia'] . '
						AND
							fecha_con
								BETWEEN
										\'' . date('d/m/Y', mktime(0, 0, 0, $g['mes'], 1, $g['anyo'])) . '\'
									AND
										\'' . date('d/m/Y', mktime(0, 0, 0, $g['mes'] + 1, 0, $g['anyo'])) . '\'
						AND
							cod_mov
								IN
									(
										1,
										16,
										13,
										7,
										79
									)
				';
				$impuesto_ide = $db->query($sql);

				// Poner como insertado el impuesto
				$imp_ide_ins[$col] = TRUE;

				if ($impuesto_ide[0]['importe'] > 0) {
					$g['importe'] += $impuesto_ide[0]['importe'];

					$tpl->assign('row_gasto.desc', trim($g['desc']) . ' + ' . ($anyo < 2010 ? 2 : 3) . '% IDE');
				}
			}
			// Si no cumple con la claúsula poner como insertado el impuesto
			// [2-Abr-2014] A partir de marzo de 2014 no se debe desglosar IDE
			else if ($cod == 182 && (mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) < mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $g['mes'], 2, $g['anyo']) > mktime(0, 0, 0, 2, 1, 2014)))
				$imp_ide_ins[$col] = TRUE;

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
	@ Hoja 3 : Comparativo de Balances
	@
	*/

	$sql = '
		SELECT
			anio
				AS
					anyo,
			mes,
			venta_zap,
			abono_emp,
			otros,
			total_otros,
			errores,
			ventas_netas,
			inv_ant,
			compras,
			desc_compras,
			traspaso_pares,
			devoluciones,
			inv_act,
			mat_prima_utilizada,
			desc_pagos,
			dev_otros_meses,
			dev_otras_tiendas,
			dev_por_otras_tiendas,
			costo_venta,
			utilidad_bruta,
			gastos_operacion,
			gastos_generales,
			gastos_caja,
			comisiones,
			reservas,
			pagos_anticipados,
			gastos_otras_cias,
			total_gastos,
			ingresos_ext,
			utilidad_neta,
			CASE
				WHEN anio = ' . $anyo . ' AND mes = ' . $mes . ' THEN
					1
				WHEN anio = ' . ($anyo - 1) . ' AND mes = ' . $mes . ' THEN
					2
				WHEN anio = ' . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . ' AND mes = ' . date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . ' THEN
					3
			END
				AS
					col
		FROM
			balances_zap
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				(
						(
								anio = ' . $anyo . '
							AND
								mes = ' . $mes . '
						)
					OR
						(
								anio = ' . ($anyo - 1) . '
							AND
								mes = ' . $mes . '
						)
					OR
						(
								anio = ' . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '
							AND
								mes = ' . date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '
						)
				)
		ORDER BY
			mes,
			anio
	';
	$historico = $db->query($sql);

	$tpl->newBlock('hoja3');
	$tpl->assign('num_cia', $bal['num_cia']);
	$tpl->assign('mes', $MonthName[$mes]['name']);
	$tpl->assign('anyo', $anyo);

	$hojas++;

	foreach ($historico as $h) {
		$col = $h['col'];

		foreach ($h as $k => $v) {
			if (in_array($k, $Campos['text']))
				$tpl->assign($k . $col, trim($v));
			else if (in_array($k, $Campos['month']))
				$tpl->assign($k . $col, $MonthName[$v]['name']);
			else if (in_array($k, $Campos['integer']))
				$tpl->assign($k . $col, $v);
			else if (in_array($k, $Campos['double2']))
				$tpl->assign($k . $col, round($v, 2) != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
			else if (in_array($k, $Campos['double3']))
				$tpl->assign($k . $col, round($v, 3) != 0 ? number_format($v, 3, '.', ',') : '&nbsp;');
			else if (in_array($k, $Campos['double4']))
				$tpl->assign($k . $col, round($v, 4) != 0 ? number_format($v, 4, '.', ',') : '&nbsp;');
			else if (in_array($k, $Campos['colored_double2']))
				$tpl->assign($k . $col, round($v, 2) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
			else if (in_array($k, $Campos['colored_double3']))
				$tpl->assign($k . $col, round($v, 3) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 3, '.', ',') . '</span>' : '&nbsp;');
			else if (in_array($k, $Campos['colored_double4']))
				$tpl->assign($k . $col, round($v, 4) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 4, '.', ',') . '</span>' : '&nbsp;');
			else if (in_array($k, $Campos['colored_double5']))
				$tpl->assign($k . $col, round($v, 5) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 5, '.', ',') . '</span>' : '&nbsp;');
		}
	}

	/*
	@
	@ Hoja 4 : Listado de Cheques
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
					concepto
				FROM
					facturas_zap
				WHERE
						num_cia = cheques.num_cia
					AND
						folio = cheques.folio
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
												cuenta,
												fecha
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
							cuenta,
							fecha
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
		$tpl->newBlock('hoja4');
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
						hoja4($bal['num_cia'], $anyo, $mes);
						$size = 0;
						$hojas++;
					}

					$tpl->newBlock('cheque_subtotal');
					$tpl->assign('subtotal',  '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
				}

				$cod = $c['cod'];

				$size += 8.4;
				if ($size > $page_size) {
					hoja4($bal['num_cia'], $anyo, $mes);
					$size = 0;
					$hojas++;
				}

				$tpl->newBlock('gasto_cheque');

				$subtotal = 0;
				$cont = 0;
			}

			$size += 10;
			if ($size > $page_size) {
				hoja4($bal['num_cia'], $anyo, $mes);
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

		$tpl->newBlock('total_cheques');
		$tpl->assign('total', '<span style="color:#' . ($total > 0 ? '00C' : 'C00') . '">' . number_format($total, 2, '.', ',') . '</span>');
	}

	if ($hojas % 2 != 0)
		$tpl->newBlock('hoja_blanca');
}

$tpl->printToScreen();
?>
