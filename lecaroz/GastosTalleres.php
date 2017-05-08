<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
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
	10 => 'OCTUBRE',
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'reporte':
			$condiciones = array();
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
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
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones1 = $condiciones;
			
			//$condiciones1[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			$condiciones1[] = 'f.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			//$condiciones1[] = 'c.num_proveedor IN (74, 78, 113, 133, 207, 208, 210, 211, 220, 222, 226, 233, 234, 258, 275, 291, 293, 302, 327, 348, 388, 427, 453, 458, 588, 660, 749, 790, 910, 927, 932, 954, 1185, 1199, 1352, 1374, 1376, 1377, 1409)';
			//$condiciones1[] = 'c.codgastos IN (45, 56, 124, 66, 57, 34, 48, 57, 147, 46)';
			$condiciones1[] = 'f.codgastos IN (45, 56, 124, 66, 57, 34, 48, 57, 147, 46)';
			
			//$condiciones1[] = 'ec.importe > 0';
			
			$condiciones2 = $condiciones;
			
			$condiciones2[] = 'gc.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			$condiciones2[] = '(gc.cod_gastos IN (84, 2) OR (gc.cod_gastos = 4 AND gc.num_cia = 800))';
			
			/*$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					ec.fecha,
					folio,
					a_nombre
						AS beneficiario,
					cg.descripcion
						AS concepto,
					c.facturas,
					ec.importe
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					' . implode(' AND ', $condiciones1) . '
				
				UNION
				
				SELECT
					num_cia,
					nombre_corto,
					fecha,
					NULL,
					\'GASTOS DE CAJA\',
					descripcion,
					NULL,
					importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos_caja cgc
						ON (cgc.id = gc.cod_gastos)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				
				ORDER BY
					num_cia,
					fecha
			';*/
			
			$sql = '
				SELECT
					f.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					f.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					f.num_fact
						AS factura,
					f.fecha,
					f.concepto,
					f.codgastos
						AS gasto,
					cg.descripcion
						AS descripcion,
					f.total
						AS importe
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					' . implode(' AND ', $condiciones1) . '
				
				UNION
				
				SELECT
					gc.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					NULL,
					NULL,
					NULL,
					gc.fecha,
					\'GASTOS DE CAJA\',
					gc.cod_gastos
						AS gasto,
					cgc.descripcion
						AS descripcion,
					importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos_caja cgc
						ON (cgc.id = gc.cod_gastos)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				
				ORDER BY
					num_cia,
					num_pro,
					factura
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/GastosTalleresReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('reporte');
				
				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2']);
				
				$gran_total = 0;
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$total = 0;
					}
					$tpl->newBlock('row');
					
					/*$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('folio', $rec['folio'] > 0 ? $rec['folio'] : '&nbsp;');
					$tpl->assign('beneficiario', $rec['beneficiario'] != '' ? utf8_encode($rec['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', utf8_encode($rec['concepto']));
					$tpl->assign('facturas', $rec['facturas'] != '' ? utf8_encode($rec['facturas']) : '&nbsp;');
					$tpl->assign('importe', number_format($rec['importe'], 2));*/
					
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
					$tpl->assign('factura', utf8_encode($rec['factura']));
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('concepto', utf8_encode($rec['concepto']));
					$tpl->assign('gasto', $rec['gasto']);
					$tpl->assign('descripcion', utf8_encode($rec['descripcion']));
					$tpl->assign('importe', number_format($rec['importe'], 2));
					
					$total += $rec['importe'];
					$gran_total += $rec['importe'];
					
					$tpl->assign('cia.total', number_format($total, 2));
					$tpl->assign('reporte.total', number_format($gran_total, 2));
				}
			}
			
			$tpl->printToScreen();
			
			break;
		
		case 'exportar':
			$condiciones = array();
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
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
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$condiciones1 = $condiciones;
			
			//$condiciones1[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			$condiciones1[] = 'f.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			//$condiciones1[] = 'c.num_proveedor IN (74, 78, 113, 133, 207, 208, 210, 211, 220, 222, 226, 233, 234, 258, 275, 291, 293, 302, 327, 348, 388, 427, 453, 458, 588, 660, 749, 790, 910, 927, 932, 954, 1185, 1199, 1352, 1374, 1376, 1377, 1409)';
			//$condiciones1[] = 'c.codgastos IN (45, 56, 124, 66, 57, 34, 48, 57, 147, 46)';
			$condiciones1[] = 'f.codgastos IN (45, 56, 124, 66, 57, 34, 48, 57, 147, 46)';
			
			//$condiciones1[] = 'ec.importe > 0';
			
			$condiciones2 = $condiciones;
			
			$condiciones2[] = 'gc.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			$condiciones2[] = '(gc.cod_gastos IN (84, 2) OR (gc.cod_gastos = 4 AND gc.num_cia = 800))';
			
			/*$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					ec.fecha,
					folio,
					c.num_proveedor
						AS num_pro,
					a_nombre
						AS nombre_pro,
					cg.descripcion
						AS concepto,
					c.facturas,
					ec.importe
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					' . implode(' AND ', $condiciones1) . '
				
				UNION
				
				SELECT
					num_cia,
					nombre_corto,
					fecha,
					NULL,
					NULL,
					\'GASTOS DE CAJA\',
					descripcion,
					NULL,
					importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos_caja cgc
						ON (cgc.id = gc.cod_gastos)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				
				ORDER BY
					num_cia,
					fecha
			';*/
			
			$sql = '
				SELECT
					f.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					f.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					f.num_fact
						AS factura,
					f.fecha,
					f.concepto,
					f.codgastos
						AS gasto,
					cg.descripcion
						AS descripcion,
					f.total
						AS importe
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					' . implode(' AND ', $condiciones1) . '
				
				UNION
				
				SELECT
					gc.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					NULL,
					NULL,
					NULL,
					gc.fecha,
					\'GASTOS DE CAJA\',
					gc.cod_gastos,
					cgc.descripcion,
					importe
				FROM
					gastos_caja gc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos_caja cgc
						ON (cgc.id = gc.cod_gastos)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				
				ORDER BY
					num_cia,
					num_pro,
					factura
			';
			
			$result = $db->query($sql);
			
			$data = '';
			
			if ($result) {
				$data .= '"REPORTE DE GASTOS DE TALLERES"' . "\n";
				$data .= '"del ' . $_REQUEST['fecha1'] . ' al ' . $_REQUEST['fecha2'] . '"' . "\n";
				
				$gran_total = 0;
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$data .= "\n" . '"","","","","","","TOTAL","' . number_format($total, 2) . '"' . "\n";
						}
						
						$num_cia = $rec['num_cia'];
						
						$data .= "\n" . '"' . $num_cia . '","' . $rec['nombre_cia'] . '"' . "\n";
						$data .= '"#PRO","PROVEEDOR","FACTURA","FECHA","CONCEPTO","GASTO","DESCRIPCION","IMPORTE"';
						
						$total = 0;
					}
					
					$data .= "\n";
					$data .= '"' . $rec['num_pro'] . '",';
					$data .= '"' . $rec['nombre_pro'] . '",';
					$data .= '"' . $rec['factura'] . '",';
					$data .= '"' . $rec['fecha'] . '",';
					$data .= '"' . $rec['concepto'] . '",';
					$data .= '"' . $rec['gasto'] . '",';
					$data .= '"' . $rec['descripcion'] . '",';
					$data .= '"' . number_format($rec['importe'], 2) . '"';
					
					$total += $rec['importe'];
					$gran_total += $rec['importe'];
				}
				
				if ($num_cia != NULL) {
					$data .= "\n" . '"","","","","","","TOTAL","' . number_format($total, 2) . '"' . "\n";
				}
				
				$data .= "\n" . '"","","","","","","GRAN TOTAL","' . number_format($gran_total, 2) . '"' . "\n";
			}
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=gastostalleres.csv');
			
			echo $data;
			
			break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/GastosTalleres.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n') - 1, 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$result = $db->query($sql);

if ($result) {
	foreach ($result as $r) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $r['value']);
		$tpl->assign('text', utf8_encode($r['text']));
	}
}

$tpl->printToScreen();
?>
