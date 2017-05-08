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
	'nvcp',
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
	echo "GenerarDatosPagosZapateriasV2.php Ver 2.15.9.8";
	echo "\nCopyright (c) 2015, Lecaroz";
	echo "\n\nModo de empleo: php GenerarDatosPagosZapateriasV2.php [OPCIONES]";
	echo "\nGenera los datos de contabilidad para pagos de compras y gastos.";
	echo "\n\nLos argumentos obligatorios para las opciones largas son también obligatorios\npara las opciones cortas.";
	echo "\n\n  --help\t\tmuestra esta ayuda y finaliza";
	echo "\n\n  --ids=IDS\t\tidentificadores, separados por comas (1,2,3,...) o\n\t\t\trangos (1-5,20-26,...) de los registros de pago";
	echo "\n\n  --fecha1=FECHA1\tfecha de inicio de búsqueda de registros de pago";
	echo "\n\n  --fecha2=FECHA2\tfecha de término de búsqueda de registros de pago";
	echo "\n\n  --cias=CIAS\t\tcompañías, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros de pago";
	echo "\n\n  --pros=PROS\t\tproveedores, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros de pago";
	echo "\n\n  --nvcf\t\tno validar si las facturas ya han sido registradas";
	echo "\n\n  --nvcp\t\tno validar si los pagos ya han sido registrados";
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

$nvcf = isset($_REQUEST['nvcf']) || isset($options['nvcf']) ? TRUE : FALSE;
$nvcp = isset($_REQUEST['nvcp']) || isset($options['nvcp']) ? TRUE : FALSE;
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
		$condiciones[] = 'sec.id IN (' . implode(', ', $ids) . ')';
	}
}

if (isset($params['fecha1']) && isset($params['fecha2']))
{
	$condiciones[] = "sec.fecha_con BETWEEN '{$params['fecha1']}' AND '{$params['fecha2']}'";
}
else if ( ! isset($ids))
{
	$condiciones[] = "sec.fecha_con >= NOW()::DATE - INTERVAL '3 MONTHS'";
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
		$condiciones[] = 'sec.num_cia IN (' . implode(', ', $cias) . ')';
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
		$condiciones[] = 'sf.num_proveedor IN (' . implode(', ', $pros) . ')';
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
	$condiciones[] = 'sf.ts_carga_conta IS NOT NULL';
}

if ( ! $nvcp)
{
	$condiciones[] = 'sf.ts_carga_pago_conta IS NULL';
}

$condiciones[] = 'sec.fecha_con IS NOT NULL';

$condiciones_string = implode(' AND ', $condiciones);

$sql = "SELECT
	COUNT(id) AS num_rows
FROM
(
	SELECT
		sec.id
	FROM
		facturas_zap sf
		LEFT JOIN estado_cuenta sec ON (
			sec.num_cia = sf.num_cia
			AND sec.cuenta = sf.cuenta
			AND sec.folio = sf.folio
		)
	WHERE
		{$condiciones_string}
	GROUP BY
		sec.id
) AS result_pagos";

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
	'N'	=> 'No Aplica'
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

