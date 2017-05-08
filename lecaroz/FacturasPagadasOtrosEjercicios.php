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
			$tpl = new TemplatePower('plantillas/fac/FacturasPagadasOtrosEjerciciosInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y') - 1);

			$rfcs = $db->query("SELECT
				rfc AS value,
				'[' || rfc || '] ' || COALESCE((
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
				num_cia < 900
				AND LENGTH(rfc) >= 12
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
			$fecha1 = "01/01/{$_REQUEST['anio']}";
			$fecha2 = "31/12/{$_REQUEST['anio']}";

			$condiciones1 = array();
			$condiciones2 = array();

			$condiciones1[] = "f.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
			$condiciones2[] = "f.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones1[] = "(ec.fecha_con < '{$fecha1}' OR ec.fecha_con > '{$fecha2}')";
			$condiciones2[] = "(ec.fecha_con < '{$fecha1}' OR ec.fecha_con > '{$fecha2}')";

			$condiciones2[] = "f.credito > 0";

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
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones1[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
					$condiciones2[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
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

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones1[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
					$condiciones2[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '')
			{
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$gastos[] = $piece;
					}
				}

				if (count($gastos) > 0)
				{
					$condiciones1[] = 'f.codgastos IN (' . implode(', ', $gastos) . ')';
					$condiciones2[] = 'f.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			$condiciones1_string = implode(' AND ', $condiciones1);
			$condiciones2_string = implode(' AND ', $condiciones2);

			$orden_string = isset($_REQUEST['ordenar_por_rfc']) ? '(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = resultado.rfc_cia), fecha, num_cia, num_pro, num_fact' : 'num_cia, fecha, num_pro, num_fact';

			$sql = "SELECT
				*
			FROM
				(
					SELECT
						f.id,
						f.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.rfc AS rfc_cia,
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						f.num_fact,
						f.fecha,
						f.concepto,
						COALESCE((
							SELECT
								SUM(cantidad * precio)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.importe) AS importe,
						COALESCE((
							SELECT
								SUM(COALESCE(desc1) + COALESCE(desc2) + COALESCE(desc3))
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), 0) AS descuentos,
						COALESCE((
							SELECT
								SUM(ieps)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.ieps) AS ieps,
						COALESCE((
							SELECT
								SUM(iva)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.iva) AS iva,
						f.retencion_iva AS ret_iva,
						f.retencion_isr AS ret_isr,
						f.total,
						fp.folio_cheque AS folio,
						fecha_cheque AS fecha_pago,
						fecha_con AS fecha_cobro,
						f.codgastos AS gasto,
						cg.descripcion AS nombre_gasto,
						CASE
							WHEN COALESCE((
								SELECT
									TRUE
								FROM
									factura_gas
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
								LIMIT
									1
							), FALSE) = TRUE THEN
								2
							WHEN COALESCE((
								SELECT
									TRUE
								FROM
									entrada_mp
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
								LIMIT
									1
							), FALSE) = TRUE THEN
								1
							ELSE
								0
						END AS tipo,
						ch.cuenta AS banco,
						ch.cod_mov,
						ch.fecha_cancelacion,
						f.xml_file,
						f.pdf_file,
						COALESCE((
							SELECT
								TRUE
							FROM
								balances_pan
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), (
							SELECT
								TRUE
							FROM
								balances_ros
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), FALSE) AS balance_generado
					FROM
						facturas f
						LEFT JOIN catalogo_gastos cg ON (cg.codgastos = f.codgastos)
						LEFT JOIN facturas_pagadas fp ON (
							fp.num_proveedor = f.num_proveedor
							AND fp.num_fact = f.num_fact
							AND fp.fecha = f.fecha
						)
						LEFT JOIN cheques ch ON (
							ch.num_cia = fp.num_cia
							AND ch.cuenta = fp.cuenta
							AND ch.folio = fp.folio_cheque
							AND ch.fecha = fp.fecha_cheque
						)
						LEFT JOIN estado_cuenta ec ON (
							ec.num_cia = fp.num_cia
							AND ec.cuenta = fp.cuenta
							AND ec.folio = fp.folio_cheque
							AND ec.fecha = fp.fecha_cheque
						)
						LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
					WHERE
						{$condiciones1_string}

					UNION

					SELECT
						f.id,
						f.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.rfc AS rfc_cia,
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						f.num_fac AS num_fact,
						f.fecha,
						'FACTURA ROSTICERIA' AS concepto,
						f.credito AS importe,
						0 AS descuentos,
						0 AS ieps,
						0 AS iva,
						0 AS ret_iva,
						0 AS ret_isr,
						f.credito AS total,
						fp.folio_cheque AS folio,
						fecha_cheque AS fecha_pago,
						fecha_con AS fecha_cobro,
						33 AS gasto,
						'PAGO PROVEEDORES' AS nombre_gasto,
						3 AS tipo,
						ch.cuenta AS banco,
						ch.cod_mov,
						ch.fecha_cancelacion,
						NULL AS xml_file,
						NULL AS pdf_file,
						COALESCE((
							SELECT
								TRUE
							FROM
								balances_pan
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), (
							SELECT
								TRUE
							FROM
								balances_ros
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), FALSE) AS balance_generado
					FROM
						total_fac_ros f
						LEFT JOIN facturas_pagadas fp ON (
							fp.num_proveedor = f.num_proveedor
							AND fp.num_fact = f.num_fac
							AND fp.fecha = f.fecha
						)
						LEFT JOIN cheques ch ON (
							ch.num_cia = fp.num_cia
							AND ch.cuenta = fp.cuenta
							AND ch.folio = fp.folio_cheque
							AND ch.fecha = fp.fecha_cheque
						)
						LEFT JOIN estado_cuenta ec ON (
							ec.num_cia = fp.num_cia
							AND ec.cuenta = fp.cuenta
							AND ec.folio = fp.folio_cheque
							AND ec.fecha = fp.fecha_cheque
						)
						LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
					WHERE
						{$condiciones2_string}
				) AS resultado

			ORDER BY
				{$orden_string}";

			$query = $db->query($sql);

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/fac/FacturasPagadasOtrosEjerciciosResultado.tpl');
				$tpl->prepare();

				$num_cia = NULL;

				$g_importe = 0;
				$g_descuentos = 0;
				$g_ieps = 0;
				$g_iva = 0;
				$g_ret_iva = 0;
				$g_ret_isr = 0;
				$g_total = 0;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('pro');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
						$tpl->assign('rfc_cia', utf8_encode($row['rfc_cia']));

						$importe = 0;
						$descuentos = 0;
						$ieps = 0;
						$iva = 0;
						$ret_iva = 0;
						$ret_isr = 0;
						$total = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_fact', $row['tipo'] > 0 ? ('<a id="detalle" alt="' . htmlentities(json_encode(array(
						'id'	=> get_val($row['id']),
						'tipo'	=> get_val($row['tipo'])
					))) . '" class="enlace ' . ($row['tipo'] == 1 ? 'blue' : 'orange') . '">' . utf8_encode($row['num_fact']) . '</a>') : utf8_encode($row['num_fact']));
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('concepto', trim($row['concepto']) != '' ? trim(utf8_encode($row['concepto'])) : '&nbsp;');
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('nombre_gasto', $row['nombre_gasto']);
					$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('descuentos', $row['descuentos'] != 0 ? number_format($row['descuentos'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] != 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] != 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ret_iva', $row['ret_iva'] != 0 ? number_format($row['ret_iva'], 2) : '&nbsp;');
					$tpl->assign('ret_isr', $row['ret_isr'] != 0 ? number_format($row['ret_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] != 0 ? number_format($row['total'], 2) : '&nbsp;');
					$tpl->assign('fecha_pago', $row['fecha_pago'] != '' ? $row['fecha_pago'] : '&nbsp;');
					$tpl->assign('banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['fecha_cancelacion'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha_cobro', $row['fecha_cobro'] != '' ? $row['fecha_cobro'] : '&nbsp;');

					$importe += $row['importe'];
					$descuentos += $row['descuentos'];
					$ieps += $row['ieps'];
					$iva += $row['iva'];
					$ret_iva += $row['ret_iva'];
					$ret_isr += $row['ret_isr'];
					$total += $row['total'];

					$g_importe += $row['importe'];
					$g_descuentos += $row['descuentos'];
					$g_ieps += $row['ieps'];
					$g_iva += $row['iva'];
					$g_ret_iva += $row['ret_iva'];
					$g_ret_isr += $row['ret_isr'];
					$g_total += $row['total'];

					$tpl->assign('pro.importe', number_format($importe, 2));
					$tpl->assign('pro.descuentos', number_format($descuentos, 2));
					$tpl->assign('pro.ieps', number_format($ieps, 2));
					$tpl->assign('pro.iva', number_format($iva, 2));
					$tpl->assign('pro.ret_iva', number_format($ret_iva, 2));
					$tpl->assign('pro.ret_isr', number_format($ret_isr, 2));
					$tpl->assign('pro.total', number_format($total, 2));

					$tpl->assign('_ROOT.importe', number_format($g_importe, 2));
					$tpl->assign('_ROOT.descuentos', number_format($g_descuentos, 2));
					$tpl->assign('_ROOT.ieps', number_format($g_ieps, 2));
					$tpl->assign('_ROOT.iva', number_format($g_iva, 2));
					$tpl->assign('_ROOT.ret_iva', number_format($g_ret_iva, 2));
					$tpl->assign('_ROOT.ret_isr', number_format($g_ret_isr, 2));
					$tpl->assign('_ROOT.total', number_format($g_total, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'detalle':
			$tpl = new TemplatePower('plantillas/fac/FacturasPagadasOtrosEjerciciosDetalle.tpl');
			$tpl->prepare();

			if ($_REQUEST['tipo'] == 3)
			{
				$result = $db->query("SELECT
					f.num_cia,
					cc.nombre_corto AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fac AS num_fact,
					f.fecha
				FROM
					total_fac_ros f
					LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");
			}
			else
			{
				$result = $db->query("SELECT
					f.num_cia,
					cc.nombre_corto AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.fecha
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");
			}

			$info_fac = $result[0];

			$tpl->assign('num_cia', $info_fac['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($info_fac['nombre_cia']));
			$tpl->assign('num_pro', $info_fac['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($info_fac['nombre_pro']));
			$tpl->assign('num_fact', utf8_encode($info_fac['num_fact']));
			$tpl->assign('fecha', $info_fac['fecha']);

			if ($_REQUEST['tipo'] == 1)
			{
				$result = $db->query("SELECT
					emp.cantidad,
					emp.codmp,
					cmp.nombre AS nombre_mp,
					emp.contenido,
					tuc.descripcion AS unidad,
					emp.precio,
					emp.cantidad * emp.precio AS importe,
					emp.desc1,
					emp.desc2,
					emp.desc3,
					emp.iva,
					emp.ieps,
					(emp.cantidad * emp.precio) - emp.desc1 - emp.desc2 - emp.desc3 + emp.iva + emp.ieps AS total
				FROM
					entrada_mp emp
					LEFT JOIN catalogo_mat_primas cmp USING (codmp)
					LEFT JOIN tipo_unidad_consumo tuc ON (idunidad = unidadconsumo)
				WHERE
					(emp.num_proveedor, emp.num_fact, emp.fecha) IN (
						SELECT
							num_proveedor,
							num_fact,
							fecha
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					emp.id");

				$importe = 0;
				$desc1 = 0;
				$desc2 = 0;
				$desc3 = 0;
				$iva = 0;
				$ieps = 0;
				$total = 0;

				$tpl->newBlock('mp');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_mp');

					$tpl->assign('cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('contenido', number_format($row['contenido'], 2));
					$tpl->assign('unidad', utf8_encode($row['unidad']));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('desc1', $row['desc1'] > 0 ? number_format($row['desc1'], 2) : '&nbsp;');
					$tpl->assign('desc2', $row['desc2'] > 0 ? number_format($row['desc2'], 2) : '&nbsp;');
					$tpl->assign('desc3', $row['desc3'] > 0 ? number_format($row['desc3'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] > 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$importe += $row['importe'];
					$desc1 += $row['desc1'];
					$desc2 += $row['desc2'];
					$desc3 += $row['desc3'];
					$iva += $row['iva'];
					$ieps += $row['ieps'];
					$total += $row['total'];

					$tpl->assign('mp.importe', number_format($importe, 2));
					$tpl->assign('mp.desc1', number_format($desc1, 2));
					$tpl->assign('mp.desc2', number_format($desc2, 2));
					$tpl->assign('mp.desc3', number_format($desc3, 2));
					$tpl->assign('mp.iva', number_format($iva, 2));
					$tpl->assign('mp.ieps', number_format($ieps, 2));
					$tpl->assign('mp.total', number_format($total, 2));
				}
			}
			else if ($_REQUEST['tipo'] == 2)
			{
				$result = $db->query("SELECT
					litros,
					precio_unit AS precio,
					litros * precio_unit AS importe,
					total - litros * precio_unit AS iva,
					total
				FROM
					factura_gas
				WHERE
					(num_proveedor, num_fact, fecha) IN (
						SELECT
							num_proveedor,
							num_fact,
							fecha
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					id");

				$importe = 0;
				$iva = 0;
				$total = 0;

				$tpl->newBlock('gas');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_gas');

					$tpl->assign('litros', number_format($row['litros'], 2));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$importe += $row['importe'];
					$iva += $row['iva'];
					$total += $row['total'];

					$tpl->assign('gas.importe', number_format($importe, 2));
					$tpl->assign('gas.iva', number_format($iva, 2));
					$tpl->assign('gas.total', number_format($total, 2));
				}
			}
			else if ($_REQUEST['tipo'] == 3)
			{
				$result = $db->query("SELECT
					fr.codmp,
					cmp.nombre AS nombre_mp,
					fr.cantidad,
					fr.kilos,
					ROUND((fr.precio * tfr.porc795 / 100)::NUMERIC, 2) AS precio,
					ROUND((fr.precio * tfr.porc795 / 100)::NUMERIC, 2) * kilos AS total
				FROM
					fact_rosticeria fr
					LEFT JOIN total_fac_ros tfr ON (
						tfr.num_cia = fr.num_cia
						AND tfr.num_proveedor = fr.num_proveedor
						AND tfr.num_fac = fr.num_fac
						AND tfr.fecha = fr.fecha_mov
					)
					LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				WHERE
					(fr.num_proveedor, fr.num_fac, fr.fecha_mov) IN (
						SELECT
							num_proveedor,
							num_fac,
							fecha
						FROM
							total_fac_ros
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					fr.idfact_rosticeria");

				$total = 0;

				$tpl->newBlock('pollos');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_pollos');

					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('kilos', number_format($row['kilos'], 2));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$total += $row['total'];

					$tpl->assign('pollos.total', number_format($total, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasPagadasOtrosEjercicios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
