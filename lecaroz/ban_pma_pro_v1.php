<?php
// PAGO MANUAL A PROVEEDORES
// Tabla ''
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay facturas por pagar";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pma_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
//unset($_SESSION['fac']);

// Cancelar movimientos
if (isset($_POST['accion']) && $_POST['accion'] == "cancelar") {
	unset($_SESSION['fac']);
}

// Se termino con el análisis, destruir datos temporales
if (isset($_POST['accion']) && $_POST['accion'] == "terminar") {
	// Declaración de variables
	$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
	$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
	$monto_min    = 0;		// Monto mínimo de un cheque (por default 0)
	
	// Generar cheques
	if (isset($_POST['numfilas']) && $_POST['numfilas'] > 0) {
		// Obtener todos los id's para la consulta
		$count = 0;
		for ($i=0; $i<$_POST['numfilas']; $i++)
			if (isset($_POST['id'.$i])) {
				$id[$count] = $_POST['id'.$i];
				$count++;
			}
		
		// Si hay registros
		if ($count > 0) {
			$sql = "SELECT id,num_cia,num_fact,total AS importe,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE id IN (";
			for ($i=0; $i<$count; $i++) {
				$sql .= $id[$i];
				if ($i < $count-1)
					$sql .= ",";
			}
			$sql .= ") ORDER BY num_cia,num_proveedor,fecha_pago ASC,total DESC";
			
			// Obtener facturas
			$factura = ejecutar_script($sql,$dsn);
			
			$num_cia = NULL;		// Última compañía revisada
			$num_proveedor = NULL;	// Último proveedor revisado
			$num_fac = 0;			// Número de facturas actual para el cheque
			//$importe_cheque = 0;	// Importe del cheque
			
			$fac_count = 0;			// Contador de facturas
			$che_count = 0;			// Contador de cheques
			
			for ($i=0; $i<count($factura); $i++) {
				// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
				if ($factura[$i]['num_cia'] != $num_cia || $factura[$i]['num_proveedor'] != $num_proveedor || $num_fac == $fac_x_cheque) {
					// Organizar datos para almacenar cheque
					if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
						// Si el importe del cheque es mayor o igual al monto minimo, generarlo
						if ($importe_cheque >= $monto_min) {
							// Datos para la tabla de 'cheques'
							$cheque['cod_mov'.$che_count]           = 5;						// PENDIENTE...
							$cheque['num_proveedor'.$che_count]     = $num_proveedor;
							$cheque['num_cia'.$che_count]           = $num_cia;
							$cheque['a_nombre'.$che_count]          = $nombre_proveedor;
							$cheque['concepto'.$che_count]          = "";						// El concepto de cheque son todas facturas que son pagadas con el mismo
							$cheque['facturas'.$che_count]          = $facturas;
							$cheque['fecha'.$che_count]             = $_SESSION['fac']['fecha_corte'];
							$cheque['folio'.$che_count]             = $folio_cheque;
							$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
							$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
							$cheque['imp'.$che_count]               = "FALSE";
							$cheque['num_cheque'.$che_count]        = "";
							$cheque['fecha_cancelacion'.$che_count] = "";
							$cheque['codgastos'.$che_count] = 33;
							
							// Datos para la tabla de 'estado_cuenta'
							$cuenta['num_cia'.$che_count] = $num_cia;
							$cuenta['fecha'.$che_count] = $_SESSION['fac']['fecha_corte'];
							$cuenta['fecha_con'.$che_count] = "";
							$cuenta['concepto'.$che_count] = $facturas;
							$cuenta['tipo_mov'.$che_count] = "TRUE";			// Cargo
							$cuenta['importe'.$che_count]  = number_format($importe_cheque,2,".","");
							$cuenta['saldo_ini'.$che_count] = 0;
							$cuenta['saldo_fin'.$che_count] = 0;
							$cuenta['cod_mov'.$che_count] = 5;
							$cuenta['folio'.$che_count] = $folio_cheque;
							
							// Datos para la tabla de folios_cheque
							$folio['folio'.$che_count]     = $folio_cheque;
							$folio['num_cia'.$che_count]   = $num_cia;
							$folio['reservado'.$che_count] = "FALSE";
							
							// Almacenar datos en la tabla de 'facturas_pagadas'
							$db = new DBclass($dsn,"facturas_pagadas",$fac_pag);
							$db->xinsertar();
							unset($db);
							
							// Borrar facturas pagadas de pasivo y para el caso de las facturas de pollos, actualizar la bandera de pagado
							for ($f=0; $f<$fac_count; $f++) {
								// Borrar de pasivo
								ejecutar_script("DELETE FROM pasivo_proveedores WHERE id = ".$fac_del['id'.$f],$dsn);
								// Actualizar facturas de rosticerias
								ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
							}
							
							// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
							ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
							
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
						$sql = "SELECT folio FROM folios_cheque WHERE num_cia = $num_cia ORDER BY folio DESC LIMIT 1";
						$result = ejecutar_script($sql,$dsn);
						$folio_cheque = ($result)?$result[0]['folio']+1:1;
					}
					
					// Cambiar proveedor
					$num_proveedor    = $factura[$i]['num_proveedor'];
					$nombre_proveedor = $factura[$i]['nombre'];
					
					// Si el proveedor es el 13 (Pollos Guerra) cambiar el número de facturas por cheque a 15, si no, regresarlo a 10
					if ($num_proveedor == 13)
						$fac_x_cheque = 15;
					else
						$fac_x_cheque = 10;
					
					// Vaciar variable que almacena facturas
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
				// Si el saldo de la cuenta es mayor o igual al importe de la factura, sumar el total de este al importe del cheque
				// Se considera el caso de el máximo número de facturas pagadas por un cheque
				if ($num_fac < $fac_x_cheque) {
					// Sumar importe de factura al importe del cheque
					$importe_cheque = number_format($importe_cheque,2,".","") + number_format($factura[$i]['importe'],2,".","");
					
					// Factura a borrar de pasivo
					$fac_del['id'.$fac_count] = $factura[$i]['id'];
					
					// Organizar datos para almacenar factura en la tabla de facturas_pagadas
					$fac_pag['num_cia'.$fac_count]       = $num_cia;
					$fac_pag['num_proveedor'.$fac_count] = $num_proveedor;
					$fac_pag['num_fact'.$fac_count]      = $factura[$i]['num_fact'];
					$fac_pag['codgastos'.$fac_count]     = $factura[$i]['codgastos'];	// ESTE CAMPO FALTA AGREGARLO A LA TABLA DE 'pasivo_proveedores'
					$fac_pag['total'.$fac_count]         = $factura[$i]['importe'];
					$fac_pag['descripcion'.$fac_count]   = $factura[$i]['descripcion'];
					$fac_pag['fecha_mov'.$fac_count]     = $factura[$i]['fecha_mov'];
					$fac_pag['fecha_pago'.$fac_count]    = $factura[$i]['fecha_pago'];
					$fac_pag['fecha_cheque'.$fac_count]  = $_SESSION['fac']['fecha_corte'];
					$fac_pag['folio_cheque'.$fac_count]  = $folio_cheque;
					
					// Agregar el número de factura al concepto del cheque
					if ($fac_count > 0)
						$facturas .= " ";
					$facturas .= fillZero($factura[$i]['num_fact'],7);
					
					// Incrementar contador de facturas
					$fac_count++;
					// Incrementar contador de facturas por cheque
					$num_fac++;
				}
			}
			if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
				// Si el importe del cheque es mayor o igual al monto minimo, generarlo
				if ($importe_cheque >= $monto_min) {
					// Datos para la tabla de 'cheques'
					$cheque['cod_mov'.$che_count]           = 5;						// PENDIENTE...
					$cheque['num_proveedor'.$che_count]     = $num_proveedor;
					$cheque['num_cia'.$che_count]           = $num_cia;
					$cheque['a_nombre'.$che_count]          = $nombre_proveedor;
					$cheque['concepto'.$che_count]          = "";						// El concepto de cheque son todas facturas que son pagadas con el mismo
					$cheque['facturas'.$che_count]          = $facturas;
					$cheque['fecha'.$che_count]             = $_SESSION['fac']['fecha_corte'];
					$cheque['folio'.$che_count]             = $folio_cheque;
					$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
					$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
					$cheque['imp'.$che_count]               = "FALSE";
					$cheque['num_cheque'.$che_count]        = "";
					$cheque['fecha_cancelacion'.$che_count] = "";
					$cheque['codgastos'.$che_count] = 33;
					
					// Datos para la tabla de 'estado_cuenta'
					$cuenta['num_cia'.$che_count] = $num_cia;
					$cuenta['fecha'.$che_count] = $_SESSION['fac']['fecha_corte'];
					$cuenta['fecha_con'.$che_count] = "";
					$cuenta['concepto'.$che_count] = $facturas;
					$cuenta['tipo_mov'.$che_count] = "TRUE";			// Cargo
					$cuenta['importe'.$che_count]  = number_format($importe_cheque,2,".","");
					$cuenta['saldo_ini'.$che_count] = 0;
					$cuenta['saldo_fin'.$che_count] = 0;
					$cuenta['cod_mov'.$che_count] = 5;
					$cuenta['folio'.$che_count] = $folio_cheque;
					
					// Datos para la tabla de folios_cheque
					$folio['folio'.$che_count]     = $folio_cheque;
					$folio['num_cia'.$che_count]   = $num_cia;
					$folio['reservado'.$che_count] = "FALSE";
					
					// Almacenar datos en la tabla de 'facturas_pagadas'
					$db = new DBclass($dsn,"facturas_pagadas",$fac_pag);
					$db->xinsertar();
					unset($db);
					
					// Borrar facturas pagadas de pasivo y para el caso de las facturas de pollos, actualizar la bandera de pagado
					for ($f=0; $f<$fac_count; $f++) {
						// Borrar de pasivo
						ejecutar_script("DELETE FROM pasivo_proveedores WHERE id = ".$fac_del['id'.$f],$dsn);
						// Actualizar facturas de rosticerias
						ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
					}
					
					// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
					ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
					
					// Imcrementar número de folio
					$folio_cheque++;
					// Incrementar contador de cheques
					$che_count++;
				}
			}
			
			// Almacenar datos en la tabla de 'cheques'
			if ($che_count > 0) {
				$db = new DBclass($dsn,"cheques",$cheque);
				$db->xinsertar();
				unset($db);
				// Almacenar datos en la tabla de 'folios_cheque'
				$db = new DBclass($dsn,"folios_cheque",$folio);
				$db->xinsertar();
				unset($db);
				// Almacenar datos en la tabla de 'estado_cuenta'
				$db = new DBclass($dsn,"estado_cuenta",$cuenta);
				$db->xinsertar();
				unset($db);
			}
		}
	}

	unset($_SESSION['fac']);
}

