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
if (isset($_REQUEST['c'])) {
	$num_cia = $_REQUEST['c'];
	$anyo = $_REQUEST['y'];
	$mes = $_REQUEST['m'];
	$cod = $_REQUEST['g'];

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));

	if ($_REQUEST['t'] == 3) {
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
				AND (mg.num_cia, mg.fecha, mg.codgastos, mg.importe) NOT IN (
					SELECT
						num_cia,
						fecha,
						codgastos,
						importe
					FROM
						pagos_otras_cias
						LEFT JOIN cheques
							USING (num_cia, folio, cuenta, fecha)
					WHERE
						num_cia = ' . $num_cia . '
						AND
							fecha
								BETWEEN
										\'' . $fecha1 . '\'
									AND
										\'' . $fecha2 . '\'
						AND fecha_cancelacion IS NULL
				)
				AND mg.idmovimiento_gastos NOT IN (
					SELECT
						idmovimiento_gastos
					FROM
						movimiento_gastos
					WHERE
						num_cia = ' . $num_cia . '
						AND fecha BETWEEN \'' . $fecha1 . '\'AND \'' . $fecha2 . '\'
						AND codgastos = 90
						AND concepto != \'DIFERENCIA INVENTARIO\'
				)

			UNION

			SELECT
				poc.fecha,
				c.concepto,
				c.importe,
				c.num_proveedor
					AS
						num_pro,
				c.a_nombre
					AS
						nombre,
				c.facturas,
				c.folio,
				c.cuenta,
				ec.fecha_con
			FROM
				pagos_otras_cias poc
				LEFT JOIN cheques c
					USING (num_cia, folio, cuenta, fecha)
				LEFT JOIN estado_cuenta ec
					USING (num_cia, folio, cuenta, fecha)
				LEFT JOIN catalogo_gastos cg
					USING (codgastos)
			WHERE
				poc.num_cia_aplica = ' . $num_cia . '
				AND c.fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
				AND c.codgastos = ' . $cod . '
				AND ROUND(c.importe::NUMERIC, 2) <> 0
				AND c.fecha_cancelacion IS NULL

			UNION

			SELECT
				CONCAT_WS(\'/\', \'01\', rg.mes, rg.anio)::DATE,
				\'GASTO EN RESERVA\',
				rg.importe,
				NULL
					AS
						num_pro,
				NULL
					AS
						nombre,
				NULL,
				NULL,
				NULL,
				NULL
			FROM
				reserva_gastos rg
				LEFT JOIN catalogo_gastos cg USING (codgastos)
			WHERE
				rg.num_cia = ' . $num_cia . '
				AND rg.anio = ' . $anyo . '
				AND rg.mes = ' . $mes . '
				AND rg.codgastos = ' . $cod . '
				AND ROUND(rg.importe::NUMERIC, 2) <> 0

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

if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 48, 50))) {
	$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
}

