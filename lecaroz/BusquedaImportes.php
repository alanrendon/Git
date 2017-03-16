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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if ((isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') && (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '')) {
				$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}
			else if (isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') {
				$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
			}
			else if (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '') {
				$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
			}
			
			if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
				$condiciones[] = 'ec.cuenta = ' . $_REQUEST['cuenta'];
			}
			
			if (!isset($_REQUEST['abonos'])) {
				$condiciones[] = 'tipo_mov <> \'FALSE\'';
			}
			
			if (!isset($_REQUEST['cargos'])) {
				$condiciones[] = 'tipo_mov <> \'TRUE\'';
			}
			
			function filter($value) {
				return $value > 0;
			}
			
			$importes = array_map('get_val', $_REQUEST['importe']);
			$importes = array_filter($importes, 'filter');
			
			$condiciones[] = 'ec.importe IN (' . implode(', ', $importes) . ')';
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha,
					fecha_con,
					concepto,
					CASE
						WHEN cuenta = 1 THEN
							\'BANORTE\'
						WHEN cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							banco,
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							importe
						ELSE
							-importe
					END
						AS
							importe
				FROM
						estado_cuenta
							ec
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
					num_cia,
					cuenta,
					fecha,
					importe
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/BusquedaImportesResultado.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('reporte');
				
				$num_cia = NULL;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $r['nombre_cia']);
						
						$banco = NULL;
						
						$total_cia = 0;
					}
					if ($banco != $r['banco']) {
						$banco = $r['banco'];
						
						$tpl->newBlock('banco');
						$tpl->assign('banco', $r['banco']);
						
						$total_banco = 0;
					}
					$tpl->newBlock('row');
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('fecha_con', $r['fecha_con']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('banco', $r['banco']);
					$tpl->assign('importe', number_format(abs($r['importe']), 2, '.', ','));
					$tpl->assign('color', $r['importe'] > 0 ? 'blue' : 'red');
					
					$total_banco += $r['importe'];
					$total_cia += $r['importe'];
					
					$tpl->assign('banco.total', number_format(abs($total_banco), 2, '.', ','));
					$tpl->assign('banco.color', $total_banco > 0 ? 'blue' : 'red');
					
					$tpl->assign('cia.total', number_format(abs($total_cia), 2, '.', ','));
					$tpl->assign('cia.color', $total_cia > 0 ? 'blue' : 'red');
				}
			}
			
			$tpl->printToScreen();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/BusquedaImportes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
