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
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@                                              @
@ Programa Principal de Balance de Rosticerias @
@                                              @
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
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
@@@ Largo estándar del área imprimible de una hoja de balance de rosticeria (milímetros)
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
		venta,
		(
			SELECT
				venta
			FROM
				balances_ros
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		)
			AS
				venta_ant,
		otros,
		ventas_netas,
		(
			SELECT
				ventas_netas
			FROM
				balances_ros
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
		gastos_fab,
		costo_produccion,
		utilidad_bruta,
		(
			SELECT
				utilidad_bruta
			FROM
				balances_ros
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
		gastos_generales,
		gastos_caja,
		comisiones,
		reserva_aguinaldos
			AS
				reservas,
		gastos_otras_cias,
		total_gastos,
		ingresos_ext,
		(
			SELECT
				ingresos_ext
			FROM
				balances_ros
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
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2) * -1
			FROM
				estado_cuenta
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS iva,
		utilidad_neta - COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS utilidad_neta,
		(
			SELECT
				utilidad_neta
			FROM
				balances_ros
			WHERE
					num_cia = bal.num_cia
				AND
					anio = bal.anio - 1
				AND
					mes = bal.mes
			LIMIT 1
		) - COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE AND (\'01/\' || bal.mes || \'/\' || (bal.anio - 1))::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
				AND cod_mov IN (1, 16)
		), 0)
			AS
				utilidad_neta_ant,
		efectivo,
		mp_vtas,
		pollos_vendidos,
		pescuezos
			AS
				pescuezos_vendidos,
		p_pavo,
		peso_normal,
		peso_chico,
		peso_grande
	FROM
			balances_ros bal
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
	'integer_format' => array(
		'pollos_vendidos',
		'pescuezos_vendidos',
		'p_pavo'
	),
	'double2' => array(
		'venta',
		'otros',
		'ventas_netas',
		'inv_ant',
		'inv_act',
		'mat_prima_utilizada',
		'gastos_fab',
		'costo_produccion',
		'gastos_generales',
		'gastos_caja',
		'reservas',
		'gastos_otras_cias',
		'total_gastos',
		'ingresos_ext',
		'mp_vtas',
		'efectivo'
	),
	'double3' => array(
		'peso_normal',
		'peso_chico',
		'peso_grande'
	),
	'double4' => array(
	),
	'colored_double2' => array(
		'compras',
		'mercancias',
		'comisiones',
		'utilidad_bruta',
		'utilidad_bruta_ant',
		'iva',
		'utilidad_neta',
		'utilidad_neta_ant',
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

$tpl = new TemplatePower('./plantillas/bal/balance_ros.tpl');
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
		else if (in_array($k, $Campos['integer_format']))
			$tpl->assign($k, $v != 0 ? number_format($v) : '&nbsp;');
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
	$vp_p = $bal['venta'] != 0 ? $bal['venta'] * 100 / $bal['ventas_netas'] : 0;
	$tpl->assign('vp_p', $vp_p != 0 ? '<span class="Orange">(' . number_format($vp_p, 2, '.', ',') . '%)</span>' : '&nbsp;');
	/*
	@ Comparativo de Venta con el año anterior
	*/
	$vp_act = $bal['ventas_netas'];
	$vp_ant = $bal['ventas_netas_ant'];
	$vp_c = $vp_ant > 0 ? abs($vp_act * 100 / $vp_ant - 100) : 0;
	$tpl->assign('vp_c', $vp_act != $vp_ant && round($vp_c, 2) != 0 ? '<span class="' . ($vp_act > $vp_ant ? 'Blue">SUBIO ' : 'Red">BAJO') . number_format($vp_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo del Ventas Netas con el año anterior
	*/
	$vn_act = $bal['ventas_netas'];
	$vn_ant = $bal['ventas_netas_ant'];
	$vn_c = $vn_ant > 0 ? abs($vn_act * 100 / $vn_ant - 100) : 0;
	$tpl->assign('vn_c', $vn_act != $vn_ant && round($vn_c, 2) != 0 ? '<span class="' . ($vn_act > $vn_ant ? 'Blue">SUBIO ' : 'Red">BAJO ') . number_format($vn_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Utilidad Bruta con el año anterior
	*/
	$ub_act = $bal['utilidad_bruta'];
	$ub_ant = $bal['utilidad_bruta_ant'];
	$ub_c = $ub_ant > 0 ? abs($ub_act * 100 / $ub_ant - 100) : 0;
	$tpl->assign('ub_c', $ub_act != $ub_ant && round($ub_c, 2) != 0 ? '<span class="' . ($ub_act > $ub_ant ? 'Blue">MEJORO ' : 'Red">EMPEORO ') . number_format($ub_c, 2, '.', ',') . '%</span>' : '&nbsp;');

	/*
	@ Comparativo de Utilidad Neta con el año anterior
	*/
	$un_act = $bal['utilidad_neta'] - $bal['ingresos_ext'];
	$un_ant = $bal['utilidad_neta_ant'] - $bal['ingresos_ext_ant'];
	$un_c = $un_ant > 0 ? abs($un_act * 100 / $un_ant - 100) : 0;
	$tpl->assign('un_c', $un_act != $un_ant && round($un_c, 2) != 0 ? '<span class="' . ($un_act > $un_ant ? 'Blue">MEJORO ' : 'Red">EMPEORO ') . number_format($un_c, 2, '.', ',') . '%</span>' : '&nbsp;');

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
		$tpl->newBlock('reservas');

		$total_reservas = 0;
		foreach ($reservas as $r) {
			$tpl->newBlock('reserva');
			$tpl->assign('reserva', $r['descripcion']);
			$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
			$total_reservas += $r['importe'];
		}
		$tpl->assign('hoja1.total_reservas', number_format($total_reservas, 2, '.', ','));
		//$tpl->assign('hoja1.asegurados', $bal['emp_afi'] > 0 ? $bal['emp_afi'] : '&nbsp;');

		$tpl->gotoBlock('hoja1');
	}
	else {
		$tpl->assign('ReservasDisplayNone', ' class="DisplayNone"');
	}

	/*
	@ Efectivo Depositado
	@
	@@@ Claúsula: A partir del 1 de Julio de 2008
	@@@
	@@@ [5-Agosto-2008] Modificación de la claúsula, el impuesto IDE ahora se calcula a partir de todos los depósitos conciliados en el mes con código
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
			utilidad_neta - COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
				WHERE
					((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
					AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS util,
			ingresos_ext
				AS ing,
			ventas_netas
				AS vta,
			pollos,
			precio_pollo
				AS precio,
			h.pescuezos,
			piernas,
			bal.mp_vtas,
			COALESCE((
				SELECT
					pollos
				FROM
					historico
				WHERE
					num_cia = h.num_cia
					AND anio = h.anio - 1
					AND mes = h.mes
			), 0)
				AS pollos_ant,
			CASE
				WHEN ventas_netas != 0 THEN
					(utilidad_neta - COALESCE((
						SELECT
							ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
						FROM
							estado_cuenta
						WHERE
							((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
							AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							AND cod_mov IN (1, 16)
					), 0)) / ventas_netas
				ELSE 0
			END
				AS util_vtas,
			CASE
				WHEN mat_prima_utilizada != 0 THEN
					(utilidad_neta - COALESCE((
						SELECT
							ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
						FROM
							estado_cuenta
						WHERE
							((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
							AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							AND cod_mov IN (1, 16)
					), 0)) / mat_prima_utilizada
				ELSE
					0
			END
				AS util_mp
		FROM
			historico h
			LEFT JOIN balances_ros bal
				USING (num_cia, anio, mes)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			num_cia = ' . $bal['num_cia'] . '
			AND anio = ' . ($anyo - 1) . '
		ORDER BY
			mes
	';
	$est_ant = $db->query($sql);

	$tpl->assign('anyo_ant', $anyo - 1);

	/*
	@ Piezas de pollo vendidas en el año y mes anterior
	*/
	$piezas_ant = 0;

	if ($est_ant) {
		$totales = array(
			'vta_ant' => 0,
			'pollos_ant' => 0,
			'precio_ant' => 0,
			'precio_cont_ant' => 0,
			'pescuezos_ant' => 0,
			'piernas_ant' => 0
		);

		foreach ($est_ant as $est) {
			/*
			@ Utilidades Año Anterior
			@
			@ [6-Agosto-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_ant', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_ant', $est['util'] != 0 ? number_format($est['util'] - (isset($_GET['no_ing']) ? $est['ing'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_ant', /*$est['ing'] != 0 && !isset($_GET['no_ing'])*/$est['pollos_ant'] > 0 ? '(' . number_format(/*$est['ing']*/$est['pollos'] * 100 / $est['pollos_ant'] - 100, 2, '.', ',') . ')' : '&nbsp;');

			/*
			@ Estadísticas Año Anterior
			*/
			$tpl->assign('vta_ant_' . $est['mes'], $est['vta'] != 0 ? number_format($est['vta'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('pollos_ant_' . $est['mes'], $est['pollos'] != 0 ? number_format($est['pollos'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('precio_ant_' . $est['mes'], $est['precio'] != 0 ? number_format($est['precio'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('pescuezos_ant_' . $est['mes'], $est['pescuezos'] != 0 ? number_format($est['pescuezos'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('piernas_ant_' . $est['mes'], $est['piernas'] != 0 ? number_format($est['piernas'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('mp_vtas_ant_' . $est['mes'], $est['mp_vtas'] != 0 ? number_format($est['mp_vtas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('util_vtas_ant_' . $est['mes'], $est['util_vtas'] != 0 ? number_format($est['util_vtas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('util_mp_ant_' . $est['mes'], $est['util_mp'] != 0 ? number_format($est['util_mp'], 2, '.', ',') : '&nbsp;');

			$totales['vta_ant'] += $est['vta'];
			$totales['pollos_ant'] += $est['pollos'];
			$totales['precio_ant'] += $est['precio'];
			$totales['precio_cont_ant'] += $est['precio'] != 0 ? 1 : 0;
			$totales['pescuezos_ant'] += $est['pescuezos'];
			$totales['piernas_ant'] += $est['piernas'];

			/*
			@ Almacenar Piezas de pollos vendidas para comparativo
			*/
			if ($est['mes'] == $mes)
				$piezas_ant = $est['pollos'];
		}

		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($k == 'precio_ant' && $totales['precio_cont_ant'] != 0 ? $v / $totales['precio_cont_ant'] : $v, in_array($k, array('vta_ant', 'precio_ant')) ? 2 : 0, '.', ',') : '&nbsp;');
	}

	/*
	@ Estadísticas Año Actual
	*/
	$sql = '
		SELECT
			mes,
			utilidad_neta - COALESCE((
				SELECT
					ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
				FROM
					estado_cuenta
				WHERE
					((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
					AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
					AND cod_mov IN (1, 16)
			), 0)
				AS util,
			ingresos_ext
				AS ing,
			ventas_netas
				AS vta,
			pollos,
			precio_pollo
				AS precio,
			h.pescuezos,
			piernas,
			(
				SELECT
					mp_vtas
				FROM
					balances_ros
				WHERE
					num_cia = h.num_cia
					AND anio = h.anio
					AND mes = h.mes
			)
				AS mp_vtas,
			COALESCE((
				SELECT
					pollos
				FROM
					historico
				WHERE
					num_cia = h.num_cia
					AND anio = h.anio - 1
					AND mes = h.mes
			), 0)
				AS pollos_ant,
			CASE
				WHEN ventas_netas != 0 THEN
					(utilidad_neta - COALESCE((
						SELECT
							ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
						FROM
							estado_cuenta
						WHERE
							((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
							AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							AND cod_mov IN (1, 16)
					), 0)) / ventas_netas
				ELSE 0
			END
				AS util_vtas,
			CASE
				WHEN mat_prima_utilizada != 0 THEN
					(utilidad_neta - COALESCE((
						SELECT
							ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
						FROM
							estado_cuenta
						WHERE
							((num_cia = h.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = h.num_cia)
							AND fecha BETWEEN (\'01/\' || h.mes || \'/\' || h.anio)::DATE AND (\'01/\' || h.mes || \'/\' || h.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							AND cod_mov IN (1, 16)
					), 0)) / mat_prima_utilizada
				ELSE
					0
			END
				AS util_mp
		FROM
			historico h
			LEFT JOIN balances_ros
				USING (num_cia, anio, mes)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			num_cia = ' . $bal['num_cia'] . '
			AND anio = ' . $anyo . '
			AND mes <= ' . $mes . '
		ORDER BY
			mes
	';
	$est_act = $db->query($sql);

	$tpl->assign('anyo_act', $anyo);

	/*
	@ Piezas de pollo vendidas en el año y mes actual
	*/
	$piezas_act = 0;

	if ($est_act) {
		$totales = array(
			'vta' => 0,
			'pollos' => 0,
			'precio' => 0,
			'precio_cont' => 0,
			'pescuezos' => 0,
			'piernas' => 0
		);

		foreach ($est_act as $est) {
			/*
			@ Utilidades Año Actual
			@
			@ [6-Agosto-2008] Si esta habilitada la opción 'no_ing' restar los ingresos extraordinarios a la utilidad neta y no mostrar importe de ingreso
			*/
			$tpl->assign('mes' . $est['mes'] . '_act', $MonthName[$est['mes']]['short_name']);
			$tpl->assign('util' . $est['mes'] . '_act', $est['util'] != 0 ? number_format($est['util'] - (isset($_GET['no_ing']) ? $est['ing'] : 0), 2, '.', ',') : '&nbsp;');
			$tpl->assign('ing' . $est['mes'] . '_act', /*$est['ing'] != 0 && !isset($_GET['no_ing'])*/$est['pollos_ant'] > 0 ? '(' . number_format(/*$est['ing']*/$est['pollos'] * 100 / $est['pollos_ant'] - 100, 2, '.', ',') . ')' : '&nbsp;');

			/*
			@ Estadísticas Año Actual
			*/
			$tpl->assign('vta_' . $est['mes'], $est['vta'] != 0 ? number_format($est['vta'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('pollos_' . $est['mes'], $est['pollos'] != 0 ? number_format($est['pollos'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('precio_' . $est['mes'], $est['precio'] != 0 ? number_format($est['precio'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('pescuezos_' . $est['mes'], $est['pescuezos'] != 0 ? number_format($est['pescuezos'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('piernas_' . $est['mes'], $est['piernas'] != 0 ? number_format($est['piernas'], 0, '.', ',') : '&nbsp;');
			$tpl->assign('mp_vtas_' . $est['mes'], $est['mp_vtas'] != 0 ? number_format($est['mp_vtas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('util_vtas_' . $est['mes'], $est['util_vtas'] != 0 ? number_format($est['util_vtas'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('util_mp_' . $est['mes'], $est['util_mp'] != 0 ? number_format($est['util_mp'], 2, '.', ',') : '&nbsp;');

			$totales['vta'] += $est['vta'];
			$totales['pollos'] += $est['pollos'];
			$totales['precio'] += $est['precio'];
			$totales['precio_cont'] += $est['precio'] != 0 ? 1 : 0;
			$totales['pescuezos'] += $est['pescuezos'];
			$totales['piernas'] += $est['piernas'];

			/*
			@ Almacenar Piezas de pollos vendidas para comparativo
			*/
			if ($est['mes'] == $mes)
				$piezas_act = $est['pollos'];
		}

		foreach ($totales as $k => $v)
			$tpl->assign($k, $v != 0 ? number_format($k == 'precio' && $totales['precio_cont'] != 0 ? $v / $totales['precio_cont'] : $v, in_array($k, array('vta', 'precio')) ? 2 : 0, '.', ',') : '&nbsp;');
	}

	/*
	@ Porcentaje de piezas vendidas contra el año anterior
	*/
	$piezas_por = $piezas_ant > 0 ? abs($piezas_act * 100 / $piezas_ant - 100) : 0;
	$tpl->assign('piezas', number_format($piezas_por, 2, '.', ',') . '%');
	$tpl->assign('un_p', '<span style="color:#' . ($piezas_act > $piezas_ant ? '00C' : 'C00') . ';">' . ($piezas_act > $piezas_ant ? 'SUBIO' : 'BAJO') . '</span>');

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
						AND codgastos NOT IN (141)
						AND ROUND(importe::NUMERIC, 2) <> 0

						/*
						@@@
						@@@ [04-Sep-2008]
						@@@ Claúsula: Omitir todos los pagos hechos para otras compañías
						@@@ [03-Oct-2008]
						@@@ Modificación: En el conjunto de campos se excluyo "cuenta" y "folio" y se agregaron los campos "codgastos" y "fecha"
						@@@
						*/

						AND (num_cia, fecha, folio, codgastos, importe) NOT IN (
							SELECT
								num_cia,
								fecha,
								folio,
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
	$gastos = $db->query($sql);//echo $sql;echo "<pre>" . print_r($gastos, TRUE) . "</pre>";

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
				// Si no fue insertado el código 182
				// [2-Abr-2014] A partir de marzo de 2014 ya no se desglosara IDE
				if ((mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 10, 1, 2006) && mktime(0, 0, 0, $mes, 1, $anyo) <= mktime(0, 0, 0, 2, 1, 2014))
						&&
							($tipo == 2 && !$imp_ide_ins[1] && !$imp_ide_ins[2] && !$imp_ide_ins[3])
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

						if ((mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) >= mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) <= mktime(0, 0, 0, 2, 1, 2014)) && !$imp_ide_ins[$col]) {
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
											)';
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
								)';
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
			else if ($cod == 182 && (mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) < mktime(0, 0, 0, 7, 1, 2008) && mktime(0, 0, 0, $g['mes'], 1, $g['anyo']) > mktime(0, 0, 0, 2, 1, 2014)))
				$imp_ide_ins[$col] = TRUE;

			$por = '';

			/*
			@ [9-Jun-2013] Para los códigos de gasto 1 y 2 compararlos con la venta neta
			@ [10-Jun-2013] Agregados los códigos 7, 52, 179, 180, 181, 183, 187, 189, 190
			*/
			if ($g['tipo'] == 2 && in_array($g['cod'], array(1, 2, 7, 53, 179, 180, 181, 182, 183, 187, 189, 190))) {
				$sql = '
					SELECT
						ventas_netas
					FROM
						balances_ros
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND anio = ' . $g['anyo'] . '
						AND mes = ' . $g['mes'] . '
				';

				$tmp = $db->query($sql);

				if ($tmp) {
					$gas_vta = $g['importe'] * 100 / $tmp[0]['ventas_netas'];

					$por = '<span style="float:left; font-size:6pt;">(' . number_format($gas_vta, 2) . '%)</span>';
				}
			}

			/*
			@ [10-Jun-2013] Para el codigo 90 compararlo contra produccion
			*/
			if ($g['tipo'] == 1/* && in_array($g['cod'], array(90))*/) {
				$sql = '
					SELECT
						pollos_vendidos
					FROM
						balances_ros
					WHERE
						num_cia = ' . $bal['num_cia'] . '
						AND anio = ' . $g['anyo'] . '
						AND mes = ' . $g['mes'] . '
				';

				$tmp = $db->query($sql);

				if ($tmp && $tmp[0]['pollos_vendidos'] > 0) {
					$gas_pollos = $g['importe'] / $tmp[0]['pollos_vendidos'];

					$por = '<span style="float:left; font-size:6pt;">($' . number_format($gas_pollos, 2) . ')</span>';
				}
			}

			$tpl->assign('row_gasto.anyo' . $col, $g['anyo']);
			$tpl->assign('row_gasto.mes' . $col, $g['mes']);

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

	if ($hojas % 2 != 0)
		$tpl->newBlock('hoja_blanca');
}

$tpl->printToScreen();
?>
