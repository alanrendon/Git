<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
			/*
			@ Intervalo de años
			*/
			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();
				
				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios =  array_merge($anios, range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				function between($a) {
					if ($a < date('Y')) {
						return 'mv.fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1, $a)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, 12, 31, $a)) . '\'';
					}
					else {
						return 'mv.fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1, $a)) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, date('n'), 0, $a)) . '\'';
					}
				}
				
				sort($anios);
				
				$conditions[] = '(' . implode(' OR ', array_map('between', $anios)) . ')';
			}
			
			$conditions[] = 'mv.codmp IN (' . implode(', ', $_REQUEST['codmp']) . ')';
			$conditions[] = 'mv.tipo_mov = \'TRUE\'';
			$conditions[] = 'mv.cantidad > 0';
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$conditions[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ Intervalo de compañías
			*/
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
					$conditions[] = 'mv.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (count($anios) > 1) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS
								nombre_cia,
						EXTRACT(year from fecha)
							AS
								anio,
						sum(cantidad)
							AS
								pollos
					FROM
							mov_inv_real
								mv
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
					WHERE
						' . implode(' AND ', $conditions) . '
					GROUP BY
						num_cia,
						nombre_cia,
						anio
					ORDER BY
						num_cia,
						anio
				';
				
				$result = $db->query($sql);
				
				if (!$result) {
					die('NO HAY RESULTADOS');
				}
				
				$titulos = $anios;
				
				if (!function_exists('array_combine')) {
					function array_combine($keys, $values) {
						$array = array();
						
						foreach ($keys as $k => $v) {
							$array[$v] = $values[$k];
						}
						
						return $array;
					}
				}
				
				$datos = array();
				
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$datos[$num_cia]['nombre'] = $r['nombre_cia'];
						$datos[$num_cia]['pollos'] = array_combine($anios, array_fill(0, count($anios), 0));
					}
					$datos[$num_cia]['pollos'][$r['anio']] = $r['pollos'];
				}
				
				$totales = array_combine($anios, array_fill(0, count($anios), 0));
			}
			else {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS
								nombre_cia,
						EXTRACT(month from fecha)
							AS
								mes,
						sum(cantidad)
							AS
								pollos
					FROM
							mov_inv_real
								mv
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
					WHERE
						' . implode(' AND ', $conditions) . '
					GROUP BY
						num_cia,
						nombre_cia,
						mes
					ORDER BY
						num_cia,
						mes
				';
				
				$result = $db->query($sql);
				
				if (!$result) {
					die('NO HAY RESULTADOS');
				}
				
				$titulos = array(
					'Ene',
					'Feb',
					'Mar',
					'Abr',
					'May',
					'Jun',
					'Jul',
					'Ago',
					'Sep',
					'Oct',
					'Nov',
					'Dic'
				);
				
				$datos = array();
				
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$datos[$num_cia]['nombre'] = $r['nombre_cia'];
						$datos[$num_cia]['pollos'] = array_fill(1, 12, 0);
					}
					$datos[$num_cia]['pollos'][$r['mes']] = $r['pollos'];
				}
				
				$totales = array_fill(1, 12, 0);
			}
			
			function filter($value) {
				return $value != 0;
			}
			
			$tpl = new TemplatePower('plantillas/ros/VentaPollosAnualListado.tpl');
			$tpl->prepare();
			
			$maxfilas = 41;
			$filas = $maxfilas;
			
			foreach ($datos as $cia => $datos) {
				if ($filas == $maxfilas) {
					$filas = 0;
					
					$tpl->newBlock('listado');
					
					$tpl->assign('salto', '<br style="page-break-after:always;" />');
					
					if (count($anios) == 1) {
						$tpl->assign('anio', ' ' . $anios[0]);
					}
					
					foreach (array_merge($titulos, array('Total', 'Prom.')) as $t) {
						$tpl->newBlock('titulo');
						$tpl->assign('titulo', $t);
					}
				}
				
				$tpl->newBlock('fila');
				$tpl->assign('num_cia', $cia);
				$tpl->assign('nombre', $datos['nombre']);
				
				foreach ($datos['pollos'] as $key => $pollos) {
					$tpl->newBlock('valor');
					$tpl->assign('valor', $pollos != 0 ? number_format($pollos, 0, '.', ',') : '&nbsp;');
					
					$totales[$key] += $pollos;
				}
				
				$total = array_sum($datos['pollos']);
				$promedio = $total / count(array_filter($datos['pollos'], 'filter'));
				
				$tpl->newBlock('valor');
				$tpl->assign('valor', '<strong>' . number_format($total, 0, '.', ',') . '</strong>');
				
				$tpl->newBlock('valor');
				$tpl->assign('valor', '<strong>' . number_format($promedio, 0, '.', ',') . '</strong>');
				
				$filas++;
			}
			
			$tpl->newBlock('totales');
			foreach ($totales as $t) {
				$tpl->newBlock('total');
				$tpl->assign('total', $t != 0 ? number_format($t, 0, '.', ',') : '&nbsp;');
			}
			
			$tpl->newBlock('total');
			$tpl->assign('total', number_format(array_sum($totales), 0, '.', ','));
			
			$tpl->newBlock('total');
			$tpl->assign('total', number_format(array_sum($totales) / count(array_filter($totales, 'filter')), 0, '.', ','));
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ros/VentaPollosAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
