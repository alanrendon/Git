<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include 'includes/cheques.inc.php';

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

function encode($el) {
	return utf8_encode($el);
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerDatos':
			$cia = $db->query("SELECT
				nombre_corto AS nombre_cia
			FROM
				catalogo_companias
			WHERE
				num_cia = {$_REQUEST['num_cia']}" . ($_SESSION['tipo_usuario'] == 2 ? ' AND num_cia BETWEEN 900 AND 998' : ''));

			if ($cia) {
				$data = array(
					'num_cia' => $_REQUEST['num_cia'],
					'nombre_cia' => utf8_encode($cia[0]['nombre_cia']),
					'empleados' => array(
						array(
							'value' => NULL,
							'text'  => NULL
						)
					),
					'cias'    => array(
						array(
							'value' => NULL,
							'text'  => NULL
						)
					),
					'puestos' => array(
						array(
							'value' => NULL,
							'text'  => NULL
						)
					),
					'turnos' => array(
						array(
							'value' => NULL,
							'text'  => NULL
						)
					)
				);

				$empleados = $db->query("SELECT
					id,
					'[' || LPAD(num_emp::varchar(5), 5, '0') || '] 'AS num,
					COALESCE(ap_paterno, '') || ' ' || COALESCE(ap_materno, '') || ' ' || COALESCE(nombre, '') AS empleado,
					(
						COALESCE(firma_contrato, FALSE) = FALSE
						OR (
							fecha_inicio_contrato IS NULL
							AND fecha_termino_contrato IS NULL
						)
					) AS status
				FROM
					catalogo_trabajadores
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND baja_rh IS NULL
				ORDER BY
					empleado");

				if ($empleados) {
					foreach ($empleados as $e) {
						$data['empleados'][] = array(
							'value' => $e['id'],
							'text'  => $e['num'] . utf8_encode($e['empleado']),
							'class' => $e['status'] == 't' ? 'red' : ''
						);
					}
				}

				$cias = $db->query("SELECT
					num_cia AS value,
					num_cia || ' ' || nombre_corto AS text
				FROM
					catalogo_companias
				WHERE
					rfc = (
						SELECT
							rfc
						FROM
							catalogo_companias
						WHERE
							num_cia = {$_REQUEST['num_cia']}
					)
				ORDER BY
					CASE
						WHEN num_cia = {$_REQUEST['num_cia']} THEN
							0
						ELSE
							1
					END,
					num_cia");

				if ($cias) {
					foreach ($cias as $c) {
						$data['cias'][] = array(
							'value' => $c['value'],
							'text'  => utf8_encode($c['text'])
						);
					}
				}

				$puestos = $db->query("SELECT
					cod_puestos AS value,
					descripcion AS text
				FROM
					catalogo_puestos
				WHERE
					giro = " . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . "
				ORDER BY
					cod_puestos");

				if ($puestos) {
					foreach ($puestos as $p) {
						$data['puestos'][] = array(
							'value' => $p['value'],
							'text'  => utf8_encode($p['text'])
						);
					}
				}

				$turnos = $db->query("SELECT
					cod_turno AS value,
					descripcion AS text
				FROM
					catalogo_turnos
				WHERE
					giro = " . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . "
				ORDER BY
					cod_turno");

				if ($turnos) {
					foreach ($turnos as $t) {
						$data['turnos'][] = array(
							'value' => $t['value'],
							'text'  => utf8_encode($t['text'])
						);
					}
				}

				echo json_encode($data);
			}
		break;

		case 'obtenerCiaSec':
			$cia = $db->query("SELECT
				nombre_corto AS nombre_cia
			FROM
				catalogo_companias
			WHERE
				num_cia = {$_REQUEST['num_cia']}" . ($_SESSION['tipo_usuario'] == 2 ? ' AND num_cia BETWEEN 900 AND 998' : ''));

			if ($cia) {
				echo utf8_encode($cia[0]['nombre_cia']);
			}
		break;

		case 'obtenerDatosEmpleado':
			$result = $db->query("SELECT
				ct.num_cia_sec,
				ccs.nombre_corto AS nombre_cia_sec,
				COALESCE(ct.num_cia_emp, ct.num_cia) AS num_cia_emp,
				(
					SELECT
						nombre_corto
					FROM
						catalogo_companias
					WHERE
						num_cia = ct.num_cia_emp
				) AS nombre_cia_emp,
				COALESCE(ct.nombre, '') AS nombre,
				COALESCE(ap_paterno, '') AS ap_paterno,
				COALESCE(ap_materno, '') AS ap_materno,
				COALESCE(ct.rfc, '') AS rfc,
				COALESCE(ct.curp, '') AS curp,
				CASE
					WHEN sexo = 'FALSE' THEN
						0
					ELSE
						1
				END AS sexo,
				fecha_nac AS fecha_nacimiento,
				COALESCE(estado_civil, 1) AS estado_civil,
				COALESCE(ct.calle, '') AS calle,
				COALESCE(ct.colonia, '') AS colonia,
				COALESCE(del_mun, '') AS municipio,
				COALESCE(entidad, '') AS estado,
				COALESCE(cod_postal, '') AS codigo_postal,
				COALESCE(ct.email, '') AS email,
				cod_puestos AS puesto,
				cod_turno AS turno,
				CASE
					WHEN ct.salario > 0 THEN
						ct.salario
					ELSE
						cp.sueldo
				END AS salario,
				fecha_inicio_contrato AS fecha_inicio,
				fecha_termino_contrato AS fecha_termino,
				LPAD(EXTRACT(HOUR FROM hora_inicio)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_inicio)::VARCHAR, 2, '0') AS hora_inicio,
				LPAD(EXTRACT(HOUR FROM hora_termino)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_termino)::VARCHAR, 2, '0') AS hora_termino,
				COALESCE(doc_acta_nacimiento, FALSE) AS doc_acta_nacimiento,
				COALESCE(doc_comprobante_domicilio, FALSE) AS doc_comprobante_domicilio,
				COALESCE(doc_curp, FALSE) AS doc_curp,
				COALESCE(doc_ife, FALSE) AS doc_ife,
				COALESCE(doc_num_seguro_social, FALSE) AS doc_num_seguro_social,
				COALESCE(doc_solicitud_trabajo, FALSE) AS doc_solicitud_trabajo,
				COALESCE(doc_comprobante_estudios, FALSE) AS doc_comprobante_estudios,
				COALESCE(doc_referencias, FALSE) AS doc_referencias,
				COALESCE(doc_no_antecedentes_penales, FALSE) AS doc_no_antecedentes_penales,
				COALESCE(doc_licencia_manejo, FALSE) AS doc_licencia_manejo,
				fecha_vencimiento_licencia_manejo,
				COALESCE(doc_rfc, FALSE) AS doc_rfc,
				COALESCE(doc_no_adeudo_infonavit, FALSE) AS doc_no_adeudo_infonavit,
				COALESCE(firma_contrato) AS firma_contrato
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias ccs
					ON (ccs.num_cia = ct.num_cia_sec)
				LEFT JOIN catalogo_puestos cp
					USING (cod_puestos)
			WHERE
				id = {$_REQUEST['id']}");

			if ($result) {
				echo json_encode(array_map('encode', $result[0]));
			}
		break;

		case 'contrato':
			if (isset($_REQUEST['renovar'])) {
				$sql = "SELECT
					ct.id,
					num_cia,
					num_cia_emp,
					num_cia_sec,
					COALESCE(nombre, '') AS nombre,
					COALESCE(ap_paterno, '') AS ap_paterno,
					COALESCE(ap_materno, '') AS ap_materno,
					COALESCE(rfc, '') AS rfc,
					COALESCE(curp, '') AS curp,
					CASE
						WHEN sexo = FALSE THEN
							'FALSE'
						ELSE
							'TRUE'
					END AS sexo,
					fecha_nac AS fecha_nacimiento,
					COALESCE(estado_civil, 1) AS estado_civil,
					COALESCE(calle, '') AS calle,
					COALESCE(colonia, '') AS colonia,
					COALESCE(del_mun, '') AS municipio,
					COALESCE(entidad, '') AS estado,
					COALESCE(cod_postal, '') AS codigo_postal,
					cod_puestos AS puesto,
					CASE
						WHEN ct.salario > 0 THEN
							ct.salario
						ELSE
							cp.sueldo
					END AS salario,
					fecha_inicio_contrato AS fecha_inicio,
					fecha_termino_contrato AS fecha_termino,
					LPAD(EXTRACT(HOUR FROM hora_inicio)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_inicio)::VARCHAR, 2, '0') AS hora_inicio,
					LPAD(EXTRACT(HOUR FROM hora_termino)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_termino)::VARCHAR, 2, '0') AS hora_termino,
					CASE
						WHEN fecha_termino_contrato IS NULL THEN
							2
						ELSE
							1
					END AS tipo
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos cp USING (cod_puestos)
				WHERE
					id = {$_REQUEST['id']}";
			}
			else {
				$sql = "SELECT
					ct.id,
					num_cia,
					num_cia_emp,
					num_cia_sec,
					COALESCE(nombre, '') AS nombre,
					COALESCE(ap_paterno, '') AS ap_paterno,
					COALESCE(ap_materno, '') AS ap_materno,
					COALESCE(rfc, '') AS rfc,
					COALESCE(curp, '') AS curp,
					CASE
						WHEN sexo = FALSE THEN
							'FALSE'
						ELSE
							'TRUE'
					END AS sexo,
					fecha_nac AS fecha_nacimiento,
					COALESCE(estado_civil, 1) AS estado_civil,
					COALESCE(calle, '') AS calle,
					COALESCE(colonia, '') AS colonia,
					COALESCE(del_mun, '') AS municipio,
					COALESCE(entidad, '') AS estado,
					COALESCE(cod_postal, '') AS codigo_postal,
					cod_puestos AS puesto,
					CASE
						WHEN ct.salario > 0 THEN
							ct.salario
						ELSE
							cp.sueldo
					END AS salario,
					fecha_inicio_contrato AS fecha_inicio,
					fecha_termino_contrato AS fecha_termino,
					LPAD(EXTRACT(HOUR FROM hora_inicio)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_inicio)::VARCHAR, 2, '0') AS hora_inicio,
					LPAD(EXTRACT(HOUR FROM hora_termino)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_termino)::VARCHAR, 2, '0') AS hora_termino,
					CASE
						WHEN fecha_termino_contrato IS NULL THEN
							2
						ELSE
							1
					END AS tipo
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos cp USING (cod_puestos)
				WHERE
					id = {$_REQUEST['empleado']}";
			}

			$result = $db->query($sql);

			$data = $result[0];

			// Generar folio del documento
			$db->query("INSERT INTO folios_contratos_trabajadores (
				id_emp,
				tscontrato,
				iduser,
				folio,
				num_cia
			) VALUES (
				{$data['id']},
				NOW(),
				{$_SESSION['iduser']},
				COALESCE((SELECT MAX(folio) + 1 FROM folios_contratos_trabajadores), 1),
				{$data['num_cia']}
			)");

			// Obtener último folio generado
			$result = $db->query("SELECT MAX(folio) AS folio FROM folios_contratos_trabajadores WHERE iduser = {$_SESSION['iduser']}");

			$folio = $result[0]['folio'];

			$tpl = new TemplatePower('plantillas/fac/ContratoTrabajadorDocumento' . $data['tipo'] . '.tpl');
			$tpl->prepare();

			$cia = $db->query("SELECT
				nombre,
				direccion AS domicilio
			FROM
				catalogo_companias
			WHERE
				num_cia = " . ($data['num_cia_emp'] != $data['num_cia'] ? $data['num_cia_emp'] : $data['num_cia']));

			$tpl->newBlock('contrato');

			$pieces = explode('/', $data['fecha_nacimiento']);

			$tsemp = mktime(0, 0, 0, $pieces[1], $pieces[0], date('Y'));

			$edad = date('Y') - $pieces[2] - (time() - $tsemp < 0 ? 1 : 0);

			$nombre = implode(' ', array(
				$data['nombre'],
				$data['ap_paterno'],
				isset($data['ap_materno']) ? $data['ap_materno'] : ''
			));

			$domicilio = implode(', ', array(
				$data['calle'],
				isset($data['colonia']) ? 'COL. ' . $data['colonia'] : '',
				$data['municipio'],
				$data['estado'],
				isset($data['codigo_postal']) ? 'CP. ' . $data['codigo_postal'] : ''
			));

			$sexo = $data['sexo'] == 'FALSE' ? 'MASCULINO' : 'FEMENINO';

			$tpl->assign('folio_contrato', str_pad($folio, 10, '0', STR_PAD_LEFT));

			$tpl->assign('cia_nombre', utf8_encode($cia[0]['nombre']));
			$tpl->assign('cia_domicilio', utf8_encode($cia[0]['domicilio']));
			$tpl->assign('empleado_nombre', utf8_encode($nombre));
			$tpl->assign('empleado_rfc', isset($data['rfc']) ? utf8_encode($data['rfc']) : utf8_encode('SIN R.F.C.'));
			$tpl->assign('empleado_curp', isset($data['curp']) ? utf8_encode($data['curp']) : utf8_encode('SIN C.U.R.P.'));
			$tpl->assign('sexo', utf8_encode($sexo));

			switch($data['estado_civil']) {
				case 1:
					$estado_civil = 'SOLTERO(A)';
				break;

				case 2:
					$estado_civil = 'CASADO(A)';
				break;

				case 3:
					$estado_civil = 'VIUDO(A)';
				break;

				case 4:
					$estado_civil = 'SEPARADO(A)';
				break;

				case 5:
					$estado_civil = 'DIVORCIADO(A)';
				break;

				case 6:
					$estado_civil = 'UNION LIBRE';
				break;

				default:
					$estado_civil = 'SIN DEFINIR';
			};

			$tpl->assign('estado_civil', utf8_encode($estado_civil));
			$tpl->assign('edad', utf8_encode($edad));
			$tpl->assign('empleado_domicilio', utf8_encode($domicilio));

			$puesto = $db->query("SELECT descripcion FROM catalogo_puestos WHERE cod_puestos = {$data['puesto']}");

			$tpl->assign('puesto', utf8_encode($puesto[0]['descripcion']));

			$tpl->assign('fecha_inicio', $data['fecha_inicio']);
			$tpl->assign('fecha_termino', isset($data['fecha_termino']) ? $data['fecha_termino'] : '');
			$tpl->assign('hora_inicio', isset($data['hora_inicio']) ? $data['hora_inicio'] : '');
			$tpl->assign('hora_termino', isset($data['hora_termino']) ? $data['hora_termino'] : '');
			$tpl->assign('salario', $data['salario']);
			$tpl->assign('salario_escrito', utf8_encode(num2string(get_val($data['salario']))));

			$pieces = explode('/', $data['fecha_inicio']);

			$tpl->assign('dia', $pieces[0]);
			$tpl->assign('mes', utf8_encode($_meses[intval($pieces[1], 10)]));
			$tpl->assign('anio', $pieces[2]);

			if (isset($data['num_cia_sec']) && $data['num_cia_sec'] > 0) {
				$tpl->assign('contrato.salto', '<br style="page-break-after:always;" />');

				$cia = $db->query("SELECT nombre, direccion AS domicilio FROM catalogo_companias WHERE num_cia = {$data['num_cia_sec']}");

				$tpl->newBlock('contrato');

				$pieces = explode('/', $data['fecha_nacimiento']);

				$tsemp = mktime(0, 0, 0, $pieces[1], $pieces[0], date('Y'));

				$edad = date('Y') - $pieces[2] - (time() - $tsemp < 0 ? 1 : 0);

				$nombre = implode(' ', array(
					$data['nombre'],
					$data['ap_paterno'],
					isset($data['ap_materno']) ? $data['ap_materno'] : ''
				));

				$domicilio = implode(', ', array(
					$data['calle'],
					isset($data['colonia']) ? 'COL. ' . $data['colonia'] : '',
					$data['municipio'],
					$data['estado'],
					isset($data['codigo_postal']) ? 'CP. ' . $data['codigo_postal'] : ''
				));

				$sexo = $data['sexo'] == 'FALSE' ? 'MASCULINO' : 'FEMENINO';

				$tpl->assign('cia_nombre', utf8_encode($cia[0]['nombre']));
				$tpl->assign('cia_domicilio', utf8_encode($cia[0]['domicilio']));
				$tpl->assign('empleado_nombre', utf8_encode($nombre));
				$tpl->assign('sexo', utf8_encode($sexo));

				switch($data['estado_civil']) {
					case 1:
						$estado_civil = 'SOLTERO(A)';
					break;

					case 2:
						$estado_civil = 'CASADO(A)';
					break;

					case 3:
						$estado_civil = 'VIUDO(A)';
					break;

					case 4:
						$estado_civil = 'SEPARADO(A)';
					break;

					case 5:
						$estado_civil = 'DIVORCIADO(A)';
					break;

					case 6:
						$estado_civil = 'UNION LIBRE';
					break;

					default:
						$estado_civil = 'SIN DEFINIR';
				};

				$tpl->assign('estado_civil', utf8_encode($estado_civil));
				$tpl->assign('edad', $edad);
				$tpl->assign('empleado_domicilio', utf8_encode($domicilio));

				$puesto = $db->query("SELECT descripcion FROM catalogo_puestos WHERE cod_puestos = {$data['puesto']}");

				$tpl->assign('puesto', utf8_encode($puesto[0]['descripcion']));

				$tpl->assign('fecha_inicio', $data['fecha_inicio']);
				$tpl->assign('fecha_termino', isset($data['fecha_termino']) ? $data['fecha_termino'] : '');
				$tpl->assign('hora_inicio', isset($data['hora_inicio']) ? $data['hora_inicio'] : '');
				$tpl->assign('hora_termino', isset($data['hora_termino']) ? $data['hora_termino'] : '');
				$tpl->assign('salario', $data['salario']);
				$tpl->assign('salario_escrito', utf8_encode(num2string(get_val($data['salario']))));

				$pieces = explode('/', $data['fecha_inicio']);

				$tpl->assign('dia', $pieces[0]);
				$tpl->assign('mes', utf8_encode($_meses[intval($pieces[1], 10)]));
				$tpl->assign('anio', $pieces[2]);
			}

			$tpl->printToScreen();

			$html = $tpl->getOutputContent();

			if ($fp = fopen("contratos/contrato-" . str_pad($folio, 10, '0', STR_PAD_LEFT) . "-{$data['id']}-" . date('Ymdhms') . ".html", "w"))
			{
				fwrite($fp, $html);

				fclose($fp);
			}


			$sql = "UPDATE catalogo_trabajadores
			SET
				ts_elaboracion_contrato = NOW(),
				iduser_elaboracion_contrato = {$_SESSION['iduser']}
			WHERE
				id = {$data['id']}";

			$db->query($sql);
		break;

		case 'alta':
			$nombre_completo = implode(' ', array(
				(isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : ''),
				(isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : ''),
				(isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '')
			));

			/*
			@ Obtener número de empleado disponible
			*/
			$result = $db->query("SELECT
				num_emp
			FROM
				catalogo_trabajadores
			WHERE
				fecha_baja IS NULL
				AND num_cia BETWEEN " . ($_REQUEST['num_cia'] >= 900 ? '900 AND 998' : '1 AND 899') . "
			ORDER BY
				num_emp");

			$num_emp = 1;
			foreach ($result as $reg) {
				if ($num_emp == $reg['num_emp']) {
					$num_emp++;
				}
				else {
					break;
				}
			}

			$sql = "INSERT INTO catalogo_trabajadores (
				num_cia,
				num_cia_sec,
				num_cia_emp,
				nombre,
				ap_paterno,
				ap_materno,
				rfc,
				curp,
				sexo,
				fecha_nac,
				estado_civil,
				calle,
				colonia,
				del_mun,
				entidad,
				cod_postal,
				email,
				cod_puestos,
				cod_turno,
				fecha_inicio_contrato,
				fecha_termino_contrato,
				hora_inicio,
				hora_termino,
				salario,
				doc_acta_nacimiento,
				doc_comprobante_domicilio,
				doc_curp,
				doc_ife,
				doc_num_seguro_social,
				doc_solicitud_trabajo,
				doc_comprobante_estudios,
				doc_referencias,
				doc_no_antecedentes_penales,
				doc_licencia_manejo,
				fecha_vencimiento_licencia_manejo,
				doc_rfc,
				doc_no_adeudo_infonavit,
				firma_contrato,
				fecha_alta,
				nombre_completo,
				num_emp,
				idalta,
				tsalta
			) VALUES (
				{$_REQUEST['num_cia']},
				" . (isset($_REQUEST['num_cia_sec']) ? $_REQUEST['num_cia_sec'] : 'NULL') . ",
				" . (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ",
				'" . (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '') . "',
				'" . (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '') . "',
				'" . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . "',
				'" . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '') . "',
				'" . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . "',
				{$_REQUEST['sexo']},
				" . (isset($_REQUEST['fecha_nacimiento']) ? "'{$_REQUEST['fecha_nacimiento']}'" : 'NULL') . ",
				{$_REQUEST['estado_civil']},
				'" . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . "',
				'" . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . "',
				'" . (isset($_REQUEST['municipio']) ? utf8_decode($_REQUEST['municipio']) : '') . "',
				'" . (isset($_REQUEST['estado']) ? utf8_decode($_REQUEST['estado']) : '') . "',
				'" . (isset($_REQUEST['codigo_postal']) ? $_REQUEST['codigo_postal'] : '') . "',
				LOWER('" . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . "'),
				{$_REQUEST['puesto']},
				{$_REQUEST['turno']},
				'{$_REQUEST['fecha_inicio']}',
				" . (isset($_REQUEST['fecha_termino']) ? "'{$_REQUEST['fecha_termino']}'" : 'NULL') . ",
				'{$_REQUEST['hora_inicio']}',
				'{$_REQUEST['hora_termino']}',
				" . (isset($_REQUEST['salario']) && get_val($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ",
				" . (isset($_REQUEST['doc_acta_nacimiento']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_comprobante_domicilio']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_curp']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_ife']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_num_seguro_social']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_solicitud_trabajo']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_comprobante_estudios']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_referencias']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_no_antecedentes_penales']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_licencia_manejo']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? "'{$_REQUEST['fecha_vencimiento_licencia_manejo']}'" : 'NULL') . ",
				" . (isset($_REQUEST['doc_rfc']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['doc_no_adeudo_infonavit']) ? 'TRUE' : 'FALSE') . ",
				" . (isset($_REQUEST['firma_contrato']) ? 'TRUE' : 'FALSE') . ",
				'{$_REQUEST['fecha_inicio']}',
				'{$nombre_completo}',
				{$num_emp},
				{$_SESSION['iduser']},
				NOW()
			)";

			if ($db->query($sql)) {
				$sql = "SELECT last_value FROM catalogo_trabajadores_id_seq";

				$last = $db->query($sql);

				$id = $last[0]['last_value'];

				$sql = "UPDATE catalogo_trabajadores
				SET nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(' ', ap_paterno, ap_materno, nombre), '\s+', ' ', 'g'))
				WHERE
					id = {$id};\n";

				$cambios[] = 'COMPAÑIA: ' . $_REQUEST['num_cia'];
				$cambios[] = 'SEGUNDA COMPAÑIA: ' . (isset($_REQUEST['num_cia_sec']) ? $_REQUEST['num_cia_sec'] : '');
				$cambios[] = 'LABORA EN: ' . (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']);
				$cambios[] = 'NOMBRE: ' . utf8_decode($_REQUEST['nombre']);
				$cambios[] = 'AP.PATERNO: ' . utf8_decode($_REQUEST['ap_paterno']);
				$cambios[] = 'AP.MATERNO: ' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '');
				$cambios[] = 'RFC: ' . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '');
				$cambios[] = 'CURP: ' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '');
				$cambios[] = 'SEXO: ' . $_REQUEST['sexo'];
				$cambios[] = 'FECHA NACIMIENTO: ' . $_REQUEST['fecha_nacimiento'];
				$cambios[] = 'ESTADO CIVIL: ' . $_REQUEST['estado_civil'];
				$cambios[] = 'CALLE: ' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '');
				$cambios[] = 'COLONIA: ' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '');
				$cambios[] = 'DELEGACION/MUNICIPIO: ' . (isset($_REQUEST['municipio']) ? utf8_decode($_REQUEST['municipio']) : '');
				$cambios[] = 'ENTIDAD: ' . (isset($_REQUEST['estado']) ? utf8_decode($_REQUEST['estado']) : '');
				$cambios[] = 'CODIGO POSTAL: ' . (isset($_REQUEST['codigo_postal']) ? utf8_decode($_REQUEST['codigo_postal']) : '');
				$cambios[] = 'EMAIL: ' . (isset($_REQUEST['email']) ? utf8_decode($_REQUEST['email']) : '');
				$cambios[] = 'PUESTO: ' . $_REQUEST['puesto'];
				$cambios[] = 'TURNO: ' . $_REQUEST['turno'];
				$cambios[] = 'FECHA INICIO CONTRATO: ' . $_REQUEST['fecha_inicio'];
				$cambios[] = 'FECHA TERMINO CONTRATO: ' . (isset($_REQUEST['fecha_termino']) ? $_REQUEST['fecha_termino'] : '');
				$cambios[] = 'HORA INICIO: ' . $_REQUEST['hora_inicio'];
				$cambios[] = 'HORA TERMINO: ' . $_REQUEST['hora_termino'];
				$cambios[] = 'SALARIO: ' . $_REQUEST['salario'];
				$cambios[] = 'DOC. ACTA NACIMIENTO: ' . (isset($_REQUEST['doc_acta_nacimiento']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. COMPROBANTE DOMICILIO: ' . (isset($_REQUEST['doc_comprobante_domicilio']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. CURP: ' . (isset($_REQUEST['doc_curp']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. IFE: ' . (isset($_REQUEST['doc_ife']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. IMSS: ' . (isset($_REQUEST['doc_num_seguro_social']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. SOLICITUD TRABAJO: ' . (isset($_REQUEST['doc_solicitud_trabajo']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. COMPROBANTE ESTUDIOS: ' . (isset($_REQUEST['doc_comprobante_estudios']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. REFERENCIAS: ' . (isset($_REQUEST['doc_referencias']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. NO ANTECEDENTES PENALES: ' . (isset($_REQUEST['doc_no_antecedentes_penales']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. LICENCIA MANEJO: ' . (isset($_REQUEST['doc_licencia_manejo']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'FECHA VENCIMIENTO LICENCIA MANEJO: ' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '');
				$cambios[] = 'DOC. RFC: ' . (isset($_REQUEST['doc_rfc']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'DOC. NO ADEUDO INFONAVIT: ' . (isset($_REQUEST['doc_no_adeudo_infonavit']) ? 'TRUE' : 'FALSE');
				$cambios[] = 'FIRMA CONTRATO: ' . (isset($_REQUEST['firma_contrato']) ? 'TRUE' : 'FALSE');

				$sql .= "INSERT INTO catalogo_trabajadores_log (
					idemp,
					iduser,
					log_description
				) VALUES (
					{$id},
					{$_SESSION['iduser']},
					'ALTA DE TRABAJADOR [RECURSOS HUMANOS]:\n" . implode("\n", $cambios) . "'
				);\n";

				$db->query($sql);

				$data = array(
					'status' => 1,
					'id' => intval($id),
					'num_emp' => $num_emp,
					'nombre_completo' => utf8_encode($nombre_completo)
				);
			}
			else {
				$data = array(
					'status' => 0
				);
			}

			echo json_encode($data);
		break;

		case 'actualizar':
			/*
			@ [07-Nov-2012] Obtener datos actuales del empleado para verificar los cambios y guardarlos en un log
			*/

			$tmp = $db->query("SELECT
				num_cia_emp,
				num_cia_sec,
				num_emp,
				nombre,
				ap_paterno,
				ap_materno,
				rfc,
				curp,
				CASE
					WHEN sexo = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS sexo,
				fecha_nac,
				estado_civil,
				calle,
				colonia,
				del_mun,
				entidad,
				cod_postal,
				email,
				cod_puestos,
				cod_turno,
				fecha_inicio_contrato,
				fecha_termino_contrato,
				LPAD(EXTRACT(HOUR FROM hora_inicio)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_inicio)::VARCHAR, 2, '0') AS hora_inicio,
				LPAD(EXTRACT(HOUR FROM hora_termino)::VARCHAR, 2, '0') || ':' || LPAD(EXTRACT(MINUTE FROM hora_termino)::VARCHAR, 2, '0') AS hora_termino,
				CASE
					WHEN ct.salario > 0 THEN
						ct.salario
					ELSE
						cp.sueldo
				END AS salario,
				CASE
					WHEN doc_acta_nacimiento = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_acta_nacimiento,
				CASE
					WHEN doc_comprobante_domicilio = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_comprobante_domicilio,
				CASE
					WHEN doc_curp = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_curp,
				CASE
					WHEN doc_ife = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_ife,
				CASE
					WHEN doc_num_seguro_social = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_num_seguro_social,
				CASE
					WHEN doc_solicitud_trabajo = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_solicitud_trabajo,
				CASE
					WHEN doc_comprobante_estudios = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END
					AS doc_comprobante_estudios,
				CASE
					WHEN doc_referencias = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_referencias,
				CASE
					WHEN doc_no_antecedentes_penales = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_no_antecedentes_penales,
				CASE
					WHEN doc_licencia_manejo = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_licencia_manejo,
				fecha_vencimiento_licencia_manejo,
				CASE
					WHEN doc_rfc = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_rfc,
				CASE
					WHEN doc_no_adeudo_infonavit = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS doc_no_adeudo_infonavit,
				CASE
					WHEN firma_contrato = TRUE THEN
						'TRUE'
					ELSE
						'FALSE'
				END AS firma_contrato
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_puestos cp USING (cod_puestos)
			WHERE
				id = {$_REQUEST['empleado']}");

			$old = $tmp[0];

			/*
			@ [07-Nov-2012] Validar los cambios hechos en los datos del trabajador
			*/

			$cambios = array();

			if (utf8_encode($old['num_cia_emp']) != (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : '')) {
				$cambios[] = 'LABORA EN: ' . $old['num_cia_emp'] . ' -> ' . (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : '');
			}
			if (utf8_encode($old['num_cia_sec']) != (isset($_REQUEST['num_cia_sec']) ? $_REQUEST['num_cia_sec'] : '')) {
				$cambios[] = 'SEGUNDA COMPAÑIA: ' . $old['num_cia_sec'] . ' -> ' . (isset($_REQUEST['num_cia_sec']) ? $_REQUEST['num_cia_sec'] : '');
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
			if (utf8_encode($old['rfc']) != (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '')) {
				$cambios[] = 'CURP: ' . $old['curp'] . ' -> ' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '');
			}
			if ($old['sexo'] != (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '')) {
				$cambios[] = 'SEXO: ' . $old['sexo'] . ' -> ' . (isset($_REQUEST['sexo']) ? $_REQUEST['sexo'] : '');
			}
			if ($old['fecha_nac'] != (isset($_REQUEST['fecha_nacimiento']) ? $_REQUEST['fecha_nacimiento'] : '')) {
				$cambios[] = 'FECHA NACIMIENTO: ' . $old['fecha_nac'] . ' -> ' . (isset($_REQUEST['fecha_nacimiento']) ? $_REQUEST['fecha_nacimiento'] : '');
			}
			if ($old['estado_civil'] != (isset($_REQUEST['estado_civil']) ? $_REQUEST['estado_civil'] : '')) {
				$cambios[] = 'ESTADO CIVIL: ' . $old['estado_civil'] . ' -> ' . (isset($_REQUEST['estado_civil']) ? $_REQUEST['estado_civil'] : '');
			}
			if (utf8_encode($old['calle']) != (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '')) {
				$cambios[] = 'CALLE: ' . $old['calle'] . ' -> ' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '');
			}
			if (utf8_encode($old['colonia']) != (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '')) {
				$cambios[] = 'COLONIA: ' . $old['colonia'] . ' -> ' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '');
			}
			if (utf8_encode($old['del_mun']) != (isset($_REQUEST['municipio']) ? utf8_decode($_REQUEST['municipio']) : '')) {
				$cambios[] = 'DELEGACION/MUNICIPIO: ' . $old['del_mun'] . ' -> ' . (isset($_REQUEST['municipio']) ? utf8_decode($_REQUEST['municipio']) : '');
			}
			if (utf8_encode($old['entidad']) != (isset($_REQUEST['estado']) ? utf8_decode($_REQUEST['estado']) : '')) {
				$cambios[] = 'ENTIDAD: ' . $old['entidad'] . ' -> ' . (isset($_REQUEST['estado']) ? utf8_decode($_REQUEST['estado']) : '');
			}
			if ($old['cod_postal'] != (isset($_REQUEST['codigo_postal']) ? $_REQUEST['codigo_postal'] : '')) {
				$cambios[] = 'CODIGO POSTAL: ' . $old['cod_postal'] . ' -> ' . (isset($_REQUEST['codigo_postal']) ? $_REQUEST['codigo_postal'] : '');
			}
			if ($old['email'] != (isset($_REQUEST['email']) ? $_REQUEST['email'] : '')) {
				$cambios[] = 'EMAIL: ' . $old['email'] . ' -> ' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '');
			}
			if ($old['cod_puestos'] != (isset($_REQUEST['puesto']) ? $_REQUEST['puesto'] : '')) {
				$cambios[] = 'PUESTO: ' . $old['cod_puestos'] . ' -> ' . (isset($_REQUEST['puesto']) ? $_REQUEST['puesto'] : '');
			}
			if ($old['cod_turno'] != (isset($_REQUEST['turno']) ? $_REQUEST['turno'] : '')) {
				$cambios[] = 'TURNO: ' . $old['cod_turno'] . ' -> ' . (isset($_REQUEST['turno']) ? $_REQUEST['turno'] : '');
			}
			if ($old['fecha_inicio_contrato'] != (isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : '')) {
				$cambios[] = 'FECHA INICIO CONTRATO: ' . $old['fecha_inicio_contrato'] . ' -> ' . (isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : '');
			}
			if ($old['fecha_termino_contrato'] != (isset($_REQUEST['fecha_termino']) ? $_REQUEST['fecha_termino'] : '')) {
				$cambios[] = 'FECHA TERMINO CONTRATO: ' . $old['fecha_termino_contrato'] . ' -> ' . (isset($_REQUEST['fecha_termino']) ? $_REQUEST['fecha_termino'] : '');
			}
			if ($old['hora_inicio'] != (isset($_REQUEST['hora_inicio']) ? $_REQUEST['hora_inicio'] : '')) {
				$cambios[] = 'HORA INICIO: ' . $old['hora_inicio'] . ' -> ' . (isset($_REQUEST['hora_inicio']) ? $_REQUEST['hora_inicio'] : '');
			}
			if ($old['hora_termino'] != (isset($_REQUEST['hora_termino']) ? $_REQUEST['hora_termino'] : '')) {
				$cambios[] = 'HORA TERMINO: ' . $old['hora_termino'] . ' -> ' . (isset($_REQUEST['hora_termino']) ? $_REQUEST['hora_termino'] : '');
			}
			if ($old['salario'] != (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0)) {
				$cambios[] = 'SALARIO: ' . number_format($old['salario'], 2) . ' -> ' . (isset($_REQUEST['salario']) ? $_REQUEST['salario'] : '');
			}
			if ((isset($_REQUEST['doc_acta_nacimiento']) && $old['doc_acta_nacimiento'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_acta_nacimiento']) && $old['doc_acta_nacimiento'] == 'TRUE')) {
				$cambios[] = 'DOC. ACTA NACIMIENTO: ' . $old['doc_acta_nacimiento'] . ' -> ' . (isset($_REQUEST['doc_acta_nacimiento']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_comprobante_domicilio']) && $old['doc_comprobante_domicilio'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_comprobante_domicilio']) && $old['doc_comprobante_domicilio'] == 'TRUE')) {
				$cambios[] = 'DOC. COMPROBANTE DOMICILIO: ' . $old['doc_comprobante_domicilio'] . ' -> ' . (isset($_REQUEST['doc_comprobante_domicilio']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_curp']) && $old['doc_curp'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_curp']) && $old['doc_curp'] == 'TRUE')) {
				$cambios[] = 'DOC. CURP: ' . $old['doc_curp'] . ' -> ' . (isset($_REQUEST['doc_curp']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_ife']) && $old['doc_ife'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_ife']) && $old['doc_ife'] == 'TRUE')) {
				$cambios[] = 'DOC. IFE: ' . $old['doc_ife'] . ' -> ' . (isset($_REQUEST['doc_ife']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_num_seguro_social']) && $old['doc_num_seguro_social'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_num_seguro_social']) && $old['doc_num_seguro_social'] == 'TRUE')) {
				$cambios[] = 'DOC. IMSS: ' . $old['doc_num_seguro_social'] . ' -> ' . (isset($_REQUEST['doc_num_seguro_social']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_solicitud_trabajo']) && $old['doc_solicitud_trabajo'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_solicitud_trabajo']) && $old['doc_solicitud_trabajo'] == 'TRUE')) {
				$cambios[] = 'DOC. SOLICITUD TRABAJO: ' . $old['doc_solicitud_trabajo'] . ' -> ' . (isset($_REQUEST['doc_solicitud_trabajo']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_comprobante_estudios']) && $old['doc_comprobante_estudios'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_comprobante_estudios']) && $old['doc_comprobante_estudios'] == 'TRUE')) {
				$cambios[] = 'DOC. COMPROBANTE ESTUDIOS: ' . $old['doc_comprobante_estudios'] . ' -> ' . (isset($_REQUEST['doc_comprobante_estudios']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_referencias']) && $old['doc_referencias'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_referencias']) && $old['doc_referencias'] == 'TRUE')) {
				$cambios[] = 'DOC. REFERENCIAS: ' . $old['doc_referencias'] . ' -> ' . (isset($_REQUEST['doc_referencias']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_no_antecedentes_penales']) && $old['doc_no_antecedentes_penales'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_no_antecedentes_penales']) && $old['doc_no_antecedentes_penales'] == 'TRUE')) {
				$cambios[] = 'DOC. NO ANTECEDENTES PENALES: ' . $old['doc_no_antecedentes_penales'] . ' -> ' . (isset($_REQUEST['doc_no_antecedentes_penales']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_licencia_manejo']) && $old['doc_licencia_manejo'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_licencia_manejo']) && $old['doc_licencia_manejo'] == 'TRUE')) {
				$cambios[] = 'DOC. LICENCIA MANEJO: ' . $old['doc_licencia_manejo'] . ' -> ' . (isset($_REQUEST['doc_referencias']) ? 'TRUE' : 'FALSE');
			}
			if ($old['fecha_vencimiento_licencia_manejo'] != (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '')) {
				$cambios[] = 'FECHA VENCIMIENTO LICENCIA MANEJO: ' . $old['fecha_vencimiento_licencia_manejo'] . ' -> ' . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? $_REQUEST['fecha_vencimiento_licencia_manejo'] : '');
			}
			if ((isset($_REQUEST['doc_rfc']) && $old['doc_rfc'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_rfc']) && $old['doc_rfc'] == 'TRUE')) {
				$cambios[] = 'DOC. RFC: ' . $old['doc_rfc'] . ' -> ' . (isset($_REQUEST['doc_rfc']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['doc_no_adeudo_infonavit']) && $old['doc_no_adeudo_infonavit'] == 'FALSE')
				|| ( ! isset($_REQUEST['doc_no_adeudo_infonavit']) && $old['doc_no_adeudo_infonavit'] == 'TRUE')) {
				$cambios[] = 'DOC. NO ADEUDO INFONAVIT: ' . $old['doc_no_adeudo_infonavit'] . ' -> ' . (isset($_REQUEST['doc_no_adeudo_infonavit']) ? 'TRUE' : 'FALSE');
			}
			if ((isset($_REQUEST['firma_contrato']) && $old['firma_contrato'] == 'FALSE')
				|| ( ! isset($_REQUEST['firma_contrato']) && $old['firma_contrato'] == 'TRUE')) {
				$cambios[] = 'FIRMA CONTRATO: ' . $old['firma_contrato'] . ' -> ' . (isset($_REQUEST['firma_contrato']) ? 'TRUE' : 'FALSE');
			}

			if ( !! $cambios) {
				$sql = "UPDATE catalogo_trabajadores
				SET
					num_cia_emp = " . (isset($_REQUEST['num_cia_emp']) ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ",
					num_cia_sec = " . (isset($_REQUEST['num_cia_sec']) ? $_REQUEST['num_cia_sec'] : 'NULL') . ",
					nombre = '" . (isset($_REQUEST['nombre']) ? utf8_decode($_REQUEST['nombre']) : '') . "',
					ap_paterno = '" . (isset($_REQUEST['ap_paterno']) ? utf8_decode($_REQUEST['ap_paterno']) : '') . "',
					ap_materno = '" . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . "',
					rfc = '" . (isset($_REQUEST['rfc']) ? utf8_decode($_REQUEST['rfc']) : '') . "',
					curp = '" . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . "',
					sexo = {$_REQUEST['sexo']},
					fecha_nac = " . (isset($_REQUEST['fecha_nacimiento']) ? "'{$_REQUEST['fecha_nacimiento']}'" : 'NULL') . ",
					estado_civil = {$_REQUEST['estado_civil']},
					calle = '" . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . "',
					colonia = '" . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . "',
					del_mun = '" . (isset($_REQUEST['municipio']) ? utf8_decode($_REQUEST['municipio']) : '') . "',
					entidad = '" . (isset($_REQUEST['estado']) ? utf8_decode($_REQUEST['estado']) : '') . "',
					cod_postal = '" . (isset($_REQUEST['codigo_postal']) ? $_REQUEST['codigo_postal'] : '') . "',
					email = LOWER('" . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . "'),
					cod_puestos = {$_REQUEST['puesto']},
					cod_turno = {$_REQUEST['turno']},
					fecha_inicio_contrato = '{$_REQUEST['fecha_inicio']}',
					fecha_termino_contrato = " . (isset($_REQUEST['fecha_termino']) ? "'{$_REQUEST['fecha_termino']}'" : 'NULL') . ",
					hora_inicio = '{$_REQUEST['hora_inicio']}',
					hora_termino = '{$_REQUEST['hora_termino']}',
					salario = " . (isset($_REQUEST['salario']) && get_val($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ",
					doc_acta_nacimiento = " . (isset($_REQUEST['doc_acta_nacimiento']) ? 'TRUE' : 'FALSE') . ",
					doc_comprobante_domicilio = " . (isset($_REQUEST['doc_comprobante_domicilio']) ? 'TRUE' : 'FALSE') . ",
					doc_curp = " . (isset($_REQUEST['doc_curp']) ? 'TRUE' : 'FALSE') . ",
					doc_ife = " . (isset($_REQUEST['doc_ife']) ? 'TRUE' : 'FALSE') . ",
					doc_num_seguro_social = " . (isset($_REQUEST['doc_num_seguro_social']) ? 'TRUE' : 'FALSE') . ",
					doc_solicitud_trabajo = " . (isset($_REQUEST['doc_solicitud_trabajo']) ? 'TRUE' : 'FALSE') . ",
					doc_comprobante_estudios = " . (isset($_REQUEST['doc_comprobante_estudios']) ? 'TRUE' : 'FALSE') . ",
					doc_referencias = " . (isset($_REQUEST['doc_referencias']) ? 'TRUE' : 'FALSE') . ",
					doc_no_antecedentes_penales = " . (isset($_REQUEST['doc_no_antecedentes_penales']) ? 'TRUE' : 'FALSE') . ",
					doc_licencia_manejo = " . (isset($_REQUEST['doc_licencia_manejo']) ? 'TRUE' : 'FALSE') . ",
					fecha_vencimiento_licencia_manejo = " . (isset($_REQUEST['fecha_vencimiento_licencia_manejo']) ? "'{$_REQUEST['fecha_vencimiento_licencia_manejo']}'" : 'NULL') . ",
					doc_rfc = " . (isset($_REQUEST['doc_rfc']) ? 'TRUE' : 'FALSE') . ",
					doc_no_adeudo_infonavit = " . (isset($_REQUEST['doc_no_adeudo_infonavit']) ? 'TRUE' : 'FALSE') . ",
					firma_contrato = " . (isset($_REQUEST['firma_contrato']) ? 'TRUE' : 'FALSE') . ",
					idmod = {$_SESSION['iduser']},
					tsmod = NOW()
				WHERE
					id = {$_REQUEST['empleado']};\n";

				$sql .= "UPDATE catalogo_trabajadores
				SET
					nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(' ', ap_paterno, ap_materno, nombre), '\s+', ' ', 'g'))
				WHERE
					id = {$_REQUEST['empleado']};\n";

				$sql .= "INSERT INTO catalogo_trabajadores_log (
					idemp,
					iduser,
					log_description
				) VALUES (
					{$_REQUEST['empleado']},
					{$_SESSION['iduser']},
					'MODIFICACION [RECURSOS HUMANOS]\n" . implode("\n", $cambios) . "');\n";

				$db->query($sql);

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

				$user = $db->query("SELECT username, CONCAT_WS(' ', nombre, apellido) AS nombre FROM auth WHERE iduser = {$_SESSION['iduser']}");

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
		break;

		case 'firma':
			$db->query("UPDATE catalogo_trabajadores
			SET firma_contrato = {$_REQUEST['status']}
			WHERE
				id = {$_REQUEST['id']}");
		break;

		case 'validarListaNegra':
			$sql = "SELECT
				folio,
				'[' || nombre_tipo_baja || '] ' || observaciones AS observaciones
			FROM
				lista_negra_trabajadores
				LEFT JOIN catalogo_tipos_baja USING (idtipobaja)
			WHERE
				tsdel IS NULL
				AND nombre = '" . utf8_decode($_REQUEST['nombre']) . "'
				AND ap_paterno = '" . utf8_decode($_REQUEST['ap_paterno']) . "'
				AND ap_materno = '" . utf8_decode($_REQUEST['ap_materno']) . "'
				AND permite_reingreso = FALSE";

			$result = $db->query($sql);

			if ($result) {
				$result[0]['observaciones'] = utf8_encode($result[0]['observaciones']);

				echo json_encode($result[0]);
			}
		break;

		case 'validarNombre':
			$result = $db->query("SELECT
				ct.num_emp,
				ct.num_cia,
				cc.nombre_corto AS nombre_cia,
				ct.ap_paterno || (CASE WHEN ct.ap_materno IS NOT NULL AND ct.ap_materno <> '' THEN ' ' || ct.ap_materno ELSE '' END) || ' ' || ct.nombre AS nombre_trabajador,
				ct.rfc,
				ct.fecha_alta,
				COALESCE(auth.nombre, '-') AS usuario
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN auth ON (auth.iduser = ct.idalta)
			WHERE
				ct.fecha_baja IS NULL
				AND ct.empleado_especial IS NULL
				AND ct.baja_rh IS NULL
				AND (
					(
						ct.nombre = '" . utf8_decode($_REQUEST['nombre']) . "'
						AND ct.ap_paterno = '" . utf8_decode($_REQUEST['ap_paterno']) . "'
						AND ct.ap_materno = '" . utf8_decode($_REQUEST['ap_materno']) . "'
					)
					" . (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '' ? "OR ct.rfc LIKE '" . substr(utf8_decode($_REQUEST['rfc']), 0, 10) . "%'" : '') . "
				)
				" . (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 ? "AND ct.id <> {$_REQUEST['id']}" : '') . "
			ORDER BY
				ct.fecha_alta,
				ct.num_cia");

			if ($result) {
				foreach ($result as &$rec) {
					$rec['nombre_cia'] = utf8_encode($rec['nombre_cia']);
					$rec['nombre_trabajador'] = utf8_encode($rec['nombre_trabajador']);
					$rec['rfc'] = utf8_encode($rec['rfc']);
					$rec['usuario'] = utf8_encode($rec['usuario']);
				}

				echo json_encode($result);
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/ContratoTrabajador.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
