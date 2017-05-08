<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$options = getopt("", array(
	'ids:',
	'fecha1:',
	'fecha2:',
	'cias:',
	'nvcd',
	'nvd',
	'no_cargar',
	'guardar_xml',
	'no_msg',
	'help',
	'max_ob_err:',
	'nderr',
	'forzar_carga'
));

if (isset($_REQUEST['help']) || isset($options['help']))
{
	echo "GenerarDatosDepositosV2.php Ver 2.15.10.8";
	echo "\nCopyright (c) 2015, Lecaroz";
	echo "\n\nModo de empleo: php GenerarDatosDepositosV2.php [OPCIONES]";
	echo "\nGenera los datos de contabilidad para depósitos de compras y gastos.";
	echo "\n\nLos argumentos obligatorios para las opciones largas son también obligatorios\npara las opciones cortas.";
	echo "\n\n  --help\t\tmuestra esta ayuda y finaliza";
	echo "\n\n  --fecha1=FECHA1\tfecha de inicio de búsqueda de registros de depósitos";
	echo "\n\n  --fecha2=FECHA2\tfecha de término de búsqueda de registros de depósitos";
	echo "\n\n  --cias=CIAS\t\tcompañías, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros de depósitos";
	echo "\n\n  --nvcd\t\tno validar si los depósitos ya han sido registradas";
	echo "\n\n  --nvd\t\tno validar diferencia de depósitos contra facturado";
	echo "\n\n  --no_cargar\t\tno cargar registros a openbravo";
	echo "\n\n  --guardar_xml\t\tguardar archivos xml de carga en directorio 'tmp/'";
	echo "\n\n  --no_msg\t\tno generar mensajes informativos";
	echo "\n\n  --max_ob_err\t\tmáximo número de errores de openbravo antes de detener\n\t\t\tscript";
	echo "\n\n  --nderr\t\tno detener script con errores de openbravo";
	echo "\n\n  --forzar_carga\tforzar la carga del documento a openbravo";
	echo "\n\nComunicar de errores en el script a carlos.candelario@lecaroz.com";
	echo "\n\n";

	die;
}

$nvcd = isset($_REQUEST['nvcd']) || isset($options['nvcd']) ? TRUE : FALSE;
$nvd = isset($_REQUEST['nvd']) || isset($options['nvd']) ? TRUE : FALSE;
$no_cargar = isset($_REQUEST['no_cargar']) || isset($options['no_cargar']) ? TRUE : FALSE;
$guardar_xml = isset($_REQUEST['guardar_xml']) || isset($options['guardar_xml']) ? TRUE : FALSE;
$no_msg = isset($_REQUEST['no_msg']) || isset($options['no_msg']) ? TRUE : FALSE;
$nderr = isset($_REQUEST['nderr']) || isset($options['nderr']) ? TRUE : FALSE;
$forzar_carga = isset($_REQUEST['forzar_carga']) || isset($options['forzar_carga']) ? TRUE : FALSE;

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
		$condiciones[] = 'sec.id IN (' . implode(', ', $ids) . ')';
	}
}

if (isset($params['fecha1']) || isset($params['fecha2']))
{
	$condiciones[] = "sec.fecha BETWEEN '{$params['fecha1']}' AND '{$params['fecha2']}'";
}
else if ( ! (isset($params['ids']) && trim($params['ids']) != ''))
{
	$condiciones[] = "sec.fecha >= NOW()::DATE - INTERVAL '3 MONTHS'";
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
		$condiciones[] = 'COALESCE(sec.num_cia_sec, sec.num_cia) IN (' . implode(', ', $cias) . ')';
	}
}

if ( ! $condiciones)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No hay parámetros de búsqueda\n";

	die;
}

$db = new DBclass($dsn, 'autocommit=yes');

if ( ! $nvcd)
{
	$condiciones[] = 'sec.ts_carga_dep IS NULL';
}

$condiciones[] = 'sec.cod_mov IN (1, 16, 44, 99)';

$condiciones[] = 'scc.tipo_cia IN (1, 2)';

$condiciones_string = implode(' AND ', $condiciones);

$sql = "SELECT
	COUNT(id) AS num_rows
FROM
(
	SELECT
		MIN(sec.id) AS id
	FROM
		estado_cuenta sec
		LEFT JOIN catalogo_companias scc ON (
			scc.num_cia = COALESCE(sec.num_cia_sec, sec.num_cia)
		)
	WHERE
		{$condiciones_string}
	GROUP BY
		COALESCE(sec.num_cia_sec, sec.num_cia),
		sec.fecha
	ORDER BY
		COALESCE(sec.num_cia_sec, sec.num_cia),
		sec.fecha
) AS result_depositos";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Ejecutando consulta a la base de datos";

$result = $db->query($sql);

if ($result[0]['num_rows'] == 0)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) No hay resultados, terminando ejecución de script.";

	die;
}

