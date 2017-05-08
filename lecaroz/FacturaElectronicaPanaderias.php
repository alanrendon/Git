<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

function toInt($value)
{
	return intval($value, 10);
}

function utf8_encode_array($value)
{
	return utf8_encode($value);
}

function utf8_decode_array($value)
{
	return utf8_decode($value);
}

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'buscar_cliente':
			$sql = "
				SELECT
					*,
					CONCAT_WS(', ',
						CONCAT_WS(' ',
							calle,
							CASE WHEN TRIM(no_exterior) != '' THEN no_exterior ELSE NULL END,
							CASE WHEN TRIM(no_interior) != '' THEN no_interior ELSE NULL END
						),
						CASE WHEN TRIM(colonia) != '' THEN colonia ELSE NULL END,
						CASE WHEN TRIM(localidad) != '' THEN localidad ELSE NULL END,
						CASE WHEN TRIM(referencia) != '' THEN referencia ELSE NULL END,
						CASE WHEN TRIM(municipio) != '' THEN municipio ELSE NULL END,
						CASE WHEN TRIM(estado) != '' THEN estado ELSE NULL END,
						CASE WHEN TRIM(pais) != '' THEN pais ELSE NULL END,
						CASE WHEN TRIM(codigo_postal) != '' THEN 'CP ' || codigo_postal ELSE NULL END
					)
						AS domicilio,
					CONCAT_WS(', ',
						CASE WHEN TRIM(tipo_pago) IN ('4', 'B', '1', '2', 'K') THEN 'TIPO: ' || (
							CASE
								WHEN tipo_pago = '4' THEN 'NO IDENTIFICADO'
								WHEN tipo_pago = 'B' THEN 'EFECTIVO'
								WHEN tipo_pago = '1' THEN 'TRANSFERENCIA BANCARIA'
								WHEN tipo_pago = '2' THEN 'CHEQUE'
								WHEN tipo_pago = 'K' THEN 'TARJETA DE CREDITO'
							END
						) ELSE NULL END,
						CASE WHEN TRIM(cuenta_pago) != '' THEN 'CUENTA: ' || TRIM(cuenta_pago) ELSE NULL END,
						CASE WHEN condiciones_pago IN (1, 2) THEN 'CONDICIONES: ' || (
							CASE
								WHEN condiciones_pago = 1 THEN 'CONTADO'
								WHEN condiciones_pago = 2 THEN 'CREDITO'
							END
						) ELSE NULL END
					)
						AS opciones_pago
				FROM
					catalogo_clientes_facturas
				WHERE
					rfc = UPPER('" . utf8_encode($_REQUEST['rfc']) . "')
					AND num_cia = {$_REQUEST['num_cia']}
					AND tsbaja IS NULL
				ORDER BY
					id
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $i => $rec)
				{
					$result[$i] = array_map('utf8_decode_array', $rec);
				}

				header('Content-Type: application/json');

				echo json_encode($result);
			}

			break;

		case 'baja_cliente':
			$sql = "
				UPDATE
					catalogo_clientes_facturas
				SET
					tsbaja = NOW()
				WHERE
					id = {$_REQUEST['id']}
			";

			$db->query($sql);

			break;

		case 'registrar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			$fac = new FacturasClass();

			/*
			@ Obtener última fecha de facturación
			*/
			$sql = '
				SELECT
					COALESCE((
						CASE
							WHEN tipo = 1 THEN
								fecha + INTERVAL \'1 DAY\'
							ELSE
								fecha
						END
					), NOW())::date
						AS fecha
				FROM
					facturas_electronicas
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND tipo_serie = 1
					AND tipo IN (1, 2)
					AND status = 1
				ORDER BY
					fecha DESC
				LIMIT
					1
			';
			$result = $db->query($sql);

			if ($result && $result[0]['fecha'] != '') {
				$fecha = $result[0]['fecha'];

				$sql = '
					SELECT
						(MAX(fecha) + INTERVAL \'1 day\')::date
							AS fecha
					FROM
						facturas_electronicas_folios_reservados
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND tipo_serie = 1
						AND tsreg IS NULL
				';

				$result = $db->query($sql);

				if ($result && $result[0]['fecha'] != '') {
					$fecha = $result[0]['fecha'];
				}

				list($dia, $mes, $anio) = array_map('toInt', explode('/', $fecha));

				$tscia = mktime(0, 0, 0, $mes, $dia, $anio);

				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$tsfac = mktime(0, 0, 0, $mes, $dia, $anio);

				/*
				@ Si la fecha de solicitud es mayor a el último día de efectivos, reservar folios
				*/
				if ($tsfac > $tscia) {
					$dias_reserva = ($tsfac - $tscia) / 86400;

					for ($dias = 0; $dias < $dias_reserva; $dias++) {
						$fecha_reserva = date('d/m/Y', $tscia + $dias * 86400);

						if (!$fac->recuperarFolio($_REQUEST['num_cia'], 1, $fecha_reserva)) {
							$folio = $fac->reservarFolio(0, $_REQUEST['num_cia'], 1, $fecha_reserva);
						}
					}
				}
			}

			$datos = array(
				'cabecera' => array (
					'num_cia'               => $_REQUEST['num_cia'],
					'clasificacion'         => 2,
					'fecha'                 => $_REQUEST['fecha'],
					'hora'                  => date('H:i:s'),
					'clave_cliente'         => 1000,
					'nombre_cliente'        => utf8_decode($_REQUEST['nombre_cliente']),
					'rfc_cliente'           => utf8_decode($_REQUEST['rfc']),
					'calle'                 => utf8_decode($_REQUEST['calle']),
					'no_exterior'           => utf8_decode($_REQUEST['no_exterior']),
					'no_interior'           => utf8_decode($_REQUEST['no_interior']),
					'colonia'               => utf8_decode($_REQUEST['colonia']),
					'localidad'             => utf8_decode($_REQUEST['localidad']),
					'referencia'            => utf8_decode($_REQUEST['referencia']),
					'municipio'             => utf8_decode($_REQUEST['municipio']),
					'estado'                => utf8_decode($_REQUEST['estado']),
					'pais'                  => utf8_decode($_REQUEST['pais']),
					'codigo_postal'         => $_REQUEST['codigo_postal'],
					'email'                 => utf8_decode($_REQUEST['email_cliente']),
					'observaciones'         => '',
					'importe'               => get_val($_REQUEST['subtotal']),
					'porcentaje_descuento'  => 0,
					'descuento'             => 0,
					'ieps'                  => get_val($_REQUEST['total_ieps']),
					'porcentaje_iva'        => get_val($_REQUEST['iva']) > 0 ? 16 : 0,
					'importe_iva'           => get_val($_REQUEST['iva']),
					'aplicar_retenciones'   => 'N',
					'importe_retencion_isr' => 0,
					'importe_retencion_iva' => 0,
					'total'                 => get_val($_REQUEST['total']),
					'tipo_pago'             => $_REQUEST['tipo_pago'],
					'cuenta_pago'           => $_REQUEST['cuenta_pago'],
					'condiciones_pago'      => $_REQUEST['condiciones_pago'],
					'tipo_reporte'          => 'RptM_CFDI_lecaroz.jrxml'
				),
				'consignatario' => array (
					'nombre'        => '',
					'rfc'           => '',
					'calle'         => '',
					'no_exterior'   => '',
					'no_interior'   => '',
					'colonia'       => '',
					'localidad'     => '',
					'referencia'    => '',
					'municipio'     => '',
					'estado'        => '',
					'pais'          => '',
					'codigo_postal' => ''
				),
				'detalle' => array()
			);

			$row = 0;

			foreach ($_REQUEST['importe'] as $i => $importe) {
				if (get_val($importe) > 0 || get_val($_REQUEST['cantidad'][$i]) > 0) {
					$cantidad = get_val($_REQUEST['cantidad'][$i]);
					$precio = get_val($_REQUEST['importe'][$i]) / get_val($_REQUEST['cantidad'][$i]);

					$importe_ieps = isset($_REQUEST['aplicar_ieps']) && in_array($i, $_REQUEST['aplicar_ieps']) ? round(get_val($_REQUEST['importe'][$i]) * 0.08, 2) : 0;

					$importe_iva = isset($_REQUEST['aplicar_iva']) && in_array($i, $_REQUEST['aplicar_iva']) ? round((get_val($_REQUEST['importe'][$i]) + $importe_ieps) * 0.16, 2) : 0;

					$datos['detalle'][$row]['clave'] = $row + 1;
					$datos['detalle'][$row]['descripcion'] = utf8_decode($_REQUEST['descripcion'][$i]);
					$datos['detalle'][$row]['cantidad'] = get_val($_REQUEST['cantidad'][$i]);
					$datos['detalle'][$row]['unidad'] = utf8_decode($_REQUEST['unidad'][$i]);
					$datos['detalle'][$row]['precio'] = get_val($_REQUEST['importe'][$i]) / get_val($_REQUEST['cantidad'][$i]);
					$datos['detalle'][$row]['importe'] = get_val($_REQUEST['importe'][$i]);
					$datos['detalle'][$row]['descuento'] = 0;
					$datos['detalle'][$row]['porcentaje_ieps'] = isset($_REQUEST['aplicar_ieps']) && in_array($i, $_REQUEST['aplicar_ieps']) > 0 ? 8 : 0;
					$datos['detalle'][$row]['importe_ieps'] = $importe_ieps;
					$datos['detalle'][$row]['porcentaje_iva'] = isset($_REQUEST['aplicar_iva']) && in_array($i, $_REQUEST['aplicar_iva']) > 0 ? 16 : 0;
					$datos['detalle'][$row]['importe_iva'] = $importe_iva;
					$datos['detalle'][$row]['numero_pedimento'] = '';
					$datos['detalle'][$row]['fecha_entrada'] = '';
					$datos['detalle'][$row]['aduana_entrada'] = '';

					$row++;
				}
			}

			$status = $fac->generarFactura(0, $_REQUEST['num_cia'], 1, $datos);

			if ($status < 0) {
				header('Content-Type: application/json');

				echo json_encode(array(
					'status'	=> -1,
					'error'		=> $fac->ultimoError()
				));

				$fac->enviarEmailError();
			}
			else {
				$email_status = $fac->enviarEmail();

				header('Content-Type: application/json');

				echo json_encode(array(
					'status'		=> 1,
					'comprobante'	=> $status,
					'email_status'	=> $email_status !== TRUE ? FALSE : TRUE,
					'email_error'	=> $email_status !== TRUE ? $email_status : NULL
				));

				if ($id = $db->query('
					SELECT
						id
					FROM
						catalogo_clientes_facturas
					WHERE
						rfc = \'' . utf8_decode($_REQUEST['rfc']) . '\'
						AND num_cia = ' . $_REQUEST['num_cia'] . '
						AND nombre = \'' . utf8_decode($_REQUEST['nombre_cliente']) . '\'
						AND calle = \'' . utf8_decode($_REQUEST['calle']) . '\'
						AND no_exterior = \'' . utf8_decode($_REQUEST['no_exterior']) . '\'
						AND no_interior = \'' . utf8_decode($_REQUEST['no_interior']) . '\'
						AND colonia = \'' . utf8_decode($_REQUEST['colonia']) . '\'
						AND localidad = \'' . utf8_decode($_REQUEST['localidad']) . '\'
						AND referencia = \'' . utf8_decode($_REQUEST['referencia']) . '\'
						AND municipio = \'' . utf8_decode($_REQUEST['municipio']) . '\'
						AND estado = \'' . utf8_decode($_REQUEST['estado']) . '\'
						AND pais = \'' . utf8_decode($_REQUEST['pais']) . '\'
						AND codigo_postal = \'' . utf8_decode($_REQUEST['codigo_postal']) . '\'
						AND tsbaja IS NULL
				')) {
					$db->query('
						UPDATE
							catalogo_clientes_facturas
						SET
							email = \'' . utf8_decode($_REQUEST['email_cliente']) . '\',
							tipo_pago = ' . ($_REQUEST['tipo_pago'] != '' ? '\'' . $_REQUEST['tipo_pago'] . '\'' : 'NULL') . ',
							cuenta_pago = ' . ($_REQUEST['cuenta_pago'] != '' ? '\'' . $_REQUEST['cuenta_pago'] . '\'' : 'NULL') . ',
							condiciones_pago = ' . ($_REQUEST['condiciones_pago'] > 0 ? $_REQUEST['condiciones_pago'] : 'NULL') . ',
							tsmod = NOW()
						WHERE
							id = ' . $id[0]['id'] . '
					');
				}
				else {
					$db->query('
						INSERT INTO
							catalogo_clientes_facturas (
								rfc,
								num_cia,
								nombre,
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
								email,
								tipo_pago,
								cuenta_pago,
								condiciones_pago
							)
						VALUES (
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							' . $_REQUEST['num_cia'] . ',
							\'' . utf8_decode($_REQUEST['nombre_cliente']) . '\',
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
							' . ($_REQUEST['tipo_pago'] != '' ? '\'' . $_REQUEST['tipo_pago'] . '\'' : 'NULL') . ',
							' . ($_REQUEST['cuenta_pago'] != '' ? '\'' . $_REQUEST['cuenta_pago'] . '\'' : 'NULL') . ',
							' . ($_REQUEST['condiciones_pago'] > 0 ? $_REQUEST['condiciones_pago'] : 'NULL') . '
						)
					');
				}
			}

			break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaPanaderias.tpl');
$tpl->prepare();

$tpl->assign('fecha', date('d/m/Y'));

$sql = "
	SELECT
		num_cia,
		nombre_corto
	FROM
		catalogo_companias
	WHERE
		num_cia = {$_REQUEST['num_cia']}
	ORDER BY
		num_cia
";

$cias = $db->query($sql);

if ($cias)
{
	foreach ($cias as $c)
	{
		$tpl->newBlock('num_cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', utf8_decode($c['nombre_corto']));
	}
}

$sql = "
	SELECT
		UPPER(\"Entidad\")
			AS estado
	FROM
		catalogo_entidades
	ORDER BY
		idob
";

$estados = $db->query($sql);

if ($estados)
{
	foreach ($estados as $e)
	{
		$tpl->newBlock('estado');
		$tpl->assign('estado', utf8_decode($e['estado']));
	}
}

$tpl->printToScreen();
?>
