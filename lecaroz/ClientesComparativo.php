<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
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
			$condiciones = array();
			
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
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 38, 42))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();
				
				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				sort($anios);
				
				if (count($anios) > 0) {
					$condiciones[] = 'EXTRACT(YEAR FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
			}
			
			if (count($anios) > 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						EXTRACT(YEAR FROM fecha)
							AS anio,
						SUM(ctes)
							AS clientes
					FROM
						captura_efectivos ce
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						anio
					ORDER BY
						num_cia,
						anio
				';
			}
			else {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						SUM(ctes)
							AS clientes
					FROM
						captura_efectivos ce
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						mes
					ORDER BY
						num_cia,
						mes
				';
			}
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ClientesComparativoReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				if (count($anios) > 1) {
					$tpl->newBlock('reporte_anual');
					
					$totales = array();
					
					foreach ($anios as $anio) {
						$totales[$anio] = 0;
						
						$tpl->newBlock('anio');
						$tpl->assign('anio', $anio);
					}
					
					$clientes = array();
					
					$num_cia = NULL;
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$clientes[$num_cia] = array(
								'nombre_cia' => utf8_encode($rec['nombre_cia']),
								'clientes' => array()
							);
							
							foreach ($anios as $anio) {
								$clientes[$num_cia]['clientes'][$anio] = 0;
							}
						}
						
						$clientes[$num_cia]['clientes'][$rec['anio']] = $rec['clientes'];
					}
					
					foreach ($clientes as $num_cia => $datos) {
						$tpl->newBlock('row_anual');
						
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $datos['nombre_cia']);
						
						$total = 0;
						$div = 0;
						
						foreach ($datos['clientes'] as $anio => $clientes) {
							$tpl->newBlock('clientes');
							$tpl->assign('clientes', $clientes != 0 ? number_format($clientes) : '&nbsp;');
							
							$total += $clientes;
							$totales[$anio] += $clientes;
							
							$div += $clientes != 0 ? 1 : 0;
						}
						
						$tpl->assign('row_anual.total', number_format($total));
						$tpl->assign('row_anual.promedio', number_format($total / $div), 0);
					}
					
					foreach ($totales as $anio => $total) {
						$tpl->newBlock('total');
						$tpl->assign('clientes', $total != 0 ? number_format($total) : '&nbsp;');
					}
					
					$tpl->assign('reporte_anual.total', number_format(array_sum($totales)));
					$tpl->assign('reporte_anual.promedio', number_format(array_sum($totales) / count(array_filter($totales, 'filter')), 0));
				}
				else {
					$tpl->newBlock('reporte_mensual');
					
					$tpl->assign('anio', $anios[0]);
					
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
					
					$num_cia = NULL;
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$tpl->newBlock('row_mensual');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
							
							$total = 0;
							$promedio = 0;
						}
						
						$tpl->assign($rec['mes'], number_format($rec['clientes']));
						
						$total += $rec['clientes'];
						$totales[$rec['mes']] += $rec['clientes'];
						
						$promedio = $total / $rec['mes'];
						
						$tpl->assign('total', number_format($total));
						$tpl->assign('promedio', number_format($promedio));
					}
					
					foreach ($totales as $mes => $total) {
						$tpl->assign('reporte_mensual.' . $mes, $total != 0 ? number_format($total) : '&nbsp;');
					}
					
					$tpl->assign('reporte_mensual.total', number_format(array_sum($totales)));
					$tpl->assign('reporte_mensual.promedio', number_format(array_sum($totales) / count(array_filter($totales, 'filter')), 0));
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'exportar':
			$condiciones = array();
			
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
			
			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();
				
				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				sort($anios);
				
				if (count($anios) > 0) {
					$condiciones[] = 'EXTRACT(YEAR FROM fecha) IN (' . implode(', ', $anios) . ')';
				}
			}
			
			if (count($anios) > 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						EXTRACT(YEAR FROM fecha)
							AS anio,
						SUM(ctes)
							AS clientes
					FROM
						captura_efectivos ce
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						anio
					ORDER BY
						num_cia,
						anio
				';
			}
			else {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						SUM(ctes)
							AS clientes
					FROM
						captura_efectivos ce
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						mes
					ORDER BY
						num_cia,
						mes
				';
			}
			
			$result = $db->query($sql);
			
			$data = '';
			
			if ($result) {
				if (count($anios) > 1) {
					$data .= '"Comparativo de clientes"' . "\r\n";
					
					$totales = array();
					
					$data .= '"#","Compañía",';
					
					foreach ($anios as $anio) {
						$totales[$anio] = 0;
						
						$data .= '"' . $anio . '",';
					}
					
					$data .= '"Total","Promedio"' . "\r\n";
					
					$clientes = array();
					
					$num_cia = NULL;
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$clientes[$num_cia] = array(
								'nombre_cia' => $rec['nombre_cia'],
								'clientes' => array()
							);
							
							foreach ($anios as $anio) {
								$clientes[$num_cia]['clientes'][$anio] = 0;
							}
						}
						
						$clientes[$num_cia]['clientes'][$rec['anio']] = $rec['clientes'];
					}
					
					foreach ($clientes as $num_cia => $datos) {
						$data .= '"' . $num_cia . '","' . $datos['nombre_cia'] . '",';
						
						$total = 0;
						$div = 0;
						
						foreach ($datos['clientes'] as $anio => $clientes) {
							$data .= '"' . ($clientes != 0 ? number_format($clientes) : '') . '",';
							
							$totales[$anio] += $clientes;
						}
						
						$data .= '"' . number_format(array_sum($datos['clientes'])) . '","' . number_format(array_sum($datos['clientes']) / count(array_filter($datos['clientes'], 'filter')), 0) . '"' . "\r\n";
					}
					
					$data .= '"","Totales",';
					
					foreach ($totales as $anio => $total) {
						$data .= '"' . ($total != 0 ? number_format($total) : '') . '",';
					}
					
					$data .= '"' . number_format(array_sum($totales)) . '","' . number_format(array_sum($totales) / count(array_filter($totales, 'filter')), 0) . '"';
				}
				else {
					$data .= '"Comparativo de clientes ' . $anios[0] . '"' . "\r\n";
					
					$data .= '""' . "\r\n";
					
					$data .= '"#","Compañía","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic","Total","Promedio"' . "\r\n";
					
					$clientes = array();
					
					$num_cia = NULL;
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$clientes[$num_cia] = array(
								'nombre_cia' => $rec['nombre_cia'],
								'clientes' => array()
							);
							
							foreach ($_meses as $mes => $nombre) {
								$clientes[$num_cia]['clientes'][$mes] = 0;
							}
						}
						
						$clientes[$num_cia]['clientes'][$rec['mes']] = $rec['clientes'];
					}
					
					$totales = array();
					
					foreach ($_meses as $mes => $nombre) {
						$totales[$mes] = 0;
					}
					
					foreach ($clientes as $num_cia => $datos) {
						$data .= '"' . $num_cia . '","' . $datos['nombre_cia'] . '",';
						
						$total = 0;
						$promedio = 0;
						
						foreach ($datos['clientes'] as $mes => $clientes) {
							$data .= '"' . ($clientes != 0 ? number_format($clientes) : '') . '",';
							
							$totales[$mes] += $clientes;
						}
						
						$data .= '"' . number_format(array_sum($datos['clientes'])) . '","' . number_format(array_sum($datos['clientes']) / count(array_filter($datos['clientes'], 'filter')), 0) . '"' . "\r\n";
					}
					
					$data .= '"","Totales",';
					
					foreach ($totales as $anio => $total) {
						$data .= '"' . ($total != 0 ? number_format($total) : '') . '",';
					}
					
					$data .= '"' . number_format(array_sum($totales)) . '","' . number_format(array_sum($totales) / count(array_filter($totales, 'filter')), 0) . '"';
				}
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ComparativoClientes.csv"');
			
			echo utf8_encode(utf8_decode($data));
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ClientesComparativo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if (!$isIpad) {
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
	$tpl->assign('anio', date('Y'));
	
	$condiciones[] = 'num_cia <= 300';
	
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
			catalogo_companias cc
			LEFT JOIN catalogo_administradores ca
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
