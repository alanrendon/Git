<?php

// Crear documento de prueba

$xml = new DOMDocument('1.0', 'UTF-8');

$xml->xmlStandalone = TRUE;
$xml->formatOutput = TRUE;

$xml_invoicelist = $xml->createElement('invoicelist');
$xml->appendChild($xml_invoicelist);

$xml_invoices = $xml->createElement('invoices');
$xml_invoicelist->appendChild($xml_invoices);

$xml_invoice = $xml->createElement('invoice');
$xml_invoices->appendChild($xml_invoice);

$xml_accountingDate = $xml->createElement('accountingDate', utf8_decode('2014-12-30'));
$xml_invoice->appendChild($xml_accountingDate);

$xml_active = $xml->createElement('active', 'Y');
$xml_invoice->appendChild($xml_active);

$xml_bussinesPartnerValue = $xml->createElement('bussinesPartnerValue');
$xml_invoice->appendChild($xml_bussinesPartnerValue);

$xml_categoryValue = $xml->createElement('categoryValue', utf8_decode(/*'GT'*/'CL'));
$xml_bussinesPartnerValue->appendChild($xml_categoryValue);

$xml_description = $xml->createElement('description', utf8_decode('Factura de Prueba'));
$xml_bussinesPartnerValue->appendChild($xml_description);

$xml_fiscalCode = $xml->createElement('fiscalCode', utf8_decode('GCU091221JF2'));
$xml_bussinesPartnerValue->appendChild($xml_fiscalCode);

$xml_locations = $xml->createElement('locations');
$xml_bussinesPartnerValue->appendChild($xml_locations);

$xml_location = $xml->createElement('location');
$xml_locations->appendChild($xml_location);

$xml_address1 = $xml->createElement('address1', utf8_decode('Estado de Mexico, '));
$xml_location->appendChild($xml_address1);

$xml_city = $xml->createElement('city', utf8_decode('Sevilla'));
$xml_location->appendChild($xml_city);

$xml_countryValue = $xml->createElement('countryValue', utf8_decode('ES'));
$xml_location->appendChild($xml_countryValue);

$xml_postal = $xml->createElement('postal', utf8_decode('53115'));
$xml_location->appendChild($xml_postal);

$xml_regionValue = $xml->createElement('regionValue', utf8_decode('ESTADO DE MEXICO'));
$xml_location->appendChild($xml_regionValue);

$xml_calle = $xml->createElement('calle', utf8_decode('CENTURION'));
$xml_location->appendChild($xml_calle);

$xml_colonia = $xml->createElement('colonia', utf8_decode('LOMAS DE LAS FUENTES'));
$xml_location->appendChild($xml_colonia);

$xml_estado = $xml->createElement('estado', utf8_decode('EDOMEX'));
$xml_location->appendChild($xml_estado);

$xml_localidad = $xml->createElement('localidad', utf8_decode(''));
$xml_location->appendChild($xml_localidad);

$xml_municipio = $xml->createElement('municipio', utf8_decode('NAUCALPAN'));
$xml_location->appendChild($xml_municipio);

$xml_noexterior = $xml->createElement('noexterior', utf8_decode('1'));
$xml_location->appendChild($xml_noexterior);

$xml_nointerior = $xml->createElement('nointerior', utf8_decode('LOCAL A'));
$xml_location->appendChild($xml_nointerior);

$xml_name = $xml->createElement('name', utf8_decode('RESTAURANTES LUNA LLENA, S.A.'));
$xml_bussinesPartnerValue->appendChild($xml_name);

$xml_value = $xml->createElement('value', utf8_decode('GCU091221JF2'));
$xml_bussinesPartnerValue->appendChild($xml_value);

$xml_vendor = $xml->createElement('vendor', utf8_decode(/*'N'*/'Y'));
$xml_bussinesPartnerValue->appendChild($xml_vendor);

