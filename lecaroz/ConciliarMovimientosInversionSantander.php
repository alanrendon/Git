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

		case 'consultar':
			$sql = '
				SELECT
					fecha
				FROM
					mov_santander
				WHERE
					cod_banco IN (161, 666, 742, 247)
					AND fecha_con IS NULL
				GROUP BY
					fecha
				ORDER BY
					fecha
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/ConciliarMovimientosInversionSantanderConsultar.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('resultado');

				foreach ($result as $i => $row) {
					$tpl->newBlock('row');
					$tpl->assign('fecha', $row['fecha']);

					if ($i == 0) {
						$tpl->assign('checked', ' checked="checked"');
					} else {
						$tpl->assign('disabled', ' disabled="disabled"');
					}
				}
			} else {
				$tpl->newBlock('no_resultado');
			}

			echo $tpl->getOutputContent();

			break;

		case 'conciliar':
			// Obtener compañías que no se concilian
			$result = $db->query("SELECT
				num_cia
			FROM
				mov_santander
			WHERE
				fecha_con IS NULL
				AND cod_banco IN (161, 247)
				AND num_cia > 0
				AND fecha = '{$_REQUEST['fecha']}'
			GROUP BY
				num_cia
			HAVING
				COUNT(num_cia) > 1");

			$omitir = array();

			if ($result)
			{
				foreach ($result as $value)
				{
					$omitir[] = $value['num_cia'];
				}
			}

			$sql = '
				INSERT INTO
					estado_cuenta
						(
							num_cia,
							cuenta,
							fecha,
							tipo_mov,
							cod_mov,
							concepto,
							importe
						)
					SELECT
						num_cia,
						2,
						(
							SELECT
								fecha
							FROM
								mov_santander
							WHERE
								num_cia = ec.num_cia
								AND cod_banco IN (161, 247)
								AND fecha_con IS NULL
								AND fecha = \'' . $_REQUEST['fecha'] . '\'
						),
						FALSE,
						11,
						\'INTERESES POR INVERSION\',
						ROUND(((
							SELECT
								importe
							FROM
								mov_santander
							WHERE
								num_cia = ec.num_cia
								AND cod_banco IN (161, 247)
								AND fecha_con IS NULL
								AND fecha = \'' . $_REQUEST['fecha'] . '\'
						) - importe)::NUMERIC, 2)
					FROM
						estado_cuenta ec
					WHERE
						cuenta = 2
						AND fecha_con IS NULL
						AND cod_mov IN (60)
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . '
					ORDER BY
						num_cia;

				UPDATE
					estado_cuenta
				SET
					fecha_con = result.fecha,
					tipo_con = 8
				FROM (
					SELECT
						num_cia,
						fecha
					FROM
						mov_santander
					WHERE
						cod_banco IN (161, 247)
						AND fecha_con IS NULL
						AND fecha = \'' . $_REQUEST['fecha'] . '\'
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . '
					GROUP BY
						num_cia,
						fecha
				) result
				WHERE
					cuenta = 2
					AND fecha_con IS NULL
					AND cod_mov IN (11, 60)
					AND estado_cuenta.num_cia = result.num_cia;

				UPDATE
					saldos
				SET
					saldo_bancos = saldo_bancos + result.importe
				FROM (
					SELECT
						num_cia,
						SUM(importe)
							AS importe
					FROM
						mov_santander
					WHERE
						fecha_con IS NULL
						AND cod_banco IN (161, 247)
						AND num_cia > 0
						AND fecha = \'' . $_REQUEST['fecha'] . '\'
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . '
					GROUP BY
						num_cia
				)
					result
				WHERE
					cuenta = 2
					AND saldos.num_cia = result.num_cia;

				UPDATE
					mov_santander
				SET
					fecha_con = fecha,
					iduser = 1,
					timestamp = NOW(),
					cod_mov = 60
				WHERE
					fecha_con IS NULL
					AND cod_banco IN (161, 247)
					AND num_cia > 0
					AND fecha = \'' . $_REQUEST['fecha'] . '\'
					' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . ';

				INSERT INTO
					estado_cuenta
						(
							num_cia,
							cuenta,
							fecha,
							fecha_con,
							tipo_mov,
							cod_mov,
							concepto,
							importe,
							tipo_con
						)
					SELECT
						num_cia,
						2,
						fecha,
						fecha,
						TRUE,
						62,
						concepto,
						importe,
						8
					FROM
						mov_santander
					WHERE
						fecha_con IS NULL
						AND cod_banco IN (666, 742)
						AND num_cia > 0
						AND fecha = \'' . $_REQUEST['fecha'] . '\'
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . ';

				UPDATE
					saldos
				SET
					saldo_bancos = saldo_bancos - result.importe
				FROM (
					SELECT
						num_cia,
						SUM(importe)
							AS importe
					FROM
						mov_santander
					WHERE
						fecha_con IS NULL
						AND cod_banco IN (666, 742)
						AND num_cia > 0
						AND fecha = \'' . $_REQUEST['fecha'] . '\'
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . '
					GROUP BY
						num_cia
				)
					result
				WHERE
					cuenta = 2
					AND saldos.num_cia = result.num_cia;

				INSERT INTO
					estado_cuenta
						(
							num_cia,
							cuenta,
							fecha,
							tipo_mov,
							cod_mov,
							concepto,
							importe
						)
					SELECT
						num_cia,
						2,
						fecha,
						FALSE,
						60,
						\'' . utf8_decode($_REQUEST['concepto']) . '\',
						importe
					FROM
						mov_santander
					WHERE
						fecha_con IS NULL
						AND cod_banco IN (666, 742)
						AND num_cia > 0
						AND fecha = \'' . $_REQUEST['fecha'] . '\'
						' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . ';

				UPDATE
					mov_santander
				SET
					fecha_con = fecha,
					iduser = 1,
					timestamp = NOW(),
					cod_mov = 60
				WHERE
					fecha_con IS NULL
					AND cod_banco IN (666, 742)
					AND num_cia > 0
					AND fecha = \'' . $_REQUEST['fecha'] . '\'
					' . ($omitir ? 'AND num_cia NOT IN (' . implode(', ', $omitir) . ')'  : '') . ';
			';

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ConciliarMovimientosInversionSantander.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
