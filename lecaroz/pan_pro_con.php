<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1241); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
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
if ($_GET['tipo_consulta'] == "dia") {
	// Obtener registros
	$sql = "SELECT * FROM produccion WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]'" . ($_GET['codpro'] > 0 ? " AND cod_producto = $_GET[codpro]" : '') . " ORDER BY cod_turnos,cod_producto ASC";
	$result = ejecutar_script($sql,$dsn);
	
	// Si no hay resul
	if (!$result) {
		header("location: ./pan_pro_con.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock("listado_dia");
	
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

	$raya_ganada = 0;
	$raya_pagada = 0;
	$total_produccion = 0;
	
	$turno = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($turno != $result[$i]['cod_turnos'] && $result[$i]['piezas'] > 0) {
			if ($turno != NULL) {
				$sql = "SELECT * FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total = '$_GET[fecha]' AND codturno = $turno";
				$total = ejecutar_script($sql,$dsn);
				
				$tpl->assign("turno.total_raya_ganada",number_format($total[0]['raya_ganada'],2,".",","));
				$tpl->assign("turno.total_raya_pagada",number_format($total[0]['raya_pagada'],2,".",","));
				$tpl->assign("turno.total_produccion",number_format($total[0]['total_produccion'],2,".",","));
				
				$raya_ganada += $total[0]['raya_ganada'];
				$raya_pagada += $total[0]['raya_pagada'];
				$total_produccion += $total[0]['total_produccion'];
			}
			
			$turno = $result[$i]['cod_turnos'];
			
			$tpl->newBlock("turno");
			$nombre_turno = ejecutar_script("SELECT descripcion FROM catalogo_turnos WHERE cod_turno = ".$result[$i]['cod_turnos'],$dsn);
			$tpl->assign("turno",$nombre_turno[0]['descripcion']);
		}
		if ($result[$i]['piezas'] > 0) {
			$tpl->newBlock("fila");
			$tpl->assign("cod_producto",$result[$i]['cod_producto']);
			$nombre_producto = ejecutar_script("SELECT nombre FROM catalogo_productos WHERE cod_producto = ".$result[$i]['cod_producto'],$dsn);
			$tpl->assign("nombre",$nombre_producto[0]['nombre']);
			$tpl->assign("piezas",number_format($result[$i]['piezas'],2,".",","));
			$tpl->assign("raya_ganada",($result[$i]['imp_raya'] > 0)?number_format($result[$i]['imp_raya'],2,".",","):"&nbsp;");
			$tpl->assign("produccion",($result[$i]['imp_produccion'] > 0)?number_format($result[$i]['imp_produccion'],2,".",","):"&nbsp;");
			$tpl->assign("precio_raya",($result[$i]['precio_raya'] > 0)?number_format($result[$i]['precio_raya'],4,".",","):"&nbsp;");
			$tpl->assign("porcentaje_raya",($result[$i]['porc_raya'] > 0)?"% ".number_format($result[$i]['porc_raya'],2,".",","):"&nbsp;");
			$tpl->assign("precio_venta",($result[$i]['precio_venta'] > 0)?number_format($result[$i]['precio_venta'],3,".",","):"&nbsp;");
		}
	}
	if ($turno != NULL) {
		$sql = "SELECT * FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total = '$_GET[fecha]' AND codturno = $turno";
		$total = ejecutar_script($sql,$dsn);
		
		$tpl->assign("turno.total_raya_ganada",number_format($total[0]['raya_ganada'],2,".",","));
		$tpl->assign("turno.total_raya_pagada",number_format($total[0]['raya_pagada'],2,".",","));
		$tpl->assign("turno.total_produccion",number_format($total[0]['total_produccion'],2,".",","));
		
		$raya_ganada += $total[0]['raya_ganada'];
		$raya_pagada += $total[0]['raya_pagada'];
		$total_produccion += $total[0]['total_produccion'];
	}
	
	$tpl->assign("listado_dia.total_raya_ganada",number_format($raya_ganada,2,".",","));
	$tpl->assign("listado_dia.total_raya_pagada",number_format($raya_pagada,2,".",","));
	$tpl->assign("listado_dia.total_produccion",number_format($total_produccion,2,".",","));

	// Imprimir el resultado
	$tpl->printToScreen();
}
// --------------------------------------------------------------------------------------------------------

// ---------------------------------- Listado acumulado -----------------------------------------------------
if ($_GET['tipo_consulta'] == "acumulado") {
	// Desglozar fecha de corte
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$fecha);
	$fecha1 = "1/$fecha[2]/$fecha[3]";
	$fecha2 = $_GET['fecha'];
	
	// Obtener registros
	$sql = "SELECT cod_producto,cod_turnos,SUM(piezas) AS piezas,SUM(imp_raya) AS imp_raya,SUM(imp_produccion) AS imp_produccion,precio_raya,porc_raya,precio_venta FROM produccion WHERE num_cia = $_GET[num_cia] AND fecha >= '$fecha1' AND fecha <= '$fecha2'" . ($_GET['codpro'] > 0 ? " AND cod_producto = $_GET[codpro]" : '') . " GROUP BY cod_turnos,cod_producto,precio_raya,porc_raya,precio_venta ORDER BY cod_turnos,cod_producto";
	$result = ejecutar_script($sql,$dsn);
	
	// Si no hay resul
	if (!$result) {
		header("location: ./pan_pro_con.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock("listado_acumulado");
	
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

	$raya_ganada = 0;
	$raya_pagada = 0;
	$total_produccion = 0;
	
	$turno = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($turno != $result[$i]['cod_turnos'] && $result[$i]['piezas'] > 0) {
			if ($turno != NULL) {
				$sql = "SELECT SUM(raya_ganada) AS raya_ganada,SUM(raya_pagada) AS raya_pagada,SUM(total_produccion) AS total_produccion FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total >= '$fecha1' AND fecha_total <= '$fecha2' AND codturno = $turno";
				$total = ejecutar_script($sql,$dsn);
				
				$tpl->assign("turno_acu.total_raya_ganada",number_format($total[0]['raya_ganada'],2,".",","));
				$tpl->assign("turno_acu.total_raya_pagada",number_format($total[0]['raya_pagada'],2,".",","));
				$tpl->assign("turno_acu.total_produccion",number_format($total[0]['total_produccion'],2,".",","));
				
				$raya_ganada += $total[0]['raya_ganada'];
				$raya_pagada += $total[0]['raya_pagada'];
				$total_produccion += $total[0]['total_produccion'];
			}
			
			$turno = $result[$i]['cod_turnos'];
			
			$tpl->newBlock("turno_acu");
			$nombre_turno = ejecutar_script("SELECT descripcion FROM catalogo_turnos WHERE cod_turno = ".$result[$i]['cod_turnos'],$dsn);
			$tpl->assign("turno",$nombre_turno[0]['descripcion']);
		}
		if ($result[$i]['piezas'] > 0) {
			$tpl->newBlock("fila_acu");
			$tpl->assign("cod_producto",$result[$i]['cod_producto']);
			$nombre_producto = ejecutar_script("SELECT nombre FROM catalogo_productos WHERE cod_producto = ".$result[$i]['cod_producto'],$dsn);
			$tpl->assign("nombre",$nombre_producto[0]['nombre']);
			$tpl->assign("piezas",number_format($result[$i]['piezas'],2,".",","));
			$tpl->assign("raya_ganada",($result[$i]['imp_raya'] > 0)?number_format($result[$i]['imp_raya'],2,".",","):"&nbsp;");
			$tpl->assign("produccion",($result[$i]['imp_produccion'] > 0)?number_format($result[$i]['imp_produccion'],2,".",","):"&nbsp;");
			$tpl->assign("precio_raya",($result[$i]['precio_raya'] > 0)?number_format($result[$i]['precio_raya'],4,".",","):"&nbsp;");
			$tpl->assign("porcentaje_raya",($result[$i]['porc_raya'] > 0)?"% ".number_format($result[$i]['porc_raya'],2,".",","):"&nbsp;");
			$tpl->assign("precio_venta",($result[$i]['precio_venta'] > 0)?number_format($result[$i]['precio_venta'],3,".",","):"&nbsp;");
		}
	}
	if ($turno != NULL) {
		$sql = "SELECT SUM(raya_ganada) AS raya_ganada,SUM(raya_pagada) AS raya_pagada,SUM(total_produccion) AS total_produccion FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total >= '$fecha1' AND fecha_total <= '$fecha2' AND codturno = $turno";
		$total = ejecutar_script($sql,$dsn);
		
		$tpl->assign("turno_acu.total_raya_ganada",number_format($total[0]['raya_ganada'],2,".",","));
		$tpl->assign("turno_acu.total_raya_pagada",number_format($total[0]['raya_pagada'],2,".",","));
		$tpl->assign("turno_acu.total_produccion",number_format($total[0]['total_produccion'],2,".",","));
		
		$raya_ganada += $total[0]['raya_ganada'];
		$raya_pagada += $total[0]['raya_pagada'];
		$total_produccion += $total[0]['total_produccion'];
	}
	
	$tpl->assign("listado_acumulado.total_raya_ganada",number_format($raya_ganada,2,".",","));
	$tpl->assign("listado_acumulado.total_raya_pagada",number_format($raya_pagada,2,".",","));
	$tpl->assign("listado_acumulado.total_produccion",number_format($total_produccion,2,".",","));

	// Imprimir el resultado
	$tpl->printToScreen();
}
?>