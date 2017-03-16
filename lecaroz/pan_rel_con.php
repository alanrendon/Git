<?php
//define ('IDSCREEN',1241); // ID de pantalla
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
$descripcion_error[1] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_rel_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['anio'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	
	for($i=1;$i<13;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",mes_escrito($i));
		if($i == date("n"))
			$tpl->assign("selected","selected");
		else
			$tpl->assign("selected","");
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
}

$diasxmes[1] = 31; // Enero
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre

if($_GET['bandera']==1){
	
	$sql="select * from venta_pastel where fecha_entrega between '1/".$_GET['mes']."/".$_GET['anio']."' and '".$diasxmes[number_format($_GET['mes'],'','','')]."/".$_GET['mes']."/".$_GET['anio']."' and num_cia=".$_GET['cia']." and kilos is not null order by fecha_entrega,letra_folio,num_remi";
	$facturas=ejecutar_script($sql,$dsn);
	
	$kilos=0;
	$num_facturas=0;

	$tpl->newBlock("por_mes");
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->assign("mes",mes_escrito($_GET['mes']));
	$tpl->assign("anio",$_GET['anio']);
	
	$total_kilos=0;
	$total_kilos_produccion=0;
	$total_facturas=0;
	$total_importe=0;
	
	for($i=1 ; $i<=$diasxmes[number_format($_GET['mes'],'','','')] ; $i++){
		$tpl->newBlock("facturas_mes");
		$tpl->assign("dia",$i);
		$kilos=0;
		$num_facturas=0;
		$importe=0;
		
		for($j=0;$j<count($facturas);$j++){
			$fecha = explode("/",$facturas[$j]['fecha_entrega']);
			if($fecha[0]==$i){
			//*******************************************FACTURA PAGADA EN EL CONTROL AMARILLO
				if($facturas[$j]['resta_pagar']==0){
					$kilos += $facturas[$j]['kilos'];
					$importe += $facturas[$j]['total_factura'];
					$importe -= number_format($facturas[$j]['base'],2,'.','');
					$num_facturas++;
				}
				else{
			//*******************************************FACTURA PAGADA CON CONTROL VERDE
					$sql="select * from venta_pastel where num_remi=".$facturas[$j]['num_remi']." and letra_folio='".$facturas[$j]['letra_folio']."' and num_cia=".$_GET['cia']." and resta is not null";
					$fac_kilos = ejecutar_script($sql,$dsn);
					if($fac_kilos){
						$kilos += $facturas[$j]['kilos'];
						$importe += $facturas[$j]['total_factura'];
						$num_facturas++;
					}
				}
			}
			else
				continue;
		}
		
		$sql="select sum(piezas) from produccion where cod_producto in (211,212,215,175,375,229,230,235,234) and num_cia=".$_GET['cia']." and fecha = '".$i."/".$_GET['mes']."/".$_GET['anio']."' and piezas is not null";
		$kilos_pedido=ejecutar_script($sql,$dsn);
		
		if($kilos_pedido[0]['sum']!=""){
			$tpl->assign("kilos_produccion",$kilos_pedido[0]['sum']);
			$total_kilos_produccion += number_format($kilos_pedido[0]['sum'],2,'.','');
		}
		else
			$tpl->assign("kilos_produccion","");
		
		if($kilos==0)
			$tpl->assign("kilos","");
		else{
			$tpl->assign("kilos",$kilos);
			$total_kilos += number_format($kilos,2,'.','');
		}

		if($num_facturas==0)
			$tpl->assign("facturas","");
		else{
			$tpl->assign("facturas",$num_facturas);
			$total_facturas += $num_facturas;
		}
			
		if($importe==0)
			$tpl->assign("importe","");
		else{
			$tpl->assign("importe",number_format($importe,2,'.',','));
			$total_importe += number_format($importe,2,'.','');
		}
	}
	$tpl->gotoBlock("por_mes");
	$tpl->assign("total_kilos",number_format($total_kilos,2,'.',','));
	$tpl->assign("total_kilos_produccion",number_format($total_kilos_produccion,2,'.',','));
	$tpl->assign("total_facturas",number_format($total_facturas,2,'.',','));
	$tpl->assign("total_importe",number_format($total_importe,2,'.',','));
	
}

//***************************************CONSULTA POR DIA
else{
	$tpl->newBlock("por_dia");
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$fecha=explode("/",$_GET['fecha']);
	$tpl->assign("dia",$fecha[0]);
	$tpl->assign("mes",mes_escrito($fecha[1]));
	$tpl->assign("anio",$fecha[2]);
	
	$sql="select * from venta_pastel where fecha_entrega ='".$_GET['fecha']."' and num_cia=".$_GET['cia']." and kilos is not null order by letra_folio,num_remi";
	$facturas=ejecutar_script($sql,$dsn);
	if(!$facturas){
		header("location: ./pan_rel_con.php?codigo_error=1");
		die();
	}
	$num_facturas=0;
	$kilos=0;
	$importe=0;
	
	for($i=0;$i<count($facturas);$i++){
	//*****************************************FACTURA YA PAGADA
		if($facturas[$i]['resta_pagar']==0){
			$tpl->newBlock("facturas_dia");
			$tpl->assign("num_remi",$facturas[$i]['num_remi']);
			if($facturas[$i]['letra_folio']=='X')
				$tpl->assign("let_folio","");
			else
				$tpl->assign("let_folio",$facturas[$i]['letra_folio']);
			
			$tpl->assign("kilos",$facturas[$i]['kilos']);
			$importe=$facturas[$i]['total_factura'] - number_format($facturas[$i]['base'],2,'.','');
			$tpl->assign("importe",number_format($importe,2,'.',','));
//			$tpl->assign("importe",number_format($facturas[$i]['total_factura'],2,'.',','));
			
			$num_facturas++;
			$kilos += $facturas[$i]['kilos'];
			$importe += $facturas[$i]['total_factura'] - number_format($facturas[$i]['base'],2,'.','');
		
		}
	//******************************************FACTURA CON CONTROL VERDE
		else{
	
			$sql="select * from venta_pastel where num_remi=".$facturas[$i]['num_remi']." and letra_folio='".$facturas[$i]['letra_folio']."' and num_cia=".$_GET['cia']." and resta is not null";
			$fac_kilos = ejecutar_script($sql,$dsn);
			if($fac_kilos){
				$tpl->newBlock("facturas_dia");
				$tpl->assign("num_remi",$fac_kilos[0]['num_remi']);
				if($fac_kilos[0]['letra_folio']=='X')
					$tpl->assign("let_folio","");
				else
					$tpl->assign("let_folio",$facturas[$i]['letra_folio']);
				
				$tpl->assign("kilos",$facturas[$i]['kilos']);
				$tpl->assign("importe",number_format($facturas[$i]['total_factura'],2,'.',','));
				
				$num_facturas++;
				$kilos += $facturas[$i]['kilos'];
				$importe += $facturas[$i]['total_factura'];
			}
		}
	}
	$tpl->gotoBlock("por_dia");
	if($kilos > 0)
		$tpl->assign("total_kilos",$kilos);
	if($importe > 0)
		$tpl->assign("total_importe",number_format($importe,2,'.',','));
	$tpl->assign("numero",$num_facturas);
	
	

	$sql="select sum(piezas) from produccion where cod_producto in (211,212,215,175,375,229,230,235,234) and num_cia=".$_GET['cia']." and fecha = '".$_GET['fecha']."' and piezas is not null";
	$kilos_pedido=ejecutar_script($sql,$dsn);
	
	$tpl->assign("total_produccion",$kilos_pedido[0]['sum']);
}
$tpl->printToScreen();
?>