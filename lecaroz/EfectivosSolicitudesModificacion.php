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

/*if ($_SESSION['iduser'] != 1) {
	die('<div style="font-size:16pt; border:solid 2px #000; padding:30px 10px;">ESTOY HACIENDO MODIFICACIONES AL PROGRAMA, NO ME LLAMEN PARA PREGUNTAR CUANDO QUEDARA, YO LES AVISO.</div>');
}*/

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'obtener_cia':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
			
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
			
			$query = $db->query($sql);
			
			if ($query) {
				echo utf8_encode($query[0]['nombre_corto']);
			}
			
			break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/EfectivosSolicitudesModificacionInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'fecha_autorizacion IS NOT NULL';
			}
			
			if (!isset($_REQUEST['aclarados'])) {
				$condiciones[] = 'fecha_autorizacion IS NULL';
			}
			
			$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					UPPER(descripcion)
						AS descripcion,
					fecha_solicitud
						AS solicitado,
					fecha_autorizacion
						AS autorizado,
					fecha_modificacion
						AS aclarado
				FROM
					modificacion_efectivos me
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					num_cia,
					solicitado,
					autorizado,
					aclarado
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/EfectivosSolicitudesModificacionResultado.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				foreach ($result as $num => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$row_color = FALSE;
						
						$cont = 0;
						$afiliados = 0;
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('descripcion', utf8_encode($rec['descripcion']));
					$tpl->assign('solicitado', $rec['solicitado']);
					$tpl->assign('autorizado', $rec['autorizado'] != '' ? $rec['autorizado'] : '&nbsp;');
					$tpl->assign('aclarado', $rec['aclarado'] != '' ? $rec['aclarado'] : '&nbsp;');
					
					$tpl->assign('mod_disabled', $rec['autorizado'] == '' || $rec['aclarado'] != '' ? '_gray' : '');
					$tpl->assign('baja_disabled', $rec['aclarado'] != '' ? '_gray' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/pan/EfectivosSolicitudesModificacionAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/EfectivosSolicitudesModificacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
