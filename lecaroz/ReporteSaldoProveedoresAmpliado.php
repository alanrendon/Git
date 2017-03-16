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
			$tpl = new TemplatePower('plantillas/ban/ReporteSaldoProveedoresAmpliadoInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "pp.total > 0";

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
					$condiciones[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				pp.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				SUM(pp.total) AS saldo,
				COUNT(pp.id) AS facturas,
				MIN(pp.fecha) AS mas_antigua,
				SUM(CASE WHEN copia_fac = TRUE THEN 1 ELSE 0 END) AS validadas,
				COALESCE((
					SELECT
						COUNT(id)
					FROM
						facturas_pendientes
					WHERE
						num_proveedor = pp.num_proveedor
						AND fecha_aclaracion IS NULL
				), 0) AS por_aclarar
			FROM
				pasivo_proveedores pp
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
			" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
			GROUP BY
				pp.num_proveedor,
				cp.nombre
			ORDER BY
				pp.num_proveedor");

			if ($result)
			{
				$totales = array(
					'saldo'			=> 0,
					'facturas'		=> 0,
					'validadas'		=> 0,
					'por_aclarar'	=> 0
				);

				$tpl = new TemplatePower('plantillas/ban/ReporteSaldoProveedoresAmpliadoConsulta.tpl');
				$tpl->prepare();

				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));

					$tpl->assign('saldo', number_format($row['saldo'], 2));
					$tpl->assign('facturas', number_format($row['facturas']));
					$tpl->assign('mas_antigua', $row['mas_antigua']);
					$tpl->assign('validadas', $row['validadas'] != 0 ? number_format($row['validadas']) : '&nbsp;');
					$tpl->assign('por_aclarar', $row['por_aclarar'] != 0 ? number_format($row['por_aclarar']) : '&nbsp;');

					$totales['saldo'] += $row['saldo'];
					$totales['facturas'] += $row['facturas'];
					$totales['validadas'] += $row['validadas'];
					$totales['por_aclarar'] += $row['por_aclarar'];
				}

				foreach ($totales as $campo => $total)
				{
					$tpl->assign('_ROOT.' . $campo, number_format($total, $campo == 'saldo' ? 2 : 0));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$condiciones = array();

			$condiciones[] = "pp.total > 0";

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
					$condiciones[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				pp.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				SUM(pp.total) AS saldo,
				COUNT(pp.id) AS facturas,
				MIN(pp.fecha) AS mas_antigua,
				SUM(CASE WHEN copia_fac = TRUE THEN 1 ELSE 0 END) AS validadas,
				COALESCE((
					SELECT
						COUNT(id)
					FROM
						facturas_pendientes
					WHERE
						num_proveedor = pp.num_proveedor
						AND fecha_aclaracion IS NULL
				), 0) AS por_aclarar
			FROM
				pasivo_proveedores pp
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
			" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
			GROUP BY
				pp.num_proveedor,
				cp.nombre
			ORDER BY
				pp.num_proveedor");

			if ($result)
			{
				$totales = array(
					'saldo'			=> 0,
					'facturas'		=> 0,
					NULL,
					'validadas'		=> 0,
					'por_aclarar'	=> 0
				);

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

						$this->Cell(0, 5, utf8_decode('REPORTE DE SALDO A PROVEEDORES AMPLIADO'), 0, 1, 'C');

						$this->Ln(5);

						$this->Cell(64, 5, utf8_decode('PROVEEDOR'), 1, 0, 'C');
						$this->Cell(28, 5, utf8_decode('SALDO'), 1, 0, 'C');
						$this->Cell(28, 5, utf8_decode('#FACTURAS'), 1, 0, 'C');
						$this->Cell(28, 5, utf8_decode('MAS ANTIGUAS'), 1, 0, 'C');
						$this->Cell(28, 5, utf8_decode('VALIDADAS'), 1, 0, 'C');
						$this->Cell(28, 5, utf8_decode('POR ACLARAR'), 1, 0, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('ARIAL', 'B', 8);
						$this->SetTextColor(0, 0, 0);
						$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 6);

				$pdf->AddPage('P', 'Letter');

				$rows = 0;

				$pdf->SetFont('ARIAL', '', 10);

				$colores = array(
					'blue'		=> array(0, 0, 204),
					'green'		=> array(0, 102, 0),
					'orange'	=> array(255, 51, 0),
					'red'		=> array(204, 0, 0),
					'purple'	=> array(102, 51, 204),
					'grey'		=> array(51, 51, 51)
				);

				foreach ($result as $row)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->SetTextColor(0, 0, 0);

					$nombre_pro = "{$row['num_pro']} {$row['nombre_pro']}";

					while ($pdf->GetStringWidth($nombre_pro) > 64)
					{
						$nombre_pro = substr($nombre_pro, 0, strlen($nombre_pro) - 1);
					}

					$pdf->Cell(64, 5, $nombre_pro, 1, 0);

					$pdf->SetTextColor($colores['blue'][0], $colores['blue'][1], $colores['blue'][2]);

					$pdf->Cell(28, 5, number_format($row['saldo'], 2), 1, 0, 'R');

					$pdf->SetTextColor($colores['orange'][0], $colores['orange'][1], $colores['orange'][2]);

					$pdf->Cell(28, 5,number_format($row['facturas']), 1, 0, 'R');

					$pdf->SetTextColor($colores['purple'][0], $colores['purple'][1], $colores['purple'][2]);

					$pdf->Cell(28, 5, $row['mas_antigua'], 1, 0, 'C');

					$pdf->SetTextColor($colores['green'][0], $colores['green'][1], $colores['green'][2]);

					$pdf->Cell(28, 5, $row['validadas'] != 0 ? number_format($row['validadas']) : '', 1, 0, 'R');

					$pdf->SetTextColor($colores['red'][0], $colores['red'][1], $colores['red'][2]);

					$pdf->Cell(28, 5, $row['por_aclarar'] != 0 ? number_format($row['por_aclarar']) : '', 1, 0, 'R');

					$pdf->Ln();

					$totales['saldo'] += $row['saldo'];
					$totales['facturas'] += $row['facturas'];
					$totales['validadas'] += $row['validadas'];
					$totales['por_aclarar'] += $row['por_aclarar'];
				}

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(64, 5, utf8_decode('TOTALES'), 1, 0, 'R');

				foreach ($totales as $campo => $total)
				{
					$pdf->Cell(28, 5, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
				}

				$pdf->Output('reporte-saldo-proveedores-ampliado.pdf', 'I');
			}

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = "pp.total > 0";

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
					$condiciones[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				pp.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				SUM(pp.total) AS saldo,
				COUNT(pp.id) AS facturas,
				MIN(pp.fecha) AS mas_antigua,
				SUM(CASE WHEN copia_fac = TRUE THEN 1 ELSE 0 END) AS validadas,
				COALESCE((
					SELECT
						COUNT(id)
					FROM
						facturas_pendientes
					WHERE
						num_proveedor = pp.num_proveedor
						AND fecha_aclaracion IS NULL
				), 0) AS por_aclarar
			FROM
				pasivo_proveedores pp
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
			" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
			GROUP BY
				pp.num_proveedor,
				cp.nombre
			ORDER BY
				pp.num_proveedor");

			if ($result)
			{
				$totales = array(
					'saldo'			=> 0,
					'facturas'		=> 0,
					NULL,
					'validadas'		=> 0,
					'por_aclarar'	=> 0
				);

				$string = '"","REPORTE DE SALDO A PROVEEDORES AMPLIADO"' . "\n\n";

				$string .= '"#","PROVEEDOR","SALDO","#FACTURAS","MAS ANTIGUA","VALIDADAS","POR ACLARAR"' . "\n";

				foreach ($result as $row)
				{
					$string .= '"' . $row['num_pro'] . '","' . utf8_encode($row['nombre_pro']) . '",';
					$string .= '"' . number_format($row['saldo'], 2) . '",';
					$string .= '"' . number_format($row['facturas'], 2) . '",';
					$string .= '"' . $row['mas_antigua'] . '",';
					$string .= '"' . ($row['validadas'] != 0 ? number_format($row['validadas'], 2) : '0') . '",';
					$string .= '"' . ($row['por_aclarar'] != 0 ? number_format($row['por_aclarar'], 2) : '0') . '"' . "\n";

					$totales['saldo'] += $row['saldo'];
					$totales['facturas'] += $row['facturas'];
					$totales['validadas'] += $row['validadas'];
					$totales['por_aclarar'] += $row['por_aclarar'];
				}

				$string .= '"","TOTALES",';

				$string .= '"' . number_format($totales['saldo'], 2) . '",';
				$string .= '"' . number_format($totales['facturas']) . '",';
				$string .= '"",';
				$string .= '"' . ($totales['validadas'] != 0 ? number_format($totales['validadas'], 2) : '0') . '",';
				$string .= '"' . ($totales['por_aclarar'] != 0 ? number_format($totales['por_aclarar'], 2) : '0') . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-saldo-proveedores-ampliado.csv');

				echo $string;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReporteSaldoProveedoresAmpliado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
