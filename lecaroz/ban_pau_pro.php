<?php
// PAGO AUTOMATICO A PROVEEDORES
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
$descripcion_error[1] = "";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pau_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Recopilar datos para proceso automático ----------------------------------- 
if (!isset($_POST['rango']) && !isset($_GET['generar'])) {
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

// -------------------------------- Generar pre-listado de facturas por pagar ----------------------------------- 
if (isset($_GET['prelistado'])) {
	// Almacenar todos las variables POST en variables SESSION
	$_SESSION['pau'] = $_POST;
	
	// Declaración de variables
	//$fecha_pago    = $_POST['fecha_pago'];		// Fecha de pago
	$fecha_corte   = $_POST['fecha_corte'];		// Fecha de corte
	
	$fecha_actual  = date("d/m/Y");				// Fecha actual
	$dia_actual    = date("d");					// Dia actual
	$mes_actual    = date("n");					// Mes actual
	$anio_actual   = date("Y");					// Año actual

	// Obtener saldos promedio
	// Obtener listado de compañías y saldos, segun opciones
	$sql = "SELECT num_cia,nombre,nombre_corto,clabe_cuenta,saldo_libros FROM catalogo_companias  LEFT JOIN saldos USING(num_cia) ";
	if ($_SESSION['pau']['rango'] == "panaderias")//$sql .= "WHERE num_cia < 101 OR num_cia > 200 AND num_cia > 702 ";
		$sql .= "WHERE num_cia < 100 ";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= "WHERE (num_cia > 100 AND num_cia < 200) OR (num_cia >= 702 AND num_cia < 800) ";
	$sql .= "ORDER BY num_cia ASC";
	$cia = ejecutar_script($sql,$dsn);
	
	$dias_deposito = $_SESSION['pau']['dias_deposito'];	// Días de depósito
	
	$total_saldo_libros = 0;					// Suma total de los saldos ne libros
	$total_promedio     = 0;					// Suma total de los promedios
	$total_saldo_pago   = 0;					// Suma total de los saldos para pago
	
	// Recorrer las compañías y calcular saldos
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] < 200 || ($cia[$i]['num_cia'] > 701 || $cia[$i]['num_cia'] < 800)) {
			// Obtener última fecha de efectivos para la compañía (dependiendo de si es panadería o rosticería)
			$sql = "SELECT fecha FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." ORDER BY fecha DESC LIMIT 1";
			$result = ejecutar_script($sql,$dsn);
			// Si tiene depósitos, calcular el pronostico de saldo
			if ($result) {
				$ultimo_fecha_depositos = ($result)?$result[0]['fecha']:date("d/m/Y");		// Ultima fecha de efectivo
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[0]['fecha'],$temp);
				$ultimo_dia_depositos  = $temp[1];
				$ultimo_mes_depositos  = $temp[2];
				$ultimo_anio_depositos = $temp[3];
				
				// Si el último efectivo capturado esta dentro del rango del mes, calcular promedios
				if ($ultimo_mes_depositos == $mes_actual && $ultimo_dia_depositos > 5) {
					$sql = "SELECT AVG(importe) AS promedio FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." AND fecha >= '1/$mes_actual/$anio_actual' AND fecha <= '$fecha_actual'";
					$result = ejecutar_script($sql,$dsn);
					$promedio = ($result)?$result[0]['promedio']:0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
				// Si el último efectivo capturado no esta dentro del rango del mes, calcular promedio del mes anterior
				else {
					$ultimo_dia_mes_anterior = date("d",mktime(0,0,0,$ultimo_mes_depositos+1,0,$ultimo_anio_depositos));
					// Calcular los días de diferencia (ultimo dia del mes - ultimo dia de efectivo + dia actual)
					$dias_dif = $ultimo_dia_mes_anterior - $ultimo_dia_depositos + $dia_actual;
					
					// Obtener depositos y calcular promedios
					$sql = "SELECT AVG(importe) AS promedio FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." AND fecha >= '1/$ultimo_mes_depositos/$ultimo_anio_depositos' AND fecha <= '$ultimo_dia_mes_anterior/$ultimo_mes_depositos/$ultimo_anio_depositos'";
					$result = ejecutar_script($sql,$dsn);
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
	}
	
	// Contar el número de proveedores sin pago
	$sinpago_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_SESSION['pau']['sin_pago'.$i] > 0) {
			$sin_pago[$sinpago_count] = $_POST['sin_pago'.$i];
			$sinpago_count++;
		}
	
	// Contar el número de compañías ke no pagaran
	$nopagan_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_SESSION['pau']['no_pagan'.$i] > 0) {
			$no_pagan[$nopagan_count] = $_SESSION['pau']['no_pagan'.$i]; 
			$nopagan_count++;
		}
	
	$sql = "SELECT id,num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE fecha_pago <= '$fecha_corte'";
	// Añade condición de proveedores sin pago a script sql
	if ($sinpago_count > 0) {
		$sql .= " AND num_proveedor NOT IN (";
		for ($i=0; $i<$sinpago_count; $i++) {
			$sql .= $sin_pago[$i];
			if ($i < $sinpago_count-1)
				$sql .= ",";
		}
		$sql .= ")";
	}
	// Añade condición de compañías que no pagaran facturas a script sql
	if ($nopagan_count > 0) {
		$sql .= " AND num_cia NOT IN (";
		for ($i=0; $i<$nopagan_count; $i++) {
			$sql .= $no_pagan[$i];
			if ($i < $nopagan_count-1)
				$sql .= ",";
		}
		$sql .= ")";
	}
	// Añade condicion de rango de compañías a script sql
	if ($_SESSION['pau']['rango'] == "panaderias")
		$sql .= " AND num_cia < 100";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= " AND (num_cia > 100 AND num_cia < 200) OR (num_cia >= 702 AND num_cia < 800)";
	// Añade condición de fecha de pago y fecha de corte
	$sql .= " ORDER BY num_cia ASC,";
	if ($_SESSION['pau']['criterio'] == "prioridad")
		$sql .= "prioridad DESC,";
	$sql .= "fecha_pago ASC,total DESC";
	
	// Obtener facturas
	$factura = ejecutar_script($sql,$dsn);
	
	// Si no hay resultados, regresar a la pantalla de inicio
	if (!$factura) {
		header("location: ./ban_pau_pro.php?codigo_error=1");
		die;
	}
	
	// Generar listado
	$tpl->newBlock("pre_listado");
	$tpl->assign("num_facturas",count($factura));
	
	$num_cia = NULL;
	for ($i=0; $i<count($factura); $i++) {
		if ($num_cia != $factura[$i]['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign("bloque_cia.total",number_format($total,2,".",","));
			
			$num_cia = $factura[$i]['num_cia'];
			
			$tpl->newBlock("bloque_cia");
			$tpl->assign("num_cia",$factura[$i]['num_cia']);
			$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
			$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
			$tpl->assign("saldo",number_format($saldo[$num_cia],2,".",","));
			
			$total = 0;
		}
		if ($saldo[$num_cia] - $factura[$i]['total'] > 0) {
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
		}
	}
	
	$tpl->printToScreen();
	
	die;
}

