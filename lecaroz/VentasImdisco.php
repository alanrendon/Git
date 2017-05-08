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
		case 'get_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia BETWEEN 900 AND 998
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
		break;
		
		case 'validar':
			if (isset($_REQUEST['num_cia'])) {
				$datos = array();
				
				foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
					if ($num_cia > 0
						&& $_REQUEST['folio'][$i] != '') {
						$datos[] = '(' . $num_cia . ', \'' . $_REQUEST['folio'][$i] . '\')';
					}
				}
				
				if ($datos) {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							fecha,
							folio,
							importe
						FROM
							ventas_imdisco vi
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							tsbaja IS NULL
							AND (num_cia, folio) IN (VALUES ' . implode(', ', $datos) . ')
						ORDER BY
							num_cia,
							fecha,
							folio
					';
					
					$result = $db->query($sql);
					
					if ($result) {
						$tpl = new TemplatePower('plantillas/zap/VentasImdiscoError.tpl');
						$tpl->prepare();
						
						foreach ($result as $i => $rec) {
							$tpl->newBlock('row');
							$tpl->assign('row_color', $i % 2 == 0 ? 'off' : 'on');
							$tpl->assign('num_cia', $rec['num_cia']);
							$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
							$tpl->assign('fecha', $rec['fecha']);
							$tpl->assign('folio', utf8_encode($rec['folio']));
							$tpl->assign('importe', number_format($rec['importe'], 2));
						}
						
						echo $tpl->getOutputContent();
					}
				}
			}
		break;
		
		case 'registrar':
			if (isset($_REQUEST['num_cia'])) {
				$sql = '';
				
				foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
					if ($num_cia > 0
						&& $_REQUEST['fecha'][$i] != ''
						&& $_REQUEST['folio'][$i] != ''
						&& get_val($_REQUEST['importe'][$i]) > 0) {
						$sql .= '
							INSERT INTO
								ventas_imdisco (
									num_cia,
									fecha,
									folio,
									importe,
									idalta
								) VALUES (
									' . $num_cia . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									\'' . $_REQUEST['folio'][$i] . '\',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . $_SESSION['iduser'] . '
								)
						' . ";\n";
					}
				}
				
				if ($sql != '') {
					$db->query($sql);
				}
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/VentasImdisco.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
