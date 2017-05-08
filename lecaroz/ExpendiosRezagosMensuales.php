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

function array_number_format($value) {
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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes2'] + 1, 0, $_REQUEST['anio2']));
			
			$ts1 = mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']);
			$ts1 = mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']);
			
			$meses = ($_REQUEST['anio2'] * 12 + $_REQUEST['mes2']) - ($_REQUEST['anio1'] * 12 + $_REQUEST['mes1']) + 1;
			
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \''  . $fecha2 . '\'';
			
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
					num_expendio
						AS num_exp,
					nombre_expendio
						AS nombre_exp,
					EXTRACT(YEAR FROM fecha)
						AS anio,
					EXTRACT(MONTH FROM fecha)
						AS mes,
					rezago_anterior
						AS rezago
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
					AND (num_cia, num_expendio, nombre_expendio, fecha) IN (
						SELECT
							num_cia,
							num_expendio,
							nombre_expendio,
							fecha
						FROM
							(
								SELECT
									num_cia,
									num_expendio,
									nombre_expendio,
									MIN(fecha)
										AS fecha,
									EXTRACT(YEAR FROM fecha)
										AS anio,
									EXTRACT(MONTH FROM fecha)
										AS mes
								FROM
									mov_expendios mov
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									num_expendio,
									nombre_expendio,
									anio,
									mes
							) AS dias
					)
				ORDER BY
					num_cia,
					num_exp,
					nombre_exp,
					anio,
					mes
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ExpendiosRezagosMensualesReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$datos = array();
				
				$rango_meses = array();
				
				for ($i = 0; $i < $meses; $i++) {
					$rango_meses[] = date('Y-n', strtotime($_REQUEST['anio1'] . '/' . $_REQUEST['mes1'] . '/01 + ' . $i . ' months'));
				}
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$datos[$num_cia] = array(
							'nombre_cia' => utf8_encode($rec['nombre_cia']),
							'expendios'  => array(),
							'totales'    => array_fill_keys($rango_meses, 0)
						);
						
						$nombre_exp = NULL;
					}
					
					if ($nombre_exp != $rec['nombre_exp']) {
						$nombre_exp = $rec['nombre_exp'];
						
						$datos[$num_cia]['expendios'][$nombre_exp] = array(
							'num_exp'     => $rec['num_exp'],
							'rezagos' => array_fill_keys($rango_meses, 0)
						);
					}
					
					$datos[$num_cia]['expendios'][$nombre_exp]['rezagos'][$rec['anio'] . '-' . $rec['mes']] = $rec['rezago'];
					
					$datos[$num_cia]['totales'][$rec['anio'] . '-' . $rec['mes']] += $rec['rezago'];
				}
				
				foreach ($datos as $num_cia => $datos_cia) {
					$tpl->newBlock('reporte');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);
					
					$tpl->assign('mes1', $_meses[$_REQUEST['mes1']]);
					$tpl->assign('anio1', $_REQUEST['anio1']);
					
					$tpl->assign('mes2', $_meses[$_REQUEST['mes2']]);
					$tpl->assign('anio2', $_REQUEST['anio2']);
					
					foreach ($rango_meses as $mes) {
						$tpl->newBlock('mes');
						
						$pieces = explode('-', $mes);
						
						$tpl->assign('mes', substr($_meses[$pieces[1]], 0, 3) . substr($pieces[0], -2));
					}
					
					foreach ($datos_cia['expendios'] as $nombre_exp => $datos_exp) {
						$tpl->newBlock('expendio');
						
						$tpl->assign('num_exp', $datos_exp['num_exp']);
						$tpl->assign('nombre_exp', utf8_encode($nombre_exp));
						
						foreach ($datos_exp['rezagos'] as $mes => $rezago) {
							$tpl->newBlock('rezago');
							
							$tpl->assign('rezago', $rezago != 0 ? number_format($rezago, 2) : '&nbsp;');
						}
					}
					
					
					foreach ($datos_cia['totales'] as $mes => $total) {
						$tpl->newBlock('total');
						
						$tpl->assign('total', number_format($total, 2));
					}
					
					$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'exportar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes2'] + 1, 0, $_REQUEST['anio2']));
			
			$ts1 = mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']);
			$ts1 = mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1']);
			
			$meses = ($_REQUEST['anio2'] * 12 + $_REQUEST['mes2']) - ($_REQUEST['anio1'] * 12 + $_REQUEST['mes1']) + 1;
			
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \''  . $fecha2 . '\'';
			
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
					num_expendio
						AS num_exp,
					nombre_expendio
						AS nombre_exp,
					EXTRACT(YEAR FROM fecha)
						AS anio,
					EXTRACT(MONTH FROM fecha)
						AS mes,
					rezago_anterior
						AS rezago
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
					AND (num_cia, num_expendio, nombre_expendio, fecha) IN (
						SELECT
							num_cia,
							num_expendio,
							nombre_expendio,
							fecha
						FROM
							(
								SELECT
									num_cia,
									num_expendio,
									nombre_expendio,
									MIN(fecha)
										AS fecha,
									EXTRACT(YEAR FROM fecha)
										AS anio,
									EXTRACT(MONTH FROM fecha)
										AS mes
								FROM
									mov_expendios mov
								WHERE
									' . implode(' AND ', $condiciones) . '
								GROUP BY
									num_cia,
									num_expendio,
									nombre_expendio,
									anio,
									mes
							) AS dias
					)
				ORDER BY
					num_cia,
					num_exp,
					nombre_exp,
					anio,
					mes
			';
			
			$result = $db->query($sql);
			
			$data = '';
			
			if ($result) {
				function Format($value) {
					return $value != 0 ? number_format($value, 2) : '';
				}
				
				$datos = array();
				
				$rango_meses = array();
				
				for ($i = 0; $i < $meses; $i++) {
					$rango_meses[] = date('Y-n', strtotime($_REQUEST['anio1'] . '/' . $_REQUEST['mes1'] . '/01 + ' . $i . ' months'));
				}
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$datos[$num_cia] = array(
							'nombre_cia' => utf8_encode($rec['nombre_cia']),
							'expendios'  => array(),
							'totales'    => array_fill_keys($rango_meses, 0)
						);
						
						$nombre_exp = NULL;
					}
					
					if ($nombre_exp != $rec['nombre_exp']) {
						$nombre_exp = $rec['nombre_exp'];
						
						$datos[$num_cia]['expendios'][$nombre_exp] = array(
							'num_exp'     => $rec['num_exp'],
							'rezagos' => array_fill_keys($rango_meses, 0)
						);
					}
					
					$datos[$num_cia]['expendios'][$nombre_exp]['rezagos'][$rec['anio'] . '-' . $rec['mes']] = $rec['rezago'];
					
					$datos[$num_cia]['totales'][$rec['anio'] . '-' . $rec['mes']] += $rec['rezago'];
				}
				
				$data .= '"Rezagos mensuales de expendios de ' . $_meses[$_REQUEST['mes1']] . ' de ' . $_REQUEST['anio1'] . ' a ' . $_meses[$_REQUEST['mes2']] . ' de ' . $_REQUEST['anio2'] . '"' . "\r\n\r\n";
				
				foreach ($datos as $num_cia => $datos_cia) {
					$data .= '"' . $num_cia . '","' . $datos_cia['nombre_cia'] . '"' . "\r\n\r\n";
					
					$data .= '"#","Expendio"';
					
					foreach ($rango_meses as $mes) {
						$pieces = explode('-', $mes);
						
						$data .= ',"' . substr($_meses[$pieces[1]], 0, 3) . substr($pieces[0], -2) . '"';
					}
					
					$data .= "\r\n";
					
					foreach ($datos_cia['expendios'] as $nombre_exp => $datos_exp) {
						$data .= '"' . $datos_exp['num_exp'] . '","' . $nombre_exp . '","' . implode('","', array_map('array_number_format', $datos_exp['rezagos'])) . '"' . "\r\n";
					}
					
					$data .= ',"Totales","' . implode('","', array_map('array_number_format', $datos_cia['totales'])) . '"' . "\r\n";
				}
				
				$data .= "\r\n";
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="RezagosMensualesExpendios.csv"');
			
			echo $data;
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ExpendiosRezagosMensuales.tpl');
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
