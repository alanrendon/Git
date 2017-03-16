<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$params = getopt("", array(
	'id:',
	'fecha1:',
	'fecha2:',
	'cias:',
	'no_cargar',
	'no_msg'
));

$condiciones = array();

if ($params)
{
	if (isset($params['id']))
	{
		$condiciones[] = "f.id = {$params['id']}";
	}

	if (isset($params['fecha1']) || isset($params['fecha2']))
	{
		$condiciones[] = "f.fecha BETWEEN '{$params['fecha1']}' AND '{$params['fecha2']}'";
	}

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$cias[] = $piece;
			}
		}

		if (count($cias) > 0)
		{
			$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}
}
else if (isset($_REQUEST))
{
	if (isset($_REQUEST['id']))
	{
		$condiciones[] = "f.id = {$_REQUEST['id']}";
	}

	if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2']))
	{
		$condiciones[] = "f.fecha BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
	}

	if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $_REQUEST['cias']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$cias[] = $piece;
			}
		}

		if (count($cias) > 0)
		{
			$condiciones[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}
}
else
{
	echo "No hay parametros de busqueda.\n\n";
	die;
}

$db = new DBclass($dsn, 'autocommit=yes');

$result = $db->query("SELECT COALESCE(COUNT(id), 0) AS num_rows FROM facturas_electronicas f WHERE " . implode(' AND ', $condiciones));

if ($result[0]['num_rows'] == 0)
{
	echo "No hay datos.\n\n";
	die;
}

// Parámetro que define si se generan mensajes informativos o no
$no_msg = isset($_REQUEST['no_msg']) || isset($params['no_msg']) ? TRUE : FALSE;

echo "Registros a insertar: {$result[0]['num_rows']}\n";

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

$num_rows = $result[0]['num_rows'];

$rows_per_query = 200;

$offset = 0;

$cont = 1;

