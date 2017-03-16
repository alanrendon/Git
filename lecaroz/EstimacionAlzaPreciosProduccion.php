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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
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
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					cod_turnos
						AS turno,
					cod_producto,
					cp.nombre
						AS producto,
					SUM(piezas)
						AS piezas,
					ROUND((SUM(imp_produccion) / SUM(piezas))::NUMERIC, 4)
						AS precio,
					SUM(imp_produccion)
						AS produccion,
					ROUND((SUM(imp_produccion) / SUM(piezas))::NUMERIC, 4) + ' . get_val($_REQUEST['alza_precio']) . '
						AS precio_estimado,
					(ROUND((SUM(imp_produccion) / SUM(piezas))::NUMERIC, 4) + ' . get_val($_REQUEST['alza_precio']) . ') * SUM(piezas)
						AS produccion_estimada
				FROM
					produccion p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia,
					turno,
					cod_producto,
					producto
				ORDER BY
					num_cia,
					turno,
					cod_producto
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/EstimacionAlzaPreciosProduccionReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;">');
						}
						
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
						$tpl->assign('anio', $_REQUEST['anio']);
						
						$turno = NULL;
					}
					
					if ($turno != $rec['turno'])  {
						$turno = $rec['turno'];
						
						$tpl->newBlock('turno');
						
						switch ($turno) {
							case 1:
								$tpl->assign('turno', 'FRANCES DE DIA');
							break;
							
							case 2:
								$tpl->assign('turno', 'FRANCES DE NOCHE');
							break;
							
							case 3:
								$tpl->assign('turno', 'BIZCOCHERO');
							break;
							
							case 4:
								$tpl->assign('turno', 'REPOSTERO');
							break;
							
							case 8:
								$tpl->assign('turno', 'PICONERO');
							break;
							
							case 9:
								$tpl->assign('turno', 'GELATINERO');
							break;
						}
					}
					
					$tpl->newBlock('row');
					$tpl->assign('cod_producto', $rec['cod_producto']);
					$tpl->assign('producto', $rec['producto']);
					$tpl->assign('piezas', number_format($rec['piezas'], 2));
					$tpl->assign('precio', $rec['precio'] != 0 ? number_format($rec['precio'], 4) : '&nbsp;');
					$tpl->assign('produccion', $rec['produccion'] != 0 ? number_format($rec['produccion'], 2) : '&nbsp;');
					$tpl->assign('precio_estimado', $rec['precio_estimado'] != 0 ? number_format($rec['precio_estimado'], 4) : '&nbsp;');
					$tpl->assign('produccion_estimada', $rec['produccion_estimada'] != 0 ? number_format($rec['produccion_estimada'], 2) : '&nbsp;');
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/EstimacionAlzaPreciosProduccion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0)));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0)), ' selected');

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
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
