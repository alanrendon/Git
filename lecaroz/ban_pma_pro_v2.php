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

function pago_proveedores($factura) {
	$query = "";			// Variable para lamacenar todos los querys de pago a proveedores
	
	// Declaración de variables
	$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
	$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
	$monto_min    = 0;
	
	$num_cia = NULL;		// Última compañía revisada
	$num_proveedor = NULL;	// Último proveedor revisado
	$num_fac = 0;			// Número de facturas actual para el cheque
	$importe_cheque = 0;	// Importe del cheque
	
	$fac_count = 0;			// Contador de facturas
	$che_count = 0;			// Contador de cheques
	
	$cosgastos = NULL;		// Código de gasto para el cheque
	$nombre_gasto = NULL;	// Nombre del gasto para el cheque
	
	for ($i=0; $i<count($factura); $i++) {
		// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
		if ($factura[$i]['num_cia'] != $num_cia || $factura[$i]['num_proveedor'] != $num_proveedor || $num_fac == $fac_x_cheque) {
			// Organizar datos para almacenar cheque
			if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
				// Si el importe del cheque es mayor o igual al monto minimo, generarlo
				if ($importe_cheque >= $monto_min) {
					// Datos para la tabla de 'cheques'
					$cheque[$che_count]['cod_mov']           = 5;
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
					$cheque[$che_count]['poliza']            = "FALSE";
					
					// Datos para la tabla de 'estado_cuenta'
					$cuenta[$che_count]['num_cia'] = $num_cia;
					$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
					$cuenta[$che_count]['concepto'] = $facturas;
					$cuenta[$che_count]['tipo_mov'] = "TRUE";
					$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
					$cuenta[$che_count]['cod_mov'] = 5;
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
			if ($factura[$i]['num_cia'] != $num_cia) {
				// Cambiar la compañía
				$num_cia = $factura[$i]['num_cia'];
				// Resetear número de proveedor
				$num_proveedor = NULL;
				// Resetear contador de facturas a cero
				$num_fac = 0;
				// Resetear importe de cheque a cero
				$importe_cheque = 0;
				
				// Obtener el último folio para los cheques de esta cuenta
				//$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia ORDER BY \"folio\" DESC LIMIT 1";
				$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia AND \"cuenta\" = {$_SESSION['pmp']['cuenta']} ORDER BY \"folio\" DESC LIMIT 1";
				$result = $GLOBALS['db']->query($sql);
				$folio_cheque = ($result) ? $result[0]['folio'] + 1 : 1;
			}
			
			// Cambiar proveedor
			$num_proveedor    = $factura[$i]['num_proveedor'];
			$nombre_proveedor = $factura[$i]['nombre'];
			
			// Si el proveedor es el 13 (Pollos Guerra) cambiar el número de facturas por cheque a 15, si no, regresarlo a 10
			if ($num_proveedor == 13)
				$fac_x_cheque = 15;
			else
				$fac_x_cheque = 10;
			
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
			$fac_pag[$fac_count]['fecha_mov']     = $factura[$i]['fecha_mov'];
			$fac_pag[$fac_count]['fecha_pago']    = $factura[$i]['fecha_pago'];
			$fac_pag[$fac_count]['fecha_cheque']  = $_SESSION['pmp']['fecha_cheque'];
			$fac_pag[$fac_count]['folio_cheque']  = $folio_cheque;
			$fac_pag[$fac_count]['proceso']       = "TRUE";						// Proceso automático
			$fac_pag[$fac_count]['imp']           = "FALSE";
			
			// Agregar el número de factura al concepto del cheque
			if ($fac_count > 0)
				$facturas .= " ";
			$facturas .= fillZero($factura[$i]['num_fact'],7);
			
			$codgastos = $factura[$i]['codgastos'];
			$nombre_gasto = $factura[$i]['nombre_gasto'];
			
			// Incrementar contador de facturas
			$fac_count++;
			// Incrementar contador de facturas por cheque
			$num_fac++;
		}
	}
	
	// Organizar datos para almacenar cheque
	if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
		// Si el importe del cheque es mayor o igual al monto minimo, generarlo
		if ($importe_cheque >= $monto_min) {
			// Datos para la tabla de 'cheques'
			$cheque[$che_count]['cod_mov']           = 5;
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
			$cheque[$che_count]['poliza']            = "FALSE";
			
			// Datos para la tabla de 'estado_cuenta'
			$cuenta[$che_count]['num_cia'] = $num_cia;
			$cuenta[$che_count]['fecha'] = $_SESSION['pmp']['fecha_cheque'];
			$cuenta[$che_count]['concepto'] = $facturas;
			$cuenta[$che_count]['tipo_mov'] = "TRUE";
			$cuenta[$che_count]['importe']  = number_format($importe_cheque, 2, ".", "");
			$cuenta[$che_count]['cod_mov'] = 5;
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
	
	return $query;
}

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por pagar";

if (isset($_GET['cancelar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_pro_v2.php");
	die;
}

if (isset($_GET['generar'])) {
	$id = $_POST['id'];
	
	$clabe_cuenta = $_SESSION['pmp']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	// Construir script sql
	$sql = "SELECT \"id\",\"num_cia\",\"num_fact\",\"total\",\"pasivo_proveedores\".\"descripcion\" AS \"descripcion\",\"fecha_mov\",\"fecha_pago\",\"num_proveedor\",\"nombre\",\"codgastos\",\"catalogo_gastos\".\"descripcion\" AS \"nombre_gasto\" FROM \"pasivo_proveedores\" JOIN \"catalogo_proveedores\" USING(\"num_proveedor\") LEFT JOIN \"catalogo_gastos\" USING (\"codgastos\") WHERE";
	$sql .= " \"id\" IN (";
	// Añade todas las facturas seleccionadas para pagar
	for ($i=0; $i < count($id); $i++)
		$sql .= $id[$i] . ($i < count($id) - 1 ? "," : ")");
	$sql .= " ORDER BY \"num_cia\" ASC,";
	$sql .= "\"num_proveedor\",\"fecha_pago\" ASC,\"total\" DESC";
	// Obtener facturas
	$facturas = $db->query($sql);
	
	// Ejecutar función de pago
	if ($facturas) {
		$sql = pago_proveedores($facturas);//echo $sql; die;
		$db->query($sql);
	}
	
	// Generar nuevamente el listado de proveedores o compañias
	$no_pago = array();
	for ($i = 0; $i < count($_SESSION['pmp']['no_pago']); $i++)
		if ($_SESSION['pmp']['no_pago'][$i] > 0)
			$no_pago[] = $_SESSION['pmp']['no_pago'][$i];
	
	if ($_SESSION['pmp']['tipo'] == 1) {
		$sql = "SELECT pasivo_proveedores.num_proveedor AS num_proveedor, catalogo_proveedores.nombre AS nombre FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " pasivo_proveedores.num_cia = {$_SESSION['pmp']['num_cia']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha_pago <= '{$_SESSION['pmp']['fecha_corte']}' AND catalogo_companias.$clabe_cuenta IS NOT NULL GROUP BY pasivo_proveedores.num_proveedor, catalogo_proveedores.nombre ORDER BY num_proveedor ASC";
		$result = $db->query($sql);
		
		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v2.php?codigo_error=1");
			die;
		}
		
		$_SESSION['pmp']['proveedores'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
		}
	}
	else {
		$sql = "SELECT num_cia, nombre_corto FROM pasivo_proveedores LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " pasivo_proveedores.num_proveedor = {$_SESSION['pmp']['num_proveedor']} AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha_pago <= '{$_SESSION['pmp']['fecha_corte']}' AND $clabe_cuenta IS NOT NULL GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC";
		$result = $db->query($sql);
		
		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v2.php?codigo_error=1");
			die;
		}
		
		$_SESSION['pmp']['compañias'] = array();
		$_SESSION['pmp']['nombres'] = array();
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}
	}
}

if (isset($_GET['terminar'])) {
	unset($_SESSION['pmp']);
	header("location: ./ban_pma_pro_v2.php");
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
	
	header("location: ./ban_pma_pro_v2.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pma_pro_v2.tpl");
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
		$sql = "SELECT pasivo_proveedores.num_proveedor AS num_proveedor, catalogo_proveedores.nombre AS nombre FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " pasivo_proveedores.num_cia = $_GET[num_cia] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha_mov <= '$_GET[fecha_corte]' AND catalogo_companias.$clabe_cuenta IS NOT NULL AND total > 0 GROUP BY pasivo_proveedores.num_proveedor, catalogo_proveedores.nombre ORDER BY num_proveedor ASC";
		$result = $db->query($sql);
		
		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v2.php?codigo_error=1");
			die;
		}
		
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['proveedores'][$i] = $result[$i]['num_proveedor'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre'];
		}
		
		if ($key = array_search($_GET['num_proveedor'], $_SESSION['pmp']['proveedores']))
			$next = $key;
		else
			$next = 0;
		
		$_SESSION['pmp']['next'] = $next;
	}
	else {
		$sql = "SELECT num_cia, nombre_corto FROM pasivo_proveedores LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_GET['num_proveedor'] > 0 ? " pasivo_proveedores.num_proveedor = $_GET[num_proveedor] AND" : "";
		if (count($no_pago) > 0) {
			$sql .= " pasivo_proveedores.num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ") AND");
		}
		$sql .= " fecha_mov <= '$_GET[fecha_corte]' AND $clabe_cuenta IS NOT NULL AND total > 0 GROUP BY num_cia, nombre_corto ORDER BY num_cia ASC";
		$result = $db->query($sql);
		
		if (!$result) {
			unset($_SESSION['pmp']);
			header("location: ./ban_pma_pro_v2.php?codigo_error=1");
			die;
		}
		
		for ($i = 0; $i < count($result); $i++) {
			$_SESSION['pmp']['compañias'][$i] = $result[$i]['num_cia'];
			$_SESSION['pmp']['nombres'][$i] = $result[$i]['nombre_corto'];
		}
		
		if ($key = array_search($_GET['num_cia'], $_SESSION['pmp']['compañias']))
			$next = $key;
		else
			$next = 0;
		
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
		$sql = "SELECT id, num_cia, nombre_corto, $clabe_cuenta, fecha_mov, num_fact, descripcion AS concepto, total AS importe FROM pasivo_proveedores LEFT JOIN catalogo_companias USING (num_cia)";
		$sql .= " WHERE pasivo_proveedores.num_proveedor = {$_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_cia'] > 0 ? " AND num_cia = {$_SESSION['pmp']['num_cia']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND num_cia NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND $clabe_cuenta IS NOT NULL AND fecha_mov <= '{$_SESSION['pmp']['fecha_corte']}' AND total > 0 ORDER BY num_cia, fecha_pago ASC";
		$result = $db->query($sql);
		
		$tpl->newBlock("proveedor");
		$tpl->assign("num_proveedor", $_SESSION['pmp']['proveedores'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_proveedor", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		
		$num_cia = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				$num_cia = $result[$i]['num_cia'];
				
				$tpl->newBlock("block_cia");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
				$tpl->assign("clabe_cuenta", $result[$i][$clabe_cuenta]);
				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);
				
				$total = 0;
			}
			$tpl->newBlock("fac_cia");
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha_mov']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", $result[$i]['concepto']);
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ""));
			$tpl->assign("fimporte", number_format($result[$i]['importe'], 2, ".", ","));
			
			$total += $result[$i]['importe'];
			$tpl->assign("block_cia.total", number_format($total, 2, ".", ""));
			
			$tpl->assign("block_cia.fin", $i);
		}
		
		for ($i = 0; $i < count($_SESSION['pmp']['proveedores']); $i++) {
			$tpl->newBlock("pro");
			$tpl->assign("num_pro", $_SESSION['pmp']['proveedores'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}
	}
	else {
		$sql = "SELECT id, num_proveedor, nombre, $clabe_cuenta, fecha_mov, num_fact, descripcion AS concepto, total AS importe FROM pasivo_proveedores LEFT JOIN catalogo_proveedores USING (num_proveedor)";
		$sql .= " WHERE num_cia = {$_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]}";
		$sql .= $_SESSION['pmp']['num_proveedor'] > 0 ? " AND num_proveedor = {$_SESSION['pmp']['num_cia']}" : "";
		if (count($no_pago) > 0) {
			$sql .= " AND num_proveedor NOT IN (";
			for ($i = 0; $i < count($no_pago); $i++)
				$sql .= $no_pago[$i] . ($i < count($no_pago) - 1 ? "," : ")");
		}
		$sql .= " AND fecha_mov <= '{$_SESSION['pmp']['fecha_corte']}' AND total > 0 AND $clabe_cuenta IS NOT NULL ORDER BY num_proveedor, fecha_pago ASC";
		$result = $db->query($sql);
		
		$tpl->newBlock("compania");
		$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$_SESSION['pmp']['next']]);
		$tpl->assign("nombre_cia", $_SESSION['pmp']['nombres'][$_SESSION['pmp']['next']]);
		
		$num_pro = NULL;
		$block = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_pro != $result[$i]['num_proveedor']) {
				$num_pro = $result[$i]['num_proveedor'];
				
				$tpl->newBlock("block_pro");
				$tpl->assign("num_proveedor", $num_pro);
				$tpl->assign("nombre_proveedor", $result[$i]['nombre']);
				$tpl->assign("ini", $i);
				$current_block = $block++;
				$tpl->assign("block", $current_block);
				
				$total = 0;
			}
			$tpl->newBlock("fac_pro");
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("block", $current_block);
			$tpl->assign("fecha", $result[$i]['fecha_mov']);
			$tpl->assign("num_fact", $result[$i]['num_fact']);
			$tpl->assign("concepto", $result[$i]['concepto']);
			$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ""));
			$tpl->assign("fimporte", number_format($result[$i]['importe'], 2, ".", ","));
			
			$total += $result[$i]['importe'];
			$tpl->assign("block_pro.total", number_format($total, 2, ".", ""));
			
			$tpl->assign("block_pro.fin", $i);
		}
		
		for ($i = 0; $i < count($_SESSION['pmp']['compañias']); $i++) {
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $_SESSION['pmp']['compañias'][$i]);
			$tpl->assign("nombre", $_SESSION['pmp']['nombres'][$i]);
		}
	}
	
	$tpl->printToScreen();
	die;
}
?>