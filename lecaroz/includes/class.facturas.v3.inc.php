<?php

class FacturasClass
{

	const SERVER_BLOCKED = TRUE;
	const SERVER_FREE = FALSE;

	// private $url = 'http://192.168.1.251:443/ob_lecaroz';
	private $url = 'http://192.168.1.3:443/ob_lecaroz';
	private $ws = '/ws/mx.cusoft.importing.rest.insertLecInvoice';
	private $username = 'Openbravo';
	private $password = 'openbravo';

	/*
	@ Conexiones a las bases de datos
	*/
	private $db;

	/*
	@ Rutas locales
	*/
	private $ruta_xml_carga;
	private $ruta_comprobantes_xml;
	private $ruta_comprobantes_pdf;
	private $ruta_codigos_qr;

	private $comprobante_xml;
	private $comprobante_pdf;

	private $file_name;

	private $max_retrieves = 15;
	private $sleep_time = 30;

	private $iduser;

	private $num_cia;
	private $tipo;
	private $serie;
	private $datos;
	private $folio;
	private $fecha_certificacion;
	private $uuid;
	private $cadena_original;
	private $sello_digital_cfdi;
	private $sello_digital_sat;

	private $status;

	private $last_error;

	private $header_error;

	private $process_init;
	private $process_end;

	private $tipo_pago = array(
		'B'		=> array('01', '01 EFECTIVO'),
		'2'		=> array('02', '02 CHEQUE NOMINATIVO'),
		'1'		=> array('03', '03 TRANSFERENCIA ELECTRONICA DE FONDOS'),
		'K'		=> array('04', '04 TARJETA DE CRÉDITO'),
		'V'		=> array('05', '05 MONEDERO ELECTRÓNICO'),
		'W'		=> array('06', '06 DINERO ELECTRÓNICO'),
		'X'		=> array('08', '08 VALES DE DESPENSA'),
		'Y'		=> array('28', '28 TARJETA DE DÉBITO'),
		'Z'		=> array('29', '29 TARJETA DE SERVICIOS'),
		'5'		=> array('00', '00 NO IDENTIFICADO'),
		'NA'	=> array('NA', '"NA"')
	);

	private $condiciones_pago = array(
		0	=> 'NO IDENTIFICADO',
		1	=> 'CONTADO',
		2	=> 'CREDITO'
	);

	function __construct()
	{
		global $db;

		/*
		@ Validar que exista una conexión a la base de datos de Lecaroz
		*/
		if ( ! isset($db))
		{
			trigger_error('No existe la instancia $db para la conexión a la base de datos de Lecaroz', E_USER_ERROR);
		}
		else
		{
			$this->db = &$db;
		}

		$this->ruta_xml_carga = str_replace('/includes', '', dirname(__FILE__)) . '/facturas/xml_carga/';
		$this->ruta_comprobantes_xml = str_replace('/includes', '', dirname(__FILE__)) . '/facturas/comprobantes_xml/';
		$this->ruta_comprobantes_pdf = str_replace('/includes', '', dirname(__FILE__)) . '/facturas/comprobantes_pdf/';
		$this->ruta_codigos_qr = str_replace('/includes', '', dirname(__FILE__)) . '/facturas/codigos_qr/';
	}


	function __destruct()
	{
		return;
	}

