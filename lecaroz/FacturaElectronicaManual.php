<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cambiaCia':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre_cia
				FROM
					catalogo_companias cc
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						(MAX(fecha) + interval \'1 day\')::date
							AS
								fecha
					FROM
						facturas_electronicas
					WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
						AND
							tipo = 1
						AND
							status = 1
				';
				$tmp = $db->query($sql);
				
				if ($tmp[0]['fecha'] != '') {
					$fecha = $tmp[0]['fecha'];
				}
				else {
					$fecha = date('01/m/Y');
				}
				
				echo json_encode(array('nombre' => $result[0]['nombre_cia'], 'fecha' => $fecha));
			}
		break;
		
		case 'validarFecha':
			$sql = '
				SELECT
					id
				FROM
					facturas_electronicas
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						fecha = \'' . $_REQUEST['fecha'] . '\'
					AND
						tipo = 1
					AND
						status = 1
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo -1;
			}
		break;
		
		case 'registrar':
			include_once('includes/class.facturas.inc.php');
			
			/*
			@ Generar popup
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaManualPopup.tpl');
			$tpl->prepare();
			
			$fac = new FacturasClass();
			
			$datos = array(
				'cabecera' => array (
					'num_cia'               => $_REQUEST['num_cia'],
					'clasificacion'         => $_REQUEST['tipo'],
					'fecha'                 => $_REQUEST['fecha'],
					'hora'                  => date('H:i'),
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
					'observaciones'         => utf8_decode($_REQUEST['observaciones']),
					'importe'               => get_val($_REQUEST['subtotal']),
					'porcentaje_descuento'  => 0,
					'descuento'             => 0,
					'porcentaje_iva'        => get_val($_REQUEST['iva']) > 0 ? 16 : 0,
					'importe_iva'           => get_val($_REQUEST['iva']),
					'aplicar_retenciones'   => 'N',
					'importe_retencion_isr' => 0,
					'importe_retencion_iva' => 0,
					'total'                 => get_val($_REQUEST['total'])
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
			
			foreach ($_REQUEST['importe'] as $i => $importe) {
				if (get_val($importe) > 0) {
					$datos['detalle'][$i]['clave'] = $i + 1;
					$datos['detalle'][$i]['descripcion'] = utf8_decode($_REQUEST['descripcion'][$i]);
					$datos['detalle'][$i]['cantidad'] = get_val($_REQUEST['cantidad'][$i]);
					$datos['detalle'][$i]['unidad'] = utf8_decode($_REQUEST['unidad'][$i]);
					$datos['detalle'][$i]['precio'] = get_val($_REQUEST['precio'][$i]);
					$datos['detalle'][$i]['importe'] = get_val($_REQUEST['importe'][$i]);
					$datos['detalle'][$i]['descuento'] = 0;
					$datos['detalle'][$i]['porcentaje_iva'] = get_val($_REQUEST['iva']) > 0 ? 16 : 0;
					$datos['detalle'][$i]['importe_iva'] = get_val($_REQUEST['iva']) > 0 ? round(get_val($_REQUEST['importe'][$i]) * 0.16, 2) : 0;
					$datos['detalle'][$i]['numero_pedimento'] = '';
					$datos['detalle'][$i]['fecha_entrada'] = '';
					$datos['detalle'][$i]['aduana_entrada'] = '';
				}
			}
			
			$status = $fac->generarFactura($_SESSION['iduser'], $_REQUEST['num_cia'], 1, $datos);
			
			if ($status < 0) {
				$tpl->newBlock('error');
				$tpl->assign('status', $fac->ultimoError());
			}
			else {
				$tpl->newBlock('comprobante');
				$tpl->assign('filename', $status);
				
				$email_status = $fac->enviarEmail();
				
				if ($email_status !== TRUE) {
					$tpl->assign('status', '<div class="red">Error al enviar los comprobantes por correo electr&oacute;nico: "' . $email_status . '", le sugerimos descargar y enviar los archivos manualmente</div>');
				}
			}
			
			echo $tpl->getOutputContent();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaManual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
