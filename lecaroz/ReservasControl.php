<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':

			$tpl = new TemplatePower('plantillas/bal/ReservasControlInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));

			$sql = "
				SELECT
					tipo_res
						AS value,
					'[' || tipo_res || '] ' || descripcion
						AS text
				FROM
					catalogo_reservas cr
				ORDER BY
					value
			";

			$result = $db->query($sql);

			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('reserva');

					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode($row['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$sql = "
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
					" . ($_SESSION['tipo_usuario'] == 1 && $_REQUEST['reserva'] == 4 ? "AND num_cia IN (SELECT numcia FROM total_produccion WHERE fecha_total BETWEEN '01/01/{$_REQUEST['anio']}' AND '31/12/{$_REQUEST['anio']}' GROUP BY numcia)" : '') . "
			";

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'consultar':
			$tpl = new TemplatePower('plantillas/bal/ReservasControlDetalle.tpl');
			$tpl->prepare();

			if ($_REQUEST['num_cia'] == '')
			{
				$result = $db->query("
					SELECT
						MIN(num_cia)
							AS num_cia
					FROM
						reservas_cias
					WHERE
						cod_reserva = {$_REQUEST['reserva']}
						AND anio = {$_REQUEST['anio']}
						" . ($_SESSION['tipo_usuario'] == 1 && $_REQUEST['reserva'] == 4 ? "AND num_cia IN (SELECT numcia FROM total_produccion WHERE fecha_total BETWEEN '01/01/{$_REQUEST['anio']}' AND '31/12/{$_REQUEST['anio']}' GROUP BY numcia)" : '') . "
						AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')
				);

				if ($result[0]['num_cia'] > 0)
				{
					$num_cia = $result[0]['num_cia'];
				}
				else if ($_SESSION['tipo_usuario'] == 1)
				{
					$num_cia = 1;
				}
				else if ($_SESSION['tipo_usuario'] == 2)
				{
					$num_cia = 901;
				}
			}
			else if (strpos($_REQUEST['num_cia'], '+') !== FALSE)
			{
				$result = $db->query("
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
						num_cia > " . str_replace('+', '', $_REQUEST['num_cia']) . "
						AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
						" . ($_SESSION['tipo_usuario'] == 1 && $_REQUEST['reserva'] == 4 ? "AND num_cia IN (SELECT numcia FROM total_produccion WHERE fecha_total BETWEEN '01/01/{$_REQUEST['anio']}' AND '31/12/{$_REQUEST['anio']}' GROUP BY numcia)" : '') . "
					ORDER BY
						num_cia
					LIMIT
						1
				");

				$num_cia = $result ? $result[0]['num_cia'] : ($_SESSION['tipo_usuario'] == 2 ? 901 : 1);
			}
			else if (strpos($_REQUEST['num_cia'], '-') !== FALSE)
			{
				$result = $db->query("
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
						num_cia < " . str_replace('-', '', $_REQUEST['num_cia']) . "
						AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
						" . ($_SESSION['tipo_usuario'] == 1 && $_REQUEST['reserva'] == 4 ? "AND num_cia IN (SELECT numcia FROM total_produccion WHERE fecha_total BETWEEN '01/01/{$_REQUEST['anio']}' AND '31/12/{$_REQUEST['anio']}' GROUP BY numcia)" : '') . "
					ORDER BY
						num_cia DESC
					LIMIT
						1
				");

				$num_cia = $result ? $result[0]['num_cia'] : ($_SESSION['tipo_usuario'] == 2 ? 901 : 1);
			}
			else
			{
				$num_cia = $_REQUEST['num_cia'];
			}

			$sql = "
				SELECT
					MAX(mes)
						AS mes
				FROM
					balances_" . ($_SESSION['tipo_usuario'] == 2 ? 'zap' : 'pan') . "
				WHERE
					anio = {$_REQUEST['anio']}
			";

			$result = $db->query($sql);

			$mes_balance = $result[0]['mes'] > 0 ? $result[0]['mes'] : date('n', mktime(0, 0, 0, date('n'), 0, date('Y')));

			$tpl->assign('num_cia', $num_cia);

			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$num_cia}");

			$tpl->assign('nombre_cia', utf8_decode($nombre_cia[0]['nombre_corto']));

			$tpl->assign('anio', $_REQUEST['anio']);

			$tpl->assign('cod_reserva', $_REQUEST['reserva']);

			$info_reserva = $db->query("SELECT descripcion, codgastos, aplicar_promedio, distribuir_diferencia FROM catalogo_reservas WHERE tipo_res = {$_REQUEST['reserva']}");

			$tpl->assign('reserva', utf8_decode($info_reserva[0]['descripcion']));

			$tpl->assign('distribuir_diferencia', $info_reserva[0]['distribuir_diferencia']);

			$reservas = array_fill(1, 12, array(
				'reserva'			=> 0,
				'pagado'			=> 0,
				'promedio'			=> 0,
				// Solo para IMSS
				'empleados'			=> 0,
				'infonavit'			=> 0,
				'prima_riesgo'		=> 0,
				'costo_empleado'	=> 0
			));

			$total_reserva = 0;
			$total_pagado = 0;

			$sql = "
				SELECT
					EXTRACT(MONTH FROM fecha)
						AS mes,
					importe
				FROM
					reservas_cias
				WHERE
					num_cia = {$num_cia}
					AND anio = {$_REQUEST['anio']}
					AND cod_reserva = $_REQUEST[reserva]
				ORDER BY
					fecha
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row) {
					$reservas[$row['mes']]['reserva'] = $row['importe'];

					$total_reserva += $row['mes'] <= $mes_balance + 1 ? $row['importe'] : 0;
				}
			}

			if ($info_reserva[0]['codgastos'] > 0)
			{
				$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 1, $_REQUEST['anio']));

				// Solo para IMSS
				if ($_REQUEST['reserva'] == 4 && $db->query("SELECT id FROM catalogo_filiales_imss WHERE num_cia = {$num_cia}"))
				{
					$sql = "
						SELECT
							anio,
							mes,
							importe
						FROM
							pagos_imss
						WHERE
							num_cia = {$num_cia}
							AND ('01/' || mes || '/' || anio)::DATE BETWEEN '{$fecha1}'::DATE AND '{$fecha2}'::DATE + INTERVAL '1 MONTH'
						ORDER BY
							anio,
							mes
					";
				}
				else
				{
					$sql = "
						SELECT
							EXTRACT(YEAR FROM fecha)
								AS anio,
							EXTRACT(MONTH FROM fecha)
								AS mes,
							SUM(importe)
								AS importe
						FROM
							cheques
							LEFT JOIN pagos_otras_cias USING (num_cia, cuenta, folio, fecha)
						WHERE
							COALESCE(num_cia_aplica, num_cia) = {$num_cia}
							AND fecha BETWEEN '{$fecha1}'::DATE AND '{$fecha2}'::DATE + INTERVAL '1 MONTH'
							AND codgastos = {$info_reserva[0]['codgastos']}
							AND fecha_cancelacion IS NULL
							AND importe > 0
						GROUP BY
							EXTRACT(YEAR FROM fecha),
							EXTRACT(MONTH FROM fecha)
						ORDER BY
							EXTRACT(YEAR FROM fecha),
							EXTRACT(MONTH FROM fecha)
					";
				}

				if ($result = $db->query($sql))
				{
					foreach ($result as $row)
					{
						$reservas[$row['anio'] == $_REQUEST['anio'] ? $row['mes'] : 13]['pagado'] = $row['importe'];
					}

					$meses = 0;

					foreach ($reservas as $mes => $reserva)
					{
						$total_pagado += $mes <= $mes_balance + 1 ? $reserva['pagado'] : 0;

						$meses += $reserva['pagado'] > 0 ? 1 : 0;

						$promedio = $mes <= $mes_balance + 1 && $meses > 0 ? $total_pagado / $meses : 0;

						$reservas[$mes]['promedio'] = $promedio;
					}
				}
			}

			if ($info_reserva[0]['codgastos'] > 0)
			{
				$tpl->newBlock('pagado_header');
			}

			if ($info_reserva[0]['aplicar_promedio'] == 't')
			{
				$tpl->newBlock('promedio_header');
			}

			// Solo para IMSS
			if ($_REQUEST['reserva'] == 4)
			{
				$tpl->newBlock('extra_info_header');
				$tpl->newBlock('extra_info_footer_1');
				$tpl->newBlock('extra_info_footer_2');

				if ($result = $db->query("SELECT mes, emp_afi FROM balances_pan WHERE num_cia = {$num_cia} AND anio = {$_REQUEST['anio']} ORDER BY mes"))
				{
					foreach ($result as $row)
					{
						$reservas[$row['mes']]['empleados'] = $row['emp_afi'];
					}
				}

				$sql = "
					SELECT
						anio,
						mes,
						SUM(importe)
							AS importe
					FROM
						(
							SELECT
								anio,
								mes,
								importe
							FROM
								infonavit i
								LEFT JOIN catalogo_trabajadores ct
									ON (ct.id = i.id_emp)
							WHERE
								num_cia = {$num_cia}
								AND anio = {$_REQUEST['anio']}

							UNION

							SELECT
								anio,
								mes,
								importe
							FROM
								infonavit_pendientes
							WHERE
								num_cia = {$num_cia}
								AND anio = {$_REQUEST['anio']}
								AND status = 0
						) result
					GROUP BY
						anio,
						mes
					ORDER BY
						anio,
						mes
				";

				if ($result = $db->query($sql))
				{
					foreach ($result as $row)
					{
						$reservas[$row['mes']]['infonavit'] = $row['importe'];
					}
				}

				$sql = "
					SELECT
						mes,
						prima_riesgo_trabajo
							AS prima_riesgo
					FROM
						balances_pan
					WHERE
						num_cia = {$num_cia}
						AND anio = {$_REQUEST['anio']}
					ORDER BY
						mes
				";

				if ($result = $db->query($sql))
				{
					foreach ($result as $row)
					{
						$reservas[$row['mes']]['prima_riesgo'] = $row['prima_riesgo'];
					}
				}

				$empleados = 0;
				$infonavit = 0;
				$pagado = 0;

				foreach ($reservas as $mes => $reserva)
				{
					$empleados += $reserva['empleados'];
					$infonavit += $reserva['infonavit'];
					$pagado += $reserva['pagado'];

					if ($mes % 2 == 0 && $reserva['empleados'] > 0)
					{
						$costo_empleado = $empleados > 0 ? ($pagado - $infonavit) / $empleados : 0;

						$reservas[$mes]['costo_empleado'] = $costo_empleado;
						$reservas[$mes - 1]['costo_empleado'] = $costo_empleado;

						$empleados = 0;
						$infonavit = 0;
						$pagado = 0;
					}
				}

			}

			foreach ($reservas as $mes => $reserva)
			{
				$tpl->newBlock('reserva');

				$tpl->assign('mes', $mes);
				$tpl->assign('nombre_mes', $_meses[$mes]);
				$tpl->assign('reserva_value', $reserva['reserva']);

				if ($info_reserva[0]['codgastos'] > 0)
				{
					$tpl->newBlock('pagado');

					$tpl->assign('pagado_value', $reserva['pagado']);
				}

				if ($info_reserva[0]['aplicar_promedio'] == 't')
				{
					$tpl->newBlock('promedio');

					$tpl->assign('promedio_value', $reserva['promedio']);
				}

				if ($mes < $mes_balance + 1)
				{
					$tpl->assign('reserva.status', 1);
					$tpl->assign('reserva.reserva', $reserva['reserva'] != 0 ? number_format($reserva['reserva'], 2) : '&nbsp;');

					if ($info_reserva[0]['codgastos'] > 0)
					{
						$tpl->assign('pagado.pagado', $reserva['pagado'] != 0 ? number_format($reserva['pagado'], 2) : '&nbsp;');
					}

					if ($info_reserva[0]['aplicar_promedio'] == 't')
					{
						$tpl->assign('promedio.promedio', $reserva['promedio'] != 0 ? number_format($reserva['promedio'], 2) : '&nbsp;');
					}
				}
				else if ($mes == $mes_balance + 1)
				{
					$tpl->assign('reserva.status', 0);
					$tpl->assign('reserva.reserva', '<a id="reserva_anchor_' . ($mes - 1) . '" class="bold underline">' . ($reserva['reserva'] != 0 ? number_format($reserva['reserva'], 2) : '-----') . '</a>');

					if ($info_reserva[0]['codgastos'] > 0)
					{
						$tpl->assign('pagado.pagado', $reserva['pagado'] != 0 ? '<span class="bold underline">' . number_format($reserva['pagado'], 2) . '</span>' : '&nbsp;');
					}

					if ($info_reserva[0]['aplicar_promedio'] == 't')
					{
						$tpl->assign('promedio.promedio', '<a id="promedio_anchor_' . ($mes - 1) . '" class="bold underline">' . ($reserva['promedio'] != 0 ? number_format($reserva['promedio'], 2) : '-----') . '</a>');
					}
				}
				else
				{
					$tpl->assign('reserva.status', -1);
					$tpl->assign('reserva.reserva', '<span id="reserva_anchor_' . ($mes - 1) . '" class="line-through light_gray">' . ($reserva['reserva'] != 0 ? number_format($reserva['reserva'], 2) : '') . '</span>');

					if ($info_reserva[0]['codgastos'] > 0)
					{
						$tpl->assign('pagado.pagado', $reserva['pagado'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['pagado'], 2) . '</span>' : '&nbsp;');
					}

					if ($info_reserva[0]['aplicar_promedio'] == 't')
					{
						$tpl->assign('promedio.promedio', $reserva['promedio'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['promedio'], 2) . '</span>' : '&nbsp;');
					}
				}

				// Solo para IMSS
				if ($_REQUEST['reserva'] == 4)
				{
					$tpl->newBlock('extra_info');

					if ($mes < $mes_balance + 1)
					{
						$tpl->assign('empleados', $reserva['empleados'] != 0 ? number_format($reserva['empleados']) : '&nbsp;');
						$tpl->assign('infonavit', $reserva['infonavit'] != 0 ? number_format($reserva['infonavit'], 2) : '&nbsp;');
						$tpl->assign('costo_empleado', $reserva['costo_empleado'] != 0 ? number_format($reserva['costo_empleado'], 2) : '&nbsp;');
						$tpl->assign('prima_riesgo', $reserva['prima_riesgo'] != 0 ? number_format($reserva['prima_riesgo'], 2) : '&nbsp;');
					}
					else if ($mes == $mes_balance + 1)
					{
						$tpl->assign('empleados', $reserva['empleados'] != 0 ? '<span class="bold underline">' . number_format($reserva['empleados']) . '</span>' : '&nbsp;');
						$tpl->assign('infonavit', $reserva['infonavit'] != 0 ? '<span class="bold underline">' . number_format($reserva['infonavit'], 2) . '</span>' : '&nbsp;');
						$tpl->assign('costo_empleado', $reserva['costo_empleado'] != 0 ? '<span class="bold underline">' . number_format($reserva['costo_empleado'], 2) . '</span>' : '&nbsp;');
						$tpl->assign('prima_riesgo', $reserva['prima_riesgo'] != 0 ? '<span class="bold underline">' . number_format($reserva['prima_riesgo'], 2) . '</span>' : '&nbsp;');
					}
					else
					{
						$tpl->assign('empleados', $reserva['empleados'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['empleados']) . '</span>' : '&nbsp;');
						$tpl->assign('infonavit', $reserva['infonavit'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['infonavit'], 2) . '</span>' : '&nbsp;');
						$tpl->assign('costo_empleado', $reserva['costo_empleado'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['costo_empleado'], 2) . '</span>' : '&nbsp;');
						$tpl->assign('prima_riesgo', $reserva['prima_riesgo'] != 0 ? '<span class="line-through light_gray">' . number_format($reserva['prima_riesgo'], 2) . '</span>' : '&nbsp;');
					}

				}
			}

			$tpl->assign('_ROOT.total_reserva', number_format($total_reserva, 2));

			$tpl->assign('_ROOT.diferencia_span', $info_reserva[0]['codgastos'] > 0 ? 2 : 1);

			if ($info_reserva[0]['codgastos'] > 0)
			{
				$tpl->newBlock('total_pagado');

				$tpl->assign('total_pagado', number_format($total_pagado, 2));
			}

			if ($info_reserva[0]['aplicar_promedio'] == 't')
			{
				$tpl->newBlock('blank_promedio_1');
				$tpl->newBlock('blank_promedio_2');
			}

			echo $tpl->getOutputContent();

			break;

		case 'actualizar':
			$sql = '';

			foreach ($_REQUEST['mes'] as $i => $mes)
			{
				$fecha = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $_REQUEST['anio']));

				if ($id = $db->query("SELECT id FROM reservas_cias WHERE num_cia = {$_REQUEST['num_cia']} AND cod_reserva = {$_REQUEST['reserva']} AND fecha = '{$fecha}'"))
				{
					$sql .= "UPDATE reservas_cias SET importe = {$_REQUEST['reserva_input'][$i]} WHERE id = {$id[0]['id']};\n";
				}
				else
				{
					$sql .= "INSERT INTO reservas_cias (num_cia, importe, fecha, cod_reserva, anio, pagado) VALUES ({$_REQUEST['num_cia']}, {$_REQUEST['reserva_input'][$i]}, '{$fecha}', {$_REQUEST['reserva']}, {$_REQUEST['anio']}, 0);\n";
				}
			}

			if ($sql != '')
			{
				$db->query($sql);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReservasControl.tpl');	//
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
