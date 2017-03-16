<?php

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					num_expendio,
					nombre_expendio,
					fecha,
					rezago_anterior
						AS saldo_ini,
					pan_p_venta
						AS reparto,
					pan_p_expendio
						AS total,
					pan_p_venta - pan_p_expendio
						AS diferencia,
					porc_ganancia
						AS ganancia,
					abono,
					devolucion
						AS devuelto,
					rezago
						AS saldo_fin
				FROM
					mov_expendios mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND num_expendio = ' . $_REQUEST['num_exp'] . '
					AND nombre_expendio = \'' . $_REQUEST['nombre_exp'] . '\'
					AND fecha BETWEEN \'' . $_REQUEST['fecha2'] . '\'::DATE - INTERVAL \'' . ($_REQUEST['dias'] - 1) . ' DAYS\' AND \'' . $_REQUEST['fecha2'] . '\'::DATE
				ORDER BY
					fecha
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ExpendioDetalleRezago.tpl');
			$tpl->prepare();
			
			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('num_expendio', $result[0]['num_expendio']);
			$tpl->assign('nombre_expendio', utf8_encode($result[0]['nombre_expendio']));
			
			$reparto = 0;
			$total = 0;
			$diferencia = 0;
			$abono = 0;
			$devuelto = 0;
			
			foreach ($result as $i => $rec) {
				$tpl->newBlock('row');
				
				$tpl->assign('num', $i + 1);
				$tpl->assign('fecha', $rec['fecha']);
				$tpl->assign('saldo_ini', $rec['saldo_ini'] != 0 ? number_format($rec['saldo_ini'], 2) : '&nbsp;');
				$tpl->assign('reparto', $rec['reparto'] != 0 ? number_format($rec['reparto'], 2) : '&nbsp;');
				$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');
				$tpl->assign('diferencia', $rec['diferencia'] != 0 ? number_format($rec['diferencia'], 2) : '&nbsp;');
				$tpl->assign('ganancia', $rec['ganancia'] != 0 ? number_format($rec['ganancia'], 2) : '&nbsp;');
				$tpl->assign('abono', $rec['abono'] != 0 ? number_format($rec['abono'], 2) : '&nbsp;');
				$tpl->assign('devuelto', $rec['devuelto'] != 0 ? number_format($rec['devuelto'], 2) : '&nbsp;');
				$tpl->assign('saldo_fin', $rec['saldo_fin'] != 0 ? number_format($rec['saldo_fin'], 2) : '&nbsp;');
				
				$reparto += $rec['reparto'];
				$total += $rec['total'];
				$diferencia += $rec['diferencia'];
				$abono += $rec['abono'];
				$devuelto += $rec['devuelto'];
				
			}
			
			$tpl->assign('_ROOT.total', number_format($total, 2));
			$tpl->assign('_ROOT.reparto', number_format($reparto, 2));
			$tpl->assign('_ROOT.diferencia', number_format($diferencia, 2));
			$tpl->assign('_ROOT.abono', number_format($abono, 2));
			$tpl->assign('_ROOT.devuelto', number_format($devuelto, 2));
			
			$tpl->assign('_ROOT.rezago_al_dia', number_format($rec['saldo_fin'], 2));
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

?>
