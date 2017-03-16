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
			$tpl = new TemplatePower('plantillas/cometra/CometraComprobantesInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha1', date('01/m/Y'));
			$tpl->assign('fecha2', date('d/m/Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "tsreg::DATE BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "reporte = TRUE";

			$sql = "
				SELECT
					CASE
						WHEN c.banco = 1 THEN
							'BANORTE'
						WHEN c.banco = 2 THEN
							'SANTANDER'
						ELSE
							'SIN DEFINIR'
					END
						AS banco,
					c.comprobante,
					c.tipo_comprobante,
					cc.num_cia_primaria,
					ccp.nombre
						AS nombre_cia_primaria,
					(
						SELECT
							nombre_fin
						FROM
							encargados
						WHERE
							num_cia = cc.num_cia_primaria
						ORDER BY
							id DESC
						LIMIT
							1
					)
						AS encargado,
					CASE
						WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> '' THEN
							ccp.clabe_cuenta
						WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> '' THEN
							ccp.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta_primaria,
					TRIM(regexp_replace(ccp.direccion, '\s+', ' ', 'g'))
						AS domicilio_primaria,
					ccp.cliente_cometra,
					c.num_cia,
					cc.nombre
						AS nombre_cia,
					CASE
						WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> '' THEN
							cc.clabe_cuenta
						WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> '' THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta,
					TRIM(regexp_replace(cc.direccion, '\s+', ' ', 'g'))
						AS domicilio,
					fecha - INTERVAL '1 DAY'
						AS fecha,
					CASE
						WHEN cod_mov = 2 AND es_cheque = 'TRUE' THEN
							99
						WHEN cod_mov = 13 THEN
							1
						ELSE
							cod_mov
					END
						AS cod_mov,
					concepto,
					importe,
					separar,
					total
				FROM
					cometra c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = cc.num_cia_primaria)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					c.comprobante,
					c.fecha,
					cc.num_cia_primaria,
					c.num_cia
			";

			$result = $db->query($sql);

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/cometra/CometraComprobantesConsulta.tpl');
				$tpl->prepare();

				$data = array();
				$comprobante = NULL;
				$cont = 0;

				foreach ($result as $r)
				{
					if ($comprobante != $r['comprobante'])
					{
						if ($comprobante != NULL)
						{
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array(
							'num_cia'         => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'      => $r['nombre_cia_primaria'],
							'domicilio'       => $r['domicilio_primaria'],
							'encargado'       => $r['encargado'],
							'banco'           => $r['banco'],
							'cuenta'          => $r['cuenta_primaria'],
							'cliente_cometra' => $r['cliente_cometra'],
							'comprobante'     => $comprobante,
							'tipo'            => $r['tipo_comprobante'],
							'importe'         => 0,
							'separar'         => 0,
							'total'           => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total'];
				}

				foreach ($data as $comprobante)
				{
					$tpl->newBlock('comprobante');

					$tpl->assign('comprobante', $comprobante['comprobante']);

					$tpl->assign('banco', $comprobante['banco']);

					if (count($comprobante['depositos']) > 1)
					{
						$tpl->newBlock('total');

						$tpl->assign('importe', $comprobante['importe'] != 0 ? number_format($comprobante['importe'], 2) : '&nbsp;');
						$tpl->assign('separar', $comprobante['separar'] != 0 ? number_format($comprobante['separar'], 2) : '&nbsp;');
						$tpl->assign('total', $comprobante['total'] != 0 ? number_format($comprobante['total'], 2) : '&nbsp;');
					}

					foreach ($comprobante['depositos'] as $deposito)
					{
						$tpl->newBlock('row');

						$tpl->assign('num_cia', $deposito['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($deposito['nombre_cia']));
						$tpl->assign('cuenta', $deposito['cuenta']);
						$tpl->assign('fecha', $deposito['fecha']);
						$tpl->assign('cod_mov', $deposito['cod_mov']);

						switch ($deposito['cod_mov'])
						{

							case 1:
								if (stripos($deposito['concepto'], 'COMPLEMENTO VENTA') !== FALSE)
								{
									$descripcion = 'COMPLEMENTO';
								}
								else if ($comprobante['num_cia'] <= 300)
								{
									$descripcion = 'PAN';
								}
								else if ($comprobante['num_cia'] >= 900)
								{
									$descripcion = 'ZAPATERIAS';
								}
								break;

							case 2:
								$descripcion = 'RENTA';
								break;

							case 7:
								$descripcion = 'PAGO FALT.';
								break;

							case 16:
								$descripcion = 'POLLOS';
								break;

							case 13:
								$descripcion = 'SOBRANTE';
								break;

							case 19:
								$descripcion = 'FALTANTE';
								break;

							case 48:
								$descripcion = 'FALSO';
								break;

							case 99:
								$descripcion = 'CHEQUE';
								break;

							case 21:
								$descripcion = 'CANC DEP';
								break;

							default:
								$sql = "
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = {$deposito['cod_mov']}

									UNION

									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = {$deposito['cod_mov']}

									GROUP BY
										descripcion
									LIMIT
										1
								";
								$tmp = $db->query($sql);

								$descripcion = $tmp[0]['descripcion'];
						}

						$tpl->assign('descripcion', utf8_encode($descripcion));
						$tpl->assign('concepto', $deposito['concepto'] != '' ? utf8_encode($deposito['concepto']) : '&nbsp;');
						$tpl->assign('importe', $deposito['importe'] != 0 ? number_format($deposito['importe'], 2) : '&nbsp;');
						$tpl->assign('separar', $deposito['separar'] != 0 ? number_format($deposito['separar'], 2) : '&nbsp;');
						$tpl->assign('total', $deposito['total'] != 0 ? number_format($deposito['total'], 2) : '&nbsp;');
					}
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'comprobantes':
			$condiciones = array();

			$condiciones[] = "tsreg::DATE BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "reporte = TRUE";

			$condiciones[] = "c.comprobante IN (" . implode(', ', $_REQUEST['comprobantes']) . ")";

			$sql = "
				SELECT
					c.banco,
					CASE
						WHEN c.banco = 1 THEN
							'BANORTE'
						WHEN c.banco = 2 THEN
							'SANTANDER'
						ELSE
							'SIN DEFINIR'
					END
						AS nombre_banco,
					c.comprobante,
					c.tipo_comprobante,
					cc.num_cia_primaria,
					ccp.nombre
						AS nombre_cia_primaria,
					(
						SELECT
							nombre_fin
						FROM
							encargados
						WHERE
							num_cia = cc.num_cia_primaria
						ORDER BY
							id DESC
						LIMIT
							1
					)
						AS encargado,
					CASE
						WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> '' THEN
							ccp.clabe_cuenta
						WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> '' THEN
							ccp.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta_primaria,
					TRIM(regexp_replace(ccp.direccion, '\s+', ' ', 'g'))
						AS domicilio_primaria,
					ccp.cliente_cometra,
					c.num_cia,
					cc.nombre
						AS nombre_cia,
					CASE
						WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> '' THEN
							cc.clabe_cuenta
						WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> '' THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta,
					TRIM(regexp_replace(cc.direccion, '\s+', ' ', 'g'))
						AS domicilio,
					fecha - INTERVAL '1 DAY'
						AS fecha,
					CASE
						WHEN cod_mov = 2 AND es_cheque = 'TRUE' THEN
							99
						WHEN cod_mov = 13 THEN
							1
						ELSE
							cod_mov
					END
						AS cod_mov,
					concepto,
					importe,
					separar,
					total,
					font,
					signature_cia,
					signature_cometra,
					sep,
					mach,
					sellos,
					hora,
					valores,
					valores_x,
					valores_y
				FROM
					cometra c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN cometra_comprobantes_generados ccg
						ON (ccg.num_cia = cc.num_cia_primaria AND ccg.banco = c.banco AND ccg.comprobante = c.comprobante)
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = cc.num_cia_primaria)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					c.comprobante,
					c.fecha,
					cc.num_cia_primaria,
					c.num_cia
			";

			$result = $db->query($sql);

			if ($result)
			{
				$data = array();
				$comprobante = NULL;
				$cont = 0;

				foreach ($result as $r)
				{
					if ($comprobante != $r['comprobante'])
					{
						if ($comprobante != NULL)
						{
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array(
							'num_cia'			=> $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'		=> $r['nombre_cia_primaria'],
							'domicilio'			=> $r['domicilio_primaria'],
							'encargado'			=> $r['encargado'],
							'banco'				=> $r['banco'],
							'nombre_banco'		=> $r['nombre_banco'],
							'cuenta'			=> $r['cuenta_primaria'],
							'cliente_cometra'	=> $r['cliente_cometra'],
							'comprobante'		=> $comprobante,
							'tipo'				=> $r['tipo_comprobante'],
							'importe'			=> 0,
							'separar'			=> 0,
							'total'				=> 0,
							'font'				=> $r['font'],
							'signature_cia'		=> $r['signature_cia'],
							'signature_cometra'	=> $r['signature_cometra'],
							'sep'				=> $r['sep'],
							'mach'				=> $r['mach'],
							'sellos'			=> $r['sellos'],
							'hora'				=> $r['hora'],
							'valores'			=> $r['valores'],
							'valores_x'			=> $r['valores_x'],
							'valores_y'			=> $r['valores_y']
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total'];
				}

				shuffle($data);

				include_once('includes/fpdf/fpdf.php');
				include_once('includes/cheques.inc.php');

				class PDF extends FPDF
				{
					function Header()
					{
						$this->Image('imagenes/ficha_cometra.jpg', 0, 0, 158);
					}
				}

				$pagew = 158;
				$pageh = 198;

				$pdf = new PDF('P', 'mm', array($pagew, $pageh));

				$pdf->AddFont('font1', '', 'font1.php');
				$pdf->AddFont('font2', '', 'font2.php');
				$pdf->AddFont('font3', '', 'font3.php');
				$pdf->AddFont('font4', '', 'font4.php');
				$pdf->AddFont('font5', '', 'font5.php');
				$pdf->AddFont('font6', '', 'font6.php');

				$pdf->SetDisplayMode('fullpage', 'single');

				$pdf->SetMargins(0, 0, 0);

				$separador = array('-', '/');

				foreach ($data as $d)
				{
					if ($d['font'] > 0)
					{
						$font = $d['font'];
						$signature_cia = $d['signature_cia'];
						$signature_cometra = $d['signature_cometra'];
						$sep = $d['sep'];
						$mach = $d['mach'];
						$sellos = $d['sellos'];
						$hora = $d['hora'];
						$valores = $d['valores'];
						$valores_x = $d['valores_x'];
						$valores_y = $d['valores_y'];
					}
					else
					{
						$font = mt_rand(1, 6);
						$signature_cia = mt_rand(1, 62);
						$signature_cometra = mt_rand(1, 40);
						$sep = mt_rand(0, 1);
						$mach = mt_rand(1, 9999);
						$sellos = mt_rand(1, 99999999);
						$hora = mt_rand(18, 20) . ':' . str_pad(mt_rand(0, 9) * 5, 2, '0', STR_PAD_LEFT);
						$valores = mt_rand(50001, 99999);
						$valores_x = mt_rand(0, 10);
						$valores_y = mt_rand(0, 5);

						$db->query("INSERT INTO cometra_comprobantes_generados (num_cia, banco, comprobante, font, signature_cia, signature_cometra, sep, mach, sellos, hora, valores, valores_x, valores_y, idins) VALUES ({$d['num_cia']}, {$d['banco']}, {$d['comprobante']}, {$font}, {$signature_cia}, {$signature_cometra}, {$sep}, {$mach}, {$sellos}, '{$hora}', {$valores}, {$valores_x}, {$valores_y}, {$_SESSION['iduser']})");
					}

					$piezas = explode('/', $d['fecha']);

					//$paquetes = ceil($d['total'] / 2000);

					$pdf->AddPage('P', array($pagew, $pageh));

					$pdf->SetFont('Arial', 'B', 16);
					$pdf->SetTextColor(164, 0, 0);

					$pdf->Text(115, 16, $d['comprobante']);

					$pdf->SetFont('Arial', '', 10);
					$pdf->SetTextColor(0, 0, 0);

					//$pdf->Text(20, 7, 'PAQUETES:_______________');
					//$pdf->Text(55, 6.8, $paquetes);
					$pdf->Text(11, 26, date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(97, 26, 'X');
					$pdf->Text(14, 35, str_pad($d['cliente_cometra'], 8, '0'));
					$pdf->Text(73, 35, 'X');
					$pdf->Text(20, 43, substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64));
					$pdf->Text(16, 52, substr($d['domicilio'], 0, 66));
					$pdf->Text(20, 60, '1');
					$pdf->Text(58, 60, 'X');
					$pdf->Text(121, 60, number_format($d['total'], 2));
					$pdf->Text(16, 69, substr(num2string($d['total']), 0, 66));
					$pdf->Text(10, 80, number_format($d['total'], 2));
					$pdf->Text(115, 80, number_format($d['total'], 2));
					$pdf->Text(20, 91, 'UNO');
					$pdf->Text(125, 91, 'UNO');
					$pdf->Text(20, 98, $d['banco'] . ' CAJA GENERAL');
					$pdf->Text(20, 105, 'CALLE IXNAHUALTONGO NO.129, COL. SAN LORENZO BOTURINI,');
					$pdf->Text(20, 109, 'DEL. VENUSTIANO CARRANZA, CP.15820, MEXICO, D.F.');
					$pdf->Text(20, 116, str_pad($mach, 4, '0', STR_PAD_LEFT));
					$pdf->Text(55, 116, str_pad($sellos, 4, '0', STR_PAD_LEFT));
					$pdf->Text(12, 141, date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(12, 158, $d['encargado']);

					$pdf->Image('imagenes/firmas/firma' . $signature_cia . '.jpg', 53, 134, 25, 20);
					$pdf->SetFont('font' . $font, '', 14);
					$pdf->Image('imagenes/firmas/cometra' . $signature_cometra . '.jpg', 137, 134, 20, 20);
					$pdf->Text(95, 141, date('d' . $sep . 'm' . $sep . 'y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(95, 148, $hora);
					$pdf->Text(130 + $valores_x, 138 + $valores_y, $valores);
				}

				$file_name = 'comprobantes-cometra-' . date('YmdHis') . '.pdf';

				$pdf->Output("cometra/{$file_name}", 'F');

				echo $file_name;
			}

			break;

		case 'imprimir':
			$condiciones = array();

			$condiciones[] = "tsreg::DATE BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";

			$condiciones[] = "reporte = TRUE";

			$condiciones[] = "c.comprobante IN (" . implode(', ', $_REQUEST['comprobantes']) . ")";

			$sql = "
				SELECT
					c.banco,
					CASE
						WHEN c.banco = 1 THEN
							'BANORTE'
						WHEN c.banco = 2 THEN
							'SANTANDER'
						ELSE
							'SIN DEFINIR'
					END
						AS nombre_banco,
					c.comprobante,
					c.tipo_comprobante,
					cc.num_cia_primaria,
					ccp.nombre
						AS nombre_cia_primaria,
					(
						SELECT
							nombre_fin
						FROM
							encargados
						WHERE
							num_cia = cc.num_cia_primaria
						ORDER BY
							id DESC
						LIMIT
							1
					)
						AS encargado,
					CASE
						WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> '' THEN
							ccp.clabe_cuenta
						WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> '' THEN
							ccp.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta_primaria,
					TRIM(regexp_replace(ccp.direccion, '\s+', ' ', 'g'))
						AS domicilio_primaria,
					ccp.cliente_cometra,
					c.num_cia,
					cc.nombre
						AS nombre_cia,
					CASE
						WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> '' THEN
							cc.clabe_cuenta
						WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> '' THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta,
					TRIM(regexp_replace(cc.direccion, '\s+', ' ', 'g'))
						AS domicilio,
					fecha - INTERVAL '1 DAY'
						AS fecha,
					CASE
						WHEN cod_mov = 2 AND es_cheque = 'TRUE' THEN
							99
						WHEN cod_mov = 13 THEN
							1
						ELSE
							cod_mov
					END
						AS cod_mov,
					concepto,
					importe,
					separar,
					total,
					font,
					signature_cia,
					signature_cometra,
					sep,
					mach,
					sellos,
					hora,
					valores,
					valores_x,
					valores_y
				FROM
					cometra c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN cometra_comprobantes_generados ccg
						ON (ccg.num_cia = cc.num_cia_primaria AND ccg.banco = c.banco AND ccg.comprobante = c.comprobante)
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = cc.num_cia_primaria)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					c.comprobante,
					c.fecha,
					cc.num_cia_primaria,
					c.num_cia
			";

			$result = $db->query($sql);

			if ($result)
			{
				$data = array();
				$comprobante = NULL;
				$cont = 0;

				foreach ($result as $r)
				{
					if ($comprobante != $r['comprobante'])
					{
						if ($comprobante != NULL)
						{
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array (
							'num_cia'         => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'      => $r['nombre_cia_primaria'],
							'domicilio'       => $r['domicilio_primaria'],
							'banco'           => $r['banco'],
							'cuenta'          => $r['cuenta_primaria'],
							'cliente_cometra' => $r['cliente_cometra'],
							'comprobante'     => $comprobante,
							'tipo'            => $r['tipo_comprobante'],
							'importe'         => 0,
							'separar'         => 0,
							'total'           => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total'];
				}

				shuffle($data);

				include_once('includes/cheques.inc.php');

				$string = '';

				foreach ($data as $d)
				{
					$piezas = explode('/', $d['fecha']);

					$string .= str_pad('', 4, "\n");
					$string .= str_pad('', 1, ' ') . date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10)));
					$string .= str_pad('', 33, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 3, ' ') . str_pad($d['cliente_cometra'], 8, '0') . str_pad('', 20, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 6, ' ') . substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64);
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . substr($d['domicilio'], 0, 66);
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 5, ' ') . '1' . str_pad('', 17, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . substr(num2string($d['total']), 0, 66);
					$string .= str_pad('', 3, "\n");
					$string .= number_format($d['total'], 2);
					$string .= str_pad('', 4, "\n");
					$string .= str_pad('', 4, ' ') . $d['banco'] . ' CAJA GENERAL';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . 'CALLE IXNAHUALTONGO NO.129, COL. SAN LORENZO BOTURINI,';
					$string .= str_pad('', 1, "\n");
					$string .= str_pad('', 4, ' ') . 'DEL. VENUSTIANO CARRANZA, CP.15820, MEXICO, D.F.';
					$string .= str_pad('', 27, "\n");
				}

				shell_exec("chmod ugo=rwx pcl");

				$fp = fopen('pcl/ComprobantesCometra.txt', 'w');

				fwrite($fp, $string);

				fclose($fp);

				shell_exec('lpr -l -P cometra pcl/ComprobantesCometra.txt');

				shell_exec("chmod ugo=r pcl");
			}

			break;

		case 'reporte':
			$db->query("UPDATE cheques SET acuenta = FALSE WHERE acuenta IS NULL");

			$fecha1 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n') - 1, 1, date('Y')) : mktime(0, 0, 0, date('n'), 1, date('Y')));
			$fecha2 = date('d/m/Y', date('j') <= 6 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('j'), date('Y')));

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha2));

			$condiciones = array();

			$condiciones[] = "cc.num_cia_saldos < 900";

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
					$condiciones[] = 'cc.num_cia_saldos IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 's.cuenta = ' . $_REQUEST['banco'];
			}


			$sql = "
				SELECT
					cc.num_cia_saldos
						AS num_cia,
					ccs.nombre_corto
						AS nombre_cia,
					SUM(s.saldo_libros) + COALESCE((
						SELECT
							SUM(ecsl.importe)
						FROM
							estado_cuenta ecsl
						LEFT JOIN cheques csl
							USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl
							USING (num_cia)
						WHERE
							ccsl.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecsl.fecha_con IS NULL
							AND ecsl.tipo_mov = TRUE
							AND ecsl.cod_mov IN (5, 41)
							AND csl.acuenta = TRUE
					), 0)
						AS saldo_libros,
					SUM(s.saldo_bancos)
						AS saldo_bancos,
					COALESCE((
						SELECT
							SUM(ecpnc.importe)
						FROM
							estado_cuenta ecpnc
							LEFT JOIN cheques cpnc
								USING (num_cia, folio, cuenta, fecha)
							LEFT JOIN catalogo_companias ccpnc
								USING (num_cia)
						WHERE
							ccpnc.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecpnc.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecpnc.fecha_con IS NULL
							AND ecpnc.tipo_mov = TRUE
							AND ecpnc.cod_mov IN (5, 41)
							AND cpnc.acuenta = FALSE
					), 0)
						AS pagos_no_cobrados,
					COALESCE((
						SELECT
							SUM(total)
						FROM
							pasivo_proveedores ppsp
							LEFT JOIN catalogo_companias ccsp
								USING (num_cia)
						WHERE
							ccsp.num_cia_saldos = cc.num_cia_saldos
							AND ppsp.total > 0
					), 0)
						AS saldo_proveedores,
					SUM(s.saldo_libros) + COALESCE((
						SELECT
							SUM(ecsl.importe)
						FROM
							estado_cuenta ecsl
						LEFT JOIN cheques csl
							USING (num_cia, cuenta, folio, fecha)
						LEFT JOIN catalogo_companias ccsl
							USING (num_cia)
						WHERE
							ccsl.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecsl.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecsl.fecha_con IS NULL
							AND ecsl.tipo_mov = TRUE
							AND ecsl.cod_mov IN (5, 41)
							AND csl.acuenta = TRUE
					), 0) - COALESCE((
						SELECT
							SUM(total)
						FROM
							pasivo_proveedores ppsp
							LEFT JOIN catalogo_companias ccsp
								USING (num_cia)
						WHERE
							ccsp.num_cia_saldos = cc.num_cia_saldos
							AND ppsp.total > 0
					), 0)
						AS dif_libros_proveedores,
					COALESCE((
						SELECT
							SUM(inventario)
						FROM
							(
								SELECT
									inv_act
										AS inventario
								FROM
									balances_pan bpinv
									LEFT JOIN catalogo_companias ccbpinv
										USING (num_cia)
								WHERE
									ccbpinv.num_cia_saldos = cc.num_cia_saldos
									AND fecha = (SELECT MAX(fecha) FROM balances_pan)

								UNION

								SELECT
									inv_act
								FROM
									balances_ros brinv
									LEFT JOIN catalogo_companias ccbrinv
										USING (num_cia)
								WHERE
									ccbrinv.num_cia_saldos = cc.num_cia_saldos
									AND fecha = (SELECT MAX(fecha) FROM balances_ros)
							) result
					), 0)
						AS inventario_inicial,
					COALESCE((
						SELECT
							SUM(monto)
						FROM
							perdidas
							LEFT JOIN catalogo_companias ccper
								USING (num_cia)
							WHERE
								ccper.num_cia_saldos = cc.num_cia_saldos
					), 0)
						AS perdidas,
					COALESCE((
						SELECT
							SUM(importe)
						FROM
							estado_cuenta ecdev
							LEFT JOIN catalogo_companias ccdev
								USING (num_cia)
						WHERE
							ccdev.num_cia_saldos = cc.num_cia_saldos
							" . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? "AND ecdev.cuenta = {$_REQUEST['banco']}" : '') . "
							AND ecdev.cod_mov = 18
							AND ecdev.fecha BETWEEN '01/01/{$anio}' AND '{$fecha2}'
					), 0)
						AS devoluciones_iva
				FROM
					saldos s
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_saldos)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				GROUP BY
					cc.num_cia_saldos,
					ccs.nombre_corto
				ORDER BY
					cc.num_cia_saldos
			";

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

						$this->Cell(0, 4, 'SALDOS CONTABLES', 0, 1, 'C');
						$this->Cell(0, 4, isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : 'BANCOS CONSOLIDADOS', 'B', 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(61, 4, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('BANCOS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('LIBROS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('NO COBRADOS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('PROVEEDORES'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DIFERENCIA'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('INVENTARIO'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('PERDIDAS'), 1, 0, 'C');
						$this->Cell(26, 4, utf8_decode('DEV. DE IVA'), 1, 0, 'C');

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

				$total = array(
					'saldo_bancos'				=> 0,
					'saldo_libros'				=> 0,
					'pagos_no_cobrados'			=> 0,
					'saldo_proveedores'			=> 0,
					'dif_libros_proveedores'	=> 0,
					'inventario_inicial'		=> 0,
					'perdidas'					=> 0,
					'devoluciones_iva'			=> 0
				);

				$rows = 0;

				foreach ($query as $row)
				{
					$pdf->SetFont('ARIAL', '', 10);

					$pdf->SetTextColor(0, 0, 0);
					$pdf->Cell(61, 4, utf8_decode("{$row['num_cia']} {$row['nombre_cia']}"), 1, 0);

					if ($row['saldo_bancos'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_bancos'] != 0 ? number_format($row['saldo_bancos'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', 'B', 10);

					if ($row['saldo_libros'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_libros'] != 0 ? number_format($row['saldo_libros'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', '', 10);

					if ($row['pagos_no_cobrados'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['pagos_no_cobrados'] != 0 ? number_format($row['pagos_no_cobrados'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', 'B', 10);

					if ($row['saldo_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['saldo_proveedores'] != 0 ? number_format($row['saldo_proveedores'], 2) : '', 1, 0, 'R');

					$pdf->SetFont('ARIAL', '', 10);

					if ($row['dif_libros_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['dif_libros_proveedores'] != 0 ? number_format($row['dif_libros_proveedores'], 2) : '', 1, 0, 'R');

					if ($row['inventario_inicial'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['inventario_inicial'] != 0 ? number_format($row['inventario_inicial'], 2) : '', 1, 0, 'R');

					if ($row['perdidas'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['perdidas'] != 0 ? number_format($row['perdidas'], 2) : '', 1, 0, 'R');

					if ($row['devoluciones_iva'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $row['devoluciones_iva'] != 0 ? number_format($row['devoluciones_iva'], 2) : '', 1, 0, 'R');

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

					$total['saldo_bancos'] += $row['saldo_bancos'];
					$total['saldo_libros'] += $row['saldo_libros'];
					$total['pagos_no_cobrados'] += $row['pagos_no_cobrados'];
					$total['saldo_proveedores'] += $row['saldo_proveedores'];
					$total['dif_libros_proveedores'] += $row['dif_libros_proveedores'];
					$total['inventario_inicial'] += $row['inventario_inicial'];
					$total['perdidas'] += $row['perdidas'];
					$total['devoluciones_iva'] += $row['devoluciones_iva'];
				}

				if (count($query) > 1)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->SetTextColor(0, 0, 0);
					$pdf->Cell(61, 4, utf8_decode('TOTALES'), 1, 0, 'R');

					if ($total['saldo_bancos'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['saldo_bancos'] != 0 ? number_format($total['saldo_bancos'], 2) : '', 1, 0, 'R');

					if ($total['saldo_libros'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['saldo_libros'] != 0 ? number_format($total['saldo_libros'], 2) : '', 1, 0, 'R');

					if ($total['pagos_no_cobrados'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['pagos_no_cobrados'] != 0 ? number_format($total['pagos_no_cobrados'], 2) : '', 1, 0, 'R');

					if ($total['saldo_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}
					$pdf->Cell(26, 4, $total['saldo_proveedores'] != 0 ? number_format($total['saldo_proveedores'], 2) : '', 1, 0, 'R');

					if ($total['dif_libros_proveedores'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['dif_libros_proveedores'] != 0 ? number_format($total['dif_libros_proveedores'], 2) : '', 1, 0, 'R');

					if ($total['inventario_inicial'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['inventario_inicial'] != 0 ? number_format($total['inventario_inicial'], 2) : '', 1, 0, 'R');

					if ($total['perdidas'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['perdidas'] != 0 ? number_format($total['perdidas'], 2) : '', 1, 0, 'R');

					if ($total['devoluciones_iva'] <= 0)
					{
						$pdf->SetTextColor(204, 0, 0);
					}
					else
					{
						$pdf->SetTextColor(0, 0, 204);
					}

					$pdf->Cell(26, 4, $total['devoluciones_iva'] != 0 ? number_format($total['devoluciones_iva'], 2) : '', 1, 0, 'R');
				}

				$pdf->Output('ReporteNomina.pdf', 'I');
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/CometraComprobantes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
