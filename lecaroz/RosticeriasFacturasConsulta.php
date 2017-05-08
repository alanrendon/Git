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
			$tpl = new TemplatePower('plantillas/ros/RosticeriasFacturasConsultaInicio.tpl');
			$tpl->prepare();

			$fecha1 = date('j') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
			$fecha2 = date('j') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');

			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

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
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

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
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			{
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				{
					$condiciones[] = "f.fecha = '{$_REQUEST['fecha1']}'";
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
				{
					$condiciones[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '')
			{
				$facturas = array();
				$facturas_between = array();

				$pieces = explode(',', $_REQUEST['facturas']);

				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$facturas_between[] =  "f.num_fac BETWEEN '{$exp[0]}' AND '{$exp[1]}'";
					}
					else
					{
						$facturas[] = $piece;
					}
				}

				$partes = array();

				if (count($facturas) > 0)
				{
					$partes[] = "f.num_fac IN ('" . implode("', '", $facturas) . "')";
				}

				if (count($facturas_between) > 0)
				{
					$partes[] = implode(' OR ', $facturas_between);
				}

				if (count($partes) > 0)
				{
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}

			if ($_REQUEST['status'] > 0)
			{
				$condiciones[] = 'fp.fecha_cheque IS ' . ($_REQUEST['status'] == 1 ? 'NULL' : 'NOT NULL');

				if ($_REQUEST['status'] == 2 && $_REQUEST['pag'] > 0)
				{
					$condiciones[] = 'ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
				}
			}

			if ( ! isset($_REQUEST['pollos_facturado']) ||  ! isset($_REQUEST['pollos_contado']))
			{
				if (isset($_REQUEST['pollos_facturado']))
				{
					$condiciones[] = "f.credito > 0";
				}

				if (isset($_REQUEST['pollos_contado']))
				{
					$condiciones[] = "f.contado > 0";
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$orden_string = isset($_REQUEST['agrupar_por_rfc']) ? '(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc), fecha, num_cia, num_fact' : 'num_cia, fecha, num_fact';

			$query = $db->query("SELECT
				f.id,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fac AS num_fact,
				f.fecha,
				f.num_cia,
				cc.nombre AS nombre_cia,
				cc.rfc AS rfc_cia,
				'FACTURA ROSTICERIA' AS concepto,
				f.contado AS contado,
				f.credito AS facturado,
				f.total_fac AS total,
				fp.folio_cheque AS folio,
				fecha_cheque AS fecha_pago,
				fecha_con AS fecha_cobro,
				ch.cuenta AS banco,
				ch.cod_mov,
				ch.fecha_cancelacion
			FROM
				total_fac_ros f
				LEFT JOIN facturas_pagadas fp ON (
					fp.num_proveedor = f.num_proveedor
					AND fp.num_fact = f.num_fac
					AND fp.fecha = f.fecha
				)
				LEFT JOIN cheques ch ON (
					ch.num_cia = fp.num_cia
					AND ch.cuenta = fp.cuenta
					AND ch.folio = fp.folio_cheque
					AND ch.fecha = fp.fecha_cheque
				)
				LEFT JOIN estado_cuenta ec ON (
					ec.num_cia = fp.num_cia
					AND ec.cuenta = fp.cuenta
					AND ec.folio = fp.folio_cheque
					AND ec.fecha = fp.fecha_cheque
				)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_pro, {$orden_string}");

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/ros/RosticeriasFacturasConsultaResultado.tpl');
				$tpl->prepare();

				$num_pro = NULL;

				$g_contado = 0;
				$g_facturado = 0;
				$g_total = 0;

				foreach ($query as $row)
				{
					if ($num_pro != $row['num_pro'])
					{
						$num_pro = $row['num_pro'];

						$tpl->newBlock('pro');

						$tpl->assign('num_pro', $row['num_pro']);
						$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));

						$contado = 0;
						$facturado = 0;
						$total = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_fact', '<a id="detalle" alt="' . htmlentities(json_encode(array(
						'id'	=> get_val($row['id'])
					))) . '" class="enlace blue">' . utf8_encode($row['num_fact']) . '</a>');
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('contado', $row['contado'] != 0 ? number_format($row['contado'], 2) : '&nbsp;');
					$tpl->assign('facturado', $row['facturado'] != 0 ? number_format($row['facturado'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] != 0 ? number_format($row['total'], 2) : '&nbsp;');
					$tpl->assign('fecha_pago', $row['fecha_pago'] != '' ? $row['fecha_pago'] : '&nbsp;');
					$tpl->assign('banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['fecha_cancelacion'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha_cobro', $row['fecha_cobro'] != '' ? $row['fecha_cobro'] : '&nbsp;');

					$contado += $row['contado'];
					$facturado += $row['facturado'];
					$total += $row['total'];

					$g_contado += $row['contado'];
					$g_facturado += $row['facturado'];
					$g_total += $row['total'];

					$tpl->assign('pro.contado', number_format($contado, 2));
					$tpl->assign('pro.facturado', number_format($facturado, 2));
					$tpl->assign('pro.total', number_format($total, 2));

					$tpl->assign('_ROOT.contado', number_format($g_contado, 2));
					$tpl->assign('_ROOT.facturado', number_format($g_facturado, 2));
					$tpl->assign('_ROOT.total', number_format($g_total, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'detalle':
			$tpl = new TemplatePower('plantillas/ros/RosticeriasFacturasConsultaDetalle.tpl');
			$tpl->prepare();

			$result = $db->query("SELECT
				f.num_cia,
				cc.nombre AS nombre_cia,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fac AS num_fact,
				f.fecha
			FROM
				total_fac_ros f
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				f.id = {$_REQUEST['id']}");

			$info_fac = $result[0];

			$tpl->assign('num_cia', $info_fac['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($info_fac['nombre_cia']));
			$tpl->assign('num_pro', $info_fac['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($info_fac['nombre_pro']));
			$tpl->assign('num_fact', utf8_encode($info_fac['num_fact']));
			$tpl->assign('fecha', $info_fac['fecha']);

			$result = $db->query("SELECT
				fr.codmp,
				cmp.nombre AS nombre_mp,
				fr.cantidad,
				fr.kilos,
				fr.precio,
				fr.total
			FROM
				fact_rosticeria fr
				LEFT JOIN total_fac_ros tfr ON (
					tfr.num_cia = fr.num_cia
					AND tfr.num_proveedor = fr.num_proveedor
					AND tfr.num_fac = fr.num_fac
					AND tfr.fecha = fr.fecha_mov
				)
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
			WHERE
				(fr.num_proveedor, fr.num_fac, fr.fecha_mov) IN (
					SELECT
						num_proveedor,
						num_fac,
						fecha
					FROM
						total_fac_ros
					WHERE
						id = {$_REQUEST['id']}
				)
			ORDER BY
				fr.idfact_rosticeria");

			$total = 0;

			foreach ($result as $row)
			{
				$tpl->newBlock('row');

				$tpl->assign('codmp', $row['codmp']);
				$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
				$tpl->assign('cantidad', number_format($row['cantidad'], 2));
				$tpl->assign('kilos', number_format($row['kilos'], 2));
				$tpl->assign('precio', number_format($row['precio'], 2));
				$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

				$total += $row['total'];

				$tpl->assign('_ROOT.total', number_format($total, 2));
			}

			echo $tpl->getOutputContent();

			break;

		case 'reporte':
			$condiciones = array();

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
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

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
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			{
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				{
					$condiciones[] = "f.fecha = '{$_REQUEST['fecha1']}'";
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
				{
					$condiciones[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '')
			{
				$facturas = array();
				$facturas_between = array();

				$pieces = explode(',', $_REQUEST['facturas']);

				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$facturas_between[] =  "f.num_fac BETWEEN '{$exp[0]}' AND '{$exp[1]}'";
					}
					else
					{
						$facturas[] = $piece;
					}
				}

				$partes = array();

				if (count($facturas) > 0)
				{
					$partes[] = "f.num_fac IN ('" . implode("', '", $facturas) . "')";
				}

				if (count($facturas_between) > 0)
				{
					$partes[] = implode(' OR ', $facturas_between);
				}

				if (count($partes) > 0)
				{
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}

			if ($_REQUEST['status'] > 0)
			{
				$condiciones[] = 'fp.fecha_cheque IS ' . ($_REQUEST['status'] == 1 ? 'NULL' : 'NOT NULL');

				if ($_REQUEST['status'] == 2 && $_REQUEST['pag'] > 0)
				{
					$condiciones[] = 'ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
				}
			}

			if ( ! isset($_REQUEST['pollos_facturado']) ||  ! isset($_REQUEST['pollos_contado']))
			{
				if (isset($_REQUEST['pollos_facturado']))
				{
					$condiciones[] = "f.credito > 0";
				}

				if (isset($_REQUEST['pollos_contado']))
				{
					$condiciones[] = "f.contado > 0";
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$orden_string = isset($_REQUEST['agrupar_por_rfc']) ? '(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc), fecha, num_cia, num_fact' : 'num_cia, fecha, num_fact';

			$query = $db->query("SELECT
				f.id,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fac AS num_fact,
				f.fecha,
				f.num_cia,
				cc.nombre AS nombre_cia,
				cc.rfc AS rfc_cia,
				'FACTURA ROSTICERIA' AS concepto,
				f.contado AS contado,
				f.credito AS facturado,
				f.total_fac AS total,
				fecha_cheque AS pagado,
				CASE
					WHEN ch.cuenta = 1 THEN
						'BANORTE'
					WHEN ch.cuenta = 2 THEN
						'SANTANDER'
				END AS banco,
				fp.folio_cheque AS folio,
				fecha_con AS cobrado,
				ch.cod_mov,
				ch.fecha_cancelacion AS cancelado
			FROM
				total_fac_ros f
				LEFT JOIN facturas_pagadas fp ON (
					fp.num_proveedor = f.num_proveedor
					AND fp.num_fact = f.num_fac
					AND fp.fecha = f.fecha
				)
				LEFT JOIN cheques ch ON (
					ch.num_cia = fp.num_cia
					AND ch.cuenta = fp.cuenta
					AND ch.folio = fp.folio_cheque
					AND ch.fecha = fp.fecha_cheque
				)
				LEFT JOIN estado_cuenta ec ON (
					ec.num_cia = fp.num_cia
					AND ec.cuenta = fp.cuenta
					AND ec.folio = fp.folio_cheque
					AND ec.fecha = fp.fecha_cheque
				)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_pro, {$orden_string}");

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

						$this->Cell(0, 5, utf8_decode('CONSULTA DE COMPRAS DE ROSTICERIA'), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(22, 5, utf8_decode('FACTURA'), 1, 0);
						$this->Cell(18, 5, utf8_decode('FECHA'), 1, 0, 'C');
						$this->Cell(70, 5, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(24, 5, utf8_decode('CONTADO'), 1, 0, 'R');
						$this->Cell(24, 5, utf8_decode('FACTURADO'), 1, 0, 'R');
						$this->Cell(24, 5, utf8_decode('TOTAL'), 1, 0, 'R');
						$this->Cell(18, 5, utf8_decode('PAGADO'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('BANCO'), 1, 0, 'C');
						$this->Cell(16, 5, utf8_decode('FOLIO'), 1, 0, 'R');
						$this->Cell(18, 5, utf8_decode('COBRADO'), 1, 0, 'C');

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

				$pdf->SetFont('ARIAL', '', 8);

				$num_pro = NULL;

				$g_contado = 0;
				$g_facturado = 0;
				$g_total = 0;

				foreach ($query as $row)
				{
					if ($num_pro != $row['num_pro'])
					{
						if ($num_pro != NULL)
						{
							if ($rows >= 36)
							{
								$rows = 0;

								$pdf->AddPage('L', 'Letter');
								$pdf->SetMargins(5, 5, 5);
							}

							$pdf->SetFont('ARIAL', 'B', 8);

							$pdf->SetTextColor(0, 0, 0);

							$pdf->Cell(110, 5, utf8_decode('TOTALES'), 1, 0, 'R');

							$pdf->Cell(24, 5, number_format($contado, 2), 1, 0, 'R');
							$pdf->Cell(24, 5, number_format($facturado, 2), 1, 0, 'R');
							$pdf->Cell(24, 5, number_format($total, 2), 1, 0, 'R');
							$pdf->Cell(76, 5, '', 1, 1);

							$rows++;

							if ($rows >= 36)
							{
								$rows = 0;

								$pdf->AddPage('L', 'Letter');
								$pdf->SetMargins(5, 5, 5);
							}

							$pdf->Cell(258, 5, '', 1, 1);

							$rows++;

							if ($rows >= 36)
							{
								$rows = 0;

								$pdf->AddPage('L', 'Letter');
								$pdf->SetMargins(5, 5, 5);
							}
						}

						$pdf->SetFont('ARIAL', 'B', 8);

						$num_pro = $row['num_pro'];

						$pdf->Cell(258, 5, utf8_decode("{$num_pro} {$row['nombre_pro']}"), 1, 1);

						$contado = 0;
						$facturado = 0;
						$total = 0;

						$rows++;

						if ($rows >= 36)
						{
							$rows = 0;

							$pdf->AddPage('L', 'Letter');
							$pdf->SetMargins(5, 5, 5);
						}

						$pdf->SetFont('ARIAL', '', 8);
					}

					$pdf->SetTextColor(0, 0, 204);

					$pdf->Cell(22, 5, utf8_decode($row['num_fact']), 1, 0);

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(18, 5, utf8_decode($row['fecha']), 1, 0, 'C');

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 70)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(70, 5, utf8_decode($nombre_cia), 1, 0);

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(24, 5, $row['contado'] > 0 ? number_format($row['contado'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(0, 0, 204);

					$pdf->Cell(24, 5, $row['facturado'] > 0 ? number_format($row['facturado'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(0, 102, 51);

					$pdf->Cell(24, 5, $row['total'] > 0 ? number_format($row['total'], 2) : '', 1, 0, 'R');
					$pdf->Cell(18, 5, utf8_decode($row['pagado']), 1, 0, 'C');

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(24, 5, utf8_decode($row['banco']), 1, 0, 'C');

					if ($row['cod_mov'] == 41)
					{
						$pdf->SetTextColor(0, 102, 51);
					}
					else if ($row['cod_mov'] == 5)
					{
						$pdf->SetTextColor(255, 51, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 0);
					}

					$pdf->Cell(16, 5, utf8_decode($row['folio']), 1, 0, 'R');

					$pdf->SetTextColor(0, 0, 204);

					$pdf->Cell(18, 5, utf8_decode($row['cobrado']), 1, 1, 'C');

					$contado += $row['contado'];
					$facturado += $row['facturado'];
					$total += $row['total'];

					$g_contado += $row['contado'];
					$g_facturado += $row['facturado'];
					$g_total += $row['total'];

					if ($rows < 36)
					{
						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', 'Letter');
						$pdf->SetMargins(5, 5, 5);
					}
				}

				if ($num_pro != NULL)
				{
					if ($rows >= 36)
					{
						$rows = 0;

						$pdf->AddPage('L', 'Letter');
						$pdf->SetMargins(5, 5, 5);
					}

					$pdf->SetFont('ARIAL', 'B', 8);

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(110, 5, utf8_decode('TOTALES'), 1, 0, 'R');

					$pdf->Cell(24, 5, number_format($contado, 2), 1, 0, 'R');
					$pdf->Cell(24, 5, number_format($facturado, 2), 1, 0, 'R');
					$pdf->Cell(24, 5, number_format($total, 2), 1, 0, 'R');
					$pdf->Cell(76, 5, '', 1, 1);

					$rows++;

					if ($rows >= 36)
					{
						$rows = 0;

						$pdf->AddPage('L', 'Letter');
						$pdf->SetMargins(5, 5, 5);
					}

					$pdf->Cell(258, 5, '', 1, 1);

					$rows++;
				}

				if ($rows >= 36)
				{
					$rows = 0;

					$pdf->AddPage('L', 'Letter');
					$pdf->SetMargins(5, 5, 5);
				}

				$pdf->SetFont('ARIAL', 'B', 10);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(110, 5, utf8_decode('TOTAL GENERAL'), 1, 0, 'R');

				$pdf->Cell(24, 5, number_format($g_contado, 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($g_facturado, 2), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($g_total, 2), 1, 0, 'R');
				$pdf->Cell(76, 5, '', 1, 1);

				$pdf->Output('reporte-totales-conceptos-bancarios.pdf', 'I');
			}

			break;

		case 'exportar':
			$condiciones = array();

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
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

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
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			{
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				{
					$condiciones[] = "f.fecha = '{$_REQUEST['fecha1']}'";
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
				{
					$condiciones[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '')
			{
				$facturas = array();
				$facturas_between = array();

				$pieces = explode(',', $_REQUEST['facturas']);

				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$facturas_between[] =  "f.num_fac BETWEEN '{$exp[0]}' AND '{$exp[1]}'";
					}
					else
					{
						$facturas[] = $piece;
					}
				}

				$partes = array();

				if (count($facturas) > 0)
				{
					$partes[] = "f.num_fac IN ('" . implode("', '", $facturas) . "')";
				}

				if (count($facturas_between) > 0)
				{
					$partes[] = implode(' OR ', $facturas_between);
				}

				if (count($partes) > 0)
				{
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}

			if ($_REQUEST['status'] > 0)
			{
				$condiciones[] = 'fp.fecha_cheque IS ' . ($_REQUEST['status'] == 1 ? 'NULL' : 'NOT NULL');

				if ($_REQUEST['status'] == 2 && $_REQUEST['pag'] > 0)
				{
					$condiciones[] = 'ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
				}
			}

			if ( ! isset($_REQUEST['pollos_facturado']) ||  ! isset($_REQUEST['pollos_contado']))
			{
				if (isset($_REQUEST['pollos_facturado']))
				{
					$condiciones[] = "f.credito > 0";
				}

				if (isset($_REQUEST['pollos_contado']))
				{
					$condiciones[] = "f.contado > 0";
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$orden_string = isset($_REQUEST['agrupar_por_rfc']) ? '(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc), fecha, num_cia, num_fact' : 'num_cia, fecha, num_fact';

			$query = $db->query("SELECT
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fac AS num_fact,
				f.fecha,
				f.num_cia,
				cc.nombre AS nombre_cia,
				f.contado AS contado,
				f.credito AS facturado,
				f.total_fac AS total,
				CASE
					WHEN ch.cuenta = 1 THEN
						'BANORTE'
					WHEN ch.cuenta = 2 THEN
						'SANTANDER'
				END AS banco,
				fp.fecha_cheque AS fecha_pago,
				fp.folio_cheque AS folio,
				ec.fecha_con AS fecha_cobro,
				ch.fecha_cancelacion
			FROM
				total_fac_ros f
				LEFT JOIN facturas_pagadas fp ON (
					fp.num_proveedor = f.num_proveedor
					AND fp.num_fact = f.num_fac
					AND fp.fecha = f.fecha
				)
				LEFT JOIN cheques ch ON (
					ch.num_cia = fp.num_cia
					AND ch.cuenta = fp.cuenta
					AND ch.folio = fp.folio_cheque
					AND ch.fecha = fp.fecha_cheque
				)
				LEFT JOIN estado_cuenta ec ON (
					ec.num_cia = fp.num_cia
					AND ec.cuenta = fp.cuenta
					AND ec.folio = fp.folio_cheque
					AND ec.fecha = fp.fecha_cheque
				)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_pro, {$orden_string}");

			$data = '"","CONSULTA DE COMPRAS DE ROSTICERIA"' . "\n\n";
			$data .= '"#PRO","PROVEEDOR","FACTURA","FECHA","#CIA","COMPAÑIA","CONTADO","FACTURADO","TOTAL","PAGADO","BANCO","FOLIO","COBRADO","CANCELADO"' . "\n";

			if ($query)
			{
				foreach ($query as $row)
				{
					$data .= '"' . implode('","', $row) . '"' . "\n";
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=rosticerias-facturas-consulta.csv');

			echo $data;

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/RosticeriasFacturasConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