// Pedir datos de inicio para la pantalla en cuestión si no existen
if (!isset($_SESSION['fac']) && !isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha_corte",date("d/m/Y"));
	
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

if (isset($_GET['tipo']) && !isset($_SESSION['fac'])) {
	$_SESSION['fac']['tipo'] = $_GET['tipo'];
	$_SESSION['fac']['fecha_corte'] = $_GET['fecha_corte'];
	
	// Si las facturas serán de una sola compañía, almacenar el número de compañía
	if (isset($_GET['num_cia']))
		$_SESSION['fac']['num_cia'] = $_GET['num_cia'];
	
	// Obtener proveedores de pasivo
	$sql = "SELECT DISTINCT ON (num_proveedor) num_proveedor,nombre AS nombre_proveedor FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE";
	if ($_SESSION['fac']['tipo'] == "cia")
		$sql .= " num_cia = ".$_SESSION['fac']['num_cia']." AND";
	$sql .= " fecha_pago <= '".$_SESSION['fac']['fecha_corte']."' ORDER BY num_proveedor ASC";
	
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./ban_pma_pro.php?terminar=1&codigo_error=1");
		die;
	}
	
	for ($i=0; $i<count($result); $i++) {
		$_SESSION['fac'][$i] = $result[$i]['num_proveedor'];
		$_SESSION['fac']['nombre_proveedor'.$i] = $result[$i]['nombre_proveedor'];
	}
	
	$_SESSION['fac']['num_proveedores'] = count($result);
	if ($key = array_search($_GET['num_proveedor'],$_SESSION['fac']))
		$next = $key;
	else
		$next = 0;
	
	$_SESSION['fac']['next'] = $next;
	$_SESSION['fac']['back'] = $next;
}

