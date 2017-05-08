<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'getCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			echo $result[0]['nombre_corto'];
		break;
		
		case 'getQuery':
			$conditions1 = array();
			$conditions2 = array();
			
			$conditions1[] = 'c.fecha ' . ($_REQUEST['fecha2'] != '' ? 'BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'' : '= \'' . $_REQUEST['fecha1'] . '\'');
			$conditions2[] = 'fecha ' . ($_REQUEST['fecha2'] != '' ? 'BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'' : '= \'' . $_REQUEST['fecha1'] . '\'');
			
			if ($_REQUEST['num_cia'] > 0) {
				$conditions1[] = 'num_cia = ' . $_REQUEST['num_cia'];
				$conditions2[] = 'num_cia = ' . $_REQUEST['num_cia'];
			}
			if ($_REQUEST['admin'] > 0) {
				$conditions1[] = 'idadministrador = ' . $_REQUEST['admin'];
				$conditions2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					fecha_mov
						AS
							fecha
				FROM
					fact_rosticeria
				WHERE
					fecha_mov
						BETWEEN
								\'' . $_GET['fecha1'] . '\'
							AND
								\'' . $_GET['fecha2'] . '\'
				GROUP BY
					fecha
				ORDER BY
					fecha
			';
			
			$sql .= '
				SELECT
					num_cia,
					fecha_mov
						AS
							fecha,
					sum(cantidad)
						AS
							piezas
				FROM
					fact_rosticeria
				WHERE
						fecha_mov
							BETWEEN
									\'' . $_GET['fecha1'] . '\'
								AND
									\'' . $_GET['fecha2'] . '\'
					AND
						codmp
							IN
								(
									\'' . implode(', ', $cods) . '\'
								) GROUP BY num_cia, fecha ORDER BY num_cia, fecha';
			
			$tpl = new TemplatePower('plantillas/fac/ConsultaCompraPollos.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('result');
				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2'] != '' ? $_REQUEST['fecha2'] : $_REQUEST['fecha1']);
				
				
			}
			else {
				$tpl->newBlock('no_result');
			}
			
			$tpl->printToScreen();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ConsultaCompraPollos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('n'), date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
		
';
$admins = $db->query($sql);

if ($admins)
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}

$tpl->printToScreen();
?>