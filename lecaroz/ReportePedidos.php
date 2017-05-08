<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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

$_meses = array(
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['tipo'])) {
	$condiciones = array();
	
	if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
		$folios = array();
		
		$pieces = explode(',', $_REQUEST['folios']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$folios[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$folios[] = $piece;
			}
		}
		
		if (count($folios) > 0) {
			$condiciones[] = 'p.folio IN (' . implode(', ', $folios) . ')';
		}
	}
	
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
			$condiciones[] = 'p.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}
	
	if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
		$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
	}
	
	if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
		$pros = array();
		
		$pieces = explode(',', $_REQUEST['pros']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$pros[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$pros[] = $piece;
			}
		}
		
		if (count($pros) > 0) {
			$condiciones[] = 'p.num_proveedor IN (' . implode(', ', $pros) . ')';
		}
	}
	
	if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
		$mps = array();
		
		$pieces = explode(',', $_REQUEST['mps']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$mps[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$mps[] = $piece;
			}
		}
		
		if (count($mps) > 0) {
			$condiciones[] = 'p.codmp IN (' . implode(', ', $mps) . ')';
		}
	}
	
	if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
		$omitir_cias = array();
		
		$pieces = explode(',', $_REQUEST['omitir_cias']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$omitir_cias[] = $piece;
			}
		}
		
		if (count($omitir_cias) > 0) {
			$condiciones[] = 'p.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
		}
	}
	
	if (isset($_REQUEST['omitir_pros']) && trim($_REQUEST['omitir_pros']) != '') {
		$omitir_pros = array();
		
		$pieces = explode(',', $_REQUEST['omitir_pros']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$omitir_pros[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$omitir_pros[] = $piece;
			}
		}
		
		if (count($omitir_pros) > 0) {
			$condiciones[] = 'p.num_proveedor NOT IN (' . implode(', ', $omitir_pros) . ')';
		}
	}
	
	if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
		$omitir_mps = array();
		
		$pieces = explode(',', $_REQUEST['omitir_mps']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$omitir_mps[] = $piece;
			}
		}
		
		if (count($omitir_mps) > 0) {
			$condiciones[] = 'p.codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
		}
	}
	
	if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
		if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
			$condiciones[] = 'p.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
		}
		else if (isset($_REQUEST['fecha1'])) {
			$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha1'] . '\'';
		}
		else if (isset($_REQUEST['fecha2'])) {
			$condiciones[] = 'p.fecha >= \'' . $_REQUEST['fecha2'] . '\'';
		}
	}
	
	if (isset($_REQUEST['id'])) {
		$condiciones[] = 'p.id IN (' . implode(', ', $_REQUEST['id']) . ')';
	}
	
	$condiciones[] = 'p.tsbaja IS NULL';
	
	switch ($_REQUEST['tipo']) {
		case 'cia':
			$orden = '
				folio,
				num_cia,
				codmp,
				pedido
			';
		break;
		
		case 'mp':
			$orden = '
				folio,
				codmp,
				num_cia,
				pedido
			';
		break;
		
		case 'pro':
			$orden = '
				folio,
				num_pro,
				num_cia,
				codmp,
				pedido
			';
		break;
		
		case 'memo':
			$orden = '
				folio,
				num_pro,
				num_cia,
				codmp,
				pedido
			';
		break;
	}
	
	$sql = '
		SELECT
			p.folio,
			p.fecha,
			p.dias,
			num_cia,
			cc.nombre_corto
				AS nombre_cia,
			codmp,
			cmp.nombre
				AS nombre_mp,
			pedido,
			p.unidad,
			entregar,
			p.presentacion,
			p.contenido,
			p.num_proveedor
				AS num_pro,
			cp.nombre
				AS nombre_pro,
			cp.telefono1,
			cp.telefono2,
			cp.email1,
			cp.email2,
			cp.email3,
			pa.anotaciones,
			p.urgente
		FROM
			pedidos_new p
			LEFT JOIN pedidos_anotaciones pa
				USING (folio, num_proveedor)
			LEFT JOIN catalogo_proveedores cp
				USING (num_proveedor)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
			LEFT JOIN catalogo_mat_primas cmp
				USING (codmp)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			' . $orden . '
	';
	
	$result = $db->query($sql);
	
	switch ($_REQUEST['tipo']) {
		case 'cia':
			$tpl = new TemplatePower('plantillas/ped/ReportePedidosCia.tpl');
			$tpl->prepare();
		break;
		
		case 'mp':
			$tpl = new TemplatePower('plantillas/ped/ReportePedidosMP.tpl');
			$tpl->prepare();
		break;
		
		case 'pro':
			$tpl = new TemplatePower('plantillas/ped/ReportePedidosPro.tpl');
			$tpl->prepare();
		break;
		
		case 'memo':
			$tpl = new TemplatePower('plantillas/ped/ReportePedidosMemo.tpl');
			$tpl->prepare();
		break;
	}
	
	if ($result) {
		switch ($_REQUEST['tipo']) {
			case 'cia':
				$folio = NULL;
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($folio != $rec['folio'] || $num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$folio = $rec['folio'];
						
						$num_cia = $rec['num_cia'];
						
						list($dia, $mes, $anio) = explode('/', $rec['fecha']);
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('dia', intval($dia, 10));
						$tpl->assign('mes', $_meses[intval($mes, 10)]);
						$tpl->assign('anio', intval($anio));
						$tpl->assign('folio', intval($rec['folio']));
						$tpl->assign('dias', intval($rec['dias']));
					}
					
					$tpl->newBlock('row');
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', $rec['nombre_mp']);
					$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
					$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', $rec['nombre_pro']);
					$tpl->assign('telefono1', $rec['telefono1']);
					$tpl->assign('telefono2', $rec['telefono2']);
					$tpl->assign('email1', $rec['email1']);
					$tpl->assign('email2', $rec['email2']);
					$tpl->assign('email3', $rec['email3']);
					
					$tpl->assign('color', $rec['urgente'] == 't' ? 'red' : 'green');
				}
			break;
			
			case 'mp':
				$folio = NULL;
				
				$codmp = NULL;
				
				foreach ($result as $rec) {
					if ($folio != $rec['folio'] || $codmp != $rec['codmp']) {
						if ($codmp != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$folio = $rec['folio'];
						
						$codmp = $rec['codmp'];
						
						list($dia, $mes, $anio) = explode('/', $rec['fecha']);
						
						$tpl->newBlock('reporte');
						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', $rec['nombre_mp']);
						$tpl->assign('dia', intval($dia, 10));
						$tpl->assign('mes', $_meses[intval($mes, 10)]);
						$tpl->assign('anio', intval($anio));
						$tpl->assign('folio', intval($rec['folio']));
						$tpl->assign('dias', intval($rec['dias']));
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', $rec['nombre_cia']);
					$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
					$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', $rec['nombre_pro']);
					$tpl->assign('telefono1', $rec['telefono1']);
					$tpl->assign('telefono2', $rec['telefono2']);
					$tpl->assign('email1', $rec['email1']);
					$tpl->assign('email2', $rec['email2']);
					$tpl->assign('email3', $rec['email3']);
					
					$tpl->assign('color', $rec['urgente'] == 't' ? 'red' : 'green');
				}
			break;
			
			case 'pro':
				$folio = NULL;
				
				$num_pro = NULL;
				
				foreach ($result as $rec) {
					if ($folio != $rec['folio'] || $num_pro != $rec['num_pro']) {
						if ($num_pro != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$folio = $rec['folio'];
						
						$num_pro = $rec['num_pro'];
						
						list($dia, $mes, $anio) = explode('/', $rec['fecha']);
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_pro', $num_pro);
						$tpl->assign('nombre_pro', $rec['nombre_pro']);
						$tpl->assign('telefono1', $rec['telefono1']);
						$tpl->assign('dia', intval($dia, 10));
						$tpl->assign('mes', $_meses[intval($mes, 10)]);
						$tpl->assign('anio', intval($anio));
						$tpl->assign('folio', intval($rec['folio']));
						$tpl->assign('dias', intval($rec['dias']));
						
						$num_cia = NULL;
					}
					
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', $rec['nombre_mp']);
					$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
					$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
					
					$tpl->assign('color', $rec['urgente'] == 't' ? 'red' : 'green');
				}
			break;
			
			case 'memo':
				$num_pro = NULL;
				
				foreach ($result as $rec) {
					if ($num_pro != $rec['num_pro']) {
						if ($num_pro != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$num_pro = $rec['num_pro'];
						
						list($dia, $mes, $anio) = explode('/', $rec['fecha']);
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_pro', $num_pro);
						$tpl->assign('nombre_pro', $rec['nombre_pro']);
						$tpl->assign('telefono1', $rec['telefono1']);
						$tpl->assign('dia', intval($dia, 10));
						$tpl->assign('mes', $_meses[intval($mes, 10)]);
						$tpl->assign('anio', intval($anio));
						$tpl->assign('folio', intval($rec['folio']));
						
						$tpl->assign('anotaciones', $rec['anotaciones'] != '' ? '<p class="underline"><strong>OBSERVACIONES: ' . $rec['anotaciones'] . '</strong></p>' : '');
						
						$num_cia = NULL;
					}
					
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', $rec['nombre_mp']);
					$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
					$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
					
					$tpl->assign('urgente', $rec['urgente'] == 't' ? 'red bold underline' : 'green');
				}
			break;
		}
	}
	
	$tpl->printToScreen();
}

?>
