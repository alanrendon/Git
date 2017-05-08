<?php
// ver_pan_exp_cap.php
// Script encargado de ordenar y evaluar los movimientos de expendios

include 'DB.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/class.db3.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$tabla = $_GET['tabla'];

switch ($tabla) {
	case "mov_expendios":
		$ok      = TRUE;
		$numcols = 5;
		$numrows = $_POST['numfilas'];
		$count   = 0;

		$tpl = new TemplatePower("./plantillas/header.tpl");
		
		// Incluir el cuerpo del documento
		$tpl->assignInclude("body","./plantillas/pan/ver_pan_exp_cap.tpl");
		$tpl->prepare();
		
		// Seleccionar script para menu
		$tpl->newBlock("menu");
		$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
		$tpl->gotoBlock("_ROOT");
		
		// Crear nuevo bloque de hoja
		$tpl->newBlock("hoja");
		
		// Seleccionar tabla
		$tpl->assign("tabla","mov_expendios");

		// ------------------------------------------ VALIDAR Y ORDENAR DATOS ---------------------------------------------
		// Consultar si existe la compañía...
		$compania = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['compania']),"","",$dsn);
		
		// Dibujar compañía y fecha
		$tpl->assign("compania",$_POST['compania']);
		$tpl->assign("nombre_cia",$compania[0]['nombre_corto']);
		$tpl->assign("fecha",$_POST['fecha']);
		$tpl->assign("num_cia",$_POST['compania']);
	
		// Almacenar datos en variables de sesion
		if (isset($_SESSION['exp'])) unset($_SESSION['exp']);
		for ($i=0; $i<$numrows; $i++) {
			$_SESSION['exp']['pan_p_venta'.$i]    = $_POST['pan_p_venta'.$i];
			$_SESSION['exp']['devolucion'.$i]     = $_POST['devolucion'.$i];
			$_SESSION['exp']['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i];
			$_SESSION['exp']['abono'.$i]          = $_POST['abono'.$i];
		}
		
		
		// Si existe número de compañía...
		for ($i=0;$i<$numrows;$i++) {
			// Obtener datos del expendio
			$expendios = obtener_registro("catalogo_expendios",array("num_cia","num_expendio"),array($_POST['compania'],$_POST['num_expendio'.$i]),"","",$dsn);
			
			// Ordenar datos para inserción
			$nombre_exp[$i]           = $_POST['nombre'.$i];		// Almacena el nombre del expendio
			$datos['num_cia'.$i]      = $_POST['compania'];			// Campo num_cia
			$datos['fecha'.$i]        = $_POST['fecha'];			// Campo fecha
			$datos['num_expendio'.$i] = $_POST['num_expendio'.$i];	// Campo num_expendio
			$datos['nombre_exp'.$i]   = $_POST['nombre'.$i];
			
			$tpl->newBlock("rows");
			$tpl->assign("i",$i);
			$tpl->assign("num_cia",         $datos['num_cia'.$i]);
			$tpl->assign("fecha",           $datos['fecha'.$i]);
			$tpl->assign("num_expendio",    $datos['num_expendio'.$i]);
			$tpl->assign("nombre_expendio", $nombre_exp[$i]);
			
			// Campo 'pan_p_venta'
			if ($_POST['pan_p_venta'.$i] == "") {
				$datos['pan_p_venta'.$i]  = 0;							// Campo pan_p_venta
				$tpl->assign("pan_p_venta",$datos['pan_p_venta'.$i]);
				$tpl->newBlock("pan_p_venta_ok");
				$tpl->assign("pan_p_venta_for",number_format($datos['pan_p_venta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if ($_POST['pan_p_venta'.$i] < 0) {					// No se admiten valores negativos
				$datos['pan_p_venta'.$i] = $_POST['pan_p_venta'.$i];
				$tpl->assign("pan_p_venta",$datos['pan_p_venta'.$i]);
				$tpl->newBlock("pan_p_venta_error");
				$tpl->assign("pan_p_venta_for",number_format($datos['pan_p_venta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else {
				$datos['pan_p_venta'.$i]  = $_POST['pan_p_venta'.$i];	// Campo pan_p_venta
				$tpl->assign("pan_p_venta",$datos['pan_p_venta'.$i]);
				$tpl->newBlock("pan_p_venta_ok");
				$tpl->assign("pan_p_venta_for",number_format($datos['pan_p_venta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
			// Campo 'abono'
			if ($_POST['abono'.$i] == "") {
				$datos['abono'.$i] = 0;									// Campo abono
				$tpl->assign("abono",$datos['abono'.$i]);
				$tpl->newBlock("abono_ok");
				$tpl->assign("abono_for",number_format($datos['abono'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if ($_POST['abono'.$i] < 0) {							// No se admiten valores negativos
				$datos['abono'.$i] = $_POST['abono'.$i];
				$tpl->assign("abono",$datos['abono'.$i]);
				$tpl->newBlock("abono_error");
				$tpl->assign("abono_for",number_format($datos['abono'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else {
				$datos['abono'.$i] = $_POST['abono'.$i]; 				// Campo abono
				$tpl->assign("abono",$datos['abono'.$i]);
				$tpl->newBlock("abono_ok");
				$tpl->assign("abono_for",number_format($datos['abono'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
			// 'pan_p_expendio' debe ser igual o mayor al porcentaje
			// de ganancia de 'pan_p_venta'
			
			// Obtener tope de ganancia del expendio
			if ($expendios[0]['porciento_ganancia'] >= 0 && $expendios[0]['importe_fijo'] == "") {
				$tope = ($datos['pan_p_venta'.$i]*(100-$expendios[0]['porciento_ganancia'])/100) - 0.30;
				$tpl->assign("porc_ganancia",$expendios[0]['porciento_ganancia']);
			}
			else if ($expendios[0]['porciento_ganancia'] == "" && $expendios[0]['importe_fijo'] >= 0) {
				$tope = $datos['pan_p_venta'.$i] - $expendios[0]['importe_fijo'] - 0.30;
			}
			
			if ($datos['pan_p_venta'.$i] == 0 && $_POST['pan_p_expendio'.$i] > 0) {
				$datos['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i];
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_error");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else if ($datos['pan_p_venta'.$i] < $_POST['pan_p_expendio'.$i]) {
				$datos['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i];
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_error");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else if (($_POST['pan_p_expendio'.$i] == "" || $_POST['pan_p_expendio'.$i] == 0) && $datos['pan_p_venta'.$i] >= 0) {
				$datos['pan_p_expendio'.$i] = 0;
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_ok");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if (($_POST['pan_p_expendio'.$i] > 0 && $datos['pan_p_venta'.$i] > 0) && $_POST['pan_p_expendio'.$i] >= /*(($datos['pan_p_venta'.$i]*(100-$expendios[0]['porciento_ganancia'])/100) - 0.30)*/$tope) {
				$datos['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i]; // Campo pan_p_expendio
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_ok");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if (($_POST['pan_p_expendio'.$i] > 0 && $datos['pan_p_venta'.$i] > 0) && $expendios[0]['porciento_ganancia'] == 0 && $_POST['pan_p_expendio'.$i] <= $tope) {
				$datos['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i]; // Campo pan_p_expendio
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_ok");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else {
				$datos['pan_p_expendio'.$i] = $_POST['pan_p_expendio'.$i];
				$tpl->assign("pan_p_expendio",$datos['pan_p_expendio'.$i]);
				$tpl->newBlock("pan_p_expendio_error");
				$tpl->assign("pan_p_expendio_for",number_format($datos['pan_p_expendio'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			
			// 'devolucion' debe ser menor a 'abono'
			
			/* Caso especial compañía 71 (modificación hecha el 28/07/2005) */
			if ($datos['num_cia'.$i] == 71 && (($datos['num_expendio'.$i] == 3 && $datos['nombre_exp'.$i] == "AGRICOLA S/D") || ($datos['num_expendio'.$i] == 4 && $datos['nombre_exp'.$i] == "ISABEL LA CATOLICA S/D")) && (($datos['abono'.$i] <= 0 && $_POST['devolucion'.$i] > 0) || ($datos['abono'.$i] > 0 && $_POST['devolucion'.$i] > 0))) {
				$datos['devolucion'.$i] = $_POST['devolucion'.$i]; // Campo devolucion
				$tpl->assign("devolucion",$datos['devolucion'.$i]);
				$tpl->newBlock("devolucion_ok");
				$tpl->assign("devolucion_for",number_format($datos['devolucion'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if ($datos['abono'.$i] <= 0 && $_POST['devolucion'.$i] > 0) {
				$datos['devolucion'.$i] = $_POST['devolucion'.$i];
				$tpl->assign("devolucion",$datos['devolucion'.$i]);
				$tpl->newBlock("devolucion_error");
				$tpl->assign("devolucion_for",number_format($datos['devolucion'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else if (($_POST['devolucion'.$i] == "" || $_POST['devolucion'.$i] == 0) && $datos['abono'.$i] >= 0) {
				$datos['devolucion'.$i] = 0;
				$tpl->assign("devolucion",$datos['devolucion'.$i]);
				$tpl->newBlock("devolucion_ok");
				$tpl->assign("devolucion_for",number_format($datos['devolucion'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if (($_POST['devolucion'.$i] > 0 && $datos['abono'.$i] > 0) && $_POST['devolucion'.$i] < $datos['abono'.$i]) {
				$datos['devolucion'.$i] = $_POST['devolucion'.$i]; // Campo devolucion
				$tpl->assign("devolucion",$datos['devolucion'.$i]);
				$tpl->newBlock("devolucion_ok");
				$tpl->assign("devolucion_for",number_format($datos['devolucion'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else {
				$datos['devolucion'.$i] = $_POST['devolucion'.$i];
				$tpl->assign("devolucion",$datos['devolucion'.$i]);
				$tpl->newBlock("devolucion_error");
				$tpl->assign("devolucion_for",number_format($datos['devolucion'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}

			// Consultar rezago anterior y actualizarlo   ***************** CORREGIR PARA QUE SOLO OBTENGA UN REGISTRO ****************
			$rezago = obtener_registro($tabla,array("num_cia","num_expendio"),array($_POST['compania'],$_POST['num_expendio'.$i]),"fecha","DESC",$dsn);
			$new_rezago = number_format($rezago[0]['rezago'] + $datos['pan_p_expendio'.$i] - $datos['abono'.$i] - $datos['devolucion'.$i],2,'.','');
			$datos['rezago'.$i] = $new_rezago; // Campo rezago = new_rezago
			$tpl->assign("rezago",$datos['rezago'.$i]);
			if ($datos['rezago'.$i] < 0) {
				$tpl->newBlock("rezago_error");
				$tpl->assign("rezago_for",number_format($datos['rezago'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
				$ok = FALSE;
			}
			else if ($datos['rezago'.$i] >= 0) {
				$tpl->newBlock("rezago_ok");
				$tpl->assign("rezago_for",number_format($datos['rezago'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else if ($datos['rezago'.$i] == 0) {
				$tpl->newBlock("rezago_ok");
				$tpl->assign("rezago_for",number_format($datos['rezago'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}

			$tpl->assign("rezagoant_for",number_format($rezago[0]['rezago'],2,'.',','));
			$tpl->assign("rezago_anterior",number_format($rezago[0]['rezago'],2,".",""));
		}
		
		// Sumar totales
		$total_pan_venta  = 0;
		$total_pan_exp    = 0;
		$total_abono      = 0;
		$total_devolucion = 0;
		$total_rezago     = 0;

		for ($i=0;$i<$numrows;$i++) {
			$total_pan_venta  += $datos['pan_p_venta'.$i];
			$total_pan_exp    += $datos['pan_p_expendio'.$i];
			$total_abono      += $datos['abono'.$i];
			$total_devolucion += $datos['devolucion'.$i];
			$total_rezago     += $datos['rezago'.$i];
		}
		
		// Dibujar totales
		$tpl->newBlock("totales");
		$tpl->assign("pan_p_venta",    number_format($total_pan_venta,2,'.',','));
		$tpl->assign("pan_p_expendio", number_format($total_pan_exp,2,'.',','));
		$tpl->assign("num_cia",        $_POST['compania']);
		$tpl->assign("fecha",          $_POST['fecha']);
		$tpl->assign("post_abono",     number_format($total_abono,2,'.',''));
		$tpl->assign("abono",          number_format($total_abono,2,'.',','));
		$tpl->assign("devolucion",     number_format($total_devolucion,2,'.',','));
		$tpl->assign("rezago",         number_format($total_rezago,2,'.',','));
		
		// *** EFECTIVO ***
		// 
		
		if ($ok)
			$tpl->newBlock("enviar");
			
		$tpl->gotoBlock("_ROOT");
		
		// Imprimir el resultado
		$tpl->printToScreen();
	break;
}
?>