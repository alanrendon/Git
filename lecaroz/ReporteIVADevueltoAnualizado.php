<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
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
			$tpl = new TemplatePower('plantillas/ban/ReporteIVADevueltoAnualizadoInicio.tpl');
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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov = 18";

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
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					result.rfc_cia,
					anio,
					mes,
					SUM(importe) AS importe
				FROM
					(
						SELECT
							ec.num_cia,
							cc.nombre_corto AS nombre_cia,
							cc.rfc AS rfc_cia,
							EXTRACT(YEAR FROM fecha) AS anio,
							EXTRACT(MONTH FROM fecha) AS mes,
							SUM(importe) AS importe
						FROM
							estado_cuenta AS ec
							LEFT JOIN catalogo_companias AS cc USING (num_cia)
						WHERE
							{$condiciones_string}
						GROUP BY
							ec.num_cia,
							nombre_cia,
							rfc_cia,
							anio,
							mes
					) AS result
				GROUP BY
					result.rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes
				";
			}
			else
			{
				$sql = "SELECT
					ec.num_cia,
					cc.razon_social AS nombre_cia,
					cc.rfc AS rfc_cia,
					EXTRACT(YEAR FROM fecha) AS anio,
					EXTRACT(MONTH FROM fecha) AS mes,
					SUM(importe) AS importe
				FROM
					estado_cuenta AS ec
					LEFT JOIN catalogo_companias AS cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					ec.num_cia,
					nombre_cia,
					rfc_cia,
					anio,
					mes
				ORDER BY
					ec.num_cia,
					anio,
					mes";
			}

			$query = $db->query($sql);

			if ($query)
			{
				$datos = array();

				$totales = array_fill(1, 12, 0);

				$num_cia = NULL;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre'	=> utf8_encode($row['nombre_cia']),
							'rfc'		=> utf8_encode($row['rfc_cia']),
							'importes'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$num_cia]['importes'][$row['mes']] = floatval($row['importe']);
				}

				$tpl = new TemplatePower('plantillas/ban/ReporteIVADevueltoAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $num_cia => $datos_cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre']);

					foreach ($datos_cia['importes'] as $mes => $importe)
					{
						// Obtener desglose de importes para tooltip
						if ($importe != 0)
						{
							$fecha_mes_1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $_REQUEST['anio']));
							$fecha_mes_2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $_REQUEST['anio']));

							$result = $db->query("SELECT
								ec.num_cia,
								cc.nombre_corto AS nombre_cia,
								ec.fecha,
								ec.fecha_con AS cobrado,
								ec.importe
							FROM
								estado_cuenta ec
								LEFT JOIN catalogo_companias cc USING (num_cia)
							WHERE
								num_cia IN (" . (isset($_REQUEST['agrupar_rfc']) ? "SELECT num_cia FROM catalogo_companias WHERE rfc = '{$datos_cia['rfc']}'" : $num_cia) . ")
								AND fecha BETWEEN '{$fecha_mes_1}' AND '{$fecha_mes_2}'
								AND cod_mov = 18
							ORDER BY
								ec.fecha,
								ec.num_cia");

							$info = '<table id="info-table"><tr><th>Compa&ntilde;&iacute;a</th><th>Fecha</th><th>Cobrado</th><th>Importe</th></tr>';

							foreach ($result as $row)
							{
								$info .= "<tr><td>{$row['num_cia']} {$row['nombre_cia']}</td><td class=\"green\">{$row['fecha']}</td><td class=\"orange\">{$row['cobrado']}</td><td class=\"blue\" align=\"right\">" . number_format($row['importe'], 2) . "</td></tr>";
							}
						}

						$tpl->assign('mes' . $mes, $importe != 0 ? '<span id="tooltip-info" data-tooltip="' . htmlentities($info) . '">' . number_format($importe, 2) . '</span>' : '&nbsp;');

						$totales[$mes] += $importe;
					}

					$tpl->assign('total', number_format(array_sum($datos_cia['importes']), 2));
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->assign('_ROOT.total' . $mes, number_format($total, 2));
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov = 18";

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
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					result.rfc_cia,
					anio,
					mes,
					SUM(importe) AS importe
				FROM
					(
						SELECT
							ec.num_cia,
							cc.nombre_corto AS nombre_cia,
							cc.rfc AS rfc_cia,
							EXTRACT(YEAR FROM fecha) AS anio,
							EXTRACT(MONTH FROM fecha) AS mes,
							SUM(importe) AS importe
						FROM
							estado_cuenta AS ec
							LEFT JOIN catalogo_companias AS cc USING (num_cia)
						WHERE
							{$condiciones_string}
						GROUP BY
							ec.num_cia,
							nombre_cia,
							rfc_cia,
							anio,
							mes
					) AS result
				GROUP BY
					result.rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes
				";
			}
			else
			{
				$sql = "SELECT
					ec.num_cia,
					cc.razon_social AS nombre_cia,
					cc.rfc AS rfc_cia,
					EXTRACT(YEAR FROM fecha) AS anio,
					EXTRACT(MONTH FROM fecha) AS mes,
					SUM(importe) AS importe
				FROM
					estado_cuenta AS ec
					LEFT JOIN catalogo_companias AS cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					ec.num_cia,
					nombre_cia,
					rfc_cia,
					anio,
					mes
				ORDER BY
					ec.num_cia,
					anio,
					mes";
			}

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$totales = array_fill(1, 12, 0);

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre'	=> utf8_encode($row['nombre_cia']),
							'rfc'		=> utf8_encode($row['rfc_cia']),
							'importes'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$num_cia]['importes'][$row['mes']] = floatval($row['importe']);

					$totales[$row['mes']] += $row['importe'];
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

						$this->Cell(0, 4, 'REPORTE DE DEVOLUCIONES DE I.V.A. ' . $_REQUEST['anio'], 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(80, 4, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(18, 4, utf8_decode('ENE'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('FEB'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('MAR'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('ABR'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('MAY'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('JUN'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('JUL'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('AGO'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('SEP'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('OCT'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('NOV'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('DIC'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('TOTAL'), 1, 0, 'R');

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

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('L', array(216, 340));

				$rows = 0;

				foreach ($datos as $num_cia => $datos_cia)
				{
					$pdf->SetFont('ARIAL', '', 8);

					$nombre_cia = "{$num_cia} {$datos_cia['nombre']}";

					while ($pdf->GetStringWidth($nombre_cia) > 80)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(80, 4, utf8_decode($nombre_cia), 1, 0);

					foreach ($datos_cia['importes'] as $mes => $importe)
					{
						$pdf->Cell(18, 4, $importe != 0 ? number_format($importe, 2) : '', 1, 0, 'R');
					}

					$pdf->Cell(18, 4, number_format(array_sum($datos_cia['importes']), 2), 1, 1, 'R');

					if ($rows < 45)
					{
						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', array(216, 340));
						$pdf->SetMargins(5, 5, 5);
					}
				}

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(80, 4, utf8_decode('Totales'), 1, 0, 'R');

				foreach ($totales as $mes => $total)
				{
					$pdf->Cell(18, 4, number_format($total, 2), 1, 0, 'R');
				}

				$pdf->Cell(18, 4, number_format(array_sum($totales), 2), 1, 0, 'R');

				$pdf->Output('reporte-iva-devuelto-' . $_REQUEST['anio'] . '.pdf', 'I');
			}

			break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "ec.cod_mov = 18";

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
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			if (isset($_REQUEST['agrupar_rfc']))
			{
				$sql = "SELECT
					(SELECT num_cia FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS num_cia,
					(SELECT razon_social FROM catalogo_companias WHERE rfc = result.rfc_cia ORDER BY num_cia LIMIT 1) AS nombre_cia,
					result.rfc_cia,
					anio,
					mes,
					SUM(importe) AS importe
				FROM
					(
						SELECT
							ec.num_cia,
							cc.nombre_corto AS nombre_cia,
							cc.rfc AS rfc_cia,
							EXTRACT(YEAR FROM fecha) AS anio,
							EXTRACT(MONTH FROM fecha) AS mes,
							SUM(importe) AS importe
						FROM
							estado_cuenta AS ec
							LEFT JOIN catalogo_companias AS cc USING (num_cia)
						WHERE
							{$condiciones_string}
						GROUP BY
							ec.num_cia,
							nombre_cia,
							rfc_cia,
							anio,
							mes
					) AS result
				GROUP BY
					result.rfc_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					anio,
					mes
				";
			}
			else
			{
				$sql = "SELECT
					ec.num_cia,
					cc.razon_social AS nombre_cia,
					cc.rfc AS rfc_cia,
					EXTRACT(YEAR FROM fecha) AS anio,
					EXTRACT(MONTH FROM fecha) AS mes,
					SUM(importe) AS importe
				FROM
					estado_cuenta AS ec
					LEFT JOIN catalogo_companias AS cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					ec.num_cia,
					nombre_cia,
					rfc_cia,
					anio,
					mes
				ORDER BY
					ec.num_cia,
					anio,
					mes";
			}

			$query = $db->query($sql);

			if ($query)
			{
				$data = '"","REPORTE DE DEVOLUCIONES DE I.V.A. ANUALIZADO ' . $_REQUEST['anio'] . '"' . "\n\n";
				$data .= '"#","COMPAÑIA","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","TOTAL"' . "\n";

				$datos = array();

				$totales = array_fill(1, 12, 0);

				$num_cia = NULL;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre'	=> utf8_encode($row['nombre_cia']),
							'rfc'		=> utf8_encode($row['rfc_cia']),
							'importes'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$num_cia]['importes'][$row['mes']] = floatval($row['importe']);

					$totales[$row['mes']] += floatval($row['importe']);
				}

				foreach ($datos as $num_cia => $datos_cia)
				{
					$data .= '"' . $num_cia . '","' . $datos_cia['nombre'] . '","' . implode('","', array_map('toNumberFormat', $datos_cia['importes'])) . '","' . number_format(array_sum($datos_cia['importes']), 2) . '"' . "\n";
				}

				if (count($query) > 1)
				{
					$data .= '"","TOTALES","' . implode('","', array_map('toNumberFormat', $totales)) . '","' . number_format(array_sum($totales), 2) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-iva-devuelto-anualizado.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReporteIVADevueltoAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
