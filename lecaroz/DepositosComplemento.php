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

function toInt($value) {
	return intval($value, 10);
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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/DepositosComplementoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected');

			echo $tpl->getOutputContent();
		break;

		case 'buscar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$navidad = date('d/m/Y', mktime(0, 0, 0, 12, 25, $_REQUEST['anio']));

			$anio_nuevo = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = '(efe::INT & exp::INT & gas::INT & pro::INT & pas::INT)::BOOLEAN = TRUE';

			$condiciones[] = 'fecha NOT IN (\'' . $navidad . '\', \'' . $anio_nuevo . '\')';

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					EXTRACT(day FROM fecha)
						AS dia,
					efectivo,
					ROUND(COALESCE((
						SELECT
							SUM(importe)
						FROM
							estado_cuenta
						WHERE
							num_cia = efe.num_cia
							AND fecha = efe.fecha
							AND cod_mov IN (1, 16, 44, 99)
					), 0)::NUMERIC, 2)
						AS depositos,
					(efe::INT & exp::INT & gas::INT & pro::INT & pas::INT)::BOOLEAN
						AS status
				FROM
					total_panaderias efe
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha
			';

			$result = $db->query($sql);

			if ($result) {
				$sql = '
					SELECT
						num_cia,
						ROUND(COALESCE(AVG(depositos), 0)::NUMERIC, 2)
							AS promedio
					FROM
						(
							SELECT
								num_cia,
								EXTRACT(day FROM fecha)
									AS dia,
								ROUND(COALESCE((
									SELECT
										SUM(importe)
									FROM
										estado_cuenta
									WHERE
										num_cia = efe.num_cia
										AND fecha = efe.fecha
										AND cod_mov IN (1, 16, 44, 99)
								), 0)::NUMERIC, 2)
									AS depositos
							FROM
								total_panaderias efe
								LEFT JOIN catalogo_companias cc
									USING (num_cia)
							WHERE
								' . implode(' AND ', $condiciones) . '
							ORDER BY
								num_cia,
								dia
						) result
					WHERE
						depositos > 0
					GROUP BY
						num_cia
					ORDER BY
						num_cia
				';

				$tmp = $db->query($sql);

				$promedios = array();

				if ($tmp) {
					foreach ($tmp as $t) {
						$promedios[$t['num_cia']] = $t['promedio'];
					}
				}

				$efectivos = array();

				foreach ($result as $rec) {
					if ($rec['status'] == 't' && isset($promedios[$rec['num_cia']]) && $rec['depositos'] > 0 && $rec['depositos'] < $promedios[$rec['num_cia']] * 0.60) {
						$efectivos[] = $rec;
					}
				}

				if (count($efectivos) > 0) {
					$tpl = new TemplatePower('plantillas/ban/DepositosComplementoResultado.tpl');
					$tpl->prepare();

					$tpl->assign('anio', $_REQUEST['anio']);
					$tpl->assign('mes', $_meses[$_REQUEST['mes']]);

					$num_cia = NULL;

					foreach ($efectivos as $efe) {
						if ($num_cia != $efe['num_cia']) {
							$num_cia = $efe['num_cia'];

							$tpl->newBlock('cia');
							$tpl->assign('num_cia', $efe['num_cia']);
							$tpl->assign('nombre_cia', $efe['nombre_cia']);
							$tpl->assign('promedio', number_format($promedios[$num_cia], 2));

							$total_depositos = 0;
							$total_complementos = 0;
						}

						$tpl->newBlock('row');
						$tpl->assign('dia', $efe['dia']);
						$tpl->assign('efectivo', number_format($efe['efectivo'], 2));
						$tpl->assign('depositos', number_format($efe['depositos'], 2));

						$total_depositos += $efe['depositos'];

						$complemento = round($promedios[$num_cia] - $efe['depositos'], -2);

						$total_complementos += $complemento;

						$tpl->assign('complemento', number_format($complemento, 2));
						$tpl->assign('total', number_format($efe['depositos'] + $complemento, 2));

						$tpl->assign('cia.depositos', number_format($total_depositos, 2));
						$tpl->assign('cia.complementos', number_format($total_complementos, 2));
						$tpl->assign('cia.total', number_format($total_depositos + $total_complementos, 2));

						$tpl->assign('data', htmlentities(json_encode(array(
							'num_cia'     => intval($num_cia),
							'anio'        => intval($_REQUEST['anio']),
							'mes'         => intval($_REQUEST['mes']),
							'dia'         => intval(intval($efe['dia'])),
							'efectivo'    => floatval($efe['efectivo']),
							'depositos'   => floatval($efe['depositos']),
							'complemento' => floatval($complemento),
							'total'       => floatval($efe['depositos'] + $complemento)
						))));
					}

					echo $tpl->getOutputContent();
				}
			}
		break;

		case 'comprobante':
			$tpl = new TemplatePower('plantillas/ban/DepositosComplementoComprobante.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'validarComprobante':
			$sql = '
				SELECT
					comprobante
				FROM
					cometra
				WHERE
					comprobante = ' . $_REQUEST['comprobante'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo $_REQUEST['comprobante'];
			}
		break;

		case 'registrar':
			$sql = '';

			foreach ($_REQUEST['data'] as $data) {
				$d = json_decode($data);

				$sql .= '
					INSERT INTO
						cometra
							(
								comprobante,
								num_cia,
								fecha,
								tipo_comprobante,
								banco,
								cod_mov,
								concepto,
								importe,
								separar,
								total,
								iduser_ins,
								tsins
							)
						VALUES
							(
								' . $_REQUEST['comprobante'] . ',
								' . $d->num_cia . ',
								\'' . date('d/m/Y', mktime(0, 0, 0, $d->mes, $d->dia, $d->anio)) . '\',
								3,
								(
									SELECT
										banco
									FROM
										cometra
									WHERE
										tsend IS NULL
									LIMIT
										1
								),
								' . ($d->num_cia <= 300 ? 1 : 16) . ',
								\'COMPLEMENTO VENTA\',
								' . $d->complemento . ',
								0,
								' . $d->complemento . ',
								' . $_SESSION['iduser'] . ',
								NOW()
							)
				' . ";\n";
			}

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/DepositosComplemento.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
