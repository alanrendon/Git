<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
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

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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
		case 'consultar':
			
			/**********************************************************************************************************/
			
			$condiciones = array();
			
			$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
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
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/**********************************************************************************************************/
			
			$condiciones_cheques = $condiciones;
			
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();
				
				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}
				
				if (count($gastos) > 0) {
					$condiciones_cheques[] = 'codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}
			
			$condiciones_cheques[] = '(fecha_cancelacion IS NULL OR fecha_cancelacion > \'' . $_REQUEST['fecha2'] . '\')';
			
			$condiciones_cheques[] = '(num_cia, cuenta, folio) NOT IN (
				SELECT
					num_cia,
					folio,
					cuenta
				FROM
					pagos_otras_cias
					LEFT JOIN cheques
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
			)';
			
			/**********************************************************************************************************/
			
			$condiciones_otros[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
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
					$condiciones_otros[] = 'num_cia_aplica IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones_otros[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();
				
				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}
				
				if (count($gastos) > 0) {
					$condiciones_otros[] = 'codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}
			
			/**********************************************************************************************************/
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					codgastos
						AS cod,
					descripcion
						AS desc,
					a_nombre,
					concepto,
					(
						SELECT
							descripcion
						FROM
							facturas_pagadas
						WHERE
							num_cia = c.num_cia
							AND folio_cheque = c.folio
							AND cuenta = c.cuenta
						LIMIT 1
					)
						AS concepto_fac,
					facturas,
					fecha,
					folio,
					importe
				FROM
					cheques c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					' . implode(' AND ', $condiciones_cheques) . '
					
				UNION
				
				SELECT
					num_cia_aplica
						AS num_cia,
					nombre_corto
						AS nombre_cia,
					codgastos
						AS cod,
					descripcion
						AS desc,
					a_nombre,
					concepto,
					(
						SELECT
							descripcion
						FROM
							facturas_pagadas
						WHERE
							num_cia = poc.num_cia
							AND folio_cheque = poc.folio
							AND cuenta = poc.cuenta
						LIMIT 1
					)
						AS concepto_fac,
					facturas,
					fecha,
					folio,
					importe
				FROM
					pagos_otras_cias poc
					LEFT JOIN cheques
						USING (num_cia, folio, cuenta)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = poc.num_cia_aplica)
				WHERE
					' . implode(' AND ', $condiciones_otros) . '
				
				ORDER BY
					num_cia,
					cod,
					fecha
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/ListadoGastosBalanceReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;
				
				$page_size = 260.00;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							if ($cod != NULL && $cont > 1) {
								$size += 4.1;
								if ($size > $page_size) {
									$tpl->newBlock('hoja');
									$tpl->assign('num_cia', $num_cia);
									$tpl->assign('nombre_cia', $rec['nombre_cia']);
									$tpl->assign('fecha1', $_REQUEST['fecha1']);
									$tpl->assign('fecha2', $_REQUEST['fecha2']);
									$tpl->assign('leyenda', '<span style="font-size:8pt;">(continuaci&oacute;n)</span>');
									
									$size = 0;
									$hojas++;
								}
								
								$tpl->newBlock('cheque_subtotal');
								$tpl->assign('subtotal',  '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
							}
							
							$tpl->newBlock('total_cheques');
							$tpl->assign('total', '<span style="color:#' . ($total > 0 ? '00C' : 'C00') . '">' . number_format($total, 2, '.', ',') . '</span>');
						}
						
						
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('hoja');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);
						
						$hojas = 0;
						$size = 10;
						
						$total = 0;
						$cod = NULL;
					}
					
					if ($cod != $rec['cod']) {
						if ($cod != NULL && $cont > 1) {
							$size += 4.1;
							if ($size > $page_size) {
								$tpl->newBlock('hoja');
								$tpl->assign('num_cia', $num_cia);
								$tpl->assign('nombre_cia', $rec['nombre_cia']);
								$tpl->assign('fecha1', $_REQUEST['fecha1']);
								$tpl->assign('fecha2', $_REQUEST['fecha2']);
								$tpl->assign('leyenda', '<span style="font-size:8pt;">(continuaci&oacute;n)</span>');
								
								$size = 0;
								$hojas++;
							}
							
							$tpl->newBlock('cheque_subtotal');
							$tpl->assign('subtotal',  '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
						}
						
						$cod = $rec['cod'];
						
						$size += 8.4;
						if ($size > $page_size) {
							$tpl->newBlock('hoja');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', $rec['nombre_cia']);
							$tpl->assign('fecha1', $_REQUEST['fecha1']);
							$tpl->assign('fecha2', $_REQUEST['fecha2']);
							$tpl->assign('leyenda', '<span style="font-size:8pt;">(continuaci&oacute;n)</span>');
							
							$size = 0;
							$hojas++;
						}
						
						$tpl->newBlock('gasto_cheque');
						
						$subtotal = 0;
						$cont = 0;
					}
					
					$size += 10;
					if ($size > $page_size) {
						$tpl->newBlock('hoja');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('fecha1', $_REQUEST['fecha1']);
						$tpl->assign('fecha2', $_REQUEST['fecha2']);
						$tpl->assign('leyenda', '<span style="font-size:8pt;">(continuaci&oacute;n)</span>');
						
						$size = 0;
						$hojas++;
					}
					
					$tpl->newBlock('row_cheque');
					$tpl->assign('cod', $rec['cod']);
					$tpl->assign('desc', $rec['desc']);
					$tpl->assign('a_nombre', $rec['a_nombre']);
					$tpl->assign('facturas', trim($rec['facturas']));
					$tpl->assign('concepto', (trim($rec['facturas']) != '' ? '<br>' : '') . (trim($rec['concepto_fac']) != '' ? strtoupper(trim($rec['concepto_fac'])) : strtoupper(trim($rec['concepto']))));
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('folio', $rec['folio']);
					$tpl->assign('importe', '<span style="color:#' . ($rec['importe'] > 0 ? '00C' : 'C00') . '">' . number_format($rec['importe'], 2, '.', ',') . '</span>');
					
					$subtotal += $rec['importe'];
					$total += $rec['importe'];
					
					$cont++;
				}
				
				if ($num_cia != NULL) {
					if ($cod != NULL && $cont > 1) {
						$size += 4.1;
						if ($size > $page_size) {
							$tpl->newBlock('hoja');
							$tpl->assign('num_cia', $num_cia);
							$tpl->assign('nombre_cia', $rec['nombre_cia']);
							$tpl->assign('fecha1', $_REQUEST['fecha1']);
							$tpl->assign('fecha2', $_REQUEST['fecha2']);
							$tpl->assign('leyenda', '<span style="font-size:8pt;">(continuaci&oacute;n)</span>');
							
							$size = 0;
							$hojas++;
						}
						
						$tpl->newBlock('cheque_subtotal');
						$tpl->assign('subtotal',  '<span style="color:#' . ($subtotal > 0 ? '00C' : 'C00') . '">' . number_format($subtotal, 2, '.', ',') . '</span>');
					}
					
					$tpl->newBlock('total_cheques');
					$tpl->assign('total', '<span style="color:#' . ($total > 0 ? '00C' : 'C00') . '">' . number_format($total, 2, '.', ',') . '</span>');
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ListadoGastosBalance.tpl');
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

$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	
	$tpl->assign('value', $a['value']);
	$tpl->assign('text', $a['text']);
}

$tpl->printToScreen();
?>
