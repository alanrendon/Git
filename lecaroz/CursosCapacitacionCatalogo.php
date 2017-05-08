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
			$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'tsbaja IS NULL';
			
			if (isset($_REQUEST['status'])) {
				$condiciones[] = 'status IN (' . implode(', ', $_REQUEST['status']) . ')';
			}
			
			$sql = '
				SELECT
					idcursocapacitacion
						AS id,
					nombre_curso,
					descripcion_curso,
					fecha_inicio,
					fecha_termino,
					status
				FROM
					cursos_capacitacion
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					fecha_inicio,
					nombre_curso
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogoConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('result');
				
				$color = FALSE;
				
				foreach ($result as $rec) {
					
					$tpl->newBlock('curso');
					
					$tpl->assign('color', $color ? 'on' : 'off');
					
					$color = !$color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('nombre_curso', utf8_encode($rec['nombre_curso']));
					$tpl->assign('descripcion_curso', utf8_encode(nl2br($rec['descripcion_curso'])));
					$tpl->assign('periodo_aplicacion', $rec['fecha_inicio'] . ' - ' . $rec['fecha_termino']);
					$tpl->assign('blank', $rec['status'] > 0 ? '_blank' : '');
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogoAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'doAlta':
			$sql = '
				INSERT INTO
					cursos_capacitacion (
						nombre_curso,
						descripcion_curso,
						fecha_inicio,
						fecha_termino
					)
					VALUES (
						\'' . utf8_decode($_REQUEST['nombre_curso']) . '\',
						\'' . utf8_decode($_REQUEST['descripcion_curso']) . '\',
						\'' . $_REQUEST['fecha_inicio'] . '\',
						\'' . $_REQUEST['fecha_termino'] . '\'
					)
			' . ";\n";
			
			$db->query($sql);
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					idcursocapacitacion
						AS id,
					nombre_curso,
					fecha_inicio,
					fecha_termino,
					descripcion_curso
				FROM
					cursos_capacitacion
				WHERE
					idcursocapacitacion = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);
			
			$rec = $result[0];
			
			$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $rec['id']);
			$tpl->assign('nombre_curso', utf8_encode($rec['nombre_curso']));
			$tpl->assign('fecha_inicio', $rec['fecha_inicio']);
			$tpl->assign('fecha_termino', $rec['fecha_termino']);
			$tpl->assign('descripcion_curso', utf8_encode($rec['descripcion_curso']));
			
			echo $tpl->getOutputContent();
		break;
		
		case 'doModificar':
			$sql = '
				UPDATE
					cursos_capacitacion
				SET
					nombre_curso = \'' . utf8_decode($_REQUEST['nombre_curso']) . '\',
					fecha_inicio = \'' . $_REQUEST['fecha_inicio'] . '\',
					fecha_termino = \'' . $_REQUEST['fecha_termino'] . '\',
					descripcion_curso = \'' . utf8_decode($_REQUEST['descripcion_curso']) . '\',
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					idcursocapacitacion = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
		break;
		
		case 'doBaja':
			 $sql = '
			 	UPDATE
					cursos_capacitacion
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idcursocapacitacion = ' . $_REQUEST['id'] . '
			 ' . ";\n";
			 
			 $db->query($sql);
		break;
		
		case 'status':
			 $sql = '
			 	UPDATE
					cursos_capacitacion
				SET
					status = 1,
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					idcursocapacitacion = ' . $_REQUEST['id'] . '
			 ' . ";\n";
			 
			 $db->query($sql);
		break;
		
		case 'empleados':
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_emp,
					ct.nombre_completo
						AS nombre_emp,
					fecha
				FROM
					cursos_capacitacion_empleados cce
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = cce.idempleado)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					cce.idcursocapacitacion = ' . $_REQUEST['id'] . '
					AND cce.tsbaja IS NULL
				ORDER BY
					nombre_emp
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogoEmpleados.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				
				foreach ($result as $i => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('emp');
					
					$tpl->assign('row_color', $i % 2 == 0 ? 'off' : 'on');
					
					$row_color = !$row_color;
					
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre_emp', utf8_encode($rec['nombre_emp']));
					$tpl->assign('fecha', $rec['fecha']);
				}
			}
			
			echo $tpl->getOutputContent();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/rh/CursosCapacitacionCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
