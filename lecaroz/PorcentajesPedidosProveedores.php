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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
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
				echo utf8_encode($result[0]['nombre']);
			}
		break;

		case 'consultar':
			$sql = '
				SELECT
					id,
					num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					tp.descripcion
						AS presentacion,
					contenido,
					tuc.descripcion
						AS unidad,
					precio
				FROM
					catalogo_productos_proveedor cpp
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN tipo_presentacion tp
						ON (tp.idpresentacion = cpp.presentacion)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
					AND cpp.para_pedido = TRUE
				ORDER BY
					num_proveedor,
					precio
			';

			$result = $db->query($sql);

			if ($result) {
				$proveedores = array();
				$presentaciones = array();

				$num_pro = NULL;
				foreach ($result as $rec) {
					if ($num_pro != $rec['num_pro']) {
						$num_pro = $rec['num_pro'];

						$proveedores[] = array(
							'num_pro'    => $rec['num_pro'],
							'nombre_pro' => utf8_encode($rec['nombre_pro'])
						);

						$presentaciones[$num_pro] = array();
					}

					$presentaciones[$num_pro][] = array(
						'id'           => $rec['id'],
						'presentacion' => $rec['presentacion'],
						'contenido'    => $rec['contenido'],
						'unidad'       => $rec['unidad'],
						'texto'        => (($rec['presentacion'] == $rec['unidad']) ? $rec['presentacion'] : $rec['presentacion'] . ' DE ' . $rec['contenido'] . ' ' . $rec['unidad']) . ($rec['contenido'] > 1 ? 'S' : '') . ' | $' . number_format($rec['precio'], 2, '.', ',')
					);
				}

				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia
					FROM
						catalogo_companias
					WHERE
						num_cia <= 300
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? 'AND idadministrador = ' . $_REQUEST['admin'] : '') . '
					ORDER BY
						num_cia
				';

				$cias = $db->query($sql);

				$sql = '
					SELECT
						num_cia,
						ppp.num_proveedor
							AS num_pro,
						porcentaje,
						presentacion
					FROM
						porcentajes_pedidos_proveedores ppp
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						codmp = ' . $_REQUEST['codmp'] . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? 'AND idadministrador = ' . $_REQUEST['admin'] : '') . '
					ORDER BY
						num_cia,
						num_pro
				';

				$result = $db->query($sql);

				$datos = array();

				if ($result) {
					foreach ($result as $rec) {
						$datos[$rec['num_cia']][$rec['num_pro']] = array(
							'porcentaje'   => $rec['porcentaje'],
							'presentacion' => $rec['presentacion']
						);
					}
				}

				$tpl = new TemplatePower('plantillas/ped/PorcentajesPedidosProveedoresConsulta.tpl');
				$tpl->prepare();

				foreach ($proveedores as $pro) {
					$tpl->newBlock('th');
					$tpl->assign('num_pro', $pro['num_pro']);
					$tpl->assign('nombre_pro', $pro['nombre_pro']);
				}

				$row_color = FALSE;
				foreach ($cias as $i => $cia) {
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $cia['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					$total = 0;
					foreach ($proveedores as $pro) {
						$tpl->newBlock('pro');
						$tpl->assign('i', $i);

						$tpl->assign('num_pro', $pro['num_pro']);

						if (isset($datos[$cia['num_cia']][$pro['num_pro']])) {
							$tpl->assign('porcentaje', number_format($datos[$cia['num_cia']][$pro['num_pro']]['porcentaje'], 2, '.', ','));

							$total += $datos[$cia['num_cia']][$pro['num_pro']]['porcentaje'];
						}

						foreach ($presentaciones[$pro['num_pro']] as $pre) {
							$tpl->newBlock('presentacion');
							$tpl->assign('value', $pre['id']);
							$tpl->assign('text', $pre['texto']);

							if (isset($datos[$cia['num_cia']][$pro['num_pro']]) && $datos[$cia['num_cia']][$pro['num_pro']]['presentacion'] == $pre['id']) {
								$tpl->assign('selected', ' selected');
							}
						}
					}

					$tpl->assign('cia.total', $total > 0 ? number_format($total, 2, '.', ',') : '');
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'actualizar':
			$sql = '';

			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				foreach ($_REQUEST['num_pro_' . $i] as $j => $num_pro) {
					$sql .= 'DELETE FROM porcentajes_pedidos_proveedores WHERE num_cia = ' . $num_cia . ' AND codmp = ' . $_REQUEST['codmp'] . ' AND num_proveedor = ' . $num_pro . ";\n";

					if (get_val($_REQUEST['porcentaje_' . $i][$j]) > 0) {
						$sql .= 'INSERT INTO porcentajes_pedidos_proveedores (num_cia, codmp, num_proveedor, presentacion, porcentaje, idins, tsins) VALUES (' . $num_cia . ', ' . $_REQUEST['codmp'] . ', ' . $num_pro . ', ' . $_REQUEST['presentacion_' . $i][$j] . ', ' . $_REQUEST['porcentaje_' . $i][$j] . ', ' . $_SESSION['iduser'] . ', now())' . ";\n";
					}
				}
			}

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ped/PorcentajesPedidosProveedores.tpl');
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

foreach ($admins as $admin) {
	$tpl->newBlock('admin');
	$tpl->assign('value', $admin['value']);
	$tpl->assign('text', utf8_encode($admin['text']));
}

$tpl->printToScreen();
?>
