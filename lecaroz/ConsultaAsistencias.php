<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'getCia':
			$condiciones[] = 'num_cia BETWEEN 900 AND 998';
			
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				$condiciones[] = 'fecha_baja IS NULL';
				
				$sql = '
					SELECT
						id
							AS
								value,
						ap_paterno || \' \' || ap_materno || \' \' || nombre
							AS
								text
					FROM
						catalogo_trabajadores
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						text
				';
				$empleados = $db->query($sql);
				
				echo '{"nombre_cia":"' . $result[0]['nombre_corto'] . '","empleados":[';
				
				$data = array();
				if ($empleados) {
					foreach ($empleados as $r) {
						$data[] = '{"value":"' . $r['value'] . '","text":"' . utf8_encode($r['text']) . '"}';
					}
				}
				
				echo implode(',', $data) . ']}';
			}
		break;
		
		case 'generar':
			$condiciones = array();
			
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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Empleados
			*/
			if (isset($_REQUEST['idemp'])) {
				$condiciones[] = 'ct.id IN (' . implode(', ', $_REQUEST['idemp']) . ')';
			}
			
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					ct.id
						AS
							idemp,
					ct.num_emp,
					ct.ap_paterno || \' \' || ct.ap_materno || \' \' || ct.nombre
						AS
							nombre_emp
				FROM
						catalogo_trabajadores
							ct
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ct.num_cia,
					ct.ap_paterno,
					ct.ap_materno,
					ct.nombre
			';
			$empleados = $db->query($sql);
			
			if ($empleados) {
				/*
				@ Convertir fechas de DD/MM/YYYY a YYYY/MM/DD
				*/
				$pieces = explode('/', $_REQUEST['fecha1']);
				$fecha1 = date('Y/m/d', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]));
				
				$pieces = explode('/', $_REQUEST['fecha2']);
				$fecha2 = date('Y/m/d', mktime(0, 0, 0, $pieces[1], $pieces[0], $pieces[2]));
				
				/*
				@ Crear rango de días
				*/
				$dias = array();
				
				$current = strtotime($fecha1);
				$last = strtotime($fecha2);
				
				while ($current <= $last) {
					$dias[] = date('d/m/Y', $current);
					$current = strtotime(date('Y/m/d', $current) . ' + 1 day');
				}
				
				/*
				@ Si no existe la función array_combine() crearla
				*/
				if (!function_exists('array_combine')) {
					function array_combine($keys, $values) {
						$array = array();
						
						foreach ($keys as $k => $v) {
							$array[$v] = $values[$k];
						}
						
						return $array;
					}
				}
				
				/*
				@ Crear un arreglo de asistencias por empleado y ponerlo a status 1 (Asistencia)
				*/
				$asistencias = array();
				foreach ($empleados as $e) {
					$asistencias[$e['idemp']] = array_combine($dias, array_fill(0, count($dias), 1));
				}
				
				$condiciones = array();
				
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
						$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				$condiciones[] = 'ct.fecha_baja IS NULL';
				$condiciones[] = 'ca.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				
				if (isset($_REQUEST['idemp'])) {
					$condiciones[] = 'ct.id IN (' . implode(', ', $_REQUEST['idemp']) . ')';
				}
				
				/*
				@ Obtener estatus de asistencias y asignarlos a las asistencias de los empleados
				*/
				$sql = '
					SELECT
						ca.id,
						ca.idemp,
						ca.fecha,
						ca.status
					FROM
							control_asistencias
								ca
						LEFT JOIN
							catalogo_trabajadores
								ct
									ON
										(
											ct.id = ca.idemp
										)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						ca.idemp,
						ca.fecha
				';
				$status = $db->query($sql);
				
				if ($status) {
					foreach ($status as $s) {
						$asistencias[$s['idemp']][$s['fecha']] = $s['status'];
					}
				}
				
				/*
				@ Reordenar registros
				*/
				foreach ($empleados as $k => $v) {
					$empleados[$k]['asistencias'] = $asistencias[$v['idemp']];
				}
				
				$tpl = new TemplatePower('plantillas/zap/ReporteAsistencias.tpl');
				$tpl->prepare();
				
				$semana = array(
					0 => 'D',
					1 => 'L',
					2 => 'M',
					3 => 'M',
					4 => 'J',
					5 => 'V',
					6 => 'S'
				);
				
				$leyenda_status = array(
					1 => 'A',
					2 => 'F',
					3 => 'I',
					4 => 'D',
					5 => 'V'
				);
				
				$num_cia = NULL;
				foreach ($empleados as $i => $e) {
					if ($num_cia != $e['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
							
							$tpl->newBlock('foot');
							$tpl->assign('span', 2 + count($dias));
							
							foreach ($totales_gral as $k => $v) {
								$tpl->assign($k, $v > 0 ? $v : NULL);
							}
							$tpl->assign('T', array_sum($totales_gral) - $totales_gral[2]);
							
							$tpl->newBlock('leyenda');
						}
						
						$num_cia = $e['num_cia'];
						
						$totales_gral = $totales = array(
							1 => 0,
							2 => 0,
							3 => 0,
							4 => 0,
							5 => 0
						);
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $e['nombre_cia']);
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);
						
						foreach ($dias as $dia) {
							$tpl->newBlock('th');
							
							$d = explode('/', $dia);
							
							$tpl->assign('dia_mes', $d[0]);
							$tpl->assign('dia_semana', $semana[date('w', mktime(0, 0, 0, $d[1], $d[0], $d[2]))]);
						}
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_emp', $e['num_emp']);
					$tpl->assign('nombre_emp', utf8_encode($e['nombre_emp']));
					
					$totales = array(
						1 => 0,
						2 => 0,
						3 => 0,
						4 => 0,
						5 => 0
					);
					
					foreach ($e['asistencias'] as $dia => $status) {
						$tpl->newBlock('td');
						$tpl->assign('status', $leyenda_status[$status]);
						$tpl->assign('color', $status);
						
						$totales[$status]++;
						$totales_gral[$status]++;
					}
					
					foreach ($totales as $k => $v) {
						$tpl->assign('row.' . $k, $v > 0 ? $v : '&nbsp;&nbsp;&nbsp;&nbsp;');
					}
					
					$tpl->assign('row.T', array_sum($totales) - $totales[2]);
				}
				if ($num_cia != NULL) {
					$tpl->newBlock('foot');
					$tpl->assign('span', 2 + count($dias));
					
					foreach ($totales_gral as $k => $v) {
						$tpl->assign($k, $v > 0 ? $v : NULL);
					}
					$tpl->assign('T', array_sum($totales_gral) - $totales_gral[2]);
					
					$tpl->newBlock('leyenda');
				}
				
				$tpl->printToScreen();
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/ConsultaAsistencias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
?>
