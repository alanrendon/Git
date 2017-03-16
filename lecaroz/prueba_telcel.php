<?php

echo "\n<br/>(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n<br/>";

if ( ! isset($_REQUEST['recharger_number']) || $_REQUEST['recharger_number'] == '')
{
	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No has especificado el número de recarga");

	die;
}

if ( ! isset($_REQUEST['amount']) || $_REQUEST['amount'] <= 0)
{
	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No has especificado el monto de recarga");

	die;
}

function generate_random_string($length = 5)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$characters_length = strlen($characters);

	$random_string = '';

	for ($i = 0; $i < $length; $i++)
	{
		$random_string .= $characters[mt_rand(0, $characters_length - 1)];
	}

	return $random_string;
}

$ISOCodes = array(
	'00'	=> 'Aprobado',
	'01'	=> 'Teléfono no válido',
	'02'	=> 'Destino no disponible',
	'03'	=> 'Monto no válido',
	'04'	=> 'Teléfono no susceptible de abono',
	'05'	=> 'Transacción denegada',
	'06'	=> 'Mantenimiento Telcel en curso',
	'07'	=> 'Rechazo por tabla de transacciones llena',
	'08'	=> 'Rechazo por Time-out interno',
	'09'	=> 'Autorizador no disponible',
	'12'	=> 'Transacción inválida',
	'13'	=> 'Monto inválido',
	'14'	=> 'Cuenta inválida',
	'21'	=> 'No se tomó acción (anulaciones y reversos)',
	'30'	=> 'Error en formato del mensaje',
	'56'	=> 'Emisor no habilitado en el sistema',
	'57'	=> 'Transacción no permitida a esta cuenta',
	'58'	=> 'Servicio inválido. Transacción no permitida a la terminal',
	'89'	=> 'Terminal inválida',
	'91'	=> 'Emisor no responde',
	'94'	=> 'Número de secuencia duplicado',
	'96'	=> 'Error de sistema'
);

$DTExeptionCodes = array(
	'AlreadyExistTrace'			=> 'Identificador de venta ya recibido',
	'AlreadyVoided'				=> 'Venta ya anulada',
	'AuthorizationDenied'		=> 'El proveedor de fondos denegó la autorización',
	'CouldNotAuthorize'			=> 'El proveedor de fondos no responde',
	'DataAccess'				=> 'Error interno al acceder a la base de datos',
	'DoesntExist'				=> 'Código de proveedor o comercio no encontrado',
	'DuplicateRequest'			=> 'Se recibió una venta idéntica en los últimos minutos',
	'FraudValidation'			=> 'La venta no superó los controles antifraude',
	'InvalidArguments'			=> 'Parámetros incompletos o incorrectos',
	'InvalidLogin'				=> 'Usuario o contraseña no válidos',
	'NoAcquirerComponent'		=> 'Error de configuración interna',
	'NoCommission'				=> 'No se pudo determinar el tipo de comisión de la venta',
	'NoComponent'				=> 'Error interno al cargar componente de comisiones',
	'None'						=> 'Operación exitosa',
	'NotEnoughPermissions'		=> 'Usuario no habilitado para la operación',
	'NoProduct'					=> 'No se encontraron productos para el proveedor indicado',
	'NoProviderComponent'		=> 'Error de configuración interno',
	'NoStock'					=> 'Referencias virtuales agotadas para el proveedor',
	'ProviderDisabled'			=> 'Proveedor del servicio temporalmente inhabilitado',
	'RequestNotApproved'		=> 'El proveedor del servicio denegó la autorización',
	'SaleLimitNotEnough'		=> 'Saldo del comercio insuficiente para la venta',
	'SaveSale'					=> 'Error interno al guardar la venta',
	'StoreDisabled'				=> 'El comercio se encuentra inhabilitado',
	'Unhandled'					=> 'Error interno no determinado'
);

$DTSaleState = array(
	'InProcess'	=> 'Venta en proceso',
	'Approved'	=> 'Venta aprobada',
	'Voided'	=> 'Venta anulada',
	'Failed'	=> 'Venta fallida',
	'ToReverse'	=> 'Venta en proceso de reverso',
	'Reversed'	=> 'Venta reversada'
);

$timeout_count = 1;
$max_timeout_counts = 4;
ini_set('default_socket_timeout', 5);

$user = 'Lecaroz1';
$password = 'qIcmg8T3';
$store_id = 29000;
$provider_id = 1;
$amount = floatval($_REQUEST['amount']);
$recharger_number = $_REQUEST['recharger_number'];
$external_trace = generate_random_string() . time();

// -----------------------------------------------------------------------------

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Creando cliente SOAP");

// $ws_url = "http://189.203.240.219:16000/TAE_TelcelWS?wsdl";
$ws_url = "http://desarrollo.eactae.com:8088/TAE_TelcelWS?wsdl";

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) URI: {$ws_url}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) SOAP version: 1.2");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Connection timeout: 60 segundos");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de espera: " . ini_get('default_socket_timeout'));


try
{
	$client = new SoapClient($ws_url, array(
		'soap_version'			=> SOAP_1_2,
		'cache_wsdl'			=> WSDL_CACHE_NONE,
		'trace'					=> TRUE,
		'connection_timeout'	=> 60
	));

	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Conectado");
}
catch (SoapFault $e)
{
	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

	die();
}

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo servicio, llamando función SOAP 'GetVersion'");

