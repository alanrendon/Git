<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontro el cheque";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

$users = array(28, 29, 30, 31);

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
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_cheq_bus.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");


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

//$sql="SELECT cheques.*, fecha_con  FROM cheques JOIN estado_cuenta using (folio,num_cia) where folio=".$_GET['cheque']." and num_cia=".$_GET['cia'];

$sql="SELECT * FROM cheques where folio=".$_GET['cheque']." and num_cia=".$_GET['cia'] . (in_array($_SESSION['iduser'], $users) ? " and num_cia between 900 and 950" : "");
$cheque=ejecutar_script($sql,$dsn);

if(!$cheque){
	header("location: ./ban_cheq_bus.php?codigo_error=1");
	die();
}
$tpl->newBlock("cheque");
$tpl->assign("num_cia",$cheque[0]['num_cia']);
$cia=obtener_registro("catalogo_companias",array("num_cia"),array($cheque[0]['num_cia']),"","",$dsn);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
$tpl->assign("num_proveedor",$cheque[0]['num_cia']);
$tpl->assign("nom_proveedor",$cheque[0]['a_nombre']);
$tpl->assign("folio",$cheque[0]['folio']);
$tpl->assign("fecha",$cheque[0]['fecha']);
$tpl->assign("facturas",$cheque[0]['facturas']);

$tpl->assign("concepto",$cheque[0]['concepto']);
$tpl->assign("codgasto",$cheque[0]['codgastos']);
$sql="select * from catalogo_gastos where codgastos=".$cheque[0]['codgastos'];
$gastos=ejecutar_script($sql,$dsn);
$tpl->assign("gasto",$gastos[0]['descripcion']);

if(existe_registro("estado_cuenta",array("num_cia","folio"),array($cheque[0]['num_cia'],$cheque[0]['folio']),$dsn)){
	$edo=obtener_registro("estado_cuenta",array("num_cia","folio"),array($cheque[0]['num_cia'],$cheque[0]['folio']),"","",$dsn);
	$tpl->assign("fecha_con",$edo[0]['fecha_con']);	
	if($cheque[0]['fecha_cancelacion'] == "" and $edo[0]['fecha_con'] != ""){
		$tpl->assign("estado","CONCILIADO");
		$tpl->assign("importe",number_format($cheque[0]['importe'],2,'.',','));		
	}
	else if($cheque[0]['fecha_cancelacion'] =="" and $edo[0]['fecha_con']=="" and $cheque[0]['importe'] > 0){
		$tpl->assign("estado","SIN CONCILIAR");
		$tpl->assign("importe",number_format($cheque[0]['importe'],2,'.',','));
	}
	else if($cheque[0]['fecha_cancelacion'] !="" or $cheque[0]['importe'] < 0 ){
		$tpl->assign("estado","CANCELADO");
		$tpl->assign("importe","");
	}
}
else{
	if($cheque[0]['fecha_cancelacion'] =="" and $cheque[0]['importe'] > 0){
		$tpl->assign("estado","SIN CONCILIAR");
		$tpl->assign("importe",number_format($cheque[0]['importe'],2,'.',','));
	}
	else if($cheque[0]['fecha_cancelacion'] !="" or $cheque[0]['importe'] < 0 ){
		$tpl->assign("estado","CANCELADO");
		$tpl->assign("importe","");
	}
}




$tpl->printToScreen();

?>