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
			$tpl = new TemplatePower('plantillas/ban/SaldosAnualizadoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));

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
			$anio = isset($_REQUEST['anio']) && $_REQUEST['anio'] > 0 ? $_REQUEST['anio'] : date('Y');
			$condiciones = array();

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
					$condiciones[] = 'cc.num_cia_saldos IN (' . implode(', ', $cias) . ')';
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

			$sql = "
				SELECT
					cc.num_cia_saldos
						AS num_cia,
					ccs.nombre
						AS nombre_cia,
					m.mes,
					SUM(s.saldo_bancos)
						AS saldo_actual,
					COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										-importe
									ELSE
										importe
								END
							)
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							num_cia_saldos = cc.num_cia_saldos
							AND fecha_con IS NOT NULL
							AND fecha_con >= ({$anio} || '/' || m.mes || '/01')::DATE + INTERVAL '1 MONTH'
							AND cuenta = 1
					), 0)
						AS movimientos,
					SUM(s.saldo_bancos) + COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										-importe
									ELSE
										importe
								END
							)
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							num_cia_saldos = cc.num_cia_saldos
							AND fecha_con IS NOT NULL
							AND fecha_con >= ({$anio} || '/' || m.mes || '/01')::DATE + INTERVAL '1 MONTH'
							AND cuenta = 1
					), 0)
						AS saldo_mes
				FROM
					saldos s
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_saldos),
					meses m
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				GROUP BY
					cc.num_cia_saldos,
					ccs.nombre,
					m.mes
				ORDER BY
					cc.num_cia_saldos,
					m.mes
			";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$result[$num_cia] = array(
							'nombre_cia'	=> utf8_encode($row['nombre_cia']),
							'meses'			=> array_fill(1, 12, 0)
						);
					}

					$result[$num_cia]['meses'][$row['mes']] = $anio . str_pad($row['mes'], 2, '0', STR_PAD_LEFT) <= date('Ym') && floor(floatval($row['movimientos']) * 100) / 100 != 0 ? floor(floatval($row['saldo_mes']) * 100) / 100 : 0;
				}

				$tpl = new TemplatePower('plantillas/ban/SaldosAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('banco', isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? '<img src="imagenes/Banorte16x16.png" /> BANORTE' : '<img src="imagenes/Santander16x16.png" /> SANTANDER') : 'BANCOS CONSOLIDADOS');
				$tpl->assign('anio', $anio);

				$totales = array_fill(1, 12, 0);

				foreach ($result as $num_cia => $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $row['nombre_cia']);

					foreach ($row['meses'] as $mes => $saldo) {
						$tpl->assign('saldo_' . $mes, $saldo != 0 ? '<span class="' . ($saldo < 0 ? 'red' : 'blue') . '">' . number_format($saldo, 2) . '</span>' : '&nbsp;');

						$totales[$mes] += $saldo;
					}
				}

				foreach ($totales as $mes => $total) {
					$tpl->assign('_ROOT.saldo_' . $mes, $total != 0 ? '<span class="' . ($total < 0 ? 'red' : 'blue') . '">' . number_format($total, 2) . '</span>' : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$anio = isset($_REQUEST['anio']) && $_REQUEST['anio'] > 0 ? $_REQUEST['anio'] : date('Y');
			$condiciones = array();

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
					$condiciones[] = 'cc.num_cia_saldos IN (' . implode(', ', $cias) . ')';
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

			$sql = "
				SELECT
					cc.num_cia_saldos
						AS num_cia,
					ccs.nombre
						AS nombre_cia,
					m.mes,
					SUM(s.saldo_bancos)
						AS saldo_actual,
					COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										-importe
									ELSE
										importe
								END
							)
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							num_cia_saldos = cc.num_cia_saldos
							AND fecha_con IS NOT NULL
							AND fecha_con >= ({$anio} || '/' || m.mes || '/01')::DATE + INTERVAL '1 MONTH'
							AND cuenta = 1
					), 0)
						AS movimientos,
					SUM(s.saldo_bancos) + COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										-importe
									ELSE
										importe
								END
							)
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							num_cia_saldos = cc.num_cia_saldos
							AND fecha_con IS NOT NULL
							AND fecha_con >= ({$anio} || '/' || m.mes || '/01')::DATE + INTERVAL '1 MONTH'
							AND cuenta = 1
					), 0)
						AS saldo_mes
				FROM
					saldos s
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_saldos),
					meses m
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				GROUP BY
					cc.num_cia_saldos,
					ccs.nombre,
					m.mes
				ORDER BY
					cc.num_cia_saldos,
					m.mes
			";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$result[$num_cia] = array(
							'nombre_cia'	=> utf8_encode($row['nombre_cia']),
							'meses'			=> array_fill(1, 12, 0)
						);
					}

					$result[$num_cia]['meses'][$row['mes']] = $anio . str_pad($row['mes'], 2, '0', STR_PAD_LEFT) <= date('Ym') && floor(floatval($row['movimientos']) * 100) / 100 != 0 ? floor(floatval($row['saldo_mes']) * 100) / 100 : 0;
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

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 4, 'SALDOS ANUALIZADO ' . $GLOBALS['anio'], 0, 1, 'C');
						$this->Cell(0, 4, isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : 'BANCOS CONSOLIDADOS', 'B', 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(61, 4, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('ENE'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('FEB'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('MAR'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('ABR'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('MAY'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('JUN'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('JUL'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('AGO'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('SEP'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('OCT'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('NOV'), 1, 0, 'C');
						$this->Cell(23.5, 4, utf8_decode('DIC'), 1, 0, 'C');

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

				$pdf = new PDF('L', 'mm', 'Legal');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('L', 'Legal');

				$totales = array_fill(1, 12, 0);

				$rows = 0;

				foreach ($result as $num_cia => $row)
				{
					$pdf->SetFont('ARIAL', '', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(61, 4, utf8_decode("{$num_cia} {$row['nombre_cia']}"), 1, 0);

					foreach ($row['meses'] as $mes => $saldo) {
						if ($saldo <= 0)
						{
							$pdf->SetTextColor(255, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 255);
						}

						$pdf->Cell(23.5, 4, $saldo != 0 ? number_format($saldo, 2) : '', 1, 0, 'R');

						$totales[$mes] += $saldo;
					}

					if ($rows < 45)
					{
						$pdf->Ln();

						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', 'Legal');
						$pdf->SetMargins(5, 5, 5);
					}
				}

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(61, 4, utf8_decode('TOTALES'), 1, 0, 'R');

				foreach ($totales as $mes => $total) {
					if ($total <= 0)
					{
						$pdf->SetTextColor(255, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 255);
					}

					$pdf->Cell(23.5, 4, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
				}

				$pdf->Output("ReporteSaldosAnualizado{$anio}.pdf", 'I');
			}

			break;

		case 'exportar':
			$db->query("UPDATE cheques SET acuenta = FALSE WHERE acuenta IS NULL");

			$fecha1 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n') - 1, 1, date('Y')) : mktime(0, 0, 0, date('n'), 1, date('Y')));
			$fecha2 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('j'), date('Y')));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

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
					$condiciones[] = 'cc.num_cia_saldos IN (' . implode(', ', $cias) . ')';
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

			$sql = "
				SELECT
					cc.num_cia_saldos
						AS \"#\",
					ccs.nombre
						AS \"COMPAÑIA\",
					SUM(s.saldo_bancos)
						AS \"SALDO BANCOS\",
					SUM(s.saldo_libros) + COALESCE((
						SELECT
							SUM(ecsl.importe)
						FROM
							estado_cuenta ecsl
						LEFT JOIN cheques csl
							USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl
							USING (num_cia)
						WHERE
							ccsl.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecsl.fecha_con IS NULL
							AND ecsl.tipo_mov = TRUE
							AND ecsl.cod_mov IN (5, 41)
							AND csl.acuenta = TRUE
					), 0)
						AS \"SALDO LIBROS\",
					COALESCE((
						SELECT
							SUM(ecpnc.importe)
						FROM
							estado_cuenta ecpnc
							LEFT JOIN cheques cpnc
								USING (num_cia, folio, cuenta, fecha)
							LEFT JOIN catalogo_companias ccpnc
								USING (num_cia)
						WHERE
							ccpnc.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecpnc.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecpnc.fecha_con IS NULL
							AND ecpnc.tipo_mov = TRUE
							AND ecpnc.cod_mov IN (5, 41)
							AND cpnc.acuenta = FALSE
					), 0)
						AS \"PAGOS NO COBRADOS\",
					COALESCE((
						SELECT
							SUM(total)
						FROM
							pasivo_proveedores ppsp
							LEFT JOIN catalogo_companias ccsp
								USING (num_cia)
						WHERE
							ccsp.num_cia_saldos = cc.num_cia_saldos
							AND ppsp.total > 0
					), 0)
						AS \"SALDO PROVEEDORES\",
					SUM(s.saldo_libros) + COALESCE((
						SELECT
							SUM(ecsl.importe)
						FROM
							estado_cuenta ecsl
						LEFT JOIN cheques csl
							USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl
							USING (num_cia)
						WHERE
							ccsl.num_cia_saldos = cc.num_cia_saldos
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
							LEFT JOIN catalogo_companias ccsp
								USING (num_cia)
						WHERE
							ccsp.num_cia_saldos = cc.num_cia_saldos
							AND ppsp.total > 0
					), 0)
						AS \"LIBROS - PROVEEDORES\",
					COALESCE((
						SELECT
							SUM(inventario)
						FROM
							(
								SELECT
									inv_act
										AS inventario
								FROM
									balances_pan bpinv
									LEFT JOIN catalogo_companias ccbpinv
										USING (num_cia)
								WHERE
									ccbpinv.num_cia_saldos = cc.num_cia_saldos
									AND fecha = (SELECT MAX(fecha) FROM balances_pan)

								UNION

								SELECT
									inv_act
								FROM
									balances_ros brinv
									LEFT JOIN catalogo_companias ccbrinv
										USING (num_cia)
								WHERE
									ccbrinv.num_cia_saldos = cc.num_cia_saldos
									AND fecha = (SELECT MAX(fecha) FROM balances_ros)
							) result
					), 0)
						AS \"INVENTARIO INICIAL\",
					COALESCE((
						SELECT
							SUM(monto)
						FROM
							perdidas
							LEFT JOIN catalogo_companias ccper
								USING (num_cia)
							WHERE
								ccper.num_cia_saldos = cc.num_cia_saldos
					), 0)
						AS \"PERDIDAS\",
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							estado_cuenta ecdev
							LEFT JOIN catalogo_companias ccdev
								USING (num_cia)
						WHERE
							ccdev.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecdev.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecdev.cod_mov = 18
							AND ecdev.fecha BETWEEN '01/01/{$anio}' AND '{$fecha2}'
					), 0)
						AS \"DEVOLUCIONES DE IVA\"
				FROM
					saldos s
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_saldos)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				GROUP BY
					cc.num_cia_saldos,
					ccs.nombre
				ORDER BY
					cc.num_cia_saldos
			";

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
					'inventario_inicial'		=> 0,
					'perdidas'					=> 0,
					'devoluciones_iva'			=> 0
				);

				$data .= '"' . implode('","', array_keys($query[0])) . '"' . "\n";

				foreach ($query as $row)
				{
					$data .= '"' . implode('","', array_values($row)) . '"' . "\n";

					$total['saldo_bancos'] += $row['SALDO BANCOS'];
					$total['saldo_libros'] += $row['SALDO LIBROS'];
					$total['pagos_no_cobrados'] += $row['PAGOS NO COBRADOS'];
					$total['saldo_proveedores'] += $row['SALDO PROVEEDORES'];
					$total['dif_libros_proveedores'] += $row['LIBROS - PROVEEDORES'];
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
				header('Content-Disposition: attachment; filename=SaldosAnualizado.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/SaldosAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
