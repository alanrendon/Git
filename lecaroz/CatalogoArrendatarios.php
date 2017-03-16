<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
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
			$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatariosInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'obtenerArrendatarios':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arr.tsbaja IS NULL';

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
				$inm = array();

				$pieces = explode(',', $_REQUEST['arrendadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$inm[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$inm[] = $piece;
					}
				}

				if (count($inm) > 0) {
					$condiciones[] = 'arrendador IN (' . implode(', ', $inm) . ')';
				}
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'categoria = ' . $_REQUEST['categoria'];
			}

			$sql = '
				SELECT
					idarrendatario
						AS value,
					\'[\' || arrendador || \'][\' || LPAD(arrendatario::varchar, 3, \'0\') || \'] \' || alias_arrendatario
						AS text
				FROM
					rentas_arrendatarios arr
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
					LEFT JOIN rentas_locales loc
						USING (idlocal)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					arrendador,
					arrendatario
			';

			$result = $db->query($sql);

			foreach ($result as &$rec) {
				$rec['text'] = utf8_encode($rec['text']);
			}

			if ($result) {
				echo json_encode($result);
			}
		break;

		case 'consultar':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'inm.oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'cli.tsbaja IS NULL';

			if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
				$arrendadores = array();

				$pieces = explode(',', $_REQUEST['arrendadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$arrendadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$arrendadores[] = $piece;
					}
				}

				if (count($arrendadores) > 0) {
					$condiciones[] = 'arrendador IN (' . implode(', ', $arrendadores) . ')';
				}
			}

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['arrendatarios'])) {
				$condiciones[] = 'idarrendatario IN (' . implode(', ', $_REQUEST['arrendatarios']) . ')';
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'categoria = ' . $_REQUEST['categoria'];
			}

			$sql = '
				SELECT
					idarrendatario
						AS id,
					arrendador,
					nombre_arrendador,
					bloque,
					alias_local
						AS local,
					categoria,
					LPAD(arrendatario::varchar, 3, \'0\') || \' \' || alias_arrendatario
						AS arrendatario,
					nombre_arrendatario,
					rfc,
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
					orden
				FROM
					rentas_arrendatarios cli
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
					LEFT JOIN rentas_locales loc
						USING (idlocal)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			if (isset($_REQUEST['vacios'])) {
				$condiciones = array();

				if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
					$condiciones[] = 'inm.oficina = ' . $_SESSION['tipo_usuario'];
				}

				$condiciones[] = 'loc.tsbaja IS NULL';

				if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
					$arrendadores = array();

					$pieces = explode(',', $_REQUEST['arrendadores']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$arrendadores[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$arrendadores[] = $piece;
						}
					}

					if (count($arrendadores) > 0) {
						$condiciones[] = 'arrendador IN (' . implode(', ', $arrendadores) . ')';
					}
				}

				$condiciones[] = 'idlocal NOT IN (
					SELECT
						idlocal
					FROM
						rentas_arrendatarios
					WHERE
						tsbaja IS NULL
				)';

				$sql .= '
					UNION

					SELECT
						NULL,
						arrendador,
						nombre_arrendador,
						NULL,
						alias_local
							AS local,
						\'<span class="red">LOCAL VACIO</span>\',
						\'\',
						\'\',
						NULL,
						0,
						0,
						0,
						0,
						0,
						0,
						0,
						0,
						0
					FROM
						rentas_locales loc
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						' . implode(' AND ', $condiciones) . '
				';
			}

			$sql .= '
				ORDER BY
					arrendador,
					bloque,
					orden
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatariosConsulta.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('result');

				$arrendador = NULL;
				$i = 0;
				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $rec['arrendador']);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));

						$color = FALSE;

						$bloque = NULL;
					}

					if ($bloque != $rec['bloque']) {
						$bloque = $rec['bloque'];

						$tpl->newBlock('bloque');
						$tpl->assign('i', $i);

						$i++;
					}

					$tpl->newBlock('arrendatario');

					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('flag_color', $rec['bloque'] == 2 ? 'red' : 'blue');
					$tpl->assign('local', utf8_encode($rec['local']));
					$tpl->assign('categoria', $rec['categoria']);
					$tpl->assign('arrendatario', utf8_encode($rec['arrendatario']));
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('periodo_arrendamiento', $rec['periodo_arrendamiento']);
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] > 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('gray', $rec['id'] > 0 ? '' : '_gray');
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'orden':
			$sql = '';

			foreach ($_REQUEST['orden'] as $orden) {
				$data = json_decode($orden);

				$sql .= 'UPDATE rentas_arrendatarios SET orden = ' . $data->orden . ' WHERE idarrendatario = ' . $data->id . ";\n";
			}

			$db->query($sql);
		break;

		case 'reporte':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'inm.oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'cli.tsbaja IS NULL';

			if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
				$arrendadores = array();

				$pieces = explode(',', $_REQUEST['arrendadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$arrendadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$arrendadores[] = $piece;
					}
				}

				if (count($arrendadores) > 0) {
					$condiciones[] = 'arrendador IN (' . implode(', ', $arrendadores) . ')';
				}
			}

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['arrendatarios'])) {
				$condiciones[] = 'idarrendatario IN (' . implode(', ', $_REQUEST['arrendatarios']) . ')';
			}

			$sql = '
				SELECT
					idarrendatario
						AS id,
					arrendador,
					nombre_arrendador,
					bloque,
					arrendatario,
					alias_arrendatario,
					nombre_arrendatario,
					rfc,
					giro,
					fecha_inicio || \' - \' || fecha_termino
						AS periodo_arrendamiento,
					renta,
					mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total
				FROM
					rentas_arrendatarios cli
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
					LEFT JOIN rentas_locales loc
						USING (idlocal)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					arrendador,
					bloque,
					orden
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatariosReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('reporte');

				$arrendador = NULL;
				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $rec['arrendador']);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));

						$totales = array(
							'renta'         => 0,
							'mantenimiento' => 0,
							'subtotal'      => 0,
							'iva'           => 0,
							'agua'          => 0,
							'retencion_iva' => 0,
							'retencion_isr' => 0,
							'total'         => 0
						);
					}

					$tpl->newBlock('arrendatario');

					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('alias_arrendatario', utf8_encode($rec['alias_arrendatario']));
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('periodo_arrendamiento', $rec['periodo_arrendamiento']);
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('giro', utf8_encode($rec['giro']));
					$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] > 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');

					$totales['renta'] += $rec['total'];
					$totales['mantenimiento'] += $rec['mantenimiento'];
					$totales['subtotal'] += $rec['subtotal'];
					$totales['iva'] += $rec['iva'];
					$totales['agua'] += $rec['agua'];
					$totales['retencion_iva'] += $rec['retencion_iva'];
					$totales['retencion_isr'] += $rec['retencion_isr'];
					$totales['total'] += $rec['total'];

					foreach ($totales as $campo => $total) {
						$tpl->assign('arrendador.' . $campo, $total > 0 ? number_format($total, 2) : '&nbsp;');
					}
				}
			}

			$tpl->printToScreen();
		break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatariosAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'obtenerArrendador':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arrendador = ' . $_REQUEST['arrendador'];

			$sql = '
				SELECT
					idarrendador,
					arrendador,
					nombre_arrendador
				FROM
					rentas_arrendadores
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$arrendador = $db->query($sql);

			$arrendador[0]['nombre_arrendador'] = utf8_encode($arrendador[0]['nombre_arrendador']);

			if ($arrendador) {
				$sql = '
					SELECT
						idlocal
							AS value,
						\'[\' || LPAD(local::varchar, 2, \'0\') || \'] \' || alias_local
							AS text
					FROM
						rentas_locales
					WHERE
						idarrendador = ' . $arrendador[0]['idarrendador'] . '
						AND tsbaja IS NULL
						AND (
							idlocal NOT IN (
								SELECT
									idlocal
								FROM
									rentas_arrendatarios
								WHERE
									idarrendador = ' . $arrendador[0]['idarrendador'] . '
									AND tsbaja IS NULL
							)
							' . (isset($_REQUEST['local']) ? 'OR idlocal = ' . $_REQUEST['local'] : '') . '
						)
					ORDER BY
						local
				';

				$locales = $db->query($sql);

				if ($locales) {
					foreach ($locales as &$local) {
						$local['text'] = utf8_encode($local['text']);
					}
				}

				echo json_encode(array_merge($arrendador[0], array('locales' => $locales)));
			}
		break;

		case 'obtenerDatosLocal':
			$sql = '
				SELECT
					tipo_local,
					domicilio,
					superficie
				FROM
					rentas_locales
				WHERE
					idlocal = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$result[0]['domicilio'] = utf8_encode($result[0]['domicilio']);

			echo json_encode($result[0]);
		break;

		case 'doAlta':
			$sql = '
				SELECT
					arrendatario
				FROM
					rentas_arrendatarios
				WHERE
					idarrendador = ' . $_REQUEST['idarrendador'] . '
					AND tsbaja IS NULL
				ORDER BY
					arrendatario
			';

			$result = $db->query($sql);

			$arrendatario = 1;

			if ($result) {
				foreach ($result as $rec) {
					if ($arrendatario == $rec['arrendatario']) {
						$arrendatario++;
					}
					else {
						break;
					}
				}
			}

			$sql = '
				INSERT INTO
					rentas_arrendatarios
						(
							idarrendador,
							idlocal,
							arrendatario,
							bloque,
							alias_arrendatario,
							nombre_arrendatario,
							rfc,
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
							email2,
							email3,
							giro,
							representante,
							fianza,
							tipo_fianza,
							fecha_inicio,
							fecha_termino,
							deposito_garantia,
							recibo_mensual,
							incremento_anual,
							porcentaje_incremento,
							renta,
							mantenimiento,
							subtotal,
							iva,
							agua,
							retencion_iva,
							retencion_isr,
							total,
							tsalta,
							tsmod,
							iduser_alta,
							iduser_mod,
							tipo_pago,
							cuenta_pago,
							condiciones_pago
						)
					VALUES
						(
							' . $_REQUEST['idarrendador'] . ',
							' . $_REQUEST['idlocal'] . ',
							' . $arrendatario . ',
							' . $_REQUEST['bloque'] . ',
							\'' . utf8_decode($_REQUEST['alias_arrendatario']) . '\',
							\'' . utf8_decode($_REQUEST['nombre_arrendatario']) . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							' . $_REQUEST['tipo_persona'] . ',
							\'' . utf8_decode($_REQUEST['calle']) . '\',
							\'' . utf8_decode($_REQUEST['no_exterior']) . '\',
							\'' . utf8_decode($_REQUEST['no_interior']) . '\',
							\'' . utf8_decode($_REQUEST['colonia']) . '\',
							\'' . utf8_decode($_REQUEST['municipio']) . '\',
							\'' . utf8_decode($_REQUEST['estado']) . '\',
							\'' . utf8_decode($_REQUEST['pais']) . '\',
							\'' . utf8_decode($_REQUEST['codigo_postal']) . '\',
							\'' . utf8_decode($_REQUEST['contacto']) . '\',
							\'' . utf8_decode($_REQUEST['telefono1']) . '\',
							\'' . utf8_decode($_REQUEST['telefono2']) . '\',
							\'' . utf8_decode($_REQUEST['email']) . '\',
							\'' . utf8_decode($_REQUEST['email2']) . '\',
							\'' . utf8_decode($_REQUEST['email3']) . '\',
							\'' . utf8_decode($_REQUEST['giro']) . '\',
							\'' . utf8_decode($_REQUEST['representante']) . '\',
							\'' . utf8_decode($_REQUEST['fianza']) . '\',
							\'' . utf8_decode($_REQUEST['tipo_fianza']) . '\',
							\'' . $_REQUEST['fecha_inicio'] . '\',
							\'' . $_REQUEST['fecha_termino'] . '\',
							' . get_val($_REQUEST['deposito_garantia']) . ',
							' . $_REQUEST['recibo_mensual'] . ',
							' . $_REQUEST['incremento_anual'] . ',
							' . get_val($_REQUEST['porcentaje_incremento']) . ',
							' . get_val($_REQUEST['renta']) . ',
							' . get_val($_REQUEST['mantenimiento']) . ',
							' . get_val($_REQUEST['subtotal']) . ',
							' . get_val($_REQUEST['iva']) . ',
							' . get_val($_REQUEST['agua']) . ',
							' . get_val($_REQUEST['retencion_iva']) . ',
							' . get_val($_REQUEST['retencion_isr']) . ',
							' . get_val($_REQUEST['total']) . ',
							now(),
							now(),
							' . $_SESSION['iduser'] . ',
							' . $_SESSION['iduser'] . ',
							\'' . $_REQUEST['tipo_pago'] . '\',
							\'' . $_REQUEST['cuenta_pago'] . '\',
							1
						)
			';

			$db->query($sql);
		break;

		case 'modificar':
			$sql = '
				SELECT
					idarrendatario,
					cli.idarrendador,
					arrendador,
					nombre_arrendador,
					idlocal,
					bloque,
					cli.alias_arrendatario,
					nombre_arrendatario,
					rfc,
					cli.tipo_persona,
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
					email2,
					email3,
					giro,
					cli.representante,
					fianza,
					tipo_fianza,
					fecha_inicio,
					fecha_termino,
					deposito_garantia,
					recibo_mensual,
					incremento_anual,
					porcentaje_incremento,
					renta,
					mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total,
					tipo_pago,
					cuenta_pago
				FROM
					rentas_arrendatarios cli
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
					LEFT JOIN rentas_locales loc
						USING (idlocal)
				WHERE
					idarrendatario = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatariosModificar.tpl');
			$tpl->prepare();

			$tpl->assign('idarrendatario', $rec['idarrendatario']);
			$tpl->assign('idarrendador', $rec['idarrendador']);
			$tpl->assign('arrendador', $rec['arrendador']);
			$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
			$tpl->assign('bloque_' . $rec['bloque'], ' checked');
			$tpl->assign('alias_arrendatario', utf8_encode($rec['alias_arrendatario']));

			$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
			$tpl->assign('rfc', utf8_encode($rec['rfc']));
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
			$tpl->assign('email', utf8_encode($rec['email']));
			$tpl->assign('email2', utf8_encode($rec['email2']));
			$tpl->assign('email3', utf8_encode($rec['email3']));

			$tpl->assign('giro', utf8_encode($rec['giro']));
			$tpl->assign('representante', utf8_encode($rec['representante']));
			$tpl->assign('fianza', utf8_encode($rec['fianza']));
			$tpl->assign('tipo_fianza', utf8_encode($rec['tipo_fianza']));
			$tpl->assign('fecha_inicio', $rec['fecha_inicio']);
			$tpl->assign('fecha_termino', $rec['fecha_termino']);
			$tpl->assign('deposito_garantia', $rec['deposito_garantia']);

			$tpl->assign('tipo_pago_' . $rec['tipo_pago'], ' selected');
			$tpl->assign('cuenta_pago', $rec['cuenta_pago']);
			$tpl->assign('recibo_mensual_' . $rec['recibo_mensual'], ' checked');
			$tpl->assign('incremento_anual_' . $rec['incremento_anual'], ' checked');
			$tpl->assign('porcentaje_incremento', $rec['porcentaje_incremento'] > 0 ? number_format($rec['porcentaje_incremento'], 2 , '.', ',') : '');
			$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'],2 , '.', ',') : '');
			$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'],2 , '.', ',') : '');
			$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'],2 , '.', ',') : '');
			$tpl->assign('aplicar_iva', $rec['iva'] > 0 ? ' checked' : '');
			$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'],2 , '.', ',') : '');
			$tpl->assign('agua', $rec['agua'] > 0 ? number_format($rec['agua'],2 , '.', ',') : '');
			$tpl->assign('aplicar_retenciones', $rec['retencion_iva'] > 0 || $rec['retencion_isr'] > 0 ? ' checked' : '');
			$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'],2 , '.', ',') : '');
			$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'],2 , '.', ',') : '');
			$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'],2 , '.', ',') : '');

			$sql = '
				SELECT
					idlocal
						AS value,
					\'[\' || LPAD(local::varchar, 2, \'0\') || \'] \' || alias_local
						AS text,
					tipo_local
						AS tipo,
					CASE
						WHEN idlocal = ' . $rec['idlocal'] . ' THEN
							\' selected\'
						ELSE
							\'\'
					END
						AS selected,
					CASE
						WHEN tipo_local = 1 THEN
							\'COMERCIAL\'
						ELSE
							\'VIVIENDA\'
					END
						AS tipo_local,
					domicilio,
					superficie
				FROM
					rentas_locales
				WHERE
					idarrendador = ' . $rec['idarrendador'] . '
					AND tsbaja IS NULL
					AND (
						idlocal NOT IN (
							SELECT
								idlocal
							FROM
								rentas_arrendatarios
							WHERE
								idarrendador = ' . $rec['idarrendador'] . '
						)
						OR idlocal = ' . $rec['idlocal'] . '
					)
				ORDER BY
					local
			';

			$locales = $db->query($sql);

			foreach ($locales as $local) {
				$tpl->newBlock('local');
				$tpl->assign('value', $local['value']);
				$tpl->assign('text', $local['text']);
				$tpl->assign('selected', $local['selected']);

				if ($local['selected'] == ' selected') {
					$tpl->assign('_ROOT.tipo', $local['tipo']);
					$tpl->assign('_ROOT.tipo_local', $local['tipo_local']);
					$tpl->assign('_ROOT.domicilio_local', utf8_encode($local['domicilio']));
					$tpl->assign('_ROOT.superficie_local', number_format($local['superficie'], 2, '.', ','));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'doModificar':
			$sql = '
				UPDATE
					rentas_arrendatarios
				SET
					idlocal = ' . $_REQUEST['idlocal'] . ',
					bloque = ' . $_REQUEST['bloque'] . ',
					alias_arrendatario = \'' . utf8_decode($_REQUEST['alias_arrendatario']) . '\',
					nombre_arrendatario = \'' . utf8_decode($_REQUEST['nombre_arrendatario']) . '\',
					rfc = \'' . utf8_decode($_REQUEST['rfc']) . '\',
					tipo_persona = ' . $_REQUEST['tipo_persona'] . ',
					calle = \'' . utf8_decode($_REQUEST['calle']) . '\',
					no_exterior = \'' . utf8_decode($_REQUEST['no_exterior']) . '\',
					no_interior = \'' . utf8_decode($_REQUEST['no_interior']) . '\',
					colonia = \'' . utf8_decode($_REQUEST['colonia']) . '\',
					municipio = \'' . utf8_decode($_REQUEST['municipio']) . '\',
					estado = \'' . utf8_decode($_REQUEST['estado']) . '\',
					pais = \'' . utf8_decode($_REQUEST['pais']) . '\',
					codigo_postal = \'' . utf8_decode($_REQUEST['codigo_postal']) . '\',
					contacto = \'' . utf8_decode($_REQUEST['contacto']) . '\',
					telefono1 = \'' . utf8_decode($_REQUEST['telefono1']) . '\',
					telefono2 = \'' . utf8_decode($_REQUEST['telefono2']) . '\',
					email = \'' . utf8_decode($_REQUEST['email']) . '\',
					email2 = \'' . utf8_decode($_REQUEST['email2']) . '\',
					email3 = \'' . utf8_decode($_REQUEST['email3']) . '\',
					giro = \'' . utf8_decode($_REQUEST['giro']) . '\',
					representante = \'' . utf8_decode($_REQUEST['representante']) . '\',
					fianza = \'' . utf8_decode($_REQUEST['fianza']) . '\',
					tipo_fianza = \'' . utf8_decode($_REQUEST['tipo_fianza']) . '\',
					fecha_inicio = \'' . $_REQUEST['fecha_inicio'] . '\',
					fecha_termino = \'' . $_REQUEST['fecha_termino'] . '\',
					deposito_garantia = \'' . get_val($_REQUEST['deposito_garantia']) . '\',
					recibo_mensual = ' . $_REQUEST['recibo_mensual'] . ',
					incremento_anual = ' . $_REQUEST['incremento_anual'] . ',
					porcentaje_incremento = ' . get_val($_REQUEST['porcentaje_incremento']) . ',
					renta = ' . get_val($_REQUEST['renta']) . ',
					mantenimiento = ' . get_val($_REQUEST['mantenimiento']) . ',
					subtotal = ' . get_val($_REQUEST['subtotal']) . ',
					iva = ' . get_val($_REQUEST['iva']) . ',
					agua = ' . get_val($_REQUEST['agua']) . ',
					retencion_iva = ' . get_val($_REQUEST['retencion_iva']) . ',
					retencion_isr = ' . get_val($_REQUEST['retencion_isr']) . ',
					total = ' . get_val($_REQUEST['total']) . ',
					tsmod = now(),
					iduser_mod = ' . $_SESSION['iduser'] . ',
					tipo_pago = \'' . $_REQUEST['tipo_pago'] . '\',
					cuenta_pago = \'' . $_REQUEST['cuenta_pago'] . '\'
				WHERE
					idarrendatario = ' . $_REQUEST['idarrendatario'] . '
			';

			$db->query($sql);
		break;

		case 'doBaja':
			 $sql = '
			 	UPDATE
					rentas_arrendatarios
				SET
					tsbaja = now(),
					iduser_baja = ' . $_SESSION['iduser'] . '
				WHERE
					idarrendatario = ' . $_REQUEST['id'] . '
			 ';

			 $db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/CatalogoArrendatarios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
