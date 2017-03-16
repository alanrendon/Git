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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtener':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			$dias = date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia BETWEEN 900 AND 998
				ORDER BY
					num_cia
			';
			
			$cias = $db->query($sql);
			
			$data = array();
			
			foreach ($cias as $cia) {
				$data[$cia['num_cia']] = array(
					'nombre_cia' => utf8_encode($cia['nombre_cia']),
					'dias'       => array_fill_keys(range(1, $dias), 0)
				);
			}
			
			$sql = '
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					importe
				FROM
					cometra_separacion_zapaterias
				WHERE
					fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					AND tsbaja IS NULL
				ORDER BY
					num_cia,
					dia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $rec) {
					$data[$rec['num_cia']]['dias'][$rec['dia']] = floatval($rec['importe']);
				}
			}
			
			$tpl = new TemplatePower('plantillas/cometra/ImportesSeparacionZapateriasResultado.tpl');
			$tpl->prepare();
			
			foreach (range(1, $dias) as $dia) {
				$tpl->newBlock('title');
				$tpl->assign('dia', $dia);
			}
			
			$i = 0;
			$row_color = FALSE;
			
			foreach ($data as $num_cia => $d) {
				$tpl->newBlock('row');
				
				$tpl->assign('row_color', $row_color ? 'on' : 'off');
				
				$row_color = !$row_color;
				
				$tpl->assign('i', $i);
				$tpl->assign('num_cia', $num_cia);
				$tpl->assign('nombre_cia', $d['nombre_cia']);
				
				foreach ($d['dias'] as $dia => $importe) {
					$tpl->newBlock('cell');
					$tpl->assign('i', $i);
					$tpl->assign('importe', $importe > 0 ? number_format($importe, 2) : '');
				}
				
				$i++;
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'actualizar':
			$status = array(
				'insertados'   => 0,
				'actualizados' => 0,
				'borrados'     => 0
			);
			
			$sql = '';
			
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				foreach ($_REQUEST['importe' . $i] as $j => $importe) {
					$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $j + 1, $_REQUEST['anio']));
					
					$datos = $db->query('
						SELECT
							idseparacionzap,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							cometra_separacion_zapaterias
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = \'' . $fecha . '\'
							AND tsbaja IS NULL
					');
					
					if (!$datos && get_val($importe) > 0) {
						$sql .= '
							INSERT INTO
								cometra_separacion_zapaterias
									(
										num_cia,
										fecha,
										importe,
										tsalta,
										idalta
									)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										' . get_val($importe) . ',
										NOW(),
										' . $_SESSION['iduser'] . '
									)
						' . ";\n";
						
						$status['insertados']++;
					}
					else if ($datos && get_val($importe) == 0) {
						$sql .= '
							UPDATE
								cometra_separacion_zapaterias
							SET
								tsbaja = NOW(),
								idbaja = ' . $_SESSION['iduser'] . '
							WHERE
								idseparacionzap = ' . $datos[0]['idseparacionzap'] . '
						' . ";\n";
						
						$status['borrados']++;
					}
					else if ($datos && get_val($importe) != $datos[0]['importe']) {
						$sql .= '
							UPDATE
								cometra_separacion_zapaterias
							SET
								tsbaja = NOW(),
								idbaja = ' . $_SESSION['iduser'] . '
							WHERE
								idseparacionzap = ' . $datos[0]['idseparacionzap'] . '
						' . ";\n";
						
						$sql .= '
							INSERT INTO
								cometra_separacion_zapaterias
									(
										num_cia,
										fecha,
										importe,
										tsalta,
										idalta
									)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										' . get_val($importe) . ',
										NOW(),
										' . $_SESSION['iduser'] . '
									)
						' . ";\n";
						
						$status['actualizados']++;
					}
				}
			}
			
			if ($sql != '') {
				$db->query($sql);
			}
			
			echo json_encode($status);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/cometra/ImportesSeparacionZapaterias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$tpl->printToScreen();
?>
