<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

function trim_array_value($value) {
	return trim($value, "\r\n");
}

function split_string($string, $sizes) {
	$pieces = array();

	$position = 0;

	foreach ($sizes as $size) {
		if ($size > 0) {
			$pieces[] = substr($string, $position, $size);
		} else {
			$pieces[] = substr($string, $position);
		}

		$position += $size;
	}

	return $pieces;
}

function obtener_valores_banorte($file_rows) {
	global $db;

	$datos = array(
		'movimientos' => array(),
		'saldos'      => array()
	);

	$count = 0;

	foreach ($file_rows as $file_row) {
		switch (substr($file_row, 0, 2)) {

			case '11':
				$pieces = split_string($file_row, array(2, 11, 12, 10, 2, 2, 2, 2, 2, 2, 1, 14, 3, 1, 26, 3));

				$cuenta = $pieces[3];

				$sql = '
					SELECT
						num_cia
					FROM
						catalogo_companias
					WHERE
						clabe_cuenta LIKE \'%' . $cuenta . '\'
				';

				$query = $db->query($sql);

				$num_cia = $query ? $query[0]['num_cia'] : NULL;

				$fecha = date('d/m/Y', mktime(0, 0, 0, intval($pieces[5], 10), intval($pieces[6], 10), intval($pieces[4], 10)));

				break;

			case '22':
				$pieces = split_string($file_row, array(2, 4, 4, 2, 2, 2, 6, 5, 1, 12, 2, 10, 12, 16, 15));

				$datos['movimientos'][$count] = array(
					'num_cia'   => $num_cia,
					'cuenta'    => str_pad($cuenta, 11, '0', STR_PAD_LEFT),
					'banco'     => 1,
					'fecha'     => date('d/m/Y', mktime(0, 0, 0, intval($pieces[4], 10), intval($pieces[5], 10), intval($pieces[3], 10))),
					'tipo_mov'  => intval($pieces[8], 10) == 1 ? 'TRUE' : 'FALSE',
					'cod_banco' => intval($pieces[7], 10),
					'folio'     => intval($pieces[11], 10),
					'concepto'  => trim(preg_replace('/\s+/', ' ', $pieces[12] . $pieces[13])),
					'importe'   => floatval($pieces[9] . '.' . $pieces[10])
				);

				$count++;

				break;

			case '33':
				$pieces = split_string($file_row, array(2, 33, 5, 14, 5, 14, 1, 12, 2, 3, 4));

				$datos['saldos'][$num_cia] = array(
					'banco' => 1,
					'fecha' => $fecha,
					'saldo' => floatval($pieces[7] . '.' . $pieces[8]) * (intval($pieces[6]) == 1 ? -1 : 1)
				);

				break;

		}
	}

	return $datos;
}

function obtener_valores_santander($file_rows) {
	global $db;

	$datos = array(
		'movimientos' => array(),
		'saldos'      => array()
	);

	/*
	@ Obtener todas las compañías con cuenta de Santander válida.
	*/

	$cuentas = array();

	$sql = '
		SELECT
			num_cia,
			clabe_cuenta2
				AS cuenta
		FROM
			catalogo_companias
		WHERE
			clabe_cuenta2 IS NOT NULL
			AND TRIM(clabe_cuenta2) <> \'\'
			AND clabe_cuenta2 ~ \'^\d{11}$\'
		ORDER BY
			cuenta
	';

	$query = $db->query($sql);

	if ($query) {
		foreach ($query as $row) {
			$cuentas[$row['cuenta']] = $row['num_cia'];
		}
	}

	/*
	@ Recorrer filas y sus valores
	*/

	foreach ($file_rows as $file_row) {
		$pieces = split_string($file_row, array(11, 5, 2, 2, 4, 8, 4, 40, 1, 12, 2, 12, 2, 8, 0));

		$datos['movimientos'][] = array(
			'num_cia'   => isset($cuentas[$pieces[0]]) ? $cuentas[$pieces[0]] : NULL,
			'cuenta'    => $pieces[0],
			'banco'     => 2,
			'fecha'     => date('d/m/Y', mktime(0, 0, 0, intval($pieces[2], 10), intval($pieces[3], 10), intval($pieces[4], 10))),
			'tipo_mov'  => $pieces[8] == '-' ? 'TRUE' : 'FALSE',
			'cod_banco' => intval($pieces[6], 10),
			'folio'     => intval($pieces[13], 10),
			'concepto'  => trim(preg_replace('/\s+/', ' ', $pieces[7] . $pieces[14])),
			'importe'   => floatval($pieces[9] . '.' . $pieces[10])
		);

		if (isset($cuentas[$pieces[0]])) {
			$datos['saldos'][$cuentas[$pieces[0]]] = array(
				'banco' => 2,
				'fecha' => date('d/m/Y', mktime(0, 0, 0, intval($pieces[2], 10), intval($pieces[3], 10), intval($pieces[4], 10))),
				'saldo' => floatval($pieces[11] . '.' . $pieces[12])
			);
		}
	}

	return $datos;
}

