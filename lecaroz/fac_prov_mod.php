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
$descripcion_error[1] = "El proveedor no existe";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_prov_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['proveedor'])) {
	$tpl->newBlock("obtener_dato");
	
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

$sql="select * from catalogo_proveedores where num_proveedor=".$_GET['proveedor'];
$proveedor=ejecutar_script($sql,$dsn);
//print_r($proveedor);
if(!($proveedor))
{
	header("location: ./fac_prov_mod.php?codigo_error=1");
	die();
}


$tpl->newBlock("modificar");
$tpl->assign("tabla","catalogo_proveedores");
$tpl->assign("id",$proveedor[0]['num_proveedor']);
$tpl->assign("nombre",$proveedor[0]['nombre']);
$tpl->assign("direccion",$proveedor[0]['direccion']);
$tpl->assign("rfc",$proveedor[0]['rfc']);
$tpl->assign("telefono1",$proveedor[0]['telefono1']);
$tpl->assign("telefono2",$proveedor[0]['telefono2']);
$tpl->assign("fax",$proveedor[0]['fax']);
$tpl->assign("diascredito",$proveedor[0]['diascredito']);
$tpl->assign("tiempoentrega",$proveedor[0]['tiempoentrega']);

$tpl->assign("valueprioridad1","0");
$tpl->assign("descripcion1","BAJA");
$tpl->assign("valueprioridad2","1");
$tpl->assign("descripcion2","ALTA");
if($proveedor[0]['prioridad']=='t') $tpl->assign("selected2","selected");
else if($proveedor[0]['prioridad']=='f') $tpl->assign("selected1","selected");

$tpl->assign("interbancario1","0");
$tpl->assign("descripcion3","NO");
$tpl->assign("interbancario2","1");
$tpl->assign("descripcion4","SI");
if($proveedor[0]['pago_via_interbancaria']=='t') $tpl->assign("selcted4","selected");
else if($proveedor[0]['pago_via_interbancaria']=='f') $tpl->assign("selected3","selected");

$tpl->assign("valueresta1","0");
$tpl->assign("descripcion5","NO");
$tpl->assign("valueresta2","1");
$tpl->assign("descripcion6","SI");
if($proveedor[0]['restacompras']=='t') $tpl->assign("selected6","selected");
else if($proveedor[0]['restacompras']=='f') $tpl->assign("selected5","selected");

$tpl->assign("valuepersona1","0");
$tpl->assign("descripcion7","MORAL");
$tpl->assign("valuepersona2","1");
$tpl->assign("descripcion8","FISICA");
if($proveedor[0]['tipopersona']=='f') $tpl->assign("selected7","selected");
else if($proveedor[0]['tipopersona']=='t') $tpl->assign("selected8","selected");

$tpl->assign("valueabono1","0");
$tpl->assign("descripcion9","NO");
$tpl->assign("valueabono2","1");
$tpl->assign("descripcion10","SI");
if($proveedor[0]['para_abono']=='f') $tpl->assign("selected9","selected");
else if($proveedor[0]['para_abono']=='t') $tpl->assign("selected10","selected");


$sql="select * from catalogo_bancos order by idbancos";
$banco=ejecutar_script($sql,$dsn);
for($i=0;$i<count($banco);$i++){
	$tpl->newBlock("banco");
	$tpl->assign("idbanco",$banco[$i]['idbancos']);
	$tpl->assign("nombrebanco",$banco[$i]['nom_banco']);
	$tpl->assign("valuebanco",$banco[$i]['idbancos']);
	if($proveedor[0]['idbancos'] == $banco[$i]['idbancos']) $tpl->assign("selected","selected");
}

$sql="select * from tipo_pago order by idtipopago";
$pago=ejecutar_script($sql,$dsn);
for($i=0;$i<count($pago);$i++){
	$tpl->newBlock("pago");
	$tpl->assign("idpago",$pago[$i]['idtipopago']);
	$tpl->assign("namepago",$pago[$i]['descripcion']);
	$tpl->assign("valuepago",$pago[$i]['idtipopago']);
	if($proveedor[0]['idtipopago'] == $pago[$i]['idtipopago']) $tpl->assign("selected","selected");
}

$sql="select * from tipo_proveedor order by idtipoproveedor";
$prov=ejecutar_script($sql,$dsn);
for($i=0;$i<count($prov);$i++){
	$tpl->newBlock("proveedor");
	$tpl->assign("nameproveedor",$prov[$i]['descripcion']);
	$tpl->assign("valueproveedor",$prov[$i]['idtipoproveedor']);
	if($proveedor[0]['idtipoproveedor'] == $prov[$i]['idtipoproveedor']) $tpl->assign("selected","selected");
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>