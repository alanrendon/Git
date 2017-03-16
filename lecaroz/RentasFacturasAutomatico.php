<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
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
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ren/RentasFacturasAutomaticoInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected');

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'ri.oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'ra.tsbaja IS NULL';

			$condiciones[] = 'recibo_mensual = TRUE';

			$condiciones[] = 'total > 0';

			if (!isset($_REQUEST['internos'])) {
				$condiciones[] = 'bloque <> 1';
			}

			if (!isset($_REQUEST['externos'])) {
				$condiciones[] = 'bloque <> 2';
			}

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
					$condiciones[] = (isset($_REQUEST['mancomunar']) ? 'homoclave' : 'arrendador') . ' IN (' . implode(', ', $arrendadores) . ')';
				}
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'categoria = ' . $_REQUEST['categoria'];
			}

			$sql = '
				SELECT
					idarrendatario,
					arrendatario,
					alias_arrendatario,
					nombre_arrendatario,
					alias_local,
					bloque,
					homoclave
					arrendador,
					nombre_arrendador,
					fecha_inicio,
					fecha_termino,
					renta,
					mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total,
					CASE
						/**
						* Contrato vencido
						*/
						WHEN bloque > 1 AND (\'01\' || \'/\' || EXTRACT(MONTH FROM fecha_termino) || \'/\' || EXTRACT(YEAR FROM fecha_termino))::DATE < \'' . $fecha . '\'::DATE THEN
							-1
						/**
						* Sin incremento anual
						*/
						WHEN bloque > 1 AND incremento_anual = TRUE AND renta <= COALESCE((
							SELECT
								SUM(renta)
							FROM
								rentas_recibos
							WHERE
								idarrendatario = ra.idarrendatario
								AND fecha = \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR\'
								AND tsbaja IS NULL
						), 0) THEN
							-2
						/**
						* Registrado en sistema
						*/
						WHEN (
							SELECT
								idreciborenta
							FROM
								rentas_recibos
							WHERE
								idarrendatario = ra.idarrendatario
								AND fecha = \'' . $fecha . '\'::DATE
								AND tsbaja IS NULL
								AND tipo_recibo = 1
							LIMIT
								1
						) IS NOT NULL THEN
							1
						/**
						* Pendiente de generar
						*/
						ELSE
							0
					END
						AS status
				FROM
					rentas_arrendatarios ra
					LEFT JOIN rentas_arrendadores ri
						USING (idarrendador)
					LEFT JOIN rentas_locales rl
						USING (idlocal)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					homoclave,
					arrendador,
					bloque,
					orden
			';

			$result = $db->query($sql);

			if ($result) {
				$tpl = new TemplatePower('plantillas/ren/RentasFacturasAutomaticoResultado.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $_REQUEST['anio']);
				$tpl->assign('mes', $_REQUEST['mes']);
				$tpl->assign('mes_escrito', $_meses[$_REQUEST['mes']]);

				$arrendador = NULL;
				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $rec['arrendador']);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));

						$bloque = NULL;
					}

					if ($bloque != $rec['bloque']) {
						$bloque = $rec['bloque'];

						$tpl->newBlock('bloque');
						$tpl->assign('bloque', $rec['bloque'] == 1 ? 'INTERNOS' : 'EXTERNOS');

						$color = FALSE;
					}

					$tpl->newBlock('arrendatario');
					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('id', $rec['idarrendatario']);
					$tpl->assign('arrendador', $arrendador);

					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2) : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2) : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] ? number_format($rec['subtotal'], 2) : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2) : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2) : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2) : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');

					$tpl->assign('disabled', $rec['status'] != 0 ? ' disabled="true"' : '');

					if ($rec['status'] < 0) {
						$tpl->assign('class', 'underline ' . ($rec['status'] == -1 ? 'green' : 'red'));
					}
					else if ($rec['status'] > 0) {
						$tpl->assign('class', 'bold blue');
					}

				}

				echo $tpl->getOutputContent();
			}
		break;

		case 'generar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			$fac = new FacturasClass();

			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$hora = date('H:i');

			/*
			@ Obtener datos para recibos de renta
			*/
			$sql = '
				SELECT
					idarrendatario,
					arrendatario,
					homoclave
						AS emisor,
					arrendador,
					nombre_arrendador,
					bloque,
					CASE
						WHEN tipo_local = 1 THEN
							\'LOCAL COMERCIAL\'
						WHEN tipo_local = 2 THEN
							\'VIVIEDA\'
					END
						AS tipo_local,
					rl.domicilio
						AS domicilio_local,
					rl.cuenta_predial,
					200000 + idarrendatario
						AS clave_cliente,
					nombre_arrendatario
						AS nombre_cliente,
					ra.rfc,
					ra.calle,
					ra.no_exterior,
					ra.no_interior,
					ra.colonia,
					\'\'
						AS localidad,
					\'\'
						AS referencia,
					ra.municipio,
					ra.estado,
					ra.pais,
					ra.codigo_postal,
					ra.email,
					ra.email2,
					ra.email3,
					renta,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((renta * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS iva_renta,
					mantenimiento,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((mantenimiento * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS iva_mantenimiento,
					subtotal,
					iva,
					agua,
					retencion_iva,
					retencion_isr,
					total,
					tipo_pago,
					cuenta_pago
				FROM
					rentas_arrendatarios ra
					LEFT JOIN rentas_arrendadores ri
						USING (idarrendador)
					LEFT JOIN rentas_locales rl
						USING (idlocal)
				WHERE
					idarrendatario IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					emisor,
					arrendador,
					bloque,
					orden
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/RentasFacturasAutomaticoReporte.tpl');
			$tpl->prepare();

			$tpl->assign('anio', $_REQUEST['anio']);
			$tpl->assign('mes_escrito', $_meses[$_REQUEST['mes']]);

			$emisor = NULL;
			foreach ($result as $rec) {
				if ($emisor != $rec['emisor']) {
					$emisor = $rec['emisor'];

					$tpl->newBlock('emisor');
					$tpl->assign('emisor', $emisor);
					$tpl->assign('nombre_emisor', utf8_encode($rec['nombre_arrendador']));

					$color = FALSE;
				}

				$tpl->newBlock('row');
				$tpl->assign('color', $color ? 'on' : 'off');

				$color = !$color;

				$tpl->assign('arrendatario', $rec['arrendatario']);
				$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_cliente']));
				$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2) : '&nbsp;');
				$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('subtotal', $rec['subtotal'] ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');

				$datos = array(
					'cabecera' => array(
						'num_cia'               => $emisor,
						'clasificacion'         => 5,
						'fecha'                 => $fecha,
						'hora'                  => $hora,
						'clave_cliente'         => $rec['clave_cliente'],
						'nombre_cliente'        => $rec['nombre_cliente'],
						'rfc_cliente'           => $rec['rfc'],
						'calle'                 => $rec['calle'],
						'no_exterior'           => $rec['no_exterior'],
						'no_interior'           => $rec['no_interior'],
						'colonia'               => $rec['colonia'],
						'localidad'             => $rec['localidad'],
						'referencia'            => $rec['referencia'],
						'municipio'             => $rec['municipio'],
						'estado'                => $rec['estado'],
						'pais'                  => $rec['pais'],
						'codigo_postal'         => $rec['codigo_postal'],
						'email'                 => $rec['email'],
						'observaciones'         => 'POR RENTA DEL INMUEBLE (' . $rec['tipo_local'] . ') UBICADO EN ' . trim($rec['domicilio_local']) . trim($rec['domicilio_local']) . ($rec['cuenta_predial'] != '' ? '. CUENTA DE PREDIAL: ' . $rec['cuenta_predial'] : ''),
						'importe'               => $rec['subtotal'] + $rec['agua'],
						'porcentaje_descuento'  => 0,
						'descuento'             => 0,
						'ieps'                  => 0,
						'porcentaje_iva'        => $rec['iva'] > 0 ? 16 : 0,
						'importe_iva'           => $rec['iva'],
						'aplicar_retenciones'   => $rec['retencion_isr'] > 0 || $rec['retencion_iva'] > 0 ? 'S' : 'N',
						'importe_retencion_isr' => $rec['retencion_isr'],
						'importe_retencion_iva' => $rec['retencion_iva'],
						'total'                 => $rec['total'],
						'tipo_pago'             => $rec['tipo_pago'],
						'cuenta_pago'           => $rec['cuenta_pago'],
						'cuenta_predial'        => $rec['cuenta_predial']
					),
					'consignatario' => array (
						'nombre'        => '',
						'rfc'           => '',
						'calle'         => '',
						'no_exterior'   => '',
						'no_interior'   => '',
						'colonia'       => '',
						'localidad'     => '',
						'referencia'    => '',
						'municipio'     => '',
						'estado'        => '',
						'pais'          => '',
						'codigo_postal' => ''
					),
					'detalle' => array()
				);

				if ($rec['renta'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						// 'descripcion'      => 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
						'descripcion'      => 'RENTA DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])),
						'cantidad'         => 1,
						'unidad'           => 'NO APLICA',
						'precio'           => $rec['renta'],
						'importe'          => $rec['renta'],
						'descuento'        => 0,
						'porcentaje_iva'   => $rec['iva_renta'] > 0 ? 16 : 0,
						'importe_iva'      => $rec['iva_renta'],
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}

				if ($rec['mantenimiento'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						'descripcion'      => 'MANTENIMIENTO DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])),
						'cantidad'         => 1,
						'unidad'           => 'NO APLICA',
						'precio'           => $rec['mantenimiento'],
						'importe'          => $rec['mantenimiento'],
						'descuento'        => 0,
						'porcentaje_iva'   => $rec['iva_mantenimiento'] > 0 ? 16 : 0,
						'importe_iva'      => $rec['iva_mantenimiento'],
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}

				if ($rec['agua'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						'descripcion'      => 'CUOTA DE RECUPERACION DE AGUA',
						'cantidad'         => 1,
						'unidad'           => 'NO APLICA',
						'precio'           => $rec['agua'],
						'importe'          => $rec['agua'],
						'descuento'        => 0,
						'porcentaje_iva'   => 0,
						'importe_iva'      => 0,
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}

				$status = $fac->generarFactura($_SESSION['iduser'], $emisor, 2, $datos);

				if ($status < 0) {
					$tpl->assign('folio', '&nbsp;');
					$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');
				}
				else {
					$fac->enviarEmail(/*array('jesus.zubizarreta@lecaroz.com')*/array($rec['email2'], $rec['email3']));

					$pieces = explode('-', $status);

					$folio = preg_replace("/\D/", '', $pieces[1]);

					$tpl->assign('folio', $folio);
					$tpl->assign('status', '<span style="color:#060;">OK</span>');

					$sql = '
						INSERT INTO
							rentas_recibos
								(
									idarrendatario,
									idcfd,
									tipo_recibo,
									fecha,
									concepto_renta,
									concepto_mantenimiento,
									renta,
									mantenimiento,
									subtotal,
									iva,
									agua,
									retencion_iva,
									retencion_isr,
									total,
									tipo,
									idalta,
									tsalta
								)
							VALUES
								(
									' . $rec['idarrendatario'] . ',
									(
										SELECT
											id
										FROM
											facturas_electronicas
										WHERE
											num_cia = ' . $emisor . '
											AND consecutivo = ' . $folio . '
											AND tipo_serie = 2
											AND status = 1
									),
									1,
									\'' . $fecha . '\',
									\'' . ($rec['renta'] > 0 ? /*'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'RENTA DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) : '') . '\',
									\'' . ($rec['mantenimiento'] > 0 ? /*'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'MANTENIMIENTO DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) : '') . '\',
									' . $rec['renta'] . ',
									' . $rec['mantenimiento'] . ',
									' . $rec['subtotal'] . ',
									' . $rec['iva'] . ',
									' . $rec['agua'] . ',
									' . $rec['retencion_iva'] . ',
									' . $rec['retencion_isr'] . ',
									' . $rec['total'] . ',
									1,
									' . $_SESSION['iduser'] . ',
									(
										SELECT
											tsins
										FROM
											facturas_electronicas
										WHERE
											num_cia = ' . $emisor . '
											AND consecutivo = ' . $folio . '
											AND tipo_serie = 2
									)
								)
					' . ";\n";

					$sql .= '
						UPDATE
							facturas_electronicas
						SET
							idlocal = ' . $rec['idarrendatario'] . '
						WHERE
							num_cia = ' . $emisor . '
							AND consecutivo = ' . $folio . '
							AND tipo_serie = 2
							AND status = 1
					' . ";\n";

					$db->query($sql);
				}
			}

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasFacturasAutomatico.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
