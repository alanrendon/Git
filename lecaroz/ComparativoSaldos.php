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

function toUTF8Encode($value)
{
	return utf8_encode($value);
}

function toUTF8Decode($value)
{
	return utf8_decode($value);
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
			$tpl = new TemplatePower('plantillas/ban/ComparativoSaldosInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio1', date('Y') - 1);
			$tpl->assign('anio2', date('Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = 's.num_cia < 900';

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
					$condiciones[] = 's.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$query = $db->query("SELECT
				*,
				saldo_banco_1 - saldo_proveedores_1 AS diferencia_1,
				saldo_banco_2 - saldo_proveedores_2 AS diferencia_2,
				saldo_banco - saldo_proveedores AS diferencia
			FROM
				(
					SELECT
						s.num_cia,
						cc.nombre_corto AS nombre_cia,
						SUM (s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_banco_1,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_proveedores_1,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_banco_2,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_proveedores_2,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_banco,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
								AND fecha_cheque >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_proveedores,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						), 0) AS utilidad_1,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) AS reparto_1,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						), 0) AS utilidad_2,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) AS reparto_2
					FROM
						saldos s
						LEFT JOIN catalogo_companias cc USING (num_cia)
					" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
					GROUP BY
						num_cia,
						nombre_cia
					ORDER BY
						num_cia
				) resultado");

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/ban/ComparativoSaldosResultado.tpl');
				$tpl->prepare();

				$tpl->assign('anio1', $_REQUEST['anio1']);
				$tpl->assign('anio2', $_REQUEST['anio2']);

				$totales = array(
					'saldo_banco_1'			=> 0,
					'saldo_proveedores_1'	=> 0,
					'diferencia_1'			=> 0,
					'saldo_banco_2'			=> 0,
					'saldo_proveedores_2'	=> 0,
					'diferencia_2'			=> 0,
					'saldo_banco'			=> 0,
					'saldo_proveedores'		=> 0,
					'diferencia'			=> 0,
					'utilidad_1'			=> 0,
					'reparto_1'				=> 0,
					'utilidad_2'			=> 0,
					'reparto_2'				=> 0
				);

				foreach ($query as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('saldo_banco_1', $row['saldo_banco_1'] != 0 ? number_format($row['saldo_banco_1'], 2) : '&nbsp;');
					$tpl->assign('saldo_proveedores_1', $row['saldo_proveedores_1'] != 0 ? number_format($row['saldo_proveedores_1'], 2) : '&nbsp;');
					$tpl->assign('diferencia_1', $row['diferencia_1'] != 0 ? '<span class="' . ($row['diferencia_1'] > 0 ? 'blue' : 'red') . '">' . number_format($row['diferencia_1'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_banco_2', $row['saldo_banco_2'] != 0 ? number_format($row['saldo_banco_2'], 2) : '&nbsp;');
					$tpl->assign('saldo_proveedores_2', $row['saldo_proveedores_2'] != 0 ? number_format($row['saldo_proveedores_2'], 2) : '&nbsp;');
					$tpl->assign('diferencia_2', $row['diferencia_2'] != 0 ? '<span class="' . ($row['diferencia_2'] > 0 ? 'blue' : 'red') . '">' . number_format($row['diferencia_2'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('saldo_banco', $row['saldo_banco'] != 0 ? number_format($row['saldo_banco'], 2) : '&nbsp;');
					$tpl->assign('saldo_proveedores', $row['saldo_proveedores'] != 0 ? number_format($row['saldo_proveedores'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $row['diferencia'] != 0 ? '<span class="' . ($row['diferencia'] > 0 ? 'blue' : 'red') . '">' . number_format($row['diferencia'], 2) . '</span>' : '&nbsp;');
					$tpl->assign('utilidad_1', $row['utilidad_1'] != 0 ? number_format($row['utilidad_1'], 2) : '&nbsp;');
					$tpl->assign('reparto_1', $row['reparto_1'] != 0 ? number_format($row['reparto_1'], 2) : '&nbsp;');
					$tpl->assign('utilidad_2', $row['utilidad_2'] != 0 ? number_format($row['utilidad_2'], 2) : '&nbsp;');
					$tpl->assign('reparto_2', $row['reparto_2'] != 0 ? number_format($row['reparto_2'], 2) : '&nbsp;');

					foreach ($row as $key => $value)
					{
						if (in_array($key, array('num_cia', 'nombre_cia')))
						{
							continue;
						}

						$totales[$key] += $value;
					}
				}

				foreach ($totales as $key => $value)
				{
					$tpl->assign("_ROOT.{$key}", $value != 0 ? number_format($value, 2) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

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
					$condiciones[] = 's.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$query = $db->query("SELECT
				*,
				saldo_banco_1 - saldo_proveedores_1 AS diferencia_1,
				saldo_banco_2 - saldo_proveedores_2 AS diferencia_2,
				saldo_banco - saldo_proveedores AS diferencia
			FROM
				(
					SELECT
						s.num_cia,
						cc.nombre_corto AS nombre_cia,
						SUM (s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_banco_1,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_proveedores_1,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_banco_2,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_proveedores_2,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_banco,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
								AND fecha_cheque >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_proveedores,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						), 0) AS utilidad_1,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) AS reparto_1,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						), 0) AS utilidad_2,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) AS reparto_2
					FROM
						saldos s
						LEFT JOIN catalogo_companias cc USING (num_cia)
					" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
					GROUP BY
						num_cia,
						nombre_cia
					ORDER BY
						num_cia
				) resultado");

			if ($query)
			{
				$totales = array(
					'saldo_banco_1'			=> 0,
					'saldo_proveedores_1'	=> 0,
					'diferencia_1'			=> 0,
					'saldo_banco_2'			=> 0,
					'saldo_proveedores_2'	=> 0,
					'diferencia_2'			=> 0,
					'saldo_banco'			=> 0,
					'saldo_proveedores'		=> 0,
					'diferencia'			=> 0,
					'utilidad_1'			=> 0,
					'reparto_1'				=> 0,
					'utilidad_2'			=> 0,
					'reparto_2'				=> 0
				);

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

						$this->Cell(0, 5, utf8_decode("COMPARATIVO DE SALDOS {$_REQUEST['anio1']} Y {$_REQUEST['anio2']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 6);

						$this->Cell(40, 10, utf8_decode('COMPAÑIA'), 1, 0);
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(53, 19, utf8_decode('SALDO'));
						$this->Text(50, 22, utf8_decode("BANCO {$_REQUEST['anio1']}"));
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(75, 18, utf8_decode('SALDO'));
						$this->Text(70, 21, utf8_decode("PROVEEDORES"));
						$this->Text(76, 24, utf8_decode("{$_REQUEST['anio1']}"));
						$this->Cell(22, 10, utf8_decode("DIFERENCIA {$_REQUEST['anio1']}"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(118, 19, utf8_decode('SALDO'));
						$this->Text(115, 22, utf8_decode("BANCO {$_REQUEST['anio2']}"));
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(141, 18, utf8_decode('SALDO'));
						$this->Text(136, 21, utf8_decode("PROVEEDORES"));
						$this->Text(142, 24, utf8_decode("{$_REQUEST['anio2']}"));
						$this->Cell(22, 10, utf8_decode("DIFERENCIA {$_REQUEST['anio2']}"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(184, 19, utf8_decode('SALDO'));
						$this->Text(184, 22, utf8_decode('BANCO'));
						$this->Cell(22, 10, utf8_decode(''), 1, 0);
						$this->Text(206, 19, utf8_decode('SALDO'));
						$this->Text(202, 22, utf8_decode('PROVEEDORES'));
						$this->Cell(22, 10, utf8_decode("DIFERENCIA"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode("UTILIDAD {$_REQUEST['anio1']}"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode("REPARTO {$_REQUEST['anio1']}"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode("UTILIDAD {$_REQUEST['anio2']}"), 1, 0, 'C');
						$this->Cell(22, 10, utf8_decode("REPARTO {$_REQUEST['anio2']}"), 1, 0, 'C');

						$this->Ln(10);
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

				$pdf->SetAutoPageBreak(FALSE);

				$pdf->AddPage('L', array(216, 340));

				$rows = 0;

				$pdf->SetFont('ARIAL', '', 8);

				foreach ($query as $row)
				{
					$nombre_cia = "{$row['num_cia']} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 60)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(40, 5, $nombre_cia, 1, 0);

					$pdf->SetTextColor(0, 102, 0);

					$pdf->Cell(22, 5, $row['saldo_banco_1'] > 0 ? number_format($row['saldo_banco_1'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(22, 5, $row['saldo_proveedores_1'] > 0 ? number_format($row['saldo_proveedores_1'], 2) : '', 1, 0, 'R');

					if ($row['diferencia_1'] > 0)
					{
						$pdf->SetTextColor(0, 0, 204);
					}
					else
					{
						$pdf->SetTextColor(204, 0, 0);
					}

					$pdf->Cell(22, 5, $row['diferencia_1'] != 0 ? number_format($row['diferencia_1'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(0, 102, 0);

					$pdf->Cell(22, 5, $row['saldo_banco_2'] > 0 ? number_format($row['saldo_banco_2'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(22, 5, $row['saldo_proveedores_2'] > 0 ? number_format($row['saldo_proveedores_2'], 2) : '', 1, 0, 'R');

					if ($row['diferencia_2'] > 0)
					{
						$pdf->SetTextColor(0, 0, 204);
					}
					else
					{
						$pdf->SetTextColor(204, 0, 0);
					}

					$pdf->Cell(22, 5, $row['diferencia_2'] != 0 ? number_format($row['diferencia_2'], 2) : '', 1, 0, 'R');

					$pdf->Cell(22, 5, $row['saldo_banco'] > 0 ? number_format($row['saldo_banco'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(22, 5, $row['saldo_proveedores'] > 0 ? number_format($row['saldo_proveedores'], 2) : '', 1, 0, 'R');

					if ($row['diferencia'] > 0)
					{
						$pdf->SetTextColor(0, 0, 204);
					}
					else
					{
						$pdf->SetTextColor(204, 0, 0);
					}

					$pdf->Cell(22, 5, $row['diferencia'] != 0 ? number_format($row['diferencia'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(102, 51, 204);

					$pdf->Cell(22, 5, $row['utilidad_1'] != 0 ? number_format($row['utilidad_1'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(22, 5, $row['reparto_1'] != 0 ? number_format($row['reparto_1'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(102, 51, 204);

					$pdf->Cell(22, 5, $row['utilidad_2'] != 0 ? number_format($row['utilidad_2'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor(255, 51, 0);

					$pdf->Cell(22, 5, $row['reparto_2'] != 0 ? number_format($row['reparto_2'], 2) : '', 1, 1, 'R');

					foreach ($row as $key => $value)
					{
						if (in_array($key, array('num_cia', 'nombre_cia')))
						{
							continue;
						}

						$totales[$key] += $value;
					}

					if ($rows < 36)
					{
						$rows++;
					}
					else
					{
						$rows = 0;

						$pdf->AddPage('L', array(216, 340));
						$pdf->SetMargins(5, 5, 5);
					}
				}

				if ($rows < 36)
				{
					$rows++;
				}
				else
				{
					$rows = 0;

					$pdf->AddPage('L', array(216, 340));
					$pdf->SetMargins(5, 5, 5);
				}

				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetFont('ARIAL', 'B', 8);

				$pdf->Cell(40, 5, 'TOTALES', 1, 0, 'R');

				foreach ($totales as $value)
				{
					$pdf->Cell(22, 5, number_format($value, 2), 1, 0, 'R');
				}

				$pdf->Output("comparativo-saldos-{$_REQUEST['anio1']}-{$_REQUEST['anio2']}.pdf", 'I');
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
					$condiciones[] = 's.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$query = $db->query("SELECT
				num_cia,
				nombre_cia,
				saldo_banco_1,
				saldo_proveedores_1,
				saldo_banco_1 - saldo_proveedores_1 AS diferencia_1,
				saldo_banco_2,
				saldo_proveedores_2,
				saldo_banco_2 - saldo_proveedores_2 AS diferencia_2,
				saldo_banco,
				saldo_proveedores,
				saldo_banco - saldo_proveedores AS diferencia,
				utilidad_1,
				reparto_1,
				utilidad_2,
				reparto_2
			FROM
				(
					SELECT
						s.num_cia,
						cc.nombre_corto AS nombre_cia,
						SUM (s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_banco_1,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio1']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio1']}'::DATE
						), 0) AS saldo_proveedores_1,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_banco_2,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < '01-01-{$_REQUEST['anio2']}'::DATE
								AND fecha_cheque >= '01-01-{$_REQUEST['anio2']}'::DATE
						), 0) AS saldo_proveedores_2,
						SUM(s.saldo_libros) - COALESCE((
							SELECT
								SUM(
									CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									ELSE
										importe
									END
								)
							FROM
								estado_cuenta
							WHERE
								num_cia = s.num_cia
								AND fecha >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_banco,
						COALESCE((
							SELECT
								SUM(total)
							FROM
								pasivo_proveedores
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(total)
							FROM
								facturas_pagadas
							WHERE
								num_cia = s.num_cia
								AND fecha < COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
								AND fecha_cheque >= COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
								), NULL)
						), 0) AS saldo_proveedores,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio1']}
						), 0) AS utilidad_1,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio1']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio1']}
								), NULL)
						), 0) AS reparto_1,
						COALESCE((
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_pan
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						),
						(
							SELECT
								SUM(utilidad_neta)
							FROM
								balances_ros
							WHERE
								num_cia = s.num_cia
								AND anio = {$_REQUEST['anio2']}
						), 0) AS utilidad_2,
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) + COALESCE((
							SELECT
								SUM(
									CASE
										WHEN tipo_mov = TRUE THEN
											importe
										ELSE
											-importe
									END
								)
							FROM
								gastos_caja
							WHERE
								num_cia = s.num_cia
								AND fecha BETWEEN '01-01-{$_REQUEST['anio2']}' AND COALESCE((
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_pan
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), (
									SELECT
										MAX(fecha) + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
									FROM
										balances_ros
									WHERE
										num_cia = s.num_cia
										AND anio = {$_REQUEST['anio2']}
								), NULL)
						), 0) AS reparto_2
					FROM
						saldos s
						LEFT JOIN catalogo_companias cc USING (num_cia)
					" . ($condiciones_string != '' ? "WHERE {$condiciones_string}" : '') . "
					GROUP BY
						num_cia,
						nombre_cia
					ORDER BY
						num_cia
				) resultado");

			$data = '"","COMPARATIVO DE SALDOS ' . $_REQUEST['anio1'] . ' Y ' . $_REQUEST['anio2'] . '"' . "\n\n";
			$data .= '"#CIA","COMPAÑIA","SALDO BANCO ' . $_REQUEST['anio1'] . '","SALDO PROVEEDORES ' . $_REQUEST['anio1'] . '","DIFERENCIA ' . $_REQUEST['anio1'] . '","SALDO BANCO ' . $_REQUEST['anio2'] . '","SALDO PROVEEDORES ' . $_REQUEST['anio2'] . '","DIFERENCIA ' . $_REQUEST['anio1'] . '","SALDO BANCO","SALDO PROVEEDORES","DIFERENCIA","UTILIDAD ' . $_REQUEST['anio1'] . '","REPARTO ' . $_REQUEST['anio1'] . '","UTILIDAD ' . $_REQUEST['anio2'] . '","REPARTO ' . $_REQUEST['anio2'] . '"' . "\n";

			if ($query)
			{
				$totales = array(
					'saldo_banco_1'			=> 0,
					'saldo_proveedores_1'	=> 0,
					'diferencia_1'			=> 0,
					'saldo_banco_2'			=> 0,
					'saldo_proveedores_2'	=> 0,
					'diferencia_2'			=> 0,
					'saldo_banco'			=> 0,
					'saldo_proveedores'		=> 0,
					'diferencia'			=> 0,
					'utilidad_1'			=> 0,
					'reparto_1'				=> 0,
					'utilidad_2'			=> 0,
					'reparto_2'				=> 0
				);

				foreach ($query as $row)
				{
					$data .= '"' . implode('","', array_map('toUTF8Encode', $row)) . '"' . "\n";

					foreach ($row as $key => $value)
					{
						if (in_array($key, array('num_cia', 'nombre_cia')))
						{
							continue;
						}

						$totales[$key] += $value;
					}
				}

				$data .= '"","TOTALES","' . implode('","', $totales) . '"' . "\n";
			}

			header('Content-Type: application/download');
			header("Content-Disposition: attachment; filename=comparativo-saldos-{$_REQUEST['anio1']}-{$_REQUEST['anio2']}.csv");

			echo $data;

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ComparativoSaldos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
