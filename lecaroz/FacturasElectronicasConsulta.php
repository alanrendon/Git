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
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaInicio.tpl');
			$tpl->prepare();

			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

			$tpl->newBlock($isIpad ? 'ipad' : 'normal');

			if ($isIpad) {
				$tpl->assign('fecha1', date('d/m/Y', date('j') < 5 ? mktime(0, 0, 0, date('n') - 1, 1) : mktime(0, 0, 0, date('n'), 1)));

				$tpl->assign('fecha2', date('d/m/Y'));

				if (!in_array($_SESSION['iduser'], array(1, 4, 26, 28, 2, 79)))
				{
					$condiciones[] = 'cc.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
				}

				if (!in_array($_SESSION['iduser'], array(1, 4, 26, 28, 2, 79))) {
					$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
				}

				$sql = 'SELECT
					num_cia,
					nombre_corto AS nombre_cia
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_administradores ca USING (idadministrador)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					num_cia';
				$cias = $db->query($sql);

				foreach ($cias as $c) {
					$tpl->newBlock('cia');
					$tpl->assign('num_cia', $c['num_cia']);
					$tpl->assign('nombre_cia', $c['nombre_cia']);
				}
			}
			else {
				$tpl->assign('fecha1', date('d/m/Y', date('j') < 5 ? mktime(0, 0, 0, date('n') - 1, 1) : mktime(0, 0, 0, date('n'), 1)));

				$tpl->assign('fecha2', date('d/m/Y'));

				$sql = 'SELECT
					idadministrador AS id,
					nombre_administrador AS nombre
				FROM
					catalogo_administradores
				ORDER BY
					nombre';
				$admins = $db->query($sql);

				foreach ($admins as $a) {
					$tpl->newBlock('admin');
					$tpl->assign('id', $a['id']);
					$tpl->assign('nombre', utf8_encode($a['nombre']));
				}

				$sql = 'SELECT
					idcontador AS id,
					nombre_contador AS nombre
				FROM
					catalogo_contadores
				ORDER BY
					nombre';
				$contadores = $db->query($sql);

				foreach ($contadores as $c) {
					$tpl->newBlock('contador');
					$tpl->assign('id', $c['id']);
					$tpl->assign('nombre', utf8_encode($c['nombre']));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'consecutivo > 0';

			$condiciones[] = 'num_cia BETWEEN ' . ( ! in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998') : '1 AND 998');

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

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['contador']) && $_REQUEST['contador'] > 0) {
				$condiciones[] = 'idcontador = ' . $_REQUEST['contador'];
			}

			if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
				$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}
			else if (isset($_REQUEST['fecha1'])) {
				$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
			}
			else if (isset($_REQUEST['fecha2'])) {
				$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
			}

			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();

				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}

				if (count($folios) > 0) {
					$condiciones[] = 'consecutivo IN (' . implode(', ', $folios) . ')';
				}
			}

			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = 'fecha_pago IS NOT NULL';
			}

			if (!isset($_REQUEST['pagadas'])) {
				$condiciones[] = 'fecha_pago IS NULL';
			}

			if (isset($_REQUEST['tipo'])) {
				$condiciones[] = 'tipo IN (' . implode(', ', $_REQUEST['tipo']) . ')';
			}

			if (!isset($_REQUEST['canceladas'])) {
				$condiciones[] = 'fe.status = 1';
			}

			$sql = 'SELECT
				fe.id,
				fe.num_cia AS emisor,
				cc.nombre AS nombre_emisor,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
						num_cia = fe.num_cia
						AND tipo_serie = fe.tipo_serie
						AND fe.consecutivo BETWEEN folio_inicial AND folio_final
				) AS serie,
				fe.consecutivo AS folio,
				fe.fecha,
				fe.fecha_pago,
				fe.nombre_cliente,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.importe
					ELSE
						fe.importe
				END AS importe,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.descuento
					ELSE
						fe.descuento
				END AS descuento,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.ieps
					ELSE
						fe.ieps
				END AS ieps,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.iva
					ELSE
						fe.iva
				END AS iva,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.retencion_iva
					ELSE
						fe.retencion_iva
				END AS retencion_iva,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.retencion_isr
					ELSE
						fe.retencion_isr
				END AS retencion_isr,
				CASE
					WHEN fe.tipo_serie = 3 THEN
						-fe.total
					ELSE
						fe.total
				END AS total,
				fe.status,
				fe.tipo,
				fe.tipo_serie
			FROM
				facturas_electronicas fe
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				' . implode(' AND ', $condiciones) . '
			ORDER BY
				fe.num_cia,
				fe.consecutivo';

			$result = $db->query($sql);

			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaResultado.tpl');
				$tpl->prepare();

				$emisor = NULL;
				foreach ($result as $rec) {
					if ($emisor != $rec['emisor']) {
						$emisor = $rec['emisor'];

						$tpl->newBlock('emisor');
						$tpl->assign('emisor', $emisor);
						$tpl->assign('nombre_emisor', utf8_encode($rec['nombre_emisor']));

						$importe = 0;
						$descuento = 0;
						$ieps = 0;
						$iva = 0;
						$retencion_iva = 0;
						$retencion_isr = 0;
						$total = 0;

						$color = FALSE;
					}

					$tpl->newBlock('factura');
					$tpl->assign('color', $rec['status'] != 0 ? ($color ? 'linea_on' : 'linea_off') : 'cancelada');
					$color = !$color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('emisor', $emisor);
					$tpl->assign('folio', ($rec['serie'] != '' ? utf8_encode($rec['serie']) . '-' : '') . $rec['folio']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('fecha_pago', $rec['fecha_pago']);
					$tpl->assign('nombre_cliente', utf8_encode($rec['nombre_cliente']));
					$tpl->assign('importe', number_format($rec['importe'], 2, '.', ','));
					$tpl->assign('ieps', $rec['ieps'] != 0 ? number_format($rec['ieps'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('descuento', $rec['descuento'] != 0 ? number_format($rec['descuento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('ieps', $rec['ieps'] != 0 ? number_format($rec['ieps'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', number_format($rec['total'], 2, '.', ','));

					$tpl->assign('refresh_icon', in_array($rec['tipo'], array(2, 6)) && in_array($_SESSION['iduser'], array(1, 4, 10, 19, 28)) ? 'refresh' : 'refresh_gray');
					$tpl->assign('cancel_icon', $rec['status'] != 0 && in_array($_SESSION['iduser'], array(1, 4, 10, 19, 28)) && !in_array($rec['tipo'], array(5)) ? 'cancel_round' : 'cancel_round_gray');

					$importe += $rec['importe'];
					$descuento += $rec['descuento'];
					$ieps += $rec['ieps'];
					$iva += $rec['iva'];
					$retencion_iva += $rec['retencion_iva'];
					$retencion_isr += $rec['retencion_isr'];
					$total += $rec['total'];

					$tpl->assign('emisor.importe', number_format($importe, 2, '.', ','));
					$tpl->assign('emisor.descuento', number_format($descuento, 2, '.', ','));
					$tpl->assign('emisor.ieps', number_format($ieps, 2, '.', ','));
					$tpl->assign('emisor.iva', number_format($iva, 2, '.', ','));
					$tpl->assign('emisor.retencion_iva', number_format($retencion_iva, 2, '.', ','));
					$tpl->assign('emisor.retencion_isr', number_format($retencion_isr, 2, '.', ','));
					$tpl->assign('emisor.total', number_format($total, 2, '.', ','));
				}

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
					id = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="factura.pdf"');

			readfile($path . $result[0]['num_cia'] . '/' . utf8_encode($result[0]['filename']));
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
					id = ' . $_REQUEST['id'] . '
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
					id = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaEmail.tpl');
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
						AS
							nombre_cia,
					(
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
					)
						AS
							serie,
					consecutivo
				FROM
						facturas_electronicas fe
					LEFT JOIN
						catalogo_companias
							USING
								(num_cia)
				WHERE
					id = ' . $_REQUEST['id'] . '
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
				$mail->Password = 'G1j7n7a*';

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
				$mail->FromName = 'Lecaroz :: Facturación Electrónica';
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

			$mail->AddAttachment($lcomprobantes_pdf . $rec['num_cia'] . '/' . utf8_encode($file_name) . '.pdf');
			$mail->AddAttachment($lcomprobantes_xml . $rec['num_cia'] . '/' . utf8_encode($file_name) . '.xml');

			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaEmailStatus.tpl');
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
			if (!in_array($_SESSION['iduser'], array(1, 4, 10, 19, 28, 79))) {
				echo -1;
			}
			else {
				$sql = '
					SELECT
						num_cia,
						cc.nombre_corto
							AS
								nombre_cia,
						consecutivo,
						fecha,
						tipo,
						tipo_pago,
						cuenta_pago,
						condiciones_pago,
						nombre_cliente,
						fe.rfc,
						fe.calle,
						fe.no_exterior,
						fe.no_interior,
						fe.colonia,
						fe.localidad,
						fe.referencia,
						fe.municipio,
						fe.estado,
						fe.pais,
						fe.codigo_postal,
						email_cliente,
						observaciones,
						nombre_consignatario,
						rfc_consignatario,
						calle_consignatario,
						no_exterior_consignatario,
						no_interior_consignatario,
						colonia_consignatario,
						localidad_consignatario,
						referencia_consignatario,
						municipio_consignatario,
						estado_consignatario,
						pais_consignatario,
						codigo_postal_consignatario,
						importe,
						porcentaje_descuento,
						descuento,
						iva,
						total
					FROM
							facturas_electronicas fe
						LEFT JOIN
							catalogo_companias cc
								USING
									(num_cia)
					WHERE
						id = ' . $_REQUEST['id'] . '
				';

				$tmp = $db->query($sql);

				$factura = $tmp[0];

				$sql = '
					SELECT
						descripcion,
						cantidad,
						precio,
						unidad,
						importe,
						numero_pedimento,
						fecha_entrada,
						aduana_entrada
					FROM
						facturas_electronicas_detalle
					WHERE
							num_cia = ' . $factura['num_cia'] . '
						AND
							consecutivo = ' . $factura['consecutivo'] . '
					ORDER BY
						id
				';

				$desglose = $db->query($sql);

				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaReimpresion.tpl');
				$tpl->prepare();

				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('num_cia', utf8_encode($factura['num_cia']));
				$tpl->assign('nombre_cia', utf8_encode($factura['nombre_cia']));
				$tpl->assign('fecha', utf8_encode($factura['fecha']));
				$tpl->assign('tipo_' . $factura['tipo'], ' checked');
				$tpl->assign('tipo_pago_' . $factura['tipo_pago'], ' selected=""');
				$tpl->assign('cuenta_pago', utf8_encode($factura['cuenta_pago']));
				$tpl->assign('condiciones_pago_' . $factura['condiciones_pago'], ' selected=""');
				$tpl->assign('nombre_cliente', utf8_encode($factura['nombre_cliente']));
				$tpl->assign('rfc', utf8_encode($factura['rfc']));
				$tpl->assign('calle', utf8_encode($factura['calle']));
				$tpl->assign('no_exterior', utf8_encode($factura['no_exterior']));
				$tpl->assign('no_interior', utf8_encode($factura['no_interior']));
				$tpl->assign('colonia', utf8_encode($factura['colonia']));
				$tpl->assign('localidad', utf8_encode($factura['localidad']));
				$tpl->assign('referencia', utf8_encode($factura['referencia']));
				$tpl->assign('municipio', utf8_encode($factura['municipio']));
				$tpl->assign('estado', utf8_encode($factura['estado']));
				$tpl->assign('pais', utf8_encode($factura['pais']));
				$tpl->assign('codigo_postal', utf8_encode($factura['codigo_postal']));
				$tpl->assign('email_cliente', utf8_encode($factura['email_cliente']));
				$tpl->assign('observaciones', utf8_encode(trim($factura['observaciones'] . ' (SUSTITUYE A LA FACTURA ' . $factura['consecutivo'] . ')')));

				$tpl->assign('nombre_consignatario', utf8_encode($factura['nombre_consignatario']));
				$tpl->assign('rfc_consignatario', utf8_encode($factura['rfc_consignatario']));
				$tpl->assign('calle_consignatario', utf8_encode($factura['calle_consignatario']));
				$tpl->assign('no_exterior_consignatario', utf8_encode($factura['no_exterior_consignatario']));
				$tpl->assign('no_interior_consignatario', utf8_encode($factura['no_interior_consignatario']));
				$tpl->assign('colonia_consignatario', utf8_encode($factura['colonia_consignatario']));
				$tpl->assign('localidad_consignatario', utf8_encode($factura['localidad_consignatario']));
				$tpl->assign('referencia_consignatario', utf8_encode($factura['referencia_consignatario']));
				$tpl->assign('municipio_consignatario', utf8_encode($factura['municipio_consignatario']));
				$tpl->assign('estado_consignatario', utf8_encode($factura['estado_consignatario']));
				$tpl->assign('pais_consignatario', utf8_encode($factura['pais_consignatario']));
				$tpl->assign('codigo_postal_consignatario', utf8_encode($factura['codigo_postal_consignatario']));

				$tpl->assign('subtotal', number_format($factura['importe'], 2, '.', ','));
				$tpl->assign('porcentaje_descuento', $factura['porcentaje_descuento'] > 0 ? number_format($factura['porcentaje_descuento'], 2, '.', ',') : '');
				$tpl->assign('descuento', $factura['descuento'] > 0 ? number_format($factura['descuento'], 2, '.', ',') : '');
				$tpl->assign('aplicar_iva', $factura['iva'] > 0 ? ' checked' : '');
				$tpl->assign('iva', $factura['iva'] > 0 ? number_format($factura['iva'], 2, '.', ',') : '');
				$tpl->assign('total', number_format($factura['total'], 2, '.', ','));

				$color = FALSE;
				foreach ($desglose as $concepto) {
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('descripcion', utf8_encode($concepto['descripcion']));
					$tpl->assign('cantidad', number_format($concepto['cantidad'], 2, '.', ','));
					$tpl->assign('precio', number_format($concepto['precio'], 2, '.', ','));
					$tpl->assign('unidad',utf8_encode($concepto['unidad']));
					$tpl->assign('importe', number_format($concepto['importe'], 2, '.', ','));
					$tpl->assign('unidad', $concepto['unidad']);
					$tpl->assign('numero_pedimento', utf8_encode($concepto['numero_pedimento']));
					$tpl->assign('fecha_entrada', $concepto['fecha_entrada']);
					$tpl->assign('aduana_entrada', utf8_encode($concepto['aduana_entrada']));
				}

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
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaReimpresionPopup.tpl');
			$tpl->prepare();

			$fac = new FacturasClass();

			$datos = array(
				'cabecera' => array (
					'num_cia'               => $_REQUEST['num_cia'],
					'clasificacion'         => $_REQUEST['tipo'],
					'fecha'                 => $_REQUEST['fecha'],
					'hora'                  => date('H:i'),
					'clave_cliente'         => 1000,
					'nombre_cliente'        => utf8_decode($_REQUEST['nombre_cliente']),
					'rfc_cliente'           => utf8_decode($_REQUEST['rfc']),
					'calle'                 => utf8_decode($_REQUEST['calle']),
					'no_exterior'           => utf8_decode($_REQUEST['no_exterior']),
					'no_interior'           => utf8_decode($_REQUEST['no_interior']),
					'colonia'               => utf8_decode($_REQUEST['colonia']),
					'localidad'             => utf8_decode($_REQUEST['localidad']),
					'referencia'            => utf8_decode($_REQUEST['referencia']),
					'municipio'             => utf8_decode($_REQUEST['municipio']),
					'estado'                => utf8_decode($_REQUEST['estado']),
					'pais'                  => utf8_decode($_REQUEST['pais']),
					'codigo_postal'         => $_REQUEST['codigo_postal'],
					'email'                 => utf8_decode($_REQUEST['email_cliente']),
					'observaciones'         => utf8_decode($_REQUEST['observaciones']),
					'importe'               => get_val($_REQUEST['subtotal']),
					'porcentaje_descuento'  => get_val($_REQUEST['porcentaje_descuento']),
					'descuento'             => get_val($_REQUEST['descuento']),
					'ieps'                  => 0,
					'porcentaje_iva'        => get_val($_REQUEST['iva']) > 0 ? 16 : 0,
					'importe_iva'           => get_val($_REQUEST['iva']),
					'aplicar_retenciones'   => 'N',
					'importe_retencion_isr' => 0,
					'importe_retencion_iva' => 0,
					'total'                 => get_val($_REQUEST['total']),
					'tipo_pago'             => $_REQUEST['tipo_pago'],
					'cuenta_pago'           => $_REQUEST['cuenta_pago'],
					'condiciones_pago'      => $_REQUEST['condiciones_pago'],
				),
				'consignatario' => array (
					'nombre'        => utf8_decode($_REQUEST['nombre_consignatario']),
					'rfc'           => utf8_decode($_REQUEST['rfc_consignatario']),
					'calle'         => utf8_decode($_REQUEST['calle_consignatario']),
					'no_exterior'   => utf8_decode($_REQUEST['no_exterior_consignatario']),
					'no_interior'   => utf8_decode($_REQUEST['no_interior_consignatario']),
					'colonia'       => utf8_decode($_REQUEST['colonia_consignatario']),
					'localidad'     => utf8_decode($_REQUEST['localidad_consignatario']),
					'referencia'    => utf8_decode($_REQUEST['referencia_consignatario']),
					'municipio'     => utf8_decode($_REQUEST['municipio_consignatario']),
					'estado'        => utf8_decode($_REQUEST['estado_consignatario']),
					'pais'          => utf8_decode($_REQUEST['pais_consignatario']),
					'codigo_postal' => utf8_decode($_REQUEST['codigo_postal_consignatario']),
				),
				'detalle' => array()
			);

			foreach ($_REQUEST['importe'] as $i => $importe) {
				if (get_val($importe) > 0) {
					$datos['detalle'][$i]['clave'] = $i + 1;
					$datos['detalle'][$i]['descripcion'] = utf8_decode(str_replace("\n", "\\n", $_REQUEST['descripcion'][$i]));
					$datos['detalle'][$i]['cantidad'] = get_val($_REQUEST['cantidad'][$i]);
					$datos['detalle'][$i]['unidad'] = utf8_decode($_REQUEST['unidad'][$i]);
					$datos['detalle'][$i]['precio'] = get_val($_REQUEST['precio'][$i]);
					$datos['detalle'][$i]['importe'] = get_val($_REQUEST['importe'][$i]);
					$datos['detalle'][$i]['descuento'] = 0;
					$datos['detalle'][$i]['porcentaje_iva'] = get_val($_REQUEST['iva']) > 0 ? 16 : 0;
					$datos['detalle'][$i]['importe_iva'] = get_val($_REQUEST['iva']) > 0 ? round(get_val($_REQUEST['importe'][$i]) * 0.16, 2) : 0;
					$datos['detalle'][$i]['numero_pedimento'] = $_REQUEST['numero_pedimento'][$i];
					$datos['detalle'][$i]['fecha_entrada'] = $_REQUEST['fecha_entrada'][$i];
					$datos['detalle'][$i]['aduana_entrada'] = $_REQUEST['aduana_entrada'][$i];
				}
			}

			$status = $fac->generarFactura($_SESSION['iduser'], $_REQUEST['num_cia'], 1, $datos);

			if ($status < 0) {
				$tpl->newBlock('error');
				$tpl->assign('status', $fac->ultimoError());
			}
			else {
				$tpl->newBlock('comprobante');
				$tpl->assign('filename', $status);

				if (!$fac->cancelarFactura($_SESSION['iduser'], $_REQUEST['id'])) {
					$tpl->assign('status', '<div class="red">' . $fac->ultimoError() . '"</div>');
				}

				$email_status = $fac->enviarEmail();

//				if ($email_status !== TRUE) {
//					$tpl->assign('status', '<div class="red">Error al enviar los comprobantes por correo electr&oacute;nico: "' . $email_status . '", le sugerimos descargar y enviar los archivos manualmente</div>');
//				}
			}

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
				//$dbf = new DBclass('pgsql://lecaroz:pobgnj@127.0.0.1:5432/ob_lecaroz', 'autocommit=yes');

				$fac = new FacturasClass();

				if (!$fac->cancelarFactura($_SESSION['iduser'], $_REQUEST['id'])) {
					echo '{"status":' . $fac->ultimoCodigoError() . ',"error":"' . utf8_encode($fac->ultimoError()) . '"}';
				}
				else {
					echo '{"status":1}';
				}
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
					id IN (' . implode(', ', $_REQUEST['id']) . ')
			';
			$result = $db->query($sql);

			$files = array();
			foreach ($result as $rec) {
				$files[] = $path . $rec['num_cia'] . '/' . $rec['filename'];
			}

			$printer = $_SESSION['tipo_usuario'] == 2 ? 'elite' : 'general';

			shell_exec('lp -d ' . $printer . ' ' . implode(' ', $files));
		break;

		case 'reporte':
			$sql = '
				SELECT
					num_cia
						AS
							emisor,
					cc.nombre
						AS
							nombre_emisor,
					(
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
					)
						AS
							serie,
					consecutivo
						AS
							folio,
					fecha,
					fecha_pago,
					nombre_cliente,
					importe,
					iva,
					total,
					fe.status
				FROM
						facturas_electronicas fe
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					num_cia,
					folio
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsultaReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$emisor = NULL;
				foreach ($result as $rec) {
					if ($emisor != $rec['emisor']) {
						$emisor = $rec['emisor'];

						$tpl->newBlock('emisor');
						$tpl->assign('emisor', $emisor);
						$tpl->assign('nombre_emisor', utf8_encode($rec['nombre_emisor']));

						$total = 0;
					}

					$tpl->newBlock('factura');

					$tpl->assign('emisor', $emisor);
					$tpl->assign('folio', ($rec['serie'] != '' ? $rec['serie'] . '-' : '') . $rec['folio']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('fecha_pago', $rec['fecha_pago']);
					$tpl->assign('nombre_cliente', utf8_encode($rec['nombre_cliente']));
					$tpl->assign('importe', number_format($rec['importe'], 2, '.', ','));
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', number_format($rec['total'], 2, '.', ','));

					$total += $rec['total'];

					$tpl->assign('emisor.total', number_format($total, 2, '.', ','));
				}
			}

			$tpl->printToScreen();
		break;

		case 'csv':
			$sql = '
				SELECT
					num_cia
						AS
							"#",
					cc.nombre
						AS
							"EMISOR",
					(
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
					)
						AS
							"SERIE",
					consecutivo
						AS
							"FOLIO",
					fecha
						AS
							"FECHA",
					fecha_pago
						AS
							"PAGADA",
					nombre_cliente
						AS
							"CLIENTE",
					importe
						AS
							"IMPORTE",
					iva
						AS
							"I.V.A.",
					total
						AS
							"TOTAL",
					CASE
						WHEN fe.status = 0 THEN
							\'CANCELADA\'
						ELSE
							\'\'
					END
						AS
							"ESTATUS"
				FROM
						facturas_electronicas fe
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					num_cia,
					consecutivo
			';

			$result = $db->query($sql);

			if ($result) {
				$data = '"' . implode('","', array_keys($result[0])) . '"' . "\r\n";

				foreach ($result as $rec) {
					$data .= '"' . implode('","', array_values($rec)) . '"' . "\r\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename="ReporteFacturasElectronicas.csv"');

				echo $data;
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
