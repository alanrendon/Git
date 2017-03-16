<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'buscar':
			$condiciones[] = 'tp.status < -1';
			
			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones[] = 'tp.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['op']) && $_REQUEST['op'] > 0) {
				$condiciones[] = 'cc.idoperadora = ' . $_REQUEST['op'];
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') {
				if (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '') {
					$condiciones[] = 'tp.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else {
					$condiciones[] = 'tp.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
			}
			
			$sql = '
				SELECT
					tp.id,
					tp.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					tp.fecha,
					ctes
						AS
							clientes,
					(
						SELECT
							ROUND(AVG(ctes))
						FROM
							captura_efectivos
						WHERE
								num_cia = tp.num_cia
							AND
								fecha BETWEEN tp.fecha - interval \'30 days\' AND tp.fecha
					)
						AS
							promedio
				FROM
						total_panaderias
							tp
					LEFT JOIN
						captura_efectivos
							ce
								USING
									(
										num_cia,
										fecha
									)
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					tp.num_cia,
					tp.fecha
			';
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/adm/ErroresClientesResult.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $r['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
						
						$color = FALSE;
					}
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('id', $r['id']);
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('clientes', number_format($r['clientes']));
					$tpl->assign('promedio', number_format($r['promedio']));
					
					$diferencia = $r['promedio'] - $r['clientes'];
					$porc = abs($r['clientes'] * 100 / $r['promedio'] - 100);
					
					$tpl->assign('diferencia', number_format(abs($diferencia)));
					$tpl->assign('porc', number_format($porc, 2, '.', ',') . '%');
					$tpl->assign('color_diferencia', $diferencia < 0 ? '00C' : 'C00');
					
					if ($porc > 20) {
						$tpl->assign('subr', 'text-decoration:underline;font-weight:bold;');
					}
					
					$color = !$color;
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'autorizar':
			$sql = '
				UPDATE
					total_panaderias
				SET
					status = (
						CASE
							WHEN status = -2 THEN
								1
							WHEN status = -3 THEN
								-1
						END
					)
				WHERE
					id
						IN
							(
								' . implode(', ', $_REQUEST['id']) . '
							)
			';
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/ErroresClientes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idoperadora
			AS
				id,
		nombre_operadora
			AS
				nombre
	FROM
		catalogo_operadoras
	WHERE
		nombre_operadora <> \'SIN ASIGNAR\'
	ORDER BY
		nombre
';
$operadoras = $db->query($sql);

foreach ($operadoras as $o) {
	$tpl->newBlock('op');
	$tpl->assign('id', $o['id']);
	$tpl->assign('nombre', $o['nombre']);
}

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
$administradores = $db->query($sql);

foreach ($administradores as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
