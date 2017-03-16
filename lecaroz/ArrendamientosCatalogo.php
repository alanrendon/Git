<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ren/ArrendamientosCatalogoInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '1 AND 999');

			$condiciones[] = 'arr.tsbaja IS NULL AND per.tsbaja IS NULL';

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
					idarrendamiento
						AS id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					LPAD(arrendamiento::varchar, 3, \'0\') || \' \' || alias_arrendamiento
						AS arrendamiento,
					nombre_arrendador,
					arr.rfc,
					arr.curp,
					fecha_inicio || \' - \' || fecha_termino
						AS periodo_arrendamiento,
					renta,
					mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total,
					renta_efectivo,
					gran_total
				FROM
					arrendamientos arr
					LEFT JOIN arrendamientos_periodos per
						USING (idarrendamiento)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					arrendamiento
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/ArrendamientosCatalogoConsulta.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('result');

				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$color = FALSE;
					}

					$tpl->newBlock('arrendamiento');

					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('arrendamiento', utf8_encode($rec['arrendamiento']));
					$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
					$tpl->assign('periodo_arrendamiento', $rec['periodo_arrendamiento']);
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('curp', $rec['curp'] ? utf8_encode($rec['curp']) : '&nbsp;');
					$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] > 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('renta_efectivo', $rec['renta_efectivo'] > 0 ? number_format($rec['renta_efectivo'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('gran_total', $rec['gran_total'] > 0 ? number_format($rec['gran_total'], 2, '.', ',') : '&nbsp;');
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ren/ArrendamientosCatalogoAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'obtenerCia':
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_cia']);
			}
		break;

		case 'doAlta':
			$sql = '
				SELECT
					arrendamiento
				FROM
					arrendamientos
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND tsbaja IS NULL
				ORDER BY
					arrendamiento
			';

			$result = $db->query($sql);

			$arrendamiento = 1;

			if ($result) {
				foreach ($result as $rec) {
					if ($arrendamiento != $rec['arrendamiento']) {
						$arrendamiento = $rec['arrendamiento'];

						break;
					}
					else {
						$arrendamiento++;
					}
				}
			}

			$sql = '
				INSERT INTO
					arrendamientos
						(
							num_cia,
							arrendamiento,
							alias_arrendamiento,
							nombre_arrendador,
							rfc,
							curp,
							tipo_persona,
							calle,
							no_exterior,
							no_interior,
							colonia,
							municipio,
							estado,
							pais,
							codigo_postal,
							contacto,
							telefono1,
							telefono2,
							email,
							tsalta,
							idalta
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							' . $arrendamiento . ',
							\'' . utf8_decode($_REQUEST['alias_arrendamiento']) . '\',
							\'' . utf8_decode($_REQUEST['nombre_arrendador']) . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							\'' . utf8_decode($_REQUEST['curp']) . '\',
							' . $_REQUEST['tipo_persona'] . ',
							\'' . utf8_decode($_REQUEST['calle']) . '\',
							\'' . utf8_decode($_REQUEST['no_exterior']) . '\',
							\'' . utf8_decode($_REQUEST['no_interior']) . '\',
							\'' . utf8_decode($_REQUEST['colonia']) . '\',
							\'' . utf8_decode($_REQUEST['municipio']) . '\',
							\'' . utf8_decode($_REQUEST['estado']) . '\',
							\'' . utf8_decode($_REQUEST['pais']) . '\',
							\'' . $_REQUEST['codigo_postal'] . '\',
							\'' . utf8_decode($_REQUEST['contacto']) . '\',
							\'' . $_REQUEST['telefono1'] . '\',
							\'' . $_REQUEST['telefono2'] . '\',
							\'' . utf8_decode($_REQUEST['email']) . '\',
							now(),
							' . $_SESSION['iduser'] . '
						)
			' . ";\n";

			$sql .= '
				INSERT INTO
					arrendamientos_periodos
						(
							idarrendamiento,
							fecha_inicio,
							fecha_termino,
							metodo_pago,
							renta,
							mantenimiento,
							subtotal,
							iva,
							agua,
							retencion_iva,
							retencion_isr,
							total,
							renta_efectivo,
							gran_total,
							tsalta,
							idalta
						)
					VALUES
						(
							(
								SELECT
									last_value
								FROM
									arrendamientos_idarrendamiento_seq
							),
							\'' . $_REQUEST['fecha_inicio'] . '\',
							\'' . $_REQUEST['fecha_termino'] . '\',
							' . $_REQUEST['metodo_pago'] . ',
							' . get_val($_REQUEST['renta']) . ',
							' . get_val($_REQUEST['mantenimiento']) . ',
							' . get_val($_REQUEST['subtotal']) . ',
							' . get_val($_REQUEST['iva']) . ',
							' . get_val($_REQUEST['agua']) . ',
							' . get_val($_REQUEST['retencion_iva']) . ',
							' . get_val($_REQUEST['retencion_isr']) . ',
							' . get_val($_REQUEST['total']) . ',
							' . get_val($_REQUEST['renta_efectivo']) . ',
							' . get_val($_REQUEST['gran_total']) . ',
							NOW(),
							' . $_SESSION['iduser'] . '
						)
			' . ";\n";

			$db->query($sql);
		break;

		case 'modificar':
			$sql = '
				SELECT
					idarrendamiento,
					idarrendamientoperiodo,
					num_cia,
					nombre_corto
						AS nombre_cia,
					alias_arrendamiento,
					nombre_arrendador,
					arr.rfc,
					arr.curp,
					arr.tipo_persona,
					arr.calle,
					arr.no_exterior,
					arr.no_interior,
					arr.colonia,
					arr.municipio,
					arr.estado,
					arr.pais,
					arr.codigo_postal,
					arr.contacto,
					arr.telefono1,
					arr.telefono2,
					arr.email,
					fecha_inicio,
					fecha_termino,
					metodo_pago,
					renta,
					mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total,
					renta_efectivo,
					gran_total,
					observaciones
				FROM
					arrendamientos arr
					LEFT JOIN arrendamientos_periodos per
						USING (idarrendamiento)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					idarrendamiento = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/ren/ArrendamientosCatalogoModificar.tpl');
			$tpl->prepare();

			$tpl->assign('idarrendamiento', $rec['idarrendamiento']);
			$tpl->assign('idarrendamientoperiodo', $rec['idarrendamientoperiodo']);
			$tpl->assign('num_cia', $rec['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
			$tpl->assign('alias_arrendamiento', utf8_encode($rec['alias_arrendamiento']));

			$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
			$tpl->assign('rfc', utf8_encode($rec['rfc']));
			$tpl->assign('curp', utf8_encode($rec['curp']));
			$tpl->assign('tipo_persona_' . $rec['tipo_persona'], ' checked');
			$tpl->assign('calle', utf8_encode($rec['calle']));
			$tpl->assign('no_exterior', utf8_encode($rec['no_exterior']));
			$tpl->assign('no_interior', utf8_encode($rec['no_interior']));
			$tpl->assign('colonia', utf8_encode($rec['colonia']));
			$tpl->assign('municipio', utf8_encode($rec['municipio']));
			$tpl->assign('estado', utf8_encode($rec['estado']));
			$tpl->assign('pais', utf8_encode($rec['pais']));
			$tpl->assign('codigo_postal', $rec['codigo_postal']);

			$tpl->assign('contacto', utf8_encode($rec['contacto']));
			$tpl->assign('telefono1', $rec['telefono1']);
			$tpl->assign('telefono2', $rec['telefono2']);
			$tpl->assign('email', $rec['email']);

			$tpl->assign('fecha_inicio', $rec['fecha_inicio']);
			$tpl->assign('fecha_termino', $rec['fecha_termino']);
			$tpl->assign('metodo_pago_' . $rec['metodo_pago'], ' checked');

			$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'],2 , '.', ',') : '');
			$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'],2 , '.', ',') : '');
			$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'],2 , '.', ',') : '');
			$tpl->assign('aplicar_iva', $rec['iva'] > 0 ? ' checked' : '');
			$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'],2 , '.', ',') : '');
			$tpl->assign('agua', $rec['agua'] > 0 ? number_format($rec['agua'],2 , '.', ',') : '');
			$tpl->assign('aplicar_retencion_iva', $rec['retencion_iva'] > 0 ? ' checked' : '');
			$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'],2 , '.', ',') : '');
			$tpl->assign('aplicar_retencion_isr', $rec['retencion_isr'] > 0 ? ' checked' : '');
			$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'],2 , '.', ',') : '');
			$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'],2 , '.', ',') : '');
			$tpl->assign('renta_efectivo', $rec['renta_efectivo'] > 0 ? number_format($rec['renta_efectivo'],2 , '.', ',') : '');
			$tpl->assign('gran_total', $rec['gran_total'] > 0 ? number_format($rec['gran_total'],2 , '.', ',') : '');

			$tpl->assign('observaciones', utf8_encode($rec['observaciones']));

			echo $tpl->getOutputContent();
		break;

		case 'doModificar':
			$sql = '
				SELECT
					*
				FROM
					arrendamientos_periodos
				WHERE
					idarrendamientoperiodo = ' . $_REQUEST['idarrendamientoperiodo'] . '
			';

			$tmp = $db->query($sql);

			$periodo = $tmp[0];

			$sql = '
				UPDATE
					arrendamientos
				SET
					alias_arrendamiento = \'' . utf8_decode($_REQUEST['alias_arrendamiento']) . '\',
					nombre_arrendador = \'' . utf8_decode($_REQUEST['nombre_arrendador']) . '\',
					rfc = \'' . utf8_decode($_REQUEST['rfc']) . '\',
					curp = \'' . utf8_decode($_REQUEST['curp']) . '\',
					tipo_persona = ' . $_REQUEST['tipo_persona'] . ',
					calle = \'' . utf8_decode($_REQUEST['calle']) . '\',
					no_exterior = \'' . utf8_decode($_REQUEST['no_exterior']) . '\',
					no_interior = \'' . utf8_decode($_REQUEST['no_interior']) . '\',
					colonia = \'' . utf8_decode($_REQUEST['colonia']) . '\',
					municipio = \'' . utf8_decode($_REQUEST['municipio']) . '\',
					estado = \'' . utf8_decode($_REQUEST['estado']) . '\',
					pais = \'' . utf8_decode($_REQUEST['pais']) . '\',
					codigo_postal = \'' . $_REQUEST['codigo_postal'] . '\',
					contacto = \'' . utf8_decode($_REQUEST['contacto']) . '\',
					telefono1 = \'' . $_REQUEST['telefono1'] . '\',
					telefono2 = \'' . $_REQUEST['telefono2'] . '\',
					email = \'' . utf8_decode($_REQUEST['email']) . '\',
					tsmod = now(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					idarrendamiento = ' . $_REQUEST['idarrendamiento'] . '
			' . ";\n";

			if ($periodo['fecha_inicio'] != $_REQUEST['fecha_inicio']
				|| $periodo['fecha_termino'] != $_REQUEST['fecha_termino']) {
					$sql .= '
						UPDATE
							arrendamientos_periodos
						SET
							tsbaja = NOW(),
							idbaja = ' . $_SESSION['iduser'] . '
						WHERE
							idarrendamientoperiodo = ' . $_REQUEST['idarrendamientoperiodo'] . '
					' . ";\n";

					$sql .= '
						INSERT INTO
							arrendamientos_periodos
								(
									idarrendamiento,
									fecha_inicio,
									fecha_termino,
									metodo_pago,
									renta,
									mantenimiento,
									subtotal,
									iva,
									agua,
									retencion_iva,
									retencion_isr,
									total,
									renta_efectivo,
									gran_total,
									observaciones,
									tsalta,
									idalta
								)
							VALUES
								(
									' . $_REQUEST['idarrendamiento'] . ',
									\'' . $_REQUEST['fecha_inicio'] . '\',
									\'' . $_REQUEST['fecha_termino'] . '\',
									' . $_REQUEST['metodo_pago'] . ',
									' . get_val($_REQUEST['renta']) . ',
									' . get_val($_REQUEST['mantenimiento']) . ',
									' . get_val($_REQUEST['subtotal']) . ',
									' . get_val($_REQUEST['iva']) . ',
									' . get_val($_REQUEST['agua']) . ',
									' . get_val($_REQUEST['retencion_iva']) . ',
									' . get_val($_REQUEST['retencion_isr']) . ',
									' . get_val($_REQUEST['total']) . ',
									' . get_val($_REQUEST['renta_efectivo']) . ',
									' . get_val($_REQUEST['gran_total']) . ',
									\'' . utf8_decode($_REQUEST['observaciones']) . '\',
									NOW(),
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
			}
			else {
				$sql .= '
					UPDATE
						arrendamientos_periodos
					SET
						metodo_pago = ' . $_REQUEST['metodo_pago'] . ',
						renta = ' . get_val($_REQUEST['renta']) . ',
						mantenimiento = ' . get_val($_REQUEST['mantenimiento']) . ',
						subtotal = ' . get_val($_REQUEST['subtotal']) . ',
						iva = ' . get_val($_REQUEST['iva']) . ',
						agua = ' . get_val($_REQUEST['agua']) . ',
						retencion_iva = ' . get_val($_REQUEST['retencion_iva']) . ',
						retencion_isr = ' . get_val($_REQUEST['retencion_isr']) . ',
						total = ' . get_val($_REQUEST['total']) . ',
						renta_efectivo = ' . get_val($_REQUEST['renta_efectivo']) . ',
						gran_total = ' . get_val($_REQUEST['gran_total']) . ',
						observaciones = \'' . utf8_decode($_REQUEST['observaciones']) . '\',
						tsmod = NOW(),
						idmod = ' . $_SESSION['iduser'] . '
					WHERE
						idarrendamientoperiodo = ' . $_REQUEST['idarrendamientoperiodo'] . '
				' . ";\n";
			}

			$db->query($sql);
		break;

		case 'doBaja':
			 $sql = '
			 	UPDATE
					arrendamientos
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idarrendamiento = ' . $_REQUEST['id'] . '
			 ' . ";\n";

			 $sql .= '
			 	UPDATE
					arrendamientos_periodos
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idarrendamiento = ' . $_REQUEST['id'] . '
					AND tsbaja IS NULL
			 ' . ";\n";

			 $db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/ArrendamientosCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
