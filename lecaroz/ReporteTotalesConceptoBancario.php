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

		case 'obtener_codigos':
			$tabla = $_REQUEST['banco'] == 1 ? 'catalogo_mov_bancos' : 'catalogo_mov_santander';

			$result = $db->query("SELECT
				cod_mov AS value,
				cod_mov || ' ' || descripcion AS text
			FROM
				{$tabla}
			GROUP BY
				value,
				text
			ORDER BY
				value");

			if ($result)
			{
				echo json_encode($result);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/ReporteTotalesConceptoBancarioInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha1', date('01/m/Y'));
			$tpl->assign('fecha2', date('d/m/Y'));

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

				$tpl = new TemplatePower('plantillas/ban/ReporteTotalesConceptoBancarioConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('cod_mov', $result[0]['cod_mov']);
				$tpl->assign('descripcion', $result[0]['descripcion']);

				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2']);

				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('importe', number_format($row['importe'], 2));

					$total += $row['importe'];
				}

				$tpl->assign('_ROOT.total', number_format($total, 2));

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

$tpl = new TemplatePower('plantillas/ban/ReporteTotalesConceptoBancario.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
