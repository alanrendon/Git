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
		case 'consultar':
			$conditions[] = 'num_cia <= 300';
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
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
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					*
				FROM
					(
						SELECT
							num_cia,
							nombre_corto,
							(
								SELECT
									fecha
								FROM
									mov_inv_real
								WHERE
										num_cia = cc.num_cia
									AND
										tipo_mov = \'TRUE\'
									AND
										fecha >= now()::date - interval \'1 months\'
								ORDER BY
									fecha
										DESC
								LIMIT
									1
							)
								AS
									fecha
						FROM
							catalogo_companias
								cc
						WHERE
							' . implode(' AND ', $conditions) . '
					)
						result
				WHERE
					fecha IS NOT NULL
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			if (!$result) {
				echo 'NO HAY RESULTADOS';
			}
			else {
				$tpl = new TemplatePower('plantillas/ped/UltimoDiaCapturadoConsumosListado.tpl');
				$tpl->prepare();
				
				$tpl->newBlock('listado');
				foreach ($result as $r) {
					$tpl->newBlock('fila');
					$tpl->assign('num_cia', $r['num_cia']);
					$tpl->assign('nombre', $r['nombre_corto']);
					$tpl->assign('fecha', $r['fecha']);
				}
				
				$tpl->printToScreen();
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/UltimoDiaCapturadoConsumos.tpl');
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
