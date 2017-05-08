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
$tpl->assignInclude("body","./plantillas/ban/ban_cheques_con.tpl");
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

//$sql="select * from cheques WHERE concepto like '%NOMINA%' and fecha='2004/12/21' order by num_cia, fecha";
$sql="select * from cheques order by num_cia, fecha";

$cheques=ejecutar_script($sql,$dsn);
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
$total=0;

$tpl->assign("fecha",$_date[0]." DE ".$nombremes[$_date[1]]." DEL ".$_date[2]);
$auxiliar=$cheques[0]['num_cia'];
//$auxiliar=0;
$aux=0;
//print_r($cheques);
for($i=0;$i<count($cheques);$i++)
{
	$tpl->newBlock("ordena");
	
	if($auxiliar != $cheques[$i]['num_cia'])
	{
		$tpl->newBlock("total_cia");
		$tpl->assign("total_cia",number_format($aux,2,'.',','));
		$aux=0;
		$auxiliar=$cheques[$i]['num_cia'];	
	}
	$tpl->newBlock("rows");	
	$tpl->assign("folio",$cheques[$i]['folio']);
	$tpl->assign("num_cia",$cheques[$i]['num_cia']);
	$nom=obtener_registro("catalogo_companias",array('num_cia'),array($cheques[$i]['num_cia']),"","",$dsn);
	$tpl->assign("nom_cia",$nom[0]['nombre']);
	$tpl->assign("num_proveedor",$cheques[$i]['num_proveedor']);
	$tpl->assign("nom_proveedor",$cheques[$i]['a_nombre']);
	$tpl->assign("cuenta",$nom[0]['clabe_cuenta']);
	$tpl->assign("fecha",$cheques[$i]['fecha']);
	if($cheques[$i]['fecha_cancelacion']=="")
	{
		$tpl->newBlock("cheque_ok");
		$tpl->assign("importe",number_format($cheques[$i]['importe'],2,'.',','));
	}
	else
	{
		$tpl->newBlock("cheque_error");
		$tpl->assign("importe","CANCELADO");
	}
	$total += $cheques[$i]['importe'];
	$aux += $cheques[$i]['importe'];
//	$auxiliar=$cheques[$i]['num_cia'];

}
$tpl->gotoBlock("_ROOT");
$tpl->assign("total",number_format($total,2,'.',','));

$tpl->printToScreen();
?>