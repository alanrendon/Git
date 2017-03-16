<?php
// CONSULTA DE COMPROBANTES DE BARREDURA
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
$tpl->assignInclude("body","./plantillas/pan/pan_bar_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT color,num_cia,no_comprobante,SUM(importe) AS importe FROM barredura WHERE fecha_pago IS NULL GROUP BY color,no_comprobante,num_cia ORDER BY color,no_comprobante";
$result = ejecutar_script($sql,$dsn);

if ($result) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",date("d/m/Y"),$fecha);
	
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

// Imprimir el resultado
$tpl->printToScreen();
?>