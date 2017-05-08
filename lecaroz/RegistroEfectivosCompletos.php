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
		case 'obtener':
			$condiciones[] = 'fecha < now()::date';
			$condiciones[] = '(efe = \'FALSE\' OR exp = \'FALSE\' OR gas = \'FALSE\' OR pro = \'FALSE\' OR pas = \'FALSE\' OR tp.status < 0)';
			
			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto,
					MAX(fecha)
						AS
							fecha
				FROM
						total_panaderias tp
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						catalogo_operadoras co
							USING
								(
									idoperadora
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_corto
				ORDER BY
					num_cia
			';
			$cias = $db->query($sql);
			
			if ($cias) {
				if (!in_array($_SESSION['iduser'], array(1, 4))) {
					$sql = '
						SELECT
							MIN(tp.status)
								AS
									status
						FROM
								total_panaderias tp
							LEFT JOIN
								catalogo_companias cc
									USING
										(
											num_cia
										)
							LEFT JOIN
								catalogo_operadoras co
									USING
										(
											idoperadora
										)
						WHERE
								tp.status < 0
							AND
								iduser = ' . $_SESSION['iduser'] . '
					';
					$status = $db->query($sql);
				}
				else {
					$status = FALSE;
				}
				
				echo '{"cias":[';
				
				$data = array();
				if ($cias) {
					foreach ($cias as $c) {
						/*
						@ [07-Jun-2010] Obtener estatus de errores de efectivo por compañía
						*/
						$sql = '
							SELECT
								tp.status
							FROM
									total_panaderias tp
							WHERE
									tp.num_cia = ' . $c['num_cia'] . '
								AND
									tp.status < 0
							LIMIT
								1
						';
						$status = $db->query($sql);
						
						if ($status) {
							switch ($status[0]['status']) {
								case -1:
									$status_keyword = '[E]';
								break;
								
								case -2:
									$status_keyword = '[C]';
								break;
								
								case -3:
									$status_keyword = '[EC]';
								break;
							}
						}
						else {
							$status_keyword = '';
						}
						
						$data[] = '{"value":"' . $c['num_cia'] . '","text":"' . utf8_encode('[' . $c['fecha'] . ']' . $status_keyword . ' ' . $c['num_cia'] . ' ' . $c['nombre_corto']) . '"' . ($status ? ',"disabled":"true","styles":{"color":"#C00"}' : '') . '}';
					}
				}
				
				echo implode(',', $data) . ']}';
			}
		break;
		
		case 'registrar':
			if (isset($_REQUEST['cias'])) {
				$sql = '
					UPDATE
						total_panaderias
					SET
						efe = \'TRUE\',
						exp = \'TRUE\',
						gas = \'TRUE\',
						pro = \'TRUE\',
						pas = \'TRUE\'
					WHERE
							fecha <= \'' . $_REQUEST['fecha'] . '\'
						AND
							(
									efe = \'FALSE\'
								OR
									exp = \'FALSE\'
								OR
									gas = \'FALSE\'
								OR
									pro = \'FALSE\'
								OR
									pas = \'FALSE\'
							)
						AND
							num_cia
								IN
									(
										' . implode(', ', $_REQUEST['cias']) . '
									)
				';
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/RegistroEfectivosCompletos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

$tpl->printToScreen();
?>
