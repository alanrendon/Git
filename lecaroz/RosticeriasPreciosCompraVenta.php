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
			$tpl = new TemplatePower('plantillas/ros/RosticeriasPreciosCompraVentaInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					tipo_cia = 2
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_mp':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					tipo_cia = FALSE
					AND codmp = ' . $_REQUEST['codmp'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_pro':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "pg.num_cia = {$_REQUEST['num_cia']}";

			$sql = "
				SELECT
					pg.id,
					pg.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pg.codmp,
					cmp.nombre
						AS nombre_mp,
					CASE
						WHEN TRIM(cmp.nombre) != TRIM(pg.nombre_alt) THEN
							pg.nombre_alt
						ELSE
							NULL
					END
						AS nombre_alt,
					pg.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					pg.precio_compra,
					pg.precio_venta,
					COALESCE(CASE WHEN pg.orden > 0 THEN pg.orden ELSE NULL END, CASE WHEN cmp.orden > 0 THEN cmp.orden ELSE NULL END, 99999)
						AS orden,
					pg.decimales
				FROM
					precios_guerra pg
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)

				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					pg.num_cia,
					COALESCE(CASE WHEN pg.orden > 0 THEN pg.orden ELSE NULL END, CASE WHEN cmp.orden > 0 THEN cmp.orden ELSE NULL END, 99999),
					pg.codmp
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ros/RosticeriasPreciosCompraVentaConsulta.tpl');
			$tpl->prepare();

			$cia = $db->query("
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
			");

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($cia[0]['nombre_corto']));

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('nombre_alt', $row['nombre_alt'] != '' ? utf8_encode($row['nombre_alt']) : '&nbsp;');
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('precio_compra', $row['precio_compra'] > 0 ? number_format($row['precio_compra'], 4) : '&nbsp;');
					$tpl->assign('precio_venta', $row['precio_venta'] > 0 ? number_format($row['precio_venta'], 2) : '&nbsp;');
					$tpl->assign('con_decimales', $row['decimales'] == 't' ? '<img src="iconos/accept.png" width="16" height="16">' : '&nbsp;');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ros/RosticeriasPreciosCompraVentaAlta.tpl');
			$tpl->prepare();

			$cia = $db->query("
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
			");

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($cia[0]['nombre_corto']));

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = "
				INSERT INTO
					precios_guerra (
						num_cia,
						codmp,
						nombre_alt,
						num_proveedor,
						precio_compra,
						precio_venta,
						orden,
						decimales
					)
					VALUES (
						{$_REQUEST['num_cia']},
						" . $_REQUEST['codmp'] . ",
						'" . ($_REQUEST['nombre_alt'] != '' ? utf8_decode($_REQUEST['nombre_alt']) : utf8_decode($_REQUEST['nombre_mp'])) . "',
						" . ($_REQUEST['num_pro'] > 0 ? $_REQUEST['num_pro'] : 'NULL') . ",
						" . get_val($_REQUEST['precio_compra']) . ",
						" . get_val($_REQUEST['precio_venta']) . ",
						COALESCE((
							SELECT
								MAX(orden) + 1
							FROM
								precios_guerra
							WHERE
								num_cia = {$_REQUEST['num_cia']}
						), 1),
						{$_REQUEST['decimales']}
					);
			";

			if ($id = $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}) AND metodo = 'actualizar_control_rosticeria' AND parametros = 'num_cia={$_REQUEST['num_cia']}' AND status BETWEEN -99 AND 0"))
			{
				$sql .= "
					UPDATE
						actualizacion_panas
					SET
						tsact = NOW(),
						iduser = {$_SESSION['iduser']}
					WHERE
						id = {$id[0]['id']};
				";
			}
			else
			{
				$sql .= "
					INSERT INTO
						actualizacion_panas (
							num_cia,
							metodo,
							parametros,
							status,
							iduser
						)
						VALUES (
							(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
							'actualizar_control_rosticeria',
							'num_cia={$_REQUEST['num_cia']}',
							0,
							{$_SESSION['iduser']}
						);
				";
			}

			$db->query($sql);

			break;

		case 'modificar':
			$sql = "
				SELECT
					pg.id,
					pg.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pg.codmp,
					cmp.nombre
						AS nombre_mp,
					CASE
						WHEN TRIM(cmp.nombre) != TRIM(pg.nombre_alt) THEN
							pg.nombre_alt
						ELSE
							NULL
					END
						AS nombre_alt,
					pg.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					pg.precio_compra,
					pg.precio_venta,
					COALESCE(CASE WHEN pg.orden > 0 THEN pg.orden ELSE NULL END, CASE WHEN cmp.orden > 0 THEN cmp.orden ELSE NULL END, 99999)
						AS orden,
					pg.decimales
				FROM
					precios_guerra pg
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)

				WHERE
					pg.id = {$_REQUEST['id']}
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ros/RosticeriasPreciosCompraVentaModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('codmp', $result[0]['codmp']);
			$tpl->assign('nombre_mp', utf8_encode($result[0]['nombre_mp']));
			$tpl->assign('nombre_alt', utf8_encode($result[0]['nombre_alt']));
			$tpl->assign('num_pro', $result[0]['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($result[0]['nombre_pro']));
			$tpl->assign('precio_compra', $result[0]['precio_compra'] > 0 ? number_format($result[0]['precio_compra'], 4) : '');
			$tpl->assign('precio_venta', $result[0]['precio_venta'] > 0 ? number_format($result[0]['precio_venta'], 2) : '');
			$tpl->assign('decimales_' . $result[0]['decimales'], ' checked="checked"');

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$sql = "
				UPDATE
					precios_guerra
				SET
					codmp = " . $_REQUEST['codmp'] . ",
					nombre_alt = '" . ($_REQUEST['nombre_alt'] != '' ? utf8_decode($_REQUEST['nombre_alt']) : utf8_decode($_REQUEST['nombre_mp'])) . "',
					num_proveedor = " . ($_REQUEST['num_pro'] > 0 ? $_REQUEST['num_pro'] : 'NULL') . ",
					precio_compra = " . get_val($_REQUEST['precio_compra']) . ",
					precio_venta = " . get_val($_REQUEST['precio_venta']) . ",
					decimales = {$_REQUEST['decimales']}
				WHERE
					id = {$_REQUEST['id']};
			";

			if ($id = $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}) AND metodo = 'actualizar_control_rosticeria' AND parametros = 'num_cia={$_REQUEST['num_cia']}' AND status BETWEEN -99 AND 0"))
			{
				$sql .= "
					UPDATE
						actualizacion_panas
					SET
						tsact = NOW(),
						iduser = {$_SESSION['iduser']}
					WHERE
						id = {$id[0]['id']};
				";
			}
			else
			{
				$sql .= "
					INSERT INTO
						actualizacion_panas (
							num_cia,
							metodo,
							parametros,
							status,
							iduser
						)
						VALUES (
							(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
							'actualizar_control_rosticeria',
							'num_cia={$_REQUEST['num_cia']}',
							0,
							{$_SESSION['iduser']}
						);
				";
			}

			$db->query($sql);

			break;

		case 'do_baja':
			$sql = "
				DELETE FROM
					precios_guerra
				WHERE
					id = {$_REQUEST['id']};
			";

			if ($id = $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}) AND metodo = 'actualizar_control_rosticeria' AND parametros = 'num_cia={$_REQUEST['num_cia']}' AND status BETWEEN -99 AND 0"))
			{
				$sql .= "
					UPDATE
						actualizacion_panas
					SET
						tsact = NOW(),
						iduser = {$_SESSION['iduser']}
					WHERE
						id = {$id[0]['id']};
				";
			}
			else
			{
				$sql .= "
					INSERT INTO
						actualizacion_panas (
							num_cia,
							metodo,
							parametros,
							status,
							iduser
						)
						VALUES (
							(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
							'actualizar_control_rosticeria',
							'num_cia={$_REQUEST['num_cia']}',
							0,
							{$_SESSION['iduser']}
						);
				";

			}

			$db->query($sql);

			break;

		case 'actualizar_orden':
			$sql = '';

			foreach ($_REQUEST['orden'] as $orden)
			{
				$data = json_decode($orden);

				$sql .= "UPDATE precios_guerra SET orden = {$data->orden} WHERE id = {$data->id};\n";
			}

			if ($id = $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}) AND metodo = 'actualizar_control_rosticeria' AND parametros = 'num_cia={$_REQUEST['num_cia']}' AND status BETWEEN -99 AND 0"))
			{
				$sql .= "
					UPDATE
						actualizacion_panas
					SET
						tsact = NOW(),
						iduser = {$_SESSION['iduser']}
					WHERE
						id = {$id[0]['id']};
				";
			}
			else
			{
				$sql .= "
					INSERT INTO
						actualizacion_panas (
							num_cia,
							metodo,
							parametros,
							status,
							iduser
						)
						VALUES (
							(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
							'actualizar_control_rosticeria',
							'num_cia={$_REQUEST['num_cia']}',
							0,
							{$_SESSION['iduser']}
						);
				";
			}

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/RosticeriasPreciosCompraVenta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
