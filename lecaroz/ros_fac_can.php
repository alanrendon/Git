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
$tpl->assignInclude("body","./plantillas/ros/ros_fac_can.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

	$sql = "
		SELECT
			num_proveedor
				AS value,
			nombre
				AS text
		FROM
			precios_guerra
			LEFT JOIN catalogo_proveedores
				USING (num_proveedor)
		WHERE
			precio_compra > 0
			AND codmp IN (160, 363, 297, 352, 364, 700, 600, 401, 869, 877, 334, 434, 573, 975, 573, 334, 990, 1083, 303)
		GROUP BY
			value,
			text
		ORDER BY
			value
	";

	$result = ejecutar_script($sql, $dsn);

	if ($result)
	{
		foreach ($result as $row) {
			$tpl->newBlock('pro');

			$tpl->assign('value', $row['value']);
			$tpl->assign('text', $row['text']);
		}
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
// Obtener rosticerias
$ros = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 OR num_cia = 702 OR num_cia=704 AND status=true ORDER BY num_cia ASC",$dsn);

$sql="SELECT * FROM fact_rosticeria WHERE ";

$cia=" num_cia='".$_GET['num_cia']."' and num_fac='".$_GET['num_fac']."' AND num_proveedor = $_GET[num_pro]";
$sql=$sql.$cia;

$facturas = ejecutar_script($sql,$dsn);

$sql2="SELECT * FROM total_fac_ros WHERE num_cia='".$_GET['num_cia']."' and num_fac='".$_GET['num_fac']."' AND num_proveedor = $_GET[num_pro]";
$pagada=ejecutar_script($sql2,$dsn);


$prov=13;
//$tpl->assign("tabla","fact_rosticeria");
if(!$facturas)
{
	header("location: ./ros_fac_can.php?codigo_error=1");
	die;
}
else
{
	if($pagada[0]['pagado']=='t')
	{
		header("location: ./ros_fac_can.php?codigo_error=2");
		die;
	}
/*
	if(existe_registro("facturas_pagadas",array("num_cia","num_fact","num_proveedor"),array($_GET['num_cia'],$_GET['num_fac'],$prov),$dsn));
	{
		header("location: ./ros_fac_can.php?codigo_error=2");
		die;
	}
*/

	$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$nompro = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['num_pro']),"","",$dsn);
	// Crear bloque de listado
	$tpl->newBlock("factura");
	$tpl->assign('numero_cia',$_GET['num_cia']);
	$tpl->assign('nombre_cia',$nomcia[0]['nombre_corto']);
	$tpl->assign('num_proveedor',$_GET['num_pro']);
	$tpl->assign('nom_proveedor',$nompro[0]['nombre']);
	$tpl->assign('num_factura',$facturas[0]['num_fac']);
	$tpl->assign('fecha_mov',$facturas[0]['fecha_mov']);
	$tpl->assign('fecha_pago',$facturas[0]['fecha_pago']);
	$total_factura= 0;
	$var=0;
	for ($j=0;$j<count($facturas);$j++)
	{
			$tpl->newBlock("rows");
			$tpl->assign("var",$var);
			$var++;
			$tpl->assign('codmp',$facturas[$j]['codmp']);
			$tpl->assign('num_cia',$_GET['num_cia']);
			$tpl->assign('num_pro', $_GET['num_pro']);
			$tpl->assign('num_factura',$facturas[0]['num_fac']);
			$tpl->assign('fecha_mov',$facturas[0]['fecha_mov']);
			$tpl->assign('fecha_pago',$facturas[0]['fecha_pago']);

			$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($facturas[$j]['codmp']),"","",$dsn);
			$tpl->assign('nom_mp',$mp[0]['nombre']);
			$tpl->assign('cantidad1',number_format($facturas[$j]['cantidad'],2,".",","));
			$tpl->assign('kilos1',number_format($facturas[$j]['kilos'],2,".",","));
			$tpl->assign('precio1',number_format($facturas[$j]['precio'],2,".",","));
			$tpl->assign('total1',number_format($facturas[$j]['total'],2,".",","));

			$tpl->assign('cantidad',$facturas[$j]['cantidad']);
			$tpl->assign('kilos',$facturas[$j]['kilos']);
			$tpl->assign('precio',$facturas[$j]['precio']);
			$tpl->assign('total',$facturas[$j]['total']);
			$total_factura += $facturas[$j]['total'];
	}

	$tpl->newBlock("totales");
	$tpl->assign('total',number_format($total_factura,2,".",","));
	$tpl->printToScreen();
}
?>
