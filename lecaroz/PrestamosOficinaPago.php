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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaPagoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'buscar':
			$condiciones[] = 'po.pagado IS NULL';
			
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
					$condiciones[] = 'po.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					po.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					id_empleado
						AS
							id,
					LPAD(num_emp, 5, \'0\')
						AS
							num,
					COALESCE(ap_paterno, \'\') || \' \' || COALESCE(ap_materno, \'\') || \' \' || COALESCE(ct.nombre, \'\')
							AS
								empleado,
					SUM(
						CASE
							WHEN po.tipo = \'FALSE\' THEN
								importe
							ELSE
								-importe
						END
					)
						AS
							saldo
				FROM
						prestamos_oficina po
					LEFT JOIN
						catalogo_companias cc
							ON
								(
									cc.num_cia = po.num_cia
								)
					LEFT JOIN
						catalogo_trabajadores ct
							ON
								(
									ct.id = po.id_empleado
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					po.num_cia,
					nombre_cia,
					po.id_empleado,
					num,
					empleado
				ORDER BY
					po.num_cia,
					empleado
			';
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaPagoResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				foreach ($result as $i => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						
						$color = !isset($color) ? FALSE : $color;
					}
					
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('id', $rec['id']);
					$tpl->assign('empleado', $rec['num'] . ' ' . $rec['empleado']);
					$tpl->assign('saldo', number_format($rec['saldo'], 2, '.', ','));
					
					$color = !$color;
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'pagar':
			$sql = '';
			
			$cont = 0;
			foreach ($_REQUEST['importe'] as $i => $importe) {
				if (get_val($importe) > 0 && $_REQUEST['fecha'][$i] != '') {
					$sql .= '
						INSERT INTO
							prestamos_oficina
								(
									num_cia,
									fecha,
									id_empleado,
									tipo,
									importe,
									iduser
								)
							VALUES
								(
									' . $_REQUEST['num_cia'][$i] . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									' . $_REQUEST['id'][$i] . ',
									\'TRUE\',
									' . get_val($importe) . ',
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
					
					if (get_val($_REQUEST['saldo'][$i]) == 0) {
						$sql .= '
							UPDATE
								prestamos_oficina
							SET
								pagado = now()
							WHERE
									num_cia = ' . $_REQUEST['num_cia'][$i] . '
								AND
									id_empleado = ' . $_REQUEST['id'][$i] . '
								AND
									pagado IS NULL
						' . ";\n";
					}
					
					$cont++;
				}
			}
			
			$db->query($sql);
			
			echo $cont;
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaPago.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
