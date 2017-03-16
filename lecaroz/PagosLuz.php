<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

function filter($value) {
	return $value != 0;
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
			
			$condiciones[] = 'c.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'c.codgastos = 12';
			
			$condiciones[] = 'c.fecha_cancelacion IS NULL';
			
			$condiciones[] = 'c.importe > 0';
			
			$condiciones[] = 'c.concepto NOT LIKE \'%CTA%\'';
			
			$condiciones[] = 'c.concepto NOT LIKE \'%CUENTA%\'';
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
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
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					c.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					EXTRACT(YEAR FROM c.fecha)
						AS anio,
					EXTRACT(MONTH FROM c.fecha)
						AS mes,
					SUM(ec.importe)
						AS importe
				FROM
					cheques c
					LEFT JOIN estado_cuenta ec
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					c.num_cia,
					nombre_cia,
					anio,
					mes
				ORDER BY
					c.num_cia,
					anio,
					mes
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/PagosLuzReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						
						$anio = NULL;
						
						$totales = array(
							1  => 0,
							2  => 0,
							3  => 0,
							4  => 0,
							5  => 0,
							6  => 0,
							7  => 0,
							8  => 0,
							9  => 0,
							10 => 0,
							11 => 0,
							12 => 0
						);
					}
					
					if ($anio != $rec['anio']) {
						$anio = $rec['anio'];
						
						$tpl->newBlock('row');
						$tpl->assign('anio', $anio);
						
						$total_anio = 0;
						
						$mes = 0;
					}
					
					$tpl->assign($rec['mes'], number_format($rec['importe'], 2));
					
					$total_anio += $rec['importe'];
					
					$totales[$rec['mes']] += $rec['importe'];
					
					$tpl->assign('total', number_format($total_anio, 2));
					
					$tpl->assign('promedio', number_format($total_anio / $rec['mes'], 2));
					
					$tpl->assign('reporte.' . $rec['mes'], number_format($totales[$rec['mes']], 2));
					
					$tpl->assign('reporte.total', number_format(array_sum($totales), 2));
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'exportar':
			$condiciones = array();
			
			$condiciones[] = 'c.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			$condiciones[] = 'c.codgastos = 12';
			
			$condiciones[] = 'c.fecha_cancelacion IS NULL';
			
			$condiciones[] = 'c.importe > 0';
			
			$condiciones[] = 'c.concepto NOT LIKE \'%CTA%\'';
			
			$condiciones[] = 'c.concepto NOT LIKE \'%CUENTA%\'';
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
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
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					c.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					EXTRACT(YEAR FROM c.fecha)
						AS anio,
					EXTRACT(MONTH FROM c.fecha)
						AS mes,
					c.fecha,
					ec.fecha_con
						AS cobrado,
					CASE
						WHEN c.cuenta = 1 THEN
							\'BANORTE\'
						WHEN c.cuenta = 2 THEN
							\'SANTANDER\'
					END
						AS banco,
					c.folio,
					ec.importe
				FROM
					cheques c
					LEFT JOIN estado_cuenta ec
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					c.num_cia,
					c.fecha
			';
			
			$result = $db->query($sql);
			
			$data = '';
			
			if ($result) {
				$data .= '"REPORTE DE PAGOS DE LUZ"';
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$data .= ',,,"TOTAL MES","' . number_format($total_mes, 2) . '"' . "\r\n";
							$data .= ',,,"TOTAL PERIODO","' . number_format($total_cia, 2) . '"';
						}
						
						$num_cia = $rec['num_cia'];
						
						$data .= "\r\n\r\n" . '"' . $num_cia . ' ' . $rec['nombre_cia'] . '"' . "\r\n\r\n";
						
						$data .= '"FECHA","COBRADO","BANCO","FOLIO","IMPORTE"' . "\r\n";
						
						$anio = NULL;
						$mes = NULL;
						
						$total_cia = 0;
					}
					
					if ($anio != $rec['anio'] || $mes != $rec['mes']) {
						if ($mes != NULL) {
							$data .= ',,,"TOTAL MES","' . number_format($total_mes, 2) . '"' . "\r\n";
						}
						
						$anio = $rec['anio'];
						$mes = $rec['mes'];
						
						$data .= '"' . $_meses[$mes] . ' ' . $anio . '"' . "\r\n";
						
						$total_mes = 0;
					}
					
					$data .= '"' . $rec['fecha'] . '","' . $rec['cobrado'] . '","' . $rec['banco'] . '","' . $rec['folio'] . '","' . number_format($rec['importe'], 2) . '"' . "\r\n";
					
					$total_mes += $rec['importe'];
					$total_cia += $rec['importe'];
				}
			}
			
			if ($num_cia != NULL) {
				$data .= ',,,"TOTAL MES","' . number_format($total_mes, 2) . '"' . "\r\n";
				$data .= ',,,"TOTAL PERIODO","' . number_format($total_cia, 2) . '"';
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="PagosLuz.csv"');
			
			echo utf8_encode(utf8_decode($data));
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/PagosLuz.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if (!$isIpad) {
	$tpl->assign(date('n'), ' selected');
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
	
	if ($admins) {
		foreach ($admins as $a) {
			$tpl->newBlock('admin_1');
			$tpl->assign('value', $a['value']);
			$tpl->assign('text', utf8_encode($a['text']));
		}
	}
}
else {
	$tpl->assign(date('n'), ' selected');
	$tpl->assign('anio', date('Y'));
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37))) {
		$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
	}
	
	$sql = '
		SELECT
			num_cia
				AS value,
			nombre_corto
				AS text
		FROM
			catalogo_companias
			LEFT JOIN catalogo_administradores
				USING (idadministrador)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	
	$cias = $db->query($sql);
	
	if ($cias) {
		foreach ($cias as $c) {
			$tpl->newBlock('cia');
			$tpl->assign('value', $c['value']);
			$tpl->assign('text', utf8_encode($c['text']));
		}
	}
	
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
			$tpl->newBlock('admin_2');
			$tpl->assign('value', $a['value']);
			$tpl->assign('text', utf8_encode($a['text']));
		}
	}
}

$tpl->printToScreen();
?>
