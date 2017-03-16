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
			$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadoresAdminInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idtipobaja
						AS tipo,
					nombre_tipo_baja
						AS descripcion
				FROM
					catalogo_tipos_baja
				WHERE
					tsbaja IS NULL
				ORDER BY
					num
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				foreach ($query as $i => $row) {
					$tpl->newBlock('tipo');
					$tpl->assign('tipo', $row['tipo']);
					$tpl->assign('descripcion', utf8_encode($row['descripcion']) . ($i < count($query) - 1 ? '<br />' : ''));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'tsdel IS NULL';
			
			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			if (isset($_REQUEST['ap_paterno']) && $_REQUEST['ap_paterno'] != '') {
				$condiciones[] = 'ap_paterno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'';
			}
			
			if (isset($_REQUEST['ap_materno']) && $_REQUEST['ap_materno'] != '') {
				$condiciones[] = 'ap_materno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'';
			}
			
			if (isset($_REQUEST['tipo'])) {
				$condiciones[] = 'idtipobaja IN (' . implode(', ', $_REQUEST['tipo']) . ')';
			}
			
			$sql = '
				SELECT
					id,
					folio,
					tsins::DATE
						AS alta,
					CONCAT_WS(\' \', ap_paterno, ap_materno, nombre)
						AS nombre,
					observaciones,
					(
						SELECT
							num || \' \' || nombre_tipo_baja
						FROM
							catalogo_tipos_baja
						WHERE
							idtipobaja = lnt.idtipobaja
					)
						AS tipo
				FROM
					lista_negra_trabajadores lnt
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ap_paterno,
					ap_materno,
					nombre
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadoresAdminConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
					$tpl->assign('tipo', utf8_encode($row['tipo']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadoresAdminAlta.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idtipobaja
						AS value,
					num || \' \' || nombre_tipo_baja
						AS text
				FROM
					catalogo_tipos_baja
				WHERE
					tsbaja IS NULL
				ORDER BY
					num
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				foreach ($query as $row) {
					$tpl->newBlock('tipo');
					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode($row['text']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					lista_negra_trabajadores (
						folio,
						nombre,
						ap_paterno,
						ap_materno,
						idtipobaja,
						observaciones,
						idins
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(folio)
							FROM
								lista_negra_trabajadores
						), 0) + 1,
						\'' . (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '') . '\',
						\'' . (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '') . '\',
						\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
						' . $_REQUEST['tipo'] . ',
						\'' . (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '') . '\',
						' . $_SESSION['iduser'] . '
					)
			';
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					nombre,
					ap_paterno,
					ap_materno,
					idtipobaja
						AS tipo,
					observaciones
				FROM
					lista_negra_trabajadores lnt
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$row = $result[0];
			
			$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadoresAdminModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($row['nombre']));
			$tpl->assign('ap_paterno', utf8_encode($row['ap_paterno']));
			$tpl->assign('ap_materno', utf8_encode($row['ap_materno']));
			$tpl->assign('observaciones', utf8_encode($row['observaciones']));
			
			$sql = '
				SELECT
					idtipobaja
						AS value,
					num || \' \' || nombre_tipo_baja
						AS text
				FROM
					catalogo_tipos_baja
				WHERE
					tsbaja IS NULL
				ORDER BY
					num
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				foreach ($query as $tipo) {
					$tpl->newBlock('tipo');
					$tpl->assign('value', $tipo['value']);
					$tpl->assign('text', utf8_encode($tipo['text']));
					
					if ($tipo['value'] == $row['tipo']) {
						$tpl->assign('selected', ' selected');
					}
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					lista_negra_trabajadores
				SET
					nombre = \'' . (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '') . '\',
					ap_paterno = \'' . (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '') . '\',
					ap_materno = \'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
					observaciones = \'' . (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '') . '\',
					idtipobaja = ' . $_REQUEST['tipo'] . ',
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				UPDATE
					lista_negra_trabajadores
				SET
					tsdel = NOW(),
					iddel = ' . $_SESSION['iduser'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/ListaNegraTrabajadoresAdmin.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
