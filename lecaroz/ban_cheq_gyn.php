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
$descripcion_error[1] = "No se encontraron cheques";
$users = array(28, 29, 30, 31);
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cheq_gyn.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha_inicial'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	
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
// -------------------------------- GENERAR ARREGLO ---------------------------------------------------------

$sql="select * from cheques WHERE AND concepto = 'AGUINALDOS' fecha between '".$_GET['fecha_inicial']."' and '".$_GET['fecha_final']."' and codgastos=134 and importe > 0 AND num_cia NOT IN (700, 800) AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 998" : "1 AND 899");echo $sql;
if ($_GET['cuenta'] > 0)
	$sql .= " and cuenta = $_GET[cuenta]";
if($_GET['cancelado']==0)
	$sql.=" and fecha_cancelacion is null";
else if($_GET['cancelado']==1)
	$sql.=" and fecha_cancelacion is not null";

$sql.=" order by num_cia,num_proveedor,fecha";

$cheques=ejecutar_script($sql,$dsn);
//----------------------------------------------------------------------------------------------------------
if(!$cheques){
	header("location: ./ban_cheq_gyn.php?codigo_error=1");
	die();
}
//print_r($cia);

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
$_d1=explode("/",$_GET['fecha_inicial']);
$_d2=explode("/",$_GET['fecha_final']);

//echo $sql;

$total=0;
$tpl->newBlock("por_gasto");
if($_GET['cancelado']==1){
	$tpl->newBlock("gas_can");
	$tpl->gotoBlock("por_gasto");
}

$tpl->assign("fecha",$_d1[0]." DE ".$nombremes[$_d1[1]]." DEL ".$_d1[2]);
$tpl->assign("fecha1",$_d2[0]." DE ".$nombremes[$_d2[1]]." DEL ".$_d2[2]);
for($i=0;$i<count($cheques);$i+=2){
	$tpl->newBlock("row_gasto");
	$tpl->assign("num_cia",$cheques[$i]['num_cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i]['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$cia[0]["nombre_corto"]);
	$tpl->assign("folio",$cheques[$i]['folio']);
	if($cheques[$i]['fecha_cancelacion']!="")
		$tpl->assign("cantidad","CANCELADO");
	else
		$tpl->assign("cantidad",number_format($cheques[$i]['importe'],2,'.',','));
	

	if(($i+1)==count($cheques)) break;
	$tpl->assign("num_cia1",$cheques[$i+1]['num_cia']);
	$cia1=obtener_registro("catalogo_companias",array("num_cia"),array($cheques[$i+1]['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia1",$cia1[0]["nombre_corto"]);
	$tpl->assign("folio1",$cheques[$i+1]['folio']);
	if($cheques[$i+1]['fecha_cancelacion']!="")
		$tpl->assign("cantidad1","CANCELADO");
	else
		$tpl->assign("cantidad1",number_format($cheques[$i+1]['importe'],2,'.',','));
	
}
for($i=0;$i<count($cheques);$i++){
	if($cheques[$i]['fecha_cancelacion']!="")
		continue;
	else
		$total+=number_format($cheques[$i]['importe'],2,'.','');
}
$tpl->gotoBlock("por_gasto");
$tpl->assign("total",number_format($total,2,'.',','));
$tpl->assign("count",count($cheques));

$tpl->printToScreen();
?>