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
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoAuditoresInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idauditor
						AS id,
					nombre_auditor
						AS nombre,
					CONCAT_WS(\' \', nombre, apellido)
						AS usuario_asociado
				FROM
					catalogo_auditores cc
					LEFT JOIN auth a
						USING (iduser)
				ORDER BY
					nombre_auditor
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
					$tpl->assign('usuario_asociado', $row['usuario_asociado'] != '' ? utf8_encode($row['usuario_asociado']) : '&nbsp;');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/CatalogoAuditoresAlta.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					iduser
						AS value,
					CONCAT_WS(\' \', nombre, apellido)
						AS text,
					COALESCE((
						SELECT
							TRUE
						FROM
							catalogo_auditores
						WHERE
							iduser = a.iduser
					), FALSE)
						AS disabled
				FROM
					auth a
				ORDER BY
					text
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $r) {
					$tpl->newBlock('iduser');
					$tpl->assign('value', $r['value']);
					$tpl->assign('text', utf8_encode($r['text']));
					$tpl->assign('disabled', $r['disabled'] == 't' ? ' disabled="disabled" class="red underline"' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_auditores (
						idauditor,
						nombre_auditor,
						iduser
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(idauditor) + 1
							FROM
								catalogo_auditores
						), 1),
						\'' . utf8_decode($_REQUEST['nombre']) . '\',
						' . (isset($_REQUEST['iduser']) && $_REQUEST['iduser'] > 0 ? $_REQUEST['iduser'] : 'NULL') . '
					)
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					idauditor
						AS id,
					nombre_auditor
						AS nombre,
					iduser
				FROM
					catalogo_auditores cc
				WHERE
					idauditor = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CatalogoAuditoresModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			
			$sql = '
				SELECT
					iduser
						AS value,
					CONCAT_WS(\' \', nombre, apellido)
						AS text,
					COALESCE((
						SELECT
							TRUE
						FROM
							catalogo_auditores
						WHERE
							iduser = a.iduser
							' . ($result[0]['iduser'] > 0 ? 'AND iduser != ' . $result[0]['iduser'] : '') . '
					), FALSE)
						AS disabled
				FROM
					auth a
				ORDER BY
					text
			';
			
			$users = $db->query($sql);
			
			if ($users) {
				foreach ($users as $r) {
					$tpl->newBlock('iduser');
					$tpl->assign('value', $r['value']);
					$tpl->assign('text', utf8_encode($r['text']));
					$tpl->assign('disabled', $r['disabled'] == 't' ? ' disabled="disabled" class="red underline"' : '');
					
					if ($r['value'] == $result[0]['iduser']) {
						$tpl->assign('selected', ' selected="selected" class="green underline"');
					}
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_auditores
				SET
					nombre_auditor = \'' . utf8_decode($_REQUEST['nombre']) . '\',
					iduser = ' . (isset($_REQUEST['iduser']) && $_REQUEST['iduser'] > 0 ? $_REQUEST['iduser'] : 'NULL') . '
				WHERE
					idauditor = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_auditores
				WHERE
					idauditor = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$sql .= '
				UPDATE
					catalogo_companias
				SET
					idauditor = NULL
				WHERE
					idauditor = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CatalogoAuditores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
