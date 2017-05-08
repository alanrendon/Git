<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

if (!function_exists('json_encode')) {
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
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerCia':
			$condiciones = array();

			if ($_SESSION['iduser'] != 1) {
				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

			$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];

			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array(
					'num_cia'    => intval($_REQUEST['num_cia']),
					'nombre_cia' => utf8_encode($result[0]['nombre_cia']),
					'fecha'      => date('d/m/Y'),
					'recibos'    => array(
						array(
							'value' => '',
							'text'  => '',
						)
					)
				);

				$sql = '
					SELECT
						idreciborenta,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						EXTRACT(YEAR FROM fecha)
							AS anio,
						fecha,
						idarrendatario,
						arrendatario,
						alias_arrendatario
							AS nombre_arrendatario,
						recibos.total
							AS renta
					FROM
						rentas_recibos recibos
						LEFT JOIN rentas_arrendatarios arrendatarios
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores arrendadores
							USING (idarrendador)
					WHERE
						arrendador = ' . $_REQUEST['num_cia'] . '
						AND fecha >= \'2015/02/01\'
						AND recibos.tsbaja IS NULL
						AND bloque = 2
						AND idreciborenta NOT IN (
							SELECT
								idreciborenta
							FROM
								estado_cuenta
							WHERE
								cod_mov = 2
								AND idreciborenta IS NOT NULL
						)
					ORDER BY
						recibos.total DESC
				';

				$recibos = $db->query($sql);

				if ($recibos) {
					foreach ($recibos as $r) {
						$data['recibos'][] = array(
							'text'  => '[' . $_meses[$r['mes']] . ' ' . $r['anio'] . '] ' . $r['arrendatario'] . ' ' . utf8_encode($r['nombre_arrendatario']) . ' - ' . number_format($r['renta'], 2),
							'value' => json_encode(array(
								'idreciborenta'       => intval($r['idreciborenta']),
								'idarrendatario'      => intval($r['idarrendatario']),
								'nombre_arrendatario' => utf8_encode($r['nombre_arrendatario']),
								'fecha'               => $r['fecha'],
								'anio'                => intval($r['anio']),
								'mes'                 => $_meses[$r['mes']],
								'renta'               => floatval($r['renta'])
							))
						);
					}
				}

				echo json_encode($data);
			}
			else {
				echo json_encode(array(
					'num_cia' => -1
				));
			}
		break;

		case 'registrar':
			$sql = '';

			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				if ($num_cia > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['recibo'][$i] != ''
					&& get_val($_REQUEST['importe'][$i]) > 0) {
					$data = json_decode($_REQUEST['recibo'][$i]);

					$sql .= '
						INSERT INTO
							estado_cuenta
								(
									num_cia,
									fecha,
									cuenta,
									tipo_mov,
									cod_mov,
									importe,
									concepto,
									iduser,
									fecha_renta,
									idarrendatario,
									idreciborenta
								)
							VALUES
								(
									' . $num_cia . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									' . $_REQUEST['banco'] . ',
									FALSE,
									2,
									' . get_val($_REQUEST['importe'][$i]) . ',
									\'' . $data->mes . ' ' . $data->anio . ' ' . utf8_decode($data->nombre_arrendatario) . '\',
									' . $_SESSION['iduser'] . ',
									\'' . $data->fecha . '\',
									' . $data->idarrendatario . ',
									' . $data->idreciborenta . '
								);

						UPDATE
							saldos
						SET
							saldo_libros = saldo_libros + ' . $data->renta . '
						WHERE
							num_cia = ' . $num_cia . '
							AND cuenta = ' . $_REQUEST['banco'] . '
					' . ";\n";
				}
			}

			$db->query($sql);

			$sql = '
				SELECT
					MAX(timestamp)
						AS timestamp
				FROM
					estado_cuenta
				WHERE
					iduser = ' . $_SESSION['iduser'] . '
					AND cod_mov = 2
					AND timestamp >= NOW() - INTERVAL \'1 HOUR\'
			';

			$result = $db->query($sql);

			echo json_encode(array(
				'iduser' => intval($_SESSION['iduser']),
				'ts'     => $result[0]['timestamp']
			));
		break;

		case 'reporte':
			$sql = '
				SELECT
					num_cia,
					nombre
						AS nombre_cia,
					CASE
						WHEN cuenta = 1 THEN
							clabe_cuenta
						WHEN cuenta = 2 THEN
							clabe_cuenta2
					END
						AS cuenta,
					fecha,
					concepto,
					importe
				FROM
					estado_cuenta ec
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					iduser = ' . $_REQUEST['iduser'] . '
					AND timestamp = \'' . $_REQUEST['ts'] . '\'
				ORDER BY
					num_cia,
					idarrendatario,
					fecha_renta
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/RentasDepositosReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('reporte');

				$tpl->assign('fecha', date('d/m/Y'));

				$total = 0;

				foreach ($result as $rec) {
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('cuenta', $rec['cuenta'] != '' ? $rec['cuenta'] : '&nbsp;');
					$tpl->assign('concepto', utf8_encode($rec['concepto']));
					$tpl->assign('importe', number_format($rec['importe'], 2));

					$total += $rec['importe'];
				}

				$tpl->assign('reporte.total', number_format($total, 2));
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/RentasDepositos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
