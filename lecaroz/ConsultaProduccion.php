<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
		
		case 'reporte':
			$condiciones = array();
			
			/*
			@ Fecha
			*/
			$condiciones[] = 'fecha_total = \'' . $_REQUEST['fecha'] . '\'';
			
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
					$condiciones[] = 'numcia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 42))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					numcia
						AS
							num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					codturno
						AS
							cod_turno,
					descripcion
						AS
							turno,
					fecha_total
						AS
							fecha,
					raya_ganada,
					raya_pagada,
					total_produccion
						AS
							produccion
				FROM
						total_produccion tp
					LEFT JOIN
						catalogo_turnos ct
							ON
								(
									ct.cod_turno = tp.codturno
								)
					LEFT JOIN
						catalogo_companias cc
							ON
								(
									cc.num_cia = tp.numcia
								)
					LEFT JOIN
						catalogo_administradores ca
							USING
								(
									idadministrador
								)
					LEFT JOIN
						catalogo_operadoras co
							USING
								(
									idoperadora
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					cod_turno
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ReporteProduccion.tpl');
			$tpl->prepare();
			
			if ($result) {
				$condiciones = array();
				
				/*
				@ Fecha
				*/
				$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha'] . '\'';
				
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
						$condiciones[] = 'p.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				/*
				@ Administrador
				*/
				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
					$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
				}
				
				/*
				@ Usuario
				*/
				if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 42))) {
					$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
				}
				
				$sql = '
					SELECT
						p.num_cia,
						p.fecha,
						p.cod_turnos
							AS
								cod_turno,
						p.cod_producto,
						cp.nombre
							AS
								producto,
						p.piezas,
						p.precio_raya,
						p.porc_raya,
						p.precio_venta,
						p.imp_raya,
						p.imp_produccion
					FROM
							produccion p
						LEFT JOIN
							catalogo_productos cp
								USING
									(
										cod_producto
									)
						LEFT JOIN
							control_produccion ctrl
								ON
									(
											ctrl.num_cia = p.num_cia
										AND
											ctrl.cod_turno = p.cod_turnos
										AND
											ctrl.cod_producto = p.cod_producto
										AND
											ctrl.precio_raya = p.precio_raya
										AND
											ctrl.porc_raya = p.porc_raya
										AND
											ctrl.precio_venta = p.precio_venta
									)
						LEFT JOIN
							catalogo_companias cc
								ON
									(
										cc.num_cia = p.num_cia
									)
						LEFT JOIN
							catalogo_administradores ca
								USING
									(
										idadministrador
									)
						LEFT JOIN
							catalogo_operadoras co
								USING
									(
										idoperadora
									)
							
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						cod_turno,
						num_orden,
						cod_producto
				';
				$tmp = $db->query($sql);
				
				$produccion = array();
				
				foreach ($tmp as $t) {
					$produccion[$t['num_cia']][$t['cod_turno']][] = array(
						'cod' => $t['cod_producto'],
						'producto' => $t['producto'],
						'piezas' => number_format($t['piezas']),
						'precio_raya' => $t['precio_raya'] > 0 && $t['porc_raya'] == 0 ? number_format($t['precio_raya'], 4, '.', ',') : ($t['precio_raya'] == 0 && $t['porc_raya'] > 0 ? number_format($t['porc_raya'], 2, '.', ',') . '%' : '&nbsp;'),
						'precio_venta' => $t['precio_venta'] > 0 ? number_format($t['precio_venta'], 2, '.', ',') : '&nbsp;',
						'raya' => $t['imp_raya'] > 0 ? number_format($t['imp_raya'], 2, '.', ',') : '&nbsp;',
						'produccion' => $t['imp_produccion'] > 0 ? number_format($t['imp_produccion']) : '&nbsp;'
					);
				}
				
				list($dia, $mes, $anio) = explode('/', $_REQUEST['fecha']);
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							foreach ($totales as $key => $value) {
								$tpl->assign('reporte.' . $key, number_format($value, 2, '.', ','));
							}
							
							$tpl->assign('reporte.salto', '<br class="saltopagina" />');
						}
						
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						
						$tpl->assign('dia', $dia);
						$tpl->assign('mes', $_meses[intval($mes, 10)]);
						$tpl->assign('anio', $anio);
						
						$totales = array(
							'raya_ganada' => 0,
							'raya_pagada' => 0,
							'produccion' => 0
						);
						
						$turno = NULL;
					}
					if ($turno != $rec['turno']) {
						$turno = $rec['cod_turno'];
						
						$tpl->newBlock('turno');
						$tpl->assign('turno', $rec['turno']);
						$tpl->assign('raya_ganada', $rec['raya_ganada'] > 0 ? number_format($rec['raya_ganada'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('raya_pagada', $rec['raya_pagada'] > 0 ? number_format($rec['raya_pagada'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('produccion', $rec['produccion'] > 0 ? number_format($rec['produccion'], 2, '.', ',') : '&nbsp;');
						
						$totales['raya_ganada'] += $rec['raya_ganada'];
						$totales['raya_pagada'] += $rec['raya_pagada'];
						$totales['produccion'] += $rec['produccion'];
					}
					
					if (isset($produccion[$num_cia][$turno])) {
						foreach ($produccion[$num_cia][$turno] as $producto) {
							$tpl->newBlock('producto');
							
							foreach ($producto as $key => $value) {
								$tpl->assign($key, $value);
							}
						}
					}
				}
			}
			
			if ($num_cia != NULL) {
				foreach ($totales as $key => $value) {
					$tpl->assign('reporte.' . $key, number_format($value, 2, '.', ','));
				}
			}
			
			$tpl->printToScreen();
			
		break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ConsultaProduccion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37))) {
		$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
	}
	
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
			LEFT JOIN
				catalogo_operadoras co
					USING
						(
							idoperadora
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);
	
	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}
else {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
	
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
}

$tpl->printToScreen();
?>
