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
		case 'mp':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
						codmp = ' . $_REQUEST['codmp'] . '
					AND
						codmp
							IN
								(
									SELECT
										codmp
									FROM
										precios_guerra
									WHERE
										precio_compra > 0
									GROUP BY
										codmp
								)
			';
			$result = $db->query($sql);
			
			echo $result[0]['nombre'];
		break;
		
		case 'actualizar':
			$cias = array();
			$omitir = array();
			$pros = array();
			$precio_compra = get_val($_REQUEST['precio_compra']);
			
			$conditions[] = 'precio_compra > 0';
			$conditions[] = 'codmp = ' . $_REQUEST['codmp'];
			
			// Intervalo de compañías
			if (isset($_REQUEST['cias'])) {
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
			
			// Intervalo de compañías omitidas
			if (isset($_REQUEST['omitir'])) {
				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}
				
				if (count($omitir) > 0) {
					$conditions[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
				}
			}
			
			// Intervalo de proveedores
			if (isset($_REQUEST['pros'])) {
				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}
				
				if (count($pros) > 0) {
					$conditions[] = 'num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}
			
			$sql = '
				UPDATE
					precios_guerra
				SET
					precio_compra = ' . $precio_compra . '
				WHERE
					' . implode(' AND ', $conditions) . '
			';
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ros/ActualizarPreciosCompra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
