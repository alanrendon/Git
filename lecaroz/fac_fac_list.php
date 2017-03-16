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
$tpl->assignInclude("body","./plantillas/fac/fac_fac_list.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha",date("d/m/Y"));


//echo date( "d/m/Y", mktime(0,0,0,12,30,1997) );
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
$sql="select * from entrada_mp where ";

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
			$sql.=" fecha_captura='".$_GET['fecha']."' and iduser = ".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" fecha_captura='".$_GET['fecha']."' order by num_cia, num_proveedor, fecha";
	}
	else{
//		if($_SESSION['iduser']==8 or $_SESSION['iduser']==5)
		if($_GET['tipo_user']==1)
			$sql.=" num_cia='".$_GET['cia']."' and fecha_captura='".$_GET['fecha']."' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" num_cia='".$_GET['cia']."' and fecha_captura='".$_GET['fecha']."' order by num_cia, num_proveedor, fecha";
	}
}
else{
	if($_GET['cia']=="")
		if($_GET['tipo_user']==1)
			$sql.=" fecha between '".$fecha."' and '".$fecha1."' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" fecha between '".$fecha."' and '".$fecha1."' order by num_cia, num_proveedor, fecha";
	else
		if($_GET['tipo_user']==1)
			$sql.=" num_cia='".$_GET['cia']."' and fecha between '".$fecha."' and '".$fecha1."' and iduser=".$_SESSION['iduser']." order by num_cia, num_proveedor, fecha";
		else
			$sql.=" num_cia='".$_GET['cia']."' and fecha between '".$fecha."' and '".$fecha1."' order by num_cia, num_proveedor, fecha";
}
$entrada=ejecutar_script($sql,$dsn);
//		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
//------------------------------------------------------------------------------------------------------------
//echo $sql;
if(!$entrada)
{
	header("location: ./fac_fac_list.php?codigo_error=1");
	die();
}
$tpl->newBlock("listado");
//echo $sql;
$tpl->assign("anio",$_GET['anio']);
$nombremes[1]="de Enero";
$nombremes[2]="de Febrero";
$nombremes[3]="de Marzo";
$nombremes[4]="de Abril";
$nombremes[5]="de Mayo";
$nombremes[6]="de Junio";
$nombremes[7]="de Julio";
$nombremes[8]="de Agosto";
$nombremes[9]="de Septiembre";
$nombremes[10]="de Octubre";
$nombremes[11]="de Noviembre";
$nombremes[12]="de Diciembre";


if ($_GET['bandera']==0){
	$fech=explode("/",$_GET['fecha']);
	$tpl->assign("fecha",$fech[0]." ".$nombremes[number_format($fech[1],0,'','')]);
}
else{
	$tpl->assign("fecha",$nombremes[$_GET['mes']]);
}


