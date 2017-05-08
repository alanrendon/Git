<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

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
		case 'obtenerArrendador':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arrendador = ' . $_REQUEST['arrendador'];

			$sql = '
				SELECT
					idarrendador,
					nombre_arrendador
				FROM
					rentas_arrendadores
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array(
					'nombre_arrendador' => utf8_encode($result[0]['nombre_arrendador']),
					'arrendatarios' => array()
				);

				$sql = '
					SELECT
						idarrendatario
							AS value,
						LPAD(arrendatario::VARCHAR, 3, \'0\') || \' \' || nombre_arrendatario || \' (\' || alias_arrendatario || \')\'
							AS text
					FROM
						rentas_arrendatarios
					WHERE
						idarrendador = ' . $result[0]['idarrendador'] . '
						AND total > 0
						AND tsbaja IS NULL
					ORDER BY
						nombre_arrendatario
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data['arrendatarios'][] = array(
							'value' => $rec['value'],
							'text'  => utf8_encode($rec['text'])
						);
					}
				}

				echo json_encode($data);
			}
		break;

		case 'obtenerDatosArrendatario':
			$sql = '
				SELECT
					CASE
						WHEN tipo_local = 1 THEN
							\'LOCAL COMERCIAL\'
						WHEN tipo_local = 2 THEN
							\'VIVIEDA\'
					END
						AS tipo_local,
					renta,
					mantenimiento,
					subtotal,
					iva,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((renta * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS iva_renta,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((mantenimiento * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS iva_mantenimiento,
					agua,
					retencion_iva,
					retencion_isr,
					total
				FROM
					rentas_arrendatarios ra
					LEFT JOIN rentas_locales rl
						USING (idlocal)
				WHERE
					idarrendatario = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				$rec = $result[0];

				$rec['tipo_local'] = utf8_encode($rec['renta']);
				$rec['renta'] = floatval($rec['renta']);
				$rec['mantenimiento'] = floatval($rec['mantenimiento']);
				$rec['subtotal'] = floatval($rec['subtotal']);
				$rec['iva'] = floatval($rec['iva']);
				$rec['iva_renta'] = floatval($rec['iva_renta']);
				$rec['iva_mantenimiento'] = floatval($rec['iva_mantenimiento']);
				$rec['agua'] = floatval($rec['agua']);
				$rec['retencion_iva'] = floatval($rec['retencion_iva']);
				$rec['retencion_isr'] = floatval($rec['retencion_isr']);
				$rec['total'] = floatval($rec['total']);

				echo json_encode($rec);
			}
		break;

		case 'registrar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			/*
			@ Generar popup
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaRentaPopup.tpl');
			$tpl->prepare();

			$fac = new FacturasClass();

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
					cuenta_pago,
					condiciones_pago
				FROM
					rentas_arrendatarios ra
					LEFT JOIN rentas_arrendadores ri
						USING (idarrendador)
					LEFT JOIN rentas_locales rl
						USING (idlocal)
				WHERE
					idarrendatario = ' . $_REQUEST['idarrendatario'] . '
				ORDER BY
					emisor,
					arrendador,
					bloque,
					arrendatario
			';

			$result = $db->query($sql);

			$rec = $result[0];

			$fecha = date('d/m/Y'/*, mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])*/);
			$hora = date('H:i');

			$fecha_renta = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));

			$renta = get_val($_REQUEST['renta']);
			$mantenimiento = get_val($_REQUEST['mantenimiento']);
			$subtotal = get_val($_REQUEST['subtotal']);
			$iva = get_val($_REQUEST['iva']);
			$iva_renta = get_val($_REQUEST['iva_renta']);
			$iva_mantenimiento = get_val($_REQUEST['iva_mantenimiento']);
			$agua = get_val($_REQUEST['agua']);
			$retencion_iva = get_val($_REQUEST['retencion_iva']);
			$retencion_isr = get_val($_REQUEST['retencion_isr']);
			$total = get_val($_REQUEST['total']);

			$datos = array(
				'cabecera' => array(
					'num_cia'               => $rec['emisor'],
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
					'observaciones'         => 'POR RENTA DEL INMUEBLE (' . $rec['tipo_local'] . ') UBICADO EN ' . trim($rec['domicilio_local']) . ($rec['cuenta_predial'] != '' ? '. CUENTA DE PREDIAL: ' . $rec['cuenta_predial'] : ''),
					'importe'               => $subtotal + $agua,
					'porcentaje_descuento'  => 0,
					'descuento'             => 0,
					'ieps'                  => 0,
					'porcentaje_iva'        => $iva > 0 ? 16 : 0,
					'importe_iva'           => $iva,
					'aplicar_retenciones'   => $retencion_iva > 0 || $retencion_isr > 0 ? 'S' : 'N',
					'importe_retencion_isr' => $retencion_isr,
					'importe_retencion_iva' => $retencion_iva,
					'total'                 => $total,
					'tipo_pago'             => $rec['tipo_pago'],
					'cuenta_pago'           => $rec['cuenta_pago'],
					'condiciones_pago'      => $rec['condiciones_pago'],
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

			if ($renta > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => $_REQUEST['concepto_renta'] != '' ? (strpos($_REQUEST['concepto_renta'], /*'RENTA DEL MES DE'*/'RENTA DEL ') === FALSE ? '[RENTA] ' : '') . utf8_decode($_REQUEST['concepto_renta']) : /*'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'RENTA DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])),
					'cantidad'         => 1,
					'unidad'           => 'NO APLICA',
					'precio'           => $renta,
					'importe'          => $renta,
					'descuento'        => 0,
					'porcentaje_iva'   => $iva_renta > 0 ? 16 : 0,
					'importe_iva'      => $iva_renta,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}

			if ($mantenimiento > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => $_REQUEST['concepto_mantenimiento'] != '' ? (strpos($_REQUEST['concepto_mantenimiento'], /*'MANTENIMIENTO DEL MES DE'*/'MANTENIMIENTO DEL ') === FALSE ? '[MANTENIMIENTO] ' : '') . utf8_decode($_REQUEST['concepto_mantenimiento']) : /*'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'MANTENIMIENTO DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])),
					'cantidad'         => 1,
					'unidad'           => 'NO APLICA',
					'precio'           => $mantenimiento,
					'importe'          => $mantenimiento,
					'descuento'        => 0,
					'porcentaje_iva'   => $iva_mantenimiento > 0 ? 16 : 0,
					'importe_iva'      => $iva_mantenimiento,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}

			if ($agua > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => 'CUOTA DE RECUPERACION DE AGUA',
					'cantidad'         => 1,
					'unidad'           => 'NO APLICA',
					'precio'           => $agua,
					'importe'          => $agua,
					'descuento'        => 0,
					'porcentaje_iva'   => 0,
					'importe_iva'      => 0,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}

			$status = $fac->generarFactura($_SESSION['iduser'], $rec['emisor'], 2, $datos);

			if ($status < 0) {
				$tpl->newBlock('error');
				$tpl->assign('status', $fac->ultimoError());
			}
			else {
				$tpl->newBlock('comprobante');
				$tpl->assign('filename', $status);

				// $email_status = $fac->enviarEmail(array('jesus.zubizarreta@lecaroz.com'));
				// $email_status = $fac->enviarEmail(array('carlos.candelario@lecaroz.com'));
				$email_status = $fac->enviarEmail(array($rec['email2'], $rec['email3']));

				if ($email_status !== TRUE) {
					$tpl->assign('status', '<div class="red">Error al enviar los comprobantes por correo electr&oacute;nico: "' . $email_status . '", le sugerimos descargar y enviar los archivos manualmente</div>');
				}

				$pieces = explode('-', $status);

				$folio = preg_replace("/\D/", '', $pieces[1]);

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
								' . $_REQUEST['idarrendatario'] . ',
								(
									SELECT
										id
									FROM
										facturas_electronicas
									WHERE
										num_cia = ' . $rec['emisor'] . '
										AND consecutivo = ' . $folio . '
										AND tipo_serie = 2
										AND status = 1
								),
								' . $_REQUEST['tipo_recibo'] . ',
								\'' . $fecha_renta . '\',
								\'' . ($renta > 0 ? $_REQUEST['concepto_renta'] != '' ? (strpos($_REQUEST['concepto_renta'], /*'RENTA DEL MES DE'*/'RENTA DEL ') === FALSE ? '[RENTA] ' : '') . utf8_decode($_REQUEST['concepto_renta']) : /*'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'RENTA DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) : '') . '\',
								\'' . ($mantenimiento > 0 ? $_REQUEST['concepto_mantenimiento'] != '' ? (strpos($_REQUEST['concepto_mantenimiento'], /*'MANTENIMIENTO DEL MES DE'*/'MANTENIMIENTO DEL ') === FALSE ? '[MANTENIMIENTO] ' : '') . utf8_decode($_REQUEST['concepto_mantenimiento']) : /*'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio']*/'MANTENIMIENTO DEL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'])) . ' AL ' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'])) : '') . '\',
								' . $renta . ',
								' . $mantenimiento . ',
								' . $subtotal . ',
								' . $iva . ',
								' . $agua . ',
								' . $retencion_iva . ',
								' . $retencion_isr . ',
								' . $total . ',
								2,
								' . $_SESSION['iduser'] . ',
								(
									SELECT
										tsins
									FROM
										facturas_electronicas
									WHERE
										num_cia = ' . $rec['emisor'] . '
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
						num_cia = ' . $rec['emisor'] . '
						AND consecutivo = ' . $folio . '
						AND tipo_serie = 2
						AND status = 1
				' . ";\n";

				$db->query($sql);
			}

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasFacturasManual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$tpl->printToScreen();
?>
