<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
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
$descripcion_error[1] = "No se encontraron cheques";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cheques_list2.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha_inicial'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	
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
// -------------------------------- GENERAR ARREGLO ---------------------------------------------------------

$sql="select * from cheques WHERE fecha between '".$_GET['fecha_inicial']."' and '".$_GET['fecha_final']."'";
if($_GET['tipo_con']==0)
	$sql.=" and num_cia=".$_GET['cia'];
else if($_GET['tipo_con']==2)
	$sql.=" and num_proveedor=".$_GET['proveedor'];
else if($_GET['tipo_con']==3){
	$sql.=" and codgastos=".$_GET['gasto'];
	if($_GET['cia']!="")
		$sql.=" and num_cia=".$_GET['cia'];
	}
	
if($_GET['emitidos']==0)
	$sql.=" and imp=true";
else if($_GET['emitidos']==1)
	$sql.=" and imp=false";

if($_GET['cancelado']==0)
	$sql.=" and fecha_cancelacion is null";
else if($_GET['cancelado']==1)
	$sql.=" and fecha_cancelacion is not null";

$sql.=" order by num_cia,num_proveedor,fecha";

$cheques=ejecutar_script($sql,$dsn);
//----------------------------------------------------------------------------------------------------------
if(!$cheques){
	header("location: ./ban_cheques_list1.php?codigo_error=1");
	die();
}
//print_r($cia);

$nombremes[1]="ENERO";
$nombremes[2]="FEBRERO";
$nombremes[3]="MARZO";
$nombremes[4]="ABRIL";
$nombremes[5]="MAYO";
$nombremes[6]="JUNIO";
$nombremes[7]="JULIO";
$nombremes[8]="AGOSTO";
$nombremes[9]="SEPTIEMBRE";
$nombremes[10]="OCTUBRE";
$nombremes[11]="NOVIEMBRE";
$nombremes[12]="DICIEMBRE";
$total=0;
$_d1=explode("/",$_GET['fecha_inicial']);
$_d2=explode("/",$_GET['fecha_final']);

//echo $sql;

if($_GET['tipo_con']==3){
	$total=0;
	$tpl->newBlock("por_gasto");
	if($_GET['cancelado']==1){
		$tpl->newBlock("gas_can");
		$tpl->gotoBlock("por_gasto");
	}
	
	$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
	$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
	for($i=0;$i<count($cheques);$i+=2){
		$tpl->newBlock("row_gasto");
		$tpl->assign("num_cia",$cheques[$i]['num_cia']);
		$cia=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]["nombre_corto"]);
		$tpl->assign("folio",$cheques[$i]['folio']);
		if($cheques[$i]['fecha_cancelacion']!="")
			$tpl->assign("cantidad","CANCELADO");
		else
			$tpl->assign("cantidad",number_format($cheques[$i]['importe'],2,'.',','));
		

		if(($i+1)==count($cheques)) break;
		$tpl->assign("num_cia1",$cheques[$i+1]['num_cia']);
		$cia1=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i+1]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia1",$cia1[0]["nombre_corto"]);
		$tpl->assign("folio1",$cheques[$i+1]['folio']);
		if($cheques[$i+1]['fecha_cancelacion']!="")
			$tpl->assign("cantidad1","CANCELADO");
		else
			$tpl->assign("cantidad1",number_format($cheques[$i+1]['importe'],2,'.',','));
		
	}
	for($i=0;$i<count($cheques);$i++){
		if($cheques[$i]['fecha_cancelacion']!="")
			continue;
		else
			$total+=number_format($cheques[$i]['importe'],2,'.','');
	}
	$tpl->gotoBlock("por_gasto");
	$tpl->assign("total",number_format($total,2,'.',','));
}

//CHEQUES POR PROVEEDOR

else if($_GET['tipo_con']==2){

	if($_GET['cancelado']==1)
	{
		$tpl->newBlock("listado_por_proveedores_cancelados");
	}
	else
	{
		$tpl->newBlock("listado_por_proveedores");
	}

//	$tpl->newBlock("listado_por_proveedores");
	$aux=0;
	$aux1=0;
	$tpl->assign("num_proveedor",$cheques[0]['num_proveedor']);
	$tpl->assign("nombre_proveedor",$cheques[0]['a_nombre']);
	if($_GET['emitidos']==0)
		$tpl->assign('emitidos',"SOLO EMITIDOS");
	else if($_GET['emitidos']==1)
		$tpl->assign('emitidos',"SIN EMITIR");
	$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
	$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
	$total=0;

	for($i=0;$i<count($cheques);$i++)
	{
		if($aux!=$cheques[$i]['num_cia'])
		{
			$tot_cia=0;
			if($_GET['cancelado']==1)
			{
				$tpl->newBlock("cia_cancel");
			}
			else
			{
				$tpl->newBlock("cia");
			}

//			$tpl->newBlock("cia");
			$tpl->assign("num_cia",$cheques[$i]['num_cia']);
			$cia=obtener_registro("catalogo_companias",array('num_cia'),array($cheques[$i]['num_cia']),"","",$dsn);
			$tpl->assign('nombre_cia',$cia[0]['nombre']);
			$tpl->assign('num_cuenta',$cia[0]['clabe_cuenta']);
			$aux=$cheques[$i]['num_cia'];
		}

		if($_GET['cancelado']==1)
		{
			$tpl->newBlock("reg_cancel");
			$tpl->assign("folio",$cheques[$i]['folio']);
			$tpl->assign("fecha",$cheques[$i]['fecha']);
			$tpl->assign("fecha1",$cheques[$i]['fecha_cancelacion']);
			
		}
		else
		{
			$tpl->newBlock("reg");
			$tpl->assign("folio",$cheques[$i]['folio']);
			$tpl->assign("fecha",$cheques[$i]['fecha']);
			if($cheques[$i]['fecha_cancelacion']=="")
			{
				if($cheques[$i]['importe']<0){
					$tpl->newBlock("cheque_prov_error");
					$tpl->assign("importe","CANCELADO");
				}
				else{
					$tpl->newBlock("cheque_prov_ok");
					$tpl->assign("importe",number_format($cheques[$i]['importe'],2,'.',','));
					$total+=$cheques[$i]['importe'];
					$tot_cia +=$cheques[$i]['importe'];
					$tpl->gotoBlock("cia");
					$tpl->assign("total_cia",number_format($tot_cia,2,'.',','));
				}
			}
			else
			{
				$tpl->newBlock("cheque_prov_error");
				$tpl->assign("importe","CANCELADO");
			}
		}
	}
	if($_GET['cancelado']!=1){
		$tpl->gotoBlock("listado_por_proveedores");
		$tpl->assign("total_proveedor",number_format($total,2,'.',','));
	}
}

