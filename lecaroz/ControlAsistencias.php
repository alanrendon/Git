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
		case 'getCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
						num_cia
							BETWEEN
									900
								AND
									998
					AND
						num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
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
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							fecha_baja IS NULL
					ORDER BY
						text
				';
				$empleados = $db->query($sql);
				
				echo '{"nombre_cia":"' . $result[0]['nombre_corto'] . '","empleados":[';
				
				if ($empleados) {
					foreach ($empleados as $r) {
						$data[] = '{"value":"' . $r['value'] . '","text":"' . utf8_encode($r['text']) . '"}';
					}
				}
				
				echo implode(',', $data) . ']}';
			}
		break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/zap/ControlAsistenciasInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))));
			$tpl->assign('fecha2', date('d/m/Y'));
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'ct.num_cia = ' . $_REQUEST['num_cia'];
			$condiciones[] = 'ct.fecha_baja IS NULL';
			
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
				
				$condiciones[] = 'ct.num_cia = ' . $_REQUEST['num_cia'];
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
				
				$tpl = new TemplatePower('plantillas/zap/ControlAsistenciasResultado.tpl');
				$tpl->prepare();
				
				$tpl->assign('num_cia', $_REQUEST['num_cia']);
				$tpl->assign('nombre_cia', $empleados[0]['nombre_cia']);
				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2']);
				
				$tpl->assign('span', 2 + count($dias));
				
				$semana = array(
					0 => 'D',
					1 => 'L',
					2 => 'M',
					3 => 'M',
					4 => 'J',
					5 => 'V',
					6 => 'S'
				);
				
				foreach ($dias as $dia) {
					$tpl->newBlock('th');
					
					$d = explode('/', $dia);
					
					$tpl->assign('dia_mes', $d[0]);
					$tpl->assign('dia_semana', $semana[date('w', mktime(0, 0, 0, $d[1], $d[0], $d[2]))]);
				}
				
				$totales_gral = $totales = array(
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					5 => 0
				);
				
				$row_color = FALSE;
				foreach ($empleados as $i => $e) {
					$tpl->newBlock('row');
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					$row_color = !$row_color;
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
						$tpl->assign($status, ' selected');
						
						$color = NULL;
						switch ($status) {
							case 1:
								$color = '00C';
							break;
							
							case 2:
								$color = 'C00';
							break;
							
							case 3:
								$color = '0C0';
							break;
							
							case 4:
								$color = 'F90';
							break;
							
							case 5:
								$color = '60C';
							break;
						}
						$tpl->assign('color', $color);
						
						$tpl->assign('row', $i);
						$tpl->assign('idemp', $e['idemp']);
						$tpl->assign('fecha', $dia);
						
						$totales[$status]++;
						$totales_gral[$status]++;
					}
					
					foreach ($totales as $k => $v) {
						$tpl->assign('row.' . $k, $v > 0 ? $v : NULL);
					}
					
					$tpl->assign('row.T', array_sum($totales) - $totales[2]);
				}
				
				foreach ($totales_gral as $k => $v) {
					$tpl->assign('_ROOT.' . $k, $v > 0 ? $v : NULL);
				}
				$tpl->assign('_ROOT.T', array_sum($totales_gral) - $totales_gral[2]);
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'actualizar':
			$sql = '
				SELECT
					id
				FROM
					control_asistencias
				WHERE
						idemp = ' . $_REQUEST['idemp'] . '
					AND
						fecha = \'' . $_REQUEST['fecha'] . '\'
			';
			$id = $db->query($sql);
			
			if ($id) {
				if ($_REQUEST['status'] > 1) {
					$sql = '
						UPDATE
							control_asistencias
						SET
							status = ' . $_REQUEST['status'] . ',
							iduser = ' . $_SESSION['iduser'] . ',
							tsmod = now()
						WHERE
							id = ' . $id[0]['id'] . '
					';
				}
				else {
					$sql = '
						DELETE FROM
							control_asistencias
						WHERE
							id = ' . $id[0]['id'] . '
					';
				}
			}
			else if ($_REQUEST['status'] > 1) {
				$sql = '
					INSERT INTO
						control_asistencias
							(
								idemp,
								fecha,
								status,
								iduser
							)
						VALUES
							(
								' . $_REQUEST['idemp'] . ',
								\'' . $_REQUEST['fecha'] . '\',
								' . $_REQUEST['status'] . ',
								' . $_SESSION['iduser'] . '
							)
				';
			}
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/ControlAsistencias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