while ($result = $db->query("SELECT
	f.num_cia,
	f.fecha,
	f.hora,
	s.serie,
	f.consecutivo
		AS folio,
	f.clave_cliente,
	f.nombre_cliente,
	f.rfc,
	f.calle,
	f.no_exterior,
	f.no_interior,
	f.colonia,
	f.localidad,
	f.referencia,
	f.municipio,
	f.estado,
	f.pais,
	f.codigo_postal,
	f.observaciones,
	f.importe
		AS subtotal,
	f.iva,
	f.total,
	f.email_cliente,
	f.tipo
		AS clasificacion,
	f.retencion_iva,
	f.retencion_isr,
	f.tipo_serie,
	f.porcentaje_descuento,
	f.descuento,
	f.tipo_pago,
	f.cuenta_pago,
	f.condiciones_pago,
	f.ieps,
	f.fecha_timbrado,
	f.uuid,
	f.no_certificado_sat,
	f.sello_cfd,
	f.sello_sat,
	f.cadena_original,
	f.documento_xml,
	f.no_certificado_digital,
	f.ob_response,
	d.clave_producto,
	d.cantidad,
	d.descripcion,
	d.precio,
	d.unidad,
	d.importe,
	d.numero_pedimento,
	d.fecha_entrada,
	d.aduana_entrada,
	d.iva
		AS iva_detalle,
	d.piva
		AS piva_detalle,
	d.pieps
		AS pieps_detalle,
	d.ieps
		AS ieps_detalle
FROM
	facturas_electronicas f
	LEFT JOIN facturas_electronicas_detalle d
		USING (num_cia, tipo_serie, consecutivo)
	LEFT JOIN facturas_electronicas_series s
		ON (s.num_cia = f.num_cia AND s.tipo_serie = f.tipo_serie AND f.consecutivo BETWEEN s.folio_inicial AND s.folio_final)
WHERE
	f.id IN (SELECT id FROM facturas_electronicas f WHERE " . implode(' AND ', $condiciones) . " ORDER BY num_cia, tipo_serie, consecutivo LIMIT {$rows_per_query} OFFSET {$offset})
ORDER BY
	f.num_cia,
	s.serie,
	f.consecutivo"))
{
	$num_cia = NULL;
	$serie = NULL;
	$folio = NULL;

	foreach ($result as $i => $df)
	{
		if ($num_cia != $df['num_cia'])
		{
			if (isset($folio) && $folio != NULL)
			{
				$xml_carga = generar_xml_datos($datos, isset($_REQUEST['no_cargar']) || isset($params['no_cargar']) ? TRUE : FALSE);

				if ( ! isset($_REQUEST['no_cargar']) && ! isset($params['no_cargar']))
				{
					$response = consumir_webservice($xml_carga);

					$ob_data = json_decode(utf8_encode($response));

					if ($response === FALSE)
					{
						$status = "Webservice no disponible.";
					}

					// Decodificar respuesta
					$ob_data = json_decode(utf8_encode($response));

					if ($ob_data->status < 0)
					{
						$status = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : print_r($response, TRUE))));
					}
					else
					{
						$status = "Carga de documento exitosa.";
					}
				}
				else
				{
					$status = "No se cargo documento a OpenBravo.";
				}

				echo "\n[#{$cont}] Documento CIA: {$num_cia} SERIE: {$serie} FACT: {$folio} RESPUESTA: {$status}";
				// echo "<br />{$xml_carga}";

				$cont++;
			}

			$num_cia = $df['num_cia'];

			$serie = NULL;
			$folio = NULL;
		}

		if ($serie != $df['serie'])
		{
			if ($folio != NULL)
			{
				$xml_carga = generar_xml_datos($datos, isset($_REQUEST['no_cargar']) || isset($params['no_cargar']) ? TRUE : FALSE);

				if ( ! isset($_REQUEST['no_cargar']) && ! isset($params['no_cargar']))
				{
					$response = consumir_webservice($xml_carga);

					$ob_data = json_decode(utf8_encode($response));

					if ($response === FALSE)
					{
						$status = "Webservice no disponible.";
					}

					// Decodificar respuesta
					$ob_data = json_decode(utf8_encode($response));

					if ($ob_data->status < 0)
					{
						$status = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : print_r($response, TRUE))));
					}
					else
					{
						$status = "Carga de documento exitosa.";
					}
				}
				else
				{
					$status = "No se cargo documento a OpenBravo.";
				}

				echo "\n[#{$cont}] Documento CIA: {$num_cia} SERIE: {$serie} FACT: {$folio} RESPUESTA: {$status}";
				// echo "<br />{$xml_carga}";

				$cont++;
			}

			$serie = $df['serie'];

			$folio = NULL;
		}

		if ($folio != $df['folio'])
		{
			if ($folio != NULL)
			{
				$xml_carga = generar_xml_datos($datos, isset($_REQUEST['no_cargar']) || isset($params['no_cargar']) ? TRUE : FALSE);

				if ( ! isset($_REQUEST['no_cargar']) && ! isset($params['no_cargar']))
				{
					$response = consumir_webservice($xml_carga);

					$ob_data = json_decode(utf8_encode($response));

					if ($response === FALSE)
					{
						$status = "Webservice no disponible.";
					}

					// Decodificar respuesta
					$ob_data = json_decode(utf8_encode($response));

					if ($ob_data->status < 0)
					{
						$status = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : print_r($response, TRUE))));
					}
					else
					{
						$status = "Carga de documento exitosa.";
					}
				}
				else
				{
					$status = "No se cargo documento a OpenBravo.";
				}

				echo "\n[#{$cont}] Documento CIA: {$num_cia} SERIE: {$serie} FACT: {$folio} RESPUESTA: {$status}";
				// echo "<br />{$xml_carga}";

				$cont++;
			}

			$folio = $df['folio'];

			$tmp = $df;

			$datos = array(
				'cabecera'	=> array (
					'num_cia'				=> $df['num_cia'],
					'tipo'					=> $df['tipo_serie'],
					'serie'					=> $df['serie'],
					'folio'					=> $df['folio'],
					'fecha'					=> $df['fecha'],
					'hora'					=> $df['hora'],
					'clasificacion'			=> $df['clasificacion'],
					'clave_cliente'			=> $df['clave_cliente'],
					'nombre_cliente'		=> $df['nombre_cliente'],
					'rfc_cliente'			=> $df['rfc'],
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
					'email'					=> $df['email_cliente'],
					'observaciones'			=> $df['observaciones'],
					'importe'				=> get_val($df['subtotal']),
					'porcentaje_descuento'	=> get_val($df['porcentaje_descuento']),
					'descuento'				=> get_val($df['descuento']),
					'ieps'					=> get_val($df['ieps']),
					'porcentaje_iva'		=> get_val($df['iva']) > 0 ? 16 : 0,
					'importe_iva'			=> get_val($df['iva']),
					'aplicar_retenciones'	=> get_val($df['retencion_isr']) > 0 ? 'S' : 'N',
					'importe_retencion_isr'	=> get_val($df['retencion_isr']),
					'importe_retencion_iva'	=> get_val($df['retencion_iva']),
					'total'					=> get_val($df['total']),
					'tipo_pago'				=> $df['tipo_pago'],
					'cuenta_pago'			=> $df['cuenta_pago'],
					'condiciones_pago'		=> $df['condiciones_pago']
				),
				'detalle'	=> array()
			);

			$row = 0;
		}

		$datos['detalle'][$row] = array(
			'clave'				=> $row + 1,
			'descripcion'		=> $df['descripcion'],
			'cantidad'			=> get_val($df['cantidad']),
			'unidad'			=> $df['unidad'],
			'precio'			=> get_val($df['precio']),
			'importe'			=> get_val($df['cantidad']) * get_val($df['precio']),
			'descuento'			=> 0,
			'porcentaje_ieps'	=> $df['pieps_detalle'],
			'importe_ieps'		=> get_val($df['ieps_detalle']),
			'porcentaje_iva'	=> $df['piva_detalle'],
			'importe_iva'		=> get_val($df['iva_detalle']),
			'numero_pedimento'	=> $df['numero_pedimento'],
			'fecha_entrada'		=> $df['fecha_entrada'],
			'aduana_entrada'	=> $df['aduana_entrada']
		);

		$row++;
	}

	if (isset($folio) && $folio != NULL)
	{
		$xml_carga = generar_xml_datos($datos, isset($_REQUEST['no_cargar']) || isset($params['no_cargar']) ? TRUE : FALSE);

		if ( ! isset($_REQUEST['no_cargar']) && ! isset($params['no_cargar']))
		{
			$response = consumir_webservice($xml_carga, isset($_REQUEST['no_cargar']) || isset($params['no_cargar']) ? TRUE : FALSE);

			$ob_data = json_decode(utf8_encode($response));

			if ($response === FALSE)
			{
				$status = "Webservice no disponible.";
			}

			// Decodificar respuesta
			$ob_data = json_decode(utf8_encode($response));

			if ($ob_data->status < 0)
			{
				$status = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : print_r($response, TRUE))));
			}
			else
			{
				$status = "Carga de documento exitosa.";
			}
		}
		else
		{
			$status = "No se cargo documento a OpenBravo.";
		}

		echo "\n[#{$cont}] Documento CIA: {$num_cia} SERIE: {$serie} FACT: {$folio} RESPUESTA: {$status}";
		// echo "<br />{$xml_carga}";

		$cont++;
	}

	$offset += $rows_per_query;
}

