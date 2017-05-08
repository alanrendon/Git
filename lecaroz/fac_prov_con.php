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
$tpl->assignInclude("body","./plantillas/fac/fac_prov_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_prov'])) {
	$tpl->newBlock("obtener_datos");

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
//-------------------------------------------
if($_GET['desgloce']==0)
{
	$tpl->newBlock("proveedor_nombre");
	if ($_GET['tipo_con']==0)
		$sql="SELECT * FROM catalogo_proveedores where num_proveedor=".$_GET['num_prov'];
	else if ($_GET['tipo_con']==1){
		$sql="SELECT * FROM catalogo_proveedores";
		if($_GET['orden']==0)
			$sql.=" order by nombre";
		else
			$sql.=" order by num_proveedor";
		}
	$proveedor=ejecutar_script($sql,$dsn);

	if(!($proveedor))
	{
		header("location: ./fac_prov_con.php?codigo_error=1");
		die();
	}

	
	$aux=0;
	for($i=0;$i<count($proveedor);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("num_proveedor",$proveedor[$i]['num_proveedor']);
		$tpl->assign("nombre",$proveedor[$i]['nombre']);
	}
}

else
{
	$tpl->newBlock("proveedor_desgloce");
//------------------
	if ($_GET['tipo_con']==0)
		$sql="SELECT * FROM catalogo_proveedores where num_proveedor=".$_GET['num_prov'];
	else if ($_GET['tipo_con']==1)
		$sql="SELECT * FROM catalogo_proveedores order by num_proveedor";
	$proveedor=ejecutar_script($sql,$dsn);

	if(!($proveedor))
	{
		header("location: ./fac_prov_con.php?codigo_error=1");
		die();
	}

	for ($i=0;$i<count($proveedor);$i++)
	{
		$tpl->newBlock("prov");
		$tpl->assign("id",$proveedor[$i]['num_proveedor']);
		$tpl->assign("nombre",$proveedor[$i]['nombre']);
		$tpl->assign("direccion",$proveedor[$i]['direccion']);
		$tpl->assign("rfc",$proveedor[$i]['rfc']);
		$tpl->assign("telefono1",$proveedor[$i]['telefono1']);
		$tpl->assign("telefono2",$proveedor[$i]['telefono2']);
		$tpl->assign("fax",$proveedor[$i]['fax']);
		$tpl->assign("diascredito",$proveedor[$i]['diascredito']);
		$tpl->assign("tiempoentrega",number_format($proveedor[$i]['tiempoentrega'],2,".",","));
		
		if($proveedor[$i]['prioridad']=='t') $tpl->assign("prioridad","Alta");
		else if($proveedor[$i]['prioridad']=='f') $tpl->assign("prioridad","Baja");
		
		if($proveedor[$i]['pago_via_interbancaria']=='f') $tpl->assign("interbancario","No");
		else if($proveedor[$i]['pago_via_interbancaria']=='t') $tpl->assign("interbancario","si");
		
		if($proveedor[$i]['restacompras']=='t') $tpl->assign("compras","Si");
		else if($proveedor[$i]['restacompras']=='f') $tpl->assign("compras","No");
		
		if($proveedor[$i]['tipopersona']=='t') $tpl->assign("tipo_persona","Física");
		else if($proveedor[$i]['tipopersona']=='f') $tpl->assign("tipo_persona","Moral");
		
		if($proveedor[$i]['idbancos']){
			$banco=obtener_registro("catalogo_bancos",array('idbancos'),array($proveedor[$i]['idbancos']),"","",$dsn);
			if(!$banco) $tpl->assign("banco","");
			else $tpl->assign("banco",$banco[0]['nom_banco']);
		}
		else $tpl->assign("banco","");

		if ($proveedor[$i]['idtipopago'] >= 0){
			$tipo_pago=obtener_registro("tipo_pago",array('idtipopago'),array($proveedor[$i]['idtipopago']),"","",$dsn);
			if(!$tipo_pago) $tpl->assign("tipo_pago","");
			else $tpl->assign("tipo_pago",$tipo_pago[0]['descripcion']);
		}
		else $tpl->assign("tipo_pago","");
		
		if($proveedor[$i]['idtipoproveedor'] >=0 ){
			$tipo_proveedor=obtener_registro("tipo_proveedor",array('idtipoproveedor'),array($proveedor[$i]['idtipoproveedor']),"","",$dsn);
			if(!$tipo_proveedor) $tpl->assign("tipo_proveedor","");
			else $tpl->assign("tipo_proveedor",$tipo_proveedor[0]['descripcion']);
		}
		else $tpl->assign("tipo_proveedor","");
	}
//------------------	
	
}

$tpl->printToScreen();

?>