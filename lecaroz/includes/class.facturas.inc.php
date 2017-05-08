<?php

class FacturasClass {
	
	const SERVER_BLOCKED = TRUE;
	const SERVER_FREE = FALSE;
	
	private $ftp_server = '192.168.1.70';
	private $ftp_user = 'mollendo';
	private $ftp_pass = 'L3c4r0z*';
	private $ftp;
	private $ftp_status;
	
	/*
	@ Rutas locales
	*/
	private $ldatos = 'facturas/datos/';
	private $lcomprobantes_xml = 'facturas/comprobantes_xml/';
	private $lcomprobantes_pdf = 'facturas/comprobantes_pdf/';
	private $lcodigos_qr = 'facturas/codigos_qr/';
	
	/*
	@ Rutas servidor
	*/
	private $rdatos = 'carga/';
	private $rcomprobantes_xml = 'comprobantes/';
	private $rcomprobantes_pdf = 'comprobantes/';
	
	private $comprobante_xml;
	private $comprobante_pdf;
	
	private $file_name;
	
	private $iduser;
	
	private $num_cia;
	private $tipo;
	private $serie;
	private $datos;
	private $folio;
	
	private $status;
	
	private $last_error;
	
	private $header_error;
	
	function __construct() {
		global $db;
		
		/*
		@ Validar que exista una conexión a la base de datos
		*/
		if (!isset($db)) {
			trigger_error('No existe la instancia $db para la conexión a la base de datos', E_USER_ERROR);
		}
		
		/*
		@ Conectarse al servidor FTP
		*/
		if (!($this->ftp = @ftp_connect($this->ftp_server))) {
			$this->ftp_status = -1;
		}
		/*
		@ Iniciar sesión en el servidor FTP
		*/
		else if (!@ftp_login($this->ftp, $this->ftp_user, $this->ftp_pass)) {
			$this->ftp_status = -2;
		}
		/*
		@ Conexión FTP correcta
		*/
		else {
			$this->ftp_status = 0;
		}
		
	}
	
	function __destruct() {
		/*
		@ Cerrar conexión FTP
		*/
		if (!($this->ftp_status < 0)) {
			@ftp_close($this->ftp);
		}
	}
	
