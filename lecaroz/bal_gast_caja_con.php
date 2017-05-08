<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron blocks para la compañía";
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
$tpl->assignInclude("body","./plantillas/bal/bal_gast_caja_mod.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['num_ciacia']))
{
	$tpl->newBlock("obtener_datos");
	$sql="select num_cia,nombre_corto from catalogo_companias where num_cia < 101 or num_cia=999 order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	}
	
	
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

// Imprimir el resultado
$sql="select * from bloc where idcia=".$_GET['cia'];

if ($_GET['stat']==0) $sql.=" and estado=true order by folio_inicio";
else if($_GET['stat']==1) $sql.=" and estado=false and folios_usados > 0 order by folio_inicio";
else if($_GET['stat']==2) $sql.=" and estado=false and folios_usados = 0 order by folio_inicio";
else if($_GET['stat']==3) $sql.=" order by folio_inicio";

$bloc=ejecutar_script($sql,$dsn);

if(!$bloc)
{
	header("location: ./pan_bloc_con.php?codigo_error=1");
	die();
}

$tpl->newBlock("bloc");
$tpl->assign("num_cia",$_GET['cia']);
$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);

$operadora=obtener_registro("catalogo_operadoras",array("idoperadora"),array($cia[0]['idoperadora']),"","",$dsn);
$tpl->assign("operadora",$operadora[0]['nombre_operadora']);


for($i=0;$i<count($bloc);$i++)
{
	$tpl->newBlock("rows");
	if($bloc[$i]['let_folio']=="X")
		$tpl->assign("let_folio","");
	else
		$tpl->assign("let_folio",$bloc[$i]['let_folio']);
	$tpl->assign("let_folio1",$bloc[$i]['let_folio']);
	$tpl->assign("folio_inicial",$bloc[$i]['folio_inicio']);
	$tpl->assign("folio_final",$bloc[$i]['folio_final']);
	$tpl->assign("num_folios",$bloc[$i]['num_folios']);
	$tpl->assign("fecha",$bloc[$i]['fecha']);
	$tpl->assign("num_cia",$_GET['cia']);
	$tpl->assign("idbloc",$bloc[$i]['id']);

	if($bloc[$i]['estado']=='t') $tpl->assign("status","TERMINADO");
	else if($bloc[$i]['estado']=='f' and $bloc[$i]['folios_usados'] > 0) $tpl->assign("status","EN PROCESO");
	else if($bloc[$i]['estado']=='f' and $bloc[$i]['folios_usados'] == 0) $tpl->assign("status","SIN USAR");
	
	if($bloc[$i]['estado']=='t')
	{	
		$tpl->newBlock("borrado");
		$tpl->assign("id",$bloc[$i]['id']);
		$tpl->assign("id_user",$_SESSION['iduser']);
	}
}
$tpl->printToScreen();
?>