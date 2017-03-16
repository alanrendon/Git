<?php

class FacturasClass {
	
	const SERVER_BLOCKED = TRUE;
	const SERVER_FREE = FALSE;
	
	private $ftp_server = '192.168.1.251';
	private $ftp_user = 'lecaroz';
	private $ftp_pass = 'pobgnj';
	private $ftp;
	private $ftp_status;
	
	/*
	@ Conexiones a las bases de datos
	*/
	private $db;
	private $dbf;
	
	//private $dsn_servfac = 'pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz';
	
	/*
	@ Rutas locales
	*/
	private $ldatos = '/var/www/lecaroz/facturas/datos/';
	private $lcomprobantes_xml = '/var/www/lecaroz/facturas/comprobantes_xml/';
	private $lcomprobantes_pdf = '/var/www/lecaroz/facturas/comprobantes_pdf/';
	private $lcodigos_qr = '/var/www/lecaroz/facturas/codigos_qr/';
	
	/*
	@ Rutas servidor
	*/
	private $rcomprobantes_xml = '/home/lecaroz/archivos/';
	private $rcomprobantes_pdf = '/home/lecaroz/archivos/';
	
	private $comprobante_xml;
	private $comprobante_pdf;
	
	private $file_name;
	private $rfile_name;
	
	private $recover_file_timeout = 300;
	
	private $iduser;
	
	private $num_cia;
	private $tipo;
	private $serie;
	private $datos;
	private $folio;
	
	private $status;
	
	private $last_error;
	
	private $header_error;
	
	private $process_init;
	private $process_end;
	
	function __construct() {
		global $db, $dbf;
		
		/*
		@ Validar que exista una conexión a la base de datos de Lecaroz
		*/
		if (!isset($db)) {
			trigger_error('No existe la instancia $db para la conexión a la base de datos de Lecaroz', E_USER_ERROR);
		}
		else {
			$this->db = &$db;
		}
		
		/*
		@ Validar que exista una conexión a la base de datos de facturas
		*/
		if (!isset($dbf)) {
			trigger_error('No existe la instancia $dbf para la conexión a la base de datos de facturas electronicas', E_USER_ERROR);
		}
		else {
			$this->dbf = &$dbf;
		}
	}
	
	
	function __destruct() {
		
	}
	
