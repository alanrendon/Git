<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

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
	12 => 'DICIEMBRE'
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
			
			$condiciones[] = 'fecha = \'' . $_REQUEST['fecha'] . '\'';
			
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
					num_cia,
					nombre_corto
						AS nombre_cia,
					SUM(venta_puerta)
						AS venta,
					SUM(venta_pastel)
						AS pastel
				FROM
					total_panaderias efe
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/VentasConsultaDiaReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->assign('fecha', $_REQUEST['fecha']);
				
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('venta', number_format($rec['venta'], 2));
					$tpl->assign('pastel', $rec['pastel'] != 0 ? number_format($rec['pastel'], 2) : '&nbsp;');
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/VentasConsultaDia.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if (!$isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2)));
	
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
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2)));
	
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
