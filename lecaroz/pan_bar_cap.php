<?php
// REGISTRO DE VENTA DE BARREDURA
// Tabla 'barredura'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El código no existe en la Base de Datos.";

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
$tpl->assignInclude("body","./plantillas/pan/pan_bar_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_POST['num_cia'])) {
	$num_colores = 5;
	$num_barreduras = 5;
	
	$num_cia = $_POST['num_cia'];
	$fecha = $_POST['fecha'];
	
	// Recorrer colores
	for ($i=1; $i<=$num_colores; $i++) {
		// Número de comprobante
		$no_comprobante = $_POST['no_comprobante'.$i];
		
		// Recorrer barreduras
		for ($j=1; $j<=$num_barreduras; $j++) {
			if ($_POST['cantidad'.$i.$j] != 0 && $no_comprobante != "") {
				// Obtener precio para la barredura
				$sql = "SELECT precio FROM catalogo_barredura WHERE color=$i AND barredura=$j";
				$temp = ejecutar_script($sql,$dsn);
				$precio = $temp[0]['precio'];
				
				$cantidad = $_POST['cantidad'.$i.$j];
				$importe = $cantidad * $precio;
				
				$sql = "INSERT INTO barredura (num_cia,fecha_cap,color,barredura,no_comprobante,precio,cantidad,importe)
				VALUES ($num_cia,'$fecha',$i,$j,$no_comprobante,$precio,$cantidad,$importe)";
				ejecutar_script($sql,$dsn);
			}
		}
	}
	
	header("location: ./pan_bar_cap.php");
	die;
}

/*if (isset($_GET['listado'])) {
	$sql = "SELECT color,num_cia,no_comprobante,SUM(importe) AS importe FROM barredura WHERE fecha_pago IS NULL GROUP BY color,no_comprobante,num_cia ORDER BY color,no_comprobante";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./pan_bar_cap.php");
		die;
	}
	
	$tpl->newBlock("listado");
	
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
			$tpl->assign("color",$nombre_color);
		}
		$tpl->newBlock("comprador");
		$tpl->assign("comprobante",$result[$i]['no_comprobante']);
		$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = ".$result[$i]['num_cia'],$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
	}
	$tpl->printToScreen();
	die;
}*/

$tpl->newBlock("captura");

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 300 ORDER BY num_cia",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

$tpl->gotoBlock("captura");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>