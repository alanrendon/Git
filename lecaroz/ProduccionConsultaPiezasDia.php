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
		case 'obtenerProducto':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_productos
				WHERE
					cod_producto = ' . $_REQUEST['producto'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}
		break;
		
		case 'reporte':
			$condiciones = array();
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))  {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'p.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha2'] . '\'';
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

			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();
				
				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}
				
				if (count($productos) > 0) {
					$condiciones[] = 'p.cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					p.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					p.cod_producto
						AS cod,
					cp.nombre
						AS producto,
					SUM(p.piezas)
						AS piezas
				FROM
					produccion p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia,
					cod,
					producto
				ORDER BY
					producto,
					num_cia
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ProduccionConsultaPiezasDiaReporte.tpl');
			$tpl->prepare();
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				$tpl->assign('periodo', 'del periodo ' . $_REQUEST['fecha1'] . ' al ' . $_REQUEST['fecha2']);
			} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
				$tpl->assign('periodo', 'del día ' . $_REQUEST['fecha1']);
			} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
				$tpl->assign('periodo', 'del día ' . $_REQUEST['fecha2']);
			}
			$tpl->assign('cod', $result[0]['cod']);
			$tpl->assign('producto', utf8_encode($result[0]['producto']));

			if ($result) {
				$cod = NULL;

				foreach ($result as $rec) {
					if ($cod != $rec['cod']) {
						$cod = $rec['cod'];

						$tpl->newBlock('producto');
						$tpl->assign('cod', $rec['cod']);
						$tpl->assign('producto', utf8_encode($rec['producto']));

						$total = 0;
					}
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('piezas', number_format($rec['piezas'], 2));

					$total += $rec['piezas'];

					$tpl->assign('producto.total', number_format($total, 2));
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ProduccionConsultaPiezasDia.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if (!$isIpad) {
	$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2)));
	
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
	$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2)));
	
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37))) {
		$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
	}
	
	$sql = '
		SELECT
			num_cia
				AS value,
			num_cia || \' \' || nombre_corto
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
	
	$sql = '
		SELECT
			cod_producto
				AS value,
			cod_producto || \' \' || nombre
				AS text
		FROM
			catalogo_productos
		ORDER BY
			nombre
	';
	
	$productos = $db->query($sql);
	
	if ($productos) {
		foreach ($productos as $p) {
			$tpl->newBlock('producto');
			$tpl->assign('value', $p['value']);
			$tpl->assign('text', utf8_encode($p['text']));
		}
	}
}

$tpl->printToScreen();
?>
