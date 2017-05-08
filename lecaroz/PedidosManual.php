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

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/PedidosManualInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre_corto'];
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
								MAX(porcentaje)
							FROM
								porcentajes_pedidos_proveedores ppp
							WHERE
								presentacion = cpp.id
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
			
			$tpl = new TemplatePower('plantillas/ped/PedidosManualAnotaciones.tpl');
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
		
		case 'registrar':
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
				if ($num_cia > 0) {
					list($presentacion, $contenido, $unidad, $precio) = explode('|', $_REQUEST['presentacion'][$i]);
					
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
									programa
								)
							VALUES
								(
									' . $folio . ',
									now()::date,
									' . $num_cia . ',
									' . $_REQUEST['codmp'][$i] . ',
									' . round(get_val($_REQUEST['cantidad'][$i]) * $contenido) . ',
									\'' . $unidad . '\',
									' . get_val($_REQUEST['cantidad'][$i]) . ',
									\'' . $presentacion . '\',
									' . $contenido . ',
									' . $precio . ',
									' . $_REQUEST['num_pro'][$i] . ',
									100,
									' . $_SESSION['iduser'] . ',
									now(),
									2
								)
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
			
			$tpl = new TemplatePower('plantillas/ped/PedidosManualFin.tpl');
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
				$tpl->assign('nombre_pro', utf8_encode($pro['nombre_pro']));
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

$tpl = new TemplatePower('plantillas/ped/PedidosManual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
