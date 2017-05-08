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

//if ($_SESSION['iduser'] != 1) {
//	die('<div style="font-size:16pt; border:solid 2px #000; padding:30px 10px;">ESTOY HACIENDO MODIFICACIONES AL PROGRAMA, NO ME LLAMEN PARA PREGUNTAR CUANDO QUEDARA, YO LES AVISO.</div>');
//}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia BETWEEN ' . (!in_array($_SESSION['iduser'], array(1, 4, 46)) ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998') : '1 AND 10000') . '
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
						descripcion
				';

				$turnos = $db->query($sql);

				foreach ($turnos as &$t) {
					$t['text'] = utf8_encode($t['text']);
				}

				$sql = '
					SELECT
						cod_horario
							AS value,
						descripcion
							AS text
					FROM
						catalogo_horarios
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						cod_horario
				';

				$horarios = $db->query($sql);

				foreach ($horarios as &$h) {
					$h['text'] = utf8_encode($h['text']);
				}

				$data = array(
					'nombre_cia' => utf8_encode($result[0]['nombre_corto']),
					'puestos'    => $puestos,
					'turnos'     => $turnos,
					'horarios'   => $horarios
				);

				echo json_encode($data);
			}
		break;

		case 'validarEdad':
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 28, 34))) {
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

				if ($_REQUEST['num_afiliacion'] != '' && $edad < 16) {
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
					AND baja_rh IS NULL
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

			if ($result && is_array($result)) {
				foreach ($result as &$rec) {
					$rec['nombre_cia'] = utf8_encode($rec['nombre_cia']);
					$rec['nombre_trabajador'] = utf8_encode($rec['nombre_trabajador']);
					$rec['rfc'] = utf8_encode($rec['rfc']);
					$rec['usuario'] = utf8_encode($rec['usuario']);
				}

				echo json_encode($result);
			}
		break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaInicio.tpl');
			$tpl->prepare();

			$sql = "
				SELECT
					idadministrador
						AS value,
					nombre_administrador
						AS text
				FROM
					catalogo_administradores
				ORDER BY
					text
			";

			$admins = $db->query($sql);

			if ($admins) {
				foreach ($admins as $a) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

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
				' . (!in_array($_SESSION['iduser'], array(1, 4, 46, 2)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
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
				' . (!in_array($_SESSION['iduser'], array(1, 4, 46, 2)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
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

			echo $tpl->getOutputContent();
		break;

		case 'buscar':
			$condiciones = array();
			$condiciones2 = array();

			$condiciones2[] = 'ct.num_cia <> ct.num_cia_emp';

			if (isset($_REQUEST['bajas']) && (!isset($_REQUEST['meses_baja']) || get_val($_REQUEST['meses_baja']) == 0)) {
				$meses_baja = 2;
			}
			else if (isset($_REQUEST['bajas']) && $_REQUEST['meses_baja'] > 0) {
				$meses_baja = $_REQUEST['meses_baja'];
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 46, 2))) {
				$condiciones[] = 'ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
				$condiciones2[] = 'ct.num_cia_emp BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones2[] = 'ct.num_cia_emp IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
				$condiciones2[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
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
					$condiciones2[] = 'ct.num_emp IN (' . implode(', ', $trabajadores) . ')';
				}
			}

			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'';
				$condiciones2[] = 'ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'';
			}

			if (isset($_REQUEST['ap_paterno']) && $_REQUEST['ap_paterno'] != '') {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'';
				$condiciones2[] = 'ct.ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'';
			}

			if (isset($_REQUEST['ap_materno']) && $_REQUEST['ap_materno'] != '') {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'';
				$condiciones2[] = 'ct.ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'';
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '') {
				$condiciones[] = 'ct.rfc LIKE \'%' . utf8_decode($_REQUEST['rfc']) . '%\'';
				$condiciones2[] = 'ct.rfc LIKE \'%' . utf8_decode($_REQUEST['rfc']) . '%\'';
			}

			if (isset($_REQUEST['puesto']) && $_REQUEST['puesto'] > 0) {
				$condiciones[] = 'cod_puestos = ' . $_REQUEST['puesto'];
				$condiciones2[] = 'cod_puestos = ' . $_REQUEST['puesto'];
			}

			if (isset($_REQUEST['turno']) && $_REQUEST['turno'] > 0) {
				$condiciones[] = 'cod_turno = ' . $_REQUEST['turno'];
				$condiciones2[] = 'cod_turno = ' . $_REQUEST['turno'];
			}

			if (!isset($_REQUEST['aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = FALSE';
				$condiciones2[] = 'solo_aguinaldo = FALSE';
			}

			if (!isset($_REQUEST['no_aguinaldo'])) {
				$condiciones[] = 'solo_aguinaldo = TRUE';
				$condiciones2[] = 'solo_aguinaldo = TRUE';
			}

			if (!isset($_REQUEST['afiliados'])) {
				$condiciones[] = '(num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\')';
				$condiciones2[] = '(num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\')';
			}

			if (!isset($_REQUEST['no_afiliados'])) {
				$condiciones[] = 'num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\'';
				$condiciones2[] = 'num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\'';
			}

			if (isset($_REQUEST['bajas'])) {
				$condiciones[] = '(ct.fecha_baja IS NULL OR pendiente_baja IS NOT NULL OR ct.fecha_baja > NOW() - INTERVAL \'' . $meses_baja . ($meses_baja > 1 ? ' MONTHS' : ' MONTH') . '\')';
				$condiciones2[] = '(ct.fecha_baja IS NULL OR pendiente_baja IS NOT NULL OR ct.fecha_baja > NOW() - INTERVAL \'' . $meses_baja . ($meses_baja > 1 ? ' MONTHS' : ' MONTH') . '\')';
			}
			else {
				$condiciones[] = '(ct.fecha_baja IS NULL OR pendiente_baja IS NOT NULL)';
				$condiciones2[] = '(ct.fecha_baja IS NULL OR pendiente_baja IS NOT NULL)';
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 5, 8, 14, 25, 26, 28, 34, 46, 63))) {
				$condiciones[] = 'empleado_especial IS NULL';
				$condiciones[] = 'baja_rh IS NULL';
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

			if ( ! (isset($_REQUEST['cias']) && $_REQUEST['cias'] != '')
				&& ! (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
				&& ! (isset($_REQUEST['trabajadores']) && $_REQUEST['trabajadores'] != '')
				&& ! (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '')
				&& ! (isset($_REQUEST['ap_paterno']) && $_REQUEST['ap_paterno'] != '')
				&& ! (isset($_REQUEST['ap_materno']) && $_REQUEST['ap_materno'] != '')
				&& ! (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
				&& ! (isset($_REQUEST['puesto']) && $_REQUEST['puesto'] > 0)
				&& ! (isset($_REQUEST['turno']) && $_REQUEST['turno'] > 0)) {
				$orden = '
					ORDER BY
						id
					LIMIT
						20
				';
			}
			else {
				$orden = '
					ORDER BY
						num_cia,
						labora,
						nombre_trabajador
				';
			}

			$sql = "SELECT
				1 AS labora, ct.id,
				ct.num_emp,
				ct.num_cia,
				cc.nombre AS nombre_cia,
				ct.num_cia_emp,
				cce.nombre AS nombre_cia_emp,
				ct.nombre_completo AS nombre_trabajador,
				ct.rfc,
				puestos.descripcion AS puesto,
				turnos.descripcion AS turno,
				num_afiliacion,
				fecha_alta,
				fecha_baja,
				fecha_alta_imss,
				fecha_baja_imss,
				pendiente_alta,
				pendiente_baja,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = FALSE THEN
								importe
							ELSE
								-importe
						END)
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
				CASE
					WHEN pendiente_alta IS NOT NULL THEN
						'PENDIENTE DE ALTA DESDE ' || pendiente_alta
					WHEN pendiente_baja IS NOT NULL THEN
						'PENDIENTE DE BAJA DESDE ' || pendiente_baja
					WHEN fecha_baja IS NULL THEN
						'LABORANDO'
					ELSE
						'BAJA DESDE ' || fecha_baja
				END AS status,
				CASE
					WHEN pendiente_alta IS NOT NULL THEN
						'blue'
					WHEN pendiente_baja IS NOT NULL THEN
						'red'
					WHEN no_baja = TRUE THEN
						'blue'
					WHEN fecha_baja IS NULL THEN
						'green'
					ELSE
						'red'
				END AS status_color,
				no_baja,
				observaciones,
				auth.nombre AS usuario,
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
						OR (
							fecha_inicio_contrato IS NULL
							AND fecha_termino_contrato IS NULL
						)
					)
				) AS contrato,
				empleado_especial,
				COALESCE((
					SELECT
						TRUE
					FROM
						documentos_trabajadores
					WHERE
						idempleado = ct.id
					LIMIT
						1
				), FALSE) AS con_documentos,
				doc_acta_nacimiento AS an,
				doc_comprobante_domicilio AS cd,
				doc_curp AS cu,
				doc_ife AS if,
				doc_num_seguro_social AS ss,
				doc_solicitud_trabajo AS st,
				doc_comprobante_estudios AS ce,
				doc_referencias AS rl,
				doc_no_antecedentes_penales AS na,
				doc_licencia_manejo AS lm,
				doc_rfc AS rf,
				doc_no_adeudo_infonavit AS in,
				idempleado
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_puestos puestos USING (cod_puestos)
				LEFT JOIN catalogo_turnos turnos USING (cod_turno)
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_companias cce ON (cce.num_cia = ct.num_cia_emp)
				LEFT JOIN auth ON (auth.iduser = ct.idalta)
			WHERE
				" . implode(' AND ', $condiciones) . "

			UNION

			SELECT
				2 AS labora,
				ct.id,
				ct.num_emp,
				ct.num_cia_emp AS num_cia,
				cc.nombre AS nombre_cia,
				ct.num_cia AS num_cia_emp,
				cce.nombre AS nombre_cia_emp,
				ct.nombre_completo AS nombre_trabajador,
				ct.rfc,
				puestos.descripcion AS puesto,
				turnos.descripcion AS turno,
				num_afiliacion,
				fecha_alta,
				fecha_baja,
				fecha_alta_imss,
				fecha_baja_imss,
				pendiente_alta,
				pendiente_baja,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = FALSE THEN
								importe
							ELSE
								-importe
						END)
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
				CASE
					WHEN pendiente_alta IS NOT NULL THEN
						'PENDIENTE DE ALTA DESDE ' || pendiente_alta
					WHEN pendiente_baja IS NOT NULL THEN
						'PENDIENTE DE BAJA DESDE ' || pendiente_baja
					WHEN fecha_baja IS NULL THEN
						'LABORANDO'
					ELSE
						'BAJA DESDE ' || fecha_baja
				END AS status,
				CASE
					WHEN pendiente_alta IS NOT NULL THEN
						'blue'
					WHEN pendiente_baja IS NOT NULL THEN
						'red'
					WHEN no_baja = TRUE THEN
						'blue'
					WHEN fecha_baja IS NULL THEN
						'green'
					ELSE
						'red'
				END AS status_color,
				no_baja,
				'LABORA EN ' || ct.num_cia || ' ' || (
					SELECT
						nombre_corto
					FROM
						catalogo_companias
					WHERE
						num_cia = ct.num_cia
				) AS observaciones,
				auth.nombre
					AS usuario,
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
				) AS contrato,
				empleado_especial,
				COALESCE((
					SELECT
						TRUE
					FROM
						documentos_trabajadores
					WHERE
						idempleado = ct.id
					LIMIT
						1
				), FALSE) AS con_documentos,
				doc_acta_nacimiento AS an,
				doc_comprobante_domicilio AS cd,
				doc_curp AS cu,
				doc_ife AS if,
				doc_num_seguro_social AS ss,
				doc_solicitud_trabajo AS st,
				doc_comprobante_estudios AS ce,
				doc_referencias AS rl,
				doc_no_antecedentes_penales AS na,
				doc_licencia_manejo AS lm,
				doc_rfc AS rf,
				doc_no_adeudo_infonavit AS in,
				idempleado
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_puestos puestos USING (cod_puestos)
				LEFT JOIN catalogo_turnos turnos USING (cod_turno)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = ct.num_cia_emp)
				LEFT JOIN catalogo_companias cce ON (cce.num_cia = ct.num_cia)
				LEFT JOIN auth ON (auth.iduser = ct.idalta)
			WHERE
				" . implode(' AND ', $condiciones2) . "

			{$orden}";

			$result = $db->query($sql);

			if ($result) {
				// $huellas_db = new DBclass('mysqli://root:pobgnj@192.168.96.1:3306/checador', 'autocommit=yes');
				$huellas_db = new DBclass('mysqli://root:pobgnj@192.168.1.2:3306/checador', 'autocommit=yes');

				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaResultado.tpl');
				$tpl->prepare();

				$tpl->assign('alta_disabled', !in_array($_SESSION['iduser'], array(1, 4, 5, 25, 28, 34, 8, 14, 63)) ? ' disabled' : '');

				$num_cia = NULL;
				foreach ($result as $num => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$tpl->assign('no_alta', !in_array($_SESSION['iduser'], array(1, 4, 5, 25, 28, 34, 8, 14, 63)) ? '_gray' : '');

						$row_color = FALSE;

						$cont = 0;
						$afiliados = 0;
					}

					// [6-Nov-2016] Buscar chequeos del empleado
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

					$tpl->assign('num', $rec['labora'] == 1 ? $num + 1 : '&nbsp;');
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_emp', $rec['num_emp'] . ($rec['idempleado'] > 0 ? "-{$rec['idempleado']}" : ''));
					$tpl->assign('no_firma', $rec['contrato'] == 'f' && $rec['labora'] != 2 ? ' class="underline red"' : ($rec['labora'] == 2 ? ' class="underline green"' : ''));
					$tpl->assign('nombre_trabajador', ($rec['num_cia_emp'] != $rec['num_cia'] ? '<a id="labora" href="javascript:;" alt="' . $rec['num_cia_emp'] . ' ' . $rec['nombre_cia_emp'] . '">[' . $rec['num_cia_emp'] . ']</a>' : '') . '<img id="chequeo_' . $rec['id'] . '" src="/lecaroz/iconos/' . $finger_img . '" width="16" height="16" alt="' . $finger_info . '">' . ($rec['observaciones'] != '' || $rec['labora'] == 2 ? '<img src="/lecaroz/iconos/info.png" alt="' . utf8_encode($rec['observaciones']) . '" name="observaciones" width="16" height="16" id="observaciones" title="Observaciones" style="float:right;" />' : '') . ($rec['empleado_especial'] == 't' ? '<img src="/lecaroz/iconos/star.png" width="16" height="16" /> ' : '') . utf8_encode($rec['nombre_trabajador']) . ($rec['observaciones'] != '' ? '&nbsp;' : ''));
					$tpl->assign('trabajador_color', $rec['no_baja'] == 't' && $rec['labora'] != 2 ? 'blue underline' : ($rec['labora'] == 2 ? 'green underline' : ''));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('puesto', utf8_encode($rec['puesto']));
					$tpl->assign('turno', utf8_encode($rec['turno']));
					$tpl->assign('num_afiliacion', trim($rec['num_afiliacion']) != '' ? trim($rec['num_afiliacion']) : '&nbsp;');
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
					$tpl->assign('saldo', $rec['saldo'] != 0 ? '<span style="float:left;" class="orange">(' . $rec['ultimo_abono'] . ' d&iacute;a(s))</span>&nbsp;' . number_format($rec['saldo'], 2) : '&nbsp;');
					$tpl->assign('status', $rec['status']);
					$tpl->assign('status_color', $rec['status_color']);

					$tpl->assign('no_modificar', $rec['fecha_baja'] != '' || !in_array($_SESSION['iduser'], array(1, 4, 5, 25, 28, 34, 8, 14, 53, 63)) || $rec['empleado_especial'] == 't' ? '_gray' : '');

					$tpl->assign('no_baja', $rec['fecha_baja'] != '' || $rec['no_baja'] == 't'|| $rec['saldo'] != 0 || !in_array($_SESSION['iduser'], array(1, 4, 5, 25, 28, 34, 8, 14, 53, 63)) || $rec['empleado_especial'] == 't' ? '_gray' : '');

					$tpl->assign('no_pension', trim($rec['num_afiliacion']) == '' || $rec['fecha_baja'] != '' || $rec['fecha_baja_imss'] != '' || $rec['no_baja'] == 't' || $rec['saldo'] != 0 || !in_array($_SESSION['iduser'], array(1, 4, 5, 25, 28, 34, 8, 14)) || $rec['empleado_especial'] == 't' ? '_gray' : '');

					$tpl->assign('no_reactivar', in_array($_SESSION['iduser'], array(1, 4, 19, 25, 28)) && $rec['fecha_baja'] != '' ? '' : '_gray');

					$tpl->assign('con_documentos', $rec['con_documentos'] == 't' ? '_text' : '');

					$documentos_entregados = array();

					if ($rec['an'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace green" data-tooltip="Acta de nacimiento">AN</a>';
					}
					if ($rec['cd'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace blue" data-tooltip="Comprobante de domicilio">CD</a>';
					}
					if ($rec['if'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace yellow" data-tooltip="Credencial del IFE">IF</a>';
					}
					if ($rec['rf'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace red" data-tooltip="RFC">RF</a>';
					}
					if ($rec['cu'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace purple" data-tooltip="CURP">CU</a>';
					}
					if ($rec['ss'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace orange" data-tooltip="N&uacute;mero de seguro social">SS</a>';
					}
					if ($rec['st'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace black" data-tooltip="Solicitud de trabajo">ST</a>';
					}
					if ($rec['ce'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace aqua" data-tooltip="Comprobante de estudios">CE</a>';
					}
					if ($rec['rl'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace light_gray" data-tooltip="Referencias laborales">RL</a>';
					}
					if ($rec['na'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace dark_gray" data-tooltip="Carta de no antecedentes penales">NA</a>';
					}
					if ($rec['lm'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace green" data-tooltip="Licencia de manejo">LM</a>';
					}
					if ($rec['in'] == 't') {
						$documentos_entregados[] = '<a href="javascript:void(0);" id="documentos_entregados" class="enlace blue" data-tooltip="Carta de no adeudo a Infonavit">IN</a>';
					}

					$tpl->assign('documentos', $documentos_entregados ? implode(', ', $documentos_entregados) : '&nbsp;');

					$cont += $rec['fecha_baja'] == '' && $rec['labora'] == 1 ? 1 : 0;

					$afiliados += trim($rec['num_afiliacion']) != '' && $rec['fecha_baja_imss'] == '' && $rec['pendiente_baja'] == '' && $rec['labora'] == 1 ? 1 : 0;

					$tpl->assign('cia.numero_trabajadores', $cont);
					$tpl->assign('cia.afiliados', $afiliados);
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaAlta.tpl');
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
							curp,
							fecha_nac,
							lugar_nac,
							sexo,
							calle,
							colonia,
							del_mun,
							entidad,
							cod_postal,
							telefono_casa,
							telefono_movil,
							email,
							cod_puestos,
							cod_turno,
							cod_horario,
							salario,
							salario_integrado,
							fecha_alta_imss,
							no_baja,
							num_afiliacion,
							credito_infonavit,
							no_infonavit,
							solo_aguinaldo,
							tipo,
							fecha_vencimiento_licencia_manejo,
							observaciones,
							uniforme,
							talla,
							control_bata,
							deposito_bata,
							fecha_alta,
							imp_alta,
							pendiente_alta,
							idalta,
							tsalta,
							idempleado
						)
					VALUES
						(
							' . $num_emp . ',
							' . $_REQUEST['num_cia'] . ',
							' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
							\'' . utf8_decode($_REQUEST['nombre']) . '\',
							\'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
							\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							\'' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . '\',
							\'' . $_REQUEST['fecha_nac'] . '\',
							\'' . (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '') . '\',
							' . $_REQUEST['sexo'] . ',
							\'' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . '\',
							\'' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . '\',
							\'' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '') . '\',
							\'' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '') . '\',
							\'' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '') . '\',
							\'' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '') . '\',
							\'' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '') . '\',
							\'' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . '\',
							' . $_REQUEST['cod_puestos'] . ',
							' . $_REQUEST['cod_turno'] . ',
							' . $_REQUEST['cod_horario'] . ',
							' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ',
							' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0) . ',
							' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
							' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
							\'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
							' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE') . ',
							\'' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '') . '\',
							' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
							' . $_REQUEST['tipo'] . ',
							' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? '\'' . $_REQUEST['fecha_vencimiento_licencia_manejo'] . '\'' : 'NULL') . ',
							\'' . (isset($_REQUEST['observaciones']) ? substr(utf8_decode($_REQUEST['observaciones']), 0, 1000) : '') . '\',
							' . (isset($_REQUEST['uniforme']) ? '\'' . $_REQUEST['uniforme'] . '\'' : 'NULL') . ',
							' . (isset($_REQUEST['talla']) ? $_REQUEST['talla'] : 'NULL') . ',
							' . (isset($_REQUEST['control_bata']) ? 'TRUE' : 'FALSE') . ',
							' . (isset($_REQUEST['deposito_bata']) ? get_val($_REQUEST['deposito_bata']) : 0) . ',
							' . (isset($_REQUEST['fecha_alta']) ? '\'' . $_REQUEST['fecha_alta'] . '\'' : 'NOW()::DATE') . ',
							' . (isset($_REQUEST['num_afiliacion']) ? 'TRUE' : 'FALSE') . ',
							' . (isset($_REQUEST['num_afiliacion']) ? 'NOW()::DATE' : 'NULL') . ',
							' . $_SESSION['iduser'] . ',
							NOW(),
							' . (isset($_REQUEST['idempleado']) && $_REQUEST['idempleado'] > 0 ? $_REQUEST['idempleado'] : 'NULL') . '
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
			$cambios[] = 'CURP: ' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '');
			$cambios[] = 'FECHA NACIMIENTO: ' . $_REQUEST['fecha_nac'];
			$cambios[] = 'LUGAR NACIMIENTO: ' . (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '');
			$cambios[] = 'SEXO: ' . $_REQUEST['sexo'];
			$cambios[] = 'CALLE: ' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '');
			$cambios[] = 'COLONIA: ' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '');
			$cambios[] = 'DELEGACION/MUNICIPIO: ' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '');
			$cambios[] = 'ENTIDAD: ' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '');
			$cambios[] = 'CODIGO POSTAL: ' . (isset($_REQUEST['cod_postal']) ? utf8_decode($_REQUEST['cod_postal']) : '');
			$cambios[] = 'TELEFONO CASA: ' . (isset($_REQUEST['telefono_casa']) ? utf8_decode($_REQUEST['telefono_casa']) : '');
			$cambios[] = 'TELEFONO MOVIL: ' . (isset($_REQUEST['telefono_movil']) ? utf8_decode($_REQUEST['telefono_movil']) : '');
			$cambios[] = 'EMAIL: ' . (isset($_REQUEST['email']) ? utf8_decode($_REQUEST['email']) : '');
			$cambios[] = 'PUESTO: ' . $_REQUEST['cod_puestos'];
			$cambios[] = 'TURNO: ' . $_REQUEST['cod_turno'];
			$cambios[] = 'HORARIO: ' . $_REQUEST['cod_horario'];
			$cambios[] = 'SALARIO DIARIO: ' . (isset($_REQUEST['salario']) ? $_REQUEST['salario'] : '');
			$cambios[] = 'SALARIO DIARIO INTEGRADO: ' . (isset($_REQUEST['salario_integrado']) ? $_REQUEST['salario_integrado'] : '');
			$cambios[] = 'FECHA ALTA IMSS: ' . (isset($_REQUEST['fecha_alta_imss']) ? $_REQUEST['fecha_alta_imss'] : '');
			$cambios[] = 'PERMANENTE: ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE');
			$cambios[] = 'NO. AFILIACION IMSS: ' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '');
			$cambios[] = 'CREDITO INFONAVIT: ' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE');
			$cambios[] = 'NO. CREDITO INFONAVIT: ' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '');
			$cambios[] = 'SOLO AGUINALDO: ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE');
			$cambios[] = 'TIPO AGUINALDO: ' . (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');
			$cambios[] = 'FECHA VENCIMIENTO LICENCIA MANEJO: ' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '');
			$cambios[] = 'OBSERVACIONES: ' . (isset($_REQUEST['observaciones']) ? $_REQUEST['observaciones'] : '');
			$cambios[] = 'ID EMPLEADO CHECADOR: ' . (isset($_REQUEST['idempleado']) ? $_REQUEST['idempleado'] : '');

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
						\'ALTA DE TRABAJADOR [OFICINA]:' . "\n" . implode("\n", $cambios) . '\'
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
			' . ";\n";

			$emp = $db->query($sql);

			$emp[0]['cia'] = utf8_encode($emp[0]['cia']);
			$emp[0]['trabajador'] = utf8_encode($emp[0]['trabajador']);

			echo json_encode($emp[0]);
		break;

		case 'info':
			$sql = "SELECT
				ct.num_cia,
				cc.nombre_corto AS nombre_cia,
				ct.num_cia_emp,
				cct.nombre_corto AS nombre_cia_emp,
				ct.nombre,
				ct.ap_paterno,
				ct.ap_materno,
				ct.rfc,
				ct.curp,
				ct.fecha_nac,
				ct.lugar_nac,
				CASE
					WHEN ct.sexo = TRUE THEN
						'MUJER'
					ELSE
						'HOMBRE'
				END AS sexo,
				ct.calle,
				ct.colonia,
				ct.del_mun,
				ct.entidad,
				ct.cod_postal,
				ct.telefono_casa,
				ct.telefono_movil,
				ct.email,
				ct.fecha_alta,
				DATE_TRUNC('second', ct.ts_elaboracion_contrato) AS fecha_elaboracion_contrato,
				puestos.descripcion AS puesto,
				turnos.descripcion AS turno,
				horarios.descripcion AS horario,
				ct.salario,
				ct.salario_integrado,
				ct.fecha_alta_imss,
				CASE
					WHEN ct.no_baja = TRUE THEN
						'SI'
					ELSE
						'NO'
				END AS no_baja,
				ct.num_afiliacion,
				CASE
					WHEN ct.credito_infonavit = TRUE THEN
						'SI'
					ELSE
						'NO'
				END AS credito_infonavit,
				ct.no_infonavit,
				CASE
					WHEN ct.solo_aguinaldo = TRUE THEN
						'SI'
					ELSE
						'NO'
				END AS solo_aguinaldo,
				CASE
					WHEN ct.tipo = 0 THEN
						'NORMAL'
					WHEN ct.tipo = 1 THEN
						'A 1 A&Ntilde;O'
					WHEN ct.tipo = 2 THEN
						'A 3 MESES'
				END AS tipo_aguinaldo,
				ct.fecha_vencimiento_licencia_manejo,
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
				), 0) AS saldo
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_companias cct ON (cct.num_cia = ct.num_cia_emp)
				LEFT JOIN catalogo_puestos puestos USING (cod_puestos)
				LEFT JOIN catalogo_horarios horarios USING (cod_horario)
				LEFT JOIN catalogo_turnos turnos USING (cod_turno)
			WHERE
				ct.id = {$_REQUEST['id']}";

			$result = $db->query($sql);

			if ($result) {
				$rec = $result[0];

				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaInfo.tpl');
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
				$tpl->assign('curp', utf8_encode($rec['curp']));
				$tpl->assign('fecha_nac', $rec['fecha_nac']);
				$tpl->assign('lugar_nac', utf8_encode($rec['lugar_nac']));
				$tpl->assign('sexo', $rec['sexo']);
				$tpl->assign('calle', utf8_encode($rec['calle']));
				$tpl->assign('colonia', utf8_encode($rec['colonia']));
				$tpl->assign('del_mun', utf8_encode($rec['del_mun']));
				$tpl->assign('entidad', utf8_encode($rec['entidad']));
				$tpl->assign('cod_postal', utf8_encode($rec['cod_postal']));
				$tpl->assign('telefono_casa', utf8_encode($rec['telefono_casa']));
				$tpl->assign('telefono_movil', utf8_encode($rec['telefono_movil']));
				$tpl->assign('email', utf8_encode($rec['email']));
				$tpl->assign('fecha_alta', $rec['fecha_alta']);
				$tpl->assign('fecha_elaboracion_contrato', $rec['fecha_elaboracion_contrato'] != '' ? $rec['fecha_elaboracion_contrato'] : '&nbsp;');
				$tpl->assign('puesto', utf8_encode($rec['puesto']));
				$tpl->assign('turno', utf8_encode($rec['turno']));
				$tpl->assign('horario', utf8_encode($rec['horario']));
				$tpl->assign('salario', $rec['salario'] > 0 ? number_format($rec['salario']) : '&nbsp;');
				$tpl->assign('salario_integrado', $rec['salario_integrado'] > 0 ? number_format($rec['salario_integrado']) : '&nbsp');
				$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss']);
				$tpl->assign('no_baja', $rec['no_baja']);
				$tpl->assign('num_afiliacion', $rec['num_afiliacion']);
				$tpl->assign('credito_infonavit', $rec['credito_infonavit']);
				$tpl->assign('no_infonavit', $rec['no_infonavit']);
				$tpl->assign('solo_aguinaldo', $rec['solo_aguinaldo']);
				$tpl->assign('tipo_aguinaldo', $rec['tipo_aguinaldo']);
				$tpl->assign('fecha_vencimiento_licencia_manejo', $rec['fecha_vencimiento_licencia_manejo']);
				$tpl->assign('observaciones', $rec['observaciones']);

				if ($rec['saldo'] != 0) {
					$tpl->newBlock('saldo');
					$tpl->assign('saldo', number_format($rec['saldo'], 2));
				}

				$sql = "SELECT
					ct.num_cia,
					cc.nombre AS nombre_cia,
					ct.num_emp,
					ct.nombre_completo AS nombre_trabajador,
					ct.fecha_alta,
					ct.fecha_alta_imss,
					ct.fecha_baja,
					ct.fecha_baja_imss,
					ct.tsalta,
					ct.idalta,
					aa.nombre AS usuario_alta,
					ct.tsmod,
					ct.idmod,
					am.nombre AS usuario_mod,
					ct.tsbaja,
					ct.idbaja,
					ab.nombre AS usuario_baja,
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
					), 0) AS saldo_prestamo
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc USING (num_cia)
					LEFT JOIN auth aa ON (aa.iduser = ct.idalta)
					LEFT JOIN auth am ON (am.iduser = ct.idmod)
					LEFT JOIN auth ab ON (ab.iduser = ct.idbaja)
				WHERE
					(ct.ap_paterno, ct.ap_materno, ct.nombre) IN (
						SELECT
							ap_paterno,
							ap_materno,
							nombre
						FROM
							catalogo_trabajadores
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					id DESC";

				$historial = $db->query($sql);

				if ($historial) {
					$row_color = FALSE;

					foreach ($historial as $i => $rec) {
						$tpl->newBlock('row');
						$tpl->assign('row_color', $row_color ? 'on' : 'off');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('num_emp', $rec['num_emp']);
						$tpl->assign('fecha_alta', $rec['fecha_alta']);
						$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss'] != '' ? $rec['fecha_alta_imss'] : '&nbsp;');
						$tpl->assign('fecha_baja', $rec['fecha_baja'] != '' ? $rec['fecha_baja'] : '&nbsp;');
						$tpl->assign('fecha_baja_imss', $rec['fecha_baja_imss'] != '' ? $rec['fecha_baja_imss'] : '&nbsp;');
						$tpl->assign('saldo_prestamo', $rec['saldo_prestamo'] != 0 ? number_format($rec['saldo_prestamo'], 2) : '&nbsp;');
						$tpl->assign('usuario_alta', $rec['usuario_alta'] != '' ? utf8_encode($rec['usuario_alta']) : '&nbsp;');
						$tpl->assign('usuario_baja', $rec['usuario_baja'] != '' ? utf8_encode($rec['usuario_baja']) : '&nbsp;');
					}
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'documentos':
			$sql = '
				SELECT
					num_emp,
					nombre_completo
				FROM
					catalogo_trabajadores
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$emp = $db->query($sql);

			$sql = '
				SELECT
					iddocumento,
					idempleado,
					DATE_TRUNC(\'second\', tsalta)
						AS fecha,
					CASE
						WHEN tipo = 1 THEN
							\'ALTA\'
						WHEN tipo = 2 THEN
							\'BAJA\'
						WHEN tipo = 3 THEN
							\'MODIFICACION\'
						WHEN tipo = 4 THEN
							\'AUTORIZACION\'
					END
						AS tipo,
					nombre_documento,
					\'doc_emp/\' || idempleado || \'/\' || nombre_documento
						AS url_documento
				FROM
					documentos_trabajadores
				WHERE
					idempleado = ' . $_REQUEST['id'] . '
					AND tsbaja IS NULL
				ORDER BY
					tsalta
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaDocumentos.tpl');
			$tpl->prepare();

			$tpl->assign('num_emp', $emp[0]['num_emp']);
			$tpl->assign('nombre_emp', utf8_encode($emp[0]['nombre_completo']));

			if ($result) {
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					$tpl->assign('id', $rec['iddocumento']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('tipo', $rec['tipo']);
					$tpl->assign('color', $rec['tipo'] == 'ALTA' ? 'blue' : ($rec['tipo'] == 'BAJA' ? 'red' : 'green'));
					$tpl->assign('url_documento', $rec['url_documento']);
					$tpl->assign('nombre_documento', $rec['nombre_documento']);
					$tpl->assign('baja', in_array($_SESSION['iduser'], array(1, 4, 5, 8, 25, 26, 28, 34, 46, 63)) ? ' <img src="/lecaroz/iconos/cancel.png" alt="' . $rec['iddocumento'] . '" name="baja_doc" width="16" height="16" align="absbottom" id="baja_doc" />' : '');
				}
			}

			if (in_array($_SESSION['iduser'], array(1, 4, 5, 8, 25, 26, 28, 34, 46, 63))) {
				$tpl->newBlock('alta_doc');
				$tpl->assign('id', $_REQUEST['id']);
			}

			echo $tpl->getOutputContent();
		break;

		case 'alta_documento':
			$finfo = new finfo(FILEINFO_MIME_TYPE);

			$data = array(
				'status' => 1
			);

			$ok = FALSE;

			if ($finfo->file($_FILES['archivo']['tmp_name']) == 'application/pdf') {
				if (!@move_uploaded_file($_FILES['archivo']['tmp_name'], 'doc_emp/tmp/' . $_FILES['archivo']['name'])) {
					$data['status'] = -7;

					$ok = FALSE;
				} else {
					$filename = $_FILES['archivo']['name'];

					$ok = TRUE;
				}
			} else if ($finfo->file($_FILES['archivo']['tmp_name']) != 'application/zip') {
				$data['status'] = -1;
				$data['mime'] = $finfo->file($_FILES['archivo']['tmp_name']);

				$ok = FALSE;
			} else if (!is_resource($zip = zip_open($_FILES['archivo']['tmp_name']))) {
				$data['status'] = -2;

				$ok = FALSE;
			} else {
				$zip_entrys = array();

				while ($zip_entrys[] = zip_read($zip));

				array_pop($zip_entrys);

				if (count($zip_entrys) > 1) {
					$data['status'] = -3;
					$data['count'] = count($zip_entrys);

					zip_close($zip);

					$ok = FALSE;
				} else if (!($fp = @fopen('doc_emp/tmp/' . basename(zip_entry_name($zip_entrys[0])), 'w+'))) {
					$data['status'] = -4;

					zip_close($zip);

					$ok = FALSE;
				} else {
					fwrite($fp, zip_entry_read($zip_entrys[0], zip_entry_filesize($zip_entrys[0])));
					fclose($fp);

					if ($finfo->file('doc_emp/tmp/' . basename(zip_entry_name($zip_entrys[0]))) != 'application/pdf') {
						$data['status'] = -6;
						$data['mime'] = $finfo->file('doc_emp/tmp/' . basename(zip_entry_name($zip_entrys[0])));

						$ok = FALSE;
					} else {
						$filename = basename(zip_entry_name($zip_entrys[0]));

						$ok = TRUE;
					}

					zip_close($zip);
				}
			}

			if ($ok && $db->query('
				SELECT
					iddocumento
				FROM
					documentos_trabajadores
				WHERE
					idempleado = ' . $_REQUEST['idempleado'] . '
					AND nombre_documento = \'' . $filename . '\'
					AND tsbaja IS NULL
			')) {
				$data['status'] = -5;

				unlink('doc_emp/tmp/' . $filename);

				$ok = FALSE;
			}

			if ($ok) {
				if (!is_dir('doc_emp/' . $_REQUEST['idempleado'])) {
					mkdir('doc_emp/' . $_REQUEST['idempleado']);
				}

				rename('doc_emp/tmp/' . $filename, 'doc_emp/' . $_REQUEST['idempleado'] . '/' . $filename);

				$sql = '
					INSERT INTO
						documentos_trabajadores (
							idempleado,
							tipo,
							nombre_documento,
							idalta
						)
						VALUES (
							' . $_REQUEST['idempleado'] . ',
							' . $_REQUEST['tipo'] . ',
							\'' . $filename . '\',
							' . $_SESSION['iduser'] . '
						)
				';

				$db->query($sql);

				$sql = '
					SELECT
						iddocumento
					FROM
						documentos_trabajadores
					WHERE
						idempleado = ' . $_REQUEST['idempleado'] . '
						AND nombre_documento = \'' . $filename . '\'
				';

				$doc = $db->query($sql);

				$data['id'] = $doc[0]['iddocumento'];
				$data['filename'] = $filename;
				$data['href'] = 'doc_emp/' . $_REQUEST['idempleado'] . '/' . $filename;
				$data['fecha'] = date('d/m/Y H:i:s');

				switch ($_REQUEST['tipo']) {

					case 1:
						$data['tipo'] = 'ALTA';
						break;

					case 2:
						$data['tipo'] = 'BAJA';
						break;

					case 3:
						$data['tipo'] = 'MODIFICACION';
						break;

					case 4:
						$data['tipo'] = 'AUTORIZACION';
						break;

				}
				//$data['tipo'] = $_REQUEST['tipo'] == 1 ? 'ALTA' : ($_REQUEST['tipo'] == 2 ? 'BAJA' : 'MODIFICACION');

				if (in_array($_REQUEST['tipo'], array(1, 3))) {
					$sql = '
						SELECT
							num_cia,
							nombre_corto,
							cc.nombre
								AS nombre_cia,
							nombre_completo
								AS nombre_empleado,
							cc.email
								AS email_cia
						FROM
							catalogo_trabajadores ct
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							id = ' . $_REQUEST['idempleado'] . '
					';

					$emp = $db->query($sql);

					include_once('includes/phpmailer/class.phpmailer.php');

					$mail = new PHPMailer();

					if ($emp[0]['num_cia'] >= 900) {
						$mail->IsSMTP();
						$mail->Host = 'mail.zapateriaselite.com';
						$mail->Port = 587;
						$mail->SMTPAuth = true;
						$mail->Username = 'elite@zapateriaselite.com';
						$mail->Password = 'facturaselectronicas';

						$mail->From = 'elite@zapateriaselite.com';
						$mail->FromName = utf8_decode('Oficinas Elite');
					}
					else {
						$mail->IsSMTP();
						$mail->Host = 'mail.lecaroz.com';
						$mail->Port = 587;
						$mail->SMTPAuth = true;
						$mail->Username = 'mollendo@lecaroz.com';
						$mail->Password = 'L3c4r0z*';

						$mail->From = 'mollendo@lecaroz.com';
						$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S.A de R.L.');
					}

					if ($emp[0]['num_cia'] >= 900) {
						$mail->AddAddress('elite@zapateriaselite.com');
					} else {
						$mail->AddAddress('margarita.hernandez@lecaroz.com');
					}
					//$mail->AddAddress('carlos.candelario@lecaroz.com');

					if ($emp[0]['email_cia'] != '') {
						$mail->AddAddress($emp[0]['email_cia']);
					}

					$mail->Subject = utf8_decode('[' . $emp[0]['num_cia'] . ' ' . $emp[0]['nombre_corto'] . '] ' . ($_REQUEST['tipo'] == 1 ? 'Alta' : 'Modificación') . ' del empleado ' . $emp[0]['nombre_empleado']);

					$tpl = new TemplatePower('plantillas/nom/email_confirmacion_movimiento_imss.tpl');
					$tpl->prepare();

					$tpl->assign('num_cia', $emp[0]['num_cia']);
					$tpl->assign('nombre_cia', $emp[0]['nombre_cia']);

					$tpl->assign('tipo_movimiento', $_REQUEST['tipo'] == 1 ? 'ALTA' : 'MODIFICACION');

					$tpl->assign('nombre_empleado', $emp[0]['nombre_empleado']);

					$tpl->assign('email_ayuda', $emp[0]['num_cia'] >= 900 ? 'elite@zapateriaselite.com' : 'margarita.hernandez@lecaroz.com');

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					$mail->AddAttachment('doc_emp/' . $_REQUEST['idempleado'] . '/' . $filename);

					if(!$mail->Send()) {
						echo $mail->ErrorInfo;
					}
				}
			}

			echo json_encode($data);
		break;

		case 'baja_documento':
			$sql = '
				UPDATE
					documentos_trabajadores
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					iddocumento = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);

			$sql = '
				SELECT
					\'doc_emp/\' || idempleado || \'/\' || nombre_documento
						AS filename,
					\'doc_emp/\' || idempleado || \'/DELETED_\' || nombre_documento
						AS newfilename
				FROM
					documentos_trabajadores
				WHERE
					iddocumento = ' . $_REQUEST['id'] . '
			';

			$doc = $db->query($sql);

			rename($doc[0]['filename'], $doc[0]['newfilename']);
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
					ct.curp,
					ct.fecha_nac,
					ct.lugar_nac,
					ct.sexo,
					ct.calle,
					ct.colonia,
					ct.del_mun,
					ct.entidad,
					ct.cod_postal,
					ct.telefono_casa,
					ct.telefono_movil,
					ct.email,
					ct.fecha_alta,
					ct.pendiente_alta,
					ct.pendiente_baja,
					ct.cod_puestos,
					ct.cod_turno,
					ct.cod_horario,
					ct.salario,
					ct.salario_integrado,
					ct.fecha_alta_imss,
					ct.no_baja,
					ct.num_afiliacion,
					ct.credito_infonavit,
					ct.no_infonavit,
					ct.solo_aguinaldo,
					ct.tipo,
					ct.fecha_vencimiento_licencia_manejo,
					ct.observaciones,
					ct.uniforme,
					ct.talla,
					ct.control_bata,
					ct.deposito_bata,
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
					ct.idempleado
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

				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaDatos.tpl');
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
				$tpl->assign('curp', utf8_encode($rec['curp']));
				$tpl->assign('fecha_nac', $rec['fecha_nac']);
				$tpl->assign('lugar_nac', utf8_encode($rec['lugar_nac']));
				$tpl->assign('sexo_' . $rec['sexo'], ' checked');
				$tpl->assign('calle', utf8_encode($rec['calle']));
				$tpl->assign('colonia', utf8_encode($rec['colonia']));
				$tpl->assign('del_mun', utf8_encode($rec['del_mun']));
				$tpl->assign('entidad', utf8_encode($rec['entidad']));
				$tpl->assign('cod_postal', utf8_encode($rec['cod_postal']));
				$tpl->assign('telefono_casa', utf8_encode($rec['telefono_casa']));
				$tpl->assign('telefono_movil', utf8_encode($rec['telefono_movil']));
				$tpl->assign('email', utf8_encode($rec['email']));
				$tpl->assign('fecha_alta', $rec['fecha_alta']);
				$tpl->assign('pendiente_alta', $rec['pendiente_alta']);
				$tpl->assign('pendiente_baja', $rec['pendiente_baja']);
				$tpl->assign('salario', $rec['salario'] > 0 ? number_format($rec['salario']) : '');
				$tpl->assign('salario_integrado', $rec['salario_integrado'] > 0 ? number_format($rec['salario_integrado']) : '');
				$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss']);
				$tpl->assign('no_baja', $rec['no_baja'] == 't' ? ' checked' : '');
				$tpl->assign('num_afiliacion', $rec['num_afiliacion']);
				$tpl->assign('credito_infonavit', $rec['credito_infonavit'] == 't' ? ' checked' : '');
				$tpl->assign('no_infonavit', $rec['no_infonavit']);
				$tpl->assign('solo_aguinaldo', $rec['solo_aguinaldo'] == 't' ? ' checked' : '');
				$tpl->assign('tipo_' . $rec['tipo'], ' selected');
				$tpl->assign('fecha_vencimiento_licencia_manejo', $rec['fecha_vencimiento_licencia_manejo']);
				$tpl->assign('observaciones', $rec['observaciones']);
				$tpl->assign('uniforme', $rec['uniforme']);
				$tpl->assign('talla_' . $rec['talla'], ' selected');
				$tpl->assign('control_bata', $rec['control_bata'] == 't' ? ' checked' : '');
				$tpl->assign('deposito_bata', $rec['deposito_bata'] > 0 ? number_format($rec['deposito_bata']) : '');
				$tpl->assign('idempleado', $rec['idempleado']);

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

				$sql = '
					SELECT
						cod_horario
							AS value,
						horaentrada || \'-\' || horasalida
							AS text
					FROM
						catalogo_horarios
					WHERE
						giro = ' . ($rec['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						value
				';

				$horarios = $db->query($sql);

				foreach ($horarios as $horario) {
					$tpl->newBlock('horario');
					$tpl->assign('value', $horario['value']);
					$tpl->assign('text', $horario['text']);

					if ($horario['value'] == $rec['cod_horario']) {
						$tpl->assign('selected', ' selected');
					}
				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'actualizar':
			/*
			@ Validar que la librería PHPMailer este cargada
			*/
			if (!class_exists('PHPMailer')) {
				include_once(dirname(__FILE__) . '/includes/phpmailer/class.phpmailer.php');
			}

			/*
			@ Validar que la librería TemplatePower este cargada
			*/
			if (!class_exists('TemplatePower')) {
				include_once(dirname(__FILE__) . '/includes/class.TemplatePower.inc.php');
			}

			$sql = '
				SELECT
					num_emp,
					num_cia,
					num_cia_emp,
					nombre,
					ap_paterno,
					ap_materno,
					rfc,
					curp,
					fecha_nac,
					lugar_nac,
					CASE
						WHEN sexo = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS sexo,
					calle,
					colonia,
					del_mun,
					entidad,
					cod_postal,
					telefono_casa,
					telefono_movil,
					email,
					cod_puestos,
					cod_turno,
					cod_horario,
					salario,
					salario_integrado,
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
						WHEN credito_infonavit = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS credito_infonavit,
					no_infonavit,
					CASE
						WHEN solo_aguinaldo = TRUE THEN
							\'TRUE\'
						ELSE
							\'FALSE\'
					END
						AS solo_aguinaldo,
					tipo,
					fecha_vencimiento_licencia_manejo,
					observaciones,
					uniforme,
					talla,
					control_bata,
					deposito_bata,
					fecha_alta,
					imp_alta,
					pendiente_alta,
					idalta,
					tsalta,
					idempleado
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
			if (utf8_encode($old['curp']) != (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '')) {
				$cambios[] = 'CURP: ' . $old['curp'] . ' -> ' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '');
			}
			if ($old['fecha_nac'] != (isset($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '')) {
				$cambios[] = 'FECHA NACIMIENTO: ' . $old['fecha_nac'] . ' -> ' . (isset($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '');
			}
			if (utf8_encode($old['lugar_nac']) != (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '')) {
				$cambios[] = 'LUGAR NACIMIENTO: ' . $old['lugar_nac'] . ' -> ' . (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '');
			}
			if ($old['sexo'] != (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '')) {
				$cambios[] = 'SEXO: ' . $old['sexo'] . ' -> ' . (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '');
			}
			if (utf8_encode($old['calle']) != (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '')) {
				$cambios[] = 'CALLE: ' . $old['calle'] . ' -> ' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '');
			}
			if (utf8_encode($old['colonia']) != (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '')) {
				$cambios[] = 'COLONIA: ' . $old['colonia'] . ' -> ' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '');
			}
			if (utf8_encode($old['del_mun']) != (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '')) {
				$cambios[] = 'DELEGACION/MUNICIPIO: ' . $old['del_mun'] . ' -> ' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '');
			}
			if (utf8_encode($old['entidad']) != (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '')) {
				$cambios[] = 'ENTIDAD: ' . $old['entidad'] . ' -> ' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '');
			}
			if ($old['cod_postal'] != (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '')) {
				$cambios[] = 'CODIGO POSTAL: ' . $old['cod_postal'] . ' -> ' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '');
			}
			if ($old['telefono_casa'] != (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '')) {
				$cambios[] = 'TELEFONO CASA: ' . $old['telefono_casa'] . ' -> ' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '');
			}
			if ($old['telefono_movil'] != (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '')) {
				$cambios[] = 'TELEFONO MOVIL: ' . $old['telefono_movil'] . ' -> ' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '');
			}
			if ($old['email'] != (isset($_REQUEST['email']) ? $_REQUEST['email'] : '')) {
				$cambios[] = 'EMAIL: ' . $old['email'] . ' -> ' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '');
			}
			if ($old['cod_puestos'] != (isset($_REQUEST['cod_puestos']) ? $_REQUEST['cod_puestos'] : '')) {
				$cambios[] = 'PUESTO: ' . $old['cod_puestos'] . ' -> ' . (isset($_REQUEST['cod_puestos']) ? $_REQUEST['cod_puestos'] : '');
			}
			if ($old['cod_turno'] != (isset($_REQUEST['cod_turno']) ? $_REQUEST['cod_turno'] : '')) {
				$cambios[] = 'TURNO: ' . $old['cod_turno'] . ' -> ' . (isset($_REQUEST['cod_turno']) ? $_REQUEST['cod_turno'] : '');
			}
			if ($old['cod_horario'] != (isset($_REQUEST['cod_horario']) ? $_REQUEST['cod_horario'] : '')) {
				$cambios[] = 'HORARIO: ' . $old['cod_horario'] . ' -> ' . (isset($_REQUEST['cod_horario']) ? $_REQUEST['cod_horario'] : '');
			}
			if (get_val($old['salario']) != (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0)) {
				$cambios[] = 'SALARIO DIARIO: ' . $old['salario'] . ' -> ' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : '');
			}
			if (get_val($old['salario_integrado']) != (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0)) {
				$cambios[] = 'SALARIO DIARIO INTEGRADO: ' . $old['salario_integrado'] . ' -> ' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : '');
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
			if ((isset($_REQUEST['credito_infonavit']) && $old['credito_infonavit'] == 'FALSE')
				|| (!isset($_REQUEST['credito_infonavit']) && $old['credito_infonavit'] == 'TRUE')) {
				$cambios[] = 'CREDITO INFONAVIT: ' . $old['credito_infonavit'] . ' -> ' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE');
			}
			if ($old['no_infonavit'] != (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '')) {
				$cambios[] = 'NO. CREDITO INFONAVIT: ' . $old['no_infonavit'] . ' -> ' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '');
			}
			if ((isset($_REQUEST['solo_aguinaldo']) && $old['solo_aguinaldo'] == 'FALSE')
				|| (!isset($_REQUEST['solo_aguinaldo']) && $old['solo_aguinaldo'] == 'TRUE')) {
				$cambios[] = 'SOLO AGUINALDO: ' . $old['solo_aguinaldo'] . ' -> ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE');
			}
			if ($old['tipo'] != (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '')) {
				$cambios[] = 'TIPO AGUINALDO: ' . $old['tipo'] . ' -> ' . (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : '');
			}
			if ($old['fecha_vencimiento_licencia_manejo'] != (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '')) {
				$cambios[] = 'FECHA VENCIMIENTO LICENCIA MANEJO: ' . $old['fecha_vencimiento_licencia_manejo'] . ' -> ' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '');
			}
			if (utf8_encode($old['observaciones']) != (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '')) {
				$cambios[] = 'OBSERVACIONES: ' . $old['observaciones'] . ' -> ' . (isset($_REQUEST['observaciones']) ? utf8_decode($_REQUEST['observaciones']) : '');
			}
			if ($old['idempleado'] != (isset($_REQUEST['idempleado']) ? $_REQUEST['idempleado'] : '')) {
				$cambios[] = 'ID EMPLEADO CHECADOR: ' . $old['idempleado'] . ' -> ' . (isset($_REQUEST['idempleado']) ? $_REQUEST['idempleado'] : '');
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
						rfc = \'' . utf8_decode($_REQUEST['rfc']) . '\',
						curp = \'' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . '\',
						fecha_nac = \'' . $_REQUEST['fecha_nac'] . '\',
						lugar_nac = \'' . (isset($_REQUEST['lugar_nac']) ? $_REQUEST['lugar_nac'] : '') . '\',
						sexo = ' . $_REQUEST['sexo'] . ',
						calle = \'' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . '\',
						colonia = \'' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . '\',
						del_mun = \'' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '') . '\',
						entidad = \'' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '') . '\',
						cod_postal = \'' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '') . '\',
						telefono_casa = \'' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '') . '\',
						telefono_movil = \'' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '') . '\',
						email = \'' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . '\',
						cod_puestos = ' . $_REQUEST['cod_puestos'] . ',
						cod_turno = ' . $_REQUEST['cod_turno'] . ',
						cod_horario = ' . $_REQUEST['cod_horario'] . ',
						salario = ' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ',
						salario_integrado = ' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0) . ',
						fecha_alta_imss = ' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
						no_baja = ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
						num_afiliacion = \'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
						credito_infonavit = ' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE') . ',
						no_infonavit = \'' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '') . '\',
						solo_aguinaldo = ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
						tipo = ' . $_REQUEST['tipo'] . ',
						fecha_vencimiento_licencia_manejo = ' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? '\'' . $_REQUEST['fecha_vencimiento_licencia_manejo'] . '\'' : 'NULL') . ',
						observaciones = \'' . (isset($_REQUEST['observaciones']) ? utf8_decode(substr($_REQUEST['observaciones'], 0, 1000)) : '') . '\',
						uniforme = ' . (isset($_REQUEST['uniforme']) ? '\'' . $_REQUEST['uniforme'] . '\'' : 'NULL') . ',
						talla = ' . (isset($_REQUEST['talla']) ? $_REQUEST['talla'] : 'NULL') . ',
						control_bata = ' . (isset($_REQUEST['control_bata']) ? 'TRUE' : 'FALSE') . ',
						deposito_bata = ' . (isset($_REQUEST['deposito_bata']) ? get_val($_REQUEST['deposito_bata']) : 0) . ',
						fecha_alta = \'' . $_REQUEST['fecha_alta'] . '\',
						imp_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? 'TRUE' : 'FALSE') . ',
						pendiente_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? '\'' . date('d/m/Y') . '\'' : (isset($_REQUEST['pendiente_alta']) && $_REQUEST['pendiente_alta'] != '' ? '\'' . $_REQUEST['pendiente_alta'] . '\'' : 'NULL')) . ',
						pendiente_baja = ' . (isset($_REQUEST['num_afiliacion']) && isset($_REQUEST['pendiente_baja']) && $_REQUEST['pendiente_baja'] != '' ? '\'' . $_REQUEST['pendiente_baja'] . '\'' : 'NULL') . ',
						idmod = ' . $_SESSION['iduser'] . ',
						tsmod = NOW(),
						idempleado = ' . (isset($_REQUEST['idempleado']) ? get_val($_REQUEST['idempleado']) : 0) . '
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
								\'MODIFICACION [OFICINA]' . "\n" . implode("\n", $cambios) . '\'
							)
					' . ";\n";

					/*
					* [21-Nov-2013] Enviar correo electrónico en caso de modificación
					*/

					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';

					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = utf8_decode('Lecaroz :: Oficinas');

					$mail->AddAddress('olga.espinoza@lecaroz.com');
					// $mail->AddAddress('cristian.gonzalez@lecaroz.com');
					// $mail->AddAddress('angelica.guzman@lecaroz.com');
					$mail->AddAddress('margarita.hernandez@lecaroz.com');
					$mail->AddAddress('recursos.humanos@lecaroz.com');
					// $mail->AddAddress('carlos.candelario@lecaroz.com');

					$mail->Subject = 'Modificación de trabajador';

					$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/nom/email_trabajador_mod.tpl');
					$tpl->prepare();

					$user = $db->query("
						SELECT
							username,
							CONCAT_WS(' ', nombre, apellido)
								AS nombre
						FROM
							auth
						WHERE
							iduser = {$_SESSION['iduser']}
					");

					$tpl->assign('usuario', "[{$user[0]['username']}] {$user[0]['nombre']}");
					$tpl->assign('num_emp', $old['num_emp']);
					$tpl->assign('nombre_trabajador', implode(' ', array($old['ap_paterno'], $old['ap_materno'], $old['nombre'])));
					$tpl->assign('fecha', date('d/m/Y'));
					$tpl->assign('hora', date('H:i:s'));
					$tpl->assign('info', implode("\n", $cambios));

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					if(!$mail->Send()) {
						echo $mail->ErrorInfo;
					}
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
							\'BAJA POR CAMBIO DE COMPAÑIA [OFICINA]\'
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
								curp,
								fecha_nac,
								lugar_nac,
								sexo,
								calle,
								colonia,
								del_mun,
								entidad,
								cod_postal,
								telefono_casa,
								telefono_movil,
								email,
								cod_puestos,
								cod_turno,
								cod_horario,
								salario,
								salario_integrado,
								fecha_alta_imss,
								no_baja,
								num_afiliacion,
								credito_infonavit,
								no_infonavit,
								solo_aguinaldo,
								tipo,
								observaciones,
								uniforme,
								talla,
								control_bata,
								deposito_bata,
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
								\'' . utf8_decode($_REQUEST['rfc']) . '\',
								\'' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . '\',
								\'' . $_REQUEST['fecha_nac'] . '\',
								\'' . (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '') . '\',
								' . $_REQUEST['sexo'] . ',
								\'' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . '\',
								\'' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . '\',
								\'' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '') . '\',
								\'' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '') . '\',
								\'' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '') . '\',
								\'' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '') . '\',
								\'' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '') . '\',
								\'' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . '\',
								' . $_REQUEST['cod_puestos'] . ',
								' . $_REQUEST['cod_turno'] . ',
								' . $_REQUEST['cod_horario'] . ',
								' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ',
								' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0) . ',
								' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
								' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
								\'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
								' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE') . ',
								\'' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '') . '\',
								' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
								' . $_REQUEST['tipo'] . ',
								\'' . (isset($_REQUEST['observaciones']) ? substr(utf8_decode($_REQUEST['observaciones']), 0, 1000) : '') . '\',
								' . (isset($_REQUEST['uniforme']) ? '\'' . $_REQUEST['uniforme'] . '\'' : 'NULL') . ',
								' . (isset($_REQUEST['talla']) ? $_REQUEST['talla'] : 'NULL') . ',
								' . (isset($_REQUEST['control_bata']) ? 'TRUE' : 'FALSE') . ',
								' . (isset($_REQUEST['deposito_bata']) ? get_val($_REQUEST['deposito_bata']) : 0) . ',
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
							\'ALTA POR CAMBIO DE COMPAÑIA [ID ANTERIOR: ' . $_REQUEST['id'] . "][OFICINA]" . (!!$cambios ? "\n" .implode("\n", $cambios) : '') . '\'
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

				$mail = new PHPMailer();

				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'mollendo@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'mollendo@lecaroz.com';
				$mail->FromName = utf8_decode('Lecaroz :: Oficinas');

				$mail->AddAddress('olga.espinoza@lecaroz.com');
				// $mail->AddAddress('cristian.gonzalez@lecaroz.com');
				// $mail->AddAddress('angelica.guzman@lecaroz.com');
				$mail->AddAddress('margarita.hernandez@lecaroz.com');
				$mail->AddAddress('recursos.humanos@lecaroz.com');
				// $mail->AddAddress('carlos.candelario@lecaroz.com');

				$mail->Subject = 'Modificación de trabajador';

				$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/nom/email_trabajador_mod.tpl');
				$tpl->prepare();

				$user = $db->query("
					SELECT
						username,
						CONCAT_WS(' ', nombre, apellido)
							AS nombre
					FROM
						auth
					WHERE
						iduser = {$_SESSION['iduser']}
				");

				$tpl->assign('usuario', "[{$user[0]['username']}] {$user[0]['nombre']}");
				$tpl->assign('num_emp', $old['num_emp']);
				$tpl->assign('nombre_trabajador', implode(' ', array($old['ap_paterno'], $old['ap_materno'], $old['nombre'])));
				$tpl->assign('fecha', date('d/m/Y'));
				$tpl->assign('hora', date('H:i:s'));
				$tpl->assign('info', "CAMBIO DE COMPA&Ntilde;IA: {$old['num_cia']} -> {$_REQUEST['num_cia']}");

				$mail->Body = $tpl->getOutputContent();

				$mail->IsHTML(true);

				if(!$mail->Send()) {
					echo $mail->ErrorInfo;
				}
			}

			$db->query($sql);
		break;

		case 'baja':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaBaja.tpl');
			$tpl->prepare();

			$tpl->assign('id', $_REQUEST['id']);

			$sql = "
				SELECT
					id_tipo_baja_trabajador
						AS value,
					descripcion
						AS text
				FROM
					catalogo_tipos_baja_trabajador
				WHERE
					tsbaja IS NULL
				ORDER BY
					value
			";

			$result = $db->query($sql);

			if ($result) {
				foreach ($result as $rec) {
					$tpl->newBlock('tipo_baja');
					$tpl->assign('value', $rec['value']);
					$tpl->assign('text', utf8_decode($rec['text']));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'do_baja':
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
					id_tipo_baja_trabajador = ' . $_REQUEST['tipo'] . ',
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
						\'BAJA DE SISTEMA [OFICINA]\'
					)
			' . ";\n";

			$sql .= '
				INSERT INTO
					bajas_trabajadores (
						id_empleado,
						idbaja
					)
					SELECT
						id,
						idbaja
					FROM
						catalogo_trabajadores
					WHERE
						id = ' . $_REQUEST['id'] . '
						AND num_afiliacion IS NOT NULL
						AND TRIM(num_afiliacion) <> \'\'
			' . ";\n";

			$db->query($sql);

			// [28-Jul-2013] Enviar correo electrónico de baja de empleado (solo empleados con seguro)

			$sql = "
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_emp,
					CONCAT_WS(' ', ct.ap_paterno, ct.ap_materno, ct.nombre)
						AS nombre_emp,
					ct.num_afiliacion,
					u.nombre
						AS usuario,
					bt.tsbaja::DATE
						AS fecha,
					CONCAT_WS(':', EXTRACT(HOUR FROM bt.tsbaja), EXTRACT(MINUTE FROM bt.tsbaja), EXTRACT(SECOND FROM DATE_TRUNC('second', bt.tsbaja)))
						AS hora,
					bt.folio
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN auth u
						ON (u.iduser = ct.idbaja)
					LEFT JOIN bajas_trabajadores bt
						ON (bt.id_empleado = ct.id)
				WHERE
					ct.id = {$_REQUEST['id']}
					AND bt.folio > 0
					AND fecha_baja = NOW()::DATE
				ORDER BY
					bt.folio DESC
				LIMIT 1
			";

			$result = $db->query($sql);

			if ($result) {
				$row = $result[0];

				/*
				@ Validar que la librería PHPMailer este cargada
				*/
				if (!class_exists('PHPMailer')) {
					include_once(dirname(__FILE__) . '/includes/phpmailer/class.phpmailer.php');
				}

				/*
				@ Validar que la librería TemplatePower este cargada
				*/
				if (!class_exists('TemplatePower')) {
					include_once(dirname(__FILE__) . '/includes/class.TemplatePower.inc.php');
				}

				$mail = new PHPMailer();

				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'recursos.humanos@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'recursos.humanos@lecaroz.com';
				$mail->FromName = utf8_decode('Lecaroz :: Recursos Humanos');

				$mail->AddAddress('olga.espinoza@lecaroz.com');
				// $mail->AddAddress('cristian.gonzalez@lecaroz.com');
				// $mail->AddAddress('angelica.guzman@lecaroz.com');
				$mail->AddAddress('margarita.hernandez@lecaroz.com');
				$mail->AddAddress('recursos.humanos@lecaroz.com');
				// $mail->AddAddress('carlos.candelario@lecaroz.com');

				$mail->Subject = 'Baja de empleado [folio ' . $row['folio'] . ']';

				$tpl = new TemplatePower(dirname(__FILE__) . '/plantillas/nom/email_baja_empleado.tpl');
				$tpl->prepare();

				$tpl->assign('folio', $row['folio']);
				$tpl->assign('num_cia', $row['num_cia']);
				$tpl->assign('nombre_cia', $row['nombre_cia']);
				$tpl->assign('num_emp', $row['num_emp']);
				$tpl->assign('nombre_emp', $row['nombre_emp']);
				$tpl->assign('num_afiliacion', $row['num_afiliacion']);
				$tpl->assign('fecha', $row['fecha']);
				$tpl->assign('hora', $row['hora']);
				$tpl->assign('usuario', $row['usuario']);

				$mail->Body = $tpl->getOutputContent();

				$mail->IsHTML(true);

				if(!$mail->Send()) {
					echo json_encode(array(
						'status'	=> -1,
						'folio'		=> $row['folio'],
						'error'		=> $mail->ErrorInfo
					));
				}
				else
				{
					echo json_encode(array(
						'status'	=> 1,
						'folio'		=> $row['folio']
					));
				}
			}

		break;

		case 'pension':
			$sql = '
				UPDATE
					catalogo_trabajadores
				SET
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
					idmod = ' . $_SESSION['iduser'] . ',
					tsmod = NOW(),
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
						\'BAJA POR PENSION [OFICINA]\'
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
						\'REACTIVACION DE TRABAJADOR [OFICINA]:' . "\n" . 'NUMERO DE EMPLEADO ANTERIOR: \' || (
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

		case 'cursos':
			$sql = '
				SELECT
					cce.idcursoempleado
						AS id,
					cc.nombre_curso,
					cce.fecha
				FROM
					cursos_capacitacion_empleados cce
					LEFT JOIN cursos_capacitacion cc
						USING (idcursocapacitacion)
				WHERE
					idempleado = ' . $_REQUEST['id'] . '
					AND cce.tsbaja IS NULL
					AND cc.tsbaja IS NULL
				ORDER BY
					cce.fecha
			';

			$cursos = $db->query($sql);

			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaCursos.tpl');
			$tpl->prepare();

			if ($cursos) {
				$row_color = FALSE;

				foreach ($cursos as $i => $curso) {
					$tpl->newBlock('row');

					$tpl->assign('row_color', $i % 2 == 0 ? 'off' : 'on');

					$row_color = !$row_color;

					$tpl->assign('id', $curso['id']);
					$tpl->assign('nombre_curso', utf8_encode($curso['nombre_curso']));
					$tpl->assign('fecha', $curso['fecha']);
				}
			}

			$sql = '
				SELECT
					idcursocapacitacion
						AS value,
					nombre_curso
						AS text
				FROM
					cursos_capacitacion
				WHERE
					tsbaja IS NULL
					AND status = 0
				ORDER BY
					text
			';

			$cursos = $db->query($sql);

			if ($cursos) {
				foreach ($cursos as $curso) {
					$tpl->newBlock('option');
					$tpl->assign('value', $curso['value']);
					$tpl->assign('text', utf8_encode($curso['text']));
				}
			}

			$tpl->assign('_ROOT.id', $_REQUEST['id']);
			$tpl->assign('_ROOT.fecha', date('d/m/Y'));

			echo $tpl->getOutputContent();
		break;

		case 'alta_curso':
			$sql = '
				INSERT INTO
					cursos_capacitacion_empleados (
						idempleado,
						idcursocapacitacion,
						fecha,
						idalta
					)
					VALUES (
						' . $_REQUEST['id'] . ',
						' . $_REQUEST['curso'] . ',
						\'' . $_REQUEST['fecha'] . '\',
						' . $_SESSION['iduser'] . '
					)
			';

			$db->query($sql);

			$sql = '
				SELECT
					idcursoempleado
						AS id,
					nombre_curso,
					fecha
				FROM
					cursos_capacitacion_empleados
					LEFT JOIN cursos_capacitacion
						USING (idcursocapacitacion)
				WHERE
					idempleado = ' . $_REQUEST['id'] . '
					AND idcursocapacitacion = ' . $_REQUEST['curso'] . '
				ORDER BY
					idcursoempleado DESC
				LIMIT
					1
			';

			$result = $db->query($sql);

			$curso = $result[0];

			echo json_encode(array(
				'id' => intval($curso['id']),
				'nombre_curso' => utf8_encode($curso['nombre_curso']),
				'fecha' => $curso['fecha']
			));
		break;

		case 'baja_curso':
			$sql = '
				UPDATE
					cursos_capacitacion_empleados
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idcursoempleado = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);
		break;

		case 'obtener_datos_checador':
			$mysql = new DBclass('mysql://root:pobgnj@192.168.1.2:3306/checador', 'autocommit=yes');

			$result = $mysql->query("SELECT
				`emp`.`IdEmpleado` AS `idempleado`,
				CONCAT_WS(' ', `emp`.`Nombre`, `emp`.`ApellidoP`, `emp`.`ApellidoM`) AS `nombre_checador`,
				COUNT(`huellas`.`id`) AS `num_huellas`
			FROM
				`tblempleado` AS `emp`
				LEFT JOIN `tblhuella` AS `huellas` ON (`huellas`.`IdEmpleado` = `emp`.`IdEmpleado`)
			WHERE
				`emp`.`IdEmpleado` = {$_REQUEST['id']}
			GROUP BY
				`idempleado`,
				`nombre_checador`");

			if ($result)
			{
				header('Content-Type: application/json');

				echo json_encode($result[0]);
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