	private function validarStatusServidor() {
		global $db;
		
		$sql = '
			SELECT
				id
			FROM
				facturas_electronicas_server_status_new
			WHERE
				num_cia = ' . $this->num_cia . '
		';
		$status = $db->query($sql);
		
		if ($status) {
			$this->status = -3;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	private function cambiarStatusServidor($status) {
		global $db;
		
		if ($status) {
			$sql = '
				INSERT INTO
					facturas_electronicas_server_status_new
						(
							num_cia,
							iduser
						)
					VALUES
						(
							' . $this->num_cia . ',
							' . $this->iduser . '
						)
			';
		}
		else {
			$sql = '
				DELETE FROM
					facturas_electronicas_server_status_new
				WHERE
					num_cia = ' . $this->num_cia . '
			';
		}
		
		$db->query($sql);
	}
	
	private function validarSerie() {
		global $db;
		
		$sql = '
			SELECT
				serie,
				tipo_factura,
				folio_inicial,
				folio_final,
				ultimo_folio_usado + 1
					AS folio,
				no_aprobacion,
				fecha_aprobacion,
				codigo_qr,
				nombre
					AS nombre_cia,
				cc.email,
				con.email
					AS email_contador,
				razon_social,
				rfc,
				calle,
				no_exterior,
				no_interior,
				colonia,
				municipio,
				estado,
				pais,
				codigo_postal
			FROM
				facturas_electronicas_series fes
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
				LEFT JOIN catalogo_contadores con
					USING (idcontador)
			WHERE
				num_cia = ' . $this->num_cia . '
				AND fes.tipo_serie = ' . $this->tipo . '
				AND fes.status = 1
		';
		
		$result = $db->query($sql);
		
		if ($result) {
			$this->serie = $result[0];
			
			$this->serie['serie'] = $this->serie['serie'];
			
			return TRUE;
		}
		else {
			$this->status = -4;
			
			return FALSE;
		}
	}
	
	private function validarDatos() {
		$this->header_error = array();
		
		/*
		@ Validar datos de cabecera
		*/
		
		if ($this->datos['cabecera']['nombre_cliente'] == '') {
			$this->header_error[] = -101;
		}
		
		if ($this->datos['cabecera']['rfc_cliente'] == '') {
			$this->header_error[] = -102;
		}
		else if (preg_match_all("/^([a-zA-ZñÑ\&]{3,4})([\d]{6})([a-zA-Z0-9]{3})$/", $this->datos['cabecera']['rfc_cliente'], $matches) == 0) {
			$this->header_error[] = -103;
		}
		
		if ($this->datos['cabecera']['rfc_cliente'] == 'XAXX010101000') {
			$validar_domicilio = FALSE;
		}
		else {
			$validar_domicilio = TRUE;
		}
		
		if ($validar_domicilio && $this->datos['cabecera']['calle'] == '') {
			$this->header_error[] = -104;
		}
		
		if ($validar_domicilio && $this->datos['cabecera']['colonia'] == '') {
			$this->header_error[] = -105;
		}
		
		if ($validar_domicilio && $this->datos['cabecera']['municipio'] == '') {
			$this->header_error[] = -106;
		}
		
		if ($validar_domicilio && $this->datos['cabecera']['estado'] == '') {
			$this->header_error[] = -107;
		}
		
		if ($this->datos['cabecera']['pais'] == '') {
			$this->header_error[] = -108;
		}
		
		if ($validar_domicilio && $this->datos['cabecera']['codigo_postal'] == '') {
			$this->header_error[] = -109;
		}
		
		if (array_sum($this->header_error) < 0) {
			$this->status = -100;
			
			return FALSE;
		}
		
		/*
		@ Validar la suma de detalles
		*/
		
		$subtotal = 0;
		foreach ($this->datos['detalle'] as $detalle) {
			$subtotal += $detalle['importe'];
		}
		
		if (round($subtotal, 2) != round($this->datos['cabecera']['importe'], 2)) {
			$this->status = -150;
			
			return FALSE;
		}
		
		/*
		@ Validar el total
		*/
		
		$total = $this->datos['cabecera']['importe'] - $this->datos['cabecera']['descuento'] + $this->datos['cabecera']['importe_iva'] - $this->datos['cabecera']['importe_retencion_isr'] - $this->datos['cabecera']['importe_retencion_iva'];
		
		if (round($total, 2) != round($this->datos['cabecera']['total'], 2)) {
			$this->status = -151;
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function validarDuplicados() {
		global $db;
		
		if ($this->num_cia >= 900) {
			return TRUE;
		}
		
		/*
		* [03-May-2012] El intervalo de 15 días se redujo solo al día que se esta emitiendo la factura
		*/
		
		$sql = '
			SELECT
				id
			FROM
				facturas_electronicas
			WHERE
				num_cia = ' . $this->num_cia . '
				AND rfc = \'' . $this->datos['cabecera']['rfc_cliente'] . '\'
				AND tipo = 2
				AND tipo_serie = 1
				AND fecha BETWEEN \'' . $this->datos['cabecera']['fecha'] . '\'::DATE/* - INTERVAL \'15 days\'*/ AND \'' . $this->datos['cabecera']['fecha'] . '\'::DATE
				AND total BETWEEN ' . ($this->datos['cabecera']['total'] - 10) . ' AND ' . ($this->datos['cabecera']['total'] + 10) . '
				AND status = 1
				AND (num_cia, TRIM(nombre_cliente)) NOT IN (
					SELECT
						num_cia,
						nombre_cliente
					FROM
						facturas_electronicas_excluir_duplicados
					WHERE
						num_cia = ' . $this->num_cia . '
				)
			LIMIT
				1
		';
		
		$result = $db->query($sql);
		
		if ($this->iduser == 0 && $result) {
			$this->status = -80;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	private function validarFolio() {
		global $db;
		
		$sql = '
			SELECT
				id
			FROM
				facturas_electronicas
			WHERE
				num_cia = ' . $this->num_cia . '
				AND tipo_serie = ' . $this->tipo . '
				AND consecutivo = ' . $this->folio . '
		';
		
		$result = $db->query($sql);
		
		if ($result) {
			$this->status = -160;
			
			return FALSE;
		}
		else if ($this->folio > $this->serie['folio']) {
			$this->status = -161;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	private function generarFacturaElectronica() {
		global $db;
		
		$this->cambiarStatusServidor(self::SERVER_BLOCKED);
		
		/*
		@ Crear archivo de carga
		*/
		$this->file_name = $this->num_cia . '-' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']);
		
		if (!($fp = @fopen($this->ldatos . $this->file_name . '.txt', 'wb+'))) {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			return -5;
		}
		
		/*
		@ Generar cadenas de datos
		*/
		
		$cabecera = array();
		
		/*
		@ Fecha y hora
		*/
		$cabecera[] = $this->datos['cabecera']['fecha'] . ' ' . $this->datos['cabecera']['hora'];
		/*
		@ Observaciones
		*/
		$cabecera[] = $this->datos['cabecera']['observaciones'];
		/*
		@ Forma de pago
		*/
		$cabecera[] = 'EFECTIVO';
		/*
		@ Clave del cliente
		*/
		$cabecera[] = $this->datos['cabecera']['clave_cliente'];
		/*
		@ Nombre del cliente
		*/
		$cabecera[] = $this->datos['cabecera']['nombre_cliente'];
		/*
		@ RFC del cliente
		*/
		$cabecera[] = $this->datos['cabecera']['rfc_cliente'];
		/*
		@ Domicilio fiscal del cliente: Pais
		*/
		$cabecera[] = $this->datos['cabecera']['pais'];
		/*
		@ Domicilio fiscal del cliente: Estado
		*/
		$cabecera[] = $this->datos['cabecera']['estado'];
		/*
		@ Domicilio fiscal del cliente: Código postal
		*/
		$cabecera[] = $this->datos['cabecera']['codigo_postal'];
		/*
		@ Domicilio fiscal del cliente: Colonia
		*/
		$cabecera[] = $this->datos['cabecera']['colonia'];
		/*
		@ Domicilio fiscal del cliente: Calle
		*/
		$cabecera[] = $this->datos['cabecera']['calle'];
		/*
		@ Domicilio fiscal del cliente: Número exterior
		*/
		$cabecera[] = $this->datos['cabecera']['no_exterior'];
		/*
		@ Domicilio fiscal del cliente: Número interior
		*/
		$cabecera[] = $this->datos['cabecera']['no_interior'];
		/*
		@ Domicilio fiscal del cliente: Localidad
		*/
		$cabecera[] = $this->datos['cabecera']['localidad'];
		/*
		@ Domicilio fiscal del cliente: Municipio
		*/
		$cabecera[] = $this->datos['cabecera']['municipio'];
		/*
		@ Domicilio fiscal del cliente: Delegación o municipio
		*/
		$cabecera[] = $this->datos['cabecera']['referencia'];
		/*
		@ S=Tiene retenciones, N=No tiene retenciones
		*/
		$cabecera[] = $this->datos['cabecera']['aplicar_retenciones'];
		/*
		@ Tipo de documento
		@    1 Factura
		@    2 Recibo de arrendamiento
		@    3 Nota de crédito
		*/
		$cabecera[] = $this->tipo;
		/*
		@ Porcentaje de I.V.A.
		*/
		$cabecera[] = $this->datos['cabecera']['porcentaje_iva'];
		/*
		@ Importe de la factura
		*/
		$cabecera[] = $this->datos['cabecera']['importe'];
		/*
		@ Importe de descuentos
		*/
		$cabecera[] = $this->datos['cabecera']['descuento'];
		/*
		@ Importe de I.V.A.
		*/
		$cabecera[] = $this->datos['cabecera']['importe_iva'];
		/*
		@ Importe de retención de I.S.R.
		*/
		$cabecera[] = $this->datos['cabecera']['importe_retencion_isr'];
		/*
		@ Importe de retención de I.V.A.
		*/
		$cabecera[] = $this->datos['cabecera']['importe_retencion_iva'];
		/*
		@ Total de la factura
		*/
		$cabecera[] = $this->datos['cabecera']['total'];
		
		/*
		@ [07-Sep-2011] Folio de factura
		*/
		$cabecera[] = $this->folio > 0 ? $this->folio : $this->serie['folio'];
		
		fwrite($fp, implode('|', $cabecera) . "\r\n");
		
		$consignatario = array();
		
		/*
		@ Nombre del consignatario
		*/
		$consignatario[] = $this->datos['consignatario']['nombre'];
		/*
		@ R.F.C. del consignatario
		*/
		$consignatario[] = $this->datos['consignatario']['rfc'];
		/*
		@ Domicilio fiscal del consignatario: País
		*/
		$consignatario[] = $this->datos['consignatario']['pais'];
		/*
		@ Domicilio fiscal del consignatario: Estado
		*/
		$consignatario[] = $this->datos['consignatario']['estado'];
		/*
		@ Domicilio fiscal del consignatario: Código postal
		*/
		$consignatario[] = $this->datos['consignatario']['codigo_postal'];
		/*
		@ Domicilio fiscal del consignatario: Colonia
		*/
		$consignatario[] = $this->datos['consignatario']['colonia'];
		/*
		@ Domicilio fiscal del consignatario: Calle
		*/
		$consignatario[] = $this->datos['consignatario']['calle'];
		/*
		@ Domicilio fiscal del consignatario: Número exterior
		*/
		$consignatario[] = $this->datos['consignatario']['no_exterior'];
		/*
		@ Domicilio fiscal del consignatario: Número interior
		*/
		$consignatario[] = $this->datos['consignatario']['no_interior'];
		/*
		@ Domicilio fiscal del consignatario: Localidad
		*/
		$consignatario[] = $this->datos['consignatario']['localidad'];
		/*
		@ Domicilio fiscal del consignatario: Municipio
		*/
		$consignatario[] = $this->datos['consignatario']['municipio'];
		
		fwrite($fp, implode('|', $consignatario) . "\r\n");
		
		foreach ($this->datos['detalle'] as $i => $detalle) {
			$concepto = array();
			
			/*
			@ Clave del producto o servicio
			*/
			$concepto[] = $detalle['clave'];
			/*
			@ Descripcion
			*/
			$concepto[] = $detalle['descripcion'];
			/*
			@ Cantidad
			*/
			$concepto[] = $detalle['cantidad'];
			/*
			@ Unidad
			*/
			$concepto[] = $detalle['unidad'];
			/*
			@ Precio unitario
			*/
			$concepto[] = $detalle['precio'];
			/*
			@ Descuento
			*/
			$concepto[] = $detalle['descuento'];
			/*
			@ Si=Excento de I.V.A., No=No Excento de I.V.A.
			*/
			$concepto[] = $detalle['porcentaje_iva'] > 0 ? 'No' : 'Si';
			/*
			@ Porcentaje de I.V.A.
			*/
			$concepto[] = $detalle['porcentaje_iva'];
			/*
			@ Importe de I.V.A.
			*/
			$concepto[] = $detalle['importe_iva'];
			/*
			@ Número de pedimento
			*/
			$concepto[] = $detalle['numero_pedimento'];
			/*
			@ Fecha de entrada
			*/
			$concepto[] = $detalle['fecha_entrada'];
			/*
			@ Aduana de entrada
			*/
			$concepto[] = $detalle['aduana_entrada'];
			
			fwrite($fp, implode('|', $concepto) . "\r\n");
		}
		
		/*
		@ Cerrar archivo de carga
		*/
		fclose($fp);
		
		/*
		@ Colocar archivo de carga en el servidor
		*/
		if (!@ftp_put($this->ftp, $this->rdatos . $this->file_name . '.txt', $this->ldatos . $this->file_name . '.txt', FTP_BINARY)) {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			return -6;
		}
		
		/*
		@ Hacer petición al servidor para generar CFD
		*/
		$url = 'http://192.168.1.70/clases/servlet/cargaLayoutFE?id_panaderia=' . $this->num_cia . '&archivo=' . $this->file_name . '.txt';
		
		$result = @file_get_contents($url);
		
		if (!$result) {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			/*
			@ Borrar archivo del servidor
			*/
			@ftp_delete($this->ftp, $this->rdatos . $this->file_name . '.txt');
			
			return -7;
		}
		
		if (strpos($result, 'Estatus') === FALSE) {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			/*
			@ Borrar archivo del servidor
			*/
			@ftp_delete($this->ftp, $this->rdatos . $this->file_name . '.txt');
			
			return -8;
		}
		
		/*
		@ Interpretar respuesta del servidor
		*/
		$url_result = explode('|', $result);
		
		foreach ($url_result as $i => $value) {
			list($var, $val) = explode('=', trim($value));
			
			${trim($var)} = trim($val);
		}
		
		/*
		@ El servidor regreso un error del tipo "Existio un error al leer el archivo" (WTF? O_o), no se borrara el archivo de carga para su revisión posterior
		*/
		if ($Error == 'Existio un error al leer el archivo') {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			$this->last_error = $Error . ' [' . $this->file_name . '.txt]';
			
			return -50;
		}
		
		/*
		@ El servidor regreso un error del tipo "Este archivo ya fue cargado" (FUFUFUUUUU? #¬¬), no se desbloqueara el proceso para la compañía emisora
		*/
		if ($Error == 'Este archivo ya fue cargado') {
			$this->last_error = $Error . ' [' . $this->file_name . '.txt]';
			
			$sql = '
				UPDATE
					facturas_electronicas_server_status_new
				SET
					status = -51,
					obs = \'' . $this->last_error . '\'
				WHERE
					num_cia = ' . $this->num_cia . '
			';
			
			$db->query($sql);
			
			/*
			@ Borrar archivo del servidor
			*/
			@ftp_delete($this->ftp, $this->rdatos . $this->file_name . '.txt');
			
			return -51;
		}
		
		/*
		@ El servidor regreso un error, guardar cadena descriptiva del evento
		*/
		if ($Estatus != 1) {
			$this->cambiarStatusServidor(self::SERVER_FREE);
			
			$this->last_error = $Error;
			
			/*
			@ Borrar archivo del servidor
			*/
			!@ftp_delete($this->ftp, $this->rdatos . $this->file_name . 'txt');
			
			return -9;
		}
		
		/*
		@ Si el folio para la factura y el generado por el servidor no coinciden informarlo y no desbloquear proceso para la compañía emisora
		*/
		if ($Folio != ($this->folio > 0 ? $this->folio : $this->serie['folio'])) {
			$this->last_error = 'El folio generado por sistema (' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . ') y el folio retornado por el servidor de facturas electrónicas (' . $Folio . ') no coinciden';
			
			$sql = '
				UPDATE
					facturas_electronicas_server_status_new
				SET
					status = -10,
					obs = \'' . $this->last_error . '\'
				WHERE
					num_cia = ' . $this->num_cia . '
			';
			
			$db->query($sql);
			
			return -10;
		}
		
		/*
		@ Validar que el directorio para almacenar los comprobantes XML exista en el servidor
		*/
		if (!is_dir($this->lcomprobantes_xml . $this->num_cia)) {
			mkdir($this->lcomprobantes_xml . $this->num_cia);
		}
		
		/*
		@ Validar que el directorio para almacenar los comprobantes XML exista en el servidor
		*/
		if (!is_dir($this->lcomprobantes_pdf . $this->num_cia)) {
			mkdir($this->lcomprobantes_pdf . $this->num_cia);
		}
		
		/*
		@ Obtener archivo comprobante XML
		*/
		$retries = 0;
		do {
			$downloaded_xml = @ftp_get($this->ftp, $this->lcomprobantes_xml . $this->num_cia . '/' . $this->file_name . '.xml', $this->rcomprobantes_xml . $this->num_cia . '/' . $ComprobanteXML, FTP_BINARY);
			
			if (!$downloaded_xml) {
				sleep(1);
				
				/*
				@ [22-Jun-2012] Intentar reconectar al servidor ftp
				*/
				$this->ftp = @ftp_connect($this->ftp_server);
				@ftp_login($this->ftp, $this->ftp_user, $this->ftp_pass);
			}
			
			$retries++;
		} while (!$downloaded_xml && $retries < 120);
		
		if (!$downloaded_xml) {
			$this->last_error = 'No se pudo obtener el archivo ' . $this->file_name . '.xml [' . $ComprobanteXML . '] del servidor';
			
			$sql = '
				UPDATE
					facturas_electronicas_server_status_new
				SET
					status = -11,
					obs = \'' . $this->last_error . '\'
				WHERE
					num_cia = ' . $this->num_cia . '
			';
			
			$db->query($sql);
			
			return -11;
		}
		
		/*
		@ Obtener archivo comprobante PDF
		*/
		$retries = 0;
		do {
			$downloaded_pdf = @ftp_get($this->ftp, $this->lcomprobantes_pdf . $this->num_cia . '/' . $this->file_name . '.pdf', $this->rcomprobantes_pdf . $this->num_cia . '/' . $ComprobantePDF, FTP_BINARY);
			
			if (!$downloaded_pdf) {
				sleep(1);
				
				/*
				@ [22-Jun-2012] Intentar reconectar al servidor ftp
				*/
				$this->ftp = @ftp_connect($this->ftp_server);
				@ftp_login($this->ftp, $this->ftp_user, $this->ftp_pass);
			}
			
			$retries++;
		} while (!$downloaded_pdf && $retries < 120);
		
		if (!$downloaded_pdf) {
			$this->last_error = 'No se pudo obtener el archivo ' . $this->file_name . '.pdf [' . $ComprobantePDF . '] del servidor';
			
			$sql = '
				UPDATE
					facturas_electronicas_server_status_new
				SET
					status = -12,
					obs = \'' . $this->last_error . '\'
				WHERE
					num_cia = ' . $this->num_cia . '
			';
			
			$db->query($sql);
			
			return -12;
		}
		
		/*
		@ [22-Jun-2012] Desconectar del servidor FTP
		*/
		//ftp_close($this->ftp);
		
		$this->comprobante_xml = $ComprobanteXML;
		$this->comprobante_pdf = $ComprobantePDF;
		
		$this->registrarFactura();
		
		$this->cambiarStatusServidor(self::SERVER_FREE);
		
		return $this->file_name;
	}
	
	private function generarFacturaImpresa() {
		global $db;
		
		/*
		@ Validar que la librería FPDF este cargada
		*/
		if (!class_exists('FPDF')) {
			include_once('includes/fpdf/fpdf.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once('includes/cheques.inc.php');
		}
		
		$this->cambiarStatusServidor(self::SERVER_BLOCKED);
		
		$pdf = new FPDF('P', 'mm', 'Letter');
		
		$pdf->SetDisplayMode('real', 'single');

		$pdf->SetMargins(0, 0, 0);
		
		$pdf->SetAutoPageBreak(FALSE);
		
		$pdf->AddPage('P', 'Letter');
		
		$pdf->Rect(6, 8, 203, 76, 'D');
		$pdf->Rect(6, 86, 203, 185, 'D');
		$pdf->Line(6, 92, 209, 92);
		$pdf->Line(6, 190, 209, 190);
		
		$pdf->SetFont('Arial', 'B', 10);
		
		$pdf->SetXY(165, 12);
		$pdf->Cell(0, 0, utf8_decode('FOLIO NO.:'));
		
		$pdf->SetFontSize(8);
		
		$pdf->Text(165, 30, utf8_decode('LUGAR DE EXPEDICIÓN'));
		$pdf->Text(165, 45, utf8_decode('FECHA DE EXPEDICIÓN'));
		
		$pdf->SetXY(8, 89);
		$pdf->Cell(85, 0, utf8_decode('DESCRIPCIÓN'));
		$pdf->Cell(23, 0, utf8_decode('CANTIDAD'), 0, 0, 'C');
		$pdf->Cell(23, 0, utf8_decode('UNIDAD'), 0, 0, 'C');
		$pdf->Cell(23, 0, utf8_decode('PRECIO'), 0, 0, 'C');
		$pdf->Cell(23, 0, utf8_decode('DESCUENTO'), 0, 0, 'C');
		$pdf->Cell(23, 0, utf8_decode('IMPORTE'), 0, 0, 'C');
		
		$pdf->SetXY(8, 193);
		$pdf->Cell(0, 0, utf8_decode('TOTAL CON LETRA:'));
		$pdf->SetXY(160, 193);
		$pdf->Cell(0, 0, utf8_decode('SUBTOTAL'));
		$pdf->SetXY(160, 198);
		$pdf->Cell(0, 0, utf8_decode('DESCUENTO'));
		$pdf->SetXY(160, 203);
		$pdf->Cell(0, 0, utf8_decode('IVA'));
		$pdf->SetXY(160, 208);
		$pdf->Cell(0, 0, 'TOTAL');
		
		$pdf->SetFont('Arial', '', 8);
		
		$pdf->SetXY(55, 218);
		$pdf->Cell(0, 0, utf8_decode('NÚMERO DE APROBACION SICOFI:'));
		
		$pdf->SetXY(55, 228);
		$pdf->Cell(0, 0, utf8_decode('NÚMERO Y FECHA DE DOCUMENTO ADUANERO:'));
		$pdf->SetXY(55, 231);
		$pdf->Cell(0, 0, utf8_decode('ADUANA:'));
		
		$pdf->SetFontSize(6);
		
		$pdf->SetXY(55, 234);
		$pdf->Cell(0, 0, utf8_decode('(sólo aplica en la importación de mercancías respecto de las que realicen ventas de primera mano)'));
		
		$pdf->SetFont('Arial', 'B', 6);
		
		$pdf->SetXY(15, 258);
		$pdf->Cell(0, 0, utf8_decode('La reproducción apócrifa de este comprobante constituye un delito en los términos de las disposiciones fiscales'));
		$pdf->SetXY(15, 261);
		$pdf->Cell(0, 0, utf8_decode('Este comprobante tendrá una vigencia de dos años a partir de la fecha de aprobación de la asignación de los folios, la cual es:'));
		$pdf->SetXY(15, 266);
		$pdf->Cell(0, 0, utf8_decode('Pago en una sola exhibición'));
		
		$pdf->Image('imagenes/logo_lecaroz_panaderias.jpg', 7, 12, 70);
		
		$pdf->Image($this->lcodigos_qr . $this->serie['codigo_qr'], 10, 202, 35, 35);
		
		$pdf->SetFont('Arial', 'B', 8);
		
		$pdf->SetXY(165, 18);
		
		switch ($this->tipo) {
			case 1:
				$tipo_doc = 'FACTURA';
			break;
			
			case 2:
				$tipo_doc = 'RECIBO DE ARRENDAMIENTO';
			break;
			
			case 3:
				$tipo_doc = 'NOTA DE CREDITO';
			break;
			
			default:
				$tipo_doc = 'FACTURA';
		}
		
		$pdf->Cell(0, 0, utf8_decode($tipo_doc));
		
		$pdf->SetXY(165, 32);
		$pdf->MultiCell(43, 3, implode(', ', array($this->serie['municipio'], $this->serie['estado'])));
		
		$pdf->SetXY(165, 48);
		$pdf->MultiCell(43, 1, $this->datos['cabecera']['fecha']);
		
		$pdf->SetFont('Arial', 'B', 10);
		
		$pdf->SetXY(165 + $pdf->GetStringWidth('FOLIO NO.:'), 12);
		$pdf->Cell(20, 0, ($this->serie['serie'] != '' ? $this->serie['serie'] . '-' : '') . ($this->folio > 0 ? $this->folio : $this->serie['folio']), 0, 0, 'R');
		
		$pdf->SetXY(60, 16);
		$pdf->MultiCell(100, 3, $this->serie['razon_social'], 0, 'C');
		
		$pdf->SetFontSize(9);
		
		$pdf->Ln(2);
		$pdf->SetX(60);
		$pdf->Cell(100, 1, 'RFC.: ' . $this->serie['rfc'], 0, 0, 'C');
		
		$pdf->SetFont('Arial', '', 8);
		
		$domicilio_cia = array();
		
		if (trim($this->serie['calle']) != '') {
			$domicilio_cia[] = $this->serie['calle'];
		}
		if (trim($this->serie['no_exterior']) != '') {
			$domicilio_cia[] = $this->serie['no_exterior'];
		}
		if (trim($this->serie['no_interior']) != '') {
			$domicilio_cia[] = $this->serie['no_interior'];
		}
		if (trim($this->serie['colonia']) != '') {
			$domicilio_cia[] = 'COL. ' . $this->serie['colonia'];
		}
		if (trim($this->serie['municipio']) != '') {
			$domicilio_cia[] = $this->serie['municipio'];
		}
		if (trim($this->serie['estado']) != '') {
			$domicilio_cia[] = $this->serie['estado'];
		}
		if (trim($this->serie['pais']) != '') {
			$domicilio_cia[] = $this->serie['pais'];
		}
		if (trim($this->serie['codigo_postal']) != '') {
			$domicilio_cia[] = 'CP. ' . $this->serie['codigo_postal'];
		}
		
		$pdf->Ln(12);
		$pdf->SetX(60);
		$pdf->MultiCell(100, 3, implode(', ', $domicilio_cia), 0, 'C');
		
		$pdf->SetFont('Arial', 'B', 9);
		
		$pdf->SetXY(8, 55);
		$pdf->MultiCell(92, 3, $this->datos['cabecera']['nombre_cliente']);
		
		$pdf->Ln(3);
		$pdf->SetX(8);
		$pdf->Cell(92, 1, $this->datos['cabecera']['rfc_cliente']);
		
		$pdf->SetFont('Arial', '', 9);
		
		$domicilio_cliente = array();
		
		if (trim($this->datos['cabecera']['calle']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['calle'];
		}
		if (trim($this->datos['cabecera']['no_exterior']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['no_exterior'];
		}
		if (trim($this->datos['cabecera']['no_interior']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['no_interior'];
		}
		if (trim($this->datos['cabecera']['colonia']) != '') {
			$domicilio_cliente[] = 'COL. ' . $this->datos['cabecera']['colonia'];
		}
		if (trim($this->datos['cabecera']['municipio']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['municipio'];
		}
		if (trim($this->datos['cabecera']['estado']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['estado'];
		}
		if (trim($this->datos['cabecera']['pais']) != '') {
			$domicilio_cliente[] = $this->datos['cabecera']['pais'];
		}
		if (trim($this->datos['cabecera']['codigo_postal']) != '') {
			$domicilio_cliente[] = 'CP. ' . $this->datos['cabecera']['codigo_postal'];
		}
		
		$pdf->Ln(3);
		$pdf->SetX(8);
		$pdf->MultiCell(92, 5, implode(', ', $domicilio_cliente));
		
		$pdf->SetFont('Arial', '', 8);
		
		$pdf->SetXY(8, 95);
		
		foreach ($this->datos['detalle'] as $detalle) {
			$pdf->Cell(85, 0, $detalle['descripcion']);
			$pdf->Cell(23, 0, number_format($detalle['cantidad'], 2, '.', ','), 0, 0, 'R');
			$pdf->Cell(23, 0, $detalle['unidad'], 0, 0, 'C');
			$pdf->Cell(23, 0, number_format($detalle['precio'], 2, '.', ','), 0, 0, 'R');
			$pdf->Cell(23, 0, number_format($detalle['descuento'], 2, '.', ','), 0, 0, 'R');
			$pdf->Cell(23, 0, number_format($detalle['importe'], 2, '.', ','), 0, 0, 'R');
			
			$pdf->Ln(5);
			$pdf->SetX(8);
		}
		
		$pdf->SetFont('Arial', 'B', 8);
		
		$pdf->SetXY(38, 191.5);
		$pdf->MultiCell(115, 3, num2string($this->datos['cabecera']['total']));
		
		$pdf->SetXY(168, 203);
		$pdf->Cell(0, 0, $this->datos['cabecera']['porcentaje_iva'] . '%');
		
		$pdf->SetXY(180, 193);
		$pdf->Cell(25, 0, number_format($this->datos['cabecera']['importe'], 2, '.', ','), 0, 0, 'R');
		$pdf->SetXY(180, 198);
		$pdf->Cell(25, 0, number_format($this->datos['cabecera']['descuento'], 2, '.', ','), 0, 0, 'R');
		$pdf->SetXY(180, 203);
		$pdf->Cell(25, 0, number_format($this->datos['cabecera']['importe_iva'], 2, '.', ','), 0, 0, 'R');
		$pdf->SetXY(180, 208);
		$pdf->Cell(25, 0, number_format($this->datos['cabecera']['total'], 2, '.', ','), 0, 0, 'R');
		
		$pdf->SetXY(105, 218);
		$pdf->Cell(25, 0, $this->serie['no_aprobacion']);
		
		$pdf->SetFont('Arial', '', 6);
		
		$pdf->SetXY(15, 250);
		$pdf->MultiCell(182, 2, utf8_decode('POR ESTE PAGARE ME OBLIGO INCONDICIONALMENTE A PAGAR A LA ORDEN DE ' . $this->serie['razon_social'] . ' EL VALOR DE LAS MERCANCIAS QUE SE RECIBIERON A ENTERA SATISFACCION LA FIRMA PUESTA EN CUALQUIER LUGAR SE CONSIDERA COMO ACEPTACION DE ESTE PAGARE.'));
		
		$pdf->SetFont('Arial', 'B', 6);
		
		$pdf->SetXY(145, 261);
		$pdf->Cell(0, 0, $this->serie['fecha_aprobacion']);
		
		$this->file_name = $this->num_cia . '-' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']);
		
		/*
		@ Validar que el directorio para almacenar los comprobantes XML exista en el servidor
		*/
		if (!is_dir($this->lcomprobantes_pdf . $this->num_cia)) {
			mkdir($this->lcomprobantes_pdf . $this->num_cia);
		}
		
		$pdf->Output($this->lcomprobantes_pdf . $this->num_cia . '/' . $this->file_name . '.pdf', 'F');
		
		$this->comprobante_xml = '';
		$this->comprobante_pdf = '';
		
		$this->registrarFactura();
		
		$this->cambiarStatusServidor(self::SERVER_FREE);
		
		return $this->file_name;
	}
	
	private function registrarFactura() {
		global $db;
		
		$sql = '
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
						porcentaje_descuento,
						descuento,
						iva,
						retencion_iva,
						retencion_isr,
						total,
						iduser_ins,
						fecha_pago,
						nombre_consignatario,
						rfc_consignatario,
						calle_consignatario,
						no_exterior_consignatario,
						no_interior_consignatario,
						colonia_consignatario,
						localidad_consignatario,
						referencia_consignatario,
						municipio_consignatario,
						estado_consignatario,
						pais_consignatario,
						codigo_postal_consignatario
					)
				VALUES
					(
						' . $this->num_cia . ',
						\'' . $this->datos['cabecera']['fecha'] . '\',
						\'' . $this->datos['cabecera']['hora'] . '\',
						' . $this->tipo . ',
						' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . ',
						' . $this->datos['cabecera']['clasificacion'] . ',
						' . $this->datos['cabecera']['clave_cliente'] . ',
						\'' . pg_escape_string($this->datos['cabecera']['nombre_cliente']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['calle']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['no_exterior']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['no_interior']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['colonia']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['localidad']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['referencia']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['municipio']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['estado']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['pais']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['codigo_postal']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['email']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['observaciones']) . '\',
						' . $this->datos['cabecera']['importe'] . ',
						' . $this->datos['cabecera']['porcentaje_descuento'] .',
						' . $this->datos['cabecera']['descuento'] . ',
						' . $this->datos['cabecera']['importe_iva'] . ',
						' . $this->datos['cabecera']['importe_retencion_iva'] . ',
						' . $this->datos['cabecera']['importe_retencion_isr'] . ',
						' . $this->datos['cabecera']['total'] . ',
						' . $this->iduser . ',
						\'' . $this->datos['cabecera']['fecha'] . '\',
						\'' . pg_escape_string($this->datos['consignatario']['nombre']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['rfc']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['calle']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['no_exterior']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['no_interior']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['colonia']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['localidad']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['referencia']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['municipio']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['estado']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['pais']) . '\',
						\'' . pg_escape_string($this->datos['consignatario']['codigo_postal']) . '\'
					)
		' . ";\n";
		
		foreach ($this->datos['detalle'] as $i => $detalle) {
			$sql .= '
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
							importe,
							iva,
							piva,
							numero_pedimento,
							fecha_entrada,
							aduana_entrada
						)
					VALUES
						(
							' . $this->num_cia . ',
							' . $this->tipo . ',
							' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . ',
							' . $detalle['clave'] . ',
							' . $detalle['cantidad'] . ',
							\'' . pg_escape_string($detalle['descripcion']) . '\',
							' . $detalle['precio'] . ',
							\'' . pg_escape_string($detalle['unidad']) . '\',
							' . $detalle['importe'] . ',
							' . $detalle['importe_iva'] . ',
							' . $detalle['porcentaje_iva'] . ',
							\'' . pg_escape_string($detalle['numero_pedimento']) . '\',
							' . ($detalle['fecha_entrada'] != '' ? '\'' . $detalle['fecha_entrada'] . '\'' : 'NULL') . ',
							\'' . pg_escape_string($detalle['aduana_entrada']) . '\'
						)
			' . ";\n";
		}
		
		/*
		@ Actualizar nombres de comprobantes en el registro de la factura electrónica
		*/
		$sql .= '
			UPDATE
				facturas_electronicas
			SET
				comprobante_xml = \'' . $this->comprobante_xml . '\',
				comprobante_pdf = \'' . $this->comprobante_pdf . '\'
			WHERE
				num_cia = ' . $this->num_cia . '
				AND tipo_serie = ' . $this->tipo . '
				AND consecutivo = ' . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . '
		' . ";\n";
		
		/*
		@ Actualizar series solo si no es reservado
		*/
		if (!$this->folio) {
			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					ultimo_folio_usado = ' . $this->serie['folio'] . '
				WHERE
					num_cia = ' . $this->num_cia . '
					AND tipo_serie = ' . $this->tipo . '
					AND status = 1
					AND folio_inicial = ' . $this->serie['folio_inicial'] . '
					AND folio_final = ' . $this->serie['folio_final'] . '
			' . ";\n";
		}
		
		/*
		@ Poner serie como terminada si se ha llegado al máximo de folios
		*/
		if ($this->serie['folio'] == $this->serie['folio_final']) {
			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					status = 2
				WHERE
					num_cia = ' . $this->num_cia . '
					AND tipo_serie = ' . $this->tipo . '
					AND status = 1
					AND folio_inicial = ' . $this->serie['folio_inicial'] . '
					AND folio_final = ' . $this->serie['folio_final'] . '
			' . ";\n";
		}
		
		$db->query($sql);
	}
	
	public function generarFactura($iduser, $num_cia, $tipo, $datos, $folio = NULL) {
		$this->iduser  = $iduser;
		
		$this->num_cia = $num_cia;
		$this->tipo    = $tipo;
		$this->datos   = $datos;
		$this->folio   = $folio;
		
		$this->status = 0;
		
		if ($this->ftp_status < 0) {
			return $this->ftp_status;
		}
		else if (!$this->validarStatusServidor()) {
			return $this->status;
		}
		else if (!$this->validarSerie()) {
			return $this->status;
		}
		else if (!$this->validarDuplicados()) {
			return $this->status;
		}
		else if (!$this->validarDatos()) {
			return $this->status;
		}
		else if ($this->folio > 0 && !$this->validarFolio()) {
			return $this->status;
		}
		else if ($this->serie['tipo_factura'] == 2) {
			$this->status = $this->generarFacturaImpresa();
			
			return $this->status;
		}
		else {
			$this->status = $this->generarFacturaElectronica();
			
			return $this->status;
		}
	}
	
	public function cancelarFactura($iduser, $id, $motivo = '') {
		global $db;
		
		$sql = '
			SELECT
				consecutivo,
				comprobante_pdf,
				status
			FROM
				facturas_electronicas
			WHERE
				id = ' . $id . '
		';
		$result = $db->query($sql);
		
		if (!$result) {
			$this->status = -500;
		}
		if ($result[0]['status'] == 2) {
			$this->status = -501;
			
			$status = FALSE;
		}
		else if ($result[0]['comprobante_pdf'] != '') {
			$url = 'http://192.168.1.70/clases/servlet/cancelaFacturaLE?archivo=' . $result[0]['comprobante_pdf'];
			
			if (!($url_result = file_get_contents($url))) {
				$this->status = -502;
				
				$status = FALSE;
			}
			else {
				$url_result = explode('|', $url_result);
				
				foreach ($url_result as $i => $value) {
					list($var, $val) = explode('=', trim($value));
					
					${trim($var)} = trim($val);
				}
				
				if ($Resultado != 1) {
					$this->last_error = 'No se pudo cancelar la factura, el servidor reporto: "' . $Error . '"';
					
					$this->status = -503;
					
					$status = FALSE;
				}
				else {
					$status = TRUE;
				}
			}
		}
		else {
			$status = TRUE;
		}
		
		if ($status) {
			$sql = '
				UPDATE
					facturas_electronicas
				SET
					status = 0,
					iduser_can = ' . $iduser . ',
					motivo_cancelacion = \'' . utf8_decode($motivo) . '\',
					tscan = now()
				WHERE
					id = ' . $id . '
			';
			
			$db->query($sql);
			
			$this->enviarEmailCancelacion($id);
			
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	public function enviarEmail($emails = array()) {
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer')) {
			include_once('includes/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once('includes/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once('includes/cheques.inc.php');
		}
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}
		
		/*
		@ Email para compañía
		*/
		if (trim($this->serie['email']) != '') {
			$mail->AddBCC($this->serie['email']);
		}
		
		/*
		@ Email para Elite
		*/
		if ($this->num_cia >= 900) {
			$mail->AddBCC('contabilidad@zapateriaselite.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($this->num_cia < 900) {
			$mail->AddBCC('beatriz.flores@lecaroz.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');
			
			if (in_array($this->datos['cabecera']['clasificacion'], array(2, 5))) {
				$mail->AddBCC('facturas@lecaroz.com');
			}
		}
		
		/*
		@ Email para contadores
		*/
		if ($this->serie['email_contador'] != '') {
			$mail->AddBCC($this->serie['email_contador']);
		}
		
		/*
		@ Email para cliente
		*/
		if ($this->datos['cabecera']['email'] != '') {
			$mail->AddAddress($this->datos['cabecera']['email']);
		}
		
		if (count($emails) > 0) {
			foreach ($emails as $email) {
				if ($email != '') {
					$mail->AddAddress($email);
				}
			}
		}
		
		$mail->Subject = 'COMPROBANTE FISCAL DIGITAL :: ' . $this->serie['razon_social'];
		
		$tpl = new TemplatePower('plantillas/fac/email_cfd.tpl');
		$tpl->prepare();
		
		$tpl->assign('nombre_cia', htmlentities($this->serie['razon_social']));
		$tpl->assign('rfc_cia', htmlentities($this->serie['rfc']));
		
		$tpl->assign('folio', ($this->serie['serie'] != '' ? $this->serie['serie'] . '-' : '') . ($this->folio > 0 ? $this->folio : $this->serie['folio']));
		$tpl->assign('nombre_cliente', htmlentities($this->datos['cabecera']['nombre_cliente']));
		$tpl->assign('rfc_cliente', htmlentities($this->datos['cabecera']['rfc_cliente']));
		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2, '.', ','));
		$tpl->assign('total_escrito', htmlentities(num2string($this->datos['cabecera']['total'])));
		
		$tpl->assign('email_ayuda', $this->num_cia >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com');
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		$mail->AddAttachment($this->lcomprobantes_pdf . $this->num_cia . '/' . $this->file_name . '.pdf');
		
		if ($this->serie['tipo_factura'] == 1) {
			$mail->AddAttachment($this->lcomprobantes_xml . $this->num_cia . '/' . $this->file_name . '.xml');
		}
		
		if(!$mail->Send()) {
			return $mail->ErrorInfo;
		}
		else {
			return TRUE;
		}
	}
	
	public function enviarEmailCancelacion($id) {
		global $db;
		
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer')) {
			include_once('includes/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once('includes/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once('includes/cheques.inc.php');
		}
		
		$sql = '
			SELECT
				num_cia,
				razon_social,
				cc.rfc
					AS rfc_cia,
				cc.email
					AS email_cia,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
							num_cia = fe.num_cia
						AND
							tipo_serie = fe.tipo_serie
						AND
							fe.consecutivo BETWEEN folio_inicial AND folio_final
				)
					AS
						serie,
				consecutivo
					AS folio,
				nombre_cliente,
				fe.rfc
					AS rfc_cliente,
				fe.email_cliente,
				total,
				tscan::DATE
					AS fecha_cancelacion,
				motivo_cancelacion
			FROM
				facturas_electronicas fe
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				id = ' . $id . '
		';
		
		$result = $db->query($sql);
		
		$rec = $result[0];
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}
		
		/*
		@ Email para cliente
		*/
		if (trim($rec['email_cliente']) != '') {
			$mail->AddAddress($rec['email_cliente']);
		}
		
		/*
		@ Email para compañía
		*/
		if (trim($rec['email_cia']) != '') {
			$mail->AddAddress($rec['email_cia']);
		}
		
		/*
		@ Email para Elite
		*/
		if ($rec['num_cia'] >= 900) {
			$mail->AddAddress('contabilidad@zapateriaselite.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($rec['num_cia'] < 900) {
			$mail->AddBCC('beatriz.flores@lecaroz.com');
		}
		
		$mail->Subject = utf8_decode('CANCELACIÓN DE COMPROBANTE FISCAL');
		
		$tpl = new TemplatePower('plantillas/fac/email_cfd_cancel.tpl');
		$tpl->prepare();
		
		$tpl->assign('nombre_cia', htmlentities($rec['razon_social']));
		$tpl->assign('rfc_cia', htmlentities($rec['rfc_cia']));
		
		$tpl->assign('folio', ($rec['serie'] != '' ? $rec['serie'] . '-' : '') . $rec['folio']);
		$tpl->assign('nombre_cliente', htmlentities($rec['nombre_cliente']));
		$tpl->assign('rfc_cliente', htmlentities($rec['rfc_cliente']));
		$tpl->assign('total', number_format($rec['total'], 2, '.', ','));
		$tpl->assign('total_escrito', htmlentities(num2string($rec['total'])));
		$tpl->assign('fecha_cancelacion', htmlentities($rec['fecha_cancelacion']));
		
		$tpl->assign('motivo_cancelacion', $rec['motivo_cancelacion'] != '' ? htmlentities($rec['motivo_cancelacion']) : 'Solicitar informaci&oacute;n al correo ' . ($rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com'));
		
		$tpl->assign('email_ayuda', $rec['num_cia'] >= 900 ? 'ayuda@zapateriaselite.com' : 'fe.ayuda@lecaroz.com');
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		if(!$mail->Send()) {
			return $mail->ErrorInfo;
		}
		else {
			return TRUE;
		}
	}
	
	public function enviarEmailError() {
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer')) {
			include_once('includes/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once('includes/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once('includes/cheques.inc.php');
		}
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas+lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}
		
		/*
		@ Email para compañía
		*/
		if (trim($this->serie['email']) != '') {
			$mail->AddAddress($this->serie['email']);
		}
		
		/*
		@ Email para Elite
		*/
		if ($this->num_cia >= 900) {
			$mail->AddAddress('contabilidad@zapateriaselite.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($this->num_cia < 900) {
			$mail->AddAddress('beatriz.flores@lecaroz.com');
			
			if (in_array($this->datos['cabecera']['clasificacion'], array(2, 5))) {
				$mail->AddAddress('facturas@lecaroz.com');
			}
		}
		
		if (in_array($this->ultimoCodigoError(), array(-10, -11, -12, -50, -51))) {
			$mail->AddBCC('carlos.candelario@lecaroz.com');
			//$mail->AddBCC('p_master5@hotmail.com');
		}
		
		$mail->Subject = 'ERROR AL GENERAR EL COMPROBANTE FISCAL';
		
		$tpl = new TemplatePower('plantillas/fac/email_cfd_error.tpl');
		$tpl->prepare();
		
		$tpl->assign('num_cia', $this->num_cia);
		$tpl->assign('nombre_cia', htmlentities(utf8_decode($this->serie['razon_social'])));
		$tpl->assign('rfc_cia', htmlentities(utf8_decode($this->serie['rfc'])));
		
		$tpl->assign('codigo', $this->ultimoCodigoError());
		$tpl->assign('descripcion', htmlentities(utf8_decode($this->ultimoError())));
		
		$tpl->assign('nombre_cliente', htmlentities(utf8_decode($this->datos['cabecera']['nombre_cliente'])));
		$tpl->assign('rfc_cliente', htmlentities(utf8_decode($this->datos['cabecera']['rfc_cliente'])));
		$tpl->assign('calle', htmlentities(utf8_decode($this->datos['cabecera']['calle'])));
		$tpl->assign('no_exterior', htmlentities(utf8_decode($this->datos['cabecera']['no_exterior'])));
		$tpl->assign('no_interior', htmlentities(utf8_decode($this->datos['cabecera']['no_interior'])));
		$tpl->assign('colonia', htmlentities(utf8_decode($this->datos['cabecera']['colonia'])));
		$tpl->assign('localidad', htmlentities(utf8_decode($this->datos['cabecera']['localidad'])));
		$tpl->assign('referencia', htmlentities(utf8_decode($this->datos['cabecera']['referencia'])));
		$tpl->assign('municipio', htmlentities(utf8_decode($this->datos['cabecera']['municipio'])));
		$tpl->assign('estado', htmlentities(utf8_decode($this->datos['cabecera']['estado'])));
		$tpl->assign('pais', htmlentities(utf8_decode($this->datos['cabecera']['pais'])));
		$tpl->assign('codigo_postal', htmlentities($this->datos['cabecera']['codigo_postal']));
		
		if ($this->ultimoCodigoError() == -100) {
			foreach ($this->header_error as $error) {
				$tpl->assign('mark_' . abs($error), 'class="mark"');
			}
		}
		
		foreach ($this->datos['detalle'] as $detalle) {
			$tpl->newBlock('detalle');
			$tpl->assign('descripcion', htmlentities(utf8_decode($detalle['descripcion'])));
			$tpl->assign('cantidad', number_format($detalle['cantidad'], 2, '.', ','));
			$tpl->assign('precio', number_format($detalle['precio'], 2, '.', ','));
			$tpl->assign('unidad', htmlentities($detalle['unidad']));
			$tpl->assign('importe', number_format($detalle['importe'], 2, '.', ','));
			
			if ($this->ultimoCodigoError() == -150) {
				$tpl->assign('mark_importe', ' class="mark"');
			}
		}
		
		$tpl->gotoBlock('_ROOT');
		
		$tpl->assign('subtotal', number_format($this->datos['cabecera']['importe'], 2, '.', ','));
		$tpl->assign('iva', number_format($this->datos['cabecera']['importe_iva'], 2, '.', ','));
		$tpl->assign('total', number_format($this->datos['cabecera']['total'], 2, '.', ','));
		
		if ($this->ultimoCodigoError() == -150 || $this->ultimoCodigoError() == -151) {
			$tpl->assign('mark_subtotal', ' class="mark"');
		}
		
		if ($this->ultimoCodigoError() == -151) {
			$tpl->assign('mark_iva', ' class="mark"');
			$tpl->assign('mark_total', ' class="mark"');
		}
		
		$oficina = $this->num_cia >= 900 ? 'ZAPATERIAS ELITE (OFICINA) AL TELEFONO (55)5709-7982' : 'OFICINAS ADMINISTRATIVAS MOLLENDO AL TELEFONO (55)5276-6570';
		
		if (in_array($this->ultimoCodigoError(), array(-1, -2, -3, -5, -6, -7, -8, -50))) {
			$tpl->assign('accion', 'DEBERA ESPERAR A QUE EL COMPROBANTE LE SEA ENVIADO O EN SU DEFECTO COMUNICARSE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.<br /><br />NO INTENTE POR NINGUN MOTIVO REPETIR EL COMPROBANTE O ESTE SE DUPLICARA E INCURRIRA EN UNA FALTA, Y SI HACE CASO OMISO DEBERA REPORTARLO INMEDIATAMENTE A LA OFICINA CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-4))) {
			$tpl->assign('accion', 'ESTA EMPRESA NO CUENTA CON UNA SERIE PARA EMITIR COMPROBANTES FISCALES, COMUNIQUESE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION Y SE HAGAN LOS TRAMITES CORRESPONDIENTES.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-80))) {
			$tpl->assign('accion', 'POR DISPOSICION DE LA OFICINA NO PUEDE REPETIR UN COMPROBANTE SIN ANTES SOLICITAR LA CANCELACION DEL ANTERIOR.<br /><br />SI NECESITA EMITIR ESTE COMPROBANTE DEBERA COMUNICARSE A ' . $oficina . ' CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION Y CON INFORMACION EN MANO SOLICITAR LE HAGA LA EMISION DEL MISMO.');
		}
		else if (in_array($this->ultimoCodigoError(), array(-9, -100, -150, -151))) {
			$tpl->assign('accion', 'REVISE QUE LOS DATOS QUE HA INGRESADO SEAN CORRECTOS Y ESTEN DEBIDAMENTE CAPTURADOS EN EL CAMPO CORRESPONDIENTE, DESPUES INTENTE EMITIR EL COMPROBANTE NUEVAMENTE. SI CONTINUA RECIBIENDO ESTE MENSAJE COMUNIQUESE A ' . $oficina . ' PARA SOLICITAR MAS INFORMACION (DEBE TENER A LA MANO LOS DATOS EXACTAMENTE COMO SE HAN CAPTURADO).');
		}
		else if (in_array($this->ultimoCodigoError(), array(-10, -11, -12, -51))) {
			$tpl->assign('accion', 'ERROR INTERNO. ES EXTREMADAMENTE IMPORTANTE SE COMUNIQUE A ' . $oficina . ' Y REPORTAR EL PROBLEMA ANTES DE SEGUIR EMITIENDO CUALQUIER TIPO DE COMPROBANTE.<br /><br />NO INTENTE POR NINGUN MOTIVO REPETIR EL COMPROBANTE O PODRIA GENERAR MAS ERRORES E INCURRIRA EN UNA FALTA, Y SI HACE CASO OMISO DEBERA REPORTARLO INMEDIATAMENTE A LA OFICINA CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION.');
		}
		
		$tpl->assign('email_info', $this->num_cia >= 900 ? 'contabilidad@zapateriaselite.com' : 'beatriz.flores@lecaroz.com');
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		if(!$mail->Send()) {
			return $mail->ErrorInfo;
		}
		else {
			return TRUE;
		}
	}
	
	public function ultimoCodigoError() {
		return $this->ftp_status | $this->status;
	}
	
	public function ultimoError() {
		switch ($this->ftp_status | $this->status) {
			case -1:
				return 'Error al conectar al servidor FTP';
			break;
			
			case -2:
				return 'Error al iniciar sesión en el servidor FTP';
			break;
			
			case -3:
				return 'No se pueden generar facturas electrónicas porque el servidor se encuentra ocupado';
			break;
			
			case -4:
				return 'La compañía no tiene folios disponibles';
			break;
			
			case -5:
				return 'No se pudo crear el archivo de carga de datos';
			break;
			
			case -6:
				return 'No se pudo enviar el archivo de carga de datos al servidor de facturas electrónicas';
			break;
			
			case -7:
				return 'No es posible accesar al generador de facturas electrónicas';
			break;
			
			case -8:
				return 'No es posible accesar al generador de facturas electrónicas: "' . $this->last_error . '"';
			break;
			
			case -9:
				return 'Error al generar CFD, el servidor reporto: "' . $this->last_error . '"';
			break;
			
			case -10:
				return $this->last_error;
			break;
			
			case -11;
				return 'No se pudo obtener el archivo ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'] . '.xml';
			break;
			
			case -12:
				return 'No se pudo obtener el archivo ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'] . '.pdf';
			break;
			
			case -50:
				return $this->last_error;
			break;
			
			case -51:
				return $this->last_error;
			break;
			
			case -80:
				return 'No puede hacer 2 facturas con el mismo importe';
			break;
			
			case -100:
				$lista_errores = array(
					-101 => 'Nombre',
					-102 => 'R.F.C.',
					-103 => 'R.F.C. (Estructura)',
					-104 => 'Calle',
					-105 => 'Colonia',
					-106 => 'Delagacion o Municipio',
					-107 => 'Estado',
					-108 => 'Pais',
					-109 => 'Código postal'
				);
				
				$errores = array();
				
				foreach ($this->header_error as $error) {
					$errores[] = $lista_errores[$error];
				}
				
				return 'La información fiscal del cliente contiene errores [' . implode('|', $errores) . ']';
			break;
			
			case -150:
				return 'La suma de los importes por detalle difiere del subtotal de la factura';
			break;
			
			case -151:
				return 'La suma del subtotal, descuentos e impuestos difiere del total de la factura';
			break;
			
			case -160:
				return 'El folio ya no esta disponible';
			break;
			
			case -161:
				return 'El folio es mayor al último disponible';
			break;
			
			case -500:
				return 'Registro de factura no existe';
			break;
			
			case -501:
				return 'La factura ya ha sido cancelada con anterioridad';
			break;
			
			case -502:
				return 'Imposible acceder a la cancelación de facturas electrónicas';
			break;
			
			case -503:
				return $this->last_error;
			break;
			
			default:
				return 'Error desconocido';
		}
	}
	
	public function codigosErrorCabecera() {
		return $this->header_error;
	}
	
	public function reservarFolio($iduser, $num_cia, $tipo_serie, $fecha) {
		global $db;
		
		$sql = '
			SELECT
				id,
				num_cia,
				tipo_serie,
				ultimo_folio_usado + 1
					AS folio,
				folio_inicial,
				folio_final
			FROM
				facturas_electronicas_series
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND status = 1
		';
		
		$result = $db->query($sql);
		
		if ($result) {
			$sql = '
				INSERT INTO
					facturas_electronicas_folios_reservados
						(
							num_cia,
							tipo_serie,
							folio,
							fecha,
							idins,
							tsins
						)
					SELECT
						num_cia,
						tipo_serie,
						ultimo_folio_usado + 1,
						\'' . $fecha . '\',
						' . $iduser . ',
						now()
					FROM
						facturas_electronicas_series
					WHERE
						id = ' . $result[0]['id'] . '
			' . ";\n";
			
			$sql .= '
				UPDATE
					facturas_electronicas_series
				SET
					ultimo_folio_usado = ultimo_folio_usado + 1
				WHERE
					id = ' . $result[0]['id'] . '
			' . ";\n";
			
			if ($result[0]['folio'] == $result[0]['folio_final']) {
				$sql .= '
					UPDATE
						facturas_electronicas_series
					SET
						status = 2
					WHERE
						id = ' . $result[0]['id'] . '
				' . ";\n";
			}
			
			$db->query($sql);
			
			return $result[0]['folio'];
		}
		else {
			return NULL;
		}
	}
	
	public function recuperarFolio($num_cia, $tipo_serie, $fecha) {
		global $db;
		
		$sql = '
			SELECT
				folio
			FROM
				facturas_electronicas_folios_reservados
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND fecha = \'' . $fecha . '\'
				AND tsreg IS NULL
		';
		
		$result = $db->query($sql);
		
		if ($result) {
			return intval($result[0]['folio'], 10);
		}
		else {
			return NULL;
		}
	}
	
	public function utilizarFolio($iduser, $num_cia, $tipo_serie, $folio) {
		global $db;
		
		$sql = '
			UPDATE
				facturas_electronicas_folios_reservados
			SET
				tsreg = now(),
				idreg = ' . $iduser . '
			WHERE
				num_cia = ' . $num_cia . '
				AND tipo_serie = ' . $tipo_serie . '
				AND folio = ' . $folio . '
		';
		
		$db->query($sql);
	}
	
}

?>