function validar_hash($hash) {
	$sql = '
		SELECT
			hash,
			tsins
		FROM
			movimientos_bancarios
		WHERE
			hash = \'' . $hash . '\'
		LIMIT
			1
	';

	$result = $db->query($sql);

	return $result ? TRUE : FALSE;
}

function almacenar_movimientos_bancarios($datos, $hash) {
	global $db;

	$sql = '';

	foreach ($datos['movimientos'] as $mov) {
		$sql .= '
			INSERT INTO
				movimientos_bancarios (
					num_cia,
					cuenta,
					banco,
					fecha,
					tipo_mov,
					cod_banco,
					concepto,
					importe,
					idins,
					folio,
					hash
				) VALUES (
					' . $mov['num_cia'] . ',
					\'' . $mov['cuenta'] . '\',
					' . $mov['banco'] . ',
					\'' . $mov['fecha'] . '\',
					' . $mov['tipo_mov'] . ',
					' . $mov['cod_banco'] . ',
					\'' . $mov['concepto'] . '\',
					' . $mov['importe'] . ',
					' . $_SESSION['iduser'] . ',
					' . $mov['folio'] . ',
					\'' . $hash . '\'
				)
		' . ";\n";
	}

	foreach ($datos['saldos'] as $num_cia => $datos) {
		$sql .= '
			UPDATE
				movimientos_bancarios_saldos
			SET
				tsend = NOW(),
				idend = ' . $_SESSION['iduser'] . '
			WHERE
				num_cia = ' . $num_cia . '
				AND tsend IS NULL
		' . ";\n";

		$sql .= '
			INSERT INTO
				movimientos_bancarios_saldos (
					num_cia,
					banco,
					saldo,
					hash,
					idins
				) VALUES (
					' . $num_cia . ',
					' . $datos['banco'] . ',
					' . $datos['saldo'] . ',
					\'' . $hash . '\',
					' . $_SESSION['iduser'] . '
				)
		' . ";\n";
	}
}

