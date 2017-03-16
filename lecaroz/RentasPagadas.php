<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
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

$db = new DBclass($dsn, 'autocommit=yes,encoding=UTF8');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 1, $_REQUEST['anio']));

			$fecha_actual = date('Y') * 12 + date('n');

			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arr.tsbaja IS NULL';

			$condiciones[] = 'arr.bloque = 2';

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

			if (isset($_REQUEST['arrendatarios']) && trim($_REQUEST['arrendatarios']) != '') {
				$arrendatarios = array();

				$pieces = explode(',', $_REQUEST['arrendatarios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$arrendatarios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$arrendatarios[] = $piece;
					}
				}

				if (count($arrendatarios) > 0) {
					$condiciones[] = 'arrendatario IN (' . implode(', ', $arrendatarios) . ')';
				}
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'categoria = ' . $_REQUEST['categoria'];
			}

			$sql = '
				SELECT
					idarrendatario,
					arrendador,
					nombre_arrendador,
					arrendatario,
					alias_arrendatario
						AS nombre_arrendatario,
					tipo_local,
					giro,
					contacto,
					telefono1,
					telefono2,
					email,
					fecha_inicio,
					fecha_termino,
					CASE
						WHEN fecha_termino < NOW()::DATE THEN
							-1
						ELSE
							0
					END
						AS contrato_vencido,
					COALESCE((
						SELECT
							EXTRACT(YEAR FROM fecha)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha)::NUMERIC
						FROM
							rentas_recibos
						WHERE
							idarrendatario = arr.idarrendatario
							AND fecha < arr.fecha_inicio
							AND EXTRACT(YEAR FROM fecha) = EXTRACT(YEAR FROM arr.fecha_inicio) - 1
							AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM arr.fecha_inicio)
							AND tipo_recibo = 1
						ORDER BY
							fecha DESC
						LIMIT
							1
					), 0)
						AS mes_inicio_anterior,
					EXTRACT(YEAR FROM fecha_inicio)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha_inicio)::NUMERIC
						AS mes_inicio,
					EXTRACT(YEAR FROM fecha_termino)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha_termino)::NUMERIC
						AS mes_termino
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
					bloque,
					orden
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/RentasPagadasReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$arrendadores = array();

				foreach ($result as $rec) {
					if (!isset($arrendadores[$rec['arrendador']])) {
						$arrendadores[$rec['arrendador']] = array(
							'nombre'        => utf8_encode($rec['nombre_arrendador']),
							'arrendatarios' => array()
						);
					}

					$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']] = array(
						'id'                  => $rec['idarrendatario'],
						'nombre'              => utf8_encode($rec['nombre_arrendatario']),
						'tipo_local'          => $rec['tipo_local'],
						'giro'                => utf8_encode($rec['giro']),
						'contacto'            => utf8_encode($rec['contacto']),
						'telefono1'           => $rec['telefono1'],
						'telefono2'           => $rec['telefono2'],
						'email'               => $rec['email'],
						'fecha_inicio'        => $rec['fecha_inicio'],
						'fecha_termino'       => $rec['fecha_termino'],
						'contrato_vencido'    => $rec['contrato_vencido'],
						'mes_inicio_anterior' => $rec['mes_inicio_anterior'],
						'mes_inicio'          => $rec['mes_inicio'],
						'mes_termino'         => $rec['mes_termino'],
						'status'              => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), 0),
						'info'                => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), ''),
						'inicio'              => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), FALSE),
						'vencido'             => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), FALSE),
						'asignado'            => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), FALSE)
					);

					foreach ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'] as $mes => &$status) {
						if ($mes < $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_inicio']) {
							$status = 4;
						}

						if ($mes >= $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_inicio']
							&& $mes <= $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_termino']
							&& $mes <= $fecha_actual) {
							$status = -1;
						}

						if ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_inicio_anterior'] == $mes) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['inicio'][$mes] = TRUE;
						}

						if ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_inicio'] == $mes) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['inicio'][$mes] = TRUE;
						}

						if ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['mes_termino'] == $mes) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['vencido'][$mes] = TRUE;
						}
					}
				}

				$sql = "SELECT
					arr.idarrendador,
					inm.arrendador,
					ec.idarrendatario,
					arr.arrendatario,
					EXTRACT(YEAR FROM ec.fecha_renta)::NUMERIC * 12 + EXTRACT(MONTH FROM ec.fecha_renta)::NUMERIC AS mes,
					CASE
						WHEN ec.fecha_con IS NOT NULL THEN
							1
						ELSE
							2
					END AS status,
					COALESCE(CONCAT_WS('<br />', 'FECHA: ' || ec.fecha, (
						CASE
							WHEN ec.fecha_con IS NOT NULL THEN
								'COBRADO: ' || ec.fecha_con
							ELSE
								NULL
						END
					), (
						CASE
							WHEN ec.cuenta = 1 THEN
								'BANCO: BANORTE'
							WHEN ec.cuenta = 2 THEN
								'BANCO: SANTANDER'
							ELSE
								NULL
						END
					), 'IMPORTE: ' || TO_CHAR(ec.importe, '999,999,999.00'), 'COMPROBANTE: ' || COALESCE((SELECT serie FROM facturas_electronicas_series WHERE num_cia = fe.num_cia AND tipo_serie = fe.tipo_serie AND fe.consecutivo BETWEEN folio_inicial AND folio_final), '') || fe.consecutivo)) AS info
				FROM
					estado_cuenta ec
					LEFT JOIN rentas_arrendatarios arr ON (arr.idarrendatario = ec.idarrendatario)
					LEFT JOIN rentas_arrendadores inm ON (inm.idarrendador = arr.idarrendador)
					LEFT JOIN rentas_recibos rec ON (rec.idreciborenta = ec.idreciborenta)
					LEFT JOIN facturas_electronicas fe ON (fe.id = rec.idcfd)
				WHERE
					ec.cod_mov = 2
					AND ec.fecha_renta BETWEEN '{$fecha}'::DATE - INTERVAL '1 YEAR 5 MONTHS' AND '{$fecha}'::DATE
					AND arr.tsbaja IS NULL
				ORDER BY
					ec.idarrendatario,
					mes";

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']])) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'][$rec['mes']] = $rec['status'];
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['info'][$rec['mes']] = $rec['info'];
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['asignado'][$rec['mes']] = TRUE;
						}
					}
				}

				$sql = '
					SELECT
						idarrendador,
						arrendador,
						idarrendatario,
						arrendatario,
						anio::NUMERIC * 12 + mes::NUMERIC
							AS mes,
						status,
						\'IMPUESTO EL \' || DATE_TRUNC(\'second\', est.tsmod) || CASE
							WHEN TRIM(observaciones) != \'\' THEN
								\'<br>OBSERVACIONES: \' || TRIM(observaciones)
							ELSE
								\'\'
						END
							AS info
					FROM
						estatus_locales est
						LEFT JOIN rentas_arrendatarios arr
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						fecha BETWEEN \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR 5 MONTHS\' AND \'' . $fecha . '\'::DATE
						AND arr.tsbaja IS NULL
						AND status IS NOT NULL
						AND (idarrendatario, fecha) NOT IN (
							SELECT
								idarrendatario,
								fecha
							FROM
								estado_cuenta ec
								LEFT JOIN rentas_arrendatarios arr
									USING (idarrendatario)
							WHERE
								cod_mov = 2
								AND fecha_renta BETWEEN \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR 5 MONTHS\' AND \'' . $fecha . '\'::DATE
								AND arr.tsbaja IS NULL
						)
					ORDER BY
						idarrendatario,
						mes
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']])
							&& !$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['asignado'][$rec['mes']]) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'][$rec['mes']] = $rec['status'];
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['info'][$rec['mes']] = $rec['info'];
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['asignado'][$rec['mes']] = TRUE;
						}
					}
				}

				if (isset($_REQUEST['solo_pendientes'])) {
					function filtro($value) {
						return $value < 0;
					}

					foreach ($arrendadores as $arrendador => $datos_arrendador) {
						foreach ($datos_arrendador['arrendatarios'] as $arrendatario => $datos_arrendatario) {
							if (!array_filter($datos_arrendatario['status'], 'filtro')) {
								unset($arrendadores[$arrendador]['arrendatarios'][$arrendatario]);
							}
						}
					}
				}

				$tpl->newBlock('reporte');

				$tpl->assign('anio1', $_REQUEST['anio'] - 1);
				$tpl->assign('anio2', $_REQUEST['anio']);

				foreach ($arrendadores as $arrendador => $datos_arrendador) {
					if ($datos_arrendador['arrendatarios']) {
						$tpl->newBlock('arrendador');

						$tpl->assign('arrendador', $arrendador);
						$tpl->assign('nombre_arrendador', $datos_arrendador['nombre']);

						$tpl->assign('anio1', $_REQUEST['anio'] - 1);
						$tpl->assign('anio2', $_REQUEST['anio']);

						foreach ($datos_arrendador['arrendatarios'] as $arrendatario => $datos_arrendatario) {
							$tpl->newBlock('arrendatario');

							$tpl->assign('id', $datos_arrendatario['id']);
							$tpl->assign('arrendatario', $arrendatario);
							$tpl->assign('nombre_arrendatario', $datos_arrendatario['nombre']);
							$tpl->assign('giro', $datos_arrendatario['giro']);
							$tpl->assign('fecha_termino', $datos_arrendatario['fecha_termino']);
							$tpl->assign('vencido', $datos_arrendatario['contrato_vencido'] < 0 ? ' vencido' : '');

							$info = implode('<br />', array_filter(array(
								($datos_arrendatario['contacto'] != '' ? 'CONTACTO: ' . $datos_arrendatario['contacto'] : NULL),
								($datos_arrendatario['telefono1'] != '' ? 'TELEFONO 1: ' . $datos_arrendatario['telefono1'] : NULL),
								($datos_arrendatario['telefono2'] != '' ? 'TELEFONO 2: ' . $datos_arrendatario['telefono2'] : NULL),
								($datos_arrendatario['email'] != '' ? 'EMAIL: ' . $datos_arrendatario['email'] : NULL),
								($datos_arrendatario['fecha_inicio'] != '' ? '<br />INICIO DE CONTRATO: ' . $datos_arrendatario['fecha_inicio'] : NULL),
								($datos_arrendatario['fecha_termino'] != '' ? 'TERMINO DE CONTRATO: ' . $datos_arrendatario['fecha_termino'] : NULL)
							)));

							$tpl->assign('info', $info);

							$cont = 0;
							foreach ($datos_arrendatario['status'] as $i => $_status) {
								if ($_status == 0) {
									$tpl->assign($cont++, $datos_arrendatario['vencido'][$i] ? '<img src="/lecaroz/imagenes/bloque_blanco_rojo.png" width="24" height="16" />' : '&nbsp;');
								}
								else if ($_status == 1) {
									$tpl->assign($cont++, '<img id="info" src="/lecaroz/imagenes/bloque_negro' . ($datos_arrendatario['inicio'][$i] ? '_amarillo' : '') . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" alt="' . $datos_arrendatario['info'][$i] . '" />');
								}
								else if ($_status == 2) {
									$tpl->assign($cont++, '<img id="info" src="/lecaroz/imagenes/bloque_azul' . ($datos_arrendatario['inicio'][$i] ? '_amarillo' : '') . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" alt="' . $datos_arrendatario['info'][$i] . '" />');
								}
								else if ($_status == 3) {
									$tpl->assign($cont++, '<img id="info" src="/lecaroz/imagenes/bloque_verde' . ($datos_arrendatario['inicio'][$i] ? '_amarillo' : '') . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" alt="' . $datos_arrendatario['info'][$i] . '" />');
								}
								else if ($_status == 4) {
									$tpl->assign($cont++, '<a class="enlace purple bold">VA</a>');
								}
								else if ($_status == -1) {
									$tpl->assign($cont++, $datos_arrendatario['vencido'][$i] ? '<img src="/lecaroz/imagenes/bloque_blanco_rojo.png" width="24" height="16" />' : '&nbsp;');
								}
								else if ($_status == -2) {
									$tpl->assign($cont++, '<a id="info" class="enlace orange bold" alt="' . $datos_arrendatario['info'][$i] . '">DG</a>');
								}

							}
						}
					}
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasPagadas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$tpl->printToScreen();

?>
