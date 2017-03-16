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

function consulta_compras($params)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $params['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $params['anio'] < date('Y') ? 12 : date("n"), $params['anio'] < date('Y') ? 31 : 0, $params['anio']));

	$condiciones1 = array();
	$condiciones2 = array();
	$condiciones3 = array();

	$condiciones1[] = "mov.fecha_mov BETWEEN '{$fecha1}' AND '{$fecha2}'";
	$condiciones2[] = "mov.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
	$condiciones3[] = "mov.fecha_mov BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
			$condiciones1[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
			$condiciones2[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
			$condiciones3[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['admin']) && $params['admin'] > 0)
	{
		$condiciones1[] = "cc.idadministrador = {$params['admin']}";
		$condiciones2[] = "cc.idadministrador = {$params['admin']}";
		$condiciones3[] = "cc.idadministrador = {$params['admin']}";
	}

	if (isset($params['num_pro']) && $params['num_pro'] > 0)
	{
		$condiciones1[] = "mov.num_proveedor = {$params['num_pro']}";
		$condiciones2[] = "mov.num_proveedor = {$params['num_pro']}";
		$condiciones3[] = "mov.num_proveedor = {$params['num_pro']}";
	}

	if (isset($params['codmp']) && $params['codmp'] > 0)
	{
		$condiciones1[] = "mov.codmp = {$params['codmp']}";
		$condiciones2[] = "mov.codmp = {$params['codmp']}";
		$condiciones3[] = "mov.codmp = {$params['codmp']}";
	}
	else
	{
		$condiciones1[] = "mov.codmp IN (160, 600, 700, 573)";
		$condiciones2[] = "mov.codmp IN (160, 600, 700, 573)";
		$condiciones3[] = "mov.codmp IN (160, 600, 700, 573)";
	}

	$condiciones1_string = implode(' AND ', $condiciones1);
	$condiciones2_string = implode(' AND ', $condiciones2);
	$condiciones3_string = implode(' AND ', $condiciones3);

	$result = $db->query("SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		mov.num_proveedor AS num_pro,
		cp.nombre AS nombre_pro,
		mov.codmp,
		cmp.nombre AS nombre_mp,
		EXTRACT(YEAR FROM mov.fecha_mov) AS anio,
		EXTRACT(MONTH FROM mov.fecha_mov) AS mes,
		SUM(mov.cantidad) AS cantidad
	FROM
		fact_rosticeria mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = mov.num_proveedor)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones1_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		num_pro,
		nombre_pro,
		mov.codmp,
		nombre_mp,
		anio,
		mes

	UNION

	SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		mov.num_proveedor AS num_pro,
		cp.nombre AS nombre_pro,
		mov.codmp,
		cmp.nombre AS nombre_mp,
		EXTRACT(YEAR FROM mov.fecha) AS anio,
		EXTRACT(MONTH FROM mov.fecha) AS mes,
		SUM(mov.cantidad * mov.contenido) AS cantidad
	FROM
		entrada_mp mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = mov.num_proveedor)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones2_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		num_pro,
		nombre_pro,
		mov.codmp,
		nombre_mp,
		anio,
		mes

	UNION

	SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		CASE
			WHEN mov.num_proveedor = 289 THEN
				100000
			ELSE
				mov.num_proveedor
		END AS num_pro,
		CASE
			WHEN mov.num_proveedor = 289 THEN
				'COMPRA DIRECTA'
			ELSE
				cp.nombre
		END AS nombre_pro,
		mov.codmp,
		cmp.nombre AS nombre_mp,
		EXTRACT(YEAR FROM mov.fecha_mov) AS anio,
		EXTRACT(MONTH FROM mov.fecha_mov) AS mes,
		SUM(mov.cantidad) AS cantidad
	FROM
		compra_directa mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = mov.num_proveedor)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones3_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		num_pro,
		nombre_pro,
		mov.codmp,
		nombre_mp,
		anio,
		mes

	ORDER BY
		codmp,
		num_pro,
		num_cia,
		anio,
		mes");

	if ($result)
	{
		$datos = array();
		$totales = array_fill(1, 12, 0);

		$codmp = NULL;

		foreach ($result as $row)
		{
			if ($codmp != $row['codmp'])
			{
				$codmp = $row['codmp'];

				$datos[$codmp] = array(
					'codmp'			=> $row['codmp'],
					'nombre_mp'		=> $row['nombre_mp'],
					'proveedores'	=> array(),
					'totales'		=> array_fill(1, 12, 0)
				);

				$num_pro = NULL;
			}

			if ($num_pro != $row['num_pro'])
			{
				$num_pro = $row['num_pro'];

				$datos[$codmp]['proveedores'][$num_pro] = array(
					'num_pro'		=> $row['num_pro'],
					'nombre_pro'	=> $row['nombre_pro'],
					'cias'			=> array(),
					'totales'		=> array_fill(1, 12, 0)
				);

				$num_cia = NULL;
			}

			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia] = array(
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre_cia'],
					'movs'			=> array_fill(1, 12, 0),
					'total'			=> 0,
					'promedio'		=> 0
				);
			}

			$datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia]['movs'][$row['mes']] = floatval($row['cantidad']);

			$datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia]['total'] += floatval($row['cantidad']);
			$datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia]['promedio'] = $datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia]['total'] / count(array_filter($datos[$codmp]['proveedores'][$num_pro]['cias'][$num_cia]['movs']));

			$datos[$codmp]['proveedores'][$num_pro]['totales'][$row['mes']] += floatval($row['cantidad']);
			$datos[$codmp]['totales'][$row['mes']] += floatval($row['cantidad']);

			$totales[$row['mes']] += floatval($row['cantidad']);
		}

		return array(
			'datos' 	=> $datos,
			'totales'	=> $totales
		);
	}

	return NULL;
}

