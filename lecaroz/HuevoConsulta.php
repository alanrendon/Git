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

function toInt($value) {
	return intval($value, 10);
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
	12 => 'DICIEMBRE'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
			$condiciones = array();
			
			if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
				if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1'])) {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['num_pro'])) {
				$condiciones[] = 'hr.num_proveedor = ' . $_REQUEST['num_pro'];
			}
			
			if (isset($_REQUEST['num_rem'])) {
				$condiciones[] = 'num_rem = \'' . $_REQUEST['num_rem'] . '\'';
			}
			
			if (isset($_REQUEST['num_fact'])) {
				$condiciones[] = 'num_fact = \'' . $_REQUEST['num_fact'] . '\'';
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'num_fact IS NOT NULL';
			}
			
			if (!isset($_REQUEST['asociadas'])) {
				$condiciones[] = 'num_fact IS NULL';
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 5, 8, 14, 18, 39, 44))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}
			
			if (!isset($_REQUEST['cias'])
				&& !isset($_REQUEST['fecha1'])
				&& !isset($_REQUEST['fecha2'])
				&& !isset($_REQUEST['num_rem'])
				&& !isset($_REQUEST['num_fact'])) {
				$condiciones[] = 'fecha BETWEEN NOW()::DATE - INTERVAL \'3 DAYS\' AND NOW()::DATE';
			}
			
			if ($_REQUEST['orden'] == 'cia') {
				$orden = 'num_cia, num_pro, num_fact, num_rem';
			}
			else if ($_REQUEST['orden'] == 'pro') {
				$orden = 'num_pro, num_cia, num_fact, num_rem';
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					hr.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					num_rem,
					num_fact,
					fecha,
					cajas,
					peso_bruto
						AS peso_bruto_pesadas,
					peso_bruto_remision,
					tara,
					peso_neto,
					precio,
					total
				FROM
					huevo_remisiones hr
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					' . $orden . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/HuevoConsultaReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				if ($_REQUEST['orden'] == 'cia') {
					$num_cia = NULL;
					
					$tpl->newBlock('reporte_cia');
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$tpl->newBlock('c_cia');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
							
							$num_pro = NULL;
						}
						
						if ($num_pro != $rec['num_pro']) {
							$num_pro = $rec['num_pro'];
							
							$tpl->newBlock('c_pro');
							$tpl->assign('num_pro', $num_pro);
							$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
						}
						
						$tpl->newBlock('c_row');
						$tpl->assign('num_rem', $rec['num_rem']);
						$tpl->assign('num_fact', $rec['num_fact']);
						$tpl->assign('fecha', $rec['fecha']);
						$tpl->assign('cajas', number_format($rec['cajas']));
						$tpl->assign('peso_bruto_pesadas', number_format($rec['peso_bruto_pesadas'], 2));
						$tpl->assign('peso_bruto_remision', number_format($rec['peso_bruto_remision'], 2));
						$tpl->assign('tara', number_format($rec['tara'], 2));
						$tpl->assign('peso_neto', number_format($rec['peso_neto'], 2));
						$tpl->assign('precio', number_format($rec['precio'], 2));
						$tpl->assign('total', number_format($rec['total'], 2));
					}
				}
				else if ($_REQUEST['orden'] == 'pro') {
					$num_pro = NULL;
					
					$tpl->newBlock('reporte_pro');
					
					foreach ($result as $rec) {
						if ($num_pro != $rec['num_pro']) {
							$num_pro = $rec['num_pro'];
							
							$tpl->newBlock('p_pro');
							$tpl->assign('num_pro', $num_pro);
							$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
							
							$num_cia = NULL;
						}
						
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$tpl->newBlock('p_cia');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						}
						
						$tpl->newBlock('p_row');
						$tpl->assign('num_rem', $rec['num_rem']);
						$tpl->assign('num_fact', $rec['num_fact']);
						$tpl->assign('fecha', $rec['fecha']);
						$tpl->assign('cajas', number_format($rec['cajas']));
						$tpl->assign('peso_bruto_pesadas', number_format($rec['peso_bruto_pesadas'], 2));
						$tpl->assign('peso_bruto_remision', number_format($rec['peso_bruto_remision'], 2));
						$tpl->assign('tara', number_format($rec['tara'], 2));
						$tpl->assign('peso_neto', number_format($rec['peso_neto'], 2));
						$tpl->assign('precio', number_format($rec['precio'], 2));
						$tpl->assign('total', number_format($rec['total'], 2));
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/HuevoConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 8)));
$tpl->assign('fecha2', date('d/m/Y'));

$sql = '
	SELECT
		num_proveedor
			AS value,
		num_proveedor || \' \' || nombre
			AS text
	FROM
		catalogo_productos_proveedor
		LEFT JOIN catalogo_proveedores
			USING (num_proveedor)
	WHERE
		codmp = 148
	GROUP BY
		value,
		text
	ORDER BY
		text
';

$pros = $db->query($sql);

foreach ($pros as $p) {
	$tpl->newBlock('pro');
	$tpl->assign('value', $p['value']);
	$tpl->assign('text', utf8_encode($p['text']));
}

$tpl->printToScreen();
?>
