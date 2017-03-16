<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		id,
		num_cia,
		fecha,
		EXTRACT(hour from hora) || \':\' || EXTRACT(minute from hora)
			AS
				hora,
		consecutivo,
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
		cantidad,
		descripcion,
		precio,
		unidad,
		importe,
		iva,
		total
	FROM
		facturas_panaderias_tmp
	WHERE
		tsreg IS NULL
	ORDER BY
		num_cia,
		consecutivo
';
$datos = $db->query($sql);

/*
@ Validar que el servidor de facturas electrónicas se encuentre disponible
*/
$sql = '
	SELECT
		status
	FROM
		facturas_electronicas_server_status
	WHERE
		status = \'FALSE\'
';
$server = $db->query($sql);

if ($datos && !$server) {
	echo '---' . date('d/m/Y H:i:s') . '---';
	
	/*
	@ Poner bandera de servidor ocupado
	*/
	$sql = '
		UPDATE
			facturas_electronicas_server_status
		SET
			status = \'FALSE\',
			iduser = 0,
			tsmod = now()
		WHERE
			empresa IN (1, 2)
	';
	$db->query($sql);
	
	echo "\nObteniendo series... ";
	
	$sql = '
		SELECT
			num_cia,
			nombre
				AS
					nombre_cia,
			serie,
			folio_inicial,
			folio_final,
			ultimo_folio_usado,
			(
				SELECT
					(MAX(fecha) + interval \'1 day\')::date
				FROM
					facturas_electronicas
				WHERE
						tipo = 1
					AND
						status = 1
					AND
						num_cia = fes.num_cia
			)
				AS
					fecha,
			email
		FROM
				facturas_electronicas_series fes
			LEFT JOIN
				catalogo_companias cc
					USING
						(
							num_cia
						)
		WHERE
				fes.status = 1
			AND
				fes.tipo_serie = 1
		ORDER BY
			num_cia
	';
	$result = $db->query($sql);
	
	$series = array();
	if ($result) {
		foreach ($result as $rec) {
			$series[$rec['num_cia']] = array(
				'nombre_cia'         => $rec['nombre_cia'],
				'serie'              => $rec['serie'],
				'folio_inicial'      => $rec['folio_inicial'],
				'folio_final'        => $rec['folio_final'],
				'ultimo_folio_usado' => $rec['ultimo_folio_usado'],
				'fecha'              => $rec['fecha'] != '' ? $rec['fecha'] : date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))),
				'email'              => $rec['email']
			);
		}
	}
	
	echo '>>Completo!!!<<';
	
	/*
	@ Parámetros de conexión FTP
	*/
	$ftp_server = '192.168.1.70';
	$ftp_user = 'mollendo';
	$ftp_pass = 'L3c4r0z*';
	
	echo "\nConectando al servidor de facturas via FTP... ";
	
	/*
	@ Conectarse al servidor FTP
	*/
	$ftp = @ftp_connect($ftp_server) or die("\n@@Error al conectar al servidor \"" . $ftp_server . '"@@');
	
	/*
	@ Iniciar sesión en el servidor FTP
	*/
	if (!@ftp_login($ftp, $ftp_user, $ftp_pass)) {
		die("\n@@Error al iniciar sesion en el servidor \"" . $ftp_server . '" con usuario "' . $ftp_user . '" y contraseña "' . $ftp_pass . '"@@');
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
	
	echo '>>Completo!!!<<';
	
	$sql_scripts = array();
	$num_cia = NULL;
	
	$generadas = 0;
	$errores = 0;
	foreach ($datos as $rec) {
		if ($num_cia != $rec['num_cia']) {
			if ($num_cia != NULL && $ok) {
				if ($consecutivo != NULL && $ok) {
					/*
					@ Cerrar archivo de datos
					*/
					fclose($fp);
					
					echo '>>Completo!!!<<';
					echo "\n** Colocando archivo de datos en el servidor...";
					
					/*
					@ Colocar archivo de datos en el servidor
					*/
					$retries = 0;
					do {
						$uploaded = @ftp_put($ftp, $rdatos . $file_name . '.txt', $ldatos . $file_name . '.txt', FTP_BINARY);
						$retries++;
					} while (!$uploaded && $retries < 50);
					
					if (!$uploaded) {
						echo "\n@@No se puede enviar el archivo \"" . $file_name . '.txt" al servidor@@';
						
						/*
						@ Decrementar folios usados en 1
						*/
						$series[$num_cia]['ultimo_folio_usado']--;
						
						$errores++;
					}
					else {
						echo '>>Completo!!!<<';
						echo "\n** Generando CFD... ";
						
						/*
						@ Hacer petición al servidor para generar CFD
						*/
						$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $num_cia . '&archivo=' . $file_name . '.txt';
						if (!($result = file_get_contents($url))) {
							echo "\n@@Imposible acceder al generador de facturas electronicas@@";
							
							/*
							@ Borrar archivo del servidor
							*/
							if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
								echo "\n@@No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
							}
							
							/*
							@ Decrementar folios usados en 1
							*/
							$series[$num_cia]['ultimo_folio_usado']--;
							
							$errores++;
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
								echo "\n@@Error al generar el CFD: \"" . $Error . '"@@';
								
								/*
								@ Borrar archivo del servidor
								*/
								if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
									echo "\n@@No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
								}
								
								/*
								@ Decrementar folios usados en 1
								*/
								$series[$num_cia]['ultimo_folio_usado']--;
								
								$errores++;
							}
							else {
								echo '>>Completo!!!<<';
								
								/*
								@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
								*/
								if (!is_dir($lcomprobantes_xml . $num_cia)) {
									mkdir($lcomprobantes_xml . $num_cia);
								}
								
								/*
								@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
								*/
								if (!is_dir($lcomprobantes_pdf . $num_cia)) {
									mkdir($lcomprobantes_pdf . $num_cia);
								}
								
								/*
								@ Obtener archivo comprobante XML
								*/
								$retries = 0;
								do {
									$downloaded_xml = @ftp_get($ftp, $lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $rcomprobantes_xml . $num_cia . '/' . $ComprobanteXML, FTP_BINARY);
									$retries++;
								} while (!$downloaded_xml && $retries < 50);
								
								if (!$downloaded_xml) {
									echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.xml" (' . $ComprobanteXML . ') del servidor@@';
								}
								
								/*
								@ Obtener archivo comprobante PDF
								*/
								$retries = 0;
								do {
									$downloaded_pdf = @ftp_get($ftp, $lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $rcomprobantes_pdf . $num_cia . '/' . $ComprobantePDF, FTP_BINARY);
									$retries++;
								} while (!$downloaded_pdf && $retries < 50);
								
								if (!$downloaded_pdf) {
									echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.pdf" (' . $ComprobantePDF . ') del servidor@@';
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
											num_cia = ' . $num_cia . '
										AND
											tipo_serie = 1
										AND
											consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
								' . ";\n";
								
								/*
								@ Actualizar series
								*/
								$sql_tmp .= '
									UPDATE
										facturas_electronicas_series
									SET
										ultimo_folio_usado = ' . $series[$num_cia]['ultimo_folio_usado'] . '
									WHERE
											num_cia = ' . $num_cia . '
										AND
											status = 1
										AND
											tipo_serie = 1
										AND
											folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
										AND
											folio_final = ' . $series[$num_cia]['folio_final'] . '
								' . ";\n";
								
								/*
								@ Poner serie como terminada si se ha llegado al máximo de folios
								*/
								if ($series[$num_cia]['ultimo_folio_usado'] == $series[$num_cia]['folio_final']) {
									$sql_tmp .= '
										UPDATE
											facturas_electronicas_series
										SET
											status = 2
										WHERE
												num_cia = ' . $num_cia . '
											AND
												status = 1
											AND
												tipo_serie = 1
											AND
												folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
											AND
												folio_final = ' . $series[$num_cia]['folio_final'] . '
									' . ";\n";
								}
								
								$sql_tmp .= '
									UPDATE
										facturas_panaderias_tmp
									SET
										tsreg = now()
									WHERE
										id IN (' . implode(', ', $ids) . ')
								' . ";\n";
								
								/*
								@ [21-Feb-2011] En caso de que el servidor de facturas electrónicas haya brincado folios,
								@ recorrerlos automáticamente y guardar un registro del evento
								*/
								if ($Folio != $series[$num_cia]['ultimo_folio_usado']) {
									$sql .= '
										UPDATE
											facturas_electronicas
										SET
											consecutivo = ' . $Folio . '
										WHERE
												num_cia = ' . $num_cia . '
											AND
												tipo_serie = 1
											AND
												consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
									' . ";\n";
									
									$sql .= '
										UPDATE
											facturas_electronicas_detalle
										SET
											consecutivo = ' . $Folio . '
										WHERE
												num_cia = ' . $num_cia . '
											AND
												tipo_serie = 1
											AND
												consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
									' . ";\n";
									
									$sql .= '
										UPDATE
											facturas_electronicas_series
										SET
											ultimo_folio_usado = ' . $Folio . '
										WHERE
												num_cia = ' . $num_cia . '
											AND
												status = 1
											AND
												tipo_serie = 1
											AND
												folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
											AND
												folio_final = ' . $series[$num_cia]['folio_final'] . '
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
													' . $num_cia . ',
													1,
													' . $series[$num_cia]['ultimo_folio_usado'] . ',
													' . $Folio . ',
													' . $_SESSION['iduser'] . ',
													now()
												)
									' . ";\n";
									
									$series[$num_cia]['ultimo_folio_usado'] = $Folio;
									
									$new_file_name = $num_cia . '-' . $Folio;
									
									rename($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $lcomprobantes_xml . $num_cia . '/' . $new_file_name . '.xml');
									rename($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $lcomprobantes_pdf . $num_cia . '/' . $new_file_name . '.pdf');
								}
								
								$db->query($sql_tmp);
								
								$generadas++;
								
								/*
								@ Proceso de envío de correo electrónico
								*/
								$mail = new PHPMailer ();
		
								if ($num_cia >= 900) {
									$mail->IsSMTP();
									$mail->Host = 'mail.zapateriaselite.com';
									$mail->Port = 587;
									$mail->SMTPAuth = true;
									$mail->Username = 'facturas.electronicas+zapateriaselite.com';
									$mail->Password = 'facturaselectronicas';
									
									$mail->From = 'facturas.electronicas@zapateriaselite.com';
									$mail->FromName = 'Zapaterías Elite :: Facturación Electrónica';
									
									$mail->AddBCC('fe.almacen@zapateriaselite.com');
								}
								else {
									$mail->IsSMTP();
									$mail->Host = 'mail.lecaroz.com';
									$mail->Port = 587;
									$mail->SMTPAuth = true;
									$mail->Username = 'facturas.electronicas+lecaroz.com';
									$mail->Password = 'L3c4r0z*';
									
									$mail->From = 'facturas.electronicas@lecaroz.com';
									$mail->FromName = 'Lecaroz :: Facturación Electrónica';
									
									$mail->AddBCC('fe.almacen@lecaroz.com');
								}
								
								/*
								@ Email para compañía
								*/
								if (trim($series[$num_cia]['email']) != '') {
									$mail->AddCC($series[$num_cia]['email']);
								}
								
								/*
								@ Email para Elite
								*/
								if ($num_cia >= 900) {
									$mail->AddBCC('contabilidad@zapateriaselite.com');
								}
								/*
								@ Email para Lecaroz
								*/
								else {
									$mail->AddBCC('beatriz.flores@lecaroz.com');
								}
								
								/*
								@ Email para cliente
								*/
								if ($email_cliente != '') {
									$mail->AddAddress($email_cliente);
								}
								
								$mail->Subject = 'COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'];
								$mail->Body = '<p><strong>COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'] . '</strong></p><p>Comprobante no. ' . $series[$num_cia]['ultimo_folio_usado'] . '</p><hr><p style="font-weight:bold;">Favor de no responder a este correo electr&oacute;nico. Este buz&oacute;n no se supervisa y no recibir&aacute; respuesta. Si necesita ayuda, escriba al correo <a href="mailto:' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '">' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '</a> y con gusto le atenderemos. </p>';
								$mail->IsHTML(true);
								
								$mail->AddAttachment($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf');
								$mail->AddAttachment($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml');
								
								if(!$mail->Send()) {
									echo "\n@@No se pudo enviar el correo a todos los destinatarios: " . $mail->ErrorInfo . '@@';
								}
							}
						}
					}
				}
			}
			
			$num_cia = $rec['num_cia'];
			
			$consecutivo = NULL;
		}
		
		if ($consecutivo != $rec['consecutivo']) {
			if ($consecutivo != NULL && $ok) {
				/*
				@ Cerrar archivo de datos
				*/
				fclose($fp);
				
				echo '>>Completo!!!<<';
				echo "\n* Colocando archivo de datos en el servidor... ";
				
				/*
				@ Colocar archivo de datos en el servidor
				*/
				$retries = 0;
				do {
					$uploaded = @ftp_put($ftp, $rdatos . $file_name . '.txt', $ldatos . $file_name . '.txt', FTP_BINARY);
					$retries++;
				} while (!$uploaded && $retries < 50);
				
				if (!$uploaded) {
					echo "\n@@No se puede enviar el archivo \"" . $file_name . '" al servidor@@';
					
					/*
					@ Decrementar folios usados en 1
					*/
					$series[$num_cia]['ultimo_folio_usado']--;
					
					$errores++;
				}
				else {
					echo '>>Completo!!!<<';
					echo "\n* Generando CFD... ";
					
					/*
					@ Hacer petición al servidor para generar CFD
					*/
					$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $num_cia . '&archivo=' . $file_name . '.txt';
					if (!($result = file_get_contents($url))) {
						echo "\n@@Imposible acceder al generador de facturas electronicas@@";
						
						/*
						@ Borrar archivo del servidor
						*/
						if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
							echo "\n@@No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
						}
						
						/*
						@ Decrementar folios usados en 1
						*/
						$series[$num_cia]['ultimo_folio_usado']--;
						
						$errores++;
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
							echo "\n@@Error al generar el CFD: \"" . $Error . '"@@';
							
							/*
							@ Borrar archivo del servidor
							*/
							if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
								echo "\n@@No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
							}
							
							/*
							@ Decrementar folios usados en 1
							*/
							$series[$num_cia]['ultimo_folio_usado']--;
							
							$errores++;
						}
						else {
							echo '>>Completo!!!<<';
							
							/*
							@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
							*/
							if (!is_dir($lcomprobantes_xml . $num_cia)) {
								mkdir($lcomprobantes_xml . $num_cia);
							}
							
							/*
							@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
							*/
							if (!is_dir($lcomprobantes_pdf . $num_cia)) {
								mkdir($lcomprobantes_pdf . $num_cia);
							}
							
							/*
							@ Obtener archivo comprobante XML
							*/
							$retries = 0;
							do {
								$downloaded_xml = @ftp_get($ftp, $lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $rcomprobantes_xml . $num_cia . '/' . $ComprobanteXML, FTP_BINARY);
								$retries++;
							} while (!$downloaded_xml && $retries < 50);
							
							if (!$downloaded_xml) {
								echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.xml" (' . $ComprobanteXML . ') del servidor@@';
							}
							
							/*
							@ Obtener archivo comprobante PDF
							*/
							$retries = 0;
							do {
								$downloaded_pdf = @ftp_get($ftp, $lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $rcomprobantes_pdf . $num_cia . '/' . $ComprobantePDF, FTP_BINARY);
								$retries++;
							} while (!$downloaded_pdf && $retries < 50);
							
							if (!$downloaded_pdf) {
								echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.pdf" (' . $ComprobantePDF . ') del servidor@@';
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
										num_cia = ' . $num_cia . '
									AND
										tipo_serie = 1
									AND
										consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
							' . ";\n";
							
							/*
							@ Actualizar series
							*/
							$sql_tmp .= '
								UPDATE
									facturas_electronicas_series
								SET
									ultimo_folio_usado = ' . $series[$num_cia]['ultimo_folio_usado'] . '
								WHERE
										num_cia = ' . $num_cia . '
									AND
										status = 1
									AND
										tipo_serie = 1
									AND
										folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
									AND
										folio_final = ' . $series[$num_cia]['folio_final'] . '
							' . ";\n";
							
							/*
							@ Poner serie como terminada si se ha llegado al máximo de folios
							*/
							if ($series[$num_cia]['ultimo_folio_usado'] == $series[$num_cia]['folio_final']) {
								$sql_tmp .= '
									UPDATE
										facturas_electronicas_series
									SET
										status = 2
									WHERE
											num_cia = ' . $num_cia . '
										AND
											status = 1
										AND
											tipo_serie = 1
										AND
											folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
										AND
											folio_final = ' . $series[$num_cia]['folio_final'] . '
								' . ";\n";
							}
							
							$sql_tmp .= '
								UPDATE
									facturas_panaderias_tmp
								SET
									tsreg = now()
								WHERE
									id IN (' . implode(', ', $ids) . ')
							' . ";\n";
							
							/*
							@ [21-Feb-2011] En caso de que el servidor de facturas electrónicas haya brincado folios,
							@ recorrerlos automáticamente y guardar un registro del evento
							*/
							if ($Folio != $series[$num_cia]['ultimo_folio_usado']) {
								$sql .= '
									UPDATE
										facturas_electronicas
									SET
										consecutivo = ' . $Folio . '
									WHERE
											num_cia = ' . $num_cia . '
										AND
											tipo_serie = 1
										AND
											consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
								' . ";\n";
								
								$sql .= '
									UPDATE
										facturas_electronicas_detalle
									SET
										consecutivo = ' . $Folio . '
									WHERE
											num_cia = ' . $num_cia . '
										AND
											tipo_serie = 1
										AND
											consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
								' . ";\n";
								
								$sql .= '
									UPDATE
										facturas_electronicas_series
									SET
										ultimo_folio_usado = ' . $Folio . '
									WHERE
											num_cia = ' . $num_cia . '
										AND
											status = 1
										AND
											tipo_serie = 1
										AND
											folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
										AND
											folio_final = ' . $series[$num_cia]['folio_final'] . '
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
												' . $num_cia . ',
												1,
												' . $series[$num_cia]['ultimo_folio_usado'] . ',
												' . $Folio . ',
												' . $_SESSION['iduser'] . ',
												now()
											)
								' . ";\n";
								
								$series[$num_cia]['ultimo_folio_usado'] = $Folio;
								
								$new_file_name = $num_cia . '-' . $Folio;
								
								rename($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $lcomprobantes_xml . $num_cia . '/' . $new_file_name . '.xml');
								rename($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $lcomprobantes_pdf . $num_cia . '/' . $new_file_name . '.pdf');
							}
							
							$db->query($sql_tmp);
							
							$generadas++;
							
							/*
							@ Proceso de envío de correo electrónico
							*/
							$mail = new PHPMailer ();
		
							if ($num_cia >= 900) {
								$mail->IsSMTP();
								$mail->Host = 'mail.zapateriaselite.com';
								$mail->Port = 587;
								$mail->SMTPAuth = true;
								$mail->Username = 'facturas.electronicas+zapateriaselite.com';
								$mail->Password = 'facturaselectronicas';
								
								$mail->From = 'facturas.electronicas@zapateriaselite.com';
								$mail->FromName = 'Zapaterías Elite :: Facturación Electrónica';
								
								$mail->AddBCC('fe.almacen@zapateriaselite.com');
							}
							else {
								$mail->IsSMTP();
								$mail->Host = 'mail.lecaroz.com';
								$mail->Port = 587;
								$mail->SMTPAuth = true;
								$mail->Username = 'facturas.electronicas+lecaroz.com';
								$mail->Password = 'L3c4r0z*';
								
								$mail->From = 'facturas.electronicas@lecaroz.com';
								$mail->FromName = 'Lecaroz :: Facturación Electrónica';
								
								$mail->AddBCC('fe.almacen@lecaroz.com');
							}
							
							/*
							@ Email para compañía
							*/
							if (trim($series[$num_cia]['email']) != '') {
								$mail->AddCC($series[$num_cia]['email']);
							}
							
							/*
							@ Email para Elite
							*/
							if ($num_cia >= 900) {
								$mail->AddBCC('contabilidad@zapateriaselite.com');
							}
							/*
							@ Email para Lecaroz
							*/
							else {
								$mail->AddBCC('beatriz.flores@lecaroz.com');
							}
							
							/*
							@ Email para cliente
							*/
							if ($email_cliente != '') {
								$mail->AddAddress($email_cliente);
							}
							
							$mail->Subject = 'COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'];
							$mail->Body = '<p><strong>COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'] . '</strong></p><p>Comprobante no. ' . $series[$num_cia]['ultimo_folio_usado'] . '</p><hr><p style="font-weight:bold;">Favor de no responder a este correo electr&oacute;nico. Este buz&oacute;n no se supervisa y no recibir&aacute; respuesta. Si necesita ayuda, escriba al correo <a href="mailto:' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '">' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '</a> y con gusto le atenderemos. </p>';
							$mail->IsHTML(true);
							
							$mail->AddAttachment($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf');
							$mail->AddAttachment($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml');
							
							if(!$mail->Send()) {
								echo "\n@@No se pudo enviar el correo a todos los destinatarios: " . $mail->ErrorInfo . '@@';
							}
						}
					}
				}
			}
			
			$consecutivo = $rec['consecutivo'];
			
			if (isset($series[$num_cia]) && $series[$num_cia]['ultimo_folio_usado'] + 1 < $series[$num_cia]['folio_final']) {
				$series[$num_cia]['ultimo_folio_usado']++;
				
				echo "\n\n>>COMPAÑIA \"" . $num_cia . '"<<';
				echo "\n>>FOLIO    \"" . $series[$num_cia]['ultimo_folio_usado'] . '"<<';
				
				echo "\n* Creando archivo de datos... ";
				
				$email_cliente = $rec['email_cliente'];
				
				$file_name = $num_cia . '-' . $series[$num_cia]['serie'] . $series[$num_cia]['ultimo_folio_usado'];
				if (!($fp = @fopen($ldatos . $file_name . '.txt', 'wb+'))) {
					echo "\n@@No se pudo crear el archivo de datos \"" . $file_name . '.txt"@@';
					
					$ok = FALSE;
					
					$series[$num_cia]['ultimo_folio_usado']--;
					
					$errores++;
				}
				else {
					$pieces = array();
					
					$pieces[] = $series[$num_cia]['fecha'] . ' ' . $rec['hora'];
					$pieces[] = strtoupper($rec['observaciones']);
					$pieces[] = 'EFECTIVO';
					$pieces[] = $rec['clave_cliente'];
					$pieces[] = strtoupper($rec['nombre_cliente']);
					$pieces[] = strtoupper($rec['rfc']);
					$pieces[] = trim($rec['pais']) != '' ? strtoupper($rec['pais']) : 'MEXICO';
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
					$pieces[] = $rec['iva'] > 0 ? 16 : 0;
					$pieces[] = $rec['importe'];
					$pieces[] = 0;
					$pieces[] = $rec['iva'];
					$pieces[] = 0;
					$pieces[] = 0;
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
									total,
									fecha_pago,
									iduser_ins,
									tsins
								)
							VALUES
								(
									' . $num_cia . ',
									\'' . $series[$num_cia]['fecha'] . '\',
									\'' . $rec['hora'] . '\',
									1,
									' . $series[$num_cia]['ultimo_folio_usado'] . ',
									' . ($rec['rfc'] == 'XAXX010101000' ? 1 : 2) . ',
									' . $rec['clave_cliente'] . ',
									\'' . pg_escape_string(strtoupper($rec['nombre_cliente'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['rfc'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['calle'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['no_exterior'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['no_interior'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['colonia'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['localidad'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['referencia'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['municipio'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['estado'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['pais'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['codigo_postal'])) . '\',
									\'' . pg_escape_string(strtolower($rec['email_cliente'])) . '\',
									\'' . pg_escape_string(strtoupper($rec['observaciones'])) . '\',
									' . $rec['importe'] . ',
									' . $rec['iva'] . ',
									' . $rec['total'] . ',
									\'' . $series[$num_cia]['fecha'] . '\',
									0,
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
					
					$clave_producto = 1;
					
					$ok = TRUE;
					
					$ids = array();
				}
			}
			else {
				$ok = FALSE;
				
				$errores++;
			}
		}
		
		if ($ok) {
			$pieces = array();
			
			$pieces[] = $clave_producto;
			$pieces[] = strtoupper($rec['descripcion']);
			$pieces[] = $rec['cantidad'];
			$pieces[] = strtoupper($rec['unidad']);
			$pieces[] = $rec['precio'];
			$pieces[] = 0;
			$pieces[] = $rec['iva'] > 0 ? 'No' : 'Si';
			$pieces[] = $rec['iva'] > 0 ? 16 : 0;
			$pieces[] = $rec['iva'] > 0 ? round($rec['cantidad'] * $rec['precio'] * 0.16, 2) : 0;
			$pieces[] = '';
			$pieces[] = '';
			$pieces[] = '';
			
			$cadena = implode('|', $pieces) . "\r\n";
			
			fwrite($fp, $cadena);
			
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
							' . $num_cia . ',
							1,
							' . $series[$num_cia]['ultimo_folio_usado'] . ',
							' . $clave_producto++ . ',
							' . $rec['cantidad'] . ',
							\'' . pg_escape_string(strtoupper($rec['descripcion'])) . '\',
							' . $rec['precio'] . ',
							\'' . pg_escape_string(strtoupper($rec['unidad'])) . '\',
							' . $rec['importe'] . '
						)
						
			' . ";\n";
			
			$ids[] = $rec['id'];
		}
	}
	
	if ($num_cia != NULL && $ok) {
		if ($consecutivo != NULL && $ok) {
			/*
			@ Cerrar archivo de datos
			*/
			fclose($fp);
			
			echo '>>Completo!!!<<';
			echo "\n* Colocando archivo de datos en el servidor...";
			
			/*
			@ Colocar archivo de datos en el servidor
			*/
			$retries = 0;
			do {
				$uploaded = @ftp_put($ftp, $rdatos . $file_name . '.txt', $ldatos . $file_name . '.txt', FTP_BINARY);
				$retries++;
			} while (!$uploaded && $retries < 50);
			
			if (!$uploaded) {
				echo "\n@@No se puede enviar el archivo \"" . $file_name . '" al servidor@@';
				
				/*
				@ Decrementar folios usados en 1
				*/
				$series[$num_cia]['ultimo_folio_usado']--;
			}
			else {
				echo '>>Completo!!!<<';
				echo "\n* Generando CFD... ";
				
				/*
				@ Hacer petición al servidor para generar CFD
				*/
				$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $num_cia . '&archivo=' . $file_name . '.txt';
				if (!($result = file_get_contents($url))) {
					echo "\n@@Imposible acceder al generador de facturas electronicas@@";
					
					/*
					@ Borrar archivo del servidor
					*/
					if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
						echo "\n@@No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
					}
					
					/*
					@ Decrementar folios usados en 1
					*/
					$series[$num_cia]['ultimo_folio_usado']--;
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
						echo "\n@@Error al generar el CFD: \"" . $Error . '"@@';
						
						/*
						@ Borrar archivo del servidor
						*/
						if (!@ftp_delete($ftp, $rdatos . $file_name . '.txt')) {
							echo "\n@@>No se pudo borrar el archivo \"" . $file_name . '.txt" del servidor@@';
						}
						
						/*
						@ Decrementar folios usados en 1
						*/
						$series[$num_cia]['ultimo_folio_usado']--;
					}
					else {
						echo '>>Completo!!!<<';
						
						/*
						@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
						*/
						if (!is_dir($lcomprobantes_xml . $num_cia)) {
							mkdir($lcomprobantes_xml . $num_cia);
						}
						
						/*
						@ [27-Ene-2011] Validar que el directorio para almacenar los comprobantes XML exista en el servidor
						*/
						if (!is_dir($lcomprobantes_pdf . $num_cia)) {
							mkdir($lcomprobantes_pdf . $num_cia);
						}
						
						/*
						@ Obtener archivo comprobante XML
						*/
						$retries = 0;
						do {
							$downloaded_xml = @ftp_get($ftp, $lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $rcomprobantes_xml . $num_cia . '/' . $ComprobanteXML, FTP_BINARY);
							$retries++;
						} while (!$downloaded_xml && $retries < 50);
						
						if (!$downloaded_xml) {
							echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.xml" (' . $ComprobanteXML . ') del servidor@@';
						}
						
						/*
						@ Obtener archivo comprobante PDF
						*/
						$retries = 0;
						do {
							$downloaded_pdf = @ftp_get($ftp, $lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $rcomprobantes_pdf . $num_cia . '/' . $ComprobantePDF, FTP_BINARY);
							$retries++;
						} while (!$downloaded_pdf && $retries < 50);
						
						if (!$downloaded_pdf) {
							echo "\n@@No se pudo obtener el archivo \"" . $file_name . '.pdf" (' . $ComprobantePDF . ') del servidor@@';
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
									num_cia = ' . $num_cia . '
								AND
									tipo_serie = 1
								AND
									consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
						' . ";\n";
						
						/*
						@ Actualizar series
						*/
						$sql_tmp .= '
							UPDATE
								facturas_electronicas_series
							SET
								ultimo_folio_usado = ' . $series[$num_cia]['ultimo_folio_usado'] . '
							WHERE
									num_cia = ' . $num_cia . '
								AND
									status = 1
								AND
									tipo_serie = 1
								AND
									folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
								AND
									folio_final = ' . $series[$num_cia]['folio_final'] . '
						' . ";\n";
						
						/*
						@ Poner serie como terminada si se ha llegado al máximo de folios
						*/
						if ($series[$num_cia]['ultimo_folio_usado'] == $series[$num_cia]['folio_final']) {
							$sql_tmp .= '
								UPDATE
									facturas_electronicas_series
								SET
									status = 2
								WHERE
										num_cia = ' . $num_cia . '
									AND
										status = 1
									AND
										tipo_serie = 1
									AND
										folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
									AND
										folio_final = ' . $series[$num_cia]['folio_final'] . '
							' . ";\n";
						}
						
						$sql_tmp .= '
							UPDATE
								facturas_panaderias_tmp
							SET
								tsreg = now()
							WHERE
								id IN (' . implode(', ', $ids) . ')
						' . ";\n";
						
						/*
						@ [21-Feb-2011] En caso de que el servidor de facturas electrónicas haya brincado folios,
						@ recorrerlos automáticamente y guardar un registro del evento
						*/
						if ($Folio != $series[$num_cia]['ultimo_folio_usado']) {
							$sql .= '
								UPDATE
									facturas_electronicas
								SET
									consecutivo = ' . $Folio . '
								WHERE
										num_cia = ' . $num_cia . '
									AND
										tipo_serie = 1
									AND
										consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
							' . ";\n";
							
							$sql .= '
								UPDATE
									facturas_electronicas_detalle
								SET
									consecutivo = ' . $Folio . '
								WHERE
										num_cia = ' . $num_cia . '
									AND
										tipo_serie = 1
									AND
										consecutivo = ' . $series[$num_cia]['ultimo_folio_usado'] . '
							' . ";\n";
							
							$sql .= '
								UPDATE
									facturas_electronicas_series
								SET
									ultimo_folio_usado = ' . $Folio . '
								WHERE
										num_cia = ' . $num_cia . '
									AND
										status = 1
									AND
										tipo_serie = 1
									AND
										folio_inicial = ' . $series[$num_cia]['folio_inicial'] . '
									AND
										folio_final = ' . $series[$num_cia]['folio_final'] . '
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
											' . $num_cia . ',
											1,
											' . $series[$num_cia]['ultimo_folio_usado'] . ',
											' . $Folio . ',
											' . $_SESSION['iduser'] . ',
											now()
										)
							' . ";\n";
							
							$series[$num_cia]['ultimo_folio_usado'] = $Folio;
							
							$new_file_name = $num_cia . '-' . $Folio;
							
							rename($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml', $lcomprobantes_xml . $num_cia . '/' . $new_file_name . '.xml');
							rename($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf', $lcomprobantes_pdf . $num_cia . '/' . $new_file_name . '.pdf');
						}
						
						$db->query($sql_tmp);
						
						$generadas++;
						
						/*
						@ Proceso de envío de correo electrónico
						*/
						$mail = new PHPMailer ();

						if ($num_cia >= 900) {
							$mail->IsSMTP();
							$mail->Host = 'mail.zapateriaselite.com';
							$mail->Port = 587;
							$mail->SMTPAuth = true;
							$mail->Username = 'facturas.electronicas+zapateriaselite.com';
							$mail->Password = 'facturaselectronicas';
							
							$mail->From = 'facturas.electronicas@zapateriaselite.com';
							$mail->FromName = 'Zapaterías Elite :: Facturación Electrónica';
							
							$mail->AddBCC('fe.almacen@zapateriaselite.com');
						}
						else {
							$mail->IsSMTP();
							$mail->Host = 'mail.lecaroz.com';
							$mail->Port = 587;
							$mail->SMTPAuth = true;
							$mail->Username = 'facturas.electronicas+lecaroz.com';
							$mail->Password = 'L3c4r0z*';
							
							$mail->From = 'facturas.electronicas@lecaroz.com';
							$mail->FromName = 'Lecaroz :: Facturación Electrónica';
							
							$mail->AddBCC('fe.almacen@lecaroz.com');
						}
						
						/*
						@ Email para compañía
						*/
						if (trim($series[$num_cia]['email']) != '') {
							$mail->AddCC($series[$num_cia]['email']);
						}
						
						/*
						@ Email para Elite
						*/
						if ($num_cia >= 900) {
							$mail->AddBCC('contabilidad@zapateriaselite.com');
						}
						/*
						@ Email para Lecaroz
						*/
						else {
							$mail->AddBCC('beatriz.flores@lecaroz.com');
						}
						
						/*
						@ Email para cliente
						*/
						if ($email_cliente != '') {
							$mail->AddAddress($email_cliente);
						}
						
						$mail->Subject = 'COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'];
						$mail->Body = '<p><strong>COMPROBANTE FISCAL DIGITAL :: ' . $series[$num_cia]['nombre_cia'] . '</strong></p><p>Comprobante no. ' . $series[$num_cia]['ultimo_folio_usado'] . '</p><hr><p style="font-weight:bold;">Favor de no responder a este correo electr&oacute;nico. Este buz&oacute;n no se supervisa y no recibir&aacute; respuesta. Si necesita ayuda, escriba al correo <a href="mailto:' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '">' . ($num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com') . '</a> y con gusto le atenderemos. </p>';
						$mail->IsHTML(true);
						
						$mail->AddAttachment($lcomprobantes_pdf . $num_cia . '/' . $file_name . '.pdf');
						$mail->AddAttachment($lcomprobantes_xml . $num_cia . '/' . $file_name . '.xml');
						
						if(!$mail->Send()) {
							echo "\n@@No se pudo enviar el correo a todos los destinatarios: " . $mail->ErrorInfo . '@@';
						}
					}
				}
			}
		}
	}
	
	ftp_close($ftp);
	
	echo "\n\n** Generadas: " . number_format($generadas);
	echo "\n** Errores:   " . number_format($errores);
	echo "\n** Total:     " . number_format($generadas + $errores);
	
	/*
	@ Poner bandera de servidor disponible
	*/
	$sql = '
		UPDATE
			facturas_electronicas_server_status
		SET
			status = \'TRUE\',
			iduser = 0,
			tsmod = now()
	';
	
	$db->query($sql);
	
	echo "\n\n---FINAL---\n\n";
}
?>
