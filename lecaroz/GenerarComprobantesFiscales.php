<?php

function generar_cfdi($num_cia, $tipo_serie, $folio, $fecha_timbrado, $uuid, $no_certificado_digital, $no_certificado_sat, $sello_cfd, $sello_sat, $cadena_original, $documento_xml, $datos_emisor, $datos)
{
	$tipo_pago = array(
		'4'	=> 'No Identificado',
		'B'	=> 'Efectivo',
		'2'	=> 'Cheque',
		'1'	=> 'Transferencia Electronica',
		'K'	=> 'Tarjeta de Credito',
		'N'	=> 'No Aplica'
	);

	$condiciones_pago = array(
		0	=> 'No Identificado',
		1	=> 'Contado',
		2	=> 'Pago a Credito'
	);

	$base_dir = dirname(__FILE__);

	$ruta_comprobantes_xml = $base_dir . '/facturas/comprobantes_xml/';
	$ruta_comprobantes_pdf = $base_dir . '/facturas/comprobantes_pdf/';
	$ruta_codigos_qr = $base_dir . '/facturas/codigos_qr/';

	$file_name = $num_cia . '-' . $datos_emisor['serie'] . $folio;

	// Crear documento XML
	if ($documento_xml != '')
	{
		$fp = fopen($ruta_comprobantes_xml . $num_cia . '/' . utf8_encode($file_name) . '.xml', 'w');

		fwrite($fp, $documento_xml . PHP_EOL);

		fclose($fp);
	}

	// Validar que la librería TemplatePower este cargada
	if ( ! class_exists('TemplatePower'))
	{
		include($base_dir . '/includes/class.TemplatePower.inc.php');
	}

	// Validar que la librería WkHtmlToPdf este cargada
	if ( ! class_exists('WkHtmlToPdf'))
	{
		include($base_dir . '/includes/WkHtmlToPdf.php');
	}

	// Validar que la librería QRcode este cargada
	if ( ! class_exists('QRcode'))
	{
		include($base_dir . '/includes/phpqrcode/qrlib.php');
	}

	// Validar que exista la función num2string()
	if ( ! function_exists('num2string'))
	{
		include($base_dir . '/includes/cheques.inc.php');
	}

	$tpl = new TemplatePower($base_dir . '/plantillas/fac/factura_electronica_pdf.tpl');
	$tpl->prepare();

	$tpl->assign('base_dir', $base_dir);

	$tpl->assign('logo', $datos_emisor['logo']);

	$tpl->assign('razon_social_emisor', $datos_emisor['razon_social']);
	$tpl->assign('rfc_emisor', $datos_emisor['rfc']);

	$domicilio_fiscal_emisor = array(
		trim($datos_emisor['calle']) != '' ? mb_strtoupper(trim($datos_emisor['calle'])) . (trim($datos_emisor['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($datos_emisor['no_exterior'])) : '') . (trim($datos_emisor['no_interior']) != '' ? ' ' . mb_strtoupper(trim($datos_emisor['no_interior'])) : '') : NULL,
		trim($datos_emisor['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($datos_emisor['colonia'])) : NULL,
		trim($datos_emisor['municipio']) != '' ? mb_strtoupper(trim($datos_emisor['municipio'])) : NULL,
		trim($datos_emisor['estado']) != '' ? mb_strtoupper(trim($datos_emisor['estado'])) : NULL,
		trim($datos_emisor['pais']) != '' ? mb_strtoupper(trim($datos_emisor['pais'])) : NULL,
		trim($datos_emisor['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($datos_emisor['codigo_postal'])) : NULL,
	);

	$tpl->assign('domicilio_fiscal_emisor', implode(', ', array_filter($domicilio_fiscal_emisor)));
	$tpl->assign('regimen_fiscal_emisor', $datos_emisor['regimen_fiscal']);

	$domicilio_fiscal_matriz = array(
		trim($datos_emisor['calle_matriz']) != '' ? mb_strtoupper(trim($datos_emisor['calle_matriz'])) . (trim($datos_emisor['no_exterior_matriz']) != '' ? ' ' . mb_strtoupper(trim($datos_emisor['no_exterior_matriz'])) : '') . (trim($datos_emisor['no_interior_matriz']) != '' ? ' ' . mb_strtoupper(trim($datos_emisor['no_interior_matriz'])) : '') : NULL,
		trim($datos_emisor['colonia_matriz']) != '' ? 'COL. ' . mb_strtoupper(trim($datos_emisor['colonia_matriz'])) : NULL,
		trim($datos_emisor['municipio_matriz']) != '' ? mb_strtoupper(trim($datos_emisor['municipio_matriz'])) : NULL,
		trim($datos_emisor['estado_matriz']) != '' ? mb_strtoupper(trim($datos_emisor['estado_matriz'])) : NULL,
		trim($datos_emisor['pais_matriz']) != '' ? mb_strtoupper(trim($datos_emisor['pais_matriz'])) : NULL,
		trim($datos_emisor['codigo_postal_matriz']) != '' ? 'CP. ' . mb_strtoupper(trim($datos_emisor['codigo_postal_matriz'])) : NULL,
	);

	$tpl->assign('domicilio_fiscal_matriz', implode(', ', array_filter($domicilio_fiscal_matriz)));

	switch ($tipo_serie)
	{
		case 1:
			$tipo_documento = 'FACTURA';
		break;

		case 2:
			$tipo_documento = 'RECIBO DE ARRENDAMIENTO';
		break;

		case 3:
			$tipo_documento = 'NOTA DE CRÉDITO';
		break;

		default:
			$tipo_documento = 'FACTURA';
	}

	$tpl->assign('tipo_documento', $tipo_documento);
	$tpl->assign('folio', ($datos_emisor['serie'] != '' ? $datos_emisor['serie'] . '-' : '') . ($folio > 0 ? $folio : $datos_emisor['folio']));
	$tpl->assign('fecha_emision', dmy_to_ymd($datos['cabecera']['fecha']) . 'T' . $datos['cabecera']['hora']);
	$tpl->assign('fecha_certificacion', $fecha_timbrado);

	$lugar_expedicion = array(
		trim($datos_emisor['municipio']) != '' ? mb_strtoupper(trim($datos_emisor['municipio'])) : NULL,
		trim($datos_emisor['estado']) != '' ? mb_strtoupper(trim($datos_emisor['estado'])) : NULL,
		trim($datos_emisor['pais']) != '' ? mb_strtoupper(trim($datos_emisor['pais'])) : NULL
	);

	$tpl->assign('lugar_expedicion', implode(', ', array_filter($lugar_expedicion)));

	if (isset($datos['consignatario']['rfc']) && trim($datos['consignatario']['rfc']) != '')
	{
		$tpl->newBlock('bloque_consignatario');

		$tpl->assign('razon_social_consignatario', mb_strtoupper(trim($datos['consignatario']['nombre'])));
		$tpl->assign('rfc_consignatario', mb_strtoupper(trim($datos['consignatario']['rfc'])));

		$domicilio_fiscal_consignatario = array(
			trim($datos['consignatario']['calle']) != '' ? mb_strtoupper(trim($datos['consignatario']['calle'])) . (trim($datos['consignatario']['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($datos['consignatario']['no_exterior'])) : '') . (trim($datos['consignatario']['no_interior']) != '' ? ' ' . mb_strtoupper(trim($datos['consignatario']['no_interior'])) : '') : NULL,
			trim($datos['consignatario']['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($datos['consignatario']['colonia'])) : NULL,
			trim($datos['consignatario']['municipio']) != '' ? mb_strtoupper(trim($datos['consignatario']['municipio'])) : NULL,
			trim($datos['consignatario']['estado']) != '' ? mb_strtoupper(trim($datos['consignatario']['estado'])) : NULL,
			trim($datos['consignatario']['pais']) != '' ? mb_strtoupper(trim($datos['consignatario']['pais'])) : NULL,
			trim($datos['consignatario']['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($datos['consignatario']['codigo_postal'])) : NULL,
		);

		$tpl->assign('domicilio_fiscal_consignatario', implode(', ', array_filter($domicilio_fiscal_consignatario)));
	}
	else
	{
		$tpl->newBlock('bloque_normal');
	}

	$tpl->assign('razon_social_receptor', mb_strtoupper(trim($datos['cabecera']['nombre_cliente'])));
	$tpl->assign('rfc_receptor', mb_strtoupper(trim($datos['cabecera']['rfc_cliente'])));

	$domicilio_fiscal_receptor = array(
		trim($datos['cabecera']['calle']) != '' ? mb_strtoupper(trim($datos['cabecera']['calle'])) . (trim($datos['cabecera']['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($datos['cabecera']['no_exterior'])) : '') . (trim($datos['cabecera']['no_interior']) != '' ? ' ' . mb_strtoupper(trim($datos['cabecera']['no_interior'])) : '') : NULL,
		trim($datos['cabecera']['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($datos['cabecera']['colonia'])) : NULL,
		trim($datos['cabecera']['municipio']) != '' ? mb_strtoupper(trim($datos['cabecera']['municipio'])) : NULL,
		trim($datos['cabecera']['estado']) != '' ? mb_strtoupper(trim($datos['cabecera']['estado'])) : NULL,
		trim($datos['cabecera']['pais']) != '' ? mb_strtoupper(trim($datos['cabecera']['pais'])) : NULL,
		trim($datos['cabecera']['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($datos['cabecera']['codigo_postal'])) : NULL,
	);

	$tpl->assign('domicilio_fiscal_receptor', implode(', ', array_filter($domicilio_fiscal_receptor)));

	$tpl->assign('folio_fiscal', $uuid);
	$tpl->assign('no_certificado_digital', $no_certificado_digital);
	$tpl->assign('serie_certificado_sat', $no_certificado_sat);

	$tpl->gotoBlock('_ROOT');

	foreach ($datos['detalle'] as $concepto)
	{
		$tpl->newBlock('concepto');

		$tpl->assign('cantidad', number_format($concepto['cantidad'], 2));
		$tpl->assign('unidad', mb_strtoupper(trim($concepto['unidad'])));
		$tpl->assign('descripcion', nl2br(mb_strtoupper(trim($concepto['descripcion']))));
		$tpl->assign('precio', number_format($concepto['precio'], 2));
		$tpl->assign('importe', number_format($concepto['importe'], 2));

		if (trim($concepto['numero_pedimento']) != '')
		{
			$tpl->newBlock('datos_aduanales');

			$tpl->assign('numero_pedimento', mb_strtoupper(trim($concepto['numero_pedimento'])));
			$tpl->assign('fecha_entrada', mb_strtoupper(trim($concepto['fecha_entrada'])));
			$tpl->assign('aduana_entrada', mb_strtoupper(trim($concepto['aduana_entrada'])));
		}
	}

	$tpl->gotoBlock('_ROOT');

	$tpl->assign('subtotal', number_format($datos['cabecera']['importe'] - $datos['cabecera']['descuento'], 2));

	if ($datos['cabecera']['ieps'] != 0)
	{
		$tpl->newBlock('ieps');
		$tpl->assign('porcentaje_ieps', 8);
		$tpl->assign('ieps', number_format($datos['cabecera']['ieps'], 2));
	}

	$tpl->newBlock('iva');
	$tpl->assign('porcentaje_iva', $datos['cabecera']['porcentaje_iva']);
	$tpl->assign('iva', number_format($datos['cabecera']['importe_iva'], 2));

	if ($datos['cabecera']['importe_retencion_iva'] != 0 || $datos['cabecera']['importe_retencion_isr'] != 0)
	{
		$tpl->newBlock('retenciones');
		$tpl->assign('retencion_iva', number_format($datos['cabecera']['importe_retencion_iva'], 2));
		$tpl->assign('retencion_isr', number_format($datos['cabecera']['importe_retencion_isr'], 2));
	}

	$tpl->gotoBlock('_ROOT');

	$tpl->assign('total', number_format($datos['cabecera']['total'], 2));

	$tpl->assign('forma_pago', mb_strtoupper('PAGO EN UNA SOLA EXHIBICIÓN'));

	$tpl->assign('metodo_pago', mb_strtoupper($tipo_pago[isset($datos['cabecera']['tipo_pago']) ? $datos['cabecera']['tipo_pago'] : 'B']));
	$tpl->assign('cuenta_pago', isset($datos['cabecera']['cuenta_pago']) ? $datos['cabecera']['cuenta_pago'] : '');
	$tpl->assign('condiciones_pago', mb_strtoupper($condiciones_pago[isset($datos['cabecera']['condiciones_pago']) ? $datos['cabecera']['condiciones_pago'] : '0']));

	$tpl->assign('importe_letra', num2string($datos['cabecera']['total']));

	$tpl->assign('cadena_original', $cadena_original);
	$tpl->assign('sello_digital_cfdi', $sello_cfd);
	$tpl->assign('sello_digital_sat', $sello_sat);

	$qr_code_data = "?re={$datos_emisor['rfc']}&rr={$datos['cabecera']['rfc_cliente']}&tt=" . number_format($datos['cabecera']['total'], 6, '.', '') . "&id={$uuid}";

	QRcode::png($qr_code_data, "{$ruta_codigos_qr}{$num_cia}/{$uuid}.png", QR_ECLEVEL_Q);

	$tpl->assign('codigo_qr', "{$ruta_codigos_qr}{$num_cia}/{$uuid}.png");

	if ($datos['cabecera']['observaciones'] != '')
	{
		$tpl->newBlock('observaciones');
		$tpl->assign('observaciones', nl2br(mb_strtoupper(trim($datos['cabecera']['observaciones']))));
	}

	$pdf = new WkHtmlToPdf(array(
		'binPath'		=> '/usr/local/bin/wkhtmltopdf',
		// 'no-outline',								// Make Chrome not complain
		'margin-top'	=> 5,
		'margin-right'	=> 5,
		'margin-bottom'	=> 5,
		'margin-left'	=> 5,
		'page-size'		=> 'Letter',
		'orientation'	=> 'Portrait',
		'disable-smart-shrinking',
		// 'user-style-sheet' => $path . '/styles/reporte-efectivos-pdf.css',
		// 'footer-center'	=> '[page] de [toPage]'
	));

	// $pdf->setPageOptions(array(
	// ));

	$pdf->addPage($tpl->getOutputContent());

	if ( ! $pdf->saveAs($ruta_comprobantes_pdf . $num_cia . '/' . utf8_encode($file_name) . '.pdf'))
	{
		throw new Exception('No se pudo crear PDF: ' . $pdf->getError());

		return FALSE;
	}
	else
	{
		return TRUE;
	}
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = strpos($date, '-') !== FALSE ? explode('-', $date) : explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
}
