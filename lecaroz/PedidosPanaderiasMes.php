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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) {
//	die('PROGRAMA EN PROCESO DE MODIFICACION');
//}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasMesInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					CASE
						WHEN SUM(CASE WHEN checked = TRUE THEN 1 ELSE 0 END) = 0 THEN
							0
						WHEN SUM(CASE WHEN checked = FALSE THEN 1 ELSE 0 END) = 0 THEN
							1
						ELSE
							2
					END
						AS status
				FROM
					pedidos_panaderia pp
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					tipo = 1
					AND tsreg IS NULL
					AND tsdel IS NULL
				GROUP BY
					num_cia,
					nombre_cia,
					fecha
				ORDER BY
					num_cia,
					fecha
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $rec) {
					$tpl->newBlock('pedido');
					
					$tpl->assign('pedido', htmlentities(json_encode(array(
						'num_cia' => intval($rec['num_cia']),
						'fecha'   => $rec['fecha']
					))));
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					
					switch ($rec['status']) {
						case 0:
							$tpl->assign('color', 'C00');
						break;
						
						case 1:
							$tpl->assign('color', '060');
						break;
						
						case 2:
							$tpl->assign('color', '00C');
						break;
					}
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$data = json_decode($_REQUEST['pedido']);
			
			$condiciones = array();
			
			$condiciones[] = 'p.tsreg IS NULL';
			
			$condiciones[] = 'p.tsdel IS NULL';
			
			$condiciones[] = 'p.cantidad > 0';
			
			$condiciones[] = 'p.tipo = 1';
			
			$condiciones[] = 'p.num_cia = ' . $data->num_cia;
			
			$condiciones[] = 'p.fecha = \'' . $data->fecha . '\'';
			
			$sql = '
				SELECT
					idpedidopanaderia
						AS id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					codmp,
					COALESCE(cmp.nombre, p.producto)
						AS nombre_mp,
					cantidad,
					UPPER(unidad)
						AS unidad,
					UPPER(observaciones)
						AS observaciones,
					checked,
					tomar_consumo,
					COALESCE((
						SELECT
							existencia
						FROM
							inventario_virtual
						WHERE
							num_cia = p.num_cia
							AND codmp = p.codmp
					), 0)
						AS existencia,
					COALESCE((
						SELECT
							MAX(consumo)
						FROM
							(
								SELECT
									EXTRACT(year FROM fecha)
										AS anio,
									EXTRACT(month FROM fecha)
										AS mes,
									SUM(cantidad)
										AS consumo
								FROM
									mov_inv_real mir
								WHERE
									num_cia = p.num_cia
									AND codmp = p.codmp
									AND tipo_mov = TRUE
									AND fecha BETWEEN (
										SELECT
											MAX(fecha)
										FROM
											historico_inventario
										WHERE
											num_cia = mir.num_cia
											AND codmp = mir.codmp
									) - INTERVAL \'2 months\' + INTERVAL \'1 day\' AND (
										SELECT
											MAX(fecha)
										FROM
											historico_inventario
										WHERE
											num_cia = mir.num_cia
											AND codmp = mir.codmp
									)
								GROUP BY
									anio,
									mes
							)
								result
					), 0)
						AS consumo,
					num_pro,
					idpre,
					fijo
				FROM
					pedidos_panaderia p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha,
					codmp
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						cpp.id,
						cpp.codmp,
						cpp.num_proveedor
							AS num_pro,
						cp.nombre
							AS nombre_pro,
						tp.descripcion
							AS presentacion,
						contenido,
						tuc.descripcion
							AS unidad,
						precio,
						COALESCE(MAX(ppp.porcentaje), 0)
							AS por,
						COALESCE((
							SELECT
								fijo
							FROM
								pedidos_panaderia
							WHERE
								tsreg IS NULL
								AND tsdel IS NULL
								AND idpre = cpp.id
								AND fijo = TRUE
							LIMIT
								1
						), FALSE)
							AS fijo
					FROM
						catalogo_productos_proveedor cpp
						LEFT JOIN catalogo_mat_primas cmp
							ON (cmp.codmp = cpp.codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (tuc.idunidad = cmp.unidadconsumo)
						LEFT JOIN tipo_presentacion tp
							ON (tp.idpresentacion = cpp.presentacion)
						LEFT JOIN porcentajes_pedidos_proveedores ppp
							ON (ppp.presentacion = cpp.id)
						LEFT JOIN catalogo_proveedores cp
							ON (cp.num_proveedor = cpp.num_proveedor)
					GROUP BY
						cpp.id,
						cpp.codmp,
						num_pro,
						nombre_pro,
						tp.descripcion,
						contenido,
						tuc.descripcion,
						precio
					ORDER BY
						codmp,
						por DESC
				';
				
				$tmp = $db->query($sql);
				
				$proveedores = array();
				$presentaciones = array();
				if ($tmp) {
					$codmp = NULL;
					
					foreach ($tmp as $t) {
						if ($codmp != $t['codmp']) {
							$codmp = $t['codmp'];
							
							$num_pro = NULL;
						}
						
						if ($num_pro != $t['num_pro']) {
							$num_pro = $t['num_pro'];
							
							$proveedores[$codmp][] = array(
								'num_pro'    => $num_pro,
								'nombre_pro' => utf8_encode($t['nombre_pro']),
								'fijo'       => $t['fijo'] == 't' ? TRUE : FALSE
							);
						}
						
						$presentaciones[$codmp][$num_pro][] = array(
							'id'        => intval($t['id']),
							'contenido' => floatval($t['contenido']),
							'fijo'      => $t['fijo'] == 't' ? TRUE : FALSE,
							'value'     => implode('|', array(
								'id'           => intval($t['id']),
								'presentacion' => $t['presentacion'],
								'contenido'    => floatval($t['contenido']),
								'unidad'       => $t['unidad'],
								'precio'       => floatval($t['precio'])
							)),
							'text'      => $t['presentacion'] . ($t['unidad'] != $t['presentacion'] || $t['contenido'] > 1 ? ' DE ' . $t['contenido'] . ' ' . $t['unidad'] . ($t['contenido'] > 1 ? (in_array($t['unidad'][strlen($t['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : '') . ' | $' . number_format($t['precio'], 2, '.', ',')
						);
					}
				}
				
				$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasMesResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				
				foreach ($result as $i => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha', $rec['fecha']);
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('pedido');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('i', $i);
					$tpl->assign('id', $rec['id']);
					$tpl->assign('checked', $rec['checked'] == 't' ? ' checked' : '');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
					$tpl->assign('cantidad', number_format($rec['cantidad'], 2, '.', ','));
					$tpl->assign('existencia', $rec['existencia']);
					$tpl->assign('consumo', $rec['consumo']);
					$tpl->assign('unidad', $rec['unidad']);
					$tpl->assign('tomar_consumo', $rec['tomar_consumo'] == 't' ? ' checked' : '');
					$tpl->assign('observaciones', utf8_encode($rec['observaciones']));
					$tpl->assign('fijo', $rec['fijo'] == 't' ? ' checked' : '');
					
					$num_pro = NULL;
					$contenido = 0;
					
					if (isset($proveedores[$rec['codmp']])) {
						foreach ($proveedores[$rec['codmp']] as $j => $pro) {
							$tpl->newBlock('pro');
							$tpl->assign('value', $pro['num_pro']);
							$tpl->assign('text', $pro['nombre_pro']);
							
							if ($rec['num_pro'] > 0 && $rec['num_pro'] == $pro['num_pro']) {
								$tpl->assign('selected', ' selected');
								
								$num_pro = $pro['num_pro'];
							}
							else if (get_val($rec['num_pro']) == 0 && $pro['fijo']) {
								$tpl->assign('selected', ' selected');
								
								$num_pro = $pro['num_pro'];
							}
							else if ($j == 0) {
								$num_pro = $pro['num_pro'];
							}
						}
						
						foreach ($presentaciones[$rec['codmp']][$num_pro] as $j => $pre) {
							$tpl->newBlock('pre');
							$tpl->assign('value', $pre['value']);
							$tpl->assign('text', $pre['text']);
							
							if ($rec['idpre'] > 0 && $rec['idpre'] == $pre['id']) {
								$tpl->assign('selected', ' selected');
								
								$contenido = $pre['contenido'];
							}
							else if (get_val($rec['idpre']) == 0 && $pre['fijo']) {
								$tpl->assign('selected', ' selected');
								
								$contenido = $pre['contenido'];
							}
							else if ($j == 0) {
								$contenido = $pre['contenido'];
							}
						}
						
						$entregar = $rec['tomar_consumo'] == 't' ? ceil($rec['cantidad'] / $contenido) : $rec['cantidad'];
						
						$tpl->assign('pedido.entregar', $entregar > 0 ? number_format($entregar, 2) : '');
					}
				}
				
				echo $tpl->getOutputContent();
			}
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
				$sql = '
					SELECT
						existencia
					FROM
						inventario_virtual
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND codmp = ' . $_REQUEST['codmp'] . '
				';
				
				$existencia = $db->query($sql);
				
				$sql = '
					SELECT
						EXTRACT(year FROM fecha)
							AS anio,
						EXTRACT(month FROM fecha)
							AS mes,
						SUM(cantidad)
							AS consumo
					FROM
						mov_inv_real mir
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND codmp = ' . $_REQUEST['codmp'] . '
						AND tipo_mov = TRUE
						AND fecha BETWEEN (
							SELECT
								MAX(fecha)
							FROM
								historico_inventario
							WHERE
								num_cia = mir.num_cia
								AND codmp = mir.codmp
						) - INTERVAL \'2 months\' + INTERVAL \'1 day\' AND (
							SELECT
								MAX(fecha)
							FROM
								historico_inventario
							WHERE
								num_cia = mir.num_cia
								AND codmp = mir.codmp
						)
					GROUP BY
						anio,
						mes
					ORDER BY
						anio,
						mes
				';
				
				$consumos = $db->query($sql);
				
				if ($consumos) {
					if (count($consumos) > 1) {
						if ($consumos[0]['consumo'] > $consumos[1]['consumo']) {
							$consumo = floatval($consumos[0]['consumo']);
						}
						else {
							$consumo = floatval($consumos[1]['consumo']);
						}
					}
					else {
						$consumo = floatval($consumos[0]['consumo']);
					}
				}
				else {
					$consumo = 0;
				}
				
				$data = array(
					'nombre_mp' => utf8_encode($result[0]['nombre']),
					'existencia' => $existencia ? floatval($existencia[0]['existencia']) : 0,
					'consumo' => $consumo
				);
				
				echo json_encode($data);
			}
		break;
		
		case 'obtenerPro':
			$sql = '
				SELECT
					cpp.num_proveedor
						AS value,
					cp.nombre
						AS text,
					COALESCE(MAX(ppp.porcentaje), 0)
						AS por
				FROM
					catalogo_productos_proveedor cpp
					LEFT JOIN porcentajes_pedidos_proveedores ppp
						 ON (ppp.presentacion = cpp.id)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = cpp.num_proveedor)
				WHERE
					cpp.codmp = ' . $_REQUEST['codmp'] . '
				GROUP BY
					value,
					text
				ORDER BY
					por,
					text
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data = array();
				
				foreach ($result as $rec) {
					$data[] = array(
						'value' => $rec['value'],
						'text' => utf8_encode($rec['text'])
					);
				}
				
				echo json_encode($data);
			}
		break;
		
		case 'obtenerPre':
			$sql = '
				SELECT
					cpp.id,
					tp.descripcion
						AS presentacion,
					contenido,
					tuc.descripcion
						AS unidad,
					precio,
					COALESCE(
						(
							SELECT
								porcentaje
							FROM
								porcentajes_pedidos_proveedores ppp
							WHERE
								presentacion = cpp.id
							LIMIT
								1
						), 0
					)
						AS porcentaje
				FROM
					catalogo_productos_proveedor cpp
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
					LEFT JOIN tipo_presentacion tp
						ON (tp.idpresentacion = cpp.presentacion)
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND codmp = ' . $_REQUEST['codmp'] . '
				ORDER BY
					porcentaje DESC,
					precio ASC
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data = array();
				
				foreach ($result as $rec) {
					$data[] = array(
						'value' => implode('|', array(
							'id'           => intval($rec['id']),
							'presentacion' => $rec['presentacion'],
							'contenido'    => floatval($rec['contenido']),
							'unidad'       => $rec['unidad'],
							'precio'       => floatval($rec['precio'])
						)),
						'text' => $rec['presentacion'] . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : '') . ' | $' . number_format($rec['precio'], 2, '.', ',')
					);
				}
				
				echo json_encode($data);
			}
		break;
		
		case 'borrar':
			$sql = '
				UPDATE
					pedidos_panaderia
				SET
					tsdel = now(),
					iddel = ' . $_SESSION['iduser'] . '
				WHERE
					idpedidopanaderia IN (' . implode(', ', $_REQUEST['id']) . ')
			';
			
			$db->query($sql);
		break;
		
		case 'guardar':
			$sql = '';
			
			foreach ($_REQUEST['pedid'] as $i => $pedid) {
				if (isset($_REQUEST['presentacion' . $i]) && $_REQUEST['presentacion' . $i] != '') {
					list($id, $presentacion, $contenido, $unidad, $precio) = explode('|', $_REQUEST['presentacion' . $i]);
				}
				
				$sql .= '
					UPDATE
						pedidos_panaderia
					SET
						checked = ' . (isset($_REQUEST['id' . $i]) ? 'TRUE' : 'FALSE') . ',
						codmp = ' . $_REQUEST['codmp' . $i] . ',
						cantidad = ' . get_val($_REQUEST['cantidad' . $i]) . ',
						tomar_consumo = ' . (isset($_REQUEST['tomar_consumo' . $i]) ? 'TRUE' : 'FALSE') . ',
						num_pro = ' . (isset($_REQUEST['num_pro' . $i]) && $_REQUEST['num_pro' . $i] > 0 ? $_REQUEST['num_pro' . $i] : 'NULL') . ',
						idpre = ' . (isset($_REQUEST['presentacion' . $i]) && $_REQUEST['presentacion' . $i] != '' ? $id : 'NULL') . ',
						fijo = ' . (isset($_REQUEST['fijo' . $i]) ? 'TRUE' : 'FALSE') . '
					WHERE
						idpedidopanaderia = ' . $pedid . '
				' . ";\n";
			}
			
			$db->query($sql);
		break;
		
		case 'anotaciones':
			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor IN (
						SELECT
							num_proveedor
						FROM
							pedidos_panaderia pp
							LEFT JOIN catalogo_productos_proveedor cpp
								ON (cpp.id = pp.idpre)
						WHERE
							tsreg IS NULL
							AND tsdel IS NULL
							AND checked = TRUE
							AND pp.tipo = 1
						GROUP BY
							num_proveedor
					)
				ORDER BY
					num_pro
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasAnotaciones.tpl');
				$tpl->prepare();
				
				$row_color = FALSE;
				foreach ($result as $i => $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'realizarPedido':
			$sql = '
				SELECT
					COALESCE(MAX(folio), 0) + 1
						AS folio
				FROM
					pedidos_new
			';
			$result = $db->query($sql);
			
			$folio = $result[0]['folio'];
			
			$sql = '
				INSERT INTO
					pedidos_new
						(
							folio,
							fecha,
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
							idins,
							tsins,
							programa,
							fecha_solicitud
						)
					SELECT
						' . $folio . ',
						NOW()::DATE,
						pp.num_cia,
						pp.codmp,
						CASE
							WHEN tomar_consumo = TRUE THEN
								cantidad
							ELSE
								ROUND(cantidad * contenido)
						END,
						tuc.descripcion,
						CASE
							WHEN tomar_consumo = TRUE THEN
								CEIL(cantidad / contenido)
							ELSE
								cantidad
						END,
						tp.descripcion,
						contenido,
						precio,
						num_proveedor,
						100,
						' . $_SESSION['iduser'] . ',
						NOW(),
						5,
						fecha
					FROM
						pedidos_panaderia pp
						LEFT JOIN catalogo_productos_proveedor cpp
							ON (cpp.id = pp.idpre)
						LEFT JOIN catalogo_mat_primas cmp
							ON (cmp.codmp = pp.codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (tuc.idunidad = cmp.unidadconsumo)
						LEFT JOIN tipo_presentacion tp
							ON (tp.idpresentacion = cpp.presentacion)
					WHERE
						tsreg IS NULL
						AND tsdel IS NULL
						AND checked = TRUE
						AND pp.tipo = 1
					ORDER BY
						num_cia,
						codmp
			' . ";\n";
			
			$sql .= '
				UPDATE
					pedidos_panaderia
				SET
					idreg = ' . $_SESSION['iduser'] . ',
					tsreg = now()
				WHERE
					tsreg IS NULL
					AND tsdel IS NULL
					AND checked = TRUE
					AND tipo = 1
			' . ";\n";
			
			foreach ($_REQUEST['num_pro_anotacion'] as $i => $num_pro) {
				if ($_REQUEST['anotacion'][$i] != '') {
					$sql .= '
						INSERT INTO
							pedidos_anotaciones
								(
									folio,
									num_proveedor,
									anotaciones,
									idins,
									tsins
								)
							VALUES
								(
									' . $folio . ',
									' . $num_pro . ',
									\'' . $_REQUEST['anotacion'][$i] . '\',
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";
				}
			}
			
			$db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasMesFin.tpl');
			$tpl->prepare();
			
			$tpl->assign('folio', $folio);
			$tpl->assign('fecha', date('d/m/Y'));
			$tpl->assign('no_pedidos', $cont);
			
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

$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasMes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
