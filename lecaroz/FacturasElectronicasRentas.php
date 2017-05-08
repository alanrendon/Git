<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
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
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasRentasInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected');
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			
			$condiciones[] = 'status = 1';
			
			$condiciones[] = 'recibo_mensual = \'TRUE\'';
			
			$condiciones[] = 'renta_con_recibo > 0';
			
			$condiciones[] = '
				arr.id NOT IN (
					SELECT
						local
					FROM
						recibos_rentas
					WHERE
							fecha = \'' . $fecha . '\'
						AND
							status = 1
				)
			';
			
			$sql = '
				SELECT
					arr.id,
					num_local
						AS
							local,
					homoclave,
					cod_arrendador
						AS
							inmobiliaria,
					nombre
						AS
							nombre_inmobiliaria,
					bloque,
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
							ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva,
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
							ROUND(((COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16)::numeric, 2)
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
							total,
					fecha_inicio,
					fecha_final
				FROM
						catalogo_arrendatarios arr
					LEFT JOIN
						catalogo_arrendadores imb
							USING
								(
									cod_arrendador
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					homoclave,
					inmobiliaria,
					bloque,
					num_local
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasRentasResultado.tpl');
				$tpl->prepare();
				
				$tpl->assign('anio', $_REQUEST['anio']);
				$tpl->assign('mes', $_REQUEST['mes']);
				$tpl->assign('mes_escrito', $_meses[$_REQUEST['mes']]);
				
				$inmobiliaria = NULL;
				foreach ($result as $rec) {
					if ($inmobiliaria != $rec['inmobiliaria']) {
						$inmobiliaria = $rec['inmobiliaria'];
						
						$tpl->newBlock('inmobiliaria');
						$tpl->assign('inmobiliaria', $rec['inmobiliaria']);
						$tpl->assign('nombre_inmobiliaria', utf8_encode($rec['nombre_inmobiliaria']));
						
						$bloque = NULL;
					}
					
					if ($bloque != $rec['bloque']) {
						$bloque = $rec['bloque'];
						
						$tpl->newBlock('bloque');
						$tpl->assign('bloque', $rec['bloque'] == 1 ? 'INTERNAS' : 'EXTERNAS');
						
						$color = FALSE;
					}
					
					$tpl->newBlock('arrendatario');
					$tpl->assign('color', $color ? 'on' : 'off');
					
					$color = !$color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('inmobiliaria', $inmobiliaria);
					
					$tpl->assign('local', $rec['local']);
					$tpl->assign('arrendatario', utf8_encode($rec['arrendatario']));
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'generar':
			include_once('includes/class.facturas.inc.php');
			
			$fac = new FacturasClass();
			
			$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$hora = date('H:i');
			
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
							emisor,
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
					email,
					COALESCE(renta_con_recibo, 0)
						AS
							renta,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(renta_con_recibo, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva_renta,
					COALESCE(mantenimiento, 0)
						AS
							mantenimiento,
					CASE
						WHEN tipo_local = 1 THEN
							ROUND((COALESCE(mantenimiento, 0) * 0.16)::numeric, 2)
						ELSE
							0
					END
						AS
							iva_mantenimiento,
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
						catalogo_arrendadores imb
							USING
								(
									cod_arrendador
								)
				WHERE
					arr.id IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					emisor,
					inmobiliaria,
					bloque,
					num_local
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasRentasReporte.tpl');
			$tpl->prepare();
			
			$tpl->assign('anio', $_REQUEST['anio']);
			$tpl->assign('mes_escrito', $_meses[$_REQUEST['mes']]);
			
			$emisor = NULL;
			foreach ($result as $rec) {
				if ($emisor != $rec['emisor']) {
					$emisor = $rec['emisor'];
					
					$tpl->newBlock('emisor');
					$tpl->assign('emisor', $emisor);
					$tpl->assign('nombre_emisor', utf8_encode($rec['nombre_inmobiliaria']));
					
					$color = FALSE;
				}
				
				$tpl->newBlock('row');
				$tpl->assign('color', $color ? 'on' : 'off');
				
				$color = !$color;
				
				$tpl->assign('local', $rec['local']);
				$tpl->assign('arrendatario', utf8_encode($rec['nombre_cliente']));
				$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('subtotal', $rec['subtotal'] ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
				$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');
				
				$datos = array(
					'cabecera' => array(
						'num_cia'               => $emisor,
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
						'importe'               => $rec['subtotal'] + $rec['agua'],
						'porcentaje_descuento'  => 0,
						'descuento'             => 0,
						'porcentaje_iva'        => $rec['iva'] > 0 ? 16 : 0,
						'importe_iva'           => $rec['iva'],
						'aplicar_retenciones'   => $rec['retencion_isr'] > 0 || $rec['retencion_iva'] > 0 ? 'S' : 'N',
						'importe_retencion_isr' => $rec['retencion_isr'],
						'importe_retencion_iva' => $rec['retencion_iva'],
						'total'                 => $rec['total']
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
				
				if ($rec['renta'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						'descripcion'      => 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
						'cantidad'         => 1,
						'unidad'           => 'SIN UNIDAD',
						'precio'           => $rec['renta'],
						'importe'          => $rec['renta'],
						'descuento'        => 0,
						'porcentaje_iva'   => $rec['iva_renta'] > 0 ? 16 : 0,
						'importe_iva'      => $rec['iva_renta'],
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}
				
				if ($rec['mantenimiento'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						'descripcion'      => 'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'],
						'cantidad'         => 1,
						'unidad'           => 'SIN UNIDAD',
						'precio'           => $rec['mantenimiento'],
						'importe'          => $rec['mantenimiento'],
						'descuento'        => 0,
						'porcentaje_iva'   => $rec['iva_mantenimiento'] > 0 ? 16 : 0,
						'importe_iva'      => $rec['iva_mantenimiento'],
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}
				
				if ($rec['agua'] > 0) {
					$datos['detalle'][] = array(
						'clave'            => 1,
						'descripcion'      => 'CUOTA DE RECUPERACION DE AGUA',
						'cantidad'         => 1,
						'unidad'           => 'SIN UNIDAD',
						'precio'           => $rec['agua'],
						'importe'          => $rec['agua'],
						'descuento'        => 0,
						'porcentaje_iva'   => 0,
						'importe_iva'      => 0,
						'numero_pedimento' => '',
						'fecha_entrada'    => '',
						'aduana_entrada'   => ''
					);
				}
				
				$status = $fac->generarFactura($_SESSION['iduser'], $emisor, 2, $datos);
				
				if ($status < 0) {
					$tpl->assign('folio', '&nbsp;');
					$tpl->assign('status', '<span style="color:#C00;">' . $fac->ultimoError() . '</span>');
				}
				else {
					$pieces = explode('-', $status);
					
					$folio = preg_replace("/\D/", '', $pieces[1]);
					
					$tpl->assign('folio', $folio);
					$tpl->assign('status', '<span style="color:#060;">OK</span>');
					
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
									\'' . $folio . '\',
									' . $rec['renta'] . ',
									' . $rec['agua'] . ',
									' . $rec['mantenimiento'] . ',
									' . $rec['iva'] . ',
									' . $rec['retencion_isr'] . ',
									' . $rec['retencion_iva'] . ',
									' . $rec['total'] . ',
									\'' . $fecha . '\',
									' . $rec['bloque'] . ',
									\'TRUE\',
									\'' . $fecha . '\',
									' . $rec['id'] . ',
									1,
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";
					
					$sql .= '
						UPDATE
							facturas_electronicas
						SET
							idlocal = ' . $rec['id'] . '
						WHERE
							num_cia = ' . $emisor . '
							AND tipo_serie = 2
							AND consecutivo = ' . $folio . '
					' . ";\n";
					
					$db->query($sql);
					
					$fac->enviarEmail();
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'generar_old':
			/*
			@ Parámetros de conexión FTP
			*/
			$ftp_server = '192.168.1.70';
			$ftp_user = 'mollendo';
			$ftp_pass = 'L3c4r0z*';
			
			/*
			@ Validar que el servidor de facturas electrónicas se encuentre disponible
			*/
			$sql = '
				SELECT
					status
				FROM
					facturas_electronicas_server_status
				WHERE
					empresa = ' . $_SESSION['tipo_usuario'] . '
			';
			$server = $db->query($sql);
			
			if ($server[0]['status'] == 'f') {
				echo -1;
			}
			/*
			@ Conectarse al servidor FTP
			*/
			else if (!($ftp = @ftp_connect($ftp_server))) {
				echo -2;
			}
			/*
			@ Iniciar sesión en el servidor FTP
			*/
			else if (!@ftp_login($ftp, $ftp_user, $ftp_pass)) {
				echo -3;
			}
			else {
				/*
				@ Poner bandera de servidor ocupado
				*/
				$sql = '
					UPDATE
						facturas_electronicas_server_status
					SET
						status = \'FALSE\',
						iduser = ' . $_SESSION['iduser'] . ',
						tsmod = now()
					WHERE
						empresa = ' . $_SESSION['tipo_usuario'] . '
				';
				$db->query($sql);
				
				/*
				@ Obtener series
				*/
				$sql = '
					SELECT
						num_cia,
						serie,
						folio_inicial,
						folio_final,
						ultimo_folio_usado
					FROM
						facturas_electronicas_series
					WHERE
							status = 1
						AND
							tipo_serie = 2
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);
				
				$series = array();
				if ($result) {
					foreach ($result as $rec) {
						$series[$rec['num_cia']] = array(
							'serie'              => $rec['serie'],
							'folio_inicial'      => $rec['folio_inicial'],
							'folio_final'        => $rec['folio_final'],
							'ultimo_folio_usado' => $rec['ultimo_folio_usado']
						);
					}
				}
				
				$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
				$hora = date('H:i');
				
				/*
				@@ TEMPORAL: Fecha para comprobante digital
				*/
				$fecha_comprobante = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
				
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
								emisor,
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
						email,
						COALESCE(renta_con_recibo, 0)
							AS
								renta,
						CASE
							WHEN tipo_local = 1 THEN
								ROUND((COALESCE(renta_con_recibo, 0) * 0.16)::numeric, 2)
							ELSE
								0
						END
							AS
								iva_renta,
						COALESCE(mantenimiento, 0)
							AS
								mantenimiento,
						CASE
							WHEN tipo_local = 1 THEN
								ROUND((COALESCE(mantenimiento, 0) * 0.16)::numeric, 2)
							ELSE
								0
						END
							AS
								iva_mantenimiento,
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
							catalogo_arrendadores imb
								USING
									(
										cod_arrendador
									)
					WHERE
						arr.id IN (' . implode(', ', $_REQUEST['id']) . ')
					ORDER BY
						emisor,
						inmobiliaria,
						bloque,
						num_local
				';
				
				$result = $db->query($sql);
				
				/*
				@ Rutas locales
				*/
				$ldatos = 'facturas/datos/';
				$lcomprobantes_xml = 'facturas/comprobantes_xml/';
				$lcomprobantes_pdf = 'facturas/comprobantes_pdf/';
				
				/*
				@ Rutas servidor
				*/
				$rdatos = 'carga/';
				$rcomprobantes_xml = 'comprobantes/';
				$rcomprobantes_pdf = 'comprobantes/';
				
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasRentasReporte.tpl');
				$tpl->prepare();
				
				$tpl->assign('anio', $_REQUEST['anio']);
				$tpl->assign('mes_escrito', $_meses[$_REQUEST['mes']]);
				
				$emisor = NULL;
				foreach ($result as $rec) {
					if ($emisor != $rec['emisor']) {
						$emisor = $rec['emisor'];
						
						$tpl->newBlock('emisor');
						$tpl->assign('emisor', $emisor);
						$tpl->assign('nombre_emisor', utf8_encode($rec['nombre_inmobiliaria']));
						
						$color = FALSE;
					}
					
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					
					$color = !$color;
					
					$tpl->assign('local', $rec['local']);
					$tpl->assign('arrendatario', utf8_encode($rec['nombre_cliente']));
					$tpl->assign('renta', $rec['renta'] != 0 ? number_format($rec['renta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('mantenimiento', $rec['mantenimiento'] != 0 ? number_format($rec['mantenimiento'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('subtotal', $rec['subtotal'] ? number_format($rec['subtotal'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('agua', $rec['agua'] != 0 ? number_format($rec['agua'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_iva', $rec['retencion_iva'] != 0 ? number_format($rec['retencion_iva'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('retencion_isr', $rec['retencion_isr'] != 0 ? number_format($rec['retencion_isr'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('total', $rec['total'] != 0 ? number_format($rec['total'], 2, '.', ',') : '&nbsp;');
					
					if (strlen(trim($rec['rfc'])) < 12) {
						$tpl->assign('status', '<span style="color:#C00;">La RFC del arrendatario es erroneo</span>');
					}
					else if (trim($rec['calle']) == ''
						|| trim($rec['colonia']) == ''
						|| trim($rec['municipio']) == ''
						|| trim($rec['estado']) == ''
						|| trim($rec['pais']) == ''
						|| trim($rec['codigo_postal']) == '') {
						$tpl->assign('status', '<span style="color:#C00;">La direcci&oacute;n del arrendatario esta mal especificada</span>');
					}
					else if (!isset($series[$emisor])) {
						$tpl->assign('status', '<span style="color:#C00;">La inmobiliaria no tiene folios registrados</span>');
					}
					else if ($series[$emisor]['ultimo_folio_usado'] + 1 > $series[$emisor]['folio_final']) {
						$tpl->assign('status', '<span style="color:#C00;">La inmobiliaria agoto sus folios disponibles</span>');
					}
					else {
						$series[$emisor]['ultimo_folio_usado']++;
						
						$file_name = $emisor . '-' . $series[$emisor]['serie'] . $series[$emisor]['ultimo_folio_usado'];
						if (!($fp = @fopen($ldatos . $file_name . '.txt', 'wb+'))) {
							$tpl->assign('status', '<span style="color:#C00;">No se pudo crear el archivo de datos"' . $file_name . '.txt"</span>');
							
							$series[$emisor]['ultimo_folio_usado']--;
						}
						else {
							$pieces = array();
							
							$pieces[] = $fecha_comprobante . ' ' . $hora;
							$pieces[] = 'POR RENTA DE ' . $rec['tipo_local'] . ' DEL INMUEBLE UBICADO EN ' . trim($rec['direccion_local']);
							$pieces[] = 'EFECTIVO';
							$pieces[] = $rec['clave_cliente'];
							$pieces[] = $rec['nombre_cliente'];
							$pieces[] = $rec['rfc'];
							$pieces[] = $rec['pais'];
							$pieces[] = $rec['estado'];
							$pieces[] = $rec['codigo_postal'];
							$pieces[] = $rec['colonia'];
							$pieces[] = $rec['calle'];
							$pieces[] = $rec['no_exterior'];
							$pieces[] = $rec['no_interior'];
							$pieces[] = $rec['localidad'];
							$pieces[] = $rec['municipio'];
							$pieces[] = $rec['referencia'];
							$pieces[] = $rec['retencion_isr'] > 0 || $rec['retencion_iva'] > 0 ? 'S' : 'N';
							$pieces[] = 2;
							$pieces[] = $rec['iva'] > 0 ? 16 : 0;
							$pieces[] = $rec['subtotal'] + $rec['agua'];
							$pieces[] = 0;
							$pieces[] = $rec['iva'];
							$pieces[] = $rec['retencion_isr'];
							$pieces[] = $rec['retencion_iva'];
							$pieces[] = $rec['total'];
							
							fwrite($fp, implode('|', $pieces) . "\r\n");
							
							$sql_tmp = '
								INSERT INTO
									facturas_electronicas
										(
											num_cia,
											fecha,
											hora,
											tipo_serie,
											consecutivo,
											tipo,
											clave_cliente,
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
											email_cliente,
											observaciones,
											importe,
											iva,
											retencion_iva,
											retencion_isr,
											total,
											iduser_ins,
											fecha_pago,
											idlocal
										)
									VALUES
										(
											' . $emisor . ',
											\'' . $fecha_comprobante . '\',
											\'' . $hora . '\',
											2,
											' . $series[$emisor]['ultimo_folio_usado'] . ',
											5,
											' . $rec['clave_cliente'] . ',
											\'' . $rec['nombre_cliente'] . '\',
											\'' . $rec['rfc'] . '\',
											\'' . $rec['calle'] . '\',
											\'' . $rec['no_exterior'] . '\',
											\'' . $rec['no_interior'] . '\',
											\'' . $rec['colonia'] . '\',
											\'' . $rec['localidad'] . '\',
											\'' . $rec['referencia'] . '\',
											\'' . $rec['municipio'] . '\',
											\'' . $rec['estado'] . '\',
											\'' . $rec['pais'] . '\',
											\'' . $rec['codigo_postal'] . '\',
											\'' . strtolower(trim($rec['email'])) . '\',
											\'POR RENTA DE ' . $rec['tipo_local'] . ' DEL INMUEBLE UBICADO EN ' . trim($rec['direccion_local']) . '\',
											' . ($rec['subtotal'] + $rec['agua']) . ',
											' . $rec['iva'] . ',
											' . $rec['retencion_iva'] . ',
											' . $rec['retencion_isr'] . ',
											' . $rec['total'] . ',
											' . $_SESSION['iduser'] . ',
											\'' . $fecha_comprobante . '\',
											' . $rec['id'] . '
										)
							' . ";\n";
							
							$sql_tmp .= '
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
											' . $series[$emisor]['ultimo_folio_usado'] . ',
											' . $rec['renta'] . ',
											' . $rec['agua'] . ',
											' . $rec['mantenimiento'] . ',
											' . $rec['iva'] . ',
											' . $rec['retencion_isr'] . ',
											' . $rec['retencion_iva'] . ',
											' . $rec['total'] . ',
											\'' . $fecha . '\',
											' . $rec['bloque'] . ',
											\'TRUE\',
											\'' . $fecha . '\',
											' . $rec['id'] . ',
											1,
											' . $_SESSION['iduser'] . ',
											now()
										)
							' . ";\n";
							
							/*
							@ Relleno para la línea de consignatario
							*/
							$pieces = array();
							
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							$pieces[] = '';
							
							fwrite($fp, implode('|', $pieces) . "\r\n");
							
							if ($rec['renta'] > 0) {
								$pieces = array();
								
								$pieces[] = 1;
								$pieces[] = 'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'];
								$pieces[] = 1;
								$pieces[] = 'SIN UNIDAD';
								$pieces[] = $rec['renta'];
								$pieces[] = 0;
								$pieces[] = $rec['iva_renta'] > 0 ? 'No' : 'Si';
								$pieces[] = $rec['iva_renta'] > 0 ? 16 : 0;
								$pieces[] = $rec['iva_renta'];
								$pieces[] = '';
								$pieces[] = '';
								$pieces[] = '';
								
								fwrite($fp, implode('|', $pieces) . "\r\n");
								
								$sql_tmp .= '
									INSERT INTO
										facturas_electronicas_detalle
											(
												num_cia,
												tipo_serie,
												consecutivo,
												clave_producto,
												cantidad,
												descripcion,
												precio,
												unidad,
												importe
											)
										VALUES
											(
												' . $emisor . ',
												2,
												' . $series[$emisor]['ultimo_folio_usado'] . ',
												1,
												1,
												\'RENTA DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'] . '\',
												' . $rec['renta'] . ',
												\'SIN UNIDAD\',
												' . $rec['renta'] . '
											)
								' . ";\n";
							}
							
							if ($rec['mantenimiento'] > 0) {
								$pieces = array();
								
								$pieces[] = 1;
								$pieces[] = 'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'];
								$pieces[] = 1;
								$pieces[] = 'SIN UNIDAD';
								$pieces[] = $rec['mantenimiento'];
								$pieces[] = 0;
								$pieces[] = $rec['iva_mantenimiento'] > 0 ? 'No' : 'Si';
								$pieces[] = $rec['iva_mantenimiento'] > 0 ? 16 : 0;
								$pieces[] = $rec['iva_mantenimiento'];
								$pieces[] = '';
								$pieces[] = '';
								$pieces[] = '';
								
								fwrite($fp, implode('|', $pieces) . "\r\n");
								
								$sql_tmp .= '
									INSERT INTO
										facturas_electronicas_detalle
											(
												num_cia,
												tipo_serie,
												consecutivo,
												clave_producto,
												cantidad,
												descripcion,
												precio,
												unidad,
												importe
											)
										VALUES
											(
												' . $emisor . ',
												2,
												' . $series[$emisor]['ultimo_folio_usado'] . ',
												1,
												1,
												\'MANTENIMIENTO DEL MES DE ' . $_meses[$_REQUEST['mes']] . ' DE ' . $_REQUEST['anio'] . '\',
												' . $rec['mantenimiento'] . ',
												\'SIN UNIDAD\',
												' . $rec['mantenimiento'] . '
											)
								' . ";\n";
							}
							
							if ($rec['agua'] > 0) {
								$pieces = array();
								
								$pieces[] = 1;
								$pieces[] = 'CUOTA DE RECUPERACION DE AGUA';
								$pieces[] = 1;
								$pieces[] = 'SIN UNIDAD';
								$pieces[] = $rec['agua'];
								$pieces[] = 0;
								$pieces[] = 'Si';
								$pieces[] = 0;
								$pieces[] = 0;
								$pieces[] = '';
								$pieces[] = '';
								$pieces[] = '';
								
								fwrite($fp, implode('|', $pieces) . "\r\n");
								
								$sql_tmp .= '
									INSERT INTO
										facturas_electronicas_detalle
											(
												num_cia,
												tipo_serie,
												consecutivo,
												clave_producto,
												cantidad,
												descripcion,
												precio,
												unidad,
												importe
											)
										VALUES
											(
												' . $emisor . ',
												2,
												' . $series[$emisor]['ultimo_folio_usado'] . ',
												1,
												1,
												\'CUOTA DE RECUPERACION DE AGUA\',
												' . $rec['agua'] . ',
												\'SIN UNIDAD\',
												' . $rec['agua'] . '
											)
											
								' . ";\n";
							}
							
							/*
							@ Cerrar archivo de datos
							*/
							fclose($fp);
							
							/*
							@ Colocar archivo de datos en el servidor
							*/
							$retries = 0;
							do {
								$uploaded = @ftp_put($ftp, $rdatos . $file_name . '.txt', $ldatos . $file_name . '.txt', FTP_BINARY);
								$retries++;
							} while (!$uploaded && $retries < 50);
							
							
							if (!$uploaded) {
								$tpl->assign('status', '<span style="color:#C00;">No se pudo enviar el archivo "' . $file_name . '" al servidor</span>');
								
								/*
								@ Decrementar folios usados en 1
								*/
								$series[$emisor]['ultimo_folio_usado']--;
							}
							else {
								/*
								@ Hacer petición al servidor para generar CFD
								*/
								$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $emisor . '&archivo=' . $file_name . '.txt';
								if (!($result = file_get_contents($url))) {
									$tpl->assign('status', '<span style="color:#C00;">Imposible acceder al generador de facturas electr&oacute;nicas</span>');
									
									/*
									@ Borrar archivo del servidor
									*/
									if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
										//echo '<strong style="color:#C00;"><li>No se pudo borrar el archivo "' . $file_name . '.txt" del servidor</strong>';
									}
									
									/*
									@ Decrementar folios usados en 1
									*/
									$series[$emisor]['ultimo_folio_usado']--;
								}
								else {
									/*
									@ Interpretar respuesta del servidor
									*/
									$url_result = explode('|', $result);
									
									foreach ($url_result as $i => $value) {
										list($var, $val) = explode('=', trim($value));
										
										${trim($var)} = trim($val);
									}
									
									if ($Estatus == 0) {
										$tpl->assign('status', '<span style="color:#C00;">Error al generar el CFD: "' . $Error . '"<br />Archivo de carga: "' . $file_name . '.txt"<br />Cadena original: "' . $result . '"</span>');
										
										/*
										@ Borrar archivo del servidor
										*/
										if (!@ftp_delete($ftp, $rdatos . $file_name)) {
											//echo '<strong style="color:#C00;"><li>No se pudo borrar el archivo "' . $file_name . '.txt" del servidor</strong>';
										}
										
										/*
										@ Decrementar folios usados en 1
										*/
										$series[$emisor]['ultimo_folio_usado']--;
									}
									else {
										$tpl->assign('folio', $series[$emisor]['ultimo_folio_usado']);
										
										/*
										@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
										*/
										if (!is_dir($lcomprobantes_xml . $emisor)) {
											mkdir($lcomprobantes_xml . $emisor);
										}
										
										/*
										@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
										*/
										if (!is_dir($lcomprobantes_pdf . $emisor)) {
											mkdir($lcomprobantes_pdf . $emisor);
										}
										
										/*
										@ Obtener archivo comprobante XML
										*/
										$retries = 0;
										do {
											$downloaded_xml = @ftp_get($ftp, $lcomprobantes_xml . $emisor . '/' . $file_name . '.xml', $rcomprobantes_xml . $emisor . '/' . $ComprobanteXML, FTP_BINARY);
											$retries++;
										} while (!$downloaded_xml && $retries < 50);
										
										/*
										@ Obtener archivo comprobante PDF
										*/
										$retries = 0;
										do {
											$downloaded_pdf = @ftp_get($ftp, $lcomprobantes_pdf . $emisor . '/' . $file_name . '.pdf', $rcomprobantes_pdf . $emisor . '/' . $ComprobantePDF, FTP_BINARY);
											$retries++;
										} while (!$downloaded_pdf && $retries < 50);
										
										if (!$downloaded_xml) {
											$tpl->assign('status', '<span style="color:#C00;">No se pudo obtener el archivo "' . $file_name . '.xml" (' . $ComprobanteXML . ') del servidor</span>');
										}
										else if (!$downloaded_pdf) {
											$tpl->assign('status', '<span style="color:#C00;">No se pudo obtener el archivo "' . $file_name . '.pdf" (' . $ComprobantePDF . ') del servidor</span>');
										}
										else {
											$tpl->assign('status', '<span style="color:#060;">OK</span>');
										}
										
										/*
										@ Actualizar nombres de comprobantes en el registro de la factura electrónica
										*/
										$sql_tmp .= '
											UPDATE
												facturas_electronicas
											SET
												comprobante_xml = \'' . $ComprobanteXML . '\',
												comprobante_pdf = \'' . $ComprobantePDF . '\',
												cadena_servidor = \'' . $result . '\'
											WHERE
													num_cia = ' . $emisor . '
												AND
													tipo_serie = 2
												AND
													consecutivo = ' . $series[$emisor]['ultimo_folio_usado'] . '
										' . ";\n";
										
										/*
										@ Actualizar series
										*/
										$sql_tmp .= '
											UPDATE
												facturas_electronicas_series
											SET
												ultimo_folio_usado = ' . $series[$emisor]['ultimo_folio_usado'] . '
											WHERE
													num_cia = ' . $emisor . '
												AND
													status = 1
												AND
													tipo_serie = 2
												AND
													folio_inicial = ' . $series[$emisor]['folio_inicial'] . '
												AND
													folio_final = ' . $series[$emisor]['folio_final'] . '
										' . ";\n";
										
										/*
										@ Poner serie como terminada si se ha llegado al máximo de folios
										*/
										if ($series[$emisor]['ultimo_folio_usado'] == $series[$emisor]['folio_final']) {
											$sql_tmp .= '
												UPDATE
													facturas_electronicas_series
												SET
													status = 2
												WHERE
														num_cia = ' . $emisor . '
													AND
														status = 1
													AND
														tipo_serie = 2
													AND
														folio_inicial = ' . $series[$emisor]['folio_inicial'] . '
													AND
														folio_final = ' . $series[$emisor]['folio_final'] . '
											' . ";\n";
										}
										
										/*
										@ [21-Feb-2011] En caso de que el servidor de facturas electrónicas haya brincado folios,
										@ recorrerlos automáticamente y guardar un registro del evento
										*/
										if ($Folio != $series[$emisor]['ultimo_folio_usado']) {
											$sql .= '
												UPDATE
													facturas_electronicas
												SET
													consecutivo = ' . $Folio . '
												WHERE
														num_cia = ' . $emisor . '
													AND
														tipo_serie = 2
													AND
														consecutivo = ' . $series[$emisor]['ultimo_folio_usado'] . '
											' . ";\n";
											
											$sql .= '
												UPDATE
													facturas_electronicas_detalle
												SET
													consecutivo = ' . $Folio . '
												WHERE
														num_cia = ' . $emisor . '
													AND
														tipo_serie = 2
													AND
														consecutivo = ' . $series[$emisor]['ultimo_folio_usado'] . '
											' . ";\n";
											
											$sql .= '
												UPDATE
													facturas_electronicas_series
												SET
													ultimo_folio_usado = ' . $Folio . '
												WHERE
														num_cia = ' . $emisor . '
													AND
														status = 1
													AND
														tipo_serie = 2
													AND
														folio_inicial = ' . $series[$emisor]['folio_inicial'] . '
													AND
														folio_final = ' . $series[$emisor]['folio_final'] . '
											' . ";\n";
											
											$sql .= '
												INSERT INTO
													facturas_electronicas_folios_brincados
														(
															num_cia,
															tipo_serie,
															folio_brincado,
															folio_nuevo,
															iduser,
															tsins
														)
													VALUES
														(
															' . $emisor . ',
															2,
															' . $series[$emisor]['ultimo_folio_usado'] . ',
															' . $Folio . ',
															' . $_SESSION['iduser'] . ',
															now()
														)
											' . ";\n";
											
											$series[$emisor]['ultimo_folio_usado'] = $Folio;
											
											$new_file_name = $emisor . '-' . $Folio;
											
											rename($lcomprobantes_xml . $emisor . '/' . $file_name . '.xml', $lcomprobantes_xml . $emisor . '/' . $new_file_name . '.xml');
											rename($lcomprobantes_pdf . $emisor . '/' . $file_name . '.pdf', $lcomprobantes_pdf . $emisor . '/' . $new_file_name . '.pdf');
										}
										
										$db->query($sql_tmp);
									}
								}
							}
						}
					}
				}
				
				echo $tpl->getOutputContent();
				
				/*
				@ Poner bandera de servidor disponible
				*/
				$sql = '
					UPDATE
						facturas_electronicas_server_status
					SET
						status = \'TRUE\',
						iduser = ' . $_SESSION['iduser'] . ',
						tsmod = now()
					WHERE
						empresa = ' . $_SESSION['tipo_usuario'] . '
				';
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasRentas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
