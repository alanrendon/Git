<?php
// PAGO MANUAL A PROVEEDORES
// Tabla ''
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

function pago_proveedores($factura, $num_cia_pago = NULL) {
	$query = "";			// Variable para lamacenar todos los querys de pago a proveedores

	// [30-Dic-2013] Query para OpenBravo
	$query_ob = '';

	// Declaración de variables
	$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
	$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
	$monto_min    = 0;

	$num_cia = NULL;		// Última compañía revisada
	$num_cia_aplica = NULL;
	$num_proveedor = NULL;	// Último proveedor revisado
	$anio = NULL;			// Último año
	$num_fac = 0;			// Número de facturas actual para el cheque
	$importe_cheque = 0;	// Importe del cheque

	$fac_count = 0;			// Contador de facturas
	$che_count = 0;			// Contador de cheques
	$trans_cont = 0;		// Contador de transferencias

	$cosgastos = NULL;		// Código de gasto para el cheque
	$nombre_gasto = NULL;	// Nombre del gasto para el cheque

	for ($i=0; $i<count($factura); $i++) {
		// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
		// [07-Jul-2008] Ahora también se verifica el cambio del código de gasto
		// [21-Ene-2013] Verificar el cambio de año
		if ($factura[$i]['num_cia'] != $num_cia_aplica || $factura[$i]['num_proveedor'] != $num_proveedor || $factura[$i]['anio'] != $anio || $factura[$i]['codgastos'] != $codgastos || $num_fac == $fac_x_cheque) {
			// Organizar datos para almacenar cheque
			if ($num_cia != NULL && $num_proveedor != NULL && $anio != NULL && $codgastos != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
				// Si el importe del cheque es mayor o igual al monto minimo, generarlo
				if ($importe_cheque >= $monto_min) {
					// Datos para la tabla de 'cheques'
					$cheque[$che_count]['cod_mov']           = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
					$cheque[$che_count]['num_proveedor']     = $num_proveedor;
					$cheque[$che_count]['num_cia']           = $num_cia;
					$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
					$cheque[$che_count]['concepto']          = $nombre_gasto;
					$cheque[$che_count]['facturas']          = $facturas;
					$cheque[$che_count]['fecha']             = $_SESSION['pmp']['fecha_cheque'];
					$cheque[$che_count]['folio']             = $folio_cheque;
					$cheque[$che_count]['importe']           = number_format($importe_cheque, 2, ".", "");
					$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
					$cheque[$che_count]['imp']               = "FALSE";
					$cheque[$che_count]['codgastos']         = $codgastos;
					$cheque[$che_count]['proceso']           = "TRUE";
					$cheque[$che_count]['cuenta']            = $_SESSION['pmp']['cuenta'];
					$cheque[$che_count]['poliza']            = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? "TRUE" : "FALSE";
					$cheque[$che_count]['site']				 = 'TRUE';

					if ($_SESSION['pmp']['cuenta'] == 2 && $trans) {
						$transfer[$trans_cont]['num_cia']       = $num_cia;
						$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
						$transfer[$trans_cont]['folio']         = $folio_cheque;
						$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
						$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
						$transfer[$trans_cont]['tipo']          = !$san ? "TRUE" : "FALSE";
						$transfer[$trans_cont]['status']        = "0";
						$transfer[$trans_cont]['folio_archivo'] = 0;
						$transfer[$trans_cont]['cuenta']        = 2;
						$transfer[$trans_cont]['gen_dep']       = 'TRUE';
						$trans_cont++;
					}
					else if ($_SESSION['pmp']['cuenta'] == 1 && $trans) {
						$transfer[$trans_cont]['num_cia']       = $num_cia;
						$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
						$transfer[$trans_cont]['folio']         = $folio_cheque;
						$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
						$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
						$transfer[$trans_cont]['tipo']          = "FALSE";
						$transfer[$trans_cont]['status']        = "0";
						$transfer[$trans_cont]['folio_archivo'] = 0;
						$transfer[$trans_cont]['cuenta']        = 1;
						$transfer[$trans_cont]['concepto']      = "PAGO FOLIO $folio_cheque";
						$transfer[$trans_cont]['gen_dep']       = "TRUE";
						$trans_cont++;
					}

					// Datos para la tabla de 'estado_cuenta'
					$cuenta[$che_count]['num_cia'] = $num_cia;
					$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
					$cuenta[$che_count]['concepto'] = $facturas;
					$cuenta[$che_count]['tipo_mov'] = "TRUE";
					$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
					$cuenta[$che_count]['cod_mov'] = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
					$cuenta[$che_count]['folio'] = $folio_cheque;
					$cuenta[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

					// Datos para la tabla de folios_cheque
					$folio[$che_count]['folio']     = $folio_cheque;
					$folio[$che_count]['num_cia']   = $num_cia;
					$folio[$che_count]['reservado'] = "FALSE";
					$folio[$che_count]['utilizado'] = "TRUE";
					$folio[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
					$folio[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

					// Datos para la tabla de movimiento_gastos
					$gasto[$che_count]['codgastos'] = $codgastos;
					$gasto[$che_count]['num_cia']   = $num_cia;
					$gasto[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
					$gasto[$che_count]['importe']   = number_format($importe_cheque, 2, ".", "");
					$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
					$gasto[$che_count]['captura']   = "TRUE";
					$gasto[$che_count]['factura']   = "";
					$gasto[$che_count]['folio']     = $folio_cheque;
					$gasto[$che_count]['cuenta']    = $_SESSION['pmp']['cuenta'];

					if ($num_cia_pago > 0) {
						$query .= "INSERT INTO pagos_otras_cias (num_cia, folio, cuenta, num_cia_aplica, fecha) VALUES ($num_cia, $folio_cheque, {$_SESSION['pmp']['cuenta']}, $num_cia_aplica, '{$_SESSION['pmp']['fecha_cheque']}');\n";
					}

					// Almacenar datos en la tabla de 'facturas_pagadas'
					$query .= $GLOBALS['db']->multiple_insert("facturas_pagadas", $fac_pag);

					// Borrar facturas pagadas de pasivo
					$query .= "DELETE FROM pasivo_proveedores WHERE id IN (";
					for ($f=0; $f<$fac_count; $f++)
						$query .= $fac_del[$f] . ($f < $fac_count - 1 ? "," : ");\n");

					// Actualizar saldo en libros
					//$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia;\n";
					$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']};\n";

					// Imcrementar número de folio
					$folio_cheque++;
					// Incrementar contador de cheques
					$che_count++;
				}
			}

			// Verificar el cambio de compañía
			if ($factura[$i]['num_cia'] != $num_cia_aplica) {
				// Cambiar la compañía
				$num_cia = $num_cia_pago > 0 ? $num_cia_pago : $factura[$i]['num_cia'];
				$num_cia_aplica = $factura[$i]['num_cia'];
				// Resetear número de proveedor
				$num_proveedor = NULL;
				// Resetear codigo de gastos
				$codgastos = NULL;
				// Resetear año
				$anio = NULL;
				// Resetear contador de facturas a cero
				$num_fac = 0;
				// Resetear importe de cheque a cero
				$importe_cheque = 0;

				// Obtener el último folio para los cheques de esta cuenta
				//$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia ORDER BY \"folio\" DESC LIMIT 1";
				$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia AND \"cuenta\" = {$_SESSION['pmp']['cuenta']} ORDER BY \"folio\" DESC LIMIT 1";
				$result = $GLOBALS['db']->query($sql);
				$folio_cheque = ($result) ? $result[0]['folio'] + 1 : 51;
			}

			// Cambiar proveedor
			$num_proveedor    = $factura[$i]['num_proveedor'];
			$nombre_proveedor = $factura[$i]['nombre'];
			$codgastos = $factura[$i]['codgastos'];
			$anio = $factura[$i]['anio'];
			$trans = $factura[$i]['trans'] == "t" && $_SESSION['pmp']['tipo_pago'] != 1 ? TRUE : FALSE;
			$san = $factura[$i]['san'] == "t" ? TRUE : FALSE;
			$tipo_saldo = $factura[$i]['trans'] == "t" ? "saldo_real" : "saldo";

			/*
			@ [22-Ene-2013] La cantidad de facturas por pago ahora esta definida en el catalogo de proveedores
			*/

			$fac_x_cheque = $factura[$i]['facturas_por_pago'];

			/*
			@ [21-May-2013] Para oficinas y talleres solo se hara un cheque por factura
			*/
			if (in_array($num_cia, array(700, 800))) {
				$fac_x_cheque = 1;
			}

			// Si el proveedor es el 13 (Pollos Guerra) cambiar el número de facturas por cheque a 20, si no, regresarlo a 10 [DESCONTINUADO]
			/*if (in_array($num_proveedor, array(13, 482)))
				$fac_x_cheque = 20;
			else if (in_array($num_proveedor, array(991))) {
				$fac_x_cheque = 1;
			}
			else
				$fac_x_cheque = 10;*/

			// Vaciar variables que almacenan facturas
			unset($fac_pag);

			// Resetear contador de registros de facturas a cero
			$fac_count = 0;
			// Resetear contador de facturas a cero
			$num_fac = 0;
			// Resetear importe de cheque a cero
			$importe_cheque = 0;
			// Resetear concepto del cheque
			$facturas = "";
		}

		// Proceso de generado de cheque
		// Se considera el caso de el máximo número de facturas pagadas por un cheque
		if ($num_fac < $fac_x_cheque) {
			// Sumar importe de factura al importe del cheque
			$importe_cheque = round($importe_cheque,2) + round($factura[$i]['total'],2);

			// Factura a borrar de pasivo
			$fac_del[$fac_count] = $factura[$i]['id'];
			$fac_pollos[$fac_count] = $factura[$i]['num_fact'];

			// Organizar datos para almacenar factura en la tabla de facturas_pagadas
			$fac_pag[$fac_count]['num_cia']       = $num_cia;
			$fac_pag[$fac_count]['num_proveedor'] = $num_proveedor;
			$fac_pag[$fac_count]['num_fact']      = $factura[$i]['num_fact'];
			$fac_pag[$fac_count]['codgastos']     = $factura[$i]['codgastos'];	// ESTE CAMPO FALTA AGREGARLO A LA TABLA DE 'pasivo_proveedores'
			$fac_pag[$fac_count]['total']         = $factura[$i]['total'];
			$fac_pag[$fac_count]['descripcion']   = $factura[$i]['descripcion'];
			$fac_pag[$fac_count]['fecha']     = $factura[$i]['fecha_mov'];
			$fac_pag[$fac_count]['fecha_cheque']  = $_SESSION['pmp']['fecha_cheque'];
			$fac_pag[$fac_count]['folio_cheque']  = $folio_cheque;
			$fac_pag[$fac_count]['proceso']       = "TRUE";						// Proceso automático
			$fac_pag[$fac_count]['imp']           = "FALSE";
			$fac_pag[$fac_count]['cuenta']        = $_SESSION['pmp']['cuenta'];

			// Agregar el número de factura al concepto del cheque
			if ($fac_count > 0)
				$facturas .= " ";
			$facturas .= /*str_pad(*/$factura[$i]['num_fact']/*, 7, '0', STR_PAD_LEFT)*/;

			$codgastos = $factura[$i]['codgastos'];
			$nombre_gasto = $factura[$i]['nombre_gasto'];

			// Incrementar contador de facturas
			$fac_count++;
			// Incrementar contador de facturas por cheque
			$num_fac++;
		}
	}

	// Organizar datos para almacenar cheque
	if ($num_cia != NULL && $num_proveedor != NULL && $anio != NULL && $codgastos != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
		// Si el importe del cheque es mayor o igual al monto minimo, generarlo
		if ($importe_cheque >= $monto_min) {
			// Datos para la tabla de 'cheques'
			$cheque[$che_count]['cod_mov']           = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
			$cheque[$che_count]['num_proveedor']     = $num_proveedor;
			$cheque[$che_count]['num_cia']           = $num_cia;
			$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
			$cheque[$che_count]['concepto']          = $nombre_gasto;
			$cheque[$che_count]['facturas']          = $facturas;
			$cheque[$che_count]['fecha']             = $_SESSION['pmp']['fecha_cheque'];
			$cheque[$che_count]['folio']             = $folio_cheque;
			$cheque[$che_count]['importe']           = number_format($importe_cheque, 2, ".", "");
			$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
			$cheque[$che_count]['imp']               = "FALSE";
			$cheque[$che_count]['codgastos']         = $codgastos;
			$cheque[$che_count]['proceso']           = "TRUE";
			$cheque[$che_count]['cuenta']            = $_SESSION['pmp']['cuenta'];
			$cheque[$che_count]['poliza']            = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? "TRUE" : "FALSE";
			$cheque[$che_count]['site']				 = 'TRUE';

			if ($_SESSION['pmp']['cuenta'] == 2 && $trans) {
				$transfer[$trans_cont]['num_cia']       = $num_cia;
				$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
				$transfer[$trans_cont]['folio']         = $folio_cheque;
				$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
				$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
				$transfer[$trans_cont]['tipo']          = !$san ? "TRUE" : "FALSE";
				$transfer[$trans_cont]['status']        = "0";
				$transfer[$trans_cont]['folio_archivo'] = 0;
				$transfer[$trans_cont]['cuenta']        = 2;
				$transfer[$trans_cont]['gen_dep']       = 'TRUE';
				$trans_cont++;
			}
			else if ($_SESSION['pmp']['cuenta'] == 1 && $trans) {
				$transfer[$trans_cont]['num_cia']       = $num_cia;
				$transfer[$trans_cont]['num_proveedor'] = $num_proveedor;
				$transfer[$trans_cont]['folio']         = $folio_cheque;
				$transfer[$trans_cont]['importe']       = number_format($importe_cheque,2,".","");
				$transfer[$trans_cont]['fecha_gen']     = $_SESSION['pmp']['fecha_cheque'];
				$transfer[$trans_cont]['tipo']          = "FALSE";
				$transfer[$trans_cont]['status']        = "0";
				$transfer[$trans_cont]['folio_archivo'] = 0;
				$transfer[$trans_cont]['cuenta']        = 1;
				$transfer[$trans_cont]['concepto']      = "PAGO FOLIO $folio_cheque";
				$transfer[$trans_cont]['gen_dep']       = "TRUE";
				$trans_cont++;
			}

			// Datos para la tabla de 'estado_cuenta'
			$cuenta[$che_count]['num_cia'] = $num_cia;
			$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
			$cuenta[$che_count]['concepto'] = $facturas;
			$cuenta[$che_count]['tipo_mov'] = "TRUE";
			$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
			$cuenta[$che_count]['cod_mov'] = /*$_SESSION['pmp']['cuenta'] == 2 && */$trans ? 41 : 5;
			$cuenta[$che_count]['folio'] = $folio_cheque;
			$cuenta[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

			// Datos para la tabla de folios_cheque
			$folio[$che_count]['folio']     = $folio_cheque;
			$folio[$che_count]['num_cia']   = $num_cia;
			$folio[$che_count]['reservado'] = "FALSE";
			$folio[$che_count]['utilizado'] = "TRUE";
			$folio[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
			$folio[$che_count]['cuenta'] = $_SESSION['pmp']['cuenta'];

			$gasto[$che_count]['codgastos'] = $codgastos;
			$gasto[$che_count]['num_cia']   = $num_cia;
			$gasto[$che_count]['fecha']     = $_SESSION['pmp']['fecha_cheque'];
			$gasto[$che_count]['importe']   = number_format($importe_cheque, 2, ".", "");
			$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
			$gasto[$che_count]['captura']   = "TRUE";
			$gasto[$che_count]['factura']   = "";
			$gasto[$che_count]['folio']     = $folio_cheque;
			$gasto[$che_count]['cuenta']    = $_SESSION['pmp']['cuenta'];

			if ($num_cia_pago > 0) {
				$query .= "INSERT INTO pagos_otras_cias (num_cia, folio, cuenta, num_cia_aplica, fecha) VALUES ($num_cia, $folio_cheque, {$_SESSION['pmp']['cuenta']}, $num_cia_aplica, '{$_SESSION['pmp']['fecha_cheque']}');\n";
			}

			// Almacenar datos en la tabla de 'facturas_pagadas'
			$query .= $GLOBALS['db']->multiple_insert("facturas_pagadas", $fac_pag);

			// Borrar facturas pagadas de pasivo
			$query .= "DELETE FROM pasivo_proveedores WHERE id IN (";
			for ($f=0; $f<$fac_count; $f++)
				$query .= $fac_del[$f] . ($f < $fac_count - 1 ? "," : ");\n");

			// Actualizar saldo en libros
			//$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia;\n";
			$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']};\n";

			// Imcrementar número de folio
			$folio_cheque++;
			// Incrementar contador de cheques
			$che_count++;
		}
	}

	// Insertar datos en la base
	$query .= $GLOBALS['db']->multiple_insert("cheques",$cheque);
	$query .= $GLOBALS['db']->multiple_insert("folios_cheque",$folio);
	$query .= $GLOBALS['db']->multiple_insert("estado_cuenta",$cuenta);
	$query .= $GLOBALS['db']->multiple_insert("movimiento_gastos",$gasto);
	if (isset($transfer)) $query .= $GLOBALS['db']->multiple_insert("transferencias_electronicas",$transfer);

	return array($query, $query_ob);
}

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por pagar";
$descripcion_error[2] = "No hay facturas por pagar para el proveedor indicado";
$descripcion_error[3] = "No hay facturas por pagar para la compañía indicada";

if (isset($_GET['cancelar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_pro_v3.php");
	die;
}

if (isset($_GET['generar'])) {
	$id = $_POST['id'];

	$clabe_cuenta = $_SESSION['pmp']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	// Construir script sql
	$sql = "SELECT id, num_cia, num_fact, total, pasivo_proveedores.descripcion AS descripcion, fecha AS fecha_mov, EXTRACT(YEAR FROM fecha) AS anio, num_proveedor, nombre, codgastos, catalogo_gastos.descripcion";
	$sql .= " AS nombre_gasto, trans, san, facturas_por_pago FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) LEFT JOIN catalogo_gastos USING (codgastos) WHERE id IN (";
	// Añade todas las facturas seleccionadas para pagar
	for ($i=0; $i < count($id); $i++)
		$sql .= $id[$i] . ($i < count($id) - 1 ? "," : ")");
	$sql .= " ORDER BY num_cia ASC,";
	$sql .= "num_proveedor, fecha ASC, total DESC";
	// Obtener facturas
	$facturas = $db->query($sql);

	$num_cia_pago = isset($_POST['num_cia_pago']) && $_POST['num_cia_pago'] > 0 ? $_POST['num_cia_pago'] : NULL;

	// Ejecutar función de pago
	if ($facturas) {
		$sql = pago_proveedores($facturas, $num_cia_pago);
		$db->query($sql[0]);

		// [30-Dic-2013] Conexión a la base de datos de facturas
		// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');
		// $dbf = new DBclass('pgsql://carlos:D4n13l4*@127.0.0.1:5432/ob_lecaroz', 'autocommit=yes');

		// $dbf->query($sql[1]);
	}

	// Generar nuevamente el listado de proveedores o compañias
	$no_pago = array();
	for ($i = 0; $i < count($_SESSION['pmp']['no_pago']); $i++)
		if ($_SESSION['pmp']['no_pago'][$i] > 0)
			$no_pago[] = $_SESSION['pmp']['no_pago'][$i];

	if ($_SESSION['pmp']['tipo'] == 1) {
		$sql = "SELECT pasivo_proveedores.num_proveedor AS num_proveedor, catalogo_proveedores.nombre AS nombre, trans FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " pasivo_proveedores.num_cia = {$_SESSION['pmp']['num_cia']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND catalogo_companias.$clabe_cuenta IS NOT NULL AND length(catalogo_companias.$clabe_cuenta) = 11 AND (verfac = FALSE OR copia_fac = TRUE)";
		$sql .= " AND (pasivo_proveedores.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " GROUP BY pasivo_proveedores.num_proveedor, catalogo_proveedores.nombre, trans ORDER BY num_proveedor ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v3.php?codigo_error=1");
			die;
		}

		$_SESSION['pmp']['proveedores'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
			$_SESSION['pmp']['tipo_pro'][$i] = $result[$i]['trans'];
		}
	}
	else {
		$sql = "SELECT num_cia, nombre_corto, catalogo_companias.rfc FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " pasivo_proveedores.num_proveedor = {$_SESSION['pmp']['num_proveedor']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '{$_SESSION['pmp']['fecha_corte']}'/* AND $clabe_cuenta IS NOT NULL AND length($clabe_cuenta) = 11*/ AND (verfac = FALSE OR copia_fac = TRUE)";
		$sql .= " AND (pasivo_proveedores.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " GROUP BY num_cia, nombre_corto, catalogo_companias.rfc ORDER BY num_cia ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v3.php?codigo_error=1");
			die;
		}

		$_SESSION['pmp']['compañias'] = array();
		$_SESSION['pmp']['rfcs'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['rfcs'][$i] = $result[$i]['rfc'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}
	}
}

if (isset($_GET['terminar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_pro_v3.php");
	die;
}

if (isset($_GET['siguiente'])) {
	$list = $_SESSION['pmp']['tipo'] == 1 ? "proveedores" : "compañias";
	$field = $_SESSION['pmp']['tipo'] == 1 ? "num_proveedor" : "num_cia";

	if ($_POST[$field] > 0) {
		if (($key = array_search($_POST[$field], $_SESSION['pmp'][$list])) !== FALSE)
			$_SESSION['pmp']['next'] = $key;
		else
			$_SESSION['pmp']['next'] = 0;
	}
	else if ($_SESSION['pmp']['next'] < count($_SESSION['pmp'][$list]) - 1)
		$_SESSION['pmp']['next'] = $_SESSION['pmp']['next'] + 1;
	else
		$_SESSION['pmp']['next'] = 0;

	header("location: ./ban_pma_pro_v3.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pma_pro_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tipo'])) {
	$_SESSION['pmp'] = $_GET;

	$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	$no_pago = array();
	for ($i = 0; $i < count($_GET['no_pago']); $i++)
		if ($_GET['no_pago'][$i] > 0)
			$no_pago[] = $_GET['no_pago'][$i];

	if ($_GET['tipo'] == 1) {
		$sql = "SELECT pasivo_proveedores.num_proveedor AS num_proveedor, catalogo_proveedores.nombre AS nombre, trans FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " pasivo_proveedores.num_cia = $_GET[num_cia] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '$_GET[fecha_corte]' AND catalogo_companias.$clabe_cuenta IS NOT NULL AND length(catalogo_companias.$clabe_cuenta) = 11 AND total <> 0 AND (verfac = FALSE OR copia_fac = TRUE)";
		$sql .= " AND (pasivo_proveedores.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " GROUP BY pasivo_proveedores.num_proveedor, catalogo_proveedores.nombre, trans ORDER BY num_proveedor ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v3.php?codigo_error=1");
			die;
		}

		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
			$_SESSION['pmp']['tipo_pro'][$i] = $result[$i]['trans'];
		}

		if (($key = array_search($_GET['num_proveedor'], $_SESSION['pmp']['proveedores'])) !== FALSE)
		{
			$next = $key;
		}
		else
		{
			if ($_GET['num_proveedor'] > 0)
			{
				unset($_SESSION['pmp']);
				header("location: ./ban_pma_pro_v3.php?codigo_error=2");
				die;
			}

			$next = 0;
		}

		$_SESSION['pmp']['next'] = $next;
	}
	else {
		$sql = "SELECT num_cia, nombre_corto, catalogo_companias.rfc FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		// $sql .= $_GET['num_cia'] > 0 ? " pasivo_proveedores.num_cia = $_GET[num_cia] AND" : "";
		$sql .= $_GET['num_proveedor'] > 0 ? " pasivo_proveedores.num_proveedor = $_GET[num_proveedor] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha <= '$_GET[fecha_corte]'/* AND $clabe_cuenta IS NOT NULL AND length($clabe_cuenta) = 11*/ AND total <> 0 AND (verfac = FALSE OR copia_fac = TRUE)";
		$sql .= " AND (pasivo_proveedores.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " GROUP BY num_cia, nombre_corto, catalogo_companias.rfc ORDER BY num_cia ASC";
		$result = $db->query($sql);

		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v3.php?codigo_error=1");
			die;
		}

		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['rfcs'][$i] = $result[$i]['rfc'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}

		if (($key = array_search($_GET['num_cia'], $_SESSION['pmp']['compañias'])) !== FALSE)
		{
			$next = $key;
		}
		else
		{
			if ($_GET['num_cia'] > 0)
			{
				unset($_SESSION['pmp']);
				header("location: ./ban_pma_pro_v3.php?codigo_error=3");
				die;
			}

			$next = 0;
		}

		$_SESSION['pmp']['next'] = $next;
	}
}

if (!isset($_SESSION['pmp'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha_corte", date("d/m/Y"));
	$tpl->assign("fecha_cheque", date("d/m/Y"));

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die;
}

if (isset($_SESSION['pmp'])) {
	$clabe_cuenta = $_SESSION['pmp']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

	$no_pago = array();
	for ($i = 0; $i < count($_SESSION['pmp']['no_pago']); $i++)
		if ($_SESSION['pmp']['no_pago'][$i] > 0)
			$no_pago[] = $_SESSION['pmp']['no_pago'][$i];

	if ($_SESSION['pmp']['tipo'] == 1) {
		$sql = "SELECT id, num_cia, nombre_corto, $clabe_cuenta, fecha AS fecha_mov, num_fact, descripcion AS concepto, total AS importe FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia)";
		$sql .= " WHERE pasivo_proveedores.num_proveedor = {$_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " AND num_cia = {$_SESSION['pmp']['num_cia']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND $clabe_cuenta IS NOT NULL AND length($clabe_cuenta) = 11 AND fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND total <> 0 AND (verfac = 'FALSE' OR copia_fac = 'TRUE')";
		$sql .= " AND (pasivo_proveedores.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " ORDER BY num_cia, fecha ASC";
		$result = $db->query($sql);

		$tpl->newBlock("proveedor");
		$tpl->assign("num_proveedor", $_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_proveedor", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		$tpl->assign("tipo_pago", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "f" ? "CHEQUE" : "TRANSFERENCIA");
		$tpl->assign("tipo", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? 1 : 0);
		$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : "checked");

		$num_cia = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				$num_cia = $result[$i]['num_cia'];

				$tpl->newBlock("block_cia");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
				$tpl->assign("clabe_cuenta", $result[$i][$clabe_cuenta]);

				$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : "checked");

				$tmp = $db->query("SELECT saldo_libros FROM saldos WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']}");
				$saldo = $tmp ? $tmp[0]['saldo_libros'] : 0;
				$depositos = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pmp']['cuenta']} AND tipo_mov = 'FALSE' AND fecha_con IS NULL");

				$tpl->assign("saldo", number_format($saldo - ($_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? $depositos[0]['sum'] : 0), 2, ".", ","));

				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);

				$total = 0;
			}
			$tpl->newBlock("fac_cia");
			$tpl->assign("checked", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "" : "checked");
			$tpl->assign("i", $i);
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha_mov']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", $result[$i]['concepto']);
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ""));
			$tpl->assign("fimporte", number_format($result[$i]['importe'], 2, ".", ","));

			$total += $result[$i]['importe'];
			$tpl->assign("block_cia.total", $_SESSION['pmp']['tipo_pro'][$_SESSION['pmp']['next']] == "t" ? "0.00" : number_format($total, 2, ".", ""));

			$tpl->assign("block_cia.fin", $i);
		}

		for ($i = 0; $i < count($_SESSION['pmp']['proveedores']); $i++) {
			$tpl->newBlock("pro");
			$tpl->assign("num_pro", $_SESSION['pmp']['proveedores'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}
	}
	else {
		$sql = "SELECT id, pp.num_proveedor, cp.nombre, $clabe_cuenta, fecha AS fecha_mov, num_fact, descripcion AS concepto, total AS importe, trans, cc.rfc FROM pasivo_proveedores pp LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = pp.num_proveedor)";
		$sql .= " LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " AND pp.num_proveedor = {$_SESSION['pmp']['num_proveedor']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND pp.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND fecha <= '{$_SESSION['pmp']['fecha_corte']}' AND total <> 0 AND (verfac = FALSE OR copia_fac = TRUE)/* AND $clabe_cuenta IS NOT NULL AND length($clabe_cuenta) = 11*/";
		$sql .= " AND (pp.num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM facturas_pendientes WHERE fecha_aclaracion IS NULL)";
		$sql .= " ORDER BY pp.num_proveedor, fecha ASC";
		$result = $db->query($sql);

		$tmp = $db->query("SELECT saldo_libros FROM saldos WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]} AND cuenta = {$_SESSION['pmp']['cuenta']}");
		$saldo = $tmp ? $tmp[0]['saldo_libros'] : 0;
		$depositos = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]} AND cuenta = {$_SESSION['pmp']['cuenta']} AND tipo_mov = 'FALSE' AND fecha_con IS NULL");

		$tpl->newBlock("compania");
		$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_cia", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		$tpl->assign("saldo", number_format($saldo - ($_SESSION['pmp']['tipo'] == "t" ? $depositos[0]['sum'] : 0), 2, ".", ","));

		// [19-May-2015] Obtener compañías con el mismo rfc y sus saldos
		$otras_cias = $db->query("SELECT
			num_cia,
			nombre_corto,
			saldo_libros
		FROM
			saldos s
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			cuenta = {$_SESSION['pmp']['cuenta']}
			AND rfc = '{$_SESSION['pmp']['rfcs'][$_SESSION['pmp']['next']]}'
			AND num_cia != {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]}
			--AND saldo_libros > 0
			AND LENGTH(TRIM(" . ($_SESSION['pmp']['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . ")) >= 10
		ORDER BY
			num_cia");

		if ($otras_cias)
		{
			foreach ($otras_cias as $oc) {
				$tpl->newBlock('otra_cia');
				$tpl->assign('num_cia', $oc['num_cia']);
				$tpl->assign('nombre_cia', $oc['nombre_corto']);
				$tpl->assign('saldo', number_format($oc['saldo_libros'], 2));
			}
		}

		$num_pro = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_pro != $result[$i]['num_proveedor']) {
				$num_pro = $result[$i]['num_proveedor'];

				$tpl->newBlock("block_pro");
				$tpl->assign("num_proveedor", $num_pro);
				$tpl->assign("nombre_proveedor", $result[$i]['nombre']);
				$tpl->assign("tipo_pago", $result[$i]['trans'] == "f" ? "CHEQUE" : "TRANSFERENCIA");
				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);
				$tpl->assign("checked", $result[$i]['trans'] == "f" ? "checked" : "");

				$total = 0;
			}
			$tpl->newBlock("fac_pro");
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("checked", $result[$i]['trans'] == "f" ? "checked" : "");
			$tpl->assign("i", $i);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha_mov']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", $result[$i]['concepto']);
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
			$tpl->assign("tipo", $result[$i]['trans'] == "f" ? 0 : 1);

			$total += $result[$i]['importe'];
			$tpl->assign("block_pro.total", $result[$i]['trans'] == "f" ? number_format($total, 2, ".", ",") : "0.00");

			$tpl->assign("block_pro.fin", $i);
		}

		for ($i = 0; $i < count($_SESSION['pmp']['compañias']); $i++) {
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}

		$cias_pago = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia < 900 ORDER BY num_cia');
		foreach ($cias_pago as $c) {
			$tpl->newBlock('cia_pago');
			$tpl->assign('num_cia', $c['num_cia']);
			$tpl->assign('nombre', $c['nombre']);
		}

	}

	$tpl->printToScreen();
	die;
}
?>
