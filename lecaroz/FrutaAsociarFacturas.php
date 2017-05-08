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

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

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
					SUM(total)
						AS total
				FROM
					fruta_remisiones hr
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_pro,
					nombre_pro,
					num_cia,
					nombre_cia,
					fecha
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
					'nombre_cia' => utf8_encode($rec['nombre_corto']),
					'fecha'      => $rec['fecha'],
					'total'      => floatval($rec['total'])
				);

				echo json_encode($data);
			}
		break;

		case 'asociar':
			$ingresar = array();

			$borrar = array();

			$no_borradas = array();

			foreach ($_REQUEST['num_rem'] as $i => $num_rem) {
				if ($num_rem != '') {
					$data = json_decode($_REQUEST['num_pro'][$i]);

					if ($_REQUEST['num_fact'][$i] != '') {
						if (!isset($ingresar[$data->num_pro][$_REQUEST['num_fact'][$i]])) {
							$ingresar[$data->num_pro][$_REQUEST['num_fact'][$i]] = array(
								'fecha'      => $data->fecha,
								'num_cia'    => $data->num_cia,
								'total'      => 0,
								'remisiones' => array()
							);
						}

						$ingresar[$data->num_pro][$_REQUEST['num_fact'][$i]]['remisiones'][] = $num_rem;
						$ingresar[$data->num_pro][$_REQUEST['num_fact'][$i]]['total'] += $data->total;
					}
					else {
						$borrar[] = array(
							'fecha'      => $data->fecha,
							'num_cia'    => $data->num_cia,
							'num_pro'    => $data->num_pro,
							'nombre_pro' => $data->nombre_pro,
							'num_rem'    => $num_rem
						);
					}
				}
			}

			if (count($ingresar) > 0) {
				foreach ($ingresar as $num_pro => $facs) {
					foreach ($facs as $num_fact => $datos) {
						$sql = '
							UPDATE
								fruta_remisiones
							SET
								num_fact = \'' . $num_fact . '\',
								idfac = ' . $_SESSION['iduser'] . ',
								tsfac = now()
							WHERE
								num_proveedor = ' . $num_pro . '
								AND num_rem IN (\'' . implode('\',\'', $datos['remisiones']) . '\')
						' . ";\n";

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
										' . $num_pro . ',
										' . $datos['num_cia'] . ',
										\'' . $num_fact . '\',
										\'' . $datos['fecha'] . '\',
										' . $datos['total'] . ',
										' . $datos['total'] . ',
										33,
										0,
										now()::date,
										' . $_SESSION['iduser'] . ',
										\'ESTA FACTURA SUSTITUYE A LAS REMISIONES ' . implode(', ', $datos['remisiones']) . '\'
									)
						' . ";\n";

						$sql .= "INSERT INTO entrada_mp (
							num_cia,
							num_proveedor,
							num_fact,
							fecha,
							codmp,
							cantidad,
							contenido,
							precio,
							importe,
							iduser
						)
						SELECT
							num_cia,
							num_proveedor,
							'{$num_fact}',
							'{$datos['fecha']}',
							codmp,
							cantidad,
							1,
							precio,
							total,
							{$_SESSION['iduser']}
						FROM
							fruta_remisiones
						WHERE
							num_cia = {$datos['num_cia']}
							AND num_proveedor = {$num_pro}
							AND num_rem IN ('" . implode("', '", $datos['remisiones']) . "');\n";

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
										' . $num_pro . ',
										' . $datos['num_cia'] . ',
										\'' . $num_fact . '\',
										\'' . $datos['fecha'] . '\',
										\'ESTA FACTURA SUSTITUYE A LAS REMISIONES ' . implode(', ', $datos['remisiones']) . '\',
										' . $datos['total'] . ',
										33,
										TRUE
									)
						' . ";\n";

						$db->query($sql);
					}
				}
			}

			if (count($borrar) > 0) {
				foreach ($borrar as $b) {
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $b['fecha']));

					$result = $db->query('
						SELECT
							id
						FROM
							balances_pan
						WHERE
							num_cia = ' . $b['num_cia'] . '
							AND anio = ' . $anio . '
							AND mes = ' . $mes . '
					');

					if (!$result) {
						$sql = '
							DELETE FROM
								mov_inv_real
							WHERE
								(
									num_cia,
									fecha,
									codmp,
									tipo_mov,
									num_proveedor,
									num_fact
								) IN (
									SELECT
										num_cia,
										fecha,
										codmp,
										FALSE,
										num_proveedor,
										num_rem
									FROM
										fruta_remisiones
									WHERE
										num_proveedor = ' . $b['num_pro'] . '
										AND num_rem = \'' . $b['num_rem'] . '\'
								)
						' . ";\n";

						$sql .= '
							DELETE FROM
								fruta_remisiones
							WHERE
								num_proveedor = ' . $b['num_pro'] . '
								AND num_rem = \'' . $b['num_rem'] . '\'
						' . ";\n";

						$db->query($sql);

						ActualizarInventario($data->num_cia, $anio, $mes, NULL, TRUE, FALSE, TRUE, FALSE);
					}
					else {
						$no_borradas[] = array(
							'num_pro'    => $b['num_pro'],
							'nombre_pro' => $b['nombre_pro'],
							'num_rem'    => $b['num_rem']
						);
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

$tpl = new TemplatePower('plantillas/fac/FrutaAsociarFacturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