else if($_GET['tipo_con']==0 or $_GET['tipo_con']==1){

	//SOLO CHEQUES CANCELADOS
	if($_GET['cancelado']==1){
		$aux_cia=0;
		$aux_proveedor=0;
		$total=0;
		$tpl->newBlock("listado_cancelado");

		for($i=0;$i<count($cheques);$i++){
			if($aux_cia!=$cheques[$i]["num_cia"]){
				$aux_proveedor=0;
				$tpl->newBlock("companias_cancelado");
				if($_GET['emitidos']==0)
					$tpl->assign('emitidos',"SOLO EMITIDOS");
				else if($_GET['emitidos']==1)
					$tpl->assign('emitidos',"SIN EMITIR");
				
				$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
				$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
				$tpl->assign("num_cia",$cheques[$i]['num_cia']);
				$cia=obtener_registro("catalogo_companias",array('num_cia'),array($cheques[$i]['num_cia']),"","",$dsn);
				$tpl->assign('nombre_cia',$cia[0]['nombre']);
				$tpl->assign('num_cuenta',$cia[0]['clabe_cuenta']);
				
			}
			$aux_cia=$cheques[$i]["num_cia"];
			
			if($aux_proveedor != $cheques[$i]['num_proveedor']){
				$tpl->newBlock("proveedor_cancelado");
				$tpl->assign("num_proveedor",$cheques[$i]['num_proveedor']);
				$tpl->assign("nombre_proveedor",$cheques[$i]['a_nombre']);
			}
			$aux_proveedor=$cheques[$i]['num_proveedor'];
			$tpl->newBlock("folios_cancelados");
			$tpl->assign("folio",$cheques[$i]['folio']);
			$tpl->assign("fecha",$cheques[$i]['fecha']);
			$tpl->assign("fecha1",$cheques[$i]['fecha_cancelacion']);
		}
	}
	
	else{
		$aux_cia=0;
		$aux_proveedor=0;
		$total=0;
		$tpl->newBlock("listado_por_companias");
		for($i=0;$i<count($cheques);$i++){
			if($aux_cia!=$cheques[$i]["num_cia"]){
				$total=0;
				$aux_proveedor=0;
				$tpl->newBlock("compania");
				if($_GET['emitidos']==0)
					$tpl->assign('emitidos',"SOLO EMITIDOS");
				else if($_GET['emitidos']==1)
					$tpl->assign('emitidos',"SIN EMITIR");
				
				$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
				$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
				$tpl->assign("num_cia",$cheques[$i]['num_cia']);
				$cia=obtener_registro("catalogo_companias",array('num_cia'),array($cheques[$i]['num_cia']),"","",$dsn);
				$tpl->assign('nombre_cia',$cia[0]['nombre']);
				$tpl->assign('num_cuenta',$cia[0]['clabe_cuenta']);
				
			}
			$aux_cia=$cheques[$i]["num_cia"];
			
			$tpl->newBlock("folios");
			$tpl->assign("num_proveedor",$cheques[$i]['num_proveedor']);
			$tpl->assign("nombre_proveedor",$cheques[$i]['a_nombre']);
			$tpl->assign("concepto",$cheques[$i]['concepto']);
			$tpl->assign("folio",$cheques[$i]['folio']);
			$tpl->assign("fecha",$cheques[$i]['fecha']);
			$tpl->assign("codgasto",$cheques[$i]['codgastos']);
			$gasto=obtener_registro("catalogo_gastos",array("codgastos"),array($cheques[$i]['codgastos']),"","",$dsn);
			$tpl->assign("nombre_gasto",$gasto[0]['descripcion']);
			
			if($cheques[$i]['fecha_cancelacion']=="")
			{
				if($cheques[$i]['importe']<0){
					$tpl->newBlock("cheque_error");
					$tpl->assign("importe","CANCELADO");
				}
				else{
					if(existe_registro("estado_cuenta",array("num_cia","folio"),array($cheques[$i]["num_cia"],$cheques[$i]['folio']),$dsn)){
						$sql="SELECT * from estado_cuenta where num_cia=".$cheques[$i]["num_cia"]." and folio=".$cheques[$i]['folio'];
						$conciliacion=ejecutar_script($sql,$dsn);
						$tpl->assign("conciliacion",$conciliacion[0]['fecha_con']);
					}
					$tpl->newBlock("cheque_ok");
					$tpl->assign("importe",number_format($cheques[$i]['importe'],2,'.',','));
					$total+=$cheques[$i]['importe'];
				}
			}
			else
			{
				$tpl->newBlock("cheque_error");
				$tpl->assign("importe","CANCELADO");
			}
			$tpl->gotoBlock("compania");
			$tpl->assign("total_cia",number_format($total,2,'.',','));

		}
	}

}



$tpl->printToScreen();
?>