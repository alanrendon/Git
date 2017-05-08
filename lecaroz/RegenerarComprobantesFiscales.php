<?php

include('includes/dbstatus.php');
include('includes/class.db.inc.php');
include('GenerarComprobantesFiscales.php');

$db = new DBclass($dsn, 'autocommit=yes');

// $result = $db->query("SELECT * FROM facturas_electronicas WHERE ob_response != '' AND TRIM(uuid) = '' ORDER BY num_cia, tipo_serie, consecutivo");
// $result = $db->query("SELECT * FROM facturas_electronicas WHERE (num_cia, tipo_serie, consecutivo) IN (VALUES (47, 1, 2336), (47, 1, 2337), (47, 1, 2338), (47, 1, 2340), (47, 1, 2341), (47, 1, 2342), (47, 1, 2343), (47, 1, 2345), (47, 1, 2346), (47, 1, 2347), (47, 1, 2348), (127, 1, 1315), (127, 1, 1316), (127, 1, 1317), (127, 1, 1318), (127, 1, 1319), (127, 1, 1320), (127, 1, 1321), (127, 1, 1322), (127, 1, 1323), (127, 1, 1324), (127, 1, 1325), (932, 1, 2745), (127, 1, 1326), (127, 1, 1327), (127, 1, 1328), (127, 1, 1329), (127, 1, 1330), (127, 1, 1331), (127, 1, 1332), (127, 1, 1333), (127, 1, 1334), (127, 1, 1335), (127, 1, 1336), (127, 1, 1337), (95, 1, 1460), (127, 1, 1338), (127, 1, 1339), (127, 1, 1340), (127, 1, 1341), (127, 1, 1342), (127, 1, 1343), (127, 1, 1344), (47, 1, 2321), (47, 1, 2322), (47, 1, 2324), (47, 1, 2329), (47, 1, 2332), (47, 1, 2333), (47, 1, 2334), (47, 1, 2335), (47, 1, 2307), (47, 1, 2308), (47, 1, 2310), (47, 1, 2311), (47, 1, 2312), (47, 1, 2313), (47, 1, 2314), (47, 1, 2315), (47, 1, 2316), (47, 1, 2319), (47, 1, 2320), (933, 1, 1023), (933, 1, 1024), (933, 1, 1025), (81, 1, 2969), (81, 1, 2974), (153, 1, 1), (136, 1, 620), (31, 1, 1845), (31, 1, 1847), (304, 1, 1581), (122, 1, 1940), (86, 1, 1492), (114, 1, 1440), (86, 1, 1493), (86, 1, 1494), (86, 1, 1495), (86, 1, 1496), (86, 1, 1497), (86, 1, 1498), (86, 1, 1499), (86, 1, 1500), (86, 1, 1501), (86, 1, 1502), (86, 1, 1504), (86, 1, 1505), (86, 1, 1506), (86, 1, 1507), (86, 1, 1508), (86, 1, 1509), (86, 1, 1510), (86, 1, 1511), (86, 1, 1512), (86, 1, 1514), (86, 1, 1515), (86, 1, 1516), (86, 1, 1517), (86, 1, 1518), (86, 1, 1519), (117, 1, 1339), (117, 1, 1340), (117, 1, 1341), (117, 1, 1342), (117, 1, 1343), (117, 1, 1344), (117, 1, 1345), (117, 1, 1346), (117, 1, 1347), (117, 1, 1348), (117, 1, 1349), (117, 1, 1350), (117, 1, 1351), (117, 1, 1352), (117, 1, 1353), (117, 1, 1354), (117, 1, 1355), (117, 1, 1356), (117, 1, 1357), (117, 1, 1358), (117, 1, 1359), (117, 1, 1360), (117, 1, 1361), (117, 1, 1362), (117, 1, 1363), (117, 1, 1364), (117, 1, 1365), (117, 1, 1366), (52, 1, 1828), (86, 1, 1489), (86, 1, 1490), (86, 1, 1491), (52, 1, 1829), (52, 1, 1830), (56, 1, 1485), (56, 1, 1508), (117, 1, 1338), (146, 1, 194), (108, 1, 1374), (60, 1, 2686)) ORDER BY num_cia, tipo_serie, consecutivo");