	private function validarStatusServidor() {
		$sql = '
			SELECT
				id
			FROM
				facturas_electronicas_server_status_new
			WHERE
				num_cia = ' . $this->num_cia . '
		';
		$status = $this->db->query($sql);
		
		if ($status) {
			$this->status = -3;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	private function cambiarStatusServidor($status) {
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
		
		$this->db->query($sql);
	}
	
	private function actualizarStatusServidor($status = 0, $observaciones = '') {
		$sql = '
			UPDATE
				facturas_electronicas_server_status_new
			SET
				status = ' . $status . ',
				obs = \'' . $observaciones . '\'
			WHERE
				num_cia = ' . $this->num_cia . '
		';
		
		$this->db->query($sql);
	}
	
	private function validarSerie() {
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
				EXTRACT(YEAR FROM fecha_aprobacion)
					AS anio_aprobacion,
				codigo_qr,
				nombre
					AS nombre_cia,
				cc.email,
				con.email
					AS email_contador,
				razon_social,
				rfc,
				regimen_fiscal,
				calle,
				no_exterior,
				no_interior,
				colonia,
				municipio,
				estado,
				pais,
				codigo_postal,
				tipo_cfd
			FROM
				facturas_electronicas_series fes
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
				LEFT JOIN catalogo_contadores con
					USING (idcontador)
			WHERE
				num_cia = ' . $this->num_cia . '
				AND fes.tipo_serie = ' . $this->tipo . '
				AND ' . ($this->folio == NULL ? 'fes.status = 1' : $this->folio . ' BETWEEN folio_inicial AND folio_final') . '
		';
		
		$result = $this->db->query($sql);
		
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
		else if (preg_match_all("/^([a-zA-Z\xf1\xd1\&]{3,4})([\d]{2})([\d]{2})([\d]{2})([a-zA-Z0-9]{3})$/", $this->datos['cabecera']['rfc_cliente'], $matches) == 0) {
			$this->header_error[] = -103;
		}/* else {
			$dias_por_mes = array(
				1	=> 31,
				2	=> ((intval($matches[0][2], 10) > 50 ? 1900 : 2000) + intval($matches[0][2], 10)) % 4 == 0 ? 29 : 28,
				3	=> 31,
				4	=> 30,
				5	=> 31,
				6	=> 30,
				7	=> 31,
				8	=> 31,
				9	=> 30,
				10	=> 31,
				11	=> 30,
				12	=> 31
			);

			if (intval($matches[0][3], 10) > 12 || intval($matches[0][4], 10) > $dias_por_mes[intval($matches[0][3], 10)]) {
				$this->header_error[] = -111;
			}
		}*/
		
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
		
		$sql = '
			SELECT
				idob,
				UPPER("Entidad")
					AS estado
			FROM
				catalogo_entidades
			ORDER BY
				estado
		';
		
		$tmp = $this->db->query($sql);
		
		$estados = array();
		
		foreach ($tmp as $t) {
			$estados[$t['estado']] = $t['idob'];
		}
		
		if (!in_array($this->datos['cabecera']['estado'], array_keys($estados))) {
			$this->header_error[] = -110;
		}
		else {
			$this->datos['cabecera']['idestado'] = $estados[$this->datos['cabecera']['estado']];
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
			if ($detalle['importe'] > 0) {
				$subtotal += $detalle['importe'];
			}
		}
		
		if (round($subtotal, 2) != round($this->datos['cabecera']['importe'], 2)) {
			$this->status = -150;
			
			return FALSE;
		}
		
		/*
		@ Validar el total
		*/
		
		$total = $this->datos['cabecera']['importe'] - $this->datos['cabecera']['descuento'] + (isset($this->datos['cabecera']['ieps']) ? $this->datos['cabecera']['ieps'] : 0) + $this->datos['cabecera']['importe_iva'] - $this->datos['cabecera']['importe_retencion_isr'] - $this->datos['cabecera']['importe_retencion_iva'];
		
		if (round($total, 2) != round($this->datos['cabecera']['total'], 2)) {
			$this->status = -151;
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function validarNombreCliente() {
		$sql = '
			SELECT
				name
			FROM
				c_bpartner
			WHERE
				value = \'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\'
		';
		
		$result = $this->dbf->query($sql);
		
		if ($result && $result[0]['name'] != $this->datos['cabecera']['nombre_cliente']) {
			$sql = '
				UPDATE
					c_bpartner
				SET
					name = \'' . pg_escape_string($this->datos['cabecera']['nombre_cliente']) . '\'
				WHERE
					value = \'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\'
			';
			
			$this->dbf->query($sql);
		}
	}
	
	private function validarDuplicados() {
		if ($this->num_cia >= 900) {
			return TRUE;
		}

		if ($this->num_cia == 17 && in_array($this->datos['cabecera']['rfc_cliente'], array('PHI830429MG6', 'TSP0004051I4')))
		{
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
		
		$result = $this->db->query($sql);
		
		if ($this->iduser == 0 && $result) {
			$this->status = -80;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

	private function validarLimiteTotal() {
		if ($this->num_cia >= 900) {
			return TRUE;
		}

		if ($this->iduser == 0 && $this->datos['cabecera']['total'] > 15000) {
			$this->status = -81;
			
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	private function validarFolio() {
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
		
		$result = $this->db->query($sql);
		
		if ($result) {
			$this->status = -160;
			
			return FALSE;
		}
		/*else if ($this->folio > $this->serie['folio']) {
			$this->status = -161;
			
			return FALSE;
		}*/
		else {
			return TRUE;
		}
	}
	
	private function generarFacturaElectronica() {
		$this->cambiarStatusServidor(self::SERVER_BLOCKED);
		
		$this->actualizarStatusServidor(0, 'Procesando factura ' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']));
		
		$this->process_init = time();
		
		/*
		@ Crear query de inserción
		*/
		
		switch ($this->tipo) {
			/*
			@ Factura
			*/
			case 1:
				$doctype = 'F' . $this->num_cia;
			break;
			
			/*
			@ Recibo de arrendamiento
			*/
			case 2:
				$doctype = 'A' . $this->num_cia;
			break;
			
			/*
			@ Nota de credito
			*/
			case 3:
				$doctype = 'N' . $this->num_cia;
			break;
		}
		
		$this->validarNombreCliente();
		
		$sql = '';
		
		foreach ($this->datos['detalle'] as $i => $detalle) {
			if ($detalle['importe'] < 0) {
				$productvalue = 11;
			}
			else if ($this->datos['cabecera']['clasificacion'] == 3 && $this->num_cia == 700) {
				$productvalue = 4;
			}
			else if ($this->datos['cabecera']['clasificacion'] == 3 && $this->num_cia == 800) {
				$productvalue = 5;
			}
			else if ($this->datos['cabecera']['clasificacion'] == 7 && $this->num_cia == 700) {
				$productvalue = 6;
			}
			else if ($this->datos['cabecera']['clasificacion'] == 5) {
				if ($detalle['porcentaje_iva'] == 0) {
					$productvalue = 7;
				}
				else if ($detalle['porcentaje_iva'] > 0 && $this->datos['cabecera']['aplicar_retenciones'] == 'N') {
					$productvalue = 8;
				}
				else if ($detalle['porcentaje_iva'] > 0 && $this->datos['cabecera']['aplicar_retenciones'] == 'S') {
					$productvalue = 9;
				}
			}
			else if (in_array($this->datos['cabecera']['clasificacion'], array(1, 2, 4, 6))) {
				if ($this->num_cia < 900) {
					if ($detalle['porcentaje_iva'] > 0) {
						if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] > 0)
						{
							$productvalue = 'PIEPSIVA';
						}
						else
						{
							$productvalue = 2;
						}
					} else {
						if (isset($detalle['porcentaje_ieps']) && $detalle['porcentaje_ieps'] > 0)
						{
							$productvalue = 'PIEPS';
						}
						else
						{
							$productvalue = 1;
						}
					}
				}
				else {
					if ($detalle['porcentaje_iva'] > 0) {
						$productvalue = 3;
						
						if ($detalle['numero_pedimento'] != '') {
							$productvalue = 13;
						}
					} else if ($detalle['porcentaje_iva'] == 0) {
						$productvalue = 14;
						
						if ($detalle['numero_pedimento'] != '') {
							$productvalue = 12;
						}
					}
					
				}
			}
			
			if ($this->serie['tipo_cfd'] == 1) {
				$archivo_reporte = isset($this->datos['cabecera']['tipo_reporte']) ? $this->datos['cabecera']['tipo_reporte'] :  'RptM_Facturae.jrxml';
			} else if ($this->serie['tipo_cfd'] == 2) {
				$archivo_reporte = isset($this->datos['cabecera']['tipo_reporte']) ? $this->datos['cabecera']['tipo_reporte'] :  'RptM_CFDI_lecaroz.jrxml';
			}

			// [28-Jul-2013] Agregado para facturaefilename nuevo tipo para facturas cfdi
			$sql .= '
				INSERT INTO
					i_invoice (
						i_invoice_id,
						ad_client_id,		-- 1000000
						ad_org_id,			-- num_cia
						createdby,			-- iduser
						updatedby,			-- iduser
						c_currency_id,		-- 130 MX
						bpartnervalue,		-- RFC de cliente
						name,					-- Razon social o nombre de cliente
						postal,				-- Codigo postal
						regionname,			-- Nombre del estado/entidad
						c_region_id,      -- Id del estado/entidad
						c_country_id,		-- Mexico (247)
						email,				-- Email de cliente
						contactname,		-- Razon social o nombre de cliente
						phone,				-- Telefono de cliente
						doctypename,		-- Tipo de documento + num_cia (F102), (N102)...
						documentno,			-- Folio y serie de la factura
						productvalue,		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
						linedescription,	-- Descripcion
						priceactual,		-- Precio actual
						dateinvoiced,		-- Fecha factura (timestamp)
						qtyinvoiced,		-- Cantidad
						calle,
						noexterior,
						nointerior,
						colonia,
						localidad,
						referencia,
						municipio,
						bpartner_rfc,		-- RFC
						description,		-- Observaciones
						paymentrule,		-- Tipo de pago, 4=No especificado, B=Efectivo, 1=Transferencia bancaria, S=Cheque, K=Tarjeta de credito
						numctapago,			-- Cuenta de cargo
						paymenttermvalue,	-- Condiciones de pago
						consignatarioname,
						consignatario_rfc,
						calle_consignatario,
						noexterior_consignatario,
						nointerior_consignatario,
						colonia_consignatario,
						localidad_consignatario,
						referencia_consignatario,
						municipio_consignatario,
						regionname_consignatario,
						c_country_consignatario_id,
						postal_consignatario,
						aduana,
						pedimento,
						fecha_pedimento,
						facturaefilename,
						isready,
						uomname,			-- [07-Sep-2012] Unidad de medida
						noaprobacion,
						anoaprobacion,
						cuentapredial
					)
					VALUES (
						COALESCE((
							SELECT
								MAX(i_invoice_id)
							FROM
								i_invoice
						), 0) + 1,
						1000000,
						' . $this->num_cia . ',
						' . $this->iduser . ',
						' . $this->iduser . ',
						130,
						\'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\',
						\'' . pg_escape_string($this->datos['cabecera']['nombre_cliente']) . '\',
						' . ($this->datos['cabecera']['codigo_postal'] != '' ? '\'' . $this->datos['cabecera']['codigo_postal'] . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['estado'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['estado']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['idestado'] > 0 ? $this->datos['cabecera']['idestado'] : 'NULL') . ',
						247,
						' . ($this->datos['cabecera']['email'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['email']) . '\'' : 'NULL') . ',
						\'' . pg_escape_string($this->datos['cabecera']['nombre_cliente']) . '\',
						NULL,
						\'' . $doctype . '\',		-- Tipo de documento + num_cia (F102), (N102)...
						\'' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']) . '\',
						\'' . $productvalue . '\',		-- 1=Panaderia sin IVA, 2=Panaderia con iva, 3=Zapateria
						\'' . pg_escape_string($detalle['descripcion']) . '\',
						' . $detalle['precio'] . ',
						\'' . $this->datos['cabecera']['fecha'] . ' ' . $this->datos['cabecera']['hora'] . '\',
						' . $detalle['cantidad'] . ',
						' . ($this->datos['cabecera']['calle'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['calle']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['no_exterior'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['no_exterior']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['no_interior'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['no_interior']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['colonia'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['colonia']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['localidad'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['localidad']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['referencia'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['referencia']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['municipio'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['municipio']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['rfc_cliente'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['rfc_cliente']) . '\'' : 'NULL') . ',
						' . ($this->datos['cabecera']['observaciones'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['observaciones']) . '\'' : 'NULL') . ',
						\'' . (isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 'B') . '\',
						' . (isset($this->datos['cabecera']['cuenta_pago']) && $this->datos['cabecera']['cuenta_pago'] != '' ? '\'' . $this->datos['cabecera']['cuenta_pago'] . '\'' : 'NULL') . ',
						' . (isset($this->datos['cabecera']['condiciones_pago']) ? '\'' . $this->datos['cabecera']['condiciones_pago'] . '\'' : '0') . ',
						' . ($this->datos['consignatario']['nombre'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['nombre']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['rfc'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['rfc']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['calle'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['calle']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['no_exterior'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['no_exterior']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['no_interior'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['no_interior']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['colonia'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['colonia']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['localidad'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['localidad']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['referencia'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['referencia']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['municipio'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['municipio']) . '\'' : 'NULL') . ',
						' . ($this->datos['consignatario']['estado'] != '' ? '\'' . pg_escape_string($this->datos['consignatario']['estado']) . '\'' : 'NULL') . ',
						247,
						' . ($this->datos['consignatario']['codigo_postal'] != '' ? '\'' . $this->datos['consignatario']['codigo_postal'] . '\'' : 'NULL') . ',
						' . ($detalle['aduana_entrada'] != '' ? '\'' . pg_escape_string($detalle['aduana_entrada']) . '\'' : 'NULL') . ',
						' . ($detalle['numero_pedimento'] != '' ? '\'' . $detalle['numero_pedimento'] . '\'' : 'NULL') . ',
						' . ($detalle['fecha_entrada'] != '' ? '\'' . $detalle['fecha_entrada'] . '\'' : 'NULL') . ',
						\'' . $archivo_reporte . '\',
						\'' . ($i == count($this->datos['detalle']) - 1 ? 'Y' : 'N') . '\',
						' . ($detalle['unidad'] != '' ? '\'' . pg_escape_string($detalle['unidad']) . '\'' : 'NULL') . ',
						' . $this->serie['no_aprobacion'] . ',
						' . $this->serie['anio_aprobacion'] . ',
						' . (isset($this->datos['cabecera']['cuenta_predial']) && $this->datos['cabecera']['cuenta_predial'] != '' ? '\'' . pg_escape_string($this->datos['cabecera']['cuenta_predial']) . '\'' : 'NULL') . '
					)
			' . ";\n";
		}
		
		$this->dbf->query($sql);
		
		/*
		@ Crear nombre del archivo local
		*/
		$this->file_name = $this->num_cia . '-' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']);
		
		/*
		@ Crear nombre del archivo remoto
		*/
		$pieces = explode('/', $this->datos['cabecera']['fecha']);
		
		$this->rfile_name = $this->num_cia . '-' . $this->serie['serie'] . ($this->folio > 0 ? $this->folio : $this->serie['folio']);
		
		/*
		@ Validar que el directorio para almacenar los comprobantes XML exista en el servidor
		*/
		if (!is_dir($this->lcomprobantes_xml . $this->num_cia)) {
			mkdir($this->lcomprobantes_xml . $this->num_cia);
		}
		
		/*
		@ Validar que el directorio para almacenar los comprobantes PDF exista en el servidor
		*/
		if (!is_dir($this->lcomprobantes_pdf . $this->num_cia)) {
			mkdir($this->lcomprobantes_pdf . $this->num_cia);
		}
		
		/*
		@ [08-Ene-2013] Obtener comprobantes [usar tabla c_file para saber si los comprobantes estan generados][NO FUNCIONO]
		*/
		
		$this->actualizarStatusServidor(0, 'Obteniendo comprobantes XML y PDF ' . $this->file_name);
		
		$retries = 0;
		
		$sql = '
			SELECT
				c_file_id
			FROM
				c_file
			WHERE
				name = \'' . $this->file_name . '.pdf\'
		';
		
		$downloaded_xml = FALSE;
		$downloaded_pdf = FALSE;
		
		do {
			$result = $this->dbf->query($sql);
			
			if ($result) {
				$this->ftp = @ftp_connect($this->ftp_server);
				@ftp_login($this->ftp, $this->ftp_user, $this->ftp_pass);
				
				$downloaded_xml = ftp_get($this->ftp, $this->lcomprobantes_xml . $this->num_cia . '/' . utf8_encode($this->file_name) . '.xml', $this->rcomprobantes_xml . utf8_encode($this->rfile_name) . '.xml', FTP_BINARY);
				
				$downloaded_pdf = ftp_get($this->ftp, $this->lcomprobantes_pdf . $this->num_cia . '/' . utf8_encode($this->file_name) . '.pdf', $this->rcomprobantes_pdf . utf8_encode($this->rfile_name) . '.pdf', FTP_BINARY);
				
				ftp_close($this->ftp);
			} else {
				sleep(1);
			}
			
			$retries++;
		} while(!$result && $retries < $this->recover_file_timeout);
		
		if (!$downloaded_xml) {
			$this->last_error = 'No se pudo obtener el archivo ' . $this->file_name . '.xml del servidor';
			
			$this->actualizarStatusServidor(-11, $this->last_error);
			
			return -11;
		} else if (!$downloaded_pdf) {
			$this->last_error = 'No se pudo obtener el archivo ' . $this->file_name . '.pdf del servidor';
			
			$this->actualizarStatusServidor(-12, $this->last_error);
			
			return -12;
		}
		
		$this->process_end = time();
		
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
			include_once('/var/www/lecaroz/includes/fpdf/fpdf.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once('/var/www/lecaroz/includes/cheques.inc.php');
		}
		
		$this->cambiarStatusServidor(self::SERVER_BLOCKED);
		
		$this->process_init = time();
		
		$pdf = new FPDF('P', 'mm', 'Letter');
		
		$pdf->SetDisplayMode('real', 'single');

		$pdf->SetMargins(0, 0, 0);
		
		$pdf->SetAutoPageBreak(FALSE);
		
		$pdf->AddPage('P', 'Letter');
		
		$pdf->Rect(6, 8, 203, 76, 'D');
		$pdf->Rect(6, 86, 203, 185, 'D');
		$pdf->Line(6, 92, 209, 92);
		$pdf->Line(6, 184, 209, 184);
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
		
		if ($this->num_cia <= 599) {
			$pdf->Image('imagenes/logo_lecaroz_panaderias.jpg', 7, 12, 70);
		} else if ($this->num_cia >= 900) {
			$pdf->Image('imagenes/logo_zapElite.jpg', 7, 12, 70);
		}
		
		
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
		
		/*
		@ [2012/11/08] Régimen fiscal
		*/
		if ($this->serie['regimen_fiscal'] != '') {
			$pdf->Ln(2);
			$pdf->SetX(60);
			$pdf->SetFontSize(6);
			$pdf->Cell(100, 1, $this->serie['regimen_fiscal'], 0, 0, 'C');
		}
		
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
		
		if (trim($this->datos['cabecera']['observaciones']) != '') {
			$pdf->SetFont('Arial', '', 6);
			$pdf->Line(6, 174, 209, 174);
			$pdf->SetXY(8, 174);
			$pdf->MultiCell(200, 4, $this->datos['cabecera']['observaciones'], 0, 'L');
		}
		
		$pdf->SetFont('Arial', 'B', 8);
		
		$tipo_pago = 'NO IDENTIFICADO';
		$cuenta_pago = '';
		$condiciones_pago = 'NO IDENTIFICADO';
		
		if (isset($this->datos['cabecera']['tipo_pago'])) {
			switch ($this->datos['cabecera']['tipo_pago']) {
				case '4':
					$tipo_pago = 'NO IDENTIFICADO';
					break;
				
				case 'B':
					$tipo_pago = 'EFECTIVO';
					break;
				
				case '1':
					$tipo_pago = 'TRANSFERENCIA BANCARIA';
					break;
				
				case '2':
					$tipo_pago = 'CHEQUE';
					break;
				
				case 'K':
					$tipo_pago = 'TARJETA DE CREDITO';
					break;
				
				default:
					$tipo_pago = 'NO IDENTIFICADO';
			}
		}
		
		if (isset($this->datos['cabecera']['cuenta_pago'])) {
			$cuenta_pago = $this->datos['cabecera']['cuenta_pago'];
		}
		
		if (isset($this->datos['cabecera']['condiciones_pago'])) {
			switch ($this->datos['cabecera']['condiciones_pago']) {
				case '1':
					$condiciones_pago = 'CONTADO';
					break;
				
				case '2':
					$condiciones_pago = 'CREDITO';
				
				default:
					$condiciones_pago = 'NO IDENTIFICADO';
			}
		}
		
		$pdf->SetXY(8, 187);
		$pdf->Cell(0, 0, 'METODO DE PAGO: ' . $tipo_pago . '   CUENTA DE PAGO: ' . $cuenta_pago . '   CONDICIONES DE PAGO: ' . $condiciones_pago);
		
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
		$pdf->MultiCell(182, 2, utf8_decode('POR ESTE PAGARE ME OBLIGO INCONDICIONALMENTE A PAGAR A LA ORDEN DE ') . $this->serie['razon_social'] . utf8_decode(' EL VALOR DE LAS MERCANCIAS QUE SE RECIBIERON A ENTERA SATISFACCION LA FIRMA PUESTA EN CUALQUIER LUGAR SE CONSIDERA COMO ACEPTACION DE ESTE PAGARE.'));
		
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
		
		$this->process_end = time();
		
		$this->registrarFactura();
		
		$this->cambiarStatusServidor(self::SERVER_FREE);
		
		return $this->file_name;
	}
	
	private function registrarFactura() {
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
						ieps,
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
						codigo_postal_consignatario,
						tipo_pago,
						cuenta_pago,
						condiciones_pago,
						process_time
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
						' . (isset($this->datos['cabecera']['ieps']) ? $this->datos['cabecera']['ieps'] : 0) . ',
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
						\'' . pg_escape_string($this->datos['consignatario']['codigo_postal']) . '\',
						\'' . (isset($this->datos['cabecera']['tipo_pago']) ? $this->datos['cabecera']['tipo_pago'] : 4) . '\',
						' . (isset($this->datos['cabecera']['cuenta_pago']) && $this->datos['cabecera']['cuenta_pago'] != '' ? '\'' . $this->datos['cabecera']['cuenta_pago'] . '\'' : 'NULL') . ',
						' . (isset($this->datos['cabecera']['condiciones_pago']) ? $this->datos['cabecera']['condiciones_pago'] : 'NULL') . ',
						' . ($this->process_end - $this->process_init) . '
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
							ieps,
							pieps,
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
							' . (isset($detalle['importe_ieps']) ? $detalle['importe_ieps'] : 0) . ',
							' . (isset($detalle['porcentaje_ieps']) ? $detalle['porcentaje_ieps'] : 0) . ',
							' . $detalle['importe_iva'] . ',
							' . $detalle['porcentaje_iva'] . ',
							\'' . pg_escape_string($detalle['numero_pedimento']) . '\',
							' . ($detalle['fecha_entrada'] != '' ? '\'' . $detalle['fecha_entrada'] . '\'' : 'NULL') . ',
							\'' . pg_escape_string($detalle['aduana_entrada']) . '\'
						)
			' . ";\n";
		}
		
		/*
		@ Actualizar series solo si no es reservado
		*/
		if ($this->folio == NULL) {
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
		if ($this->folio == NULL && $this->serie['folio'] == $this->serie['folio_final']) {
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
		
		$this->db->query($sql);
	}
	
	public function generarFactura($iduser, $num_cia, $tipo, $datos, $folio = NULL) {
		$this->iduser  = $iduser;
		
		$this->num_cia = $num_cia;
		$this->tipo    = $tipo;
		$this->datos   = $datos;
		$this->folio   = $folio;
		
		$this->status = 0;
		
		if (!$this->validarStatusServidor()) {
			return $this->status;
		}
		else if (!$this->validarSerie()) {
			return $this->status;
		}
		else if (!$this->validarDuplicados()) {
			return $this->status;
		}
		else if (!$this->validarLimiteTotal()) {
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
		global $db, $dbf;
		
		// [27-Jul-2013] Modificado el query de consulta para obtener datos extras para cancelar en openbravo
		$sql = '
			SELECT
				num_cia,
				(
					SELECT
						serie
					FROM
						facturas_electronicas_series
					WHERE
						num_cia = fe.num_cia
						AND tipo_serie = fe.tipo_serie
						AND fe.consecutivo BETWEEN folio_inicial AND folio_final
				)
					AS serie,
				consecutivo,
				comprobante_pdf,
				status
			FROM
				facturas_electronicas fe
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
			
			// [27-Jul-2013] Agregada la cancelacion de facturas en openbravo
			$sql = '
				UPDATE
					c_invoice
				SET
					tocancel = \'Y\'
				WHERE
					ad_org_id = ' . $result[0]['num_cia'] . '
					AND documentno = \'' . $result[0]['serie'] . $result[0]['consecutivo'] . '\'
			';

			$dbf->query($sql);

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
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'facturas.electronicas@lecaroz.com';
			$mail->FromName = utf8_decode('Lecaroz :: Facturación Electrónica');
		}
		
		/*
		@ Email para compañía
		*/
		if (trim($this->serie['email']) != '') {
			$mail->AddCC($this->serie['email']);
		}
		
		/*
		@ Email para Elite
		*/
		if ($this->num_cia >= 900) {
			$mail->AddBCC('contabilidad@zapateriaselite.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');
		}
		/*
		@ Email para Lecaroz
		*/
		else if ($this->num_cia < 900) {
			$mail->AddBCC('beatriz.flores@lecaroz.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');
			
			/*if (in_array($this->datos['cabecera']['clasificacion'], array(2, 5))) {
				$mail->AddBCC('facturas@lecaroz.com');
			}*/
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
		
		$mail->Subject = 'Comprobante Fiscal Digital :: ' . $this->serie['razon_social'];
		
		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd.tpl');
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
		
		$mail->AddAttachment($this->lcomprobantes_pdf . $this->num_cia . '/' . utf8_encode($this->file_name) . '.pdf');
		
		if ($this->serie['tipo_factura'] == 1) {
			$mail->AddAttachment($this->lcomprobantes_xml . $this->num_cia . '/' . utf8_encode($this->file_name) . '.xml');
		}
		
		if(!$mail->Send()) {
			return $mail->ErrorInfo;
		}
		else {
			return TRUE;
		}
	}
	
	public function enviarEmailCancelacion($id) {
		/*
		@ Validar que la librería PHPMailer este cargada
		*/
		if (!class_exists('PHPMailer')) {
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once(dirname(__FILE__) . '/cheques.inc.php');
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
		
		$result = $this->db->query($sql);
		
		$rec = $result[0];
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
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
			//$mail->AddBCC('sistemas@lecaroz.com');
		}
		
		$mail->Subject = utf8_decode('CANCELACIÓN DE COMPROBANTE FISCAL');
		
		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd_cancel.tpl');
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
			include_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		}
		
		/*
		@ Validar que la librería TemplatePower este cargada
		*/
		if (!class_exists('TemplatePower')) {
			include_once(dirname(__FILE__) . '/class.TemplatePower.inc.php');
		}
		
		/*
		@ Validar que exista la función num2string()
		*/
		if (!function_exists('num2string')) {
			include_once(dirname(__FILE__) . '/cheques.inc.php');
		}
		
		$mail = new PHPMailer();
		
		if ($this->num_cia >= 900) {
			$mail->IsSMTP();
			$mail->Host = 'mail.zapateriaselite.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@zapateriaselite.com';
			$mail->Password = 'facturaselectronicas';
			
			$mail->From = 'facturas.electronicas@zapateriaselite.com';
			$mail->FromName = utf8_decode('Zapaterías Elite :: Facturación Electrónica');
		}
		else {
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'facturas.electronicas@lecaroz.com';
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
		
		if (in_array($this->ultimoCodigoError(), array(-9, -10, -11, -12, -13, -50, -51))) {
			$mail->AddBCC('carlos.candelario@lecaroz.com');
		}
		
		$mail->Subject = 'ERROR AL GENERAR EL COMPROBANTE FISCAL';
		
		$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/fac/email_cfd_error.tpl');
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
		else if (in_array($this->ultimoCodigoError(), array(-81))) {
			$tpl->assign('accion', 'POR DISPOSICION DE LA OFICINA NO PUEDE EMITIR COMPROBANTES DE MAS DE 15,000.00 PESOS.<br /><br />SI NECESITA EMITIR ESTE COMPROBANTE DEBERA COMUNICARSE A ' . $oficina . ' CON EL ENCARGADO(A) DEL CONTROL DE FACTURACION Y CON INFORMACION EN MANO SOLICITAR LE HAGA LA EMISION DEL MISMO.');
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
		switch ($this->status) {
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
				return 'No se pudieron registrar los datos de facturación en el servidor de facturas electrónicas';
			break;
			
			case -6:
				return 'El proceso de timbrado para la factura esta detenido';
			break;
			
			case -7:
				return $this->last_error;
			break;
			
			case -8:
				return 'No se ha procesado la factura';
			break;
			
			case -9:
				return 'No se completo el timbrado de la factura';
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
			
			case -13:
				return 'El servidor de facturas reporto un error y no se generó el comprobante ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'];
			break;
			
			case -14:
				return 'El servidor de facturas no timbro el comprobante ' . $this->num_cia . '-' . $this->serie['serie'] . $this->serie['folio'];
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

			case -81:
				return 'No puede hacer facturas por más de 15,000.00 pesos';
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
					-109 => 'Código postal',
					-110 => $this->datos['cabecera']['estado']. ' NO ES UN ESTADO',
					-111 => 'R.F.C. (Estructura)'
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
				return '';
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
		
		$result = $this->db->query($sql);
		
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
			
			$this->db->query($sql);
			
			return $result[0]['folio'];
		}
		else {
			return NULL;
		}
	}
	
	public function recuperarFolio($num_cia, $tipo_serie, $fecha) {
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
		
		$result = $this->db->query($sql);
		
		if ($result) {
			return intval($result[0]['folio'], 10);
		}
		else {
			return NULL;
		}
	}
	
	public function utilizarFolio($iduser, $num_cia, $tipo_serie, $folio) {
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
		
		$this->db->query($sql);
	}
	
}

?>
