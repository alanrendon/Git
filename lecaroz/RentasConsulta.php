<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

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
			$tpl = new TemplatePower('plantillas/ren/RentasConsultaInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected');

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'arrendadores.oficina = ' . $_SESSION['tipo_usuario'];
			}

			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();

				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}

				if (count($anios) > 0) {
					$condiciones[] = 'EXTRACT(YEAR FROM recibos.fecha) IN (' . implode(', ', $anios) . ')';
				}
			}

			if (isset($_REQUEST['meses'])) {
				$condiciones[] = 'EXTRACT(MONTH FROM recibos.fecha) IN (' . implode(', ', $_REQUEST['meses']) . ')';
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
					$condiciones[] = 'arrendadores.arrendador IN (' . implode(', ', $arrendadores) . ')';
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
					$condiciones[] = 'arrendatarios.arrendatario IN (' . implode(', ', $arrendatarios) . ')';
				}
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'locales.categoria = ' . $_REQUEST['categoria'];
			}

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'arrendatarios.bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['tipo_local'])) {
				$condiciones[] = 'locales.tipo_local IN (' . implode(', ', $_REQUEST['tipo_local']) . ')';
			}

			if (isset($_REQUEST['recibos']) && trim($_REQUEST['recibos']) != '') {
				$recibos = array();

				$pieces = explode(',', $_REQUEST['recibos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$recibos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$recibos[] = $piece;
					}
				}

				if (count($recibos) > 0) {
					$condiciones[] = 'fe.consecutivo IN (' . implode(', ', $recibos) . ')';
				}
			}

			if (!isset($_REQUEST['incluir_cancelados'])) {
				$condiciones[] = 'recibos.tsbaja IS NULL';
			}

			$sql = "SELECT
				recibos.idreciborenta,
				recibos.idarrendatario,
				recibos.idcfd,
				recibos.tipo_recibo,
				arrendadores.arrendador,
				arrendadores.nombre_arrendador,
				arrendatarios.arrendatario,
				arrendatarios.nombre_arrendatario,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
						num_cia = arrendadores.homoclave
						AND tipo_serie = 2
						AND fe.consecutivo BETWEEN folio_inicial AND folio_final
				) || fe.consecutivo AS num_fact,
				EXTRACT(YEAR FROM recibos.fecha) AS anio,
				EXTRACT (MONTH FROM recibos.fecha) AS mes,
				recibos.renta,
				recibos.mantenimiento,
				recibos.subtotal,
				recibos.iva,
				recibos.agua,
				recibos.retencion_iva,
				recibos.retencion_isr,
				recibos.total,
				ec.fecha AS fecha_deposito,
				ec.fecha_con AS pagado,
				fe.status
			FROM
				rentas_recibos recibos
				LEFT JOIN rentas_arrendatarios arrendatarios USING (idarrendatario)
				LEFT JOIN rentas_arrendadores arrendadores USING (idarrendador)
				LEFT JOIN rentas_locales locales USING (idlocal)
				LEFT JOIN facturas_electronicas fe ON (fe.id = idcfd)
				LEFT JOIN estado_cuenta ec USING (idarrendatario, idreciborenta)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				arrendadores.arrendador,
				anio,
				mes,
				arrendatarios.arrendatario,
				num_fact";

			$result = $db->query($sql);

			if ($result) {
				$tpl = new TemplatePower('plantillas/ren/RentasConsultaResultado.tpl');
				$tpl->prepare();

				$arrendador = NULL;

				$total = 0;

				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $arrendador);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));

						$anio = NULL;
						$mes = NULL;

						$total_arrendador = 0;
					}

					if ($anio != $rec['anio'] || $mes != $rec['mes']) {
						$anio = $rec['anio'];
						$mes = $rec['mes'];

						$tpl->newBlock('mes');

						$tpl->assign('mes', $_meses[$mes]);
						$tpl->assign('anio', $anio);

						$total_mes = 0;

						$row_color = FALSE;
					}

					$tpl->newBlock('recibo');
					$tpl->assign('row_color', $rec['status'] != 0 ? ($row_color ? 'on' : 'off') : 'red');
					$tpl->assign('id', $rec['idreciborenta']);
					$tpl->assign('arrendador', $arrendador);
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2) : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2) : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] != 0 ? number_format($rec['subtotal'], 2) : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2) : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2) : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2) : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');
					$tpl->assign('pagado', $rec['pagado'] != '' ? $rec['pagado'] : '&nbsp;');

					$tpl->assign('refresh_icon', in_array($_SESSION['iduser'], array(1, 4, 10)) && $rec['fecha_deposito'] == '' && date('w') == 0 ? 'refresh' : 'refresh_gray');
					$tpl->assign('cancel_icon', $rec['status'] != 0 && in_array($_SESSION['iduser'], array(1, 4, 10)) && $rec['fecha_deposito'] == ''/* && date('w') == 0*/ ? 'cancel_round' : 'cancel_round_gray');

					$total_mes += $rec['total'];
					$total_arrendador += $rec['total'];
					$total += $rec['total'];

					$tpl->assign('mes.total', number_format($total_mes, 2));
					$tpl->assign('arrendador.total', number_format($total_arrendador, 2));

					$row_color = !$row_color;
				}

				$tpl->assign('_ROOT.total', number_format($total, 2));

				echo $tpl->getOutputContent();
			}
		break;

		case 'visualizar':
			$path = 'facturas/comprobantes_pdf/';

			$sql = '
				SELECT
					num_cia,
					num_cia || \'-\' || COALESCE((
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
								num_cia = fe.num_cia
							AND
								tipo_serie = fe.tipo_serie
							AND
								fe.consecutivo BETWEEN folio_inicial AND folio_final
					), \'\') || consecutivo || \'.pdf\'
						AS
							filename
				FROM
					facturas_electronicas fe
				WHERE
					id = (
						SELECT
							idcfd
						FROM
							rentas_recibos
						WHERE
							idreciborenta = ' . $_REQUEST['id'] . '
					)
			';
			$result = $db->query($sql);

			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="factura.pdf"');

			readfile($path . $result[0]['num_cia'] . '/' . $result[0]['filename']);
		break;

		case 'imprimir':
			$path = 'facturas/comprobantes_pdf/';

			$sql = '
				SELECT
					num_cia,
					num_cia || \'-\' || COALESCE((
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
								num_cia = fe.num_cia
							AND
								tipo_serie = fe.tipo_serie
							AND
								fe.consecutivo BETWEEN folio_inicial AND folio_final
					), \'\') || consecutivo || \'.pdf\'
						AS
							filename
				FROM
					facturas_electronicas fe
				WHERE
					id = (
						SELECT
							idcfd
						FROM
							rentas_recibos
						WHERE
							idreciborenta = ' . $_REQUEST['id'] . '
					)
			';

			$result = $db->query($sql);

			$printer = $_SESSION['tipo_usuario'] == 2 ? 'elite' : 'general';

			shell_exec('lp -d ' . $printer . ' ' . $path . $result[0]['num_cia'] . '/' . $result[0]['filename']);
		break;

		case 'email':
			$sql = '
				SELECT
					email_cliente
						AS
							email
				FROM
					facturas_electronicas
				WHERE
					id = (
						SELECT
							idcfd
						FROM
							rentas_recibos
						WHERE
							idreciborenta = ' . $_REQUEST['id'] . '
					)
			';
			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/RentasConsultaEmail.tpl');
			$tpl->prepare();

			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('email', $result[0]['email']);

			echo $tpl->getOutputContent();
		break;

		case 'enviarEmail':
			$sql = '
				SELECT
					num_cia,
					nombre
						AS nombre_cia,
					(
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					)
						AS serie,
					consecutivo
				FROM
					facturas_electronicas fe
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					id = (
						SELECT
							idcfd
						FROM
							rentas_recibos
						WHERE
							idreciborenta = ' . $_REQUEST['id'] . '
					)
			';
			$result = $db->query($sql);
			$rec = $result[0];

			$mail = new PHPMailer();

			if ($rec['num_cia'] >= 900) {
				$mail->IsSMTP();
				$mail->Host = 'mail.zapateriaselite.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'facturas.electronicas@zapateriaselite.com';
				$mail->Password = 'facturaselectronicas';

				$mail->From = 'facturas.electronicas@zapateriaselite.com';
				$mail->FromName = 'Zapaterías Elite :: Facturación Electrónica';
			}
			else {
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'facturas.electronicas@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'facturas.electronicas@lecaroz.com';
				$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
			}

			foreach ($_REQUEST['email'] as $email) {
				$mail->AddAddress($email);
			}

			$mail->Subject = 'COMPROBANTE FISCAL DIGITAL :: ' . $rec['consecutivo'];
			$mail->Body = '<p><strong>COMPROBANTE FISCAL DIGITAL :: ' . $rec['nombre_cia'] . '</strong></p><p>Comprobante no. ' . $rec['consecutivo'] . '</p><hr><p style="font-weight:bold;font-size:10pt;">Favor de no responder a este correo electr&oacute;nico. Este buz&oacute;n no se supervisa y no recibir&aacute; respuesta. Si necesita ayuda, escriba al correo <a href="mailto:' . ($rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '">' . ($rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '</a> y con gusto le atenderemos. </p>';
			$mail->IsHTML(true);

			$lcomprobantes_xml = 'facturas/comprobantes_xml/';
			$lcomprobantes_pdf = 'facturas/comprobantes_pdf/';
			$file_name = $rec['num_cia'] . '-' . $rec['serie'] . $rec['consecutivo'];

			$mail->AddAttachment($lcomprobantes_pdf . $rec['num_cia'] . '/' . $file_name . '.pdf');
			$mail->AddAttachment($lcomprobantes_xml . $rec['num_cia'] . '/' . $file_name . '.xml');

			$tpl = new TemplatePower('plantillas/ren/RentasConsultaEmailStatus.tpl');
			$tpl->prepare();

			if(!$mail->Send()) {
				$tpl->assign('status', '<span class="red">No se pudo enviar el comprobante a todos los destinatarios: ' . $mail->ErrorInfo . '</span>');
			}
			else {
				$tpl->assign('status', '<span class="blue">Comprobante enviado a todos los destinatarios</span>');
			}

			echo $tpl->getOutputContent();
		break;

		case 'reimpresion':
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 28))) {
				echo -1;
			}
			else {
				$sql = '
					SELECT
						idreciborenta,
						idarrendatario,
						idcfd,
						arrendador,
						nombre_arrendador,
						arrendatario,
						nombre_arrendatario,
						EXTRACT(YEAR FROM fecha)
							AS anio,
						EXTRACT(MONTH FROM fecha)
							AS mes,
						COALESCE(concepto_renta, (
							SELECT
								descripcion
							FROM
								facturas_electronicas_detalle
							WHERE
								(num_cia, tipo_serie, consecutivo) IN (
									SELECT
										num_cia,
										tipo_serie,
										consecutivo
									FROM
										facturas_electronicas
									WHERE
										id = rec.idcfd
								)
								AND descripcion LIKE \'%RENTA%\'
						), \'\')
							AS concepto_renta,
						COALESCE(concepto_mantenimiento, (
							SELECT
								descripcion
							FROM
								facturas_electronicas_detalle
							WHERE
								(num_cia, tipo_serie, consecutivo) IN (
									SELECT
										num_cia,
										tipo_serie,
										consecutivo
									FROM
										facturas_electronicas
									WHERE
										id = rec.idcfd
								)
								AND descripcion LIKE \'%MANTENIMIENTO%\'
						), \'\')
							AS concepto_mantenimiento,
						tipo_recibo,
						rec.renta,
						rec.mantenimiento,
						ROUND((rec.renta * 0.16)::NUMERIC, 2)
							AS iva_renta,
						ROUND((rec.mantenimiento * 0.16)::NUMERIC, 2)
							AS iva_mantenimiento,
						rec.subtotal,
						rec.iva,
						rec.agua,
						rec.retencion_iva,
						rec.retencion_isr,
						rec.total
					FROM
						rentas_recibos rec
						LEFT JOIN rentas_arrendatarios arr
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						idreciborenta = ' . $_REQUEST['id'] . '
				';

				$result = $db->query($sql);

				$rec = $result[0];

				$tpl = new TemplatePower('plantillas/ren/RentasConsultaReimpresion.tpl');
				$tpl->prepare();

				$tpl->assign('idreciborenta', $_REQUEST['id']);
				$tpl->assign('idarrendatario', $rec['idarrendatario']);
				$tpl->assign('idcfd', $rec['idcfd']);

				$tpl->assign('arrendador', $rec['arrendador']);
				$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
				$tpl->assign('arrendatario', $rec['arrendatario']);
				$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));

				$tpl->assign('anio', $rec['anio']);
				$tpl->assign($rec['mes'], ' selected');
				$tpl->assign('concepto_renta', utf8_encode($rec['concepto_renta']));
				$tpl->assign('concepto_mantenimiento', utf8_encode($rec['concepto_mantenimiento']));
				$tpl->assign('tipo_recibo_' . $rec['tipo_recibo'], ' checked');

				$tpl->assign('renta', $rec['renta'] > 0 ? number_format($rec['renta'], 2) : '');
				$tpl->assign('mantenimiento', $rec['mantenimiento'] > 0 ? number_format($rec['mantenimiento'], 2) : '');
				$tpl->assign('subtotal', $rec['subtotal'] > 0 ? number_format($rec['subtotal'], 2) : '');
				$tpl->assign('iva_renta', $rec['iva_renta']);
				$tpl->assign('iva_mantenimiento', $rec['iva_mantenimiento']);
				$tpl->assign('iva', $rec['iva'] > 0 ? number_format($rec['iva'], 2) : '');
				$tpl->assign('aplicar_iva', $rec['iva'] > 0 ? 'checked' : '');
				$tpl->assign('retencion_iva', $rec['retencion_iva'] > 0 ? number_format($rec['retencion_iva'], 2) : '');
				$tpl->assign('retencion_isr', $rec['retencion_isr'] > 0 ? number_format($rec['retencion_isr'], 2) : '');
				$tpl->assign('aplicar_retenciones', $rec['retencion_iva'] > 0 || $rec['retencion_isr'] > 0 ? 'checked' : '');
				$tpl->assign('total', $rec['total'] > 0 ? number_format($rec['total'], 2) : '');

				echo $tpl->getOutputContent();
			}
		break;

		case 'registrar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			/*
			@ Generar popup
			*/
			$tpl = new TemplatePower('plantillas/ren/RentasConsultaReimpresionPopup.tpl');
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
					total
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

			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$hora = date('H:i');

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
					'observaciones'         => 'POR RENTA DEL INMUEBLE (' . $rec['tipo_local'] . ') UBICADO EN ' . trim($rec['domicilio_local']),
					'importe'               => $subtotal + $agua,
					'porcentaje_descuento'  => 0,
					'descuento'             => 0,
					'porcentaje_iva'        => $iva > 0 ? 16 : 0,
					'importe_iva'           => $iva,
					'aplicar_retenciones'   => $retencion_iva > 0 || $retencion_isr > 0 ? 'S' : 'N',
					'importe_retencion_isr' => $retencion_isr,
					'importe_retencion_iva' => $retencion_iva,
					'total'                 => $total
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
					'descripcion'      => $_REQUEST['concepto_renta'] != '' ? (strpos($_REQUEST['concepto_renta'], 'RENTA DEL MES DE') === FALSE ? '[RENTA] ' : '') . utf8_decode($_REQUEST['concepto_renta']) : 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
					'cantidad'         => 1,
					'unidad'           => 'SIN UNIDAD',
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
					'descripcion'      => $_REQUEST['concepto_mantenimiento'] != '' ? (strpos($_REQUEST['concepto_mantenimiento'], 'MANTENIMIENTO DEL MES DE') === FALSE ? '[MANTENIMIENTO] ' : '') . utf8_decode($_REQUEST['concepto_mantenimiento']) : 'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
					'cantidad'         => 1,
					'unidad'           => 'SIN UNIDAD',
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
					'unidad'           => 'SIN UNIDAD',
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
								),
								' . $_REQUEST['tipo_recibo'] . ',
								\'' . $fecha . '\',
								\'' . ($renta > 0 ? $_REQUEST['concepto_renta'] != '' ? (strpos($_REQUEST['concepto_renta'], 'RENTA DEL MES DE') === FALSE ? '[RENTA] ' : '') . utf8_decode($_REQUEST['concepto_renta']) : 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'] : '') . '\',
								\'' . ($mantenimiento > 0 ? $_REQUEST['concepto_mantenimiento'] != '' ? (strpos($_REQUEST['concepto_mantenimiento'], 'MANTENIMIENTO DEL MES DE') === FALSE ? '[MANTENIMIENTO] ' : '') . utf8_decode($_REQUEST['concepto_mantenimiento']) : 'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'] : '') . '\',
								' . $renta . ',
								' . $mantenimiento . ',
								' . $subtotal . ',
								' . $iva . ',
								' . $agua . ',
								' . $retencion_iva . ',
								' . $retencion_isr . ',
								' . $total . ',
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

				/*$sql .= '
					UPDATE
						estado_cuenta
					SET
						idreciborenta = result.idreciborenta
					FROM (
						SELECT
							idreciborenta
						FROM
							rentas_recibos
						WHERE
							idcfd = (
								SELECT
									id
								FROM
									facturas_electronicas
								WHERE
									num_cia = ' . $rec['emisor'] . '
									AND consecutivo = ' . $folio . '
									AND tipo_serie = 2
							)
					) result
					WHERE
						idreciborenta = ' . $_REQUEST['idreciborenta'] . '
				' . ";\n";*/

				$sql .= '
					UPDATE
						facturas_electronicas
					SET
						idlocal = ' . $rec['idarrendatario'] . '
					WHERE
						num_cia = ' . $rec['emisor'] . '
						AND consecutivo = ' . $folio . '
						AND tipo_serie = 2
				' . ";\n";

				$db->query($sql);

				$tpl->newBlock('comprobante');
				$tpl->assign('filename', $status);

				if (!$fac->cancelarFactura($_SESSION['iduser'], $_REQUEST['idcfd'], utf8_decode($_REQUEST['motivo_cancelacion']))) {
					$tpl->assign('status', '<div class="red">' . $fac->ultimoError() . '"</div>');
				}
				else {
					$sql = '
						UPDATE
							rentas_recibos
						SET
							tsbaja = NOW(),
							idbaja = ' . $_SESSION['iduser'] . '
						WHERE
							idreciborenta = ' . $_REQUEST['idreciborenta'] . '
					';

					$db->query($sql);
				}

				$email_status = $fac->enviarEmail();
			}

			echo $tpl->getOutputContent();
		break;

		case 'motivoCancelacion':
			$tpl = new TemplatePower('plantillas/ren/RentasConsultaMotivoCancelacion.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'cancelar':
			if (!in_array($_SESSION['iduser'], array(1, 4, 10, 19, 28))) {
				echo '{"status":-1,"error":"No tiene autorizaci&oacute;n para borrar facturas electr&oacute;nicas"}';
			}
			else {
				// include_once('includes/class.facturas.v2.inc.php');
				include_once('includes/class.facturas.v3.inc.php');

				// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

				$sql = '
					SELECT
						idcfd
					FROM
						rentas_recibos
					WHERE
						idreciborenta = ' . $_REQUEST['id'] . '
				';

				$id = $db->query($sql);

				$fac = new FacturasClass();

				if (!$fac->cancelarFactura($_SESSION['iduser'], $id[0]['idcfd'], utf8_decode($_REQUEST['motivo']))) {
					echo '{"status":' . $fac->ultimoCodigoError() . ',"error":"' . utf8_encode($fac->ultimoError()) . '"}';
				}
				else {
					$sql = '
						UPDATE
							rentas_recibos
						SET
							tsbaja = NOW(),
							idbaja = ' . $_SESSION['iduser'] . '
						WHERE
							idreciborenta = ' . $_REQUEST['id'] . '
					';

					$db->query($sql);

					echo '{"status":1}';
				}
			}
		break;

		case 'reporte':
			$condiciones = array();

			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();

				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}

				if (count($anios) > 0) {
					$condiciones[] = 'EXTRACT(YEAR FROM recibos.fecha) IN (' . implode(', ', $anios) . ')';
				}
			}

			if (isset($_REQUEST['meses'])) {
				$condiciones[] = 'EXTRACT(MONTH FROM recibos.fecha) IN (' . implode(', ', $_REQUEST['meses']) . ')';
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
					$condiciones[] = 'arrendadores.arrendador IN (' . implode(', ', $arrendadores) . ')';
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
					$condiciones[] = 'arrendatarios.arrendatario IN (' . implode(', ', $arrendatarios) . ')';
				}
			}

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'arrendatarios.bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['tipo_local'])) {
				$condiciones[] = 'locales.tipo_local IN (' . implode(', ', $_REQUEST['tipo_local']) . ')';
			}

			if (isset($_REQUEST['recibos']) && trim($_REQUEST['recibos']) != '') {
				$recibos = array();

				$pieces = explode(',', $_REQUEST['recibos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$recibos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$recibos[] = $piece;
					}
				}

				if (count($recibos) > 0) {
					$condiciones[] = 'fe.consecutivo IN (' . implode(', ', $recibos) . ')';
				}
			}

			if (!isset($_REQUEST['incluir_cancelados'])) {
				$condiciones[] = 'recibos.tsbaja IS NULL';
			}

			$sql = "SELECT
				recibos.tipo_recibo,
				arrendadores.arrendador,
				arrendadores.nombre_arrendador,
				arrendatarios.arrendatario,
				arrendatarios.nombre_arrendatario,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
						num_cia = arrendadores.homoclave
						AND tipo_serie = 2
						AND fe.consecutivo BETWEEN folio_inicial AND folio_final
				) || consecutivo AS num_fact,
				EXTRACT(YEAR FROM recibos.fecha) AS anio,
				EXTRACT (MONTH FROM recibos.fecha) AS mes,
				recibos.renta,
				recibos.mantenimiento,
				recibos.subtotal,
				recibos.iva,
				recibos.agua,
				recibos.retencion_iva,
				recibos.retencion_isr,
				recibos.total,
				ce.fecha_con AS pagado,
				CASE
					WHEN recibos.tsbaja IS NULL THEN
						TRUE
					ELSE
						FALSE
				END AS status
			FROM
				rentas_recibos recibos
				LEFT JOIN rentas_arrendatarios arrendatarios USING (idarrendatario)
				LEFT JOIN rentas_arrendadores arrendadores USING (idarrendador)
				LEFT JOIN rentas_locales locales USING (idlocal)
				LEFT JOIN facturas_electronicas fe ON (fe.id = idcfd)
				LEFT JOIN estado_cuenta ec USING (idarrendatario, idreciborenta)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				arrendadores.arrendador,
				anio,
				mes,
				arrendatarios.arrendatario";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/RentasConsultaReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$arrendador = NULL;

				$total = 0;

				$tpl->newBlock('reporte');

				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $arrendador);
						$tpl->assign('nombre_arrendador', $rec['nombre_arrendador']);

						$anio = NULL;
						$mes = NULL;

						$total_arrendador = 0;
					}

					if ($anio != $rec['anio'] || $mes != $rec['mes']) {
						$anio = $rec['anio'];
						$mes = $rec['mes'];

						$tpl->newBlock('mes');

						$tpl->assign('mes', $_meses[$mes]);
						$tpl->assign('anio', $anio);

						$total_mes = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('status', $rec['status'] == 'f' ? ' red line-through' : '');
					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2) : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2) : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] != 0 ? number_format($rec['subtotal'], 2) : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2) : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2) : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2) : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');

					$total_mes += $rec['total'];
					$total_arrendador += $rec['total'];
					$total += $rec['total'];

					$tpl->assign('mes.total', number_format($total_mes, 2));
					$tpl->assign('arrendador.total', number_format($total_arrendador, 2));
				}

				$tpl->assign('reporte.total', number_format($total, 2));
			}

			$tpl->printToScreen();
		break;

		case 'exportar':
			$condiciones = array();

			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();

				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}

				if (count($anios) > 0) {
					$condiciones[] = 'EXTRACT(YEAR FROM recibos.fecha) IN (' . implode(', ', $anios) . ')';
				}
			}

			if (isset($_REQUEST['meses'])) {
				$condiciones[] = 'EXTRACT(MONTH FROM recibos.fecha) IN (' . implode(', ', $_REQUEST['meses']) . ')';
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

			if (isset($_REQUEST['bloque'])) {
				$condiciones[] = 'bloque IN (' . implode(', ', $_REQUEST['bloque']) . ')';
			}

			if (isset($_REQUEST['tipo_local'])) {
				$condiciones[] = 'tipo_local IN (' . implode(', ', $_REQUEST['tipo_local']) . ')';
			}

			if (isset($_REQUEST['recibos']) && trim($_REQUEST['recibos']) != '') {
				$recibos = array();

				$pieces = explode(',', $_REQUEST['recibos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$recibos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$recibos[] = $piece;
					}
				}

				if (count($recibos) > 0) {
					$condiciones[] = 'consecutivo IN (' . implode(', ', $recibos) . ')';
				}
			}

			if (!isset($_REQUEST['incluir_cancelados'])) {
				$condiciones[] = 'recibos.tsbaja IS NULL';
			}

			$sql = '
				SELECT
					tipo_recibo,
					arrendador,
					nombre_arrendador,
					arrendatario,
					nombre_arrendatario,
					(
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = arrendadores.homoclave
							AND tipo_serie = 2
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || consecutivo
						AS num_fact,
					EXTRACT(YEAR FROM recibos.fecha)
						AS anio,
					EXTRACT (MONTH FROM recibos.fecha)
						AS mes,
					recibos.renta,
					recibos.mantenimiento,
					recibos.subtotal,
					recibos.iva,
					recibos.agua,
					recibos.retencion_iva,
					recibos.retencion_isr,
					recibos.total,
					fecha_con
						AS pagado
				FROM
					rentas_recibos recibos
					LEFT JOIN rentas_arrendatarios arrendatarios
						USING (idarrendatario)
					LEFT JOIN rentas_arrendadores arrendadores
						USING (idarrendador)
					LEFT JOIN facturas_electronicas fe
						ON (fe.id = idcfd)
					LEFT JOIN estado_cuenta ec
						ON (ec.local = idarrendatario AND fecha_renta = recibos.fecha)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					arrendador,
					anio,
					mes,
					arrendatario
			';

			$result = $db->query($sql);

			$data = '';

			if ($result) {
				$data .= '';

				$arrendador = NULL;

				$total = 0;

				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $arrendador);
						$tpl->assign('nombre_arrendador', $rec['nombre_arrendador']);

						$anio = NULL;
						$mes = NULL;

						$total_arrendador = 0;
					}

					if ($anio != $rec['anio'] || $mes != $rec['mes']) {
						$anio = $rec['anio'];
						$mes = $rec['mes'];

						$tpl->newBlock('mes');

						$tpl->assign('mes', $_meses[$mes]);
						$tpl->assign('anio', $anio);

						$total_mes = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('nombre_arrendatario', $rec['nombre_arrendatario']);
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2) : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2) : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] != 0 ? number_format($rec['subtotal'], 2) : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2) : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2) : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2) : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2) : '&nbsp;');

					$total_mes += $rec['total'];
					$total_arrendador += $rec['total'];
					$total += $rec['total'];

					$tpl->assign('mes.total', number_format($total_mes, 2));
					$tpl->assign('arrendador.total', number_format($total_arrendador, 2));
				}

				$tpl->assign('reporte.total', number_format($total, 2));

				$tpl->printToScreen();
			}
		break;

		case 'imprimirSeleccion':
			$path = 'facturas/comprobantes_pdf/';

			$sql = '
				SELECT
					num_cia,
					num_cia || \'-\' || COALESCE((
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
								num_cia = fe.num_cia
							AND
								tipo_serie = fe.tipo_serie
							AND
								fe.consecutivo BETWEEN folio_inicial AND folio_final
					), \'\') || consecutivo || \'.pdf\'
						AS
							filename
				FROM
					facturas_electronicas fe
				WHERE
					id IN (
						SELECT
							idcfd
						FROM
							rentas_recibos
						WHERE
							idreciborenta IN (' . implode(', ', $_REQUEST['id']) . ')
					)
			';
			$result = $db->query($sql);

			$files = array();
			foreach ($result as $rec) {
				$files[] = $path . $rec['num_cia'] . '/' . $rec['filename'];
			}

			$printer = $_SESSION['tipo_usuario'] == 2 ? 'elite' : 'general';

			shell_exec('lp -d ' . $printer . ' ' . implode(' ', $files));
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
