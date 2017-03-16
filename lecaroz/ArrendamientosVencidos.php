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

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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
			
			$condiciones[] = 'arr.tsbaja IS NULL AND per.tsbaja IS NULL';
			
			if (isset($_REQUEST['meses']) && $_REQUEST['meses'] > 0) {
				$condiciones[] = 'fecha_termino <= \'' . date('d/m/Y', mktime(0, 0, 0, date('n') + 1, 0)) . '\'::DATE + INTERVAL \'' . $_REQUEST['meses'] . ' MONTHS\'';
			}
			else {
				$condiciones[] = 'fecha_termino <= \'' . date('d/m/Y', mktime(0, 0, 0, date('n') + 1, 0)) . '\'::DATE + INTERVAL \'2 MONTHS\'';
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $arrendadores) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					arrendamiento,
					alias_arrendamiento,
					nombre_arrendador,
					fecha_inicio,
					fecha_termino,
					total
				FROM
					arrendamientos arr
					LEFT JOIN arrendamientos_periodos per
						USING (idarrendamiento)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					arrendamiento
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ren/ArrendamientosVencidosReporte.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('reporte');
			
			if ($result) {
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					}
					
					$tpl->newBlock('arrendamiento');
					
					$tpl->assign('arrendamiento', str_pad($rec['arrendamiento'], 2, '0') . ' ' . utf8_encode($rec['alias_arrendamiento']));
					$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
					$tpl->assign('periodo_arrendamiento', $rec['fecha_inicio'] . ' - ' . $rec['fecha_termino']);
					$tpl->assign('total', number_format($rec['total'], 2));
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ren/ArrendamientosVencidos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