$num_rows = $result[0]['num_rows'];

$ob_err = 0;

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Depósitos a insertar: {$num_rows}";

$rows_per_query = 500;

$offset = 0;

$cont = 0;

$records = array();

$query_time = -microtime(TRUE);

while ($datos_depositos = $db->query("SELECT
	MIN(ec.id) AS id,
	COALESCE(ec.num_cia_sec, ec.num_cia) AS num_cia,
	(SELECT nombre_corto FROM catalogo_companias WHERE num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)) AS nombre_cia,
	ec.fecha,
	SUM(ec.importe) AS importe
FROM
	estado_cuenta ec
	LEFT JOIN catalogo_companias cc ON (
		cc.num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
	)
WHERE
	ec.cod_mov IN (1, 16, 44, 99)
	AND cc.tipo_cia IN (1, 2)
	AND (COALESCE(ec.num_cia_sec, ec.num_cia), ec.fecha) IN (
		SELECT
			COALESCE(sec.num_cia_sec, sec.num_cia) AS num_cia,
			sec.fecha
		FROM
			estado_cuenta sec
			LEFT JOIN catalogo_companias scc ON (
				scc.num_cia = COALESCE(sec.num_cia_sec, sec.num_cia)
			)
		WHERE
			{$condiciones_string}
		GROUP BY
			COALESCE(sec.num_cia_sec, sec.num_cia),
			sec.fecha
		ORDER BY
			COALESCE(sec.num_cia_sec, sec.num_cia),
			sec.fecha
		LIMIT {$rows_per_query} OFFSET {$offset}
	)
GROUP BY
	COALESCE(ec.num_cia_sec, ec.num_cia),
	nombre_cia,
	ec.fecha
ORDER BY
	COALESCE(ec.num_cia_sec, ec.num_cia),
	ec.fecha"))
{
	$query_time += microtime();

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de consulta: {$query_time} segundos";

	foreach ($datos_depositos as $i => $dd)
	{
		$num_cia = $dd['num_cia'];
		$fecha = $dd['fecha'];

		$cont++;

		$tmp = $dd;

		$ts = date('Y-m-d H:i:s.') . substr(microtime(), 2, 6);

		echo "\n[{$ts}]++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
		echo "\n[{$ts}](II) Registro: {$cont} de {$num_rows}";
		echo "\n[{$ts}](DD) Compañía: {$num_cia} " . utf8_encode($dd['nombre_cia']);
		echo "\n[{$ts}](DD) Fecha: {$fecha}";
		echo "\n[{$ts}](DD) Total depositado: {$dd['importe']}";

		// Obtener cuenta de la compañía
		$cuenta = $db->query("SELECT
			CASE
				WHEN ec.cuenta = 1 THEN
					cc.clabe_cuenta
				WHEN ec.cuenta = 2 THEN
					cc.clabe_cuenta2
			END AS num_cuenta
		FROM
			estado_cuenta ec
			LEFT JOIN catalogo_companias cc USING (num_cia)
		WHERE
			ec.id = {$dd['id']}");

		$datos = array(
			'deposito'		=> array(
				'id'		=> intval($dd['id']),
				'num_cia'	=> intval($dd['num_cia']),
				'fecha'		=> $dd['fecha'],
				'cuenta'	=> $cuenta[0]['num_cuenta'],
				'importe'	=> floatval($dd['importe']),
			),
			'facturas'	=> array()
		);

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obteniendo facturas asociadas";

		$facturas = $db->query("SELECT
			fe.num_cia,
			fes.serie || fe.consecutivo AS folio,
			fe.total AS importe
		FROM
			facturas_electronicas fe
		LEFT JOIN facturas_electronicas_series fes ON (
			fes.num_cia = fe.num_cia
			AND fes.tipo_serie = fe.tipo_serie
			AND fe.consecutivo BETWEEN fes.folio_inicial AND fes.folio_final
		)
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = fe.num_cia)
		WHERE
			(
				fe.num_cia = {$dd['num_cia']}
				OR fe.num_cia IN (
					SELECT
						sucursal
					FROM
						porcentajes_puntos_calientes
					WHERE
						matriz = {$dd['num_cia']}
				)
			)
			AND fe.fecha = '{$dd['fecha']}'
			AND fe.tipo IN (1, 2)
			AND fe.status = 1");

		if ($facturas)
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Facturas asociadas: " . count($facturas);

			$total_facturas = 0;

			foreach ($facturas as $f)
			{
				$ts = date('Y-m-d H:i:s.') . substr(microtime(), 2, 6);

				echo "\n[{$ts}](DD) Factura compañía: {$f['num_cia']}";
				echo "\n[{$ts}](DD) Factura folio: {$f['folio']}";
				echo "\n[{$ts}](DD) Factura importe: {$f['importe']}";

				$datos['facturas'][] = array(
					'num_cia'	=> intval($f['num_cia']),
					'folio'		=> $f['folio'],
					'importe'	=> floatval($f['importe'])
				);

				$total_facturas += floatval($f['importe']);
			}

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Total facturado: {$total_facturas}";

			if ( ! $nvd && round($datos['deposito']['importe'], 2) != round($total_facturas, 2))
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) El total depositado no coincide con el total facturado ({$datos['deposito']['importe']} " . ($datos['deposito']['importe'] > $total_facturas ? '>' : '<') . " {$total_facturas})";
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Generando datos de carga para openbravo";

				$xml_carga = generar_xml_datos($datos, $guardar_xml);

				if ( ! $no_cargar)
				{
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

							if ($records)
							{
								echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($records) . " registros";

								$db->query("UPDATE estado_cuenta
								SET ts_carga_dep = NOW()
								WHERE
									(COALESCE(num_cia_sec, num_cia), fecha) IN (VALUES " . implode(', ', $records) . ")
									AND cod_mov IN (1, 16, 44, 99)");
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

								if ($records)
								{
									echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($records) . " registros";

									$db->query("UPDATE estado_cuenta
									SET ts_carga_dep = NOW()
									WHERE
										(COALESCE(num_cia_sec, num_cia), fecha) IN (VALUES " . implode(', ', $records) . ")
										AND cod_mov IN (1, 16, 44, 99)");
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
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de depósito a openbravo exitosa";

							$records[] = "({$dd['num_cia']}, '{$dd['fecha']}'::DATE)";
						}
					}
				}
				else
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se cargo el depósito al sistema";
				}
			}
		}
		else
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No hay facturas emitidas para el día {$datos['deposito']['fecha']}, no se registraran los depósitos";
		}
	}

	$offset += $rows_per_query;

	$query_time = -microtime(TRUE);
}

