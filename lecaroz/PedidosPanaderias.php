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
			$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasInicio.tpl');
			$tpl->prepare();
			
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
			
			foreach ($admins as $admin) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $admin['value']);
				$tpl->assign('text', utf8_encode($admin['text']));
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'p.tsreg IS NULL';
			
			$condiciones[] = 'p.tsdel IS NULL';
			
			$condiciones[] = 'p.cantidad > 0';
			
			$condiciones[] = 'p.tipo = 0';
			
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
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
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
					$condiciones[] = 'p.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}
			
			/*$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					concepto
						AS producto,
					unidad,
					importe
						AS cantidad,
					obs
						AS observaciones
				FROM
					pedidos_tmp p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha,
					producto
			';*/
			
			$sql = '
				SELECT
					idpedidopanaderia
						AS id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					codmp,
					producto,
					cantidad,
					unidad,
					observaciones
				FROM
					pedidos_panaderia p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha,
					codmp
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				
				foreach ($result as $i => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('pedido');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('i', $i);
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('producto', utf8_encode($rec['producto']));
					$tpl->assign('cantidad', number_format($rec['cantidad'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad']);
					$tpl->assign('observaciones', utf8_encode($rec['observaciones']));
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
							'presentacion' => $rec['presentacion'],
							'contenido' => floatval($rec['contenido']),
							'unidad' => $rec['unidad'],
							'precio' => floatval($rec['precio'])
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
					num_proveedor IN (' . implode(', ', array_unique($_REQUEST['num_pro'], SORT_NUMERIC)) . ')
				ORDER BY
					num_pro
			';
			
			$result = $db->query($sql);
			
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
			
			$sql = '';
			
			$cont = 0;
			
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				if (isset($_REQUEST['id' . $i])) {
					list($presentacion, $contenido, $unidad, $precio) = explode('|', $_REQUEST['presentacion' . $i]);
					
					$sql .= '
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
							VALUES
								(
									' . $folio . ',
									now()::date,
									' . $num_cia . ',
									' . $_REQUEST['codmp' . $i] . ',
									' . (isset($_REQUEST['tomar_consumo' . $i]) ? get_val($_REQUEST['cantidad' . $i]) : round(get_val($_REQUEST['cantidad' . $i]) * $contenido)) . ',
									\'' . $unidad . '\',
									' . get_val($_REQUEST['entregar' . $i]) . ',
									\'' . $presentacion . '\',
									' . $contenido . ',
									' . $precio . ',
									' . $_REQUEST['num_pro' . $i] . ',
									100,
									' . $_SESSION['iduser'] . ',
									now(),
									4,
									\'' . $_REQUEST['fecha_solicitud' . $i] . '\'
								)
					' . ";\n";
					
					$sql .= '
						UPDATE
							pedidos_panaderia
						SET
							idreg = ' . $_SESSION['iduser'] . ',
							tsreg = now()
						WHERE
							idpedidopanaderia = ' . $_REQUEST['id' . $i] . '
					' . ";\n";
					
					$cont++;
				}
			}
			
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
			
			$tpl = new TemplatePower('plantillas/ped/PedidosPanaderiasFin.tpl');
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

$tpl = new TemplatePower('plantillas/ped/PedidosPanaderias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