function consulta_ventas($params)
{
	global $db;

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $params['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $params['anio'] < date('Y') ? 12 : date("n"), $params['anio'] < date('Y') ? 31 : 0, $params['anio']));

	$condiciones = array();

	$condiciones[] = "mov.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

	$condiciones[] = "mov.tipo_mov = TRUE";

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

	if (isset($params['codmp']) && $params['codmp'] > 0)
	{
		$condiciones[] = "mov.codmp = {$params['codmp']}";
	}
	else
	{
		$condiciones[] = "mov.codmp IN (160, 600, 700, 573)";
	}

	$condiciones_string = implode(' AND ', $condiciones);

	$result = $db->query("SELECT
		mov.num_cia,
		cc.nombre_corto AS nombre_cia,
		mov.codmp,
		cmp.nombre AS nombre_mp,
		EXTRACT(YEAR FROM mov.fecha) AS anio,
		EXTRACT(MONTH FROM mov.fecha) AS mes,
		SUM(mov.cantidad) AS cantidad
	FROM
		mov_inv_real mov
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = mov.num_cia)
		LEFT JOIN catalogo_mat_primas cmp ON (cmp.codmp = mov.codmp)
	WHERE
		{$condiciones_string}
	GROUP BY
		mov.num_cia,
		nombre_cia,
		mov.codmp,
		nombre_mp,
		anio,
		mes
	ORDER BY
		codmp,
		num_cia,
		anio,
		mes");

	if ($result)
	{
		$datos = array();
		$totales = array_fill(1, 12, 0);

		$codmp = NULL;

		foreach ($result as $row)
		{
			if ($codmp != $row['codmp'])
			{
				$codmp = $row['codmp'];

				$datos[$codmp] = array(
					'codmp'		=> $row['codmp'],
					'nombre_mp'	=> $row['nombre_mp'],
					'cias'		=> array(),
					'totales'	=> array_fill(1, 12, 0)
				);

				$num_cia = NULL;
			}

			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$datos[$codmp]['cias'][$num_cia] = array(
					'num_cia'		=> $row['num_cia'],
					'nombre_cia'	=> $row['nombre_cia'],
					'movs'			=> array_fill(1, 12, 0),
					'total'			=> 0,
					'promedio'		=> 0
				);
			}

			$datos[$codmp]['cias'][$num_cia]['movs'][$row['mes']] = floatval($row['cantidad']);

			$datos[$codmp]['cias'][$num_cia]['total'] += floatval($row['cantidad']);
			$datos[$codmp]['cias'][$num_cia]['promedio'] = $datos[$codmp]['cias'][$num_cia]['total'] / count(array_filter($datos[$codmp]['cias'][$num_cia]['movs']));

			$datos[$codmp]['totales'][$row['mes']] += floatval($row['cantidad']);
			$datos[$codmp]['totales'][$row['mes']] += floatval($row['cantidad']);

			$totales[$row['mes']] += floatval($row['cantidad']);
		}

		return array(
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
			$tpl = new TemplatePower('plantillas/ros/CompraPolloAnualInicio.tpl');
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

			$mps = $db->query("SELECT
				codmp AS value,
				nombre AS text
			FROM
				catalogo_mat_primas
			WHERE
				codmp IN (600, 160, 700, 573, 334, 297, 363, 434, 300, 301, 302, 1182, 1183, 1093, 451, 452, 640, 644, 673, 819, 304, 821, 822, 641, 126)
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

		case 'consulta_compras':
			if ($result = consulta_compras($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anio = $_REQUEST['anio'];

				$tpl = new TemplatePower('plantillas/ros/CompraPolloAnualConsultaCompras.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);

				$meses = $db->query("SELECT
					mes,
					abreviatura
				FROM
					meses
				ORDER BY
					mes");

				foreach ($datos as $codmp => $mp)
				{
					$tpl->newBlock('mp');

					$tpl->assign('codmp', $mp['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($mp['nombre_mp']));

					$tpl->assign('total', number_format(array_sum($mp['totales']), 2));
					$tpl->assign('promedio', number_format(array_sum($mp['totales']) / count(array_filter($mp['totales'])), 2));

					foreach ($mp['proveedores'] as $num_pro => $pro)
					{
						$tpl->newBlock('pro');

						$tpl->assign('num_pro', $pro['num_pro']);
						$tpl->assign('nombre_pro', utf8_encode($pro['nombre_pro']));

						$tpl->assign('total', number_format(array_sum($pro['totales']), 2));
						$tpl->assign('promedio', number_format(array_sum($pro['totales']) / count(array_filter($pro['totales'])), 2));

						if ($meses)
						{
							foreach ($meses as $m)
							{
								$tpl->newBlock('mes');

								$tpl->assign('mes', ucfirst(strtolower($m['abreviatura'])));
							}
						}

						foreach ($pro['cias'] as $num_cia => $cia)
						{
							$tpl->newBlock('row');

							$tpl->assign('num_cia', $cia['num_cia']);
							$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));

							$tpl->assign('total', number_format($cia['total'], 2));
							$tpl->assign('promedio', number_format($cia['promedio'], 2));

							foreach ($cia['movs'] as $mes => $cantidad)
							{
								$tpl->newBlock('cantidad');

								$tpl->assign('cantidad', $cantidad != 0 ? number_format($cantidad, 2) : '&nbsp;');
							}
						}

						foreach ($pro['totales'] as $mes => $total)
						{
							$tpl->newBlock('total_pro');

							$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
						}
					}

					foreach ($mp['totales'] as $mes => $total)
					{
						$tpl->newBlock('total_mp');

						$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
					}
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->newBlock('total');

					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));
				$tpl->assign('_ROOT.promedio', number_format(array_sum($totales) / count(array_filter($totales)), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'consulta_ventas':
			if ($result = consulta_ventas($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anio = $_REQUEST['anio'];

				$tpl = new TemplatePower('plantillas/ros/CompraPolloAnualConsultaVentas.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);

				$meses = $db->query("SELECT
					mes,
					abreviatura
				FROM
					meses
				ORDER BY
					mes");

				foreach ($datos as $codmp => $mp)
				{
					$tpl->newBlock('mp');

					$tpl->assign('codmp', $mp['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($mp['nombre_mp']));

					$tpl->assign('total', number_format(array_sum($mp['totales']), 2));
					$tpl->assign('promedio', number_format(array_sum($mp['totales']) / count(array_filter($mp['totales'])), 2));

					if ($meses)
					{
						foreach ($meses as $m)
						{
							$tpl->newBlock('mes');

							$tpl->assign('mes', ucfirst(strtolower($m['abreviatura'])));
						}
					}

					foreach ($mp['cias'] as $num_cia => $cia)
					{
						$tpl->newBlock('row');

						$tpl->assign('num_cia', $cia['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));

						$tpl->assign('total', number_format($cia['total'], 2));
						$tpl->assign('promedio', number_format($cia['promedio'], 2));

						foreach ($cia['movs'] as $mes => $cantidad)
						{
							$tpl->newBlock('cantidad');

							$tpl->assign('cantidad', $cantidad != 0 ? number_format($cantidad, 2) : '&nbsp;');
						}
					}

					foreach ($mp['totales'] as $mes => $total)
					{
						$tpl->newBlock('total_mp');

						$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
					}
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->newBlock('total');

					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));
				$tpl->assign('_ROOT.promedio', number_format(array_sum($totales) / count(array_filter($totales)), 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte_compras':
			if ($result = consulta_compras($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

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

						$this->Cell(0, 5, mb_strtoupper(utf8_decode("REPORTE DE COMPRAS {$anio}")), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(50, 5, utf8_decode('COMPAÑIA'), 1, 0);

						foreach ($_meses as $mes => $nombre)
						{
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

		case 'reporte_ventas':
			if ($result = consulta_ventas($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

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

						$this->Cell(0, 5, mb_strtoupper(utf8_decode("REPORTE DE VENTAS {$anio}")), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(50, 5, utf8_decode('COMPAÑIA'), 1, 0);

						foreach ($_meses as $mes => $nombre)
						{
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

				foreach ($datos as $codmp => $mp)
				{
					$pdf->SetFont('ARIAL', 'B', 12);

					$pdf->Cell(330, 5, "{$codmp} {$mp['nombre_mp']}", 1, 1);

					foreach ($mp['cias'] as $num_cia => $cia)
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

				$pdf->Output('ventas-pollos-anual.pdf', 'I');
			}

			break;

		case 'exportar_compras':
			if ($result = consulta_compras($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anio = $_REQUEST['anio'];

				$data = utf8_decode('"","REPORTE DE COMPRAS ' . $anio . '"') . "\n\n";

				$data .= utf8_decode('"#","COMPAÑIA","' . implode('","', array_map('mb_strtoupper', $_meses)) . '","TOTAL","PROMEDIO"') . "\n";

				foreach ($datos as $codmp => $mp)
				{
					$data .= '"","' . $mp['codmp'] . ' ' . $mp['nombre_mp'] . '"' . "\n";

					foreach ($mp['proveedores'] as $num_pro => $pro)
					{
						$data .= '"","' . $pro['num_pro'] . ' ' . $pro['nombre_pro'] . '"' . "\n";

						foreach ($pro['cias'] as $num_cia => $cia)
						{
							$data .= '"' . $cia['num_cia'] . '","' . $cia['nombre_cia'] . '",';
							$data .= '"' . implode('","', array_map('toNumberFormat', $cia['movs'])) . '",';
							$data .= '"' . number_format($cia['total'], 2) . '","' . number_format($cia['promedio'], 2) . '"' . "\n";
						}

						$data .= '"","TOTALES PROVEEDOR","' . implode('","', array_map('toNumberFormat', $pro['totales'])) . '",';
						$data .= '"' . number_format(array_sum($pro['totales']), 2) . '","' . number_format(array_sum($pro['totales']) / count(array_filter($pro['totales'])), 2) . '"' . "\n\n";
					}

					$data .= '"","TOTALES PRODUCTO","' . implode('","', array_map('toNumberFormat', $mp['totales'])) . '",';
					$data .= '"' . number_format(array_sum($mp['totales']), 2) . '","' . number_format(array_sum($mp['totales']) / count(array_filter($mp['totales'])), 2) . '"' . "\n\n";
				}

				$data .= '"","TOTALES GENERALES","' . implode('","', array_map('toNumberFormat', $totales)) . '",';
				$data .= '"' . number_format(array_sum($totales), 2) . '","' . number_format(array_sum($totales) / count(array_filter($totales)), 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=compras-pollos-anual.csv');

				echo $data;
			}

			break;

		case 'exportar_ventas':
			if ($result = consulta_ventas($_REQUEST))
			{
				$datos = $result['datos'];
				$totales = $result['totales'];

				$anio = $_REQUEST['anio'];

				$data = utf8_decode('"","REPORTE DE VENTAS ' . $anio . '"') . "\n\n";

				$data .= utf8_decode('"#","COMPAÑIA","' . implode('","', array_map('mb_strtoupper', $_meses)) . '","TOTAL","PROMEDIO"') . "\n";

				foreach ($datos as $codmp => $mp)
				{
					$data .= '"","' . $mp['codmp'] . ' ' . $mp['nombre_mp'] . '"' . "\n";

					foreach ($mp['cias'] as $num_cia => $cia)
					{
						$data .= '"' . $cia['num_cia'] . '","' . $cia['nombre_cia'] . '",';
						$data .= '"' . implode('","', array_map('toNumberFormat', $cia['movs'])) . '",';
						$data .= '"' . number_format($cia['total'], 2) . '","' . number_format($cia['promedio'], 2) . '"' . "\n";
					}

					$data .= '"","TOTALES PRODUCTO","' . implode('","', array_map('toNumberFormat', $mp['totales'])) . '",';
					$data .= '"' . number_format(array_sum($mp['totales']), 2) . '","' . number_format(array_sum($mp['totales']) / count(array_filter($mp['totales'])), 2) . '"' . "\n\n";
				}

				$data .= '"","TOTALES GENERALES","' . implode('","', array_map('toNumberFormat', $totales)) . '",';
				$data .= '"' . number_format(array_sum($totales), 2) . '","' . number_format(array_sum($totales) / count(array_filter($totales)), 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=ventas-pollos-anual.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/CompraPolloAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
