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
$descripcion_error[1] = "No se encontraron registros";
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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_con2.tpl");
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

// -------------------------------------------------------------TODOS LOS RECIBOS DEL MES Y AÑO ESPECIFICADOS
if($_GET['tipo_con']==3){
	for($j=1;$j<=2;$j++){
		$recibos=ejecutar_script("select * from recibos_rentas where fecha_pago='".$fecha."' and bloque=$j order by cod_arrendador,num_arrendatario",$dsn);
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
					$tpl->assign("num_arrendador",$recibos[$i]['cod_arrendador']);
					$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
					$aux=$recibos[$i]['cod_arrendador'];
					$total_arrendador=0;
				}
				
				$tpl->newBlock("recibo");
				$arrendatario=obtener_registro("catalogo_arrendatarios",array("num_arrendatario"),array($recibos[$i]['num_arrendatario']),"","",$dsn);
				$local=obtener_registro("catalogo_locales",array("num_local"),array($arrendatario[0]['num_local']),"","",$dsn);
				
				$tpl->assign("arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
				$tpl->assign("num_arrendatario",$recibos[$i]['num_arrendatario']);
				if($arrendatario[0]['descripcion_local'] !="")
					$tpl->assign("local",strtoupper($arrendatario[0]['descripcion_local']));
				else
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

				if($recibos[$i]['concepto']!=""){
					$tpl->newBlock("concepto_todos");
					$tpl->assign("concepto",strtoupper($recibos[$i]['concepto']));
				}
				else{
					if($recibos[$i]['fecha'] != $recibos[$i]['fecha_pago']){
						$tpl->newBlock("concepto_todos");
						$_fecha_1=explode("/",$recibos[$i]['fecha']);
						$concepto= "PAGO RENTA DE ".strtoupper(mes_escrito($_fecha_1[1]))." DEL ".$_fecha_1[2];
						$tpl->assign("concepto",$concepto);
					}
				}
				
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
}

//-------------------------------------------CONSULTA POR ARRENDADOR
elseif($_GET['tipo_con']==0){
	$recibos=ejecutar_script("select * from recibos_rentas where fecha_pago='".$fecha."' and cod_arrendador=".$_GET['arrendador']." order by num_recibo",$dsn);
	if(!$recibos){
		header("location: ./ren_recibos_con2.php?codigo_error=1");
		die();
	}
	$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$_GET['arrendador'],$dsn);
	$tpl->newBlock("list_arrendador");
	$tpl->assign("mes",strtoupper(mes_escrito($_GET['mes'])));
	$tpl->assign("anio",$_GET['anio']);
	$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
	$tpl->assign("num_arrendador",$_GET['arrendador']);
	$total_renta =0;
	$total_agua =0;
	$total_mantenimiento =0;
	$total_iva =0;
	$total_isr_ret =0;
	$total_iva_ret =0;
	$total_neto =0;

	for($i=0;$i<count($recibos);$i++){
		$tpl->newBlock("recibo2");
		$arrendatario=obtener_registro("catalogo_arrendatarios",array("num_arrendatario"),array($recibos[$i]['num_arrendatario']),"","",$dsn);
		$local=obtener_registro("catalogo_locales",array("num_local"),array($arrendatario[0]['num_local']),"","",$dsn);
		
		$tpl->assign("arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
		$tpl->assign("num_arrendatario",$recibos[$i]['num_arrendatario']);

		if($arrendatario[0]['descripcion_local'] !="")
			$tpl->assign("local",strtoupper($arrendatario[0]['descripcion_local']));
		else
			$tpl->assign("local",strtoupper($local[0]['nombre']));

		$tpl->assign("recibo",$recibos[$i]['num_recibo']);

		if($recibos[$i]['bloque']==1)
			$tpl->assign("tipo_local","INTERNO");
		else
			$tpl->assign("tipo_local","EXTERNO");
			
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

		if($recibos[$i]['concepto']!=""){
			$tpl->newBlock("concepto_arrendador");
			$tpl->assign("concepto",strtoupper($recibos[$i]['concepto']));
		}
		else{
			if($recibos[$i]['fecha'] != $recibos[$i]['fecha_pago']){
				$tpl->newBlock("concepto_arrendador");
				$_fecha_1=explode("/",$recibos[$i]['fecha']);
				$concepto= "PAGO RENTA DE ".strtoupper(mes_escrito($_fecha_1[1]))." DEL ".$_fecha_1[2];
				$tpl->assign("concepto",$concepto);
			}
		}
		
	}
	$tpl->gotoBlock("list_arrendador");
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

}
//----------------------------------------------------- CONSULTA POR ARRENDATARIO
if($_GET['tipo_con']==1){
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$sql = "SELECT * FROM recibos_rentas WHERE num_arrendatario = $_GET[arrendatario]";
	if ($_GET['mes'] > 0 && $_GET['anio'] > 0)
		$sql .= " AND fecha BETWEEN '$fecha1' AND $fecha2";
	$sql .= " ORDER BY cod_arrendador, fecha";
	//$recibos=ejecutar_script("select * from recibos_rentas where num_arrendatario=".$_GET['arrendatario']."  order by cod_arrendador",$dsn);
	$recibos=ejecutar_script($sql,$dsn);
	if(!$recibos){
		header("location: ./ren_recibos_con2.php?codigo_error=1");
		die();
	}

	$tpl->newBlock("list_arrendatario");
	$arrendatario=obtener_registro("catalogo_arrendatarios",array("num_arrendatario"),array($_GET['arrendatario']),"","",$dsn);
	$tpl->assign("arrendatario",strtoupper(	$arrendatario[0]['nombre_arrendatario']));
	$tpl->assign("num_arrendatario",$_GET['arrendatario']);
	
	$tmp = NULL;
	foreach ($recibos as $recibo) {
		if ($tmp != $recibo['cod_arrendador']) {
			$tmp = $recibo['cod_arrendador'];
			
			$tpl->newBlock("bloque_arrendador");
			$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$recibo['cod_arrendador'],$dsn);
			$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
			$tpl->assign("num_arrendador",$recibo['cod_arrendador']);
		}
		$tpl->newBlock("recibo_arrendatario");
		$local=obtener_registro("catalogo_locales",array("num_local"),array($arrendatario[0]['num_local']),"","",$dsn);
		if($arrendatario[0]['descripcion_local'] !="")
			$tpl->assign("local",strtoupper($arrendatario[0]['descripcion_local']));
		else
			$tpl->assign("local",strtoupper($local[0]['nombre']));
		$tpl->assign("recibo",$recibo['num_recibo']);
		$tpl->assign("fecha",$recibo['fecha']);
	
		if($recibo['renta'] !="" and $recibo['renta'] > 0)
			$tpl->assign("renta",number_format($recibo['renta'],2,'.',','));
	
		if($recibo['agua'] !="" and $recibo['agua'] > 0)
			$tpl->assign("agua",number_format($recibo['agua'],2,'.',','));
	
		if($recibo['mantenimiento'] !="" and $recibo['mantenimiento'] > 0)
			$tpl->assign("mantenimiento",number_format($recibo['mantenimiento'],2,'.',','));
	
		if($recibo['iva'] !="" and $recibo['iva'] > 0)
			$tpl->assign("iva",number_format($recibo['iva'],2,'.',','));
	
		if($recibo['isr_retenido'] !="" and $recibo['isr_retenido'] > 0)
			$tpl->assign("isr_ret",number_format($recibo['isr_retenido'],2,'.',','));
	
		if($recibo['iva_retenido'] !="" and $recibo['iva_retenido'] > 0)
			$tpl->assign("iva_ret",number_format($recibo['iva_retenido'],2,'.',','));
	
		if($recibo['neto'] !="" and $recibo['neto'] > 0)
			$tpl->assign("neto",number_format($recibo['neto'],2,'.',','));
	
		if($recibo['bloque']==1)
			$tpl->assign("tipo_local","INTERNO");
		else
			$tpl->assign("tipo_local","EXTERNO");
		
		if($recibo['concepto']!=""){
			$tpl->newBlock("concepto_arrendatario");
			$tpl->assign("concepto",strtoupper($recibos[0]['concepto']));
		}
		else{
			if($recibo['fecha'] != $recibo['fecha_pago']){
				$tpl->newBlock("concepto_arrendatario");
				$_fecha_1=explode("/",$recibo['fecha']);
				$concepto= "PAGO RENTA DE ".strtoupper(mes_escrito($_fecha_1[1]))." DEL ".$_fecha_1[2];
				$tpl->assign("concepto",$concepto);
			}
		}
	}
}

