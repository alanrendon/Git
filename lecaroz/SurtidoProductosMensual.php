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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'obtener_mp':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}
			
			break;
		
		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$dias = date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$condiciones = array();
			
			$condiciones[] = 'mov.codmp = ' . $_REQUEST['codmp'];
			
			$condiciones[] = 'mov.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = "mov.tipo_mov = FALSE";

			$condiciones[] = "mov.descripcion != 'DIFERENCIA INVENTARIO'";
			
			$condiciones[] = "mov.descripcion NOT LIKE 'TRASPASO DE AVIO%'";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					mov.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					EXTRACT(DAY FROM mov.fecha)
						AS dia,
					SUM(mov.cantidad)
						AS cantidad
				FROM
					mov_inv_real mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					mov.num_cia,
					nombre_cia,
					dia
				ORDER BY
					mov.num_cia,
					dia
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/SurtidoProductosMensualListado.tpl');
			$tpl->prepare();
			
			if ($result) {
				$datos = array();
				
				$totales = array_fill(1, $dias, 0);
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$datos[$num_cia] = array(
							'nombre_cia' => utf8_encode($rec['nombre_cia']),
							'cantidades'   => array_fill(1, $dias, 0)
						);
					}
					
					$datos[$num_cia]['cantidades'][$rec['dia']] = floatval($rec['cantidad']);
					
					$totales[$rec['dia']] += floatval($rec['cantidad']);
				}
				
				$tpl->newBlock('reporte');
				$tpl->assign('producto', $_REQUEST['codmp'] . ' ' . $_REQUEST['nombre_mp']);
				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
				$tpl->assign('anio', $_REQUEST['anio']);
				
				foreach (range(1, $dias) as $dia) {
					$tpl->newBlock('dia');

					$dia_semana = date('w', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']));

					$tpl->assign('dia', $dia);
					$tpl->assign('dia_semana', $_dias[$dia_semana]);
				}
				
				foreach ($datos as $num_cia => $datos_cia) {
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);
					
					foreach ($datos_cia['cantidades'] as $mes => $cantidad) {
						$tpl->newBlock('cantidad');
						$tpl->assign('cantidad', $cantidad != 0 ? '<span class="' . ($cantidad < 0 ? 'red' : 'blue') . '">' . number_format($cantidad, 2) . '</span>' : '&nbsp;');
					}
					
					$tpl->assign('row.total', number_format(array_sum($datos_cia['cantidades']), 2));
				}
				
				foreach ($totales as $dia => $total) {
					$tpl->newBlock('total_dia');
					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}
				
				$tpl->assign('reporte.total', number_format(array_sum($totales), 2));
			}
			
			$tpl->printToScreen();
			
			break;
		
		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$condiciones = array();
			
			$condiciones[] = 'anio = ' . $_REQUEST['anio'];
			
			$condiciones[] = 'mes <= ' . $_REQUEST['mes'];
			
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $omitir_cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
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
			
			if ($result) {
				$datos = array();
				
				$totales = array_fill(1, $_REQUEST['mes'], 0);
				$porcentajes = array_fill(1, $_REQUEST['mes'], 0);
				$promedios = 0;
				$porcentajes_promedio = 0;
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
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
				
				foreach (range(1, $_REQUEST['mes']) as $mes) {
					$data .= '"Util. ' . $_meses[$mes] . '","Por. ' . $_meses[$mes] . '",';
				}
				
				$data .= '"Total(U)","Total(P)","Promedio(U)","Promedio(P)"' . "\r\n";
				
				foreach ($datos as $num_cia => $datos_cia) {
					$data .= '"' . $num_cia . '","' . utf8_decode($datos_cia['nombre_cia']) . '",';
					
					foreach ($datos_cia['utilidades'] as $mes => $utilidad) {
						$data .= '"' . ($utilidad != 0 ? number_format($utilidad, 2) : '') . '","' . ($datos_cia['porcentajes'][$mes] != 0 ? number_format($datos_cia['porcentajes'][$mes], 2) : '') . '",';
					}
					
					$data .= '"' . number_format(array_sum($datos_cia['utilidades']), 2) . '","' . (array_sum($datos_cia['porcentajes']) != 0 ? number_format(array_sum($datos_cia['porcentajes']), 2) : '') . '","' . number_format(array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades'])), 2) . '","' . (count(array_filter($datos_cia['porcentajes'])) > 0 ? number_format(array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])), 2) : '') . '"' . "\r\n";
					
					$promedios += array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades']));
					$porcentajes_promedio += count(array_filter($datos_cia['porcentajes'])) > 0 ? array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])) : 0;
				}
				
				$data .= ',"Totales",';
				
				foreach ($totales as $mes => $total) {
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

$tpl = new TemplatePower('plantillas/bal/SurtidoProductosMensual.tpl');
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

$sql = '
	SELECT
		mes
			AS value,
		nombre
			AS text
	FROM
		meses
	ORDER BY
		value
';

$meses = $db->query($sql);

if ($meses)
{
	foreach ($meses as $m)
	{
		$tpl->newBlock('mes');
		$tpl->assign('value', $m['value']);
		$tpl->assign('text', utf8_encode($m['text']));

		if ($m['value'] == date('n'))
		{
			$tpl->assign('selected', ' selected="selected"');
		}
	}
}

$tpl->printToScreen();
