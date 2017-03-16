<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontró el recibo especificado";
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
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_can.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


if(isset($_POST['id'])){
	$sql="delete from recibos_rentas where id=".$_POST['id'];
	ejecutar_script($sql,$dsn);
	header("location: ./ren_recibos_can.php");
	die();
}

if (!isset($_GET['folio'])) {
	$tpl->newBlock("obtener_datos");
	
	$sql="select * from catalogo_arrendadores order by cod_arrendador";
	$arrendadores=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($arrendadores);$i++)	{
		$tpl->newBlock("nombre_arrendador");
		$tpl->assign("num_arr",$arrendadores[$i]['cod_arrendador']);
		$tpl->assign("nombre_arrendador",$arrendadores[$i]['nombre']);
	}
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
		$tpl->printToScreen();
		die();
	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
		$tpl->printToScreen();
		die();
	}
	
$tpl->printToScreen();
die();
}

$sql="SELECT * FROM recibos_rentas where num_recibo=".$_GET['folio']." and cod_arrendador=".$_GET['arrendador'];
$recibo=ejecutar_script($sql,$dsn);

if(!$recibo){
	header("location: ./ren_recibos_can.php?codigo_error=1");
	die();
}

$tpl->newBlock("cancelacion");
$tpl->assign("id",$recibo[0]['id']);
$tpl->assign("folio",$_GET['folio']);
$tpl->assign("arrendador",$_GET['nombre_arrendador']);

$arrendatario=ejecutar_script("select * from catalogo_arrendatarios where num_arrendatario=".$recibo[0]['num_arrendatario'],$dsn);
$tpl->assign("arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));

if($recibo[0]['bloque']==1)
	$tpl->assign("bloque","INTERNO");
else
	$tpl->assign("bloque","EXTERNO");
	
if($recibo[0]['renta'] > 0)
	$tpl->assign("renta",number_format($recibo[0]['renta'],2,'.',','));

if($recibo[0]['agua'] > 0)
	$tpl->assign("agua",number_format($recibo[0]['agua'],2,'.',','));

if($recibo[0]['mantenimiento'] > 0)
	$tpl->assign("mantenimiento",number_format($recibo[0]['mantenimiento'],2,'.',','));

if($recibo[0]['iva'] > 0)
	$tpl->assign("iva",number_format($recibo[0]['iva'],2,'.',','));

if($recibo[0]['isr_retenido'] > 0)
	$tpl->assign("ret_isr",number_format($recibo[0]['isr_retenido'],2,'.',','));

if($recibo[0]['iva_retenido'] > 0)
	$tpl->assign("ret_iva",number_format($recibo[0]['iva_retenido'],2,'.',','));

if($recibo[0]['neto'] > 0)
	$tpl->assign("neto",number_format($recibo[0]['neto'],2,'.',','));


$tpl->printToScreen();
die();
?>