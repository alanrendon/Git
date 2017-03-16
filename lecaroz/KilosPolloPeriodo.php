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

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'X',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

function consulta($params)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $params['mes'], 1, $params['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $params['mes'] + 1, 0, $params['anio']));

	$dias = date('j', mktime(0, 0, 0, $params['mes'] + 1, 0, $params['anio']));

	$condiciones = array();

	$condiciones[] = "mov.fecha_mov BETWEEN '{$fecha1}' AND '{$fecha2}'";

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
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
			$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones[] = "cc.idadministrador = {$params['admin']}";
	}

	if (isset($params['num_pro']) && $params['num_pro'] > 0)
	{
		$condiciones[] = "mov.num_proveedor = {$params['num_pro']}";
	}

	if (isset($params['codmp']) && count($params['codmp']) > 0)
	{
		$condiciones[] = "mov.codmp IN (" . implode(', ', $params['codmp']) . ")";
	}
	else
	{
		$condiciones[] = "mov.codmp IN (160, 600, 700, 573)";
	}

	$condiciones_string = implode(' AND ', $condiciones);

	$result = $db->query("SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		EXTRACT(DAY FROM mov.fecha_mov) AS dia,
		AVG(mov.precio) AS precio,
		SUM(mov.kilos) AS kilos
	FROM
		fact_rosticeria mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = mov.num_proveedor)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		dia
	ORDER BY
		num_cia,
		dia");

	if ($result)
	{
		$datos = array();
		$totales = array_fill(1, $dias, 0);

		$num_cia = NULL;

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$datos[$num_cia] = array(
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre_cia'],
					'kilos'			=> array_fill(1, $dias, 0),
					'precios'		=> array_fill(1, $dias, 0),
					'total'			=> 0
				);
			}

			$datos[$num_cia]['kilos'][$row['dia']] = floatval($row['kilos']);
			$datos[$num_cia]['precios'][$row['dia']] = floatval($row['precio']);
			$datos[$num_cia]['total'] += floatval($row['kilos']);

			$totales[$row['dia']] += floatval($row['kilos']);
		}

		return array(
			'dias'		=> $dias,
			'datos' 	=> $datos,
			'totales'	=> $totales
		);
	}

	return NULL;
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ros/KilosPolloPeriodoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

			$admins = $db->query("SELECT
				idadministrador AS value,
				nombre_administrador AS text
			FROM
				catalogo_administradores
			ORDER BY
				text");

			if ($admins)
			{
				foreach ($admins as $a)
				{
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			foreach ($_meses as $value => $text)
			{
				$tpl->newBlock('mes');

				$tpl->assign('value', $value);
				$tpl->assign('text', mb_strtoupper($text));

				if ($value == date('n'))
				{
					$tpl->assign('selected', ' selected=""');
				}
			}

			$mps = $db->query("SELECT
				codmp AS value,
				nombre AS text
			FROM
				catalogo_mat_primas
			WHERE
				codmp IN (600, 160, 700, 573, 334, 297, 363, 434)
			ORDER BY
				COALESCE(orden, 9999),
				codmp");

			if ($mps)
			{
				foreach ($mps as $mp)
				{
					$tpl->newBlock('mp');

					$tpl->assign('value', $mp['value']);
					$tpl->assign('text', utf8_encode($mp['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consulta':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$dias = $result['dias'];

				$anio = $_REQUEST['anio'];

				$tpl = new TemplatePower('plantillas/ros/KilosPolloPeriodoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);
				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

				foreach (range(1, $dias) as $dia)
				{
					$tpl->newBlock('dia');
					$tpl->assign('dia', '(' . $_dias[date('w', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']))] . ')&nbsp;' . $dia);
				}

				foreach ($datos as $num_cia => $cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $cia['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));

					$tpl->assign('total', '<span class="orange float-left">(' . number_format(array_sum($cia['precios']) / count(array_filter($cia['precios'])), 2) . ')</span>&nbsp;' . number_format($cia['total'], 2));
					$tpl->assign('promedio', number_format($cia['total'] / count(array_filter($cia['kilos'])), 2));

					foreach ($cia['kilos'] as $dia => $kilos)
					{
						$tpl->newBlock('kilos');

						$tpl->assign('kilos', $kilos != 0 ? '<span class="orange float-left">(' . number_format($cia['precios'][$dia], 2) . ')</span>&nbsp;' . number_format($kilos, 2) : '&nbsp;');
					}
				}

				foreach ($totales as $dia => $total)
				{
					$tpl->newBlock('total');

					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));
				$tpl->assign('_ROOT.promedio', number_format(array_sum($totales) / count(array_filter($totales)), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$dias = $result['dias'];

				$anio = $_REQUEST['anio'];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $datos, $anio;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, mb_strtoupper(utf8_decode("REPORTE DE KILOS COMPRADOS DE PRODUCTOS DE ROSTICERÍA")), 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("{$_meses[$_REQUEST['mes']]} {$anio}")), 0, 1, 'C');
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

				$pdf->SetFont('ARIAL', '', 8);

				foreach ($datos as $codmp => $mp)
				{
					$pdf->SetFont('ARIAL', 'B', 12);

					$pdf->Cell(330, 5, "{$codmp} {$mp['nombre_mp']}", 1, 1);

					foreach ($mp['proveedores'] as $num_pro => $pro)
					{
						$pdf->SetFont('ARIAL', 'B', 10);

						$pdf->Cell(330, 5, "{$num_pro} {$pro['nombre_pro']}", 1, 1);

						foreach ($pro['cias'] as $num_cia => $cia)
						{
							$pdf->SetFont('ARIAL', 'B', 8);

							$nombre_cia = "{$cia['num_cia']} {$cia['nombre_cia']}";

							while ($pdf->GetStringWidth($nombre_cia) > 50)
							{
								$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
							}

							$pdf->Cell(50, 5, $nombre_cia, 1, 0);

							$pdf->SetFont('ARIAL', '', 8);

							foreach ($cia['movs'] as $mes => $cantidad)
							{
								$pdf->Cell(20, 5, $cantidad != 0 ? number_format($cantidad, 2) : '', 1, 0, 'R');
							}

							$pdf->Cell(20, 5, number_format($cia['total'], 2), 1, 0, 'R');
							$pdf->Cell(20, 5, number_format($cia['promedio'], 2), 1, 1, 'R');
						}

						$pdf->SetFont('ARIAL', 'B', 8);

						$pdf->Cell(50, 5, 'TOTALES PROVEEDOR', 1, 0, 'R');

						foreach ($pro['totales'] as $mes => $total)
						{
							$pdf->Cell(20, 5, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
						}

						$pdf->Cell(20, 5, number_format(array_sum($pro['totales']), 2), 1, 0, 'R');
						$pdf->Cell(20, 5, number_format(array_sum($pro['totales']) / count(array_filter($pro['totales'])), 2), 1, 1, 'R');

						$pdf->Cell(330, 5, "", 1, 1);
					}

					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->Cell(50, 5, 'TOTALES PRODUCTO', 1, 0, 'R');

					foreach ($mp['totales'] as $mes => $total)
					{
						$pdf->Cell(20, 5, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
					}

					$pdf->Cell(20, 5, number_format(array_sum($mp['totales']), 2), 1, 0, 'R');
					$pdf->Cell(20, 5, number_format(array_sum($mp['totales']) / count(array_filter($mp['totales'])), 2), 1, 1, 'R');

					$pdf->Cell(330, 5, "", 1, 1);
				}

				$pdf->SetFont('ARIAL', 'B', 10);

				$pdf->Cell(50, 5, 'TOTALES GENERALES', 1, 0, 'R');

				foreach ($totales as $mes => $total)
				{
					$pdf->Cell(20, 5, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
				}

				$pdf->Cell(20, 5, number_format(array_sum($totales), 2), 1, 0, 'R');
				$pdf->Cell(20, 5, number_format(array_sum($totales) / count(array_filter($totales)), 2), 1, 1, 'R');

				$pdf->Output('compra-pollos-anual.pdf', 'I');
			}

			break;

		case 'exportar':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];
				$dias = $result['dias'];

				$anio = $_REQUEST['anio'];

				$data = utf8_decode('"","REPORTE DE KILOS COMPRADOS DE PRODUCTOS DE ROSTICERÍA"') . "\n";
				$data = utf8_decode('"","' . $_meses[$_REQUEST['mes']] . ' ' . $anio . '"') . "\n\n";

				$data .= utf8_decode('"#","COMPAÑIA",');

				foreach (range(1, $dias) as $dia)
				{
					$data .= '"(' . $_dias[date('w', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']))] . ')","' . $dia . '",';
				}

				$data .= '"","TOTAL","PROMEDIO"' . "\n";

				$data .= '"","",';

				foreach (range(1, $dias) as $dia)
				{
					$data .= '"PRECIO","KILOS",';
				}

				$data .= '"PRECIO","KILOS","KILOS"' . "\n";

				foreach ($datos as $num_cia => $cia)
				{
					$data .= '"' . $cia['num_cia'] . '","' . $cia['nombre_cia'] . '",';

					foreach ($cia['kilos'] as $dia => $kilos)
					{
						$data .= '"' . number_format($cia['precios'][$dia], 2) . '","' . number_format($kilos, 2) . '",';
					}

					$data .= '"' . number_format(array_sum($cia['precios']) / count(array_filter($cia['precios'])), 2) . '","' . number_format($cia['total'], 2) . '","' . number_format($cia['total'] / count(array_filter($cia['kilos'])), 2) . '"' . "\n";
				}

				$data .= '"","TOTALES",';

				foreach ($totales as $dia => $total)
				{
					$data .= '"","' . number_format($total, 2) . '",';
				}

				$data .= '"","' . number_format(array_sum($totales), 2) . '","' . number_format(array_sum($totales) / count(array_filter($totales)), 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=kilos-pollo.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/KilosPolloPeriodo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
