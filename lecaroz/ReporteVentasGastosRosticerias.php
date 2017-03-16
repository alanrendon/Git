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
			$tpl = new TemplatePower('plantillas/bal/ReporteVentasGastosRosticeriasInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected="selected"');

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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "v.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'v.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				v.num_cia,
				cc.nombre_corto AS nombre_cia,
				SUM(venta) AS ventas
			FROM
				total_companias v
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				v.num_cia,
				cc.nombre_corto
			ORDER BY
				v.num_cia,
				cc.nombre_corto");

			if ($result)
			{
				$ventas = array();

				$totales = array(
					'ventas'			=> 0,
					'gastos_1'			=> 0,
					'gastos_2'			=> 0,
					'gastos_3'			=> 0,
					'total_gastos'		=> 0,
					'costo_mat_prima'	=> 0,
					'pollos_chicos'		=> 0,
					'pollos_grandes'	=> 0,
					'pescuezos'			=> 0
				);

				foreach ($result as $row)
				{
					$ventas[$row['num_cia']] = array(
						'num_cia'				=> $row['num_cia'],
						'nombre_cia'			=> $row['nombre_cia'],
						'ventas'				=> $row['ventas'],
						'gastos_1'				=> 0,
						'por_gastos_1'			=> 0,
						'gastos_2'				=> 0,
						'por_gastos_2'			=> 0,
						'gastos_3'				=> 0,
						'costo_mat_prima'		=> 0,
						'por_costo_mat_prima'	=> 0,
						'pollos_chicos'			=> 0,
						'pollos_grandes'		=> 0,
						'pescuezos'				=> 0
					);

					$totales['ventas'] += $row['ventas'];
				}

				$condiciones1 = array();
				$condiciones2 = array();
				$condiciones3 = array();

				$condiciones1[] = "cc.tipo_cia = 2";
				$condiciones2[] = "cc.tipo_cia = 2";
				$condiciones3[] = "cc.tipo_cia = 2";

				$condiciones1[] = "mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones2[] = "c.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones3[] = "gc.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones1[] = "cg.codigo_edo_resultados IN (1, 2)";
				$condiciones2[] = "cg.codigo_edo_resultados IN (1, 2)";

				$condiciones1[] = "mg.codgastos NOT IN (141)";
				$condiciones2[] = "c.codgastos NOT IN (141)";

				$condiciones1[] = "(mg.num_cia, mg.fecha, mg.codgastos, mg.importe) NOT IN (
					SELECT
						num_cia,
						fecha,
						codgastos,
						importe
					FROM
						pagos_otras_cias
						LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
					WHERE
						fecha BETWEEN '2016-01-01' AND '2016-01-31'
						AND fecha_cancelacion IS NULL
				)";

				$condiciones2[] = "ROUND(c.importe::NUMERIC, 2) <> 0";
				$condiciones2[] = "c.fecha_cancelacion IS NULL";

				$condiciones3[] = "gc.clave_balance = TRUE";

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 2, 1, 2007))
				{
					$condiciones1[] = "mg.codgastos NOT IN (140)";
					$condiciones2[] = "c.codgastos NOT IN (140)";
				}

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 1, 1, 2014))
				{
					$condiciones1[] = "mg.codgastos NOT IN (84)";
					$condiciones2[] = "c.codgastos NOT IN (84)";
				}

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
						$condiciones1[] = 'mg.num_cia IN (' . implode(', ', $cias) . ')';
						$condiciones2[] = 'poc.num_cia_aplica IN (' . implode(', ', $cias) . ')';
						$condiciones3[] = 'gc.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones1[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones2[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones3[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string1 = implode(' AND ', $condiciones1);
				$condiciones_string2 = implode(' AND ', $condiciones2);
				$condiciones_string3 = implode(' AND ', $condiciones3);

				$gastos = $db->query("(
					SELECT
						num_cia,
						nombre_cia,
						tipo,
						SUM(importe) AS importe
					FROM
						(
							SELECT
								mg.num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(mg.importe)::NUMERIC, 2) AS importe
							FROM
								movimiento_gastos mg
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc USING (num_cia)
							WHERE
								{$condiciones_string1}
							GROUP BY
								mg.num_cia,
								cc.nombre_corto,
								cg.codigo_edo_resultados

							UNION

							SELECT
								poc.num_cia_aplica AS num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(c.importe)::NUMERIC, 2) AS importe
							FROM
								pagos_otras_cias poc
								LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha)
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc ON (cc.num_cia = poc.num_cia_aplica)
							WHERE
								{$condiciones_string2}
							GROUP BY
								poc.num_cia_aplica,
								cc.nombre_corto,
								cg.codigo_edo_resultados
						) AS result_gastos
					GROUP BY
						num_cia,
						nombre_cia,
						tipo
				)

				UNION

				SELECT
					gc.num_cia,
					cc.nombre_corto AS nombre_cia,
					3 AS tipo,
					ROUND(SUM(
						CASE
							WHEN gc.tipo_mov = TRUE THEN
								-gc.importe
							ELSE
								gc.importe
						END
					)::NUMERIC, 2) AS importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_gastos_caja cgc ON (cgc.id = gc.cod_gastos)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = gc.num_cia)
				WHERE
					{$condiciones_string3}
				GROUP BY
					gc.num_cia,
					cc.nombre_corto,
					tipo

				ORDER BY
					num_cia,
					tipo");

				if ($gastos)
				{
					foreach ($gastos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['gastos_' . $row['tipo']] = $row['importe'];

							$ventas[$row['num_cia']]['por_gastos_' . $row['tipo']] = $row['importe'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['gastos_' . $row['tipo']] += $row['importe'];

							$totales['total_gastos'] += $row['importe'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "bal.anio = {$_REQUEST['anio']}";

				$condiciones[] = "bal.mes = {$_REQUEST['mes']}";

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
						$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$costos_mat_prima = $db->query("SELECT
					bal.num_cia,
					bal.mat_prima_utilizada
				FROM
					balances_ros bal
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				ORDER BY
					num_cia");

				if ($costos_mat_prima)
				{
					foreach ($costos_mat_prima as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['costo_mat_prima'] = $row['mat_prima_utilizada'];

							$ventas[$row['num_cia']]['por_costo_mat_prima'] = $row['mat_prima_utilizada'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['costo_mat_prima'] += $row['mat_prima_utilizada'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "mov.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones[] = "mov.tipo_mov = TRUE";

				$condiciones[] = "mov.codmp IN (160, 573, 297)";

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
						$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$consumos = $db->query("SELECT
					mov.num_cia,
					CASE
						WHEN mov.codmp = 160 THEN
							'pollos_chicos'
						WHEN mov.codmp = 573 THEN
							'pollos_grandes'
						WHEN mov.codmp = 297 THEN
							'pescuezos'
					END AS producto,
					SUM(mov.cantidad) AS cantidad
				FROM
					mov_inv_real mov
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					mov.num_cia,
					producto
				ORDER BY
					mov.num_cia,
					producto");

				if ($consumos)
				{
					foreach ($consumos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']][$row['producto']] = $row['cantidad'];

							$totales[$row['producto']] += $row['cantidad'];
						}
					}
				}

				$tpl = new TemplatePower('plantillas/bal/ReporteVentasGastosRosticeriasConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);
				$tpl->assign('mes', mb_strtoupper($_meses[$_REQUEST['mes']]));

				foreach ($ventas as $num_cia => $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

					$tpl->assign('ventas', $row['ventas'] != 0 ? number_format($row['ventas'], 2) : '&nbsp;');
					$tpl->assign('gastos_1', $row['gastos_1'] != 0 ? '<span class="orange font8" style="float:left;">(' . number_format($row['por_gastos_1'], 2) . '%)</span>&nbsp;&nbsp;' . number_format($row['gastos_1'], 2) : '&nbsp;');
					$tpl->assign('gastos_2', $row['gastos_2'] != 0 ? '<span class="orange font8" style="float:left;">(' . number_format($row['por_gastos_2'], 2) . '%)</span>&nbsp;&nbsp;' . number_format($row['gastos_2'], 2) : '&nbsp;');
					$tpl->assign('gastos_3', $row['gastos_3'] != 0 ? '<span class="orange font8" style="float:left;">(' . number_format($row['por_gastos_3'], 2) . '%)</span>&nbsp;&nbsp;' . number_format($row['gastos_3'], 2) : '&nbsp;');
					$tpl->assign('total_gastos', '<span class="orange font8" style="float:left;">(' . number_format($row['por_gastos_1'] + $row['por_gastos_2'] + $row['por_gastos_3'], 2) . '%)</span>&nbsp;&nbsp;' . number_format($row['gastos_1'] + $row['gastos_2'] + $row['gastos_3'], 2));
					$tpl->assign('costo_mat_prima', $row['costo_mat_prima'] != 0 ? '<span class="orange font8" style="float:left;">(' . number_format($row['por_costo_mat_prima'], 2) . '%)</span>&nbsp;&nbsp;' . number_format($row['costo_mat_prima'], 2) : '&nbsp;');
					$tpl->assign('pollos_chicos', $row['pollos_chicos'] != 0 ? number_format($row['pollos_chicos']) : '&nbsp;');
					$tpl->assign('pollos_grandes', $row['pollos_grandes'] != 0 ? number_format($row['pollos_grandes']) : '&nbsp;');
					$tpl->assign('pescuezos', $row['pescuezos'] != 0 ? number_format($row['pescuezos']) : '&nbsp;');
				}

				foreach ($totales as $campo => $total)
				{
					$tpl->assign('_ROOT.' . $campo, number_format($total, ! in_array($campo, array('pollos_chicos', 'pollos_grandes', 'pescuezos')) ? 2 : 0));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "v.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'v.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				v.num_cia,
				cc.nombre_corto AS nombre_cia,
				SUM(venta) AS ventas
			FROM
				total_companias v
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				v.num_cia,
				cc.nombre_corto
			ORDER BY
				v.num_cia,
				cc.nombre_corto");

			if ($result)
			{
				$ventas = array();

				$totales = array(
					'ventas'			=> 0,
					'gastos_1'			=> 0,
					'gastos_2'			=> 0,
					'gastos_3'			=> 0,
					'total_gastos'		=> 0,
					'costo_mat_prima'	=> 0,
					'pollos_chicos'		=> 0,
					'pollos_grandes'	=> 0,
					'pescuezos'			=> 0
				);

				foreach ($result as $row)
				{
					$ventas[$row['num_cia']] = array(
						'num_cia'				=> $row['num_cia'],
						'nombre_cia'			=> $row['nombre_cia'],
						'ventas'				=> $row['ventas'],
						'gastos_1'				=> 0,
						'por_gastos_1'			=> 0,
						'gastos_2'				=> 0,
						'por_gastos_2'			=> 0,
						'gastos_3'				=> 0,
						'costo_mat_prima'		=> 0,
						'por_costo_mat_prima'	=> 0,
						'pollos_chicos'			=> 0,
						'pollos_grandes'		=> 0,
						'pescuezos'				=> 0
					);

					$totales['ventas'] += $row['ventas'];
				}

				$condiciones1 = array();
				$condiciones2 = array();
				$condiciones3 = array();

				$condiciones1[] = "cc.tipo_cia = 2";
				$condiciones2[] = "cc.tipo_cia = 2";
				$condiciones3[] = "cc.tipo_cia = 2";

				$condiciones1[] = "mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones2[] = "c.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones3[] = "gc.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones1[] = "cg.codigo_edo_resultados IN (1, 2)";
				$condiciones2[] = "cg.codigo_edo_resultados IN (1, 2)";

				$condiciones1[] = "mg.codgastos NOT IN (141)";
				$condiciones2[] = "c.codgastos NOT IN (141)";

				$condiciones1[] = "(mg.num_cia, mg.fecha, mg.codgastos, mg.importe) NOT IN (
					SELECT
						num_cia,
						fecha,
						codgastos,
						importe
					FROM
						pagos_otras_cias
						LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
					WHERE
						fecha BETWEEN '2016-01-01' AND '2016-01-31'
						AND fecha_cancelacion IS NULL
				)";

				$condiciones2[] = "ROUND(c.importe::NUMERIC, 2) <> 0";
				$condiciones2[] = "c.fecha_cancelacion IS NULL";

				$condiciones3[] = "gc.clave_balance = TRUE";

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 2, 1, 2007))
				{
					$condiciones1[] = "mg.codgastos NOT IN (140)";
					$condiciones2[] = "c.codgastos NOT IN (140)";
				}

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 1, 1, 2014))
				{
					$condiciones1[] = "mg.codgastos NOT IN (84)";
					$condiciones2[] = "c.codgastos NOT IN (84)";
				}

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
						$condiciones1[] = 'mg.num_cia IN (' . implode(', ', $cias) . ')';
						$condiciones2[] = 'poc.num_cia_aplica IN (' . implode(', ', $cias) . ')';
						$condiciones3[] = 'gc.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones1[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones2[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones3[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string1 = implode(' AND ', $condiciones1);
				$condiciones_string2 = implode(' AND ', $condiciones2);
				$condiciones_string3 = implode(' AND ', $condiciones3);

				$gastos = $db->query("(
					SELECT
						num_cia,
						nombre_cia,
						tipo,
						SUM(importe) AS importe
					FROM
						(
							SELECT
								mg.num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(mg.importe)::NUMERIC, 2) AS importe
							FROM
								movimiento_gastos mg
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc USING (num_cia)
							WHERE
								{$condiciones_string1}
							GROUP BY
								mg.num_cia,
								cc.nombre_corto,
								cg.codigo_edo_resultados

							UNION

							SELECT
								poc.num_cia_aplica AS num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(c.importe)::NUMERIC, 2) AS importe
							FROM
								pagos_otras_cias poc
								LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha)
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc ON (cc.num_cia = poc.num_cia_aplica)
							WHERE
								{$condiciones_string2}
							GROUP BY
								poc.num_cia_aplica,
								cc.nombre_corto,
								cg.codigo_edo_resultados
						) AS result_gastos
					GROUP BY
						num_cia,
						nombre_cia,
						tipo
				)

				UNION

				SELECT
					gc.num_cia,
					cc.nombre_corto AS nombre_cia,
					3 AS tipo,
					ROUND(SUM(
						CASE
							WHEN gc.tipo_mov = TRUE THEN
								-gc.importe
							ELSE
								gc.importe
						END
					)::NUMERIC, 2) AS importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_gastos_caja cgc ON (cgc.id = gc.cod_gastos)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = gc.num_cia)
				WHERE
					{$condiciones_string3}
				GROUP BY
					gc.num_cia,
					cc.nombre_corto,
					tipo

				ORDER BY
					num_cia,
					tipo");

				if ($gastos)
				{
					foreach ($gastos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['gastos_' . $row['tipo']] = $row['importe'];

							$ventas[$row['num_cia']]['por_gastos_' . $row['tipo']] = $row['importe'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['gastos_' . $row['tipo']] += $row['importe'];

							$totales['total_gastos'] += $row['importe'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "bal.anio = {$_REQUEST['anio']}";

				$condiciones[] = "bal.mes = {$_REQUEST['mes']}";

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
						$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$costos_mat_prima = $db->query("SELECT
					bal.num_cia,
					bal.mat_prima_utilizada
				FROM
					balances_ros bal
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				ORDER BY
					num_cia");

				if ($costos_mat_prima)
				{
					foreach ($costos_mat_prima as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['costo_mat_prima'] = $row['mat_prima_utilizada'];

							$ventas[$row['num_cia']]['por_costo_mat_prima'] = $row['mat_prima_utilizada'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['costo_mat_prima'] += $row['mat_prima_utilizada'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "mov.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones[] = "mov.tipo_mov = TRUE";

				$condiciones[] = "mov.codmp IN (160, 573, 297)";

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
						$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$consumos = $db->query("SELECT
					mov.num_cia,
					CASE
						WHEN mov.codmp = 160 THEN
							'pollos_chicos'
						WHEN mov.codmp = 573 THEN
							'pollos_grandes'
						WHEN mov.codmp = 297 THEN
							'pescuezos'
					END AS producto,
					SUM(mov.cantidad) AS cantidad
				FROM
					mov_inv_real mov
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					mov.num_cia,
					producto
				ORDER BY
					mov.num_cia,
					producto");

				if ($consumos)
				{
					foreach ($consumos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']][$row['producto']] = $row['cantidad'];

							$totales[$row['producto']] += $row['cantidad'];
						}
					}
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

						$this->Cell(0, 5, utf8_decode('REPORTE DE VENTAS Y GASTOS DE ROSTICERÍAS'), 0, 1, 'C');
						$this->Cell(0, 5, utf8_decode(mb_strtoupper($GLOBALS['_meses'][$_REQUEST['mes']]) . " {$_REQUEST['anio']}"), 0, 1, 'C');

						$this->Ln(5);

						$this->Cell(54, 5, utf8_decode('COMPAÑIA'), 1, 0, 'C');

						$this->Cell(34, 5, utf8_decode('VENTAS'), 1, 0, 'C');
						$this->Cell(34, 5, utf8_decode('G. OPERACIÓN'), 1, 0, 'C');
						$this->Cell(34, 5, utf8_decode('G. GENERALES'), 1, 0, 'C');
						$this->Cell(34, 5, utf8_decode('G. CAJA'), 1, 0, 'C');
						$this->Cell(34, 5, utf8_decode('TOTAL GASTOS'), 1, 0, 'C');
						$this->Cell(34, 5, utf8_decode('COSTO MAT. PRIMA'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('P. CHICOS'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('P. GRANDES'), 1, 0, 'C');
						$this->Cell(24, 5, utf8_decode('PESCUEZOS'), 1, 0, 'C');

						$this->Ln();
					}

					function Footer()
					{
						$this->SetY(-7);
						$this->SetFont('ARIAL', '', 8);
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

				$colores = array(
					'blue'		=> array(0, 0, 204),
					'green'		=> array(0, 102, 0),
					'orange'	=> array(255, 51, 0),
					'red'		=> array(204, 0, 0),
					'purple'	=> array(102, 51, 204),
					'grey'		=> array(51, 51, 51)
				);

				foreach ($ventas as $num_cia => $row)
				{
					$pdf->SetFont('ARIAL', 'B', 10);

					$pdf->SetTextColor(0, 0, 0);

					$nombre_cia = "{$num_cia} {$row['nombre_cia']}";

					while ($pdf->GetStringWidth($nombre_cia) > 64)
					{
						$nombre_cia = substr($nombre_cia, 0, strlen($nombre_cia) - 1);
					}

					$pdf->Cell(54, 5, utf8_decode($nombre_cia), 1, 0);

					$pdf->SetTextColor($colores['blue'][0], $colores['blue'][1], $colores['blue'][2]);

					$pdf->Cell(34, 5, $row['ventas'] != 0 ? number_format($row['ventas'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor($colores['orange'][0], $colores['orange'][1], $colores['orange'][2]);

					$pdf->Cell(34, 5, $row['gastos_1'] != 0 ? '(' . number_format($row['por_gastos_1'], 2) . '%) ' . number_format($row['gastos_1'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor($colores['grey'][0], $colores['grey'][1], $colores['grey'][2]);

					$pdf->Cell(34, 5, $row['gastos_2'] != 0 ? '(' . number_format($row['por_gastos_2'], 2) . '%) ' . number_format($row['gastos_2'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor($colores['purple'][0], $colores['purple'][1], $colores['purple'][2]);

					$pdf->Cell(34, 5, $row['gastos_3'] != 0 ? '(' . number_format($row['por_gastos_3'], 2) . '%) ' . number_format($row['gastos_3'], 2) : '', 1, 0, 'R');

					$pdf->SetTextColor($colores['red'][0], $colores['red'][1], $colores['red'][2]);

					$pdf->Cell(34, 5, '(' . number_format($row['por_gastos_1'] + $row['por_gastos_2'] + $row['por_gastos_3'], 2) . '%) ' . number_format($row['gastos_1'] + $row['gastos_2'] + $row['gastos_3'], 2), 1, 0, 'R');

					$pdf->SetTextColor($colores['green'][0], $colores['green'][1], $colores['green'][2]);

					$pdf->Cell(34, 5, $row['costo_mat_prima'] != 0 ? '(' . number_format($row['por_costo_mat_prima'], 2) . '%) ' . number_format($row['costo_mat_prima'], 2) : '', 1, 0, 'R');

					$pdf->Cell(24, 5, $row['pollos_chicos'] != 0 ? number_format($row['pollos_chicos'], 2) : '', 1, 0, 'R');

					$pdf->Cell(24, 5, $row['pollos_grandes'] != 0 ? number_format($row['pollos_grandes'], 2) : '', 1, 0, 'R');

					$pdf->Cell(24, 5, $row['pescuezos'] != 0 ? number_format($row['pescuezos'], 2) : '', 1, 0, 'R');

					$pdf->Ln();
				}

				$pdf->SetTextColor(0, 0, 0);

				$pdf->Cell(54, 5, utf8_decode('TOTALES'), 1, 0, 'R');

				foreach ($totales as $campo => $total)
				{
					$pdf->Cell( ! in_array($campo, array('pollos_chicos', 'pollos_grandes', 'pescuezos')) ? 34 : 24, 5, number_format($total, ! in_array($campo, array('pollos_chicos', 'pollos_grandes', 'pescuezos')) ? 2 : 0), 1, 0, 'R');
				}

				$pdf->Output('reporte-ventas-gastos-rosticerias.pdf', 'I');
			}

			break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "v.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'v.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				v.num_cia,
				cc.nombre_corto AS nombre_cia,
				SUM(venta) AS ventas
			FROM
				total_companias v
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				v.num_cia,
				cc.nombre_corto
			ORDER BY
				v.num_cia,
				cc.nombre_corto");

			if ($result)
			{
				$ventas = array();

				$totales = array(
					'ventas'			=> 0,
					'gastos_1'			=> 0,
					'gastos_2'			=> 0,
					'gastos_3'			=> 0,
					'total_gastos'		=> 0,
					'costo_mat_prima'	=> 0,
					'pollos_chicos'		=> 0,
					'pollos_grandes'	=> 0,
					'pescuezos'			=> 0
				);

				foreach ($result as $row)
				{
					$ventas[$row['num_cia']] = array(
						'num_cia'				=> $row['num_cia'],
						'nombre_cia'			=> $row['nombre_cia'],
						'ventas'				=> $row['ventas'],
						'gastos_1'				=> 0,
						'por_gastos_1'			=> 0,
						'gastos_2'				=> 0,
						'por_gastos_2'			=> 0,
						'gastos_3'				=> 0,
						'costo_mat_prima'		=> 0,
						'por_costo_mat_prima'	=> 0,
						'pollos_chicos'			=> 0,
						'pollos_grandes'		=> 0,
						'pescuezos'				=> 0
					);

					$totales['ventas'] += $row['ventas'];
				}

				$condiciones1 = array();
				$condiciones2 = array();
				$condiciones3 = array();

				$condiciones1[] = "cc.tipo_cia = 2";
				$condiciones2[] = "cc.tipo_cia = 2";
				$condiciones3[] = "cc.tipo_cia = 2";

				$condiciones1[] = "mg.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones2[] = "c.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
				$condiciones3[] = "gc.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones1[] = "cg.codigo_edo_resultados IN (1, 2)";
				$condiciones2[] = "cg.codigo_edo_resultados IN (1, 2)";

				$condiciones1[] = "mg.codgastos NOT IN (141)";
				$condiciones2[] = "c.codgastos NOT IN (141)";

				$condiciones1[] = "(mg.num_cia, mg.fecha, mg.codgastos, mg.importe) NOT IN (
					SELECT
						num_cia,
						fecha,
						codgastos,
						importe
					FROM
						pagos_otras_cias
						LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
					WHERE
						fecha BETWEEN '2016-01-01' AND '2016-01-31'
						AND fecha_cancelacion IS NULL
				)";

				$condiciones2[] = "ROUND(c.importe::NUMERIC, 2) <> 0";
				$condiciones2[] = "c.fecha_cancelacion IS NULL";

				$condiciones3[] = "gc.clave_balance = TRUE";

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 2, 1, 2007))
				{
					$condiciones1[] = "mg.codgastos NOT IN (140)";
					$condiciones2[] = "c.codgastos NOT IN (140)";
				}

				if (mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) >= mktime(0, 0, 0, 1, 1, 2014))
				{
					$condiciones1[] = "mg.codgastos NOT IN (84)";
					$condiciones2[] = "c.codgastos NOT IN (84)";
				}

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
						$condiciones1[] = 'mg.num_cia IN (' . implode(', ', $cias) . ')';
						$condiciones2[] = 'poc.num_cia_aplica IN (' . implode(', ', $cias) . ')';
						$condiciones3[] = 'gc.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones1[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones2[] = "cc.idadministrador = {$_REQUEST['admin']}";
					$condiciones3[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string1 = implode(' AND ', $condiciones1);
				$condiciones_string2 = implode(' AND ', $condiciones2);
				$condiciones_string3 = implode(' AND ', $condiciones3);

				$gastos = $db->query("(
					SELECT
						num_cia,
						nombre_cia,
						tipo,
						SUM(importe) AS importe
					FROM
						(
							SELECT
								mg.num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(mg.importe)::NUMERIC, 2) AS importe
							FROM
								movimiento_gastos mg
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc USING (num_cia)
							WHERE
								{$condiciones_string1}
							GROUP BY
								mg.num_cia,
								cc.nombre_corto,
								cg.codigo_edo_resultados

							UNION

							SELECT
								poc.num_cia_aplica AS num_cia,
								cc.nombre_corto AS nombre_cia,
								cg.codigo_edo_resultados AS tipo,
								ROUND(SUM(c.importe)::NUMERIC, 2) AS importe
							FROM
								pagos_otras_cias poc
								LEFT JOIN cheques c USING (num_cia, folio, cuenta, fecha)
								LEFT JOIN catalogo_gastos cg USING (codgastos)
								LEFT JOIN catalogo_companias cc ON (cc.num_cia = poc.num_cia_aplica)
							WHERE
								{$condiciones_string2}
							GROUP BY
								poc.num_cia_aplica,
								cc.nombre_corto,
								cg.codigo_edo_resultados
						) AS result_gastos
					GROUP BY
						num_cia,
						nombre_cia,
						tipo
				)

				UNION

				SELECT
					gc.num_cia,
					cc.nombre_corto AS nombre_cia,
					3 AS tipo,
					ROUND(SUM(
						CASE
							WHEN gc.tipo_mov = TRUE THEN
								-gc.importe
							ELSE
								gc.importe
						END
					)::NUMERIC, 2) AS importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_gastos_caja cgc ON (cgc.id = gc.cod_gastos)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = gc.num_cia)
				WHERE
					{$condiciones_string3}
				GROUP BY
					gc.num_cia,
					cc.nombre_corto,
					tipo

				ORDER BY
					num_cia,
					tipo");

				if ($gastos)
				{
					foreach ($gastos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['gastos_' . $row['tipo']] = $row['importe'];

							$ventas[$row['num_cia']]['por_gastos_' . $row['tipo']] = $row['importe'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['gastos_' . $row['tipo']] += $row['importe'];

							$totales['total_gastos'] += $row['importe'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "bal.anio = {$_REQUEST['anio']}";

				$condiciones[] = "bal.mes = {$_REQUEST['mes']}";

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
						$condiciones[] = 'bal.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$costos_mat_prima = $db->query("SELECT
					bal.num_cia,
					bal.mat_prima_utilizada
				FROM
					balances_ros bal
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				ORDER BY
					num_cia");

				if ($costos_mat_prima)
				{
					foreach ($costos_mat_prima as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']]['costo_mat_prima'] = $row['mat_prima_utilizada'];

							$ventas[$row['num_cia']]['por_costo_mat_prima'] = $row['mat_prima_utilizada'] * 100 / $ventas[$row['num_cia']]['ventas'];

							$totales['costo_mat_prima'] += $row['mat_prima_utilizada'];
						}
					}
				}

				$condiciones = array();

				$condiciones[] = "mov.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

				$condiciones[] = "mov.tipo_mov = TRUE";

				$condiciones[] = "mov.codmp IN (160, 573, 297)";

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
						$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				{
					$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
				}

				$condiciones_string = implode(' AND ', $condiciones);

				$consumos = $db->query("SELECT
					mov.num_cia,
					CASE
						WHEN mov.codmp = 160 THEN
							'pollos_chicos'
						WHEN mov.codmp = 573 THEN
							'pollos_grandes'
						WHEN mov.codmp = 297 THEN
							'pescuezos'
					END AS producto,
					SUM(mov.cantidad) AS cantidad
				FROM
					mov_inv_real mov
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					{$condiciones_string}
				GROUP BY
					mov.num_cia,
					producto
				ORDER BY
					mov.num_cia,
					producto");

				if ($consumos)
				{
					foreach ($consumos as $row)
					{
						if (isset($ventas[$row['num_cia']]))
						{
							$ventas[$row['num_cia']][$row['producto']] = $row['cantidad'];

							$totales[$row['producto']] += $row['cantidad'];
						}
					}
				}

				$string = '"","REPORTE DE VENTAS Y GASTOS DE ROSTICERÍAS"' . "\n";
				$string .= '"","' . mb_strtoupper($GLOBALS['_meses'][$_REQUEST['mes']]) . ' DE ' .$_REQUEST['anio'] . '"' . "\n\n";

				$string .= '"#","COMPAÑIA","VENTAS","G. OPERACION","%","G. GENERALES","%","G. CAJA","%","TOTAL GASTOS","%","COSTO MAT. PRIMA","%"' . "\n";

				foreach ($ventas as $num_cia => $row)
				{
					$string .= '"' . $num_cia . '","' . utf8_encode($row['nombre_cia']) . '",';
					$string .= '"' . ($row['ventas'] != 0 ? number_format($row['ventas'], 2) : '0') . '",';
					$string .= '"' . ($row['gastos_1'] != 0 ? number_format($row['gastos_1'], 2) : '0') . '",';
					$string .= '"' . ($row['por_gastos_1'] != 0 ? number_format($row['por_gastos_1'], 2) : '0') . '",';
					$string .= '"' . ($row['gastos_2'] != 0 ? number_format($row['gastos_2'], 2) : '0') . '",';
					$string .= '"' . ($row['por_gastos_2'] != 0 ? number_format($row['por_gastos_2'], 2) : '0') . '",';
					$string .= '"' . ($row['gastos_3'] != 0 ? number_format($row['gastos_3'], 2) : '0') . '",';
					$string .= '"' . ($row['por_gastos_3'] != 0 ? number_format($row['por_gastos_3'], 2) : '0') . '",';
					$string .= '"' . number_format($row['gastos_1'] + $row['gastos_2'] + $row['gastos_3'], 2) . '",';
					$string .= '"' . number_format($row['por_gastos_1'] + $row['por_gastos_2'] + $row['por_gastos_3'], 2) . '",';
					$string .= '"' . ($row['costo_mat_prima'] != 0 ? number_format($row['costo_mat_prima'], 2) : '0') . '",';
					$string .= '"' . ($row['por_costo_mat_prima'] != 0 ? number_format($row['por_costo_mat_prima'], 2) : '0') . '",';
					$string .= '"' . ($row['pollos_chicos'] != 0 ? number_format($row['pollos_chicos']) : '0') . '",';
					$string .= '"' . ($row['pollos_grandes'] != 0 ? number_format($row['pollos_grandes']) : '0') . '",';
					$string .= '"' . ($row['pescuezos'] != 0 ? number_format($row['pescuezos']) : '0') . '"' . "\n";
				}

				$string .= '"","TOTALES",';

				$string .= '"' . ($totales['ventas'] != 0 ? number_format($totales['ventas'], 2) : '0') . '",';
				$string .= '"' . ($totales['gastos_1'] != 0 ? number_format($totales['gastos_1'], 2) : '0') . '",';
				$string .= '"",';
				$string .= '"' . ($totales['gastos_2'] != 0 ? number_format($totales['gastos_2'], 2) : '0') . '",';
				$string .= '"",';
				$string .= '"' . ($totales['gastos_3'] != 0 ? number_format($totales['gastos_3'], 2) : '0') . '",';
				$string .= '"",';
				$string .= '"' . number_format($totales['total_gastos'], 2) . '",';
				$string .= '"",';
				$string .= '"' . ($totales['costo_mat_prima'] != 0 ? number_format($totales['costo_mat_prima'], 2) : '0') . '",';
				$string .= '"",';
				$string .= '"' . ($totales['pollos_chicos'] != 0 ? number_format($totales['pollos_chicos']) : '0') . '",';
				$string .= '"' . ($totales['pollos_grandes'] != 0 ? number_format($totales['pollos_grandes']) : '0') . '",';
				$string .= '"' . ($totales['pescuezos'] != 0 ? number_format($totales['pescuezos']) : '0') . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=reporte-ventas-gastos-rosticerias.csv');

				echo $string;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteVentasGastosRosticerias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
