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
			$tpl = new TemplatePower('plantillas/bal/BalancesHistoricoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));

			$admins = $db->query("SELECT
				idadministrador
					AS value,
				nombre_administrador
					AS text
			FROM
				catalogo_administradores
			ORDER BY
				text");

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
			$condiciones = array();

			$condiciones[] = "bal.anio = '{$_REQUEST['anio']}'";

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
					$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$balances_pan = $db->query("SELECT
				num_cia,
				nombre,
				anio,
				mes AS titulo,
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
				NULL AS blank1,
				inv_ant,
				compras,
				mercancias,
				inv_act,
				mat_prima_utilizada,
				mano_obra,
				panaderos,
				gastos_fab,
				costo_produccion,
				NULL AS blank2,
				utilidad_bruta,
				NULL AS blank3,
				pan_comprado,
				gastos_generales,
				gastos_caja,
				comisiones,
				reserva_aguinaldos,
				pagos_anticipados,
				gastos_otras_cias,
				total_gastos,
				NULL AS blank4,
				ingresos_ext,
				NULL AS blank5,
				utilidad_neta,
				NULL AS blank6,
				mp_vtas,
				utilidad_pro,
				mp_pro,
				gas_pro,
				NULL AS blank7,
				produccion_total,
				ganancia,
				porc_ganancia,
				faltante_pan,
				devoluciones,
				rezago_ini,
				rezago_fin,
				var_rezago,
				efectivo,
				NULL AS blank8,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = TRUE
				), 0) AS ingresos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = FALSE
				), 0) AS egresos,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS total_gastos_caja,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov = 1
				), 0) AS depositos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						otros_depositos
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS otros_depositos,
				NULL AS blank9,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_ini,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_fin,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
				), 0) AS saldo_pro_ini,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS saldo_pro_fin,
				COALESCE((
					SELECT
						SUM(importe) AS importe
					FROM
						movimiento_gastos g
						LEFT JOIN catalogo_gastos cg USING (codgastos)
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND codigo_edo_resultados = 0
						AND codgastos NOT IN (33, 134)
				), 0) AS no_inc,
				inv_act - inv_ant AS dif_inventario,
				ABS(reserva_aguinaldos) AS dif_reservas,
				-1 * ABS(pagos_anticipados) AS pagos_anticipados_negativo,
				COALESCE(-1 * ABS((
					SELECT
						SUM(importe * (EXTRACT(MONTHS FROM AGE(fecha_fin, ('01/' || mes || '/' || anio)::DATE)) + 1))
					FROM
						pagos_anticipados
					WHERE
						num_cia = bal.num_cia
						AND ('01/' || mes || '/' || anio)::DATE BETWEEN fecha_ini AND fecha_fin
				)), 0) AS pagos_anticipados_acumulados
			FROM
				balances_pan bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_cia,
				mes");

			$balances_ros = $db->query("SELECT
				num_cia,
				nombre,
				anio,
				mes AS titulo,
				venta,
				otros,
				ventas_netas,
				NULL AS blank1,
				inv_ant,
				compras,
				mercancias,
				inv_act,
				mat_prima_utilizada,
				gastos_fab,
				costo_produccion,
				NULL AS blank2,
				utilidad_bruta,
				NULL AS blank3,
				gastos_generales,
				gastos_caja,
				comisiones,
				reserva_aguinaldos,
				gastos_otras_cias,
				total_gastos,
				NULL AS blank4,
				ingresos_ext,
				NULL AS blank5,
				utilidad_neta,
				NULL AS blank6,
				mp_vtas,
				efectivo,
				pollos_vendidos,
				p_pavo,
				pescuezos,
				NULL AS blank7,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = TRUE
				), 0) AS ingresos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = FALSE
				), 0) AS egresos,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS total_gastos_caja,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov = 1
				), 0) AS depositos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						otros_depositos
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS otros_depositos,
				NULL AS blank8,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_ini,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_fin,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
				), 0) AS saldo_pro_ini,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS saldo_pro_fin,
				COALESCE((
					SELECT
						SUM(importe) AS importe
					FROM
						movimiento_gastos g
						LEFT JOIN catalogo_gastos cg USING (codgastos)
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND codigo_edo_resultados = 0
						AND codgastos NOT IN (33, 134)
				), 0) AS no_inc,
				inv_act - inv_ant AS dif_inventario,
				ABS(reserva_aguinaldos) AS dif_reservas,
				0 AS pagos_anticipados_negativo,
				0 AS pagos_anticipados_acumulados
			FROM
				balances_ros bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_cia,
				mes");

			if ($result)
			{
				$total = 0;

				$tpl = new TemplatePower('plantillas/bal/BalancesHistoricoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);


				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "ec.cod_mov = {$_REQUEST['cod_mov']}";

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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				ec.num_cia,
				cc.nombre_corto AS nombre_cia,
				ec.tipo_mov,
				ec.cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				SUM(ec.importe) AS importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				ec.num_cia,
				nombre_cia,
				ec.tipo_mov,
				ec.cod_mov,
				descripcion
			ORDER BY
				ec.num_cia");

			if ($result)
			{
				$total = 0;

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

						$this->Cell(0, 5, utf8_decode('TOTALES POR CONCEPTO BANCARIO'), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("{$GLOBALS['result'][0]['cod_mov']} {$GLOBALS['result'][0]['descripcion']}"), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("PERIODO DEL {$_REQUEST['fecha1']} AL {$_REQUEST['fecha2']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(50, 5, utf8_decode(''), 0, 0);
						$this->Cell(80, 5, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(22, 5, utf8_decode('IMPORTE'), 1, 0, 'R');

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

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('P', 'Letter');

				$rows = 0;

				$pdf->SetFont('ARIAL', '', 10);

				foreach ($result as $row)
				{

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 80)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(50, 5, utf8_decode(''), 0, 0);
					$pdf->Cell(80, 5, utf8_decode($nombre_cia), 1, 0);
					$pdf->Cell(22, 5, number_format($row['importe'], 2), 1, 1, 'R');

					$total += $row['importe'];

					if ($rows < 47)
					{
						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('P', 'Letter');
						$pdf->SetMargins(5, 5, 5);
					}
				}

				$pdf->SetFont('ARIAL', 'B', 10);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(50, 5, utf8_decode(''), 0, 0);
				$pdf->Cell(80, 5, utf8_decode('Total'), 1, 0, 'R');
				$pdf->Cell(22, 5, number_format($total, 2), 1, 0, 'R');

				$pdf->Output('reporte-totales-conceptos-bancarios.pdf', 'I');
			}

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "ec.cod_mov = {$_REQUEST['cod_mov']}";

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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				ec.num_cia,
				cc.nombre_corto AS nombre_cia,
				ec.tipo_mov,
				ec.cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				SUM(ec.importe) AS importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				ec.num_cia,
				nombre_cia,
				ec.tipo_mov,
				ec.cod_mov,
				descripcion
			ORDER BY
				ec.num_cia");

			if ($result)
			{
				$data = '"","TOTALES POR CONCEPTO BANCARIO"' . "\n";
				$data .= '"","' . $result[0]['cod_mov'] . ' ' . $result[0]['descripcion'] . '"' . "\n";
				$data .= '"","PERIODO DEL ' . $_REQUEST['fecha1'] . ' AL ' . $_REQUEST['fecha2'] . '"' . "\n\n";

				$data .= '"#","COMPAÑIA","IMPORTE"' . "\n";

				$total = 0;

				foreach ($result as $row)
				{
					$data .= '"' . $row['num_cia'] . '","' . utf8_encode($row['nombre_cia']) . '","' . number_format($row['importe'], 2) . '"' . "\n";

					$total += $row['importe'];
				}

				$data .= '"","TOTALES","' . number_format($total, 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-totales-conceptos-bancarios.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/BalancesHistorico.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
