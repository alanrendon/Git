<?php

// $ws_url = "http://192.168.99.4:8081/lecarozprueba/ws?wsdl";
$ws_url = "http://189.203.240.219:16000/TAE_TelcelWS?wsdl";

$client = new SoapClient($ws_url, array(
	'soap_version'	=> SOAP_1_2,
	'cache_wsdl'	=> WSDL_CACHE_NONE,
	'trace'			=> TRUE
));

echo "<pre>";

var_dump($client->__getFunctions());
var_dump($client->__getTypes());

// $client->__soapCall('GetVersion', array());

echo "</pre>";
