<?php
// AQUI VA EL NOMBRE DE LA CAPTURA
// Tabla 'venta_pastel'
// Menu 'Panaderias->Registro de facturas'

//define ('IDSCREEN',1621); // ID de pantalla

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
$descripcion_error[1] = "No existe numero de compañia";
$descripcion_error[2] = "Debe capturar primero los movimientos a expendios";
$descripcion_error[3] = "Debe capturar primero los efectivos";
//$descripcion_error[2] = "No existe numero de factura ".$_GET['fac'];
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_rfa_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);


//********************DESCOMENTAR PARA REALIZAR MANTENIMIENTO A LA PANTALLA, UNICAMENTE EL ADMINISTRADOR PODRA USARLA
/*
if($_SESSION['iduser']!=1){
	header("location:./mantenimiento.php");
	die();
}
*/




$tpl->assign("dia",date("d"));
$tpl->assign("mes",date("m"));
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("mes_actual",date("m"));

// Si viene de una página que genero error

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_SESSION['fac_pas'])) {
	$tpl->assign("num_cia",$_SESSION['fac_pas']['num_cia']);
	$tpl->assign("fecha",$_SESSION['fac_pas']['fecha']);
	$tpl->assign("fecha_oculta1",$_SESSION['fac_pas']['fecha_oculta']);
}

//$operadora = "select * from catalogo_operadoras where iduser=".$


$sql="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 99 or num_cia =999 order by num_cia";
$cia=ejecutar_script($sql,$dsn);

for($i=0;$i<count($cia);$i++)
{
	$tpl->newBlock("nom_cia");
	$tpl->assign("num_cia1",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

for ($i=0;$i<65;$i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
		if($i+1 == 65)
			$tpl->assign("next","0");
		else
			$tpl->assign("next",$i+1);
		
		if($i > 0)
			$tpl->assign("ant",$i-1);
		else
			$tpl->assign("ant","64");
	
	if (isset($_SESSION['fac_pas'])) {
		$tpl->assign("let_remi",$_SESSION['fac_pas']['let_remi'.$i]);
		$tpl->assign("num_remi",$_SESSION['fac_pas']['num_remi'.$i]);
		$tpl->assign("idexpendio",$_SESSION['fac_pas']['idexpendio'.$i]);
		$tpl->assign("kilos",$_SESSION['fac_pas']['kilos'.$i]);
		$tpl->assign("precio_unidad",$_SESSION['fac_pas']['precio_unidad'.$i]);
		$tpl->assign("otros",$_SESSION['fac_pas']['otros'.$i]);
		$tpl->assign("base",$_SESSION['fac_pas']['base'.$i]);
		$tpl->assign("cuenta",$_SESSION['fac_pas']['cuenta'.$i]);
		$tpl->assign("dev_base",$_SESSION['fac_pas']['dev_base'.$i]);
		$tpl->assign("resta",$_SESSION['fac_pas']['resta'.$i]);
		$tpl->assign("fecha_entrega",$_SESSION['fac_pas']['fecha_entrega'.$i]);
		$tpl->assign("pastillaje",$_SESSION['fac_pas']['pastillaje'.$i]);
		$tpl->assign("otros_efectivos",$_SESSION['fac_pas']['otros_efectivos'.$i]);
	}
	
	$tpl->gotoBlock("_ROOT");
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>