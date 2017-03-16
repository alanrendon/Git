<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$data = array();

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obtener proveedores... ");

if ($result = $db->query("SELECT
	num_proveedor AS num_pro,
	nombre AS nombre_pro,
	pass_site AS password
FROM
	catalogo_proveedores
WHERE
	TRIM(pass_site) <> ''
ORDER BY
	num_proveedor"))
{
	foreach ($result as $row)
	{
		$data['proveedores'][] = array_map('utf8_decode', $row);
	}

	echo utf8_decode("OK (" . count($result) . " registro(s))");
}
else
{
	echo utf8_decode("OK (0 registro(s))");
}

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obtener compañías... ");

if ($result = $db->query("SELECT
	num_cia,
	nombre AS nombre_cia
FROM
	catalogo_companias
WHERE
	tipo_cia IN (1, 2, 3, 5, 6)
ORDER BY
	num_cia"))
{
	foreach ($result as $row)
	{
		$data['companias'][] = array_map('utf8_decode', $row);
	}

	echo utf8_decode("OK (" . count($result) . " registro(s))");
}
else
{
	echo utf8_decode("OK (0 registro(s))");
}

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obtener pagos... ");

if ($result = $db->query("SELECT
	c.num_proveedor AS num_pro,
	c.num_cia,
	cc.nombre_corto AS nombre_cia,
	c.cuenta AS banco,
	c.folio,
	c.fecha,
	c.facturas,
	c.importe,
	c.fecha_cancelacion AS cancelado
FROM
	cheques c
	LEFT JOIN catalogo_companias cc USING (num_cia)
WHERE
	c.site = TRUE
	AND c.importe > 0
ORDER BY
	c.num_proveedor,
	c.num_cia,
	c.cuenta,
	c.folio"))
{
	foreach ($result as $row)
	{
		$data['pagos'][] = array_map('utf8_decode', $row);
	}

	echo utf8_decode("OK (" . count($result) . " registro(s))");
}
else
{
	echo utf8_decode("OK (0 registro(s))");
}

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obtener pendientes... ");

if ($result = $db->query("SELECT
	pp.num_proveedor AS num_pro,
	pp.num_fact,
	pp.fecha,
	pp.num_cia,
	cc.nombre_corto AS nombre_cia,
	pp.total AS importe
FROM
	pasivo_proveedores pp
	LEFT JOIN catalogo_companias cc USING (num_cia)
ORDER BY
	pp.num_proveedor,
	pp.num_cia,
	pp.num_fact"))
{
	foreach ($result as $row)
	{
		$data['pendientes'][] = array_map('utf8_decode', $row);
	}

	echo utf8_decode("OK (" . count($result) . " registro(s))");
}
else
{
	echo utf8_decode("OK (0 registro(s))");
}

$ws_url = "http://proveedores.lecaroz.com/proveedores/index.php/sincronizar_ws?wsdl";
// $ws_url = "http://localhost/proveedores/index.php/sincronizar_ws?wsdl";

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Conectar al webservice de sincronización");
echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) URI: {$ws_url}");
echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) SOAP version: 1.2");
echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Connection timeout: 60 segundos");
echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de espera: " . ini_get('default_socket_timeout'));
echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Conectando... ");

try
{
	$client = new SoapClient($ws_url, array(
		'soap_version'			=> SOAP_1_1,
		'cache_wsdl'			=> WSDL_CACHE_NONE,
		'trace'					=> TRUE,
		'connection_timeout'	=> 60
	));

	echo utf8_decode("OK");
}
catch (SoapFault $e)
{
	echo utf8_decode("ERROR");

	echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

	die();
}

echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo servicio, llamando función SOAP 'sincronizar'");

$request = json_encode($data);

try
{
	$response = $client->sincronizar($request);

	echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Sincronización exitosa");

	echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de sincronización");

	$db->query("UPDATE cheques SET site = FALSE, tssite = NOW() WHERE site = TRUE");
}
catch (SoapFault $e)
{
	echo utf8_decode("\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

	die();
}
