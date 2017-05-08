<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

function mandar_mail($email, $mensaje) {
	// Recipiente(s)
	$to = $email;
	
	// Asunto
	$subject = "Hoja de Inventario";
	
	// Mensaje
	$message = $mensaje;
	
	// Cabeceras adicionales
	$headers = "From: Oficinas Mollendo <lecaroz@prodigy.net.mx>\r\n";
	
	// Para enviar un correo HTML, la cabecera de tipo de contenido debe ser inicializada
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	//$headers .= "Content-Type: application/download";
	//$headers .= "Content-Disposition: attachment; filename=inventarios.html";
	//$headers .= "MIME-Version: 1.0\r\n";
	
	// Enviar correo
	mail($to, $subject, $message, $headers);
}

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/bal/bal_ifm_mail.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$day = date("d");
	$month = date("n");
	$year = date("Y");
	$date = date("d/m/Y", mktime(0, 0, 0, $month - 1, 1, $year));
	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	$sql = "SELECT num_cia, nombre_corto, codmp, catalogo_mat_primas.nombre AS nombre_mp, tipo_unidad_consumo.descripcion AS unidad, tipo FROM inventario_real LEFT JOIN catalogo_mat_primas";
	$sql .= " USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia IN (";
	foreach ($cias as $i => $num_cia)
		$sql .= $num_cia . ($i < count($cias) - 1 ? ', ' : ')');
	$sql .= " AND ((num_cia, codmp) IN (SELECT num_cia, codmp FROM mov_inv_real WHERE fecha >= '$date' AND num_cia IN (";
	foreach ($cias as $i => $num_cia)
		$sql .= $num_cia . ($i < count($cias) - 1 ? ', ' : ')');
	$sql .= " GROUP BY num_cia, codmp) OR existencia != 0)";
	$sql .= " ORDER BY num_cia, tipo, catalogo_mat_primas.nombre";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_ifm_mail.php?codigo_error=1");
		die;
	}
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=inventarios.csv");
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				/*if ($numhojas % 2 != 0)
					$tpl->newBlock("salto_pagina");
				
				$tpl->newBlock("salto_pagina");*/
				echo "\n";
			}
			
			$num_cia = $reg['num_cia'];
			echo '"' . mes_escrito(date('n'), TRUE) . '",';
			echo "\"$num_cia\",\"$reg[nombre_corto]\"\n\n";
			/*$tpl->newBlock("cia");
			$tpl->assign("nombre_mes", mes_escrito(date("n"), TRUE));
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $reg['nombre_corto']);*/
			
			//$tpl->newBlock("hoja");
			
			$tipo = $reg['tipo'];
			$numhojas = 1;
			$numfilas_x_hoja = 38;
			$numfilas = 0;
		}
		/*if ($numfilas >= $numfilas_x_hoja) {
			$numfilas_x_hoja = 46;
			$numfilas = 0;
			$numhojas++;
			
			$tpl->newBlock("salto_pagina");
			
			//if ($numhojas % 2 == 0)
				//$tpl->newBlock("salto_hoja_par");
			
			$tpl->newBlock("hoja");
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $reg['nombre_corto']);
			$numfilas++;
		}*/
		//$tpl->newBlock("fila");
		if ($tipo != $reg['tipo']) {
			$tipo = $reg['tipo'];
			echo "\"EMPAQUE\"\n";
			//$tpl->newBlock("empaque");
			//$tpl->gotoBlock("fila");
			$numfilas++;
		}
		echo "\"$reg[codmp]\",\"$reg[nombre_mp]\",\"\",\"$reg[unidad]\"\n";
		//$tpl->assign("codmp", $reg['codmp']);
		//$tpl->assign("nombre_mp", $reg['nombre_mp']);
		//$tpl->assign("unidad", strtoupper($reg['unidad']));
		$numfilas++;
	}
	//$mensaje = $tpl->getOutputContent();
	
	//header("Content-Type: application/download");
	//header("Content-Disposition: attachment; filename=inventarios.html");
	//header("Content-Disposition: attachment; filename=inventarios.csv");
	//echo $mensaje;
	
	//mandar_mail($_GET['email'], $mensaje);
	//header("location: ./bal_ifm_mail.php");
	die;
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}
$tpl->printToScreen();
?>