while ($datos_pago = $db->query("SELECT
	f.id AS id_factura,
	f.num_cia AS num_cia_factura,
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
	CASE
		WHEN f.ts_carga_conta IS NOT NULL THEN
			TRUE
		ELSE
			FALSE
	END AS carga_factura,
	ec.id AS id_pago,
	ec.num_cia AS num_cia_pago,
	cc.nombre_corto AS nombre_cia_pago,
	ec.cuenta AS banco,
	CASE
		WHEN ec.fecha_con IS NOT NULL THEN
			(
				CASE
					WHEN ec.cuenta = 1 THEN
						clabe_cuenta
					WHEN ec.cuenta = 2 THEN
						clabe_cuenta2
				END
			)
		ELSE
			NULL
	END AS cuenta_pago,
	ec.fecha_con AS fecha_pago,
	ec.folio AS folio_pago,
	ec.importe AS importe_pago
FROM
	facturas_zap f
	LEFT JOIN estado_cuenta ec ON (
		ec.num_cia = f.num_cia
		AND ec.cuenta = f.cuenta
		AND ec.folio = f.folio
	)
	LEFT JOIN catalogo_companias cc ON (cc.num_cia = ec.num_cia)
WHERE
	ec.id IN (
		SELECT
			sec.id
		FROM
			facturas_zap sf
			LEFT JOIN estado_cuenta sec ON (
				sec.num_cia = sf.num_cia
				AND sec.cuenta = sf.cuenta
				AND sec.folio = sf.folio
			)
		WHERE
			{$condiciones_string}
		GROUP BY
			sec.id
		ORDER BY
			sec.num_cia,
			sec.fecha_con,
			sec.id
		LIMIT {$rows_per_query} OFFSET {$offset}
	)
ORDER BY
	ec.num_cia,
	ec.fecha_con,
	ec.id,
	f.id"))
{
	$query_time += microtime();

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de consulta: {$query_time} segundos";

	$id_pago = NULL;

	foreach ($datos_pago as $i => $dp)
	{
		if ($id_pago != $dp['id_pago'])
		{
			if ($id_pago != NULL)
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
						$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = 'Webservice no disponible', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Webservice no disponible";

						$ob_err++;

						if ( ! $nderr && $ob_err >= $max_ob_err)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

							if ($ids)
							{
								echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

								$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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
							$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = 'Imposible decodificar respuesta de openbravo: {$response}', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Imposible decodificar respuesta de openbravo: {$response}";

							$ob_err++;

							if ( ! $nderr && $ob_err >= $max_ob_err)
							{
								echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

								if ($ids)
								{
									echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

									$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
								}

								echo "\n";

								die();
							}
						}
						else if ($ob_data->status < 0)
						{
							$msg = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : (isset($ob_data->mensaje) ? $ob_data->mensaje : print_r($response, TRUE)))));

							$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = '{$msg}', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Respuesta de openbravo: {$msg}";
						}
						else
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de pago a openbravo exitosa";

							$ids = array_merge($ids, $facs_id);
						}
					}
				}
				else
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se cargo el pago al sistema";
				}

				$cont++;
			}

			$id_pago = $dp['id_pago'];

			$tmp = $dp;

			$ts = date('Y-m-d H:i:s.') . substr(microtime(), 2, 6);

			echo "\n[{$ts}]++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
			echo "\n[{$ts}](II) Registro: {$cont} de {$num_rows}";
			echo "\n[{$ts}](DD) Id pago: {$id_pago}";
			echo "\n[{$ts}](DD) Compañía pago: {$dp['num_cia_pago']} " . utf8_encode($dp['nombre_cia_pago']);
			echo "\n[{$ts}](DD) Banco pago: " . ($dp['banco'] == 1 ? 'BANORTE' : ($dp['banco'] == 2 ? 'SANTANDER' : 'DESCONOCIDO'));
			echo "\n[{$ts}](DD) Cuenta pago: {$dp['cuenta_pago']}";
			echo "\n[{$ts}](DD) Fecha pago: {$dp['fecha_pago']}";
			echo "\n[{$ts}](DD) Folio pago: {$dp['folio_pago']}";
			echo "\n[{$ts}](DD) Importe pago: {$dp['importe_pago']}";

			$datos = array(
				'pago'		=> array(
					'id_pago'		=> $dp['id_pago'],
					'cuenta_pago'	=> $dp['cuenta_pago'],
					'fecha_pago'	=> $dp['fecha_pago'],
					'folio_pago'	=> ($dp['banco'] == 1 ? 'B' : 'S') . '-' . get_val($dp['folio_pago']),
					'importe_pago'	=> get_val($dp['importe_pago']),
					'num_cia_pago'	=> get_val($dp['num_cia_pago'])
				),
				'facturas'	=> array()
			);

			$facs_id = array();
		}

		$facs_id[] = $dp['id_factura'];

		$importe = $dp['importe'] - $dp['faltantes'] - $dp['diferencia_precio'] - $dp['devoluciones'];
		$desc1 = $dp['pdesc1'] > 0 ? round($importe * $dp['pdesc1'] / 100, 2) : ($dp['desc1'] > 0 ? $dp['desc1'] : 0);
		$desc2 = $dp['pdesc2'] > 0 ? round(($importe - $desc1) * $dp['pdesc2'] / 100, 2) : ($dp['desc2'] > 0 ? $dp['desc2'] : 0);
		$desc3 = $dp['pdesc3'] > 0 ? round(($importe - $desc1 - $desc2) * $dp['pdesc3'] / 100, 2) : ($dp['desc3'] > 0 ? $dp['desc3'] : 0);
		$desc4 = $dp['pdesc4'] > 0 ? round(($importe - $desc1 - $desc2 - $desc3) * $dp['pdesc4'] / 100, 2) : ($dp['desc4'] > 0 ? $dp['desc4'] : 0);
		$descuentos = $desc1 + $desc2 + $desc3 + $desc4;
		$subtotal = $importe - $desc1 - $desc2 - $desc3 - $desc4;
		$iva = $dp['iva'] > 0 ? $subtotal * 0.16 : 0;
		$total = $subtotal + $iva - abs($dp['retencion_iva']) - abs($dp['retencion_isr']) - $dp['fletes'] + $dp['otros'];

		echo "\n[{$ts}](DD) Id factura: {$dp['id_factura']}";
		echo "\n[{$ts}](DD) Importe factura: {$total}";

		$datos['facturas'][] = array(
			'id'		=> $dp['id_factura'],
			'num_cia'	=> $dp['num_cia_factura'],
			'importe'	=> get_val($total)
		);

		if ($dp['carga_factura'] == 'f' || $forzar_carga)
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Esta factura no esta cargada en openbravo, se procedera a registrarla";

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obteniendo datos de factura";

			$query_fac = $db->query("SELECT
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
				f.id = {$dp['id_factura']}");

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Generando datos de carga para openbravo";

			$datos_fac = array(
				'cabecera'	=> array (
					'id'					=> $query_fac[0]['id'],
					'num_cia'				=> $query_fac[0]['num_cia'],
					'tipo'					=> $query_fac[0]['tipo_registro'],
					'fecha'					=> $query_fac[0]['fecha'],
					'clave_pro'				=> $query_fac[0]['num_pro'],
					'nombre_pro'			=> $query_fac[0]['nombre_pro'],
					'rfc_pro'				=> $query_fac[0]['rfc_pro'],
					'num_fact'				=> $query_fac[0]['num_fact'],
					'calle'					=> $query_fac[0]['calle'],
					'no_exterior'			=> $query_fac[0]['no_exterior'],
					'no_interior'			=> $query_fac[0]['no_interior'],
					'colonia'				=> $query_fac[0]['colonia'],
					'localidad'				=> $query_fac[0]['localidad'],
					'referencia'			=> $query_fac[0]['referencia'],
					'municipio'				=> $query_fac[0]['municipio'],
					'estado'				=> $query_fac[0]['estado'],
					'pais'					=> $query_fac[0]['pais'],
					'codigo_postal'			=> $query_fac[0]['codigo_postal'],
					'email'					=> $query_fac[0]['email'],
					'importe'				=> get_val($query_fac[0]['importe']),
					'descuentos'			=> get_val($descuentos + $query_fac[0]['faltantes'] + $query_fac[0]['diferencia_precio'] + $query_fac[0]['devoluciones']),
					'iva'					=> get_val($iva),
					'retencion_isr'			=> get_val($query_fac[0]['retencion_isr']),
					'retencion_iva'			=> get_val($query_fac[0]['retencion_iva']),
					'total'					=> get_val($total)
				),
				'detalle'	=> array()
			);

			$tax = $query_fac[0]['iva'] != 0 && $query_fac[0]['retencion_iva'] != 0 && $query_fac[0]['retencion_isr'] != 0 ? 'HONORARIOS/ARRENDAMIENTOS' : ($query_fac[0]['iva'] != 0 && $query_fac[0]['retencion_iva'] != 0 ? 'IVA + RET 4%' : ($query_fac[0]['retencion_isr'] != 0 ? (abs(round($query_fac[0]['retencion_isr'] * 100 / $importe, 2)) == 10 ? 'ARRENDAMIENTO HABITACION' : 'R35%') : ($query_fac[0]['iva'] != 0 ? 'IVA' : 'IVA 0')));

			$datos_fac['detalle'][] = array(
				'clave'			=> $query_fac[0]['tipo_producto'] . $query_fac[0]['codigo'],
				'descripcion'	=> $query_fac[0]['descripcion'],
				'cantidad'		=> 1,
				'precio'		=> get_val($query_fac[0]['importe']),
				'importe'		=> get_val($query_fac[0]['importe']),
				'tax'			=> $tax
			);

			if (get_val($query_fac[0]['devoluciones']) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D3',
					'descripcion'	=> 'DEVOLUCIONES',
					'cantidad'		=> 1,
					'precio'		=> -get_val($query_fac[0]['devoluciones']),
					'importe'		=> -get_val($query_fac[0]['devoluciones']),
					'tax'			=> $tax
				);
			}

			if (get_val($query_fac[0]['faltantes']) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D3',
					'descripcion'	=> 'FALTANTES',
					'cantidad'		=> 1,
					'precio'		=> -get_val($query_fac[0]['faltantes']),
					'importe'		=> -get_val($query_fac[0]['faltantes']),
					'tax'			=> $tax
				);
			}

			if (get_val($query_fac[0]['diferencia_precio']) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D3',
					'descripcion'	=> 'DIFERENCIA DE PRECIO',
					'cantidad'		=> 1,
					'precio'		=> -get_val($query_fac[0]['diferencia_precio']),
					'importe'		=> -get_val($query_fac[0]['diferencia_precio']),
					'tax'			=> $tax
				);
			}

			if (get_val($descuentos) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D3',
					'descripcion'	=> $query_fac[0]['descuentos_string'],
					'cantidad'		=> 1,
					'precio'		=> -get_val($descuentos),
					'importe'		=> -get_val($descuentos),
					'tax'			=> $tax
				);
			}

			if (get_val($query_fac[0]['fletes']) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D4',
					'descripcion'	=> 'FLETES',
					'cantidad'		=> 1,
					'precio'		=> -get_val($query_fac[0]['fletes']),
					'importe'		=> -get_val($query_fac[0]['fletes']),
					'tax'			=> 'IVA 0'
				);
			}

			if (get_val($query_fac[0]['otros']) > 0)
			{
				$datos_fac['detalle'][] = array(
					'clave'			=> 'D4',
					'descripcion'	=> 'OTROS',
					'cantidad'		=> 1,
					'precio'		=> get_val($query_fac[0]['otros']),
					'importe'		=> get_val($query_fac[0]['otros']),
					'tax'			=> 'IVA 0'
				);
			}

			$xml_carga = generar_xml_datos_factura($datos_fac, $guardar_xml);

			if ( ! $no_cargar)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo webservice";

				$response = consumir_webservice_factura($xml_carga);

				$ob_data = json_decode(utf8_encode($response));

				if ($response === FALSE)
				{
					$db->query("UPDATE facturas_zap SET ts_carga_conta_error = 'Webservice no disponible', ts_carga_conta = NULL WHERE id = {$dp['id_factura']}");

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Webservice no disponible";

					$ob_err++;

					if ( ! $nderr && $ob_err >= $max_ob_err)
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

						if ($ids)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

							$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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
						$db->query("UPDATE facturas_zap SET ts_carga_conta_error = 'Imposible decodificar respuesta de openbravo: {$response}', ts_carga_conta = NULL WHERE id = {$dp['id_factura']}");

						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Imposible decodificar respuesta de openbravo: {$response}";

						$ob_err++;

						if ( ! $nderr && $ob_err >= $max_ob_err)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

							if ($ids)
							{
								echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

								$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
							}

							echo "\n";

							die();
						}
					}
					else if ($ob_data->status < 0)
					{
						$msg = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : (isset($ob_data->mensaje) ? $ob_data->mensaje : print_r($response, TRUE)))));

						$db->query("UPDATE facturas_zap SET ts_carga_conta_error = '{$msg}', ts_carga_conta = NULL WHERE id = {$dp['id_factura']}");

						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Respuesta de openbravo: {$msg}";
					}
					else
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de documento a openbravo exitosa";

						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marca de tiempo de la factura";

						$db->query("UPDATE facturas_zap SET ts_carga_conta = NOW(), ts_carga_conta_error = NULL WHERE id = {$dp['id_factura']}");
					}
				}
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se cargo el documento al sistema";
			}
		}
	}

	if ($id_pago != NULL)
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
				$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = 'Webservice no disponible', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Webservice no disponible";

				$ob_err++;

				if ( ! $nderr && $ob_err >= $max_ob_err)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

					if ($ids)
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

						$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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
					$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = 'Imposible decodificar respuesta de openbravo: {$response}', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Imposible decodificar respuesta de openbravo: {$response}";

					$ob_err++;

					if ( ! $nderr && $ob_err >= $max_ob_err)
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Openbravo llego a su máximo número de errores ({$max_ob_err}), cancelando ejecución";

						if ($ids)
						{
							echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros";

							$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
						}

						echo "\n";

						die();
					}
				}
				else if ($ob_data->status < 0)
				{
					$msg = utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : (isset($ob_data->import_msg) ? $ob_data->import_msg : (isset($ob_data->mensaje) ? $ob_data->mensaje : print_r($response, TRUE)))));

					$db->query("UPDATE facturas_zap SET ts_carga_pago_conta_error = '{$msg}', ts_carga_pago_conta = NULL WHERE id IN (" . implode(', ', $facs_id) . ")");

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Respuesta de openbravo: {$msg}";
				}
				else
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Carga de pago a openbravo exitosa";

					$ids = array_merge($ids, $facs_id);
				}
			}
		}
		else
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) No se cargo el pago al sistema";
		}

		$cont++;
	}

	$offset += $rows_per_query;

	$query_time = -microtime(TRUE);
}