function validar_codigos() {
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

			$tpl = new TemplatePower('plantillas/ban/ConciliacionBancariaAutomaticaInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'validar_diferencia_saldos':
			$sql = '
				SELECT
					num_cia,
					nombre_cia,
					cuenta,
					banco,
					COALESCE(saldo_sistema, 0)
						AS saldo_sistema,
					abonos_pendientes,
					cargos_pendientes,
					COALESCE(saldo_sistema, 0) + abonos_pendientes - cargos_pendientes
						AS saldo_total,
					saldo_banco,
					saldo_sistema + abonos_pendientes - cargos_pendientes - saldo_banco
						AS diferencia
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							clabe_cuenta
								AS cuenta,
							1
								AS banco,
							ROUND(COALESCE(saldo_bancos, 0)::NUMERIC, 2)
								AS saldo_sistema,
							ROUND(saldo::NUMERIC, 2)
								AS saldo_banco,
							COALESCE((
								SELECT
									ROUND(SUM(importe)::NUMERIC, 2)
								FROM
									mov_banorte
								WHERE
									num_cia = ss.num_cia
									AND fecha_con IS NULL
									AND tipo_mov = FALSE
							), 0)
								AS abonos_pendientes,
							COALESCE((
								SELECT
									ROUND(SUM(importe)::NUMERIC, 2)
								FROM
									mov_banorte
								WHERE
									num_cia = ss.num_cia
									AND fecha_con IS NULL
									AND tipo_mov = TRUE
							), 0)
								AS cargos_pendientes,
							CASE
								WHEN tsdif IS NOT NULL THEN
									now()::DATE - tsdif::DATE
								ELSE
									0
							END
								AS dias
						FROM
							saldos ss
							LEFT JOIN saldo_banorte
								USING (num_cia)
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							cuenta = 1

						UNION

						SELECT
							num_cia,
							nombre_corto,
							clabe_cuenta
								AS cuenta,
							2
								AS banco,
							ROUND(COALESCE(saldo_bancos, 2)::NUMERIC, 2)
								AS saldo_sistema,
							ROUND(saldo::NUMERIC, 2)
								AS saldo_banco,
							COALESCE((
								SELECT
									ROUND(SUM(importe)::NUMERIC, 2)
								FROM
									mov_santander
								WHERE
									num_cia = ss.num_cia
									AND fecha_con IS NULL
									AND tipo_mov = FALSE
							), 0)
								AS abonos_pendientes,
							COALESCE((
								SELECT
									ROUND(SUM(importe)::NUMERIC, 2)
								FROM
									mov_santander
								WHERE
									num_cia = ss.num_cia
									AND fecha_con IS NULL
									AND tipo_mov = TRUE
							), 0)
								AS cargos_pendientes,
							CASE
								WHEN tsdif IS NOT NULL THEN
									now()::DATE - tsdif::DATE
								ELSE
									0
							END
								AS dias
						FROM
							saldos ss
							LEFT JOIN saldo_santander
								USING (num_cia)
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							cuenta = 2
					) result
				WHERE
					saldo_sistema + abonos_pendientes - cargos_pendientes - saldo_banco <> 0
				ORDER BY
					banco,
					num_cia
			';

			$query = $db->query($sql);

			if ($query) {
				$tpl = new TemplatePower('plantillas/ban/ConciliacionBancariaAutomaticaDiferenciaSaldos.tpl');
				$tpl->prepare();

				$banco = NULL;

				foreach ($query as $row) {
					if ($banco != $row['banco']) {
						$banco = $row['banco'];

						$tpl->newBlock('banco');
						$tpl->assign('banco', $row['banco'] == 1 ? '<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> BANORTE' : '<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> SANTANDER');

						$saldo_sistema = 0;
						$abonos_pendientes = 0;
						$cargos_pendientes = 0;
						$saldo_total = 0;
						$saldo_banco = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('cuenta', $row['cuenta']);
					$tpl->assign('saldo_sistema', $row['saldo_sistema'] != 0 ? number_format($row['saldo_sistema'], 2) : '&nbsp;');
					$tpl->assign('abonos_pendientes', $row['abonos_pendientes'] != 0 ? number_format($row['abonos_pendientes'], 2) : '&nbsp;');
					$tpl->assign('cargos_pendientes', $row['cargos_pendientes'] != 0 ? number_format($row['cargos_pendientes'], 2) : '&nbsp;');
					$tpl->assign('saldo_total', $row['saldo_total'] != 0 ? number_format($row['saldo_total'], 2) : '&nbsp;');
					$tpl->assign('saldo_banco', $row['saldo_banco'] != 0 ? number_format($row['saldo_banco'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $row['diferencia'] != 0 ? '<span class="' . ($row['diferencia'] > 0 ? 'blue' : 'red') . '">' . number_format($row['diferencia'], 2) . '</span>' : '&nbsp;');

					$saldo_sistema += $row['saldo_sistema'];
					$abonos_pendientes = $row['abonos_pendientes'];
					$cargos_pendientes = $row['cargos_pendientes'];
					$saldo_total = $row['saldo_total'];
					$saldo_banco = $row['saldo_banco'];

					$tpl->assign('banco.saldo_sistema', $saldo_sistema != 0 ? number_format($saldo_sistema, 2) : '&nbsp;');
					$tpl->assign('banco.abonos_pendientes', $abonos_pendientes != 0 ? number_format($abonos_pendientes, 2) : '&nbsp;');
					$tpl->assign('banco.cargos_pendientes', $cargos_pendientes != 0 ? number_format($cargos_pendientes, 2) : '&nbsp;');
					$tpl->assign('banco.saldo_total', $saldo_total != 0 ? number_format($saldo_total, 2) : '&nbsp;');
					$tpl->assign('banco.saldo_banco', $saldo_banco != 0 ? number_format($saldo_banco, 2) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'procesar_archivo':

			$finfo = new finfo(FILEINFO_MIME_TYPE);

			/*
			@ Obtener el tipo de contenido del archivo
			*/

			$mime_type = $finfo->file($_FILES['archivo']['tmp_name']);

			/*
			@ Extraer el contenido del archivo y guardar las lineas en array
			*/

			if ($mime_type == 'application/x-gzip') {
				// Archivo de texto comprimido en formato GZIP
				$file_rows = array_map('trim_array_value', gzfile($_FILES['archivo']['tmp_name']));
			} else if ($mime_type == 'text/plain') {
				// Archivo de texto plano
				$file_rows = file($_FILES['archivo']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			} else {
				$status = json_encode(array(
					'status' => -1,
					'error'  => 'No es posible extraer el contenido del archivo.'
				));

				die;
			}

			/*
			@ Determinar el tipo de archivo de conciliación y obtener valores
			*/

			if (strlen($file_rows[0]) == 95 && substr($file_rows[0], 0, 2) == '11') {
				// BANORTE: lineas de 95 caracteres y el primer par de la primera línea es '11'

				$datos = obtener_valores_banorte($file_rows);

				$hash = md5(implode("\n", $file_rows));

				$status = json_encode(array(
					'status' => 1,
					'banco'  => 1,
					'hash'   => $hash
				));
			} else if (strlen($file_rows[0]) >= 153 && preg_match('/^\d{11}/', $file_rows[0])) {
				// SANTANDER: lineas de 154 caracteres y los primeros 11 caracteres de cada linea son dígitos que representan un número de cuenta

				$datos = obtener_valores_santander($file_rows);

				$hash = md5(implode("\n", $file_rows));

				$status = json_encode(array(
					'status' => 1,
					'banco'  => 2,
					'hash'   => $hash
				));
			} else {
				$status = json_encode(array(
					'status' => -2,
					'error'  => 'Archivo de conciliación no válido'
				));
			}

			/*
			@ Validar que el archivo no haya sido insertado con anterioridad
			*/

			if (validar_hash($hash)) {
				$status = json_encode(array(
					'status' => -3,
					'error'  => 'Archivo de conciliación ya fue cargado con anterioridad',
					'tsins'  => $result[0]['tsins']
				));
			} else {
				almacenar_movimientos_bancarios($datos, $hash);
			}



			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ConciliacionBancariaAutomatica.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
