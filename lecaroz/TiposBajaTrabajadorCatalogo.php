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
			
			$tpl = new TemplatePower('plantillas/nom/TiposBajaTrabajadorCatalogoInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					id_tipo_baja_trabajador
						AS id,
					descripcion
				FROM
					catalogo_tipos_baja_trabajador
				WHERE
					tsbaja IS NULL
				ORDER BY
					id
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('descripcion', utf8_encode($row['descripcion']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/nom/TiposBajaTrabajadorCatalogoAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_tipos_baja_trabajador (
						descripcion,
						idalta
					)
					VALUES (
						\'' . utf8_decode($_REQUEST['descripcion']) . '\',
						' . $_SESSION['iduser'] . '
					)
			';
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					descripcion
				FROM
					catalogo_tipos_baja_trabajador
				WHERE
					id_tipo_baja_trabajador = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/TiposBajaTrabajadorCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('descripcion', utf8_encode($result[0]['descripcion']));
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_tipos_baja_trabajador
				SET
					descripcion = \'' . utf8_decode($_REQUEST['descripcion']) . '\',
					tsmod = NOW(),
					idmod = ' . $_REQUEST['iduser'] . '
				WHERE
					id_tipo_baja_trabajador = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				UPDATE
					catalogo_tipos_baja_trabajador
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					id_tipo_baja_trabajador = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TiposBajaTrabajadorCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
