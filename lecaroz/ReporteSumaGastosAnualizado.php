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

$gastos = NULL;
$gastos_caja = NULL;

function consulta($params)
{
	global $db, $gastos, $gastos_caja;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $params['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $params['anio']));

	$condiciones1 = array();
	$condiciones2 = array();
	$condiciones3 = array();

	$condiciones1[] = 'mg.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$condiciones2[] = 'gc.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$condiciones3[] = 'rg.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

	$condiciones1[] = "mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
	$condiciones2[] = "gc.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
	$condiciones3[] = "rg.anio = {$params['anio']}";

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
			$condiciones1[] = 'mg.num_cia IN (' . implode(', ', $cias) . ')';
			$condiciones2[] = 'gc.num_cia IN (' . implode(', ', $cias) . ')';
			$condiciones3[] = 'rg.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['gastos']) && trim($params['gastos']) != '')
	{
		$gastos = array();

		$pieces = explode(',', $params['gastos']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$gastos[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$gastos[] = $piece;
			}
		}

		if (count($gastos) > 0)
		{
			$condiciones1[] = 'mg.codgastos IN (' . implode(', ', $gastos) . ')';
			$condiciones3[] = 'rg.codgastos IN (' . implode(', ', $gastos) . ')';
		}
	}
	else
	{
		$condiciones1[] = 'mg.codgastos IN (-1)';
		$condiciones3[] = 'rg.codgastos IN (-1)';
	}

	if (isset($params['gastos_caja']) && trim($params['gastos_caja']) != '')
	{
		$gastos_caja = array();

		$pieces = explode(',', $params['gastos_caja']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$gastos_caja[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$gastos_caja[] = $piece;
			}
		}

		if (count($gastos_caja) > 0)
		{
			$condiciones2[] = 'gc.cod_gastos IN (' . implode(', ', $gastos_caja) . ')';
		}
	}
	else
	{
		$condiciones2[] = 'gc.cod_gastos IN (-1)';
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones1[] = 'cc.idadministrador = ' . $params['admin'];
		$condiciones2[] = 'cc.idadministrador = ' . $params['admin'];
		$condiciones3[] = 'cc.idadministrador = ' . $params['admin'];
	}

	$result = $db->query("SELECT
		num_cia,
		nombre_cia,
		mes,
		SUM(importe) AS importe
	FROM
		(
			SELECT
				mg.num_cia,
				cc.nombre_corto AS nombre_cia,
				EXTRACT(MONTH FROM mg.fecha) AS mes,
				SUM(mg.importe) AS importe
			FROM
				movimiento_gastos mg
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones1) . "
			GROUP BY
				mg.num_cia,
				nombre_cia,
				mes

			UNION

			SELECT
				gc.num_cia,
				cc.nombre_corto AS nombre_cia,
				EXTRACT(MONTH FROM gc.fecha) AS mes,
				SUM(gc.importe) AS importe
			FROM
				gastos_caja gc
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones2) . "
			GROUP BY
				gc.num_cia,
				nombre_cia,
				mes

			UNION

			SELECT
				rg.num_cia,
				cc.nombre_corto AS nombre_cia,
				rg.mes,
				SUM(rg.importe) AS importe
			FROM
				reserva_gastos rg
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones3) . "
			GROUP BY
				rg.num_cia,
				nombre_cia,
				mes
		) resultado

	GROUP BY
		num_cia,
		nombre_cia,
		mes
	ORDER BY
		num_cia,
		mes");

	if ($result)
	{
		$num_cia = NULL;

		$datos = array();

		$totales = array_fill(1, 12, 0);

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$datos[$num_cia] = array(
					'nombre'	=> $row['nombre_cia'],
					'gastos'	=> array_fill(1, 12, 0)
				);
			}

			$datos[$num_cia]['gastos'][$row['mes']] = floatval($row['importe']);

			$totales[$row['mes']] += floatval($row['importe']);
		}

		return array(
			'datos'		=> $datos,
			'totales'	=> $totales
		);
	}

	return NULL;
}

function detalle($anio, $mes, $num_cia, $nombre_cia, $codigos_general, $codigos_caja)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

	$result = $db->query("SELECT
		'GENERAL' AS tipo,
		mg.fecha,
		mg.codgastos,
		cg.descripcion,
		mg.concepto,
		mg.importe,
		c.num_proveedor AS num_pro,
		c.a_nombre AS nombre_pro
	FROM
		movimiento_gastos mg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
		LEFT JOIN cheques c ON (
			c.num_cia = mg.num_cia
			AND c.fecha = mg.fecha
			AND c.folio = mg.folio
		)
	WHERE
		mg.num_cia = {$num_cia}
		AND mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
		AND mg.codgastos IN (" . ($codigos_general ? implode(', ', $codigos_general) : '-1') . ")

	UNION

	SELECT
		'CAJA',
		gc.fecha,
		gc.cod_gastos,
		cgc.descripcion,
		gc.comentario,
		gc.importe,
		NULL,
		NULL
	FROM
		gastos_caja gc
		LEFT JOIN catalogo_gastos_caja cgc ON (cgc.num_gasto = gc.cod_gastos)
	WHERE
		gc.num_cia = {$num_cia}
		AND gc.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
		AND gc.cod_gastos IN (" . ($codigos_caja ? implode(', ', $codigos_caja) : '-1') . ")

	UNION

	SELECT
		'GENERAL',
		CONCAT_WS('/', '01', LPAD(mes::TEXT, 2, '0'), LPAD(anio::TEXT, 2, '0'))::DATE,
		rg.codgastos,
		cg.descripcion,
		'GASTO EN RESERVA',
		rg.importe,
		NULL,
		NULL
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
	WHERE
		rg.num_cia = {$num_cia}
		AND rg.anio = {$anio}
		AND rg.mes = {$mes}
		AND rg.codgastos IN (" . ($codigos_general ? implode(', ', $codigos_general) : '-1') . ")

	ORDER BY
		fecha,
		importe DESC");

	if ($result)
	{
		$info = '<table class="info-table"><tr><th colspan="6">' . $num_cia . ' ' . utf8_encode($nombre_cia) . '</th></tr>';
		$info .= '<tr><th>Tipo</th><th>Fecha</th><th>C&oacute;digo</th><th>Concepto</th><th>Proveedor</th><th>Importe</th></tr>';

		$total = 0;

		foreach ($result as $row)
		{
			$info .= '<tr>
				<td>' . $row['tipo'] . '</td>
				<td align="center">' . $row['fecha'] . '</td>
				<td>' . utf8_encode("{$row['codgastos']} {$row['descripcion']}") . '</td>
				<td>' . utf8_encode($row['concepto']) . '</td>
				<td>' . ($row['num_pro'] > 0 ? utf8_encode("{$row['num_pro']} {$row['nombre_pro']}") : '&nbsp;') . '</td>
				<td align="right" class="' . ($row['importe'] < 0 ? 'red' : 'blue') . '">' . number_format($row['importe'], 2) . '</td>
			</tr>';

			$total += $row['importe'];
		}

		$info .= '<tr><th align="right" colspan="5">Total</th><th align="right">' . number_format($total, 2) . '</th></tr></table>';

		return $info;
	}
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/ReporteSumaGastosAnualizadoInicio.tpl');
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
				foreach ($admins as $a)
				{
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$tpl = new TemplatePower('plantillas/bal/ReporteSumaGastosAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $num_cia => $cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($cia['nombre']));

					foreach ($cia['gastos'] as $mes => $importe)
					{
						$info = detalle($_REQUEST['anio'], $mes, $num_cia, $cia['nombre'], $gastos, $gastos_caja);

						$tpl->assign("importe{$mes}", $importe != 0 ? '<span id="tooltip-info"' . ($importe < 0 ? ' class="red"' : '') . ' data-tooltip="' . htmlentities($info) . '">' . number_format($importe, 2) . '</span>' : '&nbsp;');
					}

					$tpl->assign('total', number_format(array_sum($cia['gastos']), 2));
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->assign("_ROOT.total{$mes}", $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

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

						$this->Cell(0, 5, utf8_decode('REPORTE DE GASTOS TOTALIZADOS ANUALES'), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("AÑO {$_REQUEST['anio']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(41, 5, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('ENERO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('FEBRERO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('MARZO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('ABRIL'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('MAYO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('JUNIO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('JULIO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('AGOSTO'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('SEPTIEMBRE'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('OCTUBRE'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('NOVIEMBRE'), 1, 0, 'C');
						$this->Cell(22, 5, utf8_decode('DICIEMBRE'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('TOTAL'), 1, 0, 'C');

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

				$pdf->SetFont('ARIAL', '', 10);

				foreach ($datos as $num_cia => $cia)
				{
					$pdf->SetFont('ARIAL', '', 10);

					$nombre_cia = $cia['nombre'];

					while ($pdf->GetStringWidth($nombre_cia) > 41)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(41, 5, utf8_decode("{$num_cia} {$nombre_cia}"), 1, 0);

					foreach ($cia['gastos'] as $mes => $importe)
					{
						$pdf->Cell(22, 5, $importe != 0 ? number_format($importe, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->Cell(24, 5, number_format(array_sum($cia['gastos']), 2), 1, 0, 'R');

					$pdf->Ln();
				}

				$pdf->Cell(41, 5, utf8_decode('TOTALES'), 1, 0, 'R');

				foreach ($totales as $mes => $total)
				{
					$pdf->Cell(22, 5, $total != 0 ? number_format($total, 2) : '', 1, 0, 'R');
				}

				$pdf->Cell(24, 5, number_format(array_sum($totales), 2), 1, 0, 'R');

				$pdf->Output('reporte-suma-gastos-anualizado.pdf', 'I');
			}

			break;

		case 'exportar':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$string = '"REPORTE DE GASTOS TOTALIZADOS ANUALES"' . "\n";
				$string .= '"AÑO ' . $_REQUEST['anio'] . '"' . "\n\n";

				$string .= '"COMPAÑIA","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JULIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","TOTAL"' . "\n";

				foreach ($datos as $num_cia => $cia)
				{
					$string .= '"' . utf8_encode("{$num_cia} {$cia['nombre']}") . '",';

					$string .= '"' . implode('","', $cia['gastos']) . '",';

					$string .= '"' . array_sum($cia['gastos']) . '"' . "\n";
				}

				$string .= '"TOTALES","' . implode('","', $totales) . '","' . array_sum($totales) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-suma-gastos-anualizado.csv');

				echo $string;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteSumaGastosAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
