<?php
// PAGO AUTOMÁTICO A PROVEEDORES VERSION 2

include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$session = new sessionclass($dsn);
//if ($_SESSION['iduser'] != 1) die("EN REPARACION... GOMEN  ^_^");
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pau_pro_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['prelistado']) && !isset($_POST['generar'])) {
	// Destruir variable SESSION
	unset($_SESSION['pau']);
	
	$tpl->newBlock("datos");
	
	//$tpl->assign("fecha_pago",date("d/m/Y"));
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
	die();
}

if (isset($_POST['prelistado'])) {
	$_SESSION['pau'] = $_POST;
	
	$fecha_corte   = $_POST['fecha_corte'];		// Fecha de corte
	
	$fecha_actual  = date("d/m/Y");				// Fecha actual
	$dia_actual    = date("d");					// Dia actual
	$mes_actual    = date("n");					// Mes actual
	$anio_actual   = date("Y");					// Año actual
	
	// Conectar a la base de datos
	$db = new DBclass($dsn);
	
	$clabe_cuenta = $_POST['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	// Obtener listado de compañías y saldos, segun opciones
	$sql = "SELECT \"num_cia\",\"nombre\",\"nombre_corto\",\"$clabe_cuenta\",\"saldo_libros\" FROM \"catalogo_companias\"  LEFT JOIN \"saldos\" USING(\"num_cia\") WHERE $clabe_cuenta IS NOT NULL ";
	$sql .= " AND cuenta = $_POST[cuenta]";
	if ($_SESSION['pau']['rango'] == "panaderias")
		$sql .= "AND \"num_cia\" < 100 ";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= "AND (\"num_cia\" BETWEEN 100 AND 200) OR (\"num_cia\" BETWEEN 702 AND 799) ";
	$sql .= "ORDER BY \"num_cia\" ASC";
	$cia = $db->query($sql);
	
	$dias_deposito = $_SESSION['pau']['dias_deposito'];	// Días de depósito
	
	$total_saldo_libros = 0;					// Suma total de los saldos ne libros
	$total_promedio     = 0;					// Suma total de los promedios
	$total_saldo_pago   = 0;					// Suma total de los saldos para pago
	
	// Recorrer las compañías y calcular saldos
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] < 200 || ($cia[$i]['num_cia'] > 701 || $cia[$i]['num_cia'] < 800)) {
			// Obtener última fecha de efectivos para la compañía (dependiendo de si es panadería o rosticería)
			$sql = "SELECT \"fecha\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND \"cuenta\" = $_POST[cuenta] AND \"cod_mov\" IN (1,16) ORDER BY \"fecha\" DESC LIMIT 1";
			$result = $db->query($sql);
			// Si tiene depósitos, calcular el pronostico de saldo
			if ($result) {
				$ultimo_fecha_depositos = ($result) ? $result[0]['fecha'] : date("d/m/Y");		// Ultima fecha de efectivo
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[0]['fecha'],$temp);
				$ultimo_dia_depositos  = $temp[1];
				$ultimo_mes_depositos  = $temp[2];
				$ultimo_anio_depositos = $temp[3];
				
				// Si el último efectivo capturado esta dentro del rango del mes, calcular promedios
				if ($ultimo_mes_depositos == $mes_actual && $ultimo_dia_depositos > 5) {
					$sql = "SELECT AVG(\"importe\") AS \"promedio\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND \"cuenta\" = $_POST[cuenta] AND \"cod_mov\" IN (1,16) AND \"fecha\" BETWEEN '1/$mes_actual/$anio_actual' AND '$fecha_actual'";
					$result = $db->query($sql);
					$promedio = ($result) ? $result[0]['promedio'] : 0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
				// Si el último efectivo capturado no esta dentro del rango del mes, calcular promedio del mes anterior
				else {
					$ultimo_dia_mes_anterior = date("d",mktime(0,0,0,$ultimo_mes_depositos+1,0,$ultimo_anio_depositos));
					// Calcular los días de diferencia (ultimo dia del mes - ultimo dia de efectivo + dia actual)
					$dias_dif = $ultimo_dia_mes_anterior - $ultimo_dia_depositos + $dia_actual;
					
					// Obtener depositos y calcular promedios
					$sql = "SELECT AVG(\"importe\") AS \"promedio\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND \"cuenta\" = $_POST[cuenta] AND \"cod_mov\" IN (1,16) AND \"fecha\" BETWEEN '1/$ultimo_mes_depositos/$ultimo_anio_depositos' AND '$ultimo_dia_mes_anterior/$ultimo_mes_depositos/$ultimo_anio_depositos'";
					$result = $db->query($sql);
					$promedio = ($result) ? $result[0]['promedio'] : 0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
			}
			// Si no tiene efectivos, el saldo para la compañia es cero
			else {
				$promedio = 0;
				$saldo[(int)$cia[$i]['num_cia']] = 0;
			}
		}
		else {
			$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'];
		}
	}
	
	// Contar el número de proveedores sin pago
	$sinpago_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_SESSION['pau']['sin_pago'][$i] > 0) {
			$sin_pago[$sinpago_count] = $_SESSION['pau']['sin_pago'][$i];
			$sinpago_count++;
		}
	
	// Contar el número de compañías ke no pagaran
	$nopagan_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_SESSION['pau']['no_pagan'][$i] > 0) {
			$no_pagan[$nopagan_count] = $_SESSION['pau']['no_pagan'][$i];
			$nopagan_count++;
		}
	
	$sql  = "SELECT \"id\",\"num_cia\",\"num_fact\",\"total\",\"descripcion\",\"fecha_mov\",\"fecha_pago\",\"pasivo_proveedores\".\"num_proveedor\" AS \"num_proveedor\",\"catalogo_proveedores\".\"nombre\" AS \"nombre\",\"codgastos\" FROM \"pasivo_proveedores\"";
	$sql .= " LEFT JOIN \"catalogo_proveedores\" USING(\"num_proveedor\") LEFT JOIN \"catalogo_companias\" USING (\"num_cia\") WHERE \"catalogo_companias\".\"$clabe_cuenta\" IS NOT NULL AND \"fecha_pago\" <= '$fecha_corte'";
	// Añade condición de proveedores sin pago a script sql
	if ($sinpago_count > 0) {
		$sql .= " AND \"pasivo_proveedores\".\"num_proveedor\" NOT IN (";
		for ($i=0; $i<$sinpago_count; $i++)
			$sql .= $sin_pago[$i] . ($i < $sinpago_count-1 ? "," : ")");
	}
	// Añade condición de compañías que no pagaran facturas a script sql
	if ($nopagan_count > 0) {
		$sql .= " AND \"num_cia\" NOT IN (";
		for ($i=0; $i<$nopagan_count; $i++)
			$sql .= $no_pagan[$i] . ($i < $nopagan_count-1 ? "," : ")");
	}
	// Añade condicion de rango de compañías a script sql
	if ($_SESSION['pau']['rango'] == "panaderias")
		$sql .= " AND \"num_cia\" < 100";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= " AND (\"num_cia\" BETWEEN 101 AND 199) OR (\"num_cia\" BETWEEN 702 AND 799)";
	// Añade condición de fecha de pago y fecha de corte
	$sql .= " ORDER BY \"num_cia\" ASC,";
	if ($_SESSION['pau']['criterio'] == "prioridad")
		$sql .= "\"prioridad\" DESC,";
	$sql .= "\"fecha_pago\" ASC,\"total\" DESC";
	
	// Obtener facturas
	$factura = $db->query($sql);
	
	// Si no hay resultados, regresar a la pantalla de inicio
	if (!$factura) {
		$db->desconectar();
		header("location: ./ban_pau_pro.php?codigo_error=1");
		die;
	}
	
	// Generar listado
	$tpl->newBlock("pre_listado");
	$tpl->assign("num_facturas",count($factura));
	
	$num_cia = NULL;
	$count = 0;
	for ($i=0; $i<count($factura); $i++) {
		if ($num_cia != $factura[$i]['num_cia']) {
			$num_cia = $factura[$i]['num_cia'];
			
			$tpl->newBlock("bloque_cia");
			$tpl->assign("num_cia",$factura[$i]['num_cia']);
			$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("saldo", isset($saldo[$num_cia]) ? number_format($saldo[$num_cia],2,".",",") : "&nbsp;");
			$tpl->assign("ini",$count);
			
			$total = 0;
			$omitir = FALSE;
			$obligado = array_search($num_cia,$_SESSION['pau']['obligado']) !== FALSE ? TRUE : FALSE;
		}
		if (!$omitir) {
			if ($obligado) {
				$tpl->newBlock("fila_pre");
				$tpl->assign("i",$i);
				$tpl->assign("id",$factura[$i]['id']);
				$tpl->assign("num_proveedor",$factura[$i]['num_proveedor']);
				$tpl->assign("nombre_proveedor",$factura[$i]['nombre']);
				$tpl->assign("fecha",$factura[$i]['fecha_pago']);
				$tpl->assign("num_fact",$factura[$i]['num_fact']);
				$tpl->assign("concepto",$factura[$i]['descripcion']);
				$tpl->assign("importe",number_format($factura[$i]['total'],2,".",","));
				
				$total += $factura[$i]['total'];
				$saldo[$num_cia] -= $factura[$i]['total'];
				
				$tpl->assign("bloque_cia.total",number_format($total,2,".",","));
				$tpl->assign("bloque_cia.fin",$count);
				$count++;
			}
			else if (isset($saldo[$num_cia]) && $saldo[$num_cia] - $factura[$i]['total'] > 0) {
				$tpl->newBlock("fila_pre");
				$tpl->assign("i",$i);
				$tpl->assign("id",$factura[$i]['id']);
				$tpl->assign("num_proveedor",$factura[$i]['num_proveedor']);
				$tpl->assign("nombre_proveedor",$factura[$i]['nombre']);
				$tpl->assign("fecha",$factura[$i]['fecha_pago']);
				$tpl->assign("num_fact",$factura[$i]['num_fact']);
				$tpl->assign("concepto",$factura[$i]['descripcion']);
				$tpl->assign("importe",number_format($factura[$i]['total'],2,".",","));
				
				$total += $factura[$i]['total'];
				$saldo[$num_cia] -= $factura[$i]['total'];
				
				$tpl->assign("bloque_cia.total",number_format($total,2,".",","));
				$tpl->assign("bloque_cia.fin",$count);
				$count++;
			}
			else
				$omitir = TRUE;
		}
	}
	
	$db->desconectar();
	$tpl->printToScreen();
	die;
}
// Generar cheques
if (isset($_POST['generar'])) {
	// +----------------------------------------------------------------------------------------------------------+
	// | Función principal que realiza el proceso de pago a proveedores.                                          |
	// +----------------------------------------------------------------------------------------------------------+
	function pago_proveedores($factura, $obligado = FALSE) {
		$query = "";			// Variable para lamacenar todos los querys de pago a proveedores
		
		// Declaración de variables
		$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
		$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
		$monto_min    = $GLOBALS['dia_actual'] < 22 ? 1000 : 500;	// Monto mínimo de un cheque (500 para los últimos días del mes, 1000 para los demas)
		
		$num_cia = NULL;		// Última compañía revisada
		$num_proveedor = NULL;	// Último proveedor revisado
		$num_fac = 0;			// Número de facturas actual para el cheque
		$importe_cheque = 0;	// Importe del cheque
		
		$fac_count = 0;			// Contador de facturas
		$che_count = 0;			// Contador de cheques
		
		$cosgastos = NULL;		// Código de gasto para el cheque
		$nombre_gasto = NULL;	// Nombre del gasto para el cheque
		
		// Número máximo de cheques que se van a generar (agregado el 15 de febrero del 2006)
		$max_cheques = 10000;
		
		for ($i=0; $i<count($factura); $i++) {
			// Verificar el cambio de compañía o de proveedor o máximo número de facturas para un cheque
			if ($factura[$i]['num_cia'] != $num_cia || $factura[$i]['num_proveedor'] != $num_proveedor || $num_fac == $fac_x_cheque) {
				// Organizar datos para almacenar cheque
				if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
					// Si el importe del cheque es mayor o igual al monto minimo, generarlo
					if ($importe_cheque >= $monto_min /*(15 de febrero del 2006) Número máximo de cheques*/&& $che_count < $max_cheques /*Fin cambio*/) {
						// Datos para la tabla de 'cheques'
						$cheque[$che_count]['cod_mov']           = 5;
						$cheque[$che_count]['num_proveedor']     = $num_proveedor;
						$cheque[$che_count]['num_cia']           = $num_cia;
						$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
						$cheque[$che_count]['concepto']          = $nombre_gasto;
						$cheque[$che_count]['facturas']          = $facturas;
						$cheque[$che_count]['fecha']             = $GLOBALS['fecha_actual'];
						$cheque[$che_count]['folio']             = $folio_cheque;
						$cheque[$che_count]['importe']           = number_format($importe_cheque,2,".","");
						$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
						$cheque[$che_count]['imp']               = "FALSE";
						$cheque[$che_count]['codgastos']         = $codgastos;
						$cheque[$che_count]['proceso']           = "TRUE";
						$cheque[$che_count]['cuenta']            = $_SESSION['pau']['cuenta'];
						
						// Datos para la tabla de 'estado_cuenta'
						$cuenta[$che_count]['num_cia'] = $num_cia;
						$cuenta[$che_count]['fecha'] = $GLOBALS['fecha_actual'];
						$cuenta[$che_count]['concepto'] = $facturas;
						$cuenta[$che_count]['tipo_mov'] = "TRUE";
						$cuenta[$che_count]['importe']  = number_format($importe_cheque,2,".","");
						$cuenta[$che_count]['cod_mov'] = 5;
						$cuenta[$che_count]['folio'] = $folio_cheque;
						$cuenta[$che_count]['cuenta'] = $_SESSION['pau']['cuenta'];
						
						// Datos para la tabla de folios_cheque
						$folio[$che_count]['folio']     = $folio_cheque;
						$folio[$che_count]['num_cia']   = $num_cia;
						$folio[$che_count]['reservado'] = "FALSE";
						$folio[$che_count]['utilizado'] = "TRUE";
						$folio[$che_count]['fecha'] = $GLOBALS['fecha_actual'];
						$folio[$che_count]['cuenta'] = $_SESSION['pau']['cuenta'];
						
						// Datos para la tabla de movimiento_gastos
						$gasto[$che_count]['codgastos'] = $codgastos;
						$gasto[$che_count]['num_cia']   = $num_cia;
						$gasto[$che_count]['fecha']     = $GLOBALS['fecha_actual'];
						$gasto[$che_count]['importe']   = number_format($importe_cheque,2,".","");
						$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
						$gasto[$che_count]['captura']   = "TRUE";
						$gasto[$che_count]['factura']   = "";
						$gasto[$che_count]['folio']     = $folio_cheque;
						
						// Almacenar datos en la tabla de 'facturas_pagadas'
						$query .= $GLOBALS['db']->multiple_insert("facturas_pagadas", $fac_pag);
						
						// Almacenar datos de la tabla de 'movimiento_gastos'
						//$query .= $GLOBALS['db']->multiple_insert("movimiento_gastos",$gasto);
						
						// Borrar facturas pagadas de pasivo
						$query .= "DELETE FROM pasivo_proveedores WHERE id IN (";
						for ($f=0; $f<$fac_count; $f++)
							$query .= $fac_del[$f] . ($f < $fac_count - 1 ? "," : ");\n");
						
						// Actualizar saldo en libros
						//$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia;\n";
						$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pau']['cuenta']};\n";
						
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
					$sql = "SELECT \"folio\" FROM \"folios_cheque\" WHERE \"num_cia\" = $num_cia AND \"cuenta\" = {$_SESSION['pau']['cuenta']} ORDER BY \"folio\" DESC LIMIT 1";
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
			// Si el saldo de la cuenta es mayor o igual al importe de la factura, sumar el total de este al importe del cheque
			// Se considera el caso de el máximo número de facturas pagadas por un cheque
			if (!$obligado) {
				if ($GLOBALS['saldo'][$num_cia] >= $factura[$i]['total'] && $num_fac < $fac_x_cheque) {
					// Sumar importe de factura al importe del cheque
					$importe_cheque = round($importe_cheque,2) + round($factura[$i]['total'],2);
					// Restar importe de factura al saldo de la cuenta
					$GLOBALS['saldo'][(int)$factura[$i]['num_cia']] -=  $factura[$i]['total'];
					
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
					$fac_pag[$fac_count]['fecha_cheque']  = $GLOBALS['fecha_actual'];
					$fac_pag[$fac_count]['folio_cheque']  = $folio_cheque;
					$fac_pag[$fac_count]['proceso']       = "TRUE";						// Proceso automático
					$fac_pag[$fac_count]['imp']           = "FALSE";
					
					/*$gasto[$fac_count]['codgastos'] = $factura[$i]['codgastos'];
					$gasto[$fac_count]['num_cia']   = $num_cia;
					$gasto[$fac_count]['fecha']     = $GLOBALS['fecha_actual'];
					$gasto[$fac_count]['importe']   = $factura[$i]['total'];
					$gasto[$fac_count]['concepto']  = "FACTURA ".$factura[$i]['num_fact'];
					$gasto[$fac_count]['captura']   = "TRUE";
					$gasto[$fac_count]['factura']   = $factura[$i]['num_fact'];
					$gasto[$fac_count]['folio']     = $folio_cheque;*/
					
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
			else {
				if ($num_fac < $fac_x_cheque) {
					// Sumar importe de factura al importe del cheque
					$importe_cheque = round($importe_cheque,2) + round($factura[$i]['total'],2);
					// Restar importe de factura al saldo de la cuenta
					$GLOBALS['saldo'][(int)$factura[$i]['num_cia']] -=  $factura[$i]['total'];
					
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
					$fac_pag[$fac_count]['fecha_cheque']  = $GLOBALS['fecha_actual'];
					$fac_pag[$fac_count]['folio_cheque']  = $folio_cheque;
					$fac_pag[$fac_count]['proceso']       = "TRUE";						// Proceso automático
					$fac_pag[$fac_count]['imp']           = "FALSE";
					
					/*$gasto[$fac_count]['codgastos'] = $factura[$i]['codgastos'];
					$gasto[$fac_count]['num_cia']   = $num_cia;
					$gasto[$fac_count]['fecha']     = $GLOBALS['fecha_actual'];
					$gasto[$fac_count]['importe']   = $factura[$i]['total'];
					$gasto[$fac_count]['concepto']  = "FACTURA ".$factura[$i]['num_fact'];
					$gasto[$fac_count]['captura']   = "TRUE";
					$gasto[$fac_count]['factura']   = $factura[$i]['num_fact'];
					$gasto[$fac_count]['folio']     = $folio_cheque;*/
					
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
		}
		
		// Organizar datos para almacenar cheque
		if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
			// Si el importe del cheque es mayor o igual al monto minimo, generarlo
			if ($importe_cheque >= $monto_min /*(15 de febrero del 2006) Número máximo de cheques*/&& $che_count < $max_cheques /*Fin cambio*/) {
				// Datos para la tabla de 'cheques'
				$cheque[$che_count]['cod_mov']           = 5;
				$cheque[$che_count]['num_proveedor']     = $num_proveedor;
				$cheque[$che_count]['num_cia']           = $num_cia;
				$cheque[$che_count]['a_nombre']          = $nombre_proveedor;
				$cheque[$che_count]['concepto']          = $nombre_gasto;
				$cheque[$che_count]['facturas']          = $facturas;
				$cheque[$che_count]['fecha']             = $GLOBALS['fecha_actual'];
				$cheque[$che_count]['folio']             = $folio_cheque;
				$cheque[$che_count]['importe']           = number_format($importe_cheque,2,".","");
				$cheque[$che_count]['iduser']            = $_SESSION['iduser'];
				$cheque[$che_count]['imp']               = "FALSE";
				$cheque[$che_count]['codgastos']         = $codgastos;
				$cheque[$che_count]['proceso']           = "TRUE";
				$cheque[$che_count]['cuenta']            = $_SESSION['pau']['cuenta'];
				
				// Datos para la tabla de 'estado_cuenta'
				$cuenta[$che_count]['num_cia'] = $num_cia;
				$cuenta[$che_count]['fecha'] = $GLOBALS['fecha_actual'];
				$cuenta[$che_count]['concepto'] = $facturas;
				$cuenta[$che_count]['tipo_mov'] = "TRUE";
				$cuenta[$che_count]['importe']  = number_format($importe_cheque,2,".","");
				$cuenta[$che_count]['cod_mov'] = 5;
				$cuenta[$che_count]['folio'] = $folio_cheque;
				$cuenta[$che_count]['cuenta'] = $_SESSION['pau']['cuenta'];
				
				// Datos para la tabla de folios_cheque
				$folio[$che_count]['folio']     = $folio_cheque;
				$folio[$che_count]['num_cia']   = $num_cia;
				$folio[$che_count]['reservado'] = "FALSE";
				$folio[$che_count]['utilizado'] = "TRUE";
				$folio[$che_count]['fecha'] = $GLOBALS['fecha_actual'];
				$folio[$che_count]['cuenta'] = $_SESSION['pau']['cuenta'];
				
				$gasto[$che_count]['codgastos'] = $codgastos;
				$gasto[$che_count]['num_cia']   = $num_cia;
				$gasto[$che_count]['fecha']     = $GLOBALS['fecha_actual'];
				$gasto[$che_count]['importe']   = number_format($importe_cheque,2,".","");
				$gasto[$che_count]['concepto']  = "PAGO PROVEEDOR: $num_proveedor FAC.: $facturas";
				$gasto[$che_count]['captura']   = "TRUE";
				$gasto[$che_count]['factura']   = "";
				$gasto[$che_count]['folio']     = $folio_cheque;
				
				// Almacenar datos en la tabla de 'facturas_pagadas'
				$query .= $GLOBALS['db']->multiple_insert("facturas_pagadas", $fac_pag);
				
				// Almacenar datos de la tabla de 'movimiento_gastos'
				//$query .= $GLOBALS['db']->multiple_insert("movimiento_gastos",$gasto);
				
				// Borrar facturas pagadas de pasivo
				$query .= "DELETE FROM pasivo_proveedores WHERE id IN (";
				for ($f=0; $f<$fac_count; $f++)
					$query .= $fac_del[$f] . ($f < $fac_count - 1 ? "," : ");\n");
				
				// Actualizar saldo en libros
				//$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia;\n";
				$query .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia AND cuenta = {$_SESSION['pau']['cuenta']};\n";
				
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
	// +----------------------------------------------------------------------------------------------------------+
	
	// +-------------------------------------Conectar a la base de datos------------------------------------------+
	$db = new DBclass($dsn);
	// +----------------------------------------------------------------------------------------------------------+
	
	$clabe_cuenta = $_SESSION['pau']['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 1                                                                                                   |
	// | Generar promedio de depósitos                                                                            |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Obtener listado de compañías y saldos, segun opciones
	$sql = "SELECT \"num_cia\",\"nombre\",\"nombre_corto\",\"$clabe_cuenta\",\"saldo_libros\" FROM \"catalogo_companias\" LEFT JOIN \"saldos\" USING(\"num_cia\") WHERE $clabe_cuenta IS NOT NULL AND cuenta = {$_SESSION['pau']['cuenta']}";
	if ($_SESSION['pau']['rango'] == "panaderias")
		$sql .= " AND \"num_cia\" < 100";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= " AND (\"num_cia\" BETWEEN 101 AND 199) OR (\"num_cia\" BETWEEN 702 AND 799)";
	$sql .= " ORDER BY \"num_cia\" ASC";
	$cia = $db->query($sql);
	
	// Declaración de variables
	$fecha_corte   = $_SESSION['pau']['fecha_corte'];		// Fecha de corte
	
	$fecha_actual  = date("d/m/Y");				// Fecha actual
	$dia_actual    = date("d");					// Dia actual
	$mes_actual    = date("n");					// Mes actual
	$anio_actual   = date("Y");					// Año actual
	
	$dias_deposito = $_SESSION['pau']['dias_deposito'];	// Días de depósito
	$dias_dif      = 0;							// Días de diferencia
	
	$total_saldo_libros = 0;					// Suma total de los saldos ne libros
	$total_promedio     = 0;					// Suma total de los promedios
	$total_saldo_pago   = 0;					// Suma total de los saldos para pago
	
	$tpl->newBlock("saldos");
	$tpl->assign("dia",$dia_actual);
	$tpl->assign("mes",mes_escrito($mes_actual));
	$tpl->assign("anio",$anio_actual);
	
	// Recorrer las compañías y calcular saldos
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] < 200 || ($cia[$i]['num_cia'] > 701 || $cia[$i]['num_cia'] < 800)) {
			// Obtener última fecha de efectivos para la compañía (dependiendo de si es panadería o rosticería)
			$sql = "SELECT \"fecha\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND cuenta = {$_SESSION['pau']['cuenta']} AND \"cod_mov\" IN (1,16) ORDER BY \"fecha\" DESC LIMIT 1";
			$result = $db->query($sql);
			// Si tiene depósitos, calcular el pronostico de saldo
			if ($result) {
				$ultimo_fecha_depositos = ($result) ? $result[0]['fecha'] : $fecha_actual;		// Ultima fecha de efectivo
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[0]['fecha'],$temp);
				$ultimo_dia_depositos  = $temp[1];
				$ultimo_mes_depositos  = $temp[2];
				$ultimo_anio_depositos = $temp[3];
				
				// Si el último efectivo capturado esta dentro del rango del mes, calcular promedios
				if ($ultimo_mes_depositos == $mes_actual && $ultimo_dia_depositos > 5) {
					$sql = "SELECT AVG(\"importe\") AS \"promedio\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND \"cuenta\" = {$_SESSION['pau']['cuenta']} AND \"cod_mov\" IN (1,16) AND \"fecha\" BETWEEN '1/$mes_actual/$anio_actual' AND '$fecha_actual'";
					$result = $db->query($sql);
					$promedio = ($result) ? $result[0]['promedio'] : 0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
				// Si el último efectivo capturado no esta dentro del rango del mes, calcular promedio del mes anterior
				else {
					$ultimo_dia_mes_anterior = date("d",mktime(0,0,0,$ultimo_mes_depositos+1,0,$ultimo_anio_depositos));
					// Calcular los días de diferencia (ultimo dia del mes - ultimo dia de efectivo + dia actual)
					$dias_dif = $ultimo_dia_mes_anterior - $ultimo_dia_depositos + $dia_actual;
					
					// Obtener depositos y calcular promedios
					$sql = "SELECT AVG(\"importe\") AS \"promedio\" FROM \"estado_cuenta\" WHERE \"num_cia\" = {$cia[$i]['num_cia']} AND \"cuenta\" = {$_SESSION['pau']['cuenta']} AND \"cod_mov\" IN (1,16) AND \"fecha\" BETWEEN '1/$ultimo_mes_depositos/$ultimo_anio_depositos' AND '$ultimo_dia_mes_anterior/$ultimo_mes_depositos/$ultimo_anio_depositos'";
					$result = $db->query($sql);
					$promedio = ($result)?$result[0]['promedio']:0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
				
			}
			// Si no tiene efectivos, el saldo para la compañia es cero
			else {
				$promedio = 0;
				$saldo[(int)$cia[$i]['num_cia']] = 0;
			}
		}
		else {
			$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'];
		}
		// Calcular totales generales
		$total_saldo_libros += $cia[$i]['saldo_libros'];
		$total_promedio += $promedio;
		$total_saldo_pago += $saldo[(int)$cia[$i]['num_cia']];
		
		// Generar listado de saldos para pagar
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("cuenta",$cia[$i][$clabe_cuenta]);
		$tpl->assign("nombre_cia",$cia[$i]['nombre']);
		$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
		$tpl->assign("promedio",number_format($promedio,2,".",","));
		$tpl->assign("saldo_pago",number_format($saldo[(int)$cia[$i]['num_cia']],2,".",","));
	}
	// Asignar totales al listado
	$tpl->assign("saldos.total_saldo_libros",number_format($total_saldo_libros,2,".",","));
	$tpl->assign("saldos.total_promedio",number_format($total_promedio,2,".",","));
	$tpl->assign("saldos.total_saldo_pago",number_format($total_saldo_pago,2,".",","));
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 1 BIS                                                                                               |
	// | Obtener id's de las facturas a pagar                                                                     |
	// +----------------------------------------------------------------------------------------------------------+
	$num_facturas = 0;
	for ($i=0; $i<$_POST['num_facturas']; $i++)
		if (isset($_POST['id'][$i]))
			$id[$num_facturas++] = $_POST['id'][$i];
	
	// Comenzar Transacción (en caso de cualquier error, se desharan todos los cambios en la base de datos)-------+
	$db->comenzar_transaccion();
	// +----------------------------------------------------------------------------------------------------------+
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 2                                                                                                   |
	// | Proceso de pagos obligados                                                                               |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Contar el número de proveedores con pago obligado
	$count = 0;
	for ($i=0; $i<30; $i++)
		if ($_SESSION['pau']['obligado'][$i] > 0) {
			$obligado[$count] = $_SESSION['pau']['obligado'][$i];
			$count++;
		}
	
	if ($count > 0) {
		// Construir script sql
		//$sql = "SELECT \"id\",\"num_cia\",\"num_fact\",\"total\",\"pasivo_proveedores\".\"descripcion\" AS \"descripcion\",\"fecha_mov\",\"fecha_pago\",\"num_proveedor\",\"nombre\",\"codgastos\",\"catalogo_gastos\".\"descripcion\" AS \"nombre_gasto\" FROM \"pasivo_proveedores\" LEFT JOIN \"catalogo_proveedores\" USING(\"num_proveedor\") LEFT JOIN \"catalogo_gastos\" USING (\"codgastos\") WHERE \"id\" IN (";
		$sql = "SELECT \"id\",\"num_cia\",\"num_fact\",\"total\",\"pasivo_proveedores\".\"descripcion\" AS \"descripcion\",\"fecha_mov\",\"fecha_pago\",\"pasivo_proveedores\".\"num_proveedor\" AS \"num_proveedor\",\"catalogo_proveedores\".\"nombre\" AS \"nombre\",\"codgastos\",\"catalogo_gastos\".\"descripcion\" AS \"nombre_gasto\" FROM \"pasivo_proveedores\" LEFT JOIN \"catalogo_proveedores\" USING(\"num_proveedor\") LEFT JOIN \"catalogo_gastos\" USING (\"codgastos\") LEFT JOIN \"catalogo_companias\" USING (\"num_cia\") WHERE ";
		// Añade critério de proveedores prioritarios para pagar
		$sql .= " \"pasivo_proveedores\".\"num_proveedor\" IN (";
		for ($i=0; $i<$count; $i++)
			$sql .= $obligado[$i].($i < $count-1 ? "," : ")");
		$sql .= " AND \"catalogo_companias\".\"$clabe_cuenta\" IS NOT NULL ORDER BY \"num_cia\",";
		// Añade condición de fecha de pago y fecha de corte
		if ($_SESSION['pau']['criterio'] == "prioridad")
			$sql .= "\"prioridad\" DESC,";
		$sql .= "\"num_proveedor\",\"fecha_pago\" ASC,\"total\" DESC";
		// Obtener facturas
		$facturas = $db->query($sql);
		
		// Ejecutar función de pago
		if ($facturas) {
			$sql = pago_proveedores($facturas, TRUE);
			$db->query($sql);
		}
	}
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 3                                                                                                   |
	// | Proceso de pagos normal                                                                                  |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Construir script sql
	$sql = "SELECT \"id\",\"num_cia\",\"num_fact\",\"total\",\"pasivo_proveedores\".\"descripcion\" AS \"descripcion\",\"fecha_mov\",\"fecha_pago\",\"num_proveedor\",\"nombre\",\"codgastos\",\"catalogo_gastos\".\"descripcion\" AS \"nombre_gasto\" FROM \"pasivo_proveedores\" JOIN \"catalogo_proveedores\" USING(\"num_proveedor\") LEFT JOIN \"catalogo_gastos\" USING (\"codgastos\") WHERE";
	$sql .= " \"id\" IN (";
	// Añade todas las facturas seleccionadas para pagar
	for ($i=0; $i<$num_facturas; $i++)
		$sql .= $id[$i] . ($i < $num_facturas - 1 ? "," : ")");
	$sql .= " ORDER BY \"num_cia\" ASC,";
	if ($_SESSION['pau']['criterio'] == "prioridad")
		$sql .= "\"prioridad\" DESC,";
	$sql .= "\"num_proveedor\",\"fecha_pago\" ASC,\"total\" DESC";
	// Obtener facturas
	$facturas = $db->query($sql);
	
	// Ejecutar función de pago
	if ($facturas) {
		$sql = pago_proveedores($facturas);
		$db->query($sql);
	}
	
	// Terminar Transacción --------------------------------------------------------------------------------------+
	$db->terminar_transaccion();
	// +----------------------------------------------------------------------------------------------------------+
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 4                                                                                                   |
	// | Generar listado de datos para emisión de cheques                                                         |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Buscar todos los cheques generados
	$sql = "SELECT \"num_cia\",\"folio\",\"num_proveedor\",\"a_nombre\",\"facturas\",\"importe\" FROM \"cheques\" WHERE \"imp\" = 'FALSE' AND \"proceso\" = 'TRUE' AND \"fecha_cancelacion\" IS NULL ORDER BY \"num_cia\",\"folio\"";
	$result = $db->query($sql);
	
	if ($result) {
		$tpl->newBlock("listado");
		
		$tpl->assign("dia",$dia_actual);
		$tpl->assign("mes",mes_escrito($mes_actual));
		$tpl->assign("anio",$anio_actual);
		
		$num_filas = count($result);
		
		$num_cia = NULL;
		$gran_total = 0;
		for ($i=0; $i<$num_filas; $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				$num_cia = $result[$i]['num_cia'];
				
				$tpl->newBlock("cia");
				$tpl->assign("num_cia",$num_cia);
				$nombre_cia = $db->query("SELECT \"nombre\",\"$clabe_cuenta\" FROM \"catalogo_companias\" WHERE \"num_cia\" = $num_cia");
				$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
				$tpl->assign("cuenta",$nombre_cia[0][$clabe_cuenta]);
				
				$total = 0;
			}
			$tpl->newBlock("fila_cheque");
			$tpl->assign("folio",$result[$i]['folio']);
			$tpl->assign("num_proveedor",$result[$i]['num_proveedor']);
			$tpl->assign("nombre_proveedor",$result[$i]['a_nombre']);
			$tpl->assign("facturas",$result[$i]['facturas']);
			$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
			$total += $result[$i]['importe'];
			$gran_total += $result[$i]['importe'];
			$tpl->assign("cia.total",number_format($total,2,".",","));
		}
		$tpl->assign("listado.gran_total",number_format($gran_total,2,".",","));
	}
	
	$db->desconectar();
	$tpl->printToScreen();
}
?>