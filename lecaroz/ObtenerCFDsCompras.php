<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');

$options = getopt("", array(
	'fecha1:',
	'fecha2:',
	'cias:',
	'pros:',
	'nvf',
	'no_msg',
	'help'
));

if (isset($_REQUEST['help']) || isset($options['help']))
{
	echo "ObtenerCFDsCompras.php Ver 1.15.11.19";
	echo "\nCopyright (c) 2015, Lecaroz";
	echo "\n\nModo de empleo: php ObtenerCFDsCompras.php [OPCIONES]";
	echo "\nObtiene los comprobantes de compra del OpenBravo para actualizar los del sistema de Lecaroz.";
	echo "\n\nLos argumentos obligatorios para las opciones largas son también obligatorios\npara las opciones cortas.";
	echo "\n\n  --help\t\tmuestra esta ayuda y finaliza";
	echo "\n\n  --fecha1=FECHA1\tfecha de inicio de búsqueda de registros de facturas";
	echo "\n\n  --fecha2=FECHA2\tfecha de término de búsqueda de registros de facturas";
	echo "\n\n  --cias=CIAS\t\tcompañías, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros de facturas";
	echo "\n\n  --pros=PROS\t\tproveedores, separados por comas (1,2,3,...) o rangos\n\t\t\t(1-5,20-26,...) de búsqueda de registros de facturas";
	echo "\n\n  --nvf\t\t\tno validar si los comprobantes ya han sido descargados\n\t\t\tde openbravo";
	echo "\n\n  --no_msg\t\tno generar mensajes informativos";
	echo "\n\nComunicar de errores en el script a carlos.candelario@lecaroz.com";
	echo "\n\n";

	die;
}

$nvf = isset($_REQUEST['nvf']) || isset($options['nvf']) ? TRUE : FALSE;
$no_msg = isset($_REQUEST['no_msg']) || isset($options['no_msg']) ? TRUE : FALSE;

echo "\n(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Parámetros de búsqueda";

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

if (isset($params['fecha1']) || isset($params['fecha2']))
{
	$condiciones[] = "f.fecha BETWEEN '{$params['fecha1']}' AND '{$params['fecha2']}'";
}
else
{
	$condiciones[] = "f.fecha >= NOW()::DATE - INTERVAL '2 MONTHS'";
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

	$pieces = explode(',', $params['pros']);
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

if ( ! $nvf)
{
	$condiciones[] = '(f.xml_file IS NULL OR TRIM(f.xml_file) = \'\')';
}

$condiciones_string = implode(' AND ', $condiciones);

$sql = "SELECT
	COUNT(id) AS num_rows
FROM
(
	SELECT
		f.id
	FROM
		facturas f
		LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
		LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
	WHERE
		{$condiciones_string}
) AS result_facturas";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Ejecutando consulta a la base de datos";

$result = $db->query($sql);

if ($result[0]['num_rows'] == 0)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) No hay resultados, terminando ejecución de script.";

	die;
}

$num_rows = $result[0]['num_rows'];

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Facturas a buscar: {$num_rows}";

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Conectando a la base de datos de OpenBravo";

// $dbob = new DBclass('pgsql://postgres:bell@192.168.99.4:5433/sat', 'autocommit=yes,encoding=UTF8');
$dbob = new DBclass('pgsql://postgres:bell@192.168.1.2:5432/sat1', 'autocommit=yes,encoding=UTF8');

$rows_per_query = 500;

$offset = 0;

$cont = 0;

$ids = array();

$query_time = -microtime(TRUE);
$total_query_time = -microtime(TRUE);

