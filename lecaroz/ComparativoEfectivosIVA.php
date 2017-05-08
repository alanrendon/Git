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
			$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosIVAInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

			$admins = $db->query("SELECT idadministrador AS value, nombre_administrador AS text FROM catalogo_administradores ORDER BY text");

			if ($admins)
			{
				foreach ($admins as $a) {
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			foreach ($_meses as $value => $text) {
				$tpl->newBlock('mes');

				$tpl->assign('value', $value);
				$tpl->assign('text', utf8_encode(mb_strtoupper($text)));

				if ($value == date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))))
				{
					$tpl->assign('selected', ' selected="selected"');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "COALESCE(pan.efectivo, ros.efectivo) != 0";

			$condiciones[] = "ec.cod_mov IN (1, 16, 44, 99)";

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
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
				cc.nombre_corto AS nombre_cia,
				cc.nombre,
				COALESCE(pan.efectivo, ros.efectivo) AS efectivo,
				SUM (ec.importe) AS depositos,
				CASE
					WHEN cc.tipo_cia = 2
					AND cc.persona_fis_moral = FALSE THEN
						SUM (ec.importe) * 0.16
					ELSE
						0
				END AS iva
			FROM
				estado_cuenta ec
			LEFT JOIN balances_pan pan ON (
				pan.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND pan.anio = EXTRACT(YEAR FROM ec.fecha)
				AND pan.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN balances_ros ros ON (
				ros.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND ros.anio = EXTRACT(YEAR FROM ec.fecha)
				AND ros.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN catalogo_companias cc ON (
				cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
			)
			WHERE
				" . implode(' AND ', $condiciones) . "
			GROUP BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				cc.tipo_cia,
				cc.persona_fis_moral,
				cc.nombre_corto,
				cc.nombre,
				pan.efectivo,
				ros.efectivo
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia)";

			$query = $db->query($sql);

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosIVAConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);
				$tpl->assign('mes', mb_strtoupper($_meses[$_REQUEST['mes']]));

				foreach ($query as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('efectivo', $row['efectivo'] != 0 ? number_format($row['efectivo'], 2) : '&nbsp;');
					$tpl->assign('depositos', $row['depositos'] != 0 ? number_format($row['depositos'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] != 0 ? number_format($row['iva'], 2) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "COALESCE(pan.efectivo, ros.efectivo) != 0";

			$condiciones[] = "ec.cod_mov IN (1, 16, 44, 99)";

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
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
				cc.nombre_corto AS nombre_cia,
				cc.nombre,
				COALESCE(pan.efectivo, ros.efectivo) AS efectivo,
				SUM (ec.importe) AS depositos,
				CASE
					WHEN cc.tipo_cia = 2
					AND cc.persona_fis_moral = FALSE THEN
						SUM (ec.importe) * 0.16
					ELSE
						0
				END AS iva
			FROM
				estado_cuenta ec
			LEFT JOIN balances_pan pan ON (
				pan.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND pan.anio = EXTRACT(YEAR FROM ec.fecha)
				AND pan.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN balances_ros ros ON (
				ros.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND ros.anio = EXTRACT(YEAR FROM ec.fecha)
				AND ros.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN catalogo_companias cc ON (
				cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
			)
			WHERE
				" . implode(' AND ', $condiciones) . "
			GROUP BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				cc.tipo_cia,
				cc.persona_fis_moral,
				cc.nombre_corto,
				cc.nombre,
				pan.efectivo,
				ros.efectivo
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia)";

			$query = $db->query($sql);

			if ($query)
			{
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

						$this->Cell(0, 4, 'COMPARATIVO DE EFECTIVOS CONTRA I.V.A.', 0, 1, 'C');
						$this->Cell(0, 4, utf8_decode(mb_strtoupper($GLOBALS['_meses'][$_REQUEST['mes']]) . ' ' . $_REQUEST['anio']), 'B', 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(61, 4, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('EFECTIVO'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DEPOSITOS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('I.V.A.'), 1, 0, 'C');

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

				$pdf = new PDF('L', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullwidth', 'single');

				$pdf->SetMargins(5, 5, 5);

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('L', 'Letter');

				$rows = 0;

				foreach ($query as $row)
				{
					$pdf->SetFont('ARIAL', '', 10);

					$pdf->SetTextColor(0, 0, 0);

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 61)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(61, 4, $nombre_cia, 1, 0);

					$pdf->SetTextColor(0, 102, 0);

					$pdf->Cell(26, 4, $row['efectivo'] != 0 ? number_format($row['efectivo'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(0, 0, 204);

					$pdf->Cell(26, 4, $row['depositos'] != 0 ? number_format($row['depositos'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(204, 0, 0);

					$pdf->Cell(26, 4, $row['iva'] != 0 ? number_format($row['iva'], 2) : '', 1, 0, 'R');

					if ($rows < 45)
					{
						$pdf->Ln();

						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', 'Letter');
						$pdf->SetMargins(5, 5, 5);
					}
				}

				$pdf->Output('ReporteNomina.pdf', 'I');
			}

			break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$condiciones = array();

			$condiciones[] = "cc.tipo_cia IN (1, 2)";

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "COALESCE(pan.efectivo, ros.efectivo) != 0";

			$condiciones[] = "ec.cod_mov IN (1, 16, 44, 99)";

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
					$condiciones[] = 'COALESCE(ec.num_cia_sec, ec.num_cia) IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS \"#\",
				cc.nombre_corto AS \"COMPAÑIA\",
				COALESCE(pan.efectivo, ros.efectivo) AS \"EFECTIVO\",
				SUM (ec.importe) AS \"DEPOSITOS\",
				CASE
					WHEN cc.tipo_cia = 2
					AND cc.persona_fis_moral = FALSE THEN
						SUM (ec.importe) * 0.16
					ELSE
						0
				END AS \"I.V.A.\"
			FROM
				estado_cuenta ec
			LEFT JOIN balances_pan pan ON (
				pan.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND pan.anio = EXTRACT(YEAR FROM ec.fecha)
				AND pan.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN balances_ros ros ON (
				ros.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
				AND ros.anio = EXTRACT(YEAR FROM ec.fecha)
				AND ros.mes = EXTRACT(MONTH FROM ec.fecha)
			)
			LEFT JOIN catalogo_companias cc ON (
				cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
			)
			WHERE
				" . implode(' AND ', $condiciones) . "
			GROUP BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				cc.tipo_cia,
				cc.persona_fis_moral,
				cc.nombre_corto,
				cc.nombre,
				pan.efectivo,
				ros.efectivo
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia)";

			$query = $db->query($sql);

			if ($query)
			{
				$data = '"","COMPARATIVO DE EFECTIVOS CONTRA I.V.A."' . "\n";
				$data .= '"","' . mb_strtoupper($_meses[$_REQUEST['mes']]) . ' ' . $_REQUEST['anio'] . '"' . "\n\n";

				$data .= '"' . implode('","', array_keys($query[0])) . '"' . "\n";

				foreach ($query as $row)
				{
					$data .= '"' . implode('","', array_values($row)) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=ComparativoEfectivosIVA.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ComparativoEfectivosIVA.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
