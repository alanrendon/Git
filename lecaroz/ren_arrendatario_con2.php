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
$tpl->assignInclude("body","./plantillas/ren/ren_arrendatario_con.tpl");
$tpl->prepare();
// Seleccionar script para menu

$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['num_arrendatario'])) {
	$tpl->newBlock("obtener_datos");
	
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


if($_GET['tipo_con1']==0){
	$tpl->newBlock("por_arrendatario");
	$arrendatario=ejecutar_script("select catalogo_arrendatarios.*, catalogo_locales.nombre as nombre_local, catalogo_arrendadores.nombre as nombre_arrendador, catalogo_companias.num_cia, catalogo_companias.direccion, catalogo_locales.bloque from catalogo_arrendatarios join catalogo_locales using(num_local) join catalogo_arrendadores using(cod_arrendador) join catalogo_companias using(num_cia) where num_arrendatario=".$_GET['num_arrendatario'],$dsn);
//print_r($arrendatario);
	$tpl->assign("nombre_arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
	$tpl->assign("local",strtoupper($arrendatario[0]['nombre_local']));
//	$tpl->assign("direccion",strtoupper($arrendatario[0]['direccion']));
	$tpl->assign("nombre_arrendador",strtoupper($arrendatario[0]['nombre_arrendador']));
	$tpl->assign("representante",strtoupper($arrendatario[0]['representante']));
	
	if($arrendatario[0]['nombre_aval']!="")
		$tpl->assign("aval",strtoupper($arrendatario[0]['nombre_aval']));
	else
		$tpl->assign("aval","SIN AVAL");
		
	$tpl->assign("direccion_aval",strtoupper($arrendatario[0]['bien_avaluo']));
	$tpl->assign("rfc",strtoupper($arrendatario[0]['rfc']));
	$tpl->assign("giro",strtoupper($arrendatario[0]['giro']));
	$tpl->assign("cod_arrendatario",$arrendatario[0]['num_arrendatario']);
	$tpl->assign("fecha_inicio",$arrendatario[0]['fecha_inicio']);
	$tpl->assign("fecha_final",$arrendatario[0]['fecha_final']);
	$tpl->assign("dir_fiscal",strtoupper($arrendatario[0]['direccion_fiscal']));
	
	if($arrendatario[0]['bloque']==1)
		$bloque="INTERNO";
	else
		$bloque="EXTERNO";
	$tpl->assign("bloque",$bloque);

	if($arrendatario[0]['renta_con_recibo'] !="" and $arrendatario[0]['renta_con_recibo'] > 0)
		$tpl->assign("con_recibo",number_format($arrendatario[0]['renta_con_recibo'],2,'.',','));
		
	if($arrendatario[0]['renta_sin_recibo'] !="" and $arrendatario[0]['renta_sin_recibo'] > 0)
		$tpl->assign("sin_recibo",number_format($arrendatario[0]['renta_sin_recibo'],2,'.',','));
		
	if($arrendatario[0]['agua'] !="" and $arrendatario[0]['agua'] > 0)
		$tpl->assign("agua",number_format($arrendatario[0]['agua'],2,'.',','));
		
	if($arrendatario[0]['mantenimiento'] !="" and $arrendatario[0]['mantenimiento'] > 0)
		$tpl->assign("mantenimiento",number_format($arrendatario[0]['mantenimiento'],2,'.',','));
		
	if($arrendatario[0]['cargo_daños'] !="" and $arrendatario[0]['cargo_daños'] > 0)
		$tpl->assign("danos",number_format($arrendatario[0]['cargo_daños'],2,'.',','));
		
	if($arrendatario[0]['cargo_termino'] !="" and $arrendatario[0]['cargo_termino'] > 0)
		$tpl->assign("terminado",number_format($arrendatario[0]['cargo_termino'],2,'.',','));
	
	if($arrendatario[0]['rentas_en_deposito'] !="" and $arrendatario[0]['rentas_en_deposito'] > 0)
		$tpl->assign("rentas",$arrendatario[0]['cargo_termino']);

	if($arrendatario[0]['retencion_isr'] =='f')
		$retencion_isr="NO";
	else
		$retencion_isr="SI";
	$tpl->assign("isr_ret",$retencion_isr);
	
	if($arrendatario[0]['retencion_iva']=='f')
		$retencion_iva="NO";
	else
		$retencion_iva="SI";
	
	$tpl->assign("iva_ret",$retencion_iva);
	
	if($arrendatario[0]['fianza']=='f')
		$fianza="NO";
	else
		$fianza="SI";
	$tpl->assign("fianza",$fianza);

	if($arrendatario[0]['tipo_persona']=='f')
		$persona="FISICA";
	else
		$persona="MORAL";
	$tpl->assign("persona",$persona);
	
	if($arrendatario[0]['incremento_anual']=='f')
		$incremento="NO";
	else
		$incremento="SI";
	$tpl->assign("incremento",$incremento);

	if($arrendatario[0]['recibo_mensual']=='f')
		$mensual="NO";
	else
		$mensual="SI";
	$tpl->assign("recibos_mensual",$mensual);
	
}

else{
	$tpl->newBlock("todos");
	$arrendatario=ejecutar_script("select catalogo_arrendatarios.*, catalogo_locales.nombre as nombre_local, catalogo_arrendadores.cod_arrendador, catalogo_arrendadores.nombre as nombre_arrendador, catalogo_companias.num_cia, catalogo_companias.direccion, catalogo_locales.bloque from catalogo_arrendatarios join catalogo_locales using(num_local) join catalogo_arrendadores using(cod_arrendador) join catalogo_companias using(num_cia) order by cod_arrendador, num_arrendatario",$dsn);
	$aux=0;
	for($i=0;$i<count($arrendatario);$i++){
		if($aux!=$arrendatario[$i]['cod_arrendador']){
			$tpl->newBlock("arrendador");
			$tpl->assign("arrendador",strtoupper($arrendatario[$i]['nombre_arrendador']));
			$tpl->assign("cod_arrendador",$arrendatario[$i]['cod_arrendador']);
			$aux=$arrendatario[$i]['cod_arrendador'];
			
		}
		$tpl->newBlock("arrendatarios");
		$tpl->assign("nombre_arrendatario",strtoupper($arrendatario[$i]['nombre_arrendatario']));
		$tpl->assign("local",strtoupper($arrendatario[$i]['nombre_local']));
		$tpl->assign("direccion",strtoupper($arrendatario[$i]['direccion']));
		$tpl->assign("representante",strtoupper($arrendatario[$i]['representante']));
		if($arrendatario[$i]['nombre_aval']!="")
			$tpl->assign("aval",strtoupper($arrendatario[$i]['nombre_aval']));
		else
			$tpl->assign("aval","SIN AVAL");
		$tpl->assign("direccion_aval",strtoupper($arrendatario[0]['bien_avaluo']));
		$tpl->assign("rfc",strtoupper($arrendatario[$i]['rfc']));
		$tpl->assign("giro",strtoupper($arrendatario[$i]['giro']));
		$tpl->assign("cod_arrendatario",$arrendatario[$i]['num_arrendatario']);
		$tpl->assign("fecha_inicio",$arrendatario[$i]['fecha_inicio']);
		$tpl->assign("fecha_final",$arrendatario[$i]['fecha_final']);

		if($arrendatario[$i]['bloque']==1)
			$bloque="INTERNO";
		else
			$bloque="EXTERNO";
		$tpl->assign("bloque",$bloque);

		
		if($arrendatario[$i]['renta_con_recibo'] !="" and $arrendatario[$i]['renta_con_recibo'] > 0)
			$tpl->assign("con_recibo",number_format($arrendatario[$i]['renta_con_recibo'],2,'.',','));
			
//		if($arrendatario[$i]['renta_sin_recibo'] !="" and $arrendatario[$i]['renta_sin_recibo'] > 0)
//			$tpl->assign("sin_recibo",number_format($arrendatario[$i]['renta_sin_recibo'],2,'.',','));
			
		if($arrendatario[$i]['agua'] !="" and $arrendatario[$i]['agua'] > 0)
			$tpl->assign("agua",number_format($arrendatario[$i]['agua'],2,'.',','));
			
		if($arrendatario[$i]['mantenimiento'] !="" and $arrendatario[$i]['mantenimiento'] > 0)
			$tpl->assign("mantenimiento",number_format($arrendatario[$i]['mantenimiento'],2,'.',','));
			
//		if($arrendatario[$i]['cargo_daños'] !="" and $arrendatario[$i]['cargo_daños'] > 0)
//			$tpl->assign("danos",number_format($arrendatario[$i]['cargo_daños'],2,'.',','));
			
//		if($arrendatario[$i]['cargo_termino'] !="" and $arrendatario[$i]['cargo_termino'] > 0)
//			$tpl->assign("terminado",number_format($arrendatario[$i]['cargo_termino'],2,'.',','));
		
//		if($arrendatario[$i]['rentas_en_deposito'] !="" and $arrendatario[$i]['rentas_en_deposito'] > 0)
//			$tpl->assign("rentas",$arrendatario[$i]['cargo_termino']);
	
/*		if($arrendatario[$i]['retencion_isr'] =='f')
			$retencion_isr="NO";
		else
			$retencion_isr="SI";
		$tpl->assign("isr_ret",$retencion_isr);
		
		if($arrendatario[$i]['retencion_iva']=='f')
			$retencion_iva="NO";
		else
			$retencion_iva="SI";
		
		$tpl->assign("iva_ret",$retencion_iva);
		
		if($arrendatario[$i]['fianza']=='f')
			$fianza="NO";
		else
			$fianza="SI";
		$tpl->assign("fianza",$fianza);
	
		if($arrendatario[$i]['tipo_persona']=='f')
			$persona="FISICA";
		else
			$persona="MORAL";
		$tpl->assign("persona",$persona);
		
		if($arrendatario[$i]['incremento_anual']=='f')
			$incremento="NO";
		else
			$incremento="SI";
		$tpl->assign("incremento",$incremento); */
	}
	
}


$tpl->printToScreen();
die();
?>