<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
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
				
				// Convertir objeto en arreglo
				$xml_data = get_object_vars($xml);

				// Obtener la versión del CFD
				$version  = (string) $xml_data['@attributes']['version'];

				$data = FALSE;

				// Versión del comprobante 2.2 (CFD)
				if ($version == '2.2')
				{
					$emisor_data = get_object_vars($xml->Emisor);

					if (isset($emisor_data['@attributes']['rfc']) && $emisor_data['@attributes']['rfc'] != '')
					{
						$data = array(
							'rfc'		=> $emisor_data['@attributes']['rfc'],
							'folio'		=> $xml_data['@attributes']['folio'],
							'serie'		=> isset($xml_data['@attributes']['serie']) ? mb_strtoupper($xml_data['@attributes']['serie']) : '',
							'tipo'		=> 'cfd',
							'id'		=> NULL,
							'xml_file'	=> NULL,
							'pdf_file'	=> NULL
						);
					}
				}
				// Versión del comprobante 3.2 (CFDI)
				else if ($version == '3.2')
				{
					$comprobante_data = $xml->xpath('//cfdi:Comprobante');

					if ($comprobante_data)
					{
						$emisor_data = $xml->xpath('//cfdi:Comprobante/cfdi:Emisor');

						$data = array(
							'rfc'		=> mb_strtoupper((string) $emisor_data[0]['rfc']),
							'folio'		=> (string) $comprobante_data[0]['folio'],
							'serie'		=> isset($comprobante_data[0]['serie']) ? mb_strtoupper((string) $comprobante_data[0]['serie']) : '',
							'tipo'		=> 'cfdi',
							'id'		=> NULL,
							'xml_file'	=> NULL,
							'pdf_file'	=> NULL
						);
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
					// Buscar la factura en la base de datos a partir del R.F.C. y el número de factura del proveedor
					$sql = "
						SELECT
							id,
							xml_file,
							pdf_file
						FROM
							facturas_zap
							LEFT JOIN catalogo_proveedores
								USING (num_proveedor)
						WHERE
							UPPER(rfc) = '{$data['rfc']}'
							AND UPPER(num_fact) = '{$data['serie']}{$data['folio']}'
					";

					$result = $db->query($sql);

					// Factura encontrada
					if ($result)
					{
						$data['id'] = $result[0]['id'];
						$data['xml_file'] = $result[0]['xml_file'];
						$data['pdf_file'] = $result[0]['pdf_file'];

						header('Content-Type: application/json');
						echo json_encode(array_merge(array(
							'status'	=> 1
						), $data));
					}
					// La factura no esta en la base de datos
					else
					{
						header('Content-Type: application/json');
						echo json_encode(array(
							'status'	=> -5,
							'error'		=> "El documento {$data['serie']}{$data['folio']} del proveedor con R.F.C. {$data['rfc']} no esta en el sistema"
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
			if ($_FILES['pdf_file']['error'] > 0)
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -3,
					'error'		=> 'Error en la carga del archivo PDF al servidor'
				));

				die;
			}
			// Validar que el tipo de archivo sea válido
			else if ( ! in_array($_FILES['pdf_file']['type'], $allowed_pdf_formats))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -4,
					'error'		=> $_FILES['pdf_file']['name'] . ' no es un archivo soportado',
					'type'		=> $_FILES['pdf_file']['type']
				));

				die;
			}

			$data = json_decode($_REQUEST['datos']);

			$file_name = 'cfd_zap_' . $data->id;

			if ( ! @move_uploaded_file($_FILES['xml_file']['tmp_name'], 'cfds_proveedores/' . $file_name . '.xml')
				|| ! @move_uploaded_file($_FILES['pdf_file']['tmp_name'], 'cfds_proveedores/' . $file_name . '.pdf'))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -5,
					'error'		=> 'Error al guardar los archivos en el servidor'
				));

				die;
			}

			$sql = "
				UPDATE
					facturas_zap
				SET
					xml_file = '{$file_name}.xml',
					pdf_file = '{$file_name}.pdf',
					tipo_cfd = '{$data->tipo}'
				WHERE
					id = {$data->id}
			";

			$db->query($sql);

			header('Content-Type: application/json');
			echo json_encode(array(
				'status'	=> 1
			));
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/GuardarCFDZapaterias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
