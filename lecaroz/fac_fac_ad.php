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
$descripcion_error[1] = "Lo siento pero no encontre facturas";
$descripcion_error[2] = "Lo siento pero la factura ya esta pagada, no se puede cancelar";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_fac_ad.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

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
// Obtener rosticerias
$sql = "SELECT * FROM facturas WHERE num_cia='".$_GET['num_cia']."' and num_fact='".$_GET['num_fac']."' and num_proveedor=".$_GET['proveedor'];
$factura = ejecutar_script($sql,$dsn);

$sql = "SELECT * FROM entrada_mp WHERE num_cia='".$_GET['num_cia']."' and num_documento='".$_GET['num_fac']."' and num_proveedor=".$_GET['proveedor'];
$desglozada = ejecutar_script($sql,$dsn);


if(!$factura)
{
	header("location: ./fac_fac_ad.php?codigo_error=1");
	die;
}
else
{
	if(existe_registro("facturas_pagadas",array("num_cia","num_fact","num_proveedor"),array($_GET['num_cia'],$_GET['num_fac'],$_GET['proveedor']),$dsn))
	{
		header("location: ./fac_fac_ad.php?codigo_error=2");
		die;
	}
	$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$nomproveedor = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['proveedor']),"","",$dsn);
	
	$sql = "SELECT codmp,nombre,contenido,descripcion AS unidad,precio,desc1,desc2,desc3,iva,ieps FROM catalogo_productos_proveedor JOIN catalogo_mat_primas USING(codmp) JOIN tipo_unidad_consumo ON(idunidad = unidadconsumo) WHERE num_proveedor=$_GET[proveedor] ORDER BY codmp ASC";
	$rows = ejecutar_script($sql,$dsn);

	// Crear bloque de listado
	$tpl->newBlock("factura");
	$tpl->assign('numero_cia',$_GET['num_cia']);
	$tpl->assign('nombre_cia',$nomcia[0]['nombre_corto']);
	$tpl->assign('num_proveedor',$_GET['proveedor']);
	$tpl->assign('nom_proveedor',$nomproveedor[0]['nombre']);
	$tpl->assign('num_factura',$factura[0]['num_fact']);
	$tpl->assign('fecha_mov',$factura[0]['fecha_mov']);
	$tpl->assign('fecha_pago',$factura[0]['fecha_ven']);
	$total_factura = 0;
	$var=0;
	$tpl->assign("cont",count($rows));
	for ($j=0;$j<count($rows);$j++) 
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$j);
		$tpl->assign("next",$j+1);
		$tpl->assign("codmp",$rows[$j]["codmp"]);
		$tpl->assign("nom_mp",$rows[$j]["nombre"]);
		$tpl->assign("contenido",$rows[$j]["contenido"]);
		$tpl->assign("unidad",$rows[$j]["unidad"]);
		$tpl->assign("precio",number_format($rows[$j]["precio"],3,'.',''));
		$tpl->assign("desc1",number_format($rows[$j]["desc1"],2,'.',''));
		$tpl->assign("desc2",number_format($rows[$j]["desc2"],2,'.',''));
		$tpl->assign("desc3",number_format($rows[$j]["desc3"],2,'.',''));
		$tpl->assign("iva",number_format($rows[$j]["iva"],2,'.',''));
		$tpl->assign("ieps",$rows[$j]["ieps"]);
		
		for($i=0;$i<count($desglozada);$i++){
			if($rows[$j]["codmp"] == $desglozada[$i]["codmp"] and $rows[$j]["precio"]==$desglozada[$i]["precio"]){
				$tpl->assign("cantidad",$desglozada[$i]["cantidad"]);
				$tpl->assign("total",number_format($desglozada[$i]["costo_unitario"], 2, '.', ''));
				if($desglozada[$i]["regalado"]=='t') {
					$tpl->assign("bandera","1");
					$tpl->assign("che","checked");
				}
				else{
					$tpl->assign("bandera","0");
					$tpl->assign("che","");
				}
				break;
			}
		}
	}

	$tpl->newBlock("totales");
	$tpl->assign('total_factura',number_format($factura[0]['importe_total'],2,".",""));
	$tpl->printToScreen();
}
?>