<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
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
				
				$condiciones[] = 'folio IS NULL';
				
				/*
				@ Intervalo de compañías
				*/
				if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
					$cias = array();
					
					$pieces = explode(',', $_REQUEST['cias']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$cias[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$cias[] = $piece;
						}
					}
					
					if (count($cias) > 0) {
						$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					}
				}
				
				$sql = '
					SELECT
						id,
						107
							AS
								emisor,
						num_cia,
						100000 + num_cia
							AS
								clave_cliente,
						razon_social
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
							AS
								email_cliente,
						fecha,
						kilos
							AS
								cantidad,
						precio,
						importe
							AS
								importe,
						0
							AS
								iva,
						importe
							AS
								total,
						4	
							AS
								tipo
					FROM
							facturacion_condimento fc
						LEFT JOIN
							catalogo_companias cc
								USING
									(num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						fecha
				';
				
				$importes = $db->query($sql);
				
				if (!$importes) {
					echo -4;
				}
				else {
					$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasCondimentoResult.tpl');
					$tpl->prepare();
					
//					$sql = '
//						SELECT
//							(MAX(fecha) + interval \'1 day\')::date
//								AS
//									fecha
//						FROM
//							facturas_electronicas
//						WHERE
//								num_cia = 107
//							AND
//								tipo = 1
//					';
//					$tmp = $db->query($sql);
//					
//					if ($tmp[0]['fecha'] != '') {
//						$fecha = $tmp[0]['fecha'];
//					}
//					else {
//						$fecha = date('01/m/Y');
//					}
					
					$fecha = $_REQUEST['fecha'];
					$hora = date('H:i');
					
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
								num_cia IN (107)
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
					
					$sql_scripts = array();
					
					$total = 0;
					
					foreach ($importes as $rec) {
						$tpl->newBlock('row');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', $rec['nombre_cliente']);
						$tpl->assign('importe', number_format($rec['total'], 2, '.', ','));
						
						$total += $rec['total'];
						
						$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
						
						if (trim($rec['rfc']) == '') {
							$tpl->assign('status', '<pre style="color:#C00;">La RFC de la compa&ntilde;&iacute;a es erroneo</pre>');
						}
						else if (trim($rec['calle']) == ''
							|| trim($rec['colonia']) == ''
							|| trim($rec['municipio']) == ''
							|| trim($rec['estado']) == ''
							|| trim($rec['pais']) == ''
							|| trim($rec['codigo_postal']) == '') {
							$tpl->assign('status', '<pre style="color:#C00;">La direcci&oacute;n de la compa&ntilde;&iacute;a esta mal especificada</pre>');
						}
						else if (!isset($series[$rec['emisor']])) {
							$tpl->assign('status', '<pre style="color:#C00;">La compa&ntilde;&iacute;a no tiene folios</pre>');
						}
						else if ($series[$rec['emisor']]['ultimo_folio_usado'] + 1 > $series[$rec['emisor']]['folio_final']) {
							$tpl->assign('status', '<pre style="color:#C00;">La compa&ntilde;&iacute;a agoto sus folios disponibles</pre>');
						}
						else {
							$series[$rec['emisor']]['ultimo_folio_usado']++;
							
							$file_name = $rec['emisor'] . '-' . $series[$rec['emisor']]['serie'] . $series[$rec['emisor']]['ultimo_folio_usado'];
							if (!($fp = @fopen($ldatos . $file_name . '.txt', 'wb+'))) {
								$tpl->assign('status', '<pre style="color:#C00;">No se pudo crear el archivo de datos"' . $file_name . '.txt"</pre>');
								
								$ok = FALSE;
								
								$series[$rec['emisor']]['ultimo_folio_usado']--;
							}
							else {
								$pieces = array();
								
								$pieces[] = $fecha . ' ' . $hora;
								$pieces[] = '';
								$pieces[] = 'EFECTIVO';
								$pieces[] = $rec['clave_cliente'];
								$pieces[] = strtoupper($rec['nombre_cliente']);
								$pieces[] = strtoupper($rec['rfc']);
								$pieces[] = strtoupper($rec['pais']);
								$pieces[] = strtoupper($rec['estado']);
								$pieces[] = strtoupper($rec['codigo_postal']);
								$pieces[] = strtoupper($rec['colonia']);
								$pieces[] = strtoupper($rec['calle']);
								$pieces[] = strtoupper($rec['no_exterior']);
								$pieces[] = strtoupper($rec['no_interior']);
								$pieces[] = strtoupper($rec['localidad']);
								$pieces[] = strtoupper($rec['municipio']);
								$pieces[] = strtoupper($rec['referencia']);
								$pieces[] = 'N';
								$pieces[] = 1;
								$pieces[] = 0;
								$pieces[] = $rec['importe'];
								$pieces[] = 0;
								$pieces[] = $rec['iva'];
								$pieces[] = 0;
								$pieces[] = 0;
								$pieces[] = $rec['total'];
								
								fwrite($fp, implode('|', $pieces) . "\r\n");
								
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
								
								$pieces = array();
								
								$pieces[] = 1;
								$pieces[] = 'CONDIMENTO ' . $rec['fecha'];
								$pieces[] = $rec['cantidad'];
								$pieces[] = 'KILOS';
								$pieces[] = $rec['precio'];
								$pieces[] = 0;
								$pieces[] = 'Si';
								$pieces[] = 0;
								$pieces[] = 0;
								$pieces[] = '';
								$pieces[] = '';
								$pieces[] = '';
								
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
												total,
												iduser_ins,
												fecha_pago
											)
										VALUES
											(
												' . $rec['emisor'] . ',
												\'' . $fecha . '\',
												\'' . $hora . '\',
												1,
												' . $series[$rec['emisor']]['ultimo_folio_usado'] . ',
												4,
												' . $rec['clave_cliente'] . ',
												\'' . strtoupper($rec['nombre_cliente']) . '\',
												\'' . strtoupper($rec['rfc']) . '\',
												\'' . strtoupper($rec['calle']) . '\',
												\'' . strtoupper($rec['no_exterior']) . '\',
												\'' . strtoupper($rec['no_interior']) . '\',
												\'' . strtoupper($rec['colonia']) . '\',
												\'' . strtoupper($rec['localidad']) . '\',
												\'' . strtoupper($rec['referencia']) . '\',
												\'' . strtoupper($rec['municipio']) . '\',
												\'' . strtoupper($rec['estado']) . '\',
												\'' . strtoupper($rec['pais']) . '\',
												\'' . strtoupper($rec['codigo_postal']) . '\',
												\'' . strtolower($rec['email_cliente']) . '\',
												\'\',
												' . $rec['importe'] . ',
												' . $rec['iva'] . ',
												' . $rec['total'] . ',
												' . $_SESSION['iduser'] . ',
												\'' . $fecha . '\'
											)
								' . ";\n";
								
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
												' . $rec['emisor'] . ',
												1,
												' . $series[$rec['emisor']]['ultimo_folio_usado'] . ',
												1,
												' . $rec['cantidad'] . ',
												\'CONDIMENTO ' . $rec['fecha'] . '\',
												' . $rec['precio'] . ',
												\'KILOS\',
												' . $rec['importe'] . '
											)
											
								' . ";\n";
								
								$sql_tmp .= '
									INSERT INTO
										facturas
											(
												num_cia,
												num_proveedor,
												num_fact,
												fecha,
												importe,
												piva,
												iva,
												pretencion_isr,
												pretencion_iva,
												retencion_isr,
												retencion_iva,
												total,
												codgastos,
												tipo_factura,
												fecha_captura,
												iduser,
												concepto
											)
										VALUES
											(
												' . $rec['num_cia'] . ',
												937,
												\'' . $series[$rec['emisor']]['ultimo_folio_usado'] . '\',
												\'' . $fecha . '\',
												' . $rec['importe'] . ',
												0,
												' . $rec['iva'] . ',
												0,
												0,
												0,
												0,
												' . $rec['total'] . ',
												200,
												1,
												now()::date,
												' . $_SESSION['iduser'] . ',
												\'CONDIMENTO ' . $rec['fecha'] . '\'
											)
								' . ";\n";
								
								$sql_tmp .= '
									INSERT INTO
										pasivo_proveedores
											(
												num_cia,
												num_proveedor,
												num_fact,
												fecha,
												descripcion,
												codgastos,
												total,
												copia_fac
											)
										VALUES
											(
												' . $rec['num_cia'] . ',
												937,
												\'' . $series[$rec['emisor']]['ultimo_folio_usado'] . '\',
												\'' . $fecha . '\',
												\'CONDIMENTO ' . $rec['fecha'] . '\',
												200,
												' . $rec['total'] . ',
												\'TRUE\'
											)
								' . ";\n";
								
								$sql_tmp .= '
									INSERT INTO
										entrada_mp
											(
												num_cia,
												num_proveedor,
												num_fact,
												fecha,
												iduser,
												pagado,
												codmp,
												regalado,
												cantidad,
												contenido,
												precio,
												pdesc1,
												pdesc2,
												pdesc3,
												piva,
												ieps,
												importe
											)
										VALUES
											(
												' . $rec['num_cia'] . ',
												937,
												\'' . $series[$rec['emisor']]['ultimo_folio_usado'] . '\',
												\'' . $fecha . '\',
												' . $_SESSION['iduser'] . ',
												\'FALSE\',
												912,
												\'FALSE\',
												' . $rec['cantidad'] . ',
												1,
												' . $rec['precio'] . ',
												0,
												0,
												0,
												0,
												0,
												' . $rec['total'] . '
											)
								' . ";\n";
								
								$sql_tmp .= '
									INSERT INTO
										mov_inv_real
											(
												num_cia,
												codmp,
												fecha,
												tipo_mov,
												cantidad,
												precio,
												total_mov,
												precio_unidad,
												descripcion,
												num_proveedor
											)
										VALUES
											(
												' . $rec['num_cia'] . ',
												912,
												\'' . $fecha . '\',
												\'FALSE\',
												' . $rec['cantidad'] . ',
												' . $rec['precio'] . ',
												' . $rec['total'] . ',
												' . $rec['precio'] . ',
												\'COMPRA F. NO. \' || ' . $series[$rec['emisor']]['ultimo_folio_usado'] . ',
												937
											)
								' . ";\n";
								
								$sql_tmp .= '
									UPDATE
										inventario_real
									SET
										existencia = existencia + ' . $rec['cantidad'] . ',
										precio_unidad = ' . $rec['precio'] . '
									WHERE
											num_cia = ' . $rec['num_cia'] . '
										AND
											codmp = 912
								' . ";\n";
								
								$sql_tmp .= '
									UPDATE
										facturacion_condimento
									SET
										folio = ' . $series[$rec['emisor']]['ultimo_folio_usado'] . ',
										tsprint = now()
									WHERE
										id = ' . $rec['id'] . '
								' . ";\n";
								
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
									$series[$rec['emisor']]['ultimo_folio_usado']--;
								}
								else {
									/*
									@ Hacer petición al servidor para generar CFD
									*/
									$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $rec['emisor'] . '&archivo=' . $file_name . '.txt';
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
										$series[$rec['emisor']]['ultimo_folio_usado']--;
									}
									else {
										/*
										@ Interpretar respuesta del servidor
										*/
										$url_result = explode('|', $result);
										
										foreach ($url_result as $i => $value) {
											if ($i == 3) {
												$tmp = explode('=', trim($value));
												
												if (count($tmp) > 2) {
													${trim($tmp[0])} = trim($tmp[2]);
												}
												else {
													${trim($tmp[0])} = trim($tmp[1]);
												}
											}
											else {
												list($var, $val) = explode('=', trim($value));
												
												${trim($var)} = trim($val);
											}
										}
										
										if ($Estatus == 0) {
											$tpl->assign('status', '<span style="color:#C00;">Error al generar el CFD: "' . $Error . '"</span>');
											
											/*
											@ Borrar archivo del servidor
											*/
											if (!@ftp_delete($ftp, $rdatos . $file_name)) {
												//echo '<strong style="color:#C00;"><li>No se pudo borrar el archivo "' . $file_name . '.txt" del servidor</strong>';
											}
											
											/*
											@ Decrementar folios usados en 1
											*/
											$series[$rec['emisor']]['ultimo_folio_usado']--;
										}
										else {
											$tpl->assign('folio', $series[$rec['emisor']]['ultimo_folio_usado']);
											
											/*
											@ Obtener archivo comprobante XML
											*/
											$retries = 0;
											do {
												$downloaded_xml = @ftp_get($ftp, $lcomprobantes_xml . $rec['emisor'] . '/' . $file_name . '.xml', $rcomprobantes_xml . $rec['emisor'] . '/' . $ComprobanteXML, FTP_BINARY);
												$retries++;
											} while (!$downloaded_xml && $retries < 50);
											
											/*
											@ Obtener archivo comprobante PDF
											*/
											$retries = 0;
											do {
												$downloaded_pdf = @ftp_get($ftp, $lcomprobantes_pdf . $rec['emisor'] . '/' . $file_name . '.pdf', $rcomprobantes_pdf . $rec['emisor'] . '/' . $ComprobantePDF, FTP_BINARY);
												$retries++;
											} while (!$downloaded_pdf && $retries < 50);
											
											if (!$downloaded_xml) {
												$tpl->assign('status', '<span style="color:#C00;">No se pudo obtener el archivo "' . $file_name . '.xml" del servidor</span>');
											}
											else if (!$downloaded_pdf) {
												$tpl->assign('status', '<span style="color:#C00;">No se pudo obtener el archivo "' . $file_name . '.pdf" del servidor</span>');
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
													comprobante_pdf = \'' . $ComprobantePDF . '\'
												WHERE
														num_cia = ' . $rec['emisor'] . '
													AND
														tipo_serie = 1
													AND
														consecutivo = ' . $series[$rec['emisor']]['ultimo_folio_usado'] . '
											' . ";\n";
											
											/*
											@ Actualizar series
											*/
											$sql_tmp .= '
												UPDATE
													facturas_electronicas_series
												SET
													ultimo_folio_usado = ' . $series[$rec['emisor']]['ultimo_folio_usado'] . '
												WHERE
														num_cia = ' . $rec['emisor'] . '
													AND
														status = 1
													AND
														tipo_serie = 1
													AND
														folio_inicial = ' . $series[$rec['emisor']]['folio_inicial'] . '
													AND
														folio_final = ' . $series[$rec['emisor']]['folio_final'] . '
											' . ";\n";
											
											/*
											@ Poner serie como terminada si se ha llegado al máximo de folios
											*/
											if ($series[$rec['emisor']]['ultimo_folio_usado'] == $series[$rec['emisor']]['folio_final']) {
												$sql_tmp .= '
													UPDATE
														facturas_electronicas_series
													SET
														status = 2
													WHERE
															num_cia = ' . $rec['emisor'] . '
														AND
															status = 1
														AND
															tipo_serie = 1
														AND
															folio_inicial = ' . $series[$rec['emisor']]['folio_inicial'] . '
														AND
															folio_final = ' . $series[$rec['emisor']]['folio_final'] . '
												' . ";\n";
											}
											
											/*
											@ [21-Feb-2011] En caso de que el servidor de facturas electrónicas haya brincado folios,
											@ recorrerlos automáticamente y guardar un registro del evento
											*/
											if ($Folio != $series[$rec['emisor']]['ultimo_folio_usado']) {
												$sql .= '
													UPDATE
														facturas_electronicas
													SET
														consecutivo = ' . $Folio . '
													WHERE
															num_cia = ' . $rec['emisor'] . '
														AND
															tipo_serie = 1
														AND
															consecutivo = ' . $series[$rec['emisor']]['ultimo_folio_usado'] . '
												' . ";\n";
												
												$sql .= '
													UPDATE
														facturas_electronicas_detalle
													SET
														consecutivo = ' . $Folio . '
													WHERE
															num_cia = ' . $rec['emisor'] . '
														AND
															tipo_serie = 1
														AND
															consecutivo = ' . $series[$rec['emisor']]['ultimo_folio_usado'] . '
												' . ";\n";
												
												$sql .= '
													UPDATE
														facturas_electronicas_series
													SET
														ultimo_folio_usado = ' . $Folio . '
													WHERE
															num_cia = ' . $rec['emisor'] . '
														AND
															status = 1
														AND
															tipo_serie = 1
														AND
															folio_inicial = ' . $series[$rec['emisor']]['folio_inicial'] . '
														AND
															folio_final = ' . $series[$rec['emisor']]['folio_final'] . '
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
																' . $rec['emisor'] . ',
																1,
																' . $series[$rec['emisor']]['ultimo_folio_usado'] . ',
																' . $Folio . ',
																' . $_SESSION['iduser'] . ',
																now()
															)
												' . ";\n";
												
												$series[$rec['emisor']]['ultimo_folio_usado'] = $Folio;
												
												$new_file_name = $rec['emisor'] . '-' . $Folio;
												
												rename($lcomprobantes_xml . $rec['emisor'] . '/' . $file_name . '.xml', $lcomprobantes_xml . $rec['emisor'] . '/' . $new_file_name . '.xml');
												rename($lcomprobantes_pdf . $rec['emisor'] . '/' . $file_name . '.pdf', $lcomprobantes_pdf . $rec['emisor'] . '/' . $new_file_name . '.pdf');
											}
											
											$db->query($sql_tmp);
										}
									}
								}
							}
						}
					}
					
					echo $tpl->getOutputContent();
				}
				
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

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasCondimento.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y'));

$tpl->printToScreen();
?>
