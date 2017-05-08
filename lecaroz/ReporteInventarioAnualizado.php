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
			$tpl = new TemplatePower('plantillas/bal/ReporteInventarioAnualizadoInicio.tpl');
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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'] - 1));

			$result = $db->query("SELECT MAX(fecha) - INTERVAL '1 DAY' AS fecha FROM balances_pan WHERE anio = {$_REQUEST['anio']}");

			list($max_dia, $max_mes, $max_anio) = $result[0]['fecha'] != '' ? array_map('toInt', explode('/', $result[0]['fecha'])) : array_map('toInt', explode(date('d/m/Y', mktime(0, 0, 0, 11, 30, $_REQUEST['anio']))));

			$fecha2 = $result[0]['fecha'] != '' ? $result[0]['fecha'] : date('d/m/Y', mktime(0, 0, 0, 11, 30, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "his.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'his.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '')
			{
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0)
				{
					$condiciones[] = 'his.codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				codmp,
				nombre_mp,
				mes,
				SUM(existencia_inicial) AS existencia_inicial,
				SUM(compras) AS compras,
				SUM(consumos) AS consumos,
				SUM(existencia_inicial + compras - consumos) AS existencia_final
			FROM
				(
					SELECT
						his.num_cia,
						cc.nombre_corto AS nombre_cia,
						his.codmp,
						cmp.nombre AS nombre_mp,
						EXTRACT(MONTH FROM his.fecha + INTERVAL '1 DAY') AS mes,
						SUM(his.existencia) AS existencia_inicial,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = his.num_cia
								AND codmp = his.codmp
								AND fecha BETWEEN his.fecha + INTERVAL '1 DAY' AND his.fecha + INTERVAL '1 DAY' + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
								AND tipo_mov = FALSE
						), 0) AS compras,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = his.num_cia
								AND codmp = his.codmp
								AND fecha BETWEEN his.fecha + INTERVAL '1 DAY' AND his.fecha + INTERVAL '1 DAY' + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
								AND tipo_mov = TRUE
						), 0) AS consumos
					FROM
						historico_inventario AS his
						LEFT JOIN catalogo_companias AS cc USING (num_cia)
						LEFT JOIN catalogo_mat_primas AS cmp USING (codmp)
					WHERE
						{$condiciones_string}
					GROUP BY
						his.num_cia,
						cc.nombre_corto,
						his.codmp,
						nombre_mp,
						his.fecha,
						mes
					ORDER BY
						his.codmp,
						his.num_cia,
						his.fecha,
						mes
				) AS resultado
			GROUP BY
				codmp,
				nombre_mp,
				mes
			ORDER BY
				codmp";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$codmp = NULL;

				foreach ($query as $row)
				{
					if ($codmp != $row['codmp'])
					{
						$codmp = $row['codmp'];

						$datos[$codmp] = array(
							'nombre'				=> utf8_encode($row['nombre_mp']),
							'existencias_iniciales'	=> array_fill(1, 12, 0),
							'compras'				=> array_fill(1, 12, 0),
							'consumos'				=> array_fill(1, 12, 0),
							'existencias_finales'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$codmp]['existencias_iniciales'][$row['mes']] = floatval($row['existencia_inicial']);
					$datos[$codmp]['compras'][$row['mes']] = floatval($row['compras']);
					$datos[$codmp]['consumos'][$row['mes']] = floatval($row['consumos']);
					$datos[$codmp]['existencias_finales'][$row['mes']] = floatval($row['existencia_final']);
				}

				$tpl = new TemplatePower('plantillas/bal/ReporteInventarioAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $codmp => $datos_mp)
				{
					$tpl->newBlock('producto');

					$tpl->assign('codmp', $codmp);
					$tpl->assign('nombre_mp', $datos_mp['nombre']);

					$existencia_inicial = in_array($codmp, array(1)) ? array_sum($datos_mp['existencias_iniciales']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['existencias_iniciales']) / 50 : array_sum($datos_mp['existencias_iniciales']));
					$compras = in_array($codmp, array(1)) ? array_sum($datos_mp['compras']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['compras']) / 50 : array_sum($datos_mp['compras']));
					$consumos = in_array($codmp, array(1)) ? array_sum($datos_mp['consumos']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['consumos']) / 50 : array_sum($datos_mp['consumos']));
					$existencia_final = in_array($codmp, array(1)) ? array_sum($datos_mp['existencias_finales']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['existencias_finales']) / 50 : array_sum($datos_mp['existencias_finales']));

					$tpl->assign('existencia_inicial', number_format($existencia_inicial, 2));
					$tpl->assign('compras', number_format($compras, 2));
					$tpl->assign('consumos', number_format($consumos, 2));
					$tpl->assign('existencia_final', number_format($existencia_final, 2));

					foreach ($datos_mp['existencias_iniciales'] as $mes => $existencia_inicial)
					{
						if ($mes > $max_mes)
						{
							continue;
						}

						$tpl->newBlock('mes');

						$existencia_inicial = in_array($codmp, array(1)) ? $existencia_inicial / 44 : (in_array($codmp, array(3, 4)) ? $existencia_inicial / 50 : $existencia_inicial);
						$compras = in_array($codmp, array(1)) ? $datos_mp['compras'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['compras'][$mes] / 50 : $datos_mp['compras'][$mes]);
						$consumos = in_array($codmp, array(1)) ? $datos_mp['consumos'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['consumos'][$mes] / 50 : $datos_mp['consumos'][$mes]);
						$existencia_final = in_array($codmp, array(1)) ? $datos_mp['existencias_finales'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['existencias_finales'][$mes] / 50 : $datos_mp['existencias_finales'][$mes]);

						$tpl->assign('mes', $_meses[$mes]);
						$tpl->assign('existencia_inicial', $existencia_inicial != 0 ? '<span class="' . ($existencia_inicial < 0 ? 'red' : 'green') . '">' . number_format($existencia_inicial, 2) . '</span>' : '&nbsp;');
						$tpl->assign('compras', $compras != 0 ? number_format($compras, 2) : '&nbsp;');
						$tpl->assign('consumos', $consumos != 0 ? number_format($consumos, 2) : '&nbsp;');
						$tpl->assign('existencia_final', $existencia_final != 0 ? '<span class="' . ($existencia_final < 0 ? 'red' : 'green') . '">' . number_format($existencia_final, 2) . '</span>' : '&nbsp;');
					}
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'] - 1));

			$result = $db->query("SELECT MAX(fecha) - INTERVAL '1 DAY' AS fecha FROM balances_pan WHERE anio = {$_REQUEST['anio']}");

			list($max_dia, $max_mes, $max_anio) = $result[0]['fecha'] != '' ? array_map('toInt', explode('/', $result[0]['fecha'])) : array_map('toInt', explode(date('d/m/Y', mktime(0, 0, 0, 11, 30, $_REQUEST['anio']))));

			$fecha2 = $result[0]['fecha'] != '' ? $result[0]['fecha'] : date('d/m/Y', mktime(0, 0, 0, 11, 30, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "his.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'his.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '')
			{
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0)
				{
					$condiciones[] = 'his.codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				codmp,
				nombre_mp,
				mes,
				SUM(existencia_inicial) AS existencia_inicial,
				SUM(compras) AS compras,
				SUM(consumos) AS consumos,
				SUM(existencia_inicial + compras - consumos) AS existencia_final
			FROM
				(
					SELECT
						his.num_cia,
						cc.nombre_corto AS nombre_cia,
						his.codmp,
						cmp.nombre AS nombre_mp,
						EXTRACT(MONTH FROM his.fecha + INTERVAL '1 DAY') AS mes,
						SUM(his.existencia) AS existencia_inicial,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = his.num_cia
								AND codmp = his.codmp
								AND fecha BETWEEN his.fecha + INTERVAL '1 DAY' AND his.fecha + INTERVAL '1 DAY' + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
								AND tipo_mov = FALSE
						), 0) AS compras,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = his.num_cia
								AND codmp = his.codmp
								AND fecha BETWEEN his.fecha + INTERVAL '1 DAY' AND his.fecha + INTERVAL '1 DAY' + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
								AND tipo_mov = TRUE
						), 0) AS consumos
					FROM
						historico_inventario AS his
						LEFT JOIN catalogo_companias AS cc USING (num_cia)
						LEFT JOIN catalogo_mat_primas AS cmp USING (codmp)
					WHERE
						{$condiciones_string}
					GROUP BY
						his.num_cia,
						cc.nombre_corto,
						his.codmp,
						nombre_mp,
						his.fecha,
						mes
					ORDER BY
						his.codmp,
						his.num_cia,
						his.fecha,
						mes
				) AS resultado
			GROUP BY
				codmp,
				nombre_mp,
				mes
			ORDER BY
				codmp";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$codmp = NULL;

				foreach ($query as $row)
				{
					if ($codmp != $row['codmp'])
					{
						$codmp = $row['codmp'];

						$datos[$codmp] = array(
							'nombre'				=> utf8_encode($row['nombre_mp']),
							'existencias_iniciales'	=> array_fill(1, 12, 0),
							'compras'				=> array_fill(1, 12, 0),
							'consumos'				=> array_fill(1, 12, 0),
							'existencias_finales'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$codmp]['existencias_iniciales'][$row['mes']] = floatval($row['existencia_inicial']);
					$datos[$codmp]['compras'][$row['mes']] = floatval($row['compras']);
					$datos[$codmp]['consumos'][$row['mes']] = floatval($row['consumos']);
					$datos[$codmp]['existencias_finales'][$row['mes']] = floatval($row['existencia_final']);
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

						$this->Cell(0, 4, utf8_decode('REPORTE DE INVENTARIO ANUALIZADO ' . $_REQUEST['anio']), 0, 1, 'C');

						$this->Ln(5);
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('Arial', '', 6);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', array(216, 279));

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 6);

				$pdf->AddPage('P', array(216, 340));

				$rows = 0;

				foreach ($datos as $codmp => $datos_mp)
				{
					$pdf->Cell(28, 4, utf8_decode(''), 0, 0);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(150, 4, utf8_decode("{$codmp} {$datos_mp['nombre']}"), 1, 0);

					$pdf->Ln();

					$pdf->Cell(28, 4, utf8_decode(''), 0, 0);
					$pdf->Cell(30, 4, utf8_decode('MES'), 1, 0);
					$pdf->Cell(30, 4, utf8_decode('EXISTENCIA INICIAL'), 1, 0, 'R');
					$pdf->Cell(30, 4, utf8_decode('COMPRAS'), 1, 0, 'R');
					$pdf->Cell(30, 4, utf8_decode('CONSUMOS'), 1, 0, 'R');
					$pdf->Cell(30, 4, utf8_decode('EXISTENCIA FINAL'), 1, 1, 'R');

					$pdf->SetFont('ARIAL', '', 8);

					foreach ($datos_mp['existencias_iniciales'] as $mes => $existencia_inicial)
					{
						if ($mes > $max_mes)
						{
							continue;
						}

						$pdf->Cell(28, 4, utf8_decode(''), 0, 0);

						$pdf->SetTextColor(0, 0, 0);

						$pdf->SetFont('ARIAL', 'B', 8);

						$pdf->Cell(30, 4, utf8_decode($_meses[$mes]), 1, 0);

						$existencia_inicial = in_array($codmp, array(1)) ? $existencia_inicial / 44 : (in_array($codmp, array(3, 4)) ? $existencia_inicial / 50 : $existencia_inicial);
						$compras = in_array($codmp, array(1)) ? $datos_mp['compras'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['compras'][$mes] / 50 : $datos_mp['compras'][$mes]);
						$consumos = in_array($codmp, array(1)) ? $datos_mp['consumos'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['consumos'][$mes] / 50 : $datos_mp['consumos'][$mes]);
						$existencia_final = in_array($codmp, array(1)) ? $datos_mp['existencias_finales'][$mes] / 44 : (in_array($codmp, array(3, 4)) ? $datos_mp['existencias_finales'][$mes] / 50 : $datos_mp['existencias_finales'][$mes]);

						if ($existencia_inicial < 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 102, 0);
						}

						$pdf->Cell(30, 4, $existencia_inicial != 0 ? number_format($existencia_inicial, 2) : '', 1, 0, 'R');

						$pdf->SetTextColor(0, 0, 204);

						$pdf->SetFont('ARIAL', '', 8);

						$pdf->Cell(30, 4, $compras != 0 ? number_format($compras, 2) : '', 1, 0, 'R');

						$pdf->SetTextColor(204, 0, 0);

						$pdf->SetFont('ARIAL', '', 8);

						$pdf->Cell(30, 4, $consumos != 0 ? number_format($consumos, 2) : '', 1, 0, 'R');

						if ($existencia_final < 0)
						{
							$pdf->SetTextColor(204, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 102, 0);
						}

						$pdf->Cell(30, 4, $existencia_final != 0 ? number_format($existencia_final, 2) : '', 1, 1, 'R');
					}

					$pdf->Cell(28, 4, utf8_decode(''), 0, 0);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->Cell(30, 4, utf8_decode('TOTALES'), 1, 0, 'R');

					$existencia_inicial = in_array($codmp, array(1)) ? array_sum($datos_mp['existencias_iniciales']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['existencias_iniciales']) / 50 : array_sum($datos_mp['existencias_iniciales']));
					$compras = in_array($codmp, array(1)) ? array_sum($datos_mp['compras']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['compras']) / 50 : array_sum($datos_mp['compras']));
					$consumos = in_array($codmp, array(1)) ? array_sum($datos_mp['consumos']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['consumos']) / 50 : array_sum($datos_mp['consumos']));
					$existencia_final = in_array($codmp, array(1)) ? array_sum($datos_mp['existencias_finales']) / 44 : (in_array($codmp, array(3, 4)) ? array_sum($datos_mp['existencias_finales']) / 50 : array_sum($datos_mp['existencias_finales']));

					if ($existencia_inicial < 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 102, 0);
					}

					$pdf->Cell(30, 4, $existencia_inicial != 0 ? number_format($existencia_inicial, 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(0, 0, 204);

					$pdf->Cell(30, 4, $compras != 0 ? number_format($compras, 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(204, 0, 0);

					$pdf->Cell(30, 4, $consumos != 0 ? number_format($consumos, 2) : '', 1, 0, 'R');

					if ($existencia_final < 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 102, 0);
					}

					$pdf->Cell(30, 4, $existencia_final != 0 ? number_format($existencia_final, 2) : '', 1, 1, 'R');

					$pdf->Cell(28, 4, utf8_decode(''), 0, 0);

					$pdf->Cell(150, 4, utf8_decode(''), 1, 1, 'R');
				}

				$pdf->Output("reporte-inventario-anualizado-{$_REQUEST['anio']}.pdf'", 'I');
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
					if (abs(round($row['SALDO BANCOS'], 2)) == 0
						&& abs(round($row['SALDO LIBROS'], 2)) == 0
						&& abs(round($row['PAGOS NO COBRADOS'], 2)) == 0
						&& abs(round($row['SALDO PROVEEDORES'], 2)) == 0
						&& abs(round($row['LIBROS - PROVEEDORES'], 2)) == 0
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
				header('Content-Disposition: attachment; filename=ReporteInventarioAnualizado.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteInventarioAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
