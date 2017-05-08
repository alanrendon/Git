<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('GenerarComprobantesFiscales.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if (!in_array($_SESSION['iduser'], array(1/*, 10, 34*/))) die('EN PROCESO DE ACTUALIZACION.');

function toInt($value)
{
	return intval($value, 10);
}

function ymd_to_dmy($date)
{
	list($year, $month, $day) = explode('-', $date);

	return date('d-m-Y', mktime(0, 0, 0, $month, $day, $year));
}

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{
		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias cc
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					--AND UPPER(TRIM(rfc)) = UPPER(TRIM(\'' . $_REQUEST['rfc'] . '\'))
			';
			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_cia']);
			}

			break;

		case 'obtener_arrendadores':
			$sql = "SELECT
				idarrendador AS value,
				LPAD(arrendador::VARCHAR, 3, '0') || ' ' || nombre_arrendador AS text
			FROM
				rentas_arrendadores
			WHERE
				homoclave = {$_REQUEST['num_cia']}
				AND tsdel IS NULL
			ORDER BY
				CASE
					WHEN arrendador = homoclave THEN
						1
					ELSE
						2
				END,
				arrendador";

			$result = $db->query($sql);

			if ($result)
			{
				$data = array('arrendadores' => array());

				foreach ($result as $row)
				{
					$data['arrendadores'][] = array(
						'value'		=> $row['value'],
						'text'		=> utf8_encode($row['text']),
						'selected'	=> $row['value'] == $_REQUEST['num_cia'] ? TRUE : FALSE
					);
				}

				echo json_encode($data);
			}

			break;

		case 'obtener_arrendatarios':
			$sql = "SELECT
				idarrendatario AS value,
				LPAD(arrendatario::VARCHAR, 3, '0') || ' ' || nombre_arrendatario || ' (' || alias_arrendatario || ') [' || total || ']' AS text
			FROM
				rentas_arrendatarios
			WHERE
				idarrendador = {$_REQUEST['arrendador']}
				AND total > 0
				AND tsbaja IS NULL
			ORDER BY
				nombre_arrendatario";

			$result = $db->query($sql);

			if ($result)
			{
				$data = array('arrendatarios' => array());

				foreach ($result as $row)
				{
					$data['arrendatarios'][] = array(
						'value'	=> $row['value'],
						'text'	=> utf8_encode($row['text'])
					);
				}

				echo json_encode($data);
			}

			break;

		case 'validar_xml':
			$allowed_formats = array(
				'application/xml',
				'text/xml'
			);

			// Validar que el archivo se haya cargado sin errores
			if ($_FILES['xml_file']['error'] > 0)
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -1,
					'error'		=> 'Error en la carga del archivo al servidor'
				));
			}
			// Validar que el tipo de archivo sea válido
			else if ( ! in_array($_FILES['xml_file']['type'], $allowed_formats))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -2,
					'error'		=> 'El tipo de archivo debe ser XML',
					'type'		=> $_FILES['xml_file']['type']
				));
			}
			else
			{
				// Interpretar archivo XML
				$xml = simplexml_load_file($_FILES['xml_file']['tmp_name']);

				// Obtener los namespaces del documento
				$ns = $xml->getNamespaces(true);

				$xml->registerXPathNamespace('cfdi', $ns['cfdi']);
				$xml->registerXPathNamespace('tfd', $ns['tfd']);

				// Convertir objeto en arreglo
				$xml_data = get_object_vars($xml);

				// Obtener la versión del CFD
				$version  = (string) $xml_data['@attributes']['version'];

				$data = FALSE;

				// Versión del comprobante 2.2 (CFD)
				if ($version == '2.2')
				{
					$emisor_data = get_object_vars($xml->Emisor);

					header('Content-Type: application/json');
					echo json_encode(array(
						'status'	=> -7,
						'error'		=> 'Sólo se permiten CFD/CFDI versión 3.2'
					));

					die;

					// if (isset($emisor_data['@attributes']['rfc']) && $emisor_data['@attributes']['rfc'] != '')
					// {
					// 	$data = array(
					// 		'rfc'		=> $emisor_data['@attributes']['rfc'],
					// 		'folio'		=> $xml_data['@attributes']['folio'],
					// 		'serie'		=> isset($xml_data['@attributes']['serie']) ? mb_strtoupper($xml_data['@attributes']['serie']) : '',
					// 		'tipo'		=> 'cfd',
					// 		'id'		=> NULL,
					// 		'xml_file'	=> NULL,
					// 		'pdf_file'	=> NULL
					// 	);
					// }
				}
				// Versión del comprobante 3.2 (CFDI)
				else if ($version == '3.2')
				{
					$comprobante_data = $xml->xpath('//cfdi:Comprobante');

					if ($comprobante_data)
					{
						$emisor_data = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
						$emisor_domicilio_data = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal');
						$emisor_regimen_data = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal');
						$receptor_data = $xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
						$domicilio_data = $xml->xpath('//cfdi:Comprobante//cfdi:Receptor//cfdi:Domicilio');
						$conceptos_data = $xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto');
						$cuenta_predial_data = $xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto//cfdi:CuentaPredial');
						$impuesto_data = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
						$impuestos_data = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado');
						$retenciones_data = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion');
						$timbre_data = $xml->xpath('//cfdi:Comprobante//cfdi:Complemento//tfd:TimbreFiscalDigital');

						list($fecha, $hora) = explode('T', (string) $comprobante_data[0]['fecha']);

						$fecha = ymd_to_dmy($fecha);

						// Construir cadena original
						$cadena_original_pieces = array();

						$cadena_original_pieces[] = $version;
						$cadena_original_pieces[] = $comprobante_data[0]['fecha'];
						$cadena_original_pieces[] = $comprobante_data[0]['tipoDeComprobante'];
						$cadena_original_pieces[] = $comprobante_data[0]['formaDePago'];
						$cadena_original_pieces[] = $comprobante_data[0]['condicionesDePago'];
						$cadena_original_pieces[] = $comprobante_data[0]['subTotal'];

						if (isset($comprobante_data[0]['descuento']))
						{
							$cadena_original_pieces[] = $comprobante_data[0]['descuento'];
						}

						if (isset($comprobante_data[0]['TipoCambio']))
						{
							$cadena_original_pieces[] = $comprobante_data[0]['TipoCambio'];
						}

						$cadena_original_pieces[] = $comprobante_data[0]['Moneda'];
						$cadena_original_pieces[] = $comprobante_data[0]['total'];
						$cadena_original_pieces[] = $comprobante_data[0]['metodoDePago'];
						$cadena_original_pieces[] = $comprobante_data[0]['LugarExpedicion'];

						if (isset($comprobante_data[0]['NumCtaPago']))
						{
							$cadena_original_pieces[] = $comprobante_data[0]['NumCtaPago'];
						}

						$cadena_original_pieces[] = $emisor_data[0]['rfc'];
						$cadena_original_pieces[] = $emisor_data[0]['nombre'];

						if (isset($emisor_domicilio_data[0]['calle']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['calle'];
						}

						if (isset($emisor_domicilio_data[0]['noExterior']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['noExterior'];
						}

						if (isset($emisor_domicilio_data[0]['noInterior']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['noInterior'];
						}

						if (isset($emisor_domicilio_data[0]['colonia']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['colonia'];
						}

						if (isset($emisor_domicilio_data[0]['localidad']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['localidad'];
						}

						if (isset($emisor_domicilio_data[0]['referencia']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['referencia'];
						}

						if (isset($emisor_domicilio_data[0]['municipio']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['municipio'];
						}

						if (isset($emisor_domicilio_data[0]['estado']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['estado'];
						}

						if (isset($emisor_domicilio_data[0]['pais']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['pais'];
						}

						if (isset($emisor_domicilio_data[0]['codigoPostal']))
						{
							$cadena_original_pieces[] = $emisor_domicilio_data[0]['codigoPostal'];
						}

						$cadena_original_pieces[] = $emisor_regimen_data[0]['Regimen'];

						$cadena_original_pieces[] = $receptor_data[0]['rfc'];
						$cadena_original_pieces[] = $receptor_data[0]['nombre'];

						if (isset($domicilio_data[0]['calle']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['calle'];
						}

						if (isset($domicilio_data[0]['noExterior']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['noExterior'];
						}

						if (isset($domicilio_data[0]['noInterior']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['noInterior'];
						}

						if (isset($domicilio_data[0]['colonia']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['colonia'];
						}

						if (isset($domicilio_data[0]['localidad']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['localidad'];
						}

						if (isset($domicilio_data[0]['referencia']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['referencia'];
						}

						if (isset($domicilio_data[0]['municipio']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['municipio'];
						}

						if (isset($domicilio_data[0]['estado']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['estado'];
						}

						if (isset($domicilio_data[0]['pais']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['pais'];
						}

						if (isset($domicilio_data[0]['codigoPostal']))
						{
							$cadena_original_pieces[] = $domicilio_data[0]['codigoPostal'];
						}

						foreach ($conceptos_data as $concepto)
						{
							$cadena_original_pieces[] = $concepto['cantidad'];
							$cadena_original_pieces[] = $concepto['unidad'];

							if (isset($concepto['noIdentificacion']))
							{
								$cadena_original_pieces[] = $concepto['noIdentificacion'];
							}

							$cadena_original_pieces[] = $concepto['descripcion'];
							$cadena_original_pieces[] = $concepto['valorUnitario'];
							$cadena_original_pieces[] = $concepto['importe'];

							if (isset($cuenta_predial_data[0]['numero']) && strpos($concepto['descripcion'], 'RENTA'))
							{
								$cadena_original_pieces[] = (string) $cuenta_predial_data[0]['numero'];

								$cuenta_predial = (string) $cuenta_predial_data[0]['numero'];
							}
						}

						if ($retenciones_data)
						{
							foreach ($retenciones_data as $retencion)
							{
								$cadena_original_pieces[] = $retencion['impuesto'];
								$cadena_original_pieces[] = $retencion['importe'];
							}

							$cadena_original_pieces[] = $impuesto_data[0]['totalImpuestosRetenidos'];
						}

						if ($impuestos_data)
						{
							foreach ($impuestos_data as $impuesto)
							{
								$cadena_original_pieces[] = $impuesto['impuesto'];
								$cadena_original_pieces[] = $impuesto['tasa'];
								$cadena_original_pieces[] = $impuesto['importe'];
							}

							$cadena_original_pieces[] = $impuesto_data[0]['totalImpuestosTrasladados'];
						}

						$data = array(
							'tipo'		=> 'cfdi',
							'timbre'	=> array(
								'fecha_timbrado'			=> (string) $timbre_data[0]['FechaTimbrado'],
								'uuid'						=> (string) $timbre_data[0]['UUID'],
								'no_certificado_digital'	=> (string) $comprobante_data[0]['noCertificado'],
								'no_certificado_sat'		=> (string) $timbre_data[0]['noCertificadoSAT'],
								'sello_cfd'					=> (string) $timbre_data[0]['selloCFD'],
								'sello_sat'					=> (string) $timbre_data[0]['selloSAT'],
								'cadena_original'			=> '||' . implode('|', $cadena_original_pieces) . '||'
							),
							'emisor'	=> array(
								'num_cia'			=> NULL,
								'nombre_cia'		=> NULL,
								'rfc'				=> mb_strtoupper((string) $emisor_data[0]['rfc']),
								'folio'				=> intval((string) $comprobante_data[0]['folio']),
								'serie'				=> isset($comprobante_data[0]['serie']) ? mb_strtoupper((string) $comprobante_data[0]['serie']) : '',
								'fecha'				=> $fecha,
								'hora'				=> $hora,
								'subtotal'			=> floatval((string) $comprobante_data[0]['subTotal']),
								'iva'				=> 0,
								'ieps'				=> 0,
								'retencion_iva'		=> 0,
								'retencion_isr'		=> 0,
								'total'				=> floatval((string) $comprobante_data[0]['total']),
								'condiciones_pago'	=> (string) $comprobante_data[0]['condicionesDePago'],
								'metodo_pago'		=> (string) $comprobante_data[0]['metodoDePago'],
								'cuenta_predial'	=> isset($cuenta_predial) ? $cuenta_predial : ''
							),
							'cliente'	=> array(
								'nombre'		=> (string) $receptor_data[0]['nombre'],
								'rfc'			=> (string) $receptor_data[0]['rfc'],
								'calle'			=> isset($domicilio_data[0]['calle']) ? mb_strtoupper($domicilio_data[0]['calle']) : '',
								'no_exterior'	=> isset($domicilio_data[0]['noExterior']) ? mb_strtoupper($domicilio_data[0]['noExterior']) : '',
								'no_interior'	=> isset($domicilio_data[0]['noInterior']) ? mb_strtoupper($domicilio_data[0]['noInterior']) : '',
								'colonia'		=> isset($domicilio_data[0]['colonia']) ? mb_strtoupper($domicilio_data[0]['colonia']) : '',
								'municipio'		=> isset($domicilio_data[0]['municipio']) ? mb_strtoupper($domicilio_data[0]['municipio']) : '',
								'estado'		=> isset($domicilio_data[0]['estado']) ? mb_strtoupper($domicilio_data[0]['estado']) : '',
								'pais'			=> isset($domicilio_data[0]['pais']) ? mb_strtoupper($domicilio_data[0]['pais']) : '',
								'codigo_postal'	=> isset($domicilio_data[0]['codigoPostal']) ? mb_strtoupper($domicilio_data[0]['codigoPostal']) : ''
							),
							'conceptos'	=> array(),
							'impuestos'	=> array()
						);

						foreach ($conceptos_data as $concepto)
						{
							$data['conceptos'][] = array(
								'cantidad'		=> floatval((string) $concepto['cantidad']),
								'descripcion'	=> mb_strtoupper((string) $concepto['descripcion']),
								'unidad'		=> mb_strtoupper((string) $concepto['unidad']),
								'precio'		=> floatval((string) $concepto['valorUnitario']),
								'importe'		=> floatval((string) $concepto['importe'])
							);
						}

						foreach ($impuestos_data as $impuesto)
						{
							$data['impuestos'][] = array(
								'impuesto'	=> mb_strtoupper((string) $impuesto['impuesto']),
								'tasa'		=> floatval((string) $impuesto['tasa']),
								'importe'	=> floatval((string) $impuesto['importe'])
							);

							if (mb_strtoupper((string) $impuesto['impuesto']) == 'IVA')
							{
								$data['emisor']['iva'] = floatval((string) $impuesto['importe']);
							}
							if (mb_strtoupper((string) $impuesto['impuesto']) == 'IEPS')
							{
								$data['emisor']['ieps'] = floatval((string) $impuesto['importe']);
							}
						}

						foreach ($retenciones_data as $retencion)
						{
							$data['retenciones'][] = array(
								'impuesto'	=> mb_strtoupper((string) $impuesto['impuesto']),
								'importe'	=> floatval((string) $impuesto['importe'])
							);

							if (mb_strtoupper((string) $retencion['impuesto']) == 'IVA')
							{
								$data['emisor']['retencion_iva'] = floatval((string) $retencion['importe']);
							}
							if (mb_strtoupper((string) $retencion['impuesto']) == 'ISR')
							{
								$data['emisor']['retencion_isr'] = floatval((string) $retencion['importe']);
							}
						}
					}
				}
				// El archivo XML no es un documento CFD/CFDI válido
				else
				{
					header('Content-Type: application/json');
					echo json_encode(array(
						'status'	=> -3,
						'error'		=> 'El archivo XML no corresponde a un documento CFD/CFDI válido'
					));

					die;
				}

				// Validar que el arreglo contenga información
				if ( ! $data)
				{
					header('Content-Type: application/json');
					echo json_encode(array(
						'status'	=> -4,
						'error'		=> 'El archivo XML no corresponde a un documento CFD/CFDI válido'
					));
				}
				else {
					// Buscar el emisor en la base de datos a partir del R.F.C. y la serie del comprobante
					$sql = "
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia
						FROM
							catalogo_companias cc
							LEFT JOIN facturas_electronicas_series fes
								USING (num_cia)
						WHERE
							UPPER(rfc) = UPPER('" . utf8_decode($data['emisor']['rfc']) . "')
							AND COALESCE(UPPER(serie), '') = UPPER('" . utf8_decode($data['emisor']['serie']) . "')
							AND {$data['emisor']['folio']} BETWEEN folio_inicial AND folio_final
					";

					$result = $db->query($sql);

					if ($result)
					{
						$data['emisor']['num_cia'] = $result[0]['num_cia'];
						$data['emisor']['nombre_cia'] = utf8_encode($result[0]['nombre_cia']);

						if ($db->query("
							SELECT
								id
							FROM
								facturas_electronicas
							WHERE
								num_cia = {$data['emisor']['num_cia']}
								AND tipo_serie = 1
								AND consecutivo = {$data['emisor']['folio']}
						"))
						{
							header('Content-Type: application/json');
							echo json_encode(array(
								'status'	=> -6,
								'error'		=> "El comprobante ya existe en el sistema"
							));
						}
						else
						{
							header('Content-Type: application/json');
							echo json_encode(array_merge(array(
								'status'	=> 1
							), $data));
						}
					}
					else
					{
						header('Content-Type: application/json');
						echo json_encode(array(
							'status'	=> -5,
							'error'		=> "El folio no esta dentro de ninguna serie en el catálogo"
						));
					}
				}
			}

			break;

		case 'guardar_archivos':
			$allowed_xml_formats = array(
				'application/xml',
				'text/xml'
			);

			$allowed_pdf_formats = array(
				'application/pdf'
			);

			// Validar que el archivo se haya cargado sin errores
			if ($_FILES['xml_file']['error'] > 0)
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -1,
					'error'		=> 'Error en la carga del archivo XML al servidor'
				));

				die;
			}
			// Validar que el tipo de archivo sea válido
			else if ( ! in_array($_FILES['xml_file']['type'], $allowed_xml_formats))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -2,
					'error'		=> $_FILES['xml_file']['name'] . ' no es un archivo soportado',
					'type'		=> $_FILES['xml_file']['type']
				));

				die;
			}

			// Validar que el archivo se haya cargado sin errores
			if ($_REQUEST['tipo_pdf'] == 'cargar' && $_FILES['pdf_file']['error'] > 0)
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -3,
					'error'		=> 'Error en la carga del archivo PDF al servidor'
				));

				die;
			}
			// Validar que el tipo de archivo sea válido
			else if ($_REQUEST['tipo_pdf'] == 'cargar' && ! in_array($_FILES['pdf_file']['type'], $allowed_pdf_formats))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -4,
					'error'		=> $_FILES['pdf_file']['name'] . ' no es un archivo soportado',
					'type'		=> $_FILES['pdf_file']['type']
				));

				die;
			}

			$file_name = $_REQUEST['num_cia'] . '-' . $_REQUEST['serie'] . $_REQUEST['folio'];

			if ($_REQUEST['tipo_pdf'] == 'generar')
			{
				$data = json_decode($_REQUEST['json_string']);

				$query = $db->query("SELECT
					fes.serie,
					fes.tipo_factura,
					fes.folio_inicial,
					fes.folio_final,
					fes.ultimo_folio_usado + 1 AS folio,
					cc.nombre AS nombre_cia,
					cc.email,
					con.email AS email_contador,
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
					ccm.calle AS calle_matriz,
					ccm.no_exterior AS no_exterior_matriz,
					ccm.no_interior AS no_interior_matriz,
					ccm.colonia AS colonia_matriz,
					ccm.municipio AS municipio_matriz,
					ccm.estado AS estado_matriz,
					ccm.pais AS pais_matriz,
					ccm.codigo_postal AS codigo_postal_matriz,
					fes.tipo_cfd,
					cl.nombre_imagen AS logo
				FROM
					facturas_electronicas_series fes
					LEFT JOIN catalogo_companias cc USING (num_cia)
					LEFT JOIN catalogo_contadores con USING (idcontador)
					LEFT JOIN catalogo_companias ccm ON (ccm.num_cia = cc.cia_fiscal_matriz)
					LEFT JOIN catalogo_logos_cfd cl ON (cl.id = cc.logo_cfd)
				WHERE
					fes.num_cia = {$_REQUEST['num_cia']}
					AND fes.tipo_serie = " . ($_REQUEST['tipo'] == 5 ? 2 : 1) . "
					AND {$_REQUEST['folio']} BETWEEN folio_inicial AND folio_final");

				$datos_emisor = $query[0];

				foreach ($datos_emisor as $k => $v)
				{
					$datos_emisor[$k] = utf8_encode($v);
				}

				$datos = array(
					'cabecera' => array (
						'num_cia'				=> $data->num_cia,
						'fecha'					=> $data->fecha,
						'hora'					=> $data->hora,
						// 'clave_cliente'			=> $data->clave_cliente,
						'nombre_cliente'		=> utf8_decode($data->nombre_cliente),
						'rfc_cliente'			=> utf8_decode($data->rfc),
						'calle'					=> utf8_decode($data->calle),
						'no_exterior'			=> utf8_decode($data->no_exterior),
						'no_interior'			=> utf8_decode($data->no_interior),
						'colonia'				=> utf8_decode($data->colonia),
						'localidad'				=> utf8_decode($data->localidad),
						'referencia'			=> utf8_decode($data->referencia),
						'municipio'				=> utf8_decode($data->municipio),
						'estado'				=> utf8_decode($data->estado),
						'pais'					=> utf8_decode($data->pais),
						'codigo_postal'			=> $data->codigo_postal,
						'email'					=> utf8_decode($data->email_cliente),
						'observaciones'			=> utf8_decode($data->observaciones),
						'importe'				=> get_val($data->subtotal),
						'porcentaje_descuento'	=> 0,
						'descuento'				=> 0,
						'ieps'					=> get_val($data->ieps),
						'porcentaje_iva'		=> get_val($data->iva) > 0 ? 16 : 0,
						'importe_iva'			=> get_val($data->iva),
						'importe_retencion_isr'	=> get_val($data->retencion_isr),
						'importe_retencion_iva'	=> get_val($data->retencion_iva),
						'total'					=> get_val($data->total),
						'tipo_pago'				=> $data->tipo_pago,
						'cuenta_pago'			=> $data->cuenta_pago,
						'condiciones_pago'		=> $data->condiciones_pago,
						'cuenta_predial'        => $data->cuenta_predial
					),
					'detalle' => array()
				);

				foreach ($data->importe as $i => $importe)
				{
					if (get_val($importe) > 0 || get_val($data->cantidad[$i]) > 0)
					{
						$datos['detalle'][$i]['clave'] = $i + 1;
						$datos['detalle'][$i]['descripcion'] = utf8_decode($data->descripcion[$i]);
						$datos['detalle'][$i]['cantidad'] = get_val($data->cantidad[$i]);
						$datos['detalle'][$i]['unidad'] = utf8_decode($data->unidad[$i]);
						$datos['detalle'][$i]['precio'] = get_val($data->precio[$i]);
						$datos['detalle'][$i]['importe'] = get_val($importe);
						$datos['detalle'][$i]['descuento'] = 0;
						$datos['detalle'][$i]['porcentaje_ieps'] = isset($data->{'aplicar_ieps' . $i}) ? 8 : 0;
						$datos['detalle'][$i]['importe_ieps'] = isset($data->{'aplicar_ieps' . $i}) ? get_val($importe) * 0.08 : 0;
						$datos['detalle'][$i]['porcentaje_iva'] = isset($data->{'aplicar_iva' . $i}) ? 16 : 0;
						$datos['detalle'][$i]['importe_iva'] = isset($data->{'aplicar_iva' . $i}) ? get_val($importe) * 0.16 : 0;
					}
				}
			}

			if ( ! @move_uploaded_file($_FILES['xml_file']['tmp_name'], 'facturas/comprobantes_xml/' . $_REQUEST['num_cia'] . '/' . $file_name . '.xml')
				|| ! (
					$_REQUEST['tipo_pdf'] == 'cargar' ? @move_uploaded_file($_FILES['pdf_file']['tmp_name'], 'facturas/comprobantes_pdf/' . $_REQUEST['num_cia'] . '/' . $file_name . '.pdf')
					: @generar_cfdi(
						$data->num_cia,
						$data->tipo == 5 ? 2 : 1,
						$data->folio,
						$data->fecha_timbrado,
						$data->uuid,
						$data->no_certificado_digital,
						$data->no_certificado_sat,
						$data->sello_cfd,
						$data->sello_sat,
						$data->cadena_original,
						NULL,
						$datos_emisor,
						$datos
					)
				))
			{

				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -5,
					'error'		=> 'Error al guardar los archivos en el servidor'
				));

				die;
			}

			header('Content-Type: application/json');
			echo json_encode(array(
				'status'	=> 1
			));

			break;

		case 'registrar':
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
							fecha_timbrado,
							uuid,
							no_certificado_digital,
							no_certificado_sat,
							sello_cfd,
							sello_sat,
							cadena_original,
							documento_xml
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							\'' . $_REQUEST['fecha'] . '\',
							\'' . $_REQUEST['hora'] . '\',
							' . ($_REQUEST['tipo'] == 5 ? 2 : 1) . ',
							' . $_REQUEST['folio'] . ',
							' . $_REQUEST['tipo'] . ',
							' . ($_REQUEST['rfc'] == 'XAXX010101000' ? 1 : 1000) . ',
							\'' . utf8_decode($_REQUEST['nombre_cliente']) . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							\'' . utf8_decode($_REQUEST['calle']) . '\',
							\'' . utf8_decode($_REQUEST['no_exterior']) . '\',
							\'' . utf8_decode($_REQUEST['no_interior']) . '\',
							\'' . utf8_decode($_REQUEST['colonia']) . '\',
							\'' . utf8_decode($_REQUEST['localidad']) . '\',
							\'' . utf8_decode($_REQUEST['referencia']) . '\',
							\'' . utf8_decode($_REQUEST['municipio']) . '\',
							\'' . utf8_decode($_REQUEST['estado']) . '\',
							\'' . utf8_decode($_REQUEST['pais']) . '\',
							\'' . utf8_decode($_REQUEST['codigo_postal']) . '\',
							\'' . utf8_decode($_REQUEST['email_cliente']) . '\',
							\'' . utf8_decode($_REQUEST['observaciones']) . '\',
							' . get_val($_REQUEST['subtotal']) . ',
							' . 0 .',
							' . 0 . ',
							' . get_val($_REQUEST['ieps']) . ',
							' . get_val($_REQUEST['iva']) . ',
							' . get_val($_REQUEST['retencion_iva']) . ',
							' . get_val($_REQUEST['retencion_isr']) . ',
							' . get_val($_REQUEST['total']) . ',
							' . $_SESSION['iduser'] . ',
							\'' . $_REQUEST['fecha'] . '\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'\',
							\'' . utf8_decode($_REQUEST['tipo_pago']) . '\',
							\'' . utf8_decode($_REQUEST['cuenta_pago']) . '\',
							\'' . utf8_decode($_REQUEST['condiciones_pago']) . '\',
							\'' . utf8_decode($_REQUEST['fecha_timbrado']) . '\',
							\'' . utf8_decode($_REQUEST['uuid']) . '\',
							\'' . utf8_decode($_REQUEST['no_certificado_digital']) . '\',
							\'' . utf8_decode($_REQUEST['no_certificado_sat']) . '\',
							\'' . utf8_decode($_REQUEST['sello_cfd']) . '\',
							\'' . utf8_decode($_REQUEST['sello_sat']) . '\',
							\'' . utf8_decode($_REQUEST['cadena_original']) . '\',
							\'' . utf8_decode(file_get_contents('facturas/comprobantes_xml/' . $_REQUEST['num_cia'] . '/' . $_REQUEST['num_cia'] . '-' . $_REQUEST['serie'] . $_REQUEST['folio'] . '.xml')) . '\'
						)
			' . ";\n";

			foreach ($_REQUEST['importe'] as $i => $importe)
			{
				if (get_val($importe) > 0 || get_val($_REQUEST['cantidad'][$i]) > 0)
				{
					$sql .= 'INSERT INTO facturas_electronicas_detalle (
						num_cia,
						tipo_serie,
						consecutivo,
						clave_producto,
						cantidad,
						descripcion,
						precio,
						unidad,
						importe,
						iva,
						piva,
						ieps,
						pieps,
						numero_pedimento,
						fecha_entrada,
						aduana_entrada
					)
					VALUES (
						' . $_REQUEST['num_cia'] . ',
						' . 1 . ',
						' . $_REQUEST['folio'] . ',
						' . ($i + 1) . ',
						' . get_val($_REQUEST['cantidad'][$i]) . ',
						\'' . utf8_decode($_REQUEST['descripcion'][$i]) . '\',
						' . get_val($_REQUEST['precio'][$i]) . ',
						\'' . utf8_decode($_REQUEST['unidad'][$i]) . '\',
						' . get_val($importe) . ',
						' . (isset($_REQUEST['aplicar_iva' . $i]) ? round(get_val($importe) * 0.16, 2) : 0) . ',
						' . (isset($_REQUEST['aplicar_iva' . $i]) ? 16 : 0) . ',
						' . (isset($_REQUEST['aplicar_ieps' . $i]) ? round(get_val($importe) * 0.08, 2) : 0) . ',
						' . (isset($_REQUEST['aplicar_ieps' . $i]) ? 8 : 0) . ',
						NULL,
						NULL,
						NULL
					)' . ";\n";

					if ($_REQUEST['tipo'] == 5)
					{
						if (strpos($_REQUEST['descripcion'][$i], 'RENTA') !== FALSE)
						{
							$concepto_renta = $_REQUEST['descripcion'][$i];
							$importe_renta = get_val($importe);
						}
						if (strpos($_REQUEST['descripcion'][$i], 'MANTENIMIENTO') !== FALSE)
						{
							$concepto_mantenimiento = $_REQUEST['descripcion'][$i];
							$importe_mantenimiento = get_val($importe);
						}
						if (strpos($_REQUEST['descripcion'][$i], 'AGUA') !== FALSE)
						{
							$concepto_agua = $_REQUEST['descripcion'][$i];
							$importe_agua = get_val($importe);
						}
					}
				}
			}

			$sql .= "UPDATE facturas_electronicas_series
				SET ultimo_folio_usado = {$_REQUEST['folio']}
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND tipo_serie = " . ($_REQUEST['tipo'] == 5 ? 2 : 1) . "
					AND {$_REQUEST['folio']} BETWEEN folio_inicial AND folio_final
					AND ultimo_folio_usado < {$_REQUEST['folio']};";

			$db->query($sql);

			if ($_REQUEST['tipo'] == 5)
			{
				$fecha_renta = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes_renta'], 1, $_REQUEST['anio_renta']));

				$sql = "INSERT INTO rentas_recibos (
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
				VALUES (
					{$_REQUEST['arrendatario']},
					(
						SELECT
							id
						FROM
							facturas_electronicas
						WHERE
							num_cia = {$_REQUEST['num_cia']}
							AND consecutivo = {$_REQUEST['folio']}
							AND tipo_serie = 2
							AND status = 1
					),
					1,
					'{$fecha_renta}',
					'" . (isset($concepto_renta) ? $concepto_renta : '') . "',
					'" . (isset($concepto_mantenimiento) ? $concepto_mantenimiento : '') . "',
					" . (isset($importe_renta) ? $importe_renta : 0) . ",
					" . (isset($importe_mantenimiento) ? $importe_mantenimiento : 0) . ",
					" . get_val($_REQUEST['subtotal']) . ",
					" . get_val($_REQUEST['iva']) . ",
					" . (isset($importe_agua) ? get_val($importe_agua) : 0) . ",
					" . get_val($_REQUEST['retencion_iva']) . ",
					" . get_val($_REQUEST['retencion_isr']) . ",
					" . get_val($_REQUEST['total']) . ",
					2,
					{$_SESSION['iduser']},
					(
						SELECT
							tsins
						FROM
							facturas_electronicas
						WHERE
							num_cia = {$_REQUEST['num_cia']}
							AND consecutivo = {$_REQUEST['folio']}
							AND tipo_serie = 2
					)
				);\n";

				$sql .= "UPDATE facturas_electronicas
				SET idlocal = {$_REQUEST['arrendatario']}
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND consecutivo = {$_REQUEST['folio']}
					AND tipo_serie = 2
					AND status = 1;\n";

				$db->query($sql);
			}

			header('Content-Type: application/json');
			echo json_encode(array(
				'status'	=> 1
			));

			break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaManualSoloRegistrar.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
