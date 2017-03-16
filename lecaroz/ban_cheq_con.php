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
$tpl->assignInclude("body","./plantillas/ban/ban_cheq_con.tpl");
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

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "-message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}
// -------------------------------- Mostrar listado ---------------------------------------------------------
$sql="select * from cheques where ";
if ($_GET['consulta']=="folio")
{
	$sql.="num_cia='".$_GET['cia2']."' and folio='".$_GET['folio']."'";
}
else if($_GET['consulta']=="fecha")
{
	$sql.="fecha_mov='".$_GET['fecha']."'";
	if($_GET['tipo_con']=="cia")
		$sql.=" and num_cia='".$_GET['cia']."'";
	else if($_GET['tipo_con']=="prov")
		$sql.=" and num_proveedor='".$_GET['proveedor']."'";
}
$cheques=ejecutar_script($sql,$dsn);
//------------------------------------------------------------------------------------------------------------
if(!$cheques)
{
	header("location: ./ban_cheq_con.php?codigo_error=1");
	die();
}
$tpl->newBlock("cheque");
//echo $sql."<br>";
//print_r($cheques);
for($i=0;$i<count($cheques);$i++)
	{
		$tpl->newBlock("rows");
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
		$tpl->assign("cia",$cia[0]['nombre_corto']);
		$pro = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($cheques[$i]['num_proveedor']),"","",$dsn);
		$tpl->assign("proveedor",$pro[0]['nombre']);
		$tpl->assign("concepto",$cheques[$i]['concepto']);
		$tpl->assign("fecha",$cheques[$i]['fecha_mov']);
		$tpl->assign("folio",$cheques[$i]['folio']);
		$tpl->assign("importe",$cheques[$i]['importe_cheque']);
	}
	
$tpl->printToScreen();

?>