<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron notas pendientes de pago";
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
$tpl->assignInclude("body","./plantillas/adm/admin_fac_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['fecha']))
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d") - 1,date("Y"))));
	
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

// Imprimir el resultado


$tpl->newBlock("facturas");
$tpl->assign("fecha",$_GET['fecha']);

$sql="select * from venta_pastel where estado=0 and tipo=0 and fecha_entrega <'".$_GET['fecha']."' order by num_cia,letra_folio,num_remi";
$notas=ejecutar_script($sql,$dsn);

if(!$notas){
	header("location: ./admin_fac_con.php?codigo_error=1");
	die();
}


$aux_cias=0;
$_fecha=explode("/",$_GET['fecha']);
for($i=0;$i<count($notas);$i++){
	$tpl->newBlock("rows");
	if($aux_cias != $notas[$i]['num_cia']){
		$tpl->newBlock("cias");
		$tpl->assign("num_cia",$notas[$i]['num_cia']);
		$compa=obtener_registro("catalogo_companias",array('num_cia'),array($notas[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$compa[0]['nombre_corto']);
		$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($compa[0]['idoperadora']),"","",$dsn);
		$tpl->assign("operadora",$opera[0]['nombre']);
		$aux_cias=$notas[$i]['num_cia'];
	}
	$tpl->gotoBlock("rows");
	if($notas[$i]['letra_folio']=='X')
		$tpl->assign("let_folio","");
	else
		$tpl->assign("let_folio",$notas[$i]['letra_folio']);
	$tpl->assign("num_fact",$notas[$i]['num_remi']);
		
	$tpl->assign("total",number_format($notas[$i]['total_factura'],2,'.',','));
	$tpl->assign("resta",number_format($notas[$i]['resta_pagar'],2,'.',','));
	$tpl->assign("fecha_entrega",$notas[$i]['fecha_entrega']);
	
}


$tpl->printToScreen();
?>