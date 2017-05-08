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
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_cia_fac':
			$result = $db->query("SELECT
				num_cia,
				nombre_corto AS nombre_cia
			FROM
				catalogo_companias
			WHERE
				num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
				AND num_cia = {$_REQUEST['num_cia']}");

			if ( ! $result)
			{
				return false;
			}

			echo json_encode(array(
				'num_cia'		=> intval($result[0]['num_cia']),
				'nombre_cia'	=> utf8_encode($result[0]['nombre_cia'])
			));

			break;

		case 'buscar_factura':
			$sql = "SELECT
				fe.id,
				fe.num_cia,
				fes.serie,
				fe.consecutivo,
				fe.total
			FROM
				facturas_electronicas fe
				LEFT JOIN facturas_electronicas_series fes ON (
					fes.num_cia = fe.num_cia
					AND fes.tipo_serie = fe.tipo_serie
					AND fe.consecutivo BETWEEN fes.folio_inicial AND fes.folio_final
				)
			WHERE
				fe.num_cia = {$_REQUEST['num_cia']}
				AND fe.consecutivo = {$_REQUEST['num_fact']}
				AND fe.tipo_serie IN (1, 2)";

			$result = $db->query($sql);

			if ( ! $result)
			{
				return FALSE;
			}

			$row = $result[0];

			echo json_encode(array(
				'id'		=> intval($row['id']),
				'num_cia'	=> intval($row['num_cia']),
				'serie'		=> $row['serie'],
				'folio'		=> intval($row['consecutivo']),
				'importe'	=> floatval($row['total'])
			));

			break;

		case 'obtener_cia':
			$result = $db->query("SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				" . ($_REQUEST['banco'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . " AS cuenta_cia
			FROM
				catalogo_companias
			WHERE
				num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
				AND num_cia = {$_REQUEST['num_cia']}");

			if ( ! $result)
			{
				return false;
			}

			echo json_encode(array(
				'num_cia'		=> intval($result[0]['num_cia']),
				'nombre_cia'	=> utf8_encode($result[0]['nombre_cia']),
				'cuenta_cia'	=> $result[0]['cuenta_cia']
			));

			break;

		case 'obtener_codigos':
			$result = $db->query("SELECT
				cod_mov AS value,
				cod_mov || ' ' || descripcion AS text
			FROM
				catalogo_mov_" . ($_REQUEST['banco'] == 1 ? 'bancos' : 'santander') . "
			WHERE
				tipo_mov = {$_REQUEST['tipo']}
				AND cod_mov NOT IN (2)
			GROUP BY
				value,
				text
			ORDER BY
				value");

			if ( ! $result)
			{
				return false;
			}

			echo json_encode($result);

			break;

		case 'registrar':
			$ts = date('d/m/Y H:i:s');
			$sql = '';

			foreach ($_REQUEST['num_cia'] as $i => $num_cia)
			{
				$importe = get_val($_REQUEST['importe'][$i]);

				if ($num_cia > 0 && $_REQUEST['fecha'][$i] != '' && $_REQUEST['cod_mov'][$i] > 0 && $importe > 0)
				{
					$db->query("INSERT INTO estado_cuenta (
						num_cia,
						fecha,
						cuenta,
						tipo_mov,
						cod_mov,
						concepto,
						importe,
						iduser,
						idins,
						tsins
					) VALUES (
						{$num_cia},
						'{$_REQUEST['fecha'][$i]}',
						{$_REQUEST['banco']},
						{$_REQUEST['tipo']},
						{$_REQUEST['cod_mov'][$i]},
						CASE
							WHEN TRIM('{$_REQUEST['concepto'][$i]}') != '' THEN
								TRIM('{$_REQUEST['concepto'][$i]}')
							ELSE
								(SELECT TRIM(descripcion) FROM catalogo_mov_" . ($_REQUEST['banco'] == 1 ? 'bancos' : 'santander') . " WHERE cod_mov = {$_REQUEST['cod_mov'][$i]} LIMIT 1)
						END,
						{$importe},
						{$_SESSION['iduser']},
						{$_SESSION['iduser']},
						'{$ts}'
					);");

					if (isset($_REQUEST["fac_{$i}"]))
					{
						$result = $db->query("SELECT MAX(id) AS id FROM estado_cuenta WHERE idins = {$_SESSION['iduser']} AND tsins = '{$ts}'");

						$id = $result[0]['id'];

						$ids = array();

						foreach ($_REQUEST["fac_{$i}"] as $fac_string)
						{
							$fac = json_decode($fac_string);

							$ids[] = $fac->id;
						}

						$db->query("UPDATE facturas_electronicas SET ec_id = {$id} WHERE id IN (" . implode(', ', $ids) . ")");
					}
				}
			}

			echo json_encode(array(
				'idins'	=> $_SESSION['iduser'],
				'tsins'	=> $ts
			));

			break;

		case 'reporte':
			$sql = "SELECT
				ec.num_cia,
				cc.nombre AS nombre_cia,
				CASE
					WHEN ec.cuenta = 1 THEN
						'BANORTE'
					WHEN ec.cuenta = 2 THEN
						'SANTANDER'
				END AS banco,
				CASE
					WHEN ec.cuenta = 1 THEN
						cc.clabe_cuenta
					WHEN ec.cuenta = 2 THEN
						cc.clabe_cuenta2
				END AS cuenta,
				fecha,
				cod_mov,
				CASE
					WHEN ec.cuenta = 1 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
					WHEN ec.cuenta = 2 THEN
						(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
				END AS descripcion,
				ec.concepto,
				ec.importe,
				EXTRACT(DAY FROM tsins) AS dia_captura,
				EXTRACT(MONTH FROM tsins) AS mes_captura,
				EXTRACT(YEAR FROM tsins) AS anio_captura
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				ec.idins = {$_REQUEST['iduser']}
				AND ec.tsins = '{$_REQUEST['ts']}'
			ORDER BY
				ec.id";

			$result = $db->query($sql);

			if ($result)
			{
				if ( ! class_exists('FPDF'))
				{
					include_once('includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						global $_meses, $result;

						$this->SetMargins(5, 5, 5);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(0, 5, utf8_decode("MOVIMIENTOS BANCARIOS CAPTURADOS"), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode("AL DÍA {$result[0]['dia_captura']} DE {$_meses[$result[0]['mes_captura']]} DE {$result[0]['anio_captura']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(8, 5, '#', 1, 0, 'R');
						$this->Cell(20, 5, utf8_decode('CUENTA'), 1, 0, 'C');
						$this->Cell(50, 5, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(16, 5, utf8_decode('FECHA'), 1, 0, 'C');
						$this->Cell(45, 5, utf8_decode('CÓDIGO'), 1, 0);
						$this->Cell(45, 5, utf8_decode('CONCEPTO'), 1, 0);
						$this->Cell(22, 5, utf8_decode('IMPORTE'), 1, 1, 'R');
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

				$pdf->SetAutoPageBreak(TRUE, 10);

				$pdf->AddPage('P', 'Letter');

				$pdf->SetFont('ARIAL', '', 8);

				$total = 0;

				foreach ($result as $row)
				{
					$pdf->SetFont('ARIAL', '', 8);

					$pdf->Cell(8, 5, $row['num_cia'], 1, 0, 'R');
					$pdf->Cell(20, 5, $row['cuenta'], 1, 0, 'C');

					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 50)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(50, 5, $nombre_cia, 1, 0);

					$pdf->Cell(16, 5, $row['fecha'], 1, 0, 'C');

					$codigo = "{$row['cod_mov']} {$row['descripcion']}";

					while ($pdf->GetStringWidth($codigo) > 45)
					{
						$codigo = substr($codigo, 0, strlen($codigo) - 1);
					}

					$pdf->Cell(45, 5, $codigo, 1, 0);

					$concepto = $row['concepto'];

					while ($pdf->GetStringWidth($concepto) > 45)
					{
						$concepto = substr($concepto, 0, strlen($concepto) - 1);
					}

					$pdf->Cell(45, 5, $concepto, 1, 0);

					$pdf->Cell(22, 5, number_format($row['importe'], 2), 1, 1, 'R');

					$total += $row['importe'];
				}

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->Cell(184, 5, 'TOTAL', 1, 0, 'R');

				$pdf->Cell(22, 5, number_format($total, 2), 1, 0, 'R');

				$pdf->Output('movimientos-bancarios-capturados.pdf', 'I');
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/MovimientosBancariosCaptura.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y'));

$tpl->printToScreen();