if ($records)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($records) . " registros";

	$db->query("UPDATE estado_cuenta
	SET ts_carga_dep = NOW()
	WHERE
		(COALESCE(num_cia_sec, num_cia), fecha) IN (VALUES " . implode(', ', $records) . ")
		AND cod_mov IN (1, 16, 44, 99)");
}

function generar_xml_datos($datos, $guardar_xml = FALSE)
{
	// Crear documento XML con los datos para el webservice
	$xml = new DOMDocument('1.0', 'UTF-8');

	$xml->xmlStandalone = TRUE;
	$xml->formatOutput = TRUE;

	// Construir estructura del XML

	$xml_payment = $xml->createElement('payment');
	$xml->appendChild($xml_payment);

	$xml_documentNo = $xml->createElement('documentNo', $datos['deposito']['id']);
	$xml_payment->appendChild($xml_documentNo);

	$xml_financialAccountValue = $xml->createElement('financialAccountValue', "{$datos['deposito']['cuenta']}");
	$xml_payment->appendChild($xml_financialAccountValue);

	$xml_paymentAmount = $xml->createElement('paymentAmount', $datos['deposito']['importe']);
	$xml_payment->appendChild($xml_paymentAmount);

	$xml_paymentDate = $xml->createElement('paymentDate', dmy_to_ymd($datos['deposito']['fecha']));
	$xml_payment->appendChild($xml_paymentDate);

	$xml_paymentAmount = $xml->createElement('organization', $datos['deposito']['num_cia']);
	$xml_payment->appendChild($xml_paymentAmount);

	$xml_invoices = $xml->createElement('invoices');
	$xml_payment->appendChild($xml_invoices);

	foreach ($datos['facturas'] as $f) {
		$xml_invoice = $xml->createElement('invoice');
		$xml_invoices->appendChild($xml_invoice);

		$xml_invoice_amount = $xml->createElement('amount', $f['importe']);
		$xml_invoice->appendChild($xml_invoice_amount);

		$xml_invoice_documentNo = $xml->createElement('documentNo', $f['folio']);
		$xml_invoice->appendChild($xml_invoice_documentNo);

		$xml_invoice_organization = $xml->createElement('organization', $f['num_cia']);
		$xml_invoice->appendChild($xml_invoice_organization);
	}

	// Guardar XML de carga
	if ($guardar_xml)
	{
		if ( ! is_dir("tmp/{$subdir}"))
		{
			mkdir("tmp/{$subdir}");
		}

		list($dia, $mes, $anio) = explode('/', $datos['fecha']);

		$nombre_xml = "carga-depositos-{$datos['num_cia']}-{$datos['cuenta']}-{$anio}-{$mes}-{$dia}-{$datos['id']}.xml";

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
	// $ob_response = @file_get_contents('http://192.168.1.251:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecPayment', NULL, $context);
	$ob_response = @file_get_contents('http://192.168.1.3:443/ob_lecaroz/ws/mx.cusoft.importing.rest.insertLecPayment', NULL, $context);

	return $ob_response;
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
}
