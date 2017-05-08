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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerPro':
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
				echo $result[0]['nombre'];
			}
		break;

		case 'listado':
			$sql = '
				SELECT
					num_proveedor
						AS
							num_pro,
					nombre
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor ' . ($_SESSION['tipo_usuario'] == 2 ? '> 9000' : '<= 9000') . '
				ORDER BY
					nombre
			';
			$result = $db->query($sql);

			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/ProveedoresModificacionListado.tpl');
				$tpl->prepare();

				$color = FALSE;
				foreach ($result as $r) {
					$tpl->newBlock('row');

					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('num_pro', $r['num_pro']);
					$tpl->assign('proveedor', '[' . str_pad($r['num_pro'], 5, '0', STR_PAD_LEFT) . '] ' . utf8_encode($r['nombre']));

					$color = !$color;
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'descuento':
			$sql = '
				SELECT
					cod,
					concepto,
					CASE
						WHEN tipo = 1 THEN
							\'COMPRA\'
						WHEN tipo = 2 THEN
							\'PAGO\'
					END
						AS
							tipo
				FROM
					cat_conceptos_descuentos
				WHERE
					cod = ' . $_REQUEST['cod'] . '
			';
			$result = $db->query($sql);

			if ($result) {
				echo json_encode($result[0]);
			}
		break;

		case 'modificar':
			$sql = '
				SELECT
					num_proveedor,
					clave_seguridad,
					nombre,
					rfc,
					curp,
					tipopersona,
					idtipoproveedor,
					calle,
					no_exterior,
					no_interior,
					colonia,
					localidad,
					referencia,
					municipio,
					estado,
					pais,
					codigo_postal,
					contacto,
					telefono1,
					telefono2,
					fax,
					email1,
					email2,
					email3,
					observaciones,
					tipo_doc,
					CASE
						WHEN verfac = \'TRUE\' THEN
							\' checked\'
						ELSE
							\'\'
					END
						AS
							verfac,
					CASE
						WHEN restacompras = \'TRUE\' THEN
							\' checked\'
						ELSE
							\'\'
					END
						AS
							restacompras,
					prioridad,
					idtipopago,
					diascredito,
					facturas_por_mes,
					facturas_por_pago,
					trans,
					CASE
						WHEN para_abono = \'TRUE\' THEN
							\' checked\'
						ELSE
							\'\'
					END
						AS
							para_abono,
					idbanco,
					sucursal,
					"IdEntidad",
					plaza_banxico,
					referencia_bancaria,
					cuenta,
					clabe,
					pass_site,
					contacto1,
					contacto2,
					contacto3,
					contacto4,
					desc1,
					cod_desc1,
					desc2,
					cod_desc2,
					desc3,
					cod_desc3,
					desc4,
					cod_desc4
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
			';
			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/ProveedoresModificacionDatos.tpl');
			$tpl->prepare();

			foreach ($result[0] as $key => $value) {
				$tpl->assign($key, utf8_encode($value));
			}

			$r = $result[0];

			$tpl->assign('tipopersona_' . $r['tipopersona'], ' checked');

			$sql = '
				SELECT
					idtipoproveedor
						AS
							id,
					UPPER(descripcion)
						AS
							tipo
				FROM
					tipo_proveedor
				ORDER BY
					id
			';
			$tipos = $db->query($sql);

			if ($tipos) {
				foreach ($tipos as $t) {
					$tpl->newBlock('tipo_proveedor');
					$tpl->assign('id', $t['id']);
					$tpl->assign('tipo', $t['tipo']);

					if ($t['id'] == $r['idtipoproveedor']) {
						$tpl->assign('selected', ' selected');
					}
				}

				$tpl->gotoBlock('_ROOT');
			}

			$sql = '
				SELECT
					pais
				FROM
					catalogo_paises
				ORDER BY
					pais
			';

			$paises = $db->query($sql);

			if ($paises) {
				foreach ($paises as $p) {
					$tpl->newBlock('pais');
					$tpl->assign('pais', utf8_encode(strtoupper(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $p['pais']))));

					if ($r['pais'] != '' && utf8_encode(strtoupper(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $p['pais']))) == utf8_encode(strtoupper(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $r['pais'])))) {
						$tpl->assign('selected', ' selected');
					}
					else if ($r['pais'] == '' && $p['pais'] == 'México') {
						$tpl->assign('selected', ' selected');
					}
				}

				$tpl->gotoBlock('_ROOT');
			}

			$tpl->assign('tipo_doc_' . $r['tipo_doc'], ' checked');
			$tpl->assign('prioridad_' . $r['prioridad'], ' checked');
			$tpl->assign('idtipopago_' . $r['idtipopago'], ' checked');
			$tpl->assign('trans_' . $r['trans'], ' checked');

			$sql = '
				SELECT
					idbanco
						AS
							id,
					nombre
				FROM
					catalogo_bancos
				ORDER BY
					nombre
			';
			$bancos = $db->query($sql);

			if ($bancos) {
				foreach ($bancos as $b) {
					$tpl->newBlock('banco');
					$tpl->assign('id', $b['id']);
					$tpl->assign('nombre', $b['nombre']);

					if ($b['id'] == $r['idbanco']) {
						$tpl->assign('selected', ' selected');
					}
				}

				$tpl->gotoBlock('_ROOT');
			}

			$sql = '
				SELECT
					"IdEntidad"
						AS
							id,
					UPPER("Entidad")
						AS
							entidad
				FROM
					catalogo_entidades
				ORDER BY
					"IdEntidad"
			';
			$entidades = $db->query($sql);

			foreach ($entidades as $e) {
				$tpl->newBlock("entidad");
				$tpl->assign("id", $e['id']);
				$tpl->assign("entidad", $e['entidad']);

				if ($e['id'] == $r['IdEntidad']) {
					$tpl->assign('selected', ' selected');
				}

				$tpl->gotoBlock('_ROOT');
			}

			if ($_SESSION['tipo_usuario'] == 2 || $_SESSION['iduser'] == 1) {
				$tpl->newBlock('extra');

				for ($i = 1; $i <= 4; $i++) {
					$tpl->assign('contacto' . $i);
					$tpl->assign('desc' . $i, $r['desc' . $i] > 0 ? number_format($r['desc' . $i], 2, '.', ',') : '');
					$tpl->assign('cod_desc' . $i, $r['cod_desc' . $i] > 0 ? $r['cod_desc' . $i] : '');
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'actualizar':
			function map($value) {
				return utf8_decode($value);
			}

			$datos = array_map('map', $_REQUEST);

			unset($datos['regresar']);
			unset($datos['accion']);
			unset($datos['actualizar']);
			unset($datos['PPA_ID']);
			unset($datos['PHPSESSID']);
			unset($datos['webfx-tree-cookie-persistence']);

			if (!isset($_REQUEST['verfac'])) {
				$datos['verfac'] = 'FALSE';
			}

			if (!isset($_REQUEST['restacompras'])) {
				$datos['restacompras'] = 'FALSE';
			}

			if (!isset($_REQUEST['para_abono'])) {
				$datos['para_abono'] = 'FALSE';
			}

			foreach ($datos as $key => $value) {
				$fields[] = '"' . $key . '" = ' . (trim($value) != '' ? '\'' . $value . '\'' : 'NULL');
			}

			$sql = '
				UPDATE
					catalogo_proveedores
				SET
					' . implode(', ', $fields) . '
				WHERE
					num_proveedor = ' . $_REQUEST['num_proveedor'] . '
			';
			$db->query($sql);
		break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/ProveedoresModificacionInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/ProveedoresModificacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
