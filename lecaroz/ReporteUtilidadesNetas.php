<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

function roundBetter($number, $precision = 0, $mode = PHP_ROUND_HALF_UP, $direction = NULL) {
	if (!isset($direction) || is_null($direction)) {
		return round($number, $precision, $mode);
	} else {
		$factor = pow(10, -1 * $precision);
		
		return strtolower(substr($direction, 0, 1)) == 'd'
			? floor($number / $factor) * $factor
			: ceil($number / $factor) * $factor;
	}
}

function roundBetterUp($number, $precision = 0, $mode = PHP_ROUND_HALF_UP) {
	return roundBetter($number, $precision, $mode, 'up');
}

function roundBetterDown($number, $precision = 0, $mode = PHP_ROUND_HALF_UP) {
	return roundBetter($number, $precision, $mode, 'down');
}

function round_down($value) {
	return roundBetterDown($value, -3);
}

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
		case 'reporte':
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
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
						AS porcentaje,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_1 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_1 / 100)::NUMERIC, 2)
						AS porcentaje_1,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_2 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_2 / 100)::NUMERIC, 2)
						AS porcentaje_2,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_3 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_3 / 100)::NUMERIC, 2)
						AS porcentaje_3,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_4 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_4 / 100)::NUMERIC, 2)
						AS porcentaje_4
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
					ROUND(((utilidad_neta - ingresos_ext) * por_bg / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_1 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_1 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_2 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_2 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_3 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_3 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_4 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_4 / 100)::NUMERIC, 2)
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
			
			$tpl = new TemplatePower('plantillas/bal/ReporteUtilidadesNetasListado.tpl');
			$tpl->prepare();
			
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
					$datos[$num_cia]['porcentajes'][$rec['mes']] = floatval($rec['porcentaje' . (isset($_REQUEST['tipo_bg']) ? $_REQUEST['tipo_bg'] : '')]);
					
					$totales[$rec['mes']] += floatval($rec['utilidad']);
					$porcentajes[$rec['mes']] += floatval($rec['porcentaje' . (isset($_REQUEST['tipo_bg']) ? $_REQUEST['tipo_bg'] : '')]);
				}
				
				$tpl->newBlock('reporte');
				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
				$tpl->assign('anio', $_REQUEST['anio']);
				
				foreach (range(1, $_REQUEST['mes']) as $mes) {
					$tpl->newBlock('mes');
					$tpl->assign('mes', $_meses[$mes]);
				}
				
				foreach ($datos as $num_cia => $datos_cia) {
					if (round(array_sum($datos_cia['utilidades']), 2)  == 0)
					{
						continue;
					}

					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);
					
					foreach ($datos_cia['utilidades'] as $mes => $utilidad) {
						$tpl->newBlock('utilidad');
						$tpl->assign('utilidad', $utilidad != 0 ? number_format($utilidad, 2) : '&nbsp;');
						$tpl->assign('porcentaje', $datos_cia['porcentajes'][$mes] != 0 ? number_format($datos_cia['porcentajes'][$mes], 2) : '&nbsp;');
					}
					
					$tpl->assign('row.total', number_format(array_sum($datos_cia['utilidades']), 2));
					$tpl->assign('row.porcentaje_total', array_sum($datos_cia['porcentajes']) != 0 ? number_format(array_sum($datos_cia['porcentajes']), 2) : '&nbsp;');
					$tpl->assign('row.promedio', number_format(array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades'])), 2));
					$tpl->assign('row.porcentaje_promedio', count(array_filter($datos_cia['porcentajes'])) > 0 ? number_format(array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])), 2) : '&nbsp;');
					
					$promedios += array_sum($datos_cia['utilidades']) / count(array_filter($datos_cia['utilidades']));
					$porcentajes_promedio += count(array_filter($datos_cia['porcentajes'])) > 0 ? array_sum($datos_cia['porcentajes']) / count(array_filter($datos_cia['porcentajes'])) : 0;
				}
				
				foreach ($totales as $mes => $total) {
					$tpl->newBlock('total_mes');
					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
					$tpl->assign('porcentaje', $porcentajes[$mes] != 0 ? number_format($porcentajes[$mes], 2) : '&nbsp;');
				}
				
				$tpl->assign('reporte.total', number_format(array_sum($totales), 2));
				$tpl->assign('reporte.promedio', number_format($promedios, 2));
				$tpl->assign('reporte.porcentaje_total', number_format(array_sum($porcentajes), 2));
				$tpl->assign('reporte.porcentaje_promedio', number_format($porcentajes_promedio, 2));
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
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
						AS porcentaje,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_1 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_1 / 100)::NUMERIC, 2)
						AS porcentaje_1,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_2 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_2 / 100)::NUMERIC, 2)
						AS porcentaje_2,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_3 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_3 / 100)::NUMERIC, 2)
						AS porcentaje_3,
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_4 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_4 / 100)::NUMERIC, 2)
						AS porcentaje_4
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
					ROUND(((utilidad_neta - ingresos_ext) * por_bg / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_1 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_1 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_2 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_2 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_3 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_3 / 100)::NUMERIC, 2),
					ROUND(((utilidad_neta - ingresos_ext) * por_bg_4 / 100)::NUMERIC, 2) + ROUND(((efectivo) * por_efectivo_4 / 100)::NUMERIC, 2)
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
					$datos[$num_cia]['porcentajes'][$rec['mes']] = floatval($rec['porcentaje' . (isset($_REQUEST['tipo_bg']) ? $_REQUEST['tipo_bg'] : '')]);
					
					$totales[$rec['mes']] += floatval($rec['utilidad']);
					$porcentajes[$rec['mes']] += floatval($rec['porcentaje' . (isset($_REQUEST['tipo_bg']) ? $_REQUEST['tipo_bg'] : '')]);
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

$tpl = new TemplatePower('plantillas/bal/ReporteUtilidadesNetas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$tpl->assign(date('n'), ' selected');

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

if ($admins) {
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
