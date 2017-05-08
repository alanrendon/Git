<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/PedidosAlCorteInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') + 15, date('Y'))));
			
			$tpl->assign('fecha_hoja', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));
			
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
				foreach ($admins as $admin) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $admin['value']);
					$tpl->assign('text', $admin['text']);
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerMP':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre'];
			}
		break;
		
		case 'calculoInicial':
			/*
			* [30-Dic-2011] Buscar las compañías que no cumplen con la fecha de corte de la hoja
			*/
			
			$condiciones = array();
			
			$condiciones[] = 'num_cia <= 300';
			
			$condiciones[] = '(efe::INT & exp::INT & gas::INT & pro::INT & pas::INT)::BOOLEAN = TRUE';
			
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
			
			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();
				
				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}
				
				if (count($omitir_cias) > 0) {
					$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					*
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							co.nombre
								AS operadora,
							MAX(fecha)
								AS fecha
						FROM
							total_panaderias tp
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_operadoras co
								USING (idoperadora)
						WHERE
							' . implode(' AND ', $condiciones) . '
						GROUP BY
							num_cia,
							nombre_cia,
							operadora
					) result
				WHERE
					fecha > \'' . $_REQUEST['fecha_hoja'] . '\'::DATE - INTERVAL \'2 WEEKS\'
					AND fecha < \'' . $_REQUEST['fecha_hoja'] . '\'
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ped/PedidosAlCorteCiasAtrasadas.tpl');
				$tpl->prepare();
				
				$row_color = FALSE;
				
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('operadora', utf8_encode($rec['operadora']));
					$tpl->assign('fecha', $rec['fecha']);
					
					$row_color = !$row_color;
				}
				
				echo $tpl->getOutputContent();
			}
			else {
				list($hoja_dia, $hoja_mes, $hoja_anio) = array_map('toInt', explode('/', $_REQUEST['fecha_hoja']));
				
				
				$condiciones = array();
				
				$condiciones[] = 'num_cia <= 300';
				
				$condiciones[] = 'codmp = ' . $_REQUEST['codmp'];
				
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
				
				if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
					$omitir_cias = array();
					
					$pieces = explode(',', $_REQUEST['omitir_cias']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$omitir_cias[] = $piece;
						}
					}
					
					if (count($omitir_cias) > 0) {
						$condiciones[] = 'num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
					}
				}
				
				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
					$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
				}
				
				$sql = '
					SELECT
						*,
						CASE
							WHEN consumo > 0 THEN
								consumo / ' . $hoja_dia . '/*EXTRACT(day FROM fecha)*/
							ELSE
								NULL
						END
							promedio
					FROM
						(
							SELECT
								codmp,
								cmp.nombre
									AS nombre_mp,
								num_cia,
								cc.nombre_corto
									AS nombre_cia,
								tuc.descripcion
									AS unidad,
								existencia,
								COALESCE((
									SELECT
										SUM(cantidad)
									FROM
										mov_inv_real movs
									WHERE
										num_cia = inv.num_cia
										AND codmp = inv.codmp
										AND tipo_mov = TRUE
										AND fecha BETWEEN (
											SELECT
												MAX(fecha) + INTERVAL \'1 day\'
											FROM
												historico_inventario
											WHERE
												num_cia = inv.num_cia
												AND codmp = inv.codmp
										) AND now()::date
								), 0)
									AS consumo,
								COALESCE((
									SELECT
										MAX(fecha)
									FROM
										mov_inv_real movs
									WHERE
										num_cia = inv.num_cia
										AND codmp = inv.codmp
										AND tipo_mov = TRUE
										AND fecha BETWEEN (
											SELECT
												MAX(fecha) + INTERVAL \'1 day\'
											FROM
												historico_inventario
											WHERE
												num_cia = inv.num_cia
												AND codmp = inv.codmp
										) AND now()::date
								), NULL)
									AS fecha,
								COALESCE((
									SELECT
										EXTRACT(day FROM MAX(fecha))
									FROM
										mov_inv_real movs
									WHERE
										num_cia = inv.num_cia
										AND codmp = inv.codmp
										AND tipo_mov = TRUE
										AND fecha BETWEEN (
											SELECT
												MAX(fecha) + INTERVAL \'1 day\'
											FROM
												historico_inventario
											WHERE
												num_cia = inv.num_cia
												AND codmp = inv.codmp
										) AND now()::date
								), NULL)
									AS dia
							FROM
								inventario_virtual inv
								LEFT JOIN catalogo_companias cc
									USING (num_cia)
								LEFT JOIN catalogo_mat_primas cmp
									USING (codmp)
								LEFT JOIN tipo_unidad_consumo tuc
									ON (idunidad = unidadconsumo)
							WHERE
								' . implode(' AND ', $condiciones) . '
						) result
					ORDER BY
						num_cia
				';
				
				$result = $db->query($sql);
				
				if ($result) {
					$tpl = new TemplatePower('plantillas/ped/PedidosAlCorteCalculoInicial.tpl');
					$tpl->prepare();
					
					$tpl->assign('codmp', $result[0]['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($result[0]['nombre_mp']));
					$tpl->assign('fecha', $_REQUEST['fecha']);
					
					list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $_REQUEST['fecha']));
					
					$row_color = FALSE;
					
					$total = 0;
					
					foreach ($result as $rec) {
						$tpl->newBlock('row');
						
						$tpl->assign('row_color', $row_color ? 'on' : 'off');
						
						$row_color = !$row_color;
						
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('unidad', utf8_encode($rec['unidad']) . (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES'));
						$tpl->assign('existencia', $rec['existencia'] != 0 ? number_format($rec['existencia'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('consumo', $rec['consumo'] != 0 ? number_format($rec['consumo'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('fecha', $rec['fecha'] != '' ? $rec['fecha'] : '&nbsp;');
						$tpl->assign('promedio', $rec['promedio'] != 0 ? number_format($rec['promedio'], 2, '.', ',') : '&nbsp;');
						$tpl->assign('fecha', $rec['fecha'] != '' ? $rec['fecha'] : '&nbsp;');
						
						if ($rec['fecha'] != '') {
							list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $rec['fecha']));
							
							$ts1 = mktime(0, 0, 0, $mes1, $dia1, $anio1);
							$ts2 = mktime(0, 0, 0, $mes2, $dia2, $anio2);
							
							$dias = round(($ts1 - $ts2) / 86400 - floor($rec['existencia'] / $rec['promedio']));
							
							if ($dias > 7) {
								$pedido = $rec['promedio'] * $dias;
								
								$total += $pedido;
							}
							else {
								$pedido = 0;
							}
						}
						else {
							$dias = 0;
							$pedido = 0;
						}
						
						$tpl->assign('dias', $dias > 7 ? $dias : '&nbsp;');
						$tpl->assign('pedido', $pedido > 0 ? number_format($pedido, 2, '.', ',') : '&nbsp;');
						
						$datos = array(
							'num_cia'       => intval($rec['num_cia']),
							'nombre_cia'    => utf8_encode($rec['nombre_cia']),
							'codmp'         => intval($rec['codmp']),
							'nombre_mp'     => utf8_encode($rec['nombre_mp']),
							'unidad'        => utf8_encode($rec['unidad']),
							'existencia'    => floatval($rec['existencia']),
							'consumo'       => floatval($rec['consumo']),
							'promedio'      => floatval($rec['promedio']),
							'fecha_consumo' => $rec['fecha'],
							'corte'         => $_REQUEST['fecha'],
							'dias'          => $dias,
							'pedido'        => floatval($pedido),
							'urgente'       => $rec['consumo'] > 0 ? (round($rec['existencia'] / $rec['promedio']) <= 5 ? 'TRUE' : 'FALSE') : 'FALSE'
						);
						
						$tpl->assign('datos_pedido', htmlentities(json_encode($datos)));
					}
					
					$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
					
					echo $tpl->getOutputContent();
				}
			}
		break;
		
		case 'distribuirPedidos':
			$pedidos = array();
			$productos = array();
			
			foreach ($_REQUEST['pedido'] as $pedido) {
				$data = json_decode($pedido, TRUE);
				
				$pedidos[] = $data;
				
				$productos[] = $data['num_cia'] . ', ' . $data['codmp'];
			}
			
			$sql = '
				SELECT
					num_cia,
					cpp.codmp,
					cpp.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					telefono1,
					telefono2,
					email1,
					email2,
					email3,
					ppp.porcentaje,
					contenido,
					tuc.descripcion
						AS unidad,
					tp.descripcion
						AS presentacion,
					precio
				FROM
					porcentajes_pedidos_proveedores ppp
					LEFT JOIN catalogo_productos_proveedor cpp
						ON (cpp.id = ppp.presentacion)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = cpp.num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						ON (cmp.codmp = cpp.codmp)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (idunidad = cmp.unidadconsumo)
					LEFT JOIN tipo_presentacion tp
						ON (idpresentacion = cpp.presentacion)
				WHERE
					(num_cia, ppp.codmp) IN (VALUES (' . implode('), (', $productos) . '))
				ORDER BY
					num_cia,
					cpp.codmp,
					porcentaje DESC
			';
			
			$result = $db->query($sql);
			
			$porcentajes = array();
			
			if ($result) {
				foreach ($result as $rec) {
					$porcentajes[$rec['num_cia']][$rec['codmp']][] = array(
						'num_pro'      => intval($rec['num_pro']),
						'nombre_pro'   => utf8_encode($rec['nombre_pro']),
						'telefono1'    => $rec['telefono1'],
						'telefono2'    => $rec['telefono2'],
						'email1'       => $rec['email1'],
						'email2'       => $rec['email2'],
						'email3'       => $rec['email3'],
						'porcentaje'   => floatval($rec['porcentaje']),
						'contenido'    => floatval($rec['contenido']),
						'unidad'       => $rec['unidad'],
						'presentacion' => $rec['presentacion'],
						'precio'       => floatval($rec['precio'])
					);
				}
			}
			
			$pedidos_pro = array();
			foreach ($pedidos as $pedido) {
				if (isset($porcentajes[$pedido['num_cia']][$pedido['codmp']])) {
					foreach ($porcentajes[$pedido['num_cia']][$pedido['codmp']] as $porcentaje) {
						$parte_pedido = round($pedido['pedido'] * $porcentaje['porcentaje'] / 100);
						
						$entregar = ceil($parte_pedido / $porcentaje['contenido']);
						
						if (in_array($pedido['codmp'], array(3, 4))) {
							if ($entregar % 5 != 0) {
								$entregar += 5 - $entregar % 5;
							}
						}
						
						$pedidos_pro[] = array(
							'num_cia' => intval($pedido['num_cia']),
							'nombre_cia' => $pedido['nombre_cia'],
							'codmp' => intval($pedido['codmp']),
							'nombre_mp' => $pedido['nombre_mp'],
							'pedido' => floatval($parte_pedido),
							'unidad' => $pedido['unidad'],
							'dias' => intval($pedido['dias']),
							'entregar' => floatval($entregar),
							'presentacion' => $porcentaje['presentacion'],
							'contenido' => $porcentaje['contenido'],
							'precio' => floatval($porcentaje['precio']),
							'porcentaje' => floatval($porcentaje['porcentaje']),
							'num_pro' => intval($porcentaje['num_pro']),
							'nombre_pro' => $porcentaje['nombre_pro'],
							'telefono1' => $porcentaje['telefono1'],
							'telefono2' => $porcentaje['telefono2'],
							'email1' => $porcentaje['email1'],
							'email2' => $porcentaje['email2'],
							'email3' => $porcentaje['email3'],
							'urgente' => $pedido['urgente']
						);
					}
				}
				else {
					$pedidos_pro[] = array(
						'num_cia' => intval($pedido['num_cia']),
						'nombre_cia' => $pedido['nombre_cia'],
						'codmp' => intval($pedido['codmp']),
						'nombre_mp' => $pedido['nombre_mp'],
						'pedido' => floatval($pedido['pedido']),
						'unidad' => $pedido['unidad'],
						'dias' => NULL,
						'entregar' => NULL,
						'presentacion' => NULL,
						'contenido' => NULL,
						'precio' => NULL,
						'porcentaje' => NULL,
						'num_pro' => NULL,
						'nombre_pro' => NULL,
						'telefono1' => NULL,
						'telefono2' => NULL,
						'email1' => NULL,
						'email2' => NULL,
						'email3' => NULL,
						'urgente' => 'FALSE'
					);
				}
			}
			
			function cmp($a, $b) {
				if ($a['num_cia'] == $b['num_cia']) {
					if ($a['entregar'] > 0 && $b['entregar'] > 0) {
						if ($a['codmp'] == $b['codmp']) {
							return 0;
						}
						else {
							return ($a['codmp'] < $b['codmp']) ? -1 : 1;
						}
					}
					else {
						if ($a['entregar'] <= 0 && $b['entregar'] <= 0) {
							if ($a['codmp'] == $b['codmp']) {
								return 0;
							}
							else {
								return ($a['codmp'] < $b['codmp']) ? -1 : 1;
							}
						}
						if ($a['entregar'] > 0 && $b['entregar'] <= 0) {
							return -1;
						}
						else if ($b['entregar'] > 0 && $a['entregar'] <= 0) {
							return 1;
						}
					}
				}
				else {
					return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
				}
				
			}
			
			usort($pedidos_pro, 'cmp');
			
			$tpl = new TemplatePower('plantillas/ped/PedidosAlCorteDistribucion.tpl');
			$tpl->prepare();
			
			$tpl->assign('codmp', $pedidos[0]['codmp']);
			$tpl->assign('nombre_mp', $pedidos[0]['nombre_mp']);
			$tpl->assign('fecha', $pedidos[0]['corte']);
			
			$row_color = FALSE;
			
			foreach ($pedidos_pro as $p) {
				$tpl->newBlock('row');
				
				$tpl->assign('row_color', $row_color ? 'on' : 'off');
				
				$row_color = !$row_color;
				
				$tpl->assign('datos_pedido', htmlentities(json_encode($p)));
				$tpl->assign('num_cia', $p['num_cia']);
				$tpl->assign('nombre_cia', $p['nombre_cia']);
				$tpl->assign('pedido', number_format($p['pedido'], 2, '.', ','));
				$tpl->assign('unidad', $p['unidad'] . ($p['pedido'] > 1 ? (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
				$tpl->assign('entregar', $p['entregar'] > 0 ? number_format($p['entregar'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('presentacion', $p['entregar'] > 0 ? $p['presentacion'] . ($p['entregar'] > 1 ? (in_array($p['presentacion'][strlen($p['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ' DE ' . $p['contenido'] . ' ' . $p['unidad'] . ($p['contenido'] > 1 ? (in_array($p['unidad'][strlen($p['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '')  : '&nbsp;');
				$tpl->assign('precio', $p['entregar'] > 0 ? '<span style="float:left;">$&nbsp;</span>' . number_format($p['precio'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('costo', $p['entregar'] > 0 ? '<span style="float:left;">$&nbsp;</span>' . number_format($p['entregar'] * $p['precio'],2 , '.', ',') : '&nbsp;');
				$tpl->assign('num_pro', $p['entregar'] > 0 ? $p['num_pro'] : '&nbsp;');
				$tpl->assign('nombre_pro', $p['entregar'] > 0 ? $p['nombre_pro'] : '&nbsp;');
				
				$tpl->assign('disabled', !$p['entregar'] ? ' disabled' : '');
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'registrarPedidos':
			$sql = '
				SELECT
					COALESCE(MAX(folio), 0) + 1
						AS folio
				FROM
					pedidos_new
			';
			$result = $db->query($sql);
			
			$folio = $result[0]['folio'];
			
			$sql = '';
			
			foreach ($_REQUEST['pedido'] as $json) {
				$pedido = json_decode($json, TRUE);
				
				$sql .= '
					INSERT INTO
						pedidos_new
							(
								folio,
								fecha,
								dias,
								complemento,
								num_cia,
								codmp,
								pedido,
								unidad,
								entregar,
								presentacion,
								contenido,
								precio,
								num_proveedor,
								porcentaje,
								urgente,
								idins,
								tsins,
								programa
							)
						VALUES
							(
								' . $folio . ',
								now()::date,
								' . $pedido['dias'] . ',
								FALSE,
								' . $pedido['num_cia'] . ',
								' . $pedido['codmp'] . ',
								' . $pedido['pedido'] . ',
								\'' . $pedido['unidad'] . '\',
								' . $pedido['entregar'] . ',
								\'' . $pedido['presentacion'] . '\',
								' . $pedido['contenido'] . ',
								' . $pedido['precio'] . ',
								' . $pedido['num_pro'] . ',
								' . $pedido['porcentaje'] . ',
								' . $pedido['urgente'] . ',
								' . $_SESSION['iduser'] . ',
								now(),
								3
							)
				' . ";\n";
			}
			
			$db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/PedidosAlCorteFin.tpl');
			$tpl->prepare();
			
			$tpl->assign('folio', $folio);
			$tpl->assign('fecha', date('d/m/Y'));
			$tpl->assign('no_pedidos', count($_REQUEST['pedido']));
			
			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro,
					COUNT(id)
						AS no_pedidos,
					telefono1,
					telefono2,
					email1,
					email2,
					email3
				FROM
					pedidos_new p
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
				WHERE
					folio = ' . $folio . '
				GROUP BY
					num_pro,
					nombre_pro,
					telefono1,
					telefono2,
					email1,
					email2,
					email3
				ORDER BY
					num_pro
			';
			
			$pros = $db->query($sql);
			
			$row_color = FALSE;
			
			foreach ($pros as $i => $pro) {
				$tpl->newBlock('pro');
				
				$tpl->assign('row_color', $row_color ? 'on' : 'off');
				
				$tpl->assign('num_pro', $pro['num_pro']);
				$tpl->assign('nombre_pro', $pro['nombre_pro']);
				$tpl->assign('no_pedidos', number_format($pro['no_pedidos'], 0, '.', ','));
				
				$telefonos = array();
				
				if ($pro['telefono1'] != '') {
					$telefonos[] = $pro['telefono1'];
				}
				if ($pro['telefono2'] != '') {
					$telefonos[] = $pro['telefono2'];
				}
				
				$emails = array();
				
				if ($pro['email1'] != '') {
					$emails[] = $pro['email1'];
				}
				if ($pro['email2'] != '') {
					$emails[] = $pro['email2'];
				}
				if ($pro['email3'] != '') {
					$emails[] = $pro['email3'];
				}
				
				$tpl->assign('telefonos', count($telefonos) > 0 ? implode(', ', $telefonos) : 'NO HAY TELEFONOS REGISTRADOS');
				$tpl->assign('emails', count($emails) > 0 ? implode(', ', $emails) : 'NO HAY CORREOS ELECTRONICOS REGISTRADOS');
			}
			
			echo $tpl->getOutputContent();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/PedidosAlCorte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
