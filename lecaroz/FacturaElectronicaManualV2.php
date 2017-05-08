<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if (!in_array($_SESSION['iduser'], array(1/*, 10, 34*/))) die('EN PROCESO DE ACTUALIZACION.');

function toInt($value) {
	return intval($value, 10);
}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cambiaCia':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias cc
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);

			if ($result) {
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
				$tmp = $db->query($sql);

				echo json_encode(array('nombre' => utf8_encode($result[0]['nombre_cia']), 'fecha' => $tmp[0]['fecha']));
			}
		break;

		case 'validarFecha':
			$sql = '
				SELECT
					CASE
						WHEN \'' . $_REQUEST['fecha'] . '\'::DATE < COALESCE(MAX(fecha) + INTERVAL \'1 DAY\', NOW())::DATE THEN
							-1
						WHEN \'' . $_REQUEST['fecha'] . '\'::DATE > NOW()::DATE THEN
							1
						ELSE
							0
					END
						AS status
				FROM
					facturas_electronicas
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND tipo = 1
					AND status = 1
			';
			$result = $db->query($sql);

			if ($result) {
				if ($result[0]['status'] == 0) {
					$sql = '
						SELECT
							CASE
								WHEN \'' . $_REQUEST['fecha'] . '\'::DATE < (MAX(fecha) + INTERVAL \'1 DAY\')::DATE THEN
									-1
								ELSE
									0
							END
								AS status
						FROM
							facturas_electronicas_folios_reservados
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
							AND tipo_serie = 1
							AND tsreg IS NULL
					';

					$result = $db->query($sql);
				}

				echo $result[0]['status'];
			}
		break;

		case 'registrar':
			// include_once('includes/class.facturas.v2.inc.php');
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			/*
			@ Generar popup
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaManualPopup.tpl');
			$tpl->prepare();

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

			if ($_REQUEST['num_cia'] < 900 && $result && $result[0]['fecha'] != '' && $_REQUEST['tipo'] == 2) {
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
					'clasificacion'         => $_REQUEST['tipo'] == 6 && $_REQUEST['rfc'] == 'XAXX010101000' ? 1 : $_REQUEST['tipo'],
					'fecha'                 => $_REQUEST['fecha'],
					'hora'                  => date('H:i:s'),
					'clave_cliente'         => $_REQUEST['tipo'] == 6 && $_REQUEST['rfc'] == 'XAXX010101000' ? 1 : 1000,
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
					'observaciones'         => utf8_decode($_REQUEST['observaciones']),
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
					'tipo_reporte'          => $_REQUEST['tipo_reporte'] == 2 ? 'RptM_Facturae_largo_CFDI.jrxml' : (isset($_REQUEST['long_obs']) ? 'RptM_Facturae_observa_CFDI.jrxml' : 'RptM_CFDI_lecaroz.jrxml'
					/*'tipo_reporte'          => $_REQUEST['tipo_reporte'] == 2 ? 'RptM_Facturae_largo.jrxml' : (isset($_REQUEST['long_obs']) ? 'RptM_Facturae_observa.jrxml' : 'RptM_Facturae.jrxml'*/)
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

			$status = $fac->generarFactura($_SESSION['iduser'], $_REQUEST['num_cia'], $_REQUEST['num_cia'] >= 600 && $_REQUEST['num_cia'] <= 699 ? 2 : 1, $datos);

			if ($status < 0) {
				$tpl->newBlock('error');
				$tpl->assign('status', $fac->ultimoError());
			}
			else {
				$tpl->newBlock('comprobante');
				$tpl->assign('filename', $status);

				/*
				[18-Sep-2012] Insertar o actualizar datos del cliente
				*/
				if ($id = $db->query('
					SELECT
						idcliente
					FROM
						facturas_electronicas_clientes
					WHERE
						rfc_cliente = \'' . utf8_decode($_REQUEST['rfc']) . '\'
						AND tsbaja IS NULL
				')) {
					$db->query('
						UPDATE
							facturas_electronicas_clientes
						SET
							nombre_cliente = \'' . utf8_decode($_REQUEST['nombre_cliente']) . '\',
							tipo_pago = ' . ($_REQUEST['tipo_pago'] != '' ? '\'' . $_REQUEST['tipo_pago'] . '\'' : 'NULL') . ',
							cuenta_pago = ' . ($_REQUEST['cuenta_pago'] != '' ? '\'' . $_REQUEST['cuenta_pago'] . '\'' : 'NULL') . ',
							condiciones_pago = ' . ($_REQUEST['condiciones_pago'] > 0 ? $_REQUEST['condiciones_pago'] : 'NULL') . ',
							tsmod = NOW(),
							idmod = ' . $_SESSION['iduser'] . '
						WHERE
							idcliente = ' . $id[0]['idcliente'] . '
					');
				}
				else {
					$db->query('
						INSERT INTO
							facturas_electronicas_clientes (
								nombre_cliente,
								rfc_cliente,
								tipo_pago,
								cuenta_pago,
								condiciones_pago,
								tsalta,
								idalta
							)
						VALUES (
							\'' . utf8_decode($_REQUEST['nombre_cliente']) . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							' . ($_REQUEST['tipo_pago'] != '' ? '\'' . $_REQUEST['tipo_pago'] . '\'' : 'NULL') . ',
							' . ($_REQUEST['cuenta_pago'] != '' ? '\'' . $_REQUEST['cuenta_pago'] . '\'' : 'NULL') . ',
							' . ($_REQUEST['condiciones_pago'] > 0 ? $_REQUEST['condiciones_pago'] : 'NULL') . ',
							NOW(),
							' . $_SESSION['iduser'] . '
						)
					');
				}

				$email_status = $fac->enviarEmail();

				if ($email_status !== TRUE) {
					$tpl->assign('status', '<div class="red">Error al enviar los comprobantes por correo electr&oacute;nico: "' . $email_status . '", le sugerimos descargar y enviar los archivos manualmente</div>');
				}
			}

			echo $tpl->getOutputContent();

		break;
		case 'validarProd':
			$sql="SELECT cat.cod_producto,cat.nombre,cat.precio FROM catalogo_productos as cat WHERE cat.cod_producto =".$_REQUEST['producto'];
			$result = $db->query($sql);

			if ($result) {
				$data = array();

				$num_pro = NULL;
				foreach ($result as $rec) {
					$data = array(
						'nombre' => utf8_encode($rec['nombre']),
						'precio' => utf8_encode($rec['precio'])
					);
					
				}

				echo json_encode($data);
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaManualV2.tpl');
$tpl->prepare();

$sql = '
	SELECT catp.clave_sat,catp.label FROM catp ORDER BY catp.label asc
';

$result = $db->query($sql);
$cad="";
if ($result) {

	foreach ($result as $key ) {
		$cad.='<option value="'.$key["clave_sat"].'" >'.$key["label"].'</option>';
	}
}

$tpl->assign('pay_met', $cad);

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