$xml_customer = $xml->createElement('customer', utf8_decode(/*'Y'*/'N'));
$xml_bussinesPartnerValue->appendChild($xml_customer);

$xml_clientValue = $xml->createElement('clientValue', utf8_decode('GLecaroz'));
$xml_invoice->appendChild($xml_clientValue);

$xml_creationDate = $xml->createElement('creationDate', utf8_decode('2014-12-30'));
$xml_invoice->appendChild($xml_creationDate);

$xml_curencyValue = $xml->createElement('curencyValue', utf8_decode('MXN'));
$xml_invoice->appendChild($xml_curencyValue);

$xml_documentNo = $xml->createElement('documentNo', utf8_decode('G49-001'));
$xml_invoice->appendChild($xml_documentNo);

$xml_documentTypeValue = $xml->createElement('documentTypeValue', utf8_decode('G-49'));
$xml_invoice->appendChild($xml_documentTypeValue);

$xml_dueAmount = $xml->createElement('dueAmount', utf8_decode('11'));
$xml_invoice->appendChild($xml_dueAmount);

$xml_formOfPayment = $xml->createElement('formOfPayment', utf8_decode('5'));
$xml_invoice->appendChild($xml_formOfPayment);

$xml_grandTotalAmount = $xml->createElement('grandTotalAmount', utf8_decode('5.8'));
$xml_invoice->appendChild($xml_grandTotalAmount);

$xml_invoiceDate = $xml->createElement('invoiceDate', utf8_decode('2014-12-30'));
$xml_invoice->appendChild($xml_invoiceDate);

$xml_invoicelines = $xml->createElement('invoicelines');
$xml_invoice->appendChild($xml_invoicelines);

// Detalles
$xml_invoiceline = $xml->createElement('invoiceline');
$xml_invoicelines->appendChild($xml_invoiceline);

$xml_line_active = $xml->createElement('active', utf8_decode('Y'));
$xml_invoiceline->appendChild($xml_line_active);

$xml_line_businessPartnerValue = $xml->createElement('businessPartnerValue', utf8_decode('Gurpo cusoft. SA de C.V'));
$xml_invoiceline->appendChild($xml_line_businessPartnerValue);

$xml_line_clientValue = $xml->createElement('clientValue', utf8_decode('GLecaroz'));
$xml_invoiceline->appendChild($xml_line_clientValue);

$xml_line_descriptionOnly = $xml->createElement('descriptionOnly', utf8_decode('N'));
$xml_invoiceline->appendChild($xml_line_descriptionOnly);

$xml_line_lineNo = $xml->createElement('lineNo', utf8_decode('10'));
$xml_invoiceline->appendChild($xml_line_lineNo);

$xml_line_editLineAmount = $xml->createElement('editLineAmount', utf8_decode('N'));
$xml_invoiceline->appendChild($xml_line_editLineAmount);

$xml_line_excludeForWithHolding = $xml->createElement('excludeForWithHolding', utf8_decode('N'));
$xml_invoiceline->appendChild($xml_line_excludeForWithHolding);

$xml_line_financialInvoiceLine = $xml->createElement('financialInvoiceLine', utf8_decode('N'));
$xml_invoiceline->appendChild($xml_line_financialInvoiceLine);

$xml_line_invoicedQuantity = $xml->createElement('invoicedQuantity', utf8_decode('1'));
$xml_invoiceline->appendChild($xml_line_invoicedQuantity);

$xml_line_lineNetAmount = $xml->createElement('lineNetAmount', utf8_decode('5'));
$xml_invoiceline->appendChild($xml_line_lineNetAmount);

$xml_line_listPrice = $xml->createElement('listPrice', utf8_decode('6.5'));
$xml_invoiceline->appendChild($xml_line_listPrice);

$xml_line_organizationValue = $xml->createElement('organizationValue', utf8_decode('49'));
$xml_invoiceline->appendChild($xml_line_organizationValue);

$xml_line_priceLimit = $xml->createElement('priceLimit', utf8_decode('0'));
$xml_invoiceline->appendChild($xml_line_priceLimit);

