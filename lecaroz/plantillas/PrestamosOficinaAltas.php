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
		case 'obtener':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre
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
						LPAD(num_emp, 5, \'0\') || \' \' || ap_paterno || \' \' || ap_materno || \' \' || nombre
							AS
								nombre
					FROM
						catalogo_trabajadores
					WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							fecha_baja IS NULL
					ORDER BY
						ap_paterno,
						ap_materno,
						nombre
				';
				$empleados = $db->query($sql);
				
				if ($empleados) {
					$data = array(
						'nombre_cia' => $cia[0]['nombre'],
						'empleados' => array()
					);
					
					foreach ($empleados as $e) {
						$data['empleados'][] = array(
							'value' => $e['id'],
							'text' => $e['nombre']
						);
					}
					
					echo json_encode($data);
				}
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaAltas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
