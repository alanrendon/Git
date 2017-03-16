<?php
// CONSULTA DE EXPENDIOS
// Tabla 'mov_expendios'
// Menu 'Panaderías->Expendios'

define ('IDSCREEN',1241); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_exp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha",date("d/m/Y"));
	
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

// -------------------------------- Mostrar listado ---------------------------------------------------------

// ---------------------------------- Listado por día -----------------------------------------------------
if ($_GET['tipo'] == "saldos") {
	// Obtener registros
	$sql = "SELECT * FROM mov_expendios WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY num_expendio ASC";
	$result = ejecutar_script($sql,$dsn);
	
	// Si no hay resultados
	if (!$result) {
		header("location: ./pan_exp_con.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock("saldos");
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
	
	// Asignar valores a encabezado
	$tpl->assign("num_cia",$_GET['num_cia']);			// Asignar número de compañía
	$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);	// Asignar nombre de compañía
	$tpl->assign("dia",$fecha[1]);						// Asignar día
	$tpl->assign("anio",$fecha[3]);						// Asignar año
	switch ($fecha[2]) {
		case 1:  $mes = "Enero";		break;
		case 2:  $mes = "Febrero";		break;
		case 3:  $mes = "Marzo";		break;
		case 4:  $mes = "Abril";		break;
		case 5:  $mes = "Mayo";			break;
		case 6:  $mes = "Junio";		break;
		case 7:  $mes = "Julio";		break;
		case 8:  $mes = "Agosto";		break;
		case 9:  $mes = "Septiembre";	break;
		case 10: $mes = "Octubre";		break;
		case 11: $mes = "Noviembre";	break;
		case 12: $mes = "Diciembre";	break;
	}
	$tpl->assign("mes",$mes);						// Asignar mes

	$pan_p_venta = 0;
	$pan_p_expendio = 0;
	$abono = 0;
	$devolucion = 0;
	$rezago_anterior = 0;
	$rezago_actual = 0;
	$porc = 0;
	$diferencia = 0;
	
	for ($i=0; $i<count($result); $i++) {
		if ($result[$i]['pan_p_venta'] > 0 || $result[$i]['pan_p_expendio'] > 0 || $result[$i]['abono'] > 0 || $result[$i]['devolucion'] > 0 || $result[$i]['rezago_anterior'] > 0) {
			$tpl->newBlock("fila");
			$tpl->assign("num_exp",$result[$i]['num_expendio']);
			$tpl->assign("nombre_exp",$result[$i]['nombre_expendio']);
			$tpl->assign("saldo_anterior",($result[$i]['rezago_anterior'] > 0)?number_format($result[$i]['rezago_anterior'],2,".",","):"&nbsp;");
			$tpl->assign("precio_venta",($result[$i]['pan_p_venta'] > 0)?number_format($result[$i]['pan_p_venta'],2,".",","):"&nbsp;");
			$tpl->assign("precio_exp",($result[$i]['pan_p_expendio'] > 0)?number_format($result[$i]['pan_p_expendio'],2,".",","):"&nbsp;");
			$tpl->assign("diferencia",(round($result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'],2) != 0)?number_format($result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'],2,".",","):"&nbsp;");
			$tpl->assign("porc",($result[$i]['porc_ganancia'] > 0)?number_format($result[$i]['porc_ganancia'],2,".",","):"&nbsp;");
			$tpl->assign("abono",($result[$i]['abono'] > 0)?number_format($result[$i]['abono'],2,".",","):"&nbsp;");
			$tpl->assign("devolucion",($result[$i]['devolucion'] > 0)?number_format($result[$i]['devolucion'],2,".",","):"&nbsp;");
			$tpl->assign("saldo_actual",($result[$i]['rezago'] > 0)?number_format($result[$i]['rezago'],2,".",","):"&nbsp;");
			
			$pan_p_venta += $result[$i]['pan_p_venta'];
			$pan_p_expendio += $result[$i]['pan_p_expendio'];
			$abono += $result[$i]['abono'];
			$devolucion += $result[$i]['devolucion'];
			$rezago_anterior += $result[$i]['rezago_anterior'];
			$rezago_actual += $result[$i]['rezago'];
			$porc += $result[$i]['porc_ganancia'];
			$diferencia += $result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'];
		}
	}
	
	$tpl->assign("saldos.saldo_anterior",number_format($rezago_anterior,2,".",","));
	$tpl->assign("saldos.precio_venta",number_format($pan_p_venta,2,".",","));
	$tpl->assign("saldos.precio_exp",number_format($pan_p_expendio,2,".",","));
	$tpl->assign("saldos.diferencia",number_format($diferencia,2,".",","));
	$tpl->assign("saldos.abono",number_format($abono,2,".",","));
	$tpl->assign("saldos.devolucion",number_format($devolucion,2,".",","));
	$tpl->assign("saldos.saldo_actual",number_format($rezago_actual,2,".",","));

	// Imprimir el resultado
	$tpl->printToScreen();
}
// --------------------------------------------------------------------------------------------------------

// ---------------------------------- Listado acumulado -----------------------------------------------------
if ($_GET['tipo'] == "movimientos") {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
	
	$fecha1 = "1/$fecha[2]/$fecha[3]";
	$fecha2 = $_GET['fecha'];
	
	// Obtener registros
	$sql = "SELECT fecha,SUM(pan_p_venta) AS precio_venta,SUM(pan_p_expendio) AS precio_expendio,AVG(porc_ganancia) AS porc,SUM(abono) AS abono FROM mov_expendios WHERE num_cia = $_GET[num_cia] AND fecha >= '$fecha1' AND fecha <= '$fecha2' GROUP BY fecha ORDER BY fecha ASC";
	$result = ejecutar_script($sql,$dsn);
	
	// Si no hay resultados
	if (!$result) {
		header("location: ./pan_exp_con.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock("movimientos");
	
	// Asignar valores a encabezado
	$tpl->assign("num_cia",$_GET['num_cia']);			// Asignar número de compañía
	$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);	// Asignar nombre de compañía
	$tpl->assign("dia",$fecha[1]);						// Asignar día
	$tpl->assign("anio",$fecha[3]);						// Asignar año
	switch ($fecha[2]) {
		case 1:  $mes = "Enero";		break;
		case 2:  $mes = "Febrero";		break;
		case 3:  $mes = "Marzo";		break;
		case 4:  $mes = "Abril";		break;
		case 5:  $mes = "Mayo";			break;
		case 6:  $mes = "Junio";		break;
		case 7:  $mes = "Julio";		break;
		case 8:  $mes = "Agosto";		break;
		case 9:  $mes = "Septiembre";	break;
		case 10: $mes = "Octubre";		break;
		case 11: $mes = "Noviembre";	break;
		case 12: $mes = "Diciembre";	break;
	}
	$tpl->assign("mes",$mes);						// Asignar mes
	
	$precio_venta = 0;
	$precio_expendio = 0;
	$diferencia = 0;
	$porc = 0;
	$abono = 0;
	
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("dia");
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[$i]['fecha'],$dia);
		
		$tpl->assign("dia",(int)$dia[1]);
		$tpl->assign("precio_venta",number_format($result[$i]['precio_venta'],2,".",","));
		$tpl->assign("precio_expendio",number_format($result[$i]['precio_expendio'],2,".",","));
		$tpl->assign("diferencia",number_format($result[$i]['precio_venta']-$result[$i]['precio_expendio'],2,".",","));
		$tpl->assign("porc",number_format($result[$i]['porc'],0,"",""));
		$tpl->assign("abono",number_format($result[$i]['abono'],2,".",","));
		
		$precio_venta += $result[$i]['precio_venta'];
		$precio_expendio += $result[$i]['precio_expendio'];
		$diferencia += $result[$i]['precio_venta']-$result[$i]['precio_expendio'];
		$porc += $result[$i]['porc'];
		$abono += $result[$i]['abono'];
	}
	$tpl->gotoBlock("movimientos");
	// Totales
	$tpl->assign("precio_venta",number_format($precio_venta,2,".",","));
	$tpl->assign("precio_expendio",number_format($precio_expendio,2,".",","));
	$tpl->assign("diferencia",number_format($diferencia,2,".",","));
	$tpl->assign("porc",number_format(($precio_venta-$precio_expendio)*100/$precio_venta,2,".",""));
	$tpl->assign("abono",number_format($abono,2,".",","));
	// Promedios
	$tpl->assign("prom_venta",number_format($precio_venta/$i,2,".",","));
	$tpl->assign("prom_expendio",number_format($precio_expendio/$i,2,".",","));
	$tpl->assign("prom_diferencia",number_format(($diferencia)/$i,2,".",","));
	$tpl->assign("prom_porc",number_format(($precio_venta-$precio_expendio)*100/$precio_venta,2,".",""));
	$tpl->assign("prom_abono",number_format($abono/$i,2,".",","));
	
	$tpl->printToScreen();
}
?>