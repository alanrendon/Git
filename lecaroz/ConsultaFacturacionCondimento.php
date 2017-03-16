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
			$conditions = array();
			
			// Intervalo de compañías
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
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
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$conditions[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			// Periodo de búsqueda
			if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
				$conditions[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}
			else {
				$conditions[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
			}
			
			if (!isset($_REQUEST['pendientes'])) {
				$conditions[] = 'folio > 0';
			}
			
			if (!isset($_REQUEST['facturados'])) {
				$conditios[] = 'folio IS NULL';
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto,
					fecha,
					kilos,
					precio,
					importe
				FROM
						facturacion_condimento
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
					' . implode(' AND ', $conditions) . '
				ORDER BY
					num_cia,
					fecha
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ros/ConsultaFacturacionCondimentoListado.tpl');
			$tpl->prepare();
			
			if (!$result) {
				$tpl->newBlock('no_result');
			}
			else {
				$tpl->newBlock('result');
				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', trim($_REQUEST['fecha2']) != '' ? $_REQUEST['fecha2'] : $_REQUEST['fecha1']);
				
				$total = 0;
				$kilos = 0;
				foreach ($result as $r) {
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $r['num_cia']);
					$tpl->assign('nombre', $r['nombre_corto']);
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('kilos', number_format($r['kilos'], 2, '.', ','));
					$tpl->assign('precio', number_format($r['precio'], 2, '.', ','));
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					
					$total += $r['importe'];
					$kilos += $r['kilos'];
				}
				$tpl->assign('result.total', number_format($total, 2, '.', ','));
				$tpl->assign('result.kilos', number_format($kilos, 2, '.', ','));
			}
			
			echo $tpl->getOutPutContent();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ros/ConsultaFacturacionCondimento.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
