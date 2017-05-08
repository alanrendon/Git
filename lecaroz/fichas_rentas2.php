<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[1] = "No se encontraron facturas";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ren/fichas_rentas.tpl");
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
//$tpl->assignInclude("body","./plantillas/ban/cometra.tpl");
$tpl->prepare();


$sql="select recibos_rentas.*, catalogo_arrendatarios.num_local, nombre_arrendatario, catalogo_arrendatarios.rfc, direccion_fiscal, catalogo_arrendadores.nombre, catalogo_companias.direccion as direccion_cia from recibos_rentas join catalogo_arrendadores using(cod_arrendador) join catalogo_arrendatarios using(num_arrendatario) join catalogo_locales using(num_local) join catalogo_companias using(num_cia) where recibos_rentas.num_arrendatario=".$_GET['arrendatario']." and recibos_rentas.cod_arrendador=".$_GET['arrendador']." and num_recibo between ".$_GET['inicio']." and ".$_GET['fin']." order by num_recibo";
$recibos=ejecutar_script($sql,$dsn);

for($i=0;$i<count($recibos);$i++){
	$tpl->newBlock("recibo");
	$tpl->assign("arrendatario",strtoupper($recibos[$i]['nombre_arrendatario']));
	$tpl->assign("rfc",strtoupper($recibos[$i]['rfc']));
	$tpl->assign("direccion_fisica",strtoupper($recibos[$i]['direccion_fiscal']));
	
	if($recibos[$i]['mantenimiento'] >0 and $recibos[$i]['renta'] == 0)
		$tpl->assign("local_comercial","RENTA COMERCIAL");
	else
		$tpl->assign("local_comercial","LOCAL COMERCIAL");
		
	$tpl->assign("direccion",strtoupper($recibos[$i]['direccion_cia']));
	
	$sub_total=$recibos[$i]['renta'] +$recibos[$i]['mantenimiento'];
	
	if($recibos[$i]['renta'] !="" and $recibos[$i]['renta'] > 0)
		$tpl->assign("importe",number_format($recibos[$i]['renta'],2,'.',','));
		
	if($recibos[$i]['mantenimiento'] !="" and $recibos[$i]['mantenimiento'] > 0)
		$tpl->assign("mantenimiento",number_format($recibos[$i]['mantenimiento'],2,'.',','));
		
	if($recibos[$i]['agua'] !="" and $recibos[$i]['agua'] > 0)
		$tpl->assign("agua",number_format($recibos[$i]['agua'],2,'.',','));
		
	if($recibos[$i]['iva'] !="" and $recibos[$i]['iva'] > 0)
		$tpl->assign("iva",number_format($recibos[$i]['iva'],2,'.',','));
		
	if($recibos[$i]['isr_retenido'] !="" and $recibos[$i]['isr_retenido'] > 0)
		$tpl->assign("retencion_isr",number_format($recibos[$i]['isr_retenido'],2,'.',','));
		
	if($recibos[$i]['iva_retenido'] !="" and $recibos[$i]['iva_retenido'] > 0)
		$tpl->assign("retencion_iva",number_format($recibos[$i]['iva_retenido'],2,'.',','));
		
	if($recibos[$i]['neto'] !="" and $recibos[$i]['neto'] > 0)
		$tpl->assign("total",number_format($recibos[$i]['neto'],2,'.',','));
		
	$tpl->assign("sub_total",number_format($sub_total,2,'.',','));
	
	$tpl->assign("importe_letra",num2string($recibos[$i]['neto']));
	
	if($i+1 < count($recibos))
		$tpl->assign("salto_pagina","<br style='page-break-after:always;'>");
		
	$fecha=explode("/",$recibos[$i]['fecha']);
	$fecha1=explode("/",$recibos[$i]['fecha_pago']);
	
	
	if($recibos[$i]['concepto']!="")
		$tpl->assign("mes",strtoupper($recibos[$i]['concepto']));
	else
		$tpl->assign("mes",strtoupper(mes_escrito($fecha[1])));
		
	$tpl->assign("fecha",$fecha1[0]." DE ".strtoupper(mes_escrito($fecha1[1])));
	$tpl->assign("anio",$fecha1[2]);
	
	ejecutar_script("update recibos_rentas set impreso='true' where id=".$recibos[$i]['id'],$dsn);
	
}

$tpl->printToScreen();

?>