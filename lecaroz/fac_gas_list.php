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
$tpl->assignInclude("body","./plantillas/fac/fac_gas_list.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha1'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("fecha1","1/".date("n")."/".date("Y"));
	$tpl->assign("fecha",date("d/n/Y"));
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
$sql="select num_cia, factura_gas.num_proveedor, fecha, num_fact, total, litros, precio, desc1, desc2, iva, codmp from factura_gas join catalogo_productos_proveedor on";

if($_GET['cia']==""){
	if($_GET['tipo_user']==1){
		$sql.=" (catalogo_productos_proveedor.codmp=90 and catalogo_productos_proveedor.num_proveedor = factura_gas.num_proveedor and fecha between '".$_GET['fecha1']."' and '".$_GET['fecha2']."'".($_GET['num_pro'] > 0 ? " and catalogo_productos_proveedor.num_proveedor = $_GET[num_pro]" : "").")
				where iduser=".$_SESSION['iduser']." order by num_cia, factura_gas.num_proveedor, fecha";
	}
	else{
		$sql.=" (catalogo_productos_proveedor.codmp=90 and catalogo_productos_proveedor.num_proveedor = factura_gas.num_proveedor and fecha between '".$_GET['fecha1']."' and '".$_GET['fecha2']."'".($_GET['num_pro'] > 0 ? " and catalogo_productos_proveedor.num_proveedor = $_GET[num_pro]" : "").")
				order by num_cia, factura_gas.num_proveedor, fecha";
		}
}
else{
	if($_GET['tipo_user']==1){
		$sql.=" (catalogo_productos_proveedor.codmp=90 and catalogo_productos_proveedor.num_proveedor = factura_gas.num_proveedor and fecha between'".$_GET['fecha1']."' and '".$_GET['fecha2']."'".($_GET['num_pro'] > 0 ? " and catalogo_productos_proveedor.num_proveedor = $_GET[num_pro]" : "")." and num_cia='".$_GET['cia']."')
				where iduser=".$_SESSION['iduser']." order by num_cia, factura_gas.num_proveedor, fecha";
	}
	else{
		$sql.=" (catalogo_productos_proveedor.codmp=90 and catalogo_productos_proveedor.num_proveedor = factura_gas.num_proveedor and fecha between'".$_GET['fecha1']."' and '".$_GET['fecha2']."'".($_GET['num_pro'] > 0 ? " and catalogo_productos_proveedor.num_proveedor = $_GET[num_pro]" : "")." and num_cia='".$_GET['cia']."')
				order by num_cia, factura_gas.num_proveedor, fecha";
	}
}

$entrada=ejecutar_script($sql,$dsn);

if(!$entrada)
{
	header("location: ./fac_gas_list.php?codigo_error=1");
	die();
}

$fecha_inicial=explode("/",$_GET['fecha1']);
$fecha_final = explode("/",$_GET['fecha2']);
$cadena1= $fecha_inicial[0]." DE ".strtoupper(mes_escrito($fecha_inicial[1]))." DEL ".$fecha_inicial[2];
$cadena2= $fecha_final[0]." DE ".strtoupper(mes_escrito($fecha_final[1]))." DEL ".$fecha_final[2];






if($_GET['tipo_total']==0){
	$tpl->newBlock("listado");
	$tmp=0;
	$tmp1=0;
	$tmp2=0;
	$tmp3=0;
	$total=0;
	$cont=0;
	$bandera=false;
	$aux_fecha=0;
	$litros = 0;	// Modificación hecha por Carlos 16/05/2005
	$s=0;
	$num_aux=count($entrada)-1;
	$total_proveedor=0;
	$litros_prov=0;
	
	$tpl->assign("fecha1",$cadena1);
	$tpl->assign("fecha2",$cadena2);
	
	
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
				$tpl->assign("litr_cia",number_format($litros_prov,2,'.',','));
				$litros_prov=0;
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
				$tpl->assign("litr_cia",number_format($litros_prov,2,'.',','));
				$litros_prov=0;
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
		$tpl->assign("num_fac",$entrada[$i]['num_fact']);
		$tpl->assign("litros",number_format($entrada[$i]['litros'],2,'.',','));
		$litros += $entrada[$i]['litros'];	// Modificación hecha por Carlos 16/05/2005
		$litros_prov += $entrada[$i]['litros'];
		$costo_bruto=$entrada[$i]['litros'] * $entrada[$i]['precio'];
		$tpl->assign("costo",number_format($costo_bruto,2,'.',','));
		
		if($entrada[$i]['desc1'] > 0)
			$tpl->assign("descuento",number_format($entrada[$i]['desc1'],2,'.',','));
		
		if($entrada[$i]['desc2'] > 0)
			$tpl->assign("descuento1",number_format($entrada[$i]['desc2'],2,'.',','));
		
		$tpl->assign("impuesto",number_format($entrada[$i]['iva'],2,'.',','));
		$descuento= $entrada[$i]['precio']-($entrada[$i]['precio']*$entrada[$i]['desc1'] /100);
		$descuento1=$descuento+($descuento*$entrada[$i]['desc1'] /100);
		$costo_unitario=$descuento1+($descuento1*$entrada[$i]['iva']/100) ;
	
		$tpl->assign("costo_unitario",number_format($costo_unitario,3,'.',','));
	
		$tpl->assign("valores",number_format($entrada[$i]['total'],2,'.',','));
		$total += $entrada[$i]['total'];
		$total_proveedor += $entrada[$i]['total'];
	
	
	//GENERA EL RENGLON DE TOTAL DESPUES DE TERMINAR UN PROVEEDOR
		if($entrada[$i]['num_fact']==$tmp3)
		{
			if($entrada[$i]['num_fact']!=$entrada[$i+1]['num_fact'])
			{
			$tpl->newBlock("totalfac");
			$tpl->assign("fac",number_format($total_proveedor,2,'.',','));
			$cont=1;
			$total_proveedor=0;
			$tpl->gotoBlock("rows");
			}
			$cont++;
		}
		else if ($entrada[$i]['num_fact']!=$tmp3 and $i==0)
		{ 
			$cont++;
		}
		$tmp3=$entrada[$i]['num_fact'];
		$tmp2=$entrada[$i]['codmp'];
		
		if( ($i+1) == count($entrada) )
		{
			$tpl->newBlock("totalprov3");
			$tpl->assign("total_proveedor",number_format($total_proveedor,2,'.',','));
			$tpl->assign("litr_cia",number_format($litros_prov,2,'.',','));
			$litros_prov=0;
			$total_proveedor=0;
			$tpl->gotoBlock("rows");
		}
	}
	$tpl->gotoBlock("listado");
	$tpl->assign("num_fact", $i);	// Modificación hecha por Carlos 16/05/2005
	$tpl->assign("litros", number_format($litros,2,".",","));	// Modificación hecha por Carlos 16/05/2005
	$tpl->assign("total",number_format($total,2,'.',','));
}

