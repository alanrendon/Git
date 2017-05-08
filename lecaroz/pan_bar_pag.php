<?php
// REGISTRO DE COMPROBANTES A PAGAR DE BARREDURA
// Tabla 'barredura'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay tickets para el rango y el comprador";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_bar_pag.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['fecha_pago'])) {
	// Obtener la suma de los importes para registrar en otros y en efectivos
	$sql = "SELECT num_cia,SUM(importe) AS importe FROM barredura WHERE no_comprobante BETWEEN $_GET[no_comprobante1] AND $_GET[no_comprobante2] AND color = $_GET[color] GROUP BY num_cia ORDER BY num_cia";
	$importe = ejecutar_script($sql,$dsn);
	
	if (!$importe) {
		header("location: ./pan_bar_reg.php?codigo_error=1");
		die;
	}
	// Actualizar fecha de pago en barredura
	$sql = "UPDATE barredura SET fecha_pago='$_GET[fecha_pago]' WHERE no_comprobante BETWEEN $_GET[no_comprobante1] AND $_GET[no_comprobante2] AND color = $_GET[color]";
	ejecutar_script($sql,$dsn);
	
	// Actualizar efectivo en total_panaderias
	for ($i=0; $i<count($importe); $i++) {
		if ($id = ejecutar_script("SELECT id FROM total_panaderias WHERE num_cia=".$importe[$i]['num_cia']." AND fecha='$_GET[fecha_pago]'",$dsn))
			$sql = "UPDATE total_panaderias SET efectivo=efectivo+".$importe[$i]['importe'].",otros=otros+".$importe[$i]['importe'].", efe = FALSE, pas = FALSE, exp = FALSE, pro = FALSE, gas = FALSE WHERE id=".$id[0]['id'];
		else
			$sql = "INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas)
			VALUES (".$importe[$i]['num_cia'].",'$_GET[fecha_pago]',0,0,".$importe[$i]['importe'].",0,0,0,0,0,".$importe[$i]['importe'].",FALSE,FALSE,FALSE,FALSE,FALSE)";
		ejecutar_script($sql,$dsn);
	}
	
	$_SESSION['bar']['fecha_pago'] = $_GET['fecha_pago'];
	$_SESSION['bar']['no_comprobante1'] = $_GET['no_comprobante1'];
	$_SESSION['bar']['no_comprobante2'] = $_GET['no_comprobante2'];
	$_SESSION['bar']['color'] = $_GET['color'];
	
	header("location: ./pan_bar_pag.php?listado=1");
	die;
}


if (isset($_GET['listado'])) {
	$sql = "SELECT color,num_cia,no_comprobante,SUM(importe) AS importe FROM barredura WHERE fecha_pago = '".$_SESSION['bar']['fecha_pago']."' AND no_comprobante BETWEEN ".$_SESSION['bar']['no_comprobante1']." AND ".$_SESSION['bar']['no_comprobante2']." AND color = ".$_SESSION['bar']['color']." GROUP BY color,no_comprobante,num_cia ORDER BY color,no_comprobante";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./pan_bar_reg.php");
		die;
	}
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_SESSION['bar']['fecha_pago'],$fecha);
	
	$tpl->newBlock("listado");
	$tpl->assign("dia",$fecha[1]);
	$tpl->assign("mes",mes_escrito($fecha[2]));
	$tpl->assign("anio",$fecha[3]);
	
	$color = NULL;
	for ($i=0; $i<count($result); $i++) {
		if ($color != $result[$i]['color']) {
			$color = $result[$i]['color'];
			
			$tpl->newBlock("color");
			switch ($color) {
				case 1: $nombre_color = "Azul"; break;
				case 2: $nombre_color = "Rosa"; break;
				case 3: $nombre_color = "Amarillo"; break;
				case 4: $nombre_color = "Verde"; break;
				case 5: $nombre_color = "Blanco"; break;
			}
			$tpl->assign("no_color",$color);
			$tpl->assign("color",$nombre_color);
			
			$total = 0;
		}
		$tpl->newBlock("comprador");
		$tpl->assign("comprobante",$result[$i]['no_comprobante']);
		$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		
		$total += $result[$i]['importe'];
		$tpl->assign("color.total",number_format($total,2,".",","));
	}
	
	// Listado 2
	$sql = "SELECT num_cia,nombre,SUM(importe) AS importe FROM barredura JOIN catalogo_companias USING(num_cia) WHERE fecha_pago = '".$_SESSION['bar']['fecha_pago']."' AND no_comprobante BETWEEN ".$_SESSION['bar']['no_comprobante1']." AND ".$_SESSION['bar']['no_comprobante2']." AND color = ".$_SESSION['bar']['color']." GROUP BY num_cia,nombre ORDER BY num_cia";
	$result = ejecutar_script($sql,$dsn);
	
	$tpl->newBlock("listado2");
	$tpl->assign("dia",$fecha[1]);
	$tpl->assign("mes",mes_escrito($fecha[2]));
	$tpl->assign("anio",$fecha[3]);
	
	$total = 0;
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$tpl->assign("nombre_cia",$result[$i]['nombre']);
		$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
		$total += $result[$i]['importe'];
	}
	$tpl->assign("listado2.total",number_format($total,2,".",","));
	
	$tpl->printToScreen();
	
	unset($_SESSION['bar']);
	die;
}

$tpl->newBlock("registro");

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

// Imprimir el resultado
$tpl->printToScreen();
?>