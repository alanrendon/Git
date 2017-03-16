<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIMEBRE'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));
			
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones[] = 'cod_mov IN (11, 12)';
			
			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}
			
			/*
			@ Intervalo de compañías
			*/
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
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					EXTRACT(year FROM fecha)
						AS
							anio,
					EXTRACT(month FROM fecha)
						AS
							mes,
					SUM(
						CASE
							WHEN cod_mov = 11 THEN
								importe
							WHEN cod_mov = 12 THEN
								-importe
						END
					)
						AS
							diferencia
				FROM
						estado_cuenta ec
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia,
					anio,
					mes
				ORDER BY
					num_cia,
					mes
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/DiferenciaInteresesImpuestosAnualReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$datos[$num_cia] = array(
							'nombre' => $rec['nombre_cia'],
							'anio' => $rec['anio'],
							'diferencias' => array()
						);
					}
					
					$datos[$num_cia]['diferencias'][$rec['mes']] = $rec['diferencia'];
				}
				
				$max_rows = 47;
				$rows = $max_rows;
				
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
				
				foreach ($datos as $num_cia => $info) {
					if ($rows >= $max_rows) {
						$tpl->newBlock('reporte');
						$tpl->assign('anio', $_REQUEST['anio']);
						
						$tpl->assign('salto', '<br style="page-break-after:always;" />');
						
						$rows = 0;
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $info['nombre']);
					
					foreach ($info['diferencias'] as $mes => $dif) {
						$tpl->assign('color' . $mes, $dif > 0 ? ' blue' : ' red');
						$tpl->assign($mes, number_format($dif, 2, '.', ','));
						
						$totales[$mes] += $dif;
					}
					
					$tpl->assign('total', number_format(array_sum($info['diferencias']), 2, '.', ','));
					
					$rows++;
				}
				
				$tpl->newBlock('totales');
				
				foreach ($totales as $mes => $total) {
					$tpl->assign($mes, number_format($total, 2, '.', ','));
				}
				
				$tpl->assign('total', number_format(array_sum($totales), 2, '.', ','));
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/DiferenciaInteresesImpuestosAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