$tmp=0;
$tmp1=0;
$tmp2=0;
$tmp3=0;
$total=0;
$cont=0;
$bandera=false;
$aux_fecha=0;
$num_aux=count($entrada)-1;
$total_proveedor=0;
$s=0;
for($i=0;$i<count($entrada);$i++)
{
//	GENERA EL BLOQUE DE COMPAÑÍA
	if($entrada[$i]['num_cia']!=$tmp)
	{
		$tpl->newBlock("companias");
		if($s>0)
		{
			$tpl->newBlock("totalprov1");
			$tpl->assign("total_proveedor1",number_format($total_proveedor,2,'.',','));
			$total_proveedor=0;
			$tpl->gotoBlock("companias");
		}

		$tpl->assign("num_cia",$entrada[$i]['num_cia']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($entrada[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		$tmp1=0;
		$total_proveedor=0;
		$s=0;
	}
	//GENERA EL BLOQUE DE PROVEEDOR
	$tmp=$entrada[$i]['num_cia'];
	if($entrada[$i]['num_proveedor']!=$tmp1)
	{
		
		$tpl->newBlock("proveedor");
		if($s>0)
		{
			$tpl->newBlock("totalprov");
			$tpl->assign("total_proveedor",number_format($total_proveedor,2,'.',','));
			$total_proveedor=0;
			$tpl->gotoBlock("proveedor");
		}
		$tpl->assign("num_proveedor",$entrada[$i]['num_proveedor']);
		$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($entrada[$i]['num_proveedor']),"","",$dsn);
		$tpl->assign("nombre_proveedor",$prov[0]['nombre']);
		$tmp2=0;
	}
	//GENERA LOS BLOQUES DE MATERIA PRIMA
	$tmp1=$entrada[$i]['num_proveedor'];
		$s=$i+1;
		$tpl->newBlock("rows");
		$tpl->assign("fecha",$entrada[$i]['fecha']);
		$tpl->assign("num_fac",$entrada[$i]['num_documento']);
		$tpl->assign("codmp",$entrada[$i]['codmp']);
		$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($entrada[$i]['codmp']),"","",$dsn);
		$con = ejecutar_script("SELECT concepto FROM facturas WHERE num_proveedor = {$entrada[$i]['num_proveedor']} AND num_fact = {$entrada[$i]['num_documento']}", $dsn);
		$tpl->assign("nombre_mp",$mp[0]['nombre']);
		$tpl->assign("cantidad",number_format($entrada[$i]['cantidad'],2,'.',','));
		$tpl->assign("contenido",number_format($entrada[$i]['contenido'],2,'.',','));
		$costo_bruto=$entrada[$i]['cantidad']*$entrada[$i]['precio'];
		$tpl->assign("costo",number_format($costo_bruto,2,'.',','));
		if($entrada[$i]['porciento_desc_normal']>0)	$tpl->assign("descuento",number_format($entrada[$i]['porciento_desc_normal'],2,'.',','));
		else $tpl->assign("descuento","");
		if($entrada[$i]['porciento_impuesto']>0)$tpl->assign("impuesto",number_format($entrada[$i]['porciento_impuesto'],2,'.',','));
		else $tpl->assign("impuesto","");

		$descuento= $entrada[$i]['precio']-($entrada[$i]['precio']*$entrada[$i]['porciento_desc_normal'] /100);
		if($entrada[$i]['cantidad']==0) 
			$costo_unitario=1;
		else
			$costo_unitario=$entrada[$i]['costo_unitario']/($entrada[$i]['cantidad'] * $entrada[$i]['contenido']) / (strpos($con[0]['concepto'], 'ESPECIAL') !== FALSE ? 1.15 : 1);
		
		$tpl->assign("costo_unitario",number_format(@$costo_unitario,2,'.',','));
		$unidades= $entrada[$i]['cantidad']*$entrada[$i]['contenido'];
		$tpl->assign("unidades",number_format($unidades,2,'.',','));
		
		//************
		if($entrada[$i]['porciento_desc_normal']>0)
			$descuento=1+($entrada[$i]['porciento_desc_normal']/100);
		else
			$descuento=1;

		if($entrada[$i]['porciento_impuesto']>0)
			$impuesto=1+($entrada[$i]['porciento_impuesto']/100);
		else
			$impuesto=1;

		//************
		
		$valores=($entrada[$i]['costo_unitario'] / (strpos($con[0]['concepto'], 'ESPECIAL') !== FALSE ? 1.15 : 1)) * $descuento;
		$valores *= $impuesto;
		
		$tpl->assign("valores",number_format($valores,2,'.',','));
		$total+=$valores;
		$total_proveedor+=$valores;


//GENERA EL RENGLON DE TOTAL DESPUES DE TERMINAR UN PROVEEDOR
	if($entrada[$i]['num_documento']==$tmp3)
	{

		if($entrada[$i]['num_documento']!=@$entrada[$i+1]['num_documento'])
		{
		$tpl->newBlock("totalfac");
		$tpl->assign("fac",number_format($entrada[$i]['costo_total'],2,'.',','));
		$cont=1;
		$tpl->gotoBlock("rows");
		}
		$cont++;
	}
	else if ($entrada[$i]['num_documento']!=$tmp3 and $i==0)
	{ 
		$cont++;
	}
	$tmp3=$entrada[$i]['num_documento'];
	$tmp2=$entrada[$i]['codmp'];

	if( ($i+1) == count($entrada) )
	{
		$tpl->newBlock("totalprov3");
		$tpl->assign("total_proveedor3",number_format($total_proveedor,2,'.',','));
		$total_proveedor=0;
		$tpl->gotoBlock("rows");
	}



}
$tpl->gotoBlock("listado");
$tpl->assign("total",number_format($total,2,'.',','));


$tpl->printToScreen();

?>