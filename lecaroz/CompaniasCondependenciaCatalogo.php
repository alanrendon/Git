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
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaCatalogoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'tsbaja IS NULL';
			
			if (isset($_REQUEST['cias_pri']) && trim($_REQUEST['cias_pri']) != '') {
				$cias_pri = array();
				
				$pieces = explode(',', $_REQUEST['cias_pri']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias_pri[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias_pri[] = $piece;
					}
				}
				
				if (count($cias_pri) > 0) {
					$condiciones[] = 'num_cia_pri IN (' . implode(', ', $cias_pri) . ')';
				}
			}
			
			if (isset($_REQUEST['cias_sec']) && trim($_REQUEST['cias_sec']) != '') {
				$cias_sec = array();
				
				$pieces = explode(',', $_REQUEST['cias_sec']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias_sec[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias_sec[] = $piece;
					}
				}
				
				if (count($cias_sec) > 0) {
					$condiciones[] = 'cias_sec IN (' . implode(', ', $cias_sec) . ')';
				}
			}
			
			$sql = '
				SELECT
					iddependencia
						AS id,
					num_cia_pri,
					ccp.nombre
						AS nombre_cia_pri,
					num_cia_sec,
					ccs.nombre
						AS nombre_cia_sec
				FROM
					cias_condependencia cc
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = cc.num_cia_pri)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = cc.num_cia_sec)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia_pri,
					num_cia_sec
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaCatalogoResultado.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia_pri = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia_pri != $rec['num_cia_pri']) {
						$num_cia_pri = $rec['num_cia_pri'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia_pri']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia_pri']));
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $rec['num_cia_sec']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia_sec']));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaCatalogoAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}
		break;
		
		case 'doAlta':
			foreach ($_REQUEST['num_cia_pri'] as $i => $num_cia_pri) {
				if ($num_cia_pri > 0 && $_REQUEST['num_cia_sec'][$i] && !$db->query('
					SELECT
						iddependencia
					FROM
						cias_condependencia
					WHERE
						num_cia_pri = ' . $num_cia_pri . '
						AND num_cia_sec = ' . $_REQUEST['num_cia_sec'][$i] . '
						AND tsbaja IS NULL
				') && !$db->query('
					SELECT
						iddependencia
					FROM
						cias_condependencia
					WHERE
						num_cia_pri != ' . $num_cia_pri . '
						AND num_cia_sec = ' . $_REQUEST['num_cia_sec'][$i] . '
						AND tsbaja IS NULL
				')) {
					$db->query('
						INSERT INTO
							cias_condependencia (
								num_cia_pri,
								num_cia_sec
							)
						VALUES (
							' . $num_cia_pri . ',
							' . $_REQUEST['num_cia_sec'][$i] . '
						)
					');
				}
			}
		break;
		
		case 'doBaja':
			 $sql = '
			 	UPDATE
					cias_condependencia
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					iddependencia = ' . $_REQUEST['id'] . '
			 ';
			 
			 $db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
