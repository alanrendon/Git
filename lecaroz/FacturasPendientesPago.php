<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10  => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'listado':
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					pp.num_proveedor
						AS
							num_pro,
					cp.nombre
						AS
							nombre_pro,
					fecha_mov
						AS
							fecha,
					num_fact
						AS
							factura,
					CASE
						WHEN copia_fac = \'TRUE\' THEN
							\'X\'
						ELSE
							\'\'
					END
						AS
							validada,
					total
						AS
							importe
				FROM
						pasivo_proveedores pp
					LEFT JOIN
						catalogo_proveedores cp
							USING
								(num_proveedor)
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				ORDER BY
					num_cia,
					fecha_mov
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->assign('reporte.total', number_format($total, 2, '.', ','));
							
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						
						$num_cia = $rec['num_cia'];
						$nombre_cia = $rec['nombre_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $nombre_cia);
						
						$total = 0;
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', $rec['nombre_pro']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('factura', $rec['factura']);
					$tpl->assign('validada', $rec['validada']);
					$tpl->assign('importe', number_format($rec['importe'], 2, '.', ','));
					
					$total += $rec['importe'];
				}
				
				if ($num_cia != NULL) {
					$tpl->assign('reporte.total', number_format($total, 2, '.', ','));
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPago.tpl');
$tpl->prepare();

$tpl->printToScreen();
?>