if (isset($_SESSION['fac'])) {
// ***************************************** Generar cheques y pasar al siguiente proveedor ******************************************************
	if (isset($_POST['accion']) && $_POST['accion'] == "siguiente") {
		// Declaración de variables
		$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
		$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
		$monto_min    = 0;		// Monto mínimo de un cheque (por default 0)
		
		// Generar cheques
		if (isset($_POST['numfilas']) && $_POST['numfilas'] > 0) {
			// Obtener todos los id's para la consulta
			$count = 0;
			for ($i=0; $i<$_POST['numfilas']; $i++)
				if (isset($_POST['id'.$i])) {
					$id[$count] = $_POST['id'.$i];
					$count++;
				}
			
			// Si hay registros
			if ($count > 0) {
				$sql = "SELECT id,num_cia,num_fact,total AS importe,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE id IN (";
				for ($i=0; $i<$count; $i++) {
					$sql .= $id[$i];
					if ($i < $count-1)
						$sql .= ",";
				}
				$sql .= ") ORDER BY num_cia,num_proveedor,fecha_pago ASC,total DESC";
				
				// Obtener facturas
				$factura = ejecutar_script($sql,$dsn);
				
				$num_cia = NULL;		// Última compañía revisada
				$num_proveedor = NULL;	// Último proveedor revisado
				$num_fac = 0;			// Número de facturas actual para el cheque
				$importe_cheque = 0;	// Importe del cheque
				
				$fac_count = 0;			// Contador de facturas
				$che_count = 0;			// Contador de cheques
				
				for ($i=0; $i<count($factura); $i++) {
					// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
					if ($factura[$i]['num_cia'] != $num_cia || $factura[$i]['num_proveedor'] != $num_proveedor || $num_fac == $fac_x_cheque) {
						// Organizar datos para almacenar cheque
						if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
							// Si el importe del cheque es mayor o igual al monto minimo, generarlo
							if ($importe_cheque >= $monto_min) {
								// Datos para la tabla de 'cheques'
								$cheque['cod_mov'.$che_count]           = 5;						// PENDIENTE...
								$cheque['num_proveedor'.$che_count]     = $num_proveedor;
								$cheque['num_cia'.$che_count]           = $num_cia;
								$cheque['a_nombre'.$che_count]          = $nombre_proveedor;
								$cheque['concepto'.$che_count]          = "";						// El concepto de cheque son todas facturas que son pagadas con el mismo
								$cheque['facturas'.$che_count]          = $facturas;
								$cheque['fecha'.$che_count]             = $_SESSION['fac']['fecha_corte'];
								$cheque['folio'.$che_count]             = $folio_cheque;
								$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
								$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
								$cheque['imp'.$che_count]               = "FALSE";
								$cheque['num_cheque'.$che_count]        = "";
								$cheque['fecha_cancelacion'.$che_count] = "";
								$cheque['codgastos'.$che_count] = 33;
								
								// Datos para la tabla de 'estado_cuenta'
								$cuenta['num_cia'.$che_count] = $num_cia;
								$cuenta['fecha'.$che_count] = date("d/m/Y");
								$cuenta['fecha_con'.$che_count] = "";
								$cuenta['concepto'.$che_count] = $facturas;
								$cuenta['tipo_mov'.$che_count] = "TRUE";			// Cargo
								$cuenta['importe'.$che_count]  = $importe_cheque;
								$cuenta['saldo_ini'.$che_count] = 0;
								$cuenta['saldo_fin'.$che_count] = 0;
								$cuenta['cod_mov'.$che_count] = 5;
								$cuenta['folio'.$che_count] = $folio_cheque;
								
								// Datos para la tabla de folios_cheque
								$folio['folio'.$che_count]     = $folio_cheque;
								$folio['num_cia'.$che_count]   = $num_cia;
								$folio['reservado'.$che_count] = "FALSE";
								
								// Almacenar datos en la tabla de 'facturas_pagadas'
								$db = new DBclass($dsn,"facturas_pagadas",$fac_pag);
								$db->xinsertar();
								unset($db);
								
								// Borrar facturas pagadas de pasivo y para el caso de las facturas de pollos, actualizar la bandera de pagado
								for ($f=0; $f<$fac_count; $f++) {
									// Borrar de pasivo
									ejecutar_script("DELETE FROM pasivo_proveedores WHERE id = ".$fac_del['id'.$f],$dsn);
									// Actualizar facturas de rosticerias
									ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
								}
								
								// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
								ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
								
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
							$sql = "SELECT folio FROM folios_cheque WHERE num_cia = $num_cia ORDER BY folio DESC LIMIT 1";
							$result = ejecutar_script($sql,$dsn);
							$folio_cheque = ($result)?$result[0]['folio']+1:1;
						}
						
						// Cambiar proveedor
						$num_proveedor    = $factura[$i]['num_proveedor'];
						$nombre_proveedor = $factura[$i]['nombre'];
						
						// Si el proveedor es el 13 (Pollos Guerra) cambiar el número de facturas por cheque a 15, si no, regresarlo a 10
						if ($num_proveedor == 13)
							$fac_x_cheque = 15;
						else
							$fac_x_cheque = 10;
						
						// Vaciar variable que almacena facturas
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
					// Si el saldo de la cuenta es mayor o igual al importe de la factura, sumar el total de este al importe del cheque
					// Se considera el caso de el máximo número de facturas pagadas por un cheque
					if ($num_fac < $fac_x_cheque) {
						// Sumar importe de factura al importe del cheque
						$importe_cheque = number_format($importe_cheque,2,".","") + number_format($factura[$i]['importe'],2,".","");
						
						// Factura a borrar de pasivo
						$fac_del['id'.$fac_count] = $factura[$i]['id'];
						
						// Organizar datos para almacenar factura en la tabla de facturas_pagadas
						$fac_pag['num_cia'.$fac_count]       = $num_cia;
						$fac_pag['num_proveedor'.$fac_count] = $num_proveedor;
						$fac_pag['num_fact'.$fac_count]      = $factura[$i]['num_fact'];
						$fac_pag['codgastos'.$fac_count]     = $factura[$i]['codgastos'];	// ESTE CAMPO FALTA AGREGARLO A LA TABLA DE 'pasivo_proveedores'
						$fac_pag['total'.$fac_count]         = $factura[$i]['importe'];
						$fac_pag['descripcion'.$fac_count]   = $factura[$i]['descripcion'];
						$fac_pag['fecha_mov'.$fac_count]     = $factura[$i]['fecha_mov'];
						$fac_pag['fecha_pago'.$fac_count]    = $factura[$i]['fecha_pago'];
						$fac_pag['fecha_cheque'.$fac_count]  = $_SESSION['fac']['fecha_corte'];
						$fac_pag['folio_cheque'.$fac_count]  = $folio_cheque;
						
						// Agregar el número de factura al concepto del cheque
						if ($fac_count > 0)
							$facturas .= " ";
						$facturas .= fillZero($factura[$i]['num_fact'],7);
						
						// Incrementar contador de facturas
						$fac_count++;
						// Incrementar contador de facturas por cheque
						$num_fac++;
					}
				}
				if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
					// Si el importe del cheque es mayor o igual al monto minimo, generarlo
					if ($importe_cheque >= $monto_min) {
						// Datos para la tabla de 'cheques'
						$cheque['cod_mov'.$che_count]           = 5;						// PENDIENTE...
						$cheque['num_proveedor'.$che_count]     = $num_proveedor;
						$cheque['num_cia'.$che_count]           = $num_cia;
						$cheque['a_nombre'.$che_count]          = $nombre_proveedor;
						$cheque['concepto'.$che_count]          = "";						// El concepto de cheque son todas facturas que son pagadas con el mismo
						$cheque['facturas'.$che_count]          = $facturas;
						$cheque['fecha'.$che_count]             = $_SESSION['fac']['fecha_corte'];
						$cheque['folio'.$che_count]             = $folio_cheque;
						$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
						$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
						$cheque['imp'.$che_count]               = "FALSE";
						$cheque['num_cheque'.$che_count]        = "";
						$cheque['fecha_cancelacion'.$che_count] = "";
						$cheque['codgastos'.$che_count] = 33;
						
						// Datos para la tabla de 'estado_cuenta'
						$cuenta['num_cia'.$che_count] = $num_cia;
						$cuenta['fecha'.$che_count] = date("d/m/Y");
						$cuenta['fecha_con'.$che_count] = "";
						$cuenta['concepto'.$che_count] = $facturas;
						$cuenta['tipo_mov'.$che_count] = "TRUE";			// Cargo
						$cuenta['importe'.$che_count]  = $importe_cheque;
						$cuenta['saldo_ini'.$che_count] = 0;
						$cuenta['saldo_fin'.$che_count] = 0;
						$cuenta['cod_mov'.$che_count] = 5;
						$cuenta['folio'.$che_count] = $folio_cheque;
						
						// Datos para la tabla de folios_cheque
						$folio['folio'.$che_count]     = $folio_cheque;
						$folio['num_cia'.$che_count]   = $num_cia;
						$folio['reservado'.$che_count] = "FALSE";
						
						// Almacenar datos en la tabla de 'facturas_pagadas'
						$db = new DBclass($dsn,"facturas_pagadas",$fac_pag);
						$db->xinsertar();
						unset($db);
						
						// Borrar facturas pagadas de pasivo y para el caso de las facturas de pollos, actualizar la bandera de pagado
						for ($f=0; $f<$fac_count; $f++) {
							// Borrar de pasivo
							ejecutar_script("DELETE FROM pasivo_proveedores WHERE id = ".$fac_del['id'.$f],$dsn);
							// Actualizar facturas de rosticerias
							ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
						}
						
						// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
						ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
						
						// Imcrementar número de folio
						$folio_cheque++;
						// Incrementar contador de cheques
						$che_count++;
					}
				}
				
				// Almacenar datos en la tabla de 'cheques'
				if ($che_count > 0) {
					$db = new DBclass($dsn,"cheques",$cheque);
					$db->xinsertar();
					unset($db);
					// Almacenar datos en la tabla de 'folios_cheque'
					$db = new DBclass($dsn,"folios_cheque",$folio);
					$db->xinsertar();
					unset($db);
					// Almacenar datos en la tabla de 'estado_cuenta'
					$db = new DBclass($dsn,"estado_cuenta",$cuenta);
					$db->xinsertar();
					unset($db);
				}
			}
		}
		
		// Cambiar proveedor
		if ($_POST['num_proveedor'] != "") {
			if ($key = array_search($_POST['num_proveedor'],$_SESSION['fac']))
				$next = $key;
			else
				$next = 0;
			
			$_SESSION['fac']['next'] = $next;
		}
		else
			if ($_SESSION['fac']['next'] < $_SESSION['fac']['num_proveedores'] - 1)
				$_SESSION['fac']['next'] = $_SESSION['fac']['next'] + 1;
			else
				$_SESSION['fac']['next'] = 0;
	}
// *************************************************************************************************************************************************
	
	// Construir script sql
	$sql  = "SELECT id,num_cia,fecha_mov,num_fact,descripcion AS concepto,total AS importe FROM pasivo_proveedores";
	$sql .= " WHERE num_proveedor = ".$_SESSION['fac'][$_SESSION['fac']['next']];
	if ($_SESSION['fac']['tipo'] == "cia")
		$sql .= " AND num_cia = ".$_SESSION['fac']['num_cia'];
	/******************** PROVISIONAL, NO TOMAR EN CUENTA LAS FACTURAS DE PANADERIAS **************************/
	else
		$sql .= "AND num_cia > 100";
	$sql .= " AND fecha_pago <= '".$_SESSION['fac']['fecha_corte']."' ORDER BY num_cia,fecha_pago ASC";
	// Obtener facturas
	$fac = ejecutar_script($sql,$dsn);
	
	if ($fac) {
		$tpl->newBlock("facturas");
		$tpl->assign("num_proveedor",$_SESSION['fac'][$_SESSION['fac']['next']]);
		$tpl->assign("nombre_proveedor",$_SESSION['fac']['nombre_proveedor'.$_SESSION['fac']['next']]);
		$tpl->assign("numfilas",count($fac));
		
		$num_cia = NULL;
		for ($i=0; $i<count($fac); $i++) {
			if ($num_cia != $fac[$i]['num_cia']) {
				if ($num_cia != NULL)
					$tpl->assign("cia.total",number_format($total,2,".",","));
				
				$num_cia = $fac[$i]['num_cia'];
				
				$tpl->newBlock("cia");
				$cia = ejecutar_script("SELECT nombre,clabe_cuenta AS cuenta FROM catalogo_companias WHERE num_cia = ".$fac[$i]['num_cia'],$dsn);
				$tpl->assign("num_cia",$fac[$i]['num_cia']);
				$tpl->assign("cuenta",$cia[0]['cuenta']);
				$tpl->assign("nombre_cia",$cia[0]['nombre']);
				
				$total = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("i",$i);
			$tpl->assign("id",$fac[$i]['id']);
			$tpl->assign("fecha",$fac[$i]['fecha_mov']);
			$tpl->assign("num_fact",$fac[$i]['num_fact']);
			$tpl->assign("concepto",$fac[$i]['concepto']);
			$tpl->assign("importe",number_format($fac[$i]['importe'],2,".",","));
			
			$total += $fac[$i]['importe'];
		}
	}
	if ($num_cia != NULL)
		$tpl->assign("cia.total",number_format($total,2,".",","));
	
	$tpl->gotoBlock("facturas");
	
	// Generar listado de proveedores
	for ($i=0; $i<$_SESSION['fac']['num_proveedores']; $i++) {
		$tpl->newBlock("nombre_proveedor_ini");
		$tpl->assign("num_proveedor",$_SESSION['fac'][$i]);
		$tpl->assign("nombre_proveedor",$_SESSION['fac']['nombre_proveedor'.$i]);
	}
	
	$tpl->printToScreen();
}
?>