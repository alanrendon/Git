<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

function filter($value) {
	return $value != 0;
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion']) && $_REQUEST['accion'] != '') {
	switch ($_REQUEST['accion']) {
		
		case 'exportar':
			$sql = '
				SELECT
					cp.num_proveedor
						AS "#PRO",
					cp.nombre
						AS "PROVEEDOR",
					emp.num_cia
						AS "#CIA",
					cc.nombre_corto
						AS "COMPAÑIA",
					fecha
						AS "FECHA",
					num_fact
						AS "FACTURA",
					emp.codmp
						AS "CODIGO",
					cmp.nombre
						AS "PRODUCTO",
					(cantidad * contenido) / (
						CASE
							WHEN codmp = 1 THEN
								44
							ELSE
								1
						END
					)
						AS "CANTIDAD",
					importe
						AS "IMPORTE"
				FROM
					entrada_mp emp
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					emp.num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND emp.codmp = ' . $_REQUEST['codmp'] . '
					AND fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) . '\'
				ORDER BY
					emp.num_cia,
					fecha,
					num_fact
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$data = '';
				
				$data .= '"' . implode('","', array_keys($query[0])) . '"' . "\r\n";
				
				$total = 0;
				$cantidad = 0;
				
				foreach ($query as $row) {
					$data .= '"' . implode('","', $row) . '"' . "\r\n";
					
					$total += $row['IMPORTE'];
					$cantidad += $row['CANTIDAD'];
				}
				
				$data .= '"","","","","","","","TOTALES","' . $cantidad . '","' . $total . '"' . "\r\n";
			}
			
			$data .= "\r\n" . '"AUMENTAR LO ENTREGADO A PANIFICADORA LA ROSA Y EL RISCAL"';
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="reporte_facturas.csv"');
			
			echo $data;
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ReporteFacturasMateriaPrima.tpl');
$tpl->prepare();

$sql = '
	SELECT
		cp.nombre
			AS nombre_pro,
		emp.num_cia,
		cc.nombre_corto
			AS nombre_cia,
		fecha,
		num_fact,
		emp.codmp || \' \' ||cmp.nombre
			AS producto,
		(cantidad * contenido) / (
			CASE
				WHEN codmp = 1 THEN
					44
				ELSE
					1
			END
		)
			AS cantidad,
		importe
	FROM
		entrada_mp emp
		LEFT JOIN catalogo_mat_primas cmp
			USING (codmp)
		LEFT JOIN catalogo_proveedores cp
			USING (num_proveedor)
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
	WHERE
		emp.num_proveedor = ' . $_REQUEST['num_pro'] . '
		AND emp.codmp = ' . $_REQUEST['codmp'] . '
		AND fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) . '\'
	ORDER BY
		emp.num_cia,
		fecha,
		num_fact
';

$query = $db->query($sql);

$tpl->newBlock('reporte');

$tpl->assign('num_pro', $_REQUEST['num_pro']);
$tpl->assign('nombre_pro', utf8_encode($query[0]['nombre_pro']));
$tpl->assign('producto', utf8_encode($query[0]['producto']));
$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
$tpl->assign('anio', $_REQUEST['anio']);
$tpl->assign('_mes', $_REQUEST['mes']);
$tpl->assign('codmp', $_REQUEST['codmp']);

if ($query) {
	$num_cia = NULL;
	
	$total = 0;
	$cantidad = 0;
	
	foreach ($query as $row) {
		if ($num_cia != $row['num_cia']) {
			$num_cia = $row['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
			
			$total_cia = 0;
			$cantidad_cia = 0;
		}
		
		$tpl->newBlock('row');
		$tpl->assign('fecha', $row['fecha']);
		$tpl->assign('num_fact', $row['num_fact']);
		$tpl->assign('cantidad', $row['cantidad'] != 0 ? number_format($row['cantidad'], 2) : '&nbsp;');
		$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');
		
		$total_cia += $row['importe'];
		$cantidad_cia += $row['cantidad'];
		
		$total += $row['importe'];
		$cantidad += $row['cantidad'];
		
		$tpl->assign('cia.total', number_format($total_cia, 2));
		$tpl->assign('cia.cantidad', number_format($cantidad_cia, 2));
		
		$tpl->assign('reporte.total', number_format($total, 2));
		$tpl->assign('reporte.cantidad', number_format($cantidad, 2));
	}
}

$tpl->printToScreen();
?>
