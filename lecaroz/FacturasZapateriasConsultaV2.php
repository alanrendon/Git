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

		$nombre_xml = "carga-conta-zap-{$datos['cabecera']['id']}-{$datos['cabecera']['num_cia']}-{$datos['cabecera']['clave_pro']}-{$datos['cabecera']['num_fact']}.xml";

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
			$tpl = new TemplatePower('plantillas/zap/FacturasZapateriasConsultaV2Inicio.tpl');
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
				tipo_cia = 4
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

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

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
					$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
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
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
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
					$condiciones[] = 'f.cosgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = "f.fecha = '{$_REQUEST['fecha1']}'";
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = "f.fecha <= '{$_REQUEST['fecha2']}'";
				}
			}

			if ((isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '')
				|| (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '')) {
				if ((isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '')
					&& (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '')) {
					$condiciones[] = "ec.fecha_con BETWEEN '{$_REQUEST['fecha_cobro1']}' AND '{$_REQUEST['fecha_cobro2']}'";
				} else if (isset($_REQUEST['fecha_cobro1']) && $_REQUEST['fecha_cobro1'] != '') {
					$condiciones[] = "ec.fecha_con = '{$_REQUEST['fecha_cobro1']}'";
				} else if (isset($_REQUEST['fecha_cobro2']) && $_REQUEST['fecha_cobro2'] != '') {
					$condiciones[] = "ec.fecha_con <= '{$_REQUEST['fecha_cobro2']}'";
				}
			}

			if ((isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '')
				|| (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '')) {
				if ((isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '')
					&& (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '')) {
					$condiciones[] = "f.tscap::DATE BETWEEN '{$_REQUEST['fecha_cap1']}' AND '{$_REQUEST['fecha_cap2']}'";
				} else if (isset($_REQUEST['fecha_cap1']) && $_REQUEST['fecha_cap1'] != '') {
					$condiciones[] = "f.tscap::DATE = '{$_REQUEST['fecha_cap1']}'";
				} else if (isset($_REQUEST['fecha_cap2']) && $_REQUEST['fecha_cap2'] != '') {
					$condiciones[] = "f.tscap::DATE <= '{$_REQUEST['fecha_cap2']}'";
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '') {
				$facturas = array();
				$facturas_between = array();

				$pieces = explode(',', $_REQUEST['facturas']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$facturas_between[] =  "f.num_fact BETWEEN '" . $exp[0] . "' AND '" . $exp[1] . "'";
					}
					else {
						$facturas[] = $piece;
					}
				}

				$partes = array();

				if (count($facturas) > 0) {
					$partes[] = "f.num_fact IN ('" . implode("', '", $facturas) . "')";
				}

				if (count($facturas_between) > 0) {
					$partes[] = implode(' OR ', $facturas_between);
				}

				if (count($partes) > 0) {
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}

			if ($_REQUEST['status'] > 0) {
				if ($_REQUEST['status'] == 1)
				{
					$condiciones[] = 'f.id IS NULL';
				}
				else
				{
					$condiciones[] = 'f.id IS NOT NULL AND f.fecha_cheque IS NOT NULL';
				}

				if ($_REQUEST['status'] == 2 && $_REQUEST['pag'] > 0) {
					$condiciones[] = 'ec.id IS NOT NULL AND ec.fecha_con IS ' . ($_REQUEST['pag'] == 1 ? 'NULL' : 'NOT NULL');
				}
			}

			if ( ! isset($_REQUEST['incluir_facturas']))
			{
				$condiciones[] = "f.clave > 0";
			}

			if ( ! isset($_REQUEST['incluir_remisiones']))
			{
				$condiciones[] = "(f.clave IS NULL OR f.clave = 0)";
			}

			$condiciones_string = implode(' AND ', $condiciones);

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
						f.fecha_rec AS recibido,
						f.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.rfc AS rfc_cia,
						f.concepto,
						f.importe,
						f.faltantes,
						f.dif_precio,
						CASE
							WHEN f.dev > 0 THEN
								f.dev
							ELSE
								COALESCE((
									SELECT
										SUM(importe)
									FROM
										devoluciones_zap
									WHERE
										num_proveedor = f.num_proveedor
										AND num_fact = f.num_fact
								), 0)
						END AS devoluciones,
						f.pdesc1,
						f.pdesc2,
						f.pdesc3,
						f.pdesc4,
						f.desc1,
						f.desc2,
						f.desc3,
						f.desc4,
						f.iva,
						CASE
							WHEN f.pivaret != 0 AND COALESCE(f.ivaret, 0) = 0 THEN
								f.importe * ABS(f.pivaret) / 100
							ELSE
								ABS(f.ivaret)
						END AS ret_iva,
						CASE
							WHEN f.pisr != 0 AND COALESCE(f.isr, 0) = 0 THEN
								f.importe * ABS(f.pisr) / 100
							ELSE
								ABS(f.isr)
						END AS ret_isr,
						f.total,
						f.fletes,
						f.otros,
						f.folio,
						f.tspago::DATE AS fecha_pago,
						ec.fecha_con AS fecha_cobro,
						f.codgastos AS gasto,
						cg.descripcion AS nombre_gasto,
						f.cuenta AS banco,
						ch.cod_mov,
						ch.fecha_cancelacion,
						f.xml_file,
						f.pdf_file,
						COALESCE((
							SELECT
								TRUE
							FROM
								balances_zap
							WHERE
								num_cia = f.num_cia
								AND anio = EXTRACT(YEAR FROM f.fecha)
								AND mes = EXTRACT(MONTH FROM f.fecha)
						), FALSE) AS balance_generado
					FROM
						facturas_zap f
						LEFT JOIN catalogo_gastos cg ON (cg.codgastos = f.codgastos)
						LEFT JOIN cheques ch ON (
							ch.num_cia = f.num_cia
							AND ch.cuenta = f.cuenta
							AND ch.folio = f.folio
						)
						LEFT JOIN estado_cuenta ec ON (
							ec.num_cia = f.num_cia
							AND ec.cuenta = f.cuenta
							AND ec.folio = f.folio
						)
						LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
						LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
					WHERE
						{$condiciones_string}
				) AS resultado

			ORDER BY
				num_pro, {$orden_string}";

			$query = $db->query($sql);

			if ($query) {
				$tpl = new TemplatePower('plantillas/zap/FacturasZapateriasConsultaV2Resultado.tpl');
				$tpl->prepare();

				$num_pro = NULL;

				$g_importe = 0;
				$g_faltantes = 0;
				$g_dif_precio = 0;
				$g_devoluciones = 0;
				$g_descuentos = 0;
				$g_iva = 0;
				$g_ret_iva = 0;
				$g_ret_isr = 0;
				$g_fletes = 0;
				$g_otros = 0;
				$g_total = 0;

				foreach ($query as $row) {
					if ($num_pro != $row['num_pro']) {
						$num_pro = $row['num_pro'];

						$tpl->newBlock('pro');

						$tpl->assign('num_pro', $row['num_pro']);
						$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));

						$t_importe = 0;
						$t_faltantes = 0;
						$t_dif_precio = 0;
						$t_devoluciones = 0;
						$t_descuentos = 0;
						$t_iva = 0;
						$t_ret_iva = 0;
						$t_ret_isr = 0;
						$t_fletes = 0;
						$t_otros = 0;
						$t_total = 0;
					}

					$importe = $row['importe'] - $row['faltantes'] - $row['dif_precio'] - $row['devoluciones'];
					$desc1 = $row['pdesc1'] > 0 ? round($importe * $row['pdesc1'] / 100, 2) : ($row['desc1'] > 0 ? $row['desc1'] : 0);
					$desc2 = $row['pdesc2'] > 0 ? round(($importe - $desc1) * $row['pdesc2'] / 100, 2) : ($row['desc2'] > 0 ? $row['desc2'] : 0);
					$desc3 = $row['pdesc3'] > 0 ? round(($importe - $desc1 - $desc2) * $row['pdesc3'] / 100, 2) : ($row['desc3'] > 0 ? $row['desc3'] : 0);
					$desc4 = $row['pdesc4'] > 0 ? round(($importe - $desc1 - $desc2 - $desc3) * $row['pdesc4'] / 100, 2) : ($row['desc4'] > 0 ? $row['desc4'] : 0);
					$descuentos = $desc1 + $desc2 + $desc3 + $desc4;
					$subtotal = $importe - $desc1 - $desc2 - $desc3 - $desc4;
					$iva = $row['iva'] > 0 ? $subtotal * 0.16 : 0;
					$total = $subtotal + $iva - $row['ret_iva'] - $row['ret_isr'] - $row['fletes'] + $row['otros'];

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('data_fac', htmlentities(json_encode(array(
						'id'	=> get_val($row['id'])
					))));
					$tpl->assign('num_fact', utf8_encode($row['num_fact']));
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('recibido', $row['recibido'] != '' ? $row['recibido'] : '&nbsp;');
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('rfc_cia', utf8_encode($row['rfc_cia']));
					$tpl->assign('concepto', trim($row['concepto']) != '' ? trim(utf8_encode($row['concepto'])) : '&nbsp;');
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('nombre_gasto', $row['nombre_gasto']);
					$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('faltantes', $row['faltantes'] != 0 ? number_format($row['faltantes'], 2) : '&nbsp;');
					$tpl->assign('dif_precio', $row['dif_precio'] != 0 ? number_format($row['dif_precio'], 2) : '&nbsp;');
					$tpl->assign('devoluciones', $row['devoluciones'] != 0 ? number_format($row['devoluciones'], 2) : '&nbsp;');
					$tpl->assign('descuentos', $descuentos != 0 ? number_format($descuentos, 2) : '&nbsp;');
					$tpl->assign('iva', $iva != 0 ? number_format($iva, 2) : '&nbsp;');
					$tpl->assign('ret_iva', $row['ret_iva'] != 0 ? number_format($row['ret_iva'], 2) : '&nbsp;');
					$tpl->assign('ret_isr', $row['ret_isr'] != 0 ? number_format($row['ret_isr'], 2) : '&nbsp;');
					$tpl->assign('fletes', $row['fletes'] != 0 ? number_format($row['fletes'], 2) : '&nbsp;');
					$tpl->assign('otros', $row['otros'] != 0 ? number_format($row['otros'], 2) : '&nbsp;');
					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
					$tpl->assign('fecha_pago', $row['fecha_pago'] != '' ? $row['fecha_pago'] : '&nbsp;');
					$tpl->assign('banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['fecha_cancelacion'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha_cobro', $row['fecha_cobro'] != '' ? $row['fecha_cobro'] : '&nbsp;');
					$tpl->assign('cancelar_disabled', /*(in_array($_SESSION['iduser'], array(1, 4)) && ($row['fecha_pago'] != '')) || ( ! in_array($_SESSION['iduser'], array(1, 4)) && ($row['fecha_pago'] != '' || $row['balance_generado'] == 't')) ? '_gray' : ''*/'_gray');
					$tpl->assign('icono_cancelar_class', /*(in_array($_SESSION['iduser'], array(1, 4)) && ($row['fecha_pago'] != '')) || ( ! in_array($_SESSION['iduser'], array(1, 4)) && ($row['fecha_pago'] != '' || $row['balance_generado'] == 't')) ? '' : ' class="icono"'*/'');
					$tpl->assign('cfd_disabled', $row['xml_file'] == '' ? '_gray' : '');
					$tpl->assign('icono_cfd_class', $row['xml_file'] != '' ? ' class="icono"' : '');

					$t_importe += $row['importe'];
					$t_faltantes += $row['faltantes'];
					$t_dif_precio += $row['dif_precio'];
					$t_devoluciones += $row['devoluciones'];
					$t_descuentos += $descuentos;
					$t_iva += $iva;
					$t_ret_iva += $row['ret_iva'];
					$t_ret_isr += $row['ret_isr'];
					$t_fletes += $row['fletes'];
					$t_otros += $row['otros'];
					$t_total += $total;

					$g_importe += $row['importe'];
					$g_faltantes += $row['faltantes'];
					$g_dif_precio += $row['dif_precio'];
					$g_devoluciones += $row['devoluciones'];
					$g_descuentos += $descuentos;
					$g_iva += $iva;
					$g_ret_iva += $row['ret_iva'];
					$g_ret_isr += $row['ret_isr'];
					$g_fletes += $row['fletes'];
					$g_otros += $row['otros'];
					$g_total += $total;

					$tpl->assign('pro.importe', number_format($t_importe, 2));
					$tpl->assign('pro.faltantes', number_format($t_faltantes, 2));
					$tpl->assign('pro.dif_precio', number_format($t_dif_precio, 2));
					$tpl->assign('pro.devoluciones', number_format($t_devoluciones, 2));
					$tpl->assign('pro.descuentos', number_format($t_descuentos, 2));
					$tpl->assign('pro.iva', number_format($t_iva, 2));
					$tpl->assign('pro.ret_iva', number_format($t_ret_iva, 2));
					$tpl->assign('pro.ret_isr', number_format($t_ret_isr, 2));
					$tpl->assign('pro.fletes', number_format($t_fletes, 2));
					$tpl->assign('pro.otros', number_format($t_otros, 2));
					$tpl->assign('pro.total', number_format($t_total, 2));

					$tpl->assign('_ROOT.importe', number_format($g_importe, 2));
					$tpl->assign('_ROOT.faltantes', number_format($g_faltantes, 2));
					$tpl->assign('_ROOT.dif_precio', number_format($g_dif_precio, 2));
					$tpl->assign('_ROOT.devoluciones', number_format($g_devoluciones, 2));
					$tpl->assign('_ROOT.descuentos', number_format($g_descuentos, 2));
					$tpl->assign('_ROOT.iva', number_format($g_iva, 2));
					$tpl->assign('_ROOT.ret_iva', number_format($g_ret_iva, 2));
					$tpl->assign('_ROOT.ret_isr', number_format($g_ret_isr, 2));
					$tpl->assign('_ROOT.fletes', number_format($g_fletes, 2));
					$tpl->assign('_ROOT.otros', number_format($g_otros, 2));
					$tpl->assign('_ROOT.total', number_format($g_total, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'cancelar_factura':
			$sql = '';

			if ($_REQUEST['tipo'] == 0)
			{
				$fac = $db->query("SELECT
					f.num_cia,
					cc.nombre AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.fecha,
					f.total AS importe
				FROM
					facturas_zap f
					LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");

				$sql .= "DELETE
				FROM
					pasivo_proveedores
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas_pendientes
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "INSERT INTO facturas_borradas (
					num_proveedor,
					num_cia,
					num_fact,
					fecha_mov,
					fecha_ven,
					imp_sin_iva,
					porciento_iva,
					importe_iva,
					porciento_ret_isr,
					porciento_ret_iva,
					codgastos,
					importe_total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				)
				SELECT
					num_proveedor,
					num_cia,
					num_fact,
					fecha,
					fecha,
					importe,
					piva,
					iva,
					pretencion_isr,
					pretencion_iva,
					codgastos,
					total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				FROM
					facturas
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas
				WHERE
					id = {$_REQUEST['id']};\n";
			}
			else if ($_REQUEST['tipo'] == 1)
			{
				$fac = $db->query("SELECT
					f.num_cia,
					cc.nombre AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.fecha,
					f.total AS importe
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");

				$mp = $db->query("SELECT
					num_cia,
					codmp,
					contenido,
					cantidad,
					precio
				FROM
					entrada_mp
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)");

				foreach ($mp as $pro)
				{
					$sql .= "UPDATE inventario_real
					SET existencia = existencia - {$pro['contenido']} * {$pro['cantidad']}
					WHERE
						num_cia = {$pro['num_cia']}
						AND codmp = {$pro['codmp']};\n";
				}

				$sql .= "DELETE
				FROM
					mov_inv_real
				WHERE
					tipo_mov = FALSE
					AND (num_cia, codmp, fecha, num_fact) IN (
						SELECT
							num_cia,
							codmp,
							fecha,
							num_fact
						FROM
							entrada_mp
						WHERE
							(num_proveedor, num_fact) IN (
								SELECT
									num_proveedor,
									num_fact
								FROM
									facturas
								WHERE
									id = {$_REQUEST['id']}
							)
					);\n";

				$sql .= "DELETE
				FROM
					entrada_mp
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					pasivo_proveedores
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas_pendientes
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "UPDATE fruta_remisiones
				SET
					num_fact = NULL,
					idfac = NULL,
					tsfac = NULL
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "UPDATE huevo_remisiones
				SET
					num_fact = NULL,
					idfac = NULL,
					tsfac = NULL
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "INSERT INTO facturas_borradas (
					num_proveedor,
					num_cia,
					num_fact,
					fecha_mov,
					fecha_ven,
					imp_sin_iva,
					porciento_iva,
					importe_iva,
					porciento_ret_isr,
					porciento_ret_iva,
					codgastos,
					importe_total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				)
				SELECT
					num_proveedor,
					num_cia,
					num_fact,
					fecha,
					fecha,
					importe,
					piva,
					iva,
					pretencion_isr,
					pretencion_iva,
					codgastos,
					total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				FROM
					facturas
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas
				WHERE
					id = {$_REQUEST['id']};\n";
			}
			else if ($_REQUEST['tipo'] == 2)
			{
				$fac = $db->query("SELECT
					f.num_cia,
					cc.nombre AS nombre_cia,
					f.num_proveedor AS num_pro,
					cp.nombre AS nombre_pro,
					f.num_fact,
					f.fecha,
					f.total AS importe
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					f.id = {$_REQUEST['id']}");

				$tan = $db->query("SELECT
					num_cia,
					fecha,
					SUM(litros) AS litros
				FROM
					factura_gas
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					)
				GROUP BY
					num_cia,
					fecha");

				$sql .= "UPDATE inventario_real
				SET existencia = existencia - {$tan[0]['litros']}
				WHERE
					num_cia = {$tan[0]['num_cia']}
					AND codmp = 90;\n";

				$sql .= "DELETE
				FROM
					factura_gas
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					mov_inv_real
				WHERE
					num_cia = {$fac[0]['num_cia']}
					AND codmp = 90
					AND fecha = '{$fac[0]['fecha']}'
					AND tipo_mov = FALSE
					AND descripcion LIKE '%{$fac[0]['num_fact']}%';\n";

				$sql .= "DELETE
				FROM
					pasivo_proveedores
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas_pendientes
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "INSERT INTO facturas_borradas (
					num_proveedor,
					num_cia,
					num_fact,
					fecha_mov,
					fecha_ven,
					imp_sin_iva,
					porciento_iva,
					importe_iva,
					porciento_ret_isr,
					porciento_ret_iva,
					codgastos,
					importe_total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				)
				SELECT
					num_proveedor,
					num_cia,
					num_fact,
					fecha,
					fecha,
					importe,
					piva,
					iva,
					pretencion_isr,
					pretencion_iva,
					codgastos,
					total,
					tipo_factura,
					fecha_captura,
					iduser,
					concepto
				FROM
					facturas
				WHERE
					(num_proveedor, num_fact) IN (
						SELECT
							num_proveedor,
							num_fact
						FROM
							facturas
						WHERE
							id = {$_REQUEST['id']}
					);\n";

				$sql .= "DELETE
				FROM
					facturas
				WHERE
					id = {$_REQUEST['id']};\n";
			}
			else if ($_REQUEST['tipo'] == 3)
			{

			}

			$db->query($sql);

			/*
			@ Validar que la librería PHPMailer este cargada
			*/
			if ( ! class_exists('PHPMailer'))
			{
				include_once('includes/phpmailer/class.phpmailer.php');
			}

			$mail = new PHPMailer();

			$mail->IsSMTP();

			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'mollendo@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo');

			$mail->AddAddress('aclaraciones.contables.mollendo@lecaroz.com');

			$mail->Subject = utf8_decode('Cancelación de factura de compra');

			$mail->Body = utf8_decode("<p>A quién corresponda,</p><p>Se ha cancelado la factura <strong>{$fac[0]['num_fact']}</strong> del proveedor <strong>{$fac[0]['num_pro']} {$fac[0]['nombre_pro']}</strong> con fecha <strong>{$fac[0]['fecha']}</strong> por un importe de <strong>" . number_format($fac[0]['importe'], 2) . " pesos</strong>.</p>");

			$mail->IsHTML(true);

			if( ! $mail->Send())
			{
				return $mail->ErrorInfo;
			}
			else
			{
				return TRUE;
			}

			break;

		case 'visualizar_cfd':
			$path = 'cfds_proveedores/';

			$result = $db->query("SELECT pdf_file FROM facturas f WHERE id = {$_REQUEST['id']}");

			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="factura.pdf"');

			readfile($path . '/' . utf8_encode($result[0]['pdf_file']));

			break;

		case 'imprimir_cfd':
			$path = 'cfds_proveedores/';

			$result = $db->query("SELECT pdf_file FROM facturas f WHERE id = {$_REQUEST['id']}");

			$printer = $_SESSION['tipo_usuario'] == 2 ? 'elite' : 'general';

			shell_exec('lp -d ' . $printer . ' ' . $path . '/' . $result[0]['pdf_file']);

			break;

		case 'listado':
			$condiciones = array();

			$condiciones[] = 'fecha_con IS NULL';

			$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'ec.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			$condiciones_aux = array();

			if (isset($_REQUEST['depositos'])) {
				$condicion = '(ec.tipo_mov = FALSE';

				if (isset($_REQUEST['codigos_depositos']) && trim($_REQUEST['codigos_depositos']) != '') {
					$codigos_depositos = array();

					$pieces = explode(',', $_REQUEST['codigos_depositos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_depositos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_depositos[] = $piece;
						}
					}

					if (count($codigos_depositos) > 0) {
						$condicion .= ' AND ec.cod_mov IN (' . implode(', ', $codigos_depositos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			if (isset($_REQUEST['cargos'])) {
				$condicion = '(ec.tipo_mov = TRUE';

				if (isset($_REQUEST['codigos_cargos']) && trim($_REQUEST['codigos_cargos']) != '') {
					$codigos_cargos = array();

					$pieces = explode(',', $_REQUEST['codigos_cargos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_cargos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_cargos[] = $piece;
						}
					}

					if (count($codigos_cargos) > 0) {
						$condicion .= 'ec.cod_mov IN (' . implode(', ', $codigos_cargos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			$condiciones[] = '(' . implode(' OR ', $condiciones_aux) . ')';

			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ec.cuenta
						AS banco,
					cc.clabe_cuenta
						AS cuenta_banorte,
					cc.clabe_cuenta2
						AS cuenta_santander,
					ec.fecha,
					ec.fecha_con
						AS conciliado,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					c.codgastos || \' \' || (
						SELECT
							descripcion
						FROM
							catalogo_gastos
						WHERE
							codgastos = c.codgastos
					)
						AS gasto,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo,
					ec.cod_mov
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';

			$query = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EstadoCuentaNoConciliadosListado.tpl');
			$tpl->prepare();

			if ($query) {
				$tpl->newBlock('reporte');

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$cuentas = array();

						if (trim($row['cuenta_banorte']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> ' . $row['cuenta_banorte'];
						}

						if (trim($row['cuenta_santander']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> ' . $row['cuenta_santander'];
						}

						$tpl->assign('cuentas', $cuentas ? '<br />' . implode('<br />', $cuentas) : '');

						$depositos = 0;
						$cargos = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('banco', $row['banco'] == 1 ? 'Banorte' : 'Santander');
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('deposito', $row['deposito'] != 0 ? number_format($row['deposito'], 2) : '&nbsp;');
					$tpl->assign('cargo', $row['cargo'] != 0 ? number_format($row['cargo'], 2) : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span class="' . ($row['cod_mov'] == 41 ? 'purple' : ($row['cod_mov'] == 41 ? 'orange' : 'green')) . '" info="' . $row['gasto'] . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('beneficiario', $row['beneficiario'] != '' ? utf8_encode($row['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', $row['concepto'] != '' ? utf8_encode($row['concepto']) : '&nbsp;');
					$tpl->assign('codigo', utf8_encode($row['codigo']));

					$depositos += $row['deposito'];
					$cargos += $row['cargo'];

					$tpl->assign('cia.depositos', number_format($depositos, 2));
					$tpl->assign('cia.cargos', number_format($cargos, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = 'fecha_con IS NULL';

			$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'ec.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			$condiciones_aux = array();

			if (isset($_REQUEST['depositos'])) {
				$condicion = '(ec.tipo_mov = FALSE';

				if (isset($_REQUEST['codigos_depositos']) && trim($_REQUEST['codigos_depositos']) != '') {
					$codigos_depositos = array();

					$pieces = explode(',', $_REQUEST['codigos_depositos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_depositos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_depositos[] = $piece;
						}
					}

					if (count($codigos_depositos) > 0) {
						$condicion .= ' AND ec.cod_mov IN (' . implode(', ', $codigos_depositos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			if (isset($_REQUEST['cargos'])) {
				$condicion = '(ec.tipo_mov = TRUE';

				if (isset($_REQUEST['codigos_cargos']) && trim($_REQUEST['codigos_cargos']) != '') {
					$codigos_cargos = array();

					$pieces = explode(',', $_REQUEST['codigos_cargos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_cargos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_cargos[] = $piece;
						}
					}

					if (count($codigos_cargos) > 0) {
						$condicion .= 'ec.cod_mov IN (' . implode(', ', $codigos_cargos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			$condiciones[] = '(' . implode(' OR ', $condiciones_aux) . ')';

			$sql = '
				SELECT
					/*ec.num_cia || \' \' || cc.nombre_corto
						AS cia,*/
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					CASE
						WHEN ec.cuenta = 1 THEN
							\'BANORTE\'
						WHEN ec.cuenta = 2 THEN
							\'SANTANDER\'
					END
						AS banco,
					/*cc.clabe_cuenta
						AS cuenta_banorte,
					cc.clabe_cuenta2
						AS cuenta_santander,*/
					CASE
						WHEN ec.cuenta = 1 THEN
							cc.clabe_cuenta
						WHEN ec.cuenta = 2 THEN
							cc.clabe_cuenta2
					END
						AS cuenta,
					ec.fecha,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';

			$query = $db->query($sql);

			//$data = '"MOVIMIENTOS NO CONCILIADOS"' . "\n\n";
			$data .= '"#CIA","COMPAÑIA","BANCO","CUENTA","FECHA","DEPOSITO","CARGO","FOLIO","BENEFICIARIO","CONCEPTO","CODIGO"' . "\n";

			if ($query) {
				// $cia = NULL;

				// foreach ($query as $row) {
				// 	if ($cia != $row['cia']) {
				// 		if ($cia != NULL) {
				// 			$data .= '"","TOTAL","' . $depositos . '","' . $cargos . '"' . "\n\n";
				// 		}

				// 		$cia = $row['cia'];

				// 		$data .= '"' . utf8_encode($row['cia']) . '"' . "\n";

				// 		$cuentas = array();

				// 		if (trim($row['cuenta_banorte']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
				// 			$cuentas[] = '"CUENTA BANORTE","\'' . $row['cuenta_banorte'] . '"';
				// 		}

				// 		if (trim($row['cuenta_santander']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
				// 			$cuentas[] = '"CUENTA SANTANDER","\'' . $row['cuenta_santander'] . '"';
				// 		}

				// 		$data .= implode("\n", $cuentas) . "\n";

				// 		$data .= "\n" . '"BANCO","FECHA","DEPOSITO","CARGO","FOLIO","BENEFICIARIO","CONCEPTO","CODIGO"' . "\n";

				// 		$depositos = 0;
				// 		$cargos = 0;
				// 	}

				// 	unset($row['cia']);
				// 	unset($row['cuenta_banorte']);
				// 	unset($row['cuenta_santander']);

				// 	$data .= '"' . implode('","', $row) . '"' . "\n";

				// 	$depositos += $row['deposito'];
				// 	$cargos += $row['cargo'];
				// }

				// if ($cia != NULL) {
				// 	$data .= '"","TOTAL","' . $depositos . '","' . $cargos . '"' . "\n";
				// }

				foreach ($query as $key => $value) {
					$data .= '"' . implode('","', $value) . '"' . "\n";
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=noconciliados.csv');

			echo $data;

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
				/*f.fecha*/f.tscap::DATE AS fecha,
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
				f.importe,
				f.faltantes,
				f.dif_precio AS diferencia_precio,
				CASE
					WHEN f.dev > 0 THEN
						f.dev
					ELSE
						COALESCE((
							SELECT
								SUM(importe)
							FROM
								devoluciones_zap
							WHERE
								num_proveedor = f.num_proveedor
								AND num_fact = f.num_fact
						), 0)
				END AS devoluciones,
				f.pdesc1,
				f.pdesc2,
				f.pdesc3,
				f.pdesc4,
				f.desc1,
				f.desc2,
				f.desc3,
				f.desc4,
				f.iva,
				f.ivaret AS retencion_iva,
				f.isr AS retencion_isr,
				f.total,
				f.fletes,
				f.otros,
				/* Desglose de factura */
				f.concepto AS descripcion,
				f.importe AS precio,
				1 AS cantidad,
				CASE
					WHEN f.codgastos = 33 THEN
						'C'
					ELSE
						'G'
				END AS tipo_registro,
				CASE
					WHEN f.codgastos = 33 THEN
						'CZ'
					ELSE
						'G'
				END AS tipo_producto,
				CASE
					WHEN f.codgastos = 33 THEN
						NULL
					ELSE
						f.codgastos
				END AS codigo,
				CONCAT_WS(' + ', CASE WHEN f.pdesc1 > 0 THEN f.pdesc1 || '%' ELSE NULL END, CASE WHEN f.pdesc2 > 0 THEN f.pdesc2 || '%' ELSE NULL END, CASE WHEN f.pdesc3 > 0 THEN f.pdesc3 || '%' ELSE NULL END, CASE WHEN f.pdesc4 > 0 THEN f.pdesc4 || '%' ELSE NULL END) AS descuentos_string
			FROM
				facturas_zap f
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
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Registro: {$cont} de {$num_rows}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Id factura: {$df['id']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Compañía: {$df['num_cia']} " . utf8_encode($df['nombre_cia']);
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Proveedor: {$df['num_pro']} " . utf8_encode($df['nombre_pro']);
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Folio: {$df['num_fact']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Fecha: {$df['fecha']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Tipo: {$df['tipo_registro']}";
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Importe: " . number_format($df['total'], 2);

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Generando datos de carga para openbravo";

				$importe = $df['importe'] - $df['faltantes'] - $df['diferencia_precio'] - $df['devoluciones'];
				$desc1 = $df['pdesc1'] > 0 ? round($importe * $df['pdesc1'] / 100, 2) : ($df['desc1'] > 0 ? $df['desc1'] : 0);
				$desc2 = $df['pdesc2'] > 0 ? round(($importe - $desc1) * $df['pdesc2'] / 100, 2) : ($df['desc2'] > 0 ? $df['desc2'] : 0);
				$desc3 = $df['pdesc3'] > 0 ? round(($importe - $desc1 - $desc2) * $df['pdesc3'] / 100, 2) : ($df['desc3'] > 0 ? $df['desc3'] : 0);
				$desc4 = $df['pdesc4'] > 0 ? round(($importe - $desc1 - $desc2 - $desc3) * $df['pdesc4'] / 100, 2) : ($df['desc4'] > 0 ? $df['desc4'] : 0);
				$descuentos = $desc1 + $desc2 + $desc3 + $desc4;
				$subtotal = $importe - $desc1 - $desc2 - $desc3 - $desc4;
				$iva = $df['iva'] > 0 ? $subtotal * 0.16 : 0;
				$total = $subtotal + $iva - abs($df['retencion_iva']) - abs($df['retencion_isr']) - $df['fletes'] + $df['otros'];

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
						'descuentos'			=> get_val($descuentos + $df['faltantes'] + $df['diferencia_precio'] + $df['devoluciones']),
						'iva'					=> get_val($iva),
						'retencion_isr'			=> get_val($df['retencion_isr']),
						'retencion_iva'			=> get_val($df['retencion_iva']),
						'total'					=> get_val($total)
					),
					'detalle'	=> array()
				);

				$tax = $df['iva'] != 0 && $df['retencion_iva'] != 0 && $df['retencion_isr'] != 0 ? 'HONORARIOS/ARRENDAMIENTOS' : ($df['iva'] != 0 && $df['retencion_iva'] != 0 ? 'IVA + RET 4%' : ($df['retencion_isr'] != 0 ? (abs(round($df['retencion_isr'] * 100 / $importe, 2)) == 10 ? 'ARRENDAMIENTO HABITACION' : 'R35%') : ($df['iva'] != 0 ? 'IVA' : 'IVA 0')));

				$datos['detalle'][] = array(
					'clave'			=> $df['tipo_producto'] . $df['codigo'],
					'descripcion'	=> $df['descripcion'],
					'cantidad'		=> 1,
					'precio'		=> get_val($df['importe']),
					'importe'		=> get_val($df['importe']),
					'tax'			=> $tax
				);

				if (get_val($df['devoluciones']) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D3',
						'descripcion'	=> 'DEVOLUCIONES',
						'cantidad'		=> 1,
						'precio'		=> -get_val($df['devoluciones']),
						'importe'		=> -get_val($df['devoluciones']),
						'tax'			=> $tax
					);
				}

				if (get_val($df['faltantes']) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D3',
						'descripcion'	=> 'FALTANTES',
						'cantidad'		=> 1,
						'precio'		=> -get_val($df['faltantes']),
						'importe'		=> -get_val($df['faltantes']),
						'tax'			=> $tax
					);
				}

				if (get_val($df['diferencia_precio']) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D3',
						'descripcion'	=> 'DIFERENCIA DE PRECIO',
						'cantidad'		=> 1,
						'precio'		=> -get_val($df['diferencia_precio']),
						'importe'		=> -get_val($df['diferencia_precio']),
						'tax'			=> $tax
					);
				}

				if (get_val($descuentos) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D3',
						'descripcion'	=> $df['descuentos_string'],
						'cantidad'		=> 1,
						'precio'		=> -get_val($descuentos),
						'importe'		=> -get_val($descuentos),
						'tax'			=> $tax
					);
				}

				if (get_val($df['fletes']) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D4',
						'descripcion'	=> 'FLETES',
						'cantidad'		=> 1,
						'precio'		=> -get_val($df['fletes']),
						'importe'		=> -get_val($df['fletes']),
						'tax'			=> 'IVA 0'
					);
				}

				if (get_val($df['otros']) > 0)
				{
					$datos['detalle'][] = array(
						'clave'			=> 'D4',
						'descripcion'	=> 'OTROS',
						'cantidad'		=> 1,
						'precio'		=> get_val($df['otros']),
						'importe'		=> get_val($df['otros']),
						'tax'			=> 'IVA 0'
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

							$db->query("UPDATE facturas_zap SET ts_carga_conta = NOW() WHERE id IN (" . implode(', ', $ids) . ")");
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

				$db->query("UPDATE facturas_zap SET ts_carga_conta = NOW() WHERE id IN (" . implode(', ', $ids) . ")");
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No ha sido posible cargar ningun documento\n";
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/zap/FacturasZapateriasConsultaV2.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
