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
		case 'obtenerArrendador':
			$sql = '
				SELECT
					idarrendador,
					nombre_arrendador
				FROM
					rentas_arrendadores
				WHERE
					arrendador = ' . $_REQUEST['arrendador'] . '
					AND tsdel IS NULL
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array(
					'idarrendador'  => intval($result[0]['idarrendador']),
					'arrendador'    => intval($_REQUEST['arrendador']),
					'nombre_arrendador'    => utf8_encode($result[0]['nombre_arrendador']),
					'anio'          => intval(date('Y')),
					'mes'           => intval(date('n')),
					'arrendatarios' => array(
						array(
							'value' => '',
							'text'  => '',
						)
					)
				);

				$sql = '
					SELECT
						idarrendatario,
						arrendatario,
						alias_arrendatario
							AS nombre_arrendatario,
						arrendatarios.total
							AS renta
					FROM
						rentas_arrendatarios arrendatarios
						LEFT JOIN rentas_arrendadores arrendadores
							USING (idarrendador)
					WHERE
						arrendadores.arrendador = ' . $_REQUEST['arrendador'] . '
						AND arrendatarios.tsbaja IS NULL
					ORDER BY
						nombre_arrendatario
				';

				$recibos = $db->query($sql);

				if ($recibos) {
					foreach ($recibos as $r) {
						$data['arrendatarios'][] = array(
							'text'  => $r['arrendatario'] . ' ' . utf8_encode($r['nombre_arrendatario']) . ' - ' . number_format($r['renta'], 2),
							'value' => intval($r['idarrendatario'])
						);
					}
				}

				echo json_encode($data);
			}
			else {
				echo json_encode(array(
					'arrendador' => -1
				));
			}
		break;

		case 'registrar':
			$sql = '';

			foreach ($_REQUEST['idarrendatario'] as $i => $idarrendatario) {
				if ($idarrendatario > 0
					&& $_REQUEST['anio'][$i] > 0
					&& $_REQUEST['mes'][$i] > 0) {
					$sql .= '
						DELETE FROM
							estatus_locales
						WHERE
							idarrendatario = ' . $idarrendatario . '
							AND anio = ' . $_REQUEST['anio'][$i] . '
							AND mes = ' . $_REQUEST['mes'][$i] . ';

						INSERT INTO
							estatus_locales
								(
									idarrendatario,
									anio,
									mes,
									fecha,
									status,
									iduser,
									observaciones
								)
							VALUES
								(
									' . $idarrendatario . ',
									' . $_REQUEST['anio'][$i] . ',
									' . $_REQUEST['mes'][$i] . ',
									\'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'][$i], 1, $_REQUEST['anio'][$i])) . '\',
									' . $_REQUEST['status'][$i] . ',
									' . $_SESSION['iduser'] . ',
									\'' . $_REQUEST['observaciones'][$i] . '\'
								);
					' . "\n";
				}
			}

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasEstados.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
