<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'inicio':
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoNotariosInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					cod_notario
						AS id,
					nombre,
					num_notario
				FROM
					catalogo_notario
				ORDER BY
					nombre
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
					$tpl->assign('num_notario', $row['num_notario']);
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/CatalogoNotariosAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_notario (
						cod_notario,
						nombre,
						num_notario
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(cod_notario) + 1
							FROM
								catalogo_notario
						), 1),
						\'' . utf8_decode($_REQUEST['nombre']) . '\',
						' . (isset($_REQUEST['num_notario']) && $_REQUEST['num_notario'] > 0 ? $_REQUEST['num_notario'] : 'NULL') . '
					)
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					cod_notario
						AS id,
					nombre,
					num_notario
				FROM
					catalogo_notario
				WHERE
					cod_notario = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoNotariosModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			$tpl->assign('num_notario', $result[0]['num_notario']);
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_notario
				SET
					nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\',
					num_notario = ' . (isset($_REQUEST['num_notario']) && $_REQUEST['num_notario'] > 0 ? $_REQUEST['num_notario'] : 'NULL') . '
				WHERE
					cod_notario = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_notario
				WHERE
					cod_notario = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CatalogoNotarios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
