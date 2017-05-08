<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$_meses = array(
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');

			$condiciones[] = 'ec.cod_mov IN (1, 16, 44, 99)';

			$condiciones[] = "ec.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$sql = "SELECT
				ec.num_cia,
				cc.nombre_corto
					AS nombre_cia,
				EXTRACT(MONTH FROM ec.fecha)
					AS mes,
				SUM(ec.importe)
					AS importe
			FROM
				estado_cuenta ec
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				{$condiciones_string}
			GROUP BY
				ec.num_cia,
				nombre_cia,
				mes
			ORDER BY
				ec.num_cia,
				mes";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/ReporteEfectivosAnualesListado.tpl');
			$tpl->prepare();

			if ($result)
			{
				$datos = array();

				$totales = array_fill(1, 12, 0);
				$promedios = 0;

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						$num_cia = $rec['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia' => utf8_encode($rec['nombre_cia']),
							'importes'   => array_fill(1, 12, 0),
							'info'       => array_fill(1, 12, array())
						);
					}

					$datos[$num_cia]['importes'][$rec['mes']] = floatval($rec['importe']);

					$totales[$rec['mes']] += floatval($rec['importe']);

					$sql = "SELECT
						ec.num_cia,
						EXTRACT(MONTH FROM ec.fecha)
							AS mes,
						ec.fecha,
						ec.cod_mov
							AS cod,
						CASE
							WHEN ec.cuenta = 1 THEN
								(SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = ec.cod_mov LIMIT 1)
							WHEN ec.cuenta = 2 THEN
								(SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = ec.cod_mov LIMIT 1)
							ELSE
								NULL
						END
							AS cod_desc,
						ec.concepto,
						ec.importe,
						CASE
							WHEN ec.cuenta = 1 THEN
								'BANORTE'
							WHEN ec.cuenta = 2 THEN
								'SANTANDER'
						END
							AS banco,
						ec.cuenta
							AS clave_banco
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = ec.num_cia)
					WHERE
						{$condiciones_string} AND ec.num_cia = {$num_cia}
					ORDER BY
						ec.num_cia,
						ec.fecha";

					$depositos = $db->query($sql);

					if ($depositos)
					{
						foreach ($depositos as $dep)
						{
							$datos[$dep['num_cia']]['info'][$dep['mes']][] = '<tr>' . implode('', array(
								'<td align="center">' . $dep['fecha'] . '</td>',
								'<td>' , utf8_encode("{$dep['cod']} {$dep['cod_desc']}") . '</td>',
								'<td>' , utf8_encode($dep['concepto']) . '</td>',
								'<td class="orange">' . utf8_encode($dep['banco']) . '</td>',
								'<td align="right" class="' . ($dep['importe'] < 0 ? 'red' : 'blue') . '">' . number_format($dep['importe'], 2) . '</td>',
							)) . '</tr>';
						}
					}
				}

				$tpl->newBlock('reporte');
				$tpl->assign('anio', $_REQUEST['anio']);

				foreach (range(1, 12) as $mes)
				{
					$tpl->newBlock('mes');
					$tpl->assign('mes', $_meses[$mes]);
				}

				foreach ($datos as $num_cia => $datos_cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);

					foreach ($datos_cia['importes'] as $mes => $importe)
					{
						$tpl->newBlock('importe');
						$tpl->assign('importe', $importe != 0 ? '<a id="info" name="' . htmlentities('<table id="info_tip"><tr><th colspan="5">' . $num_cia . ' ' . $datos_cia['nombre_cia'] . '</th></tr>' . implode('', array_merge((array)('<tr>' . implode('', array(
							'<th>Fecha</th>',
							'<th>C&oacute;digo</th>',
							'<th>Concepto</th>',
							'<th>Banco</th>',
							'<th>Importe</th>',
						)) . '</tr>'), $datos_cia['info'][$mes])) . '<tr><th align="right" colspan="4">Total</th><th align="right">' . number_format($importe, 2) . '</th></tr></table>') . '"' . ($importe < 0 ? ' style="color:#C00;"' : '') . '>' . number_format($importe, 2) . '</a>' : '&nbsp;');
					}

					$tpl->assign('row.total', number_format(array_sum($datos_cia['importes']), 2));
					$tpl->assign('row.promedio', count(array_filter($datos_cia['importes'])) != 0 ? number_format(array_sum($datos_cia['importes']) / count(array_filter($datos_cia['importes'])), 2) : '');

					$promedios += count(array_filter($datos_cia['importes'])) != 0 ? array_sum($datos_cia['importes']) / count(array_filter($datos_cia['importes'])) : 0;
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->newBlock('total_mes');
					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('reporte.total', number_format(array_sum($totales), 2));
				$tpl->assign('reporte.promedio', number_format($promedios, 2));
			}

			$tpl->printToScreen();

			break;

		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'anio = ' . $_REQUEST['anio'];

			$condiciones[] = 'mes <= ' . $_REQUEST['mes'];

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					mes,
					ROUND((utilidad_neta - ingresos_ext)::NUMERIC, 2)
						AS utilidad,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo / 100)::NUMERIC, 2)
						AS porcentaje
				FROM
					balances_pan b
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '

				UNION

				SELECT
					num_cia,
					nombre_corto,
					mes,
					ROUND((utilidad_neta - ingresos_ext)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo / 100)::NUMERIC, 2)
				FROM
					balances_ros b
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '

				ORDER BY
					num_cia,
					mes
			';

			$result = $db->query($sql);

			$data = '';

			if ($result)
			{
				$datos = array();

				$totales = array_fill(1, $_REQUEST['mes'], 0);
				$porcentajes = array_fill(1, $_REQUEST['mes'], 0);
				$promedios = 0;
				$porcentajes_promedio = 0;

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						$num_cia = $rec['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'  => utf8_encode($rec['nombre_cia']),
							'utilidades'  => array_fill(1, $_REQUEST['mes'], 0),
							'porcentajes' => array_fill(1, $_REQUEST['mes'], 0)
						);
					}

					$datos[$num_cia]['utilidades'][$rec['mes']] = floatval($rec['utilidad']);
					$datos[$num_cia]['porcentajes'][$rec['mes']] = floatval($rec['porcentaje']);

					$totales[$rec['mes']] += floatval($rec['utilidad']);
					$porcentajes[$rec['mes']] += floatval($rec['porcentaje']);
				}

				$data .= '';

				$data .= '"Reporte de utilidades netas al mes de ' . $_meses[$_REQUEST['mes']] . ' de ' . $_REQUEST['anio'] . '"' . "\r\n\r\n";

				$data .= utf8_decode('"#","Compañía",');

				foreach (range(1, $_REQUEST['mes']) as $mes)
				{
					$data .= '"Util. ' . $_meses[$mes] . '","Por. ' . $_meses[$mes] . '",';
				}

				$data .= '"Total(U)","Total(P)","Promedio(U)","Promedio(P)"' . "\r\n";

				foreach ($datos as $num_cia => $datos_cia)
				{
					$data .= '"' . $num_cia . '","' . utf8_decode($datos_cia['nombre_cia']) . '",';

					foreach ($datos_cia['utilidades'] as $mes => $utilidad)
					{
						$data .= '"' . ($utilidad != 0 ? number_format($utilidad, 2) : '') . '","' . ($datos_cia['porcentajes'][$mes] != 0 ? number_format($datos_cia['porcentajes'][$mes], 2) : '') . '",';
					}

					$data .= '"' . number_format(array_sum($datos_cia['utilidades']), 2) . '","' . (array_sum($datos_cia['porcentajes']) != 0 ? number_format(array_sum($datos_cia['porcentajes']), 2) : '') . '","' . number_format(array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades'])), 2) . '","' . (count(array_filter($datos_cia['porcentajes'])) > 0 ? number_format(array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])), 2) : '') . '"' . "\r\n";

					$promedios += array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades']));
					$porcentajes_promedio += count(array_filter($datos_cia['porcentajes'])) > 0 ? array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])) : 0;
				}

				$data .= ',"Totales",';

				foreach ($totales as $mes => $total)
				{
					$data .= '"' . number_format($total, 2) . '","' . ($porcentajes[$mes] != 0 ? number_format($porcentajes[$mes], 2) : '') . '",';
				}

				$data .= '"' . number_format(array_sum($totales), 2) . '","' . number_format($promedios, 2) . '",';
				$data .= '"' . number_format(array_sum($porcentajes), 2) . '","' . number_format($porcentajes_promedio, 2) . '"' . "\r\n";
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ReporteUtilidadesNetas' . $_meses[$_REQUEST['mes']] . $_REQUEST['anio'] . '.csv"');

			echo $data;
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteEfectivosAnualesInicio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$admins = $db->query($sql);

if ($admins)
{
	foreach ($admins as $a)
	{
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