	private function validarStatusServidor()
	{
		$status = $this->db->query("SELECT id FROM facturas_electronicas_server_status_new WHERE num_cia = {$this->num_cia};");

		if ($status)
		{
			$this->status = -3;

			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function cambiarStatusServidor($status)
	{
		if ($status)
		{
			$sql = "INSERT INTO facturas_electronicas_server_status_new (num_cia, iduser) VALUES ({$this->num_cia}, {$this->iduser});";
		}
		else
		{
			$sql = "DELETE FROM facturas_electronicas_server_status_new WHERE num_cia = {$this->num_cia};";
		}

		$this->db->query($sql);
	}

	private function actualizarStatusServidor($status = 0, $observaciones = '', $num_fact = NULL)
	{
		$this->db->query("UPDATE facturas_electronicas_server_status_new SET status = {$status}, obs = '{$observaciones}', num_fact = '{$num_fact}' WHERE num_cia = {$this->num_cia};");
	}

	private function validarSerie()
	{
		$sql = "
			SELECT
				fes.serie,
				fes.tipo_factura,
				fes.folio_inicial,
				fes.folio_final,
				fes.ultimo_folio_usado + 1
					AS folio,
				cc.nombre
					AS nombre_cia,
				cc.email,
				con.email
					AS email_contador,
				cc.razon_social,
				cc.rfc,
				cc.regimen_fiscal,
				cc.calle,
				cc.no_exterior,
				cc.no_interior,
				cc.colonia,
				cc.municipio,
				cc.estado,
				cc.pais,
				cc.codigo_postal,
				ccm.calle
					AS calle_matriz,
				ccm.no_exterior
					AS no_exterior_matriz,
				ccm.no_interior
					AS no_interior_matriz,
				ccm.colonia
					AS colonia_matriz,
				ccm.municipio
					AS municipio_matriz,
				ccm.estado
					AS estado_matriz,
				ccm.pais
					AS pais_matriz,
				ccm.codigo_postal
					AS codigo_postal_matriz,
				fes.tipo_cfd,
				cl.nombre_imagen
					AS logo
			FROM
				facturas_electronicas_series fes
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
				LEFT JOIN catalogo_contadores con
					USING (idcontador)
				LEFT JOIN catalogo_companias ccm
					ON (ccm.num_cia = cc.cia_fiscal_matriz)
				LEFT JOIN catalogo_logos_cfd cl
					ON (cl.id = cc.logo_cfd)
			WHERE
				fes.num_cia = {$this->num_cia}
				AND fes.tipo_serie = {$this->tipo}
				AND " . ($this->folio == NULL ? 'fes.status = 1' : $this->folio . ' BETWEEN folio_inicial AND folio_final') . ";
		";

		$result = $this->db->query($sql);

		if ($result)
		{
			$this->serie = $result[0];

			$this->serie['serie'] = $this->serie['serie'];

			return TRUE;
		}
		else
		{
			$this->status = -4;

			return FALSE;
		}
	}

	private function validarDatos()
	{
		$this->header_error = array();

		/*
		@ Validar datos de cabecera
		*/

		if ($this->datos['cabecera']['nombre_cliente'] == '')
		{
			$this->header_error[] = -101;
		}

		if ($this->datos['cabecera']['rfc_cliente'] == '')
		{
			$this->header_error[] = -102;
		}
		else if (preg_match_all("/^([a-zA-Z\xf1\xd1\&]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})$/", $this->datos['cabecera']['rfc_cliente'], $matches) == 0)
		{
			$this->header_error[] = -103;
		}/* else
		{
			$dias_por_mes = array(
				1	=> 31,
				2	=> ((intval($matches[0][2], 10) > 50 ? 1900 : 2000) + intval($matches[0][2], 10)) % 4 == 0 ? 29 : 28,
				3	=> 31,
				4	=> 30,
				5	=> 31,
				6	=> 30,
				7	=> 31,
				8	=> 31,
				9	=> 30,
				10	=> 31,
				11	=> 30,
				12	=> 31
			);

			if (intval($matches[0][3], 10) > 12 || intval($matches[0][4], 10) > $dias_por_mes[intval($matches[0][3], 10)])
			{
				$this->header_error[] = -111;
			}
		}*/

		if ($this->datos['cabecera']['rfc_cliente'] == 'XAXX010101000')
		{
			$validar_domicilio = FALSE;
		}
		else
		{
			$validar_domicilio = TRUE;
		}

		if ($validar_domicilio && $this->datos['cabecera']['calle'] == '')
		{
			$this->header_error[] = -104;
		}

		if ($validar_domicilio && $this->datos['cabecera']['colonia'] == '')
		{
			$this->header_error[] = -105;
		}

		if ($validar_domicilio && $this->datos['cabecera']['municipio'] == '')
		{
			$this->header_error[] = -106;
		}

		if ($validar_domicilio && $this->datos['cabecera']['estado'] == '')
		{
			$this->header_error[] = -107;
		}

		if ($this->datos['cabecera']['pais'] == '')
		{
			$this->header_error[] = -108;
		}

		if ($validar_domicilio && $this->datos['cabecera']['codigo_postal'] == '')
		{
			$this->header_error[] = -109;
		}

		$tmp = $this->db->query("SELECT idob, UPPER(\"Entidad\") AS estado FROM catalogo_entidades ORDER BY estado;");

		$estados = array();

		foreach ($tmp as $t)
		{
			$estados[$t['estado']] = $t['idob'];
		}

		if ( ! in_array($this->datos['cabecera']['estado'], array_keys($estados)))
		{
			$this->header_error[] = -110;
		}
		else
		{
			$this->datos['cabecera']['idestado'] = $estados[$this->datos['cabecera']['estado']];
		}

		if (array_sum($this->header_error) < 0)
		{
			$this->status = -100;

			return FALSE;
		}

		/*
		@ Validar la suma de detalles
		*/

		$subtotal = 0;
		foreach ($this->datos['detalle'] as $detalle)
		{
			if ($detalle['importe'] > 0)
			{
				$subtotal += $detalle['importe'];
			}
		}

		if (round($subtotal, 2) != round($this->datos['cabecera']['importe'], 2))
		{
			$this->status = -150;

			return FALSE;
		}

		/*
		@ Validar el total
		*/

		$total = $this->datos['cabecera']['importe'] - $this->datos['cabecera']['descuento'] + (isset($this->datos['cabecera']['ieps']) ? $this->datos['cabecera']['ieps'] : 0) + $this->datos['cabecera']['importe_iva'] - $this->datos['cabecera']['importe_retencion_isr'] - $this->datos['cabecera']['importe_retencion_iva'];

		if (round($total, 2) != round($this->datos['cabecera']['total'], 2))
		{
			$this->status = -151;

			return FALSE;
		}

		return TRUE;
	}

	private function validarDuplicados()
	{
		if ($this->num_cia >= 900)
		{
			return TRUE;
		}

		if ($this->num_cia == 17 && $this->datos['cabecera']['rfc_cliente'] == 'PHI830429MG6')
		{
			return TRUE;
		}

		/*
		* [03-May-2012] El intervalo de 15 días se redujo solo al día que se esta emitiendo la factura
		*/

		$result = $this->db->query("
			SELECT
				id
			FROM
				facturas_electronicas
			WHERE
				num_cia = {$this->num_cia}
				AND rfc = '{$this->datos['cabecera']['rfc_cliente']}'
				AND tipo = 2
				AND tipo_serie = 1
				AND fecha BETWEEN '{$this->datos['cabecera']['fecha']}'::DATE/* - INTERVAL '15 days'*/ AND '{$this->datos['cabecera']['fecha']}'::DATE
				AND total BETWEEN " . ($this->datos['cabecera']['total'] - 10) . ' AND ' . ($this->datos['cabecera']['total'] + 10) . "
				AND status = 1
				AND (num_cia, TRIM(nombre_cliente)) NOT IN (
					SELECT
						num_cia,
						nombre_cliente
					FROM
						facturas_electronicas_excluir_duplicados
					WHERE
						num_cia = {$this->num_cia}
				)
			LIMIT
				1;
		");

		if ($this->iduser == 0 && $result)
		{
			$this->status = -80;

			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function validarLimiteTotal()
	{
		if ($this->num_cia >= 900)
		{
			return TRUE;
		}

		if ($this->iduser == 0 && $this->datos['cabecera']['rfc_cliente'] != 'XAXX010101000' && $this->datos['cabecera']['total'] > 15000)
		{
			$this->status = -81;

			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	private function validarFolio()
	{
		$result = $this->db->query("SELECT id FROM facturas_electronicas WHERE num_cia = {$this->num_cia} AND tipo_serie = {$this->tipo} AND consecutivo = {$this->folio};");

		if ($result)
		{
			$this->status = -160;

			return FALSE;
		}
		/*else if ($this->folio > $this->serie['folio'])
		{
			$this->status = -161;

			return FALSE;
		}*/
		else
		{
			return TRUE;
		}
	}

	private function generarFacturaElectronica()
	{
		$this->cambiarStatusServidor(self::SERVER_BLOCKED);

		/*
		@ Crear nombre del archivo local
		*/
		$this->file_name = $this->num_cia . '-' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']);

		$this->actualizarStatusServidor(0, 'Procesando factura ' . $this->file_name, $this->file_name);

		$this->process_init = time();

		/*
		@ Validar que el directorio para almacenar los archivos de carga XML
		*/
		if ( ! is_dir($this->ruta_xml_carga . $this->num_cia))
		{
			mkdir($this->ruta_xml_carga . $this->num_cia);
		}

		$xml_data = $this->generarXMLDatos();

		// Construir arreglo de opciones para consumir el webservice
		$stream_options = array(
			'http' => array(
				'method'	=> 'POST',
				'header'	=> 'Authorization: Basic ' . base64_encode("$this->username:$this->password") . ' Content-Type: text/xml',
				'content'	=> $xml_data,
			),
		);

		// Crear contexto de flujo con las opciones para consumir el webservice
		$context = stream_context_create($stream_options);

		// Consumir webservice y obtener respuesta
		$ob_response = @file_get_contents($this->url . $this->ws, NULL, $context);

		// Si no hubo respuesta por parte del servidor terminar proceso
		if ($ob_response === FALSE)
		{
			$this->status = -600;

			$this->enviarEmailErrorOB();

			$this->cambiarStatusServidor(self::SERVER_FREE);

			return -600;
		}

		// Decodificar respuesta
		$ob_data = json_decode(utf8_encode($ob_response));

		if ($ob_data == NULL)
		{
			$this->status = -603;

			$this->last_error = $ob_response;

			$this->enviarEmailErrorOB();

			return -603;
		}

		if ($ob_data->status < 0 || $ob_data->status > 1)
		{
			$this->status = -601;

			$this->last_error = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : $ob_response)));

			$this->enviarEmailErrorOB();

			$this->cambiarStatusServidor(self::SERVER_FREE);

			return -601;
		}

		$this->process_end = time();

		$this->registrarFactura($ob_response, $ob_data->fecha_timbrado, $ob_data->uuid, $ob_data->no_certificado_digital, $ob_data->no_certificado_sat, $ob_data->sello_cfd, $ob_data->sello_sat, $ob_data->cadena_original, $ob_data->documento_xml);

		/*
		@ Validar que el directorio para almacenar los comprobantes XML exista en el servidor
		*/
		if ( ! is_dir($this->ruta_comprobantes_xml . $this->num_cia))
		{
			mkdir($this->ruta_comprobantes_xml . $this->num_cia);
		}

		/*
		@ Validar que el directorio para almacenar los comprobantes PDF exista en el servidor
		*/
		if ( ! is_dir($this->ruta_comprobantes_pdf . $this->num_cia))
		{
			mkdir($this->ruta_comprobantes_pdf . $this->num_cia);
		}

		/*
		@ Validar que el directorio para almacenar los códigos QR exista en el servidor
		*/
		if ( ! is_dir($this->ruta_codigos_qr . $this->num_cia))
		{
			mkdir($this->ruta_codigos_qr . $this->num_cia);
		}

		$this->generarDocumentoXML($ob_data->documento_xml);

		if ( ! $this->generarDocumentoPDF($ob_data->fecha_timbrado, $ob_data->uuid, $ob_data->no_certificado_digital, $ob_data->no_certificado_sat, $ob_data->sello_cfd, $ob_data->sello_sat, $ob_data->cadena_original))
		{
			$this->status = -622;

			$this->actualizarStatusServidor(-622, $this->last_error, $this->file_name);

			return -622;
		}

		$this->cambiarStatusServidor(self::SERVER_FREE);

		return $this->file_name;
	}

	private function generarXMLDatos()
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

		$xml_accountingDate = $xml->createElement('accountingDate', $this->dmy_to_ymd($this->datos['cabecera']['fecha']));
		$xml_invoice->appendChild($xml_accountingDate);

		$xml_active = $xml->createElement('active', 'Y');
		$xml_invoice->appendChild($xml_active);

		$xml_bussinesPartnerValue = $xml->createElement('bussinesPartnerValue');
		$xml_invoice->appendChild($xml_bussinesPartnerValue);

		$xml_categoryValue = $xml->createElement('categoryValue', utf8_encode('CL'));
		$xml_bussinesPartnerValue->appendChild($xml_categoryValue);

		$xml_description = $xml->createElement('description', htmlspecialchars(utf8_encode($this->datos['cabecera']['observaciones'])));
		$xml_bussinesPartnerValue->appendChild($xml_description);

		$xml_fiscalCode = $xml->createElement('fiscalCode', htmlspecialchars(utf8_encode($this->datos['cabecera']['rfc_cliente'])));
		$xml_bussinesPartnerValue->appendChild($xml_fiscalCode);

		$xml_locations = $xml->createElement('locations');
		$xml_bussinesPartnerValue->appendChild($xml_locations);

		$xml_location = $xml->createElement('location');
		$xml_locations->appendChild($xml_location);

		$xml_address1 = $xml->createElement('address1', htmlspecialchars(utf8_encode($this->datos['cabecera']['municipio'])));
		$xml_location->appendChild($xml_address1);

		$xml_city = $xml->createElement('city', htmlspecialchars(utf8_encode($this->datos['cabecera']['municipio'])));
		$xml_location->appendChild($xml_city);

		$xml_countryValue = $xml->createElement('countryValue', utf8_encode('MX'));
		$xml_location->appendChild($xml_countryValue);

		$xml_postal = $xml->createElement('postal', utf8_encode($this->datos['cabecera']['codigo_postal']));
		$xml_location->appendChild($xml_postal);

		$xml_regionValue = $xml->createElement('regionValue', htmlspecialchars(utf8_encode($this->datos['cabecera']['estado'])));
		$xml_location->appendChild($xml_regionValue);

		$xml_calle = $xml->createElement('calle', htmlspecialchars(utf8_encode($this->datos['cabecera']['calle'])));
		$xml_location->appendChild($xml_calle);

		$xml_colonia = $xml->createElement('colonia', htmlspecialchars(utf8_encode($this->datos['cabecera']['colonia'])));
		$xml_location->appendChild($xml_colonia);

		$xml_estado = $xml->createElement('estado', htmlspecialchars(utf8_encode($this->datos['cabecera']['estado'])));
		$xml_location->appendChild($xml_estado);

		$xml_localidad = $xml->createElement('localidad', htmlspecialchars(utf8_encode($this->datos['cabecera']['localidad'])));
		$xml_location->appendChild($xml_localidad);

		$xml_municipio = $xml->createElement('municipio', htmlspecialchars(utf8_encode($this->datos['cabecera']['municipio'])));
		$xml_location->appendChild($xml_municipio);

		$xml_noexterior = $xml->createElement('noexterior', htmlspecialchars(utf8_encode($this->datos['cabecera']['no_exterior'])));
		$xml_location->appendChild($xml_noexterior);

		$xml_nointerior = $xml->createElement('nointerior', htmlspecialchars(utf8_encode($this->datos['cabecera']['no_interior'])));
		$xml_location->appendChild($xml_nointerior);

		$xml_name = $xml->createElement('name', htmlspecialchars(utf8_encode($this->datos['cabecera']['nombre_cliente'])));
		$xml_bussinesPartnerValue->appendChild($xml_name);

		$xml_value = $xml->createElement('value', htmlspecialchars(utf8_encode($this->datos['cabecera']['rfc_cliente'])));
		$xml_bussinesPartnerValue->appendChild($xml_value);

		$xml_vendor = $xml->createElement('vendor', utf8_encode('Y'));
		$xml_bussinesPartnerValue->appendChild($xml_vendor);

		$xml_customer = $xml->createElement('customer', utf8_encode('N'));
		$xml_bussinesPartnerValue->appendChild($xml_customer);

		$xml_clientValue = $xml->createElement('clientValue', utf8_encode('GLecaroz'));
		$xml_invoice->appendChild($xml_clientValue);

		$xml_creationDate = $xml->createElement('creationDate', $this->dmy_to_ymd($this->datos['cabecera']['fecha']));
		$xml_invoice->appendChild($xml_creationDate);

		$xml_curencyValue = $xml->createElement('curencyValue', utf8_encode('MXN'));
		$xml_invoice->appendChild($xml_curencyValue);

		$xml_documentNo = $xml->createElement('documentNo', htmlspecialchars(utf8_encode($this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']))));
		$xml_invoice->appendChild($xml_documentNo);

		switch ($this->tipo)
		{
			/*
			@ Factura
			*/
			case 1:
				$doctype = 'F' . $this->num_cia;
				break;

			/*
			@ Recibo de arrendamiento
			*/
			case 2:
				$doctype = 'A' . $this->num_cia;
				break;

			/*
			@ Nota de credito
			*/
			case 3:
				$doctype = 'N' . $this->num_cia;
				break;
		}

		$xml_documentTypeValue = $xml->createElement('documentTypeValue', htmlspecialchars(utf8_encode($doctype)));
		$xml_invoice->appendChild($xml_documentTypeValue);

		$xml_dueAmount = $xml->createElement('dueAmount', $this->datos['cabecera']['total']);
		$xml_invoice->appendChild($xml_dueAmount);

		$xml_formOfPayment = $xml->createElement('formOfPayment', /*isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 'B'*/1);
		$xml_invoice->appendChild($xml_formOfPayment);

		$xml_grandTotalAmount = $xml->createElement('grandTotalAmount', $this->datos['cabecera']['total']);
		$xml_invoice->appendChild($xml_grandTotalAmount);

		$xml_invoiceDate = $xml->createElement('invoiceDate', $this->dmy_to_ymd($this->datos['cabecera']['fecha']));
		$xml_invoice->appendChild($xml_invoiceDate);

		$xml_invoicelines = $xml->createElement('invoicelines');
		$xml_invoice->appendChild($xml_invoicelines);

		foreach ($this->datos['detalle'] as $i => $detalle)
		{
			/*if ($detalle['importe'] < 0)
			{
				$productvalue = 11;

				$taxvalue = 'IVA 16';
			}
			else */if ($this->datos['cabecera']['clasificacion'] == 3 && $this->num_cia == 700)
			{
				$productvalue = 4;

				$taxvalue = 'IVA';
			}
			else if ($this->datos['cabecera']['clasificacion'] == 3 && $this->num_cia == 800)
			{
				$productvalue = 5;

				$taxvalue = 'IVA';
			}
			else if ($this->datos['cabecera']['clasificacion'] == 7 && $this->num_cia == 700)
			{
				$productvalue = 6;

				$taxvalue = 'IVA';
			}
			else if ($this->datos['cabecera']['clasificacion'] == 5)
			{
				if ($detalle['porcentaje_iva'] == 0 && $this->datos['cabecera']['importe_retencion_iva'] == 0 && $this->datos['cabecera']['importe_retencion_isr'] == 0)
				{
					$productvalue = 7;

					$taxvalue = 'IVA 0';
				}
				else if ($detalle['porcentaje_iva'] == 0 && $this->datos['cabecera']['importe_retencion_iva'] == 0 && $this->datos['cabecera']['importe_retencion_isr'] > 0)
				{
					$productvalue = 'ARRE3';

					$taxvalue = 'ARRE3';
				}
				else if ($detalle['porcentaje_iva'] != 0 && $this->datos['cabecera']['importe_retencion_iva'] == 0 && $this->datos['cabecera']['importe_retencion_isr'] == 0)
				{
					$productvalue = 8;

					$taxvalue = 'IVA';
				}
				else if ($detalle['porcentaje_iva'] != 0 && $this->datos['cabecera']['importe_retencion_iva'] != 0 && $this->datos['cabecera']['importe_retencion_isr'] != 0)
				{
					$productvalue = 9;

					$taxvalue = 'ARRENDAMIENTO';
				}
			}
			else if (in_array($this->datos['cabecera']['clasificacion'], array(1, 2, 4, 6)))
			{
				if ($this->num_cia < 900)
				{
					if ($detalle['porcentaje_iva'] != 0)
					{
						if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] != 0)
						{
							$productvalue = 'PIEPSIVA';

							$taxvalue = 'PIEPSIVA';
						}
						else
						{
							$productvalue = 2;

							$taxvalue = 'IVA';
						}
					} else
					{
						if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] != 0)
						{
							$productvalue = 'PIEPS';

							$taxvalue = 'PIEPS';
						}
						else
						{
							$productvalue = 1;

							$taxvalue = 'IVA 0';
						}
					}
				}
				else
				{
					if ($detalle['porcentaje_iva'] != 0)
					{
						$productvalue = 3;

						$taxvalue = 'IVA';

						if ($detalle['numero_pedimento'] != '')
						{
							$productvalue = 13;
						}
					} else if ($detalle['porcentaje_iva'] == 0)
					{
						$productvalue = 14;

						$taxvalue = 'IVA 0';

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

			$xml_line_organizationValue = $xml->createElement('organizationValue', $this->num_cia);
			$xml_invoiceline->appendChild($xml_line_organizationValue);

			$xml_line_priceLimit = $xml->createElement('priceLimit', 0);
			$xml_invoiceline->appendChild($xml_line_priceLimit);

			$xml_line_productValue = $xml->createElement('productValue', $productvalue);
			$xml_invoiceline->appendChild($xml_line_productValue);

			$xml_line_description = $xml->createElement('description', htmlspecialchars(utf8_encode($detalle['descripcion'])));
			$xml_invoiceline->appendChild($xml_line_description);

			$xml_line_standardPrice = $xml->createElement('standardPrice', $detalle['precio']);
			$xml_invoiceline->appendChild($xml_line_standardPrice);

			$xml_line_taxValue = $xml->createElement('taxValue', utf8_encode($taxvalue));
			$xml_invoiceline->appendChild($xml_line_taxValue);

			$xml_line_unitPrice = $xml->createElement('unitPrice', $detalle['precio']);
			$xml_invoiceline->appendChild($xml_line_unitPrice);

			$xml_line_productOrderUOMValue = $xml->createElement('productOrderUOMValue', htmlspecialchars(utf8_encode($detalle['unidad'])));
			$xml_invoiceline->appendChild($xml_line_productOrderUOMValue);

			if (isset($this->datos['cabecera']['cuenta_predial']) && $this->datos['cabecera']['cuenta_predial'] != '')
			{
				$xml_line_cuentaPred = $xml->createElement('cuentaPred', htmlspecialchars(utf8_encode($this->datos['cabecera']['cuenta_predial'])));
				$xml_invoiceline->appendChild($xml_line_cuentaPred);
			}

			if ($detalle['aduana_entrada'] != '' && $detalle['numero_pedimento'] != '' && $detalle['fecha_entrada'] != '')
			{
				$xml_line_aduAduana = $xml->createElement('aduAduana', htmlspecialchars(utf8_encode($detalle['aduana_entrada'])));
				$xml_invoiceline->appendChild($xml_line_aduAduana);

				$xml_line_aduFecha = $xml->createElement('aduFecha', $this->dmy_to_ymd($detalle['fecha_entrada']));
				$xml_invoiceline->appendChild($xml_line_aduFecha);

				$xml_line_aduNumero = $xml->createElement('aduNumero', htmlspecialchars(utf8_encode($detalle['numero_pedimento'])));
				$xml_invoiceline->appendChild($xml_line_aduNumero);
			}
		}

		$xml_organizationValue = $xml->createElement('organizationValue', $this->num_cia);
		$xml_invoice->appendChild($xml_organizationValue);

		$xml_paymentComplete = $xml->createElement('paymentComplete', utf8_encode('N'));
		$xml_invoice->appendChild($xml_paymentComplete);

		$xml_paymentMethodValue = $xml->createElement('paymentMethodValue', $this->tipo_pago[isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 'B'][0]);
		$xml_invoice->appendChild($xml_paymentMethodValue);

		$xml_paymentTermsValue = $xml->createElement('paymentTermsValue', $this->condiciones_pago[isset($this->datos['cabecera']['condiciones_pago']) ? $this->datos['cabecera']['condiciones_pago'] : '1']);
		$xml_invoice->appendChild($xml_paymentTermsValue);

		$xml_priceListValue = $xml->createElement('priceListValue', utf8_encode('Ventas'));
		$xml_invoice->appendChild($xml_priceListValue);

		$xml_salesTransaction = $xml->createElement('salesTransaction', utf8_encode('Y'));
		$xml_invoice->appendChild($xml_salesTransaction);

		$xml_timbre = $xml->createElement('timbre', utf8_encode('Y'));
		$xml_invoice->appendChild($xml_timbre);

		$xml_orderReference = $xml->createElement('orderReference', '');
		$xml_invoice->appendChild($xml_orderReference);

		$xml_selfService = $xml->createElement('selfService', utf8_encode('Y'));
		$xml_invoice->appendChild($xml_selfService);

		$xml_summedLineAmount = $xml->createElement('summedLineAmount', $this->datos['cabecera']['total']);
		$xml_invoice->appendChild($xml_summedLineAmount);

		$xml_totalPaid = $xml->createElement('totalPaid', 0);
		$xml_invoice->appendChild($xml_totalPaid);

		$xml_transactionDocumentValue = $xml->createElement('transactionDocumentValue', htmlspecialchars(utf8_encode($doctype)));
		$xml_invoice->appendChild($xml_transactionDocumentValue);

		// Guardar XML de carga
		$xml->save($this->ruta_xml_carga . $this->num_cia . '/' . utf8_encode($this->file_name) . '.xml');

		// Retornar el XML
		return $xml->saveXML();
	}

	private function generarDocumentoPDF($fecha_timbrado, $uuid, $no_certificado_digital, $no_certificado_sat, $sello_cfd, $sello_sat, $cadena_original)
	{
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if ( ! class_exists('TemplatePower'))
		{
			include(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}

		/*
		@ Validar que la librería WkHtmlToPdf este cargada
		*/
		if ( ! class_exists('WkHtmlToPdf'))
		{
			include(dirname(__FILE__) . '/WkHtmlToPdf.php');
		}

		/*
		@ Validar que la librería QRcode este cargada
		*/
		if ( ! class_exists('QRcode'))
		{
			include(dirname(__FILE__) . '/phpqrcode/qrlib.php');
		}

		/*
		@ Validar que exista la función num2string()
		*/
		if ( ! function_exists('num2string'))
		{
			include(dirname(__FILE__) . '/cheques.inc.php');
		}

		$base_dir = str_replace('/includes', '', dirname(__FILE__));

		$tpl = new TemplatePower($base_dir . '/plantillas/fac/factura_electronica_pdf.tpl');
		$tpl->prepare();

		$tpl->assign('base_dir', $base_dir);

		$tpl->assign('logo', $this->serie['logo']);

		$tpl->assign('razon_social_emisor', utf8_encode($this->serie['razon_social']));
		$tpl->assign('rfc_emisor', utf8_encode($this->serie['rfc']));

		$domicilio_fiscal_emisor = array(
			trim($this->serie['calle']) != '' ? mb_strtoupper(trim($this->serie['calle'])) . (trim($this->serie['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($this->serie['no_exterior'])) : '') . (trim($this->serie['no_interior']) != '' ? ' ' . mb_strtoupper(trim($this->serie['no_interior'])) : '') : NULL,
			trim($this->serie['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($this->serie['colonia'])) : NULL,
			trim($this->serie['municipio']) != '' ? mb_strtoupper(trim($this->serie['municipio'])) : NULL,
			trim($this->serie['estado']) != '' ? mb_strtoupper(trim($this->serie['estado'])) : NULL,
			trim($this->serie['pais']) != '' ? mb_strtoupper(trim($this->serie['pais'])) : NULL,
			trim($this->serie['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($this->serie['codigo_postal'])) : NULL,
		);

		$tpl->assign('domicilio_fiscal_emisor', utf8_encode(implode(', ', array_filter($domicilio_fiscal_emisor))));
		$tpl->assign('regimen_fiscal_emisor', utf8_encode($this->serie['regimen_fiscal']));

		$domicilio_fiscal_matriz = array(
			trim($this->serie['calle_matriz']) != '' ? mb_strtoupper(trim($this->serie['calle_matriz'])) . (trim($this->serie['no_exterior_matriz']) != '' ? ' ' . mb_strtoupper(trim($this->serie['no_exterior_matriz'])) : '') . (trim($this->serie['no_interior_matriz']) != '' ? ' ' . mb_strtoupper(trim($this->serie['no_interior_matriz'])) : '') : NULL,
			trim($this->serie['colonia_matriz']) != '' ? 'COL. ' . mb_strtoupper(trim($this->serie['colonia_matriz'])) : NULL,
			trim($this->serie['municipio_matriz']) != '' ? mb_strtoupper(trim($this->serie['municipio_matriz'])) : NULL,
			trim($this->serie['estado_matriz']) != '' ? mb_strtoupper(trim($this->serie['estado_matriz'])) : NULL,
			trim($this->serie['pais_matriz']) != '' ? mb_strtoupper(trim($this->serie['pais_matriz'])) : NULL,
			trim($this->serie['codigo_postal_matriz']) != '' ? 'CP. ' . mb_strtoupper(trim($this->serie['codigo_postal_matriz'])) : NULL,
		);

		$tpl->assign('domicilio_fiscal_matriz', utf8_encode(implode(', ', array_filter($domicilio_fiscal_matriz))));

		switch ($this->tipo)
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

		$tpl->assign('tipo_documento', utf8_encode($tipo_documento));
		$tpl->assign('folio', utf8_encode($this->serie['serie'] != '' ? $this->serie['serie'] . '-' : '') . ($this->folio > 0 ? $this->folio : $this->serie['folio']));
		$tpl->assign('fecha_emision', $this->dmy_to_ymd($this->datos['cabecera']['fecha']) . 'T' . $this->datos['cabecera']['hora']);
		$tpl->assign('fecha_certificacion', $fecha_timbrado);

		$lugar_expedicion = array(
			trim($this->serie['municipio']) != '' ? mb_strtoupper(trim($this->serie['municipio'])) : NULL,
			trim($this->serie['estado']) != '' ? mb_strtoupper(trim($this->serie['estado'])) : NULL,
			trim($this->serie['pais']) != '' ? mb_strtoupper(trim($this->serie['pais'])) : NULL
		);

		$tpl->assign('lugar_expedicion', utf8_encode(implode(', ', array_filter($lugar_expedicion))));

		if (isset($this->datos['consignatario']['rfc']) && trim($this->datos['consignatario']['rfc']) != '')
		{
			$tpl->newBlock('bloque_consignatario');

			$tpl->assign('razon_social_consignatario', mb_strtoupper(trim(utf8_encode($this->datos['consignatario']['nombre']))));
			$tpl->assign('rfc_consignatario', mb_strtoupper(trim(utf8_encode($this->datos['consignatario']['rfc']))));

			$domicilio_fiscal_consignatario = array(
				trim($this->datos['consignatario']['calle']) != '' ? mb_strtoupper(trim($this->datos['consignatario']['calle'])) . (trim($this->datos['consignatario']['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($this->datos['consignatario']['no_exterior'])) : '') . (trim($this->datos['consignatario']['no_interior']) != '' ? ' ' . mb_strtoupper(trim($this->datos['consignatario']['no_interior'])) : '') : NULL,
				trim($this->datos['consignatario']['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($this->datos['consignatario']['colonia'])) : NULL,
				trim($this->datos['consignatario']['municipio']) != '' ? mb_strtoupper(trim($this->datos['consignatario']['municipio'])) : NULL,
				trim($this->datos['consignatario']['estado']) != '' ? mb_strtoupper(trim($this->datos['consignatario']['estado'])) : NULL,
				trim($this->datos['consignatario']['pais']) != '' ? mb_strtoupper(trim($this->datos['consignatario']['pais'])) : NULL,
				trim($this->datos['consignatario']['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($this->datos['consignatario']['codigo_postal'])) : NULL,
			);

			$tpl->assign('domicilio_fiscal_consignatario', utf8_encode(implode(', ', array_filter($domicilio_fiscal_consignatario))));
		}
		else
		{
			$tpl->newBlock('bloque_normal');
		}

		$tpl->assign('razon_social_receptor', mb_strtoupper(trim(utf8_encode($this->datos['cabecera']['nombre_cliente']))));
		$tpl->assign('rfc_receptor', mb_strtoupper(trim(utf8_encode($this->datos['cabecera']['rfc_cliente']))));

		$domicilio_fiscal_receptor = array(
			trim($this->datos['cabecera']['calle']) != '' ? mb_strtoupper(trim($this->datos['cabecera']['calle'])) . (trim($this->datos['cabecera']['no_exterior']) != '' ? ' ' . mb_strtoupper(trim($this->datos['cabecera']['no_exterior'])) : '') . (trim($this->datos['cabecera']['no_interior']) != '' ? ' ' . mb_strtoupper(trim($this->datos['cabecera']['no_interior'])) : '') : NULL,
			trim($this->datos['cabecera']['colonia']) != '' ? 'COL. ' . mb_strtoupper(trim($this->datos['cabecera']['colonia'])) : NULL,
			trim($this->datos['cabecera']['municipio']) != '' ? mb_strtoupper(trim($this->datos['cabecera']['municipio'])) : NULL,
			trim($this->datos['cabecera']['estado']) != '' ? mb_strtoupper(trim($this->datos['cabecera']['estado'])) : NULL,
			trim($this->datos['cabecera']['pais']) != '' ? mb_strtoupper(trim($this->datos['cabecera']['pais'])) : NULL,
			trim($this->datos['cabecera']['codigo_postal']) != '' ? 'CP. ' . mb_strtoupper(trim($this->datos['cabecera']['codigo_postal'])) : NULL,
		);

		$tpl->assign('domicilio_fiscal_receptor', utf8_encode(implode(', ', array_filter($domicilio_fiscal_receptor))));

		$tpl->assign('folio_fiscal', $uuid);
		$tpl->assign('no_certificado_digital', $no_certificado_digital);
		$tpl->assign('serie_certificado_sat', $no_certificado_sat);

		$tpl->gotoBlock('_ROOT');

		foreach ($this->datos['detalle'] as $concepto)
		{
			$tpl->newBlock('concepto');

			$tpl->assign('cantidad', number_format($concepto['cantidad'], 2));
			$tpl->assign('unidad', mb_strtoupper(trim(utf8_encode($concepto['unidad']))));
			$tpl->assign('descripcion', nl2br(mb_strtoupper(trim(utf8_encode($concepto['descripcion'])))));
			$tpl->assign('precio', number_format($concepto['precio'], 2));
			$tpl->assign('importe', number_format($concepto['importe'], 2));

			if (trim($concepto['numero_pedimento']) != '')
			{
				$tpl->newBlock('datos_aduanales');

				$tpl->assign('numero_pedimento', mb_strtoupper(trim(utf8_encode($concepto['numero_pedimento']))));
				$tpl->assign('fecha_entrada', mb_strtoupper(trim($concepto['fecha_entrada'])));
				$tpl->assign('aduana_entrada', mb_strtoupper(trim(utf8_encode($concepto['aduana_entrada']))));
			}
		}

		$tpl->gotoBlock('_ROOT');

		$tpl->assign('subtotal', number_format($this->datos['cabecera']['importe'] - $this->datos['cabecera']['descuento'], 2));

		if (isset($this->datos['cabecera']['ieps']) && $this->datos['cabecera']['ieps'] != 0)
		{
			$tpl->newBlock('ieps');
			$tpl->assign('porcentaje_ieps', 8);
			$tpl->assign('ieps', number_format($this->datos['cabecera']['ieps'], 2));
		}

		$tpl->newBlock('iva');
		$tpl->assign('porcentaje_iva', $this->datos['cabecera']['porcentaje_iva']);
		$tpl->assign('iva', number_format($this->datos['cabecera']['importe_iva'], 2));

		if ($this->datos['cabecera']['importe_retencion_iva'] != 0 || $this->datos['cabecera']['importe_retencion_isr'] != 0)
		{
			$tpl->newBlock('retenciones');
			$tpl->assign('retencion_iva', number_format($this->datos['cabecera']['importe_retencion_iva'], 2));
			$tpl->assign('retencion_isr', number_format($this->datos['cabecera']['importe_retencion_isr'], 2));
		}

		$tpl->gotoBlock('_ROOT');

		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2));

		$tpl->assign('forma_pago', mb_strtoupper('PAGO EN UNA SOLA EXHIBICIÓN'));

		$tpl->assign('metodo_pago', mb_strtoupper($this->tipo_pago[isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 'B'][1]));
		$tpl->assign('cuenta_pago', isset($this->datos['cabecera']['cuenta_pago']) ? $this->datos['cabecera']['cuenta_pago'] : '');
		$tpl->assign('condiciones_pago', mb_strtoupper($this->condiciones_pago[isset($this->datos['cabecera']['condiciones_pago']) ? $this->datos['cabecera']['condiciones_pago'] : '1']));

		$tpl->assign('importe_letra', num2string($this->datos['cabecera']['total']));

		$tpl->assign('cadena_original', $cadena_original);
		$tpl->assign('sello_digital_cfdi', $sello_cfd);
		$tpl->assign('sello_digital_sat', $sello_sat);

		$qr_code_data = "?re={$this->serie['rfc']}&rr={$this->datos['cabecera']['rfc_cliente']}&tt=" . number_format($this->datos['cabecera']['total'], 6, '.', '') . "&id={$uuid}";

		QRcode::png($qr_code_data, "{$this->ruta_codigos_qr}{$this->num_cia}/{$uuid}.png", QR_ECLEVEL_Q);

		$tpl->assign('codigo_qr', "{$this->ruta_codigos_qr}{$this->num_cia}/{$uuid}.png");

		if ($this->datos['cabecera']['observaciones'] != '')
		{
			$tpl->newBlock('observaciones');
			$tpl->assign('observaciones', nl2br(mb_strtoupper(trim(utf8_encode($this->datos['cabecera']['observaciones'])))));
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
			'disable-smart-shrinking'
			// 'use-xserver',
			// 'procEnv'		=> array( 'DISPLAY' => ':0' )
			// 'user-style-sheet' => $path . '/styles/reporte-efectivos-pdf.css',
			// 'footer-center'	=> '[page] de [toPage]'
		));

		// $pdf->setPageOptions(array(
		// ));

		$pdf->addPage($tpl->getOutputContent());

		if ( ! $pdf->saveAs($this->ruta_comprobantes_pdf . $this->num_cia . '/' . utf8_encode($this->file_name) . '.pdf'))
		{
			// throw new Exception('No se pudo crear PDF: ' . $pdf->getError());
			$retrieves = 0;

			$pdf_ok = FALSE;

			do
			{
				sleep($this->sleep_time);

				$pdf_ok = $pdf->saveAs($this->ruta_comprobantes_pdf . $this->num_cia . '/' . utf8_encode($this->file_name) . '.pdf');

				$retrieves++;
			}
			while ( ! $pdf_ok && $retrieves <= $this->max_retrieves);

			if ( ! $pdf_ok)
			{
				$this->last_error = 'No se pudo crear el documento PDF del comprobante ' . $this->file_name;

				return FALSE;
			}
		}

		return TRUE;
	}

	private function generarDocumentoXML($xml_string)
	{
		$fp = fopen($this->ruta_comprobantes_xml . $this->num_cia . '/' . utf8_encode($this->file_name) . '.xml', 'w');

		fwrite($fp, $xml_string . PHP_EOL);

		fclose($fp);
	}

	private function registrarFactura($ob_response, $fecha_timbrado, $uuid, $no_certificado_digital, $no_certificado_sat, $sello_cfd, $sello_sat, $cadena_original, $documento_xml)
	{
		$sql = '
			INSERT INTO
				facturas_electronicas
					(
						num_cia,
						fecha,
						hora,
						tipo_serie,
						consecutivo,
						tipo,
						clave_cliente,
						nombre_cliente,
						rfc,
						calle,
						no_exterior,
						no_interior,
						colonia,
						localidad,
						referencia,
						municipio,
						estado,
						pais,
						codigo_postal,
						email_cliente,
						observaciones,
						importe,
						porcentaje_descuento,
						descuento,
						ieps,
						iva,
						retencion_iva,
						retencion_isr,
						total,
						iduser_ins,
						fecha_pago,
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
						tipo_pago,
						cuenta_pago,
						condiciones_pago,
						process_time,
						fecha_timbrado,
						uuid,
						no_certificado_digital,
						no_certificado_sat,
						sello_cfd,
						sello_sat,
						cadena_original,
						documento_xml,
						ob_response,
						arrendador
					)
				VALUES
					(
						' . $this->num_cia . ',
						\'' . $this->datos['cabecera']['fecha'] . '\',
						\'' . $this->datos['cabecera']['hora'] . '\',
						' . $this->tipo . ',
						' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . ',
						' . $this->datos['cabecera']['clasificacion'] . ',
						' . $this->datos['cabecera']['clave_cliente'] . ',
						\'' . pg_escape_string($this->datos['cabecera']['nombre_cliente']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['calle']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['no_exterior']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['no_interior']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['colonia']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['localidad']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['referencia']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['municipio']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['estado']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['pais']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['codigo_postal']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['email']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['observaciones']) . '\',
						' . $this->datos['cabecera']['importe'] . ',
						' . $this->datos['cabecera']['porcentaje_descuento'] .',
						' . $this->datos['cabecera']['descuento'] . ',
						' . (isset($this->datos['cabecera']['ieps']) ? $this->datos['cabecera']['ieps'] : 0) . ',
						' . $this->datos['cabecera']['importe_iva'] . ',
						' . $this->datos['cabecera']['importe_retencion_iva'] . ',
						' . $this->datos['cabecera']['importe_retencion_isr'] . ',
						' . $this->datos['cabecera']['total'] . ',
						' . $this->iduser . ',
						\'' . $this->datos['cabecera']['fecha'] . '\',
						\'' . pg_escape_string($this->datos['consignatario']['nombre']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['rfc']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['calle']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['no_exterior']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['no_interior']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['colonia']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['localidad']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['referencia']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['municipio']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['estado']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['pais']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['codigo_postal']) . '\',
						\'' . (isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 'B') . '\',
						' . (isset($this->datos['cabecera']['cuenta_pago']) && $this->datos['cabecera']['cuenta_pago'] != '' ? '\'' . $this->datos['cabecera']['cuenta_pago'] . '\'' : 'NULL') . ',
						' . (isset($this->datos['cabecera']['condiciones_pago']) ? $this->datos['cabecera']['condiciones_pago'] : '1') . ',
						' . ($this->process_end - $this->process_init) . ',
						\'' . pg_escape_string($fecha_timbrado) . '\',
						\'' . pg_escape_string($uuid) . '\',
						\'' . pg_escape_string($no_certificado_digital) . '\',
						\'' . pg_escape_string($no_certificado_sat) . '\',
						\'' . pg_escape_string($sello_cfd) . '\',
						\'' . pg_escape_string($sello_sat) . '\',
						\'' . pg_escape_string($cadena_original) . '\',
						\'' . pg_escape_string($documento_xml) . '\',
						\'' . pg_escape_string($ob_response) . '\',
						' . (isset($this->datos['cabecera']['arrendador']) ? $this->datos['cabecera']['arrendador'] : 'NULL') . '
					)
		' . ";\n";

		foreach ($this->datos['detalle'] as $i => $detalle)
		{
			$sql .= '
				INSERT INTO
					facturas_electronicas_detalle
						(
							num_cia,
							tipo_serie,
							consecutivo,
							clave_producto,
							cantidad,
							descripcion,
							precio,
							unidad,
							importe,
							ieps,
							pieps,
							iva,
							piva,
							numero_pedimento,
							fecha_entrada,
							aduana_entrada
						)
					VALUES
						(
							' . $this->num_cia . ',
							' . $this->tipo . ',
							' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . ',
							' . $detalle['clave'] . ',
							' . $detalle['cantidad'] . ',
							\'' . pg_escape_string($detalle['descripcion']) . '\',
							' . $detalle['precio'] . ',
							\'' . pg_escape_string($detalle['unidad']) . '\',
							' . $detalle['importe'] . ',
							' . (isset($detalle['importe_ieps']) ? $detalle['importe_ieps'] : 0) . ',
							' . (isset($detalle['porcentaje_ieps']) ? $detalle['porcentaje_ieps'] : 0) . ',
							' . $detalle['importe_iva'] . ',
							' . $detalle['porcentaje_iva'] . ',
							\'' . pg_escape_string($detalle['numero_pedimento']) . '\',
							' . ($detalle['fecha_entrada'] != '' ? '\'' . $detalle['fecha_entrada'] . '\'' : 'NULL') . ',
							\'' . pg_escape_string($detalle['aduana_entrada']) . '\'
						)
			' . ";\n";
		}

		/*
		@ Actualizar series solo si no es reservado
		*/
		if ($this->folio == NULL)
		{
			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					ultimo_folio_usado = ' . $this->serie['folio'] . '
				WHERE
					num_cia = ' . $this->num_cia . '
					AND tipo_serie = ' . $this->tipo . '
					AND status = 1
					AND folio_inicial = ' . $this->serie['folio_inicial'] . '
					AND folio_final = ' . $this->serie['folio_final'] . '
			' . ";\n";
		}

		/*
		@ Poner serie como terminada si se ha llegado al máximo de folios
		*/
		if ($this->folio == NULL && $this->serie['folio'] == $this->serie['folio_final'])
		{
			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					status = 2
				WHERE
					num_cia = ' . $this->num_cia . '
					AND tipo_serie = ' . $this->tipo . '
					AND status = 1
					AND folio_inicial = ' . $this->serie['folio_inicial'] . '
					AND folio_final = ' . $this->serie['folio_final'] . '
			' . ";\n";
		}

		$this->db->query($sql);
	}

	public function generarFactura($iduser, $num_cia, $tipo, $datos, $folio = NULL)
	{
		$this->iduser  = $iduser;

		$this->num_cia = $num_cia;
		$this->tipo    = $tipo;
		$this->datos   = $datos;
		$this->folio   = $folio;

		$this->status = 0;

		if ( ! $this->validarStatusServidor())
		{
			return $this->status;
		}
		else if ( ! $this->validarSerie())
		{
			return $this->status;
		}
		else if ( ! $this->validarDuplicados())
		{
			return $this->status;
		}
		else if ( ! $this->validarLimiteTotal())
		{
			return $this->status;
		}
		else if ( ! $this->validarDatos())
		{
			return $this->status;
		}
		else if ($this->folio > 0 && ! $this->validarFolio())
		{
			return $this->status;
		}
		else
		{
			$this->status = $this->generarFacturaElectronica();

			return $this->status;
		}
	}

	public function cancelarFactura($iduser, $id, $motivo = '')
	{
		global $db;

		$result = $db->query("SELECT
			num_cia,
			rfc,
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
				AS folio,
			status
		FROM
			facturas_electronicas fe
		WHERE
			id = {$id}");

		if ( ! $result)
		{
			$this->status = -500;

			return FALSE;
		}
		if ($result[0]['status'] == 2)
		{
			$this->status = -501;

			return FALSE;
		}

		$datos = array(
			'num_cia'	=> $result[0]['num_cia'],
			'rfc'		=> $result[0]['rfc'],
			'serie'		=> $result[0]['serie'],
			'folio'		=> $result[0]['folio']
		);

		$xml_data = $this->generarXMLCancelacion($datos);

		// Construir arreglo de opciones para consumir el webservice
		$stream_options = array(
			'http' => array(
				'method'	=> 'PUT',
				'header'	=> 'Authorization: Basic ' . base64_encode("$this->username:$this->password") . ' Content-Type: text/xml',
				'content'	=> $xml_data,
			),
		);

		// Crear contexto de flujo con las opciones para consumir el webservice
		$context = stream_context_create($stream_options);

		// Consumir webservice y obtener respuesta
		$ob_response = @file_get_contents($this->url . $this->ws, NULL, $context);

		if ($ob_response === FALSE)
		{
			$this->status = -600;

			return FALSE;
		}

		$ob_data = json_decode(utf8_encode($ob_response));

		if ($ob_data->status < 0)
		{
			$this->status = -601;

			$status = utf8_encode(isset($ob_data->mensaje) || $ob_data->mensaje == NULL ? $ob_data->mensaje : (isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : $ob_response))));

			$this->last_error = $status;

			return FALSE;
		}
		else
		{
			$status = "Cancelación de documento exitosa.";

			$db->query("UPDATE
				facturas_electronicas
			SET
				status = 0,
				iduser_can = {$iduser},
				motivo_cancelacion = '" . utf8_decode($motivo) . "',
				tscan = now()
			WHERE
				id = {$id}");

			$this->enviarEmailCancelacion($id);

			return TRUE;
		}
	}

	public function generarXMLCancelacion($datos)
	{
		// Crear documento XML con los datos para el webservice
		$xml = new DOMDocument('1.0', 'UTF-8');

		$xml->xmlStandalone = TRUE;
		$xml->formatOutput = TRUE;

		// Construir estructura del XML
		$xml_timbreCancel = $xml->createElement('timbreCancel');
		$xml->appendChild($xml_timbreCancel);

		$xml_rfc = $xml->createElement('rfc', htmlspecialchars(utf8_encode($datos['rfc'])));
		$xml_timbreCancel->appendChild($xml_rfc);

		$xml_invoices = $xml->createElement('invoices');
		$xml_timbreCancel->appendChild($xml_invoices);

		$xml_invoice = $xml->createElement('invoice');
		$xml_invoices->appendChild($xml_invoice);

		$xml_documentno = $xml->createElement('documentno', htmlspecialchars(utf8_encode($datos['serie'] . $datos['folio'])));
		$xml_invoice->appendChild($xml_documentno);

		$xml_org = $xml->createElement('org', $datos['num_cia']);
		$xml_invoice->appendChild($xml_org);

		// Retornar el XML
		return $xml->saveXML();
	}

	public function enviarEmail($emails = array())
	{
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer'))
		{
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}

		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower'))
		{
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}

		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string'))
		{
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}

		$mail = new PHPMailer();

		if ($this->num_cia >= 900)
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'G1j7n7a*';

			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}

		/*
		@ Email para compañía
		*/
		if (trim($this->serie['email']) != '')
		{
			$mail->AddCC($this->serie['email']);
		}

		/*
		@ Email para Elite
		*/
		if ($this->num_cia >= 900)
		{
			$mail->AddBCC('contabilidad@zapateriaselite.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($this->num_cia < 900)
		{
			$mail->AddBCC('beatriz.flores@lecaroz.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');

			/*if (in_array($this->datos['cabecera']['clasificacion'], array(2, 5)))
			{
				$mail->AddBCC('facturas@lecaroz.com');
			}*/
		}

		/*
		@ Email para contadores
		*/
		if ($this->serie['email_contador'] != '')
		{
			$mail->AddBCC($this->serie['email_contador']);
		}

		/*
		@ Email para cliente
		*/
		if ($this->datos['cabecera']['email'] != '')
		{
			$mail->AddAddress($this->datos['cabecera']['email']);
		}

		if (count($emails) > 0)
		{
			foreach ($emails as $email)
			{
				if ($email != '')
				{
					$mail->AddAddress($email);
				}
			}
		}

		$mail->Subject = 'Comprobante Fiscal Digital :: ' . $this->serie['razon_social'];

		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd.tpl');
		$tpl->prepare();

		$tpl->assign('nombre_cia', htmlentities($this->serie['razon_social']));
		$tpl->assign('rfc_cia', htmlentities($this->serie['rfc']));

		$tpl->assign('folio', ($this->serie['serie'] != '' ? $this->serie['serie'] . '-' : '') . ($this->folio > 0 ? $this->folio : $this->serie['folio']));
		$tpl->assign('nombre_cliente', htmlentities($this->datos['cabecera']['nombre_cliente']));
		$tpl->assign('rfc_cliente', htmlentities($this->datos['cabecera']['rfc_cliente']));
		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2, '.', ','));
		$tpl->assign('total_escrito', htmlentities(num2string($this->datos['cabecera']['total'])));

		$tpl->assign('email_ayuda', $this->num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com');

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		$mail->AddAttachment($this->ruta_comprobantes_pdf . $this->num_cia . '/' . utf8_encode($this->file_name) . '.pdf');

		if ($this->serie['tipo_factura'] == 1)
		{
			$mail->AddAttachment($this->ruta_comprobantes_xml . $this->num_cia . '/' . utf8_encode($this->file_name) . '.xml');
		}

		if(!$mail->Send())
		{
			return $mail->ErrorInfo;
		}
		else
		{
			return TRUE;
		}
	}

	public function enviarEmailCancelacion($id)
	{
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer'))
		{
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}

		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower'))
		{
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}

		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string'))
		{
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}

		$sql = '
			SELECT
				num_cia,
				razon_social,
				cc.rfc
					AS rfc_cia,
				cc.email
					AS email_cia,
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
					AS folio,
				nombre_cliente,
				fe.rfc
					AS rfc_cliente,
				fe.email_cliente,
				total,
				tscan::DATE
					AS fecha_cancelacion,
				motivo_cancelacion
			FROM
				facturas_electronicas fe
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				id = ' . $id . '
		';

		$result = $this->db->query($sql);

		$rec = $result[0];

		$mail = new PHPMailer();

		if ($this->num_cia >= 900)
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'G1j7n7a*';

			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}

		/*
		@ Email para cliente
		*/
		if (trim($rec['email_cliente']) != '')
		{
			$mail->AddAddress($rec['email_cliente']);
		}

		/*
		@ Email para compañía
		*/
		if (trim($rec['email_cia']) != '')
		{
			$mail->AddAddress($rec['email_cia']);
		}

		/*
		@ Email para Elite
		*/
		if ($rec['num_cia'] >= 900)
		{
			$mail->AddAddress('contabilidad@zapateriaselite.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($rec['num_cia'] < 900)
		{
			$mail->AddBCC('beatriz.flores@lecaroz.com');
			//$mail->AddBCC('sistemas@lecaroz.com');
		}

		$mail->Subject = utf8_decode('CANCELACIÓN DE COMPROBANTE FISCAL');

		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd_cancel.tpl');
		$tpl->prepare();

		$tpl->assign('nombre_cia', htmlentities($rec['razon_social']));
		$tpl->assign('rfc_cia', htmlentities($rec['rfc_cia']));

		$tpl->assign('folio', ($rec['serie'] != '' ? $rec['serie'] . '-' : '') . $rec['folio']);
		$tpl->assign('nombre_cliente', htmlentities($rec['nombre_cliente']));
		$tpl->assign('rfc_cliente', htmlentities($rec['rfc_cliente']));
		$tpl->assign('total', number_format($rec['total'], 2, '.', ','));
		$tpl->assign('total_escrito', htmlentities(num2string($rec['total'])));
		$tpl->assign('fecha_cancelacion', htmlentities($rec['fecha_cancelacion']));

		$tpl->assign('motivo_cancelacion', $rec['motivo_cancelacion'] != '' ? htmlentities($rec['motivo_cancelacion']) : 'Solicitar informaci&oacute;n al correo ' . ($rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com'));

		$tpl->assign('email_ayuda', $rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com');

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		if(!$mail->Send())
		{
			return $mail->ErrorInfo;
		}
		else
		{
			return TRUE;
		}
	}

	public function enviarEmailError()
	{
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer'))
		{
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}

		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower'))
		{
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}

		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string'))
		{
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}

		$mail = new PHPMailer();

		if ($this->num_cia >= 900)
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'G1j7n7a*';

			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}

		/*
		@ Email para compañía
		*/
		if (trim($this->serie['email']) != '')
		{
			$mail->AddAddress($this->serie['email']);
		}

		/*
		@ Email para Elite
		*/
		if ($this->num_cia >= 900)
		{
			$mail->AddAddress('contabilidad@zapateriaselite.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($this->num_cia < 900)
		{
			$mail->AddAddress('beatriz.flores@lecaroz.com');

			if (in_array($this->datos['cabecera']['clasificacion'], array(2, 5)))
			{
				$mail->AddAddress('facturas@lecaroz.com');
			}
		}

		if (in_array($this->ultimoCodigoError(), array(-9, -10, -11, -12, -13, -50, -51, -621, -622, -603)))
		{
			$mail->AddBCC('sistemas@lecaroz.com');
		}

		$mail->Subject = 'ERROR AL GENERAR EL COMPROBANTE FISCAL';

		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd_error.tpl');
		$tpl->prepare();

		$tpl->assign('num_cia', $this->num_cia);
		$tpl->assign('nombre_cia', htmlentities(utf8_decode($this->serie['razon_social'])));
		$tpl->assign('rfc_cia', htmlentities(utf8_decode($this->serie['rfc'])));

		$tpl->assign('codigo', $this->ultimoCodigoError());
		$tpl->assign('descripcion', htmlentities(utf8_decode($this->ultimoError())));

		$tpl->assign('nombre_cliente', htmlentities(utf8_decode($this->datos['cabecera']['nombre_cliente'])));
		$tpl->assign('rfc_cliente', htmlentities(utf8_decode($this->datos['cabecera']['rfc_cliente'])));
		$tpl->assign('calle', htmlentities(utf8_decode($this->datos['cabecera']['calle'])));
		$tpl->assign('no_exterior', htmlentities(utf8_decode($this->datos['cabecera']['no_exterior'])));
		$tpl->assign('no_interior', htmlentities(utf8_decode($this->datos['cabecera']['no_interior'])));
		$tpl->assign('colonia', htmlentities(utf8_decode($this->datos['cabecera']['colonia'])));
		$tpl->assign('localidad', htmlentities(utf8_decode($this->datos['cabecera']['localidad'])));
		$tpl->assign('referencia', htmlentities(utf8_decode($this->datos['cabecera']['referencia'])));
		$tpl->assign('municipio', htmlentities(utf8_decode($this->datos['cabecera']['municipio'])));
		$tpl->assign('estado', htmlentities(utf8_decode($this->datos['cabecera']['estado'])));
		$tpl->assign('pais', htmlentities(utf8_decode($this->datos['cabecera']['pais'])));
		$tpl->assign('codigo_postal', htmlentities($this->datos['cabecera']['codigo_postal']));

		if ($this->ultimoCodigoError() == -100)
		{
			foreach ($this->header_error as $error)
			{
				$tpl->assign('mark_' . abs($error), 'class="mark"');
			}
		}

		foreach ($this->datos['detalle'] as $detalle)
		{
			$tpl->newBlock('detalle');
			$tpl->assign('descripcion', htmlentities(utf8_decode($detalle['descripcion'])));
			$tpl->assign('cantidad', number_format($detalle['cantidad'], 2, '.', ','));
			$tpl->assign('precio', number_format($detalle['precio'], 2, '.', ','));
			$tpl->assign('unidad', htmlentities($detalle['unidad']));
			$tpl->assign('importe', number_format($detalle['importe'], 2, '.', ','));

			if ($this->ultimoCodigoError() == -150)
			{
				$tpl->assign('mark_importe', ' class="mark"');
			}
		}

		$tpl->gotoBlock('_ROOT');

		$tpl->assign('subtotal', number_format($this->datos['cabecera']['importe'], 2, '.', ','));
		$tpl->assign('iva', number_format($this->datos['cabecera']['importe_iva'], 2, '.', ','));
		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2, '.', ','));

		if ($this->ultimoCodigoError() == -150 || $this->ultimoCodigoError() == -151)
		{
			$tpl->assign('mark_subtotal', ' class="mark"');
		}

		if ($this->ultimoCodigoError() == -151)
		{
			$tpl->assign('mark_iva', ' class="mark"');
			$tpl->assign('mark_total', ' class="mark"');
		}

		$oficina = $this->num_cia >= 900 ? 'ZAPATERIAS ELITE (OFICINA) AL TELEFONO (55)5709-7982' : 'OFICINAS ADMINISTRATIVAS MOLLENDO AL TELEFONO (55)5276-6570';

		if (in_array($this->ultimoCodigoError(), array(-1, -2, -3, -5, -6, -7, -8, -50)))
		{
			$tpl->assign('accion', 'DEBERA ESPERAR A QUE EL COMPROBANTE LE SEA ENVIADO O EN SU DEFECTO COMUNICARSE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.<br /><br />NO INTENTE POR NINGUN MOTIVO REPETIR EL COMPROBANTE O ESTE SE DUPLICARA E INCURRIRA EN UNA FALTA, Y SI HACE CASO OMISO DEBERA REPORTARLO INMEDIATAMENTE A LA OFICINA CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-4)))
		{
			$tpl->assign('accion', 'ESTA EMPRESA NO CUENTA CON UNA SERIE PARA EMITIR COMPROBANTES FISCALES, COMUNIQUESE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION Y SE HAGAN LOS TRAMITES CORRESPONDIENTES.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-80)))
		{
			$tpl->assign('accion', 'POR DISPOSICION DE LA OFICINA NO PUEDE REPETIR UN COMPROBANTE SIN ANTES SOLICITAR LA CANCELACION DEL ANTERIOR.<br /><br />SI NECESITA EMITIR ESTE COMPROBANTE DEBERA COMUNICARSE A ' . $oficina . ' CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION Y CON INFORMACION EN MANO SOLICITAR LE HAGA LA EMISION DEL MISMO.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-81)))
		{
			$tpl->assign('accion', 'POR DISPOSICION DE LA OFICINA NO PUEDE EMITIR COMPROBANTES DE MAS DE 15,000.00 PESOS.<br /><br />SI NECESITA EMITIR ESTE COMPROBANTE DEBERA COMUNICARSE A ' . $oficina . ' CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION Y CON INFORMACION EN MANO SOLICITAR LE HAGA LA EMISION DEL MISMO.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-9, -100, -150, -151)))
		{
			$tpl->assign('accion', 'REVISE QUE LOS DATOS QUE HA INGRESADO SEAN CORRECTOS Y ESTEN DEBIDAMENTE CAPTURADOS EN EL CAMPO CORRESPONDIENTE, DESPUES INTENTE EMITIR EL COMPROBANTE NUEVAMENTE. SI CONTINUA RECIBIENDO ESTE MENSAJE COMUNIQUESE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION (DEBE TENER A LA MANO LOS DATOS EXACTAMENTE COMO SE HAN CAPTURADO).');
		}
		else if (in_array($this->ultimoCodigoError(), array(-10, -11, -12, -51)))
		{
			$tpl->assign('accion', 'ERROR INTERNO. ES EXTREMADAMENTE IMPORTANTE SE COMUNIQUE A ' . $oficina . ' Y REPORTAR EL PROBLEMA ANTES DE SEGUIR EMITIENDO CUALQUIER TIPO DE COMPROBANTE.<br /><br />NO INTENTE POR NINGUN MOTIVO REPETIR EL COMPROBANTE O PODRIA GENERAR MAS ERRORES E INCURRIRA EN UNA FALTA, Y SI HACE CASO OMISO DEBERA REPORTARLO INMEDIATAMENTE A LA OFICINA CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.');
		}

		$tpl->assign('email_info', $this->num_cia >= 900 ? 'contabilidad@zapateriaselite.com' : 'beatriz.flores@lecaroz.com');

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		if(!$mail->Send())
		{
			return $mail->ErrorInfo;
		}
		else
		{
			return TRUE;
		}
	}

	public function enviarEmailErrorOB()
	{
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer'))
		{
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}

		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower'))
		{
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}

		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string'))
		{
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}

		$mail = new PHPMailer();

		if ($this->num_cia >= 900)
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'G1j7n7a*';

			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_encode('Zapaterías Elite :: Facturación Electrónica');
		}
		else
		{
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}

		$mail->AddAddress('siacsis@gmail.com');
		$mail->AddAddress('jbello@gcusoft.com');

		// $mail->AddCC('marioal@yaznicotelecom.com');
		// $mail->AddBCC('sistemas@lecaroz.com');

		$mail->Subject = 'ERROR DE OPENBRAVO AL GENERAR EL COMPROBANTE FISCAL';

		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd_error_ob.tpl');
		$tpl->prepare();

		$tpl->assign('num_cia', $this->num_cia);
		$tpl->assign('nombre_cia', htmlentities($this->serie['razon_social']));
		$tpl->assign('rfc_cia', htmlentities($this->serie['rfc']));

		$tpl->assign('codigo', $this->ultimoCodigoError());
		$tpl->assign('descripcion', htmlentities($this->ultimoError()));

		$tpl->assign('nombre_cliente', htmlentities($this->datos['cabecera']['nombre_cliente']));
		$tpl->assign('rfc_cliente', htmlentities($this->datos['cabecera']['rfc_cliente']));
		$tpl->assign('calle', htmlentities($this->datos['cabecera']['calle']));
		$tpl->assign('no_exterior', htmlentities($this->datos['cabecera']['no_exterior']));
		$tpl->assign('no_interior', htmlentities($this->datos['cabecera']['no_interior']));
		$tpl->assign('colonia', htmlentities($this->datos['cabecera']['colonia']));
		$tpl->assign('localidad', htmlentities($this->datos['cabecera']['localidad']));
		$tpl->assign('referencia', htmlentities($this->datos['cabecera']['referencia']));
		$tpl->assign('municipio', htmlentities($this->datos['cabecera']['municipio']));
		$tpl->assign('estado', htmlentities($this->datos['cabecera']['estado']));
		$tpl->assign('pais', htmlentities($this->datos['cabecera']['pais']));
		$tpl->assign('codigo_postal', htmlentities($this->datos['cabecera']['codigo_postal']));

		if ($this->ultimoCodigoError() == -100)
		{
			foreach ($this->header_error as $error)
			{
				$tpl->assign('mark_' . abs($error), 'class="mark"');
			}
		}

		foreach ($this->datos['detalle'] as $detalle)
		{
			$tpl->newBlock('detalle');
			$tpl->assign('descripcion', htmlentities($detalle['descripcion']));
			$tpl->assign('cantidad', number_format($detalle['cantidad'], 2, '.', ','));
			$tpl->assign('precio', number_format($detalle['precio'], 2, '.', ','));
			$tpl->assign('unidad', htmlentities($detalle['unidad']));
			$tpl->assign('importe', number_format($detalle['importe'], 2, '.', ','));

			if ($this->ultimoCodigoError() == -150)
			{
				$tpl->assign('mark_importe', ' class="mark"');
			}
		}

		$tpl->gotoBlock('_ROOT');

		$tpl->assign('subtotal', number_format($this->datos['cabecera']['importe'], 2, '.', ','));
		$tpl->assign('iva', number_format($this->datos['cabecera']['importe_iva'], 2, '.', ','));
		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2, '.', ','));

		if ($this->ultimoCodigoError() == -150 || $this->ultimoCodigoError() == -151)
		{
			$tpl->assign('mark_subtotal', ' class="mark"');
		}

		if ($this->ultimoCodigoError() == -151)
		{
			$tpl->assign('mark_iva', ' class="mark"');
			$tpl->assign('mark_total', ' class="mark"');
		}

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		$mail->AddAttachment($this->ruta_xml_carga . $this->num_cia . '/' . $this->file_name . '.xml');

		if(!$mail->Send())
		{
			return $mail->ErrorInfo;
		}
		else
		{
			return TRUE;
		}
	}

	public function ultimoCodigoError()
	{
		return $this->status;
	}

	public function ultimoError()
	{
		switch ($this->status)
		{
			case -1:
				return 'Error al conectar al servidor FTP';
				break;

			case -2:
				return 'Error al iniciar sesión en el servidor FTP';
				break;

			case -3:
				return 'No se pueden generar facturas electrónicas porque el servidor se encuentra ocupado';
				break;

			case -4:
				return 'La compañía no tiene folios disponibles';
				break;

			case -5:
				return 'No se pudieron registrar los datos de facturación en el servidor de facturas electrónicas';
				break;

			case -6:
				return 'El proceso de timbrado para la factura esta detenido';
				break;

			case -7:
				return $this->last_error;
				break;

			case -8:
				return 'No se ha procesado la factura';
				break;

			case -9:
				return 'No se completo el timbrado de la factura';
				break;

			case -10:
				return $this->last_error;
				break;

			case -11;
				return 'No se pudo obtener el archivo ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'] . '.xml';
				break;

			case -12:
				return 'No se pudo obtener el archivo ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'] . '.pdf';
				break;

			case -13:
				return 'El servidor de facturas reporto un error y no se generó el comprobante ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'];
				break;

			case -14:
				return 'El servidor de facturas no timbro el comprobante ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'];
				break;

			case -50:
				return $this->last_error;
				break;

			case -51:
				return $this->last_error;
				break;

			case -80:
				return 'No puede hacer 2 facturas con el mismo importe';
				break;

			case -81:
				return 'No puede hacer facturas por más de 15,000.00 pesos';
				break;

			case -100:
				$lista_errores = array(
					-101 => 'Nombre',
					-102 => 'R.F.C.',
					-103 => 'R.F.C. (Estructura)',
					-104 => 'Calle',
					-105 => 'Colonia',
					-106 => 'Delagacion o Municipio',
					-107 => 'Estado',
					-108 => 'Pais',
					-109 => 'Código postal',
					-110 => $this->datos['cabecera']['estado']. ' NO ES UN ESTADO',
					-111 => 'R.F.C. (Estructura)'
				);

				$errores = array();

				foreach ($this->header_error as $error)
				{
					$errores[] = $lista_errores[$error];
				}

				return 'La información fiscal del cliente contiene errores [' . implode('|', $errores) . ']';
				break;

			case -150:
				return 'La suma de los importes por detalle difiere del subtotal de la factura';
				break;

			case -151:
				return 'La suma del subtotal, descuentos e impuestos difiere del total de la factura';
				break;

			case -160:
				return 'El folio ya no esta disponible';
				break;

			case -161:
				return 'El folio es mayor al último disponible';
				break;

			case -500:
				return 'Registro de factura no existe';
				break;

			case -501:
				return 'La factura ya ha sido cancelada con anterioridad';
				break;

			case -502:
				return '';
				break;

			case -503:
				return $this->last_error;
				break;

			case -600:
				return 'Webservice no disponible';
				break;

			case -601:
				return 'Openbravo retorno el error: ' . $this->last_error;
				break;

			case -603:
				return 'Imposible decodificar cadena de respuesta: ' . $this->last_error;
				break;

			case -622:
				return $this->last_error;
				break;

			default:
				return 'Error desconocido';
		}
	}

	public function codigosErrorCabecera()
	{
		return $this->header_error;
	}

	public function reservarFolio($iduser, $num_cia, $tipo_serie, $fecha)
	{
		$sql = '
			SELECT
				id,
				num_cia,
				tipo_serie,
				ultimo_folio_usado + 1
					AS folio,
				folio_inicial,
				folio_final
			FROM
				facturas_electronicas_series
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND status = 1
		';

		$result = $this->db->query($sql);

		if ($result)
		{
			$sql = '
				INSERT INTO
					facturas_electronicas_folios_reservados
						(
							num_cia,
							tipo_serie,
							folio,
							fecha,
							idins,
							tsins
						)
					SELECT
						num_cia,
						tipo_serie,
						ultimo_folio_usado + 1,
						\'' . $fecha . '\',
						' . $iduser . ',
						now()
					FROM
						facturas_electronicas_series
					WHERE
						id = ' . $result[0]['id'] . '
			' . ";\n";

			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					ultimo_folio_usado = ultimo_folio_usado + 1
				WHERE
					id = ' . $result[0]['id'] . '
			' . ";\n";

			if ($result[0]['folio'] == $result[0]['folio_final'])
			{
				$sql .= '
					UPDATE
						facturas_electronicas_series
					SET
						status = 2
					WHERE
						id = ' . $result[0]['id'] . '
				' . ";\n";
			}

			$this->db->query($sql);

			return $result[0]['folio'];
		}
		else
		{
			return NULL;
		}
	}

	public function recuperarFolio($num_cia, $tipo_serie, $fecha)
	{
		$sql = '
			SELECT
				folio
			FROM
				facturas_electronicas_folios_reservados
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND fecha = \'' . $fecha . '\'
				AND tsreg IS NULL
		';

		$result = $this->db->query($sql);

		if ($result)
		{
			return intval($result[0]['folio'], 10);
		}
		else
		{
			return NULL;
		}
	}

	public function utilizarFolio($iduser, $num_cia, $tipo_serie, $folio)
	{
		$sql = '
			UPDATE
				facturas_electronicas_folios_reservados
			SET
				tsreg = now(),
				idreg = ' . $iduser . '
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND folio = ' . $folio . '
		';

		$this->db->query($sql);
	}

	public function dmy_to_ymd($date)
	{
		list($day, $month, $year) = explode('/', $date);

		return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
	}

}