$xml_line_productValue = $xml->createElement('productValue', utf8_decode('MP1'));
$xml_invoiceline->appendChild($xml_line_productValue);

$xml_line_standardPrice = $xml->createElement('standardPrice', utf8_decode('5'));
$xml_invoiceline->appendChild($xml_line_standardPrice);

$xml_line_taxValue = $xml->createElement('taxValue', utf8_decode('IVA 16'));
$xml_invoiceline->appendChild($xml_line_taxValue);

$xml_line_unitPrice = $xml->createElement('unitPrice', utf8_decode('5'));
$xml_invoiceline->appendChild($xml_line_unitPrice);

$xml_line_productOrderUOMValue = $xml->createElement('productOrderUOMValue', utf8_decode(''));
$xml_invoiceline->appendChild($xml_line_productOrderUOMValue);

// ------------------------------------------------------------------------------------------------------------

$xml_organizationValue = $xml->createElement('organizationValue', utf8_decode('49'));
$xml_invoice->appendChild($xml_organizationValue);

$xml_paymentComplete = $xml->createElement('paymentComplete', utf8_decode('N'));
$xml_invoice->appendChild($xml_paymentComplete);

$xml_paymentMethodValue = $xml->createElement('paymentMethodValue', utf8_decode('Deposito Bancario'));
$xml_invoice->appendChild($xml_paymentMethodValue);

$xml_paymentTermsValue = $xml->createElement('paymentTermsValue', utf8_decode('Contado'));
$xml_invoice->appendChild($xml_paymentTermsValue);

$xml_priceListValue = $xml->createElement('priceListValue', utf8_decode('Compras'));
$xml_invoice->appendChild($xml_priceListValue);

$xml_salesTransaction = $xml->createElement('salesTransaction', utf8_decode('N'));
$xml_invoice->appendChild($xml_salesTransaction);

$xml_orderReference = $xml->createElement('orderReference', utf8_decode('1555'));
$xml_invoice->appendChild($xml_orderReference);

$xml_selfService = $xml->createElement('selfService', utf8_decode('N'));
$xml_invoice->appendChild($xml_selfService);

$xml_summedLineAmount = $xml->createElement('summedLineAmount', utf8_decode('5.8'));
$xml_invoice->appendChild($xml_summedLineAmount);

$xml_totalPaid = $xml->createElement('totalPaid', utf8_decode('0'));
$xml_invoice->appendChild($xml_totalPaid);

$xml_transactionDocumentValue = $xml->createElement('transactionDocumentValue', utf8_decode('G-49'));
$xml_invoice->appendChild($xml_transactionDocumentValue);

// Guardar el XML y obtener la cadena de datos

$xml->save('tmp/test.xml');
$data = $xml->saveXML();

// Incluir libreria PestXML para comunicación con el webservice

// require_once('includes/PestXML.php');

// Datos de conexión y autenticación

$url = 'http://38.123.203.151:443/ob_lecaroz';
$ws = '/ws/mx.cusoft.importing.rest.insertLecInvoice';
$username = 'Openbravo';
$password = 'openbravo';

// Crear objeto Pest

// $pest = new Pest($url);

// Autenticar en el webservice

// $pest->setupAuth($username, $password);

// Consumir el servicio POST y obtener respuesta, en caso
// contrario mostrar mensaje de error

// try
// {
// 	$response = $pest->post($ws, $data);
// }
// catch(Pest_InvalidRecord $e)
// {
// 	echo $e->getMessage();
// }

$username = "Openbravo";
$password = "openbravo";

$stream_options = array(
	'http' => array(
		'method'  => 'POST',
		'header'  =>  "Authorization: Basic " . base64_encode("$username:$password") . " Content-Type: text/xml",
		'content' => $data,
	),
);

$context  = stream_context_create($stream_options);
$response = file_get_contents($url . $ws, null, $context);

echo $response;
