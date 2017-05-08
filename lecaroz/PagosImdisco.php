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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

/*if ($_SESSION['iduser'] != 1) {
	die('<div style="font-size:16pt; border:solid 2px #000; padding:30px 10px;">ESTOY HACIENDO MODIFICACIONES AL PROGRAMA, NO ME LLAMEN PARA PREGUNTAR CUANDO QUEDARA, YO LES AVISO.</div>');
}*/

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/zap/PagosImdiscoInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('fecha1', date('01/m/Y'));
			$tpl->assign('fecha2', date('d/m/Y'));
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'tsbaja IS NULL';
			
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
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			if ((isset($_REQUEST['fecha_pago1']) && $_REQUEST['fecha_pago1'] != '')
				|| (isset($_REQUEST['fecha_pago2']) && $_REQUEST['fecha_pago2'] != '')) {
				if ((isset($_REQUEST['fecha_pago1']) && $_REQUEST['fecha_pago1'] != '')
					&& (isset($_REQUEST['fecha_pago2']) && $_REQUEST['fecha_pago2'] != '')) {
					$condiciones[] = 'fecha_pago BETWEEN \'' . $_REQUEST['fecha_pago1'] . '\' AND \'' . $_REQUEST['fecha_pago2'] . '\'';
				} else if (isset($_REQUEST['fecha_pago1']) && $_REQUEST['fecha_pago1'] != '') {
					$condiciones[] = 'fecha_pago = \'' . $_REQUEST['fecha_pago1'] . '\'';
				} else if (isset($_REQUEST['fecha_pago2']) && $_REQUEST['fecha_pago2'] != '') {
					$condiciones[] = 'fecha_pago >= \'' . $_REQUEST['fecha_pago2'] . '\'';
				}
			}
			
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();
				$folios_between = array();
				
				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios_between[] =  'folio BETWEEN \'' . $exp[0] . '\' AND \'' . $exp[1] . '\'';
					}
					else {
						$folios[] = $piece;
					}
				}
				
				$partes = array();
				
				if (count($folios) > 0) {
					$partes[] = 'folio IN (' . implode(', ', $folios) . ')';
				}
				
				if (count($folios_between) > 0) {
					$partes[] = implode(' OR ', $folios_between);
				}
				
				if (count($partes) > 0) {
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'fecha_pago IS NOT NULL';
			}
			
			if (!isset($_REQUEST['pagadas'])) {
				$condiciones[] = 'fecha_pago IS NULL';
			}
			
			$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					fecha_pago,
					folio,
					importe
				FROM
					ventas_imdisco vi
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha,
					folio
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/zap/PagosImdiscoResultado.tpl');
				$tpl->prepare();
				
				$total = 0;
				
				foreach ($result as $i => $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $i % 2 == 0 ? 'off' : 'on');
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('fecha_pago', $rec['fecha_pago'] != '' ? $rec['fecha_pago'] : '&nbsp;');
					$tpl->assign('folio', utf8_encode($rec['folio']));
					$tpl->assign('importe', number_format($rec['importe'], 2));
					
					$total += $rec['importe'];
				}
				
				$tpl->assign('_ROOT.total', number_format($total, 2));
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'fecha_pago':
			$tpl = new TemplatePower('plantillas/zap/PagosImdiscoFechaPago.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
			$tpl->assign('fecha', date('d/m/Y'));
			
			echo $tpl->getOutputContent();
		break;
		
		case 'pagar':
			$sql = '
				UPDATE
					ventas_imdisco
				SET
					fecha_pago = \'' . $_REQUEST['fecha'] . '\',
					idmod = ' . $_SESSION['iduser'] . ',
					tsmod = NOW()
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
			';
			
			$db->query($sql);
		break;
		
		case 'baja':
			$sql = '
				UPDATE
					ventas_imdisco
				SET
					idbaja = ' . $_SESSION['iduser'] . ',
					tsbaja = NOW()
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
			';
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/PagosImdisco.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