if ($ids)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de pago de " . count($ids) . " registros\n";

	$db->query("UPDATE facturas_zap SET ts_carga_pago_conta = NOW(), ts_carga_pago_conta_error = NULL WHERE id IN (" . implode(', ', $ids) . ")");
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

	$xml_documentNo = $xml->createElement('documentNo', $datos['pago']['folio_pago']);
	$xml_payment->appendChild($xml_documentNo);

	$xml_financialAccountValue = $xml->createElement('financialAccountValue', "{$datos['pago']['cuenta_pago']}");
	$xml_payment->appendChild($xml_financialAccountValue);

	$xml_paymentAmount = $xml->createElement('paymentAmount', $datos['pago']['importe_pago']);
	$xml_payment->appendChild($xml_paymentAmount);

	$xml_paymentDate = $xml->createElement('paymentDate', dmy_to_ymd($datos['pago']['fecha_pago']));
	$xml_payment->appendChild($xml_paymentDate);

	$xml_organization = $xml->createElement('organization', $datos['pago']['num_cia_pago']);
	$xml_payment->appendChild($xml_organization);

	$xml_refund = $xml->createElement('refund', 'N');
	$xml_payment->appendChild($xml_refund);

	$xml_invoices = $xml->createElement('invoices');
	$xml_payment->appendChild($xml_invoices);

	foreach ($datos['facturas'] as $factura) {
		$xml_invoice = $xml->createElement('invoice');
		$xml_invoices->appendChild($xml_invoice);

		$xml_invoice_amount = $xml->createElement('amount', $factura['importe']);
		$xml_invoice->appendChild($xml_invoice_amount);

		$xml_invoice_documentNo = $xml->createElement('documentNo', $factura['id']);
		$xml_invoice->appendChild($xml_invoice_documentNo);

		$xml_invoice_organization = $xml->createElement('organization', $factura['num_cia']);
		$xml_invoice->appendChild($xml_invoice_organization);
	}

	// Guardar XML de carga

	$subdir = date('Y-m-d');

	if ($guardar_xml)
	{
		if ( ! is_dir("tmp/{$subdir}"))
		{
			mkdir("tmp/{$subdir}");
		}

		$nombre_xml = "carga-pagos-zap-{$datos['pago']['id_pago']}-{$datos['pago']['num_cia_pago']}-{$datos['pago']['cuenta_pago']}-{$datos['pago']['folio_pago']}.xml";

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

function generar_xml_datos_factura($datos, $guardar_xml = FALSE)
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

		$xml_line_taxValue = $xml->createElement('taxValue', '');
		$xml_invoiceline->appendChild($xml_line_taxValue);

		$xml_line_unitPrice = $xml->createElement('unitPrice', $detalle['precio']);
		$xml_invoiceline->appendChild($xml_line_unitPrice);

		$xml_line_productOrderUOMValue = $xml->createElement('productOrderUOMValue', /*htmlspecialchars(utf8_encode($detalle['unidad']))*/'');
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

		$nombre_xml = "carga-conta-zap-{$datos['cabecera']['id']}-{$datos['cabecera']['num_cia']}-{$datos['cabecera']['clave_pro']}-{$datos['cabecera']['num_fact']}.xml";

		$xml->save("tmp/{$subdir}/{$nombre_xml}");

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Documento XML guardado con nombre: {$nombre_xml}";
	}

	// Retornar el XML
	return $xml->saveXML();
}

function consumir_webservice_factura($xml_data)
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
