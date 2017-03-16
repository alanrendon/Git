<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/class.auxinv.inc.php');

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
		case 'obtenerUnidades':
			$sql = '
				SELECT
					idunidad
						AS id,
					descripcion
						AS unidad
				FROM
					tipo_unidad_consumo
				ORDER BY
					unidad
			';

			$result = $db->query($sql);

			if ($result) {
				$options = '';

				foreach ($result as $rec) {
					$options .= '<option value="' . $rec['id'] . '">' . utf8_encode($rec['unidad']) . '</option>';
				}

				echo $options;
			}
		break;

		case 'validarCia':
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

		case 'validarPro':
			$sql = '
				SELECT
					cpp.id,
					cp.nombre
						AS nombre_pro,
					codmp,
					cmp.nombre
						AS producto,
					contenido,
					idunidad,
					tuc.descripcion
						AS unidad,
					precio,
					cpp.desc1,
					cpp.desc2,
					cpp.desc3,
					ieps,
					iva,
					cmp.porcentaje_ieps
				FROM
					catalogo_productos_proveedor cpp
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
				ORDER BY
					codmp,
					cpp.id
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array();

				$num_pro = NULL;
				foreach ($result as $rec) {
					if ($num_pro == NULL) {
						$num_pro = intval($_REQUEST['num_pro']);

						$data = array(
							'num_pro'    => $num_pro,
							'nombre_pro' => utf8_encode($rec['nombre_pro']),
							'productos'  => array()
						);
					}

					$data['productos'][] = array(
						'id'        => intval($rec['id']),
						'codmp'     => intval($rec['codmp']),
						'producto'  => utf8_encode($rec['producto']),
						'contenido' => floatval($rec['contenido']),
						'idunidad'  => intval($rec['idunidad']),
						'unidad'    => utf8_encode($rec['unidad']),
						'precio'    => floatval($rec['precio']),
						'desc1'     => floatval($rec['desc1']),
						'desc2'     => floatval($rec['desc2']),
						'desc3'     => floatval($rec['desc3']),
						'pieps'     => floatval($rec['porcentaje_ieps']),
						'ieps'      => floatval($rec['ieps']),
						'iva'       => floatval($rec['iva'])
					);
				}

				echo json_encode($data);
			}
		break;

		case 'validarFac':
			$sql = '
				SELECT
					num_fact,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha
				FROM
					facturas f
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
						f.num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND
						num_fact = \'' . $_REQUEST['num_fact'] . '\'
				LIMIT
					1
			';

			$result = $db->query($sql);

			if ($result) {
				echo json_encode(array(
					'num_fact'   => utf8_encode($result[0]['num_fact']),
					'num_cia'    => $result[0]['num_cia'],
					'nombre_cia' => utf8_encode($result[0]['nombre_cia']),
					'fecha'      => $result[0]['fecha']
				));
			}
		break;

		case 'validarFecha':
			$sql = '
				SELECT
					\'' . $_REQUEST['fecha'] . '\'::DATE < MAX(fecha) + INTERVAL \'1 MONTH\' AS status
				FROM
					balances_pan
			';

			$result = $db->query($sql);

			if ($result[0]['status'] == 't') {
				echo -1;
			}
		break;

		case 'modificar':
			$sql = '
				UPDATE
					catalogo_productos_proveedor
				SET
					precio = ' . get_val($_REQUEST['precio']) . ',
					contenido = ' . get_val($_REQUEST['contenido']) . ',
					desc1 = ' . (isset($_REQUEST['pdesc1']) ? get_val($_REQUEST['pdesc1']) : 0) . ',
					desc2 = ' . (isset($_REQUEST['pdesc2']) ? get_val($_REQUEST['pdesc2']) : 0) . ',
					desc3 = ' . (isset($_REQUEST['pdesc3']) ? get_val($_REQUEST['pdesc3']) : 0) . ',
					iva = ' . (isset($_REQUEST['piva']) ? get_val($_REQUEST['piva']) : 0) . ',
					unidad = ' . $_REQUEST['unidad'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);
		break;

		case 'ingresar':
			$sql = '';

			if (get_val($_REQUEST['total']) > 0) {
				$sql .= '
					INSERT INTO
						pasivo_proveedores
							(
								num_cia,
								num_proveedor,
								num_fact,
								fecha,
								total,
								codgastos,
								copia_fac
							)
						VALUES
							(
								' . $_REQUEST['num_cia'] . ',
								' . $_REQUEST['num_pro'] . ',
								\'' . $_REQUEST['num_fact'] . '\',
								\'' . $_REQUEST['fecha'] . '\',
								' . get_val($_REQUEST['total']) . ',
								33,
								COALESCE((
									SELECT
										TRUE
									FROM
										facturas_validacion
									WHERE
										num_cia = ' . $_REQUEST['num_cia'] . '
										AND num_pro = ' . $_REQUEST['num_pro'] . '
										AND num_fact = \'' . $_REQUEST['num_fact'] . '\'
										AND tsbaja IS NULL
								), FALSE)
							)
				' . ";\n";

				$sql .= '
					UPDATE
						facturas_validacion
					SET
						tsvalid = NOW(),
						idvalid = ' . $_SESSION['iduser'] . '
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND num_pro = ' . $_REQUEST['num_pro'] . '
						AND num_fact = \'' . $_REQUEST['num_fact'] . '\'
						AND tsbaja IS NULL
				' . ";\n";
			}

			if (isset($_REQUEST['aclaracion'])) {
				$sql .= '
					INSERT INTO
						facturas_pendientes
							(
								num_proveedor,
								num_fact,
								fecha_solicitud,
								obs
							)
						VALUES
							(
								' . $_REQUEST['num_pro'] . ',
								\'' . $_REQUEST['num_fact'] . '\',
								now()::date,
								\'' . $_REQUEST['observaciones'] . '\'
							)
				' . ";\n";
			}

			$ieps = 0;

			foreach ($_REQUEST['cantidad'] as $i => $cantidad) {
				if (get_val($cantidad) > 0) {
					$data = json_decode($_REQUEST['producto'][$i]);

					$sql .= '
						INSERT INTO
							entrada_mp
								(
									num_cia,
									num_proveedor,
									num_fact,
									fecha,
									codmp,
									cantidad,
									contenido,
									precio,
									pdesc1,
									desc1,
									pdesc2,
									desc2,
									pdesc3,
									desc3,
									pieps,
									ieps,
									importe,
									piva,
									iva,
									regalado,
									iduser
								)
							VALUES
								(
									' . $_REQUEST['num_cia'] . ',
									' . $_REQUEST['num_pro'] . ',
									\'' . $_REQUEST['num_fact'] . '\',
									\''. $_REQUEST['fecha'] . '\',
									' . $data->codmp . ',
									' . get_val($_REQUEST['cantidad'][$i]) . ',
									' . $data->contenido . ',
									' . $data->precio . ',
									' . $data->desc1 . ',
									' . get_val($_REQUEST['desc1'][$i]) . ',
									' . $data->desc2 . ',
									' . get_val($_REQUEST['desc2'][$i]) . ',
									' . $data->desc3 . ',
									' . get_val($_REQUEST['desc3'][$i]) . ',
									' . $data->pieps . ',
									' . get_val($_REQUEST['ieps'][$i]) . ',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . $data->iva . ',
									' . get_val($_REQUEST['iva'][$i]) . ',
									' . (get_val($_REQUEST['importe'][$i]) > 0 ? 'FALSE' : 'TRUE') . ',
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";

					$ieps += get_val($_REQUEST['ieps'][$i]);

					$cantidad = get_val($_REQUEST['cantidad'][$i]) * $data->contenido;
					$total = get_val($_REQUEST['cantidad'][$i]) * $data->precio + get_val($_REQUEST['iva'][$i]) + get_val($_REQUEST['ieps'][$i]) - get_val($_REQUEST['desc1'][$i]) - get_val($_REQUEST['desc2'][$i]) - get_val($_REQUEST['desc3'][$i]);
					$precio = $total / get_val($_REQUEST['cantidad'][$i]);
					$precio_unidad = $total / $cantidad;

					$sql .= '
						INSERT INTO
							mov_inv_real
								(
									num_cia,
									fecha,
									codmp,
									tipo_mov,
									cantidad,
									precio,
									precio_unidad,
									total_mov,
									descripcion,
									num_proveedor,
									num_fact
								)
							VALUES
								(
									' . $_REQUEST['num_cia'] . ',
									\'' . $_REQUEST['fecha'] . '\',
									' . $data->codmp . ',
									FALSE,
									' . $cantidad . ',
									' . $precio . ',
									' . $precio_unidad . ',
									' . $total . ',
									\'COMPRA F. NO. ' . $_REQUEST['num_fact'] . '\',
									' . $_REQUEST['num_pro'] . ',
									\'' . $_REQUEST['num_fact'] . '\'
								)
					' . ";\n";

					if ($id = $db->query('
						SELECT
							idinv
						FROM
							inventario_real
						WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
							AND
								codmp = ' . $data->codmp . '
					')) {
						$sql .= '
							UPDATE
								inventario_real
							SET
								existencia = existencia + ' . $cantidad . '
							WHERE
								idinv = ' . $id[0]['idinv'] . '
						' . ";\n";
					}
					else {
						$pieces = explode('/', $_REQUEST['fecha']);

						$sql .= '
							INSERT INTO
								historico_inventario
									(
										num_cia,
										fecha,
										codmp,
										existencia,
										precio_unidad
									)
								VALUES
									(
										' . $_REQUEST['num_cia'] . ',
										\'' . date('d/m/Y', mktime(0, 0, 0, $pieces[1], 0, $pieces[2])) . '\',
										' . $data->codmp . ',
										0,
										0
									)
						' . ";\n";

						$sql .= '
							INSERT INTO
								inventario_real
									(
										num_cia,
										codmp,
										existencia,
										precio_unidad
									)
								VALUES
									(
										' . $_REQUEST['num_cia'] . ',
										' . $data->codmp . ',
										' . $cantidad . ',
										' . $precio_unidad . '
									)
						' . ";\n";
					}
				}
			}

			$sql .= '
				INSERT INTO
					facturas
						(
							num_cia,
							num_proveedor,
							num_fact,
							fecha,
							importe,
							ieps,
							piva,
							iva,
							pretencion_isr,
							retencion_isr,
							pretencion_iva,
							retencion_iva,
							total,
							codgastos,
							tipo_factura,
							fecha_captura,
							iduser,
							concepto,
							tsins
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							' . $_REQUEST['num_pro'] . ',
							\'' . $_REQUEST['num_fact'] . '\',
							\'' . $_REQUEST['fecha'] . '\',
							' . get_val($_REQUEST['subtotal']) . ',
							' . $ieps . ',
							' . (get_val($_REQUEST['iva_total']) > 0 ? 16 : 0) . ',
							' . get_val($_REQUEST['iva_total']) . ',
							0,
							0,
							0,
							0,
							' . get_val($_REQUEST['total']) . ',
							33,
							0,
							now()::date,
							' . $_SESSION['iduser'] . ',
							\'FACTURA MATERIA PRIMA\',
							now()
						)
			' . ";\n";

			$db->query($sql);

			/*
			@ Actualizar inventario de la compañía
			*/

			$pieces = explode('/', $_REQUEST['fecha']);

			$aux = new AuxInvClass($_REQUEST['num_cia'], $pieces[2], $pieces[1], NULL, 'real');

			$sql = '';

			foreach ($aux->mps as $cod => $mp) {
				$sql .= '
					UPDATE
						inventario_real
					SET
						existencia = ' . $mp['existencia'] . ',
						precio_unidad = ' . $mp['precio'] . '
					WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							codmp = ' . $cod . '
				' . ";\n";
			}

			foreach ($aux->mps as $cod => $mp) {
				$sql .= '
					UPDATE
						historico_inventario
					SET
						existencia = ' . $mp['existencia'] . ',
						precio_unidad = ' . $mp['precio'] . '
					WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							codmp = ' . $cod . '
						AND
							fecha = \'' . $aux->fecha2 . '\'
				' . ";\n";
			}

			if ($sql != '')
			{
				$db->query($sql);
			}

			// [02-Ene-2014] Insertar compra en OpenBravo

			// include_once('includes/class.contabilidad.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');
			// $dbf = new DBclass('pgsql://carlos:D4n13l4*@127.0.0.1:5432/ob_lecaroz', 'autocommit=yes');

			// $conta = new Contabilidad();

			// $conta->registrar_factura($_REQUEST['num_pro'], $_REQUEST['num_fact'], 1);

		break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaMateriaPrima.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