$condiciones = array();

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0)
{
	$condiciones[] = "fe.id = {$_REQUEST['id']}";
}
else if (isset($_REQUEST['ids']) && $_REQUEST['ids'] != '')
{
	$condiciones[] = "fe.id IN ({$_REQUEST['ids']})";
}
else if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0 && isset($_REQUEST['tipo']) && $_REQUEST['tipo'] > 0 && isset($_REQUEST['folio']) && $_REQUEST['folio'] > 0)
{
	$condiciones[] = "fe.num_cia = {$_REQUEST['num_cia']}";
	$condiciones[] = "fe.tipo_serie = {$_REQUEST['tipo']}";
	$condiciones[] = "fe.consecutivo = {$_REQUEST['folio']}";
}
else if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0 && isset($_REQUEST['serie']) && isset($_REQUEST['folio']) && $_REQUEST['folio'] > 0)
{
	$condiciones[] = "fe.num_cia = {$_REQUEST['num_cia']}";
	$condiciones[] = "fes.serie = '{$_REQUEST['serie']}'";
	$condiciones[] = "fe.consecutivo = {$_REQUEST['folio']}";
}
else if (isset($_REQUEST['error']))
{
	$result = $db->query("SELECT num_fact FROM facturas_electronicas_server_status_new WHERE status = -622");

	if ( ! $result)
	{
		echo "<p>@@ No hay pendientes por generar</p>";

		die;
	}

	$series = array();

	foreach ($result as $row) {
		list($num_cia, $num_fact) = explode('-', $row['num_fact']);

		preg_match_all(rtrim("/([a-zA-Z]{0,})([0-9]{1,})/", 'g'), $num_fact, $parts, PREG_PATTERN_ORDER);

		$serie = $parts[1][0];
		$folio = $parts[2][0];

		$series[] = "({$num_cia}, '{$serie}', {$folio})";
	}

	$condiciones[] = '(fe.num_cia, fes.serie, fe.consecutivo) IN (VALUES ' . implode(', ', $series) . ')';
}
else
{
	echo "<p>@@ No se especificaron parametros de busqueda</p>";

	die;
}

$sql = "
	SELECT
		fe.*
	FROM
		facturas_electronicas fe
		LEFT JOIN facturas_electronicas_series fes
			ON (fes.num_cia = fe.num_cia AND fes.tipo_serie = fe.tipo_serie AND fe.consecutivo BETWEEN fes.folio_inicial AND fes.folio_final)
	WHERE
		" . implode(' AND ', $condiciones) . "
	ORDER BY
		fe.num_cia,
		fe.tipo_serie,
		fe.consecutivo
";

// echo "<pre>{$sql}</pre>";

$result = $db->query($sql);

if ( ! $result)
{
	echo "<p>@@ No hay resultados</p>";

	die;
}

