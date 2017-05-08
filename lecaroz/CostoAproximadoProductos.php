<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
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

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/CostoAproximadoProductosInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_mp':
			$sql = "SELECT
				cmp.nombre,
				u.descripcion AS unidad,
				COALESCE((
					SELECT
						COALESCE(precio_unidad, 0)
					FROM
						inventario_real
					WHERE
						num_cia = 99
						AND codmp = {$_REQUEST['codmp']}
					LIMIT 1
				), 0) AS precio_unidad
			FROM
				catalogo_mat_primas cmp
				LEFT JOIN tipo_unidad_consumo u ON (u.idunidad = cmp.unidadconsumo)
			WHERE
				cmp.tipo_cia = TRUE
				AND cmp.codmp = {$_REQUEST['codmp']}";

			$result = $db->query($sql);

			if ($result) {
				echo json_encode(array(
					'nombre'		=> utf8_encode($result[0]['nombre']),
					'unidad'		=> utf8_encode($result[0]['unidad']),
					'precio_unidad'	=> floatval($result[0]['precio_unidad'])
				));
			}

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "cap.eliminado IS NULL";

			if (trim($_REQUEST['nombre_producto']) != '')
			{
				$condiciones[] = "cap.nombre_producto LIKE '%{$_REQUEST['nombre_producto']}%'";
			}

			$sql = "SELECT
				cap.id,
				cap.nombre_producto,
				cap.total_consumo,
				cap.porc_raya,
				cap.raya,
				cap.costo_total
			FROM
				costos_aproximados_productos cap
			WHERE
			" . implode(' AND ', $condiciones) . "
			ORDER BY
				cap.nombre_producto";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/CostoAproximadoProductosConsulta.tpl');
			$tpl->prepare();

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('nombre_producto', utf8_encode($row['nombre_producto']));
					$tpl->assign('total_consumo', $row['total_consumo'] > 0 ? number_format($row['total_consumo'], 6) : '&nbsp;');
					$tpl->assign('raya', $row['raya'] > 0 ? '<span class="orange font6" style="float:left;">(' . number_format($row['porc_raya'], 6) . '%)&nbsp;&nbsp;</span>' . number_format($row['raya'], 6) : '&nbsp;');
					$tpl->assign('costo_total', $row['costo_total'] > 0 ? number_format($row['costo_total'], 6) : '&nbsp;');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/bal/CostoAproximadoProductosAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$ts = date('d-m-Y H:i:s');

			$db->query( "INSERT INTO costos_aproximados_productos (
				nombre_producto,
				total_consumo,
				porc_raya,
				raya,
				costo_total,
				creado,
				modificado
			)
			VALUES (
				'{$_REQUEST['nombre_producto']}',
				" . ($_REQUEST['total_consumo'] > 0 ? $_REQUEST['total_consumo'] : 0) . ",
				" . ($_REQUEST['porc_raya'] > 0 ? $_REQUEST['porc_raya'] : 0) . ",
				" . ($_REQUEST['raya'] > 0 ? $_REQUEST['raya'] : 0) . ",
				" . ($_REQUEST['costo_total'] > 0 ? $_REQUEST['costo_total'] : 0) . ",
				'{$ts}',
				'{$ts}'
			)");

			if ($id = $db->query("SELECT MAX(id) AS id FROM costos_aproximados_productos"))
			{
				foreach ($_REQUEST['costo_consumo'] as $i => $costo_consumo)
				{
					if (get_val($costo_consumo) > 0)
					{
						$db->query("INSERT INTO costos_aproximados_productos_detalle (
							producto_id,
							codmp,
							precio_unidad,
							cantidad,
							costo_consumo,
							creado,
							modificado
						) VALUES (
							{$id[0]['id']},
							{$_REQUEST['codmp'][$i]},
							" . get_val($_REQUEST['precio_unidad'][$i]) . ",
							" . get_val($_REQUEST['cantidad'][$i]) . ",
							" . get_val($costo_consumo) . ",
							'{$ts}',
							'{$ts}'
						)");
					}
				}
			}

			break;

		case 'modificar':
			$result = $db->query("SELECT
				cap.id,
				cap.nombre_producto,
				cap.total_consumo,
				cap.porc_raya,
				cap.raya,
				cap.costo_total
			FROM
				costos_aproximados_productos cap
			WHERE
				cap.id = {$_REQUEST['id']}");

			if ( ! $result)
			{
				return FALSE;
			}

			$tpl = new TemplatePower('plantillas/bal/CostoAproximadoProductosModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre_producto', utf8_encode($result[0]['nombre_producto']));
			$tpl->assign('total_consumo', $result[0]['total_consumo'] > 0 ? number_format($result[0]['total_consumo'], 6) : '');
			$tpl->assign('porc_raya', $result[0]['porc_raya'] > 0 ? $result[0]['porc_raya'] : '');
			$tpl->assign('raya', $result[0]['raya'] > 0 ? number_format($result[0]['raya'], 6) : '');
			$tpl->assign('costo_total', $result[0]['costo_total'] > 0 ? number_format($result[0]['costo_total'], 6) : '');

			$detalles = $db->query("SELECT
				capd.id,
				capd.codmp,
				cmp.nombre AS nombre_mp,
				u.descripcion AS unidad,
				capd.precio_unidad,
				capd.cantidad,
				capd.costo_consumo
			FROM
				costos_aproximados_productos_detalle capd
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				LEFT JOIN tipo_unidad_consumo u ON (u.idunidad = cmp.unidadconsumo)
			WHERE
				capd.producto_id = {$_REQUEST['id']}
				AND eliminado IS NULL");

			if ($detalles)
			{
				foreach ($detalles as $i => $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('i', $i);
					$tpl->assign('row_id', $row['id']);
					$tpl->assign('cantidad', $row['cantidad']);
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('unidad', utf8_encode($row['unidad']));
					$tpl->assign('precio_unidad', number_format($row['precio_unidad'], 6));
					$tpl->assign('costo_consumo', number_format($row['costo_consumo'], 6));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$ts = date('d-m-Y H:i:s');

			$sql = "UPDATE costos_aproximados_productos
				SET
					nombre_producto = '" . ($_REQUEST['nombre_producto'] != '' ? utf8_decode($_REQUEST['nombre_producto']) : '') . "',
					total_consumo = " . get_val($_REQUEST['total_consumo']) . ",
					porc_raya = " . get_val($_REQUEST['porc_raya']) . ",
					raya = " . get_val($_REQUEST['raya']) . ",
					costo_total = " . get_val($_REQUEST['costo_total']) . ",
					modificado = '{$ts}'
				WHERE
					id = {$_REQUEST['id']};\n";

			foreach ($_REQUEST['row_id'] as $i => $row_id)
			{
				if ($row_id > 0)
				{
					if ($_REQUEST['costo_consumo'][$i] > 0)
					{
						$sql .= "UPDATE costos_aproximados_productos_detalle
						SET
							codmp = {$_REQUEST['codmp'][$i]},
							precio_unidad = " . get_val($_REQUEST['precio_unidad'][$i]) . ",
							cantidad = " . get_val($_REQUEST['cantidad'][$i]) . ",
							costo_consumo = " . get_val($_REQUEST['costo_consumo'][$i]) . ",
							modificado = '{$ts}'
						WHERE
							id = {$row_id};\n";
					}
					else
					{
						$sql .= "UPDATE costos_aproximados_productos_detalle
						SET
							eliminado = '{$ts}'
						WHERE
							id = {$row_id};\n";
					}
				}
				else if ($_REQUEST['costo_consumo'][$i] > 0)
				{
					$sql .= "INSERT INTO costos_aproximados_productos_detalle (
						producto_id,
						codmp,
						precio_unidad,
						cantidad,
						costo_consumo,
						creado,
						modificado
					) VALUES (
						{$_REQUEST['id']},
						{$_REQUEST['codmp'][$i]},
						" . get_val($_REQUEST['precio_unidad'][$i]) . ",
						" . get_val($_REQUEST['cantidad'][$i]) . ",
						" . get_val($costo_consumo) . ",
						'{$ts}',
						'{$ts}'
					);\n";
				}
			}

			$db->query($sql);

			break;

		case 'do_baja':
			$db->query("UPDATE costos_aproximados_productos
			SET eliminado = NOW()
			WHERE
				id = {$_REQUEST['id']}");

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/CostoAproximadoProductos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