if (isset($_GET['generar'])) {
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 1                                                                                                   |
	// | Generar promedio de depósitos                                                                            |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Obtener listado de compañías y saldos, segun opciones
	$sql = "SELECT num_cia,nombre,nombre_corto,clabe_cuenta,saldo_libros FROM catalogo_companias  LEFT JOIN saldos USING(num_cia) ";
	if ($_SESSION['pau']['rango'] == "panaderias")//$sql .= "WHERE num_cia < 101 OR num_cia > 200 AND num_cia > 702 ";
		$sql .= "WHERE num_cia < 100 ";
	else if ($_SESSION['pau']['rango'] == "rosticerias")
		$sql .= "WHERE (num_cia > 100 AND num_cia < 200) OR (num_cia >= 702 AND num_cia < 800) ";
	$sql .= "ORDER BY num_cia ASC";
	$cia = ejecutar_script($sql,$dsn);
	
	// Declaración de variables
	//$fecha_pago    = $_POST['fecha_pago'];		// Fecha de pago
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
	
	// Crear bloque para listado de saldos para pagar
	$tpl->newBlock("saldos");
	$tpl->assign("dia",$dia_actual);
	switch ($mes_actual) {
		case 1:  $tpl->assign("mes","Enero");      break;
		case 2:  $tpl->assign("mes","Febrero");    break;
		case 3:  $tpl->assign("mes","Marzo");      break;
		case 4:  $tpl->assign("mes","Abril");      break;
		case 5:  $tpl->assign("mes","Mayo");       break;
		case 6:  $tpl->assign("mes","Junio");      break;
		case 7:  $tpl->assign("mes","Julio");      break;
		case 8:  $tpl->assign("mes","Agosto");     break;
		case 9:  $tpl->assign("mes","Septiembre"); break;
		case 10: $tpl->assign("mes","Octubre");    break;
		case 11: $tpl->assign("mes","Noviembre");  break;
		case 12: $tpl->assign("mes","Diciembre");  break;
	}
	$tpl->assign("anio",$anio_actual);
	
	// Recorrer las compañías y calcular saldos
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] < 200 || ($cia[$i]['num_cia'] > 701 || $cia[$i]['num_cia'] < 800)) {
			// Obtener última fecha de efectivos para la compañía (dependiendo de si es panadería o rosticería)
			$sql = "SELECT fecha FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." ORDER BY fecha DESC LIMIT 1";
			$result = ejecutar_script($sql,$dsn);
			// Si tiene depósitos, calcular el pronostico de saldo
			if ($result) {
				$ultimo_fecha_depositos = ($result)?$result[0]['fecha']:date("d/m/Y");		// Ultima fecha de efectivo
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[0]['fecha'],$temp);
				$ultimo_dia_depositos  = $temp[1];
				$ultimo_mes_depositos  = $temp[2];
				$ultimo_anio_depositos = $temp[3];
				
				// Si el último efectivo capturado esta dentro del rango del mes, calcular promedios
				if ($ultimo_mes_depositos == $mes_actual && $ultimo_dia_depositos > 5) {
					$sql = "SELECT AVG(importe) AS promedio FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." AND fecha >= '1/$mes_actual/$anio_actual' AND fecha <= '$fecha_actual'";
					$result = ejecutar_script($sql,$dsn);
					$promedio = ($result)?$result[0]['promedio']:0;
					$saldo[(int)$cia[$i]['num_cia']] = $cia[$i]['saldo_libros'] + $dias_deposito * $promedio;
				}
				// Si el último efectivo capturado no esta dentro del rango del mes, calcular promedio del mes anterior
				else {
					$ultimo_dia_mes_anterior = date("d",mktime(0,0,0,$ultimo_mes_depositos+1,0,$ultimo_anio_depositos));
					// Calcular los días de diferencia (ultimo dia del mes - ultimo dia de efectivo + dia actual)
					$dias_dif = $ultimo_dia_mes_anterior - $ultimo_dia_depositos + $dia_actual;
					
					// Obtener depositos y calcular promedios
					$sql = "SELECT AVG(importe) AS promedio FROM estado_cuenta WHERE num_cia = ".$cia[$i]['num_cia']." AND cod_mov = ".(($cia[$i]['num_cia'] < 100)?"1":"16")." AND fecha >= '1/$ultimo_mes_depositos/$ultimo_anio_depositos' AND fecha <= '$ultimo_dia_mes_anterior/$ultimo_mes_depositos/$ultimo_anio_depositos'";
					$result = ejecutar_script($sql,$dsn);
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
		$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
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
		if (isset($_POST['id'.$i]))
			$id[$num_facturas++] = $_POST['id'.$i];
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 2                                                                                                   |
	// | Proceso de pagos obligados                                                                               |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Declaración de variables
	$fac_x_cheque = 10;		// Número de facturas pagadas por cheque (aplica al proceso normal)
	$folio_cheque = 0;		// Folio para el cheque (aplica al proceso normal)
	$monto_min    = 1000;	// Monto mínimo de un cheque (por default 1,000.00)
	
	// Contar el número de proveedores con pago obligado
	$count = 0;
	for ($i=0; $i<30; $i++)
		if ($_SESSION['pau']['obligado'.$i] > 0) {
			$obligado[$count] = $_SESSION['pau']['obligado'.$i];
			$count++;
		}
	
	// Si hay pago obligado, construir script sql y obtener facturas
	if ($count > 0) {
		// Construir script sql
		$sql = "SELECT id,num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE id IN (";
		// Añade todas las facturas seleccionadas para pagar
		for ($i=0; $i<$num_facturas; $i++) {
			$sql .= $id[$i];
			if ($i < $num_facturas-1)
				$sql .= ",";
		}
		// Añade critério de proveedores prioritarios para pagar
		$sql .= ") AND num_proveedor IN (";
		for ($i=0; $i<$count; $i++) {
			$sql .= $obligado[$i];
			if ($i < $count-1)
				$sql .= ",";
		}
		//$sql .= ") AND fecha_pago <= '$fecha_corte' ORDER BY num_cia,";
		$sql .= ") ORDER BY num_cia,";
		// Añade condición de fecha de pago y fecha de corte
		if ($_SESSION['pau']['criterio'] == "prioridad")
			$sql .= "prioridad DESC,";
		$sql .= "num_proveedor,fecha_pago ASC,total DESC";
		//echo "$sql<br>";
		// Obtener facturas
		$factura = ejecutar_script($sql,$dsn);
		
		// Si hay facturas pendientes para pagar
		if ($factura) {
			$num_cia = NULL;		// Última compañía revisada
			$num_proveedor = NULL;	// Último proveedor revisado
			$num_fac = 0;			// Número de facturas actual para el cheque
			$importe_cheque = 0;	// Importe del cheque
			
			$fac_count = 0;			// Contador de facturas
			$che_count = 0;			// Contador de cheques
			
			$cosgastos = NULL;		// Código de gasto para el cheque
			
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
							$cheque['fecha'.$che_count]             = $fecha_actual;
							$cheque['folio'.$che_count]             = $folio_cheque;
							$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
							$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
							$cheque['imp'.$che_count]               = "FALSE";
							$cheque['num_cheque'.$che_count]        = "";
							$cheque['fecha_cancelacion'.$che_count] = "";
							$cheque['codgastos'.$che_count]         = $codgastos;
							
							// Datos para la tabla de 'estado_cuenta'
							$cuenta['num_cia'.$che_count] = $num_cia;
							$cuenta['fecha'.$che_count] = $fecha_actual;
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
								//ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
							}
							
							// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
							 ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
							
							// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
							//echo "<b>Folio cheque: $folio_cheque Importe Cheque: ".number_format($importe_cheque,2,".",",")."</b><br>";
							
							// Imcrementar número de folio
							$folio_cheque++;
							// Incrementar contador de cheques
							$che_count++;
						}
					}
					
					// Verificar el cambio de compañía
					if ($factura[$i]['num_cia'] != $num_cia) {
						if ($num_cia != NULL) {
							// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
							//echo "<b>CIA: $num_cia --- SALDO FINAL: ".number_format($saldo[$num_cia],2,".",",")."</b><br><br>";
						}
						
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
						
						// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
						//echo "<b>CIA: $num_cia --- SALDO INICIAL: ".number_format($saldo[$num_cia],2,".",",")."</b><br>";	// Mostrar saldo inicial de la compañía
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
				if ($saldo[$num_cia] >= $factura[$i]['total'] && $num_fac < $fac_x_cheque) {
					// Sumar importe de factura al importe del cheque
					$importe_cheque = number_format($importe_cheque,2,".","") + number_format($factura[$i]['total'],2,".","");
					// Restar importe de factura al saldo de la cuenta
					$saldo[(int)$factura[$i]['num_cia']] -=  $factura[$i]['total'];
					
					// Factura a borrar de pasivo
					$fac_del['id'.$fac_count] = $factura[$i]['id'];
					$fac_pollos['fac'.$fac_count] = $factura[$i]['num_fact'];
					
					// Organizar datos para almacenar factura en la tabla de facturas_pagadas
					$fac_pag['num_cia'.$fac_count]       = $num_cia;
					$fac_pag['num_proveedor'.$fac_count] = $num_proveedor;
					$fac_pag['num_fact'.$fac_count]      = $factura[$i]['num_fact'];
					$fac_pag['codgastos'.$fac_count]     = $factura[$i]['codgastos'];	// ESTE CAMPO FALTA AGREGARLO A LA TABLA DE 'pasivo_proveedores'
					$fac_pag['total'.$fac_count]         = $factura[$i]['total'];
					$fac_pag['descripcion'.$fac_count]   = $factura[$i]['descripcion'];
					$fac_pag['fecha_mov'.$fac_count]     = $factura[$i]['fecha_mov'];
					$fac_pag['fecha_pago'.$fac_count]    = $factura[$i]['fecha_pago'];
					$fac_pag['fecha_cheque'.$fac_count]  = $fecha_actual;
					$fac_pag['folio_cheque'.$fac_count]  = $folio_cheque;
					$fac_pag['proceso'.$fac_count]       = "TRUE";	// Proceso automático
					$fac_pag['imp'.$fac_count]           = "FALSE";
					
					$gasto['codgastos'.$fac_count] = $factura[$i]['codgastos'];
					$gasto['num_cia'.$fac_count]   = $num_cia;
					$gasto['fecha'.$fac_count]     = $fecha_actual;
					$gasto['importe'.$fac_count]   = $factura[$i]['total'];
					$gasto['concepto'.$fac_count]  = "FACTURA ".$factura[$i]['num_fact'];
					$gasto['captura'.$fac_count]   = "TRUE";
					$gasto['factura'.$fac_count]   = $factura[$i]['num_fact'];
					$gasto['folio'.$fac_count]     = $folio_cheque;
					
					// Agregar el número de factura al concepto del cheque
					if ($fac_count > 0)
						$facturas .= " ";
					$facturas .= fillZero($factura[$i]['num_fact'],7);
					
					$codgastos = $factura[$i]['codgastos'];
					
					// Incrementar contador de facturas
					$fac_count++;
					// Incrementar contador de facturas por cheque
					$num_fac++;
				}
			}
			// Organizar datos para almacenar cheque (ULTIMO CHEQUE)
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
					$cheque['fecha'.$che_count]             = $fecha_actual;
					$cheque['folio'.$che_count]             = $folio_cheque;
					$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
					$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
					$cheque['imp'.$che_count]               = "FALSE";
					$cheque['num_cheque'.$che_count]        = "";
					$cheque['fecha_cancelacion'.$che_count] = "";
					$cheque['codgastos'.$che_count]         = $codgastos;
					
					// Datos para la tabla de 'estado_cuenta'
					$cuenta['num_cia'.$che_count] = $num_cia;
					$cuenta['fecha'.$che_count] = $fecha_actual;
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
						//ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
					}
					
					// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
					 ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
					
					// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
					//echo "<b>Folio cheque: $folio_cheque Importe Cheque: ".number_format($importe_cheque,2,".",",")."</b><br>";
					
					// Imcrementar número de folio
					$folio_cheque++;
					// Incrementar contador de cheques
					$che_count++;
				}
			}
		}
	}
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 3                                                                                                   |
	// | Proceso de pagos normal                                                                                  |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Declaración de variables
	/*
	// Contar el número de proveedores sin pago
	$sinpago_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_POST['sin_pago'.$i] > 0) {
			$sin_pago[$sinpago_count] = $_POST['sin_pago'.$i];
			$sinpago_count++;
		}
	
	// Contar el número de compañías ke no pagaran
	$nopagan_count = 0;
	for ($i=0; $i<10; $i++)
		if ($_POST['no_pagan'.$i] > 0) {
			$no_pagan[$nopagan_count] = $_POST['no_pagan'.$i]; 
			$nopagan_count++;
		}*/
	
	// Construir script sql
	//$sql = "SELECT id,num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE fecha_pago <= '$fecha_corte'";
	$sql = "SELECT id,num_cia,num_fact,total,descripcion,fecha_mov,fecha_pago,num_proveedor,nombre,codgastos FROM pasivo_proveedores JOIN catalogo_proveedores USING(num_proveedor) WHERE id IN (";
	// Añade todas las facturas seleccionadas para pagar
	for ($i=0; $i<$num_facturas; $i++) {
		$sql .= $id[$i];
		if ($i < $num_facturas-1)
			$sql .= ",";
	}
	/*
	// Añade condición de proveedores sin pago a script sql
	if ($sinpago_count > 0) {
		$sql .= " AND num_proveedor NOT IN (";
		for ($i=0; $i<$sinpago_count; $i++) {
			$sql .= $sin_pago[$i];
			if ($i < $sinpago_count-1)
				$sql .= ",";
		}
		$sql .= ")";
	}
	// Añade condición de compañías que no pagaran facturas a script sql
	if ($nopagan_count > 0) {
		$sql .= " AND num_cia NOT IN (";
		for ($i=0; $i<$nopagan_count; $i++) {
			$sql .= $no_pagan[$i];
			if ($i < $nopagan_count-1)
				$sql .= ",";
		}
		$sql .= ")";
	}
	// Añade condicion derango de compañías a script sql
	if ($_POST['rango'] == "panaderias")
		$sql .= " AND num_cia < 101 OR num_cia > 200 AND num_cia != 702";
	else if ($_POST['rango'] == "rosticerias")
		$sql .= " AND (num_cia > 100 AND num_cia <= 200) OR (num_cia >= 702 AND num_cia <= 710)";*/
	// Añade condición de fecha de pago y fecha de corte
	//$sql .= " ORDER BY num_cia ASC,";
	$sql .= ") ORDER BY num_cia ASC,";
	if ($_SESSION['pau']['criterio'] == "prioridad")
		$sql .= "prioridad DESC,";
	$sql .= "num_proveedor,fecha_pago ASC,total DESC";
	//echo "$sql<br>";
	// Obtener facturas
	$factura = ejecutar_script($sql,$dsn);
	
	// Si el script genero resultados
	if ($factura) {
		$num_cia = NULL;		// Última compañía revisada
		$num_proveedor = NULL;	// Último proveedor revisado
		$num_fac = 0;			// Número de facturas actual para el cheque
		$importe_cheque = 0;	// Importe del cheque
		
		$codgastos = 0;
		
		if (!isset($fac_count) || !isset($che_count)) {
			$fac_count = 0;			// Contador de facturas
			$che_count = 0;			// Contador de cheques
		}
		
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
						$cheque['concepto'.$che_count]          = "";
						$cheque['facturas'.$che_count]          = $facturas;				// El concepto de cheque son todas facturas que son pagadas con el mismo
						$cheque['fecha'.$che_count]             = $fecha_actual;
						$cheque['folio'.$che_count]             = $folio_cheque;
						$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
						$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
						$cheque['imp'.$che_count]               = "FALSE";
						$cheque['num_cheque'.$che_count]        = "";
						$cheque['fecha_cancelacion'.$che_count] = "";
						$cheque['codgastos'.$che_count]         = $codgastos;
						
						// Datos para la tabla de 'estado_cuenta'
						$cuenta['num_cia'.$che_count] = $num_cia;
						$cuenta['fecha'.$che_count] = $fecha_actual;
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
							//ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
						}
						
						// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
						 ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
						
						// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
						//echo "<b>Folio cheque: $folio_cheque Importe Cheque: ".number_format($importe_cheque,2,".",",")."</b><br>";
						
						// Imcrementar número de folio
						$folio_cheque++;
						// Incrementar contador de cheques
						$che_count++;
					}
				}
				
				// Verificar el cambio de compañía
				if ($factura[$i]['num_cia'] != $num_cia) {
					if ($num_cia != NULL) {
						// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
						//echo "<b>CIA: $num_cia --- SALDO FINAL: ".number_format($saldo[$num_cia],2,".",",")."</b><br><br>";
					}
					
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
					// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
					//echo "<b>CIA: $num_cia --- SALDO INICIAL: ".number_format($saldo[$num_cia],2,".",",")."</b><br>";	// Mostrar saldo inicial de la compañía
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
			if ($saldo[$num_cia] >= $factura[$i]['total'] && $num_fac < $fac_x_cheque) {
				// Sumar importe de factura al importe del cheque
				$importe_cheque = number_format($importe_cheque,2,".","") + number_format($factura[$i]['total'],2,".","");
				// Restar importe de factura al saldo de la cuenta
				$saldo[(int)$factura[$i]['num_cia']] -=  $factura[$i]['total'];
				
				// Factura a borrar de pasivo
				$fac_del['id'.$fac_count] = $factura[$i]['id'];
				
				// Organizar datos para almacenar factura en la tabla de facturas_pagadas
				$fac_pag['num_cia'.$fac_count]       = $num_cia;
				$fac_pag['num_proveedor'.$fac_count] = $num_proveedor;
				$fac_pag['num_fact'.$fac_count]      = $factura[$i]['num_fact'];
				$fac_pag['codgastos'.$fac_count]     = $factura[$i]['codgastos'];	// ESTE CAMPO FALTA AGREGARLO A LA TABLA DE 'pasivo_proveedores'
				$fac_pag['total'.$fac_count]         = $factura[$i]['total'];
				$fac_pag['descripcion'.$fac_count]   = $factura[$i]['descripcion'];
				$fac_pag['fecha_mov'.$fac_count]     = $factura[$i]['fecha_mov'];
				$fac_pag['fecha_pago'.$fac_count]    = $factura[$i]['fecha_pago'];
				$fac_pag['fecha_cheque'.$fac_count]  = $fecha_actual;
				$fac_pag['folio_cheque'.$fac_count]  = $folio_cheque;
				$fac_pag['proceso'.$fac_count]       = "TRUE";	// Proceso automático
				$fac_pag['imp'.$fac_count]           = "FALSE";
				
				$gasto['codgastos'.$fac_count] = $factura[$i]['codgastos'];
				$gasto['num_cia'.$fac_count]   = $num_cia;
				$gasto['fecha'.$fac_count]     = $fecha_actual;
				$gasto['importe'.$fac_count]   = $factura[$i]['total'];
				$gasto['concepto'.$fac_count]  = "FACTURA ".$factura[$i]['num_fact'];
				$gasto['captura'.$fac_count]   = "TRUE";
				$gasto['factura'.$fac_count]   = $factura[$i]['num_fact'];
				$gasto['folio'.$fac_count]     = $folio_cheque;
				
				// Agregar el número de factura al concepto del cheque
				if ($fac_count > 0)
					$facturas .= " ";
				$facturas .= fillZero($factura[$i]['num_fact'],7);
				
				$codgastos = $factura[$i]['codgastos'];
				
				// Incrementar contador de facturas
				$fac_count++;
				// Incrementar contador de facturas por cheque
				$num_fac++;
			}
		}
		// Organizar datos para almacenar cheque (ULTIMO CHEQUE)
		if ($num_cia != NULL && $num_proveedor != NULL && $num_fac > 0 && $num_fac <= $fac_x_cheque) {
			// Si el importe del cheque es mayor o igual al monto minimo, generarlo
			if ($importe_cheque >= $monto_min) {
				// Datos para la tabla de 'cheques'
				$cheque['cod_mov'.$che_count]           = 5;						// PENDIENTE...
				$cheque['num_proveedor'.$che_count]     = $num_proveedor;
				$cheque['num_cia'.$che_count]           = $num_cia;
				$cheque['a_nombre'.$che_count]          = $nombre_proveedor;
				$cheque['concepto'.$che_count]          = "";
				$cheque['facturas'.$che_count]          = $facturas;				// El concepto de cheque son todas facturas que son pagadas con el mismo
				$cheque['fecha'.$che_count]             = $fecha_actual;
				$cheque['folio'.$che_count]             = $folio_cheque;
				$cheque['importe'.$che_count]           = number_format($importe_cheque,2,".","");
				$cheque['iduser'.$che_count]            = $_SESSION['iduser'];
				$cheque['imp'.$che_count]               = "FALSE";
				$cheque['num_cheque'.$che_count]        = "";
				$cheque['fecha_cancelacion'.$che_count] = "";
				$cheque['codgastos'.$che_count]         = $codgastos;
				
				// Datos para la tabla de 'estado_cuenta'
				$cuenta['num_cia'.$che_count] = $num_cia;
				$cuenta['fecha'.$che_count] = $fecha_actual;
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
					//ejecutar_script("UPDATE total_fac_ros SET pagado = 'TRUE' WHERE num_cia = $num_cia AND num_fac = ".$fac_pollos['fac'.$f],$dsn);
				}
				
				// Actualizar saldo en libros (POR EL MOMENTO ESTE PROCESO NO SE HARA)
				ejecutar_script("UPDATE saldos SET saldo_libros = saldo_libros - $importe_cheque WHERE num_cia = $num_cia",$dsn);
				
				// ESTE CODIGO SE PUEDE BORRAR CON CONFIANZA, SOLO SIRVE PARA MOSTRAR RESULTADOS
				//echo "<b>Folio cheque: $folio_cheque Importe Cheque: ".number_format($importe_cheque,2,".",",")."</b><br>";
				
				// Imcrementar número de folio
				$folio_cheque++;
				// Incrementar contador de cheques
				$che_count++;
			}
		}
	}
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 4                                                                                                   |
	// | Almacenar datos en la base                                                                               |
	// +----------------------------------------------------------------------------------------------------------+
	
	if ($che_count > 0) {
		// Almacenar datos en la tabla de 'cheques'
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
		// Almacenar datos en a tabla de 'movimiento_gastos'
		$db = new DBclass($dsn,"movimiento_gastos",$gasto);
		$db->xinsertar();
		unset($db);
	}
	
	// +----------------------------------------------------------------------------------------------------------+
	// | PASO 5                                                                                                   |
	// | Generar listado de datos para emisión de cheques                                                         |
	// +----------------------------------------------------------------------------------------------------------+
	
	// Construir script sql
	$sql = "SELECT num_cia,num_proveedor,nombre AS nombre_proveedor,num_fact,total AS importe,folio_cheque FROM facturas_pagadas JOIN catalogo_proveedores USING(num_proveedor) WHERE imp = 'FALSE' ORDER BY num_cia,num_proveedor,folio_cheque ASC";
	// Obtener facturas pagadas
	$result = ejecutar_script($sql,$dsn);
	// Borrar marcas de impresión
	$sql = "UPDATE facturas_pagadas SET imp = 'TRUE' WHERE imp = 'FALSE'";
	ejecutar_script($sql,$dsn);
	// Generar listado
	if ($result) {
		// Crear bloque de listado
		$tpl->newBlock("cheques");
		// Asignar valores de fecha
		$tpl->assign("dia",$dia_actual);
		$tpl->assign("mes",mes_escrito($mes_actual));
		$tpl->assign("anio",$anio_actual);
		
		// Declarar variables
		$num_cia = NULL;			// Proxima compañía
		$folio_cheque = NULL;		// Proximo lote
		
		$num_fac = 0;				// Número de facturas por lote
		$num_fac_x_cia = 0;			// Número de filas por cada bloque de compañía
		$first_line = TRUE;			// Verdadero si es el primer lote de la compañía
		
		$lote = 0;					// Número de lote actual
		$importe_lote = 0;			// Importe del lote actual
		$importe_cuenta = 0;		// Importe total de la cuenta
		
		// Recorrer facturas pagadas
		for ($i=0; $i<count($result); $i++) {
			// Cambiar compañía o lote si existe algun cambio
			if ($result[$i]['num_cia'] != $num_cia || $result[$i]['folio_cheque'] != $folio_cheque) {
				// Cambiar la compañía actual
				if ($result[$i]['num_cia'] != $num_cia && $result[$i]['folio_cheque'] != $folio_cheque) {
					// Asignar el importe del lote, solo si el lote es de mas de una factura
					if ($folio_cheque != NULL && $num_cia != NULL) {
						if ($num_fac > 0) {
							// Asignar el rowspan del bloque lote, dependiendo de si es el primer lote o uno subsecuente
							if ($first_line)
								$tpl->assign("nombre_cia.rowspan_lote",$num_fac+2);
							else
								$tpl->assign("nombre_proveedor.rowspan_lote",$num_fac+2);
							
							// Crear bloque para el total del lote
							$tpl->newBlock("total_lote");
							$tpl->assign("importe_lote",number_format($importe_lote,2,".",","));	// Asignar importe del lote
							$num_fac_x_cia++;														// Incrementar contador de número de facturas por compañía
						}
					}
					// Si la compañía a cambiado, asignar el total de la cuenta
					if ($num_cia != NULL) {
						$tpl->assign("cia.importe_total",number_format($importe_cuenta,2,".",","));	// Asignar importe total de la cuenta
						$tpl->assign("nombre_cia.rowspan",$num_fac_x_cia);							// Asignar el rowspan del bloque cia
					}
					
					$num_cia = $result[$i]['num_cia'];	// Cambiar compañía actual
					$importe_cuenta = 0;				// Poner importe de la cuenta a cero
					$importe_lote = 0;					// Poner importe del lote a cero
					
					$first_line = TRUE;					// Resetear primer lote de la compañía
					$num_fac_x_cia = 0;					// Poner número de facturas por compañía a cero
					$num_fac = 0;						// Poner número de facturas por lote a cero
					
					$folio_cheque = $result[$i]['folio_cheque'];	// Cambiar el lote actual
					$lote++;										// Incrementar el número de lotes
					
					// Crear nuevo bloque para la compañía
					$tpl->newBlock("cia");
					$cia = ejecutar_script("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
					// Asignar valores a la primera fila
					$tpl->newBlock("lote");
					$tpl->newBlock("nombre_cia");
					$tpl->assign("num_cia",$num_cia);
					$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
					$tpl->assign("nombre_cia",$cia[0]['nombre']);
					$tpl->assign("lote",$lote);
					$tpl->assign("num_proveedor",$result[$i]['num_proveedor']);
					$tpl->assign("nombre_proveedor",$result[$i]['nombre_proveedor']);
					$tpl->assign("factura",$result[$i]['num_fact']);
					$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
					
					$importe_lote += $result[$i]['importe'];	// Sumar al importe del lote la factura
					$importe_cuenta += $result[$i]['importe'];	// Sumar al importe total de la cuenta la factura
					
					$num_fac_x_cia++;	// Incrementar el contador de facturas por compañía
				}
				// Cambiar cheque actual
				else if ($result[$i]['num_cia'] == $num_cia && $result[$i]['folio_cheque'] != $folio_cheque) {
					// Asignar el importe del lote
					if ($folio_cheque != NULL) {
						if ($num_fac > 0) {
							if ($first_line)
								$tpl->assign("nombre_cia.rowspan_lote",$num_fac+2);
							else
								$tpl->assign("nombre_proveedor.rowspan_lote",$num_fac+2);
							
							$tpl->newBlock("total_lote");
							$tpl->assign("importe_lote",number_format($importe_lote,2,".",","));
							$num_fac_x_cia++;
						}
					}
					
					$first_line = FALSE;
					$folio_cheque = $result[$i]['folio_cheque'];
					$lote++;
					
					$num_fac = 0;
					$importe_lote = 0;
					
					// Crear bloque para el lote de facturas
					$tpl->newBlock("lote");
					$tpl->newBlock("nombre_proveedor");
					$tpl->assign("lote",$lote);
					$tpl->assign("num_proveedor",$result[$i]['num_proveedor']);
					$tpl->assign("nombre_proveedor",$result[$i]['nombre_proveedor']);
					$tpl->assign("factura",$result[$i]['num_fact']);
					$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
					
					$importe_lote += $result[$i]['importe'];
					$importe_cuenta += $result[$i]['importe'];
					
					$num_fac_x_cia++;
				}
			}
			else {
				$tpl->newBlock("factura");
				$tpl->assign("factura",$result[$i]['num_fact']);
				$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
				
				$importe_lote += $result[$i]['importe'];
				$importe_cuenta += $result[$i]['importe'];
				
				$num_fac++;
				$num_fac_x_cia++;
			}
		}
		if ($num_cia != NULL) {
			$tpl->assign("cia.importe_total",number_format($importe_cuenta,2,".",","));
			$tpl->assign("nombre_cia.rowspan",$num_fac_x_cia);
		}
	}
	
	$tpl->printToScreen();
}
?>