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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/ControlAvioInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerCia':
			$condiciones = array();
			
			$condiciones[] = 'cc.num_cia = ' . $_REQUEST['num_cia'];
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 42))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'inv.num_cia = ' . $_REQUEST['num_cia'];
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 42))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					inv.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					inv.codmp,
					cmp.nombre
						AS nombre_mp,
					ca.cod_turno
						AS turno,
					ca.num_orden
				FROM
					inventario_real inv
					LEFT JOIN control_avio ca
						USING (num_cia, codmp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					inv.num_cia,
					ca.num_orden,
					cmp.nombre,
					ca.cod_turno
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/pan/ControlAvioResultado.tpl');
				$tpl->prepare();
				
				$tpl->assign('num_cia', $result[0]['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
				
				$codmp = NULL;
				
				$row_color = FALSE;
				
				foreach ($result as $rec) {
					if ($codmp != $rec['codmp']) {
						$codmp = $rec['codmp'];
						
						$tpl->newBlock('row');
						
						$tpl->assign('row_color', $row_color ? 'on' : 'off');
						
						$row_color = !$row_color;
						
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('codmp', $rec['codmp']);
						$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
					}
					
					if ($rec['turno'] > 0) {
						$tpl->assign('checked' . $rec['turno'], ' checked');
					}
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'actualizar':
			$sql = '
				DELETE FROM
					control_avio
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			' . ";\n";
			
			if (isset($_REQUEST['t'])) {
				foreach ($_REQUEST['t'] as $t) {
					$data = json_decode($t);
					
					$sql .= '
						INSERT INTO
							control_avio (
								num_cia,
								codmp,
								cod_turno
							)
							VALUES (
								' . $data->num_cia . ',
								' . $data->codmp . ',
								' . $data->turno . '
							)
					' . ";\n";
				}
				
				foreach ($_REQUEST['orden'] as $o) {
					$data = json_decode($o);
					
					$sql .= '
						UPDATE
							control_avio
						SET
							num_orden = ' . $data->orden . '
						WHERE
							num_cia = ' . $data->num_cia . '
							AND codmp = ' . $data->codmp . '
					' . ";\n";
				}
			}

			// [01-Jul-2014] Guardar movimiento en la tabla de modificaciones de panaderias
			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						iduser,
						metodo,
						parametros
					)
					VALUES (
						{$_REQUEST['num_cia']},
						{$_SESSION['iduser']},
						'actualizar_control_avio',
						'num_cia={$_REQUEST['num_cia']}'
					);\n
			";
			
			$db->query($sql);
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ControlAvio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