try
{
	$response = $client->GetVersion();
}
catch (SoapFault $e)
{
	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

	die();
}

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Versión del servicio: {$response->GetVersionResult}");

$request_params = array(
	'user'				=> $user,
	'password'			=> $password,
	'storeId'			=> $store_id,
	'providerId'		=> $provider_id,
	'amount'			=> $amount,
	'rechargeNumber'	=> $recharger_number,
	'externalTrace'		=> $external_trace
);

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Realizando venta de tiempo aire");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Parámetros de envío");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) user: {$user}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) password: {$password}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) storeId: {$store_id}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) providerId: {$provider_id}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) amount: {$amount}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) rechargerNumber: {$recharger_number}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) externalTrace: {$external_trace}");

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Consumiendo servicio, llamando función SOAP 'DoStoreSale'");

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obteniendo estado de transacción, intento {$timeout_count}");

// try
// {
// 	$response = $client->FindSaleByTrace($request_params);
// }
// catch (SoapFault $e)
// {
// 	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

// 	die();
// }

// $result_value = $response->FindSaleByTraceResult->ResultValue;

// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) State: {$result_value->State} {$DTSaleState[$result_value->State]}");
// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) SaleId: {$result_value->SaleId}");
// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Date: {$result_value->Date}");
// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) AuthCode: {$result_value->AuthorizationCode}");
// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) VoidDate: {$result_value->VoidDate}");
// echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) FinantialAuthorizationCode: {$result_value->FinantialAuthorizationCode}");

// die;

try
{
	$response = $client->DoStoreSale($request_params);
}
catch (SoapFault $e)
{
	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

	$response = FALSE;
}

if ( ! $response)
{
	do
	{
		sleep(2);

		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obteniendo estado de transacción, intento {$timeout_count}");

		try
		{
			$response = $client->FindSaleByTrace($request_params);
		}
		catch (SoapFault $e)
		{
			echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

			$response = FALSE;
		}

		$timeout_count++;

		if ($response)
		{
			echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Find State: {$response->FindSaleByTraceResult->ResultValue->State}");
		}
	}
	while (( ! $response || $response->FindSaleByTraceResult->ResultValue->State == 'InProcess') && $timeout_count <= $max_timeout_counts);

	if ( ! $response)
	{
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Se ha alcanzado el máximo de reintentos ({$max_timeout_counts})");

		die;
	}
	else
	{
		$result_value = $response->FindSaleByTraceResult->ResultValue;

		if (isset($response->FindSaleByTraceResult->ErrorCode))
		{
			$error_code = $response->FindSaleByTraceResult->ErrorCode;

			echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) ErrorCode: {$error_code}: {$DTExeptionCodes[$error_code]}");
		}

		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) State: {$result_value->State} {$DTSaleState[$result_value->State]}");
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) SaleId: {$result_value->SaleId}");
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Date: {$result_value->Date}");
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) AuthCode: {$result_value->AuthorizationCode}");
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) VoidDate: {$result_value->VoidDate}");
		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) FinantialAuthorizationCode: {$result_value->FinantialAuthorizationCode}");

		// try
		// {
		// 	$response = $client->GetVersion();
		// }
		// catch (SoapFault $e)
		// {
		// 	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

		// 	die();
		// }

		die;
	}
}

if (isset($response->DoStoreSaleResult->ErrorCode))
{
	$error_code = $response->DoStoreSaleResult->ErrorCode;

	echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) ErrorCode: {$error_code}: {$DTExeptionCodes[$error_code]}");
}

$result_value = $response->DoStoreSaleResult->ResultValue;

echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) AccountBalance: {$result_value->AccountBalance}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Amount: " . (isset($result_value->Amount) ? $result_value->Amount : NULL));
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) AuthCode: " . (isset($result_value->AuthCode) ? $result_value->AuthCode : NULL));
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) CanRetry: {$result_value->CanRetry}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) IsOnlineSale: {$result_value->IsOnlineSale}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) ProviderResponseCode: {$result_value->ProviderResponseCode} " . (array_key_exists($result_value->ProviderResponseCode, $ISOCodes) ? $ISOCodes[$result_value->ProviderResponseCode] : 'Denegada'));
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) ProviderResponseMessage: {$result_value->ProviderResponseMessage}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) ProviderReference: {$result_value->ProviderReference}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Result: {$result_value->Result}");
echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) SaleId: {$result_value->SaleId}");

// if ( ! isset($response->DoStoreSaleResult->ErrorCode) && ( ! isset($result_value->AuthCode) || $result_value->AuthCode == ''))
// {
// 	do
// 	{
// 		try
// 		{
// 			$response = $client->FindSaleByTrace($request_params);
// 		}
// 		catch (SoapFault $e)
// 		{
// 			echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) {$e->faultcode} {$e->faultstring}");

// 			$response = FALSE;

// 			$timeout_count++;
// 		}
// 	}
// 	while ( (! $response || $response->FindSaleByTraceResult->ResultValue->State == 'InProcess') && $timeout_count <= $max_timeout_counts);

// 	if ( ! $response)
// 	{
// 		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Se ha alcanzado el máximo de reintentos ({$max_timeout_counts})");

// 		die;
// 	}
// 	else
// 	{
// 		$result_value = $response->FindSaleByTraceResult->ResultValue;

// 		echo utf8_decode("\n<br/>[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) AuthCode: {$result_value->AuthorizationCode}");

// 		die;
// 	}
// }
