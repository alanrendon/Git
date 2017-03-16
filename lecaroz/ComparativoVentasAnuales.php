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
			$tpl = new TemplatePower('plantillas/ban/ComparativoVentasAnualesInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha', date('d/m/Y'));

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

			$condiciones[] = "fecha IN ('{$_REQUEST['fecha']}'::DATE, '{$_REQUEST['fecha']}'::DATE - INTERVAL '1 YEAR', '{$_REQUEST['fecha']}'::DATE - INTERVAL '2 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '3 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '4 YEARS')";

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$ventas = $db->query("SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				fecha,
				EXTRACT(YEAR FROM fecha) AS anio,
				venta_puerta AS importe
			FROM
				total_panaderias tp
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_cia,
				fecha");

			if ($ventas)
			{
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$tpl = new TemplatePower('plantillas/ban/ComparativoVentasAnualesConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('dia', $dia);
				$tpl->assign('mes', mb_strtoupper($_meses[$mes]));

				for ($i = 1, $j = 4; $i <= 5; $i++, $j--)
				{
					$tpl->assign('anio' . $i, $anio - $j);
				}

				$num_cia = NULL;

				$data = array();

				$totales = array_fill($anio - 4, 5, 0);

				foreach ($ventas as $v)
				{
					if ($num_cia != $v['num_cia'])
					{
						$num_cia = $v['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $v['nombre_cia'],
							'ventas'	=> array_fill($anio - 4, 5, 0),
							'pvar'		=> 0
						);
					}

					$data[$num_cia]['ventas'][$v['anio']] = $v['importe'];

					$totales[$v['anio']] += $v['importe'];
				}

				foreach ($data as $num_cia => $cia)
				{
					$anio_ant = $anio - 1;

					$data[$num_cia]['pvar'] = $data[$num_cia]['ventas'][$anio_ant] != 0 ? $data[$num_cia]['ventas'][$anio] * 100 / $data[$num_cia]['ventas'][$anio_ant] - 100 : 0;
				}

				foreach ($data as $num_cia => $cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($cia['nombre']));

					$cont = 1;

					foreach ($cia['ventas'] as $anio => $ventas)
					{
						$tpl->assign('ventas' . $cont, $ventas != 0 ? number_format($ventas, 2) : '&nbsp;');

						$cont++;
					}

					$tpl->assign('pvar', $cia['pvar'] != 0 && abs($cia['pvar']) != 100 ? '<span class="' . ($cia['pvar'] <= 0 ? 'red' : 'blue') . '">' . number_format($cia['pvar'], 3) . '%</span>' : '&nbsp;');
				}

				$cont = 1;

				foreach ($totales as $anio => $total)
				{
					$tpl->assign('_ROOT.total' . $cont, number_format($total, 2));

					$cont++;
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$condiciones = array();

			$condiciones[] = "fecha IN ('{$_REQUEST['fecha']}'::DATE, '{$_REQUEST['fecha']}'::DATE - INTERVAL '1 YEAR', '{$_REQUEST['fecha']}'::DATE - INTERVAL '2 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '3 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '4 YEARS')";

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$ventas = $db->query("SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				fecha,
				EXTRACT(YEAR FROM fecha) AS anio,
				venta_puerta AS importe
			FROM
				total_panaderias tp
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_cia,
				fecha");

			if ($ventas)
			{
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$num_cia = NULL;

				$data = array();

				$totales = array_fill($anio - 4, 5, 0);

				foreach ($ventas as $v)
				{
					if ($num_cia != $v['num_cia'])
					{
						$num_cia = $v['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $v['nombre_cia'],
							'ventas'	=> array_fill($anio - 4, 5, 0),
							'pvar'		=> 0
						);
					}

					$data[$num_cia]['ventas'][$v['anio']] = $v['importe'];

					$totales[$v['anio']] += $v['importe'];
				}

				foreach ($data as $num_cia => $cia)
				{
					$anio_ant = $anio - 1;

					$data[$num_cia]['pvar'] = $data[$num_cia]['ventas'][$anio_ant] != 0 ? $data[$num_cia]['ventas'][$anio] * 100 / $data[$num_cia]['ventas'][$anio_ant] - 100 : 0;
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

						$this->Cell(0, 5, utf8_decode('COMPARATIVO DE VENTAS ANUALES'), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("{$GLOBALS['dia']} DE " . mb_strtoupper($GLOBALS['_meses'][$GLOBALS['mes']])), 0, 1, 'C');

						$this->Ln(5);

						$this->Cell(6, 5, utf8_decode(''), 0, 0);
						$this->Cell(50, 5, utf8_decode('COMPAÑIA'), 1, 0, 'C');

						for ($i = 1, $j = 4; $i <= 5; $i++, $j--)
						{
							$this->Cell(24, 5, utf8_decode($GLOBALS['anio'] - $j), 1, 0, 'C');
						}

						$this->Cell(24, 5, utf8_decode('% VARIACION'), 1, 0, 'C');

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

				$pdf = new PDF('P', 'mm', array(216, 340));

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(TRUE, 6);

				$pdf->AddPage('P', array(216, 340));

				$rows = 0;

				$pdf->SetFont('ARIAL', '', 10);

				foreach ($data as $num_cia => $cia)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(6, 5, utf8_decode(''), 0, 0);

					$pdf->Cell(50, 5, utf8_decode("{$num_cia} {$cia['nombre']}"), 1, 0);

					$colores = array(
						'blue'		=> array(0, 0, 204),
						'green'		=> array(0, 102, 0),
						'orange'	=> array(255, 51, 0),
						'red'		=> array(204, 0, 0),
						'purple'	=> array(102, 51, 204)
					);

					$cont = 0;

					foreach ($cia['ventas'] as $anio => $ventas)
					{
						switch($cont)
						{
							case 0:
								$pdf->SetTextColor($colores['blue'][0], $colores['blue'][1], $colores['blue'][2]);
								break;

							case 1:
								$pdf->SetTextColor($colores['green'][0], $colores['green'][1], $colores['green'][2]);
								break;

							case 2:
								$pdf->SetTextColor($colores['orange'][0], $colores['orange'][1], $colores['orange'][2]);
								break;

							case 3:
								$pdf->SetTextColor($colores['red'][0], $colores['red'][1], $colores['red'][2]);
								break;

							case 4:
								$pdf->SetTextColor($colores['purple'][0], $colores['purple'][1], $colores['purple'][2]);
								break;
						}

						$pdf->Cell(24, 5, $ventas != 0 ? number_format($ventas, 2) : '', 1, 0, 'R');

						$cont++;
					}

					if ($cia['pvar'] < 0)
					{
						$pdf->SetTextColor($colores['red'][0], $colores['red'][1], $colores['red'][2]);
					}
					else
					{
						$pdf->SetTextColor($colores['blue'][0], $colores['blue'][1], $colores['blue'][2]);
					}

					$pdf->Cell(24, 5, $cia['pvar'] != 0 && abs($cia['pvar']) != 100 ? number_format($cia['pvar'], 3) . '%' : '', 1, 0, 'R');

					$pdf->Ln();
				}

				$pdf->Cell(6, 5, utf8_decode(''), 0, 0);

				$pdf->Cell(50, 5, utf8_decode('TOTALES'), 1, 0, 'R');

				foreach ($totales as $anio => $total)
				{
					$pdf->Cell(24, 5, number_format($total, 2), 1, 0, 'R');
				}

				$pdf->Cell(24, 5, '', 1, 0, 'R');

				$pdf->Output('comparativo-ventas-auales.pdf', 'I');
			}

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = "fecha IN ('{$_REQUEST['fecha']}'::DATE, '{$_REQUEST['fecha']}'::DATE - INTERVAL '1 YEAR', '{$_REQUEST['fecha']}'::DATE - INTERVAL '2 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '3 YEARS', '{$_REQUEST['fecha']}'::DATE - INTERVAL '4 YEARS')";

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$ventas = $db->query("SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				fecha,
				EXTRACT(YEAR FROM fecha) AS anio,
				venta_puerta AS importe
			FROM
				total_panaderias tp
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_cia,
				fecha");

			if ($ventas)
			{
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$num_cia = NULL;

				$data = array();

				$totales = array_fill($anio - 4, 5, 0);

				foreach ($ventas as $v)
				{
					if ($num_cia != $v['num_cia'])
					{
						$num_cia = $v['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $v['nombre_cia'],
							'ventas'	=> array_fill($anio - 4, 5, 0),
							'pvar'		=> 0
						);
					}

					$data[$num_cia]['ventas'][$v['anio']] = $v['importe'];

					$totales[$v['anio']] += $v['importe'];
				}

				foreach ($data as $num_cia => $cia)
				{
					$anio_ant = $anio - 1;

					$data[$num_cia]['pvar'] = $data[$num_cia]['ventas'][$anio_ant] != 0 ? $data[$num_cia]['ventas'][$anio] * 100 / $data[$num_cia]['ventas'][$anio_ant] - 100 : 0;
				}

				$string = '"","COMPARATIVO DE VENTAS ANUALES"' . "\n";
				$string .= '"","' . $dia . ' DE ' . mb_strtoupper($_meses[$mes]) . '"' . "\n\n";

				$string .= '"#","COMPAÑIA",';

				for ($i = 1, $j = 4; $i <= 5; $i++, $j--)
				{
					$string .= '"' . ($anio - $j) . '",';
				}

				$string .= '"% VARIACION"' . "\n";

				foreach ($data as $num_cia => $cia)
				{
					$string .= '"' . $num_cia . '","' . utf8_encode($cia['nombre']) . '",';

					$string .= '"' . implode('","', array_map('toNumberFormat', $cia['ventas'])) . '",';

					$string .= '"' . ($cia['pvar'] != 0 && abs($cia['pvar']) != 100 ? number_format($cia['pvar'], 3) : '0') . '"' . "\n";
				}

				$string .= '"","TOTALES",';

				$string .= '"' . implode('","', array_map('toNumberFormat', $totales)) . '",';

				$string .= '""' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=comparativo-ventas-anuales.csv');

				echo $string;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ComparativoVentasAnuales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
