<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosGastosVariosInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

			$rfcs = $db->query("SELECT
				rfc AS value,
				'[' || rfc || '][' || (
					SELECT
						MIN (num_cia)
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
				) || ']' || COALESCE((
					SELECT
						razon_social
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
						AND TRIM(razon_social) != ''
					ORDER BY
						num_cia
					LIMIT 1
				), (
					SELECT
						nombre
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
						AND TRIM(nombre) != ''
					ORDER BY
						num_cia
					LIMIT 1
				), '-- SIN NOMBRE --') AS text
			FROM
				catalogo_companias cc
			WHERE
				LENGTH(rfc) >= 12
			GROUP BY
				rfc
			ORDER BY
				(
					SELECT
						MIN (num_cia)
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
				)");

			if ($rfcs)
			{
				foreach ($rfcs as $row) {
					$tpl->newBlock('rfc');

					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode($row['text']));
				}
			}

			$admins = $db->query("SELECT idadministrador AS value, nombre_administrador AS text FROM catalogo_administradores ORDER BY text");

			if ($admins)
			{
				foreach ($admins as $a) {
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			// $mes_max = $db->query("SELECT COALESCE(MAX(mes)) AS mes FROM balances_pan WHERE anio = {$_REQUEST['anio']}");
			$mes_max = $db->query("SELECT EXTRACT(MONTH FROM MAX(fecha)) AS mes FROM estado_cuenta WHERE fecha BETWEEN '01/01/{$_REQUEST['anio']}' AND '31/12/{$_REQUEST['anio']}'");

			if ($mes_max[0]['mes'] == 0)
			{
				return FALSE;
			}

			$fecha1 = "01/12/" . ($_REQUEST['anio'] - 1);
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2, 3, 4)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS alias_cia,
					rfc_cia,
					anio,
					mes,
					SUM(depositos) AS depositos,
					SUM(nominas) AS nominas,
					SUM(inventario) AS inventario,
					SUM(mat_prima) AS mat_prima,
					SUM(mat_prima_iva) AS mat_prima_iva,
					SUM(costo_venta) AS costo_venta,
					SUM(varios) AS varios,
					SUM(varios_iva) AS varios_iva,
					SUM(impuestos) AS impuestos,
					SUM(imss) AS imss,
					SUM(ieps) AS ieps,
					SUM(saldo_banco) AS saldo_banco,
					SUM(saldo_pro) AS saldo_pro,
					SUM(perdidas) AS perdidas
				FROM
					(
						SELECT
							num_cia,
							(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
							(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
							(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
							anio,
							mes,
							depositos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									cheques
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos = 134
									AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS nominas,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima_iva,
							(CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) + COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) - (CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) AS costo_venta,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios_iva,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (179, 180, 181, 187)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS impuestos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (141)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS imss,
							COALESCE(depositos * (COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (3, 4)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) * 100 / COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (1, 2, 3, 4, 8, 9)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) / 100) * COALESCE((
								SELECT
									porcentaje
								FROM
									porcentajes_ieps
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
							CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END AS inventario,
							COALESCE((
								SELECT
									SUM(saldo_libros) + COALESCE((
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
											estado_cuenta
										WHERE
											num_cia = depositos_result.num_cia
											AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

									), 0)
								FROM
									saldos
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS saldo_banco,
							COALESCE((
								SELECT
									SUM(total)
								FROM
									historico_proveedores
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
							), 0) AS saldo_pro,
							COALESCE((
								SELECT
									monto
								FROM
									perdidas
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS perdidas
						FROM
							(
								SELECT
									COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
									cc.tipo_cia,
									EXTRACT(YEAR FROM ec.fecha) AS anio,
									EXTRACT(MONTH FROM ec.fecha) AS mes,
									SUM(
										CASE
											WHEN cc.tipo_cia = 1 THEN
												ec.importe
											WHEN cc.tipo_cia = 2 THEN
												ec.importe / 1.16
											ELSE
												ec.importe
										END
									) AS depositos
								FROM
									estado_cuenta ec
									LEFT JOIN catalogo_companias cc ON (
										cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
									)
								WHERE
									" . implode(' AND ', $condiciones) . "
								GROUP BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes,
									cc.tipo_cia
								ORDER BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes
							) AS depositos_result
					) AS result
				GROUP BY
					rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes";
			}
			else
			{
				$sql = "SELECT
					num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
					(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
					anio,
					mes,
					depositos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							cheques
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos = 134
							AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS nominas,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima_iva,
					(CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) + COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) - (CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) AS costo_venta,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios_iva,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (179, 180, 181, 187)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS impuestos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (141)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS imss,
					COALESCE(depositos * (COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (3, 4)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) * 100 / COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (1, 2, 3, 4, 8, 9)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) / 100) * COALESCE((
						SELECT
							porcentaje
						FROM
							porcentajes_ieps
						WHERE
							num_cia = depositos_result.num_cia
							AND anio = depositos_result.anio
							AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
					CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END AS inventario,
					COALESCE((
						SELECT
							SUM(saldo_libros) + COALESCE((
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
									estado_cuenta
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

							), 0)
						FROM
							saldos
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS saldo_banco,
					COALESCE((
						SELECT
							SUM(total)
						FROM
							historico_proveedores
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
					), 0) AS saldo_pro,
					COALESCE((
						SELECT
							monto
						FROM
							perdidas
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS perdidas
				FROM
					(
						SELECT
							COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
							cc.tipo_cia,
							EXTRACT(YEAR FROM ec.fecha) AS anio,
							EXTRACT(MONTH FROM ec.fecha) AS mes,
							SUM(
								CASE
									WHEN cc.tipo_cia = 1 THEN
										ec.importe
									WHEN cc.tipo_cia = 2 THEN
										ec.importe / 1.16
									ELSE
										ec.importe
								END
							) AS depositos
						FROM
							estado_cuenta ec
							LEFT JOIN catalogo_companias cc ON (
								cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
							)
						WHERE
							" . implode(' AND ', $condiciones) . "
						GROUP BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes,
							cc.tipo_cia
						ORDER BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes
					) AS depositos_result";
			}

			$result = $db->query($sql);

			if ($result)
			{
				$datos = array();

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'		=> $row['nombre_cia'],
							'alias_cia'			=> $row['alias_cia'],
							'rfc_cia'			=> $row['rfc_cia'],
							'depositos'			=> array_fill(0, 13, 0),
							'nominas'			=> array_fill(0, 13, 0),
							'inventario'		=> array_fill(0, 13, 0),
							'mat_prima'			=> array_fill(0, 13, 0),
							'mat_prima_iva'		=> array_fill(0, 13, 0),
							'costo_venta'		=> array_fill(0, 13, 0),
							'varios'			=> array_fill(0, 13, 0),
							'varios_iva'		=> array_fill(0, 13, 0),
							'impuestos'			=> array_fill(0, 13, 0),
							'imss'				=> array_fill(0, 13, 0),
							'ieps'				=> array_fill(0, 13, 0),
							'saldo_banco'		=> array_fill(0, 13, 0),
							'saldo_pro'			=> array_fill(0, 13, 0),
							'totales'			=> array_fill(0, 13, 0),
							'total_estimado'	=> array_fill(0, 13, 0),
							'diferencia'		=> array_fill(0, 13, 0),
							'bancos_pros'		=> array_fill(0, 13, 0),
							'perdidas'			=> 0
						);
					}

					$mes = $row['anio'] < $_REQUEST['anio'] ? 0 : $row['mes'];

					$datos[$num_cia]['depositos'][$mes] = $row['depositos'];
					$datos[$num_cia]['nominas'][$mes] = $row['nominas'];
					$datos[$num_cia]['inventario'][$mes] = $row['inventario'];
					$datos[$num_cia]['mat_prima'][$mes] = $row['mat_prima'];
					$datos[$num_cia]['mat_prima_iva'][$mes] = $row['mat_prima_iva'];
					$datos[$num_cia]['costo_venta'][$mes] = $row['costo_venta'];
					$datos[$num_cia]['varios'][$mes] = $row['varios'];
					$datos[$num_cia]['varios_iva'][$mes] = $row['varios_iva'];
					$datos[$num_cia]['impuestos'][$mes] = $row['impuestos'];
					$datos[$num_cia]['imss'][$mes] = $row['imss'];
					$datos[$num_cia]['ieps'][$mes] = $row['ieps'];
					$datos[$num_cia]['saldo_banco'][$mes] = $row['saldo_banco'];
					$datos[$num_cia]['saldo_pro'][$mes] = $row['saldo_pro'];
					$datos[$num_cia]['totales'][$mes] = $row['depositos'] - $row['nominas'] - $row['costo_venta']/* - $row['mat_prima']*/ - $row['varios']/* - $row['impuestos']*/ - $row['imss'];
					$datos[$num_cia]['total_estimado'][$mes] = $datos[$num_cia]['depositos'][$mes] * 0.05;
					$datos[$num_cia]['diferencia'][$mes] = $datos[$num_cia]['totales'][$mes] - $datos[$num_cia]['total_estimado'][$mes];
					$datos[$num_cia]['bancos_pros'][$mes] = $row['saldo_banco'] - $row['saldo_pro'];
					$datos[$num_cia]['perdidas'] = $row['perdidas'];
				}

				$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosGastosVariosConsultaV2.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $num_cia => $cia)
				{
					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));
					$tpl->assign('rfc_cia', utf8_encode($cia['rfc_cia']));

					foreach ($cia['depositos'] as $mes => $depositos) {
						$tpl->assign('saldo_banco' . $mes, $cia['saldo_banco'][$mes] != 0 ? number_format($cia['saldo_banco'][$mes], 2) : '&nbsp;');
						$tpl->assign('saldo_pro' . $mes, $cia['saldo_pro'][$mes] != 0 ? number_format($cia['saldo_pro'][$mes], 2) : '&nbsp;');
						$tpl->assign('bancos_pros' . $mes, $cia['bancos_pros'][$mes] != 0 ? '<span class="' . ($cia['bancos_pros'][$mes] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['bancos_pros'][$mes], 2) . '</span>' : '&nbsp;');
						$tpl->assign('depositos' . $mes, $depositos != 0 ? number_format($depositos, 2) : '&nbsp;');
						$tpl->assign('nominas' . $mes, $cia['nominas'][$mes] != 0 ? number_format($cia['nominas'][$mes], 2) : '&nbsp;');
						$tpl->assign('inventario' . $mes, $cia['inventario'][$mes] != 0 ? number_format($cia['inventario'][$mes], 2) : '&nbsp;');
						$tpl->assign('mat_prima' . $mes, $cia['mat_prima'][$mes] != 0 ? number_format($cia['mat_prima'][$mes], 2) : '&nbsp;');
						$tpl->assign('mat_prima_iva' . $mes, $cia['mat_prima_iva'][$mes] != 0 ? number_format($cia['mat_prima_iva'][$mes], 2) : '&nbsp;');
						$tpl->assign('costo_venta' . $mes, $cia['costo_venta'][$mes] != 0 ? number_format($cia['costo_venta'][$mes], 2) : '&nbsp;');
						$tpl->assign('varios' . $mes, $cia['varios'][$mes] != 0 ? number_format($cia['varios'][$mes], 2) : '&nbsp;');
						$tpl->assign('varios_iva' . $mes, $cia['varios_iva'][$mes] != 0 ? number_format($cia['varios_iva'][$mes], 2) : '&nbsp;');
						$tpl->assign('impuestos' . $mes, $cia['impuestos'][$mes] != 0 ? number_format($cia['impuestos'][$mes], 2) : '&nbsp;');
						$tpl->assign('imss' . $mes, $cia['imss'][$mes] != 0 ? number_format($cia['imss'][$mes], 2) : '&nbsp;');
						$tpl->assign('ieps' . $mes, $cia['ieps'][$mes] != 0 ? number_format($cia['ieps'][$mes], 2) : '&nbsp;');
						$tpl->assign('total_mes' . $mes, $cia['totales'][$mes] != 0 ? '<span class="' . ($cia['totales'][$mes] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['totales'][$mes], 2) . '</span>' : '&nbsp;');
						$tpl->assign('total_estimado_mes' . $mes, $cia['total_estimado'][$mes] != 0 ? '<span class="' . ($cia['total_estimado'][$mes] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['total_estimado'][$mes], 2) . '</span>' : '&nbsp;');
						$tpl->assign('diferencia' . $mes, $cia['diferencia'][$mes] != 0 ? '<span class="' . ($cia['diferencia'][$mes] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['diferencia'][$mes], 2) . '</span>' : '&nbsp;');
					}

					array_shift($cia['depositos']);
					array_shift($cia['nominas']);
					array_shift($cia['mat_prima']);
					array_shift($cia['mat_prima_iva']);
					array_shift($cia['costo_venta']);
					array_shift($cia['varios']);
					array_shift($cia['varios_iva']);
					array_shift($cia['impuestos']);
					array_shift($cia['imss']);
					array_shift($cia['ieps']);
					array_shift($cia['totales']);
					array_shift($cia['total_estimado']);
					array_shift($cia['diferencia']);

					$tpl->assign('total_saldo_banco', $cia['saldo_banco'][$mes_max[0]['mes']] != 0 ? number_format($cia['saldo_banco'][$mes_max[0]['mes']], 2) : '&nbsp;');
					$tpl->assign('total_saldo_pro', $cia['saldo_pro'][$mes_max[0]['mes']] != 0 ? number_format($cia['saldo_pro'][$mes_max[0]['mes']], 2) : '&nbsp;');
					$tpl->assign('total_bancos_pros', $cia['bancos_pros'][$mes_max[0]['mes']] != 0 ? '<span class="' . ($cia['bancos_pros'][$mes_max[0]['mes']] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['bancos_pros'][$mes_max[0]['mes']], 2) . '</span>' : '&nbsp;');
					$tpl->assign('total_inventario', $cia['inventario'][$mes_max[0]['mes']] != 0 ? number_format($cia['inventario'][$mes_max[0]['mes']], 2) : '&nbsp;');

					$tpl->assign('total_depositos', array_sum($cia['depositos']) != 0 ? number_format(array_sum($cia['depositos']), 2) : '&nbsp;');
					$tpl->assign('total_nominas', array_sum($cia['nominas']) != 0 ? number_format(array_sum($cia['nominas']), 2) : '&nbsp;');
					$tpl->assign('total_mat_prima', array_sum($cia['mat_prima']) != 0 ? number_format(array_sum($cia['mat_prima']), 2) : '&nbsp;');
					$tpl->assign('total_mat_prima_iva', array_sum($cia['mat_prima_iva']) != 0 ? number_format(array_sum($cia['mat_prima_iva']), 2) : '&nbsp;');
					$tpl->assign('total_costo_venta', array_sum($cia['costo_venta']) != 0 ? number_format(array_sum($cia['costo_venta']), 2) : '&nbsp;');
					$tpl->assign('total_varios', array_sum($cia['varios']) != 0 ? number_format(array_sum($cia['varios']), 2) : '&nbsp;');
					$tpl->assign('total_varios_iva', array_sum($cia['varios_iva']) != 0 ? number_format(array_sum($cia['varios_iva']), 2) : '&nbsp;');
					$tpl->assign('total_impuestos', array_sum($cia['impuestos']) != 0 ? number_format(array_sum($cia['impuestos']), 2) : '&nbsp;');
					$tpl->assign('total_imss', array_sum($cia['imss']) != 0 ? number_format(array_sum($cia['imss']), 2) : '&nbsp;');
					$tpl->assign('total_ieps', array_sum($cia['ieps']) != 0 ? number_format(array_sum($cia['ieps']), 2) : '&nbsp;');
					$tpl->assign('total_meses', array_sum($cia['totales']) != 0 ? '<span class="' . (array_sum($cia['totales']) <= 0 ? 'red' : 'blue') . '">' . number_format(array_sum($cia['totales']), 2) . '</span>' : '&nbsp;');
					$tpl->assign('total_estimado_meses', array_sum($cia['total_estimado']) != 0 ? '<span class="' . (array_sum($cia['total_estimado']) <= 0 ? 'red' : 'blue') . '">' . number_format(array_sum($cia['total_estimado']), 2) . '</span>' : '&nbsp;');
					$tpl->assign('total_diferencia', array_sum($cia['diferencia']) - $cia['perdidas'] != 0 ? '<span class="' . (array_sum($cia['diferencia']) - $cia['perdidas'] <= 0 ? 'red' : 'blue') . '">' . number_format(array_sum($cia['diferencia']) - $cia['perdidas'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('perdidas', $cia['perdidas'] != 0 ? number_format($cia['perdidas'], 2) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$mes_max = $db->query("SELECT COALESCE(MAX(mes)) AS mes FROM balances_pan WHERE anio = {$_REQUEST['anio']}");

			if ($mes_max[0]['mes'] == 0)
			{
				return FALSE;
			}

			$fecha1 = "01/12/" . ($_REQUEST['anio'] - 1);
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2, 3, 4)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS alias_cia,
					rfc_cia,
					anio,
					mes,
					SUM(depositos) AS depositos,
					SUM(nominas) AS nominas,
					SUM(inventario) AS inventario,
					SUM(mat_prima) AS mat_prima,
					SUM(mat_prima_iva) AS mat_prima_iva,
					SUM(costo_venta) AS costo_venta,
					SUM(varios) AS varios,
					SUM(varios_iva) AS varios_iva,
					SUM(impuestos) AS impuestos,
					SUM(imss) AS imss,
					SUM(ieps) AS ieps,
					SUM(saldo_banco) AS saldo_banco,
					SUM(saldo_pro) AS saldo_pro,
					SUM(perdidas) AS perdidas
				FROM
					(
						SELECT
							num_cia,
							(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
							(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
							(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
							anio,
							mes,
							depositos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									cheques
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos = 134
									AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS nominas,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima_iva,
							(CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) + COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) - (CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) AS costo_venta,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios_iva,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (179, 180, 181, 187)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS impuestos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (141)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS imss,
							COALESCE(depositos * (COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (3, 4)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) * 100 / COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (1, 2, 3, 4, 8, 9)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) / 100) * COALESCE((
								SELECT
									porcentaje
								FROM
									porcentajes_ieps
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
							CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END AS inventario,
							COALESCE((
								SELECT
									SUM(saldo_libros) + COALESCE((
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
											estado_cuenta
										WHERE
											num_cia = depositos_result.num_cia
											AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

									), 0)
								FROM
									saldos
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS saldo_banco,
							COALESCE((
								SELECT
									SUM(total)
								FROM
									historico_proveedores
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
							), 0) AS saldo_pro,
							COALESCE((
								SELECT
									monto
								FROM
									perdidas
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS perdidas
						FROM
							(
								SELECT
									COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
									cc.tipo_cia,
									EXTRACT(YEAR FROM ec.fecha) AS anio,
									EXTRACT(MONTH FROM ec.fecha) AS mes,
									SUM(
										CASE
											WHEN cc.tipo_cia = 1 THEN
												ec.importe
											WHEN cc.tipo_cia = 2 THEN
												ec.importe / 1.16
											ELSE
												ec.importe
										END
									) AS depositos
								FROM
									estado_cuenta ec
									LEFT JOIN catalogo_companias cc ON (
										cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
									)
								WHERE
									" . implode(' AND ', $condiciones) . "
								GROUP BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes,
									cc.tipo_cia
								ORDER BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes
							) AS depositos_result
					) AS result
				GROUP BY
					rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes";
			}
			else
			{
				$sql = "SELECT
					num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
					(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
					anio,
					mes,
					depositos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							cheques
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos = 134
							AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS nominas,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima_iva,
					(CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) + COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) - (CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) AS costo_venta,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios_iva,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (179, 180, 181, 187)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS impuestos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (141)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS imss,
					COALESCE(depositos * (COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (3, 4)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) * 100 / COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (1, 2, 3, 4, 8, 9)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) / 100) * COALESCE((
						SELECT
							porcentaje
						FROM
							porcentajes_ieps
						WHERE
							num_cia = depositos_result.num_cia
							AND anio = depositos_result.anio
							AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
					CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END AS inventario,
					COALESCE((
						SELECT
							SUM(saldo_libros) + COALESCE((
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
									estado_cuenta
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

							), 0)
						FROM
							saldos
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS saldo_banco,
					COALESCE((
						SELECT
							SUM(total)
						FROM
							historico_proveedores
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
					), 0) AS saldo_pro,
					COALESCE((
						SELECT
							monto
						FROM
							perdidas
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS perdidas
				FROM
					(
						SELECT
							COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
							cc.tipo_cia,
							EXTRACT(YEAR FROM ec.fecha) AS anio,
							EXTRACT(MONTH FROM ec.fecha) AS mes,
							SUM(
								CASE
									WHEN cc.tipo_cia = 1 THEN
										ec.importe
									WHEN cc.tipo_cia = 2 THEN
										ec.importe / 1.16
									ELSE
										ec.importe
								END
							) AS depositos
						FROM
							estado_cuenta ec
							LEFT JOIN catalogo_companias cc ON (
								cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
							)
						WHERE
							" . implode(' AND ', $condiciones) . "
						GROUP BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes,
							cc.tipo_cia
						ORDER BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes
					) AS depositos_result";
			}

			$result = $db->query($sql);

			if ($result)
			{
				$datos = array();

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'		=> $row['nombre_cia'],
							'alias_cia'			=> $row['alias_cia'],
							'rfc_cia'			=> $row['rfc_cia'],
							'depositos'			=> array_fill(0, 13, 0),
							'nominas'			=> array_fill(0, 13, 0),
							'inventario'		=> array_fill(0, 13, 0),
							'mat_prima'			=> array_fill(0, 13, 0),
							'mat_prima_iva'		=> array_fill(0, 13, 0),
							'costo_venta'		=> array_fill(0, 13, 0),
							'varios'			=> array_fill(0, 13, 0),
							'varios_iva'		=> array_fill(0, 13, 0),
							'impuestos'			=> array_fill(0, 13, 0),
							'imss'				=> array_fill(0, 13, 0),
							'ieps'				=> array_fill(0, 13, 0),
							'saldo_banco'		=> array_fill(0, 13, 0),
							'saldo_pro'			=> array_fill(0, 13, 0),
							'totales'			=> array_fill(0, 13, 0),
							'total_estimado'	=> array_fill(0, 13, 0),
							'diferencia'		=> array_fill(0, 13, 0),
							'bancos_pros'		=> array_fill(0, 13, 0),
							'perdidas'			=> 0
						);

					}

					$mes = $row['anio'] < $_REQUEST['anio'] ? 0 : $row['mes'];

					$datos[$num_cia]['depositos'][$mes] = $row['depositos'];
					$datos[$num_cia]['nominas'][$mes] = $row['nominas'];
					$datos[$num_cia]['inventario'][$mes] = $row['inventario'];
					$datos[$num_cia]['mat_prima'][$mes] = $row['mat_prima'];
					$datos[$num_cia]['mat_prima_iva'][$mes] = $row['mat_prima_iva'];
					$datos[$num_cia]['costo_venta'][$mes] = $row['costo_venta'];
					$datos[$num_cia]['varios'][$mes] = $row['varios'];
					$datos[$num_cia]['varios_iva'][$mes] = $row['varios_iva'];
					$datos[$num_cia]['impuestos'][$mes] = $row['impuestos'];
					$datos[$num_cia]['imss'][$mes] = $row['imss'];
					$datos[$num_cia]['ieps'][$mes] = $row['ieps'];
					$datos[$num_cia]['saldo_banco'][$mes] = $row['saldo_banco'];
					$datos[$num_cia]['saldo_pro'][$mes] = $row['saldo_pro'];
					$datos[$num_cia]['totales'][$mes] = $row['depositos'] - $row['nominas'] - $row['costo_venta']/* - $row['mat_prima']*/ - $row['varios']/* - $row['impuestos']*/ - $row['imss'];
					$datos[$num_cia]['total_estimado'][$mes] = $datos[$num_cia]['depositos'][$mes] * 0.05;
					$datos[$num_cia]['diferencia'][$mes] = $datos[$num_cia]['totales'][$mes] - $datos[$num_cia]['total_estimado'][$mes];
					$datos[$num_cia]['bancos_pros'][$mes] = $row['saldo_banco'] - $row['saldo_pro'];
					$datos[$num_cia]['perdidas'] = $row['perdidas'];
				}

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(0, 4, utf8_decode('COMPARATIVO DE EFECTIVOS CONTRA GASTOS VARIOS'), 0, 1, 'C');
						$this->Cell(0, 4, utf8_decode('AÑO ' . $_REQUEST['anio']), 'B', 1, 'C');

						$this->Ln(5);
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, utf8_decode('PÁGINA ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('L', 'mm', array(216, 340));

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('L', array(216, 340));

				$rows = 0;

				foreach ($datos as $num_cia => $cia)
				{
					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(318, 4, $num_cia . ' ' . $cia['nombre_cia'], 1, 0);

					$pdf->Ln();

					$pdf->Cell(38, 4, utf8_decode('CONCEPTO'), 1, 0);

					$pdf->Cell(20, 4, mb_strtoupper(substr("Diciembre", 0, 3)), 1, 0);

					foreach ($_meses as $mes => $nombre_mes)
					{
						$pdf->Cell(20, 4, mb_strtoupper(substr($nombre_mes, 0, 3)), 1, 0);
					}

					$pdf->Cell(20, 4, utf8_decode('TOTAL'), 1, 0);

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('SALDO EN LIBROS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['saldo_banco'] as $saldo_banco)
					{
						$pdf->Cell(20, 4, $saldo_banco != 0 ? number_format($saldo_banco, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->Cell(20, 4, $cia['saldo_banco'][$mes_max[0]['mes']] != 0 ? number_format($cia['saldo_banco'][$mes_max[0]['mes']], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('SALDO PROVEEDORES'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['saldo_pro'] as $saldo_pro)
					{
						$pdf->Cell(20, 4, $saldo_pro != 0 ? number_format($saldo_pro, 2) : '', 1, 0, 'R');

						if ($mes <= $mes_max[0]['mes'])
						{
							$ultimo_saldo_pro = $saldo_pro;
						}
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(20, 4, $cia['saldo_pro'][$mes_max[0]['mes']] != 0 ? number_format($cia['saldo_pro'][$mes_max[0]['mes']], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('BANCOS - PROV.'), 1, 0);

					foreach ($cia['bancos_pros'] as $total)
					{
						if ($total <= 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 204);
						}

						$pdf->Cell(20, 4, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					if ($cia['bancos_pros'][$mes_max[0]['mes']] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(20, 4, $cia['bancos_pros'][$mes_max[0]['mes']] != 0 ? number_format($cia['bancos_pros'][$mes_max[0]['mes']], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('VENTAS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 0, 204);

					foreach ($cia['depositos'] as $depositos)
					{
						$pdf->Cell(20, 4, $depositos != 0 ? number_format($depositos, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['depositos']);

					$pdf->Cell(20, 4, array_sum($cia['depositos']) != 0 ? number_format(array_sum($cia['depositos']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('GASTOS Y NÓMINAS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(204, 0, 0);

					foreach ($cia['nominas'] as $mes => $nominas)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $nominas != 0 ? number_format($nominas, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['nominas']);

					$pdf->Cell(20, 4, array_sum($cia['nominas']) != 0 ? number_format(array_sum($cia['nominas']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('INVENTARIO'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['inventario'] as $mes => $inventario)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $inventario != 0 ? number_format($inventario, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(20, 4, $cia['inventario'][$mes_max[0]['mes']] != 0 ? number_format($cia['inventario'][$mes_max[0]['mes']], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('MATERIAS PRIMAS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['mat_prima'] as $mes => $mat_prima)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $mat_prima != 0 ? number_format($mat_prima, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['mat_prima']);

					$pdf->Cell(20, 4, array_sum($cia['mat_prima']) != 0 ? number_format(array_sum($cia['mat_prima']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('I.V.A.'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['mat_prima_iva'] as $mes => $mat_prima_iva)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $mat_prima_iva != 0 ? number_format($mat_prima_iva, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['mat_prima_iva']);

					$pdf->Cell(20, 4, array_sum($cia['mat_prima_iva']) != 0 ? number_format(array_sum($cia['mat_prima_iva']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('COSTO VENTA'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(204, 0, 0);

					foreach ($cia['costo_venta'] as $mes => $costo_venta)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $costo_venta != 0 ? number_format($costo_venta, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['costo_venta']);

					$pdf->Cell(20, 4, array_sum($cia['costo_venta']) != 0 ? number_format(array_sum($cia['costo_venta']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('VARIOS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(204, 0, 0);

					foreach ($cia['varios'] as $mes => $varios)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $varios != 0 ? number_format($varios, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['varios']);

					$pdf->Cell(20, 4, array_sum($cia['varios']) != 0 ? number_format(array_sum($cia['varios']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('I.V.A.'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['varios_iva'] as $mes => $varios_iva)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $varios_iva != 0 ? number_format($varios_iva, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['varios_iva']);

					$pdf->Cell(20, 4, array_sum($cia['varios_iva']) != 0 ? number_format(array_sum($cia['varios_iva']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('IMPUESTOS'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(204, 0, 0);

					foreach ($cia['impuestos'] as $mes => $impuestos)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $impuestos != 0 ? number_format($impuestos, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['impuestos']);

					$pdf->Cell(20, 4, array_sum($cia['impuestos']) != 0 ? number_format(array_sum($cia['impuestos']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('I.M.S.S.'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(204, 0, 0);

					foreach ($cia['imss'] as $mes => $imss)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $imss != 0 ? number_format($imss, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['imss']);

					$pdf->Cell(20, 4, array_sum($cia['imss']) != 0 ? number_format(array_sum($cia['imss']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('I.E.P.S.'), 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 102, 0);

					foreach ($cia['ieps'] as $mes => $ieps)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						$pdf->Cell(20, 4, $ieps != 0 ? number_format($ieps, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['ieps']);

					$pdf->Cell(20, 4, array_sum($cia['ieps']) != 0 ? number_format(array_sum($cia['ieps']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('TOTAL'), 1, 0);

					foreach ($cia['totales'] as $mes => $total)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						if ($total <= 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 204);
						}

						$pdf->Cell(20, 4, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['totales']);

					if (array_sum($cia['totales']) <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(20, 4, array_sum($cia['totales']) != 0 ? number_format(array_sum($cia['totales']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('TOTAL ESTIMADO'), 1, 0);

					foreach ($cia['total_estimado'] as $mes => $total_estimado)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						if ($total_estimado <= 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 204);
						}

						$pdf->Cell(20, 4, $total_estimado != 0 ? number_format($total_estimado, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['total_estimado']);

					if (array_sum($cia['total_estimado']) <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(20, 4, array_sum($cia['total_estimado']) != 0 ? number_format(array_sum($cia['total_estimado']), 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('PERDIDAS'), 1, 0);

					foreach ($cia['diferencia'] as $mes => $diferencia)
					{
						$pdf->Cell(20, 4, '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					if ($cia['perdidas'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(20, 4, $cia['perdidas'] != 0 ? number_format($cia['perdidas'], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(38, 4, utf8_decode('DIFERENCIA'), 1, 0);

					foreach ($cia['diferencia'] as $mes => $diferencia)
					{
						if ($mes == 0)
						{
							$pdf->Cell(20, 4, '', 1, 0, 'R');

							continue;
						}

						if ($diferencia <= 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 204);
						}

						$pdf->Cell(20, 4, $diferencia != 0 ? number_format($diferencia, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					array_shift($cia['diferencia']);

					if (array_sum($cia['diferencia']) <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(20, 4, array_sum($cia['diferencia']) - $cia['perdidas'] != 0 ? number_format(array_sum($cia['diferencia']) - $cia['perdidas'], 2) : '', 1, 0, 'R');

					$pdf->Ln();

					$pdf->Cell(318, 4, '', 1, 0);

					if ($rows < 1)
					{
						$pdf->Ln();

						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', array(216, 340));
						$pdf->SetMargins(5, 5, 5);
					}
				}

				$pdf->Output('ReporteNomina.pdf', 'I');
			}

			break;

		case 'exportar':
			$mes_max = $db->query("SELECT COALESCE(MAX(mes)) AS mes FROM balances_pan WHERE anio = {$_REQUEST['anio']}");

			if ($mes_max[0]['mes'] == 0)
			{
				return FALSE;
			}

			$fecha1 = "01/12/" . ($_REQUEST['anio'] - 1);
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2, 3, 4)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS alias_cia,
					rfc_cia,
					anio,
					mes,
					SUM(depositos) AS depositos,
					SUM(nominas) AS nominas,
					SUM(inventario) AS inventario,
					SUM(mat_prima) AS mat_prima,
					SUM(mat_prima_iva) AS mat_prima_iva,
					SUM(costo_venta) AS costo_venta,
					SUM(varios) AS varios,
					SUM(varios_iva) AS varios_iva,
					SUM(impuestos) AS impuestos,
					SUM(imss) AS imss,
					SUM(ieps) AS ieps,
					SUM(saldo_banco) AS saldo_banco,
					SUM(saldo_pro) AS saldo_pro,
					SUM(perdidas) AS perdidas
				FROM
					(
						SELECT
							num_cia,
							(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
							(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
							(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
							anio,
							mes,
							depositos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									cheques
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos = 134
									AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS nominas,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS mat_prima_iva,
							(CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_ant
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) + COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (33, 90)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) - (CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END) AS costo_venta,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios,
							COALESCE((
								SELECT
									SUM(COALESCE(iva, 0))
								FROM
									facturas
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS varios_iva,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (179, 180, 181, 187)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS impuestos,
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									movimiento_gastos
								WHERE
									num_cia = depositos_result.num_cia
									AND codgastos IN (141)
									--AND fecha_cancelacion IS NULL
									AND importe > 0
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							), 0) AS imss,
							COALESCE(depositos * (COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (3, 4)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) * 100 / COALESCE((
								SELECT
									SUM(imp_produccion)
								FROM
									produccion
								WHERE
									num_cia = depositos_result.num_cia
									AND cod_turnos IN (1, 2, 3, 4, 8, 9)
									AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
							)) / 100) * COALESCE((
								SELECT
									porcentaje
								FROM
									porcentajes_ieps
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
							CASE
								WHEN tipo_cia = 1 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_pan
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
								WHEN tipo_cia = 2 THEN
									COALESCE((
										SELECT
											inv_act
										FROM
											balances_ros
										WHERE
											num_cia = depositos_result.num_cia
											AND anio = depositos_result.anio
											AND mes = depositos_result.mes
									), 0)
							END AS inventario,
							COALESCE((
								SELECT
									SUM(saldo_libros) + COALESCE((
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
											estado_cuenta
										WHERE
											num_cia = depositos_result.num_cia
											AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

									), 0)
								FROM
									saldos
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS saldo_banco,
							COALESCE((
								SELECT
									SUM(total)
								FROM
									historico_proveedores
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
							), 0) AS saldo_pro,
							COALESCE((
								SELECT
									monto
								FROM
									perdidas
								WHERE
									num_cia = depositos_result.num_cia
							), 0) AS perdidas
						FROM
							(
								SELECT
									COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
									cc.tipo_cia,
									EXTRACT(YEAR FROM ec.fecha) AS anio,
									EXTRACT(MONTH FROM ec.fecha) AS mes,
									SUM(
										CASE
											WHEN cc.tipo_cia = 1 THEN
												ec.importe
											WHEN cc.tipo_cia = 2 THEN
												ec.importe / 1.16
											ELSE
												ec.importe
										END
									) AS depositos
								FROM
									estado_cuenta ec
									LEFT JOIN catalogo_companias cc ON (
										cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
									)
								WHERE
									" . implode(' AND ', $condiciones) . "
								GROUP BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes,
									cc.tipo_cia
								ORDER BY
									COALESCE(ec.num_cia_sec, ec.num_cia),
									anio,
									mes
							) AS depositos_result
					) AS result
				GROUP BY
					rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes";
			}
			else
			{
				$sql = "SELECT
					num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS nombre_cia,
					(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS alias_cia,
					(SELECT rfc FROM catalogo_companias WHERE num_cia = depositos_result.num_cia) AS rfc_cia,
					anio,
					mes,
					depositos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							cheques
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos = 134
							AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS nominas,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS mat_prima_iva,
					(CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_ant
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) + COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (33, 90)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) + COALESCE((
						SELECT
							SUM(credito)
						FROM
							total_fac_ros
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) - (CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END) AS costo_venta,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios,
					COALESCE((
						SELECT
							SUM(COALESCE(iva, 0))
						FROM
							facturas
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187, 224, 999, 227, 104)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS varios_iva,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (179, 180, 181, 187)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS impuestos,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = depositos_result.num_cia
							AND codgastos IN (141)
							--AND fecha_cancelacion IS NULL
							AND importe > 0
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					), 0) AS imss,
					COALESCE(depositos * (COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (3, 4)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) * 100 / COALESCE((
						SELECT
							SUM(imp_produccion)
						FROM
							produccion
						WHERE
							num_cia = depositos_result.num_cia
							AND cod_turnos IN (1, 2, 3, 4, 8, 9)
							AND fecha BETWEEN ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE AND ('01-' || depositos_result.mes || '-' || depositos_result.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					)) / 100) * COALESCE((
						SELECT
							porcentaje
						FROM
							porcentajes_ieps
						WHERE
							num_cia = depositos_result.num_cia
							AND anio = depositos_result.anio
							AND mes = depositos_result.mes)) / 100 * 0.08, 0) AS ieps,
					CASE
						WHEN tipo_cia = 1 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_pan
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
						WHEN tipo_cia = 2 THEN
							COALESCE((
								SELECT
									inv_act
								FROM
									balances_ros
								WHERE
									num_cia = depositos_result.num_cia
									AND anio = depositos_result.anio
									AND mes = depositos_result.mes
							), 0)
					END AS inventario,
					COALESCE((
						SELECT
							SUM(saldo_libros) + COALESCE((
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
									estado_cuenta
								WHERE
									num_cia = depositos_result.num_cia
									AND fecha BETWEEN ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE

							), 0)
						FROM
							saldos
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS saldo_banco,
					COALESCE((
						SELECT
							SUM(total)
						FROM
							historico_proveedores
						WHERE
							num_cia = depositos_result.num_cia
							AND fecha_arc = ('01' || '-' || depositos_result.mes || '-' || depositos_result.anio)::DATE - INTERVAL '1 DAY'
					), 0) AS saldo_pro,
					COALESCE((
						SELECT
							monto
						FROM
							perdidas
						WHERE
							num_cia = depositos_result.num_cia
					), 0) AS perdidas
				FROM
					(
						SELECT
							COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
							cc.tipo_cia,
							EXTRACT(YEAR FROM ec.fecha) AS anio,
							EXTRACT(MONTH FROM ec.fecha) AS mes,
							SUM(
								CASE
									WHEN cc.tipo_cia = 1 THEN
										ec.importe
									WHEN cc.tipo_cia = 2 THEN
										ec.importe / 1.16
									ELSE
										ec.importe
								END
							) AS depositos
						FROM
							estado_cuenta ec
							LEFT JOIN catalogo_companias cc ON (
								cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
							)
						WHERE
							" . implode(' AND ', $condiciones) . "
						GROUP BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes,
							cc.tipo_cia
						ORDER BY
							COALESCE(ec.num_cia_sec, ec.num_cia),
							anio,
							mes
					) AS depositos_result";
			}

			$result = $db->query($sql);

			if ($result)
			{
				$datos = array();

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'		=> $row['nombre_cia'],
							'alias_cia'			=> $row['alias_cia'],
							'rfc_cia'			=> $row['rfc_cia'],
							'depositos'			=> array_fill(0, 13, 0),
							'nominas'			=> array_fill(0, 13, 0),
							'inventario'		=> array_fill(0, 13, 0),
							'mat_prima'			=> array_fill(0, 13, 0),
							'mat_prima_iva'		=> array_fill(0, 13, 0),
							'costo_venta'		=> array_fill(0, 13, 0),
							'varios'			=> array_fill(0, 13, 0),
							'varios_iva'		=> array_fill(0, 13, 0),
							'impuestos'			=> array_fill(0, 13, 0),
							'imss'				=> array_fill(0, 13, 0),
							'ieps'				=> array_fill(0, 13, 0),
							'saldo_banco'		=> array_fill(0, 13, 0),
							'saldo_pro'			=> array_fill(0, 13, 0),
							'totales'			=> array_fill(0, 13, 0),
							'total_estimado'	=> array_fill(0, 13, 0),
							'diferencia'		=> array_fill(0, 13, 0),
							'bancos_pros'		=> array_fill(0, 13, 0)
						);

					}

					$mes = $row['anio'] < $_REQUEST['anio'] ? 0 : $row['mes'];

					$datos[$num_cia]['depositos'][$mes] = $mes > 0 ? $row['depositos'] : NULL;
					$datos[$num_cia]['nominas'][$mes] = $mes > 0 ? $row['nominas'] : NULL;
					$datos[$num_cia]['inventario'][$mes] = $mes > 0 ? $row['inventario'] : NULL;
					$datos[$num_cia]['mat_prima'][$mes] = $mes > 0 ? $row['mat_prima'] : NULL;
					$datos[$num_cia]['mat_prima_iva'][$mes] = $mes > 0 ? $row['mat_prima_iva'] : NULL;
					$datos[$num_cia]['costo_venta'][$mes] = $mes > 0 ? $row['costo_venta'] : NULL;
					$datos[$num_cia]['varios'][$mes] = $mes > 0 ? $row['varios'] : NULL;
					$datos[$num_cia]['varios_iva'][$mes] = $mes > 0 ? $row['varios_iva'] : NULL;
					$datos[$num_cia]['impuestos'][$mes] = $mes > 0 ? $row['impuestos'] : NULL;
					$datos[$num_cia]['imss'][$mes] = $mes > 0 ? $row['imss'] : NULL;
					$datos[$num_cia]['ieps'][$mes] = $mes > 0 ? $row['ieps'] : NULL;
					$datos[$num_cia]['saldo_banco'][$mes] = $row['saldo_banco'];
					$datos[$num_cia]['saldo_pro'][$mes] = $row['saldo_pro'];
					$datos[$num_cia]['totales'][$mes] = $mes > 0 ? $row['depositos'] - $row['nominas'] - $row['costo_venta']/* - $row['mat_prima']*/ - $row['varios']/* - $row['impuestos']*/ - $row['imss'] : NULL;
					$datos[$num_cia]['total_estimado'][$mes] = $mes > 0 ? $datos[$num_cia]['depositos'][$mes] * 0.05 : NULL;
					$datos[$num_cia]['diferencia'][$mes] = $mes > 0 ? $datos[$num_cia]['totales'][$mes] - $datos[$num_cia]['total_estimado'][$mes] : NULL;
					$datos[$num_cia]['bancos_pros'][$mes] = $row['saldo_banco'] - $row['saldo_pro'];
				}

				$data = '"COMPARATIVO DE EFECTIVOS CONTRA GASTOS VARIOS"' . "\n";
				$data .= '"AÑO ' . $_REQUEST['anio'] . '"' . "\n\n";

				foreach ($datos as $num_cia => $cia)
				{
					$data .= '"' . $num_cia . ' ' . $cia['nombre_cia'] . '"' . "\n";
					$data .= '"CONCEPTO","DIC","ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC","TOTAL"' . "\n";

					$data .= '"SALDO BANCO","' . implode('","', $cia['saldo_banco']) . '","' . $cia['saldo_banco'][$mes_max[0]['mes']] . '"' . "\n";
					$data .= '"SALDO PROVEEDORES","' . implode('","', $cia['saldo_pro']) . '","' . $cia['saldo_pro'][$mes_max[0]['mes']] . '"' . "\n";
					$data .= '"BANCOS - PROVEEDORES","' . implode('","', $cia['bancos_pros']) . '","' . $cia['bancos_pros'][$mes_max[0]['mes']] . '"' . "\n";

					$data .= '"VENTAS","' . implode('","', $cia['depositos']) . '","';
					array_shift($cia['depositos']);
					$data .= array_sum($cia['depositos']) . '"' . "\n";

					$data .= '"NOMINAS","' . implode('","', $cia['nominas']) . '","';
					array_shift($cia['nominas']);
					$data .= array_sum($cia['nominas']) . '"' . "\n";

					$data .= '"INVENTARIO","' . implode('","', $cia['inventario']) . '","';
					array_shift($cia['inventario']);
					$data .= $cia['inventario'][$mes_max[0]['mes']] . '"' . "\n";

					$data .= '"MATERIAS PRIMAS","' . implode('","', $cia['mat_prima']) . '","';
					array_shift($cia['mat_prima']);
					$data .= array_sum($cia['mat_prima']) . '"' . "\n";

					$data .= '"I.V.A.","' . implode('","', $cia['mat_prima_iva']) . '","';
					array_shift($cia['mat_prima_iva']);
					$data .= array_sum($cia['mat_prima_iva']) . '"' . "\n";

					$data .= '"COSTO VENTA","' . implode('","', $cia['costo_venta']) . '","';
					array_shift($cia['costo_venta']);
					$data .= array_sum($cia['costo_venta']) . '"' . "\n";

					$data .= '"VARIOS","' . implode('","', $cia['varios']) . '","';
					array_shift($cia['varios']);
					$data .= array_sum($cia['varios']) . '"' . "\n";

					$data .= '"I.V.A.","' . implode('","', $cia['varios_iva']) . '","';
					array_shift($cia['varios_iva']);
					$data .= array_sum($cia['varios_iva']) . '"' . "\n";

					$data .= '"IMPUESTOS","' . implode('","', $cia['impuestos']) . '","';
					array_shift($cia['impuestos']);
					$data .= array_sum($cia['impuestos']) . '"' . "\n";

					$data .= '"I.M.S.S.","' . implode('","', $cia['imss']) . '","';
					array_shift($cia['imss']);
					$data .= array_sum($cia['imss']) . '"' . "\n";

					$data .= '"TOTAL","' . implode('","', $cia['totales']) . '","';
					array_shift($cia['totales']);
					$data .= array_sum($cia['totales']) . '"' . "\n";

					$data .= '"TOTAL ESTIMADO","' . implode('","', $cia['total_estimado']) . '","';
					array_shift($cia['total_estimado']);
					$data .= array_sum($cia['total_estimado']) . '"' . "\n";

					$data .= '"PERDIDAS","","","","","","","","","","","","","","' . $cia['perdidas'] . '"' . "\n";

					$data .= '"DIFERENCIA","' . implode('","', $cia['diferencia']) . '","';
					array_shift($cia['diferencia']);
					$data .= (array_sum($cia['diferencia']) - $cia['perdidas']) . '"' . "\n";

					$data .= "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=ComparativoEfectivosGastosVarios.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosGastosVarios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
