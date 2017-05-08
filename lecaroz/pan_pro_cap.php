<?php
// CAPTURA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1221); // ID de pantalla

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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "Fecha de captura ya se encuentra en el sistema";
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

// -------------------------------- Capturar compañía -------------------------------------------------------
if (!isset($_GET['compania'])) {
	if (isset($_SESSION['pro'])) unset($_SESSION['pro']);
	
	$tpl->newBlock("obtener_compania");
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	
	// Obtener compañías por capturista
	if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia <= 300 OR num_cia IN (702,703)) ORDER BY num_cia";
	else
		$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (702,703) ORDER BY num_cia";
	$num_cia = ejecutar_script($sql,$dsn);
	
	for ($i=0; $i<count($num_cia); $i++) {
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$num_cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$num_cia[$i]['nombre_corto']);
	}
	
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

// ------------------------------- Capturar producción ------------------------------------------------------
// Verificar si existe la compañía
if (!existe_registro("catalogo_companias", array("num_cia"), array($_GET['compania']), $dsn)) {
	header("location: ./pan_pro_cap.php?codigo_error=1");
	die();
}

$ultima_fecha = ejecutar_script("SELECT fecha_total FROM total_produccion WHERE numcia=$_GET[compania] ORDER BY fecha_total DESC LIMIT 1",$dsn);
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fecha[0]['fecha_total'],$temp);
$fecha = date("d/m/Y",mktime(0,0,0,$temp[2],$temp[1]+1,$temp[3]));
$pieces = explode('/', $fecha);

// Obtener datos de compañía, turnos, control de producción y productos
$compania  = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['compania']),"","",$dsn);
$turnos    = obtener_registro("catalogo_turnos",array(),array(),"cod_turno","ASC",$dsn);
//$productos = obtener_registro("catalogo_productos",array(),array(),"cod_producto","ASC",$dsn);

// MOD. 16/Mar/2006
/*if ($compania[0]['med_agua'] == "t") {
	$sql = "SELECT fecha FROM medidor_agua WHERE num_cia = $_GET[compania] ORDER BY fecha DESC LIMIT 1";
	$med_agua = ejecutar_script($sql, $dsn);
	
	if (!$med_agua) {
		$tpl->newBlock("agua");
		$tpl->printToScreen();
		die;
	}
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $med_agua[0]['fecha'], $fecha_agua);
	
	$limit_ts = mktime(0, 0, 0, date("n"), date("d") - 10, date("Y"));
	$agua_ts = mktime(0, 0, 0, $fecha_agua[2], $fecha_agua[1], $fecha_agua[3]);
	
	if ($agua_ts < $limit_ts) {
		$tpl->newBlock("agua");
		$tpl->printToScreen();
		die;
	}
}*/

// Crear hoja de captura
$tpl->newBlock("hoja");

// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Asignar valores a los campos del formulario
// Poner compañía
$tpl->assign("num_cia",$_GET['compania']);
$tpl->assign("nombre_cia",$compania[0]['nombre_corto']);
// Poner Fecha
$tpl->assign("fecha",$fecha);