foreach ($result as $j => $row) {
	$query = $db->query("
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
			fes.num_cia = {$row['num_cia']}
			AND fes.tipo_serie = {$row['tipo_serie']}
			AND {$row['consecutivo']} BETWEEN folio_inicial AND folio_final;
	");

	$datos_emisor = $query[0];

	foreach ($datos_emisor as $k => $v)
	{
		$datos_emisor[$k] = utf8_encode($v);
	}

	$ob_data = json_decode(utf8_encode($row['ob_response']));

	if (json_last_error() != JSON_ERROR_NONE)
	{
		switch(json_last_error()) {
			case JSON_ERROR_NONE:
				echo ' - Sin errores';
				break;
			case JSON_ERROR_DEPTH:
				echo ' - Excedido tamaño máximo de la pila';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				echo ' - Desbordamiento de buffer o los modos no coinciden';
				break;
			case JSON_ERROR_CTRL_CHAR:
				echo ' - Encontrado carácter de control no esperado';
				break;
			case JSON_ERROR_SYNTAX:
				echo ' - Error de sintaxis, JSON mal formado';
				break;
			case JSON_ERROR_UTF8:
				echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
				break;
			default:
				echo ' - Error desconocido';
				break;
    	}
	}

	if ($ob_data->status < 0)
	{
		echo "<p>@#{" . ($j + 1) . "} COMPROBANTE {$row['num_cia']}-{$datos_emisor['serie']}{$row['consecutivo']}<br />@@ Error: " . utf8_encode(isset($ob_data->error) ? $ob_data->error : (isset($ob_data->complete_msg) ? $ob_data->complete_msg : $ob_data->import_msg)) . "</p>";

		continue;
	}

	$datos = array(
		'cabecera' => array (
			'num_cia'               => $row['num_cia'],
			'fecha'                 => $row['fecha'],
			'hora'                  => $row['hora'],
			'clave_cliente'         => $row['clave_cliente'],
			'nombre_cliente'        => utf8_decode($row['nombre_cliente']),
			'rfc_cliente'           => utf8_decode($row['rfc']),
			'calle'                 => utf8_decode($row['calle']),
			'no_exterior'           => utf8_decode($row['no_exterior']),
			'no_interior'           => utf8_decode($row['no_interior']),
			'colonia'               => utf8_decode($row['colonia']),
			'localidad'             => utf8_decode($row['localidad']),
			'referencia'            => utf8_decode($row['referencia']),
			'municipio'             => utf8_decode($row['municipio']),
			'estado'                => utf8_decode($row['estado']),
			'pais'                  => utf8_decode($row['pais']),
			'codigo_postal'         => $row['codigo_postal'],
			'email'                 => utf8_decode($row['email_cliente']),
			'observaciones'         => utf8_decode($row['observaciones']),
			'importe'               => $row['importe'],
			'porcentaje_descuento'  => $row['porcentaje_descuento'],
			'descuento'             => $row['descuento'],
			'ieps'                  => $row['ieps'],
			'porcentaje_iva'        => $row['iva'] > 0 ? 16 : 0,
			'importe_iva'           => $row['iva'],
			'importe_retencion_isr' => $row['retencion_isr'],
			'importe_retencion_iva' => $row['retencion_iva'],
			'total'                 => $row['total'],
			'tipo_pago'             => $row['tipo_pago'],
			'cuenta_pago'           => $row['cuenta_pago'],
			'condiciones_pago'      => $row['condiciones_pago']
		),
		'consignatario' => array (
			'nombre'        => utf8_decode($row['nombre_consignatario']),
			'rfc'           => utf8_decode($row['rfc_consignatario']),
			'calle'         => utf8_decode($row['calle_consignatario']),
			'no_exterior'   => utf8_decode($row['no_exterior_consignatario']),
			'no_interior'   => utf8_decode($row['no_interior_consignatario']),
			'colonia'       => utf8_decode($row['colonia_consignatario']),
			'localidad'     => utf8_decode($row['localidad_consignatario']),
			'referencia'    => utf8_decode($row['referencia_consignatario']),
			'municipio'     => utf8_decode($row['municipio_consignatario']),
			'estado'        => utf8_decode($row['estado_consignatario']),
			'pais'          => utf8_decode($row['pais_consignatario']),
			'codigo_postal' => utf8_decode($row['codigo_postal_consignatario'])
		),
		'detalle' => array()
	);

	$detalles = $db->query("SELECT * FROM facturas_electronicas_detalle WHERE num_cia = {$row['num_cia']} AND tipo_serie = {$row['tipo_serie']} AND consecutivo = {$row['consecutivo']} ORDER BY id");

	foreach ($detalles as $i => $detalle)
	{
		$datos['detalle'][$i]['clave'] = $detalle['clave_producto'];
		$datos['detalle'][$i]['descripcion'] = utf8_decode($detalle['descripcion']);
		$datos['detalle'][$i]['cantidad'] = $detalle['cantidad'];
		$datos['detalle'][$i]['unidad'] = utf8_decode($detalle['unidad']);
		$datos['detalle'][$i]['precio'] = $detalle['precio'];
		$datos['detalle'][$i]['importe'] = $detalle['importe'];
		$datos['detalle'][$i]['descuento'] = $row['porcentaje_descuento'];
		$datos['detalle'][$i]['porcentaje_ieps'] = $detalle['pieps'];
		$datos['detalle'][$i]['importe_ieps'] = $detalle['ieps'];
		$datos['detalle'][$i]['porcentaje_iva'] = $detalle['piva'];
		$datos['detalle'][$i]['importe_iva'] = $detalle['iva'];
		$datos['detalle'][$i]['numero_pedimento'] = $detalle['numero_pedimento'];
		$datos['detalle'][$i]['fecha_entrada'] = $detalle['fecha_entrada'];
		$datos['detalle'][$i]['aduana_entrada'] = $detalle['aduana_entrada'];
	}

	generar_cfdi($row['num_cia'], $row['tipo_serie'], $row['consecutivo'], $ob_data->fecha_timbrado, $ob_data->uuid, $ob_data->no_certificado_digital, $ob_data->no_certificado_sat, $ob_data->sello_cfd, $ob_data->sello_sat, $ob_data->cadena_original, $ob_data->documento_xml, $datos_emisor, $datos);

	$db->query("
		UPDATE
			facturas_electronicas
		SET
			fecha_timbrado = '{$ob_data->fecha_timbrado}',
			uuid = '{$ob_data->uuid}',
			no_certificado_digital = '{$ob_data->no_certificado_digital}',
			no_certificado_sat = '{$ob_data->no_certificado_sat}',
			sello_cfd = '{$ob_data->sello_cfd}',
			sello_sat = '{$ob_data->sello_sat}',
			cadena_original = '{$ob_data->cadena_original}',
			documento_xml = '" . utf8_decode($ob_data->documento_xml) . "'
		WHERE
			id = {$row['id']}
	");

	echo utf8_encode("<p>@#" . ($j + 1) . " COMPROBANTE {$row['num_cia']}-{$datos_emisor['serie']}{$row['consecutivo']}</p>");

	if (isset($_REQUEST['error']))
	{
		$db->query("DELETE FROM facturas_electronicas_server_status_new WHERE num_cia = {$row['num_cia']} AND status = -622");
	}
}