else{
	$sql="select num_cia, num_proveedor,sum(total)as total, sum(litros) as litros from factura_gas where fecha between '".$_GET['fecha1']."' and '".$_GET['fecha2']."' ";
	if($_GET['cia']==""){
		if($_GET['tipo_user']==1)
			$sql.= ($_GET['num_pro'] > 0 ? " and num_proveedor = $_GET[num_pro] and " : "")." and iduser=".$_SESSION['iduser']." group by num_cia,num_proveedor order by num_cia, num_proveedor";
		else
			$sql.= ($_GET['num_pro'] > 0 ? " and num_proveedor = $_GET[num_pro] " : "")." group by num_cia,num_proveedor order by num_cia, num_proveedor";
	}
	else{
		if($_GET['tipo_user']==1)
			$sql.= ($_GET['num_pro'] > 0 ? " and num_proveedor = $_GET[num_pro] " : "")." and num_cia = ".$_GET['cia']." and iduser=".$_SESSION['iduser']." group by num_cia,num_proveedor order by num_cia, num_proveedor";
		
		else
			$sql.= ($_GET['num_pro'] > 0 ? " and num_proveedor = $_GET[num_pro] " : "")." and num_cia = ".$_GET['cia']." group by num_cia,num_proveedor order by num_cia, num_proveedor";
	}
	$totales=ejecutar_script($sql,$dsn);
	
	if(!$totales){
		header("location: ./fac_gas_list.php?codigo_error=1");
		die();
	}
	
	
	$tpl->newBlock("listado_total");
	$tpl->assign("fecha1",$cadena1);
	$tpl->assign("fecha2",$cadena2);
	
	$litros=0;
	$total=0;
	$litros_proveedor=0;
	$aux_cia=0;
	$aux_prov=0;
	
	for($i=0;$i<count($totales);$i++){
		if($totales[$i]['num_cia'] != $aux_cia){
			$tpl->newBlock("cias");
			$tpl->assign("num_cia",$totales[$i]['num_cia']);
			$cia = obtener_registro("catalogo_companias",array("num_cia"),array($totales[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
			$aux_cia=$totales[$i]['num_cia'];
		}
		$tpl->newBlock("proveedor_tot");
		$tpl->assign("num_proveedor",$totales[$i]['num_proveedor']);
		$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($totales[$i]['num_proveedor']),"","",$dsn);
		$tpl->assign("nombre_proveedor",$prov[0]['nombre']);
		
		$tpl->assign("litr_cia",number_format($totales[$i]['litros'],2,'.',','));
		$tpl->assign("total_proveedor",number_format($totales[$i]['total'],2,'.',','));
		
		$litros += $totales[$i]['litros'];
		$total += $totales[$i]['total'];
		
	}
	
	$tpl->gotoBlock("listado_total");
	$tpl->assign("litros", number_format($litros,2,".",","));
	$tpl->assign("total",number_format($total,2,'.',','));

}
$tpl->printToScreen();

?>