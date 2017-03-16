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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$condiciones = array();
			
			$condiciones[] = '(num_cia, codmp) IN (
				SELECT
					num_cia,
					codmp
				FROM
					inventario_real
				WHERE
					num_cia <= 300
			)';
			
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
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
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
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if ($_REQUEST['tipo'] == 1) {
				$orden = 'codmp, num_cia, porcentaje DESC';
			}
			else {
				$orden = 'num_cia, codmp, porcentaje DESC';
			}
			
			$sql = '
				SELECT
					codmp,
					cmp.nombre
						AS nombre_mp,
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					porcentaje,
					ppp.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro
				FROM
					porcentajes_pedidos_proveedores ppp
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					' . $orden . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/DistribucionProductosProveedoresReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$productos = array();
				
				function map($rec) {
					return '[' . $rec['porcentaje'] . '%] ' . $rec['num_pro'] . ' ' . $rec['nombre_pro'];
				}
				
				if ($_REQUEST['tipo'] == 1) {
					$codmp = NULL;
					foreach ($result as $rec) {
						if ($codmp != $rec['codmp']) {
							$codmp = $rec['codmp'];
							
							$productos[$codmp] = array(
								'nombre_mp' => $rec['nombre_mp'],
								'cias' => array()
							);
							
							$num_cia = NULL;
						}
						
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$productos[$codmp]['cias'][$num_cia] = array(
								'nombre_cia' => $rec['nombre_cia'],
								'proveedores' => array()
							);
						}
						
						$productos[$codmp]['cias'][$num_cia]['proveedores'][] = array(
							'num_pro' => $rec['num_pro'],
							'nombre_pro' => $rec['nombre_pro'],
							'porcentaje' => $rec['porcentaje']
						);
					}
					
					foreach ($productos as $codmp => $datos_mp) {
						$tpl->newBlock('reporte' . $_REQUEST['tipo']);
						
						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', $datos_mp['nombre_mp']);
						
						foreach ($datos_mp['cias'] as $num_cia => $datos_cia) {
							$tpl->newBlock('row' . $_REQUEST['tipo']);
							
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);
							
							$tpl->assign('distribucion', implode(', ', array_map('map', $datos_cia['proveedores'])));
						}
					}
				}
				else {
					$num_cia = NULL;
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							$num_cia = $rec['num_cia'];
							
							$productos[$num_cia] = array(
								'nombre_cia' => $rec['nombre_cia'],
								'mps' => array()
							);
							
							$codmp = NULL;
						}
						
						if ($codmp != $rec['codmp']) {
							$codmp = $rec['codmp'];
							
							$productos[$num_cia]['mps'][$codmp] = array(
								'nombre_mp' => $rec['nombre_mp'],
								'proveedores' => array()
							);
						}
						
						$productos[$num_cia]['mps'][$codmp]['proveedores'][] = array(
							'num_pro' => $rec['num_pro'],
							'nombre_pro' => $rec['nombre_pro'],
							'porcentaje' => $rec['porcentaje']
						);
					}
					
					foreach ($productos as $num_cia => $datos_cia) {
						$tpl->newBlock('reporte' . $_REQUEST['tipo']);
						
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);
						
						foreach ($datos_cia['mps'] as $codmp => $datos_mp) {
							$tpl->newBlock('row' . $_REQUEST['tipo']);
							
							$tpl->assign('codmp', $codmp);
							$tpl->assign('nombre_mp', $datos_mp['nombre_mp']);
							
							$tpl->assign('distribucion', implode(', ', array_map('map', $datos_mp['proveedores'])));
						}
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/DistribucionProductosProveedores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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
	foreach ($admins as $admin) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $admin['value']);
		$tpl->assign('text', utf8_encode($admin['text']));
	}
}

$tpl->printToScreen();
?>
