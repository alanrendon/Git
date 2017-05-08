<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/jpgraph/jpgraph.php';
include './includes/jpgraph/jpgraph_line.php';

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
	0  => 'ENERO',
	1  => 'FEBRERO',
	2  => 'MARZO',
	3  => 'ABRIL',
	4  => 'MAYO',
	5  => 'JUNIO',
	6  => 'JULio',
	7  => 'AGOSTO',
	8  => 'SEPTIEMBRE',
	9  => 'OCTUBRE',
	10 => 'NOVIEMBRE',
	11 => 'DICIEMBRE'
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
		case 'obtenerDatos':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$cia = $db->query($sql);
			
			if ($cia) {
				$sql = '
					SELECT
						id,
						\'[\' || LPAD(num_emp, 5, \'0\') || \'] \'
							AS
								num,
						COALESCE(ap_paterno, \'\') || \' \' || COALESCE(ap_materno, \'\') || \' \' || COALESCE(nombre, \'\')
							AS
								empleado
					FROM
						catalogo_trabajadores
					WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							fecha_baja IS NULL
					ORDER BY
						empleado
				';
				$empleados = $db->query($sql);
				
				if ($empleados) {
					$data = array(
						'num_cia' => $_REQUEST['num_cia'],
						'nombre_cia' => $cia[0]['nombre_cia'],
						'empleados' => array(
							array(
								'value' => NULL,
								'text' => NULL
							)
						)
					);
					
					foreach ($empleados as $e) {
						$data['empleados'][] = array(
							'value' => $e['id'],
							'text' => $e['num'] . $e['empleado']
						);
					}
					
					echo json_encode($data);
				}
			}
		break;
		
		case 'acta':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$cia = $db->query($sql);
			
			$sql = '
				SELECT
					COALESCE(nombre, \'\') || \' \' || COALESCE(ap_paterno, \'\') || \' \' || COALESCE(ap_materno, \'\')
						AS
							nombre,
					descripcion
						AS
							puesto
				FROM
						catalogo_trabajadores ct
					LEFT JOIN
						catalogo_puestos cp
							USING
								(
									cod_puestos
								)
				WHERE
					id = ' . $_REQUEST['empleado'] . '
			';
			$empleado = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/ActaAdministrativaDocumento.tpl');
			$tpl->prepare();
			
			$tpl->assign('dia', date('j'));
			$tpl->assign('mes', $_meses[date('n')]);
			$tpl->assign('anio', date('Y'));
			$tpl->assign('nombre_cia', $cia[0]['nombre']);
			$tpl->assign('empleado_nombre', $empleado[0]['nombre']);
			$tpl->assign('puesto', $empleado[0]['puesto']);
			$tpl->assign('testigo1', $_REQUEST['testigo1']);
			$tpl->assign('testigo2', $_REQUEST['testigo2']);
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ActaAdministrativa.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
