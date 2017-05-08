<?php
// AQUI VA EL NOMBRE DE LA CAPTURA
// Tabla 'captura_efectivos'
// Menu 'Nombre del menu->Nombre del submenu'

//define ('IDSCREEN',1322); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe numero de compañia";
$descripcion_error[2] = "No se efectuo ninguna captura";

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
$tpl->assignInclude("body","./plantillas/pan/pan_efem_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


// Seleccionar tabla

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

// Si viene de una página que genero error

if(!isset($_GET['fecha'])){
	$tpl->newBlock("obtener_datos");
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("anio_actual",date("Y"));


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



if($_SESSION['iduser'] == 1 or $_SESSION['iduser'] == 4){
	$cias = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 100 or num_cia > 701 order by num_cia",$dsn);
}
else{
	$user = ejecutar_script("select * from catalogo_operadoras where iduser = ".$_SESSION['iduser'],$dsn);
	if(!$user){
		header("location: ./pan_exp_list.php?codigo_error=1");
		die();
	}
	$cias=ejecutar_script("select * from catalogo_companias where idoperadora=".$user[0]['idoperadora']." and num_cia < 100 or num_cia > 701 order by num_cia",$dsn);
}

$tpl->newBlock("movimientos");
$_fecha=explode("/",$_GET['fecha']);
$fecha=$_fecha[0]." DE ".strtoupper(mes_escrito($_fecha[1]))." DEL ".$_fecha[2];
$tpl->assign("fecha",$fecha);

for($i=0;$i<count($cias);$i++){
	$efectivo=ejecutar_script("select * from captura_efectivos where num_cia=".$cias[$i]['num_cia']." and fecha='$_GET[fecha]'",$dsn);
	if($efectivo){
		$tpl->newBlock("rows1");
		$tpl->assign("num_cia",$cias[$i]["num_cia"]);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		
		if($efectivo[0]['am']> 0)
			$tpl->assign("am1",number_format($efectivo[0]['am'],2,'.',','));
			
		if($efectivo[0]['am_error']> 0)
			$tpl->assign("am_error1",number_format($efectivo[0]['am_error'],2,'.',','));
			
		if($efectivo[0]['pm']> 0)
			$tpl->assign("pm1",number_format($efectivo[0]['pm'],2,'.',','));
			
		if($efectivo[0]['pm_error']> 0)
			$tpl->assign("pm_error1",number_format($efectivo[0]['pm_error'],2,'.',','));
			
		if($efectivo[0]['pastel']> 0)
			$tpl->assign("pastel1",number_format($efectivo[0]['pastel'],2,'.',','));
			
		if($efectivo[0]['venta_pta']> 0)
			$tpl->assign("venta_pta1",number_format($efectivo[0]['venta_pta'],2,'.',','));
			
		if($efectivo[0]['pastillaje']> 0)
			$tpl->assign("pastillaje1",number_format($efectivo[0]['pastillaje'],2,'.',','));
			
		if($efectivo[0]['otros']> 0)
			$tpl->assign("otros1",number_format($efectivo[0]['otros'],2,'.',','));
			
		if($efectivo[0]['ctes']> 0)
			$tpl->assign("ctes1",number_format($efectivo[0]['ctes'],2,'.',','));
			
		if($efectivo[0]['corte1']> 0)
			$tpl->assign("corte11",number_format($efectivo[0]['corte1'],2,'.',','));
			
		if($efectivo[0]['corte2']> 0)
			$tpl->assign("corte21",number_format($efectivo[0]['corte2'],2,'.',','));
	}
}


$tpl->printToScreen();
?>
