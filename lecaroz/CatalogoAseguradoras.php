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
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoAseguradorasInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idaseguradora
						AS id,
					nombre_aseguradora
						AS nombre
				FROM
					catalogo_aseguradoras
				ORDER BY
					nombre_aseguradora
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/CatalogoAseguradorasAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_aseguradoras (
						idaseguradora,
						nombre_aseguradora
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(idaseguradora) + 1
							FROM
								catalogo_aseguradoras
						), 1),
						\'' . utf8_decode($_REQUEST['nombre']) . '\'
					)
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					idaseguradora
						AS id,
					nombre_aseguradora
						AS nombre
				FROM
					catalogo_aseguradoras cc
				WHERE
					idaseguradora = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoAseguradorasModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_aseguradoras
				SET
					nombre_aseguradora = \'' . utf8_decode($_REQUEST['nombre']) . '\'
				WHERE
					idaseguradora = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_aseguradoras
				WHERE
					idaseguradora = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$sql .= '
				UPDATE
					catalogo_companias
				SET
					idaseguradora = NULL
				WHERE
					idaseguradora = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CatalogoAseguradoras.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
