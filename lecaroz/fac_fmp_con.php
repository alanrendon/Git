<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron facturas";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

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
$tpl->assignInclude("body","./plantillas/fac/fac_fmp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	if(isset($_SESSION['consulta_factura']))
	{
		$tpl->assign("num_proveedor",$_SESSION['consulta_factura']['num_proveedor']);
	}
	
	$tpl->assign("anio_actual", date("d") < 6 ? date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))) : date("Y"));
	$mes = date("d") < 6 ? date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))) : date("n");
	for($j=1;$j<=12;$j++){
		$tpl->newBlock("mes");
		$tpl->assign("num_mes",$j);
		$tpl->assign("nom_mes",mes_escrito($j));
		
		if ($j == $mes) $tpl->assign("checked", "selected");
		
		//if(date("d") < 6){
//			echo "entre menor a 6 dias<br>";
			/*if((date("n")+1) <= 12){
				if(($j+1) == date("n"))
					$tpl->assign("checked","selected");
			}
		}
		else{
			if(date("n")==$j)
				$tpl->assign("checked","selected");
		}*/
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

$fecha="1/".$_GET['mes']."/".$_GET['anio'];
$cia=0;
if($_GET['cia']==171) $cia=170;
else if($_GET['cia']==146) $cia=147;
else $cia=$_GET['cia'];
$cia=$_GET['cia'];
$sql="
select num_cia, num_proveedor, num_fact, fecha_mov, fecha_pago, total, 'false' as estado, codgastos, -1 as folio_cheque, fecha_mov as fecha_cheque, descripcion
from
pasivo_proveedores
where num_cia=".$cia." and num_proveedor=".$_GET['proveedor']." and fecha_mov >= '".$fecha."'
union
select num_cia, num_proveedor, num_fact, fecha_mov, fecha_cheque, total, 'true' as estado, codgastos, folio_cheque, fecha_cheque, descripcion
from
facturas_pagadas
where num_cia=".$cia." and num_proveedor=".$_GET['proveedor']." and fecha_mov >= '".$fecha."'";
$facturas=ejecutar_script($sql,$dsn);

if(!$facturas){
	header("location: ./fac_fmp_con.php?codigo_error=1");
	die();
}

$_SESSION['consulta_factura']['num_proveedor']=$_GET['proveedor'];

$tpl->newBlock("facturas");
//echo $sql;
$tpl->assign("num_cia",$_GET['cia']);
$tpl->assign("num_proveedor",$_GET['proveedor']);
$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
$proveedor=obtener_registro("catalogo_proveedores",array('num_proveedor'),array($_GET['proveedor']),"","",$dsn);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
$tpl->assign("nom_proveedor",$proveedor[0]['nombre']);


//if(isset($_SESSION['']))

function calcula_fecha($fecha)
{
	$_fecha=explode("/",$fecha);
	$fecha_hoy=explode("/",date("d/m/Y"));
	
	if($_fecha[2]==$fecha_hoy[2])//Caso en el que el año es el mismo para las dos fechas
	{
		if($_fecha[1]==$fecha_hoy[1]){//mismo año, mismo mes
			if($_fecha[0] >= $fecha_hoy[0]) //dia mayor al corriente bloquea el boton
				return true;
			else return true;
		}
		else if ($_fecha[1] >= $fecha_hoy[1])//mes mayor restringe el boton
			return true;
		else if($_fecha[1] == ($fecha_hoy[1] - 1)){//el mes de la fecha es anterior al actual
			if($fecha_hoy[0]==1 or $fecha_hoy[0]==2 or $fecha_hoy[0]==3 or $fecha_hoy[0]==4 or $fecha_hoy[0]==5)
				return true;
			else return false;
		}
		else return false;
	}
	else if($_fecha[2] == ($fecha_hoy[2] - 1)){//el año de la factura es anterior al actual
		if($fecha_hoy[1]==1 and $_fecha[1]==12){
			if($fecha_hoy[0]==1 or $fecha_hoy[0]==2 or $fecha_hoy[0]==3 or $fecha_hoy[0]==4 or $fecha_hoy[0]==5)
				return true;
			else return false;
		}
		else return false;
	}
	else return false;
}

$codigo_gastos=33;

$total = 0;	// Codigo metido por Carlos el 16/05/2005
for($i=0;$i<count($facturas);$i++)
{
	$tpl->newBlock("rows");
	$fecha_factura=explode("/",$facturas[$i]['fecha_pago']);
//	echo $fecha_factura[1]." <br>";
/*	
	if($facturas[$i]['fecha_pago']!=""){
		$tpl->assign("mes_factura",$fecha_factura[1]);
		$tpl->assign("anio_factura",$fecha_factura[2]);
	}*/
	$tpl->assign("mes_corriente",date("m"));
	$tpl->assign("anio_corriente",date("Y"));

	if($facturas[$i]['folio_cheque'] > 0){
		$gasto=obtener_registro("movimiento_gastos",array("num_cia","codgastos","fecha","captura","importe"),array($facturas[$i]['num_cia'],$facturas[$i]['codgastos'],$facturas[$i]['fecha_cheque'],"true",$facturas[$i]['total']),"","",$dsn);
		$tpl->assign("id",$gasto[0]['idmovimiento_gastos']);
	}
	else
		$tpl->assign("id","-1");
	
	//DATOS PARA MOSTRAR EN PANTALLA
	$tpl->assign("num_fact",$facturas[$i]['num_fact']);
	$tpl->assign("importe",number_format($facturas[$i]['total'],2,'.',','));
	$tpl->assign("importe1",number_format($facturas[$i]['total'],2,'.',''));
	$total += $facturas[$i]['total'];	// Codigo metido por Carlos el 16/05/2005
	$tpl->assign("codgasto",$facturas[$i]['codgastos']);
	$nom_gasto=obtener_registro("catalogo_gastos",array('codgastos'),array($facturas[$i]['codgastos']),"","",$dsn);
	$tpl->assign("gasto_desc",$nom_gasto[0]['descripcion']);
	$tpl->assign("num_cia",$_GET['cia']);
	$tpl->assign("num_proveedor",$_GET['proveedor']);
	$tpl->assign("cheque",$facturas[$i]['folio_cheque']);
	$tpl->assign("descripcion",$facturas[$i]['descripcion']);
	
	
	if($facturas[$i]['estado']=='true')
	{
		$tpl->assign("num_cheque",$facturas[$i]['folio_cheque']);
		$tpl->assign("fecha1",$facturas[$i]['fecha_mov']);
		$tpl->assign("fecha_pago",$facturas[$i]['fecha_pago']);
		$tpl->assign("fech1",$facturas[$i]['fecha_cheque']);
	}
	else
	{
		$tpl->assign("num_cheque",0);
		$tpl->assign("fecha1",$facturas[$i]['fecha_mov']);
		$tpl->assign("fecha_pago", /*$facturas[$i]['fecha_pago']*/"&nbsp;");
		$tpl->assign("fech1",$facturas[$i]['fecha_cheque']);
	}
	$valor=calcula_fecha($facturas[$i]['fecha_pago']);
		
	if($valor==true)
		$tpl->assign("bloquea","");
	else
		$tpl->assign("bloquea","disabled");
		
	if($facturas[$i]['codgastos']==$codigo_gastos)
		$tpl->assign("bloquea","disabled");
	
	
}
$tpl->gotoBlock("facturas");	// Codigo metido por Carlos el 16/05/2005
$tpl->assign("num_fact",$i);	// Codigo metido por Carlos el 16/05/2005
$tpl->assign("importe",number_format($total,2,".",","));	// Codigo metido por Carlos el 16/05/2005

$tpl->printToScreen();

?>