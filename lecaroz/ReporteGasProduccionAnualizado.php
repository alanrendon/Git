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
			$tpl = new TemplatePower('plantillas/bal/ReporteGasProduccionAnualizadoInicio.tpl');
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

			$condiciones[] = "bal.anio = {$_REQUEST['anio']}";

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
					$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				bal.num_cia,
				cc.nombre_corto AS nombre_cia,
				bal.mes,
				COALESCE (
					(
						SELECT
							'normal' :: VARCHAR
						FROM
							mov_inv_real
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND tipo_mov = TRUE
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND num_cia != 76
						LIMIT 1
					),
					(
						SELECT
							'natural' :: VARCHAR
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 128
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						LIMIT 1
					),
					'normal' :: VARCHAR
				) AS tipo,
				bal.produccion_total AS produccion,
				COALESCE (
					(
						SELECT
							SUM (importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 90
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					(
						SELECT
							SUM (importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 128
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS costo_consumo,
				COALESCE (
					(
						SELECT
							SUM (cantidad)
						FROM
							mov_inv_real
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND tipo_mov = TRUE
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS litros_consumo,
				COALESCE (
					(
						SELECT
							SUM (importe)
						FROM
							gastos_caja
						WHERE
							num_cia = bal.num_cia
						AND cod_gastos = 92
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = 'TRUE'
					),
					0
				) AS descuento,
				COALESCE (
					(
						SELECT
							precio_unidad
						FROM
							historico_inventario
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND fecha = bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS precio
			FROM
				balances_pan AS bal
			LEFT JOIN catalogo_companias AS cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				bal.num_cia,
				bal.mes";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$totales_normal = array_fill(1, 12, 0);
				$totales_natural = array_fill(1, 12, 0);
				$totales_general = array_fill(1, 12, 0);

				$cont_normal = array_fill(1, 12, 0);
				$cont_natural = array_fill(1, 12, 0);
				$cont_general = array_fill(1, 12, 0);

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia'])
					{
						if (isset($_REQUEST['excedentes']) && isset($excedente) && $excedente === FALSE)
						{
							unset($datos[$num_cia]);
						}

						$num_cia = $row['num_cia'];
						$tipo = $row['tipo'];

						$datos[$num_cia] = array(
							'nombre'			=> utf8_encode($row['nombre_cia']),
							'tipo'				=> $row['tipo'],
							'producciones'		=> array_fill(1, 12, 0),
							'costo_consumos'	=> array_fill(1, 12, 0),
							'litros_consumos'	=> array_fill(1, 12, 0),
							'descuentos'		=> array_fill(1, 12, 0),
							'porcentajes'		=> array_fill(1, 12, 0),
							'excedentes'		=> array_fill(1, 12, FALSE),
							'proveedores'		=> array_fill(1, 12, array()),
							'precios'			=> array_fill(1, 12, 0),
							'costo_excedentes'	=> array_fill(1, 12, 0)
						);

						$excedente = FALSE;
					}

					$datos[$num_cia]['producciones'][$row['mes']] = floatval($row['produccion']);
					$datos[$num_cia]['costo_consumos'][$row['mes']] = floatval($row['costo_consumo']);
					$datos[$num_cia]['litros_consumos'][$row['mes']] = floatval($row['litros_consumo']);
					$datos[$num_cia]['descuentos'][$row['mes']] = floatval($row['descuento']);
					$datos[$num_cia]['porcentajes'][$row['mes']] = floatval($row['produccion']) > 0 ? (floatval($row['costo_consumo']) - floatval($row['descuento'])) / floatval($row['produccion']) : 0;
					$datos[$num_cia]['excedentes'][$row['mes']] = $datos[$num_cia]['porcentajes'][$row['mes']] > 0.03 ? $datos[$num_cia]['porcentajes'][$row['mes']] - 0.03 : 0;
					$datos[$num_cia]['precios'][$row['mes']] = $tipo == 'normal' ? $row['precio'] : 0;
					$datos[$num_cia]['costo_excedentes'][$row['mes']] = $tipo == 'normal' && $datos[$num_cia]['porcentajes'][$row['mes']] != 0 ? (floatval($row['costo_consumo']) - floatval($row['descuento'])) * $datos[$num_cia]['excedentes'][$row['mes']] / $datos[$num_cia]['porcentajes'][$row['mes']] : 0;

					if ($datos[$num_cia]['excedentes'][$row['mes']] > 0)
					{
						$excedente = TRUE;
					}

					$fecha1 = date('d/m/Y', mktime(0, 0, 0, $row['mes'], 1, $_REQUEST['anio']));
					$fecha2 = date('d/m/Y', mktime(0, 0, 0, $row['mes'] + 1, 0, $_REQUEST['anio']));

					if ($tipo == 'normal' && $pros = $db->query("SELECT num_proveedor AS num_pro, nombre AS nombre FROM mov_inv_real LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = {$num_cia} AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND codmp = 90 AND tipo_mov = FALSE AND num_proveedor > 0 GROUP BY num_proveedor, nombre ORDER BY nombre"))
					{
						foreach ($pros as $pro) {
							$datos[$num_cia]['proveedores'][$row['mes']][] = "{$pro['num_pro']} {$pro['nombre']}";
						}
					}
					else if ($tipo == 'natural' && $pros = $db->query("SELECT c.num_proveedor AS num_pro, c.a_nombre AS nombre FROM movimiento_gastos AS mg LEFT JOIN cheques AS c USING (num_cia, fecha, folio) WHERE mg.num_cia = {$num_cia} AND mg.codgastos = 128 AND mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND folio > 0 GROUP BY num_pro, nombre ORDER BY nombre"))
					{
						foreach ($pros as $pro) {
							$datos[$num_cia]['proveedores'][$row['mes']][] = "{$pro['num_pro']} {$pro['nombre']}";
						}
					}
				}

				if (isset($_REQUEST['excedentes']) && isset($excedente) && $excedente === FALSE)
				{
					unset($datos[$num_cia]);
				}

				$tpl = new TemplatePower('plantillas/bal/ReporteGasProduccionAnualizadoConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);

				foreach ($datos as $num_cia => $datos_cia)
				{
					if (array_sum($datos_cia['producciones']) == 0)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre']);
					$tpl->assign('cia_color', $datos_cia['tipo'] == 'natural' ? ' class="orange"' : '');

					if (isset($_REQUEST['costos']))
					{
						foreach ($datos_cia['costo_excedentes'] as $mes => $costo)
						{
							$tpl->assign('mes' . $mes, $costo != 0 ? '<a id="info" data-info="' . htmlentities(implode('<br />', $datos_cia['proveedores'][$mes])) . '" class="red">' . number_format($costo, 2) . '</a>' : '&nbsp;');

							if ($datos_cia['tipo'] == 'normal' && $costo != 0)
							{
								$totales_normal[$mes] += $costo;
								$cont_normal[$mes]++;
							}
							else if ($datos_cia['tipo'] == 'natural' && $costo != 0)
							{
								$totales_natural[$mes] += $costo;
								$cont_natural[$mes]++;
							}

							if ($costo != 0)
							{
								$totales_general[$mes] += $costo;
								$cont_general[$mes]++;
							}
						}
					}
					else
					{
						foreach ($datos_cia['porcentajes'] as $mes => $por)
						{
							if (isset($_REQUEST['excedentes']))
							{
								$tpl->assign('mes' . $mes, $datos_cia['excedentes'][$mes] != 0 ? '<a id="info" data-info="' . htmlentities(implode('<br />', $datos_cia['proveedores'][$mes])) . '" class="red">' . number_format($datos_cia['excedentes'][$mes] * 100, 3) . '</a>' : '&nbsp;');
							}
							else
							{
								$tpl->assign('mes' . $mes, $por != 0 ? '<a id="info" data-info="' . htmlentities(implode('<br />', $datos_cia['proveedores'][$mes])) . '" class="' . ($por > 0.03 ? 'red' : 'blue') . '">' . number_format($por * 100, 3) . '</a>' : '&nbsp;');
							}

							if ($datos_cia['tipo'] == 'normal' && $por != 0)
							{
								$totales_normal[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_normal[$mes]++;
							}
							else if ($datos_cia['tipo'] == 'natural' && $por != 0)
							{
								$totales_natural[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_natural[$mes]++;
							}

							if ($por != 0)
							{
								$totales_general[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_general[$mes]++;
							}
						}
					}
				}

				foreach ($totales_general as $mes => $total_general)
				{
					if (isset($_REQUEST['costos']))
					{
						$tpl->assign('_ROOT.prom_normal' . $mes, $totales_normal[$mes] != 0 ? number_format($totales_normal[$mes], 2) : '&nbsp;');
						$tpl->assign('_ROOT.prom_natural' . $mes, $totales_natural[$mes] != 0 ? number_format($totales_natural[$mes], 2) : '&nbsp;');
						$tpl->assign('_ROOT.prom_general' . $mes, $total_general != 0 ? number_format($total_general, 2) : '&nbsp;');
					}
					else
					{
						$tpl->assign('_ROOT.prom_normal' . $mes, $totales_normal[$mes] != 0 ? number_format($totales_normal[$mes] / $cont_normal[$mes] * 100, 3) : '&nbsp;');
						$tpl->assign('_ROOT.prom_natural' . $mes, $totales_natural[$mes] != 0 ? number_format($totales_natural[$mes] / $cont_natural[$mes] * 100, 3) : '&nbsp;');
						$tpl->assign('_ROOT.prom_general' . $mes, $total_general != 0 ? number_format($total_general / $cont_general[$mes] * 100, 3) : '&nbsp;');
					}
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$condiciones = array();

			$condiciones[] = "bal.anio = {$_REQUEST['anio']}";

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
					$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				bal.num_cia,
				cc.nombre_corto AS nombre_cia,
				bal.mes,
				COALESCE (
					(
						SELECT
							'normal' :: VARCHAR
						FROM
							mov_inv_real
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND tipo_mov = TRUE
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND num_cia != 76
						LIMIT 1
					),
					(
						SELECT
							'natural' :: VARCHAR
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 128
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						LIMIT 1
					),
					'normal' :: VARCHAR
				) AS tipo,
				bal.produccion_total AS produccion,
				COALESCE (
					(
						SELECT
							SUM (importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 90
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					(
						SELECT
							SUM (importe)
						FROM
							movimiento_gastos
						WHERE
							num_cia = bal.num_cia
						AND codgastos = 128
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS costo_consumo,
				COALESCE (
					(
						SELECT
							SUM (cantidad)
						FROM
							mov_inv_real
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND tipo_mov = TRUE
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS litros_consumo,
				COALESCE (
					(
						SELECT
							SUM (importe)
						FROM
							gastos_caja
						WHERE
							num_cia = bal.num_cia
						AND cod_gastos = 92
						AND fecha BETWEEN bal.fecha
						AND bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = 'TRUE'
					),
					0
				) AS descuento,
				COALESCE (
					(
						SELECT
							precio_unidad
						FROM
							historico_inventario
						WHERE
							num_cia = bal.num_cia
						AND codmp = 90
						AND fecha = bal.fecha + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
					),
					0
				) AS precio
			FROM
				balances_pan AS bal
			LEFT JOIN catalogo_companias AS cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				bal.num_cia,
				bal.mes";

			$query = $db->query($sql);

			if ($query)
			{
				$result = array();

				$totales_normal = array_fill(1, 12, 0);
				$totales_natural = array_fill(1, 12, 0);
				$totales_general = array_fill(1, 12, 0);

				$cont_normal = array_fill(1, 12, 0);
				$cont_natural = array_fill(1, 12, 0);
				$cont_general = array_fill(1, 12, 0);

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia'])
					{
						if (isset($_REQUEST['excedentes']) && isset($excedente) && $excedente === FALSE)
						{
							unset($datos[$num_cia]);
						}

						$num_cia = $row['num_cia'];
						$tipo = $row['tipo'];

						$datos[$num_cia] = array(
							'nombre'			=> utf8_encode($row['nombre_cia']),
							'tipo'				=> $row['tipo'],
							'producciones'		=> array_fill(1, 12, 0),
							'costo_consumos'	=> array_fill(1, 12, 0),
							'litros_consumos'	=> array_fill(1, 12, 0),
							'descuentos'		=> array_fill(1, 12, 0),
							'porcentajes'		=> array_fill(1, 12, 0),
							'excedentes'		=> array_fill(1, 12, FALSE),
							'proveedores'		=> array_fill(1, 12, array()),
							'precios'			=> array_fill(1, 12, 0),
							'costo_excedentes'	=> array_fill(1, 12, 0)
						);

						$excedente = FALSE;
					}

					$datos[$num_cia]['producciones'][$row['mes']] = floatval($row['produccion']);
					$datos[$num_cia]['costo_consumos'][$row['mes']] = floatval($row['costo_consumo']);
					$datos[$num_cia]['litros_consumos'][$row['mes']] = floatval($row['litros_consumo']);
					$datos[$num_cia]['descuentos'][$row['mes']] = floatval($row['descuento']);
					$datos[$num_cia]['porcentajes'][$row['mes']] = floatval($row['produccion']) > 0 ? (floatval($row['costo_consumo']) - floatval($row['descuento'])) / floatval($row['produccion']) : 0;
					$datos[$num_cia]['excedentes'][$row['mes']] = $datos[$num_cia]['porcentajes'][$row['mes']] > 0.03 ? $datos[$num_cia]['porcentajes'][$row['mes']] - 0.03 : 0;
					$datos[$num_cia]['precios'][$row['mes']] = $tipo == 'normal' ? $row['precio'] : 0;
					$datos[$num_cia]['costo_excedentes'][$row['mes']] = $tipo == 'normal' && $datos[$num_cia]['porcentajes'][$row['mes']] != 0 ? (floatval($row['costo_consumo']) - floatval($row['descuento'])) * $datos[$num_cia]['excedentes'][$row['mes']] / $datos[$num_cia]['porcentajes'][$row['mes']] : 0;

					if ($datos[$num_cia]['excedentes'][$row['mes']] > 0)
					{
						$excedente = TRUE;
					}

					$fecha1 = date('d/m/Y', mktime(0, 0, 0, $row['mes'], 1, $_REQUEST['anio']));
					$fecha2 = date('d/m/Y', mktime(0, 0, 0, $row['mes'] + 1, 0, $_REQUEST['anio']));

					if ($tipo == 'normal' && $pros = $db->query("SELECT num_proveedor AS num_pro, nombre AS nombre FROM mov_inv_real LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = {$num_cia} AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND codmp = 90 AND tipo_mov = FALSE AND num_proveedor > 0 GROUP BY num_proveedor, nombre ORDER BY nombre"))
					{
						foreach ($pros as $pro) {
							$datos[$num_cia]['proveedores'][$row['mes']][] = "{$pro['num_pro']} {$pro['nombre']}";
						}
					}
					else if ($tipo == 'natural' && $pros = $db->query("SELECT c.num_proveedor AS num_pro, c.a_nombre AS nombre FROM movimiento_gastos AS mg LEFT JOIN cheques AS c USING (num_cia, fecha, folio) WHERE mg.num_cia = {$num_cia} AND mg.codgastos = 128 AND mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND folio > 0 GROUP BY num_pro, nombre ORDER BY nombre"))
					{
						foreach ($pros as $pro) {
							$datos[$num_cia]['proveedores'][$row['mes']][] = "{$pro['num_pro']} {$pro['nombre']}";
						}
					}
				}

				if (isset($_REQUEST['excedentes']) && isset($excedente) && $excedente === FALSE)
				{
					unset($datos[$num_cia]);
				}

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

						$this->Cell(0, 4, 'PORCENTAJES DE GAS CONTRA PRODUCCION ' . $_REQUEST['anio'], 0, 1, 'C');

						$this->Ln(5);

						$this->SetFont('ARIAL', 'B', 8);

						$this->Cell(53, 4, utf8_decode('COMPAÑIA'), 1, 0, 'C');
						$this->Cell(18, 4, utf8_decode('ENE'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('FEB'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('MAR'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('ABR'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('MAY'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('JUN'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('JUL'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('AGO'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('SEP'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('OCT'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('NOV'), 1, 0, 'R');
						$this->Cell(18, 4, utf8_decode('DIC'), 1, 0, 'R');

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

				foreach ($datos as $num_cia => $datos_cia)
				{
					if (array_sum($datos_cia['producciones']) == 0)
					{
						continue;
					}

					$pdf->SetFont('ARIAL', '', 10);

					$pdf->SetTextColor(0, 0, 0);

					$nombre_cia = "{$num_cia} {$datos_cia['nombre']}";

					while ($pdf->GetStringWidth($nombre_cia) > 53)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(53, 4, utf8_decode($nombre_cia), 1, 0);

					$proveedores = NULL;

					if (isset($_REQUEST['costos']))
					{
						foreach ($datos_cia['costo_excedentes'] as $mes => $costo)
						{
							$pdf->SetTextColor(204, 0, 0);

							$pdf->Cell(18, 4, $costo != 0 ? number_format($costo, 2) : '', 1, 0, 'R');

							if ($datos_cia['tipo'] == 'normal' && $costo != 0)
							{
								$totales_normal[$mes] += $costo;
								$cont_normal[$mes]++;
							}
							else if ($datos_cia['tipo'] == 'natural' && $costo != 0)
							{
								$totales_natural[$mes] += $costo;
								$cont_natural[$mes]++;
							}

							if ($costo != 0)
							{
								$totales_general[$mes] += $costo;
								$cont_general[$mes]++;
							}

							if ($datos_cia['proveedores'][$mes])
							{
								$proveedores = implode(', ', $datos_cia['proveedores'][$mes]);
							}
						}
					}
					else
					{
						foreach ($datos_cia['porcentajes'] as $mes => $por)
						{
							if ($por > 0.03)
							{
								$pdf->SetTextColor(204, 0, 0);
							}
							else
							{
								$pdf->SetTextColor(0, 0, 204);
							}

							if (isset($_REQUEST['excedentes']))
							{
								$pdf->Cell(18, 4, $datos_cia['excedentes'][$mes] != 0 ? number_format($datos_cia['excedentes'][$mes] * 100, 3) : '', 1, 0, 'R');
							}
							else
							{
								$pdf->Cell(18, 4, $por != 0 ? number_format($por * 100, 3) : '', 1, 0, 'R');
							}


							if ($datos_cia['tipo'] == 'normal' && $por != 0)
							{
								$totales_normal[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_normal[$mes]++;
							}
							else if ($datos_cia['tipo'] == 'natural' && $por != 0)
							{
								$totales_natural[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_natural[$mes]++;
							}

							if ($por != 0)
							{
								$totales_general[$mes] += isset($_REQUEST['excedentes']) ? $datos_cia['excedentes'][$mes] : $por;
								$cont_general[$mes]++;
							}

							if ($datos_cia['proveedores'][$mes])
							{
								$proveedores = implode(', ', $datos_cia['proveedores'][$mes]);
							}
						}
					}

					$pdf->Ln();

					$pdf->SetTextColor(0, 0, 0);

					$pdf->Cell(269, 4, $proveedores, 1, 0);

					if ($rows < 22)
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

				$pdf->SetFont('ARIAL', 'B', 10);

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(53, 4, utf8_decode('GAS L.P.'), 1, 0, 'R');

				foreach ($totales_normal as $mes => $total_normal)
				{
					if (isset($_REQUEST['costos']))
					{
						$pdf->Cell(18, 4, $total_normal != 0 ? number_format($total_normal, 2) : '', 1, 0, 'R');
					}
					else
					{
						$pdf->Cell(18, 4, $total_normal != 0 ? number_format($total_normal / $cont_normal[$mes] * 100, 3) : '', 1, 0, 'R');
					}
				}

				$pdf->Ln();

				$pdf->Cell(53, 4, utf8_decode('GAS NATURAL'), 1, 0, 'R');

				foreach ($totales_natural as $mes => $total_natural)
				{
					if (isset($_REQUEST['costos']))
					{
						$pdf->Cell(18, 4, $total_natural != 0 ? number_format($total_natural, 2) : '', 1, 0, 'R');
					}
					else
					{
						$pdf->Cell(18, 4, $total_natural != 0 ? number_format($total_natural / $cont_natural[$mes] * 100, 3) : '', 1, 0, 'R');
					}
				}

				$pdf->Ln();

				$pdf->Cell(53, 4, utf8_decode('GAS EN GENERAL'), 1, 0, 'R');

				foreach ($totales_general as $mes => $total_general)
				{
					if (isset($_REQUEST['costos']))
					{
						$pdf->Cell(18, 4, $total_general != 0 ? number_format($total_general, 2) : '', 1, 0, 'R');
					}
					else
					{
						$pdf->Cell(18, 4, $total_general != 0 ? number_format($total_general / $cont_general[$mes] * 100, 3) : '', 1, 0, 'R');
					}
				}

				$pdf->Output('reporte-gas-produccion-' . $_REQUEST['anio'] . '.pdf', 'I');
			}

			break;

		case 'exportar':
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
						AS \"#\",
					ccs.nombre
						AS \"COMPAÑIA\",
					SUM(s.saldo_bancos)
						AS \"SALDO BANCOS\",
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
						AS \"SALDO LIBROS\",
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
						AS \"PAGOS NO COBRADOS\",
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
						AS \"SALDO PROVEEDORES\",
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
						AS \"LIBROS - PROVEEDORES\",
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
						AS \"INVENTARIO INICIAL\",
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
						AS \"PERDIDAS\",
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
						AS \"DEVOLUCIONES DE IVA\"
				FROM
					saldos s
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_saldos)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				GROUP BY
					cc.num_cia_saldos,
					ccs.nombre
				ORDER BY
					cc.num_cia_saldos
			";

			$query = $db->query($sql);

			if ($query)
			{
				$data = '"","SALDOS CONTABLES"' . "\n";
				$data .= '"","' . (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0 ? ($_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : 'BANCOS CONSOLIDADOS') . '"' . "\n\n";

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

				$data .= '"' . implode('","', array_keys($query[0])) . '"' . "\n";

				foreach ($query as $row)
				{
					if (abs(round($row['SALDO BANCOS'], 2)) == 0
						&& abs(round($row['SALDO LIBROS'], 2)) == 0
						&& abs(round($row['PAGOS NO COBRADOS'], 2)) == 0
						&& abs(round($row['SALDO PROVEEDORES'], 2)) == 0
						&& abs(round($row['LIBROS - PROVEEDORES'], 2)) == 0
						&& abs(round($row['INVENTARIO INICIAL'], 2)) == 0
						&& abs(round($row['PERDIDAS'], 2)) == 0
						&& abs(round($row['DEVOLUCIONES DE IVA'], 2)) == 0)
					{
						continue;
					}

					$data .= '"' . implode('","', array_values($row)) . '"' . "\n";

					$total['saldo_bancos'] += $row['SALDO BANCOS'];
					$total['saldo_libros'] += $row['SALDO LIBROS'];
					$total['pagos_no_cobrados'] += $row['PAGOS NO COBRADOS'];
					$total['saldo_proveedores'] += $row['SALDO PROVEEDORES'];
					$total['dif_libros_proveedores'] += $row['LIBROS - PROVEEDORES'];
					$total['inventario_inicial'] += $row['INVENTARIO INICIAL'];
					$total['perdidas'] += $row['PERDIDAS'];
					$total['devoluciones_iva'] += $row['DEVOLUCIONES DE IVA'];
				}

				if (count($query) > 1)
				{
					$data .= '"","TOTALES",';

					$data .= '"' . implode('","', array_values($total)) . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=ReporteGasProduccionAnualizado.csv');

				echo $data;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteGasProduccionAnualizado.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
