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

function antiguedad($fecha, $fecha_baja = NULL) {
	list($dia_alta, $mes_alta, $anio_alta) = array_map('toInt', explode('/', $fecha));

	if ($fecha_baja != '')
	{
		list($dia_baja, $mes_baja, $anio_baja) = array_map('toInt', explode('/', $fecha_baja));
	}

	$alta = mktime(0, 0, 0, $mes_alta, $dia_alta, $anio_alta);
	$current = $fecha_baja != '' ? mktime(0, 0, 0, $mes_baja, $dia_baja, $anio_baja) : time();

	$dif = $current - $alta;

	return array(
		'anios' => $dif > 86400 ? date('Y', $dif) - 1970 : 0,
		'meses' => $dif > 86400 ? date('n', $dif) - 1 : 0,
		'dias'  => $dif > 86400 ? date('j', $dif) - 1 : 0
	);
}

function antiguedad_cadena($fecha, $formato = 'AMD', $abr = FALSE, $fecha_baja = NULL) {
	$antiguedad = antiguedad($fecha, $fecha_baja);

	$el = array();

	if (strpos($formato, 'A') !== FALSE && $antiguedad['anios'] > 0) {
		$el[] = $antiguedad['anios'] . ($abr ? ' A' : ' AÑO') . ($antiguedad['anios'] > 1 && !$abr ? 'S' : '');
	}

	if (strpos($formato, 'M') !== FALSE && $antiguedad['meses'] > 0) {
		$el[] = $antiguedad['meses'] . ($abr ? ' M' : ' MES') . ($antiguedad['meses'] > 1 && !$abr ? 'ES' : '');
	}

	if (strpos($formato, 'D') !== FALSE && $antiguedad['dias'] > 0) {
		$el[] = $antiguedad['dias'] . ($abr ? ' D' : ' DIA') . ($antiguedad['dias'] > 1 && !$abr ? 'S' : '');
	}

	if (!strpos($formato, 'D') && $antiguedad['anios'] == 0 && $antiguedad['meses'] == 0 && $antiguedad['dias'] >= 0) {
		$el[] = '< 1 ' . ($abr ? ' M' : ' MES');
	}

	return count($el) > 0 ? implode(' ', $el) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia BETWEEN ' . (!in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998') : '1 AND 10000') . '
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				$sql = '
					SELECT
						cod_puestos
							AS value,
						descripcion
							AS text
					FROM
						catalogo_puestos
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						orden,
						descripcion
				';

				$puestos = $db->query($sql);

				foreach ($puestos as &$p) {
					$p['text'] = utf8_encode($p['text']);
				}

				$sql = '
					SELECT
						cod_turno
							AS value,
						descripcion
							AS text
					FROM
						catalogo_turnos
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						orden_turno,
						descripcion
				';

				$turnos = $db->query($sql);

				foreach ($turnos as &$t) {
					$t['text'] = utf8_encode($t['text']);
				}

				$data = array(
					'nombre_cia' => utf8_encode($result[0]['nombre_corto']),
					'puestos'    => $puestos,
					'turnos'     => $turnos
				);

				echo json_encode($data);
			}
		break;

		case 'validarEdad':
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 25, 28, 34))) {
				list($dia_nac, $mes_nac, $anio_nac) = array_map('toInt', explode('/', $_REQUEST['fecha_nac']));
				list($dia_act, $mes_act, $anio_act) = explode('/', date('d/m/Y'));

				$edad = 0;

				if ($mes_nac > $mes_act) {
					$edad = $anio_act - $anio_nac - 1;
				}
				else if ($mes_nac == $mes_act && $dia_nac > $dia_act) {
					$edad = $anio_act - $anio_nac - 1;
				}
				else {
					$edad = $anio_act - $anio_nac;
				}

				if ($_REQUEST['num_afiliacion'] != '' && $edad < 18) {
					echo -1;
				}
				else {
					echo $edad;
				}
			}
		break;

		case 'validarListaNegra':
			$sql = '
				SELECT
					folio,
					\'[\' || nombre_tipo_baja || \'] \' || observaciones
						AS observaciones
				FROM
					lista_negra_trabajadores
					LEFT JOIN catalogo_tipos_baja
						USING (idtipobaja)
				WHERE
					/*nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'
					AND (
						ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
						OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
					)
					AND (
						ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
						OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
					)*/
					tsdel IS NULL
					AND nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\'
					AND ap_paterno = \'' . utf8_decode($_REQUEST['ap_paterno']) . '\'
					AND ap_materno = \'' . utf8_decode($_REQUEST['ap_materno']) . '\'
					AND permite_reingreso = FALSE
			';

			$result = $db->query($sql);

			if ($result) {
				$result[0]['observaciones'] = utf8_encode($result[0]['observaciones']);

				echo json_encode($result[0]);
			}
		break;

		case 'validarNombre':
			$sql = '
				SELECT
					num_emp,
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ap_paterno || (CASE WHEN ap_materno IS NOT NULL AND ap_materno <> \'\' THEN \' \' || ap_materno ELSE \'\' END) || \' \' || ct.nombre
						AS nombre_trabajador,
					ct.rfc,
					fecha_alta,
					COALESCE(auth.nombre, \'-\')
						AS usuario
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN auth
						ON (auth.iduser = idalta)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND (
						(
							/*ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'
							AND (
								ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
								OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
							)
							AND (
								ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
								OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
							)*/
							ct.nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\'
							AND ap_paterno = \'' . utf8_decode($_REQUEST['ap_paterno']) . '\'
							AND ap_materno = \'' . utf8_decode($_REQUEST['ap_materno']) . '\'
						)
						' . (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '' ? 'OR ct.rfc LIKE \'' . substr(utf8_decode($_REQUEST['rfc']), 0, 10) . '%\'' : '') . '
					)
					' . (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 ? 'AND ct.id <> ' . $_REQUEST['id'] : '') . '
				ORDER BY
					fecha_alta,
					num_cia
			';

			$result = $db->query($sql);

			if ($result) {
				foreach ($result as &$rec) {
					$rec['nombre_cia'] = utf8_encode($rec['nombre_cia']);
					$rec['nombre_trabajador'] = utf8_encode($rec['nombre_trabajador']);
					$rec['rfc'] = utf8_encode($rec['rfc']);
					$rec['usuario'] = utf8_encode($rec['usuario']);
				}

				$datos = array(
					'empleados' => $result,
					'admin'     => in_array($_SESSION['iduser'], array(1, 4))
				);

				echo json_encode($datos);
			}
		break;

		case 'info':
			$sql = '
				SELECT
					num_cia,
					cc.nombre
						AS nombre_cia,
					num_emp,
					nombre_completo
						AS nombre_trabajador,
					fecha_alta,
					fecha_alta_imss,
					fecha_baja,
					fecha_baja_imss,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0)
						AS aguinaldo,
					COALESCE((
						SELECT
							EXTRACT(year FROM fecha)
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0)
						AS anio
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					(ap_paterno, ap_materno, ct.nombre) IN (
						SELECT
							ap_paterno,
							ap_materno,
							nombre
						FROM
							catalogo_trabajadores
						WHERE
							id = ' . $_REQUEST['id'] . '
					)
				ORDER BY
					id DESC
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminInfo.tpl');
			$tpl->prepare();

			if ($result) {
				$row_color = FALSE;

				foreach ($result as $i => $rec) {
					if ($i == 0) {
						$tpl->assign('nombre_trabajador', utf8_encode($rec['nombre_trabajador']));
					}

					$tpl->newBlock('row');
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
					$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss'] != '' ? $rec['fecha_alta_imss'] : '&nbsp;');
					$tpl->assign('fecha_baja', $rec['fecha_baja'] != '' ? $rec['fecha_baja'] : '&nbsp;');
					$tpl->assign('fecha_baja_imss', $rec['fecha_baja_imss'] != '' ? $rec['fecha_baja_imss'] : '&nbsp;');
					$tpl->assign('anio', $rec['anio'] > 0 ? $rec['anio'] : '&nbsp;');
					$tpl->assign('aguinaldo', $rec['aguinaldo'] > 0 ? number_format($rec['aguinaldo'], 2) : '&nbsp;');
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'puesto':
			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'PUESTO: \' || (
							SELECT
								cod_puestos
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $_REQUEST['id'] . '
						) || \' -> \' || \'' . $_REQUEST['puesto'] . '\'
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					cod_puestos = ' . $_REQUEST['puesto'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$db->query($sql);
		break;

		case 'turno':
			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'TURNO: \' || (
							SELECT
								cod_turno
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $_REQUEST['id'] . '
						) || \' -> \' || \'' . $_REQUEST['turno'] . '\'
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					cod_turno = ' . $_REQUEST['turno'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);
		break;

		case 'aguinaldo':
			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'SOLO AGUINALDO: \' || (
							SELECT
								CASE
									WHEN solo_aguinaldo = TRUE THEN
										\'TRUE\'
									ELSE
										\'FALSE\'
								END
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $_REQUEST['id'] . '
						) || \' -> \' || \'' . $_REQUEST['status'] . '\'
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					solo_aguinaldo = ' . $_REQUEST['status'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$db->query($sql);
		break;

		case 'tipo':
			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'TIPO AGUINALDO: \' || (
							SELECT
								tipo
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $_REQUEST['id'] . '
						) || \' -> \' || \'' . $_REQUEST['tipo'] . '\'
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					tipo = ' . $_REQUEST['tipo'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$db->query($sql);
		break;

		case 'antiguedad':
			$sql = '
				SELECT
					nombre_completo
						AS nombre,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					fecha_alta,
					fecha_baja
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos
						USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos
						USING (cod_turno)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminAntiguedad.tpl');
			$tpl->prepare();

			$tpl->assign('i', $_REQUEST['i']);
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('nombre', utf8_encode($rec['nombre']));
			$tpl->assign('puesto', utf8_encode($rec['puesto']));
			$tpl->assign('turno', utf8_encode($rec['turno']));
			$tpl->assign('fecha_alta', $rec['fecha_alta']);

			$antiguedad = array(
				'anios' => 0,
				'meses' => 0,
				'dias'  => 0
			);

			if ($rec['fecha_alta'] != '') {
				$antiguedad = antiguedad($rec['fecha_alta'], $rec['fecha_baja']);
			}

			for ($i = 0; $i <= 50; $i++) {
				$tpl->newBlock('anio');
				$tpl->assign('value', $i);
				$tpl->assign('text', $i > 0 ? $i : '');

				if ($i == $antiguedad['anios']) {
					$tpl->assign('selected', ' selected');
				}
			}

			for ($i = 0; $i <= 12; $i++) {
				$tpl->newBlock('mes');
				$tpl->assign('value', $i);
				$tpl->assign('text', $i > 0 ? $i : '');

				if ($i == $antiguedad['meses']) {
					$tpl->assign('selected', ' selected');
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'actualizarAntiguedad':
			if ($_REQUEST['tipo_antiguedad'] == 'calculo') {
				$fecha = date('d/m/Y', mktime(0, 0, 0, date('n') - $_REQUEST['meses'], date('j') < 15 ? 1 : 15, date('Y') - $_REQUEST['anios']));
			}
			else if ($_REQUEST['tipo_antiguedad'] == 'fecha') {
				$fecha = $_REQUEST['fecha_alta'];
			}

			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'FECHA ALTA: \' || (
							SELECT
								fecha_alta
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $_REQUEST['id'] . '
						) || \' -> \' || \'' . $fecha . '\'
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					fecha_alta = \'' . $fecha . '\'
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$db->query($sql);

			echo json_encode(array(
				'i'          => intval($_REQUEST['i']),
				'antiguedad' => antiguedad_cadena($fecha, 'AM', TRUE)
			));
		break;

		case 'aguinaldoAnterior':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));

			$sql = '
				SELECT
					nombre_completo
						AS nombre,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha < \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0)
						AS importe
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos
						USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos
						USING (cod_turno)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminAguinaldoAnterior.tpl');
			$tpl->prepare();

			$tpl->assign('i', $_REQUEST['i']);
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('anio', $_REQUEST['anio']);
			$tpl->assign('nombre', utf8_encode($rec['nombre']));
			$tpl->assign('puesto', utf8_encode($rec['puesto']));
			$tpl->assign('turno', utf8_encode($rec['turno']));
			$tpl->assign('anio_ant', $_REQUEST['anio'] - 1);
			$tpl->assign('importe', $rec['importe'] > 0 ? number_format($rec['importe'], 2) : '');

			echo $tpl->getOutputContent();
		break;

		case 'actualizarAguinaldoAnterior':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 28, $_REQUEST['anio'] - 1));
			$anio = date('Y', mktime(0, 0, 0, 12, 28, $_REQUEST['anio'] - 1));

			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'AGUINALDO ANTERIOR [' . $anio . ']: \' || COALESCE((
							SELECT
								importe::VARCHAR
							FROM
								aguinaldos
							WHERE
								id_empleado = ' . $_REQUEST['id'] . '
								AND fecha = \'' . $fecha . '\'
						), \'\') || \' -> \' || \'' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . '\'
					)
			' . ";\n";

			if ($id = $db->query('
				SELECT
					id
				FROM
					aguinaldos
				WHERE
					id_empleado = ' . $_REQUEST['id'] . '
					AND fecha = \'' . $fecha . '\'
			')) {
				$sql .= '
					UPDATE
						aguinaldos
					SET
						importe = ' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . '
					WHERE
						id = ' . $id[0]['id'] . '
				' . ";\n";
			}
			else {
				$sql .= '
					INSERT INTO
						aguinaldos
							(
								id_empleado,
								fecha,
								importe,
								tipo
							)
						VALUES
							(
								' . $_REQUEST['id'] . ',
								\'' . $fecha . '\',
								' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . ',
								3
							)
				' . ";\n";
			}

			$db->query($sql);

			echo json_encode(array(
				'i'         => intval($_REQUEST['i']),
				'num_cia'   => intval($_REQUEST['num_cia']),
				'tipo'      => 'M',
				'importe' => isset($_REQUEST['importe'])? get_val($_REQUEST['importe']) : 0
			));
		break;

		case 'aguinaldoActual':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 28, $_REQUEST['anio']));

			$sql = '
				SELECT
					nombre_completo
						AS nombre,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha = \'' . $fecha . '\'
					), 0)
						AS importe
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos
						USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos
						USING (cod_turno)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminAguinaldoActual.tpl');
			$tpl->prepare();

			$tpl->assign('i', $_REQUEST['i']);
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('anio', $_REQUEST['anio']);
			$tpl->assign('nombre', utf8_encode($rec['nombre']));
			$tpl->assign('puesto', utf8_encode($rec['puesto']));
			$tpl->assign('turno', utf8_encode($rec['turno']));
			$tpl->assign('anio_ant', $_REQUEST['anio']);
			$tpl->assign('importe', $rec['importe'] > 0 ? number_format($rec['importe'], 2) : '');

			echo $tpl->getOutputContent();
		break;

		case 'actualizarAguinaldoActual':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 28, $_REQUEST['anio']));
			$anio = date('Y', mktime(0, 0, 0, 12, 28, $_REQUEST['anio']));

			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'MODIFICACION [ADMINISTRADOR]' . "\n" . 'AGUINALDO ACTUAL [' . $anio . ']: \' || COALESCE((
							SELECT
								importe::VARCHAR
							FROM
								aguinaldos
							WHERE
								id_empleado = ' . $_REQUEST['id'] . '
								AND fecha = \'' . $fecha . '\'
						), \'\') || \' -> \' || \'' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . '\'
					)
			' . ";\n";

			if ($id = $db->query('
				SELECT
					id
				FROM
					aguinaldos
				WHERE
					id_empleado = ' . $_REQUEST['id'] . '
					AND fecha = \'' . $fecha . '\'
			')) {
				$sql .= '
					UPDATE
						aguinaldos
					SET
						importe = ' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . ',
						tipo = 3
					WHERE
						id = ' . $id[0]['id'] . '
				' . ";\n";
			}
			else {
				$sql .= '
					INSERT INTO
						aguinaldos
							(
								id_empleado,
								fecha,
								importe,
								tipo
							)
						VALUES
							(
								' . $_REQUEST['id'] . ',
								\'' . $fecha . '\',
								' . (isset($_REQUEST['importe']) ? get_val($_REQUEST['importe']) : 0) . ',
								3
							)
				' . ";\n";
			}

			$db->query($sql);

			echo json_encode(array(
				'i'         => intval($_REQUEST['i']),
				'num_cia'   => intval($_REQUEST['num_cia']),
				'tipo'      => 'M',
				'importe' => isset($_REQUEST['importe'])? get_val($_REQUEST['importe']) : 0
			));
		break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminInicio.tpl');
			$tpl->prepare();

			$sql = '
				SELECT
					cod_puestos
						AS value,
					descripcion
						AS text,
					CASE
						WHEN giro = 1 THEN
							\'blue\'
						ELSE
							\'green\'
					END
						AS color
				FROM
					catalogo_puestos
				' . (!in_array($_SESSION['iduser'], array(1, 4)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
				ORDER BY
					giro,
					text
			';

			$puestos = $db->query($sql);

			if ($puestos) {
				foreach ($puestos as $p) {
					$tpl->newBlock('puesto');
					$tpl->assign('value', $p['value']);
					$tpl->assign('text', $p['text']);
					$tpl->assign('color', $p['color']);
				}
			}

			$sql = '
				SELECT
					cod_turno
						AS value,
					descripcion
						AS text,
					CASE
						WHEN giro = 1 THEN
							\'blue\'
						ELSE
							\'green\'
					END
						AS color
				FROM
					catalogo_turnos
				' . (!in_array($_SESSION['iduser'], array(1, 4, 46)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
				ORDER BY
					giro,
					text
			';

			$turnos = $db->query($sql);

			if ($turnos) {
				foreach ($turnos as $t) {
					$tpl->newBlock('turno');
					$tpl->assign('value', $t['value']);
					$tpl->assign('text', $t['text']);
					$tpl->assign('color', $t['color']);
				}
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 25, 28))) {
				$tpl->assign('_ROOT.disabled', ' disabled');
			}

			echo $tpl->getOutputContent();
		break;

		case 'listado':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 28, date('n') < 3 ? date('Y') - 1 : date('Y')));

			$condiciones = array();

			$condiciones[] = 'ct.fecha_baja IS NULL';

			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['trabajadores']) && trim($_REQUEST['trabajadores']) != '') {
				$trabajadores = array();

				$pieces = explode(',', $_REQUEST['trabajadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$trabajadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$trabajadores[] = $piece;
					}
				}

				if (count($trabajadores) > 0) {
					$condiciones[] = 'ct.num_emp IN (' . implode(', ', $trabajadores) . ')';
				}
			}

			if (isset($_REQUEST['nombre'])) {
				$condiciones[] = 'ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'';
			}

			if (isset($_REQUEST['ap_paterno'])) {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'';
			}

			if (isset($_REQUEST['ap_materno'])) {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'';
			}

			if (isset($_REQUEST['rfc'])) {
				$condiciones[] = 'ct.rfc LIKE \'%' . utf8_decode($_REQUEST['rfc']) . '%\'';
			}

			if (isset($_REQUEST['puesto']) && $_REQUEST['puesto'] > 0) {
				$condiciones[] = 'ct.cod_puestos = ' . $_REQUEST['puesto'];
			}

			if (isset($_REQUEST['turno']) && $_REQUEST['turno'] > 0) {
				$condiciones[] = 'ct.cod_turno = ' . $_REQUEST['turno'];
			}

			if (!isset($_REQUEST['aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = FALSE';
			}

			if (!isset($_REQUEST['no_aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = TRUE';
			}

			if (!isset($_REQUEST['afiliados'])) {
				$condiciones[] = '(num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\')';
			}

			if (!isset($_REQUEST['no_afiliados'])) {
				$condiciones[] = 'num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\'';
			}

			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre
						AS nombre_cia,
					ct.num_emp,
					CONCAT_WS(\' \', ap_paterno, ap_materno, ct.nombre)
						AS nombre_trabajador,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					ct.fecha_alta,
					ct.fecha_baja,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha < \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0)
						AS aguinaldo,
					(
						COALESCE(firma_contrato, FALSE)
						AND (
							(
								fecha_inicio_contrato IS NOT NULL
								AND fecha_termino_contrato IS NOT NULL
								AND NOW()::DATE < fecha_termino_contrato
							)
							OR (
								fecha_inicio_contrato IS NOT NULL
								AND fecha_termino_contrato IS NULL
							)
						)
					)
						AS contrato
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos
						USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos
						USING (cod_turno)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN auth
						ON (auth.iduser = ct.idalta)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					' . implode(', ', $_REQUEST['orden']) . '
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminReporte.tpl');
				$tpl->prepare();

			if ($result) {
				$filas_por_hoja = 27;

				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$tpl->newBlock('trabajadores');
							$tpl->assign('trabajadores', number_format($trabajadores));

							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />' . ($hojas % 2 != 0 ? '<br style="page-break-after:always;" />' : ''));
						}

						$num_cia = $rec['num_cia'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha', date('d/m/Y'));

						$filas = 0;
						$hojas = 1;
						$trabajadores = 0;
					}

					if ($filas == $filas_por_hoja) {
						$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');

						$tpl->newBlock('reporte');

						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('fecha', date('d/m/Y'));
						$tpl->assign('continuacion', '&nbsp;<span class="font6">(continuaci&oacute;n)</span>');

						$filas = 0;

						$hojas++;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre_trabajador', utf8_encode($rec['nombre_trabajador']));
					$tpl->assign('puesto', utf8_encode($rec['puesto']));
					$tpl->assign('turno', utf8_encode($rec['turno']));

					$antiguedad = array(
						'anios' => 0,
						'meses' => 0,
						'dias'  => 0
					);

					if ($rec['fecha_alta'] != '') {
						$antiguedad = antiguedad($rec['fecha_alta'], $rec['fecha_baja']);
					}

					$tpl->assign('antiguedad', /*($rec['fecha_alta'] == '' || $antiguedad['anios'] == 0 ? '*****' : '&nbsp;') . */($rec['contrato'] != 't' ? 'SC' : ''));
					$tpl->assign('aguinaldo', '&nbsp;');

					$filas++;

					$trabajadores++;
				}

				if ($num_cia != NULL) {
					$tpl->newBlock('trabajadores');
					$tpl->assign('trabajadores', number_format($trabajadores));
				}
			}

			$tpl->printToScreen();
		break;

		case 'repetidos':
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_emp,
					ct.nombre_completo
						AS nombre_trabajador,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					fecha_alta
						AS alta
				FROM
					catalogo_trabajadores ct
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
					LEFT JOIN
						catalogo_turnos turnos
							USING
								(cod_turno)
					LEFT JOIN
						catalogo_puestos puestos
							USING
								(cod_puestos)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND (ap_paterno, ap_materno, ct.nombre) IN (
						SELECT
							ap_paterno,
							ap_materno,
							nombre
						FROM
							catalogo_trabajadores
						WHERE
								id NOT IN (
									SELECT
										MIN(id)
									FROM
										catalogo_trabajadores
									WHERE
										fecha_baja IS NULL
										AND empleado_especial IS NULL
									GROUP BY
										ap_paterno,
										ap_materno,
										nombre
								)
							AND
								fecha_baja IS NULL
					)
				ORDER BY
					ap_paterno,
					ap_materno,
					ct.nombre,
					fecha_alta,
					num_cia
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminRepetidos.tpl');
				$tpl->prepare();

			if ($result) {
				$filas_por_hoja = 40;

				$filas = $filas_por_hoja;

				$hojas = 1;

				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($filas == $filas_por_hoja) {
						$tpl->newBlock('reporte');

						$tpl->assign('fecha', date('d/m/Y'));

						if ($hojas > 1) {
							$tpl->assign('continuacion', '&nbsp;<span class="font6">(continuaci&oacute;n)</span>');
						}

						$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');

						$filas = 0;

						$hojas++;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('num_emp', str_pad($rec['num_emp'], 5, '0', STR_PAD_LEFT));
					$tpl->assign('nombre_trabajador', utf8_encode($rec['nombre_trabajador']));
					$tpl->assign('puesto', utf8_encode($rec['puesto']));
					$tpl->assign('turno', utf8_encode($rec['turno']));
					$tpl->assign('alta', $rec['alta']);

					$filas++;
				}
			}

			$tpl->printToScreen();
		break;

		case 'similares':
			$sql = '
				SELECT
					set_limit(0.7);

				SELECT
					SIMILARITY(t1.nombre_completo, t2.nombre_completo)
						AS sim,
					t1.num_cia
						AS num_cia,
					(
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = t1.num_cia
					)
						AS nombre_cia,
					t1.num_emp,
					t1.nombre_completo
						AS nombre_trabajador,
					(
						SELECT
							descripcion
						FROM
							catalogo_puestos
						WHERE
							cod_puestos = t1.cod_puestos
					)
						AS puesto,
					(
						SELECT
							descripcion
						FROM
							catalogo_turnos
						WHERE
							cod_turno = t1.cod_turno
					)
						AS turno,
					t1.fecha_alta
						AS alta
				FROM
					catalogo_trabajadores t1
					JOIN catalogo_trabajadores t2
						ON t1.nombre_completo <> t2.nombre_completo
						AND t1.nombre_completo % t2.nombre_completo
						AND t1.fecha_baja IS NULL
						AND t2.fecha_baja IS NULL
				ORDER BY
					sim DESC,
					num_cia,
					nombre_trabajador
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminSimilares.tpl');
				$tpl->prepare();

			if ($result) {
				$filas_por_hoja = 63;

				$filas = $filas_por_hoja;

				$hojas = 1;

				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($filas == $filas_por_hoja) {
						$tpl->newBlock('reporte');

						$tpl->assign('fecha', date('d/m/Y'));

						if ($hojas > 1) {
							$tpl->assign('continuacion', '&nbsp;<span class="font6">(continuaci&oacute;n)</span>');
						}

						$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');

						$filas = 0;

						$hojas++;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('num_emp', str_pad($rec['num_emp'], 5, '0', STR_PAD_LEFT));
					$tpl->assign('nombre_trabajador', utf8_encode($rec['nombre_trabajador']));
					$tpl->assign('puesto', utf8_encode($rec['puesto']));
					$tpl->assign('turno', utf8_encode($rec['turno']));
					$tpl->assign('alta', $rec['alta']);

					$filas++;
				}
			}

			$tpl->printToScreen();
		break;

		case 'buscar':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 28, date('n') < 3 ? date('Y') - 1 : date('Y')));

			$condiciones = array();

			if (isset($_REQUEST['bajas']) && (!isset($_REQUEST['meses_baja']) || get_val($_REQUEST['meses_baja']) == 0)) {
				$meses_baja = 2;
			}
			else if (isset($_REQUEST['bajas']) && $_REQUEST['meses_baja'] > 0) {
				$meses_baja = $_REQUEST['meses_baja'];
			}

			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = 'ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['trabajadores']) && trim($_REQUEST['trabajadores']) != '') {
				$trabajadores = array();

				$pieces = explode(',', $_REQUEST['trabajadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$trabajadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$trabajadores[] = $piece;
					}
				}

				if (count($trabajadores) > 0) {
					$condiciones[] = 'ct.num_emp IN (' . implode(', ', $trabajadores) . ')';
				}
			}

			if (isset($_REQUEST['nombre'])) {
				$condiciones[] = 'ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'';
			}

			if (isset($_REQUEST['ap_paterno'])) {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'';
			}

			if (isset($_REQUEST['ap_materno'])) {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'';
			}

			if (isset($_REQUEST['rfc'])) {
				$condiciones[] = 'ct.rfc LIKE \'%' . utf8_decode($_REQUEST['rfc']) . '%\'';
			}

			if (isset($_REQUEST['puesto']) && $_REQUEST['puesto'] > 0) {
				$condiciones[] = 'ct.cod_puestos = ' . $_REQUEST['puesto'];
			}

			if (isset($_REQUEST['turno']) && $_REQUEST['turno'] > 0) {
				$condiciones[] = 'ct.cod_turno = ' . $_REQUEST['turno'];
			}

			if (!isset($_REQUEST['aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = FALSE';
			}

			if (!isset($_REQUEST['no_aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = TRUE';
			}

			if (!isset($_REQUEST['afiliados'])) {
				$condiciones[] = '(num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\')';
			}

			if (!isset($_REQUEST['no_afiliados'])) {
				$condiciones[] = 'num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\'';
			}

			if (isset($_REQUEST['bajas'])) {
				$condiciones[] = '(ct.fecha_baja IS NULL OR ct.fecha_baja > NOW() - INTERVAL \'' . $meses_baja . ($meses_baja > 1 ? ' MONTHS' : ' MONTH') . '\')';
			}
			else {
				$condiciones[] = 'ct.fecha_baja IS NULL';
			}

			if ( ! isset($_REQUEST['con_idempleado']))
			{
				$condiciones[] = 'ct.idempleado IS NULL';
			}
			if ( ! isset($_REQUEST['sin_idempleado']))
			{
				$condiciones[] = 'ct.idempleado IS NOT NULL';
			}

			if ( ! isset($_REQUEST['con_doc_acta_nacimiento']))
			{
				$condiciones[] = '(ct.doc_acta_nacimiento IS NULL OR ct.doc_acta_nacimiento = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_acta_nacimiento']))
			{
				$condiciones[] = 'ct.doc_acta_nacimiento = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_comprobante_domicilio']))
			{
				$condiciones[] = '(ct.doc_comprobante_domicilio IS NULL OR ct.doc_comprobante_domicilio = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_comprobante_domicilio']))
			{
				$condiciones[] = 'ct.doc_comprobante_domicilio = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_curp']))
			{
				$condiciones[] = '(ct.doc_curp IS NULL OR ct.doc_curp = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_curp']))
			{
				$condiciones[] = 'ct.doc_curp = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_ife']))
			{
				$condiciones[] = '(ct.doc_ife IS NULL OR ct.doc_ife = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_ife']))
			{
				$condiciones[] = 'ct.doc_ife = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_num_seguro_social']))
			{
				$condiciones[] = '(ct.doc_num_seguro_social IS NULL OR ct.doc_num_seguro_social = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_num_seguro_social']))
			{
				$condiciones[] = 'ct.doc_num_seguro_social = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_solicitud_trabajo']))
			{
				$condiciones[] = '(ct.doc_solicitud_trabajo IS NULL OR ct.doc_solicitud_trabajo = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_solicitud_trabajo']))
			{
				$condiciones[] = 'ct.doc_solicitud_trabajo = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_comprobante_estudios']))
			{
				$condiciones[] = '(ct.doc_comprobante_estudios IS NULL OR ct.doc_comprobante_estudios = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_comprobante_estudios']))
			{
				$condiciones[] = 'ct.doc_comprobante_estudios = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_referencias']))
			{
				$condiciones[] = '(ct.doc_referencias IS NULL OR ct.doc_referencias = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_referencias']))
			{
				$condiciones[] = 'ct.doc_referencias = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_no_antecedentes_penales']))
			{
				$condiciones[] = '(ct.doc_no_antecedentes_penales IS NULL OR ct.doc_no_antecedentes_penales = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_no_antecedentes_penales']))
			{
				$condiciones[] = 'ct.doc_no_antecedentes_penales = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_licencia_manejo']))
			{
				$condiciones[] = '(ct.doc_licencia_manejo IS NULL OR ct.doc_licencia_manejo = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_licencia_manejo']))
			{
				$condiciones[] = 'ct.doc_licencia_manejo = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_rfc']))
			{
				$condiciones[] = '(ct.doc_rfc IS NULL OR ct.doc_rfc = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_rfc']))
			{
				$condiciones[] = 'ct.doc_rfc = TRUE';
			}

			if ( ! isset($_REQUEST['con_doc_no_adeudo_infonavit']))
			{
				$condiciones[] = '(ct.doc_no_adeudo_infonavit IS NULL OR ct.doc_no_adeudo_infonavit = FALSE)';
			}
			if ( ! isset($_REQUEST['sin_doc_no_adeudo_infonavit']))
			{
				$condiciones[] = 'ct.doc_no_adeudo_infonavit = TRUE';
			}

			$sql = '
				SELECT
					ct.id,
					ct.num_emp,
					ct.num_cia,
					cc.nombre AS nombre_cia,
					ct.num_cia_emp,
					cce.nombre AS nombre_cia_emp,
					ct.nombre_completo AS nombre_trabajador,
					ct.rfc,
					ct.cod_puestos AS puesto,
					ct.cod_turno AS turno,
					ct.num_afiliacion,
					ct.fecha_alta,
					ct.fecha_baja,
					COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										importe
									ELSE
										-importe
								END
							)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
					), 0) AS saldo,
					NOW()::DATE - COALESCE((
						SELECT
							MAX(fecha)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
							AND tipo_mov = TRUE
					), (
						SELECT
							MAX(fecha)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
							AND tipo_mov = FALSE
					), NULL) AS ultimo_abono,
					COALESCE((
						SELECT
							importe
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
							AND tipo_mov = TRUE
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0) AS ultimo_abono_importe,
					ct.no_baja,
					ct.observaciones,
					ct.solo_aguinaldo,
					COALESCE(ct.tipo, 0) AS tipo,
					COALESCE((
						SELECT
							CASE
								WHEN tipo = 1 THEN
									\'P\'
								WHEN tipo = 2 THEN
									\'C\'
								WHEN tipo = 3 THEN
									\'M\'
								WHEN tipo = 4 THEN
									\'PP\'
								WHEN tipo = 5 THEN
									\'PI\'
								WHEN tipo = 6 THEN
									\'PPI\'
								ELSE
									\'D\'
							END
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha < \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), \'\') AS tipo_aguinaldo_anterior,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha < \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0) AS aguinaldo_anterior,
					COALESCE((
						SELECT
							CASE
								WHEN tipo = 1 THEN
									\'P\'
								WHEN tipo = 2 THEN
									\'C\'
								WHEN tipo = 3 THEN
									\'M\'
								WHEN tipo = 4 THEN
									\'PP\'
								WHEN tipo = 5 THEN
									\'PI\'
								WHEN tipo = 6 THEN
									\'PPI\'
								WHEN tipo = 7 THEN
									\'PC\'
								ELSE
									\'D\'
							END
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha = \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), \'\') AS tipo_aguinaldo_actual,
					COALESCE((
						SELECT
							importe
						FROM
							aguinaldos
						WHERE
							id_empleado = ct.id
							AND fecha = \'' . $fecha . '\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0) AS aguinaldo_actual,
					(
						COALESCE(ct.firma_contrato, FALSE)
						AND (
							(
								ct.fecha_inicio_contrato IS NOT NULL
								AND ct.fecha_termino_contrato IS NOT NULL
								AND NOW()::DATE < ct.fecha_termino_contrato
							)
							OR (
								ct.fecha_inicio_contrato IS NOT NULL
								AND ct.fecha_termino_contrato IS NULL
							)
						)
					) AS contrato,
					CASE
						WHEN ct.num_cia < 900 THEN
							COALESCE((
								SELECT
									TRUE
								FROM
									lista_negra_trabajadores
								WHERE
									tsdel IS NULL
									AND nombre = ct.nombre
									AND ap_paterno = ct.ap_paterno
									AND ap_materno = ct.ap_materno
								LIMIT
									1
							), FALSE)
						ELSE
							FALSE
					END AS lista_negra,
					CASE
						WHEN ct.empleado_especial IS NOT NULL THEN
							TRUE
						ELSE
							FALSE
					END AS empleado_especial,
					CASE
						WHEN ct.baja_rh IS NOT NULL THEN
							TRUE
						ELSE
							FALSE
					END AS baja_rh,
					auth.nombre AS usuario,
					ct.idempleado
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos USING (cod_turno)
					LEFT JOIN catalogo_companias cc USING (num_cia)
					LEFT JOIN catalogo_companias cce ON (cce.num_cia = ct.num_cia_emp)
					LEFT JOIN auth ON (auth.iduser = ct.idalta)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					' . implode(', ', $_REQUEST['orden']) . '
			';

			$result = $db->query($sql);

			if ($result) {
				// $huellas_db = new DBclass('mysqli://root:pobgnj@192.168.96.1:3306/checador', 'autocommit=yes');
				$huellas_db = new DBclass('mysqli://root:pobgnj@192.168.1.2:3306/checador', 'autocommit=yes');

				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminResultado.tpl');
				$tpl->prepare();

				$anio_ant = date('n') < 3 ? date('Y') - 2 : date('Y') - 1;
				$anio_act = date('n') < 3 ? date('Y') - 1 : date('Y');

				$sql = '
					SELECT
						giro,
						cod_puestos
							AS value,
						descripcion
							AS text
					FROM
						catalogo_puestos
					ORDER BY
						giro,
						orden,
						descripcion
				';

				$tmp = $db->query($sql);

				$puestos = array();
				if ($tmp) {
					foreach ($tmp as $t) {
						$puestos[$t['giro']][] = array(
							'value' => $t['value'],
							'text'  => utf8_encode($t['text'])
						);
					}
				}

				$sql = '
					SELECT
						giro,
						cod_turno
							AS value,
						descripcion
							AS text
					FROM
						catalogo_turnos
					ORDER BY
						giro,
						orden_turno
				';

				$tmp = $db->query($sql);

				$turnos = array();
				if ($tmp) {
					foreach ($tmp as $t) {
						$turnos[$t['giro']][] = array(
							'value' => $t['value'],
							'text'  => utf8_encode($t['text'])
						);
					}
				}

				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$tpl->assign('anio_ant', date('n') < 3 ? date('Y') - 2 : date('Y') - 1);
						$tpl->assign('anio_act', date('n') < 3 ? date('Y') - 1 : date('Y'));

						$row_color = FALSE;

						$aguinaldo_ant = 0;
						$aguinaldo_act = 0;

						$cont = 0;
					}

					$data = array(
						'id' => intval($rec['id'])
					);

					$data_aguinaldo = array(
						'id'      => intval($rec['id']),
						'num_cia' => intval($rec['num_cia']),
						'anio'    => $anio_act
					);

					// [2-Nov-2016] Buscar chequeos del empleado
					if ($rec['idempleado'] > 0)
					{
						$chequeos = $huellas_db->query("SELECT
							MAX(Fecha) AS fecha,
							MAX(Fecha) >= CURRENT_DATE() - INTERVAL 20 DAY AS status
						FROM
							tblchequeo
						WHERE
							IdEmpleado = {$rec['idempleado']}");
					}
					else
					{
						$chequeos = FALSE;
					}

					if ( ! isset($_REQUEST['con_chequeos']) || ! isset($_REQUEST['chequeos_atrasados']) || ! isset($_REQUEST['sin_chequeos']))
					{
						if ((isset($_REQUEST['con_chequeos']) || isset($_REQUEST['chequeos_atrasados'])) && ! isset($_REQUEST['sin_chequeos']))
						{
							if ( ! $chequeos)
							{
								continue;
							}

							if ($chequeos[0]['fecha'] == '' && (isset($_REQUEST['con_chequeos']) || isset($_REQUEST['chequeos_atrasados'])))
							{
								continue;
							}

							if ($chequeos[0]['fecha'] != '' && ( ! isset($_REQUEST['chequeos_atrasados']) && $chequeos[0]['status'] != 1))
							{
								continue;
							}

							if ($chequeos[0]['fecha'] != '' && ( ! isset($_REQUEST['con_chequeos']) && $chequeos[0]['status'] == 1))
							{
								continue;
							}
						}

						if ( ! isset($_REQUEST['con_chequeos']) && ! isset($_REQUEST['chequeos_atrasados']) && isset($_REQUEST['sin_chequeos']))
						{
							if ($chequeos && $chequeos[0]['fecha'] != '')
							{
								continue;
							}
						}
					}

					$tpl->newBlock('trabajador');

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					if ($chequeos && $chequeos[0]['fecha'] != '' && $chequeos[0]['status'] == 1)
					{
						$finger_img = 'fingerprint_green.png';
						$finger_info = "&Uacute;ltimo chequeo {$chequeos[0]['fecha']}";
					}
					else if ( ! $rec['idempleado'])
					{
						$finger_img = 'fingerprint_red.png';
						$finger_info = "No se ha ingresado el id del empleado";
					}
					else if ($chequeos[0]['fecha'] == '')
					{
						$finger_img = 'fingerprint_red.png';
						$finger_info = "El id del empleado es erroneo o nunca ha checado";
					}
					else
					{
						$finger_img = 'fingerprint_yellow.png';
						$finger_info = "El &uacute;ltimo chequeo del empleado fue el d&iacute;a {$chequeos[0]['fecha']}";
					}

					$tpl->assign('id', $rec['id']);
					$tpl->assign('data', htmlentities(json_encode($data)));
					$tpl->assign('data_aguinaldo', htmlentities(json_encode($data_aguinaldo)));
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('num_emp', $rec['num_emp'] . ($rec['idempleado'] > 0 ? "-{$rec['idempleado']}" : ''));
					$tpl->assign('no_firma', $rec['contrato'] == 'f' ? ' class="underline red"' : '');
					$tpl->assign('nombre_trabajador', ($rec['num_cia_emp'] != $rec['num_cia'] ? '<a id="labora" href="javascript:;" alt="' . $rec['num_cia_emp'] . ' ' . $rec['nombre_cia_emp'] . '">[' . $rec['num_cia_emp'] . ']</a>' : '') . '<img id="chequeo_' . $rec['id'] . '" src="/lecaroz/iconos/' . $finger_img . '" width="16" height="16" alt="' . $finger_info . '">' . ($rec['observaciones'] != '' ? '<img src="/lecaroz/iconos/info.png" alt="' . utf8_encode($rec['observaciones']) . '" name="observaciones" width="16" height="16" id="observaciones" title="Observaciones" style="float: right" />' : '') . ($rec['empleado_especial'] == 't' ? '<img src="/lecaroz/iconos/star.png" width="16" height="16" /> ' : '') . ($rec['baja_rh'] == 't' ? '<img src="/lecaroz/iconos/stop_round.png" width="16" height="16" /> ' : '') . utf8_encode($rec['nombre_trabajador']) . ($rec['observaciones'] != '' ? '&nbsp;' : ''));
					if ($rec['no_baja'] == 't') {
						$tpl->assign('trabajador_color', 'blue underline');
					}
					else if ($rec['fecha_baja'] != '') {
						$tpl->assign('trabajador_color', 'red underline');
					}
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('num_afiliacion', trim($rec['num_afiliacion']) != '' ? trim($rec['num_afiliacion']) : '&nbsp;');
					$tpl->assign('saldo', $rec['saldo'] != 0 ? '<span style="float:left;" class="font8 orange">(' . $rec['ultimo_abono'] . ' d&iacute;a(s)' . ($rec['ultimo_abono_importe'] > 0 ? ' $' . number_format($rec['ultimo_abono_importe'], 2) : '') . ')</span>&nbsp;$' . number_format($rec['saldo'], 2) : '&nbsp;');
					$tpl->assign('aguinaldo', $rec['solo_aguinaldo'] == 't' ? ' checked' : '');
					$tpl->assign('tipo_' . $rec['tipo'], ' selected');
					$tpl->assign('antiguedad', $rec['fecha_alta'] != '' ? antiguedad_cadena($rec['fecha_alta'], 'AM', TRUE, $rec['fecha_baja']) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

					$tpl->assign('aguinaldo_ant', $rec['aguinaldo_anterior'] != 0 ? '<span style="float:left;" class="orange">(' . $rec['tipo_aguinaldo_anterior'] . ')</span>&nbsp;' . number_format($rec['aguinaldo_anterior'], 2) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
					$tpl->assign('aguinaldo_act', $rec['aguinaldo_actual'] != 0 ? '<span style="float:left;" class="orange">(' . $rec['tipo_aguinaldo_actual'] . ')</span>&nbsp;' . number_format($rec['aguinaldo_actual'], 2) : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

					$tpl->assign('bloqueado', $rec['lista_negra'] == 't' ? 'TRUE' : 'FALSE');

					$tpl->assign('no_modificar', $rec['fecha_baja'] != '' ? '_gray' : '');

					$tpl->assign('no_baja', $rec['fecha_baja'] != '' || $rec['no_baja'] == 't'|| $rec['saldo'] != 0 ? '_gray' : '');

					$tpl->assign('no_reactivar', in_array($_SESSION['iduser'], array(1, 4, 19, 25, 28)) && $rec['fecha_baja'] != '' && $rec['lista_negra'] == 'f' ? '' : '_gray');

					foreach ($puestos[$num_cia < 900 ? 1 : 2] as $p) {
						$tpl->newBlock('puesto');
						$tpl->assign('value', $p['value']);
						$tpl->assign('text', $p['text']);

						if ($p['value'] == $rec['puesto']) {
							$tpl->assign('selected', ' selected');
						}
					}

					foreach ($turnos[$num_cia < 900 ? 1 : 2] as $t) {
						$tpl->newBlock('turno');
						$tpl->assign('value', $t['value']);
						$tpl->assign('text', $t['text']);

						if ($t['value'] == $rec['turno']) {
							$tpl->assign('selected', ' selected');
						}
					}

					$aguinaldo_ant += $rec['aguinaldo_anterior'];
					$aguinaldo_act += $rec['aguinaldo_actual'];
					$cont++;

					$tpl->assign('cia.aguinaldo_ant', number_format($aguinaldo_ant, 2));
					$tpl->assign('cia.aguinaldo_act', number_format($aguinaldo_act, 2));
					$tpl->assign('cia.emp', number_format($cont));
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminAlta.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', isset($_REQUEST['num_cia']) ? $_REQUEST['num_cia'] : '');

			$tpl->assign('fecha', date('d/m/Y'));

			echo $tpl->getOutputContent();
		break;

		case 'insertar':
			$sql = '
				SELECT
					num_emp
				FROM
					catalogo_trabajadores
				WHERE
					fecha_baja IS NULL
					AND num_cia BETWEEN ' . ($_REQUEST['num_cia'] >= 900 ? '900 AND 998' : '1 AND 899') . '
				ORDER BY
					num_emp
			';

			$numeros = $db->query($sql);

			$num_emp = 1;

			if ($numeros) {
				foreach ($numeros as $num) {
					if ($num_emp == $num['num_emp']) {
						$num_emp++;
					}
					else {
						break;
					}
				}
			}

			$sql = '
				UPDATE
					catalogo_trabajadores
				SET
					imp_alta = FALSE,
					ultimo = FALSE
				WHERE
					ultimo = TRUE
					AND imp_alta = TRUE
			' . ";\n";

			$sql .= '
				INSERT INTO
					catalogo_trabajadores
						(
							num_emp,
							num_cia,
							num_cia_emp,
							nombre,
							ap_paterno,
							ap_materno,
							rfc,
							fecha_nac,
							sexo,
							cod_puestos,
							cod_turno,
							fecha_alta_imss,
							no_baja,
							num_afiliacion,
							solo_aguinaldo,
							tipo,
							observaciones,
							empleado_especial,
							baja_rh,
							fecha_alta,
							imp_alta,
							pendiente_alta,
							idalta,
							tsalta
						)
					VALUES
						(
							' . $num_emp . ',
							' . $_REQUEST['num_cia'] . ',
							' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
							\'' . utf8_decode($_REQUEST['nombre']) . '\',
							\'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
							\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
							\'' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '') . '\',
							' . (isset($_REQUEST['fecha_nac']) ? '\'' . $_REQUEST['fecha_nac'] . '\'' : 'NULL') . ',
							' . $_REQUEST['sexo'] . ',
							' . $_REQUEST['cod_puestos'] . ',
							' . $_REQUEST['cod_turno'] . ',
							' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
							' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
							\'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
							' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
							' . $_REQUEST['tipo'] . ',
							\'' . (isset($_REQUEST['observaciones']) ? substr(utf8_decode($_REQUEST['observaciones']), 0, 1000) : '') . '\',
							' . (isset($_REQUEST['empleado_especial']) ? 'NOW()' : 'NULL') . ',
							' . (isset($_REQUEST['baja_rh']) ? 'NOW()' : 'NULL') . ',
							' . (isset($_REQUEST['fecha_alta']) ? '\'' . $_REQUEST['fecha_alta'] . '\'' : 'NOW()::DATE') . ',
							' . (isset($_REQUEST['num_afiliacion']) ? 'TRUE' : 'FALSE') . ',
							' . (isset($_REQUEST['num_afiliacion']) ? 'NOW()::DATE' : 'NULL') . ',
							' . $_SESSION['iduser'] . ',
							NOW()
						)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(\' \', ap_paterno, ap_materno, nombre), \'\s+\', \' \', \'g\'))
				WHERE
					id = (
						SELECT
							last_value
						FROM
							catalogo_trabajadores_id_seq
					)
			' . ";\n";

			$cambios[] = 'COMPAÑIA: ' . $_REQUEST['num_cia'];
			$cambios[] = 'LABORA EN: ' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']);
			$cambios[] = 'NOMBRE: ' . utf8_decode($_REQUEST['nombre']);
			$cambios[] = 'AP.PATERNO: ' . utf8_decode($_REQUEST['ap_paterno']);
			$cambios[] = 'AP.MATERNO: ' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '');
			$cambios[] = 'RFC: ' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '');
			$cambios[] = 'FECHA NACIMIENTO: ' . (isset($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '');
			$cambios[] = 'SEXO: ' . $_REQUEST['sexo'];
			$cambios[] = 'PUESTO: ' . $_REQUEST['cod_puestos'];
			$cambios[] = 'TURNO: ' . $_REQUEST['cod_turno'];
			$cambios[] = 'FECHA ALTA IMSS: ' . (isset($_REQUEST['fecha_alta_imss']) ? $_REQUEST['fecha_alta_imss'] : '');
			$cambios[] = 'PERMANENTE: ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE');
			$cambios[] = 'NO. AFILIACION IMSS: ' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '');
			$cambios[] = 'SOLO AGUINALDO: ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE');
			$cambios[] = 'TIPO AGUINALDO: ' . (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');
			$cambios[] = 'OBSERVACIONES: ' . (isset($_REQUEST['observaciones']) ? $_REQUEST['observaciones'] : '');

			$sql .= '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						(
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						),
						' . $_SESSION['iduser'] . ',
						\'ALTA DE TRABAJADOR [ADMINISTRADOR]:' . "\n" . implode("\n", $cambios) . '\'
					)
			' . ";\n";

			if (isset($_REQUEST['aguinaldo']) && get_val($_REQUEST['aguinaldo']) > 0) {
				$sql .= '
					INSERT INTO
						aguinaldos
							(
								importe,
								fecha,
								id_empleado,
								tipo
							)
						VALUES
							(
								' . get_val($_REQUEST['aguinaldo']) . ',
								(
									SELECT
										fecha
									FROM
										aguinaldos
									WHERE
										fecha < \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1)) . '\'
									ORDER BY
										fecha DESC
									LIMIT
										1
								),
								(
									SELECT
										last_value
									FROM
										catalogo_trabajadores_id_seq
								),
								3
							)
				' . ";\n";
			}

			$db->query($sql);

			$sql = '
				SELECT
					num_cia || \' \' || nombre_corto
						AS cia,
					num_emp || \' \' || nombre_completo
						AS trabajador
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					id = (
						SELECT
							last_value
						FROM
							catalogo_trabajadores_id_seq
					)
			';

			$emp = $db->query($sql);

			$emp[0]['cia'] = utf8_encode($emp[0]['cia']);
			$emp[0]['trabajador'] = utf8_encode($emp[0]['trabajador']);

			echo json_encode($emp[0]);
		break;

		case 'modificar':
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_cia_emp,
					cct.nombre_corto
						AS nombre_cia_emp,
					ct.nombre,
					ct.ap_paterno,
					ct.ap_materno,
					ct.rfc,
					ct.fecha_nac,
					COALESCE(ct.sexo, FALSE)
						AS sexo,
					ct.fecha_alta,
					ct.cod_puestos,
					ct.cod_turno,
					ct.fecha_alta_imss,
					ct.no_baja,
					ct.num_afiliacion,
					ct.solo_aguinaldo,
					ct.tipo,
					ct.observaciones,
					COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										importe
									ELSE
										-importe
								END
							)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
					), 0)
						AS saldo,
					CASE
						WHEN empleado_especial IS NOT NULL THEN
							TRUE
						ELSE
							FALSE
					END
						AS empleado_especial,
					CASE
						WHEN baja_rh IS NOT NULL THEN
							TRUE
						ELSE
							FALSE
					END
						AS baja_rh
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias cct
						ON (cct.num_cia = ct.num_cia_emp)
				WHERE
					ct.id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				$rec = $result[0];

				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdminModificar.tpl');
				$tpl->prepare();

				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('num_cia', $rec['num_cia']);
				$tpl->assign('readonly', $rec['saldo'] != 0 ? ' readonly' : '');
				$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
				$tpl->assign('num_cia_emp', $rec['num_cia_emp']);
				$tpl->assign('nombre_cia_emp', utf8_encode($rec['nombre_cia_emp']));
				$tpl->assign('nombre', utf8_encode($rec['nombre']));
				$tpl->assign('ap_paterno', utf8_encode($rec['ap_paterno']));
				$tpl->assign('ap_materno', utf8_encode($rec['ap_materno']));
				$tpl->assign('rfc', utf8_encode($rec['rfc']));
				$tpl->assign('fecha_nac', $rec['fecha_nac']);
				$tpl->assign('sexo_' . $rec['sexo'], ' checked');
				$tpl->assign('fecha_alta', $rec['fecha_alta']);
				$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss']);
				$tpl->assign('no_baja', $rec['no_baja'] == 't' ? ' checked' : '');
				$tpl->assign('num_afiliacion', $rec['num_afiliacion']);
				$tpl->assign('solo_aguinaldo', $rec['solo_aguinaldo'] == 't' ? ' checked' : '');
				$tpl->assign('tipo_' . $rec['tipo'], ' selected');
				$tpl->assign('observaciones', utf8_encode($rec['observaciones']));
				$tpl->assign('empleado_especial', $rec['empleado_especial'] == 't' ? ' checked' : '');
				$tpl->assign('baja_rh', $rec['baja_rh'] == 't' ? ' checked' : '');

				if ($rec['saldo'] != 0) {
					$tpl->newBlock('saldo');
					$tpl->assign('saldo', number_format($rec['saldo'], 2));
				}

				$sql = '
					SELECT
						cod_puestos
							AS value,

						descripcion
							AS text
					FROM
						catalogo_puestos
					WHERE
						giro = ' . ($rec['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';

				$puestos = $db->query($sql);

				foreach ($puestos as $puesto) {
					$tpl->newBlock('puesto');
					$tpl->assign('value', $puesto['value']);
					$tpl->assign('text', $puesto['text']);

					if ($puesto['value'] == $rec['cod_puestos']) {
						$tpl->assign('selected', ' selected');
					}
				}

				$sql = '
					SELECT
						cod_turno
							AS value,
						descripcion
							AS text
					FROM
						catalogo_turnos
					WHERE
						giro = ' . ($rec['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';

				$turnos = $db->query($sql);

				foreach ($turnos as $turno) {
					$tpl->newBlock('turno');
					$tpl->assign('value', $turno['value']);
					$tpl->assign('text', $turno['text']);

					if ($turno['value'] == $rec['cod_turno']) {
						$tpl->assign('selected', ' selected');
					}
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'actualizar':
			$sql = '
				SELECT
					num_emp,
					num_cia,
					num_cia_emp,
					nombre,
					ap_paterno,
					ap_materno,
					rfc,
					fecha_nac,
					CASE
						WHEN sexo = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS sexo,
					cod_puestos,
					cod_turno,
					fecha_alta_imss,
					CASE
						WHEN no_baja = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS no_baja,
					num_afiliacion,
					CASE
						WHEN solo_aguinaldo = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS solo_aguinaldo,
					tipo,
					observaciones,
					empleado_especial,
					baja_rh,
					fecha_alta,
					imp_alta,
					pendiente_alta,
					idalta,
					tsalta
				FROM
					catalogo_trabajadores
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$tmp = $db->query($sql);

			$old = $tmp[0];

			/*
			@ [07-Nov-2012] Validar los cambios hechos en los datos del trabajador
			*/

			$cambios = array();

			if ($old['num_cia'] != $_REQUEST['num_cia']) {
				$cambios[] = 'COMPAÑIA: ' . $old['num_cia'] . ' -> ' . $_REQUEST['num_cia'];
			}
			if ($old['num_cia_emp'] != (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia'])) {
				$cambios[] = 'LABORA EN: ' . $old['num_cia_emp'] . ' -> ' . (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']);
			}
			if (utf8_encode($old['nombre']) != (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '')) {
				$cambios[] = 'NOMBRE: ' . $old['nombre'] . ' -> ' . (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '');
			}
			if (utf8_encode($old['ap_paterno']) != (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '')) {
				$cambios[] = 'AP.PATERNO: ' . $old['ap_paterno'] . ' -> ' . (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '');
			}
			if (utf8_encode($old['ap_materno']) != (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '')) {
				$cambios[] = 'AP.MATERNO: ' . $old['ap_materno'] . ' -> ' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '');
			}
			if (utf8_encode($old['rfc']) != (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '')) {
				$cambios[] = 'RFC: ' . $old['rfc'] . ' -> ' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '');
			}
			if ($old['fecha_nac'] != (isset($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '')) {
				$cambios[] = 'FECHA NACIMIENTO: ' . $old['fecha_nac'] . ' -> ' . (isset($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '');
			}
			if ($old['sexo'] != (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '')) {
				$cambios[] = 'SEXO: ' . $old['sexo'] . ' -> ' . (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '');
			}
			if ($old['cod_puestos'] != (isset($_REQUEST['cod_puestos']) ? $_REQUEST['cod_puestos'] : '')) {
				$cambios[] = 'PUESTO: ' . $old['cod_puestos'] . ' -> ' . (isset($_REQUEST['cod_puestos']) ? $_REQUEST['cod_puestos'] : '');
			}
			if ($old['cod_turno'] != (isset($_REQUEST['cod_turno']) ? $_REQUEST['cod_turno'] : '')) {
				$cambios[] = 'TURNO: ' . $old['cod_turno'] . ' -> ' . (isset($_REQUEST['cod_turno']) ? $_REQUEST['cod_turno'] : '');
			}
			if ($old['fecha_alta_imss'] != (isset($_REQUEST['fecha_alta_imss']) ? $_REQUEST['fecha_alta_imss'] : '')) {
				$cambios[] = 'FECHA ALTA IMSS: ' . $old['fecha_alta_imss'] . ' -> ' . (isset($_REQUEST['fecha_alta_imss']) ? $_REQUEST['fecha_alta_imss'] : '');
			}
			if ((isset($_REQUEST['no_baja']) && $old['no_baja'] == 'FALSE')
				|| (!isset($_REQUEST['no_baja']) && $old['no_baja'] == 'TRUE')) {
				$cambios[] = 'NO BAJA: ' . $old['no_baja'] . ' -> ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE');
			}
			if ($old['num_afiliacion'] != (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '')) {
				$cambios[] = 'NO. AFILIACION IMSS: ' . $old['num_afiliacion'] . ' -> ' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '');
			}
			if ((isset($_REQUEST['solo_aguinaldo']) && $old['solo_aguinaldo'] == 'FALSE')
				|| (!isset($_REQUEST['solo_aguinaldo']) && $old['solo_aguinaldo'] == 'TRUE')) {
				$cambios[] = 'SOLO AGUINALDO: ' . $old['solo_aguinaldo'] . ' -> ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE');
			}
			if ($old['tipo'] != (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '')) {
				$cambios[] = 'TIPO AGUINALDO: ' . $old['tipo'] . ' -> ' . (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');
			}
			if (utf8_encode($old['observaciones']) != (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '')) {
				$cambios[] = 'OBSERVACIONES: ' . $old['observaciones'] . ' -> ' . (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '');
			}

			if ($_REQUEST['num_cia'] == $old['num_cia']) {
				$sql = '
					UPDATE
						catalogo_trabajadores
					SET
						num_cia_emp = ' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
						nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\',
						ap_paterno = \'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
						ap_materno = \'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
						rfc = \'' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '') . '\',
						fecha_nac = ' . (isset($_REQUEST['fecha_nac']) ? '\'' . $_REQUEST['fecha_nac'] . '\'' : 'NULL') . ',
						sexo = ' . $_REQUEST['sexo'] . ',
						cod_puestos = ' . $_REQUEST['cod_puestos'] . ',
						cod_turno = ' . $_REQUEST['cod_turno'] . ',
						fecha_alta_imss = ' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
						no_baja = ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
						num_afiliacion = \'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
						solo_aguinaldo = ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
						tipo = ' . $_REQUEST['tipo'] . ',
						observaciones = \'' . (isset($_REQUEST['observaciones']) ? utf8_decode(substr($_REQUEST['observaciones'], 0, 1000)) : '') . '\',
						empleado_especial = ' . (isset($_REQUEST['empleado_especial']) ? 'NOW()' : 'NULL') . ',
						baja_rh = ' . (isset($_REQUEST['baja_rh']) ? 'NOW()' : 'NULL') . ',
						fecha_alta = \'' . $_REQUEST['fecha_alta'] . '\',
						imp_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? 'TRUE' : 'FALSE') . ',
						pendiente_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? '\'' . date('d/m/Y') . '\'' : 'NULL') . ',
						idmod = ' . $_SESSION['iduser'] . ',
						tsmod = NOW()
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";

				$sql .= '
					UPDATE
						catalogo_trabajadores
					SET
						nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(\' \', ap_paterno, ap_materno, nombre), \'\s+\', \' \', \'g\'))
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";

				if (!!$cambios) {
					$sql .= '
						INSERT INTO
							catalogo_trabajadores_log (
								idemp,
								iduser,
								log_description
							) VALUES (
								' .  $_REQUEST['id'] . ',
								' . $_SESSION['iduser'] . ',
								\'MODIFICACION [ADMINISTRADOR]' . "\n" . implode("\n", $cambios) . '\'
							)
					' . ";\n";
				}
			}
			else {
				$sql = '
					UPDATE
						catalogo_trabajadores
					SET
						fecha_baja = NOW()::DATE,
						imp_baja = ' . ($old['num_afiliacion'] != '' ? 'TRUE' : 'FALSE') . ',
						pendiente_baja = ' . ($old['num_afiliacion'] != '' ? 'NOW()::DATE' : 'NULL') . ',
						idbaja = ' . $_SESSION['iduser'] . ',
						tsbaja = NOW()
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";

				$sql .= '
					INSERT INTO
						catalogo_trabajadores_log (
							idemp,
							iduser,
							log_description
						) VALUES (
							' .  $_REQUEST['id'] . ',
							' . $_SESSION['iduser'] . ',
							\'BAJA POR CAMBIO DE COMPAÑIA [ADMINISTRADOR]\'
						)
				' . ";\n";

				$sql .= '
					INSERT INTO
						catalogo_trabajadores
							(
								num_emp,
								num_cia,
								num_cia_emp,
								nombre,
								ap_paterno,
								ap_materno,
								rfc,
								fecha_nac,
								sexo,
								cod_puestos,
								cod_turno,
								fecha_alta_imss,
								no_baja,
								num_afiliacion,
								solo_aguinaldo,
								tipo,
								observaciones,
								empleado_especial,
								baja_rh,
								fecha_alta,
								imp_alta,
								pendiente_alta,
								idalta,
								tsalta
							)
						VALUES
							(
								' . $old['num_emp'] . ',
								' . $_REQUEST['num_cia'] . ',
								' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
								\'' . utf8_decode($_REQUEST['nombre']) . '\',
								\'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
								\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
								\'' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '') . '\',
								' . (isset($_REQUEST['fecha_nac']) ? '\'' . $_REQUEST['fecha_nac'] . '\'' : 'NULL') . ',
								' . $_REQUEST['sexo'] . ',
								' . $_REQUEST['cod_puestos'] . ',
								' . $_REQUEST['cod_turno'] . ',
								' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
								' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
								\'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
								' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
								' . $_REQUEST['tipo'] . ',
								\'' . (isset($_REQUEST['observaciones']) ? substr(utf8_decode($_REQUEST['observaciones']), 0, 1000) : '') . '\',
								' . (isset($_REQUEST['empleado_especial']) ? 'NOW()' : 'NULL') . ',
								' . (isset($_REQUEST['baja_rh']) ? 'NOW()' : 'NULL') . ',
								' . (isset($_REQUEST['fecha_alta']) ? '\'' . $_REQUEST['fecha_alta'] . '\'' : 'NOW()::DATE') . ',
								' . (isset($_REQUEST['num_afiliacion']) ? 'TRUE' : 'FALSE') . ',
								' . (isset($_REQUEST['num_afiliacion']) ? 'NOW()::DATE' : 'NULL') . ',
								' . $_SESSION['iduser'] . ',
								NOW()
							)
				' . ";\n";

				$sql .= '
					UPDATE
						catalogo_trabajadores
					SET
						nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(\' \', ap_paterno, ap_materno, nombre), \'\s+\', \' \', \'g\'))
					WHERE
						id = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
				' . ";\n";

				$sql .= '
					INSERT INTO
						catalogo_trabajadores_log (
							idemp,
							idempold,
							iduser,
							log_description
						) VALUES (
							(
								SELECT
									last_value
								FROM
									catalogo_trabajadores_id_seq
							),
							' . $_REQUEST['id'] . ',
							' . $_SESSION['iduser'] . ',
							\'ALTA POR CAMBIO DE COMPAÑIA [ID ANTERIOR: ' . $_REQUEST['id'] . "][ADMINISTRADOR]" . (!!$cambios ? "\n" .implode("\n", $cambios) : '') . '\'
						)
				' . ";\n";

				$sql .= '
					UPDATE
						aguinaldos
					SET
						id_empleado = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
					WHERE
						id_empleado = ' . $_REQUEST['id'] . '
				' . ";\n";

				$sql .= '
					UPDATE
						infonavit
					SET
						id_emp = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
					WHERE
						id_emp = ' . $_REQUEST['id'] . '
				' . ";\n";
			}

			$db->query($sql);
		break;

		case 'baja':
			$sql = '
				UPDATE
					catalogo_trabajadores
				SET
					fecha_baja = NOW()::DATE,
					imp_baja = CASE
						WHEN num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\' THEN
							TRUE
						ELSE
							FALSE
					END,
					pendiente_baja = CASE
						WHEN num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\' THEN
							NOW()::DATE
						ELSE
							NULL
					END,
					idbaja = ' . $_SESSION['iduser'] . ',
					tsbaja = NOW()
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$sql .= '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'BAJA DE SISTEMA [ADMINISTRADOR]\'
					)
			' . ";\n";

			$db->query($sql);
		break;

		case 'reactivar':
			$sql = '
				INSERT INTO
					catalogo_trabajadores_log (
						idemp,
						iduser,
						log_description
					) VALUES (
						' .  $_REQUEST['id'] . ',
						' . $_SESSION['iduser'] . ',
						\'REACTIVACION DE TRABAJADOR [ADMINISTRADOR]:' . "\n" . 'NUMERO DE EMPLEADO ANTERIOR: \' || (
							SELECT
								num_emp
							FROM
								catalogo_trabajadores
							WHERE
								id = ' .  $_REQUEST['id'] . '
						) || \'' . "\n" . 'NUMERO DE EMPLEADO NUEVO: \' || COALESCE((
							SELECT
								MAX(num_emp) + 1
							FROM
								catalogo_trabajadores
							WHERE
								num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						), 1)
					)
			' . ";\n";

			$sql .= '
				UPDATE
					catalogo_trabajadores
				SET
					num_emp = COALESCE((
						SELECT
							MAX(num_emp) + 1
						FROM
							catalogo_trabajadores
						WHERE
							num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
					), 1),
					fecha_baja = NULL,
					fecha_baja_imss = NULL,
					imp_baja = FALSE,
					pendiente_baja = NULL,
					idmod = ' . $_SESSION['iduser'] . ',
					tsmod = NOW(),
					idbaja = NULL,
					tsbaja = NULL
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAdmin.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
