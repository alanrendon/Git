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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerMP':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre'];
			}
		break;
		
		case 'consultar':
			$sql = '
				SELECT
					id,
					num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					tp.descripcion
						AS presentacion,
					contenido,
					tuc.descripcion
						AS unidad,
					precio
				FROM
					catalogo_productos_proveedor cpp
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN tipo_presentacion tp
						ON (tp.idpresentacion = cpp.presentacion)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
				ORDER BY
					num_proveedor,
					precio
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$proveedores = array();
				$presentaciones = array();
				
				$num_pro = NULL;
				foreach ($result as $rec) {
					if ($num_pro != $rec['num_pro']) {
						$num_pro = $rec['num_pro'];
						
						$proveedores[] = array(
							'num_pro'    => $rec['num_pro'],
							'nombre_pro' => $rec['nombre_pro']
						);
						
						$presentaciones[$num_pro] = array();
					}
					
					$presentaciones[$num_pro][] = array(
						'id'           => $rec['id'],
						'presentacion' => $rec['presentacion'],
						'contenido'    => $rec['contenido'],
						'unidad'       => $rec['unidad'],
						'texto'        => (($rec['presentacion'] == $rec['unidad']) ? $rec['presentacion'] : $rec['presentacion'] . ' DE ' . $rec['contenido'] . ' ' . $rec['unidad']) . 'S | $' . number_format($rec['precio'], 2, '.', ',')
					);
				}
				
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia
					FROM
						catalogo_companias
					WHERE
						num_cia <= 300
					ORDER BY
						num_cia
				';
				
				$cias = $db->query($sql);
				
				$tpl = new TemplatePower('plantillas/ped/PorcentajesPedidosProveedoresConsulta.tpl');
				$tpl->prepare();
				
				foreach ($proveedores as $pro) {
					$tpl->newBlock('th');
					$tpl->assign('num_pro', $pro['num_pro']);
					$tpl->assign('nombre_pro', $pro['nombre_pro']);
				}
				
				foreach ($cias as $i => $cia) {
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $cia['num_cia']);
					$tpl->assign('nombre_cia', $cia['nombre_cia']);
					
					foreach ($proveedores as $pro) {
						$tpl->newBlock('pro');
						
						foreach ($presentaciones as $pre) {
							$tpl->newBlock('presentacion');
							$tpl->assign('value', $pre['id']);
							$tpl->assign('text', $pre['texto']);
						}
					}
				}
				
				echo $tpl->getOutputContent();
			}
			
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/PorcentajesPedidosProveedores.tpl');
$tpl->prepare();

$tpl->printToScreen();
?>
