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
			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCiasInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'pp.total > 0';

			$condiciones[] = "(pp.num_proveedor, pp.num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014')";
			
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
					$condiciones[] = 'pp.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					pp.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					SUM(pp.total)
						AS saldo,
					MIN(pp.fecha)
						AS ultima
				FROM
					pasivo_proveedores pp
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				GROUP BY
					pp.num_cia,
					cc.nombre_corto
				HAVING
					NOW()::DATE - MIN(pp.fecha) > 90
				ORDER BY
					pp.num_cia
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCiasConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				$total = 0;

				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('saldo', number_format($row['saldo'], 2));
					$tpl->assign('ultima', $row['ultima']);

					$tpl->assign('data_saldo', htmlentities(json_encode(array(
						'num_cia' => $row['num_cia']
					))));

					$tpl->assign('data_ultima', htmlentities(json_encode(array(
						'num_cia' => $row['num_cia'],
						'fecha' => $row['ultima']
					))));

					$total += $row['saldo'];

					$tpl->assign('_ROOT.total', number_format($total, 2));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;

		case 'detalle':
			$condiciones = array();

			$condiciones[] = 'pp.num_cia = ' . $_REQUEST['num_cia'];

			$condiciones[] = "(pp.num_proveedor, pp.num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014')";

			if (isset($_REQUEST['fecha']))
			{
				$condiciones[] = 'pp.fecha = \'' . $_REQUEST['fecha'] . '\'';
			}
			
			$sql = '
				SELECT
					f.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					f.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					f.num_fact,
					f.fecha,
					f.total
						AS importe,
					copia_fac
						AS status
				FROM
					facturas f
					LEFT JOIN pasivo_proveedores pp
						USING (num_proveedor, num_fact)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = f.num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					fecha ASC,
					pp.id ASC
					' . (isset($_REQUEST['fecha']) ? 'LIMIT 1' : '') . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCiaDetalle.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('reporte');
			
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('fecha', date('d/m/Y'));
			
			if ($result) {
				$total = 0;

				foreach ($result as $rec) {
					$tpl->newBlock('row');
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('importe', number_format($rec['importe'], 2));
					$tpl->assign('status', $rec['status'] == 't' ? '<img src="/lecaroz/iconos/accept.png" width="16" height="16">' : '&nbsp;');
					
					$total += $rec['importe'];
					
					$tpl->assign('reporte.total', number_format($total, 2));
				}
			}
			
			$tpl->printToScreen();

			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
