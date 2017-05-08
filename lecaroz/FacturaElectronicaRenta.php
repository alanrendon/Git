<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerLocal':
			$sql = '
				SELECT
					arr.id,
					nombre_local,
					cod_arrendador || \' \' || inm.nombre
						AS
							inmobiliaria,
					nombre_arrendatario
						AS
							arrendatario,
					COALESCE(renta_con_recibo, 0)
						AS
							renta,
					COALESCE(mantenimiento, 0)
						AS
							mantenimiento,
					COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)
						AS
							subtotal,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.16)::numeric, 2) + ROUND((COALESCE(mantenimiento, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva_renta,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(mantenimiento, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva_mantenimiento,
					COALESCE(agua, 0)
						AS
							agua,
					CASE
						WHEN retencion_iva = \'TRUE\' THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.10666666667)::numeric, 2)
						ELSE
							0
					END
						AS
							retencion_iva,
					CASE
						WHEN retencion_isr = \'TRUE\' THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.10)::numeric, 2)
						ELSE
							0
					END
						AS
							retencion_isr,
					COALESCE(renta_con_recibo, 0)
					+ COALESCE(mantenimiento, 0)
					+ CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.16)::numeric, 2) + ROUND((COALESCE(mantenimiento, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
					+ COALESCE(agua, 0)
					- CASE
						WHEN retencion_iva = \'TRUE\' THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.10666666667)::numeric, 2)
						ELSE
							0
					END
					- CASE
						WHEN retencion_isr = \'TRUE\' THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.10)::numeric, 2)
						ELSE
							0
					END
						AS
							total
				FROM
						catalogo_arrendatarios arr
					LEFT JOIN
						catalogo_arrendadores inm
							USING
								(
									cod_arrendador
								)
				WHERE
						status = 1
					AND
						num_local = ' . $_REQUEST['local'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$rec = $result[0];
				
				$rec['nombre_local'] = utf8_encode($rec['nombre_local']);
				$rec['inmobiliaria'] = utf8_encode($rec['inmobiliaria']);
				$rec['arrendatario'] = utf8_encode($rec['arrendatario']);
				
				$rec['renta'] = floatval($rec['renta']);
				$rec['mantenimiento'] = floatval($rec['mantenimiento']);
				$rec['subtotal'] = floatval($rec['subtotal']);
				$rec['iva'] = floatval($rec['iva']);
				$rec['iva_renta'] = floatval($rec['iva_renta']);
				$rec['iva_mantenimiento'] = floatval($rec['iva_mantenimiento']);
				$rec['agua'] = floatval($rec['agua']);
				$rec['retencion_iva'] = floatval($rec['retencion_iva']);
				$rec['retencion_isr'] = floatval($rec['retencion_isr']);
				$rec['total'] = floatval($rec['total']);
				
				echo json_encode($rec);
			}
		break;
		
		case 'registrar':
			include_once('includes/class.facturas.inc.php');
			
			/*
			@ Generar popup
			*/
			$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaRentaPopup.tpl');
			$tpl->prepare();
			
			$fac = new FacturasClass();
			
			/*
			@ Obtener datos para recibos de renta
			*/
			$sql = '
				SELECT
					arr.id,
					num_local
						AS
							local,
					homoclave
						AS
							num_cia,
					cod_arrendador
						AS
							inmobiliaria,
					nombre
						AS
							nombre_inmobiliaria,
					bloque,
					CASE
						WHEN tipo_local = 1 THEN
							\'LOCAL COMERCIAL\'
						WHEN tipo_local = 2 THEN
							\'VIVIEDA\'
					END
						AS
							tipo_local,
					TRIM(regexp_replace(direccion_local, \'\s+\', \' \', \'g\'))
						AS
							direccion_local,
					200000 + arr.id
						AS
							clave_cliente,
					nombre_arrendatario
						AS
							nombre_cliente,
					rfc,
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
					email
				FROM
						catalogo_arrendatarios arr
					LEFT JOIN
						catalogo_arrendadores imb
							USING
								(
									cod_arrendador
								)
				WHERE
					arr.id = ' . $_REQUEST['id'] . '
				ORDER BY
					num_cia,
					inmobiliaria,
					bloque,
					num_local
			';
			
			$result = $db->query($sql);
			
			$rec = $result[0];
			
			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$hora = date('H:i');
			
			$renta = get_val($_REQUEST['renta']);
			$mantenimiento = get_val($_REQUEST['mantenimiento']);
			$subtotal = get_val($_REQUEST['subtotal']);
			$iva = get_val($_REQUEST['iva']);
			$iva_renta = get_val($_REQUEST['iva_renta']);
			$iva_mantenimiento = get_val($_REQUEST['iva_mantenimiento']);
			$agua = get_val($_REQUEST['agua']);
			$retencion_iva = get_val($_REQUEST['retencion_iva']);
			$retencion_isr = get_val($_REQUEST['retencion_isr']);
			$total = get_val($_REQUEST['total']);
			
			$datos = array(
				'cabecera' => array(
					'num_cia'               => $rec['num_cia'],
					'clasificacion'         => 5,
					'fecha'                 => $fecha,
					'hora'                  => $hora,
					'clave_cliente'         => $rec['clave_cliente'],
					'nombre_cliente'        => $rec['nombre_cliente'],
					'rfc_cliente'           => $rec['rfc'],
					'calle'                 => $rec['calle'],
					'no_exterior'           => $rec['no_exterior'],
					'no_interior'           => $rec['no_interior'],
					'colonia'               => $rec['colonia'],
					'localidad'             => $rec['localidad'],
					'referencia'            => $rec['referencia'],
					'municipio'             => $rec['municipio'],
					'estado'                => $rec['estado'],
					'pais'                  => $rec['pais'],
					'codigo_postal'         => $rec['codigo_postal'],
					'email'                 => strtolower($rec['email']),
					'observaciones'         => 'POR RENTA DE ' . $rec['tipo_local'] . ' DEL INMUEBLE UBICADO EN ' . trim($rec['direccion_local']),
					'importe'               => $subtotal + $agua,
					'porcentaje_descuento'  => 0,
					'descuento'             => 0,
					'porcentaje_iva'        => $iva > 0 ? 16 : 0,
					'importe_iva'           => $iva,
					'aplicar_retenciones'   => $retencion_iva > 0 || $retencion_isr > 0 ? 'S' : 'N',
					'importe_retencion_isr' => $retencion_isr,
					'importe_retencion_iva' => $retencion_iva,
					'total'                 => $total
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
			
			if ($renta > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => $_REQUEST['concepto'] != '' ? '[RENTA] ' . utf8_decode($_REQUEST['concepto']) : 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
					'cantidad'         => 1,
					'unidad'           => 'SIN UNIDAD',
					'precio'           => $renta,
					'importe'          => $renta,
					'descuento'        => 0,
					'porcentaje_iva'   => $iva_renta > 0 ? 16 : 0,
					'importe_iva'      => $iva_renta,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}
			
			if ($mantenimiento > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => $_REQUEST['concepto'] != '' ? '[MANTENIMIENTO] ' . utf8_decode($_REQUEST['concepto']) : 'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
					'cantidad'         => 1,
					'unidad'           => 'SIN UNIDAD',
					'precio'           => $mantenimiento,
					'importe'          => $mantenimiento,
					'descuento'        => 0,
					'porcentaje_iva'   => $iva_mantenimiento > 0 ? 16 : 0,
					'importe_iva'      => $iva_mantenimiento,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}
			
			if ($agua > 0) {
				$datos['detalle'][] = array(
					'clave'            => 1,
					'descripcion'      => 'CUOTA DE RECUPERACION DE AGUA',
					'cantidad'         => 1,
					'unidad'           => 'SIN UNIDAD',
					'precio'           => $agua,
					'importe'          => $agua,
					'descuento'        => 0,
					'porcentaje_iva'   => 0,
					'importe_iva'      => 0,
					'numero_pedimento' => '',
					'fecha_entrada'    => '',
					'aduana_entrada'   => ''
				);
			}
			
			$status = $fac->generarFactura($_SESSION['iduser'], $rec['num_cia'], 2, $datos);
			
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
				
				$pieces = explode('-', $status);
				
				$folio = preg_replace("/\D/", '', $pieces[1]);
				
				$sql = '
					INSERT INTO
						recibos_rentas
							(
								num_recibo,
								renta,
								agua,
								mantenimiento,
								iva,
								isr_retenido,
								iva_retenido,
								neto,
								fecha,
								bloque,
								impreso,
								fecha_pago,
								local,
								status,
								iduser,
								tsins
							)
						VALUES
							(
								' . $folio . ',
								' . $renta . ',
								' . $agua . ',
								' . $mantenimiento . ',
								' . $iva . ',
								' . $retencion_isr . ',
								' . $retencion_iva . ',
								' . $total . ',
								\'' . $fecha . '\',
								' . $rec['bloque'] . ',
								\'TRUE\',
								\'' . $fecha . '\',
								' . $_REQUEST['id'] . ',
								1,
								' . $_SESSION['iduser'] . ',
								now()
							)
				' . ";\n";
				
				$db->query($sql);
			}
			
			echo $tpl->getOutputContent();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaElectronicaRenta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$tpl->printToScreen();
?>
