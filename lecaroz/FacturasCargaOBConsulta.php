<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

function generar_xml_datos($datos, $guardar_xml = FALSE)
{
	// Crear documento XML con los datos para el webservice
	$xml = new DOMDocument('1.0', 'UTF-8');

	$xml->xmlStandalone = TRUE;
	$xml->formatOutput = TRUE;

	// Construir estructura del XML
	$xml_invoicelist = $xml->createElement('invoicelist');
	$xml->appendChild($xml_invoicelist);

	$xml_invoices = $xml->createElement('invoices');
	$xml_invoicelist->appendChild($xml_invoices);

	$xml_invoice = $xml->createElement('invoice');
	$xml_invoices->appendChild($xml_invoice);

	$xml_accountingDate = $xml->createElement('accountingDate', dmy_to_ymd($datos['cabecera']['fecha']));
	$xml_invoice->appendChild($xml_accountingDate);

	$xml_active = $xml->createElement('active', 'Y');
	$xml_invoice->appendChild($xml_active);

	$xml_bussinesPartnerValue = $xml->createElement('bussinesPartnerValue');
	$xml_invoice->appendChild($xml_bussinesPartnerValue);

	$xml_categoryValue = $xml->createElement('categoryValue', utf8_encode('PR'));
	$xml_bussinesPartnerValue->appendChild($xml_categoryValue);

	$xml_fiscalCode = $xml->createElement('fiscalCode', htmlspecialchars(utf8_encode($datos['cabecera']['rfc_pro'])));
	$xml_bussinesPartnerValue->appendChild($xml_fiscalCode);

	$xml_locations = $xml->createElement('locations');
	$xml_bussinesPartnerValue->appendChild($xml_locations);

	$xml_location = $xml->createElement('location');
	$xml_locations->appendChild($xml_location);

	$xml_address1 = $xml->createElement('address1', $datos['cabecera']['municipio'] != '' ? htmlspecialchars(utf8_encode($datos['cabecera']['municipio'])) : '.');
	$xml_location->appendChild($xml_address1);

	$xml_city = $xml->createElement('city', htmlspecialchars(utf8_encode($datos['cabecera']['municipio'])));
	$xml_location->appendChild($xml_city);

	$xml_countryValue = $xml->createElement('countryValue', utf8_encode('MX'));
	$xml_location->appendChild($xml_countryValue);

	$xml_postal = $xml->createElement('postal', utf8_encode($datos['cabecera']['codigo_postal']));
	$xml_location->appendChild($xml_postal);

	$xml_regionValue = $xml->createElement('regionValue', htmlspecialchars(utf8_encode($datos['cabecera']['estado'])));
	$xml_location->appendChild($xml_regionValue);

	$xml_calle = $xml->createElement('calle', htmlspecialchars(utf8_encode($datos['cabecera']['calle'])));
	$xml_location->appendChild($xml_calle);

	$xml_colonia = $xml->createElement('colonia', htmlspecialchars(utf8_encode($datos['cabecera']['colonia'])));
	$xml_location->appendChild($xml_colonia);

	$xml_estado = $xml->createElement('estado', htmlspecialchars(utf8_encode($datos['cabecera']['estado'])));
	$xml_location->appendChild($xml_estado);

	$xml_localidad = $xml->createElement('localidad', htmlspecialchars(utf8_encode($datos['cabecera']['localidad'])));
	$xml_location->appendChild($xml_localidad);

	$xml_municipio = $xml->createElement('municipio', htmlspecialchars(utf8_encode($datos['cabecera']['municipio'])));
	$xml_location->appendChild($xml_municipio);

	$xml_noexterior = $xml->createElement('noexterior', htmlspecialchars(utf8_encode($datos['cabecera']['no_exterior'])));
	$xml_location->appendChild($xml_noexterior);

	$xml_nointerior = $xml->createElement('nointerior', htmlspecialchars(utf8_encode($datos['cabecera']['no_interior'])));
	$xml_location->appendChild($xml_nointerior);

	$xml_name = $xml->createElement('name', htmlspecialchars(utf8_encode($datos['cabecera']['nombre_pro'])));
	$xml_bussinesPartnerValue->appendChild($xml_name);

	$xml_value = $xml->createElement('value', htmlspecialchars(utf8_encode($datos['cabecera']['rfc_pro'])));
	$xml_bussinesPartnerValue->appendChild($xml_value);

	$xml_vendor = $xml->createElement('vendor', utf8_encode('Y'));
	$xml_bussinesPartnerValue->appendChild($xml_vendor);

	$xml_customer = $xml->createElement('customer', utf8_encode('N'));
	$xml_bussinesPartnerValue->appendChild($xml_customer);

	$xml_clientValue = $xml->createElement('clientValue', utf8_encode('GLecaroz'));
	$xml_invoice->appendChild($xml_clientValue);

	$xml_creationDate = $xml->createElement('creationDate', dmy_to_ymd($datos['cabecera']['fecha']));
	$xml_invoice->appendChild($xml_creationDate);

	$xml_curencyValue = $xml->createElement('curencyValue', utf8_encode('MXN'));
	$xml_invoice->appendChild($xml_curencyValue);

	$xml_documentNo = $xml->createElement('documentNo', /*htmlspecialchars(utf8_encode($datos['cabecera']['num_fact']))*/$datos['cabecera']['id']);
	$xml_invoice->appendChild($xml_documentNo);

	$xml_documentTypeValue = $xml->createElement('documentTypeValue', htmlspecialchars(utf8_encode("{$datos['cabecera']['tipo']}{$datos['cabecera']['num_cia']}")));
	$xml_invoice->appendChild($xml_documentTypeValue);

	$xml_dueAmount = $xml->createElement('dueAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_dueAmount);

	$xml_formOfPayment = $xml->createElement('formOfPayment', 4);
	$xml_invoice->appendChild($xml_formOfPayment);

	$xml_grandTotalAmount = $xml->createElement('grandTotalAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_grandTotalAmount);

	$xml_invoiceDate = $xml->createElement('invoiceDate', dmy_to_ymd($datos['cabecera']['fecha']));
	$xml_invoice->appendChild($xml_invoiceDate);

	$xml_invoicelines = $xml->createElement('invoicelines');
	$xml_invoice->appendChild($xml_invoicelines);

	foreach ($datos['detalle'] as $i => $detalle)
	{
		$xml_invoiceline = $xml->createElement('invoiceline');
		$xml_invoicelines->appendChild($xml_invoiceline);

		$xml_line_active = $xml->createElement('active', utf8_encode('Y'));
		$xml_invoiceline->appendChild($xml_line_active);

		$xml_line_businessPartnerValue = $xml->createElement('businessPartnerValue', '');
		$xml_invoiceline->appendChild($xml_line_businessPartnerValue);

		$xml_line_clientValue = $xml->createElement('clientValue', utf8_encode('GLecaroz'));
		$xml_invoiceline->appendChild($xml_line_clientValue);

		$xml_line_descriptionOnly = $xml->createElement('descriptionOnly', utf8_encode('N'));
		$xml_invoiceline->appendChild($xml_line_descriptionOnly);

		$xml_line_lineNo = $xml->createElement('lineNo', ($i + 1) * 10);
		$xml_invoiceline->appendChild($xml_line_lineNo);

		$xml_line_editLineAmount = $xml->createElement('editLineAmount', utf8_encode('N'));
		$xml_invoiceline->appendChild($xml_line_editLineAmount);

		$xml_line_excludeForWithHolding = $xml->createElement('excludeForWithHolding', utf8_encode('N'));
		$xml_invoiceline->appendChild($xml_line_excludeForWithHolding);

		$xml_line_financialInvoiceLine = $xml->createElement('financialInvoiceLine', utf8_encode('N'));
		$xml_invoiceline->appendChild($xml_line_financialInvoiceLine);

		$xml_line_invoicedQuantity = $xml->createElement('invoicedQuantity', $detalle['cantidad']);
		$xml_invoiceline->appendChild($xml_line_invoicedQuantity);

		$xml_line_lineNetAmount = $xml->createElement('lineNetAmount', $detalle['precio'] * $detalle['cantidad']);
		$xml_invoiceline->appendChild($xml_line_lineNetAmount);

		$xml_line_listPrice = $xml->createElement('listPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_listPrice);

		$xml_line_organizationValue = $xml->createElement('organizationValue', $datos['cabecera']['num_cia']);
		$xml_invoiceline->appendChild($xml_line_organizationValue);

		$xml_line_priceLimit = $xml->createElement('priceLimit', 0);
		$xml_invoiceline->appendChild($xml_line_priceLimit);

		$xml_line_productValue = $xml->createElement('productValue', $detalle['clave']);
		$xml_invoiceline->appendChild($xml_line_productValue);

		$xml_line_description = $xml->createElement('description', htmlspecialchars(utf8_encode($detalle['descripcion'])));
		$xml_invoiceline->appendChild($xml_line_description);

		$xml_line_standardPrice = $xml->createElement('standardPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_standardPrice);

		$xml_line_taxValue = $xml->createElement('taxValue', $detalle['tax']);
		$xml_invoiceline->appendChild($xml_line_taxValue);

		$xml_line_unitPrice = $xml->createElement('unitPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_unitPrice);

		$xml_line_productOrderUOMValue = $xml->createElement('productOrderUOMValue', '');
		$xml_invoiceline->appendChild($xml_line_productOrderUOMValue);
	}

	$xml_organizationValue = $xml->createElement('organizationValue', $datos['cabecera']['num_cia']);
	$xml_invoice->appendChild($xml_organizationValue);

	$xml_paymentComplete = $xml->createElement('paymentComplete', utf8_encode('N'));
	$xml_invoice->appendChild($xml_paymentComplete);

	$xml_paymentMethodValue = $xml->createElement('paymentMethodValue', $GLOBALS['tipo_pago']['4']);
	$xml_invoice->appendChild($xml_paymentMethodValue);

	$xml_paymentTermsValue = $xml->createElement('paymentTermsValue', $GLOBALS['condiciones_pago'][0]);
	$xml_invoice->appendChild($xml_paymentTermsValue);

	$xml_priceListValue = $xml->createElement('priceListValue', utf8_encode('Compras'));
	$xml_invoice->appendChild($xml_priceListValue);

	$xml_salesTransaction = $xml->createElement('salesTransaction', utf8_encode('N'));
	$xml_invoice->appendChild($xml_salesTransaction);

	$xml_timbre = $xml->createElement('timbre', utf8_encode('N'));
	$xml_invoice->appendChild($xml_timbre);

	$xml_orderReference = $xml->createElement('orderReference', htmlspecialchars(utf8_encode($datos['cabecera']['num_fact'])));
	$xml_invoice->appendChild($xml_orderReference);

	$xml_selfService = $xml->createElement('selfService', utf8_encode('Y'));
	$xml_invoice->appendChild($xml_selfService);

	$xml_summedLineAmount = $xml->createElement('summedLineAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_summedLineAmount);

	$xml_totalPaid = $xml->createElement('totalPaid', 0);
	$xml_invoice->appendChild($xml_totalPaid);

	$xml_transactionDocumentValue = $xml->createElement('transactionDocumentValue', htmlspecialchars(utf8_encode("{$datos['cabecera']['tipo']}{$datos['cabecera']['num_cia']}")));
	$xml_invoice->appendChild($xml_transactionDocumentValue);

	$subdir = date('Y-m-d') . '-manual';

	if ($guardar_xml)
	{
		if ( ! is_dir("tmp/{$subdir}"))
		{
			mkdir("tmp/{$subdir}");
		}

		$nombre_xml = "carga-conta-{$datos['cabecera']['id']}-{$datos['cabecera']['num_cia']}-{$datos['cabecera']['clave_pro']}-{$datos['cabecera']['num_fact']}.xml";

		$xml->save("tmp/{$subdir}/{$nombre_xml}");

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Documento XML guardado con nombre: {$nombre_xml}";
	}

	// Retornar el XML
	return $xml->saveXML();
}

function consumir_webservice($xml_data)
{
	// Construir arreglo de opciones para consumir el webservice
	$stream_options = array(
		'http' => array(
			'method'	=> 'POST',
			'header'	=> 'Authorization: Basic ' . base64_encode("Openbravo:openbravo") . ' Content-Type: text/xml',
			'content'	=> $xml_data,
		),
	);

	// Crear contexto de flujo con las opciones para consumir el webservice
	$context = stream_context_create($stream_options);

	// Consumir webservice y obtener respuesta
	$ob_response = @file_get_contents('http://192.168.1.3:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecInvoice', NULL, $context);

	return $ob_response;
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':
			$tpl = new TemplatePower('plantillas/con/FacturasCargaOBConsultaInicio.tpl');
			$tpl->prepare();

			$fecha1 = date('j') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
			$fecha2 = date('j') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');

			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);

			$rfcs = $db->query("SELECT
				rfc AS value,
				'[' || rfc || '] ' || COALESCE((
					SELECT
						razon_social
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
						AND TRIM(razon_social) != ''
					ORDER BY
						num_cia
					LIMIT 1
				), (
					SELECT
						nombre
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
						AND TRIM(nombre) != ''
					ORDER BY
						num_cia
					LIMIT 1
				), '-- SIN NOMBRE --') AS text
			FROM
				catalogo_companias cc
			WHERE
				num_cia < 900
				AND LENGTH(rfc) >= 12
			GROUP BY
				rfc
			ORDER BY
				(
					SELECT
						MIN (num_cia)
					FROM
						catalogo_companias
					WHERE
						rfc = cc.rfc
				)");

			if ($rfcs)
			{
				foreach ($rfcs as $row) {
					$tpl->newBlock('rfc');

					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode($row['text']));
				}
			}

			$admins = $db->query("SELECT idadministrador AS value, nombre_administrador AS text FROM catalogo_administradores ORDER BY text");

			if ($admins)
			{
				foreach ($admins as $a) {
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones1 = array();
			$condiciones2 = array();

			$condiciones1[] = "f.fecha >= '01/01/2015'";
			$condiciones2[] = "f.fecha >= '01/01/2015'";

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
					$condiciones1[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
					$condiciones2[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '')
			{
				$condiciones[] = "cc.rfc = '{$_REQUEST['rfc']}'";
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones1[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
					$condiciones2[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}

				if (count($gastos) > 0) {
					$condiciones1[] = 'f.cosgastos IN (' . implode(', ', $gastos) . ')';
					$condiciones2[] = '33 IN (' . implode(', ', $gastos) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones1[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
					$condiciones2[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones1[] = "f.fecha = '{$_REQUEST['fecha1']}'";
					$condiciones2[] = "f.fecha = '{$_REQUEST['fecha1']}'";
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones1[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
					$condiciones2[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
				}
			}

			if ((isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '')
				|| (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '')) {
				if ((isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '')
					&& (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '')) {
					$condiciones1[] = "ec.fecha_con BETWEEN '{$_REQUEST['fecha_cobro1']}' AND '{$_REQUEST['fecha_cobro2']}'";
					$condiciones2[] = "ec.fecha_con BETWEEN '{$_REQUEST['fecha_cobro1']}' AND '{$_REQUEST['fecha_cobro2']}'";
				} else if (isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '') {
					$condiciones1[] = "ec.fecha_con = '{$_REQUEST['fecha_cobro1']}'";
					$condiciones2[] = "ec.fecha_con = '{$_REQUEST['fecha_cobro1']}'";
				} else if (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '') {
					$condiciones1[] = "ec.fecha_con <= '{$_REQUEST['fecha_cobro2']}'";
					$condiciones2[] = "ec.fecha_con <= '{$_REQUEST['fecha_cobro2']}'";
				}
			}

			if ((isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '')
				|| (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '')) {
				if ((isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '')
					&& (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '')) {
					$condiciones1[] = "f.fecha_captura BETWEEN '{$_REQUEST['fecha_cap1']}' AND '{$_REQUEST['fecha_cap2']}'";
				} else if (isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '') {
					$condiciones1[] = "f.fecha_captura = '{$_REQUEST['fecha_cap1']}'";
				} else if (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '') {
					$condiciones1[] = "f.fecha_captura <= '{$_REQUEST['fecha_cap2']}'";
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '') {
				$facturas = array();
				$facturas_between1 = array();
				$facturas_between2 = array();

				$pieces = explode(',', $_REQUEST['facturas']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$facturas_between1[] =  "f.num_fact BETWEEN '" . $exp[0] . "' AND '" . $exp[1] . "'";
						$facturas_between2[] =  "f.num_fac BETWEEN '" . $exp[0] . "' AND '" . $exp[1] . "'";
					}
					else {
						$facturas[] = $piece;
					}
				}

				$partes = array();

				if (count($facturas) > 0) {
					$partes1[] = "f.num_fact IN ('" . implode("', '", $facturas) . "')";
					$partes2[] = "f.num_fac IN ('" . implode("', '", $facturas) . "')";
				}

				if (count($facturas_between1) > 0) {
					$partes1[] = implode(' OR ', $facturas_between1);
					$partes2[] = implode(' OR ', $facturas_between2);
				}

				if (count($partes1) > 0) {
					$condiciones1[] = '(' . implode(' OR ', $partes1) . ')';
					$condiciones2[] = '(' . implode(' OR ', $partes2) . ')';
				}
			}

			if (isset($_REQUEST['usuario'])) {
				$condiciones1[] = "f.iduser = {$_SESSION['iduser']}";
			}

			$condiciones1[] = '(pp.id IS NOT NULL OR fp.id IS NOT NULL)';
			$condiciones2[] = '(pp.id IS NOT NULL OR fp.id IS NOT NULL)';

			if ($_REQUEST['status'] > 0) {
				if ($_REQUEST['status'] == 1)
				{
					$condiciones1[] = 'fp.id IS NULL';
					$condiciones2[] = 'fp.id IS NULL';
				}
				else
				{
					$condiciones1[] = 'fp.id IS NOT NULL AND fp.fecha_cheque IS NOT NULL';
					$condiciones2[] = 'fp.id IS NOT NULL AND fp.fecha_cheque IS NOT NULL';
				}

				if ($_REQUEST['status'] == 2 && $_REQUEST['pag'] > 0) {
					$condiciones1[] = 'ec.id IS NOT NULL AND ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
					$condiciones2[] = 'ec.id IS NOT NULL AND ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
				}
			}

			if ( ! isset($_REQUEST['pollos_facturado']) ||  ! isset($_REQUEST['pollos_contado'])) {
				if (isset($_REQUEST['pollos_facturado']))
				{
					$condiciones2[] = "f.credito > 0";
				}

				if (isset($_REQUEST['pollos_contado']))
				{
					$condiciones2[] = "f.contado > 0";
				}
			}

			$condiciones1_string = implode(' AND ', $condiciones1);
			$condiciones2_string = implode(' AND ', $condiciones2);

			$orden_string = isset($_REQUEST['ordenar_por_rfc']) ? '(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = resultado.rfc_cia), fecha, num_cia, num_fact' : 'num_cia, fecha, num_fact';

			$sql = "SELECT
				*
			FROM
				(
					SELECT
						f.id,
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						f.num_fact,
						f.fecha,
						f.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.rfc AS rfc_cia,
						f.concepto,
						COALESCE((
							SELECT
								SUM(cantidad * precio)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.importe) AS importe,
						COALESCE((
							SELECT
								SUM(COALESCE(desc1) + COALESCE(desc2) + COALESCE(desc3))
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), 0) AS descuentos,
						COALESCE((
							SELECT
								SUM(ieps)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.ieps) AS ieps,
						COALESCE((
							SELECT
								SUM(iva)
							FROM
								entrada_mp
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
								AND fecha = f.fecha
								AND regalado = FALSE
						), f.iva) AS iva,
						f.retencion_iva AS ret_iva,
						f.retencion_isr AS ret_isr,
						f.importe_otros AS otros,
						f.total,
						fp.folio_cheque AS folio,
						fecha_cheque AS fecha_pago,
						fecha_con AS fecha_cobro,
						f.codgastos AS gasto,
						cg.descripcion AS nombre_gasto,
						CASE
							WHEN COALESCE((
								SELECT
									TRUE
								FROM
									factura_gas
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
								LIMIT
									1
							), FALSE) = TRUE THEN
								2
							WHEN COALESCE((
								SELECT
									TRUE
								FROM
									entrada_mp
								WHERE
									num_proveedor = f.num_proveedor
									AND num_fact = f.num_fact
									AND fecha = f.fecha
								LIMIT
									1
							), FALSE) = TRUE THEN
								1
							ELSE
								0
						END AS tipo,
						ch.cuenta AS banco,
						ch.cod_mov,
						ch.fecha_cancelacion,
						f.xml_file,
						f.pdf_file,
						COALESCE((
							SELECT
								TRUE
							FROM
								balances_pan
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), (
							SELECT
								TRUE
							FROM
								balances_ros
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), FALSE) AS balance_generado,
						ts_carga_conta,
						ts_carga_conta_error,
						ts_carga_pago_conta,
						ts_carga_pago_conta_error
					FROM
						facturas f
						LEFT JOIN catalogo_gastos cg ON (cg.codgastos = f.codgastos)
						LEFT JOIN pasivo_proveedores pp ON (
							pp.num_proveedor = f.num_proveedor
							AND pp.num_fact = f.num_fact
							AND pp.fecha = f.fecha
						)
						LEFT JOIN facturas_pagadas fp ON (
							fp.num_proveedor = f.num_proveedor
							AND fp.num_fact = f.num_fact
							AND fp.fecha = f.fecha
						)
						LEFT JOIN cheques ch ON (
							ch.num_cia = fp.num_cia
							AND ch.cuenta = fp.cuenta
							AND ch.folio = fp.folio_cheque
							AND ch.fecha = fp.fecha_cheque
						)
						LEFT JOIN estado_cuenta ec ON (
							ec.num_cia = fp.num_cia
							AND ec.cuenta = fp.cuenta
							AND ec.folio = fp.folio_cheque
							AND ec.fecha = fp.fecha_cheque
						)
						LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
					WHERE
						{$condiciones1_string}

					UNION

					SELECT
						f.id,
						f.num_proveedor AS num_pro,
						cp.nombre AS nombre_pro,
						f.num_fac AS num_fact,
						f.fecha,
						f.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.rfc AS rfc_cia,
						'FACTURA ROSTICERIA' AS concepto,
						f.credito AS importe,
						0 AS descuentos,
						0 AS ieps,
						0 AS iva,
						0 AS ret_iva,
						0 AS ret_isr,
						0 AS otros,
						f.credito AS total,
						fp.folio_cheque AS folio,
						fecha_cheque AS fecha_pago,
						fecha_con AS fecha_cobro,
						33 AS gasto,
						'PAGO PROVEEDORES' AS nombre_gasto,
						3 AS tipo,
						ch.cuenta AS banco,
						ch.cod_mov,
						ch.fecha_cancelacion,
						NULL AS xml_file,
						NULL AS pdf_file,
						COALESCE((
							SELECT
								TRUE
							FROM
								balances_pan
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), (
							SELECT
								TRUE
							FROM
								balances_ros
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), FALSE) AS balance_generado
					FROM
						total_fac_ros f
						LEFT JOIN pasivo_proveedores pp ON (
							pp.num_proveedor = f.num_proveedor
							AND pp.num_fact = f.num_fac
							AND pp.fecha = f.fecha
						)
						LEFT JOIN facturas_pagadas fp ON (
							fp.num_proveedor = f.num_proveedor
							AND fp.num_fact = f.num_fac
							AND fp.fecha = f.fecha
						)
						LEFT JOIN cheques ch ON (
							ch.num_cia = fp.num_cia
							AND ch.cuenta = fp.cuenta
							AND ch.folio = fp.folio_cheque
							AND ch.fecha = fp.fecha_cheque
						)
						LEFT JOIN estado_cuenta ec ON (
							ec.num_cia = fp.num_cia
							AND ec.cuenta = fp.cuenta
							AND ec.folio = fp.folio_cheque
							AND ec.fecha = fp.fecha_cheque
						)
						LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
					WHERE
						{$condiciones2_string}
				) AS resultado

			ORDER BY
				num_pro, {$orden_string}";

			$query = $db->query($sql);

			if ($query) {
				$tpl = new TemplatePower('plantillas/con/FacturasCargaOBConsultaResultado.tpl');
				$tpl->prepare();

				$num_pro = NULL;

				$g_importe = 0;
				$g_descuentos = 0;
				$g_ieps = 0;
				$g_iva = 0;
				$g_ret_iva = 0;
				$g_ret_isr = 0;
				$g_otros = 0;
				$g_total = 0;

				foreach ($query as $row) {
					if ($num_pro != $row['num_pro']) {
						$num_pro = $row['num_pro'];

						$tpl->newBlock('pro');

						$tpl->assign('num_pro', $row['num_pro']);
						$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));

						$importe = 0;
						$descuentos = 0;
						$ieps = 0;
						$iva = 0;
						$ret_iva = 0;
						$ret_isr = 0;
						$otros = 0;
						$total = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('data_fac', htmlentities(json_encode(array(
						'id'	=> get_val($row['id']),
						'tipo'	=> get_val($row['tipo'])
					))));
					$tpl->assign('num_fact', $row['tipo'] > 0 ? ('<a id="detalle" alt="' . htmlentities(json_encode(array(
						'id'	=> get_val($row['id']),
						'tipo'	=> get_val($row['tipo'])
					))) . '" class="enlace ' . ($row['tipo'] == 1 ? 'blue' : 'orange') . '">' . utf8_encode($row['num_fact']) . '</a>') : utf8_encode($row['num_fact']));
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('rfc_cia', utf8_encode($row['rfc_cia']));
					$tpl->assign('concepto', trim($row['concepto']) != '' ? trim(utf8_encode($row['concepto'])) : '&nbsp;');
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('nombre_gasto', $row['nombre_gasto']);
					$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('descuentos', $row['descuentos'] != 0 ? number_format($row['descuentos'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] != 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] != 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ret_iva', $row['ret_iva'] != 0 ? number_format($row['ret_iva'], 2) : '&nbsp;');
					$tpl->assign('ret_isr', $row['ret_isr'] != 0 ? number_format($row['ret_isr'], 2) : '&nbsp;');
					$tpl->assign('otros', $row['otros'] != 0 ? number_format($row['otros'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] != 0 ? number_format($row['total'], 2) : '&nbsp;');
					$tpl->assign('fecha_pago', $row['fecha_pago'] != '' ? $row['fecha_pago'] : '&nbsp;');
					$tpl->assign('banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['fecha_cancelacion'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha_cobro', $row['fecha_cobro'] != '' ? $row['fecha_cobro'] : '&nbsp;');

					if ($row['ts_carga_conta'] != '')
					{
						$tpl->assign('estado_factura', '<img src="iconos/accept_green.png"> Cargada');
					}
					else if ($row['ts_carga_conta_error'] != '')
					{
						$tpl->assign('estado_factura', '<img src="iconos/cancel_round.png"> Error: ' . $row['ts_carga_conta_error']);
					}
					else
					{
						$tpl->assign('estado_factura', '&nbsp;');
					}

					if ($row['ts_carga_pago_conta'] != '')
					{
						$tpl->assign('estado_pago', '<img src="iconos/accept_green.png"> <span class="bold green">Cargada</span>');
					}
					else if ($row['ts_carga_pago_conta_error'] != '')
					{
						$tpl->assign('estado_pago', '<img src="iconos/cancel_round.png"> <span class="bold red">Error: ' . $row['ts_carga_pago_conta_error'] . '</span>');
					}
					else
					{
						$tpl->assign('estado_pago', '&nbsp;');
					}

					$importe += $row['importe'];
					$descuentos += $row['descuentos'];
					$ieps += $row['ieps'];
					$iva += $row['iva'];
					$ret_iva += $row['ret_iva'];
					$ret_isr += $row['ret_isr'];
					$otros += $row['otros'];
					$total += $row['total'];

					$g_importe += $row['importe'];
					$g_descuentos += $row['descuentos'];
					$g_ieps += $row['ieps'];
					$g_iva += $row['iva'];
					$g_ret_iva += $row['ret_iva'];
					$g_ret_isr += $row['ret_isr'];
					$g_otros += $row['otros'];
					$g_total += $row['total'];

					$tpl->assign('pro.importe', number_format($importe, 2));
					$tpl->assign('pro.descuentos', number_format($descuentos, 2));
					$tpl->assign('pro.ieps', number_format($ieps, 2));
					$tpl->assign('pro.iva', number_format($iva, 2));
					$tpl->assign('pro.ret_iva', number_format($ret_iva, 2));
					$tpl->assign('pro.ret_isr', number_format($ret_isr, 2));
					$tpl->assign('pro.otros', number_format($otros, 2));
					$tpl->assign('pro.total', number_format($total, 2));

					$tpl->assign('_ROOT.importe', number_format($g_importe, 2));
					$tpl->assign('_ROOT.descuentos', number_format($g_descuentos, 2));
					$tpl->assign('_ROOT.ieps', number_format($g_ieps, 2));
					$tpl->assign('_ROOT.iva', number_format($g_iva, 2));
					$tpl->assign('_ROOT.ret_iva', number_format($g_ret_iva, 2));
					$tpl->assign('_ROOT.ret_isr', number_format($g_ret_isr, 2));
					$tpl->assign('_ROOT.otros', number_format($g_otros, 2));
					$tpl->assign('_ROOT.total', number_format($g_total, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'detalle':
			$tpl = new TemplatePower('plantillas/con/FacturasCargaOBConsultaDetalle.tpl');
			$tpl->prepare();

			if ($_REQUEST['tipo'] == 3)
			{
				$result = $db->query("SELECT
					f.num_cia,
					cc.nombre_corto AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fac AS num_fact,
					f.fecha
				FROM
					total_fac_ros f
					LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");
			}
			else
			{
				$result = $db->query("SELECT
					f.num_cia,
					cc.nombre_corto AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.fecha
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");
			}

			$info_fac = $result[0];

			$tpl->assign('num_cia', $info_fac['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($info_fac['nombre_cia']));
			$tpl->assign('num_pro', $info_fac['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($info_fac['nombre_pro']));
			$tpl->assign('num_fact', utf8_encode($info_fac['num_fact']));
			$tpl->assign('fecha', $info_fac['fecha']);

			if ($_REQUEST['tipo'] == 1)
			{
				$result = $db->query("SELECT
					emp.cantidad,
					emp.codmp,
					cmp.nombre AS nombre_mp,
					emp.contenido,
					tuc.descripcion AS unidad,
					emp.precio,
					emp.cantidad * emp.precio AS importe,
					emp.desc1,
					emp.desc2,
					emp.desc3,
					emp.iva,
					emp.ieps,
					(emp.cantidad * emp.precio) - emp.desc1 - emp.desc2 - emp.desc3 + emp.iva + emp.ieps AS total
				FROM
					entrada_mp emp
					LEFT JOIN catalogo_mat_primas cmp USING (codmp)
					LEFT JOIN tipo_unidad_consumo tuc ON (idunidad = unidadconsumo)
				WHERE
					(emp.num_proveedor, emp.num_fact, emp.fecha) IN (
						SELECT
							num_proveedor,
							num_fact,
							fecha
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					emp.id");

				$importe = 0;
				$desc1 = 0;
				$desc2 = 0;
				$desc3 = 0;
				$iva = 0;
				$ieps = 0;
				$total = 0;

				$tpl->newBlock('mp');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_mp');

					$tpl->assign('cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('contenido', number_format($row['contenido'], 2));
					$tpl->assign('unidad', utf8_encode($row['unidad']));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('desc1', $row['desc1'] > 0 ? number_format($row['desc1'], 2) : '&nbsp;');
					$tpl->assign('desc2', $row['desc2'] > 0 ? number_format($row['desc2'], 2) : '&nbsp;');
					$tpl->assign('desc3', $row['desc3'] > 0 ? number_format($row['desc3'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] > 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$importe += $row['importe'];
					$desc1 += $row['desc1'];
					$desc2 += $row['desc2'];
					$desc3 += $row['desc3'];
					$iva += $row['iva'];
					$ieps += $row['ieps'];
					$total += $row['total'];

					$tpl->assign('mp.importe', number_format($importe, 2));
					$tpl->assign('mp.desc1', number_format($desc1, 2));
					$tpl->assign('mp.desc2', number_format($desc2, 2));
					$tpl->assign('mp.desc3', number_format($desc3, 2));
					$tpl->assign('mp.iva', number_format($iva, 2));
					$tpl->assign('mp.ieps', number_format($ieps, 2));
					$tpl->assign('mp.total', number_format($total, 2));
				}
			}
			else if ($_REQUEST['tipo'] == 2)
			{
				$result = $db->query("SELECT
					litros,
					precio_unit AS precio,
					litros * precio_unit AS importe,
					total - litros * precio_unit AS iva,
					total
				FROM
					factura_gas
				WHERE
					(num_proveedor, num_fact, fecha) IN (
						SELECT
							num_proveedor,
							num_fact,
							fecha
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					id");

				$importe = 0;
				$iva = 0;
				$total = 0;

				$tpl->newBlock('gas');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_gas');

					$tpl->assign('litros', number_format($row['litros'], 2));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$importe += $row['importe'];
					$iva += $row['iva'];
					$total += $row['total'];

					$tpl->assign('gas.importe', number_format($importe, 2));
					$tpl->assign('gas.iva', number_format($iva, 2));
					$tpl->assign('gas.total', number_format($total, 2));
				}
			}
			else if ($_REQUEST['tipo'] == 3)
			{
				$result = $db->query("SELECT
					fr.codmp,
					cmp.nombre AS nombre_mp,
					fr.cantidad,
					fr.kilos,
					ROUND((fr.precio * tfr.porc795 / 100)::NUMERIC, 2) AS precio,
					ROUND((fr.precio * tfr.porc795 / 100)::NUMERIC, 2) * kilos AS total
				FROM
					fact_rosticeria fr
					LEFT JOIN total_fac_ros tfr ON (
						tfr.num_cia = fr.num_cia
						AND tfr.num_proveedor = fr.num_proveedor
						AND tfr.num_fac = fr.num_fac
						AND tfr.fecha = fr.fecha_mov
					)
					LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				WHERE
					(fr.num_proveedor, fr.num_fac, fr.fecha_mov) IN (
						SELECT
							num_proveedor,
							num_fac,
							fecha
						FROM
							total_fac_ros
						WHERE
							id = {$_REQUEST['id']}
					)
				ORDER BY
					fr.idfact_rosticeria");

				$total = 0;

				$tpl->newBlock('pollos');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_pollos');

					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('kilos', number_format($row['kilos'], 2));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$total += $row['total'];

					$tpl->assign('pollos.total', number_format($total, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'obtener_datos_fac':
			if (in_array($_REQUEST['tipo'], array(0, 1, 2)))
			{
				$result = $db->query("SELECT
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.total AS importe
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				WHERE
					f.id = {$_REQUEST['id']}");
			}
			else if ($_REQUEST['tipo'] == 3)
			{
				$result = $db->query("SELECT
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fac AS num_fact,
					f.credito AS importe
				FROM
					total_fac_ros f
					LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				WHERE
					f.id = {$_REQUEST['id']}");
			}

			if ($result)
			{
				echo json_encode($result[0]);
			}

			break;

		case 'cargar_conta':
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
				2	=> 'Credito'
			);

			$sql = "SELECT
				f.id,
				f.num_cia,
				cc.razon_social AS nombre_cia,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				cp.rfc AS rfc_pro,
				f.num_fact,
				f.fecha,
				cp.calle,
				cp.no_exterior,
				cp.no_interior,
				cp.colonia,
				cp.localidad,
				cp.referencia,
				cp.municipio,
				cp.estado,
				cp.pais,
				cp.codigo_postal,
				cp.email1 AS email,
				f.concepto AS descripcion,
				f.codgastos AS codigo,
				f.importe,
				f.iva,
				f.ieps,
				f.retencion_iva,
				f.retencion_isr,
				f.concepto_otros,
				f.importe_otros,
				f.total,
				f.pieps,
				f.piva,
				f.pretencion_iva,
				f.pretencion_isr,
				CASE
					WHEN (SELECT id FROM entrada_mp WHERE num_proveedor = f.num_proveedor AND num_fact = f.num_fact AND fecha = f.fecha LIMIT 1) IS NOT NULL THEN
						'C'
					WHEN f.codgastos IS NOT NULL THEN
						'G'
				END AS tipo_registro
			FROM
				facturas f
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				f.id IN (" . implode(', ', $_REQUEST['ids']) . ")
			ORDER BY
				f.id";

			$result = $db->query($sql);

			$ids = array();

			echo "\n(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n";

			foreach ($result as $i => $df)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "]++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Id factura: {$df['id']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Compañía: {$df['num_cia']} " . utf8_encode($df['nombre_cia']);
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Proveedor: {$df['num_pro']} " . utf8_encode($df['nombre_pro']);
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Folio: {$df['num_fact']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Fecha: {$df['fecha']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Tipo: {$df['tipo_registro']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Importe: " . number_format($df['total'], 2);

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Generando datos de carga para openbravo";

				$datos = array(
					'cabecera'	=> array (
						'id'					=> $df['id'],
						'num_cia'				=> $df['num_cia'],
						'tipo'					=> $df['tipo_registro'],
						'fecha'					=> $df['fecha'],
						'clave_pro'				=> $df['num_pro'],
						'nombre_pro'			=> $df['nombre_pro'],
						'rfc_pro'				=> $df['rfc_pro'],
						'num_fact'				=> $df['num_fact'],
						'calle'					=> $df['calle'],
						'no_exterior'			=> $df['no_exterior'],
						'no_interior'			=> $df['no_interior'],
						'colonia'				=> $df['colonia'],
						'localidad'				=> $df['localidad'],
						'referencia'			=> $df['referencia'],
						'municipio'				=> $df['municipio'],
						'estado'				=> $df['estado'],
						'pais'					=> $df['pais'],
						'codigo_postal'			=> $df['codigo_postal'],
						'email'					=> $df['email'],
						'importe'				=> get_val($df['importe']),
						'ieps'					=> get_val($df['ieps']),
						'iva'					=> get_val($df['iva']),
						'retencion_isr'			=> get_val($df['retencion_isr']),
						'retencion_iva'			=> get_val($df['retencion_iva']),
						'total'					=> get_val($df['total'])
					),
					'detalle'	=> array()
				);

				if ($detalles = $db->query("SELECT
						d.codmp AS codigo,
						cmp.nombre AS descripcion,
						d.precio AS precio,
						d.cantidad AS cantidad,
						COALESCE(d.piva, 0) AS iva,
						COALESCE(d.ieps, 0) AS ieps,
						COALESCE(d.desc1, 0) + COALESCE(d.desc2, 0) + COALESCE(d.desc3, 0) AS descuento,
						CONCAT_WS(' + ',
							CASE WHEN d.pdesc1 > 0 THEN d.pdesc1 || '%' ELSE NULL END,
							CASE WHEN d.pdesc2 > 0 THEN d.pdesc2 || '%' ELSE NULL END,
							CASE WHEN d.pdesc3 > 0 THEN d.pdesc3 || '%' ELSE NULL END) AS descuentos_string
					FROM
						entrada_mp d
						LEFT JOIN catalogo_mat_primas cmp USING (codmp)
					WHERE
						d.num_proveedor = {$df['num_pro']}
						AND d.num_fact = '{$df['num_fact']}'
						AND d.fecha = '{$df['fecha']}'
						AND regalado = FALSE"))
				{
					foreach ($detalles as $dd)
					{
						$tax = $dd['iva'] != 0 && $dd['ieps'] != 0 ? ($dd['codigo'] == 87 ? 'IMP-RON' : 'PIEPSIVA') : ($dd['iva'] != 0 ? 'IVA' : ($dd['ieps'] != 0 ? 'PIEPS' : 'IVA 0'));

						$datos['detalle'][] = array(
							'clave'			=> "MP{$dd['codigo']}",
							'descripcion'	=> $dd['descripcion'],
							'cantidad'		=> get_val($dd['cantidad']),
							'precio'		=> get_val($dd['precio']),
							'importe'		=> get_val($dd['cantidad']) * get_val($dd['precio']),
							'tax'			=> $tax
						);

						if (get_val($dd['descuento']) > 0)
						{
							$datos['detalle'][] = array(
								'clave'			=> "MP{$dd['codigo']}",
								'descripcion'	=> $dd['descuentos_string'],
								'cantidad'		=> 1,
								'precio'		=> -get_val($dd['descuento']),
								'importe'		=> -get_val($dd['descuento']),
								'tax'			=> $tax
							);
						}
					}
				}
				else
				{
					$tax = $df['iva'] != 0 && $df['retencion_iva'] != 0 && $df['retencion_isr'] != 0 ? 'HONORARIOS/ARRENDAMIENTOS' : ($df['iva'] != 0 && $df['retencion_iva'] != 0 ? 'IVA + RET 4%' : ($df['retencion_isr'] != 0 ? (abs(round($df['retencion_isr'] * 100 / $df['importe'], 2)) == 10 ? 'ARRENDAMIENTO HABITACION' : 'R35%') : ($df['iva'] != 0 ? 'IVA' : 'IVA 0')));

					$datos['detalle'][] = array(
						'clave'			=> "G{$df['codigo']}",
						'descripcion'	=> $df['descripcion'],
						'cantidad'		=> 1,
						'precio'		=> get_val($df['importe']),
						'importe'		=> get_val($df['importe']),
						'tax'			=> $tax
					);
				}

				if ($df['importe_otros'] > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> "G{$df['codigo']}",
						'descripcion'	=> $df['concepto_otros'],
						'cantidad'		=> 1,
						'precio'		=> get_val($df['importe_otros']),
						'importe'		=> get_val($df['importe_otros']),
						'tax'			=> ''
					);
				}

				$xml_carga = generar_xml_datos($datos, TRUE);

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo webservice";

				$response = consumir_webservice($xml_carga);

				$ob_data = json_decode(utf8_encode($response));

				if ($response === FALSE)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Webservice no disponible";

					$ob_err++;

					if ( ! $nderr && $ob_err >= $max_ob_err)
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

						if ($ids)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registro(s)";

							$db->query("UPDATE facturas SET ts_carga_conta = NOW() WHERE id IN (" . implode(', ', $ids) . ")");
						}

						echo "\n";

						die();
					}
				}
				else
				{
					// Decodificar respuesta
					$ob_data = json_decode(utf8_encode($response));

					if ( ! isset($ob_data->status))
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Imposible decodificar respuesta de openbravo: {$response}";

						$ob_err++;

						if ( ! $nderr && $ob_err >= $max_ob_err)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

							if ($ids)
							{
								echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registros";

								$db->query("UPDATE facturas SET ts_carga_conta = NOW() WHERE id IN (" . implode(', ', $ids) . ")");
							}

							echo "\n";

							die();
						}
					}
					else if ($ob_data->status < 0)
					{
						$msg = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : (isset($ob_data->mensaje) ? $ob_data->mensaje : print_r($response, TRUE)))));

						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Respuesta de openbravo: {$msg}";
					}
					else
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de documento a openbravo exitosa";

						$ids[] = $df['id'];
					}
				}
			}

			if ($ids)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registros\n";

				$db->query("UPDATE facturas SET ts_carga_conta = NOW() WHERE id IN (" . implode(', ', $ids) . ")");
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No ha sido posible cargar ningun documento\n";
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/con/FacturasCargaOBConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
