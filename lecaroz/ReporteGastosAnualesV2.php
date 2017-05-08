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

function consulta($params)
{
	global $db;

	$condiciones1 = array();
	$condiciones2 = array();

	$condiciones1[] = "mg.codgastos = {$params['gasto']}";
	$condiciones2[] = "rg.codgastos = {$params['gasto']}";

	$anios = array_filter($params['anio']);

	sort($anios);

	$anios = array_reverse($anios);

	$periodos1 = array();
	$periodos2 = array();

	foreach ($anios as $anio)
	{
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $anio));
		$fecha2 = date('d/m/Y', mktime(0, 0, 0, $params['mes'] + 1, 0, $anio));

		$periodos1[] = "mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
		$periodos2[] = "(rg.anio = {$anio} AND rg.mes <= {$params['mes']})";
	}

	$condiciones1[] = '(' . implode(' OR ', $periodos1) . ')';
	$condiciones2[] = '(' . implode(' OR ', $periodos2) . ')';

	$condiciones1[] = "COALESCE(poc.num_cia_aplica, mg.num_cia) BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$condiciones2[] = "rg.num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

	if (isset($params['filtro']) && $params['filtro'] != '')
	{
		$condiciones1[] = "mg.folio IS {$params['filtro']}";

		if ($params['filtro'] == 'NULL')
		{
			$condiciones2[] = "FALSE";
		}
	}

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
			$condiciones1[] = 'COALESCE(poc.num_cia_aplica, mg.num_cia) IN (' . implode(', ', $cias) . ')';
			$condiciones2[] = 'rg.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones1[] = "cc.idadministrador = {$params['admin']}";
		$condiciones2[] = "cc.idadministrador = {$params['admin']}";
	}

	$condiciones1_string = implode(' AND ', $condiciones1);
	$condiciones2_string = implode(' AND ', $condiciones2);

	$sql = "SELECT
		gasto,
		nombre_gasto,
		num_cia,
		nombre_cia,
		anio,
		mes,
		SUM(importe) AS importe
	FROM
		(
			(
				SELECT
					mg.codgastos AS gasto,
					cg.descripcion AS nombre_gasto,
					COALESCE(poc.num_cia_aplica, mg.num_cia) AS num_cia,
					cc.nombre_corto AS nombre_cia,
					EXTRACT(YEAR FROM mg.fecha) AS anio,
					EXTRACT(MONTH FROM mg.fecha) AS mes,
					SUM(mg.importe) AS importe
				FROM
					movimiento_gastos mg
					LEFT JOIN pagos_otras_cias poc ON (
						poc.num_cia = mg.num_cia
						AND poc.folio = mg.folio
						AND poc.fecha = mg.fecha
					)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(poc.num_cia_aplica, mg.num_cia))
					LEFT JOIN catalogo_gastos cg ON (cg.codgastos = mg.codgastos)
				WHERE
					{$condiciones1_string}
				GROUP BY
					mg.codgastos,
					cg.descripcion,
					COALESCE(poc.num_cia_aplica, mg.num_cia),
					cc.nombre_corto,
					EXTRACT(YEAR FROM mg.fecha),
					EXTRACT(MONTH FROM mg.fecha)
			)

			UNION

			(
				SELECT
					-- rg.id,
					rg.codgastos AS gasto,
					cg.descripcion AS nombre_gasto,
					rg.num_cia,
					cc.nombre_corto AS nombre_cia,
					rg.anio,
					rg.mes,
					SUM(rg.importe) AS importe
				FROM
					reserva_gastos rg
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = rg.num_cia)
					LEFT JOIN catalogo_gastos cg ON (cg.codgastos = rg.codgastos)
				WHERE
					{$condiciones2_string}
				GROUP BY
					-- rg.id,
					rg.codgastos,
					cg.descripcion,
					rg.num_cia,
					cc.nombre_corto,
					rg.anio,
					rg.mes
			)
		) AS datos
	GROUP BY
		gasto,
		nombre_gasto,
		num_cia,
		nombre_cia,
		anio,
		mes
	ORDER BY
		num_cia,
		anio DESC,
		mes";

	$result = $db->query(/*"SELECT
		mg.codgastos AS gasto,
		cg.descripcion AS nombre_gasto,
		COALESCE(poc.num_cia_aplica, mg.num_cia) AS num_cia,
		cc.nombre_corto AS nombre_cia,
		EXTRACT(YEAR FROM mg.fecha) AS anio,
		EXTRACT(MONTH FROM mg.fecha) AS mes,
		SUM(mg.importe) AS importe
	FROM
		movimiento_gastos mg
		LEFT JOIN pagos_otras_cias poc ON (
			poc.num_cia = mg.num_cia
			AND poc.folio = mg.folio
			AND poc.fecha = mg.fecha
		)
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(poc.num_cia_aplica, mg.num_cia))
		LEFT JOIN catalogo_gastos cg ON (cg.codgastos = mg.codgastos)
	WHERE
		{$condiciones_string}
	GROUP BY
		gasto,
		nombre_gasto,
		COALESCE(poc.num_cia_aplica, mg.num_cia),
		nombre_cia,
		anio,
		mes
	ORDER BY
		COALESCE(poc.num_cia_aplica, mg.num_cia),
		anio DESC,
		mes"*/$sql);

	if ($result)
	{
		$datos = array();

		$num_cia = NULL;

		$index = 0;

		$totales = array();

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				if ($num_cia != NULL)
				{
					if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
					{
						$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
						$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));
					}
					// else
					// {
					// 	unset($datos[$index]['series'][$anio]);
					// }

					// if (count(array_filter($datos[$index]['series'][$anios[0]]['importes'])) > 0)
					// {
						$index++;
					// }
					// else
					// {
					// 	unset($datos[$index]);
					// }
				}

				$num_cia = $row['num_cia'];

				$datos[$index] = array(
					'gasto'			=> $row['gasto'],
					'nombre_gasto'	=> $row['nombre_gasto'],
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre_cia'],
					'series'		=> array()
				);

				foreach ($anios as $anio)
				{
					$datos[$index]['series'][$anio] = array(
						'anio'		=> floatval($anio),
						'importes'	=> array_fill(1, 12, 0),
						'total'		=> 0,
						'promedio'	=> 0
					);
				}

				$anio = NULL;
			}

			if ($anio != $row['anio'])
			{
				if ($anio != NULL)
				{
					if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
					{
						$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
						$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));

						$anio++;
					}
					// else
					// {
					// 	unset($datos[$index]['series'][$anio]);
					// }
				}

				$anio = $row['anio'];

				if ( ! isset($totales[$anio]))
				{
					$totales[$anio] = array_fill(1, 12, 0);
				}
			}

			$datos[$index]['series'][$anio]['importes'][$row['mes']] = floatval($row['importe']);

			$totales[$anio][$row['mes']] += floatval($row['importe']);
		}

		if (count(array_filter($datos[$index]['series'][$anio]['importes'])) > 0)
		{
			$datos[$index]['series'][$anio]['total'] = array_sum($datos[$index]['series'][$anio]['importes']);
			$datos[$index]['series'][$anio]['promedio'] = $datos[$index]['series'][$anio]['total'] / count(array_filter($datos[$index]['series'][$anio]['importes']));
		}
		else
		{
			unset($datos[$index]['series'][$anio]);
		}

		return array(
			'datos' 	=> $datos,
			'totales'	=> $totales
		);
	}

	return NULL;
}

