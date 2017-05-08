<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
	12 => 'DICIMEBRE'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtener':
			$sql = '
				SELECT
					num_cia,
					importe
				FROM
					ventas_zapaterias
				WHERE
					fecha = \'' . $_REQUEST['fecha'] . '\'
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as &$rec) {
					$rec['num_cia'] = intval($rec['num_cia']);
					$rec['importe'] = floatval($rec['importe']);
				}
				
				echo json_encode($result);
			}
		break;
		
		case 'registrar':
			$sql = '';
			
			foreach ($_REQUEST['importe'] as $i => $importe) {
				$sql_tmp = '
					SELECT
						id
					FROM
						ventas_zapaterias
					WHERE
							num_cia = ' . $_REQUEST['num_cia'][$i] . '
						AND
							fecha = \'' . $_REQUEST['fecha'] . '\'
				';
				$tmp = $db->query($sql_tmp);
				
				$id = $tmp ? $tmp[0]['id'] : FALSE;
				
				$importe = get_val($importe);
				
				if ($id && $importe > 0) {
					$sql .= '
						UPDATE
							ventas_zapaterias
						SET
							importe = ' . $importe . '
						WHERE
							id = ' . $id . '
					' . ";\n";
				}
				else if ($id && $importe == 0) {
					$sql .= '
						DELETE FROM
							ventas_zapaterias
						WHERE
							id = ' . $id . '
					' . ";\n";
				}
				else if (!$id && $importe > 0) {
					$sql .= '
						INSERT INTO
							ventas_zapaterias
								(
									num_cia,
									fecha,
									importe
								)
							VALUES
								(
									' . $_REQUEST['num_cia'][$i] . ',
									\'' . $_REQUEST['fecha'] . '\',
									' . $importe . '
								)
					' . ";\n";
				}
			}
			
			if ($sql != '') {
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/ZapateriasVentasDiarias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS
				nombre_cia
	FROM
		catalogo_companias cc
	WHERE
		num_cia BETWEEN 900 AND 998
	ORDER BY
		num_cia
';

$result = $db->query($sql);

$color = FALSE;
foreach ($result as $rec) {
	$tpl->newBlock('row');
	
	$tpl->assign('color', $color ? 'on' : 'off');
	$color = !$color;
	
	$tpl->assign('num_cia', $rec['num_cia']);
	$tpl->assign('nombre_cia', $rec['nombre_cia']);
}

$tpl->printToScreen();
?>
