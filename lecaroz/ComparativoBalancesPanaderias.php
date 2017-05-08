<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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

$_meses = array(
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
	12 => 'DICIMEBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$condiciones = array();

			$condiciones[] = 'anio = ' . $_REQUEST['anio'];

			$condiciones[] = 'mes = ' . $_REQUEST['mes'];

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

			$condiciones_pan = $condiciones;
			$condiciones_ros = $condiciones;

			if (isset($_REQUEST['campo']) && isset($_REQUEST['importe'])) {
				$condiciones_pan[] = $_REQUEST['campo'] . ' BETWEEN ' . get_val($_REQUEST['importe']) . ' * 0.80 AND ' . get_val($_REQUEST['importe']) . ' * 1.20';

				if ($_REQUEST['campo'] != 'produccion_total') {
					$condiciones_ros[] = $_REQUEST['campo'] . ' BETWEEN ' . get_val($_REQUEST['importe']) . ' * 0.80 AND ' . get_val($_REQUEST['importe']) . ' * 1.20';
				}
			}

			switch ($_REQUEST['orden']) {
				case 1:
					if (isset($_REQUEST['campo'])) {
						$orden = '
							tipo,
							' . $_REQUEST['campo'] . ' DESC,
							num_cia
						';
					}
					else {
						$orden = '
							tipo,
							num_cia
						';
					}
				break;

				case 2:
					$orden = '
						tipo,
						num_cia
					';
				break;
			}

			$sql = '
				SELECT
					num_cia,
					nombre_cia,
					anio,
					mes,
					tipo,
					venta_puerta,
					pastel_vitrina,
					pastel_pedido,
					pan_pedido,
					venta_puerta_total,
					por_venta_puerta,
					bases,
					barredura,
					pastillaje,
					abono_emp,
					otros,
					total_otros,
					abono_reparto,
					por_abono_reparto,
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
					reservas,
					gastos_otras_cias,
					total_gastos,
					ingresos_ext,
					utilidad_neta,
					mp_vtas,
					utilidad_pro,
					mp_pro,
					produccion_1,
					ROUND((produccion_1 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_1,
					produccion_2,
					ROUND((produccion_2 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_2,
					produccion_3,
					ROUND((produccion_3 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_3,
					produccion_4,
					ROUND((produccion_4 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_4,
					produccion_8,
					ROUND((produccion_8 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_8,
					produccion_9,
					ROUND((produccion_9 * 100 / produccion_total)::numeric, 2)
								AS
									por_produccion_9,
					produccion_total,
					faltante_pan,
					rezago_ini,
					rezago_fin,
					var_rezago,
					efectivo,
					pollos_vendidos,
					p_pavo,
					ingresos,
					egresos,
					total_gastos_caja,
					depositos,
					otros_depositos,
					otros_depositos + total_gastos_caja
						AS
							general,
					otros_depositos + total_gastos_caja - utilidad_neta
						AS
							diferencia
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							anio,
							mes,
							1
								AS tipo,
							venta_puerta  - pastel_vitrina - pastel_pedido - pan_pedido AS venta_puerta,
							pastel_vitrina,
							pastel_pedido,
							pan_pedido,
							venta_puerta
								AS venta_puerta_total,
							ROUND((venta_puerta * 100 / (venta_puerta + abono_reparto))::numeric, 2)
								AS
									por_venta_puerta,
							COALESCE((
								SELECT
									SUM(base)
								FROM
									venta_pastel
								WHERE
									num_cia = bal.num_cia
									AND fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND tipo = 0
									AND estado < 2
									AND base > 0
							), 0) AS bases,
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
									AND fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND tipo = 0
									AND estado < 2
									AND base > 0
							), 0) AS otros,
							total_otros,
							abono_reparto,
							ROUND((100 - (venta_puerta * 100 / (venta_puerta + abono_reparto)))::numeric, 2)
								AS
									por_abono_reparto,
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
							reserva_aguinaldos
								AS reservas,
							gastos_otras_cias,
							total_gastos,
							ingresos_ext + COALESCE((
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
							), 0) AS ingresos_ext,
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
							), 0) AS utilidad_neta,
							ROUND(mp_vtas::numeric, 3)
								AS mp_vtas,
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
							END AS utilidad_pro,
							ROUND(mp_pro::numeric, 3)
								AS mp_pro,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 1
							), 0)
								AS
									produccion_1,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 2
							), 0)
								AS
									produccion_2,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 3
							), 0)
								AS
									produccion_3,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 4
							), 0)
								AS
									produccion_4,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 8
							), 0)
								AS
									produccion_8,
							COALESCE((
								SELECT
									SUM(total_produccion)
								FROM
									total_produccion
								WHERE
										numcia = bal.num_cia
									AND
										fecha_total BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										codturno = 9
							), 0)
								AS
									produccion_9,
							produccion_total,
							faltante_pan,
							rezago_ini,
							rezago_fin,
							var_rezago,
							efectivo,
							0
								AS pollos_vendidos,
							0
								AS
									p_pavo,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									gastos_caja
								WHERE
										num_cia = bal.num_cia
									AND
										fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										tipo_mov = TRUE
							), 0)
								AS ingresos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									gastos_caja
								WHERE
										num_cia = bal.num_cia
									AND
										fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										tipo_mov = FALSE
							), 0)
								AS egresos,

							COALESCE((
								SELECT
									SUM(
										CASE
											WHEN tipo_mov = TRUE THEN
												importe
											ELSE
												-importe
										END
									)
								FROM
									gastos_caja
								WHERE
										num_cia = bal.num_cia
									AND
										fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							), 0)
								AS total_gastos_caja,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									estado_cuenta
								WHERE
										num_cia = bal.num_cia
									AND
										fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
									AND
										cod_mov = 1
							), 0)
								AS depositos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									otros_depositos
								WHERE
										num_cia = bal.num_cia
									AND
										fecha BETWEEN ( \'01/\' || bal.mes || \'/\' || bal.anio)::date AND ( \'01/\' || bal.mes || \'/\' || bal.anio)::date + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							), 0)
								AS otros_depositos
						FROM
							balances_pan bal
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							' . implode(' AND ', $condiciones_pan) . '
					)
						AS result_pan

				UNION

				SELECT
					num_cia,
					nombre_corto,
					anio,
					mes,
					2,
					0,
					0,
					0,
					0,
					venta,
					0,
					0,
					0,
					0,
					0,
					otros,
					0,
					0,
					0,
					0,
					ventas_netas,
					inv_ant,
					compras,
					mercancias,
					inv_act,
					mat_prima_utilizada,
					0,
					0,
					gastos_fab,
					costo_produccion,
					utilidad_bruta,
					0,
					gastos_generales,
					gastos_caja,
					reserva_aguinaldos,
					gastos_otras_cias,
					total_gastos,
					ingresos_ext,
					utilidad_neta - COALESCE((
						SELECT
							ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
						FROM
							estado_cuenta
						WHERE
							((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
							AND fecha BETWEEN (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE AND (\'01/\' || bal.mes || \'/\' || bal.anio)::DATE + INTERVAL \'1 MONTH\' - INTERVAL \'1 DAY\'
							AND cod_mov IN (1, 16)
					), 0) AS utilidad_neta,
					ROUND(mp_vtas::numeric, 3),
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					efectivo,
					pollos_vendidos,
					p_pavo,
					0,
					0,
					0,
					0,
					0,
					0,
					0
				FROM
					balances_ros bal
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones_ros) . '

				ORDER BY
					' . $orden . '
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/ComparativoBalancesPanaderiasReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$columnas_por_hoja = 6;

				$tipos = array();

				$tipo = NULL;
				foreach ($result as $rec) {
					if ($tipo != $rec['tipo']) {
						$tipo = $rec['tipo'];

						$tipos[$tipo] = array();

						$hoja = 0;

						$columna = 0;
					}

					if ($columna == $columnas_por_hoja) {
						$columna = 0;

						$hoja++;
					}

					$tipos[$tipo][$hoja][$columna] = $rec;

					$columna++;
				}

				foreach ($tipos as $tipo => $hojas) {
					if ($tipo == 1) {
						foreach ($hojas as $hoja => $columnas) {
							$tpl->newBlock('reporte1');
							$tpl->assign('anio', $_REQUEST['anio']);
							$tpl->assign('mes', mes_escrito($_REQUEST['mes']));

							foreach ($columnas as $columna => $rec) {
								$tpl->assign('nombre_cia_' . $columna, $rec['nombre_cia']);

								$tpl->assign('venta_puerta_' . $columna, $rec['venta_puerta_total'] != 0 ? number_format($rec['venta_puerta'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('pastel_vitrina_' . $columna, $rec['pastel_vitrina'] != 0 ? number_format($rec['pastel_vitrina'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('pastel_pedido_' . $columna, $rec['pastel_pedido'] != 0 ? number_format($rec['pastel_pedido'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('pan_pedido_' . $columna, $rec['pan_pedido'] != 0 ? number_format($rec['pan_pedido'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('venta_puerta_total_' . $columna, $rec['venta_puerta_total'] != 0 ? number_format($rec['venta_puerta_total'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_venta_puerta_' . $columna, $rec['por_venta_puerta'] != 0 ? '<span class="font6 orange">(' . $rec['por_venta_puerta'] . '%)</span>' : '');
								$tpl->assign('bases_' . $columna, $rec['bases'] != 0 ? number_format($rec['bases'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('barredura_' . $columna, $rec['barredura'] != 0 ? number_format($rec['barredura'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('pastillaje_' . $columna, $rec['pastillaje'] != 0 ? number_format($rec['pastillaje'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('abono_emp_' . $columna, $rec['abono_emp'] != 0 ? number_format($rec['abono_emp'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('otros_' . $columna, $rec['otros'] != 0 ? number_format($rec['otros'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('total_otros_' . $columna, $rec['total_otros'] != 0 ? number_format($rec['total_otros'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('abono_reparto_' . $columna, $rec['abono_reparto'] != 0 ? number_format($rec['abono_reparto'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_abono_reparto_' . $columna, $rec['por_abono_reparto'] != 0 ? '<span class="font6 orange">(' . $rec['por_abono_reparto'] . '%)</span>' : '');
								$tpl->assign('errores_' . $columna, $rec['errores'] != 0 ? number_format($rec['errores'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('ventas_netas_' . $columna, $rec['ventas_netas'] != 0 ? number_format($rec['ventas_netas'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('inv_ant_' . $columna, $rec['inv_ant'] != 0 ? number_format($rec['inv_ant'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('compras_' . $columna, $rec['compras'] != 0 ? number_format($rec['compras'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('mercancias_' . $columna, $rec['mercancias'] != 0 ? number_format($rec['mercancias'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('inv_act_' . $columna, $rec['inv_act'] != 0 ? number_format($rec['inv_act'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('mat_prima_utilizada_' . $columna, $rec['mat_prima_utilizada'] != 0 ? number_format($rec['mat_prima_utilizada'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('mano_obra_' . $columna, $rec['mano_obra'] != 0 ? number_format($rec['mano_obra'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('panaderos_' . $columna, $rec['panaderos'] != 0 ? number_format($rec['panaderos'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_fab_' . $columna, $rec['gastos_fab'] != 0 ? number_format($rec['gastos_fab'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('costo_produccion_' . $columna, $rec['costo_produccion'] != 0 ? number_format($rec['costo_produccion'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('utilidad_bruta_' . $columna, $rec['utilidad_bruta'] != 0 ? '<span class="' . ($rec['utilidad_bruta'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['utilidad_bruta'], 2, '.', ',') . '</span>' : '&nbsp;');

								$tpl->assign('pan_comprado_' . $columna, $rec['pan_comprado'] != 0 ? number_format($rec['pan_comprado'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_generales_' . $columna, $rec['gastos_generales'] != 0 ? number_format($rec['gastos_generales'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_caja_' . $columna, $rec['gastos_caja'] != 0 ? number_format($rec['gastos_caja'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('reservas_' . $columna, $rec['reservas'] != 0 ? number_format($rec['reservas'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_otras_cias_' . $columna, $rec['gastos_otras_cias'] != 0 ? number_format($rec['gastos_otras_cias'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('total_gastos_' . $columna, $rec['total_gastos'] != 0 ? number_format($rec['total_gastos'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('ingresos_ext_' . $columna, $rec['ingresos_ext'] != 0 ? number_format($rec['ingresos_ext'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('utilidad_neta_' . $columna, $rec['utilidad_neta'] != 0 ? '<span class="' . ($rec['utilidad_neta'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['utilidad_neta'], 2, '.', ',') . '</span>' : '&nbsp;');

								$tpl->assign('mp_vtas_' . $columna, $rec['mp_vtas'] != 0 ? '<span class="' . ($rec['mp_vtas'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['mp_vtas'], 3, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('utilidad_pro_' . $columna, $rec['utilidad_pro'] != 0 ? '<span class="' . ($rec['utilidad_pro'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['utilidad_pro'], 3, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('mp_pro_' . $columna, $rec['mp_pro'] != 0 ? '<span class="' . ($rec['mp_pro'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['mp_pro'], 3, '.', ',') . '</span>' : '&nbsp;');

								$tpl->assign('produccion_1_' . $columna, $rec['produccion_1'] != 0 ? number_format($rec['produccion_1'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_1_' . $columna, $rec['por_produccion_1'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_1'] . '%)</span>' : '');
								$tpl->assign('produccion_2_' . $columna, $rec['produccion_2'] != 0 ? number_format($rec['produccion_2'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_2_' . $columna, $rec['por_produccion_2'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_2'] . '%)</span>' : '');
								$tpl->assign('produccion_3_' . $columna, $rec['produccion_3'] != 0 ? number_format($rec['produccion_3'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_3_' . $columna, $rec['por_produccion_3'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_3'] . '%)</span>' : '');
								$tpl->assign('produccion_4_' . $columna, $rec['produccion_4'] != 0 ? number_format($rec['produccion_4'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_4_' . $columna, $rec['por_produccion_4'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_4'] . '%)</span>' : '');
								$tpl->assign('produccion_8_' . $columna, $rec['produccion_8'] != 0 ? number_format($rec['produccion_8'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_8_' . $columna, $rec['por_produccion_8'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_8'] . '%)</span>' : '');
								$tpl->assign('produccion_9_' . $columna, $rec['produccion_9'] != 0 ? number_format($rec['produccion_9'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('por_produccion_9_' . $columna, $rec['por_produccion_9'] != 0 ? '<span class="font6 orange">(' . $rec['por_produccion_9'] . '%)</span>' : '');
								$tpl->assign('produccion_' . $columna, $rec['produccion_total'] != 0 ? number_format($rec['produccion_total'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('faltante_pan_' . $columna, $rec['faltante_pan'] != 0 ? '<span class="' . ($rec['faltante_pan'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['faltante_pan'], 2, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('rezago_ini_' . $columna, $rec['rezago_ini'] != 0 ? number_format($rec['rezago_ini'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('rezago_fin_' . $columna, $rec['rezago_fin'] != 0 ? number_format($rec['rezago_fin'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('var_rezago_' . $columna, $rec['var_rezago'] != 0 ? '<span class="' . ($rec['var_rezago'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['var_rezago'], 2, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('efectivo_' . $columna, $rec['efectivo'] != 0 ? number_format($rec['efectivo'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('ingresos_' . $columna, $rec['ingresos'] != 0 ? number_format($rec['ingresos'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('egresos_' . $columna, $rec['egresos'] != 0 ? number_format($rec['egresos'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('total_gastos_caja_' . $columna, $rec['total_gastos_caja'] != 0 ? '<span class="' . ($rec['total_gastos_caja'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['total_gastos_caja'], 2, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('depositos_' . $columna, $rec['depositos'] != 0 ? number_format($rec['depositos'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('otros_depositos_' . $columna, $rec['otros_depositos'] != 0 ? number_format($rec['otros_depositos'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('general_' . $columna, $rec['general'] != 0 ? '<span class="' . ($rec['general'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['general'], 2, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('diferencia_' . $columna, $rec['diferencia'] != 0 ? '<span class="' . ($rec['diferencia'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['diferencia'], 2, '.', ',') . '</span>' : '&nbsp;');
							}
						}
					}
					else if ($tipo == 2) {
						foreach ($hojas as $hoja => $columnas) {
							$tpl->newBlock('reporte2');
							$tpl->assign('anio', $_REQUEST['anio']);
							$tpl->assign('mes', mes_escrito($_REQUEST['mes']));

							foreach ($columnas as $columna => $rec) {
								$tpl->assign('nombre_cia_' . $columna, $rec['nombre_cia']);

								$tpl->assign('venta_' . $columna, $rec['venta_puerta_total'] != 0 ? number_format($rec['venta_puerta_total'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('ventas_netas_' . $columna, $rec['ventas_netas'] != 0 ? number_format($rec['ventas_netas'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('inv_ant_' . $columna, $rec['inv_ant'] != 0 ? number_format($rec['inv_ant'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('compras_' . $columna, $rec['compras'] != 0 ? number_format($rec['compras'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('mercancias_' . $columna, $rec['mercancias'] != 0 ? number_format($rec['mercancias'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('inv_act_' . $columna, $rec['inv_act'] != 0 ? number_format($rec['inv_act'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('mat_prima_utilizada_' . $columna, $rec['mat_prima_utilizada'] != 0 ? number_format($rec['mat_prima_utilizada'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_fab_' . $columna, $rec['gastos_fab'] != 0 ? number_format($rec['gastos_fab'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('costo_produccion_' . $columna, $rec['costo_produccion'] != 0 ? number_format($rec['costo_produccion'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('utilidad_bruta_' . $columna, $rec['utilidad_bruta'] != 0 ? '<span class="' . ($rec['utilidad_bruta'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['utilidad_bruta'], 2, '.', ',') . '</span>' : '&nbsp;');

								$tpl->assign('gastos_generales_' . $columna, $rec['gastos_generales'] != 0 ? number_format($rec['gastos_generales'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_caja_' . $columna, $rec['gastos_caja'] != 0 ? number_format($rec['gastos_caja'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('reservas_' . $columna, $rec['reservas'] != 0 ? number_format($rec['reservas'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('gastos_otras_cias_' . $columna, $rec['gastos_otras_cias'] != 0 ? number_format($rec['gastos_otras_cias'], 2, '.', ',') : '&nbsp;');
								$tpl->assign('total_gastos_' . $columna, $rec['total_gastos'] != 0 ? number_format($rec['total_gastos'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('ingresos_ext_' . $columna, $rec['ingresos_ext'] != 0 ? number_format($rec['ingresos_ext'], 2, '.', ',') : '&nbsp;');

								$tpl->assign('utilidad_neta_' . $columna, $rec['utilidad_neta'] != 0 ? '<span class="' . ($rec['utilidad_neta'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['utilidad_neta'], 2, '.', ',') . '</span>' : '&nbsp;');

								$tpl->assign('mp_vtas_' . $columna, $rec['mp_vtas'] != 0 ? '<span class="' . ($rec['mp_vtas'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['mp_vtas'], 3, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('pollos_vendidos_' . $columna, $rec['pollos_vendidos'] != 0 ? '<span class="' . ($rec['pollos_vendidos'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['pollos_vendidos'], 3, '.', ',') . '</span>' : '&nbsp;');
								$tpl->assign('p_pavo_' . $columna, $rec['p_pavo'] != 0 ? '<span class="' . ($rec['p_pavo'] <= 0 ? 'red' : 'blue') . '">' . number_format($rec['p_pavo'], 3, '.', ',') . '</span>' : '&nbsp;');

							}
						}
					}
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ComparativoBalancesPanaderias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

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
