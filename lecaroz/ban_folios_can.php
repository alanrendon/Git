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
$descripcion_error[1] = "No se encontraron cheques cancelados";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_folios_can.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha_inicial'])) 
{

	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("f1",date("1/m/Y"));
	$tpl->assign("f2",date("d/m/Y"));
	
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
// -------------------------------- Mostrar listado ---------------------------------------------------------

$tpl->newBlock("listado");

if($_GET['fecha_inicial']==$_GET['fecha_final'])
{
	if($_GET['consulta']==0)
	$sql="select * from cheques where fecha_cancelacion='".$_GET['fecha_inicial']."' and num_cia=".$_GET['num_cia']." order by folio";
	else
	$sql="select * from cheques where fecha_cancelacion='".$_GET['fecha_inicial']."' order by num_cia, fecha_cancelacion";
}
else if ($_GET['fecha_inicial']!=$_GET['fecha_final'])
{
	if($_GET['consulta']==0)
	$sql="select * from cheques where fecha_cancelacion between '".$_GET['fecha_inicial']."' and '".$_GET['fecha_final']."' and num_cia=".$_GET['num_cia']." order by folio";
	else
	$sql="select * from cheques where fecha_cancelacion between '".$_GET['fecha_inicial']."' and '".$_GET['fecha_final']."' order by num_cia, fecha_cancelacion";
}

$cheques=ejecutar_script($sql,$dsn);
//echo $sql;

if(!$cheques)
{
	header("location: ./ban_folios_can.php?codigo_error=1");
	die();
}

//print_r($cheques);

$aux=0;
for($i=0;$i<count($cheques);$i++)
{
	if($aux!=$cheques[$i]['num_cia'])
	{	
		$tpl->newBlock("compania");
		$tpl->assign("num_cia",$cheques[$i]['num_cia']);
		$compania=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_corto",$compania[0]['nombre_corto']);
		$aux=$cheques[$i]['num_cia'];
	}
	
	$tpl->newBlock("rows");
	$tpl->assign("folio",$cheques[$i]['folio']);
	$tpl->assign("fecha",$cheques[$i]['fecha']);
	$tpl->assign("fecha_cancelacion",$cheques[$i]['fecha_cancelacion']);
	$tpl->assign("num_proveedor",$cheques[$i]['num_proveedor']);
	$proveedor=obtener_registro("catalogo_proveedores",array("num_proveedor"),array($cheques[$i]['num_proveedor']),"","",$dsn);
	$tpl->assign("nombre_proveedor",$proveedor[0]['nombre']);

}
//$tpl->gotoBlock("listado");

$tpl->printToScreen();
?>