if (!in_array($_SESSION['iduser'], array(1, 4))) {
	$condiciones[] = "bal.fecha >= '" . date('01-m-Y') . "'::DATE - INTERVAL '2 YEARS'";
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
		venta_puerta - pastel_vitrina - pastel_pedido - pan_pedido
			AS
				venta_puerta,
		pastel_vitrina,
		pastel_pedido,
		pastel_kilos,
		pan_pedido,
		venta_puerta
			AS
				venta_puerta_total,
		(
			SELECT
				venta_puerta
			FROM
				balances_pan
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				venta_puerta_total_ant,
		/*bases,*/
		COALESCE((
			SELECT
				SUM(base)
			FROM
				venta_pastel
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND tipo = 0
				AND estado < 2
				AND base > 0
		), 0)
			AS bases,
		barredura,
		pastillaje,
		abono_emp,
		otros - COALESCE((
			SELECT
				SUM(base)
			FROM
				venta_pastel
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND tipo = 0
				AND estado < 2
				AND base > 0
		), 0)
			AS otros,
		total_otros,
		abono_reparto,
		(
			SELECT
				abono_reparto
			FROM
				balances_pan
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				abono_reparto_ant,
		errores,
		(
			SELECT
				errores
			FROM
				balances_pan
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
				balances_pan
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
		compras,
		mercancias,
		inv_act,
		mat_prima_utilizada,
		mano_obra,
		panaderos,
		gastos_fab,
		costo_produccion,
		utilidad_bruta,
		(
			SELECT
				utilidad_bruta
			FROM
				balances_pan
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				utilidad_bruta_ant,
		pan_comprado,
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
				balances_pan
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
		COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
				LEFT JOIN catalogo_companias ccec
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS iva,
		utilidad_neta + COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
				LEFT JOIN catalogo_companias ccec
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS utilidad_neta,
		COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
				LEFT JOIN catalogo_companias ccec
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE AND (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS iva_ant,
		(
			SELECT
				utilidad_neta
			FROM
				balances_pan
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		) + COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
				LEFT JOIN catalogo_companias ccec
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE AND (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS
				utilidad_neta_ant,
		errores_bancarios,
		mp_vtas,
		/*utilidad_pro*/
		CASE
			WHEN produccion_total > 0 THEN
				(utilidad_neta - ingresos_ext + COALESCE((
 					SELECT
 						ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
 					FROM
 						estado_cuenta
 						LEFT JOIN catalogo_companias ccec
							USING (num_cia)
 					WHERE
 						((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
 						AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
 						AND cod_mov IN (1, 16)
 				), 0)) / produccion_total
			ELSE
				0
		END
			AS
				util_pro,
		/*utilidad_pro_pc*/
		CASE
			WHEN produccion_total > 0 THEN
				(utilidad_neta - ingresos_ext + COALESCE((
 					SELECT
 						ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
 					FROM
 						estado_cuenta
 						LEFT JOIN catalogo_companias ccec
							USING (num_cia)
 					WHERE
 						((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
 						AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
 						AND cod_mov IN (1, 16)
 				), 0)) / (produccion_total + ABS(pan_comprado))
			ELSE
				0
		END
			AS
				util_pro_pc,
		mp_pro,
		gas_pro,
		(
			SELECT
				gas_pro
			FROM
				balances_pan
			WHERE
					num_cia = bal.num_cia
				AND
					anio = ' . date('Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '
				AND
					mes = ' . date('n', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '
			LIMIT 1
		)
			AS
				gas_pro_ant,
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
				termino,
		produccion_total,
		ganancia,
		porc_ganancia,
		faltante_pan,
		por_faltante_pan,
		CASE
			WHEN venta_puerta != 0 THEN
				faltante_pan / (venta_puerta - pastel_pedido) * 100
			ELSE
				0
		END
			AS
				fp_vp,
		devoluciones,
		rezago_ini,
		rezago_fin,
		var_rezago,
		var_rezago_anual,
		efectivo,
		CASE
			WHEN excedente_efectivo > 0 THEN
				round(excedente_efectivo::numeric, -3)
			ELSE
				0
		END
			AS
				excedente_efectivo,
		emp_afi
	FROM
			balances_pan bal
		LEFT JOIN
			catalogo_companias cc
				USING
					(
						num_cia
					)
		LEFT JOIN
			catalogo_administradores ca
				USING
					(
						idadministrador
					)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] < 0 ? 'idadministrador,' : '') . '
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
		'venta_puerta',
		'pastel_vitrina',
		'pastel_pedido',
		'pan_pedido',
		'venta_puerta_total',
		'bases',
		'barredura',
		'pastillaje',
		'abono_emp',
		'otros',
		'total_otros',
		'abono_reparto',
		'errores',
		'ventas_netas',
		'inv_ant',
		'inv_act',
		'mat_prima_utilizada',
		'mano_obra',
		'panaderos',
		'gastos_fab',
		'costo_produccion',
		'pan_comprado',
		'gastos_generales',
		'gastos_caja',
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
		'compras',
		'mercancias',
		'comisiones',
		'utilidad_bruta',
		'utilidad_bruta_ant',
		// 'iva',
		'utilidad_neta',
		'utilidad_neta_ant',
		'produccion_total',
		'ganancia',
		'porc_ganancia',
		'faltante_pan',
		'devoluciones',
		'rezago_ini',
		'rezago_fin',
		'var_rezago',
		'var_rezago_anual',
		'efectivo'
	),
	'colored_double3' => array(
		'mp_vtas',
		'util_pro',
		'util_pro_pc',
		'mp_pro',
	),
	'colored_double4' => array(
	),
	'colored_double5' => array(
		'gas_pro'
	)
);

/*
@
@ Crear objeto TemplatePower
@
*/

$tpl = new TemplatePower('./plantillas/bal/balance_pan.tpl');
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
		if (in_array($k, $Campos['text'])) {
			if (in_array($k, array('inicio', 'termino'))) {
				$v_short = strlen(trim($v)) > 18 ? substr($v, 0, 15) . '...' : $v;

				$tpl->assign($k, trim($v_short));
			}
			else {
				$tpl->assign($k, trim($v));
			}
		} else if (in_array($k, $Campos['month']))
			$tpl->assign($k, $MonthName[$v]['name']);
		else if (in_array($k, $Campos['integer']))
			$tpl->assign($k, $v != 0 ? $v : '&nbsp;');
		else if (in_array($k, $Campos['double2'])) {
			/*
			@@@ Claúsula: No mostrar los Ingresos Extraordinarios si esta habilitada la opción 'no_ing'
			*/
			if (isset($_REQUEST['no_ing']) && $k == 'ingresos_ext')
				$v = /*$bal['iva']*/0;
			else if (!isset($_REQUEST['no_ing']) && $k == 'ingresos_ext')
				$v += $bal['iva'];

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
			if (isset($_REQUEST['no_ing']) && $k == 'utilidad_neta')
				$v = $v - $bal['ingresos_ext'] - $bal['iva'];

			/*
			@@@ Claúsula: Restar Ingresos Extraordinarios a la Utilidad Neta si esta habilitada la opción 'no_ing'
			*/
			if (/*isset($_REQUEST['no_ing']) && */$k == 'utilidad_neta_ant')
				$v = $v - $bal['ingresos_ext_ant'] - $bal['iva_ant'];

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
	@ [22-Jul-2010] Errores Bancarios
	*/
	$tpl->assign('errores_bancarios', $bal['errores_bancarios'] > 0 ? $bal['errores_bancarios'] . ' Errores Bancarios' : '&nbsp;');

	/*
	@ [21-Oct-2010] Kilos de pastel pedido
	*/
	$tpl->assign('pastel_kilos', $bal['pastel_kilos'] != 0 ? '<span class="Orange">(' . number_format($bal['pastel_kilos'], 2, '.', ',') . ' Kgs)</span>' : '&nbsp;');

	/*
	@ Porcentaje correspondiente a la Venta en Puerta con respecto a las Ventas Netas
	*/
	$vp_p = $bal['venta_puerta_total'] != 0 ? ($bal['venta_puerta_total'] - $bal['errores']) * 100 / $bal['ventas_netas'] : 0;
	$tpl->assign('vpt_p', $vp_p != 0 ? '<span class="Orange">(' . number_format($vp_p, 2, '.', ',') . '%)</span>' : '&nbsp;');
	/*
	@ Comparativo de Venta en Puerta con el año anterior
	*/
	$vp_act = $bal['venta_puerta_total'] - $bal['errores'];
	$vp_ant = $bal['venta_puerta_total_ant'] - $bal['errores_ant'];
	$vp_c = $vp_ant > 0 ? abs($vp_act * 100 / $vp_ant - 100) : 0;
	$tpl->assign('vpt_c', $vp_act != $vp_ant && round($vp_c, 2) != 0 ? '<span class="' . ($vp_act > $vp_ant ? 'Blue">SUBIO ' : 'Red">BAJO') . number_format($vp_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Porcentaje correspondiente al Abono de Reparto con respecto a las Ventas Netas
	*/
	$ar_p = $bal['abono_reparto'] != 0 ? $bal['abono_reparto'] * 100 / $bal['ventas_netas'] : 0;
	$tpl->assign('ar_p', $ar_p != 0 ? '<span class="Orange">(' . number_format($ar_p, 2, '.', ',') . '%)</span>' : '&nbsp;');
	/*
	@ Comparativo del Abono de Reparto con el año anterior
	*/
	$ar_act = $bal['abono_reparto'];
	$ar_ant = $bal['abono_reparto_ant'];
	$ar_c = $ar_ant > 0 ? abs(($ar_act) * 100 / ($ar_ant) - 100) : 0;
	$tpl->assign('ar_c', $ar_act != $ar_ant && round($ar_c, 2) != 0 ? '<span class="' . ($ar_act > $ar_ant ? 'Blue">SUBIO ' : 'Red">BAJO ') . number_format($ar_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo del Ventas Netas con el año anterior
	*/
	$vn_act = $bal['ventas_netas'];
	$vn_ant = $bal['ventas_netas_ant'];
	$vn_c = $vn_ant > 0 ? abs(($vn_act) * 100 / ($vn_ant) - 100) : 0;
	$tpl->assign('vn_c', $vn_act != $vn_ant && round($vn_c, 2) != 0 ? '<span class="' . ($vn_act > $vn_ant ? 'Blue">SUBIO ' : 'Red">BAJO ') . number_format($vn_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Utilidad Bruta con el año anterior
	*/
	$ub_act = $bal['utilidad_bruta'];
	$ub_ant = $bal['utilidad_bruta_ant'];
	$ub_c = $ub_ant > 0 ? abs(($ub_act) * 100 / ($ub_ant) - 100) : 0;
	$tpl->assign('ub_c', $ub_act != $ub_ant/* && $ub_c <= 100*/ && round($ub_c, 2) != 0 ? '<span class="' . ($ub_act > $ub_ant ? 'Blue">MEJORO ' : 'Red">EMPEORO ') . number_format($ub_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Utilidad Neta con el año anterior
	*/
	$un_act = $bal['utilidad_neta'] - $bal['ingresos_ext'] - $bal['iva'];
	$un_ant = $bal['utilidad_neta_ant'] - $bal['ingresos_ext_ant'] - $bal['iva_ant'];
	$un_c = $un_ant > 0 ? abs(($un_act) * 100 / ($un_ant) - 100) : 0;
	$tpl->assign('un_c', $un_act != $un_ant/* && $un_c <= 100*/ && round($un_c, 2) != 0 ? '<span class="' . ($un_act > $un_ant ? 'Blue">MEJORO ' : 'Red">EMPEORO ') . number_format($un_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Gas / Producción con el mes anterior
	*/
	$dif_act = $bal['gas_pro'];
	$dif_ant = $bal['gas_pro_ant'];
	$dif_gas = $dif_act - $dif_ant;
	$tpl->assign('dif_gas', $dif_gas != 0 ? '<span class="' . ($dif_gas < 0 ? 'Blue">Bajo' : 'Red">Subio') . '</span>' : '');

	/*
	@ [20-May-2013] % Faltante de pan
	*/
	$tpl->assign('por_faltante_pan', $bal['por_faltante_pan'] != 0 ? '<span style="font-size:6pt;" class="' . ($bal['por_faltante_pan'] > 0 ? 'Blue' : 'Red') . '">(' . number_format($bal['por_faltante_pan'], 2) . ($bal['por_faltante_pan'] > 0 ? ' Sobrante' : ' Faltante') . ')</span>&nbsp;&nbsp;' : '');

	$tpl->assign('por_devolucion', $bal['devoluciones'] > 0 && $bal['produccion_total'] > 0 ? '<span style="font-size:6pt;Red">(' . number_format($bal['devoluciones'] * 100 / $bal['produccion_total'], 2) . ')</span>&nbsp;&nbsp;' : '');

	$por_devolucion = $bal['produccion_total'] != 0 ? $bal['devoluciones'] * 100 / $bal['produccion_total'] : 0;

	/*
	@ [27-Feb-2014] % Faltante total general
	*/
	$tpl->assign('ftg', $bal['por_faltante_pan'] != 0 || $por_devolucion != 0 ? '<span style="font-size:6pt; float:left; width:15mm;" class="Orange">(FTG:' . number_format($bal['por_faltante_pan'] - $por_devolucion, 2) . ')</span>&nbsp;&nbsp;' : '<span style="width:20mm;">&nbsp;</span>');

	/*
	@ [07-Dic-2015] % Faltante de pan / venta en puerta
	*/
	$tpl->assign('fp_vp', $bal['fp_vp'] != 0 ? '<span style="font-size:6pt; float:left; width:15mm;" class="Red">(FP/(VP-PP):' . number_format($bal['fp_vp'], 2) . ')</span>' : '');

	/*
	@ [11-Mar-2014] % Mano de obra / producción
	*/

	$por_mano_obra = $bal['produccion_total'] > 0 ? $bal['mano_obra'] / $bal['produccion_total'] * 100 : 0;

	$tpl->assign('por_mano_obra', $por_mano_obra > 0 ? '<span class="Orange">(' . number_format($por_mano_obra, 2) . '%)<span>' : '&nbsp');

	/*
	@ [11-Mar-2014] % Panaderos / producción
	*/

	$por_panaderos = $bal['produccion_total'] > 0 ? $bal['panaderos'] / $bal['produccion_total'] * 100 : 0;

	$tpl->assign('por_panaderos', $por_panaderos > 0 ? '<span class="Orange">(' . number_format($por_panaderos, 2) . '%)<span>' : '&nbsp');

	/*
	@ [11-Mar-2014] % mano de obra + % panaderos
	*/

	$por_total_mano_obra = $por_mano_obra + $por_panaderos;

	$tpl->assign('por_total_mano_obra', $por_total_mano_obra > 0 ? '<span class="Red">(MOT:' . number_format($por_total_mano_obra, 2) . '%)<span>' : '&nbsp');

	/*
	@ Variación del Rezago de Expendios
	*/
	$tpl->assign('var', $bal['var_rezago'] != 0 ? '<span class="' . ($bal['var_rezago'] < 0 ? 'Blue">Bajo' : 'Red">Subio') . '</span>' : 'Igual');

	/*
	@ Variación del Rezago de Expendios con respecto al rezago de inicio de año
	*/
	$tpl->assign('var_anual', $bal['var_rezago_anual'] != 0 ? '<span class="' . ($bal['var_rezago_anual'] < 0 ? 'Blue">Bajo' : 'Red">Subio') . '</span>' : 'Igual');

	/*
	@ Promedios de Consumo / Producción
	*/
	$sql = '
		SELECT
			cod_turno,
			con_pro,
			pro,
			bultos
		FROM
			consumo_produccion
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
		ORDER BY
			cod_turno
	';
	$con_pro = $db->query($sql);

	$bultos_turno = array(
		1 => 0,
		2 => 0,
		3 => 0,
		4 => 0,
		8 => 0,
		9 => 0,
		10 => 0
	);

	if ($con_pro) {
		foreach ($con_pro as $cp) {
			$tpl->assign('hoja1.con_pro_' . $cp['cod_turno'], number_format($cp['con_pro'], 3));
			$tpl->assign('hoja1.pro_' . $cp['cod_turno'], number_format($cp['pro'], 0));
			$tpl->assign('hoja1.bul_' . $cp['cod_turno'], $cp['bultos'] != 0 ? number_format($cp['bultos'], 2) : '&nbsp;');

			$bultos_turno[$cp['cod_turno']] = $cp['bultos'];
		}
	}

	/*
	@ Promedios de Consumo / Producción año anterior
	*/
	$sql = '
		SELECT
			cod_turno,
			con_pro,
			pro,
			bultos
		FROM
			consumo_produccion
		WHERE
				num_cia = ' . $bal['num_cia'] . '
			AND
				anio = ' . $anyo . ' - 1
			AND
				mes = ' . $mes . '
		ORDER BY
			cod_turno
	';
	$con_pro_ant = $db->query($sql);

	$bultos_turno = array(
		1 => 0,
		2 => 0,
		3 => 0,
		4 => 0,
		8 => 0,
		9 => 0,
		10 => 0
	);

	if ($con_pro_ant) {
		foreach ($con_pro_ant as $cp_ant) {
			$tpl->assign('hoja1.con_pro_ant_' . $cp_ant['cod_turno'], number_format($cp_ant['con_pro'], 3));
			$tpl->assign('hoja1.pro_ant_' . $cp_ant['cod_turno'], number_format($cp_ant['pro'], 0));
			$tpl->assign('hoja1.bul_ant_' . $cp_ant['cod_turno'], $cp_ant['bultos'] != 0 ? number_format($cp_ant['bultos'], 2) : '&nbsp;');

			$bultos_turno[$cp_ant['cod_turno']] = $cp_ant['bultos'];
		}
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
		$imss = 0;
		foreach ($reservas as $r) {
			$tpl->newBlock('reserva');
			$tpl->assign('reserva', $r['descripcion']);
			$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
			$total_reservas += $r['importe'];

			if ($r['cod_reserva'] == 4) {
				$imss = $r['importe'];
			}
		}
		$tpl->assign('hoja1.total_reservas', number_format($total_reservas, 2, '.', ','));
		$tpl->assign('hoja1.asegurados', $bal['emp_afi'] > 0 ? $bal['emp_afi'] : '&nbsp;');
		$tpl->assign('hoja1.costo_emp', $bal['emp_afi'] > 0 ? number_format($imss / $bal['emp_afi'], 2) : '&nbsp;');
		$tpl->gotoBlock('hoja1');
	}

	/*
	@ Rendimientos de Harina
	*/

	$rendimientos = array(
		1 => array(
			'rendimiento'     => 0,
			'rendimiento_ant' => 0,
			'diferencia'      => 0,
			'total'           => 0
		),
		2 => array(
			'rendimiento'     => 0,
			'rendimiento_ant' => 0,
			'diferencia'      => 0,
			'total'           => 0
		),
		3 => array(
			'rendimiento'     => 0,
			'rendimiento_ant' => 0,
			'diferencia'      => 0,
			'total'           => 0
		),
		4 => array(
			'rendimiento'     => 0,
			'rendimiento_ant' => 0,
			'diferencia'      => 0,
			'total'           => 0
		)
	);

	$sql = '
		SELECT
			codturno,
			SUM(total_produccion)
				AS produccion,
			SUM(cantidad) / 44
				AS consumo
		FROM
			total_produccion tp
			LEFT JOIN mov_inv_real mov
				ON (
					mov.num_cia = tp.numcia
					AND fecha = fecha_total
					AND codmp = 1
					AND cod_turno = codturno
					AND tipo_mov = TRUE
				)
		WHERE
			tp.numcia = ' . $bal['num_cia'] . '
			AND fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			AND cod_turno IN (1, 2, 3, 4)
		GROUP BY
			codturno
		ORDER BY
			codturno
	';
	$tmp = $db->query($sql);

	if ($tmp) {
		foreach ($tmp as $t) {
			$rendimientos[$t['codturno']]['rendimiento'] = $t['consumo'] != 0 ? $t['produccion'] / $t['consumo'] : 0;
			$rendimientos[$t['codturno']]['diferencia'] = $rendimientos[$t['codturno']]['rendimiento'] > 0 && $rendimientos[$t['codturno']]['rendimiento_ant'] > 0 ? $rendimientos[$t['codturno']]['rendimiento'] - $rendimientos[$t['codturno']]['rendimiento_ant'] : 0;
		}
	}

	/*
	@ [15-May-2013] Rendimientos de Harina del mes anterior
	*/
	$sql = '
		SELECT
			codturno,
			SUM(total_produccion)
				AS produccion,
			SUM(cantidad) / 44
				AS consumo
		FROM
			total_produccion tp
			LEFT JOIN mov_inv_real mov
				ON (
					mov.num_cia = tp.numcia
					AND fecha = fecha_total
					AND codmp = 1
					AND cod_turno = codturno
					AND tipo_mov = TRUE
				)
		WHERE
			tp.numcia = ' . $bal['num_cia'] . '
			AND fecha_total BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo)) . '\'
			AND cod_turno IN (1, 2, 3, 4)
		GROUP BY
			codturno
		ORDER BY
			codturno
	';
	$tmp = $db->query($sql);

	if ($tmp) {
		foreach ($tmp as $t) {
			$rendimientos[$t['codturno']]['rendimiento_ant'] = $t['consumo'] != 0 ? $t['produccion'] / $t['consumo'] : 0;
			$rendimientos[$t['codturno']]['diferencia'] = $rendimientos[$t['codturno']]['rendimiento'] > 0 && $rendimientos[$t['codturno']]['rendimiento_ant'] > 0 ? $rendimientos[$t['codturno']]['rendimiento'] - $rendimientos[$t['codturno']]['rendimiento_ant'] : 0;
		}
	}

	if ($rendimientos) {
		$dif_ren = 0;
		$total_ren = 0;

		foreach ($rendimientos as $turno => $r) {
			switch ($turno) {
				case 1:
					$nombre_turno = 'FRANCES DE DIA';
					break;
				case 2:
					$nombre_turno = 'FRANCES DE NOCHE';
					break;
				case 3:
					$nombre_turno = 'BIZCOCHERO';
					break;
				case 4:
					$nombre_turno = 'REPOSTERO';
					break;
			}
			if ($r['rendimiento'] != 0 || $r['rendimiento_ant'] != 0) {
				$tpl->newBlock('rendimiento');
				$tpl->assign('turno', $nombre_turno);
				$tpl->assign('rendimiento', $r['rendimiento'] != 0 ? number_format($r['rendimiento'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('rendimiento_ant', $r['rendimiento_ant'] != 0 ? number_format($r['rendimiento_ant'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('dif', $r['diferencia'] != 0 ? '<span class="' . ($r['diferencia'] > 0 ? 'Blue' : 'Red') . '">' . number_format($r['diferencia'], 2, '.', ',') . '</span>' : '&nbsp;');
				$tpl->assign('total', $r['diferencia'] != 0 && $bultos_turno[$turno] != 0 ? '<span class="' . ($r['diferencia'] * $bultos_turno[$turno] > 0 ? 'Blue' : 'Red') . '">' . number_format(round($r['diferencia'], 2) * round($bultos_turno[$turno], 2), 2, '.', ',') . '</span>' : '&nbsp;');

				$dif_ren += $r['diferencia'];
				$total_ren += round($r['diferencia'], 2) * round($bultos_turno[$turno], 2);

				$tpl->assign('hoja1.dif_ren', $dif_ren != 0 ? '<span class="' . ($r['diferencia'] > 0 ? 'Blue' : 'Red') . '">' . number_format($dif_ren, 2) . '</pre>' : '&nbsp;');
				$tpl->assign('hoja1.total_ren', $total_ren != 0 && $bal['produccion_total'] != 0 ? '<span style="float:left; font-size:6pt;" class="' . ($r['diferencia'] > 0 ? 'Blue' : 'Red') . '">(%' . (number_format(abs($total_ren) * 100 / $bal['produccion_total'], 4)) . ')</span><span class="' . ($r['diferencia'] > 0 ? 'Blue' : 'Red') . '">' . number_format($total_ren, 2) . '</span>' : '&nbsp;');
			}
		}
		$tpl->gotoBlock('hoja1');
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
	@ [09-Nov-2009] Excedente de Efectivo
	*/
	if ($bal['excedente_efectivo'] > 0) {
		$tpl->newBlock('excedente');
		$tpl->assign('excedente_efectivo', number_format($bal['excedente_efectivo'], 2, '.', ','));
		$tpl->gotoBlock('hoja1');
	}

	/*
	@ Estadísticas Año Anterior
	*/
	$sql = '
		SELECT
			mes,
			utilidad_neta + COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE AND (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS util,
			COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE AND (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS iva,
			ingresos_ext
				AS ing,
			venta
				AS vta,
			pastel_pedido
				AS pasteles,
			por_efe,
			reparto
				AS abono,
			produccion
				AS prod,
			mp_pro,
			bultos,
			clientes,
			por_faltante_pan
				AS por_fal
		FROM
			historico
			LEFT JOIN balances_pan
				USING (num_cia, anio, mes)
		WHERE
			num_cia = ' . $bal['num_cia'] . '
			AND anio = ' . ($anyo - 1) . '
		ORDER BY
			mes
	';
	$est_ant = $db->query($sql);

	$tpl->assign('anyo_ant', $anyo - 1);

	if ($est_ant) {
		$totales = array(
			'vta_ant' => 0,
			'abono_ant' => 0,
			'prod_ant' => 0,
			'bultos_ant' => 0,
			'clientes_ant' => 0
		);

		foreach ($est_ant as $est) {
			/*
			@ Utilidades Año Anterior
			@
			@ [6-Ago-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_ant', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_ant', $est['util'] != 0 ? number_format($est['util'] - (isset($_REQUEST['no_ing']) ? $est['ing'] + $est['iva'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_ant', $est['ing'] + $est['iva'] != 0 && !isset($_REQUEST['no_ing']) ? '(' . number_format($est['ing'] + $est['iva'], 2, '.', ',') . ')' : /*($est['iva'] != 0 ? '(' . number_format($est['iva'], 2, '.', ',') . ')' : '&nbsp;')*/'&nbsp;');

			/*
			@ Estadísticas Año Anterior
			*/
			$tpl->assign('vta_ant_' . $est['mes'], $est['vta'] != 0 ? substr(number_format($est['vta'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('por_efe_ant_' . $est['mes'], $est['por_efe'] != 0 ? substr(number_format($est['por_efe'], 2, '.', ','), 1) : '&nbsp;');
			$tpl->assign('abono_ant_' . $est['mes'], $est['abono'] != 0 ? substr(number_format($est['abono'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('prod_ant_' . $est['mes'], $est['prod'] != 0 ? substr(number_format($est['prod'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('mp_pro_ant_' . $est['mes'], $est['mp_pro'] != 0 ? substr(number_format($est['mp_pro'], 3, '.', ','), 1) : '&nbsp;');
			$tpl->assign('bultos_ant_' . $est['mes'], $est['bultos'] != 0 ? substr(number_format($est['bultos'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('clientes_ant_' . $est['mes'], $est['clientes'] != 0 ? number_format($est['clientes']) : '&nbsp;');
			$tpl->assign('por_fal_ant_' . $est['mes'], $est['por_fal'] != 0 ? '<span class="' . ($est['por_fal'] > 0 ? 'Blue' : 'Red') . '">' . number_format($est['por_fal'], 2) . '</span>' : '&nbsp;');

			/*
			@ [21-Oct-2010] A la venta restar el pastel pedido para promedio de pan vendido por cliente
			*/
			@$prom = ($est['vta'] - $est['pasteles']) / $est['clientes'];

			$tpl->assign('prom_ant_' . $est['mes'], $prom != 0 ? number_format($prom, 2, '.', ',') : '&nbsp;');

			$totales['vta_ant'] += $est['vta'];
			$totales['abono_ant'] += $est['abono'];
			$totales['prod_ant'] += $est['prod'];
			$totales['bultos_ant'] += $est['bultos'];
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
			utilidad_neta + COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE AND (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS util,
			COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = historico.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE AND (\'01/\' || historico.mes || \'/\' || historico.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS iva,
			ingresos_ext
				AS ing,
			venta
				AS vta,
			pastel_pedido
				AS pasteles,
			por_efe,
			reparto
				AS abono,
			produccion
				AS prod,
			mp_pro,
			bultos,
			clientes,
			por_faltante_pan
				AS por_fal
		FROM
			historico
			LEFT JOIN balances_pan
				USING (num_cia, anio, mes)
		WHERE
			num_cia = ' . $bal['num_cia'] . '
			AND anio = ' . $anyo . '
			AND mes <= ' . $mes . '
		ORDER BY
			mes
	';
	$est_act = $db->query($sql);

	$tpl->assign('anyo_act', $anyo);

	if ($est_act) {
		$totales = array(
			'vta' => 0,
			'abono' => 0,
			'prod' => 0,
			'bultos' => 0,
			'clientes' => 0
		);

		foreach ($est_act as $est) {
			/*
			@ Utilidades Año Actual
			@
			@ [6-Ago-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_act', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_act', $est['util'] != 0 ? number_format($est['util'] - (isset($_REQUEST['no_ing']) ? $est['ing'] + $est['iva'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_act', $est['ing'] + $est['iva'] != 0 && !isset($_REQUEST['no_ing']) ? '(' . number_format($est['ing'] + $est['iva'], 2, '.', ',') . ')' : /*($est['iva'] != 0 ? '(' . number_format($est['iva'], 2, '.', ',') . ')' : '&nbsp;')*/'&nbsp;');

			/*
			@ Estadísticas Año Actual
			*/
			$tpl->assign('vta_' . $est['mes'], $est['vta'] != 0 ? substr(number_format($est['vta'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('por_efe_' . $est['mes'], $est['por_efe'] != 0 ? substr(number_format($est['por_efe'], 2, '.', ','), 1) : '&nbsp;');
			$tpl->assign('abono_' . $est['mes'], $est['abono'] != 0 ? substr(number_format($est['abono'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('prod_' . $est['mes'], $est['prod'] != 0 ? substr(number_format($est['prod'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('mp_pro_' . $est['mes'], $est['mp_pro'] != 0 ? substr(number_format($est['mp_pro'], 3, '.', ','), 1) : '&nbsp;');
			$tpl->assign('bultos_' . $est['mes'], $est['bultos'] != 0 ? substr(number_format($est['bultos'], 2, '.', ','), 0, -3) : '&nbsp;');
			$tpl->assign('clientes_' . $est['mes'], $est['clientes'] != 0 ? number_format($est['clientes']) : '&nbsp;');
			$tpl->assign('por_fal_' . $est['mes'], $est['por_fal'] != 0 ? '<span class="' . ($est['por_fal'] > 0 ? 'Blue' : 'Red') . '">' . number_format($est['por_fal'], 2) . '</span>' : '&nbsp;');

			/*
			@ [21-Oct-2010] A la venta restar el pastel pedido para promedio de pan vendido por cliente
			*/
			@$prom = ($est['vta'] - $est['pasteles']) / $est['clientes'];

			$tpl->assign('prom_' . $est['mes'], $prom != 0 ? number_format($prom, 2, '.', ',') : '&nbsp;');

			$totales['vta'] += $est['vta'];
			$totales['abono'] += $est['abono'];
			$totales['prod'] += $est['prod'];
			$totales['bultos'] += $est['bultos'];
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
				SUM(importe)
					AS importe
			FROM
				(
					SELECT
						codgastos
							AS cod,
						descripcion
							AS desc,
						codigo_edo_resultados
							AS tipo,
						EXTRACT(YEAR FROM fecha)
							AS anyo,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						orden,
						ROUND(SUM(importe)::NUMERIC, 2)
							AS importe
					FROM
						movimiento_gastos mg
						LEFT JOIN catalogo_gastos cg
							USING (codgastos)
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND (
							fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . $fecha2 . '\'
							OR fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
						)
						AND codigo_edo_resultados IN (1, 2)
						/*
						@@@ [07-Ene-2017] Omitir códigos 9 LECHE y 76 VINOS Y LICORES
						*/
						AND codgastos NOT IN (141, 9, 76)
						AND ROUND(importe::NUMERIC, 2) <> 0

						/*
						@@@
						@@@ [04-Sep-2008]
						@@@ Claúsula: Omitir todos los pagos hechos para otras compañías
						@@@ [03-Oct-2008]
						@@@ Modificación: En el conjunto de campos se excluyo "cuenta" y "folio" y se agregaron los campos "codgastos" y "fecha"
						@@@
						*/

						AND (num_cia, folio, fecha, codgastos, importe) NOT IN (
							SELECT
								num_cia,
								folio,
								fecha,
								codgastos,
								importe
							FROM
								pagos_otras_cias
								LEFT JOIN cheques
									USING (num_cia, folio, cuenta, fecha)
							WHERE
								num_cia = ' . $bal['num_cia'] . '
								AND (
									fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . $fecha2 . '\'
									OR fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
								)
								AND fecha_cancelacion IS NULL
						)

						/*
						@@@
						@@@ Termina claúsula
						@@@
						*/

						/* [04-Ago-2016] Para la compañía 132 no incluir compras de gas */
						AND idmovimiento_gastos NOT IN (
							SELECT
								idmovimiento_gastos
							FROM
								movimiento_gastos
							WHERE
								num_cia = 132
								AND fecha <= \'31/07/2016\'
								AND (
									fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . $fecha2 . '\'
									OR fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
								)
								AND codgastos = 90
								AND concepto != \'DIFERENCIA INVENTARIO\'
						)

					GROUP BY
						codgastos,
						descripcion,
						tipo,
						anyo,
						mes,
						orden

					UNION

					-- [03-Oct-2016] Agregados gastos en reserva
					SELECT
						codgastos
							AS cod,
						descripcion
							AS desc,
						codigo_edo_resultados
							AS tipo,
						anio
							AS anyo,
						mes,
						orden,
						ROUND(SUM(importe)::NUMERIC, 2)
							AS importe
					FROM
						reserva_gastos rg
						LEFT JOIN catalogo_gastos cg
							USING (codgastos)
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND (
							(anio = ' . $anyo . ' AND mes = ' . $mes . ')
							OR (anio = ' . ($mes == 1 ? $anyo - 1 : $anyo) . ' AND mes = ' . ($mes == 1 ? 12 : $mes - 1) . ')
							OR (anio = ' . ($anyo - 1) . ' AND mes = ' . $mes . ')
						)
						AND codigo_edo_resultados IN (1, 2)
						AND ROUND(importe::NUMERIC, 2) <> 0
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
							AS cod,
						descripcion
							AS desc,
						codigo_edo_resultados
							AS tipo,
						EXTRACT(YEAR FROM fecha)
							AS anyo,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						orden,
						ROUND(SUM(importe)::NUMERIC, 2)
							AS importe
					FROM
						pagos_otras_cias
						LEFT JOIN cheques
							USING (num_cia, folio, cuenta, fecha)
						LEFT JOIN catalogo_gastos
							USING (codgastos)
					WHERE
						num_cia_aplica = ' . $bal['num_cia'] . '
						AND (
							fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . $fecha2 . '\'
							OR fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
						)
						AND codigo_edo_resultados IN (1, 2)
						AND codgastos NOT IN (141)
						AND ROUND(importe::NUMERIC, 2) <> 0
						AND fecha_cancelacion IS NULL
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
					AS result_gastos
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
				AS cod,
			descripcion
				AS desc,
			3
				AS tipo,
			EXTRACT(YEAR FROM fecha)
				AS anyo,
			EXTRACT(MONTH FROM fecha)
				AS mes,
			1
				AS orden,
			ROUND(SUM(
				CASE
					WHEN tipo_mov = TRUE THEN
						-importe
					ELSE
						importe
				END
			)::NUMERIC, 2)
				AS importe
		FROM
			gastos_caja gc
			LEFT JOIN catalogo_gastos_caja cgc
				ON (cgc.id = gc.cod_gastos)
		WHERE
			num_cia = ' . $bal['num_cia'] . '
			AND (
				fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anyo)) . '\' AND \'' . $fecha2 . '\'
				OR fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo - 1)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo - 1)) . '\'
			)
			AND clave_balance = TRUE
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
			mes DESC,
			anyo DESC
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

						if ((mktime(0, 0, 0, $mes_gasto, 1, $anyo_gasto) >= mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $mes_gasto, 1, $anyo_gasto) <= mktime(0, 0, 0, 2, 1, 2014)) && !$imp_ide_ins[$col]) {
							/*
							@ [03-Feb-2010] A partir del año 2010 el porcentaje IDE pasa a ser 3% en lugar de 2%
							*/
							$por_ide = $anyo < 2010 ? 0.02 : 0.03;
							$sql = '
								SELECT
									round(
										(sum(importe)::numeric - 25000) * ' . $por_ide . ' ,
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
			@@@ Claúsula: Todos los movimientos con código 84 'ASEGURADORAS' y con fecha mayor al 1 de Enero de 2014 omitirlo
			*/
			if ($tipo != 3 && $cod == 84 && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) >= mktime(0, 0, 0, 1, 1, 2014))
				continue;

			/*
			@@@ Claúsula: Sumar al importe del código 182 el importe del movimiento con código 78 'IMPUESTO IDE' del estado de cuenta
			@@@ correspondiente a la compañía, año y mes a partir del 1 de Julio de 2008
			@@@
			@@@ [5-Ago-2008] Modificación de la claúsula, el impuesto IDE ahora se calcula a partir de todos los depósitos conciliados en el mes con código
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
			else if ($cod == 182 && (mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) < mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) > mktime(0, 0, 0, 2, 1, 2014))) {
				$imp_ide_ins[$col] = TRUE;
			}

			/*
			@
			*/
			$tpl->assign('row_gasto.anyo' . $col, $g['anyo']);
			$tpl->assign('row_gasto.mes' . $col, $g['mes']);

			$por = '';

			/*
			@ [9-Jun-2013] Para los códigos de gasto 1 y 2 compararlos con la venta neta
			@ [10-Jun-2013] Agregados los códigos 7, 52, 179, 180, 181, 183, 187, 189, 190
			*/
			if ($g['tipo'] == 2 && in_array($g['cod'], array(1, 2, 7, 52, 179, 180, 181, 182, 183, 187, 189, 190))) {
				$sql = '
					SELECT
						ventas_netas
					FROM
						balances_pan
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND anio = ' . $g['anyo'] . '
						AND mes = ' . $g['mes'] . '
				';

				$tmp = $db->query($sql);

				if ($tmp) {
					$gas_vta = $tmp[0]['ventas_netas'] != 0 ? $g['importe'] * 100 / $tmp[0]['ventas_netas'] : 0;

					$por = '<span style="float:left; font-size:6pt;">(' . number_format($gas_vta, 2) . '%)</span>';
				}
			}

			/*
			@ [9-Jun-2013] Para los códigos de gasto 49 compararlo contra los abonos de los expendios seleccionados
			*/
			if ($g['tipo'] == 2 && in_array($g['cod'], array(49))) {
				$sql = '
					SELECT
						SUM(abono)
							AS abonos
					FROM
						mov_expendios mov
						LEFT JOIN catalogo_expendios ce
							ON (
								ce.num_expendio = mov.num_expendio
								AND ce.nombre = mov.nombre_expendio
							)
					WHERE
						mov.num_cia = ' . $bal['num_cia'] . '
						AND mov.fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $g['mes'], 1, $g['anyo'])) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $g['mes'] + 1, 0, $g['anyo'])) . '\'
						AND ce.paga_renta = TRUE
				';

				$tmp = $db->query($sql);

				if ($tmp[0]['abonos'] != 0) {
					$gas_exp = $g['importe'] * 100 / $tmp[0]['abonos'];

					$por = '<span style="float:left; font-size:6pt;">(' . number_format($gas_exp, 2) . '%)</span>';
				}
			}

			/*
			@ [10-Jun-2013] Para el codigo 90 compararlo contra produccion
			*/
			if ($g['tipo'] == 1/* && in_array($g['cod'], array(90))*/) {
				$sql = '
					SELECT
						produccion_total
					FROM
						balances_pan
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND anio = ' . $g['anyo'] . '
						AND mes = ' . $g['mes'] . '
				';

				$tmp = $db->query($sql);

				if ($tmp) {
					$gas_pro = $tmp[0]['produccion_total'] != 0 ? $g['importe'] * 100 / $tmp[0]['produccion_total'] : 0;

					$por = '<span style="float:left; font-size:6pt;">(' . number_format($gas_pro, 2) . '%)</span>';
				}
			}

			$tpl->assign('row_gasto.importe' . $col, $por . number_format($g['importe'], 2, '.', ','));

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
			venta_puerta,
			bases,
			barredura,
			pastillaje,
			abono_emp,
			otros,
			total_otros,
			abono_reparto,
			errores,
			ventas_netas,
			inv_ant,
			compras,
			mercancias,
			inv_act,
			mat_prima_utilizada,
			mano_obra,
			panaderos,
			gastos_fab,
			costo_produccion,
			utilidad_bruta,
			pan_comprado,
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
			COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = balances_pan.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = balances_pan.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::DATE AND (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS iva,
			utilidad_neta + COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias ccec
						USING (num_cia)
				WHERE
					((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = balances_pan.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = balances_pan.num_cia AND tipo_cia = 2))
					AND fecha BETWEEN (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::DATE AND (\'01/\' || balances_pan.mes || \'/\' || balances_pan.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS utilidad_neta,
			mp_vtas,
			utilidad_pro
				AS
					util_pro,
			mp_pro,
			gas_pro,
			produccion_total,
			ganancia,
			porc_ganancia,
			faltante_pan,
			devoluciones,
			rezago_ini,
			rezago_fin,
			var_rezago,
			var_rezago_anual,
			efectivo,
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
			balances_pan
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
			else if (in_array($k, $Campos['double2'])) {
				/*
				@@@ Claúsula: No mostrar los Ingresos Extraordinarios si esta habilitada la opción 'no_ing'
				*/
				if (isset($_REQUEST['no_ing']) && $k == 'ingresos_ext')
					$v = 0;
				else if (!isset($_REQUEST['no_ing']) && $k == 'ingresos_ext')
					$v += $h['iva'];

				$tpl->assign($k . $col, round($v, 2) != 0 ? number_format($v, 2, '.', ',') : '&nbsp;');
			}
			else if (in_array($k, $Campos['double3']))
				$tpl->assign($k . $col, round($v, 3) != 0 ? number_format($v, 3, '.', ',') : '&nbsp;');
			else if (in_array($k, $Campos['double4']))
				$tpl->assign($k . $col, round($v, 4) != 0 ? number_format($v, 4, '.', ',') : '&nbsp;');
			else if (in_array($k, $Campos['colored_double2'])) {
				/*
				@@@ Claúsula: Restar Ingresos Extraordinarios a la Utilidad Neta si esta habilitada la opción 'no_ing'
				*/
				if (isset($_REQUEST['no_ing']) && $k == 'utilidad_neta')
					$v = $v - $h['ingresos_ext'] - $h['iva'];

				$tpl->assign($k . $col, round($v, 2) != 0 ? '<span class="' . ($v > 0 ? 'Blue' : 'Red') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
			}
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
							num_cia = poc.num_cia_aplica
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
			AND fecha_cancelacion IS NULL
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

	/*
	@
	@ Hoja 5 : Listado de pagos fijps
	@
	*/

	$sql = "
		SELECT
			fecha_ini,
			fecha_fin,
			concepto,
			importe,
			importe * (EXTRACT(MONTHS FROM AGE(fecha_fin, '01/{$mes}/{$anyo}'::DATE)) + 1)
				AS acumulado,
			EXTRACT(MONTHS FROM AGE(fecha_fin, NOW()::DATE)) + 1
				AS meses_restantes
		FROM
			pagos_anticipados
		WHERE
			num_cia = {$bal['num_cia']}
			AND '01/{$mes}/{$anyo}'::DATE BETWEEN fecha_ini AND fecha_fin
		ORDER BY
			fecha_ini
	";

	$result = $db->query($sql);

	if ($result)
	{
		$tpl->newBlock('hoja5');
		$tpl->assign('num_cia', $bal['num_cia']);
		$tpl->assign('mes', $MonthName[$mes]['name']);
		$tpl->assign('anyo', $anyo);

		$hojas++;

		$total = 0;

		foreach ($result as $row)
		{
			$tpl->newBlock('row_pago');
			$tpl->assign('fecha_ini', $row['fecha_ini']);
			$tpl->assign('fecha_fin', $row['fecha_fin']);
			$tpl->assign('concepto', $row['concepto']);
			$tpl->assign('importe', number_format($row['importe'], 2));
			$tpl->assign('acumulado', number_format($row['acumulado'], 2));
			$tpl->assign('meses_restantes', number_format($row['meses_restantes']));

			$total += $row['importe'];

			$tpl->assign('hoja5.total', number_format($total, 2));
		}
	}

	if ($hojas % 2 != 0)
		$tpl->newBlock('hoja_blanca');
}

$tpl->printToScreen();
?>