while ($datos_facturas = $db->query("SELECT
	f.id,
	f.num_cia,
	cc.nombre_corto AS nombre_cia,
	cc.rfc AS rfc_cia,
	f.num_proveedor AS num_pro,
	cp.nombre AS nombre_pro,
	cp.rfc AS rfc_pro,
	f.fecha,
	f.num_fact,
	f.total
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
	$query_time += microtime(TRUE);

	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo de consulta: {$query_time} segundos";

	foreach ($datos_facturas as $i => $df)
	{
		$cont++;

		$ts = date('Y-m-d H:i:s.') . substr(microtime(), 2, 6);

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "]++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Registro: {$cont} de {$num_rows}";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) ID: {$df['id']}";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Compañía: {$df['num_cia']} " . utf8_encode($df['nombre_cia']) . " (" . utf8_encode($df['rfc_cia']) . ")";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Proveedor: {$df['num_pro']} " . utf8_encode($df['nombre_pro']) . " (" . utf8_encode($df['rfc_pro']) . ")";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Fecha: {$df['fecha']}";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Factura: {$df['num_fact']}";
		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](DD) Total: {$df['total']}";

		echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Buscando comprobante de OpenBravo";

		$ob_result = $dbob->query(utf8_encode("SELECT
			em.rfc AS rfc_emisor,
			re.rfc AS rfc_receptor,
			com.no_comprobante,
			com.total,
			com.xml
		FROM
			comprobante32 com
			LEFT JOIN emisor em ON (em.id = com.emisor_id)
			LEFT JOIN receptor re ON (re.id = com.receptor_id)
		WHERE
			UPPER(em.rfc) = UPPER('{$df['rfc_pro']}')
			AND UPPER(re.rfc) = UPPER('{$df['rfc_cia']}')
			AND UPPER(com.no_comprobante) = UPPER('{$df['num_fact']}')"));

		if ($ob_result)
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Comprobante encontrado, comparando importes";

			if (abs($df['total'] - $ob_result[0]['total']) <= 1)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Los importes coinciden";

				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Guardando comprobante en el sistema";

				if (file_put_contents(dirname(__FILE__) . '/cfds_proveedores/cfd_' . $df['id'] . '.xml', $ob_result[0]['xml']) === FALSE)
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Error al intentar guardar el documento XML";
				}
				else
				{
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Documento cfd_{$df['id']}.xml almacenado con exito";

					$ids[] = $df['id'];
				}
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Los importes no coinciden (SISTEMA {$df['total']} != OB {$ob_result[0]['total']})";
			}
		}
		else
		{
			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Comprobante no encontrado";
		}
	}

	$offset += $rows_per_query;

	$query_time = -microtime(TRUE);
}

if ($ids)
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Actualizando marcas de tiempo de " . count($ids) . " registros";

	$db->query("UPDATE facturas
	SET xml_file = 'cfd_' || id || '.xml'
	WHERE
		id IN (" . implode(', ', $ids) . ")");
}

$total_query_time += microtime(TRUE);

// Enviar correo electrónico con los resultados

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Enviando correo informativo";

// Validar que la librería PHPMailer este cargada
if ( ! class_exists('PHPMailer'))
{
	include_once(dirname(__FILE__) . '/includes/phpmailer/class.phpmailer.php');
}

$mail = new PHPMailer();

$mail->IsSMTP();
$mail->Host = 'mail.lecaroz.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = 'sistemas@lecaroz.com';
$mail->Password = 'L3c4r0z*';

$mail->From = 'facturas.electronicas@lecaroz.com';
$mail->FromName = utf8_decode('Lecaroz :: Sistemas');

$mail->AddAddress('marioal@yaznicotelecom.com');
// $mail->AddAddress('carlos.candelario@lecaroz.com');

$ts = date('Y-m-d H:i:s');

$mail->Subject = "Comprobantes de compras encontrados [{$ts}]";

$mail->Body = "[{$ts}]";
$mail->Body .= "\n\nComprobantes buscados: " . number_format($num_rows);
$mail->Body .= "\nComprobantes encontrados: " . number_format(count($ids));

$mail->IsHTML(FALSE);

if( ! $mail->Send())
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) Error al enviar correo informativo: {$mail->ErrorInfo}";
}
else
{
	echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Correo enviado con éxito";
}

echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Tiempo total de ejecución: {$total_query_time}  segundo(s)\n";
