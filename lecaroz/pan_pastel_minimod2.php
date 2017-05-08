<?php
// MODIFICACIÓN RÁPIDA DE UN PRODUCTO EN CONTROL DE PRODUCCION
// Tablas 'control_produccion'
// Menu 'No definido'
//define ('IDSCREEN',2); // ID de pantalla
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
//$descripcion_error[]
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pastel_minimod2.tpl");
$tpl->prepare();
$venta=0;
$dif_otros=0;
$dif_venta=0;
//$mostrar_venta_puerta=0;
//$mostrar_gastos=0;
//$mostrar_abono_expendio=0;

//MODIFICACION DE LA NOTA
if (isset($_POST['idpastel'])) {
	ejecutar_script("UPDATE modificacion_pastel SET fecha_modificacion='".date("d/m/Y")."' where id=".$_POST['idmodifica'],$dsn);
	$fac=obtener_registro("venta_pastel",array('id'),array($_POST['idpastel']),"","",$dsn);
	$mod=obtener_registro("modificacion_pastel",array("id"),array($_POST['idmodifica']),"","",$dsn);
//	$blocs=ejecutar_script("SELECT * FROM bloc WHERE idcia=".$fac[0]['num_cia']." order by folio_inicio",$dsn);
	$mostrar_venta_puerta=0;
	$mostrar_gastos=0;
	$mostrar_abono_expendio=0;

	if($mod[0]['perdida']=='t'){
		ejecutar_script("UPDATE modificacion_pastel SET fecha_modificacion='".date("d/m/Y")."' where id=".$_POST['idmodifica'],$dsn);
	}
//CAMBIO DE FECHA DE ENTREGA DE LA NOTA DE PASTEL	
	else if($mod[0]['cambio_fecha']=='t'){
		$sql="UPDATE venta_pastel set fecha_entrega='".$_POST['fecha_entrega']."' WHERE id=".$_POST['idpastel'];
		ejecutar_script($sql,$dsn);
	}

//CAMBIO DE FECHA DE LAS NOTAS DE PASTEL	
	else if($mod[0]['fecha_nueva']=="t"){
		ejecutar_script("UPDATE venta_pastel SET fecha='".$_POST["fecha_nueva"]."' WHERE id=".$_POST['idpastel'],$dsn);
//-------------------------------
		//si la nota NO es de expendios		
		if($fac[0]['idexpendio']==""){
			//La nota es control amarillo
			if($fac[0]['fecha_entrega']!=""){
				$venta=number_format($fac[0]['cuenta'],2,'.','') - number_format($fac[0]['base'],2,'.','');
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['cuenta'],2,'.','').", venta_pastel = venta_pastel - ".number_format($fac[0]['cuenta'],2,'.','').", venta_puerta = venta_puerta - ".number_format($venta,2,'.','').", otros=otros - ".number_format($fac[0]['base'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$fac[0]['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE total_panaderias SET efectivo= efectivo + ".number_format($fac[0]['cuenta'],2,'.','').", venta_pastel = venta_pastel + ".number_format($fac[0]['cuenta'],2,'.','').", venta_puerta = venta_puerta + ".number_format($venta,2,'.','').", otros=otros + ".number_format($fac[0]['base'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha_nueva']."'";
				ejecutar_script($sql,$dsn);
			}
			//La nota es de control verde
			else{
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['resta'],2,'.','').", venta_pastel = venta_pastel - ".number_format($fac[0]['resta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$fac[0]['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql2="UPDATE total_panaderias SET efectivo= efectivo + ".number_format($fac[0]['resta'],2,'.','').", venta_pastel = venta_pastel + ".number_format($fac[0]['resta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha_nueva']."'";
				ejecutar_script($sql,$dsn);
			}
		}
		//si la nota SI es de expendios
		else{
			$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($fac[0]['num_cia'],$fac[0]['idexpendio']),"","",$dsn);
			$venta = $venta - $venta * ($porcentaje[0]['porciento_ganancia']/100);

			if($fac[0]['fecha_entrega']!=""){
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['cuenta'],2,'.','').", abono_pastel = abono_pastel - ".number_format($fac[0]['cuenta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$fac[0]['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago - ".$fac[0]['cuenta']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$fac[0]['fecha']."' AND num_expendio=".$fac[0]['idexpendio']."";
				ejecutar_script($sql,$dsn);

				$sql="UPDATE total_panaderias SET efectivo= efectivo + ".number_format($fac[0]['cuenta'],2,'.','').", abono_pastel = abono_pastel + ".number_format($fac[0]['cuenta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha_nueva']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago + ".number_format($fac[0]['cuenta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$_POST['fecha_nueva']."' AND num_expendio=".$fac[0]['idexpendio']."";
				ejecutar_script($sql,$dsn);


			}
			else{
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['resta'],2,'.','').", abono_pastel = abono_pastel - ".number_format($fac[0]['resta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$fac[0]['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago - ".number_format($fac[0]['total_factura'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$fac[0]['fecha']."' AND num_expendio=".$fac[0]['idexpendio'];
				ejecutar_script($sql,$dsn);

				$sql="UPDATE total_panaderias SET efectivo= efectivo + ".number_format($fac[0]['resta'],2,'.','').", abono_pastel = abono_pastel + ".number_format($fac[0]['resta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha_nueva']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago + ".number_format($fac[0]['total_factura'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$_POST['fecha_nueva']."' AND num_expendio=".$fac[0]['idexpendio'];
				ejecutar_script($sql,$dsn);

			}
		}
//-------------------------------		
	}

//CANCELACION DE FACTURAS DE PASTEL	
	else if($mod[0]['cancelar']=='t'){
		
		//Busca las facturas
		$sql="SELECT * FROM venta_pastel where num_cia=".$fac[0]['num_cia']." and num_remi=".$fac[0]['num_remi']." and letra_folio='".$fac[0]['letra_folio']."' and dev_base is null" ;
		$facturas_pastel=ejecutar_script($sql,$dsn);
		
		$sql="UPDATE venta_pastel SET resta_pagar=0, estado=2 where id=".$_POST['idpastel'];// SE AGREGO EL ESTADO DE CANCELADO AL CONTROL AMARILLO
		ejecutar_script($sql,$dsn);
	
		$venta=number_format($fac[0]['cuenta'],2,'.','') - number_format($fac[0]['base'],2,'.','');
		
		if($fac[0]['idexpendio']==""){
			if(count($facturas_pastel) < 2){
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['cuenta'],2,'.','').", venta_pastel = venta_pastel - ".number_format($fac[0]['cuenta'],2,'.','').", gastos=gastos + ".number_format($fac[0]['cuenta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
				ejecutar_script($sql,$dsn);
			}
			else{//SE CREARA MODIFICACION AQUI
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['total_factura'],2,'.','').", venta_pastel = venta_pastel - ".number_format($fac[0]['total_factura'],2,'.','').", gastos=gastos + ".number_format($fac[0]['total_factura'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
				ejecutar_script($sql,$dsn);
			}
		}
		else{
			$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($fac[0]['num_cia'],$fac[0]['idexpendio']),"","",$dsn);
			$venta = $venta - $venta * ($porcentaje[0]['porciento_ganancia']/100);

			if(count($facturas_pastel) < 2){
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['cuenta'],2,'.','').", abono_pastel = abono_pastel - ".number_format($fac[0]['cuenta'],2,'.','').", gastos = gastos + ".number_format($fac[0]['cuenta'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago - ".$fac[0]['resta_pagar']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio']."";
				ejecutar_script($sql,$dsn);
			}
			else{//SE CREARA MODIFICACION AQUI
				$sql="UPDATE total_panaderias SET efectivo= efectivo - ".number_format($fac[0]['total_factura'],2,'.','').", abono_pastel = abono_pastel - ".number_format($fac[0]['total_factura'],2,'.','').", gastos = gastos + ".number_format($fac[0]['total_factura'],2,'.','')." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
				ejecutar_script($sql,$dsn);
				$sql="UPDATE mov_expendios SET rezago = rezago - ".$fac[0]['total_factura']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >= '".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio']."";
				ejecutar_script($sql,$dsn);
			}
		}
		
		if(count($facturas_pastel) < 2){
			$sql="INSERT INTO movimiento_gastos(codgastos,num_cia,fecha,importe,concepto, captura) VALUES(115,".$fac[0]['num_cia'].",'".$_POST['fecha']."',".number_format($fac[0]['cuenta'],2,'.','').",'CANCELACION DE FACTURA DE PASTEL','false')";
			ejecutar_script($sql,$dsn);
			$mostrar_gastos=$fac[0]['cuenta'];
		}
		else{//SE CREARA MODIFICACION AQUI
			$sql="INSERT INTO movimiento_gastos(codgastos,num_cia,fecha,importe,concepto, captura) VALUES(115,".$fac[0]['num_cia'].",'".$_POST['fecha']."',".number_format($fac[0]['resta_pagar'],2,'.','').",'CANCELACION DE FACTURA DE PASTEL','false')";
			ejecutar_script($sql,$dsn);
			$mostrar_gastos=$fac[0]['resta_pagar'];
		}

	}
	//MODIFICACION DE KILOS, PRECIO UNIDAD, BASES Y OTROS
//--------------------------------------------------------------------------------------	
	else{
		$kilos=number_format($_POST['kilos'],2,'.','') + number_format($_POST['kilos_mas'],2,'.','') - number_format($_POST['kilos_menos'],2,'.','');
		if($_POST['resta_pagar'] >= 0){
			ejecutar_script("
			UPDATE venta_pastel SET
			kilos=".$kilos.",
			precio_unidad=".$_POST['precio_unidad'].",
			otros=".$_POST['otros'].",
			base=".$_POST['base'].",
			cuenta=".$_POST['cuenta'].",
			resta_pagar=".$_POST['resta_pagar'].",
			total_factura=".$_POST['total_factura'].",
			estado=1
			WHERE
			id=".$_POST['idpastel']
			,$dsn);
		}
		else if($_POST['resta_pagar'] < 0){
			ejecutar_script("
			UPDATE venta_pastel SET
			kilos=".$kilos.",
			precio_unidad=".$_POST['precio_unidad'].",
			otros=".$_POST['otros'].",
			base=".$_POST['base'].",
			cuenta=".$_POST['cuenta'].",
			resta_pagar=0,
			total_factura=".$_POST['total_factura'].",
			estado=1
			WHERE
			id=".$_POST['idpastel']
			,$dsn);
			
			ejecutar_script("
			INSERT INTO movimiento_gastos(codgastos,num_cia,fecha,importe,concepto,captura)
			VALUES (115,".$fac[0]['num_cia'].",'".$_POST['fecha']."',".abs($_POST['resta_pagar']).",'MODIFICACION DE NOTA',false)
			",$dsn);
			ejecutar_script("
			UPDATE total_panaderias set gastos=gastos + ".abs($_POST['resta_pagar']).", efectivo=efectivo-".abs($_POST['resta_pagar'])." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'
			",$dsn);
		}
		
		if($fac[0]['idexpendio']==""){
			if($_POST['resta_pagar'] > 0){
				$sql="INSERT INTO venta_pastel (num_cia,fecha,num_remi,letra_folio,resta,tipo) VALUES (".$fac[0]['num_cia'].",'".$_POST['fecha']."', ".$fac[0]['num_remi'].", '".$fac[0]['letra_folio']."', ".$_POST['resta_pagar'].",1)";
				ejecutar_script($sql,$dsn);
			}
		
			if($fac[0]['base'] >= $_POST['base'])
			{
				if($_POST['resta_pagar'] > 0){
					$sql="UPDATE total_panaderias SET venta_puerta= venta_puerta + ".$_POST['resta_pagar'].", efectivo=efectivo + ".$_POST['resta_pagar'].", venta_pastel = venta_pastel + ".$_POST['resta_pagar']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
					ejecutar_script($sql,$dsn);
					$mostrar_venta_puerta=$_POST['resta_pagar'];
				}
				
			}
			else if($fac[0]['base'] < $_POST['base'])
			{
				$dif_otros=$_POST['base'] - $fac[0]['base'];
				$dif_venta=$_POST['resta_pagar'] - $dif_otros;
				$dif_efec = $dif_venta + $dif_otros;
				if($_POST['resta_pagar'] > 0){
					$sql="UPDATE total_panaderias SET venta_puerta= venta_puerta + ".$dif_venta.", otros = otros + ".$dif_otros.", venta_pastel = venta_pastel + ".$dif_venta.", efectivo=efectivo+".$dif_efec." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
					ejecutar_script($sql,$dsn);
					$mostrar_venta_puerta=$dif_venta;
				}
			}
		}
		else{
			if($_POST['resta_pagar'] >= 0){
				$sql="INSERT INTO venta_pastel (num_cia,fecha,num_remi,letra_folio,resta,idexpendio,tipo) VALUES (".$fac[0]['num_cia'].",'".$_POST['fecha']."', ".$fac[0]['num_remi'].", '".$fac[0]['letra_folio']."', ".$_POST['resta_pagar'].", ".$fac[0]['idexpendio'].",1)";
				ejecutar_script($sql,$dsn);
			}

			if($fac[0]['base'] >= $_POST['base'])
			{
				if($_POST['resta_pagar'] > 0){
					$sql="UPDATE mov_expendios SET abono= abono + ".$_POST['resta_pagar']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio'];
					ejecutar_script($sql,$dsn);
					$mostrar_abono_expendio=$_POST['resta_pagar'];
					$sql="UPDATE mov_expendios SET rezago = rezago -".$_POST['resta_pagar']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >='".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio'];
					ejecutar_script($sql,$dsn);
					$sql="UPDATE total_panaderias SET abono = abono + ".$_POST['resta_pagar'].", efectivo=efectivo + ".$_POST['resta_pagar'].", abono_pastel = abono_pastel + ".$_POST['resta_pagar']." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
					ejecutar_script($sql,$dsn);
				}
			}
			else if($fac[0]['base'] < $_POST['base'])
			{
				$dif_otros=$_POST['base'] - $fac[0]['base'];
				$dif_venta=$_POST['resta_pagar'] - $dif_otros;

				if($_POST['resta_pagar'] > 0){
					$sql="UPDATE mov_expendios SET abono = abono + ".$dif_venta." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio'];
					ejecutar_script($sql,$dsn);
					
					$sql="UDATE mov_expendios SET rezago=rezago - ".$dif_venta." WHERE num_cia=".$fac[0]['num_cia']." AND fecha >='".$_POST['fecha']."' AND num_expendio=".$fac[0]['idexpendio'];
					ejecutar_script($sql,$dsn);
					
					$sql="UPDATE total_panaderias SET otros = otros + ".$dif_otros.", efectivo=efectivo+".$dif_venta." WHERE num_cia=".$fac[0]['num_cia']." AND fecha='".$_POST['fecha']."'";
					ejecutar_script($sql,$dsn);
					$mostrar_abono_expendio=$dif_venta;
				}
			}
		}
	}
//-------------------------------------------------------------
	
	if($mod[0]['cambio_fecha'] == 'f' and $mod[0]['perdida']=='f' and $mod[0]['fecha_nueva']=='f' and $mod[0]['cancelar']=='f'){
		$bloc=ejecutar_script("select * from bloc where folio_inicio <= ".$fac[0]['num_remi']." and folio_final >=".$fac[0]['num_remi']." and idcia=".$fac[0]['num_cia']." and let_folio='".$fac[0]['letra_folio']."'",$dsn);
		$indice=$bloc[0]['id'];
		$folios_usados=$bloc[0]['folios_usados'];
		$num_folios=$bloc[0]['num_folios'];
		
		$sql="UPDATE bloc SET folios_usados= folios_usados + 1 where id=".$indice;
		ejecutar_script($sql,$dsn);
		
		$folios_usados++;
		if($folios_usados >= $num_folios){
			$sql="UPDATE bloc SET estado=true where id=".$indice;
			ejecutar_script($sql,$dsn);
		}
	}
	
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia",$fac[0]['num_cia']);
	$sql="select num_cia,nombre_corto from catalogo_companias where num_cia=".$fac[0]['num_cia'];
	$ncia=ejecutar_script($sql,$dsn);
	$tpl->assign("fecha",$_POST['fecha']);
	if($fac[0]['letra_folio']=='X')
		$tpl->assign("let_folio","");
	else
		$tpl->assign("let_folio",$fac[0]['letra_folio']);

	$tpl->assign("num_remi",$fac[0]['num_remi']);
	$tpl->assign("nombre_cia",$ncia[0]['nombre_corto']);
	$tpl->assign("venta_puerta",number_format($mostrar_venta_puerta,2,'.',','));
	$tpl->assign("abono_exp",number_format($mostrar_abono_expendio,2,'.',','));
	$tpl->assign("gastos",number_format($mostrar_gastos,2,'.',','));
	
	if($mod[0]['perdida'] == 't'){
		$tpl->newBlock("perdida_control");
	}
	
	else if($mod[0]['cambio_fecha'] == 't'){
		$tpl->newBlock("cambio_fecha1");
	}

	$tpl->printToScreen();
	die;
}

$tpl->newBlock("modificar");
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("fecha",date("d/m/Y"));
//print_r($_GET);

// Obtener datos del producto
$pastel = ejecutar_script("SELECT * FROM venta_pastel WHERE letra_folio='".$_GET['let_folio']."' and num_remi=".$_GET['num_remi']." and num_cia=".$_GET['num_cia']." and fecha_entrega is not null",$dsn);
$modificar = ejecutar_script("SELECT * FROM modificacion_pastel WHERE id = ".$_GET['idmodifica'],$dsn);
$tpl->assign("idpastel",$pastel[0]['id']);
$tpl->assign("idmodifica",$_GET['idmodifica']);
$tpl->assign("cuenta",($pastel[0]['cuenta'] > 0)?number_format($pastel[0]['cuenta'],2,".",""):0);
if($pastel[0]['letra_folio']=='X')
	$tpl->assign("let_folio",'');
else
	$tpl->assign("let_folio",$pastel[0]['letra_folio']);
$tpl->assign("num_remi",$pastel[0]['num_remi']);
$tpl->assign("fecha_fac",$pastel[0]['fecha']);
$tpl->assign("kilos_capturados",($pastel[0]['kilos'] > 0)?number_format($pastel[0]['kilos'],2,".",""):0);
$tpl->assign("precio_unidad",($pastel[0]['precio_unidad'] > 0)?number_format($pastel[0]['precio_unidad'],2,".",""):0);
$tpl->assign("otros",($pastel[0]['otros'] > 0)?number_format($pastel[0]['otros'],2,".",""):0);
$tpl->assign("base",($pastel[0]['base'] > 0)?number_format($pastel[0]['base'],2,".",""):0);
$tpl->assign("total_factura",($pastel[0]['total_factura'] > 0)?number_format($pastel[0]['total_factura'],2,".",""):0);
$tpl->assign("resta_pagar", number_format($pastel[0]['resta_pagar'],2,".",""));

$tpl->assign("seleccion","fecha");	

//abre los campos para modificar segun la solicitud
if($modificar[0]['kilos_mas']=='f') 
	$tpl->assign("leerkilosmas","readonly");
else{
	$tpl->assign("color","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","0");
}
	
if($modificar[0]['kilos_menos']=='f') 
	$tpl->assign("leerkilosmenos","readonly");
else{
	$tpl->assign("color1","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","0");
}
	
if($modificar[0]['base']=='f')
	$tpl->assign("leerbase","readonly");
else{
	$tpl->assign("color4","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","0");
}
	
if($modificar[0]['precio_unidad']=='f')
	$tpl->assign("leerprecio","readonly");
else{
	$tpl->assign("color2","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","0");
}

if($modificar[0]['otros']=='f')
	$tpl->assign("leerotros","readonly");
else{
	$tpl->assign("color3","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","0");
}
	
if($modificar[0]['cancelar']=='t'){
	$tpl->assign("movimiento","0");
	$tpl->newBlock("cancelar");
}

if($modificar[0]['perdida']=='t'){
	$tpl->assign("readonly","readonly");
	$tpl->assign("movimiento","1");
	$tpl->newBlock("perdida");
	}

if($modificar[0]['cambio_fecha']=='t'){
	$tpl->assign("readonly","readonly");
	$tpl->assign("seleccion","fecha_entrega");
	$tpl->assign("color5","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","2");
	$tpl->newBlock("cambio_fecha");
}
else{
	$tpl->gotoBlock("modificar");
	$tpl->assign("fechaentrega","readonly");
}
	
if($modificar[0]['fecha_nueva']=='t'){
	$tpl->assign("seleccion","fecha_nueva");
	$tpl->assign("readonly","readonly");
	$tpl->assign("color6","bgcolor='#FFFF99'");
	$tpl->assign("movimiento","3");
	$tpl->newBlock("fecha_nueva");
}

else{
	$tpl->gotoBlock("modificar");
	$tpl->assign("nuevafecha","readonly");
}

if($pastel[0]["estado"] == 1){
	$tpl->newBlock("factura_pagada");
}
// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>