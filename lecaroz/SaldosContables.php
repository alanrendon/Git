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
			$tpl = new TemplatePower('plantillas/ban/SaldosContablesInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$db->query("UPDATE cheques SET acuenta = FALSE WHERE acuenta IS NULL");

			$fecha1 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n') - 1, 1, date('Y')) : mktime(0, 0, 0, date('n'), 1, date('Y')));
			$fecha2 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('j'), date('Y')));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$mes_max = $db->query("SELECT EXTRACT(MONTH FROM MAX(fecha)) AS mes, EXTRACT(YEAR FROM MAX(fecha)) AS anio FROM balances_pan");

			$fecha_bal = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $mes_max[0]['anio']));

			$condiciones = array();

			$condiciones[] = "cc.num_cia < 900";

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
					$condiciones[] = 'cc.rfc IN (SELECT rfc FROM catalogo_companias WHERE cc.num_cia IN (' . implode(', ', $cias) . ') GROUP BY rfc)';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 's.cuenta = ' . $_REQUEST['banco'];
			}


			$sql = "SELECT
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS num_cia,
				(SELECT nombre FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS nombre_cia,
				cc.rfc AS rfc_cia,
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
					LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) AS saldo_libros,
				SUM(s.saldo_bancos) AS saldo_bancos,
				COALESCE((
					SELECT
						SUM(ecpnc.importe)
					FROM
						estado_cuenta ecpnc
						LEFT JOIN cheques cpnc USING (num_cia, folio, cuenta, fecha)
						LEFT JOIN catalogo_companias ccpnc USING (num_cia)
					WHERE
						ccpnc.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecpnc.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecpnc.fecha_con IS NULL
						AND ecpnc.tipo_mov = TRUE
						AND ecpnc.cod_mov IN (5, 41)
						AND cpnc.acuenta = FALSE
				), 0) AS pagos_no_cobrados,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS saldo_proveedores,
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
					LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) - COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS dif_libros_proveedores,
				COALESCE((
					SELECT
						SUM(inventario)
					FROM
						(
							SELECT
								inv_act AS inventario
							FROM
								balances_pan bpinv
								LEFT JOIN catalogo_companias ccbpinv USING (num_cia)
							WHERE
								ccbpinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_pan)

							UNION

							SELECT
								inv_act
							FROM
								balances_ros brinv
								LEFT JOIN catalogo_companias ccbrinv USING (num_cia)
							WHERE
								ccbrinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_ros)
						) result
				), 0) AS inventario_inicial,
				COALESCE((
					SELECT
						SUM(monto)
					FROM
						perdidas
						LEFT JOIN catalogo_companias ccper USING (num_cia)
						WHERE
							ccper.rfc = cc.rfc
				), 0) AS perdidas,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta ecdev
						LEFT JOIN catalogo_companias ccdev USING (num_cia)
					WHERE
						ccdev.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecdev.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecdev.cod_mov = 18
						AND ecdev.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha2}'
				), 0) AS devoluciones_iva,
				CASE
					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
							SELECT
								SUM(
									CASE
										WHEN ccven.tipo_cia = 1 THEN
											ecven.importe
										WHEN ccven.tipo_cia = 2 THEN
											ecven.importe / 1.16
										ELSE
											ecven.importe
									END
								)
							FROM
								estado_cuenta ecven
								LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
							WHERE
								ccven.rfc = cc.rfc
								" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
								AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
								AND ecven.cod_mov IN (1, 16, 44, 99)
						), 0) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								cheques cnom
								LEFT JOIN catalogo_companias ccnom USING (num_cia)
							WHERE
								ccnom.rfc = cc.rfc
								AND codgastos = 134
								AND fecha_cancelacion IS NULL
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0) - (
							/*COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
						)/* - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimp
		 						LEFT JOIN catalogo_companias ccimp USING (num_cia)
		 					WHERE
		 						ccimp.rfc = cc.rfc
		 						AND codgastos IN (179, 180, 181, 187)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)*/ - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimss
		 						LEFT JOIN catalogo_companias ccimss USING (num_cia)
		 					WHERE
		 						ccimss.rfc = cc.rfc
		 						AND codgastos IN (141)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								facturas fvarios
								LEFT JOIN catalogo_companias ccvarios USING (num_cia)
							WHERE
								ccvarios.rfc = cc.rfc
								AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)
					ELSE
						0
				END AS utilidad_estimada,
				CASE
					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
		 					SELECT
		 						SUM(
		 							CASE
		 								WHEN ccven.tipo_cia = 1 THEN
		 									ecven.importe
		 								WHEN ccven.tipo_cia = 2 THEN
		 									ecven.importe / 1.16
		 								ELSE
		 									ecven.importe
		 							END
		 						)
		 					FROM
		 						estado_cuenta ecven
		 						LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
		 					WHERE
		 						ccven.rfc = cc.rfc
		 						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
		 						AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 						AND ecven.cod_mov IN (1, 16, 44, 99)
		 				), 0) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						cheques cnom
		 						LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 					WHERE
		 						ccnom.rfc = cc.rfc
		 						AND codgastos = 134
		 						AND fecha_cancelacion IS NULL
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0) - (
		 					/*COALESCE((
		 						SELECT
		 							SUM(importe)
		 						FROM
		 							facturas fnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND codgastos IN (33, 90)
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0) + COALESCE((
		 						SELECT
		 							SUM(credito)
		 						FROM
		 							total_fac_ros frnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
		 				) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						facturas fvar
		 						LEFT JOIN catalogo_companias ccvar USING (num_cia)
		 					WHERE
		 						ccvar.rfc = cc.rfc
		 						AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)/* - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimp
		 						LEFT JOIN catalogo_companias ccimp USING (num_cia)
		 					WHERE
		 						ccimp.rfc = cc.rfc
		 						AND codgastos IN (179, 180, 181, 187)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)*/ - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimss
		 						LEFT JOIN catalogo_companias ccimss USING (num_cia)
		 					WHERE
		 						ccimss.rfc = cc.rfc
		 						AND codgastos IN (141)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)) - COALESCE((
		 					SELECT
		 						SUM(
		 							CASE
		 								WHEN ccven.tipo_cia = 1 THEN
		 									ecven.importe
		 								WHEN ccven.tipo_cia = 2 THEN
		 									ecven.importe / 1.16
		 								ELSE
		 									ecven.importe
		 							END
		 						)
		 					FROM
		 						estado_cuenta ecven
		 						LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
		 					WHERE
		 						ccven.rfc = cc.rfc
		 						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
		 						AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 						AND ecven.cod_mov IN (1, 16, 44, 99)
		 				), 0) * 0.05
		 			ELSE
		 				0
 				END AS diferencia_contable
			FROM
				saldos s
				LEFT JOIN catalogo_companias cc USING (num_cia)
			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			GROUP BY
				rfc_cia
			ORDER BY
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1)";//echo $sql;die;

			$query = $db->query($sql);

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/ban/SaldosContablesConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('banco', isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? '<img src="imagenes/Banorte16x16.png" /> BANORTE' : '<img src="imagenes/Santander16x16.png" /> SANTANDER') : 'BANCOS CONSOLIDADOS');

				$total = array(
					'saldo_bancos'				=> 0,
					'saldo_libros'				=> 0,
					'pagos_no_cobrados'			=> 0,
					'saldo_proveedores'			=> 0,
					'dif_libros_proveedores'	=> 0,
					'utilidad_estimada'			=> 0,
					'diferencia_contable'		=> 0,
					'inventario_inicial'		=> 0,
					'perdidas'					=> 0,
					'devoluciones_iva'			=> 0
				);

				foreach ($query as $row)
				{
					if (abs(round($row['saldo_bancos'], 2)) == 0
						&& abs(round($row['saldo_libros'], 2)) == 0
						&& abs(round($row['pagos_no_cobrados'], 2)) == 0
						&& abs(round($row['saldo_proveedores'], 2)) == 0
						&& abs(round($row['dif_libros_proveedores'], 2)) == 0
						&& abs(round($row['utilidad_estimada'], 2)) == 0
						&& abs(round($row['diferencia_contable'], 2)) == 0
						&& abs(round($row['inventario_inicial'], 2)) == 0
						&& abs(round($row['perdidas'], 2)) == 0
						&& abs(round($row['devoluciones_iva'], 2)) == 0)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('rfc_cia', utf8_encode($row['rfc_cia']));
					$tpl->assign('saldo_bancos', $row['saldo_bancos'] != 0 ? '<span class="' . ($row['saldo_bancos'] < 0 ? 'red' : 'blue') . '">' . number_format($row['saldo_bancos'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_libros', $row['saldo_libros'] != 0 ? '<span class="' . ($row['saldo_libros'] < 0 ? 'red' : 'blue') . '">' . number_format($row['saldo_libros'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('pagos_no_cobrados', $row['pagos_no_cobrados'] != 0 ? '<span class="' . ($row['pagos_no_cobrados'] < 0 ? 'red' : 'blue') . '">' . number_format($row['pagos_no_cobrados'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_proveedores', $row['saldo_proveedores'] != 0 ? '<span class="' . ($row['saldo_proveedores'] < 0 ? 'red' : 'blue') . '">' . number_format($row['saldo_proveedores'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('dif_libros_proveedores', $row['dif_libros_proveedores'] != 0 ? '<span class="' . ($row['dif_libros_proveedores'] < 0 ? 'red' : 'blue') . '">' . number_format($row['dif_libros_proveedores'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('utilidad_estimada', $row['utilidad_estimada'] != 0 ? '<span class="' . ($row['utilidad_estimada'] < 0 ? 'red' : 'blue') . '">' . number_format($row['utilidad_estimada'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('diferencia_contable', $row['diferencia_contable'] != 0 ? '<span class="' . ($row['diferencia_contable'] < 0 ? 'red' : 'blue') . '">' . number_format($row['diferencia_contable'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('inventario_inicial', $row['inventario_inicial'] != 0 ? '<span class="' . ($row['inventario_inicial'] < 0 ? 'red' : 'blue') . '">' . number_format($row['inventario_inicial'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('perdidas', $row['perdidas'] != 0 ? '<span class="' . ($row['perdidas'] < 0 ? 'red' : 'blue') . '">' . number_format($row['perdidas'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('devoluciones_iva', $row['devoluciones_iva'] != 0 ? '<span class="' . ($row['devoluciones_iva'] < 0 ? 'red' : 'blue') . '">' . number_format($row['devoluciones_iva'], 2) . '</span>' : '&nbsp;');

					$total['saldo_bancos'] += $row['saldo_bancos'];
					$total['saldo_libros'] += $row['saldo_libros'];
					$total['pagos_no_cobrados'] += $row['pagos_no_cobrados'];
					$total['saldo_proveedores'] += $row['saldo_proveedores'];
					$total['dif_libros_proveedores'] += $row['dif_libros_proveedores'];
					$total['utilidad_estimada'] += $row['utilidad_estimada'];
					$total['diferencia_contable'] += $row['diferencia_contable'];
					$total['inventario_inicial'] += $row['inventario_inicial'];
					$total['perdidas'] += $row['perdidas'];
					$total['devoluciones_iva'] += $row['devoluciones_iva'];
				}

				if (count($query) > 1)
				{
					$tpl->newBlock('totales');

					$tpl->assign('saldo_bancos', $total['saldo_bancos'] != 0 ? '<span class="' . ($total['saldo_bancos'] < 0 ? 'red' : 'blue') . '">' . number_format($total['saldo_bancos'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_libros', $total['saldo_libros'] != 0 ? '<span class="' . ($total['saldo_libros'] < 0 ? 'red' : 'blue') . '">' . number_format($total['saldo_libros'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('pagos_no_cobrados', $total['pagos_no_cobrados'] != 0 ? '<span class="' . ($total['pagos_no_cobrados'] < 0 ? 'red' : 'blue') . '">' . number_format($total['pagos_no_cobrados'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_proveedores', $total['saldo_proveedores'] != 0 ? '<span class="' . ($total['saldo_proveedores'] < 0 ? 'red' : 'blue') . '">' . number_format($total['saldo_proveedores'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('dif_libros_proveedores', $total['dif_libros_proveedores'] != 0 ? '<span class="' . ($total['dif_libros_proveedores'] < 0 ? 'red' : 'blue') . '">' . number_format($total['dif_libros_proveedores'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('utilidad_estimada', $total['utilidad_estimada'] != 0 ? '<span class="' . ($total['utilidad_estimada'] < 0 ? 'red' : 'blue') . '">' . number_format($total['utilidad_estimada'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('diferencia_contable', $total['diferencia_contable'] != 0 ? '<span class="' . ($total['diferencia_contable'] < 0 ? 'red' : 'blue') . '">' . number_format($total['diferencia_contable'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('inventario_inicial', $total['inventario_inicial'] != 0 ? '<span class="' . ($total['inventario_inicial'] < 0 ? 'red' : 'blue') . '">' . number_format($total['inventario_inicial'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('perdidas', $total['perdidas'] != 0 ? '<span class="' . ($total['perdidas'] < 0 ? 'red' : 'blue') . '">' . number_format($total['perdidas'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('devoluciones_iva', $total['devoluciones_iva'] != 0 ? '<span class="' . ($total['devoluciones_iva'] < 0 ? 'red' : 'blue') . '">' . number_format($total['devoluciones_iva'], 2) . '</span>' : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$db->query("UPDATE cheques SET acuenta = FALSE WHERE acuenta IS NULL");

			$fecha1 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n') - 1, 1, date('Y')) : mktime(0, 0, 0, date('n'), 1, date('Y')));
			$fecha2 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('j'), date('Y')));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$mes_max = $db->query("SELECT EXTRACT(MONTH FROM MAX(fecha)) AS mes, EXTRACT(YEAR FROM MAX(fecha)) AS anio FROM balances_pan");

			$fecha_bal = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $mes_max[0]['anio']));

			$condiciones = array();

			$condiciones[] = "cc.num_cia_saldos < 900";

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
					$condiciones[] = 'cc.rfc IN (SELECT rfc FROM catalogo_companias WHERE cc.num_cia IN (' . implode(', ', $cias) . ') GROUP BY rfc)';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 's.cuenta = ' . $_REQUEST['banco'];
			}


			$sql = "SELECT
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS num_cia,
				(SELECT nombre FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS nombre_cia,
				cc.rfc AS rfc_cia,
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
					LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) AS saldo_libros,
				SUM(s.saldo_bancos) AS saldo_bancos,
				COALESCE((
					SELECT
						SUM(ecpnc.importe)
					FROM
						estado_cuenta ecpnc
						LEFT JOIN cheques cpnc USING (num_cia, folio, cuenta, fecha)
						LEFT JOIN catalogo_companias ccpnc USING (num_cia)
					WHERE
						ccpnc.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecpnc.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecpnc.fecha_con IS NULL
						AND ecpnc.tipo_mov = TRUE
						AND ecpnc.cod_mov IN (5, 41)
						AND cpnc.acuenta = FALSE
				), 0) AS pagos_no_cobrados,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS saldo_proveedores,
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
					LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) - COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS dif_libros_proveedores,
				COALESCE((
					SELECT
						SUM(inventario)
					FROM
						(
							SELECT
								inv_act AS inventario
							FROM
								balances_pan bpinv
								LEFT JOIN catalogo_companias ccbpinv USING (num_cia)
							WHERE
								ccbpinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_pan)

							UNION

							SELECT
								inv_act
							FROM
								balances_ros brinv
								LEFT JOIN catalogo_companias ccbrinv USING (num_cia)
							WHERE
								ccbrinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_ros)
						) result
				), 0) AS inventario_inicial,
				COALESCE((
					SELECT
						SUM(monto)
					FROM
						perdidas
						LEFT JOIN catalogo_companias ccper USING (num_cia)
					WHERE
						ccper.rfc = cc.rfc
				), 0) AS perdidas,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta ecdev
						LEFT JOIN catalogo_companias ccdev USING (num_cia)
					WHERE
						ccdev.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecdev.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecdev.cod_mov = 18
						AND ecdev.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha2}'
				), 0) AS devoluciones_iva,
				CASE
					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
		 					SELECT
		 						SUM(
		 							CASE
		 								WHEN ccven.tipo_cia = 1 THEN
		 									ecven.importe
		 								WHEN ccven.tipo_cia = 2 THEN
		 									ecven.importe / 1.16
		 								ELSE
		 									ecven.importe
		 							END
		 						)
		 					FROM
		 						estado_cuenta ecven
		 						LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
		 					WHERE
		 						ccven.rfc = cc.rfc
		 						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
		 						AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 						AND ecven.cod_mov IN (1, 16, 44, 99)
		 				), 0) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						cheques cnom
		 						LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 					WHERE
		 						ccnom.rfc = cc.rfc
		 						AND codgastos = 134
		 						AND fecha_cancelacion IS NULL
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0) - (
		 					/*COALESCE((
		 						SELECT
		 							SUM(importe)
		 						FROM
		 							facturas fnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND codgastos IN (33, 90)
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0) + COALESCE((
		 						SELECT
		 							SUM(credito)
		 						FROM
		 							total_fac_ros frnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
		 				) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						facturas fvar
		 						LEFT JOIN catalogo_companias ccvar USING (num_cia)
		 					WHERE
		 						ccvar.rfc = cc.rfc
		 						AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)/* - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimp
		 						LEFT JOIN catalogo_companias ccimp USING (num_cia)
		 					WHERE
		 						ccimp.rfc = cc.rfc
		 						AND codgastos IN (179, 180, 181, 187)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)*/ - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimss
		 						LEFT JOIN catalogo_companias ccimss USING (num_cia)
		 					WHERE
		 						ccimss.rfc = cc.rfc
		 						AND codgastos IN (141)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0))
 					ELSE
 						0
 				END AS utilidad_estimada,
 				CASE
 					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
		 					SELECT
		 						SUM(
		 							CASE
		 								WHEN ccven.tipo_cia = 1 THEN
		 									ecven.importe
		 								WHEN ccven.tipo_cia = 2 THEN
		 									ecven.importe / 1.16
		 								ELSE
		 									ecven.importe
		 							END
		 						)
		 					FROM
		 						estado_cuenta ecven
		 						LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
		 					WHERE
		 						ccven.rfc = cc.rfc
		 						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
		 						AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 						AND ecven.cod_mov IN (1, 16, 44, 99)
		 				), 0) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						cheques cnom
		 						LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 					WHERE
		 						ccnom.rfc = cc.rfc
		 						AND codgastos = 134
		 						AND fecha_cancelacion IS NULL
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0) - (
		 					/*COALESCE((
		 						SELECT
		 							SUM(importe)
		 						FROM
		 							facturas fnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND codgastos IN (33, 90)
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0) + COALESCE((
		 						SELECT
		 							SUM(credito)
		 						FROM
		 							total_fac_ros frnom
		 							LEFT JOIN catalogo_companias ccnom USING (num_cia)
		 						WHERE
		 							ccnom.rfc = cc.rfc
		 							AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 					), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
		 				) - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						facturas fvar
		 						LEFT JOIN catalogo_companias ccvar USING (num_cia)
		 					WHERE
		 						ccvar.rfc = cc.rfc
		 						AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)/* - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimp
		 						LEFT JOIN catalogo_companias ccimp USING (num_cia)
		 					WHERE
		 						ccimp.rfc = cc.rfc
		 						AND codgastos IN (179, 180, 181, 187)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)*/ - COALESCE((
		 					SELECT
		 						SUM(importe)
		 					FROM
		 						movimiento_gastos mgimss
		 						LEFT JOIN catalogo_companias ccimss USING (num_cia)
		 					WHERE
		 						ccimss.rfc = cc.rfc
		 						AND codgastos IN (141)
		 						AND importe > 0
		 						AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 				), 0)) - COALESCE((
		 					SELECT
		 						SUM(
		 							CASE
		 								WHEN ccven.tipo_cia = 1 THEN
		 									ecven.importe
		 								WHEN ccven.tipo_cia = 2 THEN
		 									ecven.importe / 1.16
		 								ELSE
		 									ecven.importe
		 							END
		 						)
		 					FROM
		 						estado_cuenta ecven
		 						LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
		 					WHERE
		 						ccven.rfc = cc.rfc
		 						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
		 						AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
		 						AND ecven.cod_mov IN (1, 16, 44, 99)
		 				), 0) * 0.05
 					ELSE
 						0
 				END AS diferencia_contable
			FROM
				saldos s
				LEFT JOIN catalogo_companias cc USING (num_cia)
			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			GROUP BY
				rfc_cia
			ORDER BY
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1)";

			$query = $db->query($sql);

			if ($query)
			{
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

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 4, 'SALDOS CONTABLES', 0, 1, 'C');
						$this->Cell(0, 4, isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : 'BANCOS CONSOLIDADOS', 'B', 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(61, 4, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('BANCOS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('LIBROS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('NO COBRADOS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('PROVEEDORES'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DIFERENCIA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('UTIL. ESTIMADA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DIF. CONTABLE'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('INVENTARIO'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('PERDIDAS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DEV. DE IVA'), 1, 0, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('L', 'mm', array(216, 340));

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 6);

				$pdf->AddPage('L', array(216, 340));

				$total = array(
					'saldo_bancos'				=> 0,
					'saldo_libros'				=> 0,
					'pagos_no_cobrados'			=> 0,
					'saldo_proveedores'			=> 0,
					'dif_libros_proveedores'	=> 0,
					'utilidad_estimada'			=> 0,
					'diferencia_contable'		=> 0,
					'inventario_inicial'		=> 0,
					'perdidas'					=> 0,
					'devoluciones_iva'			=> 0
				);

				$rows = 0;

				foreach ($query as $row)
				{
					if (abs(round($row['saldo_bancos'], 2)) == 0
						&& abs(round($row['saldo_libros'], 2)) == 0
						&& abs(round($row['pagos_no_cobrados'], 2)) == 0
						&& abs(round($row['saldo_proveedores'], 2)) == 0
						&& abs(round($row['dif_libros_proveedores'], 2)) == 0
						&& abs(round($row['utilidad_estimada'], 2)) == 0
						&& abs(round($row['diferencia_contable'], 2)) == 0
						&& abs(round($row['inventario_inicial'], 2)) == 0
						&& abs(round($row['perdidas'], 2)) == 0
						&& abs(round($row['devoluciones_iva'], 2)) == 0)
					{
						continue;
					}

					$pdf->SetFont('ARIAL', '', 10);

					$pdf->SetTextColor(0, 0, 0);

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 61)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(61, 4, utf8_decode($nombre_cia), 1, 0);

					if ($row['saldo_bancos'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_bancos'] != 0 ? number_format($row['saldo_bancos'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', 'B', 10);

					if ($row['saldo_libros'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_libros'] != 0 ? number_format($row['saldo_libros'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', '', 10);

					if ($row['pagos_no_cobrados'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['pagos_no_cobrados'] != 0 ? number_format($row['pagos_no_cobrados'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', 'B', 10);

					if ($row['saldo_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_proveedores'] != 0 ? number_format($row['saldo_proveedores'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', '', 10);

					if ($row['dif_libros_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['dif_libros_proveedores'] != 0 ? number_format($row['dif_libros_proveedores'], 2) : '', 1, 0, 'R');

					if ($row['utilidad_estimada'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['utilidad_estimada'] != 0 ? number_format($row['utilidad_estimada'], 2) : '', 1, 0, 'R');

					if ($row['diferencia_contable'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['diferencia_contable'] != 0 ? number_format($row['diferencia_contable'], 2) : '', 1, 0, 'R');

					if ($row['inventario_inicial'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['inventario_inicial'] != 0 ? number_format($row['inventario_inicial'], 2) : '', 1, 0, 'R');

					if ($row['perdidas'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['perdidas'] != 0 ? number_format($row['perdidas'], 2) : '', 1, 0, 'R');

					if ($row['devoluciones_iva'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['devoluciones_iva'] != 0 ? number_format($row['devoluciones_iva'], 2) : '', 1, 1, 'R');

					$total['saldo_bancos'] += $row['saldo_bancos'];
					$total['saldo_libros'] += $row['saldo_libros'];
					$total['pagos_no_cobrados'] += $row['pagos_no_cobrados'];
					$total['saldo_proveedores'] += $row['saldo_proveedores'];
					$total['dif_libros_proveedores'] += $row['dif_libros_proveedores'];
					$total['utilidad_estimada'] += $row['utilidad_estimada'];
					$total['diferencia_contable'] += $row['diferencia_contable'];
					$total['inventario_inicial'] += $row['inventario_inicial'];
					$total['perdidas'] += $row['perdidas'];
					$total['devoluciones_iva'] += $row['devoluciones_iva'];
				}

				if (count($query) > 1)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->SetTextColor(0, 0, 0);
					$pdf->Cell(61, 4, utf8_decode('TOTALES'), 1, 0, 'R');

					if ($total['saldo_bancos'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['saldo_bancos'] != 0 ? number_format($total['saldo_bancos'], 2) : '', 1, 0, 'R');

					if ($total['saldo_libros'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['saldo_libros'] != 0 ? number_format($total['saldo_libros'], 2) : '', 1, 0, 'R');

					if ($total['pagos_no_cobrados'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['pagos_no_cobrados'] != 0 ? number_format($total['pagos_no_cobrados'], 2) : '', 1, 0, 'R');

					if ($total['saldo_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['saldo_proveedores'] != 0 ? number_format($total['saldo_proveedores'], 2) : '', 1, 0, 'R');

					if ($total['dif_libros_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['dif_libros_proveedores'] != 0 ? number_format($total['dif_libros_proveedores'], 2) : '', 1, 0, 'R');

					if ($total['diferencia_contable'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['diferencia_contable'] != 0 ? number_format($total['diferencia_contable'], 2) : '', 1, 0, 'R');

					if ($total['inventario_inicial'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['inventario_inicial'] != 0 ? number_format($total['inventario_inicial'], 2) : '', 1, 0, 'R');

					if ($total['perdidas'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['perdidas'] != 0 ? number_format($total['perdidas'], 2) : '', 1, 0, 'R');

					if ($total['devoluciones_iva'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['devoluciones_iva'] != 0 ? number_format($total['devoluciones_iva'], 2) : '', 1, 0, 'R');
				}

				$pdf->Output('ReporteNomina.pdf', 'I');
			}

			break;

		case 'exportar':
			$db->query("UPDATE cheques SET acuenta = FALSE WHERE acuenta IS NULL");

			$fecha1 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n') - 1, 1, date('Y')) : mktime(0, 0, 0, date('n'), 1, date('Y')));
			$fecha2 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('j'), date('Y')));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$mes_max = $db->query("SELECT EXTRACT(MONTH FROM MAX(fecha)) AS mes, EXTRACT(YEAR FROM MAX(fecha)) AS anio FROM balances_pan");

			$fecha_bal = date('d/m/Y', mktime(0, 0, 0, $mes_max[0]['mes'] + 1, 0, $mes_max[0]['anio']));

			$condiciones = array();

			$condiciones[] = "cc.num_cia_saldos < 900";

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
					$condiciones[] = 'cc.rfc IN (SELECT rfc FROM catalogo_companias WHERE cc.num_cia IN (' . implode(', ', $cias) . ') GROUP BY rfc)';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 's.cuenta = ' . $_REQUEST['banco'];
			}

			$sql = "SELECT
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS \"#\",
				(SELECT nombre FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) AS \"COMPAÑIA\",
				cc.rfc AS \"R.F.C.\",
				SUM(s.saldo_bancos) AS \"SALDO BANCOS\",
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
						LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) AS \"SALDO LIBROS\",
				COALESCE((
					SELECT
						SUM(ecpnc.importe)
					FROM
						estado_cuenta ecpnc
						LEFT JOIN cheques cpnc USING (num_cia, folio, cuenta, fecha)
						LEFT JOIN catalogo_companias ccpnc USING (num_cia)
					WHERE
						ccpnc.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecpnc.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecpnc.fecha_con IS NULL
						AND ecpnc.tipo_mov = TRUE
						AND ecpnc.cod_mov IN (5, 41)
						AND cpnc.acuenta = FALSE
				), 0) AS \"PAGOS NO COBRADOS\",
				COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS \"SALDO PROVEEDORES\",
				SUM(s.saldo_libros) + COALESCE((
					SELECT
						SUM(ecsl.importe)
					FROM
						estado_cuenta ecsl
						LEFT JOIN cheques csl USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl USING (num_cia)
					WHERE
						ccsl.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecsl.fecha_con IS NULL
						AND ecsl.tipo_mov = TRUE
						AND ecsl.cod_mov IN (5, 41)
						AND csl.acuenta = TRUE
				), 0) - COALESCE((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores ppsp
						LEFT JOIN catalogo_companias ccsp USING (num_cia)
					WHERE
						ccsp.rfc = cc.rfc
						AND ppsp.total > 0
				), 0) AS \"LIBROS - PROVEEDORES\",
				CASE
					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
							SELECT
								SUM(
									CASE
										WHEN ccven.tipo_cia = 1 THEN
											ecven.importe
										WHEN ccven.tipo_cia = 2 THEN
											ecven.importe / 1.16
										ELSE
											ecven.importe
									END
								)
							FROM
								estado_cuenta ecven
								LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
							WHERE
								ccven.rfc = cc.rfc
								" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
								AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
								AND ecven.cod_mov IN (1, 16, 44, 99)
						), 0) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								cheques cnom
								LEFT JOIN catalogo_companias ccnom USING (num_cia)
							WHERE
								ccnom.rfc = cc.rfc
								AND codgastos = 134
								AND fecha_cancelacion IS NULL
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0) - (
							/*COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
						) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								facturas fvar
								LEFT JOIN catalogo_companias ccvar USING (num_cia)
							WHERE
								ccvar.rfc = cc.rfc
								AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)/* - COALESCE((
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos mgimp
								LEFT JOIN catalogo_companias ccimp USING (num_cia)
							WHERE
								ccimp.rfc = cc.rfc
								AND codgastos IN (179, 180, 181, 187)
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)*/ - COALESCE((
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos mgimss
								LEFT JOIN catalogo_companias ccimss USING (num_cia)
							WHERE
								ccimss.rfc = cc.rfc
								AND codgastos IN (141)
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0))
					ELSE
						0
				END AS \"UTILIDAD ESTIMADA\",
				CASE
					WHEN (SELECT tipo_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1) IN (1, 2, 4) THEN
						(COALESCE((
							SELECT
								SUM(
									CASE
										WHEN ccven.tipo_cia = 1 THEN
											ecven.importe
										WHEN ccven.tipo_cia = 2 THEN
											ecven.importe / 1.16
										ELSE
											ecven.importe
									END
								)
							FROM
								estado_cuenta ecven
								LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
							WHERE
								ccven.rfc = cc.rfc
								" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
								AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
								AND ecven.cod_mov IN (1, 16, 44, 99)
						), 0) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								cheques cnom
								LEFT JOIN catalogo_companias ccnom USING (num_cia)
							WHERE
								ccnom.rfc = cc.rfc
								AND codgastos = 134
								AND fecha_cancelacion IS NULL
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0) - (
							/*COALESCE((
								SELECT
									SUM(importe)
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(credito)
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0)*/
							COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_pan bpiicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND bpiicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(inv_ant)
								FROM
									balances_ros briicv
									LEFT JOIN catalogo_companias cciicv USING (num_cia)
								WHERE
									cciicv.rfc = cc.rfc
									AND briicv.anio = {$mes_max[0]['anio']}
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(importe::NUMERIC, 2))
								FROM
									facturas fnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND codgastos IN (33, 90)
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) + COALESCE((
								SELECT
									SUM(ROUND(credito::NUMERIC, 2))
								FROM
									total_fac_ros frnom
									LEFT JOIN catalogo_companias ccnom USING (num_cia)
								WHERE
									ccnom.rfc = cc.rfc
									AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_pan bpifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND bpifcv.anio = {$mes_max[0]['anio']}
							), 0) - COALESCE((
								SELECT
									SUM(inv_act)
								FROM
									balances_ros brifcv
									LEFT JOIN catalogo_companias ccifcv USING (num_cia)
								WHERE
									ccifcv.rfc = cc.rfc
									AND brifcv.anio = {$mes_max[0]['anio']}
							), 0)
						) - COALESCE((
							SELECT
								SUM(importe)
							FROM
								facturas fvar
								LEFT JOIN catalogo_companias ccvar USING (num_cia)
							WHERE
								ccvar.rfc = cc.rfc
								AND codgastos NOT IN (134, 33, 90, 179, 180, 181, 187)
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)/* - COALESCE((
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos mgimp
								LEFT JOIN catalogo_companias ccimp USING (num_cia)
							WHERE
								ccimp.rfc = cc.rfc
								AND codgastos IN (179, 180, 181, 187)
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)*/ - COALESCE((
							SELECT
								SUM(importe)
							FROM
								movimiento_gastos mgimss
								LEFT JOIN catalogo_companias ccimss USING (num_cia)
							WHERE
								ccimss.rfc = cc.rfc
								AND codgastos IN (141)
								AND importe > 0
								AND fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
						), 0)) - COALESCE((
							SELECT
								SUM(
									CASE
										WHEN ccven.tipo_cia = 1 THEN
											ecven.importe
										WHEN ccven.tipo_cia = 2 THEN
											ecven.importe / 1.16
										ELSE
											ecven.importe
									END
								)
							FROM
								estado_cuenta ecven
								LEFT JOIN catalogo_companias ccven ON (ccven.num_cia = COALESCE(ecven.num_cia_sec, ecven.num_cia))
							WHERE
								ccven.rfc = cc.rfc
								" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecven.cuenta = {$_REQUEST['banco']}" : '') . "
								AND ecven.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha_bal}'
								AND ecven.cod_mov IN (1, 16, 44, 99)
						), 0) * 0.05
					ELSE
						0
				END AS \"DIFERENCIA CONTABLE\",
				COALESCE((
					SELECT
						SUM(inventario)
					FROM
						(
							SELECT
								inv_act AS inventario
							FROM
								balances_pan bpinv
								LEFT JOIN catalogo_companias ccbpinv USING (num_cia)
							WHERE
								ccbpinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_pan)

							UNION

							SELECT
								inv_act
							FROM
								balances_ros brinv
								LEFT JOIN catalogo_companias ccbrinv USING (num_cia)
							WHERE
								ccbrinv.rfc = cc.rfc
								AND fecha = (SELECT MAX(fecha) FROM balances_ros)
						) result
				), 0) AS \"INVENTARIO INICIAL\",
				COALESCE((
					SELECT
						SUM(monto)
					FROM
						perdidas
						LEFT JOIN catalogo_companias ccper USING (num_cia)
					WHERE
						ccper.rfc = cc.rfc
				), 0) AS \"PERDIDAS\",
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta ecdev
						LEFT JOIN catalogo_companias ccdev USING (num_cia)
					WHERE
						ccdev.rfc = cc.rfc
						" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecdev.cuenta = {$_REQUEST['banco']}" : '') . "
						AND ecdev.cod_mov = 18
						AND ecdev.fecha BETWEEN '01/01/{$mes_max[0]['anio']}' AND '{$fecha2}'
				), 0) AS \"DEVOLUCIONES DE IVA\"
			FROM
				saldos s
				LEFT JOIN catalogo_companias cc USING (num_cia)
			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			GROUP BY
				cc.rfc
			ORDER BY
				(SELECT num_cia FROM catalogo_companias WHERE rfc = cc.rfc ORDER BY num_cia LIMIT 1)";

			$query = $db->query($sql);

			if ($query)
			{
				$data = '"","SALDOS CONTABLES"' . "\n";
				$data .= '"","' . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : 'BANCOS CONSOLIDADOS') . '"' . "\n\n";

				$total = array(
					'saldo_bancos'				=> 0,
					'saldo_libros'				=> 0,
					'pagos_no_cobrados'			=> 0,
					'saldo_proveedores'			=> 0,
					'dif_libros_proveedores'	=> 0,
					'utilidad_estimada'			=> 0,
					'diferencia_contable'		=> 0,
					'inventario_inicial'		=> 0,
					'perdidas'					=> 0,
					'devoluciones_iva'			=> 0
				);

				$data .= '"' . implode('","', array_keys($query[0])) . '"' . "\n";

				foreach ($query as $row)
				{
					if (abs(round($row['SALDO BANCOS'], 2)) == 0
						&& abs(round($row['SALDO LIBROS'], 2)) == 0
						&& abs(round($row['PAGOS NO COBRADOS'], 2)) == 0
						&& abs(round($row['SALDO PROVEEDORES'], 2)) == 0
						&& abs(round($row['LIBROS - PROVEEDORES'], 2)) == 0
						&& abs(round($row['UTILIDAD ESTIMADA'], 2)) == 0
						&& abs(round($row['DIFERENCIA CONTABLE'], 2)) == 0
						&& abs(round($row['INVENTARIO INICIAL'], 2)) == 0
						&& abs(round($row['PERDIDAS'], 2)) == 0
						&& abs(round($row['DEVOLUCIONES DE IVA'], 2)) == 0)
					{
						continue;
					}

					$data .= '"' . implode('","', array_values($row)) . '"' . "\n";

					$total['saldo_bancos'] += $row['SALDO BANCOS'];
					$total['saldo_libros'] += $row['SALDO LIBROS'];
					$total['pagos_no_cobrados'] += $row['PAGOS NO COBRADOS'];
					$total['saldo_proveedores'] += $row['SALDO PROVEEDORES'];
					$total['dif_libros_proveedores'] += $row['LIBROS - PROVEEDORES'];
					$total['utilidad_estimada'] += $row['UTILIDAD ESTIMADA'];
					$total['diferencia_contable'] += $row['DIFERENCIA CONTABLE'];
					$total['inventario_inicial'] += $row['INVENTARIO INICIAL'];
					$total['perdidas'] += $row['PERDIDAS'];
					$total['devoluciones_iva'] += $row['DEVOLUCIONES DE IVA'];
				}

				if (count($query) > 1)
				{
					$data .= '"","TOTALES",';

					$data .= '"' . implode('","', array_values($total)) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=SaldosContables.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/SaldosContables.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
