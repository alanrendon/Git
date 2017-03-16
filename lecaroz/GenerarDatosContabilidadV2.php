<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$options = getopt("", array(
	'ids:',
	'fecha1:',
	'fecha2:',
	'cias:',
	'pros:',
	'nvcf',
	'no_cargar',
	'guardar_xml',
	'no_msg',
	'help',
	'max_ob_err:',
	'nderr'
));

if (isset($_REQUEST['help']) || isset($options['help']))
{
	echo "GenerarDatosContabilidadV2.php Ver 2.15.9.8";
	echo "\nCopyright (c) 2015, Lecaroz";
	echo "\n\nModo de empleo: php GenerarDatosContabilidadV2.php [OPCIONES]";
	echo "\nGenera los datos de contabilidad para pagos de compras y gastos.";
	echo "\n\nLos argumentos obligatorios para las opciones largas son también obligatorios\npara las opciones cortas.";
	echo "\n\n  --help\t\tmuestra esta ayuda y finaliza";
	echo "\n\n  --ids=IDS\t\tidentificadores, separados por comas (1,2,3,...) o\n\t\t\trangos (1-5,20-26,...) de los registros";
	echo "\n\n  --fecha1=FECHA1\tfecha de inicio de búsqueda de registros";
	echo "\n\n  --fecha2=FECHA2\tfecha de término de búsqueda de registros";
	echo "\n\n  --cias=CIAS\t\tcompañías, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros";
	echo "\n\n  --pros=PROS\t\tproveedores, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros";
	echo "\n\n  --nvcf\t\tno validar si las facturas ya han sido registradas";
	echo "\n\n  --no_cargar\t\tno cargar registros a openbravo";
	echo "\n\n  --guardar_xml\t\tguardar archivos xml de carga en directorio 'tmp/'";
	echo "\n\n  --no_msg\t\tno generar mensajes informativos";
	echo "\n\n  --max_ob_err\t\tmáximo número de errores de openbravo antes de detener\n\t\t\tscript";
	echo "\n\n  --nderr\t\tno detener script con errores de openbravo";
	echo "\n\nComunicar de errores en el script a carlos.candelario@lecaroz.com";
	echo "\n\n";

	die;
}

$nvcf = isset($_REQUEST['nvcf']) || isset($options['nvcf']) ? TRUE : FALSE;
$no_cargar = isset($_REQUEST['no_cargar']) || isset($options['no_cargar']) ? TRUE : FALSE;
$guardar_xml = isset($_REQUEST['guardar_xml']) || isset($options['guardar_xml']) ? TRUE : FALSE;
$no_msg = isset($_REQUEST['no_msg']) || isset($options['no_msg']) ? TRUE : FALSE;
$nderr = isset($_REQUEST['nderr']) || isset($options['nderr']) ? TRUE : FALSE;

$max_ob_err = isset($_REQUEST['max_ob_err']) || isset($options['max_ob_err']) ? (isset($_REQUEST['max_ob_err']) ? intval($_REQUEST['max_ob_err']) : intval($options['max_ob_err'])) : 1;

echo "\n(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Parámetros de búsqueda";

if (isset($options['ids']) || isset($_REQUEST['ids']))
{
	$params['ids'] = isset($options['ids']) ? $options['ids'] : $_REQUEST['ids'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) ids={$params['ids']}";
}

if (isset($options['fecha1']) || isset($_REQUEST['fecha1']))
{
	$params['fecha1'] = isset($options['fecha1']) ? $options['fecha1'] : $_REQUEST['fecha1'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) fecha1={$params['fecha1']}";
}

if (isset($options['fecha2']) || isset($_REQUEST['fecha2']))
{
	$params['fecha2'] = isset($options['fecha2']) ? $options['fecha2'] : $_REQUEST['fecha2'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) fecha2={$params['fecha2']}";
}

if (isset($options['cias']) || isset($_REQUEST['cias']))
{
	$params['cias'] = isset($options['cias']) ? $options['cias'] : $_REQUEST['cias'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) cias={$params['cias']}";
}

if (isset($options['pros']) || isset($_REQUEST['pros']))
{
	$params['pros'] = isset($options['pros']) ? $options['pros'] : $_REQUEST['pros'];

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) pros={$params['pros']}";
}

$condiciones = array();

