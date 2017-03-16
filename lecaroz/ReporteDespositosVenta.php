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
			$tpl = new TemplatePower('plantillas/ban/ReporteDespositosVentaInicio.tpl');
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

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

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
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$depositos = $db->query("SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
				cc.nombre AS nombre_cia,
				ec.cuenta AS banco,
				ec.fecha,
				ec.fecha_con AS cobrado,
				ec.cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				ec.concepto,
				ec.importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia))
			WHERE
				{$condiciones_string}
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				ec.fecha,
				ec.importe DESC");

			if ($depositos)
			{
				$tpl = new TemplatePower('plantillas/ban/ReporteDespositosVentaConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2']);

				$num_cia = NULL;

				$data = array();

				foreach ($depositos as $d)
				{
					if ($num_cia != $d['num_cia'])
					{
						$num_cia = $d['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $d['nombre_cia'],
							'dias'		=> array()
						);

						$fecha = NULL;
					}

					if ($fecha != $d['fecha'])
					{
						$fecha = $d['fecha'];

						$data[$num_cia]['dias'][$fecha] = array(
							'depositos'			=> array(),
							'facturas'			=> array(),
							'total_depositado'	=> 0,
							'total_facturado'	=> 0
						);

						$facturas = $db->query("SELECT
							fe.num_cia,
							cc.nombre AS emisor,
							fes.serie || fe.consecutivo AS folio,
							fe.nombre_cliente AS receptor,
							fe.rfc,
							fe.total AS importe
						FROM
							facturas_electronicas fe
						LEFT JOIN facturas_electronicas_series fes ON (
							fes.num_cia = fe.num_cia
							AND fes.tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN fes.folio_inicial
							AND fes.folio_final
						)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = fe.num_cia)
						WHERE
							(
								fe.num_cia = {$d['num_cia']}
								OR fe.num_cia IN (
									SELECT
										sucursal
									FROM
										porcentajes_puntos_calientes
									WHERE
										matriz = {$d['num_cia']}
								)
							)
							AND fe.fecha = '{$d['fecha']}'
							AND fe.tipo IN (1, 2, 3, 4)
							AND fe.status = 1");

						if ($facturas)
						{
							foreach ($facturas as $f)
							{
								$data[$num_cia]['dias'][$fecha]['facturas'][] = array(
									'num_cia'	=> $f['num_cia'],
									'emisor'	=> $f['emisor'],
									'folio'		=> $f['folio'],
									'receptor'	=> $f['receptor'],
									'rfc'		=> $f['rfc'],
									'importe'	=> $f['importe']
								);

								$data[$num_cia]['dias'][$fecha]['total_facturado'] += $f['importe'];
							}
						}
					}

					$data[$num_cia]['dias'][$fecha]['depositos'][] = array(
						'banco'			=> $d['banco'],
						'cobrado'		=> $d['cobrado'],
						'cod_mov'		=> $d['cod_mov'],
						'descripcion'	=> $d['descripcion'],
						'concepto'		=> $d['concepto'],
						'importe'		=> $d['importe']
					);

					$data[$num_cia]['dias'][$fecha]['total_depositado'] += $d['importe'];
				}

				$total_general_depositado = 0;
				$total_general_facturado = 0;

				foreach ($data as $num_cia => $cia)
				{
					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $cia['nombre']);

					$total_cuenta_depositado = 0;
					$total_cuenta_facturado = 0;

					foreach ($cia['dias'] as $fecha => $datos)
					{
						$tpl->newBlock('dia');

						$tpl->assign('fecha', $fecha);

						$tpl->assign('total_depositado', number_format($datos['total_depositado'], 2));
						$tpl->assign('total_facturado', number_format($datos['total_facturado'], 2));

						$diferencia = $datos['total_depositado'] - $datos['total_facturado'];

						$tpl->assign('diferencia', $diferencia != 0 ? '<span class="' . ($diferencia > 0 ? 'blue' : 'red') . '">' . number_format($diferencia, 2) . '</span>' : '&nbsp;');

						if (count($datos['depositos']) >= count($datos['facturas']))
						{
							foreach ($datos['depositos'] as $i => $deposito)
							{
								$tpl->newBlock('row');

								$tpl->assign('banco', $deposito['banco'] == 1 ? '<img src="/lecaroz/imagenes/Banorte16x16.png" />' : '<img src="/lecaroz/imagenes/Santander16x16.png" />');
								$tpl->assign('cobrado', $deposito['cobrado']);
								$tpl->assign('cod_mov', $deposito['cod_mov']);
								$tpl->assign('descripcion', $deposito['descripcion']);
								$tpl->assign('importe_deposito', number_format($deposito['importe'], 2));

								$total_cuenta_depositado += $deposito['importe'];
								$total_general_depositado += $deposito['importe'];

								if (isset($datos['facturas'][$i]))
								{
									$tpl->assign('num_cia', $datos['facturas'][$i]['num_cia']);
									$tpl->assign('emisor', $datos['facturas'][$i]['emisor']);
									$tpl->assign('folio', $datos['facturas'][$i]['folio']);
									$tpl->assign('receptor', $datos['facturas'][$i]['receptor']);
									$tpl->assign('rfc', $datos['facturas'][$i]['rfc']);
									$tpl->assign('importe_factura', number_format($datos['facturas'][$i]['importe'], 2));

									$total_cuenta_facturado += $datos['facturas'][$i]['importe'];
									$total_general_facturado += $datos['facturas'][$i]['importe'];
								}
							}
						}
						else
						{
							foreach ($datos['facturas'] as $i => $factura)
							{
								$tpl->newBlock('row');

								$tpl->assign('num_cia', $factura['num_cia']);
								$tpl->assign('emisor', $factura['emisor']);
								$tpl->assign('folio', $factura['folio']);
								$tpl->assign('receptor', $factura['receptor']);
								$tpl->assign('rfc', $factura['rfc']);
								$tpl->assign('importe_factura', number_format($factura['importe'], 2));

								$total_cuenta_facturado += $factura['importe'];
								$total_general_facturado += $factura['importe'];

								if (isset($datos['depositos'][$i]))
								{
									$tpl->assign('banco', $datos['depositos'][$i]['banco'] == 1 ? '<img src="/lecaroz/imagenes/Banorte16x16.png" />' : '<img src="/lecaroz/imagenes/Santander16x16.png" />');
									$tpl->assign('cobrado', $datos['depositos'][$i]['cobrado']);
									$tpl->assign('cod_mov', $datos['depositos'][$i]['cod_mov']);
									$tpl->assign('descripcion', $datos['depositos'][$i]['descripcion']);
									$tpl->assign('importe_deposito', number_format($datos['depositos'][$i]['importe'], 2));

									$total_cuenta_depositado += $datos['depositos'][$i]['importe'];
									$total_general_depositado += $datos['depositos'][$i]['importe'];
								}
							}
						}
					}

					$tpl->assign('cia.total_depositado', number_format($total_cuenta_depositado, 2));
					$tpl->assign('cia.total_facturado', number_format($total_cuenta_facturado, 2));
				}

				$tpl->assign('_ROOT.total_depositado', number_format($total_general_depositado, 2));
				$tpl->assign('_ROOT.total_facturado', number_format($total_general_facturado, 2));

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

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
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$depositos = $db->query("SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
				cc.nombre AS nombre_cia,
				ec.cuenta AS banco,
				ec.fecha,
				ec.fecha_con AS cobrado,
				ec.cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				ec.concepto,
				ec.importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia))
			WHERE
				{$condiciones_string}
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				ec.fecha,
				ec.importe DESC");

			if ($depositos)
			{
				$num_cia = NULL;

				$data = array();

				foreach ($depositos as $d)
				{
					if ($num_cia != $d['num_cia'])
					{
						$num_cia = $d['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $d['nombre_cia'],
							'dias'		=> array()
						);

						$fecha = NULL;
					}

					if ($fecha != $d['fecha'])
					{
						$fecha = $d['fecha'];

						$data[$num_cia]['dias'][$fecha] = array(
							'depositos'			=> array(),
							'facturas'			=> array(),
							'total_depositado'	=> 0,
							'total_facturado'	=> 0
						);

						$facturas = $db->query("SELECT
							fe.num_cia,
							cc.nombre AS emisor,
							fes.serie || fe.consecutivo AS folio,
							fe.nombre_cliente AS receptor,
							fe.rfc,
							fe.total AS importe
						FROM
							facturas_electronicas fe
						LEFT JOIN facturas_electronicas_series fes ON (
							fes.num_cia = fe.num_cia
							AND fes.tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN fes.folio_inicial
							AND fes.folio_final
						)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = fe.num_cia)
						WHERE
							(
								fe.num_cia = {$d['num_cia']}
								OR fe.num_cia IN (
									SELECT
										sucursal
									FROM
										porcentajes_puntos_calientes
									WHERE
										matriz = {$d['num_cia']}
								)
							)
							AND fe.fecha = '{$d['fecha']}'
							AND fe.tipo IN (1, 2, 3, 4)
							AND fe.status = 1");

						if ($facturas)
						{
							foreach ($facturas as $f)
							{
								$data[$num_cia]['dias'][$fecha]['facturas'][] = array(
									'num_cia'	=> $f['num_cia'],
									'emisor'	=> $f['emisor'],
									'folio'		=> $f['folio'],
									'receptor'	=> $f['receptor'],
									'rfc'		=> $f['rfc'],
									'importe'	=> $f['importe']
								);

								$data[$num_cia]['dias'][$fecha]['total_facturado'] += $f['importe'];
							}
						}
					}

					$data[$num_cia]['dias'][$fecha]['depositos'][] = array(
						'banco'			=> $d['banco'],
						'cobrado'		=> $d['cobrado'],
						'cod_mov'		=> $d['cod_mov'],
						'descripcion'	=> $d['descripcion'],
						'concepto'		=> $d['concepto'],
						'importe'		=> $d['importe']
					);

					$data[$num_cia]['dias'][$fecha]['total_depositado'] += $d['importe'];
				}

				$total_general_depositado = 0;
				$total_general_facturado = 0;

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

						$this->Cell(0, 5, utf8_decode('DEPOSITOS DE VENTA'), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("PERIODO DEL {$_REQUEST['fecha1']} AL {$_REQUEST['fecha2']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 10);

						$this->Cell(102, 5, utf8_decode('DEPOSITOS'), 1, 0, 'C');
						$this->Cell(226, 5, utf8_decode('FACTURAS'), 1, 0, 'C');

						$this->Ln();

						// $this->Cell(50, 5, utf8_decode(''), 0, 0);
						$this->Cell(14, 5, utf8_decode('BANCO'), 1, 0, 'C');
						$this->Cell(20, 5, utf8_decode('COBRADO'), 1, 0, 'C');
						$this->Cell(44, 5, utf8_decode('CÓDIGO'), 1, 0);
						$this->Cell(24, 5, utf8_decode('IMPORTE'), 1, 0, 'R');
						$this->Cell(76, 5, utf8_decode('EMISOR'), 1, 0);
						$this->Cell(20, 5, utf8_decode('FOLIO'), 1, 0, 'R');
						$this->Cell(76, 5, utf8_decode('RECEPTOR'), 1, 0);
						$this->Cell(30, 5, utf8_decode('R.F.C.'), 1, 0);
						$this->Cell(24, 5, utf8_decode('IMPORTE'), 1, 0, 'R');

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

				foreach ($data as $num_cia => $cia)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->Cell(328, 5, utf8_decode("{$num_cia} {$cia['nombre']}"), 1, 0);

					$pdf->Ln();

					$total_cuenta_depositado = 0;
					$total_cuenta_facturado = 0;

					foreach ($cia['dias'] as $fecha => $datos)
					{
						$pdf->Cell(328, 5, utf8_decode($fecha), 1, 0);

						$pdf->Ln();

						$pdf->SetFont('ARIAL', '', 10);

						if (count($datos['depositos']) >= count($datos['facturas']))
						{
							foreach ($datos['depositos'] as $i => $deposito)
							{
								$pdf->Cell(14, 5, utf8_decode(''), 1, 0);

								$x = $pdf->GetX();
								$y = $pdf->GetY();

								$pdf->Image('imagenes/' . ($deposito['banco'] == 1 ? 'Banorte16x16' : 'Santander16x16') . '.jpg', $x - 9, $y + 0.5, 4.23, 4.23);

								$pdf->Cell(20, 5, utf8_decode($deposito['cobrado']), 1, 0);

								$codigo = "{$deposito['cod_mov']} {$deposito['descripcion']}";

								while ($pdf->GetStringWidth($codigo) > 44)
								{
									$codigo = substr($codigo, 0, strlen($codigo) - 1);
								}

								$pdf->Cell(44, 5, utf8_decode($codigo), 1, 0);
								$pdf->Cell(24, 5, number_format($deposito['importe'], 2), 1, 0, 'R');

								$total_cuenta_depositado += $deposito['importe'];
								$total_general_depositado += $deposito['importe'];

								if (isset($datos['facturas'][$i]))
								{
									$emisor = "{$datos['facturas'][$i]['num_cia']} {$datos['facturas'][$i]['emisor']}";

									while ($pdf->GetStringWidth($emisor) > 76)
									{
										$emisor = substr($emisor, 0, strlen($emisor) - 1);
									}

									$pdf->Cell(76, 5, utf8_decode($emisor), 1, 0);
									$pdf->Cell(20, 5, utf8_decode($datos['facturas'][$i]['folio']), 1, 0, 'R');

									$receptor = $datos['facturas'][$i]['receptor'];

									while ($pdf->GetStringWidth($receptor) > 76)
									{
										$receptor = substr($receptor, 0, strlen($receptor) - 1);
									}

									$pdf->Cell(76, 5, utf8_decode($receptor), 1, 0);
									$pdf->Cell(30, 5, utf8_decode($datos['facturas'][$i]['rfc']), 1, 0);
									$pdf->Cell(24, 5, number_format($datos['facturas'][$i]['importe'], 2), 1, 0, 'R');

									$total_cuenta_facturado += $datos['facturas'][$i]['importe'];
									$total_general_facturado += $datos['facturas'][$i]['importe'];
								}
								else
								{
									$pdf->Cell(76, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(20, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(76, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(30, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(24, 5, utf8_decode(''), 1, 0);
								}

								$pdf->Ln();
							}
						}
						else
						{
							foreach ($datos['facturas'] as $i => $factura)
							{
								if (isset($datos['depositos'][$i]))
								{
									$pdf->Cell(14, 5, utf8_decode(''), 1, 0);

									$x = $pdf->GetX();
									$y = $pdf->GetY();

									$pdf->Image('imagenes/' . ($datos['depositos'][$i]['banco'] == 1 ? 'Banorte16x16' : 'Santander16x16') . '.jpg', $x - 9, $y + 0.5, 4.23, 4.23);

									$pdf->Cell(20, 5, utf8_decode($datos['depositos'][$i]['cobrado']), 1, 0);

									$codigo = "{$datos['depositos'][$i]['cod_mov']} {$datos['depositos'][$i]['descripcion']}";

									while ($pdf->GetStringWidth($codigo) > 44)
									{
										$codigo = substr($codigo, 0, strlen($codigo) - 1);
									}

									$pdf->Cell(44, 5, utf8_decode($codigo), 1, 0);
									$pdf->Cell(24, 5, number_format($datos['depositos'][$i]['importe'], 2), 1, 0, 'R');

									$total_cuenta_depositado += $datos['depositos'][$i]['importe'];
									$total_general_depositado += $datos['depositos'][$i]['importe'];
								}
								else
								{
									$pdf->Cell(14, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(20, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(44, 5, utf8_decode(''), 1, 0);
									$pdf->Cell(24, 5, utf8_decode(''), 1, 0);
								}

								$emisor = "{$factura['num_cia']} {$factura['emisor']}";

								while ($pdf->GetStringWidth($emisor) > 76)
								{
									$emisor = substr($emisor, 0, strlen($emisor) - 1);
								}

								$pdf->Cell(76, 5, utf8_decode($emisor), 1, 0);
								$pdf->Cell(20, 5, utf8_decode($factura['folio']), 1, 0, 'R');

								$receptor = $factura['receptor'];

								while ($pdf->GetStringWidth($receptor) > 76)
								{
									$receptor = substr($receptor, 0, strlen($receptor) - 1);
								}

								$pdf->Cell(76, 5, utf8_decode($receptor), 1, 0);
								$pdf->Cell(30, 5, utf8_decode($factura['rfc']), 1, 0);
								$pdf->Cell(24, 5, number_format($factura['importe'], 2), 1, 0, 'R');

								$total_cuenta_facturado += $factura['importe'];
								$total_general_facturado += $factura['importe'];

								$pdf->Ln();
							}
						}

						$pdf->SetFont('ARIAL', 'B', 10);

						$pdf->Cell(78, 5, utf8_decode('TOTAL DEPOSITADO'), 1, 0, 'R');
						$pdf->Cell(24, 5, number_format($datos['total_depositado'], 2), 1, 0, 'R');
						$pdf->Cell(202, 5, utf8_decode('TOTAL FACTURADO'), 1, 0, 'R');
						$pdf->Cell(24, 5, number_format($datos['total_facturado'], 2), 1, 0, 'R');

						$pdf->Ln();
					}

					$pdf->Cell(78, 5, utf8_decode('TOTAL DEPOSITADO EN CUENTA'), 1, 0, 'R');
					$pdf->Cell(24, 5, number_format($total_cuenta_depositado, 2), 1, 0, 'R');
					$pdf->Cell(202, 5, utf8_decode('TOTAL FACTURADO EN CUENTA'), 1, 0, 'R');
					$pdf->Cell(24, 5, number_format($total_cuenta_facturado, 2), 1, 0, 'R');

					$pdf->Ln();
				}

				$pdf->Cell(78, 5, utf8_decode('TOTAL GENERAL DEPOSITADO'), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($total_general_depositado, 2), 1, 0, 'R');
				$pdf->Cell(202, 5, utf8_decode('TOTAL GENERAL FACTURADO'), 1, 0, 'R');
				$pdf->Cell(24, 5, number_format($total_general_facturado, 2), 1, 0, 'R');

				$pdf->Output('reporte-depositos-venta.pdf', 'I');
			}

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = "ec.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "ec.cod_mov IN (1, 2, 16, 44, 99)";

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
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$depositos = $db->query("SELECT
				COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
				cc.nombre AS nombre_cia,
				ec.cuenta AS banco,
				ec.fecha,
				ec.fecha_con AS cobrado,
				ec.cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				ec.concepto,
				ec.importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia))
			WHERE
				{$condiciones_string}
			ORDER BY
				COALESCE(ec.num_cia_sec, ec.num_cia),
				ec.fecha,
				ec.importe DESC");

			if ($depositos)
			{
				$num_cia = NULL;

				$data = array();

				foreach ($depositos as $d)
				{
					if ($num_cia != $d['num_cia'])
					{
						$num_cia = $d['num_cia'];

						$data[$num_cia] = array(
							'nombre'	=> $d['nombre_cia'],
							'dias'		=> array()
						);

						$fecha = NULL;
					}

					if ($fecha != $d['fecha'])
					{
						$fecha = $d['fecha'];

						$data[$num_cia]['dias'][$fecha] = array(
							'depositos'			=> array(),
							'facturas'			=> array(),
							'total_depositado'	=> 0,
							'total_facturado'	=> 0
						);

						$facturas = $db->query("SELECT
							fe.num_cia,
							cc.nombre AS emisor,
							fes.serie || fe.consecutivo AS folio,
							fe.nombre_cliente AS receptor,
							fe.rfc,
							fe.total AS importe
						FROM
							facturas_electronicas fe
						LEFT JOIN facturas_electronicas_series fes ON (
							fes.num_cia = fe.num_cia
							AND fes.tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN fes.folio_inicial
							AND fes.folio_final
						)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = fe.num_cia)
						WHERE
							(
								fe.num_cia = {$d['num_cia']}
								OR fe.num_cia IN (
									SELECT
										sucursal
									FROM
										porcentajes_puntos_calientes
									WHERE
										matriz = {$d['num_cia']}
								)
							)
							AND fe.fecha = '{$d['fecha']}'
							AND fe.tipo IN (1, 2, 3, 4)
							AND fe.status = 1");

						if ($facturas)
						{
							foreach ($facturas as $f)
							{
								$data[$num_cia]['dias'][$fecha]['facturas'][] = array(
									'num_cia'	=> $f['num_cia'],
									'emisor'	=> $f['emisor'],
									'folio'		=> $f['folio'],
									'receptor'	=> $f['receptor'],
									'rfc'		=> $f['rfc'],
									'importe'	=> $f['importe']
								);

								$data[$num_cia]['dias'][$fecha]['total_facturado'] += $f['importe'];
							}
						}
					}

					$data[$num_cia]['dias'][$fecha]['depositos'][] = array(
						'banco'			=> $d['banco'],
						'cobrado'		=> $d['cobrado'],
						'cod_mov'		=> $d['cod_mov'],
						'descripcion'	=> $d['descripcion'],
						'concepto'		=> $d['concepto'],
						'importe'		=> $d['importe']
					);

					$data[$num_cia]['dias'][$fecha]['total_depositado'] += $d['importe'];
				}

				$total_general_depositado = 0;
				$total_general_facturado = 0;

				$string = '"DEPOSITOS DE VENTA"' . "\n";
				$string .= '"PERIODO DEL ' . $_REQUEST['fecha1'] . ' AL ' . $_REQUEST['fecha2'] . '"' . "\n\n";

				$string .= '"BANCO","COBRADO","CODIGO","IMPORTE","","EMISOR","FOLIO","RECEPTOR","R.F.C.","IMPORTE"' . "\n";

				foreach ($data as $num_cia => $cia)
				{
					$string .= '"' . utf8_encode("{$num_cia} {$cia['nombre']}") . '"' . "\n";

					$total_cuenta_depositado = 0;
					$total_cuenta_facturado = 0;

					foreach ($cia['dias'] as $fecha => $datos)
					{
						$string .= '"' . utf8_encode($fecha) . '"' . "\n";

						if (count($datos['depositos']) >= count($datos['facturas']))
						{
							foreach ($datos['depositos'] as $i => $deposito)
							{
								$string .= '"' . utf8_encode($deposito['banco'] == 1 ? 'BANORTE' : 'SANTANDER') . '",';
								$string .= '"' . utf8_encode($deposito['cobrado']) . '",';
								$string .= '"' . utf8_encode("{$deposito['cod_mov']} {$deposito['descripcion']}") . '",';
								$string .= '"' . number_format($deposito['importe'], 2) . '",';

								$total_cuenta_depositado += $deposito['importe'];
								$total_general_depositado += $deposito['importe'];

								if (isset($datos['facturas'][$i]))
								{
									$string .= '"",';
									$string .= '"' . utf8_encode("{$datos['facturas'][$i]['num_cia']} {$datos['facturas'][$i]['emisor']}") . '",';
									$string .= '"' . utf8_encode($datos['facturas'][$i]['folio']) . '",';
									$string .= '"' . utf8_encode($datos['facturas'][$i]['receptor']) . '",';
									$string .= '"' . utf8_encode($datos['facturas'][$i]['rfc']) . '",';
									$string .= '"' . number_format($datos['facturas'][$i]['importe'], 2) . '"';

									$total_cuenta_facturado += $datos['facturas'][$i]['importe'];
									$total_general_facturado += $datos['facturas'][$i]['importe'];
								}
								else
								{
									$string .= '"","","","","",""';
								}

								$string .= "\n";
							}
						}
						else
						{
							foreach ($datos['facturas'] as $i => $factura)
							{
								if (isset($datos['depositos'][$i]))
								{
									$string .= '"' . utf8_encode($datos['depositos'][$i]['banco'] == 1 ? 'BANORTE' : 'SANTANDER') . '",';
									$string .= '"' . utf8_encode($datos['depositos'][$i]['cobrado']) . '",';
									$string .= '"' . utf8_encode("{$datos['depositos'][$i]['cod_mov']} {$datos['depositos'][$i]['descripcion']}") . '",';
									$string .= '"' . number_format($datos['depositos'][$i]['importe'], 2) . '",';

									$total_cuenta_depositado += $datos['depositos'][$i]['importe'];
									$total_general_depositado += $datos['depositos'][$i]['importe'];
								}
								else
								{
									$string .= '"","","","",';
								}

								$string .= '"",';
								$string .= '"' . utf8_encode("{$factura['num_cia']} {$factura['emisor']}") . '",';
								$string .= '"' . utf8_encode($factura['folio']) . '",';
								$string .= '"' . utf8_encode($factura['receptor']) . '",';
								$string .= '"' . utf8_encode($factura['rfc']) . '",';
								$string .= '"' . number_format($factura['importe'], 2) . '"';

								$string .= "\n";

								$total_cuenta_facturado += $factura['importe'];
								$total_general_facturado += $factura['importe'];
							}
						}

						$string .= '"","","TOTAL DEPOSITADO","' . number_format($datos['total_depositado'], 2) . '","","","","","TOTAL FACTURADO","' . number_format($datos['total_facturado'], 2) . '"' . "\n";
					}

					$string .= '"","","TOTAL DEPOSITADO EN CUENTA","' . number_format($total_cuenta_depositado, 2) . '","","","","","TOTAL FACTURADO EN CUENTA","' . number_format($total_cuenta_facturado, 2) . '"' . "\n";
				}

				$string .= '"","","TOTAL GENERAL DEPOSITADO","' . number_format($total_general_depositado, 2) . '","","","","","TOTAL GENERAL FACTURADO","' . number_format($total_general_facturado, 2) . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-depositos-venta.csv');

				echo $string;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReporteDespositosVenta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
