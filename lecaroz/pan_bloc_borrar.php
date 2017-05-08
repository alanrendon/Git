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
$descripcion_error[1] = "No existen blocs borrados";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_bloc_borrar.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
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
// -------------------------------- Mostrar listado ---------------------------------------------------------

if($_GET['stat']==0)
	$sql="SELECT * FROM blocs_borrados where num_cia=".$_GET['cia']." order by folio_inicio";
else if($_GET['stat']==1)
	$sql="SELECT * from blocs_borrados order by num_cia, folio_inicio";
	
$bloc=ejecutar_script($sql,$dsn);

if(!$bloc)
{
	header("location: ./pan_bloc_borrar.php?codigo_error=1");
	die;
}

$var=0;
$var1=0;
$tpl->newBlock("bloc");
$fecha=date("d/n/Y");
$_date=explode("/",$fecha);
$nombremes[1]="ENERO";
$nombremes[2]="FEBRERO";
$nombremes[3]="MARZO";
$nombremes[4]="ABRIL";
$nombremes[5]="MAYO";
$nombremes[6]="JUNIO";
$nombremes[7]="JULIO";
$nombremes[8]="AGOSTO";
$nombremes[9]="SEPTIEMBRE";
$nombremes[10]="OCTUBRE";
$nombremes[11]="NOVIEMBRE";
$nombremes[12]="DICIEMBRE";
$tpl->assign("fecha",$_date[0]." DE ".$nombremes[$_date[1]]." DEL ".$_date[2]);


for($i=0;$i<count($bloc);$i++)
{
	if($var!=$bloc[$i]['num_cia'])
	{	
		$tpl->newBlock("compania");
		$tpl->assign("num_cia",$bloc[$i]['num_cia']);
		$cia=obtener_registro("catalogo_companias",array('num_cia'),array($bloc[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		$var=$bloc[$i]['num_cia'];
	}
	$tpl->newBlock("rows");
	$tpl->assign("let_folio",$bloc[$i]['let_folio']);
	$tpl->assign("folio_inicio",$bloc[$i]['folio_inicio']);
	$tpl->assign("folio_final",$bloc[$i]['folio_final']);
	$var1+=1;
}
$tpl->gotoBlock("bloc");
$tpl->assign("total",$var1);
$tpl->printToScreen();

if($_GET['stat']==0)
	$sql="delete from blocs_borrados where num_cia=".$_GET['num_cia'];
else if ($_GET['stat']==1)
	$sql="truncate blocs_borrados";

ejecutar_script($sql,$dsn);

// --------------------------------------------------------------------------------------------------------
?>