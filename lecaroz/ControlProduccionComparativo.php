<?php
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$condiciones = array();
			
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
					$condiciones[] = 'c.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turno IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}
			
			$sql = '
				SELECT
					c.num_cia,
					c.cod_turno
						AS turno,
					c.cod_producto
						AS producto,
					c.precio_raya,
					c.porc_raya,
					c.precio_venta
				FROM
					control_produccion c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
					LEFT JOIN catalogo_turnos ct
						USING (cod_turno)
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					turno,
					producto
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ControlProduccionComparativoReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$controles = array();
				
				foreach ($result as $rec) {
					$controles[$rec['num_cia']][$rec['turno']][$rec['producto']] = array(
						'precio_raya'  => floatval($rec['precio_raya']),
						'porc_raya'    => floatval($rec['porc_raya']),
						'precio_venta' => floatval($rec['precio_venta'])
					);
				}
				
				$sql = '
					SELECT
						cc.num_cia,
						cc.nombre_corto
							AS nombre_cia
					FROM
						control_produccion c
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_turnos ct
							USING (cod_turno)
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						cc.num_cia,
						nombre_cia
					ORDER BY
						num_cia
				';
				
				$result = $db->query($sql);
				
				/*
				@ Reordenar compañías por bloques de 7
				*/
				$bloques = array();
				$bloque = 0;
				foreach ($result as $i => $rec) {
					if (($i + 1) % 7 == 1) {
						$bloque++;
					}
					
					$bloques[$bloque][] = array(
						'num_cia'    => $rec['num_cia'],
						'nombre_cia' => $rec['nombre_cia']
					);
				}
				
				$sql = '
					SELECT
						c.cod_turno
							AS turno,
						ct.descripcion
							AS nombre_turno,
						c.cod_producto
							AS producto,
						cp.nombre
							AS nombre_producto
					FROM
						control_produccion c
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_turnos ct
							USING (cod_turno)
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						turno,
						nombre_turno,
						producto,
						nombre_producto
					ORDER BY
						turno,
						producto
				';
				
				$result = $db->query($sql);
				
				$turnos = array();
				
				$turno = NULL;
				
				foreach ($result as $rec) {
					if ($turno != $rec['turno']) {
						$turno = $rec['turno'];
						
						$turnos[$turno] = array(
							'nombre_turno' => $rec['nombre_turno'],
							'productos'    => array()
						);
					}
					
					$turnos[$turno]['productos'][$rec['producto']] = $rec['nombre_producto'];
				}
				
				$max_filas = 62;
				$nuevo_bloque = NULL;
				
				foreach ($bloques as $bloque => $cias) {
					$filas = $max_filas;
					
					$nuevo_bloque = $bloque;
					
					/*
					@ Recorrer turnos
					*/
					foreach ($turnos as $turno => $datos_turno) {
						/*
						@ Validar que el turno cuente con los controles para el bloque y compañías en curso
						*/
						
						$productos = $datos_turno['productos'];
						
						foreach ($datos_turno['productos'] as $producto => $nombre_producto) {
							$cont = 0;
							
							foreach ($cias as $i => $cia) {
								if (isset($controles[$cia['num_cia']][$turno][$producto])) {
									$cont++;
								}
							}
							
							if ($cont == 0) {
								unset($productos[$producto]);
							}
						}
						
						if (count($productos) == 0) {
							continue;
						}
						
						if ($filas == $max_filas) {
							$tpl->newBlock('reporte');
							
							foreach ($cias as $i => $cia) {
								$tpl->assign('num_cia' . $i, $cia['num_cia']);
								$tpl->assign('nombre_cia' . $i, utf8_encode($cia['nombre_cia']));
							}
							
							$filas = 0;
						}
						
						$tpl->newBlock('turno');
						
						$tpl->assign('nombre_turno', utf8_encode($datos_turno['nombre_turno']));
						
						$filas++;
						
						foreach ($productos as $producto => $nombre_producto) {
							if ($filas == $max_filas) {
								$tpl->newBlock('reporte');
								
								foreach ($cias as $i => $cia) {
									$tpl->assign('num_cia' . $i, $cia['num_cia']);
									$tpl->assign('nombre_cia' . $i, utf8_encode($cia['nombre_cia']));
								}
								
								$filas = 0;
								
								$tpl->newBlock('turno');
								
								$tpl->assign('nombre_turno', utf8_encode($datos_turno['nombre_turno'] . '<span class="font6"> (continuaci&oacute;n...)</span>'));
								
								$filas++;
							}
							
							$tpl->newBlock('row');
							
							$tpl->assign('producto', $producto);
							$tpl->assign('nombre_producto', utf8_encode($nombre_producto));
							
							$filas++;
							
							/*
							@ Buscar por compañía el control de producción
							*/
							foreach ($cias as $i => $cia) {
								if (isset($controles[$cia['num_cia']][$turno][$producto])) {
									$tpl->assign('raya' . $i, $controles[$cia['num_cia']][$turno][$producto]['porc_raya'] > 0 ? '%' . number_format($controles[$cia['num_cia']][$turno][$producto]['porc_raya'], 2) : number_format($controles[$cia['num_cia']][$turno][$producto]['precio_raya'], 4));
									$tpl->assign('venta' . $i, $controles[$cia['num_cia']][$turno][$producto]['precio_venta'] > 0 ? number_format($controles[$cia['num_cia']][$turno][$producto]['precio_venta'], 2) : '&nbsp;');
								}
								else {
									$tpl->assign('raya' . $i, '&nbsp;');
									$tpl->assign('venta' . $i, '&nbsp;');
								}
							}
						}
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ControlProduccionComparativo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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

if ($admins) {
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
