<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/auxinv.inc.php');

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

function toInt($value) {
	return intval($value);
}

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerRemision':
			$condiciones[] = 'num_rem = \'' . strtoupper($_REQUEST['num_rem']) . '\'';
			$condiciones[] = 'tsfac IS NULL';

			if (!in_array($_SESSION['iduser'], array(1, 4, 8, 14, 18, 39))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			$sql = '
				SELECT
					hr.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					fecha,
					cajas,
					peso_neto,
					precio,
					total
				FROM
					huevo_remisiones hr
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					fecha DESC,
					num_pro
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array();

				foreach ($result as $rec) {
					$data[] = array(
						'text'           => utf8_encode('[' . str_pad($rec['num_pro'], 4, '0', STR_PAD_LEFT) . '] ' . $rec['nombre_pro']),
						'value'          => json_encode(array(
							'num_pro'    => intval($rec['num_pro']),
							'nombre_pro' => utf8_encode($rec['nombre_pro']),
							'num_cia'    => intval($rec['num_cia']),
							'nombre_cia' => utf8_encode($rec['nombre_cia']),
							'fecha'      => $rec['fecha'],
							'cajas'      => intval($rec['cajas']),
							'peso_neto'  => floatval($rec['peso_neto']),
							'precio'     => floatval($rec['precio']),
							'total'      => floatval($rec['total'])
						))
					);
				}

				echo json_encode($data);
			}
		break;

		case 'validarFactura':
			$sql = '
				SELECT
					num_cia,
					nombre_corto,
					fecha,
					total
				FROM
					facturas f
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					f.num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND num_fact = \'' . $_REQUEST['num_fact'] . '\'
			';

			$result = $db->query($sql);

			if ($result) {
				$rec = $result[0];

				$data = array(
					'num_cia'    => intval($rec['num_cia']),
					'nombre_cia' => utf8_encode($rec['nombre_coerto']),
					'fecha'      => $rec['fecha'],
					'total'      => floatval($rec['total'])
				);

				echo json_encode($data);
			}
		break;

		case 'asociar':
			$sql = '';

			$no_borradas = array();

			foreach ($_REQUEST['num_rem'] as $i => $num_rem) {
				if ($num_rem != '') {
					$data = json_decode($_REQUEST['num_pro'][$i]);

					if ($_REQUEST['num_fact'][$i] != '') {
						$sql = '
							UPDATE
								huevo_remisiones
							SET
								num_fact = \'' . $_REQUEST['num_fact'][$i] . '\',
								idfac = ' . $_SESSION['iduser'] . ',
								tsfac = now()
							WHERE
								num_proveedor = ' . $data->num_pro . '
								AND num_rem = \'' . $num_rem . '\'
						' . ";\n";

//						$sql .= '
//							UPDATE
//								mov_inv_real
//							SET
//								num_fact = \'' . $_REQUEST['num_fact'][$i] . '\',
//								descripcion = \'COMPRA F. NO. ' . $_REQUEST['num_fact'][$i] . '\'
//							WHERE
//								num_cia = ' . $data->num_cia . '
//								AND fecha = \'' . $data->fecha . '\'
//								AND codmp = 148
//								AND tipo_mov = FALSE
//								AND num_proveedor = ' . $data->num_pro . '
//								AND num_fact = \'' . $num_rem . '\'
//						' . ";\n";

						$sql .= '
							INSERT INTO
								facturas
									(
										num_proveedor,
										num_cia,
										num_fact,
										fecha,
										importe,
										total,
										codgastos,
										tipo_factura,
										fecha_captura,
										iduser,
										concepto
									)
								VALUES
									(
										' . $data->num_pro . ',
										' . $data->num_cia . ',
										\'' . $_REQUEST['num_fact'][$i] . '\',
										\'' . $data->fecha . '\',
										' . $data->total . ',
										' . $data->total . ',
										33,
										0,
										now()::date,
										' . $_SESSION['iduser'] . ',
										\'ESTA FACTURA SUSTITUYE A LA REMISION ' . $num_rem . '\'
									)
						' . ";\n";

						$sql .= '
							INSERT INTO
								entrada_mp
									(
										num_proveedor,
										num_cia,
										num_fact,
										fecha,
										codmp,
										cantidad,
										precio,
										contenido,
										importe,
										iduser
									)
								VALUES
									(
										' . $data->num_pro . ',
										' . $data->num_cia . ',
										\'' . $_REQUEST['num_fact'][$i] . '\',
										\'' . $data->fecha . '\',
										148,
										' . $data->cajas . ',
										' . ($data->total / $data->cajas) . ',
										360,
										' . $data->total . ',
										' . $_SESSION['iduser'] . '
									)
						' . ";\n";

						$sql .= '
							INSERT INTO
								pasivo_proveedores
									(
										num_proveedor,
										num_cia,
										num_fact,
										fecha,
										descripcion,
										total,
										codgastos,
										copia_fac
									)
								VALUES
									(
										' . $data->num_pro . ',
										' . $data->num_cia . ',
										\'' . $_REQUEST['num_fact'][$i] . '\',
										\'' . $data->fecha . '\',
										\'FACTURA MATERIA PRIMA\',
										' . $data->total . ',
										33,
										TRUE
									)
						' . ";\n";

						$db->query($sql);
					}
					else {
						list($dia, $mes, $anio) = array_map('toInt', explode('/', $data->fecha));

						$result = $db->query('
							SELECT
								id
							FROM
								balances_pan
							WHERE
								num_cia = ' . $data->num_cia . '
								AND anio = ' . $anio . '
								AND mes = ' . $mes . '
						');

						if (!$result) {
							$sql = '
								DELETE FROM
									huevo_remisiones
								WHERE
									num_proveedor = ' . $data->num_pro . '
									AND num_rem = \'' . $num_rem . '\'
							' . ";\n";

							$sql .= '
								DELETE FROM
									huevo_pesadas
								WHERE
									num_proveedor = ' . $data->num_pro . '
									AND num_rem = \'' . $num_rem . '\'
							' . ";\n";

							$sql .= '
								DELETE FROM
									mov_inv_real
								WHERE
									num_cia = ' . $data->num_cia . '
									AND fecha = \'' . $data->fecha . '\'
									AND codmp = 148
									AND tipo_mov = FALSE
									AND num_proveedor = ' . $data->num_pro . '
									AND num_fact = \'' . $num_rem . '\'
							' . ";\n";

							$db->query($sql);

							ActualizarInventario($data->num_cia, $anio, $mes, 148, TRUE, FALSE, TRUE, FALSE);
						}
						else {
							$no_borradas[] = array(
								'num_pro'    => $data->num_pro,
								'nombre_pro' => $data->nombre_pro,
								'num_rem'    => $num_rem
							);
						}
					}
				}
			}

			if (count($no_borradas) > 0) {
				echo json_encode($no_borradas);
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/HuevoAsociarFacturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
