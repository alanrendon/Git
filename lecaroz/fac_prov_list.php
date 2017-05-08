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
$tpl->assignInclude("body","./plantillas/fac/fac_prov_list.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("obtener_datos");
//echo date( "d/m/Y", mktime(0,0,0,12,30,1997) );

	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha",date("d/m/Y"));
	
	for($j=1;$j<=12;$j++){
		$tpl->newBlock("mes");
		$tpl->assign("num_mes",$j);
		$tpl->assign("nom_mes",mes_escrito($j));
		if ($j == date("n")) $tpl->assign("checked","selected");
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
// -------------------------------- Mostrar listado ---------------------------------------------------------
$sql="select * from facturas where ";
$anio=$_GET['anio'];
$diasxmes[1] = 31; // Enero
if ($anio%4 == 0)
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


$fecha="01/".$_GET['mes']."/".$anio;
$fecha1=$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$anio;
if ($_GET['bandera']==0){
	if($_GET['cia']==""){  
//		if($_SESSION['iduser']==8 or $_SESSION['iduser']==5)
		if($_GET['tipo_user']==1)
			$sql.=" fecha_captura='".$_GET['fecha']."' and concepto not like 'FACTURA%' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" fecha_captura='".$_GET['fecha']."' and concepto not like 'FACTURA%' order by num_cia, num_proveedor, fecha";
	}
	else{
//		if($_SESSION['iduser']==8 or $_SESSION['iduser']==5)
		if($_GET['tipo_user']==1)
			$sql.=" num_cia='".$_GET['cia']."' and fecha_captura='".$_GET['fecha']."' and concepto not like 'FACTURA%' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" num_cia='".$_GET['cia']."' and fecha_captura='".$_GET['fecha']."' and concepto not like 'FACTURA%' order by num_cia, num_proveedor, fecha";
	}
}

else{
	if($_GET['cia']==""){
		if($_GET['tipo_user']==1)
			$sql.=" fecha between '".$fecha."' and '".$fecha1."' and concepto not like 'FACTURA%' and iduser= ".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" fecha between '".$fecha."' and '".$fecha1."' and concepto not like 'FACTURA%' order by num_cia, num_proveedor, fecha";
	}		
	else
		if($_GET['tipo_user']==1)
			$sql.=" num_cia='".$_GET['cia']."' and fecha between '".$fecha."' and '".$fecha1."' and concepto not like 'FACTURA%' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha_mov";
		else
			$sql.=" num_cia='".$_GET['cia']."' and fecha between '".$fecha."' and '".$fecha1."' and concepto not like 'FACTURA%' order by num_cia, num_proveedor, fecha";

}
$entrada=ejecutar_script($sql,$dsn);
//		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
//------------------------------------------------------------------------------------------------------------
if(!$entrada)
{
	header("location: ./fac_fac_list.php?codigo_error=1");
	die();
}
$tpl->newBlock("listado");
$tpl->assign("anio",$_GET['anio']);
if ($_GET['bandera']==0)
	$tpl->assign("fecha",$_GET['fecha']);
else{
	switch ($_GET['mes']) {
		   case 1:
			   $tpl->assign("fecha"," de Enero");
			   break;
		   case 2:
			   $tpl->assign("fecha","de Febrero");
			   break;
		   case 3:
			   $tpl->assign("fecha","de Marzo");
			   break;
		   case 4:
			   $tpl->assign("fecha","de Abril");
			   break;
		   case 5:
			   $tpl->assign("fecha","de Mayo");
			   break;
		   case 6:
			   $tpl->assign("fecha","de Junio");
			   break;
		   case 7:
			   $tpl->assign("fecha","de Julio");
			   break;
		   case 8:
			   $tpl->assign("fecha","de Agosto");
			   break;
		   case 9:
			   $tpl->assign("fecha","de Septiembre");
			   break;
		   case 10:
			   $tpl->assign("fecha","de Octubre");
			   break;
		   case 11:
			   $tpl->assign("fecha","de Noviembre");
			   break;
		   case 12:
			   $tpl->assign("fecha","de Diciembre");
			   break;
	}
}


$tmp=0;
$tmp1=0;
$tmp2=0;
$tmp3=0;
$total=0;
$cont=0;
$bandera=false;

for($i=0;$i<count($entrada);$i++)
{
	if($entrada[$i]['num_cia']!=$tmp)
	{
		$tpl->newBlock("companias");
		$tpl->assign("num_cia",$entrada[$i]['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($entrada[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		$tmp1=0;
	}
	
	$tmp=$entrada[$i]['num_cia'];
	if($entrada[$i]['num_proveedor']!=$tmp1)
	{
		$tpl->newBlock("proveedor");
		$tpl->assign("num_proveedor",$entrada[$i]['num_proveedor']);
		$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($entrada[$i]['num_proveedor']),"","",$dsn);
		$tpl->assign("nombre_proveedor",$prov[0]['nombre']);
		$tmp2=0;
	}
	
	$tmp1=$entrada[$i]['num_proveedor'];
	if($entrada[$i]['num_fact']!=$tmp2)
	{
		$tpl->newBlock("rows");
		$tpl->assign("fecha",$entrada[$i]['fecha']);
		$tpl->assign("num_fac",$entrada[$i]['num_fact']);
		$tpl->assign("concepto",$entrada[$i]['concepto']);
		$tpl->assign("valores",number_format($entrada[$i]['total'],2,'.',','));

		$total+=$entrada[$i]['total'];
	}
//---------------------------------
//	$tmp3=$entrada[$i]['num_documento'];
/*	if($entrada[$i]['num_fact']==$tmp3)
	{
		$cont++;
		if ($cont>1)
		{
		$tpl->newBlock("totalfac");
		$tpl->assign("fac",number_format($entrada[$i]['costo_total'],2,'.',','));
		$cont=1;
		}
	}
	else if ($entrada[$i]['num_fact']!=$tmp3 and $i==0)
	{ 
		$cont++;
	}
	*/

//	$tmp3=$entrada[$i]['num_fact'];
	$tmp2=$entrada[$i]['num_fact'];

}
$tpl->gotoBlock("listado");
$tpl->assign("total1",number_format($total,2,'.',','));


$tpl->printToScreen();

?>