if (isset($params['ids']) && trim($params['ids']) != '')
{
	$ids = array();

	$pieces = explode(',', $params['ids']);
	foreach ($pieces as $piece)
	{
		if (count($exp = explode('-', $piece)) > 1)
		{
			$ids[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$ids[] = $piece;
		}
	}

	if (count($ids) > 0)
	{
		$condiciones[] = 'f.id IN (' . implode(', ', $ids) . ')';
	}
}

if (isset($params['fecha1']) || isset($params['fecha2']))
{
	$condiciones[] = "f.fecha BETWEEN '{$params['fecha1']}'::DATE AND '{$params['fecha2']}'::DATE";
}
else if ( ! isset($ids))
{
	$condiciones[] = "f.fecha >= NOW()::DATE - INTERVAL '3 MONTHS'";
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

if (isset($params['pros']) && trim($params['pros']) != '')
{
	$pros = array();

	$pieces = explode(',', $options['pros']);
	foreach ($pieces as $piece)
	{
		if (count($exp = explode('-', $piece)) > 1)
		{
			$pros[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$pros[] = $piece;
		}
	}

	if (count($pros) > 0)
	{
		$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
	}
}

if ( ! $condiciones)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No hay parámetros de búsqueda\n";

	die;
}

$db = new DBclass($dsn, 'autocommit=yes');

if ( ! $nvcf)
{
	$condiciones[] = 'f.ts_carga_conta IS NULL';
}

$condiciones_string = implode(' AND ', $condiciones);

$sql = "SELECT
	COUNT(f.id) AS num_rows
FROM
	facturas f
WHERE
	{$condiciones_string}";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Ejecutando consulta a la base de datos";

$result = $db->query($sql);

if ($result[0]['num_rows'] == 0)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) No hay resultados, terminando ejecución de script.";

	die;
}

$tipo_pago = array(
	'4'	=> 'No Identificado',
	'B'	=> 'Efectivo',
	'2'	=> 'Cheque',
	'1'	=> 'Transferencia Electronica',
	'K'	=> 'Tarjeta de Credito',
	'N'	=> 'No Identificado'
);

$condiciones_pago = array(
	0	=> 'No Identificado',
	1	=> 'Contado',
	2	=> 'Credito'
);

$num_rows = $result[0]['num_rows'];

$ob_err = 0;

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Pagos a insertar: {$num_rows}";

$rows_per_query = 500;

$offset = 0;

$cont = 1;

$ids = array();

$query_time = -microtime(TRUE);

while ($datos_facturas = $db->query("SELECT
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
	{$condiciones_string}
ORDER BY
	f.id
LIMIT {$rows_per_query} OFFSET {$offset}"))
{
	$query_time += microtime();

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de consulta: {$query_time} segundos";

	$id_pago = NULL;

	foreach ($datos_facturas as $i => $df)
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

		$xml_carga = generar_xml_datos($datos, $guardar_xml);

		if ($no_cargar)
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se cargo el documento al sistema";

			continue;
		}

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo webservice";

		$response = consumir_webservice($xml_carga);

		$ob_data = json_decode(utf8_encode($response));

		if ($response === FALSE)
		{
			$db->query("UPDATE facturas SET ts_carga_conta_error = 'Webservice no disponible', ts_carga_conta = NULL WHERE id = {$df['id']}");

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Webservice no disponible";

			$ob_err++;

			if ( ! $nderr && $ob_err >= $max_ob_err)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

				if ($ids)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registro(s)";

					$db->query("UPDATE facturas SET ts_carga_conta = NOW(), ts_carga_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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
				$db->query("UPDATE facturas SET ts_carga_conta_error = 'Imposible decodificar respuesta de openbravo: {$response}', ts_carga_conta = NULL WHERE id = {$df['id']}");

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Imposible decodificar respuesta de openbravo: {$response}";

				$ob_err++;

				if ( ! $nderr && $ob_err >= $max_ob_err)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

					if ($ids)
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registros";

						$db->query("UPDATE facturas SET ts_carga_conta = NOW(), ts_carga_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
					}

					echo "\n";

					die();
				}
			}
			else if ($ob_data->status < 0)
			{
				$msg = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : (isset($ob_data->mensaje) ? $ob_data->mensaje : print_r($response, TRUE)))));

				$db->query("UPDATE facturas SET ts_carga_conta_error = '{$msg}', ts_carga_conta = NULL WHERE id = {$df['id']}");

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Respuesta de openbravo: {$msg}";
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de documento a openbravo exitosa";

				$ids[] = $df['id'];
			}
		}

		$cont++;
	}

	$offset += $rows_per_query;

	$query_time = -microtime(TRUE);
}

if ($ids)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registros\n";

	$db->query("UPDATE facturas SET ts_carga_conta = NOW(), ts_carga_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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

	$xml_documentNo = $xml->createElement('documentNo', $datos['cabecera']['id']);
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

	// Guardar XML de carga

	$subdir = date('Y-m-d');

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
	// $ob_response = @file_get_contents('http://192.168.1.251:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecInvoice', NULL, $context);
	$ob_response = @file_get_contents('http://192.168.1.3:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecInvoice', NULL, $context);

	return $ob_response;
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
}
