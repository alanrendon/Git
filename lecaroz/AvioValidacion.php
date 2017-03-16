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

$dias_festivos = array(
	'01/01',
	'04/01',
	'05/01',
	'06/01',
	'07/01',
	'14/02',
	'30/04',
	'10/05',
	'30/10',
	'31/10',
	'01/11',
	'02/11',
	'12/12',
	'24/12',
	'25/12',
	'31/12'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/AvioValidacionInicio.tpl');
			$tpl->prepare();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 57, 42)))
			{
				$result = $db->query("SELECT nombre_operadora FROM catalogo_operadoras WHERE iduser = {$_SESSION['iduser']}");
				$usuario = $result[0]['nombre_operadora'];
			}
			else
			{
				$usuario = "ADMINISTRADOR";
			}

			$tpl->assign('usuario', utf8_encode($usuario));

			$condiciones = array();

			$condiciones[] = "efe.ts_aut IS NULL";

			$condiciones[] = "inv.ts_aut IS NULL";

			$condiciones[] = "cc.tipo_cia = 1";

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 57, 42)))
			{
				$condiciones[] = "co.iduser = {$_SESSION['iduser']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				inv.num_cia,
				cc.nombre_corto AS nombre_cia,
				inv.fecha,
				CASE
					WHEN inv.fecha - COALESCE((
						SELECT
							MAX(fecha)
						FROM
							mov_inv_real
						WHERE
							num_cia = inv.num_cia
							AND tipo_mov = TRUE
					), NOW()::DATE) = 1 THEN
						TRUE
					ELSE
						FALSE
				END AS status
			FROM
				efectivos_tmp efe
				LEFT JOIN mov_inv_tmp inv USING (num_cia, fecha)
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_operadoras co USING (idoperadora)
			WHERE
				{$condiciones_string}
			GROUP BY
				inv.num_cia,
				cc.nombre_corto,
				inv.fecha
			ORDER BY
				inv.num_cia,
				inv.fecha");

			if ($result)
			{
				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('disabled', $row['status'] == 'f' ? ' disabled="disabled"' : '');
				}
			}
			else
			{
				$tpl->newBlock('no_result');
			}

			echo $tpl->getOutputContent();

			break;

		case 'consulta':
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$condiciones = array();

			$condiciones[] = "inv.num_cia = {$_REQUEST['num_cia']}";

			$condiciones[] = "inv.fecha = '{$_REQUEST['fecha']}'";

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				inv.id,
				inv.codmp,
				cmp.nombre AS nombre_mp,
				COALESCE(inv.cod_turno, 0) AS turno,
				ct.descripcion AS nombre_turno,
				CASE
					WHEN inv.cod_turno > 0 THEN
						ca.num_orden
					ELSE
						COALESCE((
							SELECT
								num_orden
							FROM
								control_avio
							WHERE
								num_cia = inv.num_cia
								AND codmp = inv.codmp
							LIMIT 1
						), 999999)
				END AS num_orden,
				inv.tipomov AS tipo,
				inv.cantidad,
				CASE
					WHEN inv.codmp = 1 THEN
						iv.existencia / 44
					ELSE
						iv.existencia
				END AS existencia,
				CASE
					WHEN ir.existencia != iv.existencia THEN
						FALSE
					ELSE
						TRUE
				END AS diferencia,
				CASE
					WHEN inv.tipomov = TRUE THEN
						COALESCE((
							SELECT
								ROUND(AVG(cantidad)::NUMERIC, 2)
							FROM
								mov_inv_real
							WHERE
								num_cia = inv.num_cia
								AND fecha BETWEEN inv.fecha - INTERVAL '3 MONTHS' AND inv.fecha
								AND codmp = inv.codmp
								AND tipo_mov = TRUE
								AND cod_turno = inv.cod_turno
								AND descripcion NOT LIKE 'DIFERENCIA INVENTARIO'
						), 0)
					ELSE
						0
				END AS promedio,
				CASE
					WHEN ca.idavio IS NOT NULL OR inv.tipomov = FALSE THEN
						TRUE
					ELSE
						FALSE
				END AS existe_control
			FROM
				mov_inv_tmp inv
				LEFT JOIN inventario_virtual iv USING (num_cia, codmp)
				LEFT JOIN inventario_real ir USING (num_cia, codmp)
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				LEFT JOIN catalogo_turnos ct USING (cod_turno)
				LEFT JOIN control_avio ca USING (num_cia, codmp, cod_turno)
			WHERE
				{$condiciones_string}
			ORDER BY
				num_orden,
				inv.codmp,
				inv.tipomov,
				inv.cod_turno");

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/pan/AvioValidacionConsulta.tpl');
				$tpl->prepare();

				$cia = $db->query("SELECT nombre_corto AS nombre_cia FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");

				$tpl->assign('num_cia', $_REQUEST['num_cia']);
				$tpl->assign('fecha', $_REQUEST['fecha']);

				$tpl->assign('nombre_cia', utf8_encode($cia[0]['nombre_cia']));

				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$tpl->assign('dia', $dia);
				$tpl->assign('mes', $mes);
				$tpl->assign('anio', $anio);
				$tpl->assign('mes_escrito', mb_strtoupper($_meses[$mes]));

				$excedentes_consumo = array();
				$sin_promedio = array();
				$excedentes_existencia = array();

				$ok = TRUE;

				$codmp = NULL;

				foreach ($result as $row)
				{
					if ($codmp != $row['codmp'])
					{
						$codmp = $row['codmp'];

						$tpl->newBlock('row');

						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));

						$tpl->assign('existencia_inicio', $row['existencia'] != 0 ? number_format($row['existencia'], 2) : '&nbsp;');
						$tpl->assign('existencia_inicio_color', $row['diferencia'] == 'f' ? 'red' : 'green');

						$existencia_inicio = $row['existencia'];
						$entrada = 0;
						$consumos = 0;
						$promedios = 0;
						$existencia_fin = $row['existencia'];
					}

					$tpl->assign("movimiento_" . $row['turno'], $row['cantidad'] != 0 ? number_format($row['cantidad'], 2) : '&nbsp;');

					if ($row['existe_control'] == 'f')
					{
						$tpl->assign('no_control', ' style="background-color:#FFD7D6"');

						$ok = FALSE;
					}

					if ($row['tipo'] == 'f')
					{
						$existencia_fin += $row['cantidad'];

						$tpl->assign('total', number_format($existencia_inicio + $row['cantidad'], 2));
					}
					else
					{
						$consumos += $row['cantidad'];
						$promedios += $row['promedio'];
						$existencia_fin -= $row['cantidad'];

						$tpl->assign('consumos', $consumos != 0 ? number_format($consumos, 2) : '&nbsp;');

						if ($row['promedio'] == 0)
						{
							$sin_promedio[] = $row;
						}

						if ($row['promedio'] > 0 && $row['cantidad'] > round($row['promedio'] * 1.20, 2))
						{
							$excedentes_consumo[] = $row;
						}
					}

					$tpl->assign('existencia_fin', $existencia_fin != 0 ? number_format($existencia_fin, 2) : '&nbsp;');
					$tpl->assign('existencia_fin_color', $existencia_fin <= 0 ? 'red' : 'green');
				}

				if ($excedentes_consumo)
				{
					$tpl->newBlock('consumos_excedentes');

					foreach ($excedentes_consumo as $row)
					{
						$tpl->newBlock('row_consumo_excedente');

						$tpl->assign('codmp', $row['codmp']);
						$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
						$tpl->assign('turno', utf8_encode($row['nombre_turno']));
						$tpl->assign('promedio', number_format($row['promedio'] * 1.20, 2));
						$tpl->assign('consumo', number_format($row['cantidad'], 2));

						$por_diferencia = ($row['cantidad'] * 100 / ($row['promedio'] * 1.20)) - 100 + 20;

						$tpl->assign('por_diferencia', number_format($por_diferencia, 2));
						$tpl->assign('por_diferencia_color', $por_diferencia >= 200 ? ' red' : '');

						$tpl->assign('consumo_excedente', json_encode(array(
							'num_cia'		=> $_REQUEST['num_cia'],
							'fecha'			=> $_REQUEST['fecha'],
							'codmp'			=> $row['codmp'],
							'turno'			=> $row['turno'],
							'consumo'		=> $row['cantidad'],
							'promedio'		=> $row['promedio'],
							'diferencia'	=> $por_diferencia
						)));
					}
				}

				if ($sin_promedio)
				{
					$tpl->newBlock('sin_promedios');

					foreach ($sin_promedio as $row)
					{
						$tpl->newBlock('row_sin_promedio');

						$tpl->assign('codmp', $row['codmp']);
						$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
						$tpl->assign('turno', utf8_encode($row['nombre_turno']));
						$tpl->assign('consumo', number_format($row['cantidad'], 2));

						$tpl->assign('sin_promedio', json_encode(array(
							'num_cia'		=> $_REQUEST['num_cia'],
							'fecha'			=> $_REQUEST['fecha'],
							'codmp'			=> $row['codmp'],
							'turno'			=> $row['turno'],
							'consumo'		=> $row['cantidad'],
							'promedio'		=> 0,
							'diferencia'	=> 0
						)));
					}
				}

				$excedentes_existencia = $db->query("SELECT
					inv.codmp,
					cmp.nombre AS nombre_mp,
					CASE
						WHEN inv.codmp = 1 THEN
							inv.existencia / 44
						ELSE
							inv.existencia
					END AS existencia,
					CASE
						WHEN inv.codmp = 1 THEN
							ROUND(AVG(mov.cantidad)::NUMERIC, 2) * 25 / 44
						ELSE
							ROUND(AVG(mov.cantidad)::NUMERIC, 2) * 25
					END AS consumos
				FROM
					mov_inv_real mov
					LEFT JOIN inventario_virtual inv USING (num_cia, codmp)
					LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND fecha BETWEEN '{$_REQUEST['fecha']}'::DATE - INTERVAL '3 MONTHS' AND '{$_REQUEST['fecha']}'::DATE
					AND tipo_mov = TRUE
					AND descripcion NOT LIKE 'DIFERENCIA INVENTARIO'
				GROUP BY
					inv.codmp,
					cmp.nombre,
					inv.existencia
				HAVING
					inv.existencia > ROUND(AVG(mov.cantidad)::NUMERIC, 2) * 25
				ORDER BY
					inv.codmp");

				if ($excedentes_existencia)
				{
					$tpl->newBlock('existencias_excedentes');

					foreach ($excedentes_existencia as $row)
					{
						$tpl->newBlock('row_existencia_excedente');

						$tpl->assign('codmp', $row['codmp']);
						$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
						$tpl->assign('consumos', number_format($row['consumos'], 2));
						$tpl->assign('existencia', number_format($row['existencia'], 2));

						$por_diferencia = ($row['existencia'] * 100 / $row['consumos']) - 100;

						$tpl->assign('por_diferencia', number_format($por_diferencia, 2));
						$tpl->assign('por_diferencia_color', $por_diferencia >= 200 ? ' red' : '');
					}
				}

				if ( ! $ok)
				{
					$tpl->assign('_ROOT.disabled', ' disabled="disabled"');

					$tpl->newBlock('leyenda_no_control');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'validar':
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$sql = "INSERT INTO mov_inv_virtual (
				num_cia,
				fecha,
				codmp,
				cod_turno,
				tipo_mov,
				cantidad,
				descripcion
			)
			SELECT
				num_cia,
				fecha,
				codmp,
				cod_turno,
				tipomov,
				CASE
					WHEN codmp = 1 THEN
						cantidad * 44
					ELSE
						cantidad
				END,
				CASE
					WHEN tipomov = FALSE THEN
						'ENTRADA VIRTUAL DE AVIO'
					ELSE
						'SALIDA VIRTUAL DE AVIO'
				END
			FROM
				mov_inv_tmp
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND fecha = '{$_REQUEST['fecha']}'
			ORDER BY
				codmp,
				tipomov;\n";

			$sql .= "INSERT INTO mov_inv_real (
				num_cia,
				fecha,
				codmp,
				cod_turno,
				tipo_mov,
				cantidad,
				descripcion
			)
			SELECT
				num_cia,
				fecha,
				codmp,
				cod_turno,
				tipomov,
				CASE
					WHEN codmp = 1 THEN
						cantidad * 44
					ELSE
						cantidad
				END,
				'SALIDA DE AVIO'
			FROM
				mov_inv_tmp
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND fecha = '{$_REQUEST['fecha']}'
				AND tipomov = TRUE
			ORDER BY
				codmp,
				tipomov;\n";

			$sql .= "UPDATE mov_inv_tmp
			SET
				ts_aut = now(),
				iduser = {$_SESSION['iduser']}
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND fecha = '{$_REQUEST['fecha']}';\n";

			if (isset($_REQUEST['consumo_excedente']))
			{
				foreach ($_REQUEST['consumo_excedente'] as $row)
				{
					$data = json_decode($row);

					$sql .= "INSERT INTO his_aut_con_avio (
						num_cia,
						fecha,
						codmp,
						cod_turno,
						consumo,
						promedio,
						diferencia,
						iduser,
						tsmod,
						status
					) VALUES (
						{$data->num_cia},
						'{$data->fecha}',
						{$data->codmp},
						{$data->turno},
						{$data->consumo},
						{$data->promedio},
						{$data->diferencia},
						{$_SESSION['iduser']},
						NOW(),
						1
					);\n";
				}
			}

			if (isset($_REQUEST['sin_promedio']))
			{
				foreach ($_REQUEST['sin_promedio'] as $row)
				{
					$data = json_decode($row);

					$sql .= "INSERT INTO his_aut_con_avio (
						num_cia,
						fecha,
						codmp,
						cod_turno,
						consumo,
						promedio,
						diferencia,
						iduser,
						tsmod,
						status
					) VALUES (
						{$data->num_cia},
						'{$data->fecha}',
						{$data->codmp},
						{$data->turno},
						{$data->consumo},
						{$data->promedio},
						{$data->diferencia},
						{$_SESSION['iduser']},
						NOW(),
						1
					);\n";
				}
			}

			$db->query($sql);

			include('includes/auxinv.inc.php');

			$sql = ActualizarInventario($_REQUEST['num_cia'], $anio, $mes, NULL, TRUE, TRUE, FALSE, FALSE);

			if ($sql)
			{
				$db->query($sql);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/AvioValidacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
