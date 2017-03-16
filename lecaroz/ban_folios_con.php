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
$descripcion_error[1] = "No existen folios";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_folios_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
//if (!isset($_GET['cia'])) {
//	$tpl->newBlock("obtener_datos");
	
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

//	$tpl->printToScreen();
	//die();
//}
// -------------------------------- Mostrar listado ---------------------------------------------------------

$sql="select distinct(num_cia) from folios_cheque order by num_cia";

$cia=ejecutar_script($sql,$dsn);
//print_r($cia);
$fecha=date("d/m/Y");
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

$var=0;
$tpl->assign("fecha",$_date[0]." DE ".$nombremes[$_date[1]]." DEL ".$_date[2]);

for($i=0;$i<count($cia);$i++)
{
	$tpl->newBlock("rows");
	$sql="select * from folios_cheque where num_cia=".$cia[$i]['num_cia']." order by folio desc";
	$folio=ejecutar_script($sql,$dsn);
	$tpl->assign("folio",$folio[0]['folio']);
	$tpl->assign("num_cia",$folio[0]['num_cia']);
	$nom=obtener_registro("catalogo_companias",array('num_cia'),array($folio[0]['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$nom[0]['nombre']);
	$tpl->assign("nombre_corto",$nom[0]['nombre_corto']);
}

$tpl->printToScreen();
?>