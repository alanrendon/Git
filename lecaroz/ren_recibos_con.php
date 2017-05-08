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
$descripcion_error[1] = "Ya existen registros";
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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla

if(!isset($_GET['mes'])){
	$tpl->newblock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($i=0;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($i)));
		if(date("n")==$i)
			$tpl->assign("selected","selected");
	}
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

$fecha="1/".$_GET['mes']."/".$_GET['anio'];

for($j=1;$j<=2;$j++){
	
	$recibos=ejecutar_script("select * from recibos_rentas where fecha='".$fecha."' and bloque=$j order by cod_arrendador,num_arrendatario",$dsn);

	$tpl->newBlock("listado");
	
	if($j==1)
		$tpl->assign("bloque","INTERNOS");
	else
		$tpl->assign("bloque","EXTERNOS");
	$tpl->assign("mes",strtoupper(mes_escrito($_GET['mes'])));
	$tpl->assign("anio",$_GET['anio']);
	$total_agua=0;
	$total_mantenimiento=0;
	$total_renta=0;
	$total_iva=0;
	$total_isr_ret=0;
	$total_iva_ret=0;
	$total_neto=0;
	$aux=0;
	$total_arrendador=0;
	
	if($recibos){
		for($i=0;$i<count($recibos);$i++){
			$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$recibos[$i]['cod_arrendador'],$dsn);
			if($aux!=$recibos[$i]['cod_arrendador']){
				$tpl->newBlock("arrendador");
				$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
				$aux=$recibos[$i]['cod_arrendador'];
				$total_arrendador=0;
			}
			
			$tpl->newBlock("recibo");
			$arrendatario=obtener_registro("catalogo_arrendatarios",array("num_arrendatario"),array($recibos[$i]['num_arrendatario']),"","",$dsn);
			$local=obtener_registro("catalogo_locales",array("num_local"),array($arrendatario[0]['num_local']),"","",$dsn);
			
			$tpl->assign("arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
			$tpl->assign("local",strtoupper($local[0]['nombre']));
			$tpl->assign("recibo",$recibos[$i]['num_recibo']);
	
			if($recibos[$i]['renta'] !="" and $recibos[$i]['renta'] > 0)
				$tpl->assign("renta",number_format($recibos[$i]['renta'],2,'.',','));
	
			if($recibos[$i]['agua'] !="" and $recibos[$i]['agua'] > 0)
				$tpl->assign("agua",number_format($recibos[$i]['agua'],2,'.',','));
	
			if($recibos[$i]['mantenimiento'] !="" and $recibos[$i]['mantenimiento'] > 0)
				$tpl->assign("mantenimiento",number_format($recibos[$i]['mantenimiento'],2,'.',','));
	
			if($recibos[$i]['iva'] !="" and $recibos[$i]['iva'] > 0)
				$tpl->assign("iva",number_format($recibos[$i]['iva'],2,'.',','));
	
			if($recibos[$i]['isr_retenido'] !="" and $recibos[$i]['isr_retenido'] > 0)
				$tpl->assign("isr_ret",number_format($recibos[$i]['isr_retenido'],2,'.',','));
	
			if($recibos[$i]['iva_retenido'] !="" and $recibos[$i]['iva_retenido'] > 0)
				$tpl->assign("iva_ret",number_format($recibos[$i]['iva_retenido'],2,'.',','));
	
			if($recibos[$i]['neto'] !="" and $recibos[$i]['neto'] > 0)
				$tpl->assign("neto",number_format($recibos[$i]['neto'],2,'.',','));
			
			$total_renta += number_format($recibos[$i]['renta'],2,'.','');
			$total_agua += number_format($recibos[$i]['agua'],2,'.','');
			$total_mantenimiento += number_format($recibos[$i]['mantenimiento'],2,'.','');
			$total_iva += number_format($recibos[$i]['iva'],2,'.','');
			$total_isr_ret += number_format($recibos[$i]['isr_retenido'],2,'.','');
			$total_iva_ret += number_format($recibos[$i]['iva_retenido'],2,'.','');
			$total_neto += number_format($recibos[$i]['neto'],2,'.','');
			
			$total_arrendador += number_format($recibos[$i]['neto'],2,'.','');
			
			$tpl->gotoBlock("arrendador");
			$tpl->assign("total_arrendador",number_format($total_arrendador,2,'.',','));
			
		}
	}
	$tpl->gotoBlock("listado");
	if($total_renta > 0) 
		$tpl->assign("total_renta",number_format($total_renta,2,'.',','));
		
	if($total_agua > 0)
		$tpl->assign("total_agua",number_format($total_agua,2,'.',','));
		
	if($total_mantenimiento > 0)
		$tpl->assign("total_mantenimiento",number_format($total_mantenimiento,2,'.',','));
		
	if($total_iva > 0)
		$tpl->assign("total_iva",number_format($total_iva,2,'.',','));
		
	if($total_isr_ret > 0)
		$tpl->assign("total_isr_ret",number_format($total_isr_ret,2,'.',','));
		
	if($total_iva_ret > 0)
	$tpl->assign("total_iva_ret",number_format($total_iva_ret,2,'.',','));
	
	if($total_neto > 0)
		$tpl->assign("total_neto",number_format($total_neto,2,'.',','));

	if($j == 1){
		$tpl->gotoBlock("listado_todos");
		$tpl->assign("salto_pagina","<br style='page-break-after:always;'>");
	}

		
}


$tpl->printToScreen();
die();
?>