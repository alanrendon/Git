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
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
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
$tpl->assignInclude("body","./plantillas/ren/ren_arrendatario_mod.tpl");
$tpl->prepare();
// Seleccionar script para menu

$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla

if(isset($_POST['cod_arrendatario'])){
	ejecutar_script("UPDATE catalogo_arrendatarios
	 SET 
	 num_local=".$_POST['num_local'].", 
	 rfc='".$_POST['rfc']."', 
	 giro='".$_POST['giro']."', 
	 fecha_inicio='".$_POST['fecha_inicial']."',
	 fecha_final='".$_POST['fecha_final']."',
	 incremento_anual='".$_POST['incremento']."',
	 renta_con_recibo =".number_format($_POST['con_recibo'],2,'.','').",
	 renta_sin_recibo =".number_format($_POST['sin_recibo'],2,'.','').",
	 agua=".number_format($_POST['agua'],2,'.','').",
	 mantenimiento=".number_format($_POST['mantenimiento'],2,'.','').",
	 rentas_en_deposito=".number_format($_POST['rentas_deposito'],2,'.','').",
	 retencion_isr='".$_POST['retencion_isr']."',
	 retencion_iva='".$_POST['iva']."',
	 fianza='".$_POST['fianza']."',
	 tipo_persona='".$_POST['tipo_persona']."',
	 nombre_arrendatario='".$_POST['nombre_arrendatario']."',
	 representante='".$_POST['representante']."',
	 nombre_aval='".$_POST['nombre_aval']."',
	 bien_avaluo='".$_POST['bien_avaluo']."',
	 cargo_daños=".number_format($_POST['daños'],2,'.','').",
	 cargo_termino=".number_format($_POST['termino'],2,'.','').",
	 direccion_fiscal='".strtoupper($_POST['dir_fiscal'])."',
	 recibo_mensual='".$_POST['imprime_recibo']."',
	 descripcion_local='".strtoupper($_POST['descripcion_local'])."' 
	 where num_arrendatario= ".$_POST['cod_arrendatario'],$dsn);
	
	header("location: ./ren_arrendatario_mod.php");
	die();
}

if(!isset($_GET['arrendatario'])){
	$tpl->newBlock("obtener_arrendatario");
	$arrendatario=ejecutar_script("select * from catalogo_arrendatarios order by num_arrendatario",$dsn);
	
	for($i=0;$i<count($arrendatario);$i++){
		$tpl->newBlock("nombre_arrendatario");
		$tpl->assign("num_arrendatario",$arrendatario[$i]['num_arrendatario']);
		$tpl->assign("nombre_arrendatario",$arrendatario[$i]['nombre_arrendatario']);
	}
	$tpl->printToScreen();
	die();
}

$arrendatario=ejecutar_script("select * from catalogo_arrendatarios where num_arrendatario=".$_GET["arrendatario"],$dsn);
$_local=ejecutar_script("select num_local, catalogo_locales.nombre,num_cia, direccion, cod_arrendador, catalogo_arrendadores.nombre as nombre_arrendador, catalogo_arrendadores.tipo_persona, cta_predial,bloque from catalogo_locales join catalogo_companias using (num_cia) join catalogo_arrendadores using(cod_arrendador) where num_local=".$arrendatario[0]['num_local'],$dsn);

$finicio=explode("/",$arrendatario[0]['fecha_inicio']);
$ffinal=explode("/",$arrendatario[0]['fecha_final']);
$finicio=number_format($finicio[0],0,'','')."/".number_format($finicio[1],0,'','')."/".number_format($finicio[2],0,'','');
$ffinal=number_format($ffinal[0],0,'','')."/".number_format($ffinal[1],0,'','')."/".number_format($ffinal[2],0,'','');

$tpl->newBlock("modificar_datos");
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("id",$_GET['arrendatario']);
$tpl->assign("arrendatario",$_GET['nombre_arrendatario']);
$tpl->assign("local",$arrendatario[0]['num_local']);
$tpl->assign("representante",strtoupper($arrendatario[0]['representante']));
$tpl->assign("aval",strtoupper($arrendatario[0]['nombre_aval']));
$tpl->assign("dir_aval",strtoupper($arrendatario[0]['bien_avaluo']));
$tpl->assign("rfc",strtoupper($arrendatario[0]['rfc']));
$tpl->assign("giro",strtoupper($arrendatario[0]['giro']));
$tpl->assign("fecha_inicio",$finicio);
$tpl->assign("fecha_final",$ffinal);
$tpl->assign("dir_fiscal",strtoupper($arrendatario[0]['direccion_fiscal']));

$tpl->assign("nombre_local",strtoupper($_local[0]['nombre']));
$tpl->assign("direccion",strtoupper($_local[0]['direccion']));
$tpl->assign("arrendador",strtoupper($_local[0]['nombre_arrendador']));
$tpl->assign("predial",strtoupper($_local[0]['cta_predial']));

if($_local[0]['bloque']==1)
	$tpl->assign("bloque","INTERNO");
elseif($_local[0]['bloque']==2)
	$tpl->assign("bloque","EXTERNO");

if($_local[0]['tipo_persona']=='f')
//	$tpl->assign("contrato","./contrato_renta2.php");
	$tpl->assign("contrato","contrato_renta2.php");
else
//	$tpl->assign("contrato","./contrato_renta.php");
	$tpl->assign("contrato","contrato_renta.php");


if($arrendatario[0]['renta_con_recibo']!="" and $arrendatario[0]['renta_con_recibo'] > 0)
	$tpl->assign("con_recibo",number_format($arrendatario[0]['renta_con_recibo'],2,'.',''));

if($arrendatario[0]['renta_sin_recibo']!="" and $arrendatario[0]['renta_sin_recibo'] > 0)
	$tpl->assign("sin_recibo",number_format($arrendatario[0]['renta_sin_recibo'],2,'.',''));

if($arrendatario[0]['agua']!="" and $arrendatario[0]['agua'] > 0)
	$tpl->assign("agua",number_format($arrendatario[0]['agua'],2,'.',''));

if($arrendatario[0]['mantenimiento']!="" and $arrendatario[0]['mantenimiento'] > 0)
	$tpl->assign("mantenimiento",number_format($arrendatario[0]['mantenimiento'],2,'.',''));

if($arrendatario[0]['rentas_en_deposito']!="" and $arrendatario[0]['rentas_en_deposito'] > 0)
	$tpl->assign("depositos",number_format($arrendatario[0]['rentas_en_deposito'],2,'.',''));

if($arrendatario[0]['cargo_daños']!="" and $arrendatario[0]['cargo_daños'] > 0)
	$tpl->assign("danos",number_format($arrendatario[0]['cargo_daños'],2,'.',''));

if($arrendatario[0]['cargo_termino']!="" and $arrendatario[0]['cargo_termino'] > 0)
	$tpl->assign("termino",number_format($arrendatario[0]['cargo_termino'],2,'.',''));

if($arrendatario[0]['incremento_anual']=='f')
	$tpl->assign("incremento_no","checked");
else
	$tpl->assign("incremento_si","checked");

if($arrendatario[0]['retencion_isr']=='f')
	$tpl->assign("isr_no","checked");
else
	$tpl->assign("isr_si","checked");
	
if($arrendatario[0]['retencion_iva']=='f')
	$tpl->assign("iva_no","checked");
else
	$tpl->assign("iva_si","checked");
	
if($arrendatario[0]['fianza']=='f')
	$tpl->assign("fianza_no","checked");
else
	$tpl->assign("fianza_si","checked");
	
if($arrendatario[0]['tipo_persona']=='f')
	$tpl->assign("persona_fisica","checked");
else
	$tpl->assign("persona_moral","checked");
	
if($arrendatario[0]['recibo_mensual']=='f')
	$tpl->assign("imprime_no","checked");
else
	$tpl->assign("imprime_si","checked");



$local=ejecutar_script("select num_local, catalogo_locales.nombre,num_cia, direccion, cod_arrendador, catalogo_arrendadores.nombre as nombre_arrendador, catalogo_arrendadores.tipo_persona, cta_predial,bloque from catalogo_locales join catalogo_companias using (num_cia) join catalogo_arrendadores using(cod_arrendador) where ocupado=false order by num_local",$dsn);
for($i=0;$i<count($local);$i++){
	$tpl->newBlock("nombre_local");
	$tpl->assign("num_local",$local[$i]['num_local']);
	$tpl->assign("nombre_local",$local[$i]['nombre']);
	$tpl->assign("direccion",$local[$i]['direccion']);
	$tpl->assign("nombre_arrendador",$local[$i]['nombre_arrendador']);
	$tpl->assign("predial",$local[$i]['cta_predial']);
	if($local[$i]['bloque']==1)
		$tpl->assign("bloque","PROPIO");
	elseif($local[$i]['bloque']==2)
		$tpl->assign("bloque","AJENO");

	if($local[$i]['tipo_persona']=='f')
		$tpl->assign("contrato","./contrato_renta2.php");
	else
		$tpl->assign("contrato","./contrato_renta.php");
		
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
?>