// ------------------------- Generar turnos -------------------------
// Total de productos
$result = ejecutar_script("SELECT count(cod_producto) FROM control_produccion WHERE num_cia=$_GET[compania]",$dsn);
$total_filas = $result[0]['count'];
$filas = 0;
$totales = 0;
$importe_raya_ganada_total = 0;
$importe_raya_pagada_total = 0;
$importe_produccion_total = 0;
for ($i=0; $i<count($turnos); $i++) {
	if ($turnos[$i]['cod_turno'] != 5 && $turnos[$i]['cod_turno'] != 6 && $turnos[$i]['cod_turno'] != 7) {
		$control = obtener_registro("control_produccion",array("num_cia","cod_turno"),array($_GET['compania'],$turnos[$i]['cod_turno']),"num_orden","ASC",$dsn);
		
		// Obtener totales del día anterior
		if (!$dia_anterior = ejecutar_script("SELECT SUM(raya_ganada)-SUM(raya_pagada) AS raya_debida FROM total_produccion WHERE numcia=$_GET[compania] AND codturno=".$turnos[$i]['cod_turno']." and fecha_total>='".date("d/m/Y",mktime(0,0,0,intval($pieces[1], 10)-1,1,intval($pieces[2], 10)))."' and fecha_total<='".date("d/m/Y",mktime(0,0,0,intval($pieces[1],10),intval($pieces[0],10),intval($pieces[2],10)))."'",$dsn))
			$raya_debida = 0;
		else
			$raya_debida = $dia_anterior[0]['raya_debida'];
		if ($control != FALSE) {
			// Crear nuevo bloque para el turno en uso
			$tpl->newBlock("turno");
			$tpl->assign("turno",$turnos[$i]['descripcion']);
			
			$importe_raya_turno = 0;
			$importe_produccion_turno = 0;
			// Crear filas de productos para el turno
			for ($j=0; $j<count($control); $j++) {
				if ($control[$j]['precio_raya'] != "" || $control[$j]['precio_venta'] != "" || $control[$j]['porc_raya'] != "") {
					$tpl->newBlock("fila");
					$tpl->assign("i",$filas);
					$tpl->assign("id",$control[$j]['idcontrol_produccion']);
					$tpl->assign("cod_producto",$control[$j]['cod_producto']);
					$tpl->assign("cod_turnos",$turnos[$i]['cod_turno']);
					$tpl->assign("compania",$_GET['compania']);
					$tpl->assign("fecha",$fecha);
					$productos = obtener_registro("catalogo_productos",array("cod_producto"),array($control[$j]['cod_producto']),"","",$dsn);
					$tpl->assign("nombre",$productos[0]['nombre']);
					// Si 'raya' maneja precio en lugar de porcentaje...
					if ($control[$j]['precio_raya'] > 0 && ($control[$j]['porc_raya'] == "" || $control[$j]['porc_raya'] == 0)) {
						$tpl->newBlock("piezas_precio");
							$tpl->assign("i",$filas);
							
							if (isset($_SESSION['pro'][$control[$j]['idcontrol_produccion']])) {
								$tpl->assign("piezas",$_SESSION['pro'][$control[$j]['idcontrol_produccion']]);
								$importe_raya = $_SESSION['pro'][$control[$j]['idcontrol_produccion']] * $control[$j]['precio_raya'];
								$importe_produccion = $_SESSION['pro'][$control[$j]['idcontrol_produccion']] * $control[$j]['precio_venta'];//echo "$importe_raya $importe_produccion<br>";
								$tpl->gotoBlock("fila");
								$tpl->assign("importe_raya",number_format($importe_raya,2,".",""));
								$tpl->assign("importe_produccion",number_format($importe_produccion,2,".",""));
								$tpl->gotoBlock("piezas_precio");
								$importe_raya_turno += $importe_raya;
								$importe_produccion_turno += $importe_produccion;
							}
							
							if ($filas < $total_filas-1)
								$tpl->assign("next",$filas+1);
							else
								$tpl->assign("next",0);
							
							if ($filas >= 1)
								$tpl->assign("back",$filas-1);
							else
								$tpl->assign("back",$total_filas-1);
							$tpl->assign("tot",$totales);
							$tpl->gotoBlock("fila");
						$tpl->newBlock("precio_raya");
							$tpl->assign("i",$filas);
							$tpl->assign("precio_raya_for",number_format($control[$j]['precio_raya'],4,".",","));
							$tpl->assign("precio_raya",$control[$j]['precio_raya']);
							$tpl->gotoBlock("fila");
					}
					// Si 'raya' maneja porcentaje en lugar de precio...
					else if (($control[$j]['precio_raya'] == "" || $control[$j]['precio_raya'] == 0) && $control[$j]['porc_raya'] > 0) {
						$tpl->newBlock("piezas_porc");
							$tpl->assign("i",$filas);
							
							if (isset($_SESSION['pro'][$control[$j]['idcontrol_produccion']])) {
								$tpl->assign("piezas",$_SESSION['pro'][$control[$j]['idcontrol_produccion']]);
								$importe_produccion = $_SESSION['pro'][$control[$j]['idcontrol_produccion']] * $control[$j]['precio_venta'];
								$importe_raya = $importe_produccion * ($control[$j]['porc_raya']/100);//echo "$importe_raya $importe_produccion<br>";
								$tpl->gotoBlock("fila");
								$tpl->assign("importe_raya",number_format($importe_raya,2,".",""));
								$tpl->assign("importe_produccion",number_format($importe_produccion,2,".",""));
								$tpl->gotoBlock("piezas_porc");
								$importe_raya_turno += $importe_raya;
								$importe_produccion_turno += $importe_produccion;
							}
							
							if ($filas < $total_filas-1)
								$tpl->assign("next",$filas+1);
							else
								$tpl->assign("next",0);
							
							if ($filas >= 1)
								$tpl->assign("back",$filas-1);
							else
								$tpl->assign("back",$total_filas-1);
							$tpl->assign("tot",$totales);
							$tpl->gotoBlock("fila");
						$tpl->newBlock("porc_raya");
							$tpl->assign("i",$filas);
							$tpl->assign("porc_raya_for",number_format($control[$j]['porc_raya'],2,".",""));
							$tpl->assign("porc_raya",$control[$j]['porc_raya']);
							$tpl->gotoBlock("fila");
					}
					// Si 'raya' no maneja precio o porcentaje
					else if ($control[$j]['precio_raya'] == 0 && $control[$j]['porc_raya'] == 0) {
						$tpl->newBlock("piezas_porc");
							$tpl->assign("i",$filas);
							
							if (isset($_SESSION['pro'][$control[$j]['idcontrol_produccion']])) {
								$tpl->assign("piezas",$_SESSION['pro'][$control[$j]['idcontrol_produccion']]);
								$importe_produccion = $_SESSION['pro'][$control[$j]['idcontrol_produccion']] * $control[$j]['precio_venta'];
								$importe_raya = 0;
								$tpl->gotoBlock("fila");
								$tpl->assign("importe_raya",number_format($importe_raya,2,".",""));
								$tpl->assign("importe_produccion",number_format($importe_produccion,2,".",""));
								$tpl->gotoBlock("piezas_porc");
								$importe_raya_turno += $importe_raya;
								$importe_produccion_turno += $importe_produccion;
							}
							
							if ($filas < $total_filas-1)
								$tpl->assign("next",$filas+1);
							else
								$tpl->assign("next",0);
							
							if ($filas >= 1)
								$tpl->assign("back",$filas-1);
							else
								$tpl->assign("back",$total_filas-1);
							$tpl->assign("tot",$totales);
							$tpl->gotoBlock("fila");
						$tpl->newBlock("porc_raya");
							$tpl->assign("i",$filas);
							$tpl->assign("porc_raya_for",number_format(0,2,".",""));
							$tpl->assign("porc_raya",0);
							$tpl->gotoBlock("fila");
					}
					if ($control[$j]['precio_venta'] > 0){
						$tpl->assign("precio_venta",$control[$j]['precio_venta']);
						$tpl->assign("precio_venta_for",number_format($control[$j]['precio_venta'],3,".",","));
					}
					else {
						$tpl->assign("precio_venta",0);
						$tpl->assign("precio_venta_for",number_format(0,3,".",","));
					}
					$filas++;
					$tpl->gotoBlock("turno");
				}
			}
			$tpl->newBlock("totales");
			$tpl->assign("i",$totales);
			
			if ($filas < $total_filas-1)
				$tpl->assign("next",$filas+1);
			else
				$tpl->assign("next",0);
			
			if ($filas >= 1)
				$tpl->assign("back",$filas-1);
			else
				$tpl->assign("back",$total_filas-1);
			
			$tpl->assign("raya_debida",$raya_debida);
			$tpl->assign("fecha",$fecha);
			$tpl->assign("turno",$turnos[$i]['cod_turno']);
			$tpl->assign("cia",$_GET['compania']);
			//$tpl->gotoBlock("fila");
			$totales++;
			
			if (isset($_SESSION['pro'])) {
				$importe_produccion_total += $importe_produccion_turno;
				if (isset($_SESSION['pro']['raya_pagada'.$i]) && $_SESSION['pro']['raya_pagada'.$i] != $importe_raya_turno) {
					$tpl->assign("raya_pagada",number_format($_SESSION['pro']['raya_pagada'.$i],2,".",""));
					$importe_raya_pagada_total += $_SESSION['pro']['raya_pagada'.$i];
					$importe_raya_ganada_total += $importe_raya_turno;
				}
				else {
					$tpl->assign("raya_pagada",number_format($importe_raya_turno,2,".",""));
					$importe_raya_ganada_total += $importe_raya_turno;
					$importe_raya_pagada_total += $importe_raya_turno;
				}
				$tpl->assign("importe_raya_turno",number_format($importe_raya_turno,2,".",""));
				$tpl->assign("importe_produccion_turno",number_format($importe_produccion_turno,2,".",""));
			}
			else {
				$tpl->assign("importe_raya_turno","0.00");
				$tpl->assign("importe_produccion_turno","0.00");
				$tpl->assign("raya_pagada","0.00");
			}
		}
	}
}

//if (isset($_POST['pro'])) {
	$tpl->gotoBlock("hoja");
	$tpl->assign("importe_raya_ganada_total",number_format($importe_raya_ganada_total,2,".",""));
	$tpl->assign("importe_raya_pagada_total",number_format($importe_raya_pagada_total,2,".",""));
	$tpl->assign("importe_produccion_total",number_format($importe_produccion_total,2,".",""));
//}
/*else {
	$tpl->gotoBlock("hoja");
	$tpl->assign("importe_raya_ganada_total","0.00");
	$tpl->assign("importe_raya_pagada_total","0.00");
	$tpl->assign("importe_produccion_total","0.00");
}*/

// Imprimir el resultado
$tpl->printToScreen();
?>