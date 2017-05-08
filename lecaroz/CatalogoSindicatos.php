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
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoSindicatosInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idsindicato
						AS id,
					nombre_sindicato
						AS nombre
				FROM
					catalogo_sindicatos
				ORDER BY
					nombre_sindicato
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
			$tpl = new TemplatePower('plantillas/ban/CatalogoSindicatosAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_sindicatos (
						idsindicato,
						nombre_sindicato
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(idsindicato) + 1
							FROM
								catalogo_sindicatos
						), 1),
						\'' . utf8_decode($_REQUEST['nombre']) . '\'
					)
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					idsindicato
						AS id,
					nombre_sindicato
						AS nombre
				FROM
					catalogo_sindicatos cc
				WHERE
					idsindicato = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoSindicatosModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_sindicatos
				SET
					nombre_sindicato = \'' . utf8_decode($_REQUEST['nombre']) . '\'
				WHERE
					idsindicato = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_sindicatos
				WHERE
					idsindicato = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$sql .= '
				UPDATE
					catalogo_companias
				SET
					idsindicato = NULL
				WHERE
					idsindicato = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CatalogoSindicatos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
