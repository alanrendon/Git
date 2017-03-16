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
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
			$condiciones[] = 'imp_' . $_REQUEST['tipo'] . ' = TRUE';
			
			$condiciones[] = 'idcontador = ' . $_REQUEST['idcontador'];
			
			$sql = '
				SELECT
					ct.num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.nombre_corto
						AS alias_cia,
					conta.nombre_contador,
					ct.ap_paterno,
					ct.ap_materno,
					ct.nombre
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_contadores conta
						USING (idcontador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ct.num_cia,
					ct.ap_paterno,
					ct.ap_materno,
					ct.nombre
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/CartaMovimientosIMSSReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('reporte');
				
				$tpl->assign('dia', date('j'));
				$tpl->assign('mes', $_meses[date('n')]);
				$tpl->assign('anio', date('Y'));
				
				$tpl->assign('contador', utf8_encode($result[0]['nombre_contador']));
				
				$tpl->assign('tipo', strtoupper($_REQUEST['tipo']));
				
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('nombre_trabajador', utf8_encode(implode(' ', array_filter(array(
						$rec['ap_paterno'],
						$rec['ap_materno'],
						$rec['nombre']
					)))));
					
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia'] . ($rec['alias_cia'] != '' ? ' (' . $rec['alias_cia'] . ')' : '')));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'limpiar_altas':
			$db->query('
				UPDATE
					catalogo_trabajadores
				SET
					imp_alta = FALSE
				WHERE
					num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
					AND imp_alta = TRUE
			');
		break;
		
		case 'limpiar_bajas':
			$db->query('
				UPDATE
					catalogo_trabajadores
				SET
					imp_baja = FALSE
				WHERE
					num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
					AND imp_baja = TRUE
			');
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/CartaMovimientosIMSS.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idcontador
			AS value,
		nombre_contador
			AS text
	FROM
		catalogo_contadores
	ORDER BY
		value
';

$contadores = $db->query($sql);

if ($contadores) {
	foreach ($contadores as $c) {
		$tpl->newBlock('contador');
		$tpl->assign('value', $c['value']);
		$tpl->assign('text', utf8_encode($c['text']));
	}
}

$tpl->printToScreen();
?>
