<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('Modificando');

$text = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ñ');
$html = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;', '&Ntilde;');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cia':
			$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
			
			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'iduser = ' . $_SESSION['iduser'];
			}
			$sql = '
				SELECT
					nombre_corto
				FROM
						catalogo_companias cc
					LEFT JOIN
						catalogo_operadoras co
							USING
								(
									idoperadora
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre_corto'];
			}
		break;
		
		case 'buscar':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'iduser = ' . $_SESSION['iduser'];
			}
			
			if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0) {
				$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
			}
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			if (isset($_REQUEST['folio']) && $_REQUEST['folio'] > 0) {
				$condiciones[] = 'folio = ' . $_REQUEST['folio'];
			}
			if (isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') {
				if (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '') {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
			}
			if (!isset($_REQUEST['aclarados'])) {
				$condiciones[] = 'fecha_aclarado IS NULL';
			}
			
			$sql = '
				SELECT
					id,
					folio,
					num_cia,
					nombre_corto
						AS
							nombre,
					fecha,
					firma
						AS
							capturista,
					ccp,
					memo,
					fecha_reclamo
						AS
							fecha_hoja
				FROM
						memorandums
							m
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
					LEFT JOIN
						catalogo_operadoras
							co
								USING
									(
										idoperadora
									)
			';
			
			if (count($condiciones) > 0) {
				$sql .= '
					WHERE
						' . implode(' AND ', $condiciones) . '
				';
			}
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ban/ban_con_mem_result.tpl');
				$tpl->prepare();
				
				$color = FALSE;
				foreach ($result as $r) {
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('id', $r['id']);
					$tpl->assign('folio', $r['folio']);
					$tpl->assign('num_cia', $r['num_cia']);
					$tpl->assign('nombre', $r['nombre']);
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('capturista', $r['capturista']);
					$tpl->assign('ccp', $r['ccp']);
					$tpl->assign('memo', strpos($r['memo'], '<BR>') ? substr($r['memo'], 0, strpos($r['memo'], '<BR>')) . '...' : $r['memo']);
					$tpl->assign('fecha_hoja', $r['fecha_hoja']);
					
					$color = !$color;
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'aclarar':
			$sql = '
				UPDATE
					memorandums
				SET
					fecha_aclarado = now()::date,
					iduser = ' . $_SESSION['iduser'] . '
				WHERE
					id
						IN
							(
								' . implode(', ', $_REQUEST['id']) . '
							)
			';
			$db->query($sql);
		break;
		
		case 'memo':
			$sql = '
				SELECT
					folio,
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					EXTRACT(day FROM fecha)
						AS
							dia,
					EXTRACT(month FROM fecha)
						AS
							mes,
					EXTRACT(year FROM fecha)
						AS
							anio,
					(
						SELECT
							nombre_inicio
						FROM
							encargados
						WHERE
								num_cia = m.num_cia
							AND
								anio = EXTRACT(year FROM m.fecha)
							AND
								mes = EXTRACT(month FROM m.fecha)
					)
						AS
							encargado,
					memo
						AS
							texto,
					firma,
					ccp
				FROM
						memorandums
							m
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
					LEFT JOIN
						catalogo_operadoras
							co
								USING
									(
										idoperadora
									)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			$tmp = $db->query($sql);
			$reg = $tmp[0];
			
			$tpl = new TemplatePower('plantillas/ban/memorandum.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('memo');
			$tpl->assign('folio', $reg['folio']);
			$tpl->assign('dia', $reg['dia']);
			$tpl->assign('mes', mes_escrito($reg['mes'], TRUE));
			$tpl->assign('anio', $reg['anio']);
			$tpl->assign('nombre_cia', $reg['nombre_cia']);
			$tpl->assign('encargado', $reg['encargado']);
			$tpl->assign('texto', $reg['texto']);
			$tpl->assign('firma', $reg['firma']);
			$tpl->assign('ccp', $reg['ccp']);
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/ban_con_mem.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>