//--------------------------------------------------- CONSULTA POR RECIBO
if($_GET['tipo_con']==2){
	$tpl->newBlock("list_recibo");
	$recibos=ejecutar_script("select * from recibos_rentas where num_recibo=".$_GET['recibo']."  order by cod_arrendador",$dsn);
	if(!$recibos){
		header("location: ./ren_recibos_con2.php?codigo_error=1");
		die();
	}
	$tpl->assign("num_recibo",$_GET['recibo']);
	for($i=0;$i<count($recibos);$i++){
		$tpl->newBlock("arrendador3");
		$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$recibos[$i]['cod_arrendador'],$dsn);
		$tpl->assign("arrendador",strtoupper($arrendador[0]['nombre']));
		$tpl->assign("num_arrendador",$recibos[$i]['cod_arrendador']);
		
		$arrendatario=obtener_registro("catalogo_arrendatarios",array("num_arrendatario"),array($recibos[$i]['num_arrendatario']),"","",$dsn);
		$local=obtener_registro("catalogo_locales",array("num_local"),array($arrendatario[0]['num_local']),"","",$dsn);
		
		$tpl->assign("arrendatario",strtoupper($arrendatario[0]['nombre_arrendatario']));
		$tpl->assign("num_arrendatario",strtoupper($recibos[$i]['num_arrendatario']));
		
		if($arrendatario[0]['descripcion_local'] !="")
			$tpl->assign("local",strtoupper($arrendatario[0]['descripcion_local']));
		else
			$tpl->assign("local",strtoupper($local[0]['nombre']));
		$tpl->assign("recibo",$recibos[$i]['num_recibo']);
		$tpl->assign("fecha",$recibos[$i]['fecha']);

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

		if($recibos[$i]['bloque']==1)
			$tpl->assign("tipo_local","INTERNO");
		else
			$tpl->assign("tipo_local","EXTERNO");

		if($recibos[$i]['concepto']!=""){
			$tpl->newBlock("concepto_recibo");
			$tpl->assign("concepto",strtoupper($recibos[$i]['concepto']));
		}
		else{
			if($recibos[$i]['fecha'] != $recibos[$i]['fecha_pago']){
				$tpl->newBlock("concepto_recibo");
				$_fecha_1=explode("/",$recibos[$i]['fecha']);
				$concepto= "PAGO RENTA DE ".strtoupper(mes_escrito($_fecha_1[1]))." DEL ".$_fecha_1[2];
				$tpl->assign("concepto",$concepto);
			}
		}
	}
}


$tpl->printToScreen();
die();
?>