function detalle($gasto, $anio, $mes, $num_cia, $nombre_cia, $filtro)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

	$result = $db->query("SELECT
		mg.idmovimiento_gastos AS id,
		COALESCE(poc.num_cia_aplica, mg.num_cia) AS num_cia,
		EXTRACT(MONTH FROM mg.fecha) AS mes,
		mg.fecha,
		CASE
			WHEN TRIM(c.facturas) != '' THEN
				(
					CASE
						WHEN (
							SELECT
								COUNT(id)
							FROM
								facturas_pagadas
							WHERE
								num_proveedor = c.num_proveedor
								AND cuenta = c.cuenta
								AND folio_cheque = c.folio
								AND TRIM(descripcion) != ''
						) = 1 THEN (
							SELECT
								descripcion
							FROM
								facturas_pagadas
							WHERE
								num_proveedor = c.num_proveedor
								AND cuenta = c.cuenta
								AND folio_cheque = c.folio
						)
						ELSE
							mg.concepto
					END
				)
			ELSE
				mg.concepto
		END concepto,
		mg.importe,
		CONCAT_WS(' ', c.num_proveedor, c.a_nombre) AS proveedor,
		c.facturas,
		CASE
			WHEN c.cuenta = 1 THEN
				'BANORTE'
			WHEN c.cuenta = 2 THEN
				'SANTANDER'
		END AS banco,
		c.cuenta AS clave_banco,
		c.folio,
		ec.fecha_con AS cobrado
	FROM
		movimiento_gastos mg
		LEFT JOIN cheques c ON (
			c.num_cia = mg.num_cia
			AND c.fecha = mg.fecha
			AND c.folio = mg.folio
		)
		LEFT JOIN pagos_otras_cias poc ON (
			poc.num_cia = mg.num_cia
			AND poc.folio = mg.folio
			AND poc.fecha = mg.fecha
		)
		LEFT JOIN estado_cuenta ec ON (
			ec.num_cia = c.num_cia
			AND ec.cuenta = c.cuenta
			AND ec.folio = c.folio
			AND ec.fecha = c.fecha
		)
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(poc.num_cia_aplica, mg.num_cia))
	WHERE
		COALESCE(poc.num_cia_aplica, mg.num_cia) = {$num_cia}
		AND mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
		AND mg.codgastos = {$gasto}
		" . ( !! $filtro ? "AND mg.folio IS {$_REQUEST['filtro']}" : '') . "

	UNION

	SELECT
		rg.id,
		rg.num_cia,
		rg.mes,
		CONCAT_WS('/', '01', LPAD(mes::TEXT, 2, '0'), LPAD(anio::TEXT, 2, '0'))::DATE,
		'GASTO EN RESERVA',
		rg.importe,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_companias cc USING (num_cia)
	WHERE
		rg.num_cia = {$num_cia}
		AND rg.anio = {$anio}
		AND rg.mes = {$mes}
		AND rg.codgastos = {$gasto}
		" . ($filtro == 'NULL' ? 'FALSE' : '') . "

	ORDER BY
		fecha,
		id");

	if ($result)
	{
		$info = '<table class="info-table"><tr><th colspan="8">' . $num_cia . ' ' . utf8_encode($nombre_cia) . '</th></tr>';
		$info .= '<tr><th>Fecha</th><th>Concepto</th><th>Importe</th><th>Proveedor</th><th>Facturas</th><th>Banco</th><th>Folio</th><th>Cobrado</th></tr>';

		$total = 0;

		foreach ($result as $row)
		{
			$info .= '<tr>
				<td align="center">' . $row['fecha'] . '</td>
				<td>' . utf8_encode($row['concepto']) . '</td>
				<td align="right" class="' . ($row['importe'] < 0 ? 'red' : 'blue') . '">' . number_format($row['importe'], 2) . '</td>
				<td>' . utf8_encode($row['proveedor']) . '</td>
				<td class="green">' . utf8_encode($row['facturas']) . '</td>
				<td class="orange">' . utf8_encode($row['banco']) . '</td>
				<td align="right">' . $row['folio'] . '</td>
				<td align="center" class="purple">' . $row['cobrado'] . '</td>
			</tr>';

			$total += $row['importe'];
		}

		$info .= '<tr><th align="right" colspan="2">Total</th><th align="right">' . number_format($total, 2) . '</th><th colspan="5">&nbsp;</th></tr></table>';

		return $info;
	}
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_gasto':
			$result = $db->query("SELECT
					descripcion
				FROM
					catalogo_gastos
				WHERE
					codgastos = {$_REQUEST['gasto']}");

			if ($result)
			{
				echo utf8_encode($result[0]['descripcion']);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/ReporteGastosAnualesV2Inicio.tpl');
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

			$meses = $db->query("SELECT
				mes AS value,
				nombre AS text
			FROM
				meses
			ORDER BY
				mes");

			if ($meses)
			{
				foreach ($meses as $m)
				{
					$tpl->newBlock('mes');

					$tpl->assign('value', $m['value']);
					$tpl->assign('text', utf8_encode($m['text']));

					if ($m['value'] == date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))))
					{
						$tpl->assign('selected', ' selected=""');
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consulta':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				$tpl = new TemplatePower('plantillas/bal/ReporteGastosAnualesV2Consulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);

				$tpl->assign('concepto', utf8_encode("{$datos[0]['gasto']} {$datos[0]['nombre_gasto']}"));

				$meses = $db->query("SELECT
					mes,
					abreviatura
				FROM
					meses
				ORDER BY
					mes");

				if ($meses)
				{
					foreach ($meses as $m)
					{
						if ($m['mes'] > $_REQUEST['mes'])
						{
							continue;
						}

						$tpl->newBlock('mes');

						$tpl->assign('mes', $m['abreviatura']);
					}
				}

				foreach ($datos as $row)
				{
					if (count(array_filter($row['series'][$anio]['importes'])) == 0)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

					$tpl->assign('color', $row['series'][$anio]['total'] < 0 ? ' red' : '');
					$tpl->assign('total', number_format($row['series'][$anio]['total'], 2));
					$tpl->assign('promedio', number_format($row['series'][$anio]['promedio'], 2));

					foreach ($row['series'][$anio]['importes'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						$tpl->newBlock('importe');

						$tpl->assign('color', $importe < 0 ? ' class="red"' : '');

						$info = detalle($row['gasto'], $anio, $mes, $row['num_cia'], $row['nombre_cia'], $_REQUEST['filtro']);

						$tpl->assign('importe', $importe != 0 ? '<span id="tooltip-info" data-tooltip="' . htmlentities($info) . '">' . number_format($importe, 2) . '</span>' : '&nbsp;');
					}
				}

				foreach ($totales[$anio] as $mes => $total_mes)
				{
					if ($mes > $_REQUEST['mes'])
					{
						continue;
					}

					$tpl->newBlock('total_mes');

					$tpl->assign('total_mes', $total_mes != 0 ? number_format($total_mes, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total_anio', number_format(array_sum($totales[$anio]), 2));
				$tpl->assign('_ROOT.promedio_anio', number_format(array_sum($totales[$anio]) / count(array_filter($totales[$anio])), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

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

						$this->Cell(0, 5, mb_strtoupper(utf8_decode("REPORTE DEL GASTO {$datos[0]['gasto']} {$datos[0]['nombre_gasto']}")), 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("DEL AÑO {$anio}")), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(50, 5, 'COMPAÑIA', 1, 0);

						foreach ($_meses as $mes => $nombre)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							$this->Cell(20, 5, mb_strtoupper($nombre), 1, 0, 'C');
						}

						$this->Cell(20, 5, 'TOTAL', 1, 0, 'C');
						$this->Cell(20, 5, 'PROMEDIO', 1, 0, 'C');

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

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('L', array(216, 340));

				$pdf->SetFont('ARIAL', '', 8);

				foreach ($datos as $row)
				{
					if (count(array_filter($row['series'][$anio]['importes'])) == 0)
					{
						continue;
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 50)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(50, 5, $nombre_cia, 1, 0);

					$pdf->SetFont('ARIAL', '', 8);

					foreach ($row['series'][$anio]['importes'] as $mes => $importe)
					{
						if ($mes > $_REQUEST['mes'])
						{
							continue;
						}

						if ($importe < 0)
						{
							$pdf->SetTextColor(206, 0, 0);
						}
						else
						{
							$pdf->SetTextColor(0, 0, 0);
						}

						$pdf->Cell(20, 5, $importe != 0 ? number_format($importe, 2) : '', 1, 0, 'R');
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					if ($row['series'][$anio]['total'] < 0)
					{
						$pdf->SetTextColor(206, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 0);
					}

					$pdf->Cell(20, 5, number_format($row['series'][$anio]['total'], 2), 1, 0, 'R');
					$pdf->Cell(20, 5, number_format($row['series'][$anio]['promedio'], 2), 1, 1, 'R');
				}

				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->Cell(50, 5, 'TOTALES', 1, 0, 'R');

				foreach ($totales[$anio] as $mes => $total_mes)
				{
					if ($mes > $_REQUEST['mes'])
					{
						continue;
					}

					$pdf->Cell(20, 5, $total_mes != 0 ? number_format($total_mes, 2) : '', 1, 0, 'R');
				}

				$pdf->Cell(20, 5, number_format(array_sum($totales[$anio]), 2), 1, 0, 'R');
				$pdf->Cell(20, 5, number_format(array_sum($totales[$anio]) / count(array_filter($totales[$anio])), 2), 1, 0, 'R');

				$pdf->Output('reporte-gastos-anuales.pdf', 'I');
			}

			break;

		case 'exportar':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				$data = utf8_decode('"","REPORTE DEL GASTO ' . $datos[0]['gasto'] . " " . $datos[0]['nombre_gasto'] . '"') . "\n";
				$data .= utf8_decode('"","DEL AÑO ' . $anio . '"') . "\n\n";

				$data .= utf8_decode('"#","COMPAÑIA","' . implode('","', array_map('mb_strtoupper', $_meses)) . '","TOTAL","PROMEDIO"') . "\n";

				foreach ($datos as $row)
				{
					if (count(array_filter($row['series'][$anio]['importes'])) == 0)
					{
						continue;
					}

					$data .= '"' . $row['num_cia'] . '","' . $row['nombre_cia'] . '","';

					$data .= implode('","', array_map('toNumberFormat', $row['series'][$anio]['importes'])) . '",';

					$data .= '"' . number_format($row['series'][$anio]['total'], 2) . '","' . number_format($row['series'][$anio]['promedio'], 2) . '"' . "\n";
				}

				$data .= '"","TOTALES","' . implode('","', array_map('toNumberFormat', $totales[$anio])) . '",';

				$data .= '"' . number_format(array_sum($totales[$anio]), 2) . '","' . number_format(array_sum($totales[$anio]) / count(array_filter($totales[$anio])), 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-gastos-anuales.csv');

				echo $data;
			}

			break;

		case 'graficas':
			if ($result = consulta($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anios = array_filter($_REQUEST['anio']);

				sort($anios);

				$anios = array_reverse($anios);

				$anio = $anios[0];

				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				if ( ! class_exists('pChart'))
				{
					include("includes/pChart/pData.php");
					include("includes/pChart/pChart.php");
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $datos, $anio;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(0, 5, mb_strtoupper(utf8_decode("REPORTE DEL GASTO {$datos[0]['gasto']} {$datos[0]['nombre_gasto']}")), 0, 1, 'C');
						$this->Cell(0, 5, mb_strtoupper(utf8_decode("DEL AÑO {$anio}")), 0, 1, 'C');

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

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('L', 'Letter');

				$pdf->SetFont('ARIAL', 'B', 10);

				foreach ($datos as $row)
				{
					$pdf->Cell(0, 5, "{$row['num_cia']} {$row['nombre_cia']}", 0, 0, 'C');

					$pdf->Ln(5);

					$data = new pData();
					$labels = array();

					$series = array_reverse($row['series']);

					$min = array();
					$max = array();

					foreach ($series as $serie_index => $serie)
					{
						foreach ($serie['importes'] as $mes => $importe)
						{
							if ($mes > $_REQUEST['mes'])
							{
								continue;
							}

							if ( ! isset($min[$mes]))
							{
								$min[$mes] = $importe;
							}

							if ( ! isset($max[$mes]))
							{
								$max[$mes] = $importe;
							}

							if ($importe != 0)
							{
								$data->AddPoint($importe, 'Serie' . ($serie_index + 1), substr($_meses[$mes], 0, 3));

								if ($importe <= $min[$mes])
								{
									$min[$mes] = $importe;

									$labels[$mes][0] = array(
										'serie'		=> 'Serie' . ($serie_index + 1),
										'mes'		=> substr($_meses[$mes], 0, 3),
										'importe'	=> $importe,
										'r'			=> 214,
										'g'			=> 92,
										'b'			=> 79
									);
								}

								if ($importe >= $max[$mes])
								{
									$max[$mes] = $importe;

									$labels[$mes][1] = array(
										'serie'		=> 'Serie' . ($serie_index + 1),
										'mes'		=> substr($_meses[$mes], 0, 3),
										'importe'	=> $importe,
										'r'			=> 66,
										'g'			=> 139,
										'b'			=> 202
									);
								}
							}
							else
							{
								$data->AddPoint('', 'Serie' . ($serie_index + 1), substr($_meses[$mes], 0, 3));
							}
						}

						$data->AddSerie('Serie' . ($serie_index + 1));
						$data->SetSerieName($serie['anio'], 'Serie' . ($serie_index + 1));
					}

					// foreach ($row['importes'] as $mes => $importe)
					// {
					// 	if ($mes > $_REQUEST['mes'])
					// 	{
					// 		continue;
					// 	}

					// 	if ($importe != 0)
					// 	{
					// 		$data->AddPoint($importe, 'Serie1', substr($_meses[$mes], 0, 3));

					// 		$labels[substr($_meses[$mes], 0, 3)] = $importe;
					// 	}
					// 	else
					// 	{
					// 		$data->AddPoint('', 'Serie1', substr($_meses[$mes], 0, 3));
					// 	}
					// }

					// $data->AddSerie('Serie1');
					// $data->SetSerieName("{$datos[0]['gasto']} {$datos[0]['nombre_gasto']} {$_REQUEST['anio']}", 'Serie1');

					$data->SetYAxisFormat('currency');

					$chart = new pChart(1200, 780);

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->setGraphArea(160, 10, 1110, 760);
					$chart->drawGraphArea(252, 252, 252);
					$chart->drawScale($data->GetData(), $data->GetDataDescription(), SCALE_NORMAL, 0, 0, 0, TRUE, 0, 2, $_REQUEST['tipo'] == 'barras' ? TRUE : FALSE);
					$chart->drawGrid(4, TRUE, 230, 230, 230, 255);
					$chart->setCurrency("$");

					// $chart->setColorPalette(0, 0, 0, 206);
					// $chart->drawFilledLineGraph($data->GetData(), $data->GetDataDescription(), 10, TRUE);
					// $chart->drawPlotGraph($data->GetData(), $data->GetDataDescription(), 3, 2, 255, 255, 255);
					// $chart->loadColorPalette('chartcolors/tones-5.txt');
					// $chart->drawBarGraph($data->GetData(), $data->GetDataDescription(), TRUE);
					if ($_REQUEST['tipo'] == 'barras')
					{
						$chart->drawBarGraph($data->GetData(), $data->GetDataDescription(), TRUE);
					}
					else if ($_REQUEST['tipo'] == 'lineas')
					{
						$chart->drawFilledLineGraph($data->GetData(), $data->GetDataDescription(), 10, TRUE);
						$chart->drawPlotGraph($data->GetData(), $data->GetDataDescription(), 3, 2, 255, 255, 255);
					}

					$chart->setFontProperties('fonts/tahoma.ttf', 8);

					// foreach ($labels as $mes => $importe)
					// {
					// 	$chart->setLabel($data->GetData(), $data->GetDataDescription(), 'Serie1', $mes, number_format($importe, 2));
					// }
					foreach ($labels as $labels_mes)
					{
						foreach ($labels_mes as $label)
						{
							$chart->setLabel($data->GetData(), $data->GetDataDescription(), $label['serie'], $label['mes'], number_format($label['importe'], 2), $label['r'], $label['g'], $label['b']);
						}
					}

					$chart->setFontProperties('fonts/tahoma.ttf', 10);
					$chart->drawLegend(5, 20, $data->GetDataDescription(), 255, 255, 255);

					$chart_filename = "chart-{$_REQUEST['gasto']}-{$anio}-{$row['num_cia']}.png";

					$chart->Render("tmp/{$chart_filename}");

					$pdf->Image("tmp/{$chart_filename}", NULL, NULL, /*190*/265);

					$pdf->Ln(5);
				}

				$pdf->Output('reporte-gastos-anuales-graficas.pdf', 'I');
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteGastosAnualesV2.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
