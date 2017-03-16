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
			$tpl = new TemplatePower('plantillas/ban/ReporteComprasProveedoresAnualizadoInicio.tpl');
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

			$condiciones[] = "f.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "f.importe > 0";

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
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

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
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['filtro']) && $_REQUEST['filtro'] != '')
			{
				$condiciones[] = $_REQUEST['filtro'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				num_pro,
				nombre_pro,
				anio,
				mes,
				SUM(importe) AS importe
			FROM
				(
					SELECT
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						EXTRACT (YEAR FROM f.fecha) AS anio,
						EXTRACT (MONTH FROM f.fecha) AS mes,
						SUM(f.total) AS importe
					FROM
						facturas AS f
						LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor)
						LEFT JOIN catalogo_companias AS cc USING (num_cia)
					WHERE
						{$condiciones_string}
					GROUP BY
						num_pro,
						nombre_pro,
						f.fecha
				) compras
			GROUP BY
				num_pro,
				nombre_pro,
				anio,
				mes
			ORDER BY
				num_pro,
				anio,
				mes";

			$query = $db->query($sql);

			if ($query)
			{
				$datos = array();

				$totales = array_fill(1, 12, 0);

				$num_pro = NULL;

				$cont = 0;

				foreach ($query as $row)
				{
					if ($num_pro != $row['num_pro'])
					{
						if ($num_pro != NULL)
						{
							$cont++;
						}

						$num_pro = $row['num_pro'];

						$datos[$cont] = array(
							'num_pro'	=> intval($num_pro),
							'nombre'	=> utf8_encode($row['nombre_pro']),
							'importes'	=> array_fill(1, 12, 0),
							'porc'		=> 0
						);
					}

					$datos[$cont]['importes'][$row['mes']] = floatval($row['importe']);

					$totales[$row['mes']] += floatval($row['importe']);
				}

				foreach ($datos as $i => $row)
				{
					$datos[$i]['porc'] = array_sum($row['importes']) * 100 / array_sum($totales);
				}

				function cmp($a, $b)
				{
					if (array_sum($a['importes']) == array_sum($b['importes']))
					{
						return 0;
					}

					return (array_sum($a['importes']) > array_sum($b['importes'])) ? -1 : 1;
				}

				usort($datos, 'cmp');

				$tpl = new TemplatePower('plantillas/ban/ReporteComprasProveedoresAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $datos_pro)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_pro', $datos_pro['num_pro']);
					$tpl->assign('nombre_pro', $datos_pro['nombre']);

					foreach ($datos_pro['importes'] as $mes => $importe)
					{
						$tpl->assign('mes' . $mes, $importe != 0 ? number_format($importe, 2) : '&nbsp;');
					}

					$tpl->assign('total', '<span style="float:left;" class="font6 orange">(' . number_format($datos_pro['porc'], 2) . '%)</span> ' . number_format(array_sum($datos_pro['importes']), 2));
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

			$condiciones[] = "f.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "f.importe > 0";

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
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

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
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['filtro']) && $_REQUEST['filtro'] != '')
			{
				$condiciones[] = $_REQUEST['filtro'];
			}


			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				num_pro,
				nombre_pro,
				anio,
				mes,
				SUM(importe) AS importe
			FROM
				(
					SELECT
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						EXTRACT (YEAR FROM f.fecha) AS anio,
						EXTRACT (MONTH FROM f.fecha) AS mes,
						SUM(f.total) AS importe
					FROM
						facturas AS f
						LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor)
						LEFT JOIN catalogo_companias AS cc USING (num_cia)
					WHERE
						{$condiciones_string}
					GROUP BY
						num_pro,
						nombre_pro,
						f.fecha
				) compras
			GROUP BY
				num_pro,
				nombre_pro,
				anio,
				mes
			ORDER BY
				num_pro,
				anio,
				mes";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$totales = array_fill(1, 12, 0);

				$num_pro = NULL;

				$cont = 0;

				foreach ($query as $row)
				{
					if ($num_pro != $row['num_pro'])
					{
						if ($num_pro != NULL)
						{
							$cont++;
						}

						$num_pro = $row['num_pro'];

						$datos[$cont] = array(
							'num_pro'	=> intval($num_pro),
							'nombre'	=> utf8_encode($row['nombre_pro']),
							'importes'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$cont]['importes'][$row['mes']] = floatval($row['importe']);

					$totales[$row['mes']] += $row['importe'];
				}

				function cmp($a, $b)
				{
					if (array_sum($a['importes']) == array_sum($b['importes']))
					{
						return 0;
					}

					return (array_sum($a['importes']) > array_sum($b['importes'])) ? -1 : 1;
				}

				usort($datos, 'cmp');

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

						$this->Cell(0, 4, 'REPORTE DE COMPRAS DE PROVEEDORES ' . $_REQUEST['anio'], 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(44, 4, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(22, 4, utf8_decode('ENE'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('FEB'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('MAR'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('ABR'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('MAY'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('JUN'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('JUL'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('AGO'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('SEP'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('OCT'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('NOV'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('DIC'), 1, 0, 'R');
						$this->Cell(22, 4, utf8_decode('TOTAL'), 1, 0, 'R');

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

				$rows = 0;

				foreach ($datos as $datos_pro)
				{
					$pdf->SetFont('ARIAL', '', 8);

					$nombre_pro = "{$datos_pro['num_pro']} {$datos_pro['nombre']}";

					while ($pdf->GetStringWidth($nombre_pro) > 44)
					{
						$nombre_pro = substr($nombre_pro, 0, strlen($nombre_pro) - 1);
					}

					$pdf->Cell(44, 4, utf8_decode($nombre_pro), 1, 0);

					foreach ($datos_pro['importes'] as $mes => $importe)
					{
						$pdf->Cell(22, 4, $importe != 0 ? number_format($importe, 2) : '', 1, 0, 'R');
					}

					$pdf->Cell(22, 4, number_format(array_sum($datos_pro['importes']), 2), 1, 1, 'R');
				}

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(44, 4, utf8_decode('Totales'), 1, 0, 'R');

				foreach ($totales as $mes => $total)
				{
					$pdf->Cell(22, 4, number_format($total, 2), 1, 0, 'R');
				}

				$pdf->Cell(22, 4, number_format(array_sum($totales), 2), 1, 0, 'R');

				$pdf->Output('reporte-compras-proveedores-anualizado-' . $_REQUEST['anio'] . '.pdf', 'I');
			}

			break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "f.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "f.importe > 0";

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
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

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
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['filtro']) && $_REQUEST['filtro'] != '')
			{
				$condiciones[] = $_REQUEST['filtro'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				num_pro,
				nombre_pro,
				anio,
				mes,
				SUM(importe) AS importe
			FROM
				(
					SELECT
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						EXTRACT (YEAR FROM f.fecha) AS anio,
						EXTRACT (MONTH FROM f.fecha) AS mes,
						SUM(f.total) AS importe
					FROM
						facturas AS f
						LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor)
						LEFT JOIN catalogo_companias AS cc USING (num_cia)
					WHERE
						{$condiciones_string}
					GROUP BY
						num_pro,
						nombre_pro,
						f.fecha
				) compras
			GROUP BY
				num_pro,
				nombre_pro,
				anio,
				mes
			ORDER BY
				num_pro,
				anio,
				mes";

			$query = $db->query($sql);

			if ($query)
			{
				$data = '"","REPORTE DE COMPRAS DE PROVEEDORES ' . $_REQUEST['anio'] . '"' . "\n\n";
				$data .= '"#","PROVEEDORES","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","TOTAL"' . "\n";

				$datos = array();

				$totales = array_fill(1, 12, 0);

				$num_pro = NULL;

				$cont = 0;

				foreach ($query as $row)
				{
					if ($num_pro != $row['num_pro'])
					{
						if ($num_pro != NULL)
						{
							$cont++;
						}

						$num_pro = $row['num_pro'];

						$datos[$cont] = array(
							'num_pro'	=> intval($num_pro),
							'nombre'	=> utf8_encode($row['nombre_pro']),
							'importes'	=> array_fill(1, 12, 0)
						);
					}

					$datos[$cont]['importes'][$row['mes']] = floatval($row['importe']);

					$totales[$row['mes']] += $row['importe'];
				}

				function cmp($a, $b)
				{
					if (array_sum($a['importes']) == array_sum($b['importes']))
					{
						return 0;
					}

					return (array_sum($a['importes']) > array_sum($b['importes'])) ? -1 : 1;
				}

				usort($datos, 'cmp');

				foreach ($datos as $datos_pro)
				{
					$data .= '"' . $datos_pro['num_pro'] . '","' . $datos_pro['nombre'] . '","' . implode('","', array_map('toNumberFormat', $datos_pro['importes'])) . '","' . number_format(array_sum($datos_pro['importes']), 2) . '"' . "\n";
				}

				if (count($query) > 1)
				{
					$data .= '"","TOTALES","' . implode('","', array_map('toNumberFormat', $totales)) . '","' . number_format(array_sum($totales), 2) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-compras-proveedores-anualizado.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReporteComprasProveedoresAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