function generar_xml_datos($datos, $no_cargar = FALSE)
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

	$xml_categoryValue = $xml->createElement('categoryValue', utf8_encode('CL'));
	$xml_bussinesPartnerValue->appendChild($xml_categoryValue);

	$xml_description = $xml->createElement('description', htmlspecialchars(utf8_encode($datos['cabecera']['observaciones'])));
	$xml_bussinesPartnerValue->appendChild($xml_description);

	$xml_fiscalCode = $xml->createElement('fiscalCode', htmlspecialchars(utf8_encode($datos['cabecera']['rfc_cliente'])));
	$xml_bussinesPartnerValue->appendChild($xml_fiscalCode);

	$xml_locations = $xml->createElement('locations');
	$xml_bussinesPartnerValue->appendChild($xml_locations);

	$xml_location = $xml->createElement('location');
	$xml_locations->appendChild($xml_location);

	$xml_address1 = $xml->createElement('address1', htmlspecialchars(utf8_encode($datos['cabecera']['municipio'])));
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

	$xml_name = $xml->createElement('name', htmlspecialchars(utf8_encode($datos['cabecera']['nombre_cliente'])));
	$xml_bussinesPartnerValue->appendChild($xml_name);

	$xml_value = $xml->createElement('value', htmlspecialchars(utf8_encode($datos['cabecera']['rfc_cliente'])));
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

	$xml_documentNo = $xml->createElement('documentNo', htmlspecialchars(utf8_encode($datos['cabecera']['serie'] . $datos['cabecera']['folio'])));
	$xml_invoice->appendChild($xml_documentNo);

	switch ($datos['cabecera']['tipo'])
	{
		/*
		@ Factura
		*/
		case 1:
			$doctype = 'F' . $datos['cabecera']['num_cia'];
			break;

		/*
		@ Recibo de arrendamiento
		*/
		case 2:
			$doctype = 'A' . $datos['cabecera']['num_cia'];
			break;

		/*
		@ Nota de credito
		*/
		case 3:
			$doctype = 'N' . $datos['cabecera']['num_cia'];
			break;
	}

	$xml_documentTypeValue = $xml->createElement('documentTypeValue', htmlspecialchars(utf8_encode($doctype)));
	$xml_invoice->appendChild($xml_documentTypeValue);

	$xml_dueAmount = $xml->createElement('dueAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_dueAmount);

	$xml_formOfPayment = $xml->createElement('formOfPayment', isset($datos['cabecera']['tipo_pago']) ? $datos['cabecera']['tipo_pago'] : 'B');
	$xml_invoice->appendChild($xml_formOfPayment);

	$xml_grandTotalAmount = $xml->createElement('grandTotalAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_grandTotalAmount);

	$xml_invoiceDate = $xml->createElement('invoiceDate', dmy_to_ymd($datos['cabecera']['fecha']));
	$xml_invoice->appendChild($xml_invoiceDate);

	$xml_invoicelines = $xml->createElement('invoicelines');
	$xml_invoice->appendChild($xml_invoicelines);

	foreach ($datos['detalle'] as $i => $detalle)
	{
		if ($detalle['importe'] < 0)
		{
			$productvalue = 11;
		}
		else if ($datos['cabecera']['clasificacion'] == 3 && $datos['cabecera']['num_cia'] == 700)
		{
			$productvalue = 4;
		}
		else if ($datos['cabecera']['clasificacion'] == 3 && $datos['cabecera']['num_cia'] == 800)
		{
			$productvalue = 5;
		}
		else if ($datos['cabecera']['clasificacion'] == 7 && $datos['cabecera']['num_cia'] == 700)
		{
			$productvalue = 6;
		}
		else if ($datos['cabecera']['clasificacion'] == 5)
		{
			if ($detalle['porcentaje_iva'] == 0)
			{
				$productvalue = 7;
			}
			else if ($detalle['porcentaje_iva'] > 0 && $datos['cabecera']['aplicar_retenciones'] == 'N')
			{
				$productvalue = 8;
			}
			else if ($detalle['porcentaje_iva'] > 0 && $datos['cabecera']['aplicar_retenciones'] == 'S')
			{
				$productvalue = 9;
			}
		}
		else if (in_array($datos['cabecera']['clasificacion'], array(1, 2, 4, 6)))
		{
			if ($datos['cabecera']['num_cia'] < 900)
			{
				if ($detalle['porcentaje_iva'] > 0)
				{
					if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] > 0)
					{
						$productvalue = 'PIEPSIVA';
					}
					else
					{
						$productvalue = 2;
					}
				} else
				{
					if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] > 0)
					{
						$productvalue = 'PIEPS';
					}
					else
					{
						$productvalue = 1;
					}
				}
			}
			else
			{
				if ($detalle['porcentaje_iva'] > 0)
				{
					$productvalue = 3;

					if ($detalle['numero_pedimento'] != '')
					{
						$productvalue = 13;
					}
				} else if ($detalle['porcentaje_iva'] == 0)
				{
					$productvalue = 14;

					if ($detalle['numero_pedimento'] != '')
					{
						$productvalue = 12;
					}
				}

			}
		}

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

		$xml_line_productValue = $xml->createElement('productValue', $productvalue);
		$xml_invoiceline->appendChild($xml_line_productValue);

		$xml_line_description = $xml->createElement('description', htmlspecialchars(utf8_encode($detalle['descripcion'])));
		$xml_invoiceline->appendChild($xml_line_description);

		$xml_line_standardPrice = $xml->createElement('standardPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_standardPrice);

		$xml_line_taxValue = $xml->createElement('taxValue', '');
		$xml_invoiceline->appendChild($xml_line_taxValue);

		$xml_line_unitPrice = $xml->createElement('unitPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_unitPrice);

		$xml_line_productOrderUOMValue = $xml->createElement('productOrderUOMValue', htmlspecialchars(utf8_encode($detalle['unidad'])));
		$xml_invoiceline->appendChild($xml_line_productOrderUOMValue);

		if (isset($datos['cabecera']['cuenta_predial']) && $datos['cabecera']['cuenta_predial'] != '')
		{
			$xml_line_cuentaPred = $xml->createElement('cuentaPred', htmlspecialchars(utf8_encode($datos['cabecera']['cuenta_predial'])));
			$xml_invoiceline->appendChild($xml_line_cuentaPred);
		}

		if ($detalle['aduana_entrada'] != '' && $detalle['numero_pedimento'] != '' && $detalle['fecha_entrada'] != '')
		{
			$xml_line_aduAduana = $xml->createElement('aduAduana', htmlspecialchars(utf8_encode($detalle['aduana_entrada'])));
			$xml_invoiceline->appendChild($xml_line_aduAduana);

			$xml_line_aduFecha = $xml->createElement('aduFecha', dmy_to_ymd($detalle['fecha_entrada']));
			$xml_invoiceline->appendChild($xml_line_aduFecha);

			$xml_line_aduNumero = $xml->createElement('aduNumero', htmlspecialchars(utf8_encode($detalle['numero_pedimento'])));
			$xml_invoiceline->appendChild($xml_line_aduNumero);
		}
	}

	$xml_organizationValue = $xml->createElement('organizationValue', $datos['cabecera']['num_cia']);
	$xml_invoice->appendChild($xml_organizationValue);

	$xml_paymentComplete = $xml->createElement('paymentComplete', utf8_encode('N'));
	$xml_invoice->appendChild($xml_paymentComplete);

	$xml_paymentMethodValue = $xml->createElement('paymentMethodValue', $GLOBALS['tipo_pago'][isset($datos['cabecera']['tipo_pago']) ? $datos['cabecera']['tipo_pago'] : 'B']);
	$xml_invoice->appendChild($xml_paymentMethodValue);

	$xml_paymentTermsValue = $xml->createElement('paymentTermsValue', $GLOBALS['condiciones_pago'][isset($datos['cabecera']['condiciones_pago']) ? $datos['cabecera']['condiciones_pago'] : '0']);
	$xml_invoice->appendChild($xml_paymentTermsValue);

	$xml_priceListValue = $xml->createElement('priceListValue', utf8_encode('Ventas'));
	$xml_invoice->appendChild($xml_priceListValue);

	$xml_salesTransaction = $xml->createElement('salesTransaction', utf8_encode('Y'));
	$xml_invoice->appendChild($xml_salesTransaction);

	$xml_timbre = $xml->createElement('timbre', utf8_encode('N'));
	$xml_invoice->appendChild($xml_timbre);

	$xml_orderReference = $xml->createElement('orderReference', '');
	$xml_invoice->appendChild($xml_orderReference);

	$xml_selfService = $xml->createElement('selfService', utf8_encode('Y'));
	$xml_invoice->appendChild($xml_selfService);

	$xml_summedLineAmount = $xml->createElement('summedLineAmount', $datos['cabecera']['total']);
	$xml_invoice->appendChild($xml_summedLineAmount);

	$xml_totalPaid = $xml->createElement('totalPaid', 0);
	$xml_invoice->appendChild($xml_totalPaid);

	$xml_transactionDocumentValue = $xml->createElement('transactionDocumentValue', htmlspecialchars(utf8_encode($doctype)));
	$xml_invoice->appendChild($xml_transactionDocumentValue);

	// Guardar XML de carga
	if ($no_cargar)
	{
		$xml->save(dirname(__FILE__) . "/tmp/carga-ventas-{$datos['cabecera']['num_cia']}-{$datos['cabecera']['serie']}{$datos['cabecera']['folio']}.xml");
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
	// $ob_response = @file_get_contents('http://192.168.1.251:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecInvoice', NULL, $context);
	$ob_response = @file_get_contents('http://192.168.1.3:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecInvoice', NULL, $context);

	return $ob_